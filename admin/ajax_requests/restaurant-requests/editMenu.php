<?php
/* 
** Requests to edit recipe 
** Requests from restaurant dashboard, meals tab
** Only POST requests
*/

function removeOldMenuImage($menuID,$folderPath){
  global $con;
  $stmt = $con->prepare("SELECT image FROM menu WHERE id=?");
  $stmt->execute([$menuID]);

  if($stmt->rowCount() > 0){
    $imageName = $stmt->fetch()['image'];
    $imagePath = $folderPath . $imageName;
    if(!empty($imageName) && file_exists($imagePath) && is_file($imagePath)){
      if(!unlink($imagePath))
        return false;
    }
  }// else there is no such recipe
  return true;
}

if($_SERVER['REQUEST_METHOD']=='POST'){
  require "../../config.php";
  require "../../connect.php";
  include "../../includes/functions/functions.php";

  $restID = $_POST['restID'];
  $menuID = $_POST['menuID'];
  $name = $_POST['name'];
  $description = $_POST['description'];

  $imageFile = $_FILES['menuImage'];

  $isImageUpdated = true;// we assume that image don't need to change
  $isMenuUpdated = false;
  // check if image needs to change also 
  
  $mustEditImage = !empty($imageFile['name']); //if image not empty it needs to change
  
  if($mustEditImage){
    $folderPath =  FULL_ROOTPATH . '/uploads/menus/';
    // first remove old image
    removeOldMenuImage($menuID, $folderPath);// i won't check if it has been deleted or not :)
    // second upload new image
    /// destination  path
    $imageFinalName = $restID . "_" . rand(1,1000000) . "_" . $imageFile['name'];
    $imageUploadPath = $folderPath . $imageFinalName;
    move_uploaded_file($imageFile['tmp_name'],$imageUploadPath);

    // Update image query 
    $stmt = $con->prepare("UPDATE menu SET image = ? WHERE id = ? ");
    $isImageUpdated = $stmt->execute([$imageFinalName,$menuID]);

  }
  // edit basic recipe info
  $stmt = $con->prepare("UPDATE menu SET
                          name = ?,
                          description = ?
                          WHERE id = ? ");
  $isMenuUpdated = $stmt->execute([$name,$description,$menuID]);
  
  
  if($isMenuUpdated && $isImageUpdated){
    $editedMenuInfo = getMenuInfo(($menuID));
    $result = [
      "result" => true,
      "message" => "تم التعديل بنجاح",
      "edited_menu" => $editedMenuInfo
    ];
  }else{
    $result = [
      "result" => false,
      "message" => "لم نتمكن من تعديل القائمة لسبب غير معروف"
    ];
  }

  echo json_encode($result);

}
<?php
session_start();
/* 
** Requests to edit user info 
** Requests from user dashboard, account tab
** Only POST requests
*/

function removeOldUserImage($userID,$folderPath){
  global $con;
  $stmt = $con->prepare("SELECT image FROM customer WHERE id=?");
  $stmt->execute([$userID]);

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

  $userID = intval($_POST['userID']);
  $name = $_POST['name'];
  $email = $_POST['email'];
  $address = $_POST['address'];
  $phone = $_POST['phone'];

  $imageFile = $_FILES['userImage'];

  $isImageUpdated = true;// we assume that image don't need to change
  $isUserUpdated = false;
  // check if image needs to change also 
  
  $mustEditImage = !empty($imageFile['name']); //if image not empty it needs to change
  
  if($mustEditImage){
    $folderPath =  FULL_ROOTPATH . '/uploads/customers/';
    // first remove old image
    removeOldUserImage($userID, $folderPath);// i won't check if it has been deleted or not :)
    // second upload new image
    /// destination  path
    $imageFinalName = $userID . "_" . rand(1,1000000) . "_" . $imageFile['name'];
    $imageUploadPath = $folderPath . $imageFinalName;
    move_uploaded_file($imageFile['tmp_name'],$imageUploadPath);

    // Update image query 
    $stmt = $con->prepare("UPDATE customer SET image = ? WHERE id = ?");
    $isImageUpdated = $stmt->execute([$imageFinalName,$userID]);

  }
  // edit basic recipe info
  $stmt = $con->prepare("UPDATE customer SET
                          name = ?,
                          email = ?,
                          phone = ?,
                          address = ?
                          WHERE id = ? ");
  $isUserUpdated = $stmt->execute([$name,$email,$phone,$address,$userID]);
  
  
  if($isUserUpdated && $isImageUpdated){
    $_SESSION['user'] = getUserInfo($userID);
    $result = [
      "result" => true,
      "message" => "تم التعديل بنجاح",
      "updatedUser" => getUserInfo($userID),
    ];
  }else{
    $result = [
      "result" => false,
      "message" => "لم نتمكن من تعديل المعلومات لسبب غير معروف"
    ];
  }

  echo json_encode($result);

}
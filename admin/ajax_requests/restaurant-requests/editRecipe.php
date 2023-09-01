<?php
/* 
** Requests to edit recipe 
** Requests from restaurant dashboard, meals tab
** Only POST requests
*/

function removeOldRecipeImage($recipeID,$folderPath){
  global $con;
  $stmt = $con->prepare("SELECT image FROM recipe WHERE id=?");
  $stmt->execute([$recipeID]);

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
  $recipeID = $_POST['recipeID'];
  $menuID = $_POST['menu'];
  $name = $_POST['name'];
  $description = $_POST['description'];
  $price = $_POST['price'];
  $discount = empty($_POST['discountPrice'])? NULL: $_POST['discountPrice'] ;
  $tags = $_POST['tags'];
  $isAvailable = !isset($_POST['is_not_available']);

  $imageFile = $_FILES['mealImage'];

  $isImageUpdated = true;// we assume that image don't need to change
  $isRecipeUpdated = false;
  // check if image needs to change also 
  
  $mustEditImage = !empty($imageFile['name']); //if image not empty it needs to change
  
  if($mustEditImage){
    $folderPath =  FULL_ROOTPATH . '/uploads/recipes/';
    // first remove old image
    removeOldRecipeImage($recipeID, $folderPath);// i won't check if it has been deleted or not :)
    // second upload new image
    /// destination  path
    $imageFinalName = $restID . "_" . rand(1,1000000) . "_" . $imageFile['name'];
    $imageUploadPath = $folderPath . $imageFinalName;
    move_uploaded_file($imageFile['tmp_name'],$imageUploadPath);

    // Update image query 
    $stmt = $con->prepare("UPDATE recipe SET image = ? WHERE id = ? ");
    $isImageUpdated = $stmt->execute([$imageFinalName,$recipeID]);

  }
  // edit basic recipe info
  $destinationMenuID = getDestinationMenuID($restID,$menuID);
  if($destinationMenuID != -1){
    $stmt = $con->prepare("UPDATE recipe SET
                            name = ?,
                            menu_id = ?,
                            description = ?,
                            price = ?,
                            discount_price = ?,
                            availability = ?,
                            updated_at = NOW(),
                            categories = ? 
                            WHERE id = ? ");
    $isRecipeUpdated = $stmt->execute([$name,$menuID,$description,$price,$discount,$isAvailable,$tags, $recipeID]);
  }// else getDestinationMenuID failed to create default menu that's need it
  
  if($isRecipeUpdated && $isImageUpdated){

    $result = [
      "result" => true,
      "message" => "تم التعديل بنجاح",
      "edited_recipe" => getRecipeInfo($recipeID)
    ];
  }else{
    $result = [
      "result" => false,
      "message" => "لم نتمكن من تعديل الوجبة لسبب غير معروف"
    ];
  }

  echo json_encode($result);

}
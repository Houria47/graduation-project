<?php
/* 
** Requests to delete Recipe
** Only POST requests
** requests from restaurant dashboard, meals section
*/


function removeOldRecipeImage($recipeID){
  global $con;
  $stmt = $con->prepare("SELECT image FROM recipe WHERE id=?");
  $stmt->execute([$recipeID]);

  if($stmt->rowCount() > 0){
    $imageName = $stmt->fetch()['image'];
    $folderPath =  FULL_ROOTPATH . '/uploads/recipes/';
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
  
  // Get restaurant ID
  $recipeID = $_POST['recipeID'];
  // Delete recipe image
  removeOldRecipeImage($recipeID);
  // Delete restaurant account from DB
  $stmt = $con->prepare("DELETE FROM recipe WHERE id=?");
  $stmt->execute([$recipeID]);

  if($stmt->rowCount() > 0){
    $result = [
      "result" => true,
      "message" => "تم حذف الوجبة بنجاح."
    ];
  }else{
    $result = [
      "result" => false,
      "message" => "لم نتمكن من حذف الوجبة لسبب غير معروف."
    ];
  }
  echo json_encode($result);  

}
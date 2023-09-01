<?php
/* 
** Requests to delete Recipe
** Only POST requests
** requests from restaurant dashboard, meals section
*/


function removeOldMenuImage($menuID){
  global $con;
  $stmt = $con->prepare("SELECT image FROM menu WHERE id=?");
  $stmt->execute([$menuID]);

  if($stmt->rowCount() > 0){
    $imageName = $stmt->fetch()['image'];
    $folderPath =  FULL_ROOTPATH . '/uploads/menus/';
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
  $menuID = $_POST['menuID'];
  $restID = $_POST['restID'];
  // Delete menu image
  removeOldMenuImage($menuID);

  // remove meals from this menu to default menu
  $defaultMenuID = getDefaultMenuID($restID);
  $stmt = $con->prepare("UPDATE recipe set menu_id = ? WHERE menu_id = ?");
  $stmt->execute([$defaultMenuID,$menuID]);
  // Delete restaurant account from DB
  $stmt = $con->prepare("DELETE FROM menu WHERE id=?");
  $stmt->execute([$menuID]);
  
  if($stmt->rowCount() > 0){
    $result = [
      "result" => true,
      "message" => "تم حذف القائمة بنجاح.",
      "menus"=> getRestaurantMenus($restID)
    ];
  }else{
    $result = [
      "result" => false,
      "message" => "لم نتمكن من حذف القائمة لسبب غير معروف."
    ];
  }
  echo json_encode($result);  

}
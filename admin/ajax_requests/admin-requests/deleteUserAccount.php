<?php
/* 
** Requests to delete User account
** Only POST requests
** requests from admin dashboard, users section
*/

if($_SERVER['REQUEST_METHOD']=='POST'){
  require "../../config.php";
  require "../../connect.php";
  include "../../includes/functions/functions.php";
  
  // Get user ID
  $userID = $_POST['userID'];
  
  if(deleteFiles($userID)){
    // Delete user account from DB
    $stmt = $con->prepare("DELETE FROM customer WHERE id=?");
    $queryResult = $stmt->execute([$userID]);
    if($queryResult){
      $result = [
        "status" => true,
        "message" => "تم حذف الحساب بنجاح."
      ];
    }else{
      $result = [
        "status" => false,
        "message" => "لم يتم حذف الحساب! ربما الحساب غير موجوة في قاعدة البيانات، تأكد من المعرّف المحدد."
      ];
    }
  }else{
      $result = [
        "status" => true,
        "message" => " لم ينجح حذف الملفات الخاصة بالحساب!.",
      ];
  }
  echo json_encode($result);  

}

function deleteFiles($userID){
  global $con;

  $stmt = $con->prepare("SELECT image FROM customer WHERE id = ?");
  $queryResult = $stmt->execute([$userID]);

  if(!$queryResult)
    return false;

  $image  = $stmt->fetch()['image'];
  $dir = FULL_ROOTPATH . "uploads/customers/";

  if(empty($image))
    return true;
    
  if(is_dir($dir) && unlink( $dir . $image)){
    return true;
  }

  return false;
}
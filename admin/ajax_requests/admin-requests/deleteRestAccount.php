<?php
/* 
** Requests to delete Restaurant account
** Only POST requests
** requests from admin dashboard, restaurants section
*/

if($_SERVER['REQUEST_METHOD']=='POST'){
  require "../../config.php";
  require "../../connect.php";
  include "../../includes/functions/functions.php";
  
  // Get restaurant ID
  $restID = $_POST['restID'];
  // Delete authentications files Directory for this account
  $dataDeleted = deleteRestaurantFiles($restID);
  
  
  if($dataDeleted){
    // Delete restaurant account from DB
    $stmt = $con->prepare("DELETE FROM restaurant WHERE id=?");
    $queryResult = $stmt->execute([$restID]);
    if($queryResult){
      if(isset($_SESSION['restaurant'])){
        unset($_SESSION['restaurant']);
      }
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
        "message" => " لم ينجح حذف الملفات الخاص بالحساب!.",
        "stepNumber" => $dataDeleted
      ];
  }
  echo json_encode($result);  

}
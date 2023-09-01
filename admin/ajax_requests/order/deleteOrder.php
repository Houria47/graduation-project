<?php
session_start();
/* 
** Request to delete Order 
** Request from customer dashboatd, orders tab
** Only POST request
*/

if($_SERVER['REQUEST_METHOD']=='POST'){
  require "../../config.php";
  require "../../connect.php";
  include "../../includes/functions/functions.php";

  $result = [
    "result" => false,
    "message" => " حدث خطأ غير متوقع. " . __LINE__,
    "no_user" => true,
  ];
  
  if(isset($_SESSION['user'])){

    $result['no_user'] = false;
    
    $orderID = $_POST ['orderID'];

    $stmt = $con->prepare("DELETE FROM order_ WHERE id = ?");
    try {
      $stmt->execute([$orderID]);
      $result['message'] = "تم حذف الطلب بنجاح";
      $result['result'] = true;
    } catch ( PDOException $e){
      $result['message'] .= $e->getMessage();
    }    
  }
  echo json_encode($result);

}
<?php
session_start();
/* 
** Request to get Order 
** Request from Order page
** Only GET request
*/

if($_SERVER['REQUEST_METHOD']=='GET'){
  require "../../config.php";
  require "../../connect.php";
  include "../../includes/functions/functions.php";

  $result = [
    "result" => false,
    "message" => " حدث خطأ غير متوقع. " . __LINE__,
    "no_user" => true,
    "order" => []
  ];
  
  
  if(isset($_SESSION['user'])){

    $userID = $_SESSION['user']['id'];
    $result['no_user'] = false;
    
    $restID = $_GET['restID'];
    $order = getUserRestOrder($restID, $_SESSION['user']['id']);

    if(count($order) > 0){
      $result['message'] = "تم جلب الطلبيانات بنجاح";
      $result['result'] = true;
      $result['order'] = $order;
    }else{
      $result['message'] = "لا يوجد طلبات من هذا المطعم";
    }
  }
  echo json_encode($result);

}
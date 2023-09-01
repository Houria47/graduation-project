<?php
session_start();
/* 
** Request to get Orders
** Request from custmer dashboard , orders tab
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
    
    $order = getOrders($_SESSION['user']['id']);

    if(count($order) > 0){
      $result['message'] = "تم جلب الطلبيانات بنجاح";
      $result['result'] = true;
      $result['items'] = $order;
    }else{
      $result['message'] = "لا يوجد طلبات";
    }
  }
  echo json_encode($result);

}
<?php
session_start();
/* 
** Request to get Reservations
** Request from custmer dashboard , reservation tab
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
    "items" => []
  ];
  
  
  if(isset($_SESSION['user'])){

    $userID = $_SESSION['user']['id'];
    $result['no_user'] = false;
    
    $reservs = getResrvations($_SESSION['user']['id']);

    if(count($reservs) > 0){
      $result['message'] = "تم جلب الحجوزات بنجاح";
      $result['result'] = true;
      $result['items'] = $reservs;
    }else{
      $result['message'] = "لا يوجد حجوزات";
    }
  }
  echo json_encode($result);

}
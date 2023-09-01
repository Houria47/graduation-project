<?php
session_start();
/* 
** Request to delete reservation 
** Request from customer dashboatd, reservations tab
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
    
    $reservID = $_POST ['reservID'];

    $stmt = $con->prepare("DELETE FROM reservation WHERE id = ?");
    try {
      $stmt->execute([$reservID]);
      $result['message'] = "تم حذف الحجز بنجاح";
      $result['result'] = true;
    } catch ( PDOException $e){
      $result['message'] .= $e->getMessage();
    }    
  }
  echo json_encode($result);

}
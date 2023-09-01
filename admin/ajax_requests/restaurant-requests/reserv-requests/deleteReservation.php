<?php
/* 
** Requests to delete reservation
** Requests from restaurant dashboard, reservation tab
** Only POST requests
*/

if($_SERVER['REQUEST_METHOD']=='POST'){
  require "../../../config.php";
  require "../../../connect.php";
  include "../../../includes/functions/functions.php";

  $reservID = $_POST['reservID'];
  
  $stmt = $con->prepare("DELETE FROM reservation WHERE id = ?");
  $queryResult = $stmt->execute([$reservID]);

  if($queryResult){
    $result = [
      "result" => true,
      "message" => "تم الحذف بنجاح"
    ];
  }else{
    $result = [
      "result" => false,
      "message" => "لم يتم حذف الحجز لسبب غير معروف!"
    ];
  }

  echo json_encode($result);

}
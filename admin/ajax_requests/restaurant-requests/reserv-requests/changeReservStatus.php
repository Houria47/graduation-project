<?php
/* 
** Requests to change reservation status
** Requests from restaurant dashboard, reservation tab
** Only POST requests
*/

if($_SERVER['REQUEST_METHOD']=='POST'){
  require "../../../config.php";
  require "../../../connect.php";
  include "../../../includes/functions/functions.php";

  $reservID = $_POST['reservID'];
  $status = $_POST['status'];
  
  $stmt = $con->prepare("UPDATE reservation SET status = ? WHERE id = ?");
  $queryResult = $stmt->execute([$status ,$reservID]);

  if($queryResult){
    $result = [
      "result" => true,
      "message" => "تم تعديل الحالة بنجاح"
    ];
  }else{
    $result = [
      "result" => false,
      "message" => "لم يتم تعديل الحالة لسبب غير معروف!"
    ];
  }

  echo json_encode($result);

}
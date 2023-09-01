<?php
/* 
** Requests to delete orders status
** Requests from restaurant dashboard, orders tab
** Only POST requests
*/

if($_SERVER['REQUEST_METHOD']=='POST'){
  require "../../../config.php";
  require "../../../connect.php";
  include "../../../includes/functions/functions.php";

  $reservID = $_POST['orderID'];
  
  $stmt = $con->prepare("DELETE FROM order_ WHERE id = ?");
  $queryResult = $stmt->execute([$reservID]);

  if($queryResult){
    $result = [
      "result" => true,
      "message" => "تم الحذف بنجاح"
    ];
  }else{
    $result = [
      "result" => false,
      "message" => "لم يتم حذف الطلب لسبب غير معروف!"
    ];
  }

  echo json_encode($result);

}
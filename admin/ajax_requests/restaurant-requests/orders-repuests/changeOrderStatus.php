<?php
/* 
** Requests to change order status
** Requests from restaurant dashboard, order sts tab
** Only POST requests
*/

if($_SERVER['REQUEST_METHOD']=='POST'){
  require "../../../config.php";
  require "../../../connect.php";
  include "../../../includes/functions/functions.php";

  $orderID = $_POST['orderID'];
  $status = $_POST['status'];
  
  $stmt = $con->prepare("UPDATE order_ SET order_status = ? WHERE id = ?");
  $queryResult = $stmt->execute([$status ,$orderID]);

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
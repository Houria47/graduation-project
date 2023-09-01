<?php
/* 
** Requests to get restaurant orders
** Requests from restaurant dashboard, orders tab
** Only GET requests
*/

if($_SERVER['REQUEST_METHOD']=='GET'){
  require "../../../config.php";
  require "../../../connect.php";
  include "../../../includes/functions/functions.php";

  $restID = $_GET['restID'];
  
  $orders = getRestOrders($restID);
  
  $stmt = $con->prepare("SELECT id , status FROM order_status");
  $stmt->execute([]);

  $status = [];

  if($stmt->rowCount() > 0){
    $status = $stmt->fetchAll(PDO::FETCH_ASSOC);
  }
  if(count($orders) > 0){
    $result = [
      "result" => true,
      "orders" => $orders['orders'],
      "status" => $status
    ];
  }else{
    $result = [
      "result" => false,
    ];
  }

  echo json_encode($result);

}
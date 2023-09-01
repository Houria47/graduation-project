<?php
/* 
** Requests to get restaurant reservations
** Requests from restaurant dashboard, reservations tab
** Only GET requests
*/

if($_SERVER['REQUEST_METHOD']=='GET'){
  require "../../../config.php";
  require "../../../connect.php";
  include "../../../includes/functions/functions.php";

  $restID = $_GET['restID'];
  
  $reservations = getRestResrvations($restID);
  
  $stmt = $con->prepare("SELECT id , status FROM reservation_status");
  $stmt->execute([]);

  $status = [];

  if($stmt->rowCount() > 0){
    $status = $stmt->fetchAll(PDO::FETCH_ASSOC);
  }
  if(count($reservations) > 0){
    $result = [
      "result" => true,
      "reservations" => $reservations['reservations'],
      "status" => $status
    ];
  }else{
    $result = [
      "result" => false,
    ];
  }

  echo json_encode($result);

}
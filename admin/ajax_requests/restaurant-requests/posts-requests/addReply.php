<?php
/* 
** Requests to add Post 
** Requests from restaurant dashboard, Posts tab
** Only POST requests
*/

if($_SERVER['REQUEST_METHOD']=='POST'){
  require "../../../config.php";
  require "../../../connect.php";
  include "../../../includes/functions/functions.php";

  $commentID = $_POST['commentID'];
  $reply = $_POST['reply'];

  $stmt = $con->prepare("UPDATE ad_comment SET reply_date = NOW(), reply = ? WHERE id = ?");
  $queryResult = $stmt->execute([$reply,$commentID]);

  if($queryResult){
    $result = [
      "result" => true,
      "message" => "تمت إضافة الرد"
    ];
  }else{
    $result = [
      "result" => false,
      "message" => "لم نتمكن من إضافة الرد لسبب غير معروف"
    ];

  }


  echo json_encode($result);

}
<?php
session_start();
/* 
** Requests to Get users accounts
** Requests from admin dashboard users section
** onLoad restaurants table (#users-accounts-table) event
** Only GET requests
*/

if($_SERVER['REQUEST_METHOD']=='GET'){
  require "../../config.php";
  require "../../connect.php";
  include "../../includes/functions/functions.php";

  $users = getUsersInfo();
  if(!empty($users)){
    $result = [
      "status" => true,
      'result' => $users,
      "message" => "Accounts arrived :), Line: " . __LINE__
    ];
  }else{
    $result = [
      "status" => false,
      "message" => " حدث خطأ غير متوقع، لم نتمكن من جلب البيانات. " . __LINE__
    ];
  }
  echo json_encode($result);
}
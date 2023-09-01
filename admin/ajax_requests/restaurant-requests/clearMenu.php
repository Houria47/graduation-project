<?php
/* 
** Requests to clear menu 
** Requests from restaurant dashboard, menus tab
** Only POST requests
*/


if($_SERVER['REQUEST_METHOD']=='POST'){
  require "../../config.php";
  require "../../connect.php";
  include "../../includes/functions/functions.php";

  $restID = $_POST['restID'];
  $menuID = $_POST['menuID'];

  $defaultMenu =getDefaultMenuID($restID);

  $stmt = $con->prepare("UPDATE recipe SET menu_id = ? WHERE menu_id = ?");
  $queryResult = $stmt->execute([$defaultMenu,$menuID]);

  if($queryResult){
    $result = [
      "result" => true,
      "message" => "تمت الإزالة بنجاح",
      "default_menu" => $defaultMenu
    ];
  }else{
    $result = [
      "result" => false,
      "message" => "لم نتمكن من إزالة الوجبات من القائمة لسبب غير معروف"
    ];
  }


  echo json_encode($result);

}
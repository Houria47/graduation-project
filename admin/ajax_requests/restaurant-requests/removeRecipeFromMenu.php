<?php
/* 
** Requests to remove recipefrom menu 
** Requests from restaurant dashboard, menus tab
** Only POST requests
*/


if($_SERVER['REQUEST_METHOD']=='POST'){
  require "../../config.php";
  require "../../connect.php";
  include "../../includes/functions/functions.php";

  $recipeID = $_POST['recipeID'];
  $restID = $_POST['restID'];

  $defaultMenu =getDefaultMenuID($restID);

  $stmt = $con->prepare("UPDATE recipe SET menu_id = ? WHERE id = ?");
  $queryResult = $stmt->execute([$defaultMenu,$recipeID]);

  if($queryResult){
    $result = [
      "result" => true,
      "message" => "تمت الإزالة بنجاح",
      "default_menu" => $defaultMenu
    ];
  }else{
    $result = [
      "result" => false,
      "message" => "لم نتمكن من إزالة الوجبة من القائمة لسبب غير معروف"
    ];
  }


  echo json_encode($result);

}
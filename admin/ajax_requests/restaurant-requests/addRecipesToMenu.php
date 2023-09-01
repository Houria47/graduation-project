<?php
/* 
** Requests to add recipes to Menu 
** Requests from restaurant dashboard, menus tab
** Only POST requests
*/

if($_SERVER['REQUEST_METHOD']=='POST'){
  require "../../config.php";
  require "../../connect.php";
  include "../../includes/functions/functions.php";

  $menuID = $_POST['menuID'];
  $recipes = $_POST['recipes'];

  $in  = str_repeat('?,', count($recipes) - 1) . '?';
  $stmt = $con->prepare("UPDATE recipe SET menu_id = ? WHERE id IN ($in)");
  $queryResult = $stmt->execute([$menuID,...$recipes]);

  if($queryResult){
    $result = [
      "result" => true,
      "message" => "تمت الإضافة بنجاح",
    ];
  }else{
    $result = [
      "result" => false,
      "message" => "لم تتم الإضافة لسبب غير معروف!",
    ];
  }

  echo json_encode($result);

}
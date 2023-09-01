<?php
session_start();
/* 
** Requests to Get restaurants Recipes And Menus
** Requests from restaurant dashboard meals section
** onLoad restaurants meals list
** Only GET requests
*/

if($_SERVER['REQUEST_METHOD']=='GET'){
  require "../config.php";
  require "../connect.php";
  include "../includes/functions/functions.php";

  $restID = $_GET['restID'];
  $recipes = getRestaurantRecipe($restID);
  $menus = getRestaurantMenus($restID);
  if($recipes !== false && $menus !== false){
    $result = [
      "result" => true,
      'recipes' => $recipes,
      'menus' => $menus,
      "message" => "Accounts arrived :), Line: " . __LINE__
    ];
  }else{
    $result = [
      "result" => false,
      "message" => " حدث خطأ غير متوقع، لم نتمكن من جلب البيانات. " . __LINE__
    ];
  }
  echo json_encode($result);
}
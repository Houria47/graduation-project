<?php
session_start();
/* 
** Requests to Get platform Recipes
** Requests from recipes page 
** Only GET requests
*/

if($_SERVER['REQUEST_METHOD']=='GET'){
  require "../config.php";
  require "../connect.php";
  include "../includes/functions/functions.php";

  $recipes = getRecipes();
  if($recipes !== false){
    $result = [
      "result" => true,
      'recipes' => $recipes,
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
<?php
session_start();
/* 
** Requests to Get restaurants accounts
** Requests from admin dashboard restaurants section
** onLoad restaurants table (#rests-accounts-table) event
** Only GET requests
*/

if($_SERVER['REQUEST_METHOD']=='GET'){
  require "../config.php";
  require "../connect.php";
  include "../includes/functions/functions.php";

  $Restaurants = getRestaurantsInfo();
  if($Restaurants){
    // get Restaurants Statistics
    $statistics = calcRestaurantsSatistics($Restaurants);
    $finalResult = [
      'restaurants' => $Restaurants,
      'statistics' => $statistics
    ];
    $result = [
      "status" => true,
      'result' => $finalResult,
      "message" => "Accounts arrived :), Line: " . __LINE__
    ];
  }else{
    $result = [
      "status" => false,
      'result' => $finalResult,
      "message" => " حدث خطأ غير متوقع، لم نتمكن من جلب البيانات. " . __LINE__
    ];
  }
  echo json_encode($result);
}
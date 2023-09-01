<?php
/* 
** Requests to get restaurant posts
** Requests from restaurant dashboard, posts tab
** Only POST requests
*/

if($_SERVER['REQUEST_METHOD']=='GET'){
  require "../../../config.php";
  require "../../../connect.php";
  include "../../../includes/functions/functions.php";

  $restID = $_GET['restID'];
  
  $posts = getRestaurantPosts($restID);
  
  if($posts){
    $result = [
      "result" => true,
      "posts" => $posts
    ];
  }else{
    $result = [
      "result" => false,
    ];
  }

  echo json_encode($result);

}
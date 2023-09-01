<?php
session_start();
/* 
** Requests to Get latest files from session when unAthuenticated 
** restaurant account cancle updeateing the filse
** to preview these files in the modal
** Only GET requests
*/

if($_SERVER['REQUEST_METHOD']=='GET'){
  require "../../config.php";
  require "../../connect.php";
  include "../../includes/functions/functions.php";

  
  $authFiles = $_SESSION['restaurant']['authentication_files'];
  $filesPreviewContent = "";
  if(count($authFiles) > 0){
    foreach($authFiles as $file){
      $path = $file["altPath"];
      $name = $file["name"];
      $filesPreviewContent .= "<div class='box rad-6 p-10'><img src='$path' alt='' /><span class='d-block txt-c mt-2 c-white fs-13'>$name</span></div>";
    }
  }else{
    $filesPreviewContent =  "<small>لم يتم تحديد أية ملفات</small>";
  }
  echo $filesPreviewContent;
}
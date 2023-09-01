<?php
session_start();
header("Access-Control-Allow-Method: POST");
header('Access-Control-Allow-Origin: *');
/* 
** Request to update restaurant authentication files
** Requests from restaurant dashborad page, modal section
*/
if($_SERVER['REQUEST_METHOD']=='POST'){
  require "../../config.php";
  require "../../connect.php";
  include "../../includes/functions/functions.php";

  $authFiles = $_FILES['authFiles'];
  $restID = $_POST['restID'];
  
  $oldDir = "../../uploads/authentication_files/$restID";
  
  if(!empty($restID)&& rrmdir($oldDir)){
    $files = uploadAuthenticationFiles($restID, $authFiles);
    $stmt = $con->prepare("UPDATE restaurant SET authentication_files = ? WHERE id=?");
    $stmt->execute([$files,$restID]);
    if($stmt->rowCount() > 0){
      $result = [
        "result" => true,
        "message" => "Files Updated Successfuly, in File: updateAuthenticationFiles.php, on Line:  " . __LINE__
      ];
      // update session variable
      $_SESSION['restaurant']['authentication_files']=decodeRestAuthFiles($files);
    }else{
      $result = [
        "result" => false,
        "message" => "Could not Update the Files, Query failed, in File: updateAuthenticationFiles.php, on Line:  " . __LINE__
      ];
    }
  
  }else{
    $result = [
      "result" => false,
      "message" => "Could not Delete the folder, in File: updateAuthenticationFiles.php, on Line:  " . __LINE__
    ];
  }
  echo json_encode($result);
}
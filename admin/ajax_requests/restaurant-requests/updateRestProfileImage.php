<?php
session_start();
header("Access-Control-Allow-Method: POST");
header('Access-Control-Allow-Origin: *');
/* 
** Request to update restaurant Profile cover image
** Requests from restaurant dashborad page, control tab, info section,
** update back image form modal
*/
if($_SERVER['REQUEST_METHOD']=='POST'){
  require "../../config.php";
  require "../../connect.php";
  include "../../includes/functions/functions.php";

  $fieldToUpdate = $_POST['imageType'] == "COVER"?"cover_image": "profile_image";
  $imageFile = $_FILES['profileImage'];
  $restID = $_POST['restID'];
  // get and remove old image file
  if(!empty($imageFile['name'])){
    $stmt = $con->prepare("SELECT $fieldToUpdate FROM restaurant WHERE id=?");
    $stmt->execute([$restID]);
    if($stmt->rowCount() > 0){
      // delete old file if exist
      $imageName = $stmt->fetch()[$fieldToUpdate];
      $folderPath =  ROOTPATH . '/uploads/restaurant_profile_images/';
      $oldImagePath = $folderPath . $imageName;
      $isEmptyImage = empty($imageName);
      $filesFunctionAcceptedPath = $_SERVER['DOCUMENT_ROOT'] . $oldImagePath;
      if(!$isEmptyImage && file_exists($filesFunctionAcceptedPath)){
        if(is_file($filesFunctionAcceptedPath)){
          unlink($filesFunctionAcceptedPath);
        }
      }
      // upload new file
      $imageFinalName = $restID . "_" . rand(1,1000000) . "_" . $imageFile['name'];
      $filesFunctionAcceptedPath = $_SERVER['DOCUMENT_ROOT'] . $folderPath . $imageFinalName;
      move_uploaded_file($imageFile['tmp_name'],  $filesFunctionAcceptedPath);
      // update restaurant cover image
      $stmt = $con->prepare("UPDATE restaurant SET $fieldToUpdate = ? WHERE id=?");
      $queryResult = $stmt->execute([$imageFinalName,$restID]);
      // update value in session variable
      $_SESSION['restaurant'][$fieldToUpdate] = $imageFinalName;
      if($queryResult){
        $result = [
          "result" => true,
          "message" => "تم تعديل الصورة بنجاح",
          "imageName" => $imageFinalName
        ];
      }else{
        $result = [
          "result" => false,
          "message" => "لم يتم تعديل الصورة لسبب غير معروف"
        ];
      }
    }else{
      $result = [
        "result" => false,
        "message" => "لم يتم تعديل الصورة، الحساب قد يكون غير موجود."
      ];
    }
  }else{
    //  no selected file message
    $result = [
      "result" => false,
      "message" => "لم يتم تحديد صورة!"
    ];
  }
 
  echo json_encode($result);
}
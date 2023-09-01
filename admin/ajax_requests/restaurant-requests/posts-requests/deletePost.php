<?php
/* 
** Requests to delete post
** Only POST requests
** requests from restaurant dashboard, posts section
*/


function removePostMedia($menuID){
  global $con;
  $stmt = $con->prepare("SELECT media FROM advert WHERE id=?");
  $stmt->execute([$menuID]);

  if($stmt->rowCount() > 0){
    $media = $stmt->fetch()['media'];
    $mediaArray = explode(",",$media);

    $folderPath =  FULL_ROOTPATH . '/uploads/ads/';
    $result = true;
    foreach($mediaArray as $image){
      $imagePath = $folderPath . $image;
      if(!empty($image) && file_exists($imagePath) && is_file($imagePath)){
        if(!unlink($imagePath))
          $result =  false;
      }
    }
  }// else there is no such recipe
  return $result;
}

if($_SERVER['REQUEST_METHOD']=='POST'){
  require "../../../config.php";
  require "../../../connect.php";
  include "../../../includes/functions/functions.php";
  
  // Get restaurant ID
  $postID = $_POST['postID'];
  // Delete post media
  removePostMedia($postID);
  // Delete restaurant account from DB
  $stmt = $con->prepare("DELETE FROM advert WHERE id=?");
  $stmt->execute([$postID]);

  if($stmt->rowCount() > 0){
    $result = [
      "result" => true,
      "message" => "تم حذف الإعلان بنجاح."
    ];
  }else{
    $result = [
      "result" => false,
      "message" => "لم نتمكن من حذف الإعلان لسبب غير معروف."
    ];
  }
  echo json_encode($result);  

}
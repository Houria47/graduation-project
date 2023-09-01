<?php
/* 
** Requests to edit post 
** Requests from restaurant dashboard, posts tab
** Only POST requests
*/

function removeOldPostMedia($postID,$folderPath){
  global $con;
  $stmt = $con->prepare("SELECT media FROM advert WHERE id=?");
  $stmt->execute([$postID]);

  $result = true;
  if($stmt->rowCount() > 0){
    
    $media = $stmt->fetch()['media'];
    $mediaArray = explode(",",$media);

    foreach($mediaArray as $image){
      $imagePath = $folderPath . $image;
      if(!empty($image) && file_exists($imagePath) && is_file($imagePath)){
        if(!unlink($imagePath))
          $result =  false;
      }
    }

  }// else there is no such post
  return $result;
}

if($_SERVER['REQUEST_METHOD']=='POST'){
  require "../../../config.php";
  require "../../../connect.php";
  include "../../../includes/functions/functions.php";

  $restID = $_POST['restID'];
  $postID = $_POST['postID'];
  $caption = $_POST['caption'];
  $adType = $_POST['adType'];
  
  $media = $_FILES['media'];

  $isMediaUpdated = true;// we assume that image don't need to change
  $isPostUpdated = false;
  // check if image needs to change also 
  
  $mustEditMedia = (isset($_FILES['media']) && count($media['name']) > 0 && !empty($media['name'][0])); //if image not empty it needs to change
  
  if($mustEditMedia){
    $folderPath =  FULL_ROOTPATH . '/uploads/ads/';
    // first remove old media
    removeOldPostMedia($postID, $folderPath);// i won't check if it has been deleted or not :)
    // second upload new media
    $uploadedMedia = uploadPostMedai($media,$restID);
    
    // Update media query 
    $stmt = $con->prepare("UPDATE advert SET media = ? WHERE id = ? ");
    $isMediaUpdated = $stmt->execute([$uploadedMedia,$postID]);

  }
  // edit basic recipe info
  $stmt = $con->prepare("UPDATE advert SET
                          caption = ?,
                          updated_at = NOW(),
                          ad_type = ?
                          WHERE id = ? ");
  $isPostUpdated = $stmt->execute([$caption,$adType,$postID]);
  
  
  if($isPostUpdated && $isMediaUpdated){
    $result = [
      "result" => true,
      "message" => "تم التعديل بنجاح",
      "updated_post" => getPostInfo($postID),
    ];
  }else{
    $result = [
      "result" => false,
      "message" => "لم نتمكن من تعديل الإعلان لسبب غير معروف"
    ];
  }

  echo json_encode($result);

}
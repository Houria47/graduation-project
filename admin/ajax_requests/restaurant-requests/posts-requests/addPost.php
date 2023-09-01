<?php
/* 
** Requests to add Post 
** Requests from restaurant dashboard, Posts tab
** Only POST requests
*/

if($_SERVER['REQUEST_METHOD']=='POST'){
  require "../../../config.php";
  require "../../../connect.php";
  include "../../../includes/functions/functions.php";

  $restID = $_POST['restID'];
  $caption = $_POST['caption'];
  $type = $_POST['adType'];

  $media = $_FILES['media'];

  // upload images
  
  $uploadedMedia = uploadPostMedai($media,$restID);
  $stmt = $con->prepare("INSERT INTO advert
                          (restaurant_id,
                          caption,
                          media,
                          ad_type,
                          updated_at) 
                          VALUES(
                            :restaurant_id,
                            :caption,
                            :media,
                            :type,
                            NOW()
                          ) ");
  $queryResult = $stmt->execute([
    "caption"=> $caption,
    "restaurant_id" => $restID,
    "media" => $uploadedMedia,
    "type"=> $type
  ]);

  if($queryResult){
    $date = new DateTime();
    $post = [
      "id" => $con->lastInsertId(),
      "caption" => $caption,
      "media" => decodePostMedia($uploadedMedia),
      "created_at" => $date,
      "updated_at" => $date,
      "type"=> $type,
      "reacts" => [],
      "comments" => []
    ];
    $result = [
      "result" => true,
      "message" => "تمت الإضافة بنجاح",
      "inserted_post" => getPostInfo($con->lastInsertId()) 
    ];
  }else{
    $result = [
      "result" => false,
      "message" => "لم نتمكن من إضافة الإعلان لسبب غير معروف"
    ];
  }
  echo json_encode($result);

}
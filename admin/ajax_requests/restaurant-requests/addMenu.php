<?php
/* 
** Requests to add Menu 
** Requests from restaurant dashboard, menus tab
** Only POST requests
*/

if($_SERVER['REQUEST_METHOD']=='POST'){
  require "../../config.php";
  require "../../connect.php";
  include "../../includes/functions/functions.php";

  $restID = $_POST['restID'];
  $name = $_POST['name'];
  $description = $_POST['description'];

  $imageFile = $_FILES['menuImage'];

  // upload image 
  
  $imageName = $imageFile['name'];
  // destination  path
  $folderPath =  FULL_ROOTPATH . '/uploads/menus/';
  $imageFinalName = $restID . "_" . rand(1,1000000) . "_" . $imageName;
  $imagePath = $folderPath . $imageFinalName;
  
  if(!empty($imageName) && is_dir($folderPath)){
    move_uploaded_file($imageFile['tmp_name'],$imagePath);
    $stmt = $con->prepare("INSERT INTO menu
                            (name,
                            restaurant_id,
                            description,
                            image) 
                            VALUES(
                              :name,
                              :restaurant_id,
                              :description,
                              :image
                            ) ");
    $stmt->execute([
      "name"=> $name,
      "restaurant_id" => $restID,
      "description" => $description,
      "image"=> $imageFinalName
    ]);

    if($stmt->rowCount() > 0){
      $result = [
        "result" => true,
        "message" => "تمت الإضافة بنجاح",
        "inserted_menu" => getMenuInfo($con->lastInsertId()),
        "post" => $_POST
      ];
    }else{
      $result = [
        "result" => false,
        "message" => "لم نتمكن من إضافة القائمة لسبب غير معروف"
      ];
    }
  }else{
    $result = [
      "result" => false,
      "message" => "لم نتمكن من تحميل الصورة",
    ];
  }
  echo json_encode($result);

}
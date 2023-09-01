<?php
/* 
** Requests to add recipe 
** Requests from restaurant dashboard, meals tab
** Only POST requests
*/

if($_SERVER['REQUEST_METHOD']=='POST'){
  require "../../config.php";
  require "../../connect.php";
  include "../../includes/functions/functions.php";

  $restID = $_POST['restID'];
  $menuID = $_POST['menu'];
  $name = $_POST['name'];
  $description = $_POST['description'];
  $price = $_POST['price'];
  $discount = $_POST['discountPrice'];
  $tags = $_POST['tags'];

  $imageFile = $_FILES['mealImage'];

  // upload image 
  
  $imageName = $imageFile['name'];
  // destination  path
  $folderPath =  ROOTPATH . '/uploads/recipes/';
  $imageFinalName = $restID . "_" . rand(1,1000000) . "_" . $imageName;
  $filesFunctionAcceptedPath = $_SERVER['DOCUMENT_ROOT'] . $folderPath . $imageFinalName;
  
  if(!empty($imageName) && is_dir($_SERVER['DOCUMENT_ROOT'] . $folderPath)){
    move_uploaded_file($imageFile['tmp_name'],$filesFunctionAcceptedPath);
    $destinationMenuID = getDestinationMenuID($restID,$menuID);
    if($destinationMenuID != -1){
      $stmt = $con->prepare("INSERT INTO recipe
                              (name,
                              menu_id,
                              description,
                              price,
                              discount_price,
                              image,
                              updated_at,
                              categories) 
                              VALUES(
                                :name,
                                :menuID,
                                :description,
                                :price,
                                :discount_price,
                                :image,
                                NOW(),
                                :categories
                              ) ");
      $recipeInfo = [
        "name"=> $name,
        "menuID" => $destinationMenuID,
        "description" => $description,
        "price" => $price,
        "discount_price" => empty($discount) ? null:$discount,
        "image"=> $imageFinalName,
        "categories" => $tags
      ];
      $stmt->execute($recipeInfo);
      if($stmt->rowCount() > 0){
        $result = [
          "result" => true,
          "message" => "تمت الإضافة بنجاح",
          "inserted_recipe" => getRecipeInfo($con->lastInsertId()),
          "inserted_recipe_id" => $con->lastInsertId()
        ];
      }else{
        $result = [
          "result" => false,
          "message" => "لم نتمكن من إضافة الوجبة لسبب غير معروف"
        ];
      }
    }
  }else{
    $result = [
      "result" => false,
      "message" => "لم نتمكن من تحميل الصورة",
    ];
  }
  $result[] = $_POST;
  echo json_encode($result);

}
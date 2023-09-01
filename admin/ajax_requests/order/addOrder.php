<?php
session_start();
/* 
** Request to add Order 
** Request from Order page
** Only POST request
*/

if($_SERVER['REQUEST_METHOD']=='POST'){
  require "../../config.php";
  require "../../connect.php";
  include "../../includes/functions/functions.php";

  $result = [
    "result" => false,
    "message" => " حدث خطأ غير متوقع. " . __LINE__,
    "no_user" => true,
    "inserted_id" => null,
    "cart_size" => 0
  ];
  
  
  if(isset($_SESSION['user'])){

    $userID = $_SESSION['user']['id'];
    $result['no_user'] = false;
    
    $items = $_POST['items'];
    $name = $_POST['name'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    $notes = $_POST['notes'];


    $con->beginTransaction();

    try {
      $stmt = $con->prepare("INSERT INTO order_(customer_id,address,name,order_status,phone,	notes)
      VALUES(:userID,:address,:name,1,:phone,:notes)");

      $queryResult = $stmt->execute([
        "userID" => $userID,
        "address"=>$address,
        "name"=>$name,
        "phone" =>$phone,
        "notes" =>$notes
      ]);
  
      if(!$queryResult){
        throw new RuntimeException("لم نتمكن من إضافة الطلب لسبب غير متوقع!");
      }

      $orderID = $con->lastInsertId();
      
      $stmt = $con->prepare("INSERT INTO order_item(order_id,recipe_id,quantity) VALUES(:orderID,:recipeID,:qty)");
      
      foreach($items as $item){
        $queryResult = $stmt->execute([
          "orderID"=> $orderID,
          "recipeID" => $item['recipe']['id'],
          "qty" => $item['quantity']
        ]);

        if(!$queryResult){
          throw new RuntimeException("لم تتم إضافة عنصر من الطلبية");
        }
      }

      if(!removeItemsFromCart($items)){
        throw new RuntimeException("لم نتمكن من إزالة الطلبيات من السلة.");
      }

      $con->commit();
  
      $result['message'] = "تمت إضافة الطلبية بنجاح";
      $result['result'] = true;
      $result['cart_size'] = getUserCartItemsNum($userID);
  
    } catch( RuntimeException $e  ) {
      $con->rollBack();
      $result['message'] = $e->getMessage();
    } catch( PDOException $e){
      $con->rollBack();
      $result['message'] = $e->getMessage();
    }

   echo json_encode($result);

  }
  
}
function removeItemsFromCart($items){
  global $con;
  $stmt = $con->prepare("DELETE FROM cart WHERE id = ?");
  foreach($items as $item){
    $queryResult = $stmt->execute([$item['id']]);
    if(!$queryResult){
      return false;
    }
  }
  return true;
}
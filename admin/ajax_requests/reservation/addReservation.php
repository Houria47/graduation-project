<?php
session_start();
/* 
** Request to add Reservation 
** Request from reservation page
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
    "inserted_id" => null
  ];
  
  
  if(isset($_SESSION['user'])){

    $userID = $_SESSION['user']['id'];
    $result['no_user'] = false;
    
    $restID = $_POST['restID'];
    $items = $_POST['items'];
    $name = $_POST['name'];
    $phone = $_POST['phone'];
    $guestsNum = $_POST['guestsNum'];
    $notes = $_POST['notes'];
    $date = $_POST['date'];

    // Start Transaction
    $con->beginTransaction();

    try {
      $stmt = $con->prepare("INSERT INTO reservation(customer_id,restaurant_id,name,phone,reserv_date,guests_num,notes,status)
      VALUES(:userID,:restID,:name,:phone,:date,:gnum,:notes,1)");
  
      $queryResult = $stmt->execute([
        "userID" => $userID,
        "restID"=>$restID,
        "name"=>$name,
        "phone"=>$phone,
        "date" =>$date,
        "gnum" =>$guestsNum,
        "notes" =>$notes
      ]);
  
      if(!$queryResult){
        throw new RuntimeException("لم نتمكن من إضافة الحجز لسبب غير متوقع!");
      }
      $reservID = $con->lastInsertId();

      $stmt = $con->prepare("INSERT INTO reservation_item(reservation_id,recipe_id,quantity) VALUES(:reservID,:recipeID,:qty)");

      if($items != null){
        foreach($items as $item){
          $queryResult = $stmt->execute([
            "reservID"=> $reservID,
            "recipeID" => $item['recipeID'],
            "qty" => $item['quantity']
          ]);
          
          if(!$queryResult){
            throw new RuntimeException("لم تتم إضافة عنصر من الطلبية");
          }
        }
      }
      
      $con->commit();
  
      $result['message'] = "تم إرسال طلبك للمطعم، يمكنك معرفة حالة الطلب من حسابك من خلال تبويبة \"حجوزاتي\".";
      $result['result'] = true ;
  
    } catch( RuntimeException $e  ) {
      $con->rollBack();
      $result['message'] = $e->getMessage();
    } catch( PDOException $e){
      $con->rollBack();
      $result['message'] = $e->getMessage();
    }
  
  }
    
  echo json_encode($result);

}
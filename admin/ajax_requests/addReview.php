<?php
session_start();
/* 
** Requests to Add review on Recipes/Restaurant
** Only POST requests
*/

if($_SERVER['REQUEST_METHOD']=='POST'){
  require "../config.php";
  require "../connect.php";
  include "../includes/functions/functions.php";


  $result = [
    "result" => true,
    "message" => " حدث خطأ غير متوقع. " . __LINE__,
    "no_user" => true,
    "no_review" => true,
    "old_review" => null,
    "inserted_id" => null
  ];
  
  
  if(isset($_SESSION['user'])){

    $userID = $_SESSION['user']['id'];
    $result['no_user'] = false;

    $do = $_POST['do'];
    $ratedID = $_POST['ratedID'];
    switch ($do) {
      case 'RATE_RECIPE':
        $review = $_POST['review'];
        $rate = $_POST['rate'];

        $stmt = $con->prepare("INSERT INTO recipe_reviews(customer_id,recipe_id,review,rate) VALUES(?,?,?,?)");
        $queryResult = $stmt->execute([$userID,$ratedID,$review,$rate]);

        if($queryResult){
          $result['message'] = "تمت إضافة التقييم بنجاح";
          $result['inserted_id'] = $con->lastInsertId();
        }else{
          $result['result'] = false;
        }
        break;
      case 'RATE_REST':
        $review = $_POST['review'];
        $rate = $_POST['rate'];

        $stmt = $con->prepare("INSERT INTO restaurant_reviews(customer_id,restaurant_id,review,rate) VALUES(?,?,?,?)");
        $queryResult = $stmt->execute([$userID,$ratedID,$review,$rate]);

        if($queryResult){
          $result['message'] = "تمت إضافة التقييم بنجاح";
          $result['inserted_id'] = $con->lastInsertId();
        }else{
          $result['result'] = false;
        }
        break;
      case 'ATTEMPT_RECIPE':
        $stmt = $con->prepare("SELECT * FROM recipe_reviews WHERE customer_id = ? AND recipe_id = ? LIMIT 1");
        $queryResutl = $stmt->execute([$userID,$ratedID]);
        if($queryResutl && $stmt->rowCount() > 0){
          $result['no_review'] = false;
          $row = $stmt->fetch();
          $result['old_review'] = [
            "id" => $row['id'],
            "review" => $row['review'],
            "rate" => $row['rate']
          ];
          $result['message'] = 'يوجد تعليق قديم، عدل عليه';

        }else{//else there is no review, so it true
          $result['message'] = 'لا يوجد تعليق قديم، اضف تقييم جديد';
        }
        break;
      case 'ATTEMPT_REST':
        $stmt = $con->prepare("SELECT * FROM restaurant_reviews WHERE customer_id = ? AND restaurant_id = ? LIMIT 1");
        $queryResutl = $stmt->execute([$userID,$ratedID]);
        if($queryResutl && $stmt->rowCount() > 0){
          $result['no_review'] = false;
          $row = $stmt->fetch();
          $result['old_review'] = [
            "id" => $row['id'],
            "review" => $row['review'],
            "rate" => $row['rate']
          ];
          $result['message'] = 'يوجد تعليق قديم، عدل عليه';

        }else{//else there is no review, so it true
          $result['message'] = 'لا يوجد تعليق قديم، اضف تقييم جديد';
        }
        break;
      case 'EDIT_RECIPE':
        $review = $_POST['review'];
        $rate = $_POST['rate'];

        $stmt = $con->prepare("UPDATE recipe_reviews 
        SET review = ?,rate = ?, added_at = NOW()
        WHERE id = ?");
        $queryResult = $stmt->execute([$review,$rate,$ratedID]);

        if($queryResult){
          $result['message'] = "تم تعديل التقييم بنجاح";
        }else{
          $result['result'] = false;
        }
        break;
      case 'EDIT_RESTAURANT':
        $review = $_POST['review'];
        $rate = $_POST['rate'];

        $stmt = $con->prepare("UPDATE restaurant_reviews 
        SET review = ?,rate = ?, added_at = NOW()
        WHERE id = ?");
        $queryResult = $stmt->execute([$review,$rate,$ratedID]);

        if($queryResult){
          $result['message'] = "تم تعديل التقييم بنجاح";
        }else{
          $result['result'] = false;
        }
        break;
      default:
        $result = [
          "result" => false,
          "message" => "طلب غير صحيح" . __LINE__
        ];
        break;
    }
  }

  echo json_encode($result);
}
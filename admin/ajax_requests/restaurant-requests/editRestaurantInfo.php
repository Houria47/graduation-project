<?php
session_start();
header("Access-Control-Allow-Method: POST");
header('Access-Control-Allow-Origin: *');
/* 
** Request to update restaurant Profile info
** Requests from restaurant dashborad page, control tab
** Requests form these sections
** 1- BASIC_INFO: restaurant-preview widget, basic info form
** 1- FASTFOOD_STATUS_INFO: services widget, brestaurant status options
*/
if($_SERVER['REQUEST_METHOD']=='POST'){
  require "../../config.php";
  require "../../connect.php";
  include "../../includes/functions/functions.php";

  $restID = $_POST['restID'];
  $infoType = $_POST['infoType'];
  $queryResult = false;
  $failureMessage = "لم ينجح تعديل المعلومات لسبب غير معروف! قد يكون الحساب غير موجود.";
  switch($infoType){
    case "BASIC_INFO":  
      $name = $_POST['name'];
      $phone = $_POST['phone'];
      $email = $_POST['email'];
      $description = $_POST['description'];
      // update restaurant name,desc,phone,email info
      $stmt = $con->prepare("UPDATE restaurant 
                              SET name = ?,
                                phone = ?,
                                email = ?,
                                description = ?
                              WHERE id=?");
      $queryResult = $stmt->execute([$name,$phone,$email,$description,$restID]);
      if($queryResult){
        $_SESSION['restaurant']['name'] = $name;
        $_SESSION['restaurant']['email'] = $email;
        $_SESSION['restaurant']['phone'] = $phone;
        $_SESSION['restaurant']['description'] = $description;
        $result = [
          "result" => true,
          "message" => "تم تعديل المعلومات بنجاح"
        ];
      }
    break;
    case "SERVICES":
      // get service type and value
      $serviceType = $_POST['serviceType'];
      $serviceValue = intval($_POST['serviceValue']);
      switch($serviceType){
        case "fastfood":
          // update restaurant type status
          if($serviceValue){
            // disable reservation service also
            $stmt = $con->prepare("UPDATE restaurant SET fast_food = ?, reserv_service = ? WHERE id=?");
            $queryResult = $stmt->execute([1,0,$restID]);
          }else{
            $stmt = $con->prepare("UPDATE restaurant SET fast_food = ? WHERE id=?");
            $queryResult = $stmt->execute([0,$restID]);
          }
          if($queryResult){
            if($serviceValue){
              $_SESSION['restaurant']['fast_food'] = 1;
              $_SESSION['restaurant']['reserv_service'] = 0;
            }else{
              $_SESSION['restaurant']['fast_food'] = 0;
            }
            $result = [
              "result" => true,
              "message" => "تم تعديل الحالة بنجاح",
            ];
          }
        break;
        case "reservation":
          // update reservation service
          $stmt = $con->prepare("UPDATE restaurant SET reserv_service = ? WHERE id=?");
          $queryResult = $stmt->execute([$serviceValue,$restID]);

          if($queryResult){
            $_SESSION['restaurant']['reserv_service'] = $serviceValue;
            $result = [
              "result" => true,
              "message" => "تم تعديل الحالة بنجاح"
            ];
          }
        break;
        case "delivery":
          // update reservation service
          $stmt = $con->prepare("UPDATE restaurant SET delivery_service = ? WHERE id=?");
          $queryResult = $stmt->execute([$serviceValue,$restID]);

          if($queryResult){
            $_SESSION['restaurant']['delivery_service'] = $serviceValue;
            $result = [
              "result" => true,
              "message" => "تم تعديل الحالة بنجاح"
            ];
          }
        break;
        default :
          $failureMessage = "نوع الخدمة غير موجود!";
        break;
      }
    break;
    case "WORKHOURS":
        
      $openTime = $_POST['openTime'];
      $closeTime = $_POST['closeTime'];
      // update restaurant name,desc,phone,email info
      $stmt = $con->prepare("UPDATE restaurant SET open_time = ?, close_time = ? WHERE id=?");
      $queryResult = $stmt->execute([$openTime,$closeTime,$restID]);
      if($queryResult){
        $_SESSION['restaurant']['open_time'] = $openTime;
        $_SESSION['restaurant']['close_time'] = $closeTime;
        $result = [
          "result" => true,
          "message" => "تم تعديل المعلومات بنجاح"
        ];
      }
    break;
    case "ADD_ADDRESS":
      // update restaurant name,desc,phone,email info
      $stmt = $con->prepare("INSERT INTO address(restaurant_id ,state,region,street) 
                                         VALUES(:restid,:state,:region,:street)");
      $queryResult = $stmt->execute([
        "restid" => $restID,
        "state" => $_POST['state_id'],
        "region" => $_POST['region'],
        "street" => $_POST['street'],
      ]);
      if($queryResult){
        $insertedAddressID = $con->lastInsertId();
        // add address to session
        $addedAddress = [
          'id' => $insertedAddressID,
          'state_id'=> $_POST['state_id'],
          'state' => $_POST['state'],
          'region' => $_POST['region'],
          'street' => $_POST['street']
        ];
        $_SESSION['restaurant']['addresses'][]= $addedAddress;
        $result = [
          "result" => true,
          "message" => "تمت إضافة العنوان بنجاح",
          "addedAddress" => $addedAddress
        ];
      }else{
        $failureMessage = "لم نتمكن من إضافة العنوان لسبب غير معروف! قد تكون المعلومات المدخلة غير صالحة.";
      }
    break;
    case "DELETE_ADDRESS":
      $addressID = $_POST['addressID'];
      $stmt = $con->prepare("DELETE FROM address WHERE id=?");
      $queryResult = $stmt->execute([$addressID]);
      if($queryResult){
        $deletedAddressIndex = findIndexByID($_SESSION['restaurant']['addresses'],$addressID);
        if($deletedAddressIndex != -1){
          unset($_SESSION['restaurant']['addresses'][$deletedAddressIndex]);
        }
        $result = [
          "result" => true,
          "message" => "تم حذف العنوان بنجاح"
        ];
      }
    break;
    case "EDIT_ADDRESS":
      $addressID = $_POST['addressID'];
      $stmt = $con->prepare("UPDATE address 
                                SET state = ? , 
                                    region = ? ,
                                    street = ?
                                WHERE id=?");
      $queryResult = $stmt->execute([ 
        $_POST['state_id'],
        $_POST['region'],
        $_POST['street'],
        $addressID
      ]);
      if($queryResult){
        $editedAddressIndex = findIndexByID($_SESSION['restaurant']['addresses'],$addressID);
        if($editedAddressIndex != -1){
          $_SESSION['restaurant']['addresses'][$editedAddressIndex]=[
            'id' => $addressID,
            'state_id'=> $_POST['state_id'],
            'state' => $_POST['state'],
            'region' => $_POST['region'],
            'street' => $_POST['street']
          ];
        }
        $result = [
          "result" => true,
          "message" => "تم تعديل العنوان بنجاح"
        ];
      }
    break;
    case "CONFIRM_PASSWORD":
      $pass = sha1($_POST['pass']);
      // check if password currect
      $stmt = $con->prepare("SELECT id FROM restaurant WHERE id = ? AND password = ?");
      $queryResult = $stmt->execute([$restID, $pass]);

      if($stmt->rowCount()  > 0){
        $result = [
          "result" => true,
          "message" => "كلمة المرور صحيحة."
        ];
      }else{
        $result = [
          "result" => false,
          "message" => "كلمة المرور غير صحيحة!"
        ];
      }
    break;
    case "CHANGE_PASSWORD":
      $pass = sha1($_POST['pass']);
      // Delete restaurant account from DB
      $stmt = $con->prepare("UPDATE restaurant SET password = ? WHERE id=?");
      $queryResult =  $stmt->execute([ $pass,$restID]);

      if($queryResult){
        $result = [
          "result" => true,
          "message" => "تم تغيير كلمة المرور بنجاح."
        ];
      }else{
        $failureMessage = "لم نتمكن من تغيير كلمة المرور!";
      }
    break;
    case "DELETE_MY_RESTAURANT":
       // Delete restaurant files
       $msg =  "تم حذف الحساب بنجاح.";
       if(!deleteRestaurantFiles($restID)){
         // Delete restaurant account from DB
        $stmt = $con->prepare("DELETE FROM restaurant WHERE id=?");
        $queryResult = $stmt->execute([$restID]);
        if($queryResult){
          $result = [
            "result" => true,
            "message" => $msg
          ];
          // remove account from session
          if(isset($_SESSION['restaurant'])){
            unset($_SESSION['restaurant']);
            $_SESSION['restaurantHasBeenDeleted'] = true;// to show message in index page after redirect
          }
        }
       }else{
        $failureMessage = "لم نتمكن من حذف بيانات الحساب!";
      }
    break;
  }
  if(!$queryResult){
    $result = [
      "result" => false,
      "message" => $failureMessage
    ];
  }
  echo json_encode($result);
}
function findIndexByID($array,$searched_id){
  foreach($array as $key => $subArray){
    if($subArray['id'] == $searched_id)
      return $key;
  }
  return -1;
}
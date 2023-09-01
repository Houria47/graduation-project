<?php
session_start();
/* 
** Requests to register user account 
** Requests from user register page
** Only POST requests
*/

if($_SERVER['REQUEST_METHOD'] == "POST"){
  require "../../config.php";
  require "../../connect.php";
  include "../../includes/functions/functions.php";

  $result = [
    "result" => false,
    "message" => "فشل تسجيل الحساب!"
  ];
  // Get the data
  $restName = $_POST['restName'];
  $email = $_POST['email'];
  $password = $_POST['pass'];
  $desc = $_POST['description'];
  $address = [
    'state' => $_POST['state'],
    'region' => $_POST['region'],
    'street' => $_POST['street']
  ];
  $delivery = $_POST['delivery'];
  $reserv = $_POST['reserv'];
  $fastFood = $_POST['fastfood'];
  $openTime = $_POST['openTime'];
  $closeTime = $_POST['closeTime'];
  $phone = "09" . $_POST['phone'];

  $authFiles = $_FILES['authFiles'];
  
  // insert the data and send verify code

  // Create verify token
  $token = md5(rand());

  try{
    $con->beginTransaction();

    // send verify code
    $libs = "./../../includes/libraries/";
    require("./../../includes/functions/mail.php");
    $faildMessage = sendVerifyToken($email,$token,"RESTAURANT");

    if($faildMessage !== false) {

      throw new RuntimeException("فشل إرسال رابط التأكيد لبريدك الإلكتروني، تحقق من الاتصال بالإنترنت 
      <div class='d-none'> $faildMessage, line: " . __LINE__ . "</div>");

    }

    // start adding data
    
    // insert all data except authentication files

    // insert basic data to restaurant table
    $stmt = $con->prepare("INSERT INTO restaurant(
      name,
      email,
      phone,
      password,
      description,
      open_time,
      close_time,
      delivery_service,
      reserv_service,
      fast_food,
      verify_token
      ) Values(
        :name,
        :email,
        :phone,
        :pass,
        :desc,
        :opent,
        :closet,
        :dels,
        :ress,
        :fast,
        :token)");
    $queryResult =  $stmt->execute([
      "name" => $restName,
      "email" => $email,
      "phone" => $phone,
      "pass" => sha1($password),
      'desc' => $desc,
      "opent" => $openTime,
      "closet" => $closeTime,
      "dels" => $delivery,
      "ress" => $reserv,
      "fast" => $fastFood,
      "token" => $token
    ]);

    if($queryResult && $stmt->rowCount() == 0){
      throw new RuntimeException("فشل تسجيل الحساب! <div class='d-none'>Failed to insert restaurant data: faild to add authentication_files, line: " . __LINE__ . "</div>");
    }
      
    // get id to upload and insert authentication files
    $restID = $con->lastInsertId();

    // upload files
    $files = uploadAuthenticationFiles($restID, $authFiles);
    // insert files
    $stmt = $con->prepare("UPDATE restaurant SET authentication_files = ? WHERE id=?");
    $queryResult = $stmt->execute([$files,$restID]);

    if($queryResult && $stmt->rowCount() == 0){
      throw new RuntimeException("فشل تسجيل الحساب! <div class='d-none'>Failed to insert restaurant data: faild to add authentication_files, line: " . __LINE__ . "</div>");  
    }
    
    // insert address data to address table
    $stmt = $con->prepare("INSERT INTO address(
      restaurant_id,
      street,
      region,
      state) VALUES(
        :restId,
        :street,
        :region,
        :state)");
     $queryResult = $stmt->execute([
      "restId" => $restID,
      "street" => $address['street'],
      "region" => $address['region'],
      "state" => $address['state'],
    ]);

    if($queryResult && $stmt->rowCount() == 0){
      throw new RuntimeException("<br>Failed to insert restaurant data: insert Address data failed, line: " . __LINE__ . "<br>");  
    }

    $con->commit();

    $result['message'] = "لقد قمنا بإرسال رابط لتأكيد الحساب إلى بريدك الالكتروني يرجي التحقق منه.";
    $result['result'] = true;
    
  } catch( RuntimeException $e ) {

    if(!empty($restID)){
      $authDir = "../../uploads/authentication_files/$restID";
      rrmdir($authDir);
    }
    $con->rollBack();
    $result['message'] = $e->getMessage();

  } catch( PDOException $e){

    if(!empty($restID)){
      $authDir = "../../uploads/authentication_files/$restID";
      rrmdir($authDir);
    }
    $con->rollBack();
    $result['message'] = $e->getMessage();

  }

  echo json_encode($result);

}

  
/*
  if(!$registerIsFailed){
    // registeration successed: show success msg
    ?>
<div class="popup-overlay allow-close">
  <div class="popup-box">
    <img class="img-confitte" src="../layout/images/confetti.png" alt="">
    <h2>تم التسجيل بنجاح</h2>
    <img src="../layout/images/mail.jpg" alt="">
    <p>لقد قمنا بإرسال رابط لتأكيد الحساب إلى بريدك الالكتروني يرجي التحقق منه.</p>
    <a href="restLogin.php">تسجيل الدخول</a>
  </div>
</div>
<?php
  }else{
    // registeration faild: show error msg
    ?>
<div class="popup-overlay allow-close">
  <div class="popup-box">
    <h2>فشل التسجيل</h2>
    <img src="../layout/images/sad_customer.jpg" alt="">
    <p><?=$registerIsFailed?></p>
    <a href="#" class="js-close-popup">Return</a>
  </div>
</div>
<?php
  }
  
*/
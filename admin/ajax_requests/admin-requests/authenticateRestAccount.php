<?php
session_start();
/* 
** Requests to Verify Restaurant account
** Only POST requests
** requests from admin dashboard, restaurants section
*/

if($_SERVER['REQUEST_METHOD']=='POST'){
  require "../../config.php";
  require "../../connect.php";
  include "../../includes/functions/functions.php";
  
  $result = [
    "status" => false,
    "message" => "حدث خطأ غير متوقع"
  ];
  try{
    $con->beginTransaction();

    $restID = $_POST['restID'];
    $stmt = $con->prepare("UPDATE restaurant SET account_status = ? WHERE id=?");
    $result = $stmt->execute([2,$restID]);

    if(!$result){
      throw new RuntimeException("لم يتم توثيق الحساب!"); 
    }
    // get restaurant email to send authenticated message
    $stmt = $con->prepare("SELECT email,name FROM restaurant WHERE id=?");
    $stmt->execute([$restID]);
    $row = $stmt->fetch();
    $name = $row['name'];
    $email = $row['email'];
    $libs = './../../includes/libraries/';
    require "../../includes/functions/mail.php";
    // Delete authentications files Directory for this account
    $dir = './../../uploads/authentication_files/' . $restID;
    // delete files....
    rrmdir($dir);

    $faildMessage = sendRestuarantHasBeenVerified($email,$name);
    if($faildMessage !== false){
      throw new RuntimeException("لكن لم نتمكن من إرسال البريد لصاحب المطعم، تأكد من وجود اتصال بالإنترنت!"); 
    }

    $con->commit();


    if(isset($_SESSION['restaurant'])){
      $_SESSION['restaurant']['account_status'] = 2;
    }

    $result = [
      "resul"=> $_SESSION['restaurant']['account_status'],
      "status" => true,
      "message" => "تم توثيق الحساب بنجاح."
    ];
    
    
  } catch( RuntimeException $e ) {

    $con->rollBack();
    $result = [
      "status" => false,
      "message" => $e->getMessage()
    ];
    // $result['message'] = $e->getMessage();

  } catch( PDOException $e){

    $con->rollBack();
    $result = [
      "status" => false,
      "message" => $e->getMessage()
    ];
    // $result['message'] = $e->getMessage();

  }
  echo json_encode($result);
}
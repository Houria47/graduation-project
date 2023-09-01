<?php 
session_start();
// verify restaurant & user account
$pageTitle = "Resto | تأكيد حساب";
include "init.php";
if($_SERVER['REQUEST_METHOD'] == "GET"){
  if($_GET['do'] == 'RESTAURANT'){
    // verify restaurant account
    // 
    $token = $_GET['token'];

    $stmt = $con->prepare("SELECT verify_token,account_status FROM restaurant WHERE verify_token=? LIMIT 1");
    $stmt->execute([$token]);

    if($stmt->rowCount() > 0 ){
      $row= $stmt->fetch();
      if(!$row['account_status']){
        $stmt = $con->prepare("UPDATE restaurant SET account_status = ?  WHERE verify_token=? LIMIT 1");
        $stmt->execute([1,$row['verify_token']]);

        if($stmt->rowCount() > 0){
          $_SESSION['verifyMessage']="تم تأكيد الحساب";
          header('Location:./restaurant_account/restLogin.php');
          exit();
        }
        
      }else{
        $_SESSION['verifyMessage']="الحساب مؤكد مسبقاً";
        header('Location:./restaurant_account/restLogin.php');
        exit();
      }
    }else{
      $_SESSION['verifyMessage']="رمز التأكيد غير صحيح";
      header('Location:./restaurant_account/restRegister.php');
      exit();
    }
  }else if($_GET['do'] == 'USER'){
    // verify user account
    // 
    echo 'user verification';
    // 
    $token = $_GET['token'];

    $stmt = $con->prepare("SELECT verify_token,account_status FROM customer WHERE verify_token=? LIMIT 1");
    $stmt->execute([$token]);

    if($stmt->rowCount() > 0 ){
      $row= $stmt->fetch();
      if(!$row['account_status']){
        $stmt = $con->prepare("UPDATE customer SET account_status = ?  WHERE verify_token=? LIMIT 1");
        $stmt->execute([1,$row['verify_token']]);

        if($stmt->rowCount() > 0){
          $_SESSION['verifyMessage']="تم تأكيد الحساب";
          header('Location:./user_account/registerLogin.php?do=login');
          exit();
        }
        
      }else{
        $_SESSION['verifyMessage']="الحساب مؤكد مسبقاً";
        header('Location:./user_account/registerLogin.php?do=login');
        exit();
      }
    }else{
      $_SESSION['verifyMessage']="رمز التأكيد غير صحيح";
      header('Location:./user_account/registerLogin.php');
      exit();
    }
  }
}else{
  // Just to show the error
  echo "only GET request";
}
?>

<?php include $tpl . 'footer.php';
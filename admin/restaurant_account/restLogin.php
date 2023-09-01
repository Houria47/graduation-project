<?php
SESSION_START();
// Login Page to Restaurant Account
$pageTitle = "Resto | تسجيل دخول لحساب مطعم";
$css = "../layout/css/";
$js = "../layout/js/";
$func = "../includes/functions/";
$cssFiles = ["restaurant_account/loginRegister.css"];
include "../init.php";

if($_SERVER['REQUEST_METHOD']=='POST'){
  // get entered data
  $email = $_POST['email'];
  $password = $_POST['pass'];
  $hashedPassword = sha1($password);

  // check if account exist
  $stmt = $con->prepare("SELECT * FROM restaurant WHERE email=? AND password=?");
  $stmt->execute([$email,$hashedPassword]);

  if($stmt->rowCount() > 0){
    // Account exist, check if it's confirmed
    $restaurant = $stmt->fetch();
    $status = intval($restaurant['account_status']);
    if($status){
      // add restaurant information to session
      $_SESSION['restaurant'] = getRestaurantsInfo($restaurant['id']);
      header('Location: dashboard/restDashboard.php');
      exit();
    }else{
      echo "
      <div class='popup-overlay allow-close'>
        <div class='popup-box'>
          <h2 style='margin-bottom:20px'>الحساب غير مؤكد! </h2>
          <img style='width:150px' src='../layout/images/shield-with-x-mark.jpg' alt=''>
          <p style='margin-top:10px'>يرجى التحقق من بريدك الالكتروني وتأكيد الحساب عبر الرابط المرسل.</p>
          <a href='#' class='js-close-popup'>حسناً</a>
        </div>
      </div>";
    }
  }else{
    $loginSuccess = "كلمة المرور أو اسم المستخدم غير صحيح.";
  }

}

if(isset($_SESSION['verifyMessage'])){
  $msg = $_SESSION['verifyMessage'];
  echo "
  <div class='popup-overlay allow-close'>
    <div class='popup-box'>
      <img class='img-confitte' src='../layout/images/confetti.png' alt=''>
      <h2>$msg</h2>
      <img src='../layout/images/check-mark.png' alt=''>
      <p>يمكنك تسجيل الدخول للحساب.</p>
      <a href='#'  class='js-close-popup'>تسجيل الدخول</a>
    </div>
  </div>";
  unset($_SESSION['verifyMessage']);
}


?>

<div class="log-reg-page log-page center-flex">
  <div class="p-0 mt-30 mb-30 rad-10">
    <div class="head p-10 center-flex">
      <div class="platform-logo-links">
        <a href="./../index.php" class="logo">ReStO</a>
        <div class="icons between-flex mt-20">
          <a class="fb" href="#"><i class="fa-brands fa-facebook-f fa-fw "></i></a>
          <a class="gl" href="#"><i class="fa-brands fa-google fa-fw "></i></a>
          <a class="tw" href="#"><i class="fa-brands  fa-twitter fa-fw "></i></a>
        </div>
      </div>
    </div>
    <div class="content p-30 bg-white">
      <?php
      if(isset($loginSuccess)){
        echo "<div class='my-alert error' style='margin-top:-10px'>
              $loginSuccess
              </div>";
      }
      ?>
      <h2 class="fs-18 p-relative pb-5 mb-30 txt-c-mobile">تسجيل دخول لحساب مطعم</h2>
      <form action=<?=$_SERVER['PHP_SELF']?> method="POST" id="res-log" novalidate>
        <!-- Email -->
        <div class="mb-20">
          <label for="" class="form-label req">البريد الإلكتروني</label>
          <input type="email" name="email" class="form-control" value=<?=isset($email)?$email:""?>>
          <small class="fs-13 c-orang email-msg"></small>
        </div>
        <!-- Password -->
        <div>
          <label for="" class="form-label req">كلمة السر</label>
          <div class="p-relative">
            <i class="fa fa-eye eye"></i>
            <input type="password" class="form-control" name="pass">
          </div>
          <small class="fs-13 c-orang pass-msg"></small>
        </div>
        <!-- Submit Button -->
        <div class="span-2 center-flex mt-20 mb-5">
          <input type="submit" value="تسجيل الدخول" class="btn rad-6 bg-green c-white">
        </div>
      </form>
      <div class="fs-13 c-black txt-c"> ليس لديك حساب؟<a href="restRegister.php" class="mr-5">تسجيل حساب</a></div>
    </div>
  </div>
</div>
<?php 
$jsFiles = ["restAccount.js"];
include '../includes/templates/footer.php';
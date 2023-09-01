<?php 
SESSION_START();
$pageTitle = "حساب مستخدم | Resto";
$css = "../layout/css/";
$js = "../layout/js/";
$func = "../includes/functions/";
$libs = "../includes/libraries/";
$cssFiles = ["user_account/loginRegister.css"];
include "../init.php";

// variable to which page to show login or register
$isLoginPage = isset($_GET['do']);
if($_SERVER['REQUEST_METHOD'] == 'POST'){
  if(isset($_POST['register'])){
    // Register request
    // get entered data
    $email = $_POST['email'];
    $pass = $_POST['pass'];

    $hashedPassword = sha1($pass);
    // Create verify token
    $token = md5(rand());
    $stmt = $con->prepare("INSERT INTO customer(email,name,password,verify_token) VALUES(:email,:name,:pass,:token)");
    $stmt->execute([
      "name" => strstr($email,"@",true),
      "email" => $email,
      "pass" => $hashedPassword,
      "token" => $token
    ]);

    $registerIsFailed = false;
    if($stmt->rowCount() > 0){
      // send verify code
      // TODO: uncomment these three line after testing is finished
      include $func . "mail.php";
      $faildMessage = sendVerifyToken($email,$token,"USER");
      if($faildMessage !== false) {
        $registerIsFailed = "<br>فشل إرسال رابط التأكيد لبريدك الإلكتروني، تحقق من الاتصال بالإنترنت<br> $faildMessage, line: " . __LINE__ . "<br>";
      }
    }else{
      $registerIsFailed = "<br>Failed to send insert user data : insert query failed, line: " . __LINE__ . "<br>";
    }
    
    if(!$registerIsFailed){
      // registeration successed: show success msg
      ?>
<div class="popup-overlay allow-close">
  <div class="popup-box">
    <img class="img-confitte" src="../layout/images/confetti.png" alt="">
    <h2>تم التسجيل بنجاح</h2>
    <img src="../layout/images/mail.jpg" alt="">
    <p>لقد قمنا بإرسال رابط لتأكيد الحساب إلى بريدك الالكتروني يرجي التحقق منه.</p>
    <a href="./registerLogin.php?do=login">تسجيل الدخول</a>
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
    <a href="" class="js-close-popup">Return</a>
  </div>
</div>
<?php
    }
  }else if(isset($_POST['login'])){
    $isLoginPage = true;
    // Login request
    // get entered data
    $email = $_POST['email'];
    $password = $_POST['pass'];
    $hashedPassword = sha1($password);

    // check if account exist
    $stmt = $con->prepare("SELECT * FROM customer WHERE email=? AND password=?");
    $stmt->execute([$email,$hashedPassword]);

    if($stmt->rowCount() > 0){
      // Account exist, check if it confirmed
      $user = $stmt->fetch();
      $status = $user['account_status'];
      if($status){
        $userInfo = [
          'id' => $user['id'],
          "name" => $user['name'],
          "email" => $user['email'],
          "phone" => $user['phone'],
          "image" => getImagePath($user['image'],"USER_PROFILE"),
          "is_admin" => $user['is_admin'],
          "account_status" => $user['account_status'],
          'created_at' => $user['created_at'],
          'address' => $user['address']
        ];
        if($user['is_admin']){
          $_SESSION['admin'] = $userInfo;
          header('Location:../dashboard/dashboard.php');
          exit();
        }else{
          // add user info to session
          $_SESSION['user'] = $userInfo;
          header('Location:dashboard/userDashboard.php');
          exit();
        }
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
}
if(isset($_SESSION['verifyMessage'])){
  $msg = $_SESSION['verifyMessage'];
  if($isLoginPage){
    echo "
    <div class='popup-overlay allow-close'>
      <div class='popup-box'>
        <img class='img-confitte' src='../layout/images/confetti.png' alt=''>
        <h2>$msg</h2>
        <img src='../layout/images/check-mark.png' alt=''>
        <p>يمكنك تسجيل الدخول للحساب.</p>
        <a href='#' class='js-close-popup'>تسجيل الدخول</a>
        </div>
        </div>";
      }else{
        echo "
        <div class='popup-overlay allow-close'>
        <div class='popup-box'>
        <h2>$msg</h2>
        <img src='../layout/images/shield-with-x-mark.jpg' alt=''>
        <p>يرجى التحقق من الرابط المرسل أو إعادة تسجيل الحساب</p>
        <a href='#' class='js-close-popup'>تسجيل الدخول</a>
      </div>
    </div>";
  }
  unset($_SESSION['verifyMessage']);
}

?>
<div class="user-logreg-page">
  <div class="fixed-background"></div>
  <div class="container">
    <input type="checkbox" id="flip" <?= $isLoginPage?"checked":"";?> />
    <div class="cover">
      <div class="face front">
        <div class="platform-logo-links">
          <a class="logo" href="../index.php">ReStO</a>
          <div class="icons">
            <a class="fb" href="#"><i class="fa-brands fa-facebook-f fa-fw"></i></a>
            <a class="gl" href="#"><i class="fa-brands fa-google fa-fw"></i></a>
            <a class="tw" href="#"><i class="fa-brands fa-twitter fa-fw"></i></a>
          </div>
        </div>
      </div>
      <div class="face back">
        <div class="platform-logo-links">
          <a class="logo" href="../index.php">ReStO</a>
          <div class="icons">
            <a class="fb" href="#"><i class="fa-brands fa-facebook-f fa-fw"></i></a>
            <a class="gl" href="#"><i class="fa-brands fa-google fa-fw"></i></a>
            <a class="tw" href="#"><i class="fa-brands fa-twitter fa-fw"></i></a>
          </div>
        </div>
      </div>
    </div>
    <div class="forms">
      <div class="form-content">
        <div class="login-form">
          <h2>تسجيل دخول</h2>
          <form id="user-login" action=<?=$_SERVER['PHP_SELF']?> method="POST" novalidate>
            <input type="hidden" name="login" />
            <div class="inputs">
              <div>
                <?php
                    if(isset($loginSuccess)){
                      echo "<div class='my-alert error' style='margin-top:-10px'>
                            $loginSuccess
                            </div>";
                    }
                  ?>
                <input name="email" type="text" placeholder=" ادخل بريدك الالكتروني"
                  value="<?=isset($_POST['email'])?$_POST['email']:""?>" required />
                <small class="log-email-msg"></small>
                <div class="p-relative">
                  <i class="fa fa-eye eye"></i>
                  <input name="pass" type="password" placeholder=" ادخل كلمة المرور" required />
                </div>
                <small class="log-pass-msg"></small>
              </div>
            </div>
            <a href="#">نسيت كلمة المرور؟</a>
            <input class="submit-btn" type="submit" value="إرسال" />
            <div class="link">
              لا تملك أي حساب؟ <label for="flip">اشترك الآن</label>
            </div>
          </form>
        </div>
        <div class="signup-form">
          <h2>تسجيل حساب جديد</h2>
          <form id="user-register" action=<?=$_SERVER['PHP_SELF']?> method="POST" novalidate>
            <input type="hidden" name="register" />
            <div class="inputs">
              <div>
                <input name="email" type="text" placeholder="ادخل بريدك الالكتروني" required />
                <small class="reg-email-msg"></small>
                <input name="pass" type="password" placeholder="ادخل كلمة المرور" required
                  oninput="checkStrength(this.value,$())" />
                <div class="strength-bar">
                  <div class="strength"></div>
                </div>
                <div class="pass-msg-tags">
                  <!-- <div class="pass-msg-tag">8 احرف</div> -->
                </div>
                <small class="reg-pass-msg"></small>
                <input name="rePass" type="password" placeholder=" تأكيد كلمة المرور" required />
                <small class="reg-rePass-msg"></small>
              </div>
            </div>
            <input class="submit-btn" type="submit" value="إرسال" />
            <div class="link">
              هل لديك حساب مسبقاً؟<label for="flip"><span>سجّل دخول الآن </span></label>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>

<?php
$jsFiles = ["userAccount.js"];
include '../includes/templates/footer.php';
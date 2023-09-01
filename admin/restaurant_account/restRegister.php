<?php 
session_start();
// Register to Restaurant Account Page
$pageTitle = "Resto | تسجيل حساب مطعم";
$css = "../layout/css/";
$js = "../layout/js/";
$func = "../includes/functions/";
$libs = "../includes/libraries/";
$cssFiles = ["restaurant_account/loginRegister.css"];
include "../init.php";
// Get states from database to fill select input
$stmt = $con->prepare("SELECT * FROM state");
$stmt->execute([]);
if($stmt->rowCount() > 0){
  $states = $stmt->fetchAll();
}else{
  echo " مدري ايش السبب ماعم يحسن يجيب المحافظات من قاعدة البيانات بالسطر: " . __LINE__;
  exit();
}

if(isset($_SESSION['verifyMessage'])){
  $msg = $_SESSION['verifyMessage'];
  echo "
  <div class='popup-overlay allow-close'>
    <div class='popup-box'>
      <h2>$msg</h2>
      <img src='layout/images/check-mark.png' alt=''>
      <p>يرجى التحقق من الرابط المرسل أو إعادة تسجيل الحساب</p>
      <a href='#' class='js-close-popup'>تسجيل الدخول</a>
    </div>
  </div>";
  unset($_SESSION['verifyMessage']);
}
?>

<div class="log-reg-page center-flex">
  <div class="container p-0 mt-30 mb-30 rad-10">
    <div class="head p-15 center-flex">
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
      <h2 class="fs-18 p-relative pb-5 mb-30 txt-c-mobile">تسجيل حساب مطعم</h2>
      <form action=<?=$_SERVER['PHP_SELF']?> method="POST" id="res-reg" class="grid-form" enctype="multipart/form-data"
        novalidate>
        <!-- Restaurant Name -->
        <div>
          <label for="" class="form-label req">اسم المطعم</label>
          <input class="form-control" type="text" name="restName">
          <small class="name-msg"></small>
        </div>
        <!-- Restaurant describtion -->
        <div>
          <label for="" class="form-label">وصف المطعم</label>
          <textarea name="description" class="form-control" rows="1"></textarea>
        </div>
        <!-- Address Selects -->
        <h3 class="fs-15 span-2 m-0 mt-2">
          العنوان الرئيسي
          <span class="title mr-5"
            data-title="عنوان الفرع الرئيسي في حال وجود أكثر من فرع للمطعم. يمكنك إضافة فروع أخرى عند قبول حسابك بالمنصة.">
            <i class="fa fa-circle-info"></i>
          </span>
        </h3>
        <div class="d-flex gap-10">
          <div class="flex-1">
            <label for="" class="form-label req">المحافظة</label>
            <!-- <input class="form-control" name="state" type="text"> -->
            <Select class="form-control" name="state">
              <option value="" selected disabled hidden></option>
              <?php
                foreach($states as $state){
                  echo "<option value='{$state['id']}'>{$state['name']}</option>";
                }
                ?>
            </Select>
            <small class="state-msg"></small>
          </div>
          <div class="flex-1">
            <label for="" class="form-label req">المنطقة</label>
            <input class="form-control" name="region" type="text">
            <!-- <Select class="form-control" name="region">
                <option value="1">op1</option>
                <option value="2">op2</option>
            </Select> -->
            <small class="region-msg"></small>
          </div>
        </div>
        <div>
          <label for="" class="form-label req">الشارع</label>
          <input class="form-control" type="text" name="street">
          <small class="street-msg"></small>
        </div>
        <!-- Radio Inputs -->
        <div class="radio-input mt-4 mb-4">
          <div id="fastFoodRadio" class="d-flex align-center">
            <label for="">
              <span class="req">مطعم أم محل
                <span class="title" data-title="هل توفّر استضافة للزبائن ضمن المطعم أم محل للطلبات الخارجية فقط؟">
                  <i class="fa fa-circle-info"></i>
                </span>
            </label>
            <div class="radioBtn-js btn-group ltr">
              <div class="notActive btn btn-primary btn-sm" data-radio="fastfood" data-value="0">
                مطعم
              </div>
              <input id="fastfood" type="hidden" name="fastfood" value="">
              <div class="btn btn-primary btn-sm notActive" data-radio="fastfood" data-value="1">
                محل
              </div>
            </div>
          </div>
          <small class="fastfood-msg"></small>
          <div id="reserveRadio" class="d-flex align-center mt-2">
            <label for="">
              <span class="req">خدمة الحجز</span>
            </label>
            <div class="radioBtn-js btn-group ltr">
              <div class="btn btn-primary btn-sm notActive" data-radio="reserv" data-value="1">
                متوفرة
              </div>
              <input id="reserv" type="hidden" name="reserv" value="">
              <div class="btn btn-primary btn-sm notActive" data-radio="reserv" data-value="0">
                غير متوفرة
              </div>
            </div>
          </div>
          <small class="reserv-msg"></small>
          <div class="d-flex align-center mt-2">
            <label for="">
              <span class="req">خدمة التوصيل</span>
            </label>
            <div class="radioBtn-js btn-group ltr">
              <div class="btn btn-primary btn-sm notActive" data-radio="delivery" data-value="1">
                متوفرة
              </div>
              <input id="delivery" type="hidden" name="delivery" value="">
              <div class="btn btn-primary btn-sm notActive" data-radio="delivery" data-value="0">
                غير متوفرة
              </div>
            </div>
          </div>
          <small class="delivery-msg"></small>
        </div>
        <!-- Open/Close Time Input -->
        <div class="mt-10">
          <h3 class="fs-15 mb-20"><span class="req">ساعات الدوام</span></h3>
          <div class="row mr-20">
            <label for="" class="col-2 form-label">من</label>
            <input class="flex-0 form-control" type="time" name="openTime">
          </div>
          <div class="row mt-3  mr-20">
            <label for="" class="col-2 form-label">إلى</label>
            <input class="col flex-0 form-control" type="time" name="closeTime">
          </div>
          <small class="workHour-msg mt-10 d-block"></small>
        </div>
        <!-- Email -->
        <div>
          <label for="" class="form-label req">البريد الإلكتروني</label>
          <input type="email" name="email" class="form-control">
          <small class="email-msg"></small>
        </div>
        <!-- Phone -->
        <div>
          <label for="" class="form-label req">الرقم</label>
          <div class="phone-input p-relative ltr">
            <input type="text" name="phone" class="form-control" maxlength="8" oninput="onlyNums(this);">
            <span>09</span>
          </div>
          <small class="phone-msg"></small>
        </div>
        <!-- Password -->
        <div>
          <label for="" class="form-label req">كلمة المرور</label>
          <input type="password" class="form-control" name="pass" oninput="checkStrength(this.value,$())">
          <div class="strength-bar">
            <div class="strength"></div>
          </div>
          <div class="pass-msg-tags">
            <!-- <div class="pass-msg-tag">8 احرف</div> -->
          </div>
        </div>
        <!-- Confirm Password -->
        <div>
          <label for="" class="form-label req">تأكيد كلمة المرور</label>
          <input type="password" name="rePass" class="form-control">
        </div>
        <small class="pass-msg span-2"></small>
        <!-- Authentication Files (PDF || JPG || PNG) -->
        <h3 class="fs-15 span-2 m-0 mt-3">
          <span class="req">
            وثائق تأكيد الملكية
          </span>
        </h3>
        <!-- Authentication Files -->
        <div>
          <p>سند ملكية أو عقد أجار للمطعم أو أيّة أوراق رسمية تثبت ملكية المطعم.<br>يمكنك تحميل صور للمستندات (jpg ,
            png) أو ملف pdf.</p>
          <small class="files-msg mb-20 d-block"></small>
          <label class="file-upload-btn rad-10 btn-shape bg-orange c-white d-block txt-c fw-bold" for="auth-files">تحميل
            الوثائق</label>
          <input id="auth-files" name="authFiles[]" type="file" accept="image/png, image/jpeg , .pdf" hidden multiple>
        </div>
        <!-- Preview Authentication Files -->
        <div class="files-preview mt-2">
        </div>
        <!-- Submit Button -->
        <div class="span-2 center-flex mt-20 mb-5">
          <input type="submit" value="تسجيل الحساب" class="btn rad-6 bg-green c-white">
        </div>
      </form>
      <div class="fs-13 c-black txt-l txt-c-mobile">لديك حساب مسبقاً؟ <a href="restLogin.php" class="mr-5">تسجيل
          دخول</a></div>
    </div>
  </div>
</div>

<?php 
$jsFiles = ["restAccount.js"];
include '../includes/templates/footer.php';
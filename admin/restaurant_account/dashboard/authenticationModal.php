<?php
$filesPreviewContent = "";
if(count($authFiles) > 0){
  foreach($authFiles as $file){
    $path = $file['altPath'];
    $name = $file['name'];
    $filesPreviewContent .= "<div class='box rad-6 p-10'><img src='$path' alt='' /><span class='d-block txt-c mt-2 c-white fs-13'>$name</span></div>";
  }
}else{
  $filesPreviewContent =  "<small>لم يتم تحديد أية ملفات</small>";
}
?>
<div class="popup-overlay">
  <div class="popup-box--black">
    <header>
      <h2>الحساب قيد المراجعة </h2>
    </header>
    <main>
      <p>
        يتم الآن مراجعة الوثائق التي أرفقتها من أجل التأكد من صحة المعلومات
        التي أدخلتها عن المطعم، عند التحقق منها سيتم إعلامك بالنتيجة عبر
        البريد الإلكتروني.
      </p>
    </main>
    <footer>
      <p>هل ترغب بتعديل الوثائق التي أرفقتها مع معلومات المطعم؟</p>
      <div id="edit-auth-result-msg"></div>
      <div class="edit-auth-files">
        <button class="popup-btn js-edit-auth" type="button">
          تعديل الوثائق
        </button>
        <div class="edit-auth-form d-none">
          <div class="files-preview">
            <?=$filesPreviewContent?>
            <!-- <small>لم يتم تحديد أية ملفات</small> -->
          </div>
          <form id="edit-auth-form" enctype="multipart/form-data">
            <label class="popup-btn" for="edit-auth-input">تحميل الوثائق</label>
            <button type="submit" class="popup-btn disable">حفظ</button>
            <button type="button" class="popup-btn-alt js-cancel-edit-auth">
              إلغاء
            </button>
            <input name="restID" type="hidden" value="<?=$restID?>" />
            <input id="edit-auth-input" name="authFiles[]" type="file" accept="image/png, image/jpeg , .pdf" hidden
              multiple />
          </form>
        </div>
      </div>
    </footer>
    <div class="links">
    <a href="./../../index.php">Resto</a>
    <a href="./../../signOut.php?do=REST">تسجيل الخروج</a>
    </div>
  </div>
</div>
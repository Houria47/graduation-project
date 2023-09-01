<h2 class="tab-title">
  <span>الحساب</span>
</h2>
<main data-userID="<?=$userID?>" class="cards" data-userImage="<?=$user['image']?>">
  <section class="welcome my-card">
    <div class="head">
      <div>
        <h2 class="m-0">مرحباً</h2>
        <p data-userName class="c-gray mt-10"><?=$user['name']?></p>
      </div>
      <img src="../../layout/images/restaurant-team.png" />
    </div>
    <img data-userImage class="rad-half avater" src="<?=$user['image']?>" />
    <div class="cont">
      <div>
        <div data-userName><?=$user['name']?></div>
        <span><?=$user['email']?></span>
      </div>
      <div>
        <div data-userAddress class="fs-13"><?=empty($user['address'])?"لم يحدد":$user['address']?></div>
        <span>العنوان</span>
      </div>
      <div>
        <div data-userNumber><?=empty($user['phone'])?"لم يضف":$user['phone']?></div>
        <span>الرقم</span>
      </div>
    </div>
  </section>

  <section class="my-card">
    <div class="component-title">معلومات الحساب</div>
    <form class="user-info" id="userInfo" enctype="multipart/form-data">
      <!-- name input -->
      <div class="form-input">
        <label for="name">الاسم</label>
        <input id="name" type="text" name="name" value="<?=$user['name']?>" disabled />
      </div>
      <!-- description input -->
      <div class="form-input">
        <label for="address">العنوان</label>
        <input id="address" type="text" name="address" value="<?=$user['address']?>"
          placeholder="المحافظة، المنطقة، الشارع" disabled />
      </div>
      <!-- email input -->
      <div class="form-input">
        <label for="email">البريد</label>
        <input id="email" type="text" name="email" value="<?=$user['email']?>" disabled />
      </div>
      <!-- phone input -->
      <div class="form-input">
        <label for="phone">الرقم</label>
        <input id="phone" type="text" name="phone" value="<?=$user['phone']?>" maxlength="10" disabled />
      </div>
      <div class="form-input">
        <label><img id="userImagePreview" src="<?=$user['image']?>" /></label>
        <label class="file-input" for="userImage"><span>تحميل صورة</span></label>
        <input id="userImage" type="file" hidden name="userImage" disabled />
      </div>
      <input type="hidden" name="userID" value="<?=$userID?>" />
      <div class="actions">
        <small></small>
        <button type="button" class="dash-btn" id="edit-user-info-btn">
          تعديل
        </button>
      </div>
    </form>
  </section>

  <section class="my-card">
    <div class="component-title">الإعدادات</div>
    <div class="setting">
      <p class="fs-14 fw-bold mb-10">كلمة المرور:</p>
      <div class="change-pass-form"></div>
      <button id="change-pass-btn" class="dash-btn w-full dash-btn-green txt-c">
        تغيير كلمة المرور
      </button>
    </div>
    <div class="setting">
      <p class="fs-14 fw-bold mb-10">حذف الحساب:</p>
      <div class="delete-my-account-form"></div>
      <div id="delete-myAccount" class="dash-btn dash-btn-red txt-c">
        حذف
      </div>
    </div>
  </section>
</main>
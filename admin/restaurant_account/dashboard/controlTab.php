<?php
// get states from db to fill select input
$states = getStates();
$status = $restaurant['account_status'];
// get profile images
$backImg = $restaurant['cover_image'];
$logoImg = $restaurant['profile_image'];
// rate content
$rate = $restaurant['ratesNum'] == 0? "لا يوجد تقييم":$restaurant['rate'];
$ratesNum = $restaurant['ratesNum'] == 0? "لم يقم أحد بتقييم المطعم": $restaurant['ratesNum'];
// description
$desc = empty(trim($restaurant['description']))
        ?["","لا يوجد وصف"]
        :[$restaurant['description'],""];
// services
$delivery = $restaurant['delivery_service'];
$reserv = $restaurant['reserv_service'];
$fastFood = $restaurant['fast_food'];
// work hours
$openTime = dateFormater($restaurant['open_time'],"hh:mm a");
$closeTime = dateFormater($restaurant['close_time'],"hh:mm a");
// addresses
$addresses = $restaurant['addresses'];
?>
<h2 class="tab-title">
  <span>لوحة التحكم</span>
</h2>
<main data-restID="<?=$restID?>" class="control-content">
  <div class="my-card restaurant-preview span-two-col">
    <header>
      <div class="back-image">
        <img id="back-img" src="<?=$backImg?>" />
        <i class="fas fa-edit" id="editBackImage"></i>
      </div>
      <div class="profile-name-img">
        <div class="logo-image">
          <img id="logo-img" src="<?=$logoImg?>" />
          <i class="fas fa-edit" id="editLogoImage"></i>
        </div>
        <div class="rest-title">
          <h3><?=$restaurant['name']?></h3>
          <div class="small-rate">
            <i class="fa fa-solid fa-star"></i>
            <span><?=$rate?></span>
          </div>
        </div>
      </div>
    </header>
    <form class="rest-info" id="restBasicInfo">
      <!-- name input -->
      <div class="form-input">
        <label for="name">الاسم</label>
        <input id="name" type="text" name="name" value="<?=$restaurant['name']?>" disabled />
      </div>
      <small class="name-msg"></small>
      <!-- description input -->
      <div class="form-input">
        <label for="desc">الوصف</label>
        <input id="desc" type="text" name="description" value="<?=$desc[0]?>" placeholder="<?=$desc[1]?>" disabled />
      </div>
      <small></small>
      <!-- email input -->
      <div class="form-input">
        <label for="email">البريد</label>
        <input id="email" type="text" name="email" value="<?=$restaurant['email']?>" disabled />
      </div>
      <small class="email-msg"></small>
      <!-- phone input -->
      <div class="form-input">
        <label for="phone">الرقم</label>
        <input id="phone" type="text" name="phone" value="<?=$restaurant['phone']?>" maxlength="10" disabled />
      </div>
      <small class="phone-msg"></small>
      <input type="hidden" name="restID" value="<?=$restID?>">
      <div class="actions">
        <button class="dash-btn" id="edit-rest-info-btn">تعديل</button>
      </div>
    </form>
  </div>
  <!-- Services Widget -->
  <div class="my-card services">
    <h3 class="component-title">الخدمات</h3>
    <p>
      عند التحويل من <em>معطم</em> لـ <em>محل</em> لن تتمكن من تفعيل
      خدمة الحجز.
    </p>
    <div id="servicesMsg"></div>

    <div class="fastfood-service">
      <div class="<?=$fastFood?"":"active"?>">
        <img src="./../../layout/images/restaurant.png" alt="" />
        معطم
      </div>
      <input id="fastfood" name="fastfood" type="checkbox" hidden <?=$fastFood?"checked":""?> />
      <div class="<?=!$fastFood?"":"active"?>">
        <img src="./../../layout/images/fast-food.png" alt="" />
        محل
      </div>
    </div>
    <div class="service <?=$fastFood?"disable":""?>">
      <i class="fas fa-bookmark"></i>
      <span>خدمة الحجز</span>
      <label for="reservation">
        <input id="reservation" name="reservation" class="toggle-checkbox" type="checkbox" hidden
          <?=$reserv?"checked":""?> />
        <div class="switch-toggle"></div>
      </label>
    </div>
    <div class="service">
      <i class="fas fa-shipping-fast"></i>
      <span>خدمة التوصيل</span>
      <label for="delivery">
        <input id="delivery" name="delivery" class="toggle-checkbox" type="checkbox" hidden
          <?=$delivery?"checked":""?> />
        <div class="switch-toggle"></div>
      </label>
    </div>
  </div>
  <!-- Work Time Widget -->
  <div class="my-card work-hours">
    <h3 class="component-title">أوقات العمل</h3>
    <h3 class="fs-14">المواعيد الحالية:</h3>
    <div class="mb-20">
      <i class="fa fa-solid fa-clock"></i>
      <span id="currentTimes">من <?=$openTime?> إلى <?=$closeTime?></span>
    </div>
    <h3 class="fs-14"> تعديل المواعيد:</h3>
    <form id="editWorkHours">
      <div class="row w-full flex-0 mt-10">
        <label class="col-2">من</label>
        <input class="col dash-input" type="time" name="openTime" />
      </div>
      <div class="row w-full flex-0 mt-10 mb-10">
        <label class="col-2">إلى</label>
        <input class="col dash-input" type="time" name="closeTime" />
      </div>
      <button type="submit" class="dash-btn">تعديل</button>
      <small id="workHour-msg"></small>
    </form>
  </div>
  <!-- Addresses Widget -->
  <div class="my-card span-two-col">
    <h3 class="component-title">العناوين</h3>
    <table id="restAddresses" class="addresses">
      <thead>
        <tr>
          <th>#</th>
          <th>المحافظة</th>
          <th>المنطقة</th>
          <th>الشارع</th>
        </tr>
      </thead>
      <tbody>
        <?php
        if(!empty($addresses)){
          $firstAddress = true;
          foreach($addresses as $address){
            $delOption = $firstAddress
            ? ""
            :'<i data-deleteAddress class="fas fa-trash del"></i>';
            echo <<< "addressRow"
            <tr data-addressRowID="{$address['id']}">
              <td>
                $delOption
                <i data-editAddress class="fas fa-edit"></i>
              </td>
              <td data-stateID="{$address['state_id']}">{$address['state']}</td>
              <td data-region>{$address['region']}</td>
              <td data-street>{$address['street']}</td>
            </tr>
            addressRow;
            $firstAddress = false;
          }
        }else{
          echo '<tr><td colspan="5">لم تتم إضافة عناوين</td></tr>';
        }
        ?>
      </tbody>
    </table>
    <h4 class="add-add-title mt-5">أضف عنوان</h4>
    <form id="add-address" class="addres-from">
      <div class="inputs">
        <select class="w-full" name="state">
          <option value="" selected disabled hidden></option>
          <?php
          foreach($states as $state){
            echo <<< "stateOption"
            <option value="{$state['id']}">{$state['name']}</option>
            stateOption;
          }
          ?>
        </select>
        <input name="region" class="dash-input" type="text" />
        <input name="street" class="dash-input" type="text" />
      </div>
      <button class="dash-btn dash-btn-green" type="submit">
        إضافة
      </button>
      <small></small>
    </form>
  </div>
  <!-- Rates Widget -->
  <div class="my-card rate">
    <h3 class="component-title">التقييمات</h3>
    <div class="rate-content">
      <div class="rate-numbers">
        <div class="rate-percentage"><?=$restaurant['rate']?></div>
        <div class="ratings">
          <div class="empty-stars"></div>
          <div class="full-stars" style="width: <?=$restaurant['ratePercenatge']?>%"></div>
        </div>
        <div class="rated-users-num"><?=$ratesNum?></div>
      </div>
      <div class="rates-chart">
        <div class="rate-bar">
          <span>1</span>
          <div class="rate-bar--inner">
            <div class="rate-bar--fill" style="width: <?=$restaurant['ratesLevelsPercentage'][0]?>%"></div>
          </div>
        </div>
        <div class="rate-bar">
          <span>2</span>
          <div class="rate-bar--inner">
            <div class="rate-bar--fill" style="width: <?=$restaurant['ratesLevelsPercentage'][1]?>%"></div>
          </div>
        </div>
        <div class="rate-bar">
          <span>3</span>
          <div class="rate-bar--inner">
            <div class="rate-bar--fill" style="width: <?=$restaurant['ratesLevelsPercentage'][2]?>%"></div>
          </div>
        </div>
        <div class="rate-bar">
          <span>4</span>
          <div class="rate-bar--inner">
            <div class="rate-bar--fill" style="width: <?=$restaurant['ratesLevelsPercentage'][3]?>%"></div>
          </div>
        </div>
        <div class="rate-bar">
          <span>5</span>
          <div class="rate-bar--inner">
            <div class="rate-bar--fill" style="width: <?=$restaurant['ratesLevelsPercentage'][4]?>%"></div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- Reviews Widget -->
  <div class="my-card revivews-card">
    <h3 class="component-title">الآراء</h3>
    <div class="reviews-num">
      <?php
      $reviewersCount = count($restaurant['reviews']);
      echo $reviewersCount == 0
      ? "لم يقم أحد بإبداء رأي عن المطعم."
      :($reviewersCount == 2
      ?"يوجد $reviewersCount شخص أضافوا تعليقهم على المطعم."
      :($reviewersCount >= 3
      ?"يوجد $reviewersCount أشخاص أضافوا تعليقهم على المطعم."
      :"يوجد $reviewersCount شخص ضاف تعليقه على المطعم."));
      ?>
    </div>
    <ul class="reviews">
      <?php
      if(!empty($restaurant['reviews'])){
        foreach($restaurant['reviews'] as $review){
          echo <<< "review"
            <li class="review">
            <img
              class="user-img"
              src="{$review['reviewer_image']}"
            />
            <div class="user-info">
              <h4>{$review['reviewer_name']}</h4>
              <p>{$review['review']}</p>
            </div>
            <div class="small-rate">
              <i class="fa fa-solid fa-star"></i>
              <span>{$review['given_rate']}</span>
            </div>
           </li>
          review;
        }
      }else{
        echo '<li class="no-reviews">لا يوجد تعليقات</li>';
      }
      ?>
    </ul>
  </div>
  <!-- Setting Widget -->
  <div class="my-card">
    <div class="component-title">إعدادت الحساب</div>
    <p class="fs-14 fw-bold mb-10">كلمة المرور:</p>
    <div class="change-pass-form"></div>
    <button id="change-pass-btn" data-restID="<?=$restID?>" class="dash-btn w-full dash-btn-green txt-c">
      تغيير كلمة المرور
    </button>
    <p class="fs-14 fw-bold mb-10 mt-20">حذف الحساب:</p>
    <div class="delete-my-rest-form"></div>
    <div id="delete-myRest" data-restID="<?=$restID?>" class="dash-btn dash-btn-red txt-c">
      حذف
    </div>
  </div>
</main>


<!-- add javaScript file -->
<?php
$jsFiles[] = "controlTab.js";
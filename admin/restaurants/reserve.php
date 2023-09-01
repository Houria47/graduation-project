<?php

SESSION_START();

$restID =  $_GET['restID'];

$pageTitle = "حجز بمطعم | Resto";
$needsNav = true;
$needsFooter = true;
// files directories
$css = "../layout/css/";
$js = "../layout/js/";
$func = "../includes/functions/";
$cssFiles = ["restaurants/reserve.css"];

include "../init.php";

$restaurant = getRestaurantsInfo($restID);

if(!isset($_SESSION['user'])){
  // no session! get the f out of here
  header("Loaction:../index.php");
  exit();
}

$menus = getRestaurantMenus($restID);

?>
<main class="reserve-page" data-restID="<?=$restaurant['id']?>">
  <form id="reserve-form" class="mycontainer">
    <section class="reserve-info box">
      <div class="wellcome">
        <div class="overlay"></div>
        <div class="content">
          <p>أهلاً بك في مطعم</p>
          <a href="./restProfile.php?id=<?=$restaurant['id']?>"><?=$restaurant['name']?></a>
          <p>احجز الآن مع عائلتك والأصدقاء</p>
        </div>
      </div>
      <div class="reserve-inputs">
        <div class="input">
          <label for="name">الاسم</label>
          <input id="name" name="name" type="text" value="<?=$_SESSION['user']['name']?>" />
          <small id="name-msg"></small>
        </div>
        <div class="input">
          <label for="phone">رقم التواصل</label>
          <input id="phone" name="phone" type="text" oninput="onlyNums(this)" maxlength="10"
            value="<?=$_SESSION['user']['phone']?>" />
          <small id="phone-msg"></small>
        </div>
        <div class="input">
          <label for="guestsNum">عدد الضيوف</label>
          <input id="guestsNum" name="guestsNum" type="number" oninput="onlyNums(this)" value="" />
          <small id="guestsNum-msg"></small>
        </div>

        <div class="input">
          <label for="date">موعد الحجز</label>
          <input id="date" name="date" type="datetime-local" value="" />
          <small id="date-msg"></small>
        </div>
        <div class="input">
          <label for="notes">ملاحظات</label>
          <textarea name="notes" name="" id="notes" rows="3"></textarea>
        </div>
      </div>
    </section>
    <?php if(count($menus) > 0){?>
    <section class="recipes-list">
      <div class="order">
        <h2 class="my-title">قائمة الطلبات</h2>
        <div class="box">
          <div class="order-now">
            <p>
              أضف الوجبات التي تود تناولها عند قدومك
              <i class="fas fa-arrow-left"></i>
            </p>
            <img src="./../layout/images/take-order.png" alt="" />
          </div>
          <table id="order">
            <thead>
              <tr>
                <th>#</th>
                <th>الوجبة</th>
                <th>السعر</th>
                <th>الكمية</th>
                <th>المجموع</th>
              </tr>
            </thead>
            <tbody>
              <tr class="no-items">
                <td colspan="5" class="p-10">
                  لم تتم إضافة طلبات
                </td>
              </tr>
            </tbody>
          </table>
          <div class="between-flex total">
            <h3>الإجمالي</h3>
            <div>
              <span data-total class="num">0</span>
              <span class="unit">ل.س</span>
            </div>
          </div>
        </div>
      </div>
      <div class="menu">
        <h2 class="my-title">قائمة المطعم</h2>
        <div class="list box">
          <?php
        foreach($menus as $menu){
          if(count($menu['recipes']) == 0) continue;
          if($menu['is_default']){
            echo "<h3 class='menu-title'>قائمة {$restaurant['name']}</h3>";
          }else{
            echo "<h3 class='menu-title'>{$menu['name']}</h3>";
          }
          foreach($menu['recipes'] as $recipe){
            $imgPath = getImagePath($recipe['image'],"REST_RECIPE");
            $price = number_format($recipe['price']);
            if($recipe['discount_price'] != null){
              $price = number_format($recipe['discount_price']);

            }
            echo <<< "recipe"
            <div class="item">
              <div class="img-box" data-recipeID='{$recipe['id']}'>
                <div class="check"><i class="fas fa-check"></i></div>
                <img src="$imgPath" />
                <div class="price">$price</div>
              </div>
              <a href="./../menus_recipes/recipePreview.php?recipeID={$recipe['id']}">{$recipe['name']}</a>
            </div>
            recipe;
          }
        }
        ?>
        </div>
      </div>
    </section>
    <?php }?>
    <button class="submit-btn">احجز</button>
  </form>
</main>
<?php
$jsFiles = ["reserve.js"];
include '../includes/templates/footer.php';
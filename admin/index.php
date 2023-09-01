<?php 
session_start();
$pageTitle = "الصفحة الرئيسية | Resto";
$needsNav = true;
$needsFooter = true;
$cssFiles = ["index.css"];
$jsFiles = ["index.js"];

include "init.php";

// handel messages for account deletion
if(isset($_SESSION['restaurantHasBeenDeleted'])){
  ?>
<div class='popup-overlay allow-close'>
  <div class='popup-box txt-c p-15'>
    <h2 class="fs-18 mb-20 c-black">تم حذف حساب المطعم الخاص بك!</h2>
    <!-- <img style='width:150px' src="" alt=''> -->
    <p class="fs-15 mb-30">يمكنك تسجيل حساب مطعم جديد متى ما أردت.</p>
    <a href='./restaurant_account/restRegister.php' class='js-close-popup c-white bg-green rad-6 btn-shape'>تسجيل حساب
      مطعم</a>
  </div>
</div>
<?php
  unset($_SESSION['restaurantHasBeenDeleted']);
}
if(isset($_SESSION['userHasBeenDeleted'])){
  ?>
<div class='popup-overlay allow-close'>
  <div class='popup-box txt-c p-15'>
    <h2 class="fs-18 mb-20 c-black">تم حذف حسابك!</h2>
    <!-- <img style='width:150px' src="" alt=''> -->
    <p class="fs-15 mb-30">يمكنك تسجيل حساب جديد متى ما أردت.</p>
    <a href='./user_account/registerLogin.php' class='js-close-popup c-white bg-green rad-6 btn-shape'>تسجيل حساب</a>
  </div>
</div>
<?php
  unset($_SESSION['userHasBeenDeleted']);
}

// get top restaruants
$topRests = getTopRatedRests();

// get recipes with discount
$topRecipes = getTopRecipes();

// get recipes on sale
$recipesOnSale = getRecipesOnSale();

?>
<!-- Start Header Section -->
<section class="header-section">
  <div class="mycontainer">
    <ul class="links col-md-3">
      <li>
        <a href="./menus_recipes/recipes.php?do=top#recipesList"><i class="fas fa-star"></i> الوجبات الأعلى تقييماّ</a>
      </li>
      <li>
        <a style="animation-delay: 0.1s" href="./menus_recipes/recipes.php?do=delivary#recipesList"><i
            class="fas fa-truck-fast"></i> وجبات مع توصيل</a>
      </li>
      <li>
        <a style="animation-delay: 0.2s" href="#sales"><i class="fas fa-bowl-food"></i> وجبة & حسم</a>
      </li>
      <li>
        <a style="animation-delay: 0.3s" href="./restaurants/restaurants.php?do=top#seperator"><i
            class="fas fa-star"></i> المطاعم الاعلى تقييماّ</a>
      </li>
      <li>
        <a style="animation-delay: 0.4s" href="./restaurants/restaurants.php?do=reserv#restsList"><i
            class="fas fa-bookmark"></i>
          مطاعم مع حجز</a>
      </li>
    </ul>
    <div class="header-slider" id="js-header-sl">
      <div class="slide">
        <img class="background" src="./layout/images/index-media/table-with-recipes.jpg" />
        <div class="content">
          <p>تعرّف على اشهى الأطباق والوجبات في مختلف المطاعم والمحلات</p>
          <a href="./menus_recipes/recipes.php#recipesList"><i class="fas fa-truck-fast"></i>عرض الوجبات</a>
        </div>
      </div>
      <div class="slide">
        <img class="background" src="./layout/images/index-media/delivary-guy.jpg" />
        <div class="content">
          <p>أعجبك طبق ما؟ يمكنك طلب وجلب ماتشتهي في أي وقت</p>
          <a href="./menus_recipes/recipes.php#recipesList"><i class="fas fa-bowl-food"></i>عرض الوجبات</a>
        </div>
      </div>
      <div class="slide">
        <img class="background" src="./layout/images/index-media/tablesjpg.jpg" />
        <div class="content">
          <p>
            يمكنك الحجز في مطعمك المفضلك وتحديد طلبك ليكون جاهزاً حين وصولك
          </p>
          <a href="./restaurants/restaurants.php?do=reserv#restsList"><i class="fas fa-bookmark"></i> مطاعم توفر حجز</a>
        </div>
      </div>
      <div class="slide">
        <img class="background" src="./layout/images/index-media/rating.jpg" />
        <div class="content">
          <p>
            شارك رأيك بالوجبات والمطاعم وتفاعل مع الخدمات والعروض المميزة..
          </p>
          <a href="./ads/posts.php"><i class="fas fa-ad"></i> الخدمات والعروض</a>
        </div>
      </div>
    </div>
    <div class="side-imgs">
      <div class="img-box">
        <img src="./layout/images/index-media/tiktak3.jpg" alt="" />
      </div>
      <div class="img-box">
        <img src="./layout/images/index-media/1مهروسة مطعمn.jpg" alt="" />
      </div>
    </div>
  </div>
</section>
<!-- End Header Section -->
<!-- Start Brands Section -->
<section class="brands-section" id="js-brands-sl">
  <?php
  foreach($topRests as $rest){
    echo <<< "topRest"
    <a href="./restaurants/restProfile.php?id={$rest['id']}" class="brand-item">
      <img src="{$rest['profile_image']}" alt="" />
      <h2>{$rest['name']}</h2>
    </a>
    topRest;
  }
  ?>

</section>
<!-- End Brands Section -->
<!-- Start Recipes Section -->
<section class="recipes-section mycontainer">
  <div class="section-title">أشهى الأطباق</div>
  <ul id="js-recipes-sl">
    <?php
    foreach($topRecipes as $recipe){
      $price = number_format($recipe['price']);
      if($recipe['discount'] != null)
        $price = number_format($recipe['discount']);

      echo <<< "topRecipe"
      <li data-recipeID={$recipe['id']}>
        <div class="head">
          <h2>{$recipe['name']}</h2>
          <div class="ratings">
            <div class="empty-stars"></div>
            <div class="full-stars" style="width: {$recipe['ratePercenatge']}%"></div>
          </div>
        </div>
        <div class="img-box">
          <img src="{$recipe['image']}" alt="" />
        </div>
        <div class="footer">
          <div class="price">$price<span class="unit">ل.س</span></div>
        </div>
        <div class="overlay center-flex">
          <div>
            <p>{$recipe['description']}</p>
            <div class="actions">
              <button data-add-to-cart class="fa fa-cart-plus"></button>
              <button data-rate-recipe class="fa fa-star"></button>
              <a href="./menus_recipes/recipePreview.php?recipeID={$recipe['id']}" class="fa fa-bowl-food" title="عرض الوجبة"></a>
            </div>
          </div>
        </div>
      </li>
      topRecipe;
    }
    ?>
  </ul>
</section>
<!-- End Recipes Section -->
<!-- Start User Section -->
<section class="rigster-section user p-relative">
  <div class="mycontainer">
    <div>
      <h2>اشترك الآن</h2>
      <p>
        تمكنك منصة Resto استعراض الآراء حول الوجبات والمطاعم لتساعدك في
        اختيار ما هو مناسب لك، ويمكنك مشاركة رأيك الخاص لتساهم في تقييم
        الخدمات التي تقدمها المطاعم
      </p>
      <a href="./user_account/registerLogin.php">تسجيل حساب</a>
    </div>
    <img src="./layout/images/index-media/girl-order-food-from-mobile-app.png" alt="" />
  </div>
  <div id="sales" style="bottom:35px; position:absolute"></div>
</section>
<!-- End User Section -->
<!-- Start Sales Section -->
<section class="sales-section mycontainer">
  <div class="section-title">أحدث العروض</div>
  <ul>
    <?php
    $cnt = 4;
    foreach($recipesOnSale as $recipe){
      if(!$cnt--)
        break;
      $price = number_format($recipe['price']);
      $discount = number_format($recipe['discount']);
      
      echo <<< "recipeOnSale"
      <li>
        <img src="{$recipe['image']}" alt="" />
        <div class="content">
          <div class="price">
            <span>$discount</span>
            <span>$price</span>
          </div>
          <p>{$recipe['description']}</p>
          <a href="./menus_recipes/recipePreview.php?recipeID={$recipe['id']}">
            <span class="fas fa-bowl-food"></span>
            {$recipe['name']}
            <i class="fa fa-arrow-right"></i>
          </a>
          <a href="./restaurants/restProfile.php?id={$recipe['restaurant']['id']}">
            <span class="fas fa-house"></span>
            {$recipe['restaurant']['name']}
            <i class="fa fa-arrow-right"></i>
          </a>
        </div>
      </li>
      recipeOnSale;
    }
  ?>
  </ul>
</section>
<!-- End Sales Section -->
<!-- Start Restaurant Section -->
<section class="rigster-section rest">
  <div class="mycontainer">
    <img src="./layout/images/index-media/restautant-manage-online.png" alt="" />
    <div>
      <h2>شارك وجباتك وخدماتك!</h2>
      <p>
        من خلال تسجيل حساب لمطعمك بإمكانك إضافة خدماتك وقوائمك وإدارة
        الوجبات والترويج لها بسهولة لتصل لأكبر عدد من الزبائن، كما يمكنك
        معرفة رأي الزبائن بخدماتك ووجباتك وتقييمهم لها.
      </p>
      <a href="./restaurant_account/restRegister.php">تسجيل حساب مطعم</a>
    </div>
  </div>
</section>
<!-- End Restaurant Section -->
<?php
include $tpl . 'footer.php';
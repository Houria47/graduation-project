<?php 
SESSION_START();

$pageTitle = "الوجبات | Resto";
$needsNav = true;
$needsFooter = true;
$selectedTab = "recipes";

// files directories
$css = "../layout/css/";
$js = "../layout/js/";
$func = "../includes/functions/";
$cssFiles = ["recipes.css"];
include "../init.php";

// handle filter request
$filter = isset($_GET['do'])?$_GET['do']:"";

$allOrTop ="selected";
$top = "";
if($filter == "top"){
  $top = "selected";
$allOrTop ="";

}

$delivary = "";
if($filter == "delivary"){
  $delivary = "checked";
}
// get recipes from database
$recipes = getRecipes();
if($recipes  === false){
  $recipes = [];
}
$states = getStates(); 
?>
<main>
  <section class="meals-head">
    <p>ابحث <br />عن طلبك</p>
    <form id="searchRecipeForm">
      <input id="searchRecipeInput" name="searchText" type="search" placeholder="ابحث عن وجبة" />
      <button><i class="fas fa-search"></i></button>
    </form>
    <div id="recipesList" class="mt-7"></div>
  </section>
  <section class="mycontainer meals">
    <div class="sidebar">
      <h2>أدوات البحث</h2>
      <div class="filter">
        <label>السعر</label>
        <div class="range-wrapper">
          <div class="multi-range-slider">
            <input type="range" id="input-left" min="0" step="500" max="100000" value="10000" />
            <input type="range" id="input-right" min="0" step="500" max="250000" value="200000" />
            <div class="slider">
              <div class="track"></div>
              <div class="range"></div>
            </div>
          </div>
          <div class="price__wrapper">
            <span class="price-from"></span>
            <span class="price-to"></span>
          </div>
        </div>
      </div>
      <div class="filter">
        <label>المحافظة</label>
        <select id="stateFilter" name="state">
          <option value="-1">الكل</option>
          <?php
          foreach($states as $state){
              echo "<option value='{$state['id']}'>{$state['name']}</option>";
            }?>
        </select>
      </div>
      <div class="filter" id="rate-filter">
        <label>التقييم</label>
        <span class="<?=$allOrTop?> rate" data-val="-1"><i class="far fa-star"></i>الكل</span>
        <span class="rate" data-val="1"><i class="fas fa-star"></i>1</span>
        <span class="rate" data-val="2"><i class="fas fa-star"></i>2</span>
        <span class="rate" data-val="3"><i class="fas fa-star"></i>3</span>
        <span class="rate" data-val="4"><i class="fas fa-star"></i>4</span>
        <span class="rate" data-val="5"><i class="fas fa-star"></i>5</span>
      </div>
      <div class="filter">
        <label>الخدمات</label>
        <div class="services">
          <div>
            <input <?=$delivary?> id="delivary" type="checkbox" />
            <label for="delivary">مع خدمة توصيل</label>
          </div>
          <div>
            <input id="reserv" type="checkbox" />
            <label for="reserv">مع خدمة حجز</label>
          </div>
          <div>
            <input id="fastfood" type="checkbox" />
            <label for="fastfood">إمكانية الاستضافة ضمن المطعم</label>
          </div>
        </div>
      </div>
      <button id="searchRecipeBtn" class="search-btn" type="button">
        بحث
      </button>
    </div>
    <div class="content">
      <!-- TODO: handel no recipes in platform case -->
      <ul id="recipes-cards" class="recipes">
        <?php
        foreach($recipes as $recipe){
          if(!empty($top)){
            if($recipe['averageRate'] < 4) continue;
          }
          if(!empty($delivary)){
            if(!$recipe['restaurant']['delivery_service'])
            continue;
          }
          // reviews section
          $reviews = '';
          $reviewsNum = count($recipe['reviews']);
          if($reviewsNum > 0){
            foreach($recipe['reviews'] as $review){
              $reviews .= "<li>
              <div class='rate'>{$review['given_rate']}<i class='fas fa-star'></i></div>
              <img src='{$review['reviewer_image']}' alt='' />
              <h4>{$review['reviewer_name']}</h4>
              <p class='review'>
              {$review['review']}
              </p>
            </li>";
            }
          }else{
            $reviews = "<li class='no-reviews fs-13 txt-c'>لا يوجد آراء</li>";
          }
          // recipe card
          $rate = $recipe['ratesNum']>0? $recipe['averageRate']:"بلا تقييم";
          $formatedPrice = number_format($recipe['price']);
          $prices = "<span>{$formatedPrice} ل.س</span><span></span>";
          if($recipe['discount'] != null){
            $formatedDiscount = number_format($recipe['discount']);
            $prices = "<span>{$formatedDiscount} ل.س</span>
                      <span>{$formatedPrice} ل.س</span>";
          }
          $availableClass = "";
          $cartBtn ="<i class='fas fa-cart-plus'></i>";
          $cartBtnJSData ="data-add-to-cart";

          if(!$recipe['availability'] || !$recipe['restaurant']['delivery_service'] ){
            $availableClass ="cant-order";
            $cartBtnJSData ="";
        }
          if(!$recipe['restaurant']['delivery_service']){
            $cartBtn ="لا يوجد <br>توصيل";
          }
          if(!$recipe['availability']){
            $cartBtn ="غير متاح";
            $availableClass .=" not-available";
          }

          echo <<< "recipeCard"
            <li data-recipeID="{$recipe['id']}" class="$availableClass">
              <div class="meal-head">
                <img class="meal-img" src="{$recipe['image']}" alt="" />
                <div class="rate"><i class="fas fa-star"></i>$rate</div>
                <a href="../restaurants/restProfile.php?id={$recipe['restaurant']['id']}" class="rest">
                  <img src="{$recipe['restaurant']['image']}" alt="" />
                  <span>{$recipe['restaurant']['name']}</span>
                </a>
                <div class="rate right">
                  <i class="fas fa-star"></i>عدد المقييمين: {$recipe['ratesNum']}
                </div>
                <a href="./recipePreview.php?recipeID={$recipe['id']}" class="description">
                {$recipe['description']}
                </a>
              </div>
              <div class="content">
                <div class="name">
                  <div>
                    <h3>{$recipe['name']}</h3>
                    <div class="price">
                    $prices
                    </div>
                  </div>
                  <button $cartBtnJSData>
                    $cartBtn
                  </button>
                </div>
              </div>
              <div class="actions">
                <div class="rates" data-show-reviews>
                  <span>الآراء</span>
                  <span class="num">$reviewsNum</span>
                </div>
                <button data-rate-recipe>قيّم الوجبة</button>
              </div>
              <div class="reviews">
                <ul>
                  $reviews
                </ul>
                <button data-hide-reviews>
                  إخفاء <i class="fas fa-angles-down"></i>
                </button>
              </div>
            </li>
          recipeCard;
        }
        ?>
      </ul>
    </div>
  </section>
  <section class="pagination-section">
    <ul class="pagination justify-content-center">
      <li class="page-item disabled">
        <a class="page-link" href="#" tabindex="-1">السابق</a>
      </li>
      <li class="page-item active"><a class="page-link" href="#">1</a></li>
      <li class="page-item"><a class="page-link" href="#">2</a></li>
      <li class="page-item"><a class="page-link" href="#">3</a></li>
      <li class="page-item">
        <a class="page-link" href="#">التالي</a>
      </li>
    </ul>
  </section>
</main>
<?php
$jsFiles = ["recipes.js"];
include '../includes/templates/footer.php';
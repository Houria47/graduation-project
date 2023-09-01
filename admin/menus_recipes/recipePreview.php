<?php
SESSION_START();

$needsNav = true;
$needsFooter = true;
// files directories
$css = "../layout/css/";
$js = "../layout/js/";
$func = "../includes/functions/";
$cssFiles = ["recipePreview.css"];

$pageTitle = " وجبة | Resto";
include "../init.php";
$recipeID = null;
if(isset($_GET['recipeID'])){
  $recipeID =  $_GET['recipeID'];
}
if($recipeID === null ||  !isExist("id","recipe",$recipeID)){
    //TODO: show error message instead of redirection
    header("Location: ../index.php");
    exit();
}
$recipe = getRecipes($recipeID);
$restaurant = $recipe['restaurant'];
$restMenus = getRestaurantMenus($restaurant['id']);

// work hours
$openTime = dateFormater($restaurant['open_time'],"hh:mm a");
$closeTime = dateFormater($restaurant['close_time'],"hh:mm a");

if(!$restMenus){
  $restMenus = [];
}
$ratenums = "لا يوجد تقييمات";
if($recipe['ratesNum']>0){
  $ratenums = $recipe['ratesNum'];
}
?>
<div class="recipe-page" data-recipeID=<?=$recipeID?>>
  <div class="mycontainer">
    <aside>
      <div class="cover">
        <img src="<?=$restaurant['cover']?>" alt="" class="cover-img" />
        <img src="<?=$restaurant['image']?>" class="logo-img" />
      </div>
      <div class="head">
        <h3><a href="./../restaurants/restProfile.php?id=<?=$restaurant['id']?>"><?=$restaurant['name']?></a></h3>
      </div>
      <div class="rest-info">
        <?php
        if(count($restaurant['addresses']) > 0){
          foreach($restaurant['addresses'] as $address){
            echo <<< "address"
            <div class="infoo">
              <i class="fas fa-location-dot fa-fw"></i>
              {$address['state']}، {$address['region']} {$address['street']}
            </div>
            address;
          }
        }
        ?><div class="infoo">
          <i class="fas fa-clock fa-fw"></i>
          من <?=$openTime?> إلى <?=$closeTime?>
        </div>
        <?php
          if($restaurant['reserv_service'])
            echo "<a data-reserve-link href='./../restaurants/reserve.php?restID={$restaurant['id']}' class='reserv-link'><i
            class='fas fa-bookmark'></i> احجز الآن</a>";
        ?>

      </div>
      <div class="menus">
        <div class="section-title">
          <h3>القوائم</h3>
          <hr />
        </div>
        <?php
        if(count($restMenus) != 0 && !(count($restMenus) == 1 && $restMenus[0]['is_default'] == 1)){
          echo "<ul>";
          foreach($restMenus as $menu){
            if(!$menu['is_default']){
              $recipes = $menu['recipes'];
              $recipesNum = count($recipes);
              $recipesAction = "<span>$recipesNum وجبات</span>";
              $recipesContent ="";
              if($recipesNum > 0){
                $recipesAction = " <span data-show-menu-meals>$recipesNum وجبات <i class='fas fa-angle-down'></i></span>";
                $recipesContent = "<div class='menu-recipes'>";
                foreach($recipes as $oneRecipe){
                  $recipeImage = getImagePath($oneRecipe['image'],"REST_RECIPE");
                  $recipesContent .= "<a href='?recipeID={$oneRecipe['id']}' class='recipe-el'>
                      <img src='$recipeImage'/>
                      <h5>{$oneRecipe['name']}</h5>
                    </a>";
                }
                $recipesContent .= "</div>";
              }
              echo<<<"menu"
              <li>
                <img src="{$menu['image']['path']}" alt="" />
                <div class="flex-1">
                  <h4>{$menu['name']}</h4>
                  <p>{$menu['description']}</p>
                  $recipesAction
                </div>
                $recipesContent
              </li>
              menu;
            }
          }
          echo "</ul>";
        }else{
          echo "<p class='no-menus'>لا يوجد قوائم</p>";     
        }
        ?>
      </div>
    </aside>
    <main>
      <div class="recipe">
        <div class="img-box">
          <img src="<?=$recipe['image']?>" alt="" />
        </div>
        <div class="recipe-info">
          <h2><?=$recipe['name']?></h2>
          <p><?=$recipe['description']?></p>
          <div class="price">
            <?php
          if($recipe['discount'] !== null){
            echo "<span>{$recipe['discount']}<em class='unit'>ل.س</em></span>
            <span>{$recipe['price']}<em class='unit'>ل.س</em></span>";
          }else{
            
            echo "<span>{$recipe['price']}<em class='unit'>ل.س</em></span>
            <span></span>";
          }
          ?>
          </div>
          <?php
          if($restaurant['delivery_service']){
            echo "<button id='add-to-cart-btn'>
            أضف للسلة
            <i class='fas fa-cart-plus'></i>
          </button>";
          }
          ?>
          <div class="last_updated_date">آخر تحديث منذ <?=dateFormater($recipe['updated_at'],"y, d MMMM")?></div>
        </div>
      </div>
      <div class="reviews">
        <div class="section-title">
          <h3>التقييمات</h3>
          <hr />
        </div>
        <div class="content">
          <div class="rate-recipe">
            <img src="./../layout/images/rate-restaurant.png" alt="">
            <div data-recipeID=<?=$recipeID?>>
              <p>ما هو رأيك بالوجبة؟</p>
              <button data-rate-recipe><i class="fas fa-star"></i> <span>قيّم الجبة</span></button>
            </div>
          </div>
          <div class="rate-content">
            <div class="rate-numbers">
              <div class="rate-percentage"><?=$recipe['averageRate']?></div>
              <div class="ratings">
                <div class="empty-stars"></div>
                <div class="full-stars" style="width: <?=$recipe['ratePercenatge']?>%"></div>
              </div>
              <div class="rated-users-num"><?=$ratenums?></div>
            </div>
            <div class="rates-chart">
              <div class="rate-bar">
                <span>1</span>
                <div class="rate-bar--inner">
                  <div class="rate-bar--fill" style="width: <?=$recipe['ratesLevelsPercentage'][0]?>%"></div>
                </div>
              </div>
              <div class="rate-bar">
                <span>2</span>
                <div class="rate-bar--inner">
                  <div class="rate-bar--fill" style="width: <?=$recipe['ratesLevelsPercentage'][1]?>%"></div>
                </div>
              </div>
              <div class="rate-bar">
                <span>3</span>
                <div class="rate-bar--inner">
                  <div class="rate-bar--fill" style="width: <?=$recipe['ratesLevelsPercentage'][2]?>%"></div>
                </div>
              </div>
              <div class="rate-bar">
                <span>4</span>
                <div class="rate-bar--inner">
                  <div class="rate-bar--fill" style="width: <?=$recipe['ratesLevelsPercentage'][3]?>%"></div>
                </div>
              </div>
              <div class="rate-bar">
                <span>5</span>
                <div class="rate-bar--inner">
                  <div class="rate-bar--fill" style="width: <?=$recipe['ratesLevelsPercentage'][4]?>%"></div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="section-title">
          <h3>الآراء</h3>
          <hr />
        </div>
        <?php
          if(count($recipe['reviews'])>0){
            echo "<ul>";
            foreach($recipe['reviews'] as $review){
              echo<<< "review"
              <li>
                <div class='rate'>{$review['given_rate']}<i class='fas fa-star'></i></div>
                <img src='{$review['reviewer_image']}' alt='' />
                <h4>{$review['reviewer_name']}</h4>
                <p class='review'>
                {$review['review']}
                </p>
              </li>
              review;
            }
            echo "</ul>";
          }else{
            echo "<p class='no-reviews'>لا يوجد تعليقات</p>";
          }
        ?>
      </div>
    </main>
  </div>
</div>
<?php
$jsFiles = ["recipePreview.js"];
include '../includes/templates/footer.php';
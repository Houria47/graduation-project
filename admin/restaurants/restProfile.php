<?php 
SESSION_START();
$needsFooter = true;
$needsNav = true;

$pageTitle = "Dashboard | Resto";
$css = "../layout/css/";
$js = "../layout/js/";
$func = "../includes/functions/";
$cssFiles = ["restaurants/restProfile.css"];

include "../init.php";

$userID = isset($_SESSION['user'])?$_SESSION['user']['id']:-1;

$restID = $_GET['id'];
if(!isExist("id","restaurant",$restID)){
  header("Location: ../index.php");
  exit();
}

$restaurant = getRestaurantsInfo($restID);
$menus = getRestaurantMenus($restID);
$posts = getRestaurantPosts($restID);
$recipes = getRestaurantRecipe($restID);

// get reacts options
$reacts = getReactsList();

$reactsOptions = "";
foreach($reacts as $react){
  $reactsOptions .= "<img data-reactID='{$react['id']}' src='{$react['image']}'>";
}
// Start content format 
$workHours = "";
$openTime = dateFormater($restaurant['open_time'],"hh:mm a");
$closeTime = dateFormater($restaurant['close_time'],"hh:mm a");

$currTime = strtotime("now");
$openNow1 = "";
$openNow2 = "";
$ot = strtotime($restaurant['open_time']);
$ct = strtotime($restaurant['close_time']);
if( $currTime > $ot && $currTime < $ct){
  $openNow1 = "<span class='openNow open'>مفتوح الآن</span>";
  $openNow2 = "<span class='openNow'></span>";
}else{
  $openNow1 = "<span class='openNow close'>مغلق</span>";
  $openNow2 = "";
}

// echo "<pre>";
// var_dump(getRestaurantRecipe($restID));
// echo "</pre>";
?>
<main class="rest-profile">
  <div class="mycontainer">
    <section id="recipes" class="recipes">
      <h1 class="section-title">الوجبات 😋</h1>
      <ul>
        <?php
        if(count($recipes) == 0){
          echo "<li><p class ='txt-c fs-15 c-777 p-10'>لم يقم المطعم بإضافة وجبات لحسابه</p></li>";
        }
        foreach($recipes as $recipe){
          $price = number_format($recipe['price']);
          $discount = number_format($recipe['discount']);

          $priceContent = $recipe['discount'] == null?
          "<span>$price <em class='unit'>ل.س</em></span>
          <span> </span>"
          :"<span>$discount<em class='unit'>ل.س</em></span>
          <span>$price</span>";

          $rate = $recipe['ratesNum'] == 0? 'بلا تقييم':"<div class='empty-stars'></div>
          <div class='full-stars' style='width: {$recipe['ratePercenatge']}%''></div>";

          $availableClass = "";
          $cartBtn ="<i class='fas fa-cart-plus'></i>";
          $cartBtnJSData ="data-add-to-cart";

          if(!$recipe['availability'] || !$restaurant['delivery_service'] ){
            $availableClass ="cant-order";
            $cartBtnJSData ="";
        }
          if(!$restaurant['delivery_service']){
            $cartBtn ="لا يوجد <br>توصيل";
          }
          if(!$recipe['availability']){
            $cartBtn ="غير متاح";
            $availableClass .=" not-available";
          }
          echo <<< "recipe"
          <li data-recipeID="{$recipe['id']}"  class="$availableClass">
            <a href="../menus_recipes/recipePreview.php?recipeID={$recipe['id']}">
              <h2>{$recipe['name']}</h2>
              <div class="ratings">
                $rate
              </div>
              <img src="{$recipe['image']['path']}" alt="" />
            </a>
            <div class="details">
              <div class="prices">
                $priceContent
              </div>
              <button $cartBtnJSData>
                $cartBtn
              </button>
            </div>
          </li>
          recipe;
        }
        ?>
      </ul>
    </section>
    <section class="main">
      <div class="head">
        <div class="cover">
          <img src="<?=$restaurant['cover_image']?>" alt="" />
        </div>
        <div class="logo">
          <div class="logo-box">
            <img src="<?=$restaurant['profile_image']?>" alt="" />
            <?=$openNow2?>
          </div>
        </div>
        <div class="name">
          <h1><?=$restaurant['name']?></h1>
          <?php
          if($restaurant['ratesNum'] > 0){
            echo "<div class='ratings'>
                <div class='empty-stars'></div>
                <div class='full-stars' style='width:{$restaurant['ratePercenatge']}%'>
            </div>
          </div>";
          }else{
          echo "<div class='ratings'><span>لا يوجد تقييم</span></div>";
          }
          echo $openNow1;
          ?>

        </div>
      </div>
      <div class="content">
        <div class="rinfo">
          <div>
            <?php
        foreach($restaurant['addresses'] as $address){
          echo "<p><i class='fa fa-map-marker fa-fw'></i>{$address['state']}، {$address['region']} {$address['street']}</p>";
        }
        ?>
            <p>
              <i class="fa fa-clock fa-fw"></i>يفتح من الساعة <?=$openTime?> حتى الساعة <?=$closeTime?>
            </p>
            <p>
              <i class="fa fa-envelope fa-fw"></i><?=$restaurant['email']?>
            </p>
            <p><i class="fa fa-phone fa-fw"></i><?=$restaurant['phone']?></p>
          </div>
          <?php
          if($restaurant['reserv_service'])
           echo "<a class='reserv-link' data-reserve-link href='./reserve.php?restID=$restID'>احجز الآن</a>";
          ?>
        </div>
        <div class="services">
          <h2 class="info-title">الخدمات</h2>
          <div class="services-row">
            <div class="service">
              <h3>التوصيل</h3>
              <img src="./../layout/images/delivary.png" alt="" />
              <p><?= $restaurant['delivery_service']?"متوفر":"لا يوجد"?></p>
            </div>
            <div class="service">
              <h3>الحجز</h3>
              <img src="./../layout/images/rest-reservation.png" alt="" />
              <p><?= $restaurant['reserv_service']?"متوفر":"لا يوجد"?></p>
            </div>
            <div class="service">
              <h3>الاستضافة</h3>
              <img src="./../layout/images/table-chairs.png" alt="" />
              <p><?= $restaurant['fast_food']?"سفري فقط":"متوفرة"?></p>
            </div>
          </div>
        </div>
        <div class="rates-reviews">
          <h2 class="info-title">الآراء والتقييمات</h2>
          <div class="rate-rest">
            <img src="./../layout/images/rate-restaurant.png" alt="">
            <div data-restID=<?=$restID?>>
              <p>ما هو رأيك بالمطعم؟</p>
              <button data-rate-rest><i class="fas fa-star"></i> <span>قيّم المطعم</span></button>
            </div>
          </div>
          <div class="rate-content">
            <div class="rate-numbers">
              <div class="rate-percentage"><?=$restaurant['rate']?></div>
              <div class="ratings">
                <div class="empty-stars"></div>
                <div class="full-stars" style="width: <?=$restaurant['ratePercenatge']?>%"></div>
              </div>
              <div class="rated-users-num">
                <?=$restaurant['ratesNum']==0?"لم يقم أحد بتقييم المطعم":$restaurant['ratesNum']?></div>
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
          <?php
            $reviewsNum = count($restaurant['reviews'] );
            if( $reviewsNum > 3){
              echo "<ul>";
            }else{
              echo "<ul class='active'>";  
            }
            foreach($restaurant['reviews'] as $review){
              echo <<<"review"
              <li>
                <img src="{$review['reviewer_image']}" alt="" />
                <div>
                  <h3>{$review['reviewer_name']}<i class="fas fa-star">{$review['given_rate']}</i></h3>
                  <p>{$review['review']}</p>
                </div>
              </li>
              review;
            }
            echo "</ul>";
            
            if($reviewsNum > 3){
              echo "<button class='show-reviews-btn'>عرض الكل</button>";
            }
          ?>

        </div>
        <div id="main-content" class="main-content">
          <h2 class="info-title">العروض والإعلانات</h2>
          <div class="mob-head">
            <div data-posts>الإعلانات</div>
            <div data-recipes>الوجبات</div>
            <div data-menus>القوائم</div>
          </div>
          <ul id="posts">
            <?php
            if(count($posts) == 0){
              echo "<p class ='txt-c fs-15 c-777 p-10'>لا يوجد إعلانات للمطعم</p>";
            }
            foreach($posts as $post){
              $media = "";
              if(count($post['media']) > 0){
                $sliderClass = count($post['media']) > 1?"js-slider":"";
                $images = "";
                foreach($post['media'] as $img){
                  $images .= "<img src='$img'/>";
                }
                $media = "<div class='media-container $sliderClass'>$images</div>";
              }

              $date = dateFormater($post['created_at'],"d MMMM, hh:mm");


              $reactsNum = $post['reacts']['reacts_number'];
              if($reactsNum > 0){
                $Imgs = "";
                foreach($post['reacts']['top_three'] as $img){
                  $Imgs .= "<img src='{$img['image']}' />";
                }
                $reacts = "<div data-reacts class='center-flex'>$Imgs
                <span>$reactsNum</span>
                </div>";
              }else{
                $reacts = "<div data-reacts class='center-flex'>
                    <span><i class='d-none'>wtf</i></span>
                  </div>";
              }
              
              $commentsNum = count($post['comments']);
              $comments = "<div data-showComments class='commetns center-flex'>
                <span></span></div>";
              if($commentsNum > 0){
                $comments = "<div data-showComments class='commetns center-flex'>
                <span>$commentsNum</span>
                <i class='fas fa-comment'></i>
              </div>";
              }

              $commentsContent = count($post['comments'])>0?"": "<li data-noComments>لا يوجد تعليقات</li>";
              foreach($post['comments'] as $comment){
                $date = dateFormater($comment['date'],"d MMMM, hh:mm");
                $reply = "";
                if($comment['reply'] != null){
                  $reply = "<div class='reply'>
                  <a href='restProfile.php?id=$restID'>{$restaurant['name']}</a>
                  <p>{$comment['reply']}</p>
                  </div>";
                }
                $removeOption = "";
                if($comment['customer']['id'] == $userID){
                  $removeOption = "<span data-delComment='{$comment['id']}'>حذف</span>";
                }
                $commentsContent .= "<li data-commentID = '{$comment['id']}'>
                <div class='comment'>
                  <img src='{$comment['customer']['image']}' alt='' />
                  <div>
                    <h3>
                      {$comment['customer']['name']}
                      <!-- <img src='reactImg' /> -->
                    </h3>
                    <p>{$comment['content']}</p>
                    <div class='actions'>
                      <div class='btns'>
                      $removeOption
                      </div>
                      <div class='date'>$date</div>
                    </div>
                  </div>
                </div>
                $reply
              </li>";
              }
              echo <<<"post"
              <li class="post" data-postID="{$post['id']}">
                $media
                <div class="content">
                  <p class="caption">{$post['caption']}</p>
                  <div class="date">$date</div>
                </div>
                <div class="actions">
                  <div class="activities between-flex">
                    $reacts
                    $comments
                  </div>
                  <div class="between-flex">
                    <button class="reacts-btn center-flex">
                      <div class="btn-content">
                        <i class="fas fa-thumbs-up fa-fw"></i>تفاعل
                        <!-- <img
                          src="./uploads/settings/reactions/fire.png"
                          alt=""
                        /> -->
                      </div>
                      <div class="reacts">
                      $reactsOptions
                      </div>
                    </button>
                    <button data-comment class="center-flex">
                      <i class="fas fa-comment-alt fa-fw"></i>تعليق
                    </button>
                  </div>
                </div>
                <form class="comment-form">
                  <input type="text" name="comment" placeholder="اكتب تعليقك" />
                  <input type="hidden" name="postID" value="{$post['id']}" />
                  <button type="submit">
                    <i class="fas fa-commenting"></i>
                  </button>
                </form>
                <div class="post-comments">
                  <h4>التعليقات</h4>
                  <ul data-commentsUl class="comments">
                    $commentsContent
                  </ul>
                </div>
              </li>
            
              post;
            }
            ?>
          </ul>
        </div>
      </div>
    </section>
    <seection id="menus" class="menus">
      <h1 class="section-title">القوائم</h1>
      <ul>
        <?php
        if(count($menus) == 0 || (count($menus) == 1 && $menus[0]['is_default'] == 1)){
          $Mrecipes = "";
          if(count($recipes)>0){
            foreach($recipes as $recipe){
              $price = $recipe['discount'] != null? $recipe['discount']:$recipe['price'];
              $price = number_format($price);

              $Mrecipes .="<li>
              <a href='./../menus_recipes/recipePreview.php?recipeID='><img
                  src='{$recipe['image']['path']}' />
                <h4>{$recipe['name']}</h4>
                <span>$price</span>
              </a>
            </li>";
          }
          }else{
            $Mrecipes = "<li class='no-meals'>لا يوجد وجبات في هذه القائمة</li>";
          }
          echo <<<"oneMenu"
          <li>
            <div class="img-box">
              <img src="{$restaurant['cover_image']}" alt="" />
            </div>
            <div data-showMenuDetails class="menu-lable">
              <h3>{$restaurant['name']}</h3>
              <i class="fas fa-angle-down"></i>
            </div>
            <ul class="menu-meals">
            $Mrecipes
            </ul>
          </li>
          oneMenu;
        }

        foreach($menus as $menu){
          if($menu['is_default']){
            continue;
          }
          $Mrecipes = "";
          if(count($menu['recipes']) > 0){
            foreach($menu['recipes'] as $recipe){
              $price = $recipe['discount_price'] != null? $recipe['discount_price']:$recipe['price'];
              $price = number_format($price);
              $img = getImagePath($recipe['image'],"REST_RECIPE");

              $Mrecipes .="<li>
              <a href='./../menus_recipes/recipePreview.php?recipeID={$recipe['id']}'><img
                  src='$img' />
                <h4>{$recipe['name']}</h4>
                <span>$price</span>
              </a>
            </li>";
            }
          }else{
            $Mrecipes = "<li class='no-meals'>لا يوجد وجبات في هذه القائمة</li>";
          }
          echo <<< "menu"
          <li>
            <div class="img-box">
              <img src="{$menu['image']['path']}" alt="" />
            </div>
            <div data-showMenuDetails class="menu-lable">
              <h3>{$menu['name']}</h3>
              <i class="fas fa-angle-down"></i>
            </div>
            <ul class="menu-meals">
            $Mrecipes
            </ul>
          </li>
          menu;
        }
        ?>
      </ul>
    </seection>
  </div>
</main>

<?php

$jsFiles = ["restProfile.js","../ads/posts.js"];
include '../includes/templates/footer.php';
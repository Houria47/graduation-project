<?php
// get menus option from db
$menus = getRestaurantMenus($restID);

// get recipes from db
$recipes = getRestaurantRecipe($restID);

// menus options 
$menusOption = "";
$noDefaultMenu = true;
foreach($menus as $menu){
  if($menu['is_default']){
    $noDefaultMenu = false;
  }
  $menusOption .= "<option value='{$menu['id']}'>{$menu['name']}</option>";
}
$menusFilterOptions =  $menusOption;
if($noDefaultMenu){
  $menusOption = "<option value='-1'>وجبات أخرى</option>" . $menusOption;
}   

// get top rated recipes
$topRated = getTopRatedRecipes($restID);
// get latest newwwwwss
$latestReviews = getLatestReviewsOnRecipes($restID);
// get top orderd Recipes
$mostDemanding = getMostDemandingRecipes($restID);
?>
<h2 class="tab-title">
  <span>الوجبات</span>
</h2>
<main class="meals-content" data-restID="<?=$restID?>">

  <div class="statistcs">
    <div class="my-card top-rate">
      <div class="component-title">الأعلى تقييماً <i class="fas fa-star"></i></div>
      <?php
      if(count($recipes) > 0){
        if(count($topRated) > 0){
          echo "<ul>";
          foreach($topRated as $ele){
            $unit = $ele['nums']>=3 && $ele['nums']<=10 ? "أشخاص":"شخص";
            echo <<< topRateEle
            <li>
              <img src="{$ele['image']}" alt="">
              <div class="content">
                <div class="name">
                  <h4>{$ele['name']}</h4>
                  <p>قييّمها <span>{$ele['nums']}</span> $unit</p>
                </div>
                <div class="rate" data-rate="{$ele['averageRate']}">
                </div>
              </div>
            </li>
            topRateEle;
          }
          echo "</ul>";

        }else{
          echo <<< "noRatedRecipes"
          <div class="no-content">
            <img src="./../../layout/images/chef-wondering-in-kitchen.webp" alt="">
            <h3>لا يوجد تقييمات</h3>
            <p>لم يقم أحد بتقييم وجباتك، يمكنك إضافة بعض الإعلانات لوجباتك كي تجذب اهتمام الزبائن لها من خلال تبويبة
              <a href="?tab=ads">الإعلانات</a>
            </p>
          </div>
          noRatedRecipes;
        }
      }else{
        echo <<< "noRecipes"
        <div class="no-content">
          <img src="./../../layout/images/chef-wondering-in-kitchen.webp" alt="">
          <h3>لا يوجد وجبات </h3>
          <p>لم تقم بإضافة وجبات، يستطيع الزبائن تقييم وجباتك وإبداء رأيهم فيها متى ما أضفتها إلى الحساب من خلال استمارة
            <a href="#add-recipe-card">إضافة وجبة</a>
          </p>
        </div>
        noRecipes;
      }
      ?>
    </div>
    <div class="my-card most-ordered">
      <div class="component-title">الأكثر طلباً <i class="fas fa-truck-fast ltr"></i></div>
      <?php
        if(count($recipes) > 0){
          if($_SESSION['restaurant']['delivery_service']){
            if(count($mostDemanding) > 0){
              echo "<ul>";
              foreach ($mostDemanding as $ele) {
              $customersUnit =$ele['customers_num'] >=3 && $ele['customers_num'] <=10? "أشخاص":"شخص";
              $ordersUnit = $ele['orders_num'] >=3 && $ele['customers_num'] <= 10?"طلبات":"طلب";
              $qtyUnit = $ele['qty'] >=3 && $ele['qty']<= 10?"وجبات":"وجبة";
                echo <<< "mostDemanedEle"
                <li>
                  <div class="head">
                    <img  src="{$ele['image']}" alt="" />
                    <h4>{$ele['name']}</h4>
                  </div>
                  <div class="nums">
                    <div class="num">
                      <div>طلبت من قبل</div>
                      <div class="number">{$ele['customers_num']}</div>
                      <div>$customersUnit</div>
                    </div>
                    <div class="num">
                      <div>عدد الطلبات</div>
                      <div class="number">{$ele['orders_num']}</div>
                      <div>$ordersUnit</div>
                    </div>
                    <div class="num">
                      <div>بمجمل كمية</div>
                      <div class="number">{$ele['qty']}</div>
                      <div>$qtyUnit</div>
                    </div>
                  </div>
                </li>
                mostDemanedEle;
              }
              echo "</ul>";
            }else{
              echo <<< "noReviews"
              <div class="no-content">
                <p>لم يقم احد بطلب وجباتك، يمكنك إضافة إعلانات لوجباتك كي تجذب اهتمام الزبائن لها من خلال تبويبة  
                  <a href="?tab=ads">الإعلانات</a>
                </p>
                <h3>لا يوجد طلبات !</h3>
                <img src="./../../layout/images/no-items-found.png" alt="">
              </div>
              noReviews;
            }
          }else{
            echo <<< "noDeliveryService"
            <div class="no-content">
              <p>كي يستطيع الزبائن طلب وجباتك يجب أن تفعل خدمة التوصيل لاستقبال الطلبات وذلك من توبية
                <a href="?tab=dashboard">لوحة التحكم</a>
              </p>
              <h3>لا يوجد خدمة توصيل</h3>
              <img src="./../../layout/images/no-items-found.png" alt="">
            </div>
            noDeliveryService;
          }
        }else{
          echo <<< "noRecipes"
          <div class="no-content">
            <p>بإمكانك استقبال طلبات الزبائن على مختلف الوجبات التي تقوم بإضافتها وعرض هذه الطلبات من خلال تبوية
              <a href="?tab=orders">الطلبات</a>
            </p>
            <h3>لم يجد الزبائن وجبات في حسابك!</h3>
            <img src="./../../layout/images/no-items-found.png" alt="">
          </div>
          noRecipes;
        }
      
      ?>

    </div>
    <div class="my-card new-rates">
      <div class="component-title">أحدث الآراء <i class="fas fa-comment-alt"></i></div>
      <?php
      if(count($recipes) > 0){
        if(count($latestReviews) > 0){
          foreach ($latestReviews as $ele) {
            echo "<ul>";
            echo <<< "reviewEle"
            <li>
              <div class="head">
                <i class="fas fa-comment-alt"></i>
                علّق
                <span>{$ele['reviewer_name']}</span>
                على
                <span>{$ele['recipe_name']}</span>
              </div>
              <div class="review">
                <img  src="{$ele['reviewer_image']}" alt="" />
                <p>{$ele['review']}</p>
              </div>
            </li>
            reviewEle;
            echo "</ul>";
          }
        }else{
          echo <<< "noReviews"
          <div class="no-content">
            <img src="./../../layout/images/mobile-comments.webp" alt="">
            <h3>لم تتم إضافة تعليقات</h3>
            <p>لم يقم أحد بالتعليق على وجباتك، يمكنك إضافة بعض الإعلانات لوجباتك كي تجذب اهتمام الزبائن لها من خلال تبويبة
              <a href="?tab=ads">الإعلانات</a>
            </p>
          </div>
          noReviews;
        }
      }else{
        echo <<< "noRecipes"
        <div class="no-content">
          <img src="./../../layout/images/mobile-comments.webp" alt="">
          <h3>لم تقم بإضافة وجبات</h3>
          <p>بإمكانك استعراض كافة الآراء والتعليقات على وجباتك المميزة من خلال <a href="#meals-card">قائمة الوجبات
              المضافة</a>
          </p>
        </div>
        noRecipes;
      }
      ?>
    </div>
  </div>

  <div id="add-recipe-card" class="my-card mb-20">
    <div class="component-title">إضافة وجبة</div>
    <form id="add-meal-form" enctype="multipart/form-data">
      <div class="row">
        <div class="col-lg">
          <div class="form-input">
            <label>اسم الوجبة</label>
            <input name="name" type="text" />
            <small id="name-msg"></small>
          </div>
          <div class="form-input">
            <label>وصف الوجبة</label>
            <input name="description" type="text" />
            <small id="desc-msg"></small>
          </div>
          <div class="form-input">
            <label>السعر</label>
            <input type="text" name="price" oninput="onlyNums(this)" />
            <small id="price-msg"></small>
          </div>
          <div class="form-input">
            <label>السعر بعد الحسم</label>
            <input name="discountPrice" type="text" oninput="onlyNums(this);" />
            <small id="discount-msg"></small>
          </div>
          <div class="form-input">
            <label>القائمة</label>
            <select name="menu" class="select">
              <!-- <option value="-1">افتراضية</option> -->
              <?php echo $menusOption?>
            </select>
            <small id="menu-msg"></small>
          </div>
        </div>
        <div class="col image-col">
          <div class="meal-img-box center-flex">
            <img src="./../../layout/images/Standard/recipe-items.jpg" alt="" />
            <label for="mealImgInput" class="overlay center-flex">
              <i class="fas fa-upload"></i>
            </label>
          </div>
          <label id="file-input-label" class="dash-btn" for="mealImgInput">
            تحميل صورة
            <input id="mealImgInput" type="file" hidden name="mealImage" Accept="image/*" />
          </label>
          <small id="image-msg"></small>
        </div>
      </div>
      <div class="tags-input">
        <label>إضافة وسوم</label>
        <div class="tags-content">
          <p>إضغط Enter أو أدخل فاصلة بين كل وسم</p>
          <ul data-mealID="-1">
            <input type="text" spellcheck="false"
              placeholder="شاورما، برغر، عربي، غربي، حلويات، بوظة، شراب، حاد، حلو،..." />
          </ul>
          <div class="tags-details">
            <p><span>10</span> وسوم متبقية</p>
            <button type="button" class="dash-btn">مسح الكل</button>
          </div>
        </div>
      </div>
      <input name="restID" type="hidden" value=<?=$restID?>>
      <button class="dash-btn dash-btn-green mt-20">أضف الوجبة</button>
    </form>
  </div>

  <div id="meals-card" class="my-card">
    <div class="component-title">الوجبات المضافة</div>
    <?php
    if(count($recipes) > 0){
      echo <<< "recipesHeader"
        <div class="meals-filters">
          <div class="menus-filter">
            <label for="menus-filter">القائمة</label>
            <select id="menus-filter" name="menus-filter">
            <option selected value="-1">الكل</option>
              $menusFilterOptions
            </select>
          </div>
          <div class="p-relative search">
            <input
              id="searchMeals"
              placeholder="ابحث عن وجبة"
              type="search"
            />
          </div>
        </div>
        <ul id="meals-list">
      recipesHeader;
      foreach($recipes as $recipe){
        $price = $recipe['discount'] == NULL? $recipe['price'] : $recipe['discount'];
        $rate = $recipe['ratesNum'] == 0? "بلا تقييم": $recipe['averageRate'] ;
        echo <<< "recipeItem"
          <li class="meal" data-mealId="{$recipe['id']}">
          <div class="img-box">
            <img src="{$recipe['image']['path']}" alt="" />
          </div>
          <div class="meal-container">
            <div class="meal-details">
              <div class="name">
                <i class="fas fa-bowl-food"></i>
                <span data-mealName>{$recipe['name']}</span>
              </div>
              <div class="price">
                <i class="fas fa-money-bill-alt"></i>
                <span data-MealPrice>$price</span>
              </div>
              <div>
                <i class="fas fa-star"></i> <span>$rate</span>
              </div>
              <div class="actions">
                <span
                  data-showMealRates
                  class="dash-btn dash-btn-orange c-white rad-half w-fit"
                >
                  <i class="fas fa-comment-dots"></i>
                </span>
                <span
                  data-editMeal
                  class="dash-btn dash-btn c-white rad-half w-fit mr-5"
                >
                  <i class="fas fa-edit"></i>
                </span>
                <span
                  data-deleteMeal
                  class="dash-btn dash-btn-red c-white rad-half w-fit mr-5"
                >
                  <i class="fas fa-trash"></i>
                </span>
              </div>
            </div>
          </div>
        </li>
        recipeItem;

      }
      echo "</ul>";
    }else{
      echo <<< "noRecipes"
        <div class="no-meals">
          <p>لا يوجد أية وجبات! <a href="#add-recipe-card">إضافة وجبة</a></p>
          <img src="./../../layout/images/chefs.jpg" alt="" />
        </div>
      noRecipes;
    }
    ?>
  </div>

</main>
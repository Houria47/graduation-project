<?php 
SESSION_START();
$pageTitle = "المطاعم | Resto";
$needsNav = true;
$needsFooter = true;
$selectedTab = "rests";
$css = "../layout/css/";
$js = "../layout/js/";
$func = "../includes/functions/";
$cssFiles = ["restaurants/restaurants.css"];

include "../init.php";

$rests = getRestaurantsInfo();

$topRated = getTopRatedRests();

$states = getStates(); 



$filter = isset($_GET['do'])?$_GET['do']:"";

$allOrTop ="selected";
$top = "";
if($filter == "top"){
  $top = "selected";
  $allOrTop ="";
}

$reservDo = "";
if($filter == "reserv"){
  $reservDo = "checked";
}

?>

<main class="rests-page">
  <!-- Header -->
  <section class="head">
    <div class="mycontainer p-relative">
      <div class="boxes js-rests-slider">
        <?php
        foreach($topRated as $topRest){
          $openTime = dateFormater($topRest['open_time'],"hh:mm a");
          $closeTime = dateFormater($topRest['close_time'],"hh:mm a");
          
          $addresses = "";
          for($i = 0; $i < min(count($topRest['addresses']),2 ) ; $i++){
            $address = $topRest['addresses'][$i];

            $addresses .= "<div class='infoo'>
              {$address['state']}، {$address['region']} {$address['street']}
              <i class='fas fa-location-dot'></i>
            </div>";
          }
          echo <<<"topRest"
          <a href="./../restaurants/restProfile.php?id={$topRest['id']}" class="box">
            <div class="rest-box-head d-flex">
              <div>
                <h2>{$topRest['name']}</h2>
                <div class="ratings">
                  <div class="empty-stars"></div>
                  <div class="full-stars" style="width: {$topRest['ratePercenatge']}%"></div>
                </div>
              </div>
              <img src="{$topRest['profile_image']}" alt="" />
            </div>
            <div class="content">
              <p class="desc">{$topRest['description']}</p>
              <div class="around-flex infoos">
                <div class="txt-c center-flex gap-10">
                  <span
                    >من $openTime <br />
                    حتى $closeTime</span
                  >
                  <i class="fas fa-clock"></i>
                </div>
                <div>
                  $addresses
                  <div class="infoo">
                    {$topRest['phone']}<i class="fas fa-phone"></i>
                  </div>
                </div>
              </div>
            </div>
          </a>
          topRest;
          
        }
        ?>
      </div>
      <div class="text">
        <h1>أفضل المطاعم</h1>
        <p>تصفح أشهر المطاعم في سوريا...</p>
        <a href="#seperator" class="show-all"> عرض كافة المطاعم </a>
      </div>
      <a id="restsList" href="#seperator" class="arrow-btn fas fa-angle-double-down"></a>
    </div>
  </section>
  <!--  Seperator -->
  <section id="seperator" class="seperator">
    <img src="./../layout/images/wave.svg" alt="" />
  </section>
  <!-- Rests List -->
  <section class="main">
    <div class="mycontainer">
      <div class="sidebar">
        <h2>أدوات البحث</h2>
        <form id="searchRestForm">
          <div>
            <button><i class="fas fa-search"></i></button>
            <input type="text" id="searchRestInput" name="searchText" placeholder="ابحث عن مطعم">
          </div>
        </form>
        <div class="filter">
          <label>المحافظة</label>
          <select id="stateFilter" name="state">
            <option value="-1">الكل</option>
            <?php
              foreach($states as $state){
                  echo "<option value='{$state['id']}'>{$state['name']}</option>";
              }
            ?>
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
              <input id="delivary" type="checkbox" />
              <label for="delivary">مع خدمة توصيل</label>
            </div>
            <div>
              <input <?=$reservDo?> id="reserv" type="checkbox" />
              <label for="reserv">مع خدمة حجز</label>
            </div>
            <div>
              <input id="fastfood" type="checkbox" />
              <label for="fastfood">إمكانية الاستضافة ضمن المطعم</label>
            </div>
          </div>
        </div>
        <button id="searchRestBtn" class="search-btn" type="button">
          بحث
        </button>
      </div>
      <div class="content">
        <div id="no-result" class="no-items d-none">
          <img src="./../layout/images/sad-search.png" alt="" />
          <p>لا يوجد نتائج مطابقة!</p>
        </div>
        <?php
        if(count($rests) == 0){
          echo "<div class='no-items'>
          <img src='./../layout/images/no-items-found.png' alt='' />
          <p>لا يوجد مطاعم للاسف!</p>
        </div>";
        }else{
          echo "<ul id='rests-list' class='rests'>";
          foreach($rests as $rest){

            $notMatchedClass = "";
            if(!empty($top)){
              if($rest['rate'] < 4) 
              $notMatchedClass = "d-none";
            }
            if(!empty($reservDo)){
              if(!$rest['reserv_service'])
              $notMatchedClass = "d-none";
            }
            if($rest['account_status'] != 2){
              continue;
            }
            $addresses = "";
            for($i = 0; $i < min(count($rest['addresses']),2 ) ; $i++){
              $address = $rest['addresses'][$i];

              $addresses .= "<p>{$address['state']}، {$address['region']} {$address['street']}</p>";
            }

            $reserv = $rest['reserv_service'] == 0?"":"<a data-reserve-link class='action' href='./reserve.php?restID={$rest['id']}'>
              <i class='fas fa-bookmark'></i>احجز
            </a>";

            $rate = $rest['ratesNum'] == 0 ? "بلا تقييم":$rest['rate'];
            $openTime = dateFormater($topRest['open_time'],"hh:mm a");
            $closeTime = dateFormater($topRest['close_time'],"hh:mm a");

            echo <<<"rest"
            <li data-restID = "{$rest['id']}" class="$notMatchedClass">
              <div class="cover-img">
                <div class="overlay"></div>
                <img alt="" src="{$rest['cover_image']}"/>
              </div>
              <div class="infoo">
                <div class="logo-img">
                  <img src="{$rest['profile_image']}" alt="" />
                </div>
                <div class="wrapper">
                  <div class="cont">
                    <a class="name" href="./restProfile.php?id={$rest['id']}">
                    {$rest['name']}
                      <span class="rate"><i class="fas fa-star"></i>$rate</span>
                    </a>
                    <p class="desc">
                    {$rest['description']}
                    </p>
                    <div class="infoo-row">
                      <i class="fas fa-phone fa-fw"></i>
                      <div class="flex-1">
                        <p>{$rest['phone']}</p>
                      </div>
                    </div>
                    <div class="infoo-row">
                      <i class="fas fa-location-dot fa-fw"></i>
                      <div class="flex-1">
                        $addresses
                      </div>
                    </div>
                    <div class="infoo-row">
                      <i class="fas fa-clock fa-fw"></i>
                      <div class="flex-1">
                        <p>يفتح من $openTime حتى $closeTime</p>
                      </div>
                    </div>
                    <div class="actions">
                      <span data-rate-rest class="action"><i class="fas fa-star"></i>قيّم المطعم</span>
                      $reserv
                    </div>
                  </div>
                </div>
              </div>
            </li>
          
            rest;
          }
          echo "</ul>";

        }
        if(count($rests) != 0){ 
          ?>

        <section id="pagination" class="pagination-section">
          <ul class="pagination justify-content-center">
            <li class="page-item disabled">
              <a class="page-link" href="#" tabindex="-1">السابق</a>
            </li>
            <li class="page-item active">
              <a class="page-link" href="#">1</a>
            </li>
            <li class="page-item disabled">
              <a class="page-link" href="#">التالي</a>
            </li>
          </ul>
        </section>
        <?php
        }
       ?>
      </div>
    </div>
  </section>


  <?php
$jsFiles = ["restaurants.js"];
include '../includes/templates/footer.php';
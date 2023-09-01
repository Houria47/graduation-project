<?php 
SESSION_START();
$css = "../../layout/css/";
$js = "../../layout/js/";
$func = "../../includes/functions/";
$libs = "../../includes/libraries/";
$cssFiles = ["restaurant_account/dashboard.css"];
$jsFiles = ["restDashboard.js"];
$currentTab = 'dashboard';
if(isset($_GET['tab']) && !empty($_GET['tab'])){
  $currentTab = $_GET['tab'];
}
switch ($currentTab){
  case 'meals':
    $pageTitle = "Dashboard | الوجبات";
    $cssFiles[] = "restaurant_account/mealsTab.css";
    $jsFiles[] = "./meals.js";
  break;
  case 'menus':
    $pageTitle = "Dashboard | القوائم";
    $cssFiles[] = "restaurant_account/menusTab.css";
    $jsFiles[] = "./menus.js";
  break;
  case 'ads':
    $pageTitle = "Dashboard | الإعلانات";
    $cssFiles[] = "restaurant_account/postsTab.css";
    $jsFiles[] = "./posts.js";
  break;
  case 'orders':
    $pageTitle = "Dashboard | الطلبات";
    $cssFiles[] = "restaurant_account/orders.css";
    $jsFiles[] = "./../../layout/js/chartJS.js";
    $jsFiles[] = "./orders.js";
  break;
  case 'reservations':
    $pageTitle = "Dashboard | الحجوزات";
    $cssFiles[] = "restaurant_account/orders.css";
    $jsFiles[] = "./../../layout/js/chartJS.js";
    $jsFiles[] = "./reservations.js";
  break;
}
include "../../init.php";


if(isset($_SESSION['restaurant'])){
  $restID = $_SESSION['restaurant']['id'];
  $accountStatus = $_SESSION['restaurant']['account_status'];
  if($accountStatus == 0){
    // account not verified, access not allowed, redirect to home
    header("location: ../../index.php");
    exit();
  }else if($accountStatus == 1){
    // account not authenticated, show "account is under review" message
    $isAuthenticated = false; 
    $authFiles = $_SESSION['restaurant']['authentication_files'];
  }{
    // else  account is verified and authenticated so get 
    // restaurant data from session and show dashboard 
    $restaurant = $_SESSION['restaurant'];
  }
}else{
  // no registered account, access not allowed, redirect to home
  header("location:../../index.php");
  exit();
}
if(isset($isAuthenticated) && !$isAuthenticated){
    require("./authenticationModal.php");
}
?>
<div class="dashboard d-flex">
  <!-- Include Sidebar section -->
  <?php include './sidebar.php'?>
  <!-- Start Content -->
  <div class="content w-full">
    <a class="exit" href="../../signOut.php?do=REST"><i class="fas fa-sign-out"></i></a>

    <?php
    switch ($currentTab){
      case 'menus':
        include "./menus.php";
      break;
      case 'meals':
        include "./meals.php";
      break;
      case 'ads':
        include "./posts.php";
      break;
      case 'orders':
        include "./orders.php";
      break;
      case 'reservations':
        include "./reservations.php";
      break;
      default://dashboard
        include "./controlTab.php";
      break;
    }
    ?>
  </div>
  <!-- End Content -->
</div>
<?php
include '../../includes/templates/footer.php';
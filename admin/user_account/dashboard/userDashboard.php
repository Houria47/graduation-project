<?php 
SESSION_START();
$pageTitle = "Dashboard | Resto";
$css = "../../layout/css/";
$js = "../../layout/js/";
$func = "../../includes/functions/";
$libs = "../../includes/libraries/";
$cssFiles = ["user_account/dashboard.css"];
$jsFiles = ["userDashboard.js"];
$currentTab = 'account';
$pageTitle = "حسابي";

if(isset($_GET['tab']) && !empty($_GET['tab'])){
  $currentTab = $_GET['tab'];
}
switch ($currentTab){

  case 'cart':
    $pageTitle = "حسابي | السلة";
    $cssFiles[] = "user_account/cartTab.css";
    $jsFiles[] = "cart.js";
  break;
  case 'orders':
    $pageTitle = "حسابي | الطلبات";
    $cssFiles[] = "user_account/ordersTab.css";
    $jsFiles[] = "./orders.js";
    break;
  case 'reservs':
    $pageTitle = "حسابي | الحجوزات";
    $cssFiles[] = "user_account/ordersTab.css";
    $jsFiles[] = "./orders.js";
    break;
  }
  
include "../../init.php"; 

if(isset($_SESSION['user'])){
  $userID = $_SESSION['user']['id'];
  $accountStatus = $_SESSION['user']['account_status'];
  if($accountStatus == 0){
    // account not verified, access not allowed, redirect to home
    header("location:../../index.php");
    exit();
  }else{
    // else  account is verified so get 
    // user data from session and show dashboard 
    $user = $_SESSION['user'];
  }
}else{
  // no registered account, access not allowed, redirect to home
  header("location:../../index.php");
  exit();
}
?>
<div class="dashboard d-flex">
  <!-- Include Sidebar section -->
  <?php include './sidebar.php'?>
  <!-- Start Content -->
  <div class="content w-full">
    <a class="exit" href="../../signOut.php?do=USER"><i class="fas fa-sign-out"></i></a>

    <?php
    switch ($currentTab){
      case "cart":
        include "./cartTab.php";
        break;
      case "orders":
        include "./ordersTab.php";
        break;
      case "reservs":
        include "./reservationTab.php";
        break;
      default://dashboard
        include "./accountTab.php";
      break;
    }
    ?>
  </div>
  <!-- End Content -->
</div>

<?php

include '../../includes/templates/footer.php';
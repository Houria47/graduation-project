<?php 
SESSION_START();
$pageTitle = "Admin Dashboard | Resto";
$css = "../layout/css/";
$js = "../layout/js/";
$func = "../includes/functions/";
$libs = "../includes/libraries/";
$cssFiles = ["dashboard/adminDashborad.css"];
$jsFiles = ["adminDashboard.js"];

if(!isset($_SESSION['admin'])){
  header("Location:../index.php");
  exit();
}
$currentTab = 'dashboard';
if(isset($_GET['tab']) && !empty($_GET['tab'])){
  $currentTab = $_GET['tab'];
}
?>
<div class="dashboard d-flex">
  <?php include './sidebar.php';?>

  <!-- Start Content -->
  <div class="content w-full">
    <header>
      <div class="triangle"></div>
      <div class="header">
        <h1>Resto Platform</h1>
        <p>Interactive Promotional Platform for Restaurants</p>
      </div>
    </header>
    <?php
    switch ($currentTab){
      case 'restaurants':
        $jsFiles[] = "restaurantTab.js";
        include "../init.php";
        include "./restaurantsTab.php";
      break;
      case 'users':
        $cssFiles[] = "dashboard/usersTab.css";
        $jsFiles[] = "usersTab.js";
        include "../init.php";
        include "./usersTab.php";
      break;
      default://dashboard
        $cssFiles[] = "dashboard/controlTab.css";
        $jsFiles[] = "./../layout/js/chartJS.js";
        $jsFiles[] = "controlTab.js";
        include "../init.php";
        include "./controlTab.php";
      break;
    }
    ?>
  </div><!-- Content div Closed Tag -->
  <!-- End Content -->
</div>
<?php
include '../includes/templates/footer.php';
<?php $selectedTab = isset($selectedTab)?$selectedTab:(isset($_GET['tab'])?$_GET['tab']:"");
// manage user account options
$ordersEL = "";
$userSignInLink = ROOTPATH . "user_account/registerLogin.php?do=login";
$userRegisterLink = ROOTPATH . "user_account/registerLogin.php?";
$userSignOutLink = ROOTPATH . "signOut.php?do=USER";
$userProfileLink = ROOTPATH . "user_account/dashboard/userDashboard.php";
$accountIconLink = $userSignInLink;
$userAccountOptions = "<a href='$userRegisterLink'>تسجيل حساب مستخدم</a>
<a href='$userSignInLink'>تسجيل دخول</a>";
// manage rest account options
$restLoginLink = ROOTPATH . "restaurant_account/restLogin.php";
$restRegisterLink = ROOTPATH . "restaurant_account/restRegister.php";
$restDashboardLink = ROOTPATH . "restaurant_account/dashboard/restDashboard.php";
$restSignOutLink = ROOTPATH . "signOut.php?do=REST";
$restAccountOptions = "<a href='$restRegisterLink'>تسجيل حساب مطعم</a>
<a href='$restLoginLink'>تسجيل دخول</a>";
if(isset($_SESSION['user'])){
  $accountIconLink = $userProfileLink;
  $userAccountOptions = "<a href='$userProfileLink'>حسابي</a>
  <a href='$userSignOutLink'>تسجيل خروج</a>";
  $numOfOrders = getUserCartItemsNum($_SESSION['user']['id']);// get num of orders for user
  $ordersEL = "<span>$numOfOrders</span>";
}
if(isset($_SESSION['restaurant'])){
    // rest
    $restAccountOptions = "<a href='$restDashboardLink'>مطعمي</a>
    <a href='$restSignOutLink'>تسجيل خروج</a>";
}
if(isset($_SESSION['admin'])){
  $accountIconLink = ROOTPATH . "dashboard/dashboard.php";
  $adminSignOutLink = ROOTPATH . "signOut.php?do=ADMIN";
  $userAccountOptions = "<a href='$accountIconLink'>لوحة التحكم</a>
  <a href='$adminSignOutLink'>تسجيل خروج</a>";
}
?>
<header class="sticky">
  <div class="home-header-1">
    <div class="mycontainer">
      <a href="<?=FULL_ROOTPATH . "/index.php"?>" class="logo">ReStO<i class="fas fa-shopping-basket"></i></a>

      <div class="search-box-container">
        <i class="fas fa-search"></i>
        <input type="search" id="search-box" placeholder="بحث" />
      </div>
    </div>
  </div>
  <nav class="home-header-2">
    <div class="mycontainer">
      <div id="menu-bar" class="fas fa-bars"></div>
      <ul>
        <li class="<?=$selectedTab == "main"?"active":""?>">
          <a href="<?=ROOTPATH . "index.php?tab=main"?>">الرئيسية</a>
        </li>
        <li class="<?=$selectedTab == "recipes"?"active":""?>">
          <a href="<?=ROOTPATH . "menus_recipes/recipes.php?tab=recipes"?>">الوجبات</a>
        </li>
        <li class="<?=$selectedTab == "rests"?"active":""?>">
          <a href="<?=ROOTPATH . "restaurants/restaurants.php?tab=rests"?>">المطاعم</a>
        </li>
        <li class="<?=$selectedTab == "ads"?"active":""?>">
          <a href="<?=ROOTPATH . "ads/posts.php?tab=ads"?>">العروض</a>
        </li>
        <li class="accounts">
          <a>الحساب</a>
          <div class="dropdown">
            <div class="top-trans"></div>
            <ul>
              <li>
                <h3><i class="fas fa-user"></i>حساب مستخدم</h3>
                <?=$userAccountOptions?>

              </li>
              <li>
                <h3><i class="fas fa-store"></i>حساب مطعم</h3>
                <?=$restAccountOptions?>
              </li>
            </ul>
          </div>
        </li>
      </ul>
      <div class="icons">
        <a id="navCartBtn" href="#" class="fa fa-cart-plus">
          <?=$ordersEL?>
        </a>
        <a href='<?=$accountIconLink?>' class='fas fa-user-circle'></a>
      </div>
    </div>
  </nav>
</header>
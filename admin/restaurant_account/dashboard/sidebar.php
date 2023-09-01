<div class="sidebar p-20 p-relative">
  <h3 class="p-relative txt-c mt-0"><?=$restaurant['name']?></h3>
  <ul>
    <li>
      <a class="<?=$currentTab == "dashboard" ?"active":""?> d-flex align-center p-10 fs-14 rad-6"
        href="restDashboard.php?tab=dashboard">
        <i class="fas fa-gear fa-fw"></i>
        <span>لوحة التحكم</span>
      </a>
    </li>
    <li>
      <a class="<?=$currentTab == "menus" ?"active":""?> d-flex align-center p-10 fs-14 rad-6"
        href="restDashboard.php?tab=menus">
        <i class="fas fa-chart-bar fa-fw"></i>
        <span>القوائم</span>
      </a>
    </li>
    <li>
      <a class="<?=$currentTab == "meals" ?"active":""?> d-flex align-center p-10 fs-14 rad-6"
        href="restDashboard.php?tab=meals">
        <i class="fas fa-bowl-food fa-fw"></i>
        <span>الوجبات</span>
      </a>
    </li>
    <li>
      <a class="<?=$currentTab == "ads" ?"active":""?> d-flex align-center p-10 fs-14 rad-6"
        href="restDashboard.php?tab=ads">
        <i class="fas fa-ad fa-fw"></i>
        <span>الإعلانات</span>
      </a>
    </li>
    <li>
      <a class=" <?=$currentTab == "orders" ?"active":""?> d-flex align-center p-10 fs-14 rad-6"
        href="restDashboard.php?tab=orders">
        <i class="fas fa-file-pen fa-fw"></i>
        <span>الطلبات</span>
      </a>
    </li>
    <li>
      <a class="<?=$currentTab == "reservations" ?"active":""?> d-flex align-center p-10 fs-14 rad-6"
        href="restDashboard.php?tab=reservations">
        <i class="fas fa-bookmark fa-fw"></i>
        <span>الحجوزات</span>
      </a>
    </li>
  </ul>
  <div class="platform-logo-links">
    <a href="../../index.php" class="logo">ReStO</a>
    <div class="icons between-flex mt-20">
      <a class="fb" href="#"><i class="fab fa-facebook-f fa-fw"></i></a>
      <a class="gl" href="#"><i class="fab fa-google fa-fw"></i></a>
      <a class="tw" href="#"><i class="fab fa-twitter fa-fw"></i></a>
    </div>
  </div>
</div>
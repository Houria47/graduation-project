<div class="sidebar bg-white p-20 p-relative">
  <div class="side-pro">
    <div class="box">
      <img data-userImage src="<?= $user['image']?>" alt="" />
    </div>
    <h3 data-userName class="p-relative txt-c mt-0"><?= $user['name']?></h3>
  </div>
  <ul>
    <li>
      <a class=" <?=$currentTab == "account" ?"active":""?> d-flex align-center p-10 fs-14 rad-6"
        href="userDashboard.php?tab=account">
        <i class="fas fa-user fa-fw"></i>
        <span>الحساب</span>
      </a>
    </li>
    <li>
      <a class="<?=$currentTab == "cart" ?"active":""?> d-flex align-center p-10 fs-14 rad-6"
        href="userDashboard.php?tab=cart">
        <i class="fa-solid fa-shopping-basket fa-fw"></i>
        <span>السلة</span>
      </a>
    </li>
    <li>
      <a class="<?=$currentTab == "orders" ?"active":""?> d-flex align-center p-10 fs-14 rad-6"
        href="userDashboard.php?tab=orders">
        <i class="fas fa-truck-fast fa-fw"></i>
        <span>طلباتي</span>
      </a>
    </li>
    <li>
      <a class="<?=$currentTab == "reservs" ?"active":""?> d-flex align-center p-10 fs-14 rad-6"
        href="userDashboard.php?tab=reservs">
        <i class="fas fa-bookmark fa-fw"></i>
        <span>حجوزاتي</span>
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
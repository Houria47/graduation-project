<div class="sidebar p-20 p-relative">
  <h3 class="p-relative txt-c mt-0"><a class="c-white" href="./../index.php">Resto Platform</a></h3>
  <ul>
    <li>
      <a class="<?=$currentTab=='dashboard'?'active':''?> d-flex align-center p-10 fs-14 rad-6" href="./dashboard.php">
        <i class="fa-regular fa-chart-bar fa-fw"></i>
        <span>لوحة التحكم</span>
      </a>
    </li>
    <li>
      <a class="<?=$currentTab=='restaurants'?'active':''?> d-flex align-center p-10 fs-14 rad-6"
        href="./dashboard.php?tab=restaurants">
        <i class="fa-solid fa-store-alt fa-fw"></i>
        <span>المطاعم</span>
      </a>
    </li>
    <li>
      <a class="<?=$currentTab=='users'?'active':''?> d-flex align-center p-10 fs-14 rad-6"
        href="./dashboard.php?tab=users">
        <i class="fa-regular fa-user fa-fw"></i>
        <span>المستخدمين</span>
      </a>
    </li>
  </ul>
</div>
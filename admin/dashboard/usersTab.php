<?php
$users = getUsersInfo();
$usersNum = count($users);
$usersVerifiedNum = getUserVerifiedAccountsNum();
$usersUnVerifiedNum = $usersNum - $usersVerifiedNum;

$verifiedPercentage = +bcdiv($usersVerifiedNum / $usersNum * 100,1,1);
$unVerifiedPercentage = 100 - $verifiedPercentage;
?>
<main>
  <h2 class="tab-title">المستخدمين</h2>
  <div class="rests-content tab">
    <section class="statics">
      <div class="colmn-1 rests-counters">
        <div class="my-counter my-card">
          <div class="wrapper">
            <span data-val="<?=$usersNum?>" class="number num-animation">0</span>
          </div>
          <h3>عدد الحسابات الكلي</h3>
        </div>
        <div class="my-counter my-card">
          <div class="wrapper">
            <span data-val="<?=$usersVerifiedNum?>" class="number num-animation"><?=$usersVerifiedNum?></span>
          </div>
          <h3>الحسابات المؤكدة</h3>
        </div>
        <div class="my-counter my-card">
          <div class="wrapper">
            <span data-val="<?=$usersUnVerifiedNum?>" class="number num-animation"><?=$usersUnVerifiedNum?></span>
          </div>
          <h3>الحسابات الغير مؤكدة</h3>
        </div>
        <figure class="pie-chart" style="--p1: <?=$verifiedPercentage?>%;  --p2: <?=$unVerifiedPercentage?>%;">
          <div id="pieChartinnerCaption" class="inner-caption">
            <span data-percentage="<?=$verifiedPercentage?>%">مؤكدة</span>
            <span data-percentage="<?=$unVerifiedPercentage?>%">غير مؤكدة</span>
          </div>
        </figure>
      </div>
    </section>
    <section class="rests-accounts my-card">
      <h3 class="component-title">حسابات الزبائن المسجلة بالمنصة:</h3>
      <div id="filter-rests-table" class="filters">
        <div data-val="-1" class="filter active">الكل</div>
        <div data-val="1" class="filter">المؤكدة</div>
        <div data-val="0" class="filter">الغير مؤكدة</div>
        <form id="searchUser">
          <button type="submit" class="fas fa-search"></button>
          <input name="searchText" type="search" placeholder="بحث">
        </form>
      </div>

      <div class="responsive-table">
        <table id="rests-accounts-table">
          <thead>
            <tr>
              <th>الاسم</th>
              <th>البريد</th>
              <th>العنوان</th>
              <th>رقم التواصل</th>
              <th>الحالة</th>
              <th>التحكم</th>
            </tr>
          </thead>
          <tbody>
            <?php
              if(count($users) > 0){
                $noUsersClass = "d-none";
                foreach($users as $user){
                  // user id
                  $id = $user['id'];
                  // get status 
                  $status = $user['account_status'];
                  // status word 
                  $statusWord= $status == 0?'غير مؤكد':'مؤكد';
                  // address
                  $address = !empty($user['address']) ? $user['address']:"<span>لا يوجد</span>";
                  
                  echo <<< "restRow"
                  <tr data-restID="$id" data-restStatus="$status">
                    <td>
                      <i class="row-info closed fa fas fa-angle-down"></i>
                      <span>{$user['name']}</span>
                    </td>
                    <td>{$user['email']}</td>
                    <td>$address</td>
                    <td>{$user['phone']}</td>
                    <td><span class="status s-$status">$statusWord</span></td>
                    <td>
                      <div
                        class="actions"
                        data-restName="{$user['name']}"
                        data-restID="$id">
                        <button class='table-btn del-btn'>حذف</button>
                      </div>
                    </td>
                  </tr>
                  restRow;
                }
                echo <<<"test"
                
                test;
              }else{
                $noUsersClass = "";
              }
            ?>
          </tbody>
          <tfoot class="<?=$noUsersClass?>">
            <tr>
              <td colspan="10">لا يوجد أية حقول.</td>
            </tr>
          </tfoot>
        </table>
      </div>
    </section>
  </div>
</main>
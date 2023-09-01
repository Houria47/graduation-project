<?php
// get restaurants information and statistics
date_default_timezone_set("Asia/Damascus");
$Restaurants = getRestaurantsInfo();
$statistics = calcRestaurantsSatistics($Restaurants);
if($Restaurants){
  // get Restaurants Statistics
  $statistics = calcRestaurantsSatistics($Restaurants);
  $statesPercentage = $statistics['statesPercentage'];
  $authenticatedNum = $statistics['authenticated'];
  $verifiedNum = $statistics['verified'];
  $totalAccountsNum = count($Restaurants);
  $unVerifiedNum = $totalAccountsNum - ($authenticatedNum + $verifiedNum);
  $authenticatedPercentage = +bcdiv($authenticatedNum / $totalAccountsNum * 100,1,1) ;
  $verifiedPercentage = +bcdiv($verifiedNum / $totalAccountsNum * 100,1,1);
  $unVerifiedPercentage = 100 - $authenticatedPercentage - $verifiedPercentage;
}else{
  $error = " حدث خطأ غير متوقع، لم نتمكن من جلب البيانات. " . __LINE__;
  echo $error;
  exit();// to show the error and fix it in case getting data failed...
}
?>
<main>
  <h2 class="tab-title">المطاعم</h2>
  <div class="rests-content tab">
    <section class="statics">
      <div class="colmn-1">
        <div class="rests-counters">
          <div class="my-counter my-card">
            <div class="wrapper">
              <span data-statistic="0" data-val="<?=$totalAccountsNum?>" class="number num-animation">0</span>
            </div>
            <h3>عدد المطاعم الكلي</h3>
          </div>
          <div class="my-counter my-card">
            <div class="wrapper">
              <span data-statistic="1" data-val="<?=$authenticatedNum?>" class="number num-animation">0</span>
            </div>
            <h3>المطاعم الموثَقة</h3>
          </div>
          <div class="my-counter my-card">
            <div class="wrapper">
              <span data-statistic="2" data-val="<?=$verifiedNum?>" class="number num-animation">0</span>
            </div>
            <h3>المطاعم المؤكدة</h3>
          </div>
          <div class="my-counter my-card">
            <div class="wrapper">
              <span data-statistic="3" data-val="<?=$unVerifiedNum?>" class="number num-animation">0</span>
            </div>
            <h3>المطاعم الغير مؤكدة</h3>
          </div>
        </div>
        <div class="my-card">
          <h3 class="component-title">
            نسبة المطاعم الفعالة (الموثَقة) في كل محافظة:
          </h3>
          <div id="restsColChart" class="my-chart">
            <?php
            foreach($statesPercentage as $state){
              $name=$state['name'];
              $percentage=$state["percentage"];
              echo <<< "label1"
                <div class='chart-bar'>
                  <span class='bar-percentage'>$percentage%</span>
                  <div class='chart-bar-inner'>
                    <div style='--height: $percentage%' class='inner-fill'></div>
                  </div>
                  <div class='chart-bar-title'>$name</div>
                </div>
              label1;
            }
            ?>
          </div>
        </div>
      </div>
      <div class="colmn-2 my-card">
        <div id="restsPieChart">
          <figcaption class="component-title">
            الحسابات المسجلة بالمنصة حسب حالتها:
          </figcaption>
          <figure class="pie-chart" style="--p1: <?=$authenticatedPercentage?>%;
                   --p2: <?=$unVerifiedPercentage?>%;
                   --p3: <?=$verifiedPercentage?>%">
            <div id="pieChartinnerCaption" class="inner-caption">
              <span data-p1 data-percentage="<?=$authenticatedPercentage?>%">موثَقة</span>
              <span data-p2 data-percentage="<?=$verifiedPercentage?>%">مؤكدة</span>
              <span data-p3 data-percentage="<?=$unVerifiedPercentage?>%">غير مؤكدة</span>
            </div>
          </figure>
        </div>
        <div id="waitingRestuarants" class="my-counter">
          <div class="wrapper">
            <span data-val="<?=$verifiedNum?>" class="number num-animation">0</span>
          </div>
          <h3>يوجد <?=$verifiedNum?> مطعم ينتظر التوثيق</h3>
        </div>
      </div>
    </section>
    <section class="rests-accounts my-card">
      <h3 class="component-title">حسابات المطاعم المسجلة بالمنصة:</h3>
      <div id="filter-rests-table" class="filters">
        <div data-val="-1" class="filter active">الكل</div>
        <div data-val="2" class="filter">الموثّقة</div>
        <div data-val="1" class="filter">المؤكدة</div>
        <div data-val="0" class="filter">الغير مؤكدة</div>
        <Select class="select-filter" name="state">
          <option value="-1">جميع المحافظات</option>
          <?php
            foreach($statesPercentage as $stateID => $state){
              echo "<option value='$stateID'>{$state['name']}</option>";
            }
            ?>
        </Select>
      </div>

      <div class="responsive-table">
        <table id="rests-accounts-table">
          <thead>
            <tr>
              <th>الاسم</th>
              <th>المحافظة</th>
              <th>البريد</th>
              <th>رقم التواصل</th>
              <th>التقييم</th>
              <th>الحالة</th>
              <th>التحكم</th>
              <th>الوثائق المرفقة</th>
            </tr>
          </thead>
          <tbody>
            <?php
              if(count($Restaurants) > 0){
                foreach($Restaurants as $restaurant){
                  // restaurant id
                  $id = $restaurant['id'];
                  // get status 
                  $status = $restaurant['account_status'];
                  // status word 
                  $statusWord= $status == 0?'غير مؤكد':($status == 1? 'مؤكد':'موثّق');
                  // get state from default address
                  $addressState = count($restaurant['addresses']) > 0 ?
                                 $restaurant['addresses'][0]['state']
                                 :"لا يوجد";
                  // authenticattion and deletion buttons
                  $actionsContent = "<button class='table-btn del-btn'>حذف</button>";
                  if($status == 1){
                    $actionsContent = "<button class='table-btn del-btn'>حذف</button><button class='table-btn verify-btn'>توثيق</button>";
                  }
                  // authentication files control
                  $filesNum = count($restaurant['authentication_files']);
                  if($status != 2 && $filesNum > 0){
                    $unit = $filesNum >= 3 ? 'ملفات' : 'ملف';
                    $filesContent = "<span>$filesNum $unit</span>
                    <button
                      data-showFilesBtn='$id'
                      class='table-btn show-files-btn closed'>
                      عرض
                    </button>";
                  }else{
                    $filesContent = "<span>لا يوجد</span>";
                  }
                  // rate content
                  $rateContent = "<span>لا يوجد</span>";
                  if($status == 2){
                    $rateContent = "<div class='ratings'>
                        <div class='empty-stars'></div>
                        <div class='full-stars' style='width: {$restaurant['ratePercenatge']}%'></div>
                      </div>";
                  }
                  echo <<< "restRow"
                  <tr data-restID="$id" data-restStatus="$status">
                    <td>
                      <i class="row-info closed fa fas fa-angle-down"></i>
                      <a href="./restaurants/restaurant_profile/restProfile.php?restID=$id">
                        {$restaurant['name']}
                      </a>
                    </td>
                    <td>{$addressState}</td>
                    <td>{$restaurant['email']}</td>
                    <td>{$restaurant['phone']}</td>
                    <td class="rest-rate">
                      $rateContent
                    </td>
                    <td><span class="status s-$status">$statusWord</span></td>
                    <td>
                      <div
                        class="actions"
                        data-restName="{$restaurant['name']}"
                        data-restID="$id">
                        $actionsContent
                      </div>
                    </td>
                    <td>
                      <div class="actions">
                        $filesContent
                      </div>
                    </td>
                  </tr>
                  restRow;
                }
              }else{
                // there is no accounts, show message
                echo <<< "footerMessage"
                  <tfoot>
                    <tr>
                      <td colspan="10">لا يوجد أية حقول.</td>
                    </tr>
                  </tfoot>
                footerMessage;
              }
            ?>
          </tbody>
          <tfoot class="d-none">
            <tr>
              <td colspan="10">لا يوجد أية حقول.</td>
            </tr>
          </tfoot>
        </table>
      </div>
    </section>
  </div>
</main>
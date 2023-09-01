<?php
  $reservations = getRestResrvations($restID);
  $reservsNum = count($reservations['reservations']);


  $pendingPersentage = $processingPersentage = $cancledPersentage = $donePersentage = 0;

  if($reservsNum > 0){
    $pendingPersentage = +bcdiv($reservations['pending'] / $reservsNum * 100,1,1);
    $processingPersentage = +bcdiv($reservations['processing'] / $reservsNum * 100,1,1);
    $cancledPersentage = +bcdiv($reservations['cancled'] / $reservsNum * 100,1,1);
    $donePersentage = 100 - ($pendingPersentage + $processingPersentage + $cancledPersentage);
  }

?>
<h2 class="tab-title">
  <span>الحجوزات</span>
</h2>
<main class="orders-content" data-restID="<?=$restID?>">
  <section class="numbers">
    <div class="o-card">
      <img src="./../../layout/images/Standard/pending-reserv.png" alt="" />
      <div>
        <h3 class="num"><?=$reservations['pending']?></h3>
        <span>معلق</span>
      </div>
    </div>
    <div class="o-card">
      <img src="./../../layout/images/Standard/processing-reserve.png" alt="" />
      <div>
        <h3 class="num"><?=$reservations['processing']?></h3>
        <span>مؤكد</span>
      </div>
    </div>
    <div class="o-card">
      <img src="./../../layout/images/Standard/canceld-reserv.png" alt="" />
      <div>
        <h3 class="num"><?=$reservations['cancled']?></h3>
        <span>ملغي/مرفوض</span>
      </div>
    </div>
    <div class="o-card">
      <img src="./../../layout/images/Standard/reserv-done.png" alt="" />
      <div>
        <h3 class="num"><?=$reservations['done']?></h3>
        <span>منقضي</span>
      </div>
    </div>
  </section>
  <section class="statistic-section">
    <div class="o-card pie-chart-card">
      <figure class="pie-chart" style="
      --p1: <?=$pendingPersentage?>%; 
      --p2: <?=$processingPersentage?>%; 
      --p3: <?=$cancledPersentage?>%; 
      --p4: <?=$donePersentage?>%">
        <div id="pieChartinnerCaption" class="inner-caption">
          <span data-p1 data-percentage="<?=$pendingPersentage?>%">معلقة</span>
          <span data-p2 data-percentage="<?=$processingPersentage?>%">مؤكدة</span>
          <span data-p3 data-percentage="<?=$cancledPersentage?>%">ملغية/مرفوضة</span>
          <span data-p4 data-percentage="<?=$donePersentage?>%">منقضية</span>
        </div>
      </figure>
      <div class="orders-info">
        <img src="./../../layout/images/Standard/reserv_statistic.png" alt="" />
        <div>
          <div>
            <span class="num-unit">إجمالي الطلبات</span>
            <span class="num"><?=$reservsNum?></span>
            <span class="num-unit"><?=$reservsNum >=3 && $reservsNum <=10?"طلبات":"طلب"?></span>
          </div>
          <?=$reservations['pending'] == 0 ? "<p>لا يوجد طلبات جديدة</p>":"<p>يوجد {$reservations['pending']} طلب ينتظر التأكيد</p>"?>
        </div>
      </div>
    </div>
    <div class="o-card line-chart-card">
      <canvas id="ordersChart"></canvas>
    </div>
  </section>
  <div class="sec-title">الحجوزات</div>
  <section class="orders-list">
    <?php
    if($reservsNum == 0){
      $noService = $_SESSION['restaurant']['reserv_service'] != 0? "":"
      <p>خدمة الحجز غير مفعلة في حسابك!، لن يتمكن الزبائن من إرسال طلبات حجز ما لم تفعل الخدمة أولاً. </p>
      ";
      echo <<< "noOrders"
      <table id="orders-table">
      <thead>
        <tr>
          <th>#</th>
          <th>الزبون</th>
          <th>
            <div class="center-flex gap-1">
              <img data-date-filter class="filter" src="./../../layout/images/up-down-arrows.png" /> تاريخ الحجز
            </div>
          </th>
          <th>الحالة</th>
          <th>
            <div class="center-flex gap-1">
              <img data-amount-filter class="filter" src="./../../layout/images/up-down-arrows.png" /> قائمة الطلبات
            </div>
          </th>
          <th>التحكم</th>
        </tr>
      </thead>
      <tbody>
      <tr> <td colspan="7" class="no-items o-card">
        لا يوجد طلبات
        $noService
      </td></tr>
      </tbody>
      noOrders;
    }else{
      echo <<< "ordersHead"
      <table id="orders-table">
        <thead>
          <tr>
            <th>#</th>
            <th>الزبون</th>
            <th>
              <div class="center-flex gap-1">
                <img data-date-filter class="filter" src="./../../layout/images/up-down-arrows.png" /> تاريخ الحجز
              </div>
            </th>
            <th>الحالة</th>
            <th>
              <div class="center-flex gap-1">
                <img data-resrveDate-filter class="filter" src="./../../layout/images/up-down-arrows.png" /> الموعد
              </div>
            </th>
            <th>التحكم</th>
          </tr>
        </thead>
        <tbody>
      ordersHead;
      echo getOrdersContent($reservations['reservations']);
      echo <<< "ordersEnd"
        </tbody>
      </table>
      ordersEnd;
    }
    ?>
  </section>
</main>

<?php
function getOrdersContent($reservs){
  
  $content = '';
  foreach($reservs as $reserv){
    $status = $reserv['status']['id'];
    $statusClass= $status == 1? "s1"
    :($status == 3? "s2"
    :($status == 2 || $status == 5? "s3":"s4"));

    $total = number_format($reserv['total']);

    $date = timeAgo($reserv['created_at']);
    $reservDate = dateFormater($reserv['date'],"d/M , hh:mm a");
    // $itemsNum = count($reserv['items']);
    // $itemsNumText = $itemsNum == 1? "طلب واحد"
    // :($itemsNum == 2? "طلبين":"$itemsNum طلبات");
    // if($itemsNum == 0) $itemsNumText = "لم تحدد";
    $content .= "<tr 
    data-reservID = '{$reserv['id']}'
    data-date = '{$reserv['created_at']}'
    data-resrveDate = '{$reserv['date']}'>
    <td>#{$reserv['id']}</td>
    <td>
      <div class='user-info'>
        <img
          class='user-img'
          src='{$reserv['customer_image']}'/>
        <h3>{$reserv['customer_name']}</h3>
      </div>
    </td>
    <td class='date'>$date</td>
    <td><div data-order-status class='order-status $statusClass'>{$reserv['status']['name']}</div></td>
    <td>
      <button data-details-btn class='details-btn'>
      $reservDate <i class='fas fa-angle-down'></i>
      </button>
    </td>
    <td class='actions-td p-relative'>
      <i class='fas fa-ellipsis-v'></i>
      <div class='actions'>
        <button data-deletereserv>حذف</button>
      </div>
    </td>
  </tr>
  <tr data-orderDetailsRow = '{$reserv['id']}' class='order-details'>
    <td colspan='7'>
      <div class='parent'>
    ";
    
  if(count($reserv['items']) > 0 ){
    $content .= "
    <div>
      <div class='checkout'>
        <table>
        <thead>
          <tr>
            <th>#</th>
            <th>الوجبة</th>
            <th>السعر</th>
            <th>الكمية</th>
            <th>المجموع</th>
          </tr>
        </thead>
        <tbody>";
    $content .= getReservItemsContent($reserv['items']);
    $content .= "</tbody>
      </table>
    </div>
    <div class='total-box'>
      <div class='around-flex'>
        <h3>الإجمالي</h3>
        <div>
          <span class='num'>$total</span>
          <span class='unit'>ل.س</span>
        </div>
      </div>
    </div>
  </div>
      ";
  }else{
    $content .= "<div class='o-card'>لا يوجد طلبات</div>";
  }
  $content .="";
  $content .= getCustomerInfo($reserv);
  $content .= "
    </div>
    </td>
  </tr>";
  }
  return $content;
}

function getReservItemsContent($items){
  $content = '';
  foreach($items as $item){
    $price = number_format($item['price']);
    $total = number_format($item['price'] * $item['quantity']);
    $content .= "<tr>
      <td>
        <div class='img-box'>
          <img
            src='{$item['image']}'
          />
        </div>
      </td>
      <td>{$item['name']}</td>
      <td>$price</td>
      <td>{$item['quantity']}</td>
      <td>$total</td>
    </tr>";
  }
  return $content;
}

function getCustomerInfo($reserv){
  $notes = empty($reserv['notes'])? "-":$reserv['notes'];

  $date = dateFormater($reserv['date'],"YYY MMMM d , hh:mm a");
  $content = "<div class='user-details'>
    <img src='{$reserv['customer_image']}' />
    <h5>{$reserv['customer_name']}</h5>
    <div class='det-row'>
      <span>حجز باسم</span>
      <p>{$reserv['name']}</p>
    </div>
    <div class='det-row'>
      <span>الرقم</span>
      <p>{$reserv['phone']}</p>
    </div>
    <div class='det-row'>
      <span>تاريخ الحجز</span>
      <p>$date</p>
    </div>
    <div class='det-row'>
      <span>الملاحظات</span>
      <p>$notes</p>
    </div>
  </div>";
  return $content;
}
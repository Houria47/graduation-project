<?php
  $orders = getRestOrders($restID);
  $ordersNum = count($orders['orders']);

  $pendingPersentage = $processingPersentage = $cancledPersentage = $donePersentage = 0;

  if($ordersNum > 0){
    $pendingPersentage = +bcdiv($orders['pending_orders'] / $ordersNum * 100,1,1);
    $processingPersentage = +bcdiv($orders['processing_orders'] / $ordersNum * 100,1,1);
    $cancledPersentage = +bcdiv($orders['cancled_orders'] / $ordersNum * 100,1,1);
    $donePersentage = 100 - ($pendingPersentage + $processingPersentage + $cancledPersentage);
  }
?>
<h2 class="tab-title">
  <span>الطلبات</span>
</h2>
<main class="orders-content" data-restID="<?=$restID?>">
  <section class="numbers">
    <div class="o-card">
      <img src="./../../layout/images/Standard/pending-order.png" alt="" />
      <div>
        <h3 class="num"><?=$orders['pending_orders']?></h3>
        <span>معلق</span>
      </div>
    </div>
    <div class="o-card">
      <img src="./../../layout/images/Standard/processing-order.png" alt="" />
      <div>
        <h3 class="num"><?=$orders['processing_orders']?></h3>
        <span>قيد التحضير/التوصيل</span>
      </div>
    </div>
    <div class="o-card">
      <img src="./../../layout/images/Standard/canceld-order.png" alt="" />
      <div>
        <h3 class="num"><?=$orders['cancled_orders']?></h3>
        <span>ملغي/مرفوض</span>
      </div>
    </div>
    <div class="o-card">
      <img src="./../../layout/images/Standard/done-order.png" alt="" />
      <div>
        <h3 class="num"><?=$orders['done_orders']?></h3>
        <span>مُسلم</span>
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
          <span data-p2 data-percentage="<?=$processingPersentage?>%">قيد التحضير</span>
          <span data-p3 data-percentage="<?=$cancledPersentage?>%">ملغية/مرفوضة</span>
          <span data-p4 data-percentage="<?=$donePersentage?>%">مٌسلّمة</span>
        </div>
      </figure>
      <div class="orders-info">
        <img src="./../../layout/images/Standard/order-statistic.png" alt="" />
        <div>
          <div>
            <span class="num-unit">إجمالي الطلبات</span>
            <span class="num"><?=$ordersNum?></span>
            <span class="num-unit"><?=$ordersNum >=3 && $ordersNum <=10?"طلبات":"طلب"?></span>
          </div>
          <?=$orders['pending_orders'] == 0 ? "<p>لا يوجد طلبات جديدة</p>":"<p>يوجد {$orders['pending_orders']} طلب ينتظر التأكيد</p>"?>
        </div>
      </div>
    </div>
    <div class="o-card line-chart-card">
      <canvas id="ordersChart"></canvas>
    </div>
  </section>
  <div class="sec-title">الطلبات</div>
  <section class="orders-list">
    <?php
    if($ordersNum == 0){
      $noService = $_SESSION['restaurant']['delivery_service'] != 0? "":"
      <p>خدمة التوصيل غير مفعلة في حسابك!، لن يتمكن الزبائن من إرسال طلبات ما لم تفعل الخدمة أولاً. </p>
      ";
      echo <<< "noOrders"
      <table id="orders-table">
      <thead>
        <tr>
          <th>#</th>
          <th>الزبون</th>
          <th>
            <div class="center-flex gap-1">
            <img data-date-filter class="filter" src="./../../layout/images/up-down-arrows.png" /> تاريخ الطلب
            </div>
          </th>
          <th>الحالة</th>
          <th>
          <div class="center-flex gap-1">
            <img data-total-filter class="filter" src="./../../layout/images/up-down-arrows.png" /> الإجمالي
            </div>
          
          </th>
          <th>قائمة الطلبات</th>
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
                <img data-date-filter class="filter" src="./../../layout/images/up-down-arrows.png" /> تاريخ الطلب
              </div>
            </th>
            <th>الحالة</th>
            <th>
              <div class="center-flex gap-1">
                <img data-total-filter class="filter" src="./../../layout/images/up-down-arrows.png" /> الإجمالي
              </div>
            </th>
            <th>
            <div class="center-flex gap-1">
              <img data-amount-filter class="filter" src="./../../layout/images/up-down-arrows.png" /> قائمة الطلبات
              </div>
            </th>
            <th>التحكم</th>
          </tr>
        </thead>
        <tbody>
      ordersHead;
      echo getOrdersContent($orders['orders']);
      echo <<< "ordersEnd"
        </tbody>
      </table>
      ordersEnd;
    }
    ?>
  </section>
</main>

<?php
function getOrdersContent($orders){
  
  $delivary_fee = $_SESSION['restaurant']['delivary_fee'];
  $delivary_fee_formated = number_format($delivary_fee);
  
  $content = '';
  foreach($orders as $order){
    $status = $order['status']['id'];
    $statusClass= $status == 1? "s1"
    :($status == 2 || $status == 3? "s2"
    :($status == 4 || $status == 6? "s3":"s4"));

    $total = number_format($order['total']);

    $date = timeAgo($order['date']);

    $itemsNum = count($order['items']);
    $itemsNumText = $itemsNum == 1? "طلب واحد"
    :($itemsNum == 2? "طلبين":"$itemsNum طلبات");
    $content .= "<tr 
    data-orderID = '{$order['id']}' 
    data-total = '{$order['total']}'
    data-date = '{$order['date']}'
    data-amount = '$itemsNum'>
    <td>#{$order['id']}</td>
    <td>
      <div class='user-info'>
        <img
          class='user-img'
          src='{$order['customer_image']}'/>
        <h3>{$order['customer_name']}</h3>
      </div>
    </td>
    <td class='date'>$date</td>
    <td><div data-order-status class='order-status $statusClass'>{$order['status']['name']}</div></td>
    <td>{$total}<span class='unit'>ل.س</span></td>
    <td>
      <button data-details-btn class='details-btn'>
        $itemsNumText <i class='fas fa-angle-down'></i>
      </button>
    </td>
    <td class='actions-td p-relative'>
      <i class='fas fa-ellipsis-v'></i>
      <div class='actions'>
        <button data-deleteOrder>حذف</button>
      </div>
    </td>
  </tr>
  <tr data-orderDetailsRow = '{$order['id']}' class='order-details'>
    <td colspan='7'>
      <div class='parent'>
    ";
    
  if(count($order['items']) > 0 ){
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
    $content .= getOrderItemsContent($order['items']);
    $sum = number_format($order['total'] + $delivary_fee);
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
      <div class='around-flex'>
        <h3>تكلفة التوصيل</h3>
        <div>
          <span class='num'>$delivary_fee_formated</span>
          <span class='unit'>ل.س</span>
        </div>
      </div>
      <div class='around-flex total'>
        <h3>المجموع</h3>
        <div>
          <span class='num'>$sum</span>
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
  $content .= getCustomerInfo($order);
  $content .= "
    </div>
    </td>
  </tr>";
  }
  return $content;
}

function getOrderItemsContent($items){
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

function getCustomerInfo($order){
  $notes = empty($order['notes'])? "-":$order['notes'];

  $date = dateFormater($order['date'],"YYY MMMM d , hh:mm");
  $content = "<div class='user-details'>
    <img src='{$order['customer_image']}' />
    <h5>{$order['customer_name']}</h5>
    <div class='det-row'>
      <span>العنوان</span>
      <p>{$order['address']}</p>
    </div>
    <div class='det-row'>
      <span>الرقم</span>
      <p>{$order['phone']}</p>
    </div>
    <div class='det-row'>
      <span>تاريخ الطلب</span>
      <p>$date</p>
    </div>
    <div class='det-row'>
      <span>الملاحظات</span>
      <p>$notes</p>
    </div>
  </div>";
  return $content;
}
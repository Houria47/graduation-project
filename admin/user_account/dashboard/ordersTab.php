<?php
$orders = getOrders($userID);
$selectedOrder = [];
$selectedClass = "";

$news = '';
$ordresContent = '';
?>
<h2 class="tab-title">
  <span>طلباتي</span>
</h2>
<main class="orders" data-userID="<?=$userID?>">
  <?php
  if(count($orders) == 0){
    echo <<< "noOrders"
    <div class="between-flex head">
      <ul class="news no-scrol flex-1">
      <li class="no-news">
        <div>
        <h3>لا يوجد طلبات! </h3>
        <p>يمكنك استعراض السلة وإضافة الطلبات من تبوية السلة</p>
        <a href="?tab=cart">إضافة طلبات</a></div>
        <img src="./../../layout/images/search-recipes.png" alt="" />
      </li>
      </ul>
    </div>
    noOrders;
  }else{
    getContent();

  ?>
  <div class="between-flex head">
    <ul class="news flex-1">
      <?=$news?>
    </ul>
    <img src="./../../layout/images/search-recipes.png" alt="" />
  </div>
  <div>
    <h3 class="component-title">الطلبات</h3>
    <div class="table-container">
      <div class="flex-1">
        <div id="filters" class="filters">
          <span data-val="-1" class="selected">الكل</span>
          <span data-val="1">معلقة</span>
          <span data-val="6">ملغية</span>
          <span data-val="4">مرفوضة</span>
          <span data-val="5">مسلمة</span>
          <span data-val="2">مقبولة</span>
        </div>
        <table id="items-table" class="my-table w-full">
          <thead>
            <tr>
              <th>#</th>
              <th>المطعم</th>
              <th>الإجمالي</th>
              <th>الحالة</th>
              <th><span class="d-none"></span></th>
            </tr>
          </thead>
          <tbody>
            <?=$ordersContent?>
            <tr class="no-items d-none ">
              <td colspan="5">لا يوجد طلبات </td>
            </tr>
          </tbody>
        </table>

      </div>
      <div id="preview" class="preview flex-1">
        <h3>طلب رقم <?=$selectedOrder['id']?></h3>
        <div class="order-info">
          <div>
            <span>الاسم</span>
            <span><?=$selectedOrder['name']?></span>
          </div>
          <div>
            <span>العنوان</span>
            <span><?=$selectedOrder['address']?></span>
          </div>
          <div>
            <span>الرقم</span>
            <span><?=$selectedOrder['phone']?></span>
          </div>
          <div>
            <span>الملاحظات</span>
            <span><?=empty($selectedOrder['notes'])?"-":$selectedOrder['notes']?></span>
          </div>
        </div>
        <h4>الطلبات</h4>
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
          <tbody>
            <?php
            foreach($selectedOrder['items'] as $item){
              $price = number_format($item['price']);
              $subTotal = number_format($item['price'] * $item['quantity']);
              echo <<< "order"
            <tr>
              <td>
                <div class="img-box">
                  <img src="{$item['image']}" alt="" />
                </div>
              </td>
              <td><a href="./../../menus_recipes/recipePreview.php?recipeID={$item['recipe_id']}">{$item['name']}</a></td>
              <td>$price</td>
              <td>{$item['quantity']}x</td>
              <td>$subTotal</td>
            </tr>
            order;
            }
            ?>
          </tbody>
        </table>
        <div class="order-total">
          <div class="between-flex">
            <span>تكلفة التوصيل</span>
            <span><?=number_format($selectedOrder['restFee'])?> <span class="unit">ل.س</span></span>
          </div>
          <div class="between-flex">
            <span>الإجمالي</span>
            <span><?=number_format($selectedOrder['restFee'] + $selectedOrder['total'])?><span
                class="unit">ل.س</span></span>
          </div>
        </div>
      </div>
    </div>
  </div>
  <?php
  }
  ?>
</main>

<?php
function getContent(){
  global $orders,$news,$ordersContent;

  foreach($orders as $order){
    $news .= getNewsContent($order);
    $ordersContent .= getordersContent($order);
  }
  if(empty($news)){
    $news = "<li>لم تتم الموافقة على أية طلبات</li>";
  }
}
function getNewsContent($order){
  $content = '';
  $status = '';
  $msg = "";
  if($order['status']['id'] == 4){
    $status = "s2";
    $msg = "تم رفض طلبك: طلب رقم <span>{$order['id']}</span> ";
  }else if($order['status']['id'] == 3 || $order['status']['id'] == 2){
    
    $status = "s1";
    $msg = "تم قبول طلبك: طلب رقم <span>{$order['id']}</span> ";
    if($order['status']['id'] == 2){
      $msg .= "قيد التحضير ";
    }else{
      $msg .= "قيد التوصيل ";
    }
  } else if($order['status']['id'] == 6){
    $status = "s3";
    $msg = "تم إلغاء طلبك: طلب رقم <span>{$order['id']}</span> ";
  }else{
    return;
  }
  $content = "<li class='$status'>
    <img src='{$order['restImage']}' alt='' />
    <div>
      <h3>{$order['restName']}</h3>
      <p>$msg<span></span></p>
    </div>
  </li>";
  return $content;
}
function getordersContent($order){
  global $selectedCalss,$selectedOrder;
  $content = '';
  $selectedCalss = false;

  if(!$selectedOrder){
    $selectedOrder = $order;
    $selectedCalss = "selected";
  }

  $total = number_format($order['total']);
  $status = $order['status']['id'];

  $action = $status == 1?"<button data-del><i class='fas fa-times'></i>إلغاء</button>":"";
  
  $status = $status == 1?"s4"
  :(($status == 2 || $status == 3)?"s2"
  :($status==5?"s1":"s3"));

  $content ="
  <tr data-itemID = {$order['id']} data-status =  {$order['status']['id']} class='$selectedCalss'>
    <td>{$order['id']}</td>
    <td>{$order['restName']}</td>
    <td>$total</td>
    <td class='$status s'>{$order['status']['name']}</td>
    <td>
      $action
    </td>
  </tr>
  ";
  return $content;
}
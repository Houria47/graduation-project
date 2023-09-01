<?php
$reservations = getResrvations($userID);
$selectedReservation = [];
$selectedClass = "";


$news = '';
$reservationsContent = '';
?>
<h2 class="tab-title">
  <span>حجوزاتي</span>
</h2>
<main class="reservs" data-userID="<?=$userID?>">
  <?php
  if(count($reservations) == 0){
    echo <<< "noreservations"
    <div class="between-flex head">
      <ul class="news no-scrol flex-1">
      <li class="no-news">
      <div>
      <h3>لا يوجد حجوزات! </h3>
      <p>يمكنك استعراض وإضافة حجوزات للمطاعم المميزة من صفحة المطاعم</p>
      <a href="./../../restaurants/restaurants.php">إضافة حجوزات</a></div>
      <img src="./../../layout/images/reservation.png" alt="" />
    </li>
      </ul>
    </div>
    noreservations;
  }else{
    getContent();
  ?>
  <div class="between-flex head">
    <ul class="news flex-1">
      <?=$news?>
    </ul>
    <img src="./../../layout/images/reservation.png" alt="" />

  </div>
  <div>
    <h3 class="component-title">الحجوزات</h3>
    <div class="table-container">
      <div class="flex-1">
        <div id="filters" class="filters">
          <span data-val="-1" class="selected">الكل</span>
          <span data-val="1">معلق</span>
          <span data-val="2">مرفوض</span>
          <span data-val="3">مؤكد</span>
          <span data-val="4">منقضي</span>
          <span data-val="5">ملغي</span>
        </div>
        <table id="items-table" class="my-table w-full">
          <thead>
            <tr>
              <th>#</th>
              <th>المطعم</th>
              <th>الموعد</th>
              <th>الحالة</th>
              <th><span class="d-none"></span></th>
            </tr>
          </thead>
          <tbody>
            <?=$reservationsContent?>
            <tr class="no-items d-none ">
              <td colspan="5">لا يوجد حجوزات </td>
            </tr>
          </tbody>
        </table>

      </div>
      <div id="preview" class="preview flex-1">
        <h3>حجز رقم <?=$selectedReservation['id']?></h3>
        <div class="order-info">
          <div>
            <span>الاسم</span>
            <span><?=$selectedReservation['name']?></span>
          </div>
          <div>
            <span>الموعد</span>
            <span><?=dateFormater($selectedReservation['date'],"d/M , hh:mm a")?></span>
          </div>
          <div>
            <span>الرقم</span>
            <span><?=$selectedReservation['phone']?></span>
          </div>
          <div>
            <span>الملاحظات</span>
            <span><?=empty($selectedReservation['notes'])?"-":$selectedReservation['notes']?></span>
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
            foreach($selectedReservation['items'] as $item){
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
            <span>الإجمالي</span>
            <span><?=number_format($selectedReservation['total'])?><span class="unit">ل.س</span></span>
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
  global $reservations,$news,$reservationsContent;

  foreach($reservations as $reserv){
    $news .= getNewsContent($reserv);
    $reservationsContent .= getreservationsContent($reserv);
  }
  if(empty($news)){
    $news = "<li>لم يتم تأكيد على أية حجوزات</li>";
  }
}
function getNewsContent($reserv){
  $content = '';
  $status = '';
  $msg = "";
  if($reserv['status']['id'] == 2){
    $status = "s2";
    $msg = "تم رفض حجزك: حجز رقم <span>{$reserv['id']}</span> ";
  }else if($reserv['status']['id'] == 3){
    $status = "s1";
    $msg = "تم تأكيد حجزك: حجز رقم <span>{$reserv['id']}</span> ";
  } else {
    return;
  }
  $content = "<li class='$status'>
    <img src='{$reserv['restImage']}' alt='' />
    <div>
      <h3>{$reserv['restName']}</h3>
      <p>$msg<span></span></p>
    </div>
  </li>";
  return $content;
}
function getreservationsContent($reserv){
  global $selectedCalss,$selectedReservation;
  $content = '';
  $selectedCalss = false;

  if(!$selectedReservation){
    $selectedReservation = $reserv;
    $selectedCalss = "selected";
  }

  $date = dateFormater($reserv['date'],"d/M , hh:mm a");
  $status = $reserv['status']['id'];

  $action = $status == 1?"<button data-del><i class='fas fa-times'></i>إلغاء</button>":"";
  
  $status = $status == 1?"s4"
  :(($status == 2 || $status == 3)?"s2"
  :($status==5?"s1":"s3"));

  $content ="
  <tr data-itemID = {$reserv['id']} data-status =  {$reserv['status']['id']} class='$selectedCalss'>
    <td>{$reserv['id']}</td>
    <td>{$reserv['restName']}</td>
    <td>$date</td>
    <td class='$status s'>{$reserv['status']['name']}</td>
    <td>
      $action
    </td>
  </tr>
  ";
  return $content;
}
<?php
SESSION_START();

$pageTitle = "إرسال طلبية | Resto";
$needsNav = true;
$needsFooter = true;
// files directories
$css = "../layout/css/";
$js = "../layout/js/";
$func = "../includes/functions/";
$cssFiles = ["order.css"];
include "../init.php";
$restID =  $_GET['restID'];
if(isset($_SESSION['user'])){
  $order = getUserRestOrder($restID,$_SESSION['user']['id']);
}else{
  // no session! get the f out of here
  header("Loaction:../index.php");
  exit();
}
echo "<main data-restID='$restID' class='order-page'>";
if(count($order) > 0){
?>
<form id="order-form" class="mycontainer">
  <section class="order-list">
    <h2 class="my-title">قائمة الطلبات</h2>
    <div class="box">
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
            foreach($order["items"] as $orderItem){
              $subTotal = $orderItem['recipe']['price'] * $orderItem['quantity'];
              $subTotal = number_format($subTotal);
              $price = number_format($orderItem['recipe']['price']);
              echo <<< "orderItem"
              <tr data-itemID="{$orderItem['id']}">
                <td>
                  <div class="img-box">
                    <img src="{$orderItem['recipe']['image']}" alt="" />
                  </div>
                </td>
                <td>{$orderItem['recipe']['name']}</td>
                <td>{$price}</td>
                <td>
                  <input class="qty" 
                  value="{$orderItem['quantity']}"
                  type="number" 
                  min="1" 
                  oninput="onlyNums(this)"
                  onblur="emptyQTY(this)" />
                </td>
                <td data-subTotal>{$subTotal}</td>
              </tr>
              orderItem;
            }
            ?>

        </tbody>
      </table>
    </div>
    <div class="box total-box">
      <div class="between-flex">
        <h3>الإجمالي</h3>
        <div>
          <span data-total class="num"><?=number_format($order['total'])?></span>
          <span class="unit">ل.س</span>
        </div>
      </div>
      <div class="between-flex">
        <h3>تكلفة التوصيل</h3>
        <div>
          <span data-fee class="num"><?=number_format($order['delivary_fee'])?></span>
          <span class="unit">ل.س</span>
        </div>
      </div>
      <div class="between-flex total">
        <h3>المجموع</h3>
        <div>
          <span data-total-fee class="num"><?=number_format($order['total'] + $order['delivary_fee'])?></span>
          <span class="unit">ل.س</span>
        </div>
      </div>
    </div>
  </section>
  <section class="customer-info">
    <h2 class="my-title">معلومات التوصيل</h2>
    <div class="box">
      <div class="input">
        <label for="name">الاسم</label>
        <input id="name" name="name" type="text" value="<?=$_SESSION['user']['name']?>" />
        <small id="name-msg"></small>
      </div>
      <div class="input">
        <label for="address">العنوان</label>
        <input id="address" name="address" type="text" value="<?=$_SESSION['user']['address']?>" />
        <small id="address-msg"></small>
      </div>
      <div class="input">
        <label for="phone">رقم التواصل</label>
        <input id="phone" name="phone" type="text" oninput="onlyNums(this)" maxlength="10"
          value="<?=$_SESSION['user']['phone']?>" />
        <small id="phone-msg"></small>
      </div>
      <div class="input">
        <label for="notes">ملاحظات</label>
        <textarea name="notes" name="" id="notes" rows="5"></textarea>
      </div>
    </div>
  </section>
  <button>إرسال</button>
</form>
<?php
}else{
  echo <<< "noOrder"
  <div class="no-orders">
    <div>
      <p class="box">لم تقم بإضافة طلبات من هذا المطعم.</p>
      <a href="../menus_recipes/recipes.php">إضافة وجبات</a>
    </div>
  </div>
noOrder;
}
echo "</main>";
$jsFiles = ["order.js"];
include '../includes/templates/footer.php';
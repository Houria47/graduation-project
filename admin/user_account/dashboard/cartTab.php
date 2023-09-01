<?php
$cart = getUserCart($userID);
?>
<h2 class="tab-title">
  <span>السلة</span>
</h2>
<main data-userID="<?=$userID?>">
  <?php
  if(count($cart) > 0){
    $selectedRecipe = false;
    // echo cart header tags
    echo <<< "cartHead"
    <div class='cart'>
      <div class='rest-carts'>
    cartHead;
    // echo rest cart items
    foreach($cart as $cartItem){
      echo <<< "cartItemHead"
      <div class="my-card rest-cart" data-restID={$cartItem['restaurant']['id']} >
        <div class="rest-img">
          <img src="{$cartItem['restaurant']['cover']}" alt="" />
        </div>
        <div class="cart-content">
          <div class="cart-title">
            <img class="rest-logo" src="{$cartItem['restaurant']['logo']}" />
            <h2>وجبات من مطعم <a href="./../../restaurants/restProfile.php?id={$cartItem['restaurant']['id']}">{$cartItem['restaurant']['name']}</a></h2>
            <a class="order-link" href="./../../menus_recipes/order.php?restID={$cartItem['restaurant']['id']}">إطلب</a>
          </div>
          <div class="table-container">
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
      cartItemHead;
      // echo rest cart items
      foreach($cartItem['items'] as $item){
        $selectedCalss = "";
        if(!$selectedRecipe){
          $selectedRecipe = [
            "restaurant" =>$cartItem['restaurant'],
            "recipe" => $item['recipe']
          ];
          $selectedCalss = "active";
        }

        $price = $item['recipe']['price'];
        if($item['recipe']['discount_price'] != null){
          $price = $item['recipe']['discount_price'];
        }
        $subTotal = number_format($price * $item['quantity']);
        $formatedPrice = number_format($price);
        echo <<< "recipe"
                <tr class="$selectedCalss"
                 data-recipe-preview
                 data-recipeID="{$item['recipe']['id']}" 
                 data-itemID="{$item['id']}">
                  <td>
                    <div class="img-box" data-del-item>
                      <img src= "{$item['recipe']['image']}" alt="" />
                    </div>
                  </td>
                  <td>{$item['recipe']['name']}</td>
                  <td>$formatedPrice</td>
                  <td>
                    <input
                      class="qty"
                      value="{$item['quantity']}"
                      type="number"
                      min="1"
                      oninput="onlyNums(this)"
                      onblur="emptyQTY(this)"
                    />
                  </td>
                  <td data-subTotal>$subTotal</td>
                </tr>
        recipe;
      }
      $totalWithFee = number_format($cartItem['total'] + $cartItem['delivary_fee']);
      $totalWithoutFee = number_format($cartItem['total']);

      $delivary_fee = number_format($cartItem['delivary_fee']);
      echo <<< "cartItemEnd"
              </tbody>
            </table>
          </div>
          <div class="total-box">
            <div class="between-flex">
              <h3>الإجمالي</h3>
              <div>
                <span data-total class="num">{$totalWithoutFee}</span>
                <span class="unit">ل.س</span>
              </div>
            </div>
            <div class="between-flex">
              <h3>تكلفة التوصيل</h3>
              <div>
                <span data-fee class="num">{$delivary_fee}</span>
                <span class="unit">ل.س</span>
              </div>
            </div>
            <div class="between-flex total">
              <h3>المجموع</h3>
              <div>
                <span data-total-fee class="num">$totalWithFee</span>
                <span class="unit">ل.س</span>
              </div>
            </div>
          </div>
        </div>
      </div>
      cartItemEnd;
    }
    // echo rest carts close tags and overview open tag
    echo <<< "cartMid"
      </div>
      <div id="overView" class="my-card overview">
    cartMid;
    // echo overview card content
    $currPrice = number_format($selectedRecipe['recipe']['price']);
    $discount = "";
    if($selectedRecipe['recipe']['discount_price'] != null){
      $discount = "{$currPrice}<span class='unit'>ل.س</span>";
      $currPrice = number_format($selectedRecipe['recipe']['discount_price']);
    }
    $addresses = $selectedRecipe['restaurant']['addresses'];
    $addContent = "";
    foreach($addresses as $address){
      $addContent .= "<li>{$address['state']}, {$address['region']}, {$address['street']}</li>";
    }
    $openTime = dateFormater($selectedRecipe['restaurant']['open_time'],"hh:mm a");
    $closeTime = dateFormater($selectedRecipe['restaurant']['close_time'],"hh:mm a");
    echo <<< "overviewContent"
    <div class="img-box">
      <img src="{$selectedRecipe['recipe']['image']}" alt="" />
    </div>
    <h3 class="ovreview-title">{$selectedRecipe['recipe']['name']}</h3>
    <div class="view-content">
      <div class="infoo">
        <div class="label">السعر:</div>
        <div class="flex-1 price">
          <span>$currPrice<span class="unit">ل.س</span></span>
          <span class="discount">$discount</span>
        </div>
      </div>
      <div class="infoo">
        <div class="label">الوصف:</div>
        <p class="flex-1">{$selectedRecipe['recipe']['description']}</p>
      </div>
      <div class="infoo rest">
        <img src="{$selectedRecipe['restaurant']['logo']}" alt="" />
        <div class="flex-1">
          <h3>{$selectedRecipe['restaurant']['name']}</h3>
          <div>
            <span class="label">أوقات الدوام:</span>
            <span>من $openTime إلى $closeTime</span>
          </div>
          
          <div>
            <span class="label">التواصل:</span>
            <span>{$selectedRecipe['restaurant']['phone']}</span>
          </div>
          <div>
            <span class="label"> العناوين:</span>
            <ul>
              $addContent
            </ul>
          </div>
        </div>
      </div>
    </div>
    overviewContent;
    // echo cart and overview close tags
    echo <<< "cartEnd"
      </div>
    </div>
    cartEnd;
  }else{
    echo <<<"emptyCart"
    <div class="empty-cart">
      <div>
        <h3>السلة فارغة..!</h3>
        <p>يمكنك استعراض الوجبات وإضافتها للسلة من خلال صفحة الوجبات</p>
        <a href="./../../menus_recipes/recipes.php">عرض الوجبات</a>
      </div>
      <img src="./../../layout/images/empty-cart.jpg" alt="" />
    </div>
    emptyCart;
  }
  ?>
</main>
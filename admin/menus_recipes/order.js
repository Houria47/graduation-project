// get order on laod
let Order = { total: 0, delivary_fee: 0, items: [] };
$(document).ready(function () {
  let restID = $("[data-restID]").attr("data-restID");
  $.ajax({
    method: "GET",
    url: "./../ajax_requests/order/getOrder.php",
    data: { restID },
    success: (res) => {
      console.log(res);
      res = JSON.parse(res);
      console.log(res);
      Order = res.order;
    },
  });
});
function getOrderItem(id) {
  let index = Order.items.findIndex((item) => item.id == id);
  return Order.items[index];
}
function setQTY(id, qty) {
  Order.items.map((el, idx, arr) => {
    if (el.id == id) {
      arr[idx].quantity = qty;
    }
  });
}
// on order item quantity change
$(document).on("keyup , input", ".order-list table input", function (e) {
  let enterdValue = parseInt(e.target.value);
  // get order item id
  let itemID = $(this).parents("[data-itemID]").attr("data-itemID");
  let item = getOrderItem(itemID);
  // get subtotal element
  let subTotalEl = $(this).parents("tr").find("[data-subTotal]");
  // calc old & new subtotal value
  let oldSubTotal = item.recipe.price * item.quantity;
  let subtotal = item.recipe.price;
  if (enterdValue != 0 && e.target.value.length != 0) {
    subtotal = enterdValue * item.recipe.price;
    setQTY(itemID, enterdValue);
  } else {
    setQTY(itemID, 1);
  }
  // get total and total with fee  elements
  let totalEl = $(".total-box [data-total]");
  let totalFeeEl = $(".total-box [data-total-fee]");

  // calc & change total
  let total = Order.total - oldSubTotal + subtotal;
  Order.total = total;
  // update total
  totalEl.html(total.toLocaleString("us"));
  let totalFee = total + Order.delivary_fee;
  totalFeeEl.html(totalFee.toLocaleString("us"));
  subTotalEl.html(subtotal.toLocaleString("us"));
});
// NOTE: finish this shit later
// on delete order item
// $(document).on("click", ".order-list table [data-del-item]", function () {
//   let itemsNum = $(".order-list table tbody tr").length;
//   if (itemsNum == 1) {
//   }
// });

// on order submit
$(document).on("submit", "#order-form", function (e) {
  e.preventDefault();
  let validator = new ValidateIt();
  $("#name-msg").html("");
  $("#address-msg").html("");

  if (validator.isEmpty(this.name.value)) {
    $("#name-msg").html("حقل الاسم لا يجب أن يكون فارغ");
    $(this.name).parent().addClass("invalid");
  }
  if (validator.isEmpty(this.address.value)) {
    $("#address-msg").html("حقل العنوان لا يجب أن يكون فارغ");
    $(this.address).parent().addClass("invalid");
  }
  if (validator.phone(this.phone.value, $("#phone-msg"))) {
    $(this.phone).parent().addClass("invalid");
  }
  if (validator.isValid && Order.items.length > 0) {
    // submit
    let userInfo = {
      name: this.name.value,
      address: this.address.value,
      notes: this.notes.value,
      phone: this.phone.value,
    };
    $.ajax({
      method: "POST",
      url: "./../ajax_requests/order/addOrder.php",
      data: { ...userInfo, items: Order.items },
      success: (res) => {
        console.log(res);
        res = JSON.parse(res);
        console.log(res);
        if (res.result) {
          $("main").html(getCheckout(userInfo));
          $(navCartBtn).find("span").html(res.cart_size);
        } else {
          $("main").html(getCheckoutError(res.message));
        }
      },
    });
  } //else not valid input
});
// on input focus
$(document).on("input", "#order-form .invalid input", function (e) {
  console.log($(this).parent());
  $(this).parent().removeClass("invalid");
});

function getCheckout(userInfo) {
  let checkout = $(`<div class="checkout"></div>`);
  let items = ``;
  Order.items.map((item) => {
    items += ` <tr>
    <td>
      <div class="img-box">
        <img
          src="${item.recipe.image}"
          alt=""
        />
      </div>
    </td>
    <td>${item.recipe.name}</td>
    <td>${item.recipe.price}</td>
    <td>${item.quantity}x</td>
    <td data-subTotal>${item.recipe.price * item.quantity}</td>
  </tr>`;
  });
  checkout.append(`<h2 class="my-title">تم إرسال الطلبية</h2>
  <div class="box">
    <div class="order-info">
      <div>
        <span>الاسم</span>
        <span>${userInfo.name}</span>
      </div>
      <div>
        <span>العنوان</span>
        <span>${userInfo.address}</span>
      </div>
      <div>
        <span>الرقم</span>
        <span>${userInfo.phone}</span>
      </div>
      <div>
        <span>الملاحظات</span>
        <span>${userInfo.notes.length == 0 ? "-" : userInfo.notes}</span>
      </div>
    </div>
    <h3>الطلبات</h3>
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
        ${items}
      </tbody>
    </table>
  </div>
  <div class="box order-total">
    <div class="between-flex">
      <span>تكلفة التوصيل</span>
      <span>${Order.delivary_fee} <span class="unit">ل.س</span></span>
    </div>
    <div class="between-flex">
      <span>الإجمالي</span>
      <span>${Order.total}<span class="unit">ل.س</span></span>
    </div>
  </div>
  <a href="./../index.php">عودة للصفحة الرئيسية</a>`);
  return checkout;
}

function getCheckoutError(msg) {
  let checkout = $(`<div class="checkout"></div>`);
  checkout.append(`<div class="checkout has-error">
  <h2 class="my-title">لم يتم إرسال الطلبية</h2>
  <div class="box">
    <p>${msg}</p>
  </div>
  <a href="#">عودة</a>
</div>`);
  return checkout;
}

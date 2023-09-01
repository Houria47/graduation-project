let Recipes = [];

function getRecipeItem(id) {
  let index = Recipes.findIndex((item) => item.id == id);
  return Recipes[index];
}

function getPrice(recpie) {
  if (recpie.discount != null) {
    return recpie.discount;
  }
  return recpie.price;
}
let Order = [];
let Total = 0;
let totalEl = $("[data-total]");

function getOrderItem(recipeID) {
  let index = Order.findIndex((item) => item.recipeID == recipeID);
  return Order[index];
}
function removeOrderItem(recipeID) {
  Total = Total - getOrderItem(recipeID).total;
  Order = Order.filter((item) => item.recipeID != recipeID);
}
function addOrderItem(recipeID) {
  let price = getPrice(getRecipeItem(recipeID));
  Total += price;
  Order.push({ recipeID, quantity: 1, price, total: price });
}
function setQTY(recipeID, qty) {
  Order.map((el, idx, arr) => {
    if (el.recipeID == recipeID) {
      arr[idx].quantity = qty;
    }
  });
}
// get order on laod

$(document).ready(function () {
  let restID = $("[data-restID]").attr("data-restID");
  $.ajax({
    method: "GET",
    url: "./../ajax_requests/getRestaurantRecipesAndMenus.php",
    data: { restID },
    success: (res) => {
      res = JSON.parse(res);
      Recipes = res.recipes;
    },
  });
});

$(document).on("click", "[data-recipeID]", function () {
  let recipeID = $(this).data("recipeid");
  if ($(this).hasClass("selected")) {
    removeOrderItem(recipeID);
    removeRow(recipeID);
  } else {
    addOrderItem(recipeID);
    addRow(recipeID);
  }
  $(this).toggleClass("selected");
  $(this).find(".check").toggleClass("checked");
});
function addRow(recipeID) {
  let recipe = getRecipeItem(recipeID);
  let price = getPrice(recipe).toLocaleString("us");

  let row = $(`<tr data-itemID="${recipe.id}">
    <td>
      <div class="img-box">
        <img src="${recipe.image.path}" />
      </div>
    </td>
    <td>${recipe.name}</td>
    <td>${price}</td>
    <td>
      <input
        class="qty"
        value="1"
        type="number"
        min="1"
        oninput="onlyNums(this)"
        onblur="emptyQTY(this)"
      />
    </td>
    <td data-subTotal>${price}</td>
  </tr>`);

  $("tr.no-items").remove();
  row.hide().prependTo("#order tbody").fadeIn(300);
  totalEl.html(Total.toLocaleString("us"));
}

function removeRow(recipeID) {
  $(`#order tbody tr[data-itemID=${recipeID}]`).fadeOut(300, function () {
    $(this).remove();
    totalEl.html(Total.toLocaleString("us"));
    if ($("#order tbody tr[data-itemID]").length == 0) {
      $("#order tbody").append(
        $(`<tr class="no-items">
      <td colspan="5" class="p-10">
        لم تتم إضافة طلبات
      </td>
    </tr>`)
      );
    }
  });
}
// on order item quantity change
$(document).on("keyup , input", "#order input", function (e) {
  let enterdValue = parseInt(e.target.value);
  // get order item id
  let itemID = $(this).parents("[data-itemID]").attr("data-itemID");
  let item = getOrderItem(itemID);
  // calc old & new subtotal value
  let oldSubTotal = item.price * item.quantity;
  let subtotal = item.price;
  if (enterdValue != 0 && e.target.value.length != 0) {
    subtotal = enterdValue * item.price;
    setQTY(itemID, enterdValue);
  } else {
    setQTY(itemID, 1);
  }
  // get subtotal element
  let subTotalEl = $(this).parents("tr").find("[data-subTotal]");
  // calc & change total
  Total = Total - oldSubTotal + subtotal;
  item.total = subtotal;
  // update total
  totalEl.html(Total.toLocaleString("us"));
  subTotalEl.html(subtotal.toLocaleString("us"));
});

$(document).on("submit", "#reserve-form", function (e) {
  e.preventDefault();
  let validator = new ValidateIt();
  $("#name-msg").html("");
  $("#date-msg").html("");
  $("#guestsNum-msg").html("");

  if (validator.isEmpty(this.name.value)) {
    $("#name-msg").html("حقل الاسم لا يجب أن يكون فارغ");
    $(this.name).parent().addClass("invalid");
  }
  let isNotValidDate = new Date(this.date.value) < new Date();
  if (validator.isEmpty(this.date.value)) {
    $("#date-msg").html("يرجى تحديد موعد الحجز");
    $(this.date).parent().addClass("invalid");
  } else {
    if (isNotValidDate) {
      $("#date-msg").html("موعد الحجز يجب أن يكون بعد التاريخ الحالي");
      $(this.date).parent().addClass("invalid");
    }
  }
  if (validator.isEmpty(this.guestsNum.value)) {
    $("#guestsNum-msg").html("يرجى تحديد عد الضيوف");
    $(this.guestsNum).parent().addClass("invalid");
  }
  if (validator.phone(this.phone.value, $("#phone-msg"))) {
    $(this.phone).parent().addClass("invalid");
  }
  if (validator.isValid && !isNotValidDate) {
    let restID = $("[data-restID]").attr("data-restID");
    // submit
    let reservInfo = {
      name: this.name.value,
      notes: this.notes.value,
      phone: this.phone.value,
      date: this.date.value,
      guestsNum: this.guestsNum.value,
    };
    let items = Order;
    if (Order.length == 0) {
      items = null;
    }
    $.ajax({
      method: "POST",
      url: "./../ajax_requests/reservation/addReservation.php",
      data: { ...reservInfo, items, restID },
      success: (res) => {
        console.log(res);
        res = JSON.parse(res);
        console.log(res);
        if (res.result) {
          $("main").html(getCheckout(reservInfo, res.message));
        } else {
          $("main").html(getCheckoutError(res.message));
        }
      },
    });
  } else {
    document
      .getElementById("reserve-form")
      .scrollIntoView({ behavior: "smooth" });
  }
});

// on input focus
$(document).on("input", "#reserve-form .invalid input", function (e) {
  $(this).parent().removeClass("invalid");
  $(this).siblings("small").html("");
});

function getCheckout(reservInfo, msg) {
  let checkout = $(`<div class="checkout"></div>`);

  let orders = `<div>
    <span>الطلبات</span>
    <span>-</span>
  </div>`;
  let total = "";
  if (Order.length > 0) {
    let items = ``;

    Order.map((item) => {
      let recipe = getRecipeItem(item.recipeID);
      items += ` <tr>
      <td>
        <div class="img-box">
          <img src="${recipe.image.path}"  />
        </div>
      </td>
      <td>${recipe.name}</td>
      <td>${item.price.toLocaleString("us")}</td>
      <td>${item.quantity}x</td>
      <td data-subTotal>${item.total.toLocaleString("us")}</td>
    </tr>`;
    });

    orders = `<h3>الطلبات</h3>
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
    </table>`;
    total = `<div class="total between-flex box">
    <span>الإجمالي</span>
    <span>${Total.toLocaleString("us")}<span class="unit">ل.س</span></span>
  </div>`;
  }
  checkout.append(`
  <div class="head">
    <h2>تم إرسال طلب الحجز</h2>
    <p>${msg}</p>
  </div>
  <div class="box">
    <div class="order-info">
      <div>
        <span>الاسم</span>
        <span>${reservInfo.name}</span>
      </div>
      <div>
        <span>الرقم</span>
        <span>${reservInfo.phone}</span>
      </div>
      <div>
        <span>موعد الحجز</span>
        <span>${new Date(reservInfo.date).toLocaleDateString("ar-EG", {
          month: "long",
          day: "numeric",
          hour: "2-digit",
          minute: "2-digit",
        })}</span>
      </div>
      <div>
        <span>عدد الضيوف</span>
        <span>${reservInfo.guestsNum}</span>
      </div>
      <div>
        <span>الملاحظات</span>
        <span>${reservInfo.notes.length == 0 ? "-" : reservInfo.notes}</span>
      </div>
    </div>
    ${orders}
  </div>
  ${total}
  <a href="./../index.php">عودة للصفحة الرئيسية</a>`);
  return checkout;
}

function getCheckoutError(msg) {
  let checkout = $(`<div class="checkout"></div>`);
  checkout.append(`<div class="checkout has-error">
  <div class="head">
    <h2>لم يتم الحجز!</h2>
  </div>
  <div class="box">
    <p>${msg}</p>
  </div>
  <a href="javascript:window.location.reload(true)">عودة</a>
</div>`);
  return checkout;
}

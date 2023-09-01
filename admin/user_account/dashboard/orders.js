let Orders = null;
let Reservs = null;

function getOrderItem(id) {
  let index = Orders.findIndex((item) => item.id == id);
  return Orders[index];
}

function getReservItem(id) {
  let index = Reservs.findIndex((item) => item.id == id);
  return Reservs[index];
}

$(document).ready(function () {
  if ($(".orders").length > 0) {
    $.ajax({
      method: "GET",
      url: "./../../ajax_requests/order/getOrders.php",
      data: {},
      success: (res) => {
        res = JSON.parse(res);
        Orders = res.items;
      },
    });
  }
  if ($(".reservs").length > 0) {
    $.ajax({
      method: "GET",
      url: "./../../ajax_requests/reservation/getReservations.php",
      data: {},
      success: (res) => {
        res = JSON.parse(res);
        Reservs = res.items;
      },
    });
  }
});

$(document).on("click", ".orders #items-table tbody tr", function (e) {
  if (e.target.hasAttribute("data-del") || $(this).hasClass("no-items")) {
    return;
  }
  if (!$(this).hasClass("selected")) {
    let itemID = $(this).attr("data-itemID");
    $("#preview").html(getPreviewContent(itemID, 0));
  }
  $("#items-table tbody tr.selected").removeClass("selected");
  $(this).addClass("selected");
});

function getPreviewContent(itemID, type) {
  let items = "";

  let item = null;

  let option = "";
  let fee = "";

  if (type == 0) {
    item = getOrderItem(itemID);
    option = `<span>العنوان</span>
    <span>${item["address"]}</span>`;
    fee = `<div class="between-flex">
    <span>تكلفة التوصيل</span>
    <span>${item["restFee"].toLocaleString(
      "us"
    )} <span class="unit">ل.س</span></span>
  </div>`;
  } else {
    item = getReservItem(itemID);
    option = `<span>الموعد</span>
    <span>${item["date"]}</span>`;
  }

  let total = "";
  if (item.items.length != 0) {
    items += `
    <h4>الطلبات</h4><table>
    <thead>
      <tr>
        <th>#</th>
        <th>الوجبة</th>
        <th>السعر</th>
        <th>الكمية</th>
        <th>المجموع</th>
      </tr>
    </thead>
    <tbody>`;

    item.items.forEach((el) => {
      let price = el["price"].toLocaleString("us");
      let subTotal = (el["price"] * el["quantity"]).toLocaleString("us");

      items += `<tr>
      <td>
        <div class="img-box">
          <img src="${el["image"]}" alt="" />
        </div>
      </td>
      <td><a href="./../../menus_recipes/recipePreview.php?recipeID=${el["recipe_id"]}">${el["name"]}</a></td>
      <td>${price}</td>
      <td>${el["quantity"]}x</td>
      <td>${subTotal}</td>
    </tr>`;
    });
    items += `
    </tbody>
    </table>`;
    let withFee = type == 0 ? item["restFee"] : 0;
    total = `<div class="order-total">
    ${fee}
    <div class="between-flex">
      <span>الإجمالي</span>
      <span>${(withFee + item["total"]).toLocaleString("us")}<span
          class="unit">ل.س</span></span>
    </div>`;
  } else {
    items = `<div>
    <span>الطلبات</span>
    <span>لم تحدد طلبات</span>
  </div>`;
  }

  let content = `<h3>${type == 0 ? "طلب" : "حجز"} رقم ${item["id"]}</h3>
  <div class="order-info">
    <div>
      <span>الاسم</span>
      <span>${item["name"]}</span>
    </div>
    <div>
      ${option}
    </div>
    <div>
      <span>الرقم</span>
      <span>${item["phone"]}</span>
    </div>
    <div>
      <span>الملاحظات</span>
      <span>${item["notes"].length == 0 ? "-" : item["notes"].length}</span>
    </div>
    ${items}
  </div>
  
  ${total}
  </div>`;
  return content;
}

$(document).on("click", ".orders #filters span", function () {
  if (!$(this).hasClass("selected")) {
    $("#filters span.selected").removeClass("selected");
    $(this).addClass("selected");

    let value = $(this).data("val");
    let rows = $("#items-table tbody tr");

    let num = 0;
    rows.each(function (el) {
      let status = $(this).data("status");

      if (value == -1 || value == status || (value == 2 && status == 3)) {
        $(this).removeClass("d-none");
        num++;
      } else {
        $(this).addClass("d-none");
      }
    });

    if (num == 0) {
      $("#items-table tbody tr.no-items").removeClass("d-none");
    } else {
      $("#items-table tbody tr.no-items").addClass("d-none");
    }
  }
});

$(document).on("click", ".orders [data-del]", function () {
  let itemID = $(this).parents("[data-itemID]").data("itemid");

  $("body").prepend(`<div class="popup-overlay allow-close">
  <div id="changeStatus" class="popup-box">
    <header>
      <h2>إلغاء طلب - طلب رقم #${itemID}</h2>
    </header>
    <main>
      <p>هل أنت متأكد من إلغاء الطلب؟</p>
    </main>
    <footer>
      <button onClick="confirmDeleteOrder(${itemID})" class="popup-btn">
        إلغاء الطلب
      </button>
      <button class="popup-btn-alt js-close-popup">إلغاء</button>
    </footer>
  </div>
</div>`);
});

function confirmDeleteOrder(orderID) {
  $.ajax({
    method: "POST",
    url: "./../../ajax_requests/order/deleteOrder.php",
    data: { orderID },
    success: (res) => {
      res = JSON.parse(res);
      let msgClasses = res.result ? "success" : "error";
      $(".popup-box main").html(
        `<p class="my-alert ${msgClasses}">${res.message}</p>`
      );
      $(".popup-box footer").html(
        `<button class="popup-btn-alt js-close-popup">إغلاق</button>`
      );

      if (res.result) {
        Orders = Orders.filter((item) => item.id != orderID);
        if ($(`[data-itemID]`).length == 1) {
          $("#items-table tbody .no-items").removeClass("d-none");
        } else {
          $(`[data-itemID=${orderID}]`).fadeOut(function () {
            $(this).remove();
          });
        }
      }
    },
  });
}

// handle reservations
$(document).on("click", ".reservs #items-table tbody tr", function (e) {
  if (e.target.hasAttribute("data-del") || $(this).hasClass("no-items")) {
    return;
  }
  if (!$(this).hasClass("selected")) {
    let itemID = $(this).attr("data-itemID");
    $("#preview").html(getPreviewContent(itemID, 1));
  }
  $("#items-table tbody tr.selected").removeClass("selected");
  $(this).addClass("selected");
});

$(document).on("click", ".reservs #filters span", function () {
  if (!$(this).hasClass("selected")) {
    $("#filters span.selected").removeClass("selected");
    $(this).addClass("selected");

    let value = $(this).data("val");
    let rows = $("#items-table tbody tr");

    let num = 0;
    rows.each(function (el) {
      let status = $(this).data("status");

      if (value == -1 || value == status) {
        $(this).removeClass("d-none");
        num++;
      } else {
        $(this).addClass("d-none");
      }
    });

    if (num == 0) {
      $("#items-table tbody tr.no-items").removeClass("d-none");
    } else {
      $("#items-table tbody tr.no-items").addClass("d-none");
    }
  }
});

$(document).on("click", ".reservs [data-del]", function () {
  let itemID = $(this).parents("[data-itemID]").data("itemid");

  $("body").prepend(`<div class="popup-overlay allow-close">
  <div id="changeStatus" class="popup-box">
    <header>
      <h2>إلغاء حجز - حجز رقم #${itemID}</h2>
    </header>
    <main>
      <p>هل أنت متأكد من إلغاء الحجز؟</p>
    </main>
    <footer>
      <button onClick="confirmDeleteReserv(${itemID})" class="popup-btn">
        إلغاء الحجز
      </button>
      <button class="popup-btn-alt js-close-popup">إلغاء</button>
    </footer>
  </div>
</div>`);
});

function confirmDeleteReserv(reservID) {
  $.ajax({
    method: "POST",
    url: "./../../ajax_requests/reservation/deleteReservation.php",
    data: { reservID },
    success: (res) => {
      console.log(res);
      res = JSON.parse(res);
      let msgClasses = res.result ? "success" : "error";
      $(".popup-box main").html(
        `<p class="my-alert ${msgClasses}">${res.message}</p>`
      );
      $(".popup-box footer").html(
        `<button class="popup-btn-alt js-close-popup">إغلاق</button>`
      );

      if (res.result) {
        Reservs = Reservs.filter((item) => item.id != Reservs);
        if ($(`[data-itemID]`).length == 1) {
          $("#items-table tbody .no-items").removeClass("d-none");
        } else {
          $(`[data-itemID=${reservID}]`).fadeOut(function () {
            $(this).remove();
          });
        }
      }
    },
  });
}

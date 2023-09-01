let SelectBoxItOptions = {
  // Uses the jQueryUI theme for the drop down
  theme: "jqueryui",
};
$("select").selectBoxIt(SelectBoxItOptions);

const ordersChart = document.getElementById("ordersChart");
const Labels = [
  "كانون الثاني",
  "شباط",
  "آذار",
  "نيسان",
  "أيار",
  "حزيران",
  "تموز",
  "آب",
  "أيلول",
  "تشرين الأول",
  "تشرين الثاني",
  "كانون الأول",
];

let lineChartData = [];
let lineChart = new Chart(ordersChart, {
  type: "line",
  data: {
    labels: Labels,
    datasets: [
      {
        label: "عدد الطلبات لعام 2023",
        data: lineChartData,
        borderWidth: 1,
      },
    ],
  },
  options: {
    scales: {
      y: {
        beginAtZero: true,
      },
    },
  },
});
// get order on laod
let Orders = [];

let Status = [];

function getStatusName(id) {
  let index = Status.findIndex((status) => status.id == id);
  return Status[index].status;
}

$(document).ready(function () {
  let restID = $("[data-restID]").attr("data-restID");

  $.ajax({
    method: "GET",
    url: "./../../ajax_requests/restaurant-requests/orders-repuests/getOrders.php",
    data: { restID },
    success: (res) => {
      res = JSON.parse(res);
      console.log(res);
      Orders = res.orders;
      Status = res.status;
      setupLineChart(Orders);
    },
  });
});

function setupLineChart(data) {
  let currentYear = new Date().getFullYear();
  let currentMonth = new Date().getMonth();
  ordersMonthData = data
    .filter((order) => order.status.id == 5)
    .map((order) => new Date(order.date));

  let datasetValues = {};
  ordersMonthData.forEach(function (x) {
    if (x.getFullYear() == currentYear) {
      datasetValues[x.getMonth()] = (datasetValues[x.getMonth()] || 0) + 1;
    }
  });
  let dataset = [];
  for (let i = 0; i <= currentMonth; i++) {
    let value = datasetValues[i] || 0;
    dataset.push(value);
  }
  lineChart.data.datasets[0].data = dataset;
  lineChart.update();
}

$(document).on("click", "[data-details-btn]", function () {
  let orderID = $(this).parents("[data-orderID]").attr("data-orderID");
  let isActive = $(`[data-orderDetailsRow='${orderID}']`).hasClass("active");

  $(`[data-details-btn] i`).removeClass("fa-angle-up");
  $(`[data-details-btn] i`).addClass("fa-angle-down");
  $(`[data-orderDetailsRow]`).removeClass("active");
  if (!isActive) {
    $(`[data-orderDetailsRow='${orderID}']`).addClass("active");
    $(this).find("i").removeClass("fa-angle-down");
    $(this).find("i").addClass("fa-angle-up");
  }
});

$(document).on("click", "[data-order-status]", function () {
  let orderID = $(this).parents("[data-orderID]").attr("data-orderID");
  $("body").prepend(getStatusModal(orderID));
});

function getStatusModal(orderID) {
  let statusOptions = "";
  Status.map((status) => {
    statusOptions += `<option value='${status.id}'>${status.status}</option>`;
  });

  let modal = $(`<div class="popup-overlay allow-close">
  <form id = "changeStatus" class="popup-box">
    <header>
      <h2>تعديل حالة الطلب - طلب رقم #${orderID}</h2>
    </header>
    <main>
      <p>الحالة</p>
      <select name="status">
      ${statusOptions}
      </select>
      <input name="orderID" value="${orderID}" hidden>
    </main>
    <footer>
      <button type="submit" class="popup-btn">حفظ</button>
      <button class="popup-btn-alt js-close-popup">إلغاء</button>
    </footer>
  </form>
</div>`);
  modal.find("select").selectBoxIt(SelectBoxItOptions);
  return modal;
}
$(document).on("submit", "#changeStatus", function (e) {
  e.preventDefault();
  let orderID = this.orderID.value;
  let status = this.status.value;
  $.ajax({
    method: "POST",
    url: "./../../ajax_requests/restaurant-requests/orders-repuests/changeOrderStatus.php",
    data: { orderID, status },
    success: (res) => {
      console.log(res);
      res = JSON.parse(res);
      console.log(res);
      let msgClasses = res.result ? "success" : "error";
      $(".popup-box main").html(
        `<p class="my-alert ${msgClasses}">${res.message}</p>`
      );
      $(".popup-box footer").html(
        `<button class="popup-btn-alt js-close-popup">إغلاق</button>`
      );

      if (res.result) {
        let statusClass =
          status == 1
            ? "s1"
            : status == 2 || status == 3
            ? "s2"
            : status == 4 || status == 6
            ? "s3"
            : "s4";
        $(`[data-orderID=${orderID}] .order-status`).attr(
          "class",
          `order-status ${statusClass}`
        );
        $(`[data-orderID=${orderID}] .order-status`).html(
          getStatusName(status)
        );
      }
    },
  });
});

$(document).on("click", "#orders-table [data-total-filter]", function () {
  let table = $("#orders-table > tbody");

  OrdersRows = $("#orders-table > tbody > tr[data-orderID]");

  let condition = (a, b) => (a > b ? 1 : -1);
  if ($(this).hasClass("active")) {
    condition = (a, b) => (a < b ? 1 : -1);
  }
  $(this).toggleClass("active");
  OrdersRows.sort((a, b) => {
    let totalA = parseInt($(a).data("total"));
    let totalB = parseInt($(b).data("total"));
    return condition(totalA, totalB);
  }).prependTo(table);

  addDetailsRow();
});

$(document).on("click", "#orders-table [data-date-filter]", function () {
  let table = $("#orders-table > tbody");

  OrdersRows = $("#orders-table > tbody > tr[data-orderID]");

  let condition = (a, b) => (a > b ? 1 : -1);
  if ($(this).hasClass("active")) {
    condition = (a, b) => (a < b ? 1 : -1);
  }
  $(this).toggleClass("active");

  OrdersRows.sort((a, b) => {
    let dateA = new Date($(a).data("date")).getTime();
    let dateB = new Date($(b).data("date")).getTime();
    return condition(dateA, dateB);
  }).prependTo(table);

  addDetailsRow();
});

$(document).on("click", "#orders-table [data-amount-filter]", function () {
  let table = $("#orders-table > tbody");

  OrdersRows = $("#orders-table > tbody > tr[data-orderID]");

  let condition = (a, b) => (a > b ? 1 : -1);
  if ($(this).hasClass("active")) {
    condition = (a, b) => (a < b ? 1 : -1);
  }
  $(this).toggleClass("active");

  OrdersRows.sort((a, b) => {
    let dateA = new Date($(a).data("amount")).getTime();
    let dateB = new Date($(b).data("amount")).getTime();
    return condition(dateA, dateB);
  }).prependTo(table);

  addDetailsRow();
});

function addDetailsRow() {
  OrdersDetailsRows = $("#orders-table > tbody > tr[data-orderDetailsRow]");

  OrdersDetailsRows.each(function (index, row) {
    let orderID = $(this).attr("data-orderDetailsRow");
    $(this).insertAfter(`tr[data-orderID='${orderID}']`);
  });
}

// delete reservation
$(document).on("click", "[data-deleteOrder]", function () {
  let orderID = $(this).parents("[data-orderID]").attr("data-orderID");

  $("body").prepend(`
  <div class="popup-overlay allow-close">
  <div id = "changeStatus" class="popup-box">
    <header>
      <h2>حذف طلب - طلب رقم #${orderID}</h2>
    </header>
    <main>
      <p>هل أنت متأكد من أنك تود حذف الطلب؟</p>
    </main>
    <footer>
      <button onClick = "confirmDelete(${orderID})" class="popup-btn">حذف</button>
      <button class="popup-btn-alt js-close-popup">إلغاء</button>
    </footer>
  </div>
</div>
  `);
});

function confirmDelete(orderID) {
  $.ajax({
    method: "POST",
    url: "./../../ajax_requests/restaurant-requests/orders-repuests/deleteOrder.php",
    data: { orderID },
    success: (res) => {
      console.log(res);
      res = JSON.parse(res);
      console.log(res);
      let msgClasses = res.result ? "success" : "error";
      $(".popup-box main").html(
        `<p class="my-alert ${msgClasses}">${res.message}</p>`
      );
      $(".popup-box footer").html(
        `<button class="popup-btn-alt js-close-popup">إغلاق</button>`
      );

      if (res.result) {
        Orders = Orders.filter((item) => item.id != orderID);

        if ($(`[data-orderID]`).length == 1) {
          $("#orders-table tbody")
            .html(`<tr> <td colspan="7" class="no-items o-card">
          لا يوجد طلبات
        </td></tr>`);
        } else {
          $(`[data-orderID=${orderID}]`).fadeOut(function () {
            $(this).remove();
          });
          $(`[data-orderDetailsRow='${orderID}']`).fadeOut(function () {
            $(this).remove();
          });
        }
      }
    },
  });
}

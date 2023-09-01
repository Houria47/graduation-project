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
        label: "عدد الحجوزات لعام 2023",
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
let Reservations = [];

let Status = [];

function getStatusName(id) {
  let index = Status.findIndex((status) => status.id == id);
  return Status[index].status;
}

$(document).ready(function () {
  let restID = $("[data-restID]").attr("data-restID");

  $.ajax({
    method: "GET",
    url: "./../../ajax_requests/restaurant-requests/reserv-requests/getReservations.php",
    data: { restID },
    success: (res) => {
      res = JSON.parse(res);
      Reservations = res.reservations;
      Status = res.status;
      setupLineChart(Reservations);
    },
  });
});

function setupLineChart(data) {
  let currentYear = new Date().getFullYear();
  let currentMonth = new Date().getMonth();
  ordersMonthData = data
    .filter((order) => order.status.id == 4)
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
  let reservID = $(this).parents("[data-reservID]").attr("data-reservID");
  let isActive = $(`[data-orderDetailsRow='${reservID}']`).hasClass("active");

  $(`[data-details-btn] i`).removeClass("fa-angle-up");
  $(`[data-details-btn] i`).addClass("fa-angle-down");
  $(`[data-orderDetailsRow]`).removeClass("active");
  if (!isActive) {
    $(`[data-orderDetailsRow='${reservID}']`).addClass("active");
    $(this).find("i").removeClass("fa-angle-down");
    $(this).find("i").addClass("fa-angle-up");
  }
});

$(document).on("click", "[data-order-status]", function () {
  let reservID = $(this).parents("[data-reservID]").attr("data-reservID");
  $("body").prepend(getStatusModal(reservID));
});

function getStatusModal(reservID) {
  let statusOptions = "";
  Status.map((status) => {
    statusOptions += `<option value='${status.id}'>${status.status}</option>`;
  });

  let modal = $(`<div class="popup-overlay allow-close">
  <form id = "changeStatus" class="popup-box">
    <header>
      <h2>تعديل حالة الحجز - حجز رقم #${reservID}</h2>
    </header>
    <main>
      <p>الحالة</p>
      <select name="status">
      ${statusOptions}
      </select>
      <input name="reservID" value="${reservID}" hidden>
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
  let reservID = this.reservID.value;
  let status = this.status.value;
  $.ajax({
    method: "POST",
    url: "./../../ajax_requests/restaurant-requests/reserv-requests/changeReservStatus.php",
    data: { reservID, status },
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
        let statusClass =
          status == 1
            ? "s1"
            : status == 3
            ? "s2"
            : status == 5 || status == 2
            ? "s3"
            : "s4";
        $(`[data-reservID=${reservID}] .order-status`).attr(
          "class",
          `order-status ${statusClass}`
        );
        $(`[data-reservID=${reservID}] .order-status`).html(
          getStatusName(status)
        );
      }
    },
  });
});

$(document).on("click", "#orders-table [data-date-filter]", function () {
  let table = $("#orders-table > tbody");

  OrdersRows = $("#orders-table > tbody > tr[data-reservID]");

  let condition = (a, b) => (a > b ? 1 : -1);
  if ($(this).hasClass("active")) {
    condition = (a, b) => (a < b ? 1 : -1);
  }
  $(this).toggleClass("active");

  OrdersRows.sort((a, b) => {
    let dateA = new Date($(a).data("created_at")).getTime();
    let dateB = new Date($(b).data("created_at")).getTime();
    return condition(dateA, dateB);
  }).prependTo(table);

  addDetailsRow();
});

$(document).on("click", "#orders-table [data-resrveDate-filter]", function () {
  let table = $("#orders-table > tbody");

  OrdersRows = $("#orders-table > tbody > tr[data-reservID]");

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

function addDetailsRow() {
  OrdersDetailsRows = $("#orders-table > tbody > tr[data-orderDetailsRow]");

  OrdersDetailsRows.each(function (index, row) {
    let reservID = $(this).attr("data-orderDetailsRow");
    $(this).insertAfter(`tr[data-reservID='${reservID}']`);
  });
}

// delete reservation
$(document).on("click", "[data-deletereserv]", function () {
  let reservID = $(this).parents("[data-reservID]").attr("data-reservID");

  $("body").prepend(`
  <div class="popup-overlay allow-close">
  <div id = "changeStatus" class="popup-box">
    <header>
      <h2>حذف طلب حجز - حجز رقم #${reservID}</h2>
    </header>
    <main>
      <p>هل أنت متأكد من أنك تود حذف الطلب؟</p>
    </main>
    <footer>
      <button onClick = "confirmDelete(${reservID})" class="popup-btn">حذف</button>
      <button class="popup-btn-alt js-close-popup">إلغاء</button>
    </footer>
  </div>
</div>
  `);
});

function confirmDelete(reservID) {
  $.ajax({
    method: "POST",
    url: "./../../ajax_requests/restaurant-requests/reserv-requests/deleteReservation.php",
    data: { reservID },
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
        Reservations = Reservations.filter((item) => item.id != reservID);

        if ($(`[data-reservID]`).length == 1) {
          $("#orders-table tbody")
            .html(`<tr> <td colspan="7" class="no-items o-card">
          لا يوجد طلبات
        </td></tr>`);
        } else {
          $(`[data-reservID=${reservID}]`).fadeOut(function () {
            $(this).remove();
          });
          $(`[data-orderDetailsRow='${reservID}']`).fadeOut(function () {
            $(this).remove();
          });
        }
      }
    },
  });
}

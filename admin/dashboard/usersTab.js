let AllRows = [];

$(document).ready(function () {
  // get data from Database
  if ($("#rests-accounts-table").length > 0) {
    // users accounts table has been loaded
    // get data
    Users.fetch();

    AllRows = $("#rests-accounts-table tbody tr").clone(true);
    console.log(AllRows);
  }
});

// show user information
$(document).on("click", "#rests-accounts-table .row-info", function (e) {
  // get parent row to get user id
  let parentRow = $(this).closest("tr");
  let restID = parentRow.attr("data-restID");
  if ($(this).hasClass("closed")) {
    // open info row..
    // create info row to insert after parent row
    let infoRow = $(
      `<div style="display: table-row;" data-restInfoID="${restID}"></div>`
    );
    infoRow.html('<td colspan="10"></td>');
    infoRow.children("td").animate({ padding: 0 }).html(createInfoRow(restID));
    removeDetailsElements();
    infoRow.insertAfter(parentRow);
    infoRow
      .find("td")
      .wrapInner('<div style="display: none;" />')
      .parent()
      .find("td > div")
      .slideDown(700, function () {
        var $set = $(this);
        $set.replaceWith($set.contents());
      });
    // toggle icon class
    $(this).removeClass("closed");
    $(this).switchClass("fa-angle-down", "fa-angle-up");
    $("this");
  } else {
    // close info row
    $(this).addClass("closed");
    $(this).switchClass("fa-angle-up", "fa-angle-down");
    // get info row
    let existedInfoRow = $(
      `#rests-accounts-table [data-restInfoID="${restID}"]`
    );
    existedInfoRow
      .find("td")
      .wrapInner('<div style="display: block;" />')
      .parent()
      .find("td > div")
      .slideUp(700, function () {
        $(this).parent().parent().remove();
      });
  }
});
function createInfoRow(restID) {
  let accountIndex = Users.accounts.findIndex((acc) => acc.id == restID);
  let account = Users.accounts[accountIndex];

  let contentDiv = $(`<div class="info-row-container"></div>`);

  let creationDate = new Date(account["created_at"]);
  formatedDate = creationDate.toLocaleDateString("ar-EG", {
    weekday: "long",
    year: "numeric",
    month: "short",
    day: "numeric",
  });

  contentDiv.append(`
  <div class="info-col">
      <div>
        <h3>الاسم</h3>
        <span><i class="fas fa-user"></i>${account["name"]}</span>
      </div>
      <div>
        <h3>تاريخ الإضافة</h3>
        <span><i class="fas fa-calendar"></i>${formatedDate}</span>
      </div>
      <div>
        <h3>العنوان</h3>
        <span
          ><i class="fas fa-location-pin"></i>${
            account["address"].length == 0 ? "لا يوجد" : account["address"]
          }</span>
      </div>
    </div>
    <img
      class="info-col"
      src="${account["image"]}"
    />
    <div class="info-col last">
      <div>
        <h3>التقييمات التي أضافها</h3>
        <span><i class="fas fa-bowl-food"></i>قيّم ${
          account["recpRevNum"] +
          (account["recpRevNum"] >= 3 ? " وجبات " : " وجبة")
        }</span>
        <span><i class="fas fa-store"></i>قيّم ${
          account["restRevNum"] +
          (account["restRevNum"] >= 3 ? " مطاعم " : " مطعم")
        }</span>
      </div>
      <div>
        <h3>الطلبات والحجوزات</h3>
        <span><i class="fas fa-bookmark"></i>${
          account["reservsNum"] +
          (account["reservsNum"] >= 3 ? " حجوزات " : " حجز")
        }</span>
        <span><i class="fas fa-truck-fast"></i>${
          account["ordersNum"] +
          (account["ordersNum"] >= 3 ? " طلبات " : " طلب")
        }</span>
      </div>
      <div>
        <h3>التفاعل</h3>
        <span><i class="fas fa-comment"></i>${
          account["commentsNum"] +
          (account["commentsNum"] >= 3 ? " تعليقات " : " تعليق")
        }</span>
        <span><i class="fas fa-like"></i>${
          account["reactsNum"] +
          (account["reactsNum"] >= 3 ? " إعجابات " : " إعجاب")
        }</span>
      </div>
    </div>`);
  return contentDiv;
}

function removeDetailsElements() {
  // remove any files or info preview divs
  $("#rests-accounts-table tbody > div").remove();
  // return any show row info icon with open state to closed state
  // get show row info icons
  $("#rests-accounts-table .row-info").map(function () {
    if (!$(this).hasClass("closed")) {
      $(this).addClass("closed");
      $(this).switchClass("fa-angle-up", "fa-angle-down");
    }
  });
}

// Delete restaurnts account
$(document).on(
  "click",
  "#rests-accounts-table td .actions .del-btn",
  function (e) {
    let restID = $(this).parent().attr("data-restID");
    let restName = $(this).parent().attr("data-restName");
    let modal = `<div class="popup-overlay allow-close">
    <div class="popup-box--black">
      <header>
        <h2>تأكيد الحذف - حساب ${restName}</h2>
      </header>
      <main>
        <p>هل أنت متأكد من حذف حساب : ${restName}؟</p>
      </main>
      <footer>
        <button onClick="confirmRestuarantDelete(${restID},'${restName}')" class="popup-btn del-rest-btn">تأكيد</button>
        <button class="popup-btn-alt js-close-popup">إلغاء</button>
      </footer>
    </div>
  </div>`;
    let wrapper = document.createElement("div");
    wrapper.innerHTML = modal;
    modal = wrapper.firstChild;
    document.body.prepend(modal);
  }
);
function confirmRestuarantDelete(restID, restName) {
  let response = Users.delete(restID);
  response.done((result) => {
    if (result.status) {
      // Show mesage
      $(".popup-box--black main").html(
        `<p>تم حذف حساب : ${restName}</p>
      <div class="my-alert success-black"> ${result.message}</div>`
      );
      // remove any files or info preview divs
      removeDetailsElements();
      // remove account row from table
      let targetRow = $(`#rests-accounts-table tr[data-restID=${restID}]`);
      targetRow.hide("slow", function () {
        $(this).remove();
      });
      // change buttons
      $(".popup-box--black footer").html(
        `<button class="popup-btn js-close-popup">إغلاق</button>`
      );
    } else {
      $(".popup-box--black main").html(
        `<p>لم يتم حذف حساب : ${restName}</p>
      <div class="my-alert error-black"> ${result.message}</div>`
      );
    }
  });
}

// filter table
let searchText = -1;
$(document).on("input", "#searchUser input", function (e) {
  if (e.target.value.trim().length != 0) searchText = e.target.value.trim();
});

$(document).on("submit", "#searchUser", function (e) {
  e.preventDefault();
  searchText = this.searchText.value.trim();
  filterTable();
});

$(document).on("click", "#filter-rests-table .filter", function (e) {
  // toggle active class between old and new selected filter element
  $("#filter-rests-table .filter.active").removeClass("active");
  $(this).addClass("active");
  filterTable();
});
function filterTable() {
  clearRestaurantsTable();
  // get filter value
  let selectedStatus = parseInt(
    $("#filter-rests-table .filter.active").attr("data-val")
  );
  let cnt = -1;
  if (selectedStatus == -1 && searchText == -1) {
    AllRows.clone(true).appendTo("#rests-accounts-table tbody");
  } else {
    cnt = 0;
    AllRows.map(function (el) {
      let f2 = true,
        f1 = true;

      let status = $(this).data("reststatus");
      if (selectedStatus != -1 && status != selectedStatus) {
        f1 = false;
      }
      let name = $(this).find("[data-restName]").data("restname");
      if (
        searchText != -1 &&
        !name.toLowerCase().includes(searchText.toLowerCase())
      ) {
        f2 = false;
      }

      if (f1 && f2) {
        $(this).clone(true).appendTo("#rests-accounts-table tbody");
        cnt++;
      }
    });
  }

  if (!cnt) {
    $("#rests-accounts-table tfoot").removeClass("d-none");
  }
}
// function to clear the table before filtering the content to add filtered content
function clearRestaurantsTable() {
  // hide message in tfoot
  $("#rests-accounts-table tfoot").addClass("d-none");
  removeDetailsElements();
  // clear table body
  $("#rests-accounts-table tbody").html("");
}

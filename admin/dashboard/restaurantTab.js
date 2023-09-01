$("#filter-rests-table select").selectBoxIt({
  // Uses the jQueryUI theme for the drop down
  theme: "jqueryui",
  defaultText: "المحافظة",
});
// Start restaurants accounts table script
/*
 ** Get restaurants accounts info from DB on tabe load and display the rows
 */
$(document).ready(function () {
  // get data from Database
  if ($("#rests-accounts-table").length > 0) {
    // restaurants accounts table has been loaded
    // get data
    Restaurants.fetch();
  }
});
// function to create table row for restaurant account
// to append to restaurants accounts table with id #rests-accounts-table
function createRestAccountRow(account) {
  let status = account.account_status;
  let trow = $(
    `<tr data-restID="${account.id}" data-restStatus="${status}"></tr>`
  );
  // row content
  let rateContent, actionsContent, filesContent, statusWord;
  if (status == 0 || status == 1) {
    rateContent = `<span>لا يوجد</span>`;
  } else {
    rateContent = `<div class="ratings">
      <div class="empty-stars"></div>
      <div class="full-stars" style="width: ${account.ratePercenatge}%"></div>
    </div>`;
  }
  // actions content
  actionsContent = `<button class="table-btn del-btn">حذف</button>`;
  actionsContent =
    status == 1
      ? `<button class="table-btn del-btn">حذف</button><button class="table-btn verify-btn">توثيق</button>`
      : actionsContent;
  // status word
  statusWord = status == 0 ? "غير مؤكد" : status == 1 ? "مؤكد" : "موثّق";
  // files content
  // TODO: change files content after decideing what to do with auth files after account authentication
  let filesNum = account.authentication_files.length;
  if (status != 2 && filesNum > 0) {
    filesContent = `<span>${filesNum} ${filesNum >= 3 ? "ملفات" : "ملف"}</span>
      <button
        data-showFilesBtn="${account.id}"
        class="table-btn show-files-btn closed">
        عرض
      </button>`;
  } else {
    filesContent = `<span>لا يوجد</span>`;
  }
  // get state of first address
  let state =
    account.addresses.length > 0 ? account.addresses[0].state : "لا يوجد";
  // row Content
  let rowContent = `
  <td><i class="row-info closed fa fa-solid fa-angle-down"></i>
    <a href="./restaurants/restaurant_profile/restProfile.php?restID=${account.id}">
      ${account.name}
    </a>
  </td>
  <td>${state}</td>
  <td>${account.email}</td>
  <td>${account.phone}</td>
  <td class="rest-rate">${rateContent}</td>
  <td><span class="status s-${status}">${statusWord}</span></td>
  <td>
    <div 
      class="actions"
      data-restName="${account.name}"
      data-restID="${account.id}">
      ${actionsContent}
    </div>
  </td>
  <td>
    <div class="actions">
      ${filesContent}
    </div>
  </td>
  `;
  trow.html(rowContent);
  return trow;
}
// filter table
$(document).on("change", "#filter-rests-table select", filterTable);
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
  // get selected state
  let selectedState = parseInt($("#filter-rests-table select").val());
  let filteredRows = Restaurants.accounts;
  if (selectedStatus != -1 || selectedState != -1) {
    filteredRows = Restaurants.accounts.filter((el) => {
      let f1 = true; // in case there is no addresses for the account
      if (el.addresses.length > 0) {
        f1 =
          selectedState != -1
            ? el.addresses[0].state_id == selectedState
            : true;
      }
      let f2 =
        selectedStatus != -1 ? el.account_status === selectedStatus : true;

      return f1 && f2;
    });
  }
  filteredRows.map((account) => {
    $("#rests-accounts-table tbody").append(createRestAccountRow(account));
  });
  if (filteredRows.length == 0) {
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
// show restaurant information
$(document).on("click", "#rests-accounts-table .row-info", function (e) {
  // get parent row to get restaurant id
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
  let accountIndex = Restaurants.accounts.findIndex((acc) => acc.id == restID);
  let account = Restaurants.accounts[accountIndex];
  let contentDiv = $(`<div class="info-row-container"></div>`);
  let creationDate = new Date(account["created_at"]);
  formatedDate = creationDate.toLocaleDateString("ar-EG", {
    weekday: "long",
    year: "numeric",
    month: "short",
    day: "numeric",
  });
  // open & close time formating
  let openTime = getHourMinutesFormat(account["open_time"]);
  let closeTime = getHourMinutesFormat(account["close_time"]);
  let contentCol1 = $(`<div class="info-col"></div>`);
  let rateContent =
    account["account_status"] != 2
      ? "الحساب غير فعال، لايوجد تقييم له."
      : account["ratesNum"] == 0
      ? `لم يقم أحد بتقييم المطعم إلى الآن.`
      : ` تم تقييم
            المطعم من قبل ${account["ratesNum"]} ${
          account["ratesNum"] >= 3 ? "أشخاص" : "شخص"
        }`;
  contentCol1.html(`
  <div>
    <h3>اسم المطعم</h3>
    <span><i class="fa fa-solid fa-store-alt"></i>${account["name"]}</span>
  </div>
  <div>
    <h3>تاريخ الإضافة</h3>
    <span><i class="fa fa-solid fa-calendar"></i>${formatedDate}</span>
  </div>
  <div class="rate">
    <h3> التقييم
      <span >${account["rate"]}<i class="fa fa-solid fa-star"></i></span>
    </h3>
    <span><i class="fa fa-solid fa-star"></i>${rateContent}</span>
  </div>`);
  let contentCol2 = $(`<div class="info-col"></div>`);
  // services
  let fastFoodServ = account["fast_food"] ? "fa-cancel" : "fa-check";
  let delevServ = account["delivery_service"] ? "fa-check" : "fa-cancel";
  let reservServ = account["reserv_service"] ? "fa-check" : "fa-cancel";
  contentCol2.html(`
  <div>
    <h3> الإضافات</h3>
    <span><i class="fa fa-solid fa-bowl-food"></i>
      ${
        account["recipesNum"] + (account["ratesNum"] >= 3 ? " وجبات " : " وجبة")
      }</span>
    <span><i class="fa fa-solid fa-list"></i> 
    ${
      account["listsNum"] + (account["listsNum"] >= 3 ? " قوائم " : " قائمة")
    }</span>
    <span><i class="fa fa-solid fa-ad"></i>
    ${
      account["postsNum"] + (account["postsNum"] >= 3 ? " إعلانات " : " إعلان")
    }</span>
  </div>
  <div>
    <h3>الخدمات</h3>
    <span><i class="fa fa-solid ${fastFoodServ}"></i>مطعم </span>
    <span><i class="fa fa-solid ${delevServ}"></i>التوصيل </span>
    <span><i class="fa fa-solid ${reservServ}"></i>الحجز </span>
  </div>
  <div>
    <h3> أوقات الدوام</h3>
    <span><i class="fa fa-solid fa-clock"></i>من ${openTime} إلى ${closeTime}</span>
  </div>`);
  let contentCol3 = $(`<div class="info-col"></div>`);
  let addresses = `<span><i class="fa fa-solid fa-location-pin"></i> لم تتم إضافة عناوين</span>`;
  if (account["addresses"].length > 0) {
    addresses = "";
    account["addresses"].forEach((address) => {
      addresses += `<span><i class="fa fa-solid fa-location-pin"></i>
                    ${address["state"]}،  ${address["region"]} , ${address["street"]}
                    </span>`;
    });
  }
  contentCol3.html(`
  <div class="addresses">
    <h3>العناوين</h3>
    ${addresses}
  </div>`);
  contentDiv.append(contentCol1);
  contentDiv.append(contentCol2);
  contentDiv.append(contentCol3);
  return contentDiv;
}
// Show authentiation files
$(document).on("click", "#rests-accounts-table .show-files-btn", function (e) {
  let restID = $(this).attr("data-showFilesBtn");
  if ($(this).hasClass("closed")) {
    removeDetailsElements();
    $(this).removeClass("closed");
    let filesPreviewRow = $(
      `<div style="display: table-row;" data-rowPreviewID = ${restID} ></div>`
    );
    filesPreviewRow.html(`<td colspan='10'></td>`);
    filesPreviewRow.children("td").html(createAuthFilesPreview(restID));
    $(filesPreviewRow)
      .hide()
      .insertAfter(`#rests-accounts-table tr[data-restID=${restID}]`)
      .fadeIn(300);
  } else {
    $(`tbody [data-rowPreviewID=${restID}]`).fadeOut(300, function () {
      $(this).remove();
    });
    $(this).addClass("closed");
  }
});
function createAuthFilesPreview(restID) {
  let accountIndex = Restaurants.accounts.findIndex((acc) => acc.id == restID);
  let accountFiles = Restaurants.accounts[accountIndex].authentication_files;
  // create the element
  let preview = $("<div class='auth-files-row'></div>");
  let ul = $("<ul></ul>");
  accountFiles.forEach((file, idx) => {
    let path = file.altPath;
    let relPath = file.relPath;
    let name = file.name;
    let li = $(`<li class="${idx++ == 0 ? "active" : ""}"></li>`);
    li.html(`<a target='restID${restID}' href='${relPath}' class='file'>
              <img src='${path}' />
              <span>${name}</span>
            </a>
            <a href='${relPath}' target='_blank'>
              <i class='external-link fa fa-solid fa-external-link'></i>
            </a>`);
    ul.append(li);
  });
  let iframe = $(`<iframe onload="addStylesToIframe()" name="restID${restID}"
                  src="${accountFiles[0].relPath}"
                  class="preview"><iframe>
                  `);
  preview.append(ul);
  preview.append(iframe);
  return preview;
}
// preview choosen file
$(document).on("click", "#rests-accounts-table a.file", function (e) {
  $("#rests-accounts-table a.file").parent().removeClass("active");
  $(this).parent().addClass("active");
});
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
        <h2>تأكيد الحذف - مطعم ${restName}</h2>
      </header>
      <main>
        <p>هل أنت متأكد من حذف حساب المطعم: ${restName}؟</p>
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
  let response = Restaurants.delete(restID);
  response.done((result) => {
    if (result.status) {
      // Show mesage
      $(".popup-box--black main").html(
        `<p>تم حذف حساب المطعم: ${restName}</p>
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
        `<p>لم يتم حذف حساب المطعم: ${restName}</p>
      <div class="my-alert error-black"> ${result.message}</div>`
      );
    }
    updateUI();
  });
}
// Verify restaurant account
$(document).on(
  "click",
  "#rests-accounts-table td .actions .verify-btn",
  function (e) {
    let restID = $(this).parent().attr("data-restID");
    let restName = $(this).parent().attr("data-restName");
    let modal = `<div class="popup-overlay allow-close">
    <div class="popup-box--black">
      <header>
        <h2>توثيق الحساب - معطم ${restName}</h2>
      </header>
      <main>
        <p> هل أنت متأكد من توثيق المطعم: ${restName}؟ سيتم إعلام صاحب الحساب بالتوثيق عبر البريد الإلكتروني.</p>
      </main>
      <footer>
        <button onClick="confirmRestuarantVerfiy(${restID},'${restName}')" class="popup-btn del-rest-btn">تأكيد</button>
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
function confirmRestuarantVerfiy(restID, restName) {
  $(".popup-box--black main").html(
    `<div class="popup-sppiner"><div class="lds-ring"><div></div><div></div><div></div><div></div></div></div>`
  );
  $(".popup-box--black footer").addClass("disable");
  let response = Restaurants.authenticate(restID);
  response.done((result) => {
    console.log(result);
    $(".popup-box--black footer").removeClass("disable");
    if (result.status) {
      $(".popup-box--black main").html(
        `<p>تم توثيق حساب المطعم: ${restName}</p>
        <div class="my-alert success-black"> ${result.message}</div>`
      );
      // remove verify button
      $(`#rests-accounts-table tr[data-restID=${restID}] .verify-btn`).remove();
      // change row status
      $(`#rests-accounts-table tr[data-restID=${restID}] span.status`)
        .removeClass("s-1")
        .addClass("s-2")
        .html("موثّق");
      // remove view auth files button from row
      $(`#rests-accounts-table tr[data-restID=${restID}] td:last-child`).html(`
        <div class="actions">
          <span>لا يوجد</span>
        </div>`);
      // change Modal buttons
      $(".popup-box--black footer").html(
        '<button class="popup-btn js-close-popup">إغلاق</button>'
      );
    } else {
      $(".popup-box--black main").html(
        `<p>لم يتم توثيق حساب المطعم: ${restName}</p>
        <div class="my-alert error-black"> ${result.message}</div>`
      );
    }
    updateUI();
  });
}
function updateUI() {
  // get updated data from database
  Restaurants.fetch().then(() => {
    // update counters ui
    let counter0 = $(`[data-statistic=0]`); // for totalnum
    let counter1 = $(`[data-statistic=1]`); // for authenticated
    let counter2 = $(`[data-statistic=2]`); // for verified
    let counter3 = $(`[data-statistic=3]`); // for unVerfied
    counter0.html(Restaurants.accountsNum);
    counter1.html(Restaurants.authenticatedAccountsNum);
    counter2.html(Restaurants.verifiedAccountsNum);
    counter3.html(Restaurants.unVerifiedAccountsNum);
    // update wiating restaurants
    $("#waitingRestuarants span")
      .html(Restaurants.verifiedAccountsNum)
      .attr("data-val", Restaurants.verifiedAccountsNum);
    $("#waitingRestuarants h3").html(
      `يوجد ${Restaurants.verifiedAccountsNum} مطعم ينتظر التوثيق`
    );
    // update column chart
    let colChart = $("#restsColChart");
    colChart.html("");
    let statesPercentageArray = Object.entries(Restaurants.statesPercentage);
    for ([key, state] of statesPercentageArray) {
      let bar = $(`<div class='chart-bar'></div>`);
      bar.html(`
    <span class='bar-percentage'>${state.percentage}%</span>
    <div class='chart-bar-inner'>
      <div style='--height: ${state.percentage}%' class='inner-fill'></div>
    </div>
    <div class='chart-bar-title'>${state.name}</div>`);
      colChart.append(bar);
    }
    // get updated percentages
    let percentages = Restaurants.calcAccountsPercentages();
    // update pie chart values
    $("#restsPieChart figure").attr(
      "style",
      `
  --p1: ${percentages[0]}%;
  --p2: ${percentages[1]}%;
  --p3: ${percentages[2]}%`
    );
    // updated pie chart content
    $("#pieChartinnerCaption [data-p1").attr(
      "data-percentage",
      `${percentages[0]}%`
    );
    $("#pieChartinnerCaption [data-p2").attr(
      "data-percentage",
      `${percentages[1]}%`
    );
    $("#pieChartinnerCaption [data-p3").attr(
      "data-percentage",
      `${percentages[2]}%`
    );
  });
}
// style iframe inner page
function addStylesToIframe() {
  jQuery("#rests-accounts-table iframe.preview").contents().find("body")
    .append(`
  <style>
  body{
    display: flex;
    align-items: center;
    justify-content: center;
  }
  body img{
    max-height:100%;
    max-width:100%;
    object-fit:cover;
  }
  </style>
  `);
}
// End restaurants accounts table script

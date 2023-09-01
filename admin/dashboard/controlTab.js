const ordersChart = document.getElementById("accountsChart");
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

let lineChartData = [2, 5, 20, 50];
let lineChartData1 = [2, 5, 30, 40, 15];
let lineChart = new Chart(ordersChart, {
  type: "line",
  data: {
    labels: Labels,
    datasets: [
      {
        label: "عدد حسابات الزبائن لعام 2023",
        data: lineChartData,
        borderWidth: 1,
        borderColor: "#034732",
        backgroundColor: "#034732",
      },
      {
        label: "عدد حسابات المطاعم لعام 2023",
        data: lineChartData1,
        borderWidth: 1,
        borderColor: "#eebb4d",
        backgroundColor: "#eebb4d",
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

/*
 ** Get restaurants & users accounts info from DB
 */
$(document).ready(function () {
  // get data from Database
  if ($(".content-control").length > 0) {
    // get data
    Restaurants.fetch().then((data) => {
      Users.fetch().then((data) => {
        setupLineChart();
      });
    });
  }
});

function setupLineChart() {
  let currentYear = new Date().getFullYear();
  let currentMonth = new Date().getMonth();

  let usersDate = Users.accounts
    .filter((user) => user.account_status == 1)
    .map((user) => new Date(user.created_at));
  let restsDate = Restaurants.accounts
    .filter((rest) => rest.account_status == 2)
    .map((rest) => new Date(rest.created_at));

  let usersDatasetValues = {},
    restsDatasetValues = {};

  usersDate.forEach(function (x) {
    if (x.getFullYear() == currentYear) {
      usersDatasetValues[x.getMonth()] =
        (usersDatasetValues[x.getMonth()] || 0) + 1;
    }
  });

  restsDate.forEach(function (x) {
    if (x.getFullYear() == currentYear) {
      restsDatasetValues[x.getMonth()] =
        (restsDatasetValues[x.getMonth()] || 0) + 1;
    }
  });

  let usersDateset = [],
    restsDataset = [];
  for (let i = 0; i <= currentMonth; i++) {
    usersDateset.push(usersDatasetValues[i] || 0);
    restsDataset.push(restsDatasetValues[i] || 0);
  }

  lineChart.data.datasets[0].data = usersDateset;
  lineChart.data.datasets[1].data = restsDataset;
  lineChart.update();
}
let nameInput = $("#platformNameInput");
let emailInput = $("#emailInput");

// Start edit scripts
// name
$(document).on("click", "[data-editName]", function (e) {
  let actionsParent = $(this).parent();
  let value = nameInput.val();
  actionsParent.html(`<i onClick="cancelName('${value}')" class="x fas fa-times"></i>
  <i onClick="updateName()" class="y fas fa-check"></i>`);
  nameInput.addClass("active");
  nameInput.focus();
});
function cancelName(oldName) {
  nameInput.removeClass("active");
  nameInput.val(oldName);
  $(".actions.name").html('<i data-editName class="fas fa-edit"></i>');
}
function updateName() {
  let value = nameInput.val().trim();
  if (value.length === 0) {
    $("#nameMsg").html("يرجى تحديد الاسم");
    nameInput.focus();
    setTimeout(() => {
      $("#nameMsg").html("");
    }, 5000);
  } else {
    $(".actions.name").html('<i data-editName class="fas fa-edit"></i>');
    nameInput.removeClass("active");
  }
}
// email
$(document).on("click", "[data-editEmail]", function (e) {
  let actionsParent = $(this).parent();
  let value = emailInput.val();
  actionsParent.html(`<i onClick="cancelEmail('${value}')" class="x fas fa-times"></i>
  <i onClick="updateEmail()" class="y fas fa-check"></i>`);
  emailInput.addClass("active");
  emailInput.focus();
});
function cancelEmail(oldEmail) {
  emailInput.removeClass("active");
  emailInput.val(oldEmail);
  $(".actions.email").html('<i data-editEmail class="fas fa-edit"></i>');
}
function updateEmail() {
  let value = emailInput.val().trim();
  let validator = new ValidateIt();
  validator.email(value, $("#emailMsg"));
  if (validator.isValid) {
    changeEmailRequest(value);
    $(".actions.email").html('<i data-editEmail class="fas fa-edit"></i>');
    emailInput.removeClass("active");
  } else {
    emailInput.focus();
    setTimeout(() => {
      $("#emailMsg").html("");
    }, 5000);
  }
}
function changeEmailRequest(email) {
  $.ajax({
    method: "POST",
    url: "./../ajax_requests/admin-requests/editPlatformInfo.php",
    data: {
      infoType: "EDIT_EMAIL",
      email,
    },
    success: (res) => {
      res = JSON.parse(res);
      if (!res.result) {
        showErrorModal(res.message);
      }
    },
  });
}
// password
$(document).on("click", "[data-editPass]", function (e) {
  $("body").prepend(`<div class="popup-overlay allow-close">
  <form id="confirmCurrentPass" class="popup-box--black">
    <header>
      <h2>تغيير كلمة المرور</h2>
    </header>
    <main>
      <p>أدخل كلمة المرور الحالية للمتابعة:</p>
      <input type="password" name="pass" placeholder="كلمة المرور"/>
      <small></small>
    </main>
    <footer>
      <button type="submit" class="popup-btn">إرسال</button>
      <button class="popup-btn-alt js-close-popup">إلغاء</button>
    </footer>
  </form>
</div>`);
});

$(document).on("submit", "#confirmCurrentPass", function (e) {
  e.preventDefault();
  $(this).find("small").html("");
  $.ajax({
    method: "POST",
    url: "./../ajax_requests/admin-requests/editPlatformInfo.php",
    data: {
      infoType: "CONFIRM_PASSWORD",
      pass: this.pass.value,
    },
    success: (res) => {
      res = JSON.parse(res);
      if (res.result) {
        $(".popup-overlay").html(getChangePassForm());
      } else {
        $(this).find("small").html(res.message);
      }
    },
  });
});

function getChangePassForm() {
  return `<form id="changePass" class="popup-box--black">
  <header>
    <h2>تغيير كلمة المرور</h2>
  </header>
  <main>
    <p>أدخل كلمة المرور الجديدة:</p>
    <input type="password" name="pass" placeholder="كلمة المرور"/>
    <input type="password" name="rePass" placeholder="تأكيد كلمة المرور"/>
    <small></small>
  </main>
  <footer>
    <button type="submit" class="popup-btn">إرسال</button>
    <button class="popup-btn-alt js-close-popup">إلغاء</button>
  </footer>
</form>`;
}

$(document).on("submit", "#changePass", function (e) {
  e.preventDefault();
  let validator = new ValidateIt();
  let pass = this.pass.value;
  let rePass = this.rePass.value;
  let small = $(this).find("small");
  validator.password(pass, rePass, small);

  if (validator.isValid) {
    $.ajax({
      method: "POST",
      url: "./../ajax_requests/admin-requests/editPlatformInfo.php",
      data: {
        infoType: "CHANGE_PASSWORD",
        pass: this.pass.value,
      },
      success: (res) => {
        res = JSON.parse(res);
        $(".popup-overlay").html(`<div class="popup-box--black">
          <header>
            <h2>${res.message}</h2>
          </header>
          <main>
          <div class="my-alert ${res.result ? "success" : "error"}">${
          res.message
        }</div>
          </main>
          <footer>
            <button class="popup-btn-alt js-close-popup">إلغاء</button>
          </footer>
        </div>`);
      },
    });
  }
});

// rests options scripts

// Start Advert types script
// *************************

// edit
$(document).on("click", "[data-editType]", function () {
  let parent = $(this).parents("[data-typeId]");

  parent.find("input").addClass("active").focus();
  parent.find(".actions").html(`<i class="x fas fa-times"></i>
  <i class="y fas fa-check"></i>`);
});
// cancel editing
$(document).on("click", "[data-typeId] .x", function () {
  let parent = $(this).parents("[data-typeId]");

  parent.find("input").val(parent.data("val")).removeClass("active");
  parent.find(".actions").html(`<i data-editType class="fas fa-edit"></i>`);
});
// confirm editing
$(document).on("click", "[data-typeId] .y", function () {
  let parent = $(this).parents("[data-typeId]");
  let typeID = parent.data("typeid");
  let value = parent.find("input").val().trim();
  if (value.length === 0) {
    $("#typesMsg").html("الحقل فارغ!!");
    parent.find("input").focus();
    return;
  }

  $.ajax({
    method: "POST",
    url: "./../ajax_requests/admin-requests/editPlatformInfo.php",
    data: {
      infoType: "EDIT_AD_TYPE",
      typeID,
      value,
    },
    success: (res) => {
      console.log(res);
      res = JSON.parse(res);
      if (res.result) {
        parent.data("val", value);
      } else {
        showErrorModal(res.message);
      }
      parent.find("input").remove("active").html(parent.data("val"));
      parent.find(".actions").html(`<i data-editType class="fas fa-edit"></i>`);
    },
  });
});
// add new
$(document).on("click", "[data-addNewType]", function () {
  if ($(this).hasClass("active")) {
    removeAdTypeFrom();
  } else {
    $(`<form class="addForm"  id="newAdTypeForm">
    <label> أضافة إعلان جديد</label>
    <div class="input">
      <input name="adType" placeholder="أكتب نوع الإعلان" />
      <button type="submit">حفظ</button>
    </div>
  </form>`)
      .hide()
      .insertBefore($(this))
      .fadeIn();
    $(this).html("إلغاء");
  }
  $(this).toggleClass("active");
});
// helper function
function removeAdTypeFrom() {
  $("#newAdTypeForm").fadeOut(100, function () {
    $(this).remove();
    $("[data-addNewType]").html("إضافة نوع جديد");
  });
}
// submit new
$(document).on("submit", "#newAdTypeForm", function (e) {
  e.preventDefault();
  let value = this.adType.value.trim();
  if (value.length === 0) {
    $(this).addClass("invalid");
    $(this.adType).focus();
  } else {
    $.ajax({
      method: "POST",
      url: "./../ajax_requests/admin-requests/editPlatformInfo.php",
      data: {
        infoType: "ADD_AD_TYPE",
        value,
      },
      success: (res) => {
        res = JSON.parse(res);
        if (res.result) {
          $(`<li data-typeId='${res.insertedID}' data-val='${value}'>
          <span class='used-num'>0</span>
          <span class="actions">
            <i data-editType class="fas fa-edit"></i>
          </span>
          <input value="${value}" />
          <i data-delType class='fas fa-trash'></i>
        </li>`)
            .hide()
            .appendTo($("#adTypesList"))
            .fadeIn();
          removeAdTypeFrom();
        } else {
          showErrorModal(res.message);
        }
      },
    });
  }
});
// delete
$(document).on("click", "[data-delType]", function () {
  let typeID = $(this).parents("[data-typeId]").data("typeid");
  $.ajax({
    method: "POST",
    url: "./../ajax_requests/admin-requests/editPlatformInfo.php",
    data: {
      infoType: "DELETE_AD_TYPE",
      typeID,
    },
    success: (res) => {
      res = JSON.parse(res);
      if (res.result) {
        $(this)
          .parents("[data-typeId]")
          .fadeOut(300, function () {
            $(this).remove();
          });
      } else {
        showErrorModal(res.message);
      }
    },
  });
});

// End Advert types script

// Start Advert Reacts scripts
// ***************************
// edit

$(document).on("click", "[data-editReact]", function () {
  let parent = $(this).parents("[data-reactID]");

  parent.find("input").addClass("active").focus();
  parent.find(".actions").html(`<i class="x fas fa-times"></i>
  <i class="y fas fa-check"></i>`);
});
// cancel editing

$(document).on("click", "[data-reactID] .x", function () {
  let parent = $(this).parents("[data-reactID]");

  parent
    .find("input[name='reactName']")
    .val(parent.data("val"))
    .removeClass("active");
  parent.find("img").attr("src", parent.data("img"));

  parent.find(".actions").html(`<i data-editReact class="fas fa-edit"></i>`);
});

// on react edit icon change
$(document).on("change", "[data-reactID] input[type='file']", function (e) {
  let parent = $(this).parents("[data-reactID]");
  let files = e.target.files;
  if (files.length != 0) {
    parent.find("img").attr("src", URL.createObjectURL(files[0]));
  }
});
// confirm editing
$(document).on("click", "[data-reactID] .y", function () {
  let parent = $(this).parents("[data-reactID]");
  let value = parent.find("input").val().trim();
  if (value.length === 0) {
    $("#reactMsg").html("الحقل  لا يجب أن يُترك فارغ!!");
    parent.find("input").focus();
    return;
  }
  console.log($(this).parents("form").get(0));
  let formData = new FormData($(this).parents("form").get(0));
  formData.append("infoType", "EDIT_AD_REACT");
  $.ajax({
    method: "POST",
    url: "./../ajax_requests/admin-requests/editPlatformInfo.php",
    data: formData,
    contentType: false,
    cache: false,
    processData: false,
    success: (res) => {
      console.log(res);
      res = JSON.parse(res);
      if (res.result) {
        parent.data("val", value);
        parent.data("img", res.insertedImage);
      } else {
        showErrorModal(res.message);
      }
      parent.find("input").removeClass("active").html(parent.data("val"));
      parent.find("img").attr("src", parent.data("img"));
      parent.find(".actions").html(`<i data-editType class="fas fa-edit"></i>`);
    },
  });
});
// on react add icon change
$(document).on("change", "#newReactIcon", function (e) {
  let files = e.target.files;
  if (files.length != 0) {
    $("#newReactIconImg").attr("src", URL.createObjectURL(files[0]));
  }
});
// add new
$(document).on("click", "#addNewReact", function () {
  if ($(this).hasClass("active")) {
    removeAdRreactFrom();
  } else {
    $(`<form class="addForm" id="newReactForm" enctype="multipart/form-data">
    <label> أضافة تفاعل جديد</label>
    <div class="input">
      <input name="reactName" placeholder="اكتب التفاعل الجديد"/>
      <img id="newReactIconImg" src="./../layout/images/light-green-bg.jpg" alt="">
      <label for="newReactIcon" class="file-btn fas fa-upload"></label>
      <input type="file" id="newReactIcon" name="reactIcon" accept="image/png, image/jpeg" hidden>
      <button type="submit">حفظ</button>
    </div>
  </form>`)
      .hide()
      .insertBefore($(this))
      .fadeIn();
    $(this).html("إلغاء");
  }
  $(this).toggleClass("active");
});
// helper function 1
function removeAdRreactFrom() {
  $("#newReactForm").fadeOut(100, function () {
    $(this).remove();
    $("#addNewReact").html("إضافة نوع جديد");
  });
}
// submit new
$(document).on("submit", "#newReactForm", function (e) {
  $("#reactMsg").html("");
  $(this).removeClass("invalid");

  e.preventDefault();
  let value = this.reactName.value.trim();
  if (value.length === 0) {
    $(this).addClass("invalid");
    $("#reactMsg").html("الحقل  لا يجب أن يُترك فارغ!!");

    $(this.reactName).focus();
    return;
  }
  if (this.reactIcon.files.length === 0) {
    $(this).addClass("invalid");
    $("#reactMsg").html("يرجى تحديد صورة للتفاعل");
    return;
  }
  let formData = new FormData(this);
  formData.append("infoType", "ADD_AD_REACT");
  $.ajax({
    method: "POST",
    url: "./../ajax_requests/admin-requests/editPlatformInfo.php",
    data: formData,
    contentType: false,
    cache: false,
    processData: false,
    success: (res) => {
      console.log(res);
      res = JSON.parse(res);
      if (res.result) {
        getReactListItem(res.insertedID, res.insertedImage, value)
          .hide()
          .appendTo($("#reactsList"))
          .fadeIn();
        removeAdRreactFrom();
      } else {
        showErrorModal(res.message);
      }
    },
  });
});
// helper function 2
function getReactListItem(id, image, name) {
  return $(`<li data-reactID="${id}" data-val="${name}" data-img="${image}">
  <form enctype="multipart/form-data">
    <span class='used-num'>0</span>
    <span class="actions">
      <i data-editReact class="fas fa-edit"></i>
    </span>
    <img src="${image}" alt="">
    <input name="reactName" value="{${name}"/>
    <label for="reactIcon${id}" class="fas fa-upload"></label>
    <input  type="file" 
            id="reactIcon${id}" 
            name="reactIcon" 
            accept="image/png, image/jpeg" 
            hidden>
    <input type="text" name="reactID" value="${id}" hidden>
    <i data-delReact class='fas fa-trash'></i>
  </form>
</li>`);
}
// delete
$(document).on("click", "[data-delReact]", function () {
  let reactID = $(this).parents("[data-reactID]").data("reactid");
  $.ajax({
    method: "POST",
    url: "./../ajax_requests/admin-requests/editPlatformInfo.php",
    data: {
      infoType: "DELETE_AD_REACT",
      reactID,
    },
    success: (res) => {
      console.log(res);
      res = JSON.parse(res);
      if (res.result) {
        $(this)
          .parents("[data-reactID]")
          .fadeOut(300, function () {
            $(this).remove();
          });
      } else {
        showErrorModal(res.message);
      }
    },
  });
});
// End Advert Reacts scripts

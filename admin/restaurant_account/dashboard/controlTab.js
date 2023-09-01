let SelectBoxItOptions = {
  // Uses the jQueryUI theme for the drop down
  theme: "jqueryui",
  defaultText: " ",
};
$("select").selectBoxIt(SelectBoxItOptions);
// on edit restaurant background image
$(document).on("click", "#editBackImage", function () {
  getProfileImagesForm("BACK-IMAGE").hide().prependTo($("body")).fadeIn("fast");
});
$(document).on("click", "#editLogoImage", function () {
  getProfileImagesForm("LOGO-IMAGE").hide().prependTo($("body")).fadeIn("fast");
});
function getProfileImagesForm(imageType) {
  let Modal = $(`<div class="popup-overlay allow-close"></div>`);
  let title, formID, defaultImage;
  if (imageType == "BACK-IMAGE") {
    title = "صورة الغلاف";
    formID = "profileBackImageForm";
    defaultImage = $("#back-img").prop("src");
  } else {
    title = "الصورة المصغرة";
    formID = "profileLogoImageForm";
    defaultImage = $("#logo-img").prop("src");
  }
  let restID = $("main[data-restID]").data().restid;
  Modal.html(`
  <div class="popup-box">
    <form
      id="${formID}"
      class="profileImagsForm"
      enctype="multipart/form-data"
    >
      <h2>${title}</h2>
      <div id="edit-profile-image-result-msg"></div>
      <img src="${defaultImage}" />
      <div class='actions'>
        <label for="profileBackImage"><i class="fas fa-upload"></i> تحميل صورة</label>
        <input
          class="d-none"
          id="profileBackImage"
          accept="image/png, image/jpeg"
          type="file"
          name="profileImage"
        />
        <input type="hidden" name="restID" value="${restID}" />
        <button class="disabled" type="submit" disabled>حفظ</button>
        <button class="red js-close-popup">إلغاء</button>
      </div>
    </form>
  </div>`);
  return Modal;
}
$(document).on("change", ".profileImagsForm input[type='file']", function (e) {
  // clear any old messages
  $("#edit-profile-image-result-msg").html("");
  // get files
  let files = e.target.files;
  if (files.length <= 0) {
    $("#edit-profile-image-result-msg").html(
      "<div class='my-alert error'>لم تقم بتحديد ملف</div>"
    );
    $("form.profileImagsForm button[type='submit']")
      .attr("disabled", true)
      .toggleClass("disabled");
    $("form.profileImagsForm img").attr("src", "");
  } else {
    $("form.profileImagsForm img").attr("src", URL.createObjectURL(files[0]));
    $("form.profileImagsForm button[type='submit']")
      .removeAttr("disabled")
      .toggleClass("disabled");
  }
});
// on edit cover/logo image submition
$(document).on("submit", "#profileBackImageForm", function (e) {
  e.preventDefault();
  editRestProfileImageRequest.bind(this)("COVER");
});
$(document).on("submit", "#profileLogoImageForm", function (e) {
  e.preventDefault();
  editRestProfileImageRequest.bind(this)("LOGO");
});
function editRestProfileImageRequest(imageType) {
  let formData = new FormData(this);
  formData.append("imageType", imageType);
  $.ajax({
    method: "POST",
    url: "./../../ajax_requests/restaurant-requests/updateRestProfileImage.php",
    data: formData,
    contentType: false,
    cache: false,
    processData: false,
    success: (res) => {
      res = JSON.parse(res);
      let messageClass = "success";
      if (res.result) {
        $(this)
          .children(".actions")
          .html(`<button class="js-close-popup">إغلاق</button>`);
        let imgIdToChange = imageType == "COVER" ? "#back-img" : "#logo-img";
        $(imgIdToChange).attr(
          "src",
          `./../../uploads/restaurant_profile_images/${res.imageName}`
        );
      } else {
        messageClass = "error";
        $(this)
          .children("button[type='submit']")
          .attr("disabled", true)
          .addClass("disabled");
      }
      $("#edit-profile-image-result-msg").html(
        `<div class='my-alert ${messageClass}'>${res.message}</div>`
      );
    },
  });
}

/*
 ** Handle restaurant basic info section events
 ** enable/disable basic info form editing
 ** handle save edited info submition
 */
// on attempt to edit basic restaurant information
// var to store old info in case the user cancle the editing
let oldRestInfoFormData = restBasicInfo.cloneNode(true);
$(document).on("click", "#restBasicInfo #edit-rest-info-btn", function () {
  // remove any old messages
  $("#restBasicInfo").find(".my-alert").remove();
  // toggle buttons
  $(this).parent().html(`
  <button class="dash-btn dash-btn-green" type="submit">حفظ</button>
  <button class="dash-btn dash-btn-red" id="cancel-rest-info-btn">إلغاء</button>`);
  $("#restBasicInfo").addClass("active");
  $("#restBasicInfo input").removeAttr("disabled");
  oldRestInfoFormData = restBasicInfo.cloneNode(true);
});
// on cancel editing basic restaurant information
$(document).on("click", "#restBasicInfo #cancel-rest-info-btn", function () {
  closeRestInfoEditing();
  // return to old values
  restBasicInfo.name.value = oldRestInfoFormData.name.value;
  restBasicInfo.email.value = oldRestInfoFormData.email.value;
  restBasicInfo.phone.value = oldRestInfoFormData.phone.value;
  restBasicInfo.description.value = oldRestInfoFormData.description.value;
});
//
function closeRestInfoEditing() {
  $("#restBasicInfo .actions").html(
    `<button class="dash-btn" id="edit-rest-info-btn">تعديل</button>`
  );
  $("#restBasicInfo").removeClass("active");
  $("#restBasicInfo input").attr("disabled", true);
  $("#restBasicInfo small").html("");
}
// on submit edtied basic restaurant information
$(document).on("submit", "#restBasicInfo", function (e) {
  e.preventDefault();
  let validator = new ValidateIt();
  validator.email(this.email.value, $(".email-msg"));
  validator.name(this.name.value, $(".name-msg"));
  validator.phone(this.phone.value, $(".phone-msg"));
  if (validator.isValid) {
    let formData = new FormData(this);
    formData.append("infoType", "BASIC_INFO");
    $.ajax({
      method: "POST",
      url: "./../../ajax_requests/restaurant-requests/editRestaurantInfo.php",
      data: formData,
      contentType: false,
      cache: false,
      processData: false,
      success: (res) => {
        res = JSON.parse(res);
        let msgClass = res.result ? "success" : "error";
        $(`<div class="my-alert ${msgClass}">${res.message}</div>`)
          .hide()
          .prependTo($(this))
          .fadeIn("slow");
        closeRestInfoEditing();
        updateRestNameInUI(this.name.value);
      },
    });
  }
});
function updateRestNameInUI(newName) {
  $(".rest-title h3").html(newName);
  $(".sidebar h3").html(newName);
}
/*
 ** Handle Services changes
 **
 */
// manage custom checkbox events
$(".fastfood-service > div").click(function () {
  if (!$(this).hasClass("active")) {
    $(".fastfood-service > div").toggleClass("active");
    let checkboxStatus = $("#fastfood").prop("checked");
    $("#fastfood").prop("checked", !checkboxStatus).triggerHandler("click");
    if (!checkboxStatus) {
      // disable reservation service cuz it's fastfood
      $("#reservation").parents(".service").addClass("disable");
      // change reservation status to false in UI, (database dynamically will change it)
      $("#reservation").prop("checked", false);
    } else {
      // enable reservation service cuz it's restaurant
      $("#reservation").parents(".service").removeClass("disable");
    }
  }
});
$(document).on("click", ".services input[type='checkbox']", function () {
  updateServiceCall.bind(this)();
});
$("#fastfood").click(function (e) {
  updateServiceCall.bind(this)();
});
function updateServiceCall() {
  let serviceType = $(this).attr("name");
  let checkboxValue = $(this).prop("checked");
  let restID = $("main[data-restID]").data().restid;
  // change restaurant type to fastfood status
  $.ajax({
    method: "POST",
    url: "./../../ajax_requests/restaurant-requests/editRestaurantInfo.php",
    data: {
      infoType: "SERVICES",
      serviceType: serviceType,
      serviceValue: checkboxValue ? 1 : 0,
      restID: restID,
    },
    success: (res) => {
      console.log(res);
      res = JSON.parse(res);
      if (!res.result) {
        $("#servicesMsg").html("");
        $(`<div class="my-alert error">${res.message}</div>`)
          .hide()
          .prependTo($("#servicesMsg"))
          .fadeIn("slow");
      }
    },
  });
}
/*
 ** Handle Work Hours section events
 */
$(document).on("submit", "form#editWorkHours", function (e) {
  e.preventDefault();
  let restID = $("main[data-restID]").data().restid;
  let openTime = this.openTime.value;
  let closeTime = this.closeTime.value;
  let validator = new ValidateIt();
  validator.workHour(openTime, closeTime, $("#workHour-msg"));
  if (validator.isValid) {
    $.ajax({
      method: "POST",
      url: "./../../ajax_requests/restaurant-requests/editRestaurantInfo.php",
      data: {
        infoType: "WORKHOURS",
        openTime: openTime,
        closeTime: closeTime,
        restID: restID,
      },
      success: (res) => {
        res = JSON.parse(res);
        let msgClass = res.result ? "success" : "error";
        $(this).find(".my-alert").remove();
        $(`<div class="my-alert ${msgClass}">${res.message}</div>`)
          .hide()
          .prependTo(this)
          .fadeIn("slow");
        if (res.result) {
          // update workhour in ui
          openTime = getHourMinutesFormat(openTime);
          closeTime = getHourMinutesFormat(closeTime);
          $("#currentTimes").html(`من ${openTime} إلى ${closeTime}`);
          // clear form inputs
        }
      },
    });
  }
});
/*
 ** Handle Addresses section events, addresses table
 ** show/hide edit address form,
 ** handle new address submition
 ** handle address deletion
 */
// on attempt to delete addresses
$(document).on("click", "[data-deleteAddress]", function () {
  // get address id
  const addressID = parseInt($(this).parents("tr").attr("data-addressRowID"));
  // show confirm modal
  let addressInfo = getAddressInfoFromRow(addressID);
  let deleteModal = createAddressDelConfirmModal(addressInfo);
  $("body").prepend(deleteModal);
});
function createAddressDelConfirmModal(addressInfo) {
  let modal = $(
    `<div id="restDelModal" class="popup-overlay allow-close"></div>`
  );
  modal.append(`<div class="popup-box txt-c">
    <h2 class="fs-15">هل أنت متأكد من حذف العنوان</h2>
    <div class="fs-14 m-20">
      ${addressInfo.state}, 
      ${addressInfo.region}, 
      ${addressInfo.street}
    </div>
    <div class='actions'>
      <button class="js-close-popup">إلغاء</button>
      <button class="red" onClick="deleteAddress(${addressInfo.id})">حذف</button>
    </div>
  </div>`);
  return modal;
}
function deleteAddress(addressID) {
  let restID = $("main[data-restID]").data().restid;
  $.ajax({
    method: "POST",
    url: "./../../ajax_requests/restaurant-requests/editRestaurantInfo.php",
    data: {
      infoType: "DELETE_ADDRESS",
      addressID: addressID,
      restID: restID,
    },
    success: (res) => {
      res = JSON.parse(res);
      msgClass = res.result ? "success" : "error";
      $("#restDelModal .popup-box").html(`
      <div class="my-alert ${msgClass}">${res.message}</div>
      <button class="js-close-popup">إغلاق</button>
      `);
      if (res.result) {
        // remove address row from table
        $(`table#restAddresses [data-addressRowID='${addressID}']`).fadeOut(
          function () {
            $(this).remove();
          }
        );
      }
    },
  });
}
// on attempt to edit addresse
$(document).on("click", "[data-editAddress]", function () {
  // get address id
  let parentRow = $(this).parents("tr");
  const addressID = parseInt(parentRow.attr("data-addressRowID"));
  if ($(this).hasClass("active")) {
    $(this).removeClass("active");
    // remove existed edit form row
    $(`#restAddresses #${addressID}`).fadeOut(300, function () {
      $(this).remove();
    });
  } else {
    // remove any old opened forms
    $("#restAddresses tr:has(#edit-address)").remove();
    $("#restAddresses [data-editAddress]").removeClass("active");
    $(this).addClass("active");
    // get address current info
    let addInfo = getAddressInfoFromRow(addressID);
    // create edit address from
    let editForm = createEditAddressForm(addInfo);
    // create new row to add form in it and append it to the table
    let row = $(`<tr id='${addressID}'><td colspan='4'></td></tr>`);
    row.children("td").append(editForm);
    row.hide().insertAfter(parentRow).fadeIn(300);
  }
});
function createEditAddressForm(addressInfo) {
  // destroy old selectboxit object and add new one
  $("#add-address select").data("selectBox-selectBoxIt").destroy();
  // get existed add address form and customize it
  let editForm = $("#add-address").clone(true).prop("id", "edit-address");
  $("#add-address select").selectBoxIt(SelectBoxItOptions);
  editForm.find("select").selectBoxIt(SelectBoxItOptions);
  editForm.find("select").val(addressInfo.state_id).change();
  editForm.find("[name='region']").val(addressInfo.region);
  editForm.find("[name='street']").val(addressInfo.street);
  editForm.find("[type='submit']").html("تعديل");
  // remove any messages
  editForm.find(".my-alert").remove();
  editForm.find("small").html("");
  // add hidden input for address id
  editForm.append(
    `<input type="hidden" name="addressID" value="${addressInfo.id}" />`
  );
  return editForm;
}
// on edit address
$(document).on("submit", "#edit-address", function (e) {
  e.preventDefault();
  let restID = $("main[data-restID]").data().restid;
  let addressID = this.addressID.value;
  let addressInfo = {
    state_id: this.state.value,
    state: $(this).find("option:selected").text(),
    region: this.region.value,
    street: this.street.value,
  };
  // validation
  let isNotValid = isNotValidAddress(addressInfo);
  if (isNotValid) {
    $(this).find("small").html(isNotValid);
  } else {
    $.ajax({
      method: "POST",
      url: "./../../ajax_requests/restaurant-requests/editRestaurantInfo.php",
      data: {
        infoType: "EDIT_ADDRESS",
        ...addressInfo,
        restID: restID,
        addressID: addressID,
      },
      success: (res) => {
        res = JSON.parse(res);
        if (res.result) {
          // update address in table
          let editedAddressRow = $(`[data-addressRowID="${addressID}"]`);
          updateAddressRow(editedAddressRow, addressInfo);
          // remove form
          editedAddressRow.find("[data-editAddress]").click();
        } else {
          $(this).find("small").html(res.message);
        }
      },
    });
  }
});
function updateAddressRow(addressRow, addressInfo) {
  addressRow.find("[data-stateID]").attr("data-stateID", addressInfo.state_id);
  addressRow.find("[data-stateID]").html(addressInfo.state);
  addressRow.find("[data-region]").html(addressInfo.region);
  addressRow.find("[data-street]").html(addressInfo.street);
}
// on add new address
$(document).on("submit", "#add-address", function (e) {
  e.preventDefault();
  // remove any old messages
  $(this).find("small").html("");
  $(this).find(".my-alert").remove();
  let restID = $("main[data-restID]").data().restid;
  // get entered info
  let addressInfo = {
    state_id: this.state.value,
    state: $(this).find("option:selected").text(),
    region: this.region.value,
    street: this.street.value,
  };
  let isNotValid = isNotValidAddress(addressInfo);
  if (isNotValid) {
    $(this).find("small").html(isNotValid);
  } else {
    // add address
    $.ajax({
      method: "POST",
      url: "./../../ajax_requests/restaurant-requests/editRestaurantInfo.php",
      data: {
        infoType: "ADD_ADDRESS",
        ...addressInfo,
        restID: restID,
      },
      success: (res) => {
        res = JSON.parse(res);
        let msgClass = res.result ? "success" : "error";
        let msgAlert = $(
          `<div class="my-alert ${msgClass}">${res.message}</div>`
        );
        msgAlert.hide().prependTo(this).fadeIn("slow");
        if (res.result) {
          // add new address to addresses table
          createAddressRow(res.addedAddress)
            .hide()
            .appendTo($("#restAddresses"))
            .fadeIn();
          // clear form inputs
          $(this).find("input").val("");
          $(this).find("select").val("").change();
        }
      },
    });
  }
});
function createAddressRow(address) {
  let rowsNum = $("#restAddresses tbody tr[data-addressRowID]").length;
  if (rowsNum == 0) {
    deleteOption = "";
    // remove "no address" row
    $("#restAddresses tbody").html("");
  } else {
    deleteOption = `<i data-deleteAddress class="fas fa-trash del"></i>`;
  }
  return $(`
  <tr data-addressRowID="${address.id}">
    <td>
      ${deleteOption}
      <i data-editAddress class="fas fa-edit"></i>
    </td>
    <td data-stateID="${address.state_id}">${address.state}</td>
    <td data-region>${address.region}</td>
    <td data-street>${address.street}</td>
  </tr>`);
}
// helper functions for addresses delete/edit events
function isNotValidAddress(address) {
  let validator = new ValidateIt();
  let msg = [];
  if (validator.isEmpty(address.state_id)) {
    msg.push("المحافظة");
  }
  if (validator.isEmpty(address.region)) {
    msg.push("المنطقة");
  }
  if (validator.isEmpty(address.street)) {
    msg.push("الشارع");
  }
  if (msg.length > 0) {
    msg[0] = "يرجى تحديد " + msg[0];
    return msg.join("،");
  }
  return false;
}
function getAddressInfoFromRow(addressID) {
  let addressRow = $(`[data-addressRowID='${addressID}']`);
  return {
    id: addressID,
    state_id: addressRow.find("[data-stateID]").attr("data-stateID"),
    state: addressRow.find("[data-stateID]").html(),
    region: addressRow.find("[data-region]").html(),
    street: addressRow.find("[data-street]").html(),
  };
}

/*
 ** Handle change password event
 ** show/hide confirm password form on attempt to delete
 ** in change password section
 */
$(document).on("click", "#change-pass-btn", function () {
  let restID = $(this).attr("data-restID");
  if ($(this).hasClass("active")) {
    $(".change-pass-form > *").slideUp("slow", function () {
      $(this).remove();
      $("#change-pass-btn").html("تغيير كلمة المرور");
    });
    $("#change-pass-btn").html("تغيير كلمة المرور"); // in case form container has nothing to slideUp
  } else {
    getPasswordConfirmForm(restID, "confirmPasswordToChange")
      .hide()
      .prependTo($(".change-pass-form"))
      .slideDown("slow", () => {
        $(this).html("إلغاء");
      });
  }
  $(this).toggleClass("active");
});
// on confirm current password to change account password
$(document).on("submit", "#confirmPasswordToChange", function (e) {
  e.preventDefault();
  confirmPasswordRequest.bind(this)(getChangePassForm(this.restID.value));
});
function getChangePassForm(restID) {
  let changePassForm = $(`<form id="changePassword"></form>`);
  changePassForm.html(`
  <label class="fs-13 mb-10">أدخل كلمة المرور الجديدة:</label>
  <input
  type="password"
  name="pass"
  placeholder="كلمة المرور"
  class="dash-input w-full fs-13"
  />
  <input
  type="password"
  name="rePass"
  placeholder="تأكيد كلمة المرور"
  class="dash-input w-full fs-13 mt-10"
  />
  <input type="hidden" name="restID" value="${restID}" />
  <input class="mt-10 mb-10 dash-btn" type="submit" value="إرسال" />
  <small></small>`);
  return changePassForm;
}
// on change password
$(document).on("submit", "#changePassword", function (e) {
  e.preventDefault();
  let parent = $(this).parent();
  let validator = new ValidateIt();
  validator.password(this.pass.value, this.rePass.value, $(this).find("small"));
  if (validator.isValid) {
    $.ajax({
      method: "POST",
      url: "./../../ajax_requests/restaurant-requests/editRestaurantInfo.php",
      data: {
        infoType: "CHANGE_PASSWORD",
        restID: this.restID.value,
        pass: this.pass.value,
      },
      success: (res) => {
        console.log(res);
        res = JSON.parse(res);
        console.log(res);
        let msgClass = res.result ? "success" : "error";
        let msgAlert = `<div class='my-alert ${msgClass}'>${res.message}</div>`;
        $(msgAlert)
          .hide()
          .prependTo(parent)
          .fadeIn(() => {
            $(this).slideUp(function () {
              $(this).remove();
            });
          });
        $("#change-pass-btn").html("إغلاق");
      },
    });
  }
  // A0997735597k!
});

/*
 ** Handle attempt to delete my restaurant account
 ** show/hide confirm password form on attempt to delete
 ** in delete account section
 */
$(document).on("click", "#delete-myRest", function () {
  let restID = $(this).attr("data-restID");
  if ($(this).hasClass("active")) {
    $(".delete-my-rest-form > *").slideUp("slow", function () {
      $(this).remove();
      $("#delete-myRest").html("حذف");
    });
  } else {
    getPasswordConfirmForm(restID, "confirmPasswordToDelete")
      .hide()
      .prependTo($(".delete-my-rest-form"))
      .slideDown("slow", () => {
        $(this).html("إلغاء");
      });
  }
  $(this).toggleClass("active");
});
// on confirm current password to delete my restaurant account
$(document).on("submit", "#confirmPasswordToDelete", function (e) {
  e.preventDefault();
  confirmPasswordRequest.bind(this)(getDeleteAccountForm(this.restID.value));
});
function getDeleteAccountForm(restID) {
  let deleteForm = $(`<form id="deleteMyRest"></form>`);
  deleteForm.html(`
    <input type="hidden" name="restID" value="${restID}" />
    <label class="fw-bold fs-14 c-orang"
      >هل أنت متأكد من حذف الحساب بشكل نهائي! سيتم حذف جميع بيانات المطعم في حال تأكيد الحذف.</label
    >
    <input
      class="dash-btn dash-btn-orange-alt w-full mt-10 mb-10"
      type="submit"
      value="نعم، حذف حساب المعطم الخاص بي."
    />
    <small></small>`);
  return deleteForm;
}
// delete my restaurant submition
$(document).on("submit", "#deleteMyRest", function (e) {
  e.preventDefault();
  $.ajax({
    method: "POST",
    url: "./../../ajax_requests/restaurant-requests/editRestaurantInfo.php",
    data: { infoType: "DELETE_MY_RESTAURANT", restID: this.restID.value },
    success: (res) => {
      console.log(res);
      res = JSON.parse(res);
      if (res.result) {
        window.location.replace("./../../index.php");
      } else {
        $(this).find("small").html(res.message);
      }
    },
  });
});
// functions to help with password confirmation
function getPasswordConfirmForm(restID, formID) {
  let confirmPassForm = $(`<form id="${formID}"></form>`);
  confirmPassForm.html(`
  <label class="fs-13 mb-10">يرجى إدخال كلمة المرور الحالية للمتابعة:</label>
  <input
    type="password"
    name="pass"
    placeholder="كلمة المرور"
    class="dash-input w-full fs-13"
  />
  <input type="hidden" name="restID" value="${restID}" />
  <input class="mt-10 mb-10 dash-btn" type="submit" value="إرسال" />
  <small></small>`);
  return confirmPassForm;
}
function confirmPasswordRequest(formToAddAfterSuccess) {
  let password = this.pass.value;
  return $.ajax({
    method: "POST",
    url: "./../../ajax_requests/restaurant-requests/editRestaurantInfo.php",
    data: {
      infoType: "CONFIRM_PASSWORD",
      restID: this.restID.value,
      pass: password,
    },
  })
    .then((result) => {
      console.log(result);
      return JSON.parse(result);
    })
    .then((res) => {
      console.log(res);
      if (res.result) {
        let parent = $(this).parent();
        $(this).fadeOut("fast", function () {
          $(this).remove();
          formToAddAfterSuccess.hide().appendTo(parent).fadeIn("fast");
        });
      } else {
        $(this).find("small").html(res.message);
      }
    });
}

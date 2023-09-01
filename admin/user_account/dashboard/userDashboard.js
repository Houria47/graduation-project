// on attempt to edit basic user information
// var to store old info in case the user cancle the editing
let oldUserInfoFormData = userInfo.cloneNode(true);
let oldUserImage = $("#userImagePreview").attr("src");
$(document).on("click", "#userInfo #edit-user-info-btn", function () {
  // remove any old messages
  $("#userInfo").find(".my-alert").remove();
  // toggle buttons
  $(this).parent().html(`
  <button class="dash-btn dash-btn-green" type="submit">حفظ</button>
  <button class="dash-btn dash-btn-orange" id="cancel-user-info-btn">إلغاء</button>`);
  $("#userInfo").addClass("active");
  $("#userInfo input").removeAttr("disabled");
  oldUserInfoFormData = userInfo.cloneNode(true);
});
// on cancel editing basic restaurant information
$(document).on("click", "#userInfo #cancel-user-info-btn", function () {
  closeRestInfoEditing();
  // return to old values
  userInfo.name.value = oldUserInfoFormData.name.value;
  userInfo.email.value = oldUserInfoFormData.email.value;
  userInfo.phone.value = oldUserInfoFormData.phone.value;
  userInfo.address.value = oldUserInfoFormData.address.value;
  $("#userImagePreview").attr("src", oldUserImage);
});
//
function closeRestInfoEditing() {
  $("#userInfo .actions").html(
    `<button class="dash-btn" id="edit-user-info-btn">تعديل</button>`
  );
  $("#userInfo").removeClass("active");
  $("#userInfo input").attr("disabled", true);
  $("#userInfo small").html("");
}
// on user image change
$(document).on("change", "#userImage", function (e) {
  let files = e.target.files;
  if (files.length != 0) {
    $("#userImagePreview").attr("src", URL.createObjectURL(files[0]));
  }
});
// on submit edtied basic restaurant information
$(document).on("submit", "#userInfo", function (e) {
  e.preventDefault();
  let validator = new ValidateIt();
  validator.email(this.email.value, $(this).find("small"));
  if (validator.isValid) {
    $.ajax({
      method: "POST",
      url: "./../../ajax_requests/user-requests/editUserInfo.php",
      data: new FormData(this),
      contentType: false,
      cache: false,
      processData: false,
      success: (res) => {
        console.log(res);
        res = JSON.parse(res);
        console.log(res);
        let msgClass = res.result ? "success" : "error";
        $(`<div class="my-alert ${msgClass}">${res.message}</div>`)
          .hide()
          .prependTo($(this))
          .fadeIn("slow");
        closeRestInfoEditing();
        updateUserInfoInUI(res.updatedUser);
      },
    });
  }
});
function updateUserInfoInUI(updatedUser) {
  $("[data-userName]").html(updatedUser.name);
  $("[data-userAddress]").html(updatedUser.address);
  $("[data-userNumber]").html(updatedUser.phone);
  $("[data-userImage]").attr("src", updatedUser.image);
}

/*
 ** Handle change password event
 ** show/hide confirm password form on attempt to delete
 ** in change password section
 */
$(document).on("click", "#change-pass-btn", function () {
  let userID = $("[data-userID]").attr("data-userID");
  if ($(this).hasClass("active")) {
    $(".change-pass-form > *").slideUp("slow", function () {
      $(this).remove();
      $("#change-pass-btn").html("تغيير كلمة المرور");
    });
    $("#change-pass-btn").html("تغيير كلمة المرور"); // in case form container has nothing to slideUp
  } else {
    getPasswordConfirmForm(userID, "confirmPasswordToChange")
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
  confirmPasswordRequest.bind(this)(getChangePassForm(this.userID.value));
});
function getChangePassForm(userID) {
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
  <input type="hidden" name="userID" value="${userID}" />
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
      url: "./../../ajax_requests/user-requests/accountSettings.php",
      data: {
        infoType: "CHANGE_PASSWORD",
        userID: this.userID.value,
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
 ** Handle attempt to delete my user account
 ** show/hide confirm password form on attempt to delete
 ** in delete account section
 */
$(document).on("click", "#delete-myAccount", function () {
  let userID = $("[data-userID]").attr("data-userID");
  if ($(this).hasClass("active")) {
    $(".delete-my-account-form > *").slideUp("slow", function () {
      $(this).remove();
      $("#delete-myAccount").html("حذف");
    });
  } else {
    getPasswordConfirmForm(userID, "confirmPasswordToDelete")
      .hide()
      .prependTo($(".delete-my-account-form"))
      .slideDown("slow", () => {
        $(this).html("إلغاء");
      });
  }
  $(this).toggleClass("active");
});
// on confirm current password to delete my restaurant account
$(document).on("submit", "#confirmPasswordToDelete", function (e) {
  e.preventDefault();
  confirmPasswordRequest.bind(this)(getDeleteAccountForm(this.userID.value));
});
function getDeleteAccountForm(userID) {
  let deleteForm = $(`<form id="deleteMyAccount"></form>`);
  deleteForm.html(`
    <input type="hidden" name="userID" value="${userID}" />
    <label class="fw-bold fs-14 c-orang"
      >هل أنت متأكد من حذف الحساب بشكل نهائي! سيتم حذف جميع بياناتك في حال تأكيد الحذف.</label
    >
    <input
      class="dash-btn dash-btn-orange w-full mt-10 mb-10"
      type="submit"
      value="نعم، حذف حسابي."
    />
    <small></small>`);
  return deleteForm;
}
// delete my restaurant submition
$(document).on("submit", "#deleteMyAccount", function (e) {
  e.preventDefault();
  $.ajax({
    method: "POST",
    url: "./../../ajax_requests/user-requests/accountSettings.php",
    data: { infoType: "DELETE_MY_ACCOUNT", userID: this.userID.value },
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
function getPasswordConfirmForm(userID, formID) {
  let confirmPassForm = $(`<form id="${formID}"></form>`);
  confirmPassForm.html(`
  <label class="fs-13 mb-10">يرجى إدخال كلمة المرور الحالية للمتابعة:</label>
  <input
    type="password"
    name="pass"
    placeholder="كلمة المرور"
    class="dash-input w-full fs-13"
  />
  <input type="hidden" name="userID" value="${userID}" />
  <input class="mt-10 mb-10 dash-btn" type="submit" value="إرسال" />
  <small></small>`);
  return confirmPassForm;
}
function confirmPasswordRequest(formToAddAfterSuccess) {
  let password = this.pass.value;
  console.log(password);
  console.log(this.userID.value);
  return $.ajax({
    method: "POST",
    url: "./../../ajax_requests/user-requests/accountSettings.php",
    data: {
      infoType: "CONFIRM_PASSWORD",
      userID: this.userID.value,
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

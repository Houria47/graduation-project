// Trigger the SelectBoxIt
$(".log-reg-page select").selectBoxIt({
  // Uses the jQueryUI theme for the drop down
  theme: "jqueryui",
  defaultText: " ",
});

$(document).on("click", "#fastFoodRadio .radioBtn-js div", function (e) {
  const val = parseInt($(this).attr("data-value"));
  if (val) {
    // if it's a fast food restaurant
    $("#reserveRadio .radioBtn-js div[data-value=0]").switchClass(
      "notActive",
      "active"
    );
    $("#reserveRadio .radioBtn-js div[data-value=1]").switchClass(
      "active",
      "notActive"
    );
    $("#reserveRadio .radioBtn-js input").val(0);
    $("#reserveRadio").addClass("availability-none");
  } else {
    $("#reserveRadio").removeClass("availability-none");
  }
});
/*
 ** Preview Authentication Files after Upload
 ** this is for restaurant register page and may i use it for
 ** another pages or file inputs later by adding some updates..
 ** #auth-files is the element that will uplaod the files on click.
 ** .files-preview is the element that will preview them.
 */
$(document).on("change", "#auth-files", function (e) {
  let files = e.target.files;
  $(".files-preview").html("");
  for (let i = 0; i < files.length; i++) {
    let url =
      files[i].type !== "application/pdf"
        ? URL.createObjectURL(files[i])
        : "../layout/images/Standard/pdf.svg";
    let name = files[i].name;
    $(".files-preview").append(createFileBoxPreview(name, url));
  }
});
/*
 ** Function to create box element which will appended to
 ** .files-preview for each file added by #auth-files
 */
function createFileBoxPreview(fileName, filePath) {
  let box = `
    <div class="box rad-6 p-10">
      <img src="${filePath}" alt="">
      <span class="d-block txt-c mt-2">${fileName}</span>
    </div>`;
  return box;
}

// Restaurant Regester Form submission
$("form#res-reg").on("submit", (e) => {
  e.preventDefault();
  let form = e.target;
  let validator = new ValidateIt();
  validator.email(form.email.value, $(".email-msg"));
  validator.name(form.restName.value, $(".name-msg"));
  validator.password(form.pass.value, form.rePass.value, $(".pass-msg"));
  validator.workHour(
    form.openTime.value,
    form.closeTime.value,
    $(".workHour-msg")
  );
  validator.phone("09" + form.phone.value, $(".phone-msg"));
  validator.isEmpty(form.state.value)
    ? $(".state-msg").html("يرجى تحديد المحافظة")
    : $(".state-msg").html("");

  validator.isEmpty(form.region.value)
    ? $(".region-msg").html("يرجى تحديد المنطقة")
    : $(".region-msg").html("");

  validator.isEmpty(form.street.value)
    ? $(".street-msg").html("يرجى تحديد الشارع")
    : $(".street-msg").html("");

  validator.isEmpty(form.delivery.value)
    ? $(".delivery-msg").html("يرجى تحديد خيار")
    : $(".delivery-msg").html("");

  validator.isEmpty(form.fastfood.value)
    ? $(".fastfood-msg").html("يرجى تحديد خيار")
    : $(".fastfood-msg").html("");

  validator.isEmpty(form.reserv.value)
    ? $(".reserv-msg").html("يرجى تحديد خيار")
    : $(".reserv-msg").html("");

  validator.isEmpty(form["authFiles[]"].value)
    ? $(".files-msg").html("يرجى تحميل وثائق تأكيد الملكية")
    : $(".files-msg").html("");
  if (validator.isValid) {
    // AJAX request to check if email available
    $.ajax({
      method: "POST",
      url: "../ajax_requests/isExist.php",
      data: {
        select: "email",
        from: "restaurant",
        value: `${form.email.value}`,
      },
      success: (res) => {
        console.log(res);
        res = JSON.parse(res);
        if (res.result) {
          // email already exist, show error message
          $(".email-msg").html("البريد موجود مسبقا، يرجى اختيار بريد آخر");
        } else {
          let formData = new FormData(form);
          submitReisterData(formData);
        }
      },
    });
  }
  // validator.isValid = true;
});

function submitReisterData(data) {
  console.log("running");
  // show loading sppiner
  $("body").prepend(`<div class="popup-overlay">
    <div class="popup-sppiner center-flex h-full">
      <div class="lds-ring">
        <div></div><div></div><div></div><div></div></div>
      </div>
    </div>`);

  $.ajax({
    method: "POST",
    url: "./../ajax_requests/user-requests/register.php",
    data: data,
    contentType: false,
    cache: false,
    processData: false,
    success: (res) => {
      console.log(res);
      res = JSON.parse(res);
      console.log(res);
      $(".popup-overlay").addClass("allow-close");
      if (res.result) {
        showSuccessMessage(res.message);
      } else {
        showFailedMessage(res.message);
      }
    },
  });
}

function showSuccessMessage(msg) {
  $(".popup-overlay").html(`
  <div class="popup-box">
    <img class="img-confitte" src="../layout/images/confetti.png" alt="">
    <h2>تم التسجيل بنجاح</h2>
    <img src="../layout/images/mail.jpg" alt="">
    <p>${msg}</p>
    <a href="restLogin.php">تسجيل الدخول</a>
  </div>`);
}

function showFailedMessage(msg) {
  $(".popup-overlay").html(`
  <div class="popup-box">
    <h2>فشل التسجيل</h2>
    <img src="../layout/images/sad_customer.jpg" alt="">
    <p>${msg}</p>
    <a href="#" class="js-close-popup">حسناً</a>
  </div>`);
}
// Restaurant Login Form submission
$("form#res-log").on("submit", (e) => {
  e.preventDefault();
  let form = e.target;
  let validator = new ValidateIt();
  validator.email(form.email.value, $(".email-msg"));
  $(".pass-msg").html("");
  if (validator.isEmpty(form.pass.value))
    $(".pass-msg").html("حقل كلمة المرور لا يجب أن يكون فارغ");

  if (validator.isValid) {
    form.submit();
  }
});

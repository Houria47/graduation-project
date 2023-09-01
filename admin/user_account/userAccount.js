// Restaurant Login Form submission
$("form#user-login").on("submit", (e) => {
  e.preventDefault();
  let form = e.target;
  let validator = new ValidateIt();
  validator.email(form.email.value, $(".log-email-msg"));
  $(".log-pass-msg").html("");
  if (validator.isEmpty(form.pass.value))
    $(".log-pass-msg").html("حقل كلمة المرور لا يجب أن يكون فارغ");

  if (validator.isValid) {
    form.submit();
  }
});

// Restaurant Login Form submission
$("form#user-register").on("submit", (e) => {
  e.preventDefault();
  let form = e.target;
  let validator = new ValidateIt();
  validator.email(form.email.value, $(".reg-email-msg"));
  validator.password(form.pass.value, form.rePass.value, $(".reg-pass-msg"));
  $(".reg-rePass-msg").html("");
  if (validator.isEmpty(form.rePass.value))
    $(".reg-rePass-msg").html("حقل تأكيد كلمة المرور لا يجب أن يكون فارغ");

  if (validator.isValid) {
    // AJAX request to check if email available
    $.ajax({
      method: "POST",
      url: "../ajax_requests/isExist.php",
      data: {
        select: "email",
        from: "customer",
        value: `${form.email.value}`,
      },
      success: function (res) {
        res = JSON.parse(res);
        if (res.result) {
          // email already exist, show error message
          $(".reg-email-msg").html("البريد موجود مسبقا، يرجى اختيار بريد آخر");
        } else {
          form.submit();
        }
      },
    });
  }
});

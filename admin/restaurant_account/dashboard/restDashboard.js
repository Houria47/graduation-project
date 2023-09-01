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
      <span class="d-block txt-c mt-2 c-white fs-13">${fileName}</span>
    </div>`;
  return box;
}
/*
 ** Show update authentiaction files form on enable editing in the modal
 */
$(document).on("click", ".js-edit-auth", function () {
  // clear any old messages
  $("#edit-auth-result-msg").html("");
  $(".edit-auth-form").removeClass("d-none");
  $(this).addClass("d-none");
});

/*
 ** Preview old authentiaction files onCancle in the modal
 */
$(document).on("click", ".js-cancel-edit-auth", function () {
  // clear any old messages
  $("#edit-auth-result-msg").html("");
  // get old files, and preview them
  $.ajax({
    method: "GET",
    url: "./../../ajax_requests/restaurant-requests/getLatestAuthFilesFromSession.php",
    success: function (res) {
      $(".files-preview").html(res);
    },
  });
  $(".js-edit-auth").removeClass("d-none");
  $(".edit-auth-form").addClass("d-none");
});

/*
 ** Update authentiaction files Preview in the modal
 */
$(document).on("change", "#edit-auth-input", function (e) {
  // clear any old messages
  $("#edit-auth-result-msg").html("");
  // get files
  let files = e.target.files;
  if (files.length <= 0) {
    $("#edit-auth-result-msg").html(
      "<div class='my-alert error-black'>لم يتم تحديد أية ملفات</div>"
    );
    $("form#edit-auth-form button[type='submit']").addClass("disable");
  } else {
    $(".files-preview").html("");
    for (let i = 0; i < files.length; i++) {
      let url =
        files[i].type !== "application/pdf"
          ? URL.createObjectURL(files[i])
          : "../../layout/images/Standard/pdf.svg";
      let name = files[i].name;
      $(".files-preview").append(createFileBoxPreview(name, url));
    }
    $("form#edit-auth-form button[type='submit']").removeClass("disable");
  }
});
/*
 ** Update authentiaction files onSave in the modal
 */
$(document).on("submit", "form#edit-auth-form", function (e) {
  // clear any old messages
  $("#edit-auth-result-msg").html("");
  e.preventDefault();
  // updateAuthenticationFiles.php
  var formData = new FormData(this);
  $.ajax({
    method: "POST",
    url: "./../../ajax_requests/restaurant-requests/updateAuthenticationFiles.php",
    data: formData,
    contentType: false,
    cache: false,
    processData: false,
    success: function (res) {
      res = JSON.parse(res);
      if (res.result) {
        //  show error message
        let msg = "تم تعديل الملفات بنجاح.";
        $("#edit-auth-result-msg").html(
          `<div class="my-alert success-black"> ${msg}</div>`
        );
      } else {
        //
        let msg = `فشل التعديل: ${res.message}`;
        $("#edit-auth-result-msg").html(
          `<div class="my-alert error-black"> ${msg}</div>`
        );
      }
    },
  });
  $("form#edit-auth-form button[type='submit']").addClass("disable");
});

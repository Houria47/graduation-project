let SelectBoxItOptions = {
  // Uses the jQueryUI theme for the drop down
  theme: "jqueryui",
};
$("select").selectBoxIt(SelectBoxItOptions);

let Posts = [];
function getPostElement(id) {
  let index = Posts.findIndex((post) => post.id == id);
  return Posts[index];
}
// Get posts data from database on page load
$(document).ready(function () {
  // get data from Database
  if ($("#posts-card").length > 0) {
    // meals list has been loaded
    let restID = $("[data-restID]").attr("data-restID");
    // get restaurant menus list and recipes
    $.ajax({
      method: "GET",
      url: "./../../ajax_requests/restaurant-requests/posts-requests/getRestaurantPosts.php",
      data: { restID },
      success: (res) => {
        res = JSON.parse(res);
        if (res.result) {
          Posts = res.posts;
        }
      },
    });
  }
});
// Start Add Post script
// form validation and textarea chars count changes
$(document).on("input", "#add-post-form textarea", function (e) {
  let enteredTextLength = e.target.value.trim().length;
  let maxlength = $(this).attr("maxlength");
  $(this).parent().removeClass("over-flow");

  $(this).parent().remove("over-flow");
  if (enteredTextLength > 0) {
    $("#add-post-form button").removeClass("disabled");
  } else {
    $("#add-post-form button").addClass("disabled");
  }
  if (enteredTextLength >= maxlength) {
    $(this).parent().addClass("over-flow");
  }
  $("#add-post-form .word-count span").html(maxlength - enteredTextLength);
});
// preview media on select images for the post
$(document).on("change", "#add-post-form #media", function (e) {
  let mediaPreview = $("#add-post-form .media-preview");
  mediaPreview.html("");
  for (let i = 0; i < this.files.length; i++) {
    mediaPreview.append(
      `<img src="${window.URL.createObjectURL(this.files[i])}" />`
    );
  }
});

// on add post submit
$(document).on("submit", "#add-post-form", function (e) {
  e.preventDefault();
  $(this).find("small").html("");
  let validator = new ValidateIt();
  if (validator.isEmpty(this.caption.value)) {
    $(this).find("small").html("يرجى إضافة تفاصيل الإعلان");
    return;
  }
  if (validator.isEmpty(this.adType.value)) {
    $(this).find("small").html("يرجى تحديد نوع الإعلان");
    return;
  }
  $.ajax({
    method: "POST",
    url: "./../../ajax_requests/restaurant-requests/posts-requests/addPost.php",
    data: new FormData(this),
    contentType: false,
    cache: false,
    processData: false,
    success: (res) => {
      console.log(res);
      res = JSON.parse(res);
      console.log(res);
      if (res.result) {
        clearAddPostFrom(this);
        Posts.push(res.inserted_post);
        if ($(".posts").length == 0) {
          $("#posts-card .no-posts").remove();
          $("#posts-card").append(`<ul class="posts"></ul>`);
        }
        $(".posts").prepend(createPostListItem(res.inserted_post));
      }
      // remove old message
      $(this).find(".my-alert").remove();
      let msgClasess = res.result ? "success" : "error";
      $(this).prepend(
        $(`<div class="my-alert ${msgClasess}">${res.message}</div>`)
      );
    },
  });
});
function clearAddPostFrom(form) {
  form.caption.value = "";
  form.media.value = "";
  $(form).find(".word-count span").html(500);
  $(form).find(".media-preview").html("");
}
function createPostListItem(post) {
  let li = $(`<li class='post' data-postID='${post.id}'></li>`);

  let comments = `<div class='no-comments'>
          <p>لا يوجد تعليقات</p>
        </div>`;
  if (post.comments.length != 0) {
    comments = "<ul>";
    post.comments.map((comment) => {
      let date = new Date(comment.date);
      date = date.toLocaleDateString("ar-EG", {
        month: "long",
        day: "numeric",
        hour: "2-digit",
        minute: "2-digit",
      });
      comments += `<li>
          <img src='${comment["customer"]["image"]}' />
          <div>
            <div class='comment'>
              <h4>${comment["customer"]["name"]}</h4>
              <p>${comment["content"]}</p>
            </div>
            <div class='details'>
              <span>رد</span>
              <span>${date}</span>
            </div>
          </div>
        </li>`;
    });
    $comments += "</ul>";
  }
  let ad_type = post["type"]["name"].replace("إعلان", "");
  let postDate = new Date(post["created_at"]);
  postDate = postDate.toLocaleDateString("ar-EG", {
    month: "long",
    day: "numeric",
  });
  let reacts = "";
  let reactsNum = post["reacts"]["reacts_number"];

  if (reactsNum == 0) {
    let topThree = post["reacts"]["top_three"];
    topThree.map((react) => {
      reacts += `<img src='${react["image"]}'/>`;
    });
  }

  let commentsNum = post["comments"].length;
  let postMedia = "";
  if (post["media"].length != 0) {
    post.media.map((image, idx) => {
      postMedia += `<div class='box ${idx == 0 ? "" : "d-none"}'>
            <img src='${image}'/>
          </div>`;
    });
    if (post["media"].length > 1) {
      let count = post["media"].length - 1;
      postMedia += `<div data-post-media class='more-media'>
      <div class='show'><span>+${count}</span>عرض الكل</div>
      <div class='hide'>إغلاق</div>
    </div>`;
    }
  }
  let content = `<header>
  <i data-post-options class='fas fa fa-ellipsis-h'></i>
  <div class='post-options'>
    <div data-edit-post ><i class='fas fa-edit'></i>تعديل</div>
    <div data-del-post ><i class='fas fa-trash'></i>حذف</div>
  </div>
  <div>
    <div class='type'>${ad_type}</div>
    <div class='date'>${postDate}</div>
  </div>
</header>
<main>
  <p>${post["caption"]}</p>
  <div class='media'>
    ${postMedia}
  </div>
</main>
<div class='footer'>
  <div class='reacts'>
    ${reacts}
    <span>${reactsNum}</span>
  </div>
  <div data-post-comments>${commentsNum}<i class='fas fa-comment'></i></div>
</div>
<div class='comments'>
  ${comments}
  <div data-close-comments class='close-comments'>إغلاق <i class='fas fa-angle-down'></i></div>
</div>`;

  li.append(content);
  return li;
}
// End Add Post script
// Start Edit Post script
// on attempt to edit post
$(document).on("click", "[data-edit-post]", function () {
  let postID = $(this).parents("li").attr("data-postID");
  let editPostModal = createEditPostModal(getPostElement(postID));
  $("body").prepend(editPostModal);
});
function createEditPostModal(post) {
  let restID = $("[data-restID]").attr("data-restID");
  let modal = $(`<div class="popup-overlay allow-close"></div>`);
  let media = "";
  post.media.map((image) => {
    media += `<img src="${image}" />`;
  });

  // destroy old selectboxit object and add new one
  $("#add-post-form select").data("selectBox-selectBoxIt").destroy();
  // get existed add post form and customize it
  let clonedSelect = $("#add-post-form select").clone(true);
  $("#add-post-form select").selectBoxIt(SelectBoxItOptions);
  let content = `<div class="popup-box">
    <header><h2>تعديل الإعلان</h2></header>
    <main>
      <form class="post-form" id="edit-post-form">
        <div class="textarea">
          <textarea
            name="caption"
            id="caption"
            cols="30"
            rows="5"
            maxlength="500"
            placeholder="اكتب تفاصيل الإعلان هنا"
          >${post["caption"]}</textarea>
          <div class="word-count">تبقى: <span>${
            500 - post.caption.length
          }</span> حرف</div>
        </div>
        <div class="input file">
          <div class="media-preview">${media}</div>
          <label for="media"><i class="fas fa-plus"></i> أضف صور</label>
          <input
            type="file"
            name="media[]"
            hidden
            id="media"
            multiple
            accept="image/*"
          />
        </div>
        <div class="input select-input">
          <label for="adType"><i class="fas fa-ad"></i> نوع الإعلان</label>
        </div>
        <input name="postID" value="${post.id}" hidden/>
        <input name="restID" value="${restID}" hidden/>
        <div class="actions">
          <button class="popup-btn">تعديل</button>
          <button type="button" class="popup-btn-alt js-close-popup">
            إلغاء
          </button>
        </div>
        <small></small>
      </form>
    </main>
  </div>
</div>`;
  modal.append(content);
  modal.find(".select-input").append(clonedSelect);
  modal.find("select").selectBoxIt(SelectBoxItOptions);
  modal.find("select").val(post.type.id).change();

  return modal;
}
// form validation and textarea chars count changes
$(document).on("input", "#edit-post-form textarea", function (e) {
  let enteredTextLength = e.target.value.trim().length;
  let maxlength = $(this).attr("maxlength");
  $(this).parent().removeClass("over-flow");

  $(this).parent().remove("over-flow");
  if (enteredTextLength > 0) {
    $("#edit-post-form button").removeClass("disabled");
  } else {
    $("#edit-post-form button").addClass("disabled");
  }
  if (enteredTextLength >= maxlength) {
    $(this).parent().addClass("over-flow");
  }
  $("#edit-post-form .word-count span").html(maxlength - enteredTextLength);
});
$(document).on("submit", "#edit-post-form", function (e) {
  e.preventDefault();
  if (this.caption.value.length == 0) {
    $(this).find("small").html("يرجى إضافة تفاصيل الإعلان");
    return;
  }

  $.ajax({
    method: "POST",
    url: "./../../ajax_requests/restaurant-requests/posts-requests/editPost.php",
    data: new FormData(this),
    contentType: false,
    cache: false,
    processData: false,
    success: (res) => {
      console.log(res);
      res = JSON.parse(res);
      console.log(res);
      if (res.result) {
        $(".popup-overlay").find("main").html(`<p>${res.message}</p>`);
        $(".popup-box").append(`<footer>
        <button class="popup-btn-alt js-close-popup">إغلاق</button>
      </footer>`);
        updateLocalPostAndUI(res.updated_post);
      } else {
        $(this).prepend(
          `<div class="my-alert ${msgClasess}">${res.message}</div>`
        );
      }
    },
  });
});
function updateLocalPostAndUI(updatedPost) {
  let postIdx = Posts.findIndex((post) => post.id === updatedPost.id);
  Posts[postIdx] = updatedPost;

  $(`[data-postID='${updatedPost.id}'] .type`).html(
    updatedPost.type.name.replace("إعلان", "")
  );
  $(`[data-postID='${updatedPost.id}'] main p`).html(updatedPost.caption);

  let media = "";
  if (updatedPost.media.length != 0) {
    updatedPost.media.map((image, idx) => {
      media += `<div class='box ${idx == 0 ? "" : "d-none"}'>
            <img src='${image}'/>
          </div>`;
    });
    if (updatedPost.media.length > 1) {
      media += `<div data-post-media class='more-media'>
      <div class='show'><span>+${
        updatedPost.media.length - 1
      }</span>عرض الكل</div>
      <div class='hide'>إغلاق</div>
    </div>`;
    }
  }
  $(`[data-postID='${updatedPost.id}'] main .media`).html(media);
}
// preview media on select images for the post
$(document).on("change", "#edit-post-form #media", function (e) {
  let mediaPreview = $("#edit-post-form .media-preview");
  mediaPreview.html("");
  for (let i = 0; i < this.files.length; i++) {
    mediaPreview.append(
      `<img src="${window.URL.createObjectURL(this.files[i])}" />`
    );
  }
});
// End edit post script
// on show post options
$(document).on("click", "[data-post-options]", function () {
  let isOpend = $(this).parent().hasClass("active");
  $("[data-post-options]").parent().removeClass("active");
  if (!isOpend) {
    $(this).parent().addClass("active");
  }
});
// show post comments
$(document).on("click", "[data-post-comments]", function () {
  $(this).parents("li").find(".comments").addClass("active");
});

// close post comments
$(document).on("click", "[data-close-comments]", function () {
  $(this).parents(".comments").removeClass("active");
});

// delete post
$(document).on("click", "[data-del-post]", function () {
  let postID = $(this).parents("li").attr("data-postID");
  console.log(postID);
  let postCapion = getPostElement(postID).caption;

  $("body").prepend(`<div class="popup-overlay allow-close">
  <div class="popup-box">
    <header><h2>حذف الإعلان</h2></header>
    <main>
      <p>هل أنت متأكد من حذف الإعلان؟</p>
      <p class="txt-c">"<em>${postCapion}</em>"</p>
    </main>
    <footer>
      <button onClick="deletePost(${postID})" class="popup-btn">حذف</button>
      <button class="popup-btn-alt js-close-popup">إلغاء</button>
    </footer>
  </div>
</div>`);
});

function deletePost(postID) {
  $.ajax({
    method: "POST",
    url: "./../../ajax_requests/restaurant-requests/posts-requests/deletePost.php",
    data: { postID },
    success: (res) => {
      console.log(res);
      res = JSON.parse(res);
      console.log(res);
      if (res.result) {
        $(".popup-box main").html(`<p>${res.message}</p>`);
        $(".popup-box footer")
          .html(`<button class="popup-btn-alt js-close-popup">إغلاق</button>
        `);
        deleteLocalPost(postID);
        if ($(".posts li").length == 0) {
          $("#posts-card .posts").remove();
          $("#posts-card").append(`<div class="no-posts">
            <div>
              <p>لم تقم بإضافة إعلانات بعد.</p>
              <a href="#add-post-card" class="dash-btn">إضافة إعلان</a>
            </div>
            <img src="./../../layout/images/people-wondering.jpg" alt="" />
          </div>`);
        }
      } else {
        $(".popup-box main").append(
          `<div class="my-alert error">${res.message}</div>`
        );
      }
    },
  });
}
function deleteLocalPost(postID) {
  $(`.posts li[data-postID='${postID}']`).remove();
  Posts = Posts.filter((post) => post.id != postID);
}

$(document).on("click", "[data-post-media]", function () {
  if ($(this).hasClass("active")) {
    $(this).siblings(":not(:first-child)").addClass("d-none");
  } else {
    $(this).siblings().removeClass("d-none");
  }
  $(this).toggleClass("active");
});

// on reply
$(document).on("click", "[data-reply]", function () {
  if ($(this).hasClass("active")) {
    $("#replyForm").slideUp(100);
    $(this).html("رد");
  } else {
    let commentID = $(this).attr("data-reply");
    let replyForm = $(`<form id="replyForm" class='reply-form'>
  <input type='text' name='reply' />
  <input type='hidden' name='commentID' value='${commentID}' />
  <button type='submit'>
    <i class='fas fa-paper-plane'></i>
  </button>
</form>`);
    replyForm.hide().insertAfter($(this).parent()).slideDown("fast");
    $(this).html("إلغاء");
  }
  $(this).toggleClass("active");
});

$(document).on("submit", "#replyForm", function (e) {
  e.preventDefault();
  if (this.reply.value.trim().length > 0) {
    $.ajax({
      method: "POST",
      url: "./../../ajax_requests/restaurant-requests/posts-requests/addReply.php",
      data: {
        commentID: this.commentID.value,
        reply: this.reply.value,
      },
      success: (res) => {
        res = JSON.parse(res);
        if (res.result) {
          let date = new Date(Date.now()).toLocaleString("ar-EG", {
            month: "long",
            day: "numeric",
            hour: "2-digit",
            minute: "2-digit",
          });
          let restName = $("[data-restName]").attr("data-restName");
          let reply = $(`<div class='reply'>
        <div class='text'>
          <h5>${restName}</h5>
          <p>${this.reply.value}</p>
        </div>
        <div class='date'>${date}</div>
      </div>`);
          $(this).parent().append(reply);
          $(this).parent().find("[data-reply]").html("");
          $(this).parent().find("[data-reply]").removeAttr("data-replay");
        }
        $(this).remove();
      },
    });
  } else {
    $(this).parent().find("[data-reply]").html("رد");
    $(this).parent().find("[data-reply]").removeClass("active");
    $(this).remove();
  }
});

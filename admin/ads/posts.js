$("#adType").selectBoxIt({
  theme: "jqueryui",
  defaultText: "اختر نوع الإعلان",
});
$(".js-posts-slider").slick({
  dots: true,
  slidesToScroll: 1,
});

// Start Search Script
let adType = -1;
let searchValue = "";
$(document).on("change", "#adType", function () {
  adType = parseInt(this.value);
  filterPosts(true);
});

$(document).on("submit", "#postSearch", function (e) {
  e.preventDefault();
  searchValue = this.postsSearch.value.trim();
  filterPosts();
  this.postsSearch.value = "";
});

function filterPosts(adTypeChaged = false) {
  let posts = $("#posts-list > li");

  if (searchValue.length == 0 && !adTypeChaged) {
    posts.fadeIn();
    return;
  }

  let searchArray = searchValue.split(/\s+/);

  let cnt = 0;
  posts.each(function (index, postItem) {
    let postType = $(this).data("type");
    let caption = $(this).find("[data-caption]").html();
    let restName = $(this).find("[data-restName]").html();

    let f1 = searchArray.some((el) => caption.includes(el));
    let f2 = searchArray.some((el) => restName.includes(el));
    let f3 = adType == -1 ? true : adType == postType;

    if ((f1 || f2) && f3) {
      $(this).fadeIn(0);
      cnt++;
    } else {
      $(this).fadeOut(0);
    }
  });

  if (!cnt) {
    if ($(".no-items").length == 0) {
      $(`<div class="no-items">
      <img src="./../layout/images/no-items-found.png" alt="" />
      <p>لا يوجد إعلانات مطابقة!..</p>
      </div>`).insertBefore($("#posts-list"));
    }
  } else {
    $(".no-items").remove();
  }
  searchValue = "";
}
// Start Post Script
$(document).on("click", "[data-reactID]", function () {
  if ($_USER_ID !== null) {
    let postID = $(this).parents("[data-postID]").data("postid");
    let parent = $(this).parents("button.reacts-btn");
    let reactsEl = $(`[data-postID="${postID}"] [data-reacts]`);

    if ($(this).hasClass("active")) {
      postRequest({ action: "REMOVE_REACT", postID }).then((res) => {
        if (res !== false) {
          parent
            .find(".btn-content")
            .html(`<i class="fas fa-thumbs-up fa-fw"></i>تفاعل`);
          parent.removeClass("active");
          updatePostReacts(reactsEl, res.reacts);
        }
      });
    } else {
      let src = $(this).attr("src");
      let reactID = $(this).data("reactid");
      postRequest({ action: "ADD_REACT", reactID, postID }).then((res) => {
        if (res !== false) {
          parent.find(".btn-content").html(`<img src="${src}" />`);
          parent.addClass("active");
          updatePostReacts(reactsEl, res.reacts);
        }
      });
    }
    $(this).siblings().removeClass("active");
    $(this).toggleClass("active");
  } else {
    $("body").prepend(getSigninModal(SINGIN_TO_REACT));
  }
});

function postRequest(data) {
  return $.ajax({
    method: "POST",
    url: $_ROOT_PATH + "ajax_requests/postActions.php",
    data: data,
  }).then((res) => {
    console.log(res);
    res = JSON.parse(res);
    console.log(res);

    if (res.result) {
      if (res.no_user) {
        $("body").prepend(getSigninModal(SINGIN_TO_RATE_MSG));
      } else {
        return res;
      }
    } else {
      showErrorModal(res.message);
    }

    return false;
  });
}
function updatePostReacts(el, reacts) {
  let reactsNum = reacts.reacts_number;

  let reactsContent = `<span><i class='d-none'>wtf</i></span>`;

  if (reactsNum > 0) {
    let Imgs = "";
    reacts.top_three.map((img) => {
      Imgs += `<img src='${img["image"]}' />`;
    });

    reactsContent = `${Imgs}<span>${reactsNum}</span>`;
  }

  el.html(reactsContent);
}
// show post comments
$(document).on("click", "[data-showComments]", function () {
  let comments = $(this).parents("li").find(".post-comments");
  if ($(this).hasClass("active")) {
    comments.slideUp();
  } else {
    comments.slideDown();
  }
  $(this).toggleClass("active");
});

// show comment form
$(document).on("click", "[data-comment]", function () {
  console.log("jo");
  if ($_USER_ID !== null) {
    let commentForm = $(this).parents("li").find(".comment-form");
    if ($(this).hasClass("active")) {
      commentForm.fadeOut(() => {
        $(this).parents("li").css("padding-bottom", 0);
      });
    } else {
      commentForm.fadeIn();
      $(this).parents("li").css("padding-bottom", 10);
    }
    $(this).toggleClass("active");
  } else {
    $("body").prepend(getSigninModal(SINGIN_TO_REACT));
  }
});
// on add comment submit
$(document).on("submit", ".comment-form", function (e) {
  e.preventDefault();
  let comment = this.comment.value.trim();
  let postID = this.postID.value;

  if (comment.length != 0) {
    postRequest({ action: "ADD_COMMENT", comment, postID }).then((res) => {
      if (res !== false) {
        addCommentToPost(postID, res.comment, res.commentsNum);
      }
    });
  }

  $(this).fadeOut(() => {
    $(this).parents("li").css("padding-bottom", 0);
    // clear form
    this.comment.value = "";
  });
  $(this).parents("li").find("[data-comment]").removeClass("active");
});

function addCommentToPost(postID, comment, commentsNum) {
  let commentsUl = $(`[data-postID="${postID}"] [data-commentsUl]`);

  commentsUl.prepend(
    $(`<li data-commentID = '${comment["id"]}'>
    <div class='comment'>
      <img src='${comment["image"]}' alt='' />
      <div>
        <h3>
          ${comment["name"]}
          <!-- <img src='reactImg' /> -->
        </h3>
        <p>${comment["comment"]}</p>
        <div class='actions'>
          <div class='btns'>
            <span data-delComment="${comment["id"]}">حذف</span>
          </div>
          <div class='date'>${comment["date"]}</div>
        </div>
      </div>
    </div>
  </li>`)
  );

  commentsUl.find("li[data-noComments]").remove();

  let commentsBtn = $(`[data-postID="${postID}"] [data-showComments]`);
  commentsBtn.html(`<span>${commentsNum}</span>
  <i class='fas fa-comment'></i>`);
  commentsBtn.removeClass("active").click();
}

$(document).on("click", "[data-delComment]", function () {
  let postID = $(this).parents("[data-postID]").data("postid");
  let commentID = $(this).data("delcomment");
  let modal = $(`<div class="popup-overlay allow-close">
    <div class="popup-box">
      <header>
        <h2>حذف تعليقك</h2>
      </header>
      <main>
        <p>هل أنت متأكد من حذف التعليق؟</p>
      </main>
      <footer>
        <button onClick=confirmDeleteCommnt(${commentID},${postID}) class="popup-btn">تأكيد</button>
        <button class="popup-btn-alt js-close-popup">إلغاء</button>
      </footer>
    </div>
  </div>`);

  $("body").prepend(modal);
});

function confirmDeleteCommnt(commentID, postID) {
  postRequest({
    action: "REMOVE_COMMENT",
    commentID,
    postID,
  }).then((res) => {
    if (res !== false) {
      $(".popup-box main").html(
        `<p class="success my-alert">${res.message}</p>`
      );
      $(".popup-box footer").html(
        `<button class="popup-btn-alt js-close-popup">إغلاق</button>`
      );

      let parentUl = $(`[data-commentID="${commentID}"]`).parent();
      console.log(parentUl);
      if (parentUl.find("li").length == 1) {
        parentUl.html(`<li data-noComments>لا يوجد تعليقات</li>`);
      } else {
        $(`[data-commentID="${commentID}"]`).fadeOut("fast", function () {
          $(this).remove();
        });
      }

      let commentsBtn = $(`[data-postID="${postID}"] [data-showComments]`);
      if (res.commentsNum == 0) {
        commentsBtn.html(`<span></span>`);
        commentsBtn.addClass("active").click();
      } else {
        commentsBtn.html(`<span>${res.commentsNum}</span>
        <i class='fas fa-comment'></i>`);
      }
    }
  });
}

// Start Constances
const SINGIN_TO_RATE_MSG = `سجلّ دخولك للمنصة لتتمكن من إضافة آراءك وتقييماتك للمطاعم والوجبات،
كما يمكنك طلب وجباتك المفضلة والحجز في المطاعم المميزة.`;
const SINGIN_TO_ORDER_MSG = `سجّل دخولك للمنصة لتتمكن من إضافة الوجبات للسلة وطلبها، كما يمكنك التعليق على المطاعم والوجبات وتقييمها عند تسجيل الدخول.`;
const SINGIN_TO_REACT = `سجّل دخولك للمنصة لتتمكن من التفاعل مع الإعلانات والتعليق عليها.`;
const SINGIN_TO_RESERVE = `سجّل دخولك للمنصة لتتمكن من الحجز في المطاعم.`;
// End Constances

// close Popup when background clicked
$(document).on("click", ".popup-overlay.allow-close", function (event) {
  if (event.target == this) {
    this.remove();
  }
});
// remove popup when an element with js-close-popup is clicked
$(document).on("click", ".js-close-popup", function () {
  $(".popup-overlay").remove();
});
//show password after click on eye icon
$(".eye").click(function () {
  $(this).toggleClass("fa-eye");
  $(this).toggleClass("fa-eye-slash");
  if ($(this).hasClass("fa-eye-slash")) {
    $(this).siblings().attr("type", "text");
    $(this).attr("title", "إخفاء كلمة المرور");
  } else {
    $(this).siblings().attr("type", "password");
    $(this).attr("title", "إظهار كلمة المرور");
  }
});
// Radio button
$(".radioBtn-js div").on("click", function () {
  var sel = $(this).data("value");
  var tog = $(this).data("radio");
  $("#" + tog).prop("value", sel);

  $('div[data-radio="' + tog + '"]')
    .not('[data-value="' + sel + '"]')
    .removeClass("active")
    .addClass("notActive");
  $('div[data-radio="' + tog + '"][data-value="' + sel + '"]')
    .removeClass("notActive")
    .addClass("active");
});

// function to allow numbers only on input
function onlyNums(e) {
  e.value = e.value.replace(/[^0-9]/g, "");
}
function emptyQTY(e) {
  if (e.value.length == 0 || e.value == 0) {
    e.value = 1;
  }
}
// Close my Alert
$(document).on("click", ".my-alert ", function (e) {
  this.remove();
});

// Counters Animation
let counters = document.querySelectorAll(".num-animation");
let interval = 1000;
counters.forEach((counter) => {
  let startValue = 0;
  let endValue = parseInt(counter.getAttribute("data-val"));
  let duration = Math.floor(interval / endValue);
  let timerHandler = setInterval(() => {
    startValue++;
    counter.textContent = startValue;
    if (startValue == endValue) {
      clearInterval(timerHandler);
    }
  }, duration);
});

/*
 ** Function to get formated time form string with pattern :
 ** HH:MM:SS => ص/م HH:MM
 */
function getHourMinutesFormat(hhmmssString) {
  let date = new Date();
  let [hour, minute, second] = hhmmssString.split(":");
  if (hhmmssString.length > 5) {
    date.setHours(hour, minute, second);
  } else {
    date.setHours(hour, minute);
  }
  return date.toLocaleTimeString("ar-EG", { timeStyle: "short" });
}
/*
 ** Time Ago Finction v0.0
 ** Function to get time as string descriping it
 ** according to it's value
 */
function timeAgo(date) {
  if (typeof date !== "object") {
    date = new Date(date);
  }
  let time_elapsed = Math.floor((new Date() - date) / 1000);
  let seconds = Math.round(time_elapsed);
  // console.log(seconds);
  let minutes = Math.round(time_elapsed / 60);
  let hours = Math.round(time_elapsed / 3600);
  let days = Math.round(time_elapsed / 86400);
  let weeks = Math.round(time_elapsed / 604800);
  let months = Math.round(time_elapsed / 2600640);
  let years = Math.round(time_elapsed / 31207680);
  // Seconds
  if (seconds <= 60) {
    return "الآن";
  }
  //Minutes
  else if (minutes <= 60) {
    if (minutes == 1) {
      return "منذ دقيقة";
    } else {
      return `منذ ${minutes} د`;
    }
  }
  //Hours
  else if (hours <= 24) {
    if (hours == 1) {
      return "منذ ساعة";
    } else {
      return `منذ ${hours} س`;
    }
  }
  //Days
  else if (days <= 7) {
    if (days == 1) {
      return "البارحة";
    } else {
      return `منذ ${days} ي`;
    }
  }
  //Weeks
  else if (weeks <= 4.3) {
    if (weeks == 1) {
      return "منذ أسبوع";
    } else {
      return `منذ ${weeks} أ`;
    }
  }
  //Months
  else if (months <= 12) {
    if (months == 1) {
      return "منذ شهر";
    } else {
      return `منذ ${months} ش`;
    }
  }
  //Years
  else {
    if (years == 1) {
      return "منذ سنة";
    } else {
      return `منذ ${years} سنة`;
    }
  }
}
// Start home nav bar script
$(document).on("click", "#menu-bar", function () {
  $(this).toggleClass("fa-bars");
  $(this).toggleClass("fa-times");
  $(this).siblings("ul").toggleClass("active");
});
// End home nav bar script
// Start signin modal
function getSigninModal(paragraph) {
  let modal = $(`<div class="popup-overlay allow-close"></div>`);
  let content = `
  <div id="signin-modal" class="popup-box">
        <div class="platform-logo-links">
          <a href="#" class="logo">ReStO</a>
          <div class="icons between-flex mt-20">
            <a class="fb" href="#"><i class="fab fa-facebook-f fa-fw"></i></a>
            <a class="gl" href="#"><i class="fab fa-google fa-fw"></i></a>
            <a class="tw" href="#"><i class="fab fa-twitter fa-fw"></i></a>
          </div>
        </div>
        <img src="${$_ROOT_PATH}/layout/images/food_delivery_preview.jpg" alt="" />
        <p>
          ${paragraph}
        </p>
        <a class="modal-btn" href="${$_ROOT_PATH}user_account/registerLogin.php?do=login">
          <span>تسجيل الدخول</span>
          </a>
        <small>لا تملك حساب؟ <a href="${$_ROOT_PATH}user_account/registerLogin.php?do=register">سجل حساب الآن<a></small>
      </div>`;
  modal.append(content);
  return modal;
}
// End signin modal
/*
 ** Start Cart Script
 **
 */
// Start Cart Modal
let Cart = [];

$(document).on("click", "#navCartBtn", function () {
  if ($_USER_ID !== null) {
    let modal = getCartModal();
    if (modal) {
      $("body").prepend(modal);
    }
  } else {
    $("body").prepend(getSigninModal(SINGIN_TO_ORDER_MSG));
  }
});
function getCartModal() {
  let modal = $(`<div class="popup-overlay allow-close">
    <div class="popup-box cart-modal">
      <a href="${$_ROOT_PATH}user_account/dashboard/userDashboard.php?tab=cart" class="cart-title">السلة<i class="fas fa-cart-plus"></i>
      </a>
    </div>
  </div>`);

  cartRequest({ action: "GET_ITEMS" }).then((res) => {
    if (res.result) {
      Cart = res.items;
      if (Cart.length != 0) {
        let cartUL = $(`<ul class="orders-by-rest"></ul>`);
        Object.entries(Cart).map(([restID, restData]) => {
          let cartLI = $(`<li class="order-g">
        <h2>
          وجبات من مطعم
          <a href='${
            $_ROOT_PATH + "restaurants/restProfile.php?id=" + restID
          }'><span>${restData.restaurantInfo.name}</span></a>
        </h2>`);
          cartLI.append(getRestCartItemsList(restID, restData.items));
          cartLI.append(`<div class="total">
        <a href="${$_ROOT_PATH}menus_recipes/order.php?restID=${restID}" class="order-btn">أطلب</a>
        <div>
          <span>الإجمالي:</span>
          <span class="total-price">${restData.total}</span><span class="unit">ل.س</span>
        </div>
      </div>`);
          cartUL.append(cartLI);
        });
        modal.find(".popup-box").append(cartUL);
      } else {
        modal
          .find(".popup-box")
          .append(`<p class="empty-cart">لا يوجد عناصر في السلة</p>`);
      }
    } else {
      showErrorModal(res.message);
      modal = false;
    }
  });
  return modal;
}
function getRestCartItemsList(restID, items) {
  let restItemsUL = $(`<ul class="cart-items"></ul>`);
  items.map((restItem) => {
    restItemsUL.append(`<li class="cart-item">
    <button onclick = "decrementCartItemQ.bind(this)(${restID},${restItem.id})">−</button>
    <button onclick = "incrementCartItemQ.bind(this)(${restID},${restItem.id})">+</button>
    <h3>${restItem.name}</h3>
    <span class="amount">x ${restItem.amount}</span>
    <span class="price">${restItem.price}<span class="unit">ل.س</span></span>
  </li>`);
  });
  return restItemsUL;
}
function incrementCartItemQ(restID, cartItemID) {
  cartRequest({ action: "INCREMENT", itemID: cartItemID }).then((res) => {
    if (res.result) {
      updateCartUI(1);
      let itemInfo = null;
      Cart[restID].items.map((item, idx, arr) => {
        if (item.id == cartItemID) {
          itemInfo = item;
          arr[idx].amount += 1;
        }
      });
      if (itemInfo) {
        Cart[restID].total += itemInfo.price;
        let LIparent = $(this).parents("li.cart-item");
        let grantParent = $(this).parents("li.order-g");
        LIparent.find(".amount").html(`x ${itemInfo.amount}`);
        grantParent.find(".total-price").html(Cart[restID].total);
      } else {
        console.log("error: can't find cart item");
      }
    }
  });
}
function decrementCartItemQ(restID, cartItemID) {
  cartRequest({ action: "DECREMENT", itemID: cartItemID }).then((res) => {
    if (res.result) {
      updateCartUI(-1);
      let itemInfo = null;
      Cart[restID].items.map((item, idx, arr) => {
        if (item.id == cartItemID) {
          itemInfo = item;
          arr[idx].amount -= 1;
        }
      });
      if (itemInfo) {
        let LIparent = $(this).parents("li.cart-item");
        let grantParent = $(this).parents("li.order-g");
        if (itemInfo.amount == 0 && Cart[restID].items.length == 1) {
          grantParent.fadeOut(function () {
            $(this).remove();
          });
          delete Cart[restID];
        } else {
          if (itemInfo.amount == 0) {
            LIparent.fadeOut(function () {
              $(this).remove();
            });
            Cart[restID].items = Cart[restID].items.filter(
              (item) => item.id != cartItemID
            );
          } else {
            LIparent.find(".amount").html(`x ${itemInfo.amount}`);
          }
          Cart[restID].total -= itemInfo.price;
          grantParent.find(".total-price").html(Cart[restID].total);
        }
      } else {
        console.log("error: can't find cart item");
      }
    }
  });
}
// End Cart Modal

// Start Cart Requests
function cartRequest(data) {
  return $.ajax({
    method: "POST",
    url: $_ROOT_PATH + "ajax_requests/Cart.php",
    data: data,
  }).then((res) => {
    console.log(res);
    res = JSON.parse(res);
    console.log(res);
    return res;
  });
}

// Start add to cart script
$(document).on("click", "[data-add-to-cart]", function (e) {
  if ($_USER_ID !== null) {
    let recipeID = $(this).parents("[data-recipeID]").attr("data-recipeID");
    let data = {
      action: "ADD",
      recipeID: recipeID,
      amount: 1,
    };
    cartRequest(data).then((res) => {
      if (res.result) {
        e.currentTarget.classList.add("plus");
        let timerHandler2 = setTimeout(function () {
          e.currentTarget.classList.remove("plus");
        }, 500);
        updateCartUI(1);
      }
    });
  } else {
    $("body").prepend(getSigninModal(SINGIN_TO_ORDER_MSG));
  }
});
// End add to cart script
// End Cart Requests

function updateCartUI(amount) {
  navCartBtn.classList.add("bump");
  let cuurentAmount = parseInt($(navCartBtn).find("span").html());
  $(navCartBtn)
    .find("span")
    .html(cuurentAmount + amount);
  let timerHandler = setTimeout(function () {
    navCartBtn.classList.remove("bump");
  }, 300);
}
/*
 ** End Cart Script
 **
 */
// rate recipe script
$(document).on("input", ".stars-widget input", function () {
  $(".rate-form .form-content").addClass("active");
});
$(document).on("click", "[data-rate-recipe]", function () {
  if ($_USER_ID !== null) {
    // NOTE: be aware that here we are getting recipe id from parent element not from "this"
    let recipeID = $(this).parents("[data-recipeID]").attr("data-recipeID");
    $.ajax({
      method: "POST",
      url: `${$_ROOT_PATH}ajax_requests/addReview.php`,
      data: { do: "ATTEMPT_RECIPE", ratedID: recipeID },
      success: (res) => {
        res = JSON.parse(res);
        if (res.result) {
          if (res.no_user) {
            $("body").prepend(getSigninModal(SINGIN_TO_RATE_MSG));
          } else {
            let edit = res.no_review ? false : res.old_review;
            $("body").prepend(getRateModal(recipeID, "RECIPE", edit));
          }
        } else {
          showErrorModal(res.message);
        }
      },
    });
  } else {
    $("body").prepend(getSigninModal(SINGIN_TO_RATE_MSG));
  }
});

// rate restaurant script
$(document).on("click", "[data-rate-rest]", function () {
  let modal = "";
  if ($_USER_ID !== null) {
    let restID = $(this).parents("[data-restID]").attr("data-restID");
    $.ajax({
      method: "POST",
      url: `${$_ROOT_PATH}ajax_requests/addReview.php`,
      data: { do: "ATTEMPT_REST", ratedID: restID },
      success: (res) => {
        res = JSON.parse(res);
        if (res.result) {
          if (res.no_user) {
            $("body").prepend(getSigninModal(SINGIN_TO_RATE_MSG));
          } else {
            let edit = res.no_review ? false : res.old_review;
            $("body").prepend(getRateModal(restID, "REST", edit));
          }
        } else {
          showErrorModal(res.message);
        }
      },
    });
  } else {
    modal = getSigninModal(SINGIN_TO_RATE_MSG);
  }
  $("body").prepend(modal);
});
function getRateModal(id, type, is_to_edit = false) {
  let modal = $(`<div class="popup-overlay allow-close"></div>`);

  let formID = "";
  if (type == "RECIPE") {
    formID = is_to_edit ? "meal_edit_rate_form" : "meal_rate_form";
  }
  if (type == "REST") {
    formID = is_to_edit ? "rest_edit_rate_form" : "rest_rate_form";
  }
  let content = `
  <div class="rate-modal popup-box">
    <h2>${is_to_edit ? "تعديل تقييم" : "قيّم"} ${
    type == "RECIPE" ? "الوجبة" : "المطعم"
  }</h2>
    <main>
      <form class="rate-form" id="${formID}">
        <div class="stars-widget">
          <input type="radio" name="rate" value="5" id="rate-5" />
          <label for="rate-5" class="fas fa-star"></label>
          <input type="radio" name="rate" value="4" id="rate-4" />
          <label for="rate-4" class="fas fa-star"></label>
          <input type="radio" name="rate" value="3" id="rate-3" />
          <label for="rate-3" class="fas fa-star"></label>
          <input type="radio" name="rate" value="2" id="rate-2" />
          <label for="rate-2" class="fas fa-star"></label>
          <input type="radio" name="rate" value="1" id="rate-1" />
          <label for="rate-1" class="fas fa-star"></label>
        </div>
        <div class="form-content  ${is_to_edit ? "active" : ""}">
          ${
            is_to_edit
              ? "<p>تعديل تقييمك</p>"
              : `<p>ماهو رأيك ${type == "RECIPE" ? "بالوجبة" : "بالمطعم"}؟</p>`
          }
          <div class="textarea">
            <textarea cols="30" name="review" placeholder="اكتب تقيمك (اختياري)">${
              is_to_edit ? is_to_edit.review : ""
            }</textarea>
          </div>
          <input type="text" name="ratedID" value="${
            is_to_edit ? is_to_edit.id : id
          }" hidden></input>
          <div class="actions">
            <button type="submit">${
              is_to_edit ? "عدّل" : "أضف"
            } التقييم</button>
          </div>
        </div>
      </form>
    </main>
  </div>`;
  modal.html(content);
  if (is_to_edit) {
    modal.find(`#rate-${is_to_edit.rate}`).attr("checked", true);
  }
  return modal;
}

$(document).on("submit", "#meal_rate_form", function (e) {
  e.preventDefault();
  let selectedRate = $(".rate-form input[name='rate']:checked");
  if (selectedRate.length != 0) {
    $.ajax({
      method: "POST",
      url: `${$_ROOT_PATH}ajax_requests/addReview.php`,
      data: {
        do: "RATE_RECIPE",
        ratedID: this.ratedID.value,
        rate: selectedRate.val(),
        review: this.review.value.trim(),
      },
      success: (res) => {
        res = JSON.parse(res);
        if (res.result) {
          // TODO: increse reviewsNum in recipe card, and add the review to reviews section
          showAfterRateContent();
        } else {
          showErrorModal(res.message);
        }
      },
    });
  } //else show message :)
});
$(document).on("submit", "#meal_edit_rate_form", function (e) {
  e.preventDefault();
  let selectedRate = $(".rate-form input[name='rate']:checked");
  if (selectedRate.length != 0) {
    $.ajax({
      method: "POST",
      url: `${$_ROOT_PATH}ajax_requests/addReview.php`,
      data: {
        do: "EDIT_RECIPE",
        ratedID: this.ratedID.value,
        rate: selectedRate.val(),
        review: this.review.value.trim(),
      },
      success: (res) => {
        res = JSON.parse(res);
        if (res.result) {
          // TODO: increse reviewsNum in recipe card, and add the review to reviews section
          showAfterRateContent();
        } else {
          showErrorModal(res.message);
        }
      },
    });
  } //else show message :)
});

$(document).on("submit", "#rest_rate_form", function (e) {
  e.preventDefault();
  let selectedRate = $(".rate-form input[name='rate']:checked");
  if (selectedRate.length != 0) {
    $.ajax({
      method: "POST",
      url: `${$_ROOT_PATH}ajax_requests/addReview.php`,
      data: {
        do: "RATE_REST",
        ratedID: this.ratedID.value,
        rate: selectedRate.val(),
        review: this.review.value.trim(),
      },
      success: (res) => {
        res = JSON.parse(res);
        if (res.result) {
          showAfterRateContent();
        } else {
          showErrorModal(res.message);
        }
      },
    });
  } //else show message :)
});
$(document).on("submit", "#rest_edit_rate_form", function (e) {
  e.preventDefault();
  let selectedRate = $(".rate-form input[name='rate']:checked");
  if (selectedRate.length != 0) {
    $.ajax({
      method: "POST",
      url: `${$_ROOT_PATH}ajax_requests/addReview.php`,
      data: {
        do: "EDIT_RESTAURANT",
        ratedID: this.ratedID.value,
        rate: selectedRate.val(),
        review: this.review.value.trim(),
      },
      success: (res) => {
        res = JSON.parse(res);
        if (res.result) {
          showAfterRateContent();
        } else {
          showErrorModal(res.message);
        }
      },
    });
  } //else show message :)
});

function showAfterRateContent() {
  $(".popup-box main").fadeOut(function () {
    $(this)
      .html(
        `
      <img src="${$_ROOT_PATH}layout/images/recipe-review.png" />
      <p class="txt-c">تم إرسال التقييم.<br>يسعدنا مشاركتك لنا برأيك.</p>
      <button class="mt-20 js-close-popup">إغلاق</button>
    `
      )
      .fadeIn();
  });
}
/*
 **  Errors Modal
 */
function showErrorModal(errorMessage) {
  $("body").prepend(`<div class='popup-overlay allow-close'>
    <div class='popup-box'>
      <p class="txt-c">${errorMessage}</p>
    </div>
  </div>`);
}

// handle reserve links
$(document).on("click", "[data-reserve-link]", function (e) {
  e.preventDefault();
  if ($_USER_ID === null) {
    let modal = getSigninModal(SINGIN_TO_RESERVE);
    if (modal) {
      $("body").prepend(modal);
    }
  } else {
    window.open($(this).attr("href"), "_blank");
  }
});

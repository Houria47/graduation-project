$(".js-slider").slick({
  dots: true,
  slidesToScroll: 1,
});

$(document).on("click", ".show-reviews-btn", function () {
  $(this).siblings("ul").toggleClass("active");
  if ($(this).hasClass("active")) {
    $(this).html("عرض الكل");
  } else {
    $(this).html("إخفاء");
  }
  $(this).toggleClass("active");
});

$(document).on("click", ".mob-head div", function () {
  $(this).parent().children(".active").removeClass("active");
  $(this).addClass("active");
});
$(document).on("click", "[data-recipes]", function () {
  $("#menus").css("display", "none");
  $("#posts").css("display", "none");
  $("#recipes").appendTo("#main-content").css("display", "block");
});
$(document).on("click", "[data-posts]", function () {
  $("#recipes").css("display", "none");
  $("#menus").css("display", "none");
  $("#posts").css("display", "block");
});
$(document).on("click", "[data-menus]", function () {
  $("#posts").css("display", "none");
  $("#recipes").css("display", "none");
  $("#menus").appendTo("#main-content").css("display", "block");
});

// show menu details
$(document).on("click", "[data-showMenuDetails]", function () {
  // get meals list
  let mealsList = $(this).parents("li").find(".menu-meals");
  if ($(this).hasClass("active")) {
    mealsList.slideUp(300);
    $(this).find("i").switchClass("fa-angle-up", "fa-angle-down");
  } else {
    removeOpendMenuLists();
    mealsList.slideDown(500);
    $(this).find("i").switchClass("fa-angle-down", "fa-angle-up");
  }
  $(this).toggleClass("active");
});

function removeOpendMenuLists() {
  $(".menu-meals").slideUp(300);
  $("[data-showMenuDetails] i.fa-angle-up").switchClass(
    "fa-angle-up",
    "fa-angle-down"
  );
  $("[data-showMenuDetails].active").removeClass("active");
}

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

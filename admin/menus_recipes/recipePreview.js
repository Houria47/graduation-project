$(document).on("click", "[data-show-menu-meals]", function () {
  $(this).parents("li").find(".menu-recipes").toggleClass("active");
  $(this).find("i").toggleClass("fa-angle-down");
  $(this).find("i").toggleClass("fa-angle-up");
  $(this).toggleClass("active");
});

$(document).on("click", "#add-to-cart-btn", function () {
  if ($_USER_ID !== null) {
    let recipeID = $("[data-recipeID]").attr("data-recipeID");
    let data = {
      action: "ADD",
      recipeID: recipeID,
      amount: 1,
    };
    cartRequest(data).then((res) => {
      if (res.result) {
        updateCartUI(1);
        $(this).html("تمت الإضافة");
        $(this).addClass("disabled");
      }
    });
  } else {
    $("body").prepend(getSigninModal(SINGIN_TO_ORDER_MSG));
  }
});

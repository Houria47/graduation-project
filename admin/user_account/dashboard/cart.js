let _Cart = null;

function getCartItemRecipe(id, restID) {
  let index = _Cart[restID].items.findIndex((item) => item.recipe.id == id);
  return _Cart[restID].items[index].recipe;
}

function getCartItem(id, restID) {
  let index = _Cart[restID].items.findIndex((item) => item.id == id);
  return _Cart[restID].items[index];
}
function setQTY(id, restID, qty) {
  _Cart[restID].items.map((el, idx, arr) => {
    if (el.id == id) {
      arr[idx].quantity = qty;
    }
  });
}
$(document).ready(function () {
  $.ajax({
    method: "POST",
    url: "./../../ajax_requests/Cart.php",
    data: { action: "GET_CART" },
    success: (res) => {
      res = JSON.parse(res);
      _Cart = res.items;
    },
  });
});

// on item quantity change
$(document).on("keyup , input", ".rest-cart table input", function (e) {
  let enterdValue = parseInt(e.target.value);
  if (enterdValue == 0 || e.target.value.length == 0) {
    enterdValue = 1;
  }

  // get  item & rest id
  let itemID = $(this).parents("[data-itemID]").attr("data-itemID");

  cartRequest({ action: "SET_QTY", itemID, amount: enterdValue }).then(
    (res) => {
      if (res.result) {
        updateTotalAndSubTotal.bind(this)(itemID, enterdValue);
      } else {
        showErrorModal(res.message);
        modal = false;
      }
    }
  );
});
function updateTotalAndSubTotal(itemID, enterdValue) {
  let restID = $(this).parents("[data-restID]").attr("data-restID");

  let item = getCartItem(itemID, restID);
  // get subtotal element
  let subTotalEl = $(this).parents("tr").find("[data-subTotal]");
  // calc old & new subtotal value
  let price = item.recipe.price;
  if (item.recipe.discount_price != null) {
    price = item.recipe.discount_price;
  }
  let oldSubTotal = price * item.quantity;
  // let subtotal = price;
  let subtotal = enterdValue * price;
  setQTY(itemID, restID, enterdValue);

  // get total and total with fee  elements
  let totalEl = $(this).parents(".rest-cart").find(".total-box [data-total]");
  let totalFeeEl = $(this)
    .parents(".rest-cart")
    .find(".total-box [data-total-fee]");

  // calc & change total
  let total = _Cart[restID].total - oldSubTotal + subtotal;
  _Cart[restID].total = total;
  // update total

  totalEl.html(total.toLocaleString("us"));
  let totalFee = total + _Cart[restID].delivary_fee;
  totalFeeEl.html(totalFee.toLocaleString("us"));
  subTotalEl.html(subtotal.toLocaleString("us"));
}
// preview recipe on table row click
$(document).on("click", "[data-recipe-preview]", function (e) {
  if ($(e.target).hasClass("img-box")) {
    return;
  }
  if (!$(this).hasClass("active")) {
    $("[data-recipe-preview].active").removeClass("active");
    let recipeID = $(this).attr("data-recipeID");
    let restID = $(this).parents("[data-restID]").attr("data-restID");
    let recipe = getCartItemRecipe(recipeID, parseInt(restID));
    let overViewEl = $("#overView");
    overViewEl.html(getPreviewContent(recipe, _Cart[restID].restaurant));
    $(this).addClass("active");
  }
});
function getPreviewContent(recipe, restaurant) {
  let overViewContent = "";
  // echo overview card content
  let currPrice = recipe["price"];
  let discount = "";
  if (recipe["discount_price"] != null) {
    currPrice = recipe["discount_price"];
    discount = `${recipe["price"].toLocaleString(
      "us"
    )}<span class='unit'>ل.س</span>`;
  }
  let addresses = restaurant["addresses"];
  let addContent = "";
  addresses.map((address) => {
    addContent += `<li>${address["state"]}, ${address["region"]}, ${address["street"]}</li>`;
  });
  let openTime = getHourMinutesFormat(restaurant["open_time"]);
  let closeTime = getHourMinutesFormat(restaurant["close_time"]);
  overViewContent = ` <div class="img-box">
    <img src="${recipe["image"]}" alt="" />
  </div>
  <h3 class="ovreview-title">${recipe["name"]}</h3>
  <div class="view-content">
    <div class="infoo">
      <div class="label">السعر:</div>
      <div class="flex-1 price">
        <span>${currPrice.toLocaleString(
          "us"
        )}<span class="unit">ل.س</span></span>
        <span class="discount">${discount}</span>
      </div>
    </div>
    <div class="infoo">
      <div class="label">الوصف:</div>
      <p class="flex-1">${recipe["description"]}</p>
    </div>
    <div class="infoo rest">
      <img src="${restaurant["logo"]}" alt="" />
      <div class="flex-1">
        <h3>${restaurant["name"]}</h3>
        <div>
          <span class="label">أوقات الدوام:</span>
          <span>من ${openTime} إلى ${closeTime}</span>
        </div>

        <div>
          <span class="label">التواصل:</span>
          <span>${restaurant["phone"]}</span>
        </div>
        <div>
          <span class="label"> العناوين:</span>
          <ul>
            ${addContent}
          </ul>
        </div>
      </div>
    </div>
  </div>`;
  return overViewContent;
}

// on delete item
$(document).on("click", "[data-del-item]", function () {
  let itemID = $(this).parents("[data-itemID]").attr("data-itemID");

  $.ajax({
    method: "POST",
    url: "./../../ajax_requests/Cart.php",
    data: { action: "REMOVE", itemID },
    success: (res) => {
      res = JSON.parse(res);
      if (res.result) {
        if ($(this).parents("table").find("tbody tr").length == 1) {
          $(this)
            .parents(".rest-cart")
            .fadeOut("fast", function () {
              $(this).remove();
              if ($(".rest-cart").length == 0) {
                $("main").html(`<div class="empty-cart">
                  <div>
                    <h3>السلة فارغة..!</h3>
                    <p>يمكنك استعراض الوجبات وإضافتها للسلة من خلال صفحة الوجبات</p>
                    <a href="./../../menus_recipes/recipes.php">عرض الوجبات</a>
                  </div>
                  <img src="./../../layout/images/empty-cart.jpg" alt="" />
                </div>`);
              }
            });
        } else {
          $(this)
            .parents("tr")
            .fadeOut("fast", function () {
              $(this).remove();
            });
          updateCartTotal.bind(this)();
          let restID = $(this).parents("[data-restID]").attr("data-restID");
          _Cart[restID].items = _Cart[restID].items.filter(
            (item) => item.id != itemID
          );
        }
      }
    },
  });
});
function updateCartTotal() {
  let itemID = $(this).parents("[data-itemID]").attr("data-itemID");
  let restID = $(this).parents("[data-restID]").attr("data-restID");
  let item = getCartItem(itemID, restID);

  // calc removed subtotal value
  let price = item.recipe.price;
  if (item.recipe.discount_price != null) {
    price = item.recipe.discount_price;
  }
  let subTotal = price * item.quantity;
  // get total and total with fee  elements
  let totalEl = $(this).parents(".rest-cart").find(".total-box [data-total]");
  let totalFeeEl = $(this)
    .parents(".rest-cart")
    .find(".total-box [data-total-fee]");

  // calc & change total
  let total = _Cart[restID].total - subTotal;
  _Cart[restID].total = total;
  // update total

  totalEl.html(total.toLocaleString("us"));
  let totalFee = total + _Cart[restID].delivary_fee;
  totalFeeEl.html(totalFee.toLocaleString("us"));
}

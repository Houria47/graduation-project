let SelectBoxItOptions = {
  // Uses the jQueryUI theme for the drop down
  theme: "jqueryui",
};
$("select").selectBoxIt(SelectBoxItOptions);
let minPrice_Filter = 10000,
  maxPrice_Filter = 40000;
// Start Multi range slider
let inputLeft = document.getElementById("input-left");
let inputRight = document.getElementById("input-right");
let range = document.querySelector(".slider > .range");
let priceFrom = document.querySelector(".price-from");
let priceTo = document.querySelector(".price-to");
let liftStep = parseInt(inputLeft.step);
let rightStep = parseInt(inputRight.step);

function setLeftValue() {
  let _this = inputLeft,
    min = parseInt(_this.min),
    max = parseInt(_this.max);

  _this.value = Math.min(
    parseInt(_this.value),
    parseInt(inputRight.value) - liftStep
  );
  priceFrom.textContent = `من :${parseInt(_this.value).toLocaleString("us")}`;

  let percent = ((_this.value - min) / (max - min)) * 100;

  range.style.right = percent + "%";
  minPrice_Filter = parseInt(_this.value);
}

setLeftValue();

function setRightValue() {
  let _this = inputRight,
    min = parseInt(_this.min),
    max = parseInt(_this.max);

  _this.value = Math.max(
    parseInt(_this.value),
    parseInt(inputLeft.value) + rightStep
  );
  priceTo.textContent = `إلى : ${parseInt(_this.value).toLocaleString("us")}`;

  let percent = ((_this.value - min) / (max - min)) * 100;

  range.style.left = 100 - percent + "%";
  maxPrice_Filter = parseInt(_this.value);
}

setRightValue();

inputLeft.addEventListener("input", setLeftValue);
inputRight.addEventListener("input", setRightValue);

inputLeft.addEventListener("mouseover", (e) => {
  inputLeft.classList.add("hover");
});
inputLeft.addEventListener("mouseout", (e) => {
  inputLeft.classList.remove("hover");
});
inputLeft.addEventListener("mousedown", (e) => {
  inputLeft.classList.add("active");
});
inputLeft.addEventListener("mouseup", (e) => {
  inputLeft.classList.remove("active");
});
inputLeft.addEventListener("touchstart", (e) => {
  inputLeft.classList.add("active");
});
inputLeft.addEventListener("touchend", (e) => {
  inputLeft.classList.remove("active");
});

inputRight.addEventListener("mouseover", (e) => {
  inputRight.classList.add("hover");
});
inputRight.addEventListener("mouseout", (e) => {
  inputRight.classList.remove("hover");
});
inputRight.addEventListener("mousedown", (e) => {
  inputRight.classList.add("active");
});
inputRight.addEventListener("mouseup", (e) => {
  inputRight.classList.remove("active");
});
inputRight.addEventListener("touchstart", (e) => {
  inputRight.classList.add("active");
});
inputRight.addEventListener("touchend", (e) => {
  inputRight.classList.remove("active");
});

/*
 **
 ** Start recipes list script
 **
 */
let Recipes = [];
// on recipes list load
$(document).ready(function () {
  // get data from Database
  $.ajax({
    method: "GET",
    url: "./../ajax_requests/getRecipes.php",
    data: {},
    success: (res) => {
      res = JSON.parse(res);
      Recipes = res.recipes;
    },
  });
});
// show\hide recipes reviews
$(document).on("click", "[data-show-reviews]", function () {
  $(this).parents("li").find(".reviews").addClass("active");
});
$(document).on("click", "[data-hide-reviews]", function () {
  $(this).parent().removeClass("active");
});
// Start filter script
// initial filter data
let rate_Filter = -1,
  state_Filter = -1,
  delivary_Filter = 0,
  reserv_Filter = 0,
  fastfood_Filter = 1,
  searchText_Filter = "";

minPrice_Filter = inputLeft.value;
maxPrice_Filter = inputRight.value;

// get filter data on user input
$(document).on("click", "#rate-filter span", function () {
  $(this).siblings(".selected").removeClass("selected");
  $(this).addClass("selected");
  rate_Filter = $(this).attr("data-val");
});
$(document).on("change", "#stateFilter", function () {
  state_Filter = this.value;
  console.log(this.value);
});
$(document).on("change", "#delivary", function () {
  fastfood_Filter = !this.checked;
});
$(document).on("change", "#reserv", function () {
  reserv_Filter = this.checked;
});
$(document).on("change", "#delivary", function () {
  delivary_Filter = this.checked;
});
$(document).on("change", "#searchRecipeInput", function () {
  searchText_Filter = this.value.trim();
});
// filter recipes on searchRecipeForm submit & searchRecipeBtn click
$(document).on("submit", "#searchRecipeForm", function (e) {
  e.preventDefault();
  searchText_Filter = this.searchText.value.trim();
  filterRecipes();
  document
    .getElementById("recipes-cards")
    .scrollIntoView({ behavior: "smooth" });
});
$(document).on("click", "#searchRecipeBtn", filterRecipes);
function filterRecipes() {
  $("#recipes-cards").html("");
  let noResults = true;
  let textArray = searchText_Filter.split(/\s+/);
  Recipes.map((recipe) => {
    let tags = recipe.tags.length === 0 ? [] : recipe.tags.split("،");

    if (searchText_Filter.length != 0) {
      if (
        !textArray.some((el) => recipe.name.includes(el)) &&
        !textArray.some((el) => tags.includes(el)) &&
        !tags.some((el) => el === searchText_Filter) &&
        !textArray.some((el) => recipe.description.includes(el))
      ) {
        return;
      }
    }
    let price = recipe.discount !== null ? recipe.discount : recipe.price;
    if (minPrice_Filter > price || price > maxPrice_Filter) {
      return;
    }
    if (reserv_Filter && !recipe.restaurant.reserv_service) {
      return;
    }
    if (delivary_Filter && !recipe.restaurant.delivery_service) {
      return;
    }
    if (!fastfood_Filter && recipe.restaurant.fast_food) {
      return;
    }
    if (state_Filter != -1) {
      let states = recipe.restaurant.states;
      let stateMatches = states.filter((state) => state.id == state_Filter);
      if (stateMatches.length == 0) {
        return;
      }
    }
    if (rate_Filter != -1) {
      if (Math.trunc(recipe.averageRate) != rate_Filter) {
        return;
      }
    }
    noResults = false;
    $("#recipes-cards").append(getRecipeCard(recipe));
  });

  if (noResults) {
    $("#recipes-cards").append(`<li class="no-items">
    <img src="./../layout/images/sad-search.png" alt="" />
    <p>لا يوجد وجبات مطابقة!</p>
  </li>`);
  }
}
function getRecipeCard(recipe) {
  let reviews = getRecipeReviewsSection(recipe.reviews);
  // recipe card
  let reviewsNum = recipe.reviews.length;
  let rate = recipe.ratesNum > 0 ? recipe["averageRate"] : "بلا تقييم";
  let prices = `<span>${recipe["price"].toLocaleString(
    "us"
  )} ل.س</span><span></span>`;
  if (recipe["discount"] != null) {
    prices = `<span>${recipe["discount"].toLocaleString("us")} ل.س</span>
              <span>${recipe["price"].toLocaleString("us")} ل.س</span>`;
  }

  let availableClass = "";
  let cartBtn = "<i class='fas fa-cart-plus'></i>";
  let cartBtnJSData = "data-add-to-cart";
  if (!recipe["availability"] || !recipe["restaurant"]["delivery_service"]) {
    availableClass = "cant-order";
    cartBtnJSData = "";
  }
  if (!recipe["restaurant"]["delivery_service"]) {
    cartBtn = "لا يوجد <br>توصيل";
  }
  if (!recipe["availability"]) {
    cartBtn = "غير متاح";
    availableClass += " not-available";
  }
  let card = `<li data-recipeID="${recipe["id"]}" class="${availableClass}">
      <div class="meal-head">
        <img class="meal-img" src="${recipe["image"]}" alt="" />
        <div class="rate"><i class="fas fa-star"></i>${rate}</div>
        <a href="../restaurants/restProfile.php?id=${recipe["restaurant"]["id"]}" class="rest">
          <img src="${recipe["restaurant"]["image"]}" alt="" />
          <span>${recipe["restaurant"]["name"]}</span>
        </a>
        <div class="rate right">
          <i class="fas fa-star"></i>عدد المقييمين: ${recipe["ratesNum"]}
        </div>
        <p class="description">
        ${recipe["description"]}
        </p>
      </div>
      <div class="content">
        <div class="name">
          <div>
            <h3>${recipe["name"]}</h3>
            <div class="price">
            ${prices}
            </div>
          </div>
          <button ${cartBtnJSData}>
            ${cartBtn}
          </button>
        </div>
      </div>
      <div class="actions">
        <div class="rates" data-show-reviews>
          <span>الآراء</span>
          <span class="num">${reviewsNum}</span>
        </div>
        <button data-rate-recipe>قيّم الوجبة</button>
      </div>
      <div class="reviews">
        <ul>
          ${reviews}
        </ul>
        <button data-hide-reviews>
          إخفاء <i class="fas fa-angles-down"></i>
        </button>
      </div>
    </li>`;
  return card;
}
function getRecipeReviewsSection(reviews) {
  // reviews section
  let reviewsItems = "";
  if (reviews.length > 0) {
    reviews.map((review) => {
      reviewsItems += `<li>
    <div class='rate'>${review.given_rate}<i class='fas fa-star'></i></div>
    <img src='${review.reviewer_image}' alt='' />
    <h4>${review.reviewer_name}</h4>
    <p class='review'>
    ${review.review}
    </p>
  </li>`;
    });
  } else {
    reviewsItems = "<li class='no-reviews fs-13 txt-c'>لا يوجد آراء</li>";
  }
  return reviewsItems;
}
// End filter script
/*
 **
 ** End recipes list script
 **
 */

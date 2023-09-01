let SelectBoxItOptions = {
  // Uses the jQueryUI theme for the drop down
  theme: "jqueryui",
};
$("select").selectBoxIt(SelectBoxItOptions);

let Meals = [];
let Menus = [];

function getMealElement(id) {
  let index = Meals.findIndex((meal) => meal.id == id);
  return Meals[index];
}
function UpdateMealElement(id, updatedMeal) {
  let mealIndex;
  Meals.map((meal, idx) => {
    if (meal.id == id) {
      mealIndex = idx;
    }
  });
  Meals[mealIndex] = updatedMeal;
  Meals[mealIndex].tags =
    Meals[mealIndex].tags.length === 0 ? [] : Meals[mealIndex].tags.split("،");
}
const tagsUl = document.querySelector(".tags-input ul"),
  tagsInput = document.querySelector(".tags-input input"),
  tagNumb = document.querySelector(".tags-input .tags-details span");

let maxTags = 10,
  tags = [];

// Start Tags Input Script
$(document).on("keyup", ".tags-content input", function (e) {
  if (e.key == "Enter") {
    let mealId = parseInt($(this).parent().attr("data-mealID"));
    let mealTagsParam = mealId == -1 ? tags : getMealElement(mealId).tags;
    let tagsContainerParam =
      mealId == -1
        ? $(tagsUl)
        : $(`li[data-mealId='${mealId}'] .tags-content ul`);
    addTag(mealTagsParam, e.target.value, tagsContainerParam);
    e.target.value = "";
    if (mealId == -1) {
      countTags();
    }
  }
});

function countTags() {
  tagsInput.focus();
  tagNumb.innerText = maxTags - tags.length;
}

function remove(element, tag) {
  let mealID = $(element).parent().parent().attr("data-mealID");
  let tagsArray = mealID == -1 ? tags : getMealElement(mealID).tags;
  filteredTags = tagsArray.filter((tagEl) => tagEl != tag);
  tagsArray.length = 0;
  tagsArray.push(...filteredTags);
  $(element).parent().remove();
  if (mealID == -1) countTags();
}

function addTag(tags, newTag, tagsContainer) {
  let tag = newTag.trim().replace(/\s+/g, " ");
  if (tag.length > 0 && tags.length < 10) {
    tag.split(/[,،]/).forEach((tag) => {
      let enteredtag = tag.trim();
      if (
        enteredtag.length !== 0 &&
        !tags.includes(enteredtag) &&
        tags.length < 10
      ) {
        tags.push(enteredtag);
        let liTag = `<li>${enteredtag} <i class="fas fa-multiply" onclick="remove(this, '${enteredtag}')"></i></li>`;
        tagsContainer.prepend(liTag);
      }
    });
  }
}

function clearTags() {
  tags.length = 0;
  tagsUl.querySelectorAll("li").forEach((li) => li.remove());
  countTags();
}
const tagsRemoveBtn = document.querySelector(".tags-details button");
tagsRemoveBtn.addEventListener("click", clearTags);
// End Tags Input Script

// Start add meal submit handler
$(document).on("submit", "#add-meal-form", function (e) {
  e.preventDefault();
  // remove old messages
  $(this).find(".my-alert").remove();
  //  validate input
  let validator = new ValidateIt();
  validator.isEmpty(this.name.value)
    ? $("#name-msg").html("حقل الاسم لا يجب أن يكون فارغ")
    : $("#name-msg").html("");
  validator.isEmpty(this.description.value)
    ? $("#desc-msg").html("حقل الوصف لا يجب أن يكون فارغ")
    : $("#desc-msg").html("");
  validator.isEmpty(this.menu.value)
    ? $("#menu-msg").html("يرجى تحديد قائمة")
    : $("#menu-msg").html("");
  this.mealImage.files.length === 0
    ? $("#image-msg").html("يرجى اختيار صورة")
    : $("#image-msg").html("");

  validator.price(
    this.price.value,
    this.discountPrice.value,
    $("#price-msg"),
    $("#discount-msg")
  );

  if (validator.isValid && this.mealImage.files.length !== 0) {
    let formData = new FormData(this);
    formData.append("tags", tags.join("،"));
    $.ajax({
      method: "POST",
      url: "./../../ajax_requests/restaurant-requests/addRecipe.php",
      data: formData,
      contentType: false,
      cache: false,
      processData: false,
      success: (res) => {
        console.log(res);
        res = JSON.parse(res);
        console.log(res);
        if (res.result) {
          clearRecipeForm(this);
          let recipeLi = createRecipeElement(res.inserted_recipe);
          if ($("#meals-list").length == 0) {
            // create meals list
            $("#meals-card").html(createMealsList());
          }
          $("#meals-list").prepend(recipeLi);
          if (res.inserted_recipe.tags.length === 0) {
            res.inserted_recipe.tags = [];
          } else {
            res.inserted_recipe.tags = res.inserted_recipe.tags.split("،");
          }
          Meals.push(res.inserted_recipe);
        }
        let msgClasses = res.result ? "success" : "error";
        $(this).prepend(
          `<div class='my-alert ${msgClasses}'>${res.message}</div>`
        );
        document
          .getElementById("add-recipe-card")
          .scrollIntoView({ behavior: "smooth" });
      },
    });
  } else {
    document
      .getElementById("add-meal-form")
      .scrollIntoView({ behavior: "smooth" });
  }
});
function clearRecipeForm(form) {
  form.name.value = "";
  form.description.value = "";
  form.price.value = "";
  form.discountPrice.value = "";
  form.mealImage.value = "";
  previewDefaulMealImage();
  let firstMenuOption = $(form).find("select option:first-child").attr("value");
  $(form).find("select").val(firstMenuOption).change();
  clearTags();
  $(form).find(".my-alert").remove();
}
$(document).on("keypress", "#add-meal-form, #edit-meal-form", function (e) {
  return e.keyCode != 13;
});
function createMealsList() {
  // menus options
  let menusOption = "";
  Menus.map((menu) => {
    menusOption += `<option value='${menu.id}'>${menu.name}</option>`;
  });

  let mealsListFilters = $(`<div class="meals-filters"></div>`);
  mealsListFilters.html(`
    <div class="menus-filter">
      <label for="menus-filter">القائمة</label>
      <select id="menus-filter">
      <option selected value="-1">الكل</option>
      ${menusOption}
      </select>
    </div>
    <div class="p-relative search">
      <input
        id="searchMeals"
        placeholder="ابحث عن وجبة"
        type="search"
      />
    </div>
  `);
  let mealsList = $(`<ul id="meals-list"></ul>`);
  mealsListFilters.find("select").selectBoxIt(SelectBoxItOptions);
  let wrapper = $("<div></div>");
  wrapper.append(mealsListFilters);
  wrapper.append(mealsList);
  return wrapper.children();
}
// End add meal submit handler
function createRecipeElement(recipe) {
  let recipeListItem = $(`<li class="meal" data-mealId="${recipe.id}"></li>`);
  let price = recipe.discount == null ? recipe.price : recipe.discount;
  let rate = recipe.ratesNum == 0 ? "بلا تقييم" : recipe.ratePercenatge;
  let content = `
  <div class="img-box">
    <img src="${recipe.image.path}" alt="" />
  </div>
  <div class="meal-container">
    <div class="meal-details">
      <div class="name">
        <i class="fas fa-bowl-food"></i>
        <span data-mealName>${recipe.name}</span>
      </div>
      <div class="price">
        <i class="fas fa-money-bill-alt"></i>
        <span data-MealPrice>${price}</span>
      </div>
      <div>
        <i class="fas fa-star"></i> <span>${rate}</span>
      </div>
      <div class="actions">
        <span
          data-showMealRates
          class="dash-btn dash-btn-orange c-white rad-half w-fit"
        >
          <i class="fas fa-comment-dots"></i>
        </span>
        <span
          data-editMeal
          class="dash-btn dash-btn c-white rad-half w-fit mr-5"
        >
          <i class="fas fa-edit"></i>
        </span>
        <span
          data-deleteMeal
          class="dash-btn dash-btn-red c-white rad-half w-fit mr-5"
        >
          <i class="fas fa-trash"></i>
        </span>
      </div>
    </div>
  </div>`;
  recipeListItem.append(content);
  return recipeListItem;
}
/*
 ** Function to change image source after file upload
 */
$(document).on("change", "#mealImgInput", function () {
  if (this.files.length !== 0) {
    $(".meal-img-box img").attr(
      "src",
      window.URL.createObjectURL(this.files[0])
    );
    $(".meal-img-box .overlay").css("display", "none");
  } else {
    previewDefaulMealImage();
  }
});
function previewDefaulMealImage() {
  $(".meal-img-box img").attr(
    "src",
    "./../../layout/images/Standard/recipe-items.jpg"
  );
  $(".meal-img-box .overlay").css("display", "flex");
}
// Start meals list script
$(document).ready(function () {
  // get data from Database
  if ($("#meals-card").length > 0) {
    // meals list has been loaded
    let restID = $("[data-restID]").attr("data-restID");
    // get restaurant menus list and recipes
    $.ajax({
      method: "GET",
      url: "./../../ajax_requests/getRestaurantRecipesAndMenus.php",
      data: { restID },
      success: (res) => {
        res = JSON.parse(res);
        if (res.result) {
          Meals = res.recipes;
          Meals.forEach((meal) => {
            meal.tags = meal.tags.length === 0 ? [] : meal.tags.split("،");
          });
          Menus = res.menus;
          console.log(Meals);
        }
      },
    });
  }
});
// End meals list script

// Start edit meal script
// show edit form
$(document).on("click", "#meals-list li [data-editMeal]", function () {
  let mealId = $(this).parents("li").attr("data-mealId");
  let formParent = $(`#meals-list li[data-mealId="${mealId}"] .meal-container`);
  let isOpened = $(this).hasClass("active");
  closeRatesPreview();
  closeEditForm();
  if (!isOpened) {
    let editMealForm = createEditMealForm(getMealElement(mealId));
    editMealForm
      .hide()
      .appendTo(formParent)
      .slideDown(() => {
        document
          .getElementById("edit-meal-form")
          .scrollIntoView({ behavior: "smooth" });
      });
    $(this).addClass("active");
  }
});
// create edit meal form
function createEditMealForm(meal) {
  let restID = $("[data-restID]").attr("data-restID");
  let editForm = $(
    `<form id="edit-meal-form" enctype="multipart/form-data"></form>`
  );
  let tagsList = "";
  meal.tags.map((tag) => {
    tagsList += `<li>
      ${tag}
      <i class="fas fa-multiply" onclick="remove(this, '${tag}')"></i>
    </li>`;
  });
  let menusOption = "";
  Menus.map((menu) => {
    let selected = meal.menu_id == menu.id ? "selected" : "";
    menusOption += `<option ${selected} value="${menu.id}">${menu.name}</option>`;
    // TODO: out of this function, check if there is no default menu add an option for the menusOption var
  });
  let formContent = `
  <h4>تعديل الوجبة</h4>
  <div class="input">
    <label for="">الاسم</label>
    <input type="text" name="name" value="${meal.name}"/>
  </div>
  <div class="input">
    <label for="">الوصف</label>
    <input type="text" name="description" value="${meal.description}"/>
  </div>
  <div class="input">
    <label for="">السعر</label>
    <input type="text" name="price" oninput="onlyNums(this);" value="${
      meal.price
    }"/>
  </div>
  <div class="input">
    <label for="">الحسم</label>
    <input type="text" name="discountPrice" oninput="onlyNums(this);" value="${
      meal.discount ? meal.discount : ""
    }"/>
  </div>
  <div class="input">
    <label for="">القائمة</label>
    <select name="menu">
    <option value="-1">وجبات أخرى</option>
    ${menusOption}
    </select>
  </div>
  <div class="input">
    <label>الصورة</label>
    <div class="img-input">
      <span>${meal.image.name}</span>
      <label
      for="edit-meal-img"
      class="dash-btn"
      >
        تحميل صورة
        <i class="fas fa-upload"></i>
        <input
          id="edit-meal-img"
          type="file"
          hidden
          name="mealImage"
        />
      </label>
    </div>
  </div>
  <div class="input">
    <label for="">الوسوم</label>
    <div class="tags-content">
      <ul data-mealID="${meal.id}">
      ${tagsList}
        <input
          type="text"
          spellcheck="false"
          placeholder="شاورما، برغر، عربي، غربي، حلويات، بوظة، شراب، حاد، حلو،..."
        />
      </ul>
    </div>
  </div>
  <div class="checkbox-input">
  <input
    id="availability"
    ${meal.availability ? "" : "checked"}
    name="is_not_available"
    type="checkbox"
  />
  <label for="availability"
    >الوجبة غير متاحة حالياً.
    <span
      >عند تحديد هذا الخيار ستبقى الوجبة معروضة للجميع لكن
      لن يتمكنوا من طلبها.</span
    ></label
  >
</div>
  <input type="hidden" name="recipeID" value="${meal.id}">
  <input type="hidden" name="restID" value="${restID}">
  <small></small>
  <div class="actions mt-10">
    <button class="dash-btn dash-btn-green">حفظ</button>
    <button type="button" data-cancel-meal-editing class="dash-btn dash-btn-red">إلغاء</button>
  </div>`;
  editForm.append(formContent);
  editForm.find("select").selectBoxIt(SelectBoxItOptions);
  return editForm;
}
// on cancel meal editing
$(document).on("click", "[data-cancel-meal-editing]", closeEditForm);
// on cancel edit form , or on open new edit form
function closeEditForm() {
  $("[data-editMeal].active").removeClass("active");
  $("#edit-meal-form").slideUp(function () {
    $(this).remove();
  });
}
// on edit meal image change
$(document).on("change", "#edit-meal-img", function () {
  let inputParent = $(this).parents(".img-input");
  if (this.files.length !== 0) {
    inputParent.find("span").html(this.files[0].name);
  }
});
// on edit meal submit
$(document).on("submit", "#edit-meal-form", function (e) {
  e.preventDefault();
  let validator = new ValidateIt();
  let smallEL = $(this).find("small");
  if (validator.isEmpty(this.name.value)) {
    smallEL.html("حقل الاسم لا يجب أن يكون فارغ");
    $(this.name).parents(".input").addClass("invalid");
    return;
  }
  if (validator.isEmpty(this.description.value)) {
    smallEL.html("حقل الوصف لا يجب أن يكون فارغ");
    $(this.description).parents(".input").addClass("invalid");
    return;
  }
  let priceIsValid = validator.price(
    this.price.value,
    this.discountPrice.value,
    smallEL,
    smallEL
  );
  if (!priceIsValid) {
    if (this.discountPrice.value.length != 0) {
      $(this.discountPrice).parents(".input").addClass("invalid");
    } else {
      $(this.price).parents(".input").addClass("invalid");
    }
    console.log($(this.discountPrice));
    console.log($(this.price));
    return;
  }
  if (validator.isEmpty(this.menu.value)) {
    smallEL.html("يرجى تحديد القائمة");
    $(this.menu).parents(".input").addClass("invalid");
    return;
  }
  let oldMeal = getMealElement(this.recipeID.value);
  let formData = new FormData(this);
  formData.append("tags", oldMeal.tags.join("،"));
  $(this).css({ opacity: 0.5, pointerEvents: "none" });
  $.ajax({
    method: "POST",
    url: "./../../ajax_requests/restaurant-requests/editRecipe.php",
    data: formData,
    contentType: false,
    cache: false,
    processData: false,
    success: (res) => {
      console.log(res);
      res = JSON.parse(res);
      console.log(res);
      if (res.result) {
        if (res.edited_recipe.image == false) {
          res.edited_recipe.image = oldMeal.image;
        }
        UpdateMealElement(this.recipeID.value, res.edited_recipe);
        updateMealInUI(res.edited_recipe);
      }
      // remove old message
      $(this).find(".my-alert").remove();
      let msgClasess = res.result ? "success-black" : "error-black";
      $(
        `<div class="my-alert ${msgClasess}">${res.message}</div>`
      ).insertBefore($(this).find(".actions"));
      $(this).css({ opacity: 1, pointerEvents: "all" });
    },
  });
});
// update UI on save edited meal info
function updateMealInUI(meal) {
  let mealItem = $(`li[data-mealId='${meal.id}']`);
  mealItem.find("[data-mealName]").html(meal.name);
  mealItem.find("[data-mealPrice]").html(meal.price);
  mealItem.find("img").attr("src", meal.image.path);
}

// on input focus, remove invalid class
$(document).on("focus", "#edit-meal-form .invalid input", function () {
  $(this).parents(".input.invalid").removeClass("invalid");
});
// End edit meal script

// Start delete meal script
$(document).on("click", "[data-deleteMeal]", function () {
  let mealId = $(this).parents("li").attr("data-mealId");
  let meal = getMealElement(mealId);
  console.log(meal);
  let modal = $(`
  <div class="popup-overlay allow-close">
    <div class="popup-box">
      <header>
        <h2>تأكيد الحذف - وجبة ${meal.name}</h2>
      </header>
      <main>
        <p>هل أنت متأكد من حذف الوجبة: ${meal.name}</p>
      </main>
      <footer>
        <button onClick = "deleteRecipe(${mealId})" class="popup-btn">تأكيد</button>
        <button class="popup-btn-alt js-close-popup">إلغاء</button>
      </footer>
    </div>
  </div>`);
  $("body").prepend(modal);
});
function deleteRecipe(recipeID) {
  $.ajax({
    method: "POST",
    url: "./../../ajax_requests/restaurant-requests/deleteRecipe.php",
    data: { recipeID },
    success: (res) => {
      console.log(res);
      res = JSON.parse(res);
      console.log(res);
      if (res.result) {
        $(".popup-box main").html(`<p>${res.message}</p>`);
        $(".popup-box footer").html(
          `<button class="popup-btn-alt js-close-popup">إغلاق</button>`
        );
        $(`li[data-mealId=${recipeID}]`).fadeOut(function () {
          $(this).remove();
          if ($("#meals-list li").length == 0) {
            $("#meals-card").html(`<div class="no-meals">
            <p>لا يوجد أية وجبات! <a href="#add-recipe-card">إضافة وجبة</a></p>
            <img src="./../../layout/images/chefs.jpg" alt="" />
          </div>`);
          }
        });
        Meals = Meals.filter((meal) => meal.id != recipeID);
      } else {
        $(".popup-box main").html(`<div my-alert error>${res.message}</div>`);
      }
    },
  });
}
// End delete meal script
// Start show rates and reviews script
$(document).on("click", "[data-showMealRates]", function () {
  let mealId = $(this).parents("li").attr("data-mealId");
  let meal = getMealElement(mealId);
  let isOpened = $(this).hasClass("active");
  closeEditForm();
  closeRatesPreview();
  if (!isOpened) {
    let previewParent = $(
      `#meals-list li[data-mealId="${mealId}"] .meal-container`
    );
    createRatesAndReviewSection(meal)
      .hide()
      .appendTo(previewParent)
      .slideDown(() => {
        document
          .getElementById("reviews-preview")
          .scrollIntoView({ behavior: "smooth" });
      });
    $(this).addClass("active");
  }
});
function closeRatesPreview() {
  $("[data-showMealRates].active").removeClass("active");
  $("#reviews-preview").slideUp(function () {
    $(this).remove();
  });
}
function createRatesAndReviewSection(recipe) {
  console.log(recipe.reviews);
  let reviews = `<li class="no-reviews">لا يوجد تعليقات</li>`;
  let ratesNum = recipe.ratesNum == 0 ? "لا يوجد تقييم" : recipe.ratesNum;
  let commentsNum = "لم يقم أحد بإبداء رأي عن الوجبة.";
  if (recipe.reviews.length > 0) {
    if (recipe.reviews.length >= 3) {
      commentsNum = `يوجد ${recipe.reviews.length} أشخاص أضافوا تعليقهم على الوجبة.`;
    }
    if (recipe.reviews.length == 2) {
      commentsNum = `يوجد ${recipe.reviews.length} شخص أضافوا تعليقهم على الوجبة.`;
    } else {
      commentsNum = `قام شخص واحد بإضافة تعليقه على الوجبة.`;
    }
  }
  console.log(recipe.ratesLevelsPercentage);
  if (recipe.reviews.length > 0) {
    reviews = "";
    recipe.reviews.map((review) => {
      reviews += `<li class="review">
        <img
          class="user-img"
          src="${review.reviewer_image}"
        />
        <div class="user-info">
          <h4>${review.reviewer_name}</h4>
          <p>${review.review}</p>
        </div>
        <div class="small-rate">
          <i class="fa fa-solid fa-star"></i>
          <span>${review.given_rate}</span>
        </div>
      </li>`;
    });
  }
  let reviewsPreview = $(`
  <div id="reviews-preview" class="reviews-preview">
    <div class="reviews-num">
      ${commentsNum}
    </div>
    <ul class="reviews">
    ${reviews}
    </ul>
    <div class="rate-content">
      <div class="rate-numbers">
        <div class="rate-percentage">${recipe.averageRate}</div>
        <div class="ratings">
          <div class="empty-stars"></div>
          <div class="full-stars" style="width: ${recipe.ratePercenatge}%"></div>
        </div>
        <div class="rated-users-num">${ratesNum}</div>
      </div>
      <div class="rates-chart">
        <div class="rate-bar">
          <span>1</span>
          <div class="rate-bar--inner">
            <div class="rate-bar--fill" style="width: ${recipe.ratesLevelsPercentage[0]}%"></div>
          </div>
        </div>
        <div class="rate-bar">
          <span>2</span>
          <div class="rate-bar--inner">
            <div class="rate-bar--fill" style="width: ${recipe.ratesLevelsPercentage[1]}%"></div>
          </div>
        </div>
        <div class="rate-bar">
          <span>3</span>
          <div class="rate-bar--inner">
            <div class="rate-bar--fill" style="width: ${recipe.ratesLevelsPercentage[2]}%"></div>
          </div>
        </div>
        <div class="rate-bar">
          <span>4</span>
          <div class="rate-bar--inner">
            <div class="rate-bar--fill" style="width: ${recipe.ratesLevelsPercentage[3]}%"></div>
          </div>
        </div>
        <div class="rate-bar">
          <span>5</span>
          <div class="rate-bar--inner">
            <div class="rate-bar--fill" style="width: ${recipe.ratesLevelsPercentage[4]}%"></div>
          </div>
        </div>
      </div>
    </div>
  </div>`);
  return reviewsPreview;
}
// End show rates and reviews script
// Start filter meal list
let searchWord = "";
let selectedMenu = -1;
$(document).on("input", "#searchMeals", function (e) {
  searchWord = e.target.value;
  filterMealsList();
});
$(document).on("change", "#menus-filter", function (e) {
  selectedMenu = e.target.value;
  filterMealsList();
});
function filterMealsList() {
  $("#meals-list").html("");
  let noItems = true;
  Meals.map((meal) => {
    let wordFlag =
      meal.name.includes(searchWord) || meal.description.includes(searchWord);
    let menuFlag = selectedMenu == -1 ? true : meal.menu_id == selectedMenu;
    if (wordFlag && menuFlag) {
      $("#meals-list").append(createRecipeElement(meal));
      noItems = false;
    }
  });
  if (noItems) {
    $("#meals-list").append(`
    <li class="no-items">
      <img src="./../../layout/images/upset-search.jpg" alt="" />
      <p>لا يوجد عناصر مطابقة!</p>
    </li>`);
  }
}
// End filter meal list

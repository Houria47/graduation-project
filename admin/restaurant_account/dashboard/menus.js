let Menus = [];
let Meals = [];

function getMenuElement(id) {
  let index = Menus.findIndex((meal) => meal.id == id);
  return Menus[index];
}
function getRecipeElement(id) {
  let index = Meals.findIndex((meal) => meal.id == id);
  return Meals[index];
}
function UpdateMenuElement(id, updatedMenu) {
  let mealIndex;
  Menus.map((meal, idx) => {
    if (meal.id == id) {
      mealIndex = idx;
    }
  });
  Menus[mealIndex] = updatedMenu;
}
function UpdateMealElement(id, updatedMeal) {
  let mealIndex;
  Meals.map((meal, idx) => {
    if (meal.id == id) {
      mealIndex = idx;
    }
  });
  Meals[mealIndex] = updatedMeal;
}
function updateMenuRecipes(menuID) {
  Menus.forEach((menu) => {
    if (menu.id == menuID) {
      menu.recipes = menu.recipes.filter((recipe) => recipe.menu_id == menuID);
    }
  });
}
// on menus card load
$(document).ready(function () {
  // get data from Database
  if ($("#menus-card").length > 0) {
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
        }
      },
    });
  }
});
$(document).on("click", "#file-input-label", removeInvalid);
$(document).on("focus", "#add-menu-form input", removeInvalid);
function removeInvalid() {
  $(this).parent().removeClass("invalid");
}
// on selected menu image change
$(document).on("change", "#menuImgInput", function () {
  if (this.files.length != 0) {
    $("#add-menu-form .menu-img-box img").attr(
      "src",
      window.URL.createObjectURL(this.files[0])
    );
    $("#add-menu-form .menu-img-box .overlay").css("display", "none");
  } else {
    previewDefaulMealImage();
  }
});

function previewDefaulMealImage() {
  $("#add-menu-form .menu-img-box img").attr(
    "src",
    "./../../layout/images/Standard/menu-items.jpg"
  );
  $("#add-menu-form  .menu-img-box .overlay").css("display", "flex");
}
// on add menu submit
$(document).on("submit", "#add-menu-form", function (e) {
  e.preventDefault();
  $(this).find("small").html("");
  let validator = new ValidateIt();
  if (validator.isEmpty(this.name.value)) {
    $(this).find("small").html("حقل الاسم لا يجب أن يكون فارغ.");
    $(this.name).parent().addClass("invalid");
    return;
  }
  // if (validator.isEmpty(this.description.value)) {
  //   $(this).find("small").html("حقل الوصف لا يجب أن يكون فارغ.");
  //   $(this.description).parent().addClass("invalid");
  //   return;
  // }
  if (this.menuImage.files.length == 0) {
    $(this).find("small").html("يرجى اختيار صورة");
    $(this.menuImage).parent().addClass("invalid");
    return;
  }

  let formData = new FormData(this);
  $.ajax({
    method: "POST",
    url: "./../../ajax_requests/restaurant-requests/addMenu.php",
    data: formData,
    contentType: false,
    cache: false,
    processData: false,
    success: (res) => {
      // console.log(res);
      res = JSON.parse(res);
      console.log(res);
      let msgClasses = "error";
      if (res.result) {
        msgClasses = "success";
        clearAddMenuForm(this);
        Menus.push(res.inserted_menu);
        addMenuToList(res.inserted_menu);
      }
      $(this)
        .find("small")
        .html(`<div class='my-alert ${msgClasses}'>${res.message}</div>`);
    },
  });
});
function clearAddMenuForm(form) {
  form.name.value = "";
  form.description.value = "";
  form.menuImage.value = "";
  previewDefaulMealImage();
  $(form).find("small").html("");
}
function addMenuToList(menu) {
  if ($("#menus-list").length == 0) {
    // create meals list
    $("#menus-card").html(createMenusList());
  }
  $("#menus-list").prepend(createMenuElement(menu));
}
function createMenusList() {
  let menusList = `<div class="p-relative search-menu">
    <input
      id="searchMenues"
      placeholder="ابحث عن قائمة"
      type="search"
      name = "searchMenu"
    />
  </div>
  <ul id="menus-list" class="menus"></ul>`;
  return menusList;
}

// Start delete menu script
$(document).on("click", "[data-del-menu]", function () {
  let menuId = $(this).parents("li").attr("data-menuID");
  console.log(menuId);
  let menu = getMenuElement(menuId);
  let modal = $(`
  <div class="popup-overlay allow-close">
    <div class="popup-box">
      <header>
        <h2>تأكيد الحذف - قائمة ${menu.name}</h2>
      </header>
      <main>
        <p>هل أنت متأكد من حذف القائمة: ${menu.name}</p>
      </main>
      <footer>
        <button onClick = "deleteMenu(${menuId})" class="popup-btn">تأكيد</button>
        <button class="popup-btn-alt js-close-popup">إلغاء</button>
      </footer>
    </div>
  </div>`);
  $("body").prepend(modal);
});
function deleteMenu(menuID) {
  let restID = $("[data-restID]").attr("data-restID");
  $.ajax({
    method: "POST",
    url: "./../../ajax_requests/restaurant-requests/deleteMenu.php",
    data: { menuID, restID },
    success: (res) => {
      console.log(res);
      res = JSON.parse(res);
      console.log(res);
      if (res.result) {
        $(".popup-box main").html(`<p>${res.message}</p>`);
        $(".popup-box footer").html(
          `<button class="popup-btn-alt js-close-popup">إغلاق</button>`
        );
        $(`li[data-menuID=${menuID}]`).fadeOut(function () {
          $(this).remove();
          if ($("#menus-list li").length == 0) {
            $("#menus-card").html(`<div class="no-meals">
            <p>لا يوجد أية قوائم! <a href="#add-recipe-card">إضافة قائمة</a></p>
            <img src="./../../layout/images/chef-with-menu.png.jpg" alt="" />
          </div>`);
          }
        });
        Menus = res.menus;
        // get default menu id
        let defaultMenuID;
        Menus.map((menu) => {
          if (menu.is_default) {
            defaultMenuID = menu.id;
          }
        });
        Meals.map((meal, idx, arr) => {
          if (meal.menu_id == menuID) {
            arr[idx].menu_id = defaultMenuID;
          }
        });
      } else {
        $(".popup-box main").html(`<div my-alert error>${res.message}</div>`);
      }
    },
  });
}
// End delete menu script

// Start edit menu script

// on attempt to edit menu show edit form
$(document).on("click", "#menus-list li [data-edit-menu]", function () {
  let menuId = $(this).parents("li").attr("data-menuID");
  let formParent = $(`#menus-list li[data-menuID="${menuId}"] main`);
  let isOpened = $(this).hasClass("active");
  removeOpenedEditForms();
  if (!isOpened) {
    let editMenuForm = createEditMenuForm(getMenuElement(menuId));
    editMenuForm.hide().appendTo(formParent).slideDown();
    $(this).addClass("active");
  }
});
// on cancel edit form , or on open new edit form
function removeOpenedEditForms() {
  $("[data-edit-menu].active").removeClass("active");
  $("#edit-menu-form").slideUp(function () {
    $(this).remove();
  });
}
// create edit meal form
function createEditMenuForm(menu) {
  let restID = $("[data-restID]").attr("data-restID");
  let editForm = $(
    `<form id="edit-menu-form" enctype="multipart/form-data"></form>`
  );
  let formContent = `
  <div class="input">
    <label for="name">الاسم</label>
    <input type="text" name="name" id="name" value="${menu.name}" />
  </div>
  <div class="input">
    <label for="description">الوصف</label>
    <input type="text" name="description" id="description" value="${menu.description}" />
  </div>
  <div class="input">
    <label for="desc">الصورة</label>
    <div class="img-input">
      <span>${menu.image.name}</span>
      <label for="edit-menu-img" class="dash-btn">
        تحميل صورة
        <i class="fas fa-upload"></i>
        <input
          id="edit-menu-img"
          type="file"
          hidden
          name="menuImage"
        />
      </label>
    </div>
  </div>
  <input type="hidden" name="menuID" value="${menu.id}">
  <input type="hidden" name="restID" value="${restID}">
  <small></small>
  <div class="actions mt-10">
    <button class="dash-btn dash-btn-green">حفظ</button>
    <button
      type="button"
      data-cancel-menu-editing
      class="dash-btn dash-btn-red"
    >
      إلغاء
    </button>
  </div>`;
  editForm.append(formContent);
  return editForm;
}
// on cancel meal editing
$(document).on("click", "[data-cancel-menu-editing]", removeOpenedEditForms);
// on edit meal image change
$(document).on("change", "#edit-meal-img", function () {
  let inputParent = $(this).parents(".img-input");
  if (this.files.length !== 0) {
    inputParent.find("span").html(this.files[0].name);
  }
});
// preview selected image on edit
$(document).on("change", "#edit-menu-img", function () {
  let menuID = $(this).parents("li").attr("data-menuID");
  let imgEl = $(this).parents("li").find("header img");
  if (this.files.length != 0) {
    imgEl.attr("src", window.URL.createObjectURL(this.files[0]));
  } else {
    imgEl.attr("src", getMenuElement(menuID).image.path);
  }
});
// on edit menu submit
$(document).on("submit", "#edit-menu-form", function (e) {
  e.preventDefault();
  let validator = new ValidateIt();
  let smallEL = $(this).find("small");
  // clear old message
  smallEL.html("");

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
  let oldMenu = getMenuElement(this.menuID.value);
  $(this).css({ opacity: 0.5, pointerEvents: "none" });
  $.ajax({
    method: "POST",
    url: "./../../ajax_requests/restaurant-requests/editMenu.php",
    data: new FormData(this),
    contentType: false,
    cache: false,
    processData: false,
    success: (res) => {
      res = JSON.parse(res);
      if (res.result) {
        if (res.edited_menu.image == false) {
          res.edited_menu.image = oldMenu.image;
        }
        UpdateMenuElement(this.menuID.value, res.edited_menu);
        updateMenuInUI(res.edited_menu);
      }
      // remove old message
      $(this).find(".my-alert").remove();
      let msgClasess = res.result ? "success-black" : "error-black";
      $(
        `<div class="my-alert ${msgClasess}">${res.message}</div>`
      ).insertBefore($(this).find(".actions"));
      $(this).css({ opacity: 1, pointerEvents: "all" });
      $(this).find(".actions")
        .html(`<button type="button" data-cancel-menu-editing="" class="dash-btn dash-btn-red">
      إغلاق
    </button>`);
    },
  });
});
// update UI on save edited meal info
function updateMenuInUI(menu) {
  let menuItem = $(`li[data-menuID='${menu.id}']`);
  menuItem.find("header img").attr("src", menu.image.path);
  menuItem.find("main h3").html(menu.name);
  menuItem.find("main p").html(menu.description);
}
// on input focus, remove invalid class
$(document).on("focus", "#edit-menu-form .invalid input", function () {
  $(this).parents(".input.invalid").removeClass("invalid");
});
// end edit menu

// Start menu Recipes script
$(document).on("click", "[data-show-meals]", function () {
  let menuID = $(this).parents("li").attr("data-menuID");
  let isOpened = $(this).hasClass("active");
  removeOpendRecipesLists();
  if (!isOpened) {
    let menuRecipes = Meals.filter((meal) => meal.menu_id == menuID);
    let list = createMenuRecipesList(menuRecipes);
    list.hide().appendTo($(this).parents("li")).slideDown();
    $(this).addClass("active");
  }
});
function removeOpendRecipesLists() {
  $("[data-show-meals].active").removeClass("active");
  $("#menu-meals").slideUp(function () {
    $(this).remove();
  });
}
function createMenuRecipesList(recipes) {
  let recipesList = $(`<ul id="menu-meals" class="menu-meals"></ul>`);
  let content = ``;
  if (recipes.length == 0) {
    content = `<li class="no-recipes">لا يوجد وجبات مضافة للقائمة</li>
    <div class="action"><div data-add-to-menu class="dash-btn dash-btn-green">أضف وجبات للقائمة</div></div>`;
  } else {
    recipes.map((recipe) => {
      content += `<li>
      <div class="first-col">
        <img src="${recipe.image.path}" alt="" />
        <div class="details">
          <h4>${recipe.name}</h4>
          <p>${recipe.description}</p>
        </div>
      </div>
      <span data-removeRecipeFromMenu = "${recipe.id}">إزالة</span>
    </li>`;
    });
    content += `<div class="action"><div data-remove-all-recipes class="dash-btn dash-btn-orange">إزالة جميع الوجبات</div></div>`;
  }
  recipesList.append(content);
  return recipesList;
}
// End menu Recipes script
// Start add and clear meals from menu
$(document).on("click", "[data-remove-all-recipes]", function () {
  let parentLI = $(this).parents("li");
  let menuID = parentLI.attr("data-menuID");
  let restID = $("[data-restID]").attr("data-restID");
  $.ajax({
    method: "POST",
    url: "./../../ajax_requests/restaurant-requests/clearMenu.php",
    data: { restID, menuID },
    success: (res) => {
      console.log(res);
      res = JSON.parse(res);
      console.log(res);
      if (res.result) {
        $("#menu-meals").html(
          `<li class="no-recipes">لا يوجد وجبات مضافة للقائمة</li>
          <div class="action"><div data-add-to-menu class="dash-btn dash-btn-green">أضف وجبات للقائمة</div></div>`
        );
        // edit local meals
        Meals.forEach((meal) => {
          if (meal.menu_id == menuID) {
            meal.menu_id = res.default_menu;
          }
        });
        let recipesNumEL = parentLI.find(".recipes-num");
        recipesNumEL.removeClass("num-low");
        recipesNumEL.removeClass("num-high");
        recipesNumEL.find("span").html("0");
      }
    },
  });
});
let MealsToAddToMenu = [];
// on add recipes to menu
$(document).on("click", "[data-add-to-menu]", function () {
  // close menu meals list
  removeOpendRecipesLists();
  let menuID = $(this).parents("li").attr("data-menuID");
  MealsToAddToMenu = [];
  $("body").append(createAddRecipesModal(menuID));
});
function createAddRecipesModal(menuID) {
  // get default menu id
  let defaultMenuID;
  Menus.map((menu) => {
    if (menu.is_default) {
      defaultMenuID = menu.id;
    }
  });
  // get default menu meals
  let availableMeals = Meals.filter((meal) => meal.menu_id == defaultMenuID);

  let mealsOptions = `<p>لا يوجد وجبات لإضافتها، جميع الوجبات مضافة لقوائم أخرى.</p>`;
  let modalActions = `<a href="?tab=meals#add-recipe-card" class="popup-btn">إضافة وجبة جديدة</a>
  <button class="popup-btn-alt js-close-popup">إلغاء</button>`;
  if (Meals.length == 0) {
    modalActions = `<a href="?tab=meals#add-recipe-card" class="popup-btn">إضافة وجبة</a>
    <button class="popup-btn-alt js-close-popup">إلغاء</button>`;
    mealsOptions = `<p>لا يوجد وجبات لإضافتها، لم تقم بإضافة وجبات لمطعمك.</p>`;
  }
  if (availableMeals.length != 0) {
    mealsOptions = `<ul id="availabe-meals" class="availabe-meals">`;
    availableMeals.map((meal) => {
      mealsOptions += `<li data-id="${meal.id}">
      <img src="${meal.image.path}" alt="" />
      <h3>${meal.name}</h3>
    </li>`;
    });
    mealsOptions += `</ul>`;
    modalActions = `<button data-submit-btn onClick="addRecipesToMenu(${menuID})" class="popup-btn disabled">إضافة</button>
    <button class="popup-btn-alt js-close-popup">إلغاء</button>`;
  }
  let menu = getMenuElement(menuID);
  let modal = `<div class="popup-overlay allow-close">
    <div class="popup-box">
      <header>
        <h2>إضافة وجبات لقائمة - ${menu.name}</h2>
      </header>
      <main>
        ${mealsOptions}
      </main>
      <footer>
        ${modalActions}
      </footer>
    </div>
  </div>`;
  return modal;
}
// on select meal to add to menu
$(document).on("click", "ul#availabe-meals li", function () {
  let recipeID = $(this).attr("data-id");
  if ($(this).hasClass("selected")) {
    MealsToAddToMenu = MealsToAddToMenu.filter((mealid) => mealid != recipeID);
    $(this).removeClass("selected");
    if (MealsToAddToMenu.length == 0) {
      $(".popup-overlay footer [data-submit-btn]").addClass("disabled");
    }
  } else {
    MealsToAddToMenu.push(recipeID);
    $(this).addClass("selected");
    $(".popup-overlay footer [data-submit-btn]").removeClass("disabled");
  }
  console.log(MealsToAddToMenu);
});
function addRecipesToMenu(menuID) {
  if (MealsToAddToMenu.length != 0) {
    $.ajax({
      method: "POST",
      url: "./../../ajax_requests/restaurant-requests/addRecipesToMenu.php",
      data: { recipes: MealsToAddToMenu, menuID },
      success: (res) => {
        console.log(res);
        res = JSON.parse(res);
        console.log(res);
        if (res.result) {
          $(".popup-overlay footer").html(
            `<button class="popup-btn-alt js-close-popup">إغلاق</button>`
          );
          $(".popup-overlay main").html(`<p>${res.message}</p>`);
          // update local meals
          Meals.forEach((meal, idx, arr) => {
            if (MealsToAddToMenu.includes(`${meal.id}`)) {
              arr[idx] = { ...meal, menu_id: menuID };
            }
          });
          console.log(Meals);
          let recipesNumEl = $(
            `#menus-list li[data-menuID="${menuID}"] .recipes-num`
          );
          let finalNum =
            parseInt(recipesNumEl.find("span").html()) +
            MealsToAddToMenu.length;
          if (finalNum > 2) {
            recipesNumEl.removeClass("num-low");
            recipesNumEl.addClass("num-high");
          }
          if (finalNum > 0 && finalNum < 3) {
            recipesNumEl.removeClass("num-high");
            recipesNumEl.addClass("num-low");
          }
          recipesNumEl.find("span").html(finalNum);
        } else {
          $(".popup-overlay main").prepend(
            `<div class="my-alert error">${res.message}</div>`
          );
        }
      },
    });
  }
}
// End add and clear meals from menu
// Start filter Menus list
$(document).on("input", "#searchMenues", function (e) {
  let enteredValue = e.target.value;
  let noItems = true;
  $("#menus-list").html("");
  Menus.map((menu) => {
    if (menu.is_default) {
      return;
    }
    if (
      menu.name.includes(enteredValue) ||
      menu.description.includes(enteredValue)
    ) {
      noItems = false;
      $("#menus-list").append(createMenuElement(menu));
    }
  });
  if (noItems) {
    $("#menus-list").append(`<li class="no-items">
    <img src="./../../layout/images/upset-search.jpg" alt="" />
    <p>لا يوجد عناصر مطابقة!</p>
  </li>`);
  }
});
function createMenuElement(menu) {
  let menuItem = $(`<li class="menu" data-menuID = ${menu.id}></li>`);
  let recipesNum = Meals.filter((meal) => meal.menu_id == menu.id).length;
  let recipesNumUnit = recipesNum >= 3 ? "وجبات" : "وجبة";
  let recipesNumClass = "";
  if (recipesNum > 0 && recipesNum <= 2) {
    recipesNumClass = "num-low";
  }
  if (recipesNum > 2) {
    recipesNumClass = "num-high";
  }
  let content = `<header>
      <div class="img-box">
        <img src="${menu.image.path}" alt="" />
      </div>
      <div class="recipes-num ${recipesNumClass}">${recipesNum}<br />${recipesNumUnit}</div>
    </header>
    <main>
      <h3>${menu.name}</h3>
      <p>${menu.description}</p>
    </main>
    <footer class="actions">
      <span class="dash-btn" data-show-meals>الوجبات</span>
      <span class="dash-btn" data-add-to-menu>
        <i class="fas fa-add"></i>
      </span>
      <span class="dash-btn dash-btn-green" data-edit-menu>
        <i class="fas fa-edit"></i>
      </span>
      <span class="dash-btn dash-btn-red" data-del-menu>
        <i class="fas fa-trash"></i>
      </span>
    </footer>
  </li>`;
  menuItem.append(content);
  return menuItem;
}
// End filter Menus list
// on remove recipe from menu
$(document).on("click", "[data-removeRecipeFromMenu]", function () {
  let restID = $("[data-restID]").attr("data-restID");
  let recipeID = $(this).attr("data-removeRecipeFromMenu");
  $.ajax({
    method: "POST",
    url: "./../../ajax_requests/restaurant-requests/removeRecipeFromMenu.php",
    data: { recipeID, restID },
    success: (res) => {
      res = JSON.parse(res);
      if (res.result) {
        $(this)
          .parent()
          .slideUp("fast", function () {
            $(this).remove();
            if ($("#menu-meals li").length == 0) {
              $("#menu-meals").html(
                `<li class="no-recipes">لا يوجد وجبات مضافة للقائمة</li>
                <div class="action"><div data-add-to-menu class="dash-btn dash-btn-green">أضف وجبات للقائمة</div></div>`
              );
            }
          });
        let oldRecipe = getRecipeElement(recipeID);
        UpdateMealElement(recipeID, {
          ...oldRecipe,
          menu_id: res.default_menu,
        });
        updateMenuRecipes(oldRecipe.menu_id);
        console.log(Meals);
        let recipesNumEL = $(this)
          .parents("[data-menuID]")
          .find(".recipes-num");
        let recipesNum = parseInt(recipesNumEL.find("span").html());
        if (recipesNum == 1) {
          recipesNumEL.removeClass("num-low");
        }
        if (recipesNum == 3) {
          recipesNumEL.switchClass("num-high", "num-low");
        }
        recipesNumEL.find("span").html(recipesNum - 1);
      } else {
        // TODO: idk what to do here
      }
    },
  });
});

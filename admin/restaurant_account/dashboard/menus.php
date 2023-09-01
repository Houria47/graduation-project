<?php
// get menus option from db
$menus = getRestaurantMenus($restID);

// get recipes from db
$recipes = getRestaurantRecipe($restID);
     
?>
<h2 class="tab-title">
  <span>القوائم</span>
</h2>
<main class="menus-content" data-restID="<?=$restID?>">
  <div id="add-menu-card" class="my-card mb-20">
    <form id="add-menu-form" enctype="multipart/form-data">
      <div class="component-title">إضافة قائمة</div>
      <div class="image-col">
        <div class="menu-img-box center-flex">
          <img src="./../../layout/images/Standard/menu-items.jpg" alt="" />
          <label for="menuImgInput" class="overlay center-flex">
            <i class="fas fa-upload"></i>
          </label>
        </div>
        <label id="file-input-label" class="dash-btn" for="menuImgInput">
          تحميل صورة
        </label>
        <input id="menuImgInput" type="file" hidden name="menuImage" accept="image/*" />
      </div>
      <div>
        <div class="form-input">
          <label>اسم القائمة</label>
          <input name="name" type="text" />
        </div>
        <div class="form-input">
          <label>وصف القائمة</label>
          <input name="description" type="text" />
        </div>
      </div>
      <div class="action between-flex gap-10">
        <small></small>
        <button class="dash-btn dash-btn-green">أضف القائمة</button>
      </div>
      <input name="restID" type="hidden" value="<?=$restID?>" />
    </form>
  </div>
  <div id="menus-card" class="my-card">
    <div class="component-title">القوائم المضافة</div>
    <?php
      $isJustOneDefaultMenu = count($menus) == 1 && $menus[0]['is_default'];
      if(count($menus) == 0 || $isJustOneDefaultMenu){
        echo <<< "no_menus"
        <div class="no-menus">
          <img src="./../../layout/images/chef-with-menu.png" alt="" />
          <p>لا يوجد أية قوائم! <a href="#add-menu-card">إضافة قائمة</a></p>
        </div>
        no_menus;
      }else{
        echo <<< "menus_header"
        <div class="p-relative search-menu">
          <input
            id="searchMenues"
            placeholder="ابحث عن قائمة"
            type="search"
            name = "searchMenu"
          />
        </div>
        <ul id="menus-list" class="menus">
        menus_header;
        foreach($menus as $menu){
          if($menu['is_default'])
            continue;
          $recipesNum = count($menu['recipes']);
          $recipesNumUnit = $recipesNum >= 3? "وجبات":"وجبة" ;
          $recipesNumClass = '';
          if($recipesNum > 0 && $recipesNum <= 2){
            $recipesNumClass = "num-low";
          }
          if( $recipesNum > 2){
            $recipesNumClass = "num-high";
          }

          echo <<< "menu_item"
          <li class="menu" data-menuID = {$menu['id']}>
            <header>
              <div class="img-box">
                <img src="{$menu['image']['path']}" alt="" />
              </div>
              <div class="recipes-num $recipesNumClass"><span>$recipesNum</span><br />$recipesNumUnit</div>
            </header>
            <main>
              <h3>{$menu['name']}</h3>
              <p>{$menu['description']}</p>
            </main>
            <footer class="actions">
              <span class="dash-btn dash-btn" data-show-meals>الوجبات</span>
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
          </li>
          menu_item;
        }
        echo "</ul>";
      }
    ?>
  </div>
</main>
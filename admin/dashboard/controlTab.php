<?php
$usersNum = getUserVerifiedAccountsNum();
$restsNum = calcRestaurantsSatistics(getRestaurantsInfo())['authenticated'];

$accountsNum = $usersNum + $restsNum;
$restsPrecentage = +bcdiv($restsNum / $accountsNum * 100,1,1);
$usersPrecentage = 100 - $restsPrecentage;
echo $usersPrecentage;

$adtypes = getAdTypes();
$reacts = getReactsList()

?>
<main>
  <h2 class="tab-title">لوحة التحكم</h2>
  <div class="content-control tab">
    <section class="statics-section">
      <div class="ele pie-card">
        <!-- <figure class="pie-chart" style="--p1: <?=$usersPrecentage?>%; --p2: <?=$restsPrecentage?>%"></figure> -->
        <div class="caption">
          <h3>الحسابات المسجلة في المنصة</h3>
          <div class="nums">
            <div class="rests">
              <div class="center-flex"><i class="fas fa-store"></i> <span
                  data-percentage="<?=$restsPrecentage?>%"></span></div>
              <div class="num"><?=$restsNum?></div>
            </div>
            <div class="users">
              <div class="center-flex"><i class="fas fa-user"></i> <span
                  data-percentage="<?=$usersPrecentage?>%"></span></div>
              <div class="num"><?=$usersNum?></div>
            </div>
          </div>
        </div>
      </div>
      <div class="my-card ele">
        <canvas id="accountsChart"></canvas>
      </div>
    </section>
    <section class="settings my-card">
      <div class="component-title">إعدادت المنصة</div>
      <!-- <div class="set">
        <h3 class="set-title">اسم المنصة</h3>
        <div class="between-flex">
          <input id="platformNameInput" name="platformName" value="Resto" />
          <span class="actions name">
            <i data-editName class="fas fa-edit"></i>
          </span>
        </div>
        <small id="nameMsg"></small>
      </div> -->

      <div class="set">
        <h3 class="set-title">حساب المدير</h3>
        <div class="two-set">
          <div class="set">
            <h3 class="set-title">الإيميل</h3>
            <div class="between-flex">
              <input id="emailInput" name="email" value="<?=$_SESSION['admin']['email']?>" />
              <span class="actions email">
                <i data-editEmail class="fas fa-edit"></i>
              </span>
            </div>
            <small id="emailMsg"></small>
          </div>
          <div class="set">
            <h3 class="set-title">كلمة المرور</h3>
            <div class="between-flex">
              <input type="password" id="passInput" name="pass" value="Password" />
              <i data-editPass class="fas fa-edit"></i>
            </div>
          </div>
        </div>
      </div>

      <div class="set">
        <h3 class="set-title">المطاعم</h3>

        <div class="two-set">
          <div class="set">
            <h3 class="set-title">أنواع الإعلانات لحسابات المطاعم</h3>
            <ul class="data" id="adTypesList">
              <?php
                foreach($adtypes as $type){
                  $isNotUsed = $type['used_num'] === 0?"<i data-delType class='fas fa-trash'></i>":"";
                  echo <<< "type"
                  <li data-typeId='{$type['id']}' data-val='{$type['name']}'>
                    <span class='used-num'>{$type['used_num']}</span>
                    <span class="actions">
                      <i data-editType class="fas fa-edit"></i>
                    </span>
                    <input value="{$type['name']}" />
                    {$isNotUsed}
                  </li>   
                  type;
                }
              ?>
            </ul>
            <small id="typesMsg"></small>
            <button class="set-btn" data-addNewType>أضف نوع جديد</button>
          </div>
          <div class="set">
            <h3 class="set-title">أنواع التفاعلات على إعلانات المطاعم</h3>
            <ul id="reactsList" class="data">
              <?php
              foreach($reacts as $react){
                $isNotUsed = $react['used_num'] === 0?"<i data-delReact class='fas fa-trash'></i>":"";
                echo <<< "react"
                <li data-reactID="{$react['id']}" data-val="{$react['name']}" data-img="{$react['image']}">
                  <form enctype="multipart/form-data">
                    <span class='used-num'>{$react['used_num']}</span>
                    <span class="actions">
                      <i data-editReact class="fas fa-edit"></i>
                    </span>
                    <img src="{$react['image']}" alt="">
                    <input name="reactName" value="{$react['name']}"/>
                    <label for="reactIcon{$react['id']}" class="fas fa-upload"></label>
                    <input  type="file" 
                            id="reactIcon{$react['id']}" 
                            name="reactIcon" 
                            accept="image/png, image/jpeg" 
                            hidden>
                    <input type="text" name="reactID" value="{$react['id']}" hidden>
                    {$isNotUsed}
                    </form>
                </li>
                react;
              }
              ?>
            </ul>
            <small id="reactMsg"></small>
            <button id="addNewReact" class="set-btn">أضف تفاعل جديد</button>
          </div>
        </div>

      </div>
    </section>
  </div>
</main>

<!-- <i class="x fas fa-times"></i>
                  <i class="y fas fa-check"></i> -->
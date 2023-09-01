<?php 
SESSION_START();

$pageTitle = "الإعلانات | Resto";
$needsNav = true;
$needsFooter = true;
$selectedTab = "ads";

// files directories
$css = "../layout/css/";
$js = "../layout/js/";
$func = "../includes/functions/";
$cssFiles = ["posts.css"];
include "../init.php";

$userID = isset($_SESSION['user'])?$_SESSION['user']['id']:-1;


// get reacts options
$reacts = getReactsList();

$reactsOptions = "";
foreach($reacts as $react){
  $reactsOptions .= "<img data-reactID='{$react['id']}' src='{$react['image']}'>";
}

// get posts from database
$posts = getPosts(); 
// get ads types
$adTypes = getAdTypes();

// 
$recipesOnSale = getRecipesOnSale();
// echo '<pre>';
// var_dump($adTypes);
// echo '</pre>';
?>
<main class="posts-page">
  <div class="mycontainer">
    <section class="search-section">
      <div class="form-con">
        <form id="postSearch">
          <input type="search" name="postsSearch" id="postsSearch" placeholder="ابحث عن إعلانات لمطعم، وجبة.." />
          <button type="submit" class="fas fa-search"></button>
        </form>
      </div>
      <img src="./../layout/images/phone-pizza-restaurant.png" alt="" />
      <div class="type-filter">
        <label for="type">نوع الإعلانات</label>
        <select name="ad-type" id="adType">
          <option value="-1">الكل</option>
          <?php
          foreach($adTypes as $type){
            echo "<option value='{$type['id']}'>{$type['name']}</option>";
          }
          ?>
        </select>
      </div>
    </section>
    <section class="main-section">
      <?php
      if(count($posts) == 0){
        echo <<<"noposts"
        <div class="no-items">
          <img src="./../layout/images/no-items-found.png" alt="" />
          <p>لا يوجد إعلانات!..</p>
        </div>
        noposts;
      }else{
        echo "<ul id='posts-list'>";
        foreach($posts as $post ){
          $media = "";
          if(count($post['media']) > 0){
          $sliderClass = count($post['media']) > 1?"js-posts-slider":"";
          $images = "";
          foreach($post['media'] as $img){
            $images .= "<img src='$img'/>";
          }
          $media = "<div class='media-container $sliderClass'>$images</div>";
        }
  
        $date = dateFormater($post['created_at'],"d MMMM, hh:mm");
  
  
        $reactsNum = $post['reacts']['reacts_number'];
        if($reactsNum > 0){
          $Imgs = "";
          foreach($post['reacts']['top_three'] as $img){
            $Imgs .= "<img src='{$img['image']}' />";
          }
          $reacts = "<div data-reacts class='center-flex'>$Imgs
          <span>$reactsNum</span>
          </div>";
        }else{
          $reacts = "<div data-reacts class='center-flex'>
              <span><i class='d-none'></i></span>
            </div>";
        }
        
        $commentsNum = count($post['comments']);
        $comments = "<div data-showComments class='commetns center-flex'>
          <span></span></div>";
        if($commentsNum > 0){
          $comments = "<div data-showComments class='commetns center-flex'>
          <span>$commentsNum</span>
          <i class='fas fa-comment'></i>
        </div>";
        }
  
        $commentsContent = count($post['comments'])>0?"": "<li data-noComments>لا يوجد تعليقات</li>";
        foreach($post['comments'] as $comment){
          $date = dateFormater($comment['date'],"d MMMM, hh:mm");
          $reply = "";
          if($comment['reply'] != null){
            $reply = "<div class='reply'>
            <a href='restProfile.php?id={$post['restID']}'>{$post['restName']}</a>
            <p>{$comment['reply']}</p>
            </div>";
          }
          $removeOption = "";
          if($comment['customer']['id'] == $userID){
            $removeOption = "<span data-delComment='{$comment['id']}'>حذف</span>";
          }
          $commentsContent .= "<li data-commentID = '{$comment['id']}'>
          <div class='comment'>
            <img src='{$comment['customer']['image']}' alt='' />
            <div>
              <h3>
                {$comment['customer']['name']}
                <!-- <img src='reactImg' /> -->
              </h3>
              <p>{$comment['content']}</p>
              <div class='actions'>
                <div class='btns'>
                $removeOption
                </div>
                <div class='date'>$date</div>
              </div>
            </div>
          </div>
          $reply
        </li>";
        }
        echo <<<"post"
        <li class="post" data-postID="{$post['id']}" data-type="{$post['type']['id']}">
          <div class="post-head">
            <img src='{$post['restImage']}'/>
            <a href="./../restaurants/restProfile.php?id={$post['restID']}" data-restName>{$post['restName']}</a>
          </div>
          <div class='post-body'>
            $media
            <div class="content">
              <p class="caption" data-caption>{$post['caption']}</p>
              <div class="date">$date</div>
            </div>
            <div class="actions">
              <div class="activities between-flex">
                $reacts
                $comments
              </div>
              <div class="between-flex">
                <button class="reacts-btn center-flex">
                  <div class="btn-content">
                    <i class="fas fa-thumbs-up fa-fw"></i>تفاعل
                    <!-- <img
                      src="./uploads/settings/reactions/fire.png"
                      alt=""
                    /> -->
                  </div>
                  <div class="reacts">
                  $reactsOptions
                  </div>
                </button>
                <button data-comment class="center-flex">
                  <i class="fas fa-comment-alt fa-fw"></i>تعليق
                </button>
              </div>
            </div>
            <form class="comment-form">
              <input type="text" name="comment" placeholder="اكتب تعليقك" />
              <input type="hidden" name="postID" value="{$post['id']}" />
              <button type="submit">
                <i class="fas fa-commenting"></i>
              </button>
            </form>
            <div class="post-comments">
              <h4>التعليقات</h4>
              <ul data-commentsUl class="comments">
                $commentsContent
              </ul>
            </div>
          </div>
        </li>
      
        post;
        }
        echo "</ul>";
      }

      ?>
    </section>
    <section class="news-section">
      <h1>احدث العروض</h1>
      <ul>
        <?php
        foreach($recipesOnSale as $recipe){
          $price = number_format($recipe['price']);
          $discount = number_format($recipe['discount']);
          echo <<<"recipe"
          <li>
            <a href="./../menus_recipes/recipePreview.php?recipeID={$recipe['id']}">
              <div class="img-box">
                <img src="{$recipe['image']}" />
                <div class="overlay"></div>
                <h2 class="name">{$recipe['name']}</h2>
                <div class="price">
                  <span>$price</span>
                  <span>$discount</span>
                </div>
              </div>
            </a>
          </li>
        
          recipe;
        }
        ?>
      </ul>
    </section>
  </div>
</main>
<?php
$jsFiles = ["posts.js"];
include '../includes/templates/footer.php';
<?php     
$posts = getRestaurantPosts($restID);
$postsEvents = getRestaurantPostsEvents($restID);

$latestReacts = $postsEvents['reacts'];
$latestComments = $postsEvents['comments'];
// get ad types
$ad_types = getAdTypes();


$restName = $_SESSION['restaurant']['name'];
function latestEvents(){
  global $latestComments,$latestReacts;
  $events ="";

  $i = 0;
  $j = 0;

  for( ; $i < count($latestComments) && $j < count($latestReacts) ;){
    $react = $latestReacts[$j];
    $comment = $latestComments[$i];

    if($react['date'] > $comment['date']){
      $ad_type1 = str_replace("إعلان","",$react['ad_type']);
      $eventMsg1 = "<p>تفاعل <b>{$react['customer']['name']}</b> مع إعلانك $ad_type1: {$react['name']}</p>";
      $img1 = $react['image'];
      $events .= addEvent($react,$eventMsg1,$img1);
      $j++;
    }else{
      $ad_type2 = str_replace("إعلان","",$comment['ad_type']);
      $eventMsg2 = "<p>علق <b>{$comment['customer']['name']}</b> على إعلانك $ad_type2 :
          '<span>{$comment['content']}</span>'
        </p>";
        $img2 = "../../layout/images/Standard/comment-icon.png";
      $events .= addEvent($comment,$eventMsg2,$img2);
      $i++;
    }
  }
  for( ;$j < count($latestReacts) ; $j++){
    $event = $latestReacts[$j];

    $ad_type = str_replace("إعلان","",$event['ad_type']);
    $eventMsg = "<p>تفاعل <b>{$event['customer']['name']}</b> مع إعلانك $ad_type: {$event['name']}</p>";

    $events .= addEvent($event,$eventMsg,$event['image']);
  }
  for( ;$i < count($latestComments) ; $i++){
    $event = $latestComments[$i];

    $ad_type = str_replace("إعلان","",$event['ad_type']);
    $eventMsg = "<p>
      علق <b>{$event['customer']['name']}</b> على إعلانك $ad_type :
      '<span>{$event['content']}</span>'
    </p>";

    $events .= addEvent($event,$eventMsg,"../../layout/images/Standard/comment-icon.png");
  }
  
  $events = "<ul class='events'>" . $events;
  
  $events .= "</ul>";

  return $events;
}

?>
<h2 class="tab-title">
  <span>الإعلانات</span>
</h2>
<main class="posts-content" data-restID="<?=$restID?>" data-restName="<?=$restName?>">
  <div id="add-post-card" class="my-card">
    <div class="component-title">إضافة إعلان جديد</div>
    <form class="post-form" id="add-post-form">
      <div class="textarea">
        <textarea name="caption" id="caption" cols="30" rows="5" maxlength="500"
          placeholder="اكتب تفاصيل الإعلان هنا"></textarea>
        <div class="word-count">تبقى: <span>500</span> حرف</div>
      </div>
      <div class="input file">
        <div class="media-preview"></div>
        <label for="media"><i class="fas fa-plus"></i> أضف صور</label>
        <input type="file" name="media[]" hidden id="media" multiple accept="image/*" />
      </div>
      <div class="input">
        <label for="adType"><i class="fas fa-ad"></i> نوع الإعلان</label>
        <select name="adType" id="adType">
          <?php
          foreach($ad_types as $type){
            echo "<option value='{$type['id']}'>{$type['name']}</option>";
          }
          ?>
        </select>
      </div>
      <input type="text" hidden name="restID" value="<?=$restID?>">
      <button class="dash-btn disabled">إضافة</button>
      <small></small>
    </form>
  </div>
  <div class="my-card">
    <div class="component-title">تفاعل الزبائن</div>
    <?php
    if(count($latestComments) == 0 && count($latestReacts) == 0){
      $msg = count($posts) == 0? "لم تقم بإضافة إعلانات بعد.":"لم يتفاعل أحد مع إعلاناتك إلى الآن";
      echo <<< "no_events"
      <div class="no-events">
        <img src="./../../layout/images/searching-for-items-in-box.png" alt="" />
        <p>
          لا يوجد أية تفاعلات.<br />
          $msg 
        </p>
      </div>
      no_events;
    }else{
      echo latestEvents();
    }
    ?>

  </div>
  <div id="posts-card" class="my-card span-2">
    <div class="component-title">الإعلانات المضافة</div>
    <?php
    if(count($posts) == 0){
      echo <<< "no_posts"
      <div class="no-posts">
        <div>
          <p>لم تقم بإضافة إعلانات بعد.</p>
          <a href="#add-post-card" class="dash-btn">إضافة إعلان</a>
        </div>
        <img src="./../../layout/images/people-wondering.jpg" alt="" />
      </div>
      no_posts;
    }else{
      echo getPostsContent($posts);
    }
    ?>
  </div>
</main>

<?php
function getPostsContent($posts){
  global $restName;
  $content = "<ul class='posts'>";
  foreach($posts as $post){
    $comments = "<div class='no-comments'>
      <p>لا يوجد تعليقات</p>
    </div>";
    if(count($post['comments']) !=0){
      $comments = "<ul>";
      foreach($post['comments'] as $comment){
        $date = dateFormater($comment['date'],"d MMMM, hh:mm");
        $replyBtn = "<span data-reply='{$comment['id']}'>رد</span>";
        $reply = "";
        if($comment['reply'] != null && !empty(trim($comment['reply']))){
          $replyDate = dateFormater($comment['reply_date'],"d MMMM, hh:mm");
          $reply="<div class='reply'>
            <div class='text'>
              <h5>$restName</h5>
              <p>{$comment['reply']}</p>
            </div>
            <div class='date'>$replyDate</div>
          </div>
          ";
          $replyBtn = "<span></span>";
        }
        $comments .= "<li>
          <img src='{$comment['customer']['image']}' />
          <div>
            <div class='comment'>
              <h4>{$comment['customer']['name']}</h4>
              <p>{$comment['content']}</p>
            </div>
            <div class='details'>
              $replyBtn
              <span>$date</span>
            </div>
            $reply
          </div>
        </li>";
      }
      $comments .= "</ul>";
    }
    $ad_type = str_replace("إعلان","",$post['type']['name']);
    $postDate = dateFormater($post['created_at'],"d MMMM");

    $reacts = "";
    $reactsNum = $post['reacts']['reacts_number'];

    if($reactsNum != 0){
      $topThree = $post['reacts']['top_three'];
      foreach($topThree as $react){
        $reacts .= "<img src='{$react['image']}'/>";
      }
    }

    $commentsNum = count($post['comments']);

    $postMedia = '';
    if(count($post['media']) != 0){
      for( $i= 0 ;$i< count($post['media']); $i++){
        $showOrHide = $i == 0?"":"d-none";
        $postMedia .= "<div class='box $showOrHide'>
              <img src='{$post['media'][$i]}'/>
            </div>";
      }
      if(count($post['media']) > 1){
        $count = count($post['media']) -1;
        $postMedia .="<div data-post-media class='more-media'>
        <div class='show'><span>+$count</span>عرض الكل</div>
        <div class='hide'>إغلاق</div>
      </div>";
      }
    }
    $content .= "<li class='post' data-postID='{$post['id']}'>
      <header>
        <i data-post-options class='fas fa fa-ellipsis-h'></i>
        <div class='post-options'>
          <div data-edit-post ><i class='fas fa-edit'></i>تعديل</div>
          <div data-del-post ><i class='fas fa-trash'></i>حذف</div>
        </div>
        <div>
          <div class='type'>$ad_type</div>
          <div class='date'>$postDate</div>
        </div>
      </header>
      <main>
        <p>{$post['caption']}</p>
        <div class='media'>
          $postMedia
        </div>
      </main>
      <div class='footer'>
        <div class='reacts'>
          $reacts
          <span>$reactsNum</span>
        </div>
        <div data-post-comments>$commentsNum<i class='fas fa-comment'></i></div>
      </div>
      <div class='comments'>
        $comments
        <div data-close-comments class='close-comments'>إغلاق <i class='fas fa-angle-down'></i></div>
      </div>
    </li>";
  }
  $content .= "</ul>";

  return $content;
}
function addEvent($event,$eventMsg,$img){

  $date = dateFormater($event['date'],"d MMMM, hh:mm");
  return "<li class='event'>
      <div class='user'>
        <img
          class='user-img'
          src='{$event['customer']['image']}'
          alt=''
        />
        <img class='react-img' src='$img'/>
      </div>
      <div class='details'>
        $eventMsg
        <span class='date'>$date</span>
      </div>
    </li>";
}
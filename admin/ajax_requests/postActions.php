<?php
session_start();
/* 
** Requests to Add review on Recipes/Restaurant
** Only POST requests
*/

if($_SERVER['REQUEST_METHOD']=='POST'){
  require "../config.php";
  require "../connect.php";
  include "../includes/functions/functions.php";


  $result = [
    "result" => true,
    "message" => " حدث خطأ غير متوقع. " . __LINE__,
    "no_user" => true,
    "comment" => [],
    "commentsNum" => [],
    "reacts" => [
      "reacts_number" => 0,
      "top_three" => []
    ]
  ];
  
  
  if(isset($_SESSION['user'])){

    $userID = $_SESSION['user']['id'];
    $result['no_user'] = false;

    $action = $_POST['action'];
    $postID = $_POST['postID'];
    switch ($action) {
      case 'ADD_REACT':
        $reactID = $_POST['reactID'];
        
        $reactRowId = isReactExist($userID,$postID);

        if($reactRowId != -1){
          $stmt = $con->prepare("UPDATE ad_react SET react_id = ? WHERE id = ?");
          $queryResult = $stmt->execute([$reactID,$reactRowId]);
        }else{
          $stmt = $con->prepare("INSERT INTO ad_react(advert_id,customer_id,react_id) VALUES(?,?,?)");
          $queryResult = $stmt->execute([$postID,$userID,$reactID]);
        }

        if($queryResult){
          $result['message'] = "تمت إضافة التفاعل بنجاح";
          $result['inserted_id'] = $con->lastInsertId();
          $result['reacts'] = getPostReacts($postID);
        }else{
          $result['result'] = false;
        }
        break;
      case 'REMOVE_REACT':
        $reactRowId = isReactExist($userID,$postID);
        if($reactRowId != -1){
          $stmt = $con->prepare("DELETE FROM ad_react WHERE id = ?");
          $queryResult = $stmt->execute([$reactRowId]);
          if($queryResult){
            $result['message'] = "تمت الازالة بنجاح";
            $result['reacts'] = getPostReacts($postID);

          }else{
            $result['result'] = false;
          }
        }# else do nothing, there is no react already
        break;
      case 'ADD_COMMENT':
        $comment = $_POST['comment'];
        $stmt = $con->prepare("INSERT INTO ad_comment(advert_id,customer_id,content) VALUES(?,?,?)");
        $queryResult = $stmt->execute([$postID,$userID,$comment]);

        if($queryResult){
          $result['message'] = "تمت إضافة التعليق بنجاح";
          $result['comment'] = [
            "id" => $con->lastInsertId() ,
            "comment" => $comment,
            "image" => $_SESSION['user']['image'],
            "name" => $_SESSION['user']['name'],
            "date" => dateFormater(date("y-m-d h:i:s"),"d MMMM, hh:mm"),
          ];
          $result['commentsNum'] = count(getPostComments($postID));
        }else{
          $result['result'] = false;
        }
        break;
      case 'REMOVE_COMMENT':
        $commentID = $_POST['commentID'];

        $stmt = $con->prepare("DELETE FROM ad_comment WHERE id = ?");
        $queryResult = $stmt->execute([$commentID]);
        if($queryResult){
          $result['message'] = "تم حذف التعليق بنجاح";
          $result['commentsNum'] = count(getPostComments($postID));
        }else{
          $result['result'] = false;
        }

        break;
      default:
        $result = [
          "result" => false,
          "message" => "طلب غير صحيح" . __LINE__
        ];
        break;
    }
  }

  echo json_encode($result);
}
function isReactExist($userID,$postID){
  global $con;
  $isExist = $con->prepare("SELECT id FROM ad_react WHERE customer_id = ? AND advert_id = ? LIMIT 1");
  $queryResult  = $isExist->execute([$userID,$postID]);
  if($queryResult && $isExist->rowCount() > 0){
    return $isExist->fetch()['id'];
  }
  
  return -1;

}
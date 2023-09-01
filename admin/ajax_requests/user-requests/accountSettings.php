<?php
session_start();
header("Access-Control-Allow-Method: POST");
header('Access-Control-Allow-Origin: *');
/* 
** Request to update user setting
** Requests from customer dashborad page, account tab
*/
function removeUserImage($userID){
  global $con;
  $stmt = $con->prepare("SELECT image FROM customer WHERE id=?");
  $stmt->execute([$userID]);

  if($stmt->rowCount() > 0){
    $imageName = $stmt->fetch()['image'];
    $folderPath =  FULL_ROOTPATH . '/uploads/customers/';
    $imagePath = $folderPath . $imageName;
    if(!empty($imageName) && file_exists($imagePath) && is_file($imagePath)){
      if(!unlink($imagePath))
        return false;
    }
  }// else there is no such recipe
  return true;
}
if($_SERVER['REQUEST_METHOD']=='POST'){
  require "../../config.php";
  require "../../connect.php";
  include "../../includes/functions/functions.php";

  $userID = $_POST['userID'];
  $infoType = $_POST['infoType'];
  $queryResult = false;
  $failureMessage = "لم ينجح تعديل المعلومات لسبب غير معروف! قد يكون الحساب غير موجود.";
  switch($infoType){
    case "CONFIRM_PASSWORD":
      $pass = sha1($_POST['pass']);
      // check if password currect
      $stmt = $con->prepare("SELECT id FROM customer WHERE id = ? AND password = ?");
      $queryResult = $stmt->execute([$userID, $pass]);

      if($stmt->rowCount()  > 0){
        $result = [
          "result" => true,
          "message" => "كلمة المرور صحيحة."
        ];
      }else{
        $result = [
          "result" => false,
          "message" => "كلمة المرور غير صحيحة!"
        ];
      }
    break;
    case "CHANGE_PASSWORD":
      $pass = sha1($_POST['pass']);
      // Delete restaurant account from DB
      $stmt = $con->prepare("UPDATE customer SET password = ? WHERE id=?");
      $queryResult =  $stmt->execute([ $pass,$userID]);

      if($queryResult){
        $result = [
          "result" => true,
          "message" => "تم تغيير كلمة المرور بنجاح."
        ];
      }else{
        $failureMessage = "لم نتمكن من تغيير كلمة المرور!";
      }
    break;
    case "DELETE_MY_ACCOUNT":
      // Delete restaurant account from DB
      removeUserImage($userID);
      $stmt = $con->prepare("DELETE FROM customer WHERE id=?");
      $queryResult = $stmt->execute([$userID]);
      if($queryResult){
        $result = [
          "result" => true,
          "message" => "تم حذف الحساب بنجاح"
        ];
        // remove account from session
        if(isset($_SESSION['user'])){
          unset($_SESSION['user']);
          $_SESSION['userHasBeenDeleted'] = true;// to show message in index page after redirect
        }
      }
    break;
  }
  if(!$queryResult){
    $result = [
      "result" => false,
      "message" => $failureMessage
    ];
  }
  echo json_encode($result);
}
function findIndexByID($array,$searched_id){
  foreach($array as $key => $subArray){
    if($subArray['id'] == $searched_id)
      return $key;
  }
  return -1;
}
<?php
session_start();
header("Access-Control-Allow-Method: POST");
header('Access-Control-Allow-Origin: *');
/* 
** Request to update restaurant Profile info
** Requests from restaurant dashborad page, control tab
** Requests form these sections
** 1- BASIC_INFO: restaurant-preview widget, basic info form
** 1- FASTFOOD_STATUS_INFO: services widget, brestaurant status options
*/
if($_SERVER['REQUEST_METHOD']=='POST'){
  require "../../config.php";
  require "../../connect.php";
  include "../../includes/functions/functions.php";

  $infoType = $_POST['infoType'];
  $successMsg = "";
  $failedMsg = "";
  $result = [
    "result" => false,
    "message" =>"لم ينجح تعديل المعلومات لسبب غير معروف! قد يكون الحساب غير موجود."
  ];
  try{
    $con->beginTransaction();

    switch($infoType){
      case "EDIT_EMAIL":
        $email = $_POST['email'];
  
        $failedMsg = "لم نتمكن من تغيير البريد!";
        $stmt = $con->prepare("UPDATE customer SET email = ? WHERE is_admin = 1");
        $queryResult =  $stmt->execute([ $email]);
  
        if(!$queryResult){
          throw new RuntimeException($failedMsg);
        }

        if(isset($_SESSION['admin'])){
          $_SESSION['admin']['email']=$email;
        }
        
        $successMsg = "تم تغيير البريد بنجاح.";
        
        break;
      case "CONFIRM_PASSWORD":
        $pass = sha1($_POST['pass']);

        $failedMsg = "كلمة المرور غير صحيحة!";
        // check if password currect
        $stmt = $con->prepare("SELECT id FROM customer WHERE is_admin = 1 AND password = ? LIMIT 1");
        $queryResult = $stmt->execute([$pass]);
  
        if($stmt->rowCount()  === 0){
          throw new RuntimeException($failedMsg);
        }

        $successMsg = "كلمة المرور صحيحة.";
        
      break;
      case "CHANGE_PASSWORD":
        $pass = sha1($_POST['pass']);
        $failedMsg = "لم نتمكن من تغيير كلمة المرور!";

        $stmt = $con->prepare("UPDATE customer SET password = ? WHERE is_admin = 1");
        $queryResult =  $stmt->execute([ $pass]);
  
        if(!$queryResult){
          throw new RuntimeException($failedMsg);
        }

        $successMsg = "تم تغيير كلمة المرور بنجاح.";

      break;
      case "EDIT_AD_TYPE":
        $typeID = $_POST['typeID'];
        $value = $_POST['value'];

        $failedMsg = "لم نتمكن من تغيير نوع الإعلان لسبب غير معروف!";

        $stmt = $con->prepare("UPDATE ad_type SET name = ? WHERE id = ?");
        $queryResult =  $stmt->execute([ $value,$typeID]);
  
        if(!$queryResult){
          throw new RuntimeException($failedMsg);
        }

        $successMsg = "تم تغيير النوع بنجاح.";

        break;
      case "ADD_AD_TYPE":
        $value = $_POST['value'];
        $failedMsg = "لم نتمكن من إضافة نوع الإعلان الذي قمت بإدخاله لسبب غير معروف!";

        $stmt = $con->prepare("INSERT INTO ad_type(name) VALUES(?)");
        $queryResult = $stmt->execute([$value]);
        if(!$queryResult){
          throw new RuntimeException($failedMsg);
        }

        $result +=["insertedID" => $con->lastInsertId() ];
        $successMsg = "تمت الإضافة بنجاح";

        break;
      case "DELETE_AD_TYPE":
        $failedMsg = "لم نتمكن من حذف نوع الإعلان المحدد لسبب غير معروف";

        $typeID = $_POST['typeID'];
        $stmt = $con->prepare("DELETE FROM ad_type WHERE id = ?");
        $queryResult = $stmt->execute([$typeID]);
        if(!$queryResult){
          throw new RuntimeException($failedMsg);
        }

        $successMsg = "تم الحذف بنجاح";

        break;
      case "EDIT_AD_REACT":
        $failedMsg = "لم نتمكن من تعديل التفاعل لسبب غير معروف!";

        $reactID = $_POST['reactID'];
        $value = $_POST['reactName'];
        $imageFile = $_FILES['reactIcon'];
        $mustEditImage = !empty($imageFile['name']); //if image not empty it needs to change

        if($mustEditImage){
          if(!removeOldReactImage($reactID)){
            throw new RuntimeException("لم نتمكن من حذف ملف صورة التفاعل القديمة");
          }
          // upload new image file
          $dir = FULL_ROOTPATH . "uploads/settings/reactions/";
          $finalImageName = $reactID . "_" . rand(1,1000000) . "_" . $imageFile["name"];
          move_uploaded_file($imageFile['tmp_name'],  $dir . $finalImageName);

          $stmt = $con->prepare("UPDATE react SET image = ? WHERE id = ?");
          $queryResult = $stmt->execute([$finalImageName,$reactID]);

          if(!$queryResult){
            throw new RuntimeException("لم نتمكن من تعديل الصورة لسبب غير معروف");
          }

          $result +=["insertedImage" => getImagePath($finalImageName,"REACT")];

        }

        $stmt = $con->prepare("UPDATE react SET name = ? WHERE id = ?");
        $queryResult = $stmt->execute([$value,$reactID]);
        if(!$queryResult){
          throw new RuntimeException($failedMsg);
        }
        
        $successMsg = "تم التعديل بنجاح";

        break;

      case "ADD_AD_REACT":
        $failedMsg = "لم نتمكن من إضافة التفاعل لسبب غير معروف!";

        $value = $_POST['reactName'];
        $imageFile = $_FILES['reactIcon'];

        $dir = FULL_ROOTPATH . "uploads/settings/reactions/";
        $finalImageName = rand(1,1000000) . "_" . $imageFile["name"];
        move_uploaded_file($imageFile['tmp_name'],  $dir . $finalImageName);

        $stmt = $con->prepare("INSERT INTO react(name,image) VALUES(?,?)");
        $queryResult = $stmt->execute([$value,$finalImageName]);
        if(!$queryResult){
          throw new RuntimeException($failedMsg);
        }
        $result +=["insertedImage" => getImagePath($finalImageName,"REACT")];
        $result +=["insertedID" => $con->lastInsertId()];
        
        $successMsg = "تمت الإضافة بنجاح";

        break;
      case "DELETE_AD_REACT":
        $failedMsg = "لم نتمكن من حذف التفاعل المحدد لسبب غير معروف";

        $reactID = $_POST['reactID'];

        if(!removeOldReactImage($reactID)){
          throw new RuntimeException("لم نتمكن من حذف ملف صورة التفاعل القديمة");
        }

        $stmt = $con->prepare("DELETE FROM react WHERE id = ?");
        $queryResult = $stmt->execute([$reactID]);

        if(!$queryResult){
          throw new RuntimeException($failedMsg);
        }
        $successMsg = "تم الحذف بنجاح";

        break;
    }

    $con->commit();

    $result["result"] = true;
    $result["message"] = $successMsg;

  } catch( RuntimeException $e ) {

    $con->rollBack();
    $result['message'] = $failedMsg . $e->getMessage();

  } catch( PDOException $e){

    $con->rollBack();
    $result['message'] = $failedMsg . $e->getMessage();

  }
  echo json_encode($result);
}
function removeOldReactImage($reactID){
  global $con;
  $stmt = $con->prepare("SELECT image FROM react WHERE id=?");
  $stmt->execute([$reactID]);

  if($stmt->rowCount() > 0){
    $imageName = $stmt->fetch()['image'];
    $imagePath = FULL_ROOTPATH . "uploads/settings/reactions/" . $imageName;
    if(!empty($imageName) && file_exists($imagePath) && is_file($imagePath)){
      if(!unlink($imagePath))
        return false;
    }
  }// else there is no such react
  return true;
}
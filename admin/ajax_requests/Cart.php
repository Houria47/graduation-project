<?php
session_start();
/* 
** Requests to Handle Cart Requests
** Add/Remove && Increment/Decrement quantity
** Only POST requests
*/

if($_SERVER['REQUEST_METHOD']=='POST'){
  require "../config.php";
  require "../connect.php";
  include "../includes/functions/functions.php";


  $result = [
    "result" => false,
    "message" => " حدث خطأ غير متوقع. " . __LINE__,
    "no_user" => true,
    "inserted_id" => null,
    "items" => []
  ];
  
  
  if(isset($_SESSION['user'])){

    $userID = $_SESSION['user']['id'];
    $result['no_user'] = false;

    $action = $_POST['action'];
    switch ($action) {
      case 'ADD':
        $recipeID = $_POST['recipeID'];
        $amount = $_POST['amount'];
        if($amount <= 0){
          $result["message"] = "يجب اختيار كمية مقبولة (أكبر من صفر).";
          break;
        }
        $cartItemID = isAdded($recipeID,$userID);
        if($cartItemID != -1){
          if(addAmount($amount,$cartItemID)){
            $result['result'] = true;
            $result["message"] = "تمت الإضافة بنجاح";
          }
        }else{
          $stmt = $con->prepare("INSERT INTO cart(recipe_id,customer_id,quantity) VALUES(:recipeID,:customerID,:amount) ");
          $queryResult = $stmt->execute([
            "recipeID" => $recipeID,
            "customerID" =>$userID,
            "amount" => $amount
          ]);
          if($queryResult){
            $result['result'] = true;
            $result["message"] = "تمت الإضافة بنجاح";
            $result['inserted_id'] = $con->lastInsertId();
          }
        }
        break;
      case "SET_QTY":
        $itemID = $_POST['itemID'];
        $amount = $_POST['amount'];
        if($amount <= 0){
          $result["message"] = "يجب اختيار كمية مقبولة (أكبر من صفر).";
          break;
        }
        if(setAmount($amount,$itemID)){
          $result['result'] = true;
          $result["message"] = "تمت الإضافة بنجاح";
        }
        break;
      case 'REMOVE':
        $itemID = $_POST['itemID'];
        $stmt = $con->prepare("DELETE FROM cart WHERE id = ? LIMIT 1");
        $queryResult = $stmt->execute([$itemID]);
      
        if($queryResult){
          $result['result'] = true;
          $result["message"] = "تمت الإزالة بنجاح";
        }
        break;
      case 'INCREMENT':
        $cartItemID = $_POST['itemID'];
        $stmt = $con->prepare("SELECT id,quantity FROM cart WHERE id = ? LIMIT 1");
        $queryResult = $stmt->execute([$cartItemID]);
      
        if($queryResult && $stmt->rowCount() > 0){
          $cartItem = $stmt->fetch();
            if(addAmount(1,$cartItem['id'])){
              $result['result'] = true;
              $result["message"] = "تمت الإضافة بنجاح";
            }
        }
        break;
      case 'DECREMENT':
        $cartItemID = $_POST['itemID'];
        $stmt = $con->prepare("SELECT id,quantity FROM cart WHERE id = ? LIMIT 1");
        $queryResult = $stmt->execute([$cartItemID]);
      
        if($queryResult && $stmt->rowCount() > 0){
          $cartItem = $stmt->fetch();
          if($cartItem['quantity'] > 1){
            if(addAmount(-1,$cartItem['id'])){
              $result['result'] = true;
              $result["message"] = "تمت الإزالة بنجاح";
            }
          }else{
            if(removeFromCart($cartItem['id'])){
              $result['result'] = true;
              $result["message"] = "تمت الإزالة بنجاح";
            }
          }
        }
        break;
      case 'GET_ITEMS':
        $stmt = $con->prepare("SELECT c.*,res.id as restaurantID, res.name as restaurantName,r.name as recipeName 
        , r.price,r.discount_price
        FROM cart c 
        LEFT OUTER JOIN recipe r 
        ON c.recipe_id = r.id 
        LEFT OUTER JOIN menu m 
        ON r.menu_id = m.id 
        LEFT OUTER JOIN restaurant res 
        ON m.restaurant_id = res.id 
        WHERE c.customer_id = ?");
        $items = [];
        $queryResult = $stmt->execute([$userID]);
        if($queryResult){
          $result["message"] = "تم جلب البيانات بنجاح";
          $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
          foreach($rows as $row){
            $price = $row['price'];
            if($row['discount_price'] != null){
              $price = $row['discount_price'];
            }
            $item = [
              "id"=>$row['id'],
              "name" => $row['recipeName'],
              "amount" => $row['quantity'],
              "price" => $price
            ];
            if(isset($items[$row['restaurantID']])){
              $items[$row['restaurantID']]['items'][] = $item;
              $items[$row['restaurantID']]['total'] += $price * $item['amount'];
            }else{
              $items += [$row['restaurantID'] => [
                "restaurantInfo" => [
                  "id" => $row['restaurantID'],
                  "name" => $row['restaurantName'],
                  #... update query & add more info here if needed 
                ],
                "items" => [$item],
                "total" => $item['price'] * $item['amount']
              ]];
            }
          }
          $result["result"] = true;
          $result['items'] = $items;

        }
        break;
      case "GET_CART":
          $cart = getUserCart($userID);
          $result["result"] = true;
          $result["message"] = "تم جلب البيانات بنجاح";
          $result['items'] = $cart;
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

function addAmount($amount,$cartItemID){
  global $con;
  $stmt = $con->prepare("UPDATE cart SET quantity = quantity + ? WHERE id = ?");
  $queryResult = $stmt->execute([$amount,$cartItemID]);
  if($queryResult){
    return true;
  }
  return false;
}


function setAmount($amount,$cartItemID){
  global $con;
  $stmt = $con->prepare("UPDATE cart SET quantity = ? WHERE id = ?");
  $queryResult = $stmt->execute([$amount,$cartItemID]);
  if($queryResult){
    return true;
  }
  return false;
}
function isAdded($recipeID,$customerID){
  global $con;
  $stmt = $con->prepare("SELECT id FROM cart WHERE recipe_id = ? AND customer_id = ? LIMIT 1");
  $queryResult = $stmt->execute([$recipeID,$customerID]);

  if($queryResult && $stmt->rowCount() > 0){
    return $stmt->fetch()['id'];
  }

  return -1;
}
function removeFromCart($itemId){
  global $con;
  $stmt = $con->prepare("DELETE FROM cart WHERE id = ?");
  $queryResult = $stmt->execute([$itemId]);
  if($queryResult){
    return true;
  }
  return false;
}
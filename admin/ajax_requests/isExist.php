<?php
/* 
** Requests to check if account exist in database
** both Restaurant and user account
** we could use it for any other kind data [name,id...etc] 
** Only POST requests
*/

if($_SERVER['REQUEST_METHOD']=='POST'){
  require "../config.php";
  require "../connect.php";
  include "../includes/functions/functions.php";

  $select = $_POST['select'];
  $table = $_POST['from'];
  $value = $_POST['value'];

  $result = isExist($select, $table, $value);
  echo json_encode(["result" => $result]);

}

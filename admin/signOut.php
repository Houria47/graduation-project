<?php 
session_start();
if(isset($_GET['do'])){
  if($_GET['do']=="USER"){
    unset($_SESSION['user']);
  }
  if($_GET['do'] == "REST"){
    unset($_SESSION['restaurant']);
  }
  if($_GET['do'] == "ADMIN"){
    unset($_SESSION['admin']);
  }
}
header('Location: index.php');
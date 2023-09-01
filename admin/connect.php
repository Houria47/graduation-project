<?php
$db = "mysql:host=localhost;dbname={$CONFIGS['db_name']}";
$user = $CONFIGS['db_user'];
$pass = $CONFIGS['db_password'];
$option = [
    PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',
];

try{
    $con = new PDO($db , $user , $pass, $option);
    $con->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
}catch(PDOException $e){
    echo 'Failed to Connect ' . $e->getMessage();
    // TODO: write sweet html message code here
    exit;
}
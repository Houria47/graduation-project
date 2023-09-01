<?php
    //  set time zone
    date_default_timezone_set("Asia/Damascus");

    // include required files
    require "config.php";
    require "connect.php";
    //include "includes/languages/english.php";


    //Routes
    if(!isset($tpl)){
      $tpl = 'includes/templates/'; // Templates Directory
    }
    if(!isset($css)){
      $css = 'layout/css/'; // Css Directory
    }
    if(!isset($js)){
      $js = 'layout/js/'; // Js Directory
    }
    if(!isset($func)){
      $func = 'includes/functions/';
    }
    if(!isset($libs)){
      $libs = 'includes/libraries/';
    }
    
    
    // Include the important files
    include $func . 'functions.php';
    include $tpl . 'header.php';

    // include nav if needed
    if(isset($needsNav) && $needsNav){
      include $tpl . 'nav.php';
    }
<?php
include("utils/TypeHint.php");

date_default_timezone_set("Europe/Paris");// for PHPUnit
error_reporting(E_ALL | E_STRICT);//for PHPUnit
ini_set('display_errors', '1');

spl_autoload_extensions(".php"); // comma-separated list

// Auto load from namespace
spl_autoload_register(
  function ($class) {
        include str_replace("\\", "/", $class). '.php';
  }
);
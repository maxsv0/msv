<?php
// *** ./load.php
// *** DO NOT EDIT THIS FILE
// *** WILL BE OVERWRITTEN DURING UPDATE

if (!defined("ABS")) die(".");

if (defined("PHP_HIDE_ERRORS") && PHP_HIDE_ERRORS) {
	ini_set("display_errors", 0);
	error_reporting(0);
} else {
	ini_set("display_errors", 1);
	error_reporting(E_ALL & ~E_NOTICE);
}

if (defined("PHP_LOCALE")) {
	setlocale(LC_ALL, PHP_LOCALE); 
}

if (defined("PHP_TIMEZONE")) {
	date_default_timezone_set(PHP_TIMEZONE);
}

session_start();
set_time_limit(1000);


$pathInclude = ABS."/include";
$pathIncludeLocal = "/include";

define("ABS_INCLUDE", $pathInclude);
define("ABS_MODULE", $pathInclude."/module");
define("ABS_CUSTOM", $pathInclude."/custom");
define("ABS_TEMPLATE", ABS."/templates");

define("LOCAL_INCLUDE", $pathIncludeLocal);
define("LOCAL_MODULE", $pathIncludeLocal."/module");
define("LOCAL_TEMPLATE", "templates");

include(ABS_INCLUDE."/class.module.php");
include(ABS_INCLUDE."/class.msv.php");


// create MSV Website instance
$website = new MSV_Website();

// start the instance
$website->start();
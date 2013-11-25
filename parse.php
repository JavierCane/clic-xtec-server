<?
include "class/includes.php";

header('Content-Type: text/html; charset=utf-8');
error_reporting(E_ALL ^ E_NOTICE);//E_ERROR | E_PARSE);
ini_set('max_execution_time', 0);


echo "<pre>";

$cp = CtrlParser::getInstance();

$cp->run();


?>
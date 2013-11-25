<?php
require_once('class/includes.php');

if (!$_GET['width'] || !$_GET['height']) {
    die('USAGE: /perfil/?width=WIDTH&height=HEIGHT');
}


$perfiles = CtrlPerfiles::getInstance();
list($width,$height,$extension) = $perfiles->getDadesPerfil($_GET['width'],$_GET['height']);
echo $width." - ".$height." ".$extension."<br/>";
?>
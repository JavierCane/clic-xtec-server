<?php
require_once('class/CtrlPerfiles.php');
$perfiles = CtrlPerfiles::getInstance();
list($width,$height,$extension) = $perfiles->getDadesPerfil($_GET['width'],$_GET['height']);
echo $width." ".$height." ".$extension."<br/>";
?>
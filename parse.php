<?
include "class/includes.php";

header('Content-Type: text/html; charset=utf-8');
error_reporting(E_ERROR | E_PARSE);


$id = $_REQUEST['id'];
$lang = $_REQUEST['lang'];

$ccm = ClicCatalegManager::getInstance();
echo "<pre>";
if(!$id){
	$list = $ccm->obtenirListIdClicXTEC($id);
	
	print_r($list);
}
else{
	$clic = $ccm->obtenirClicXTEC($id,$lang);
	print_r($clic);
	//print_r($clic->getJSONEncode());
}


// get last two segments of host name

function getIdFromUrl($url){
	preg_match('/(id=)(.*)/', $url, $res);
	return $res[2];
}
?>
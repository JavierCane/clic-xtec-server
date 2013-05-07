<?
include "class/includes.php";

header('Content-Type: text/html; charset=utf-8');
error_reporting(E_ERROR | E_PARSE);


echo "<pre>";

$id = $_REQUEST['id'];
$lang = $_REQUEST['lang'];

$ccm = ClicCatalegManager::getInstance();
if(!$id){
	$list = $ccm->obtenirListIdClicXTEC($id);
	
	print_r($list);
}
else{
	$list_clic = $ccm->obtenirClicXTEC($id,$lang);
	foreach($list_clic as $clic){
		$ccm->guardarClic($clic);
		//echo $clic->getSQL()."<br>";
	}
	//print_r($clic->getJSONEncode());
}


// get last two segments of host name

function getIdFromUrl($url){
	preg_match('/(id=)(.*)/', $url, $res);
	return $res[2];
}
?>
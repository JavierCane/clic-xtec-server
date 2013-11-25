<?
include_once "class/includes.php";

header('Content-Type: text/plain; charset=utf-8');
//error_reporting(E_ERROR | E_PARSE);

$lang = $_GET['lang'];
$lang_cataleg = $_GET['lang_cataleg'];
$inici = (is_numeric($_GET['inici']))?$_GET['inici']:0;
$limit = (is_numeric($_GET['limit']))?$_GET['limit']:50;
$nivell = $_GET['nivell'];
$area = $_GET['area'];

if(!$lang_cataleg){
    die('USAGE: /cataleg/?lang=LANG{&lang_cataleg=LANG_CATALEG}{&limit=LIMIT}{&inici=INICI}');
}

if(HTML::getHTTPHeader("GPSAUTH") != md5("GPS_PASSWORD")){
	die("No estas autoritzat");
}

$ccm = CtrlClicCataleg::getInstance();

if ($nivell != '' or $area != '') {
	//crida custom per recuperar els clicks en funcio dels filtres especificats
	$clicsFiltres = $ccm->getClicsFiltres($lang, $inici, $limit, $nivell, $area);
} else {
	$llista = $ccm->getAllClics($lang, $inici, $limit);	
}

$res = array();
foreach($llista as $clic){
	array_push($res, $clic->getPublicClass($lang_cataleg));
}

$json = json_encode($res);

if($_GET['pretty']){
    echo PrettyJSON::pretty_json($json);
}
else{
    echo $json;
}

?>
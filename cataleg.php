<?
include_once "class/includes.php";

header('Content-Type: text/plain; charset=utf-8');
//error_reporting(E_ERROR | E_PARSE);

$lang = $_GET['lang'];
$inici = (is_numeric($_GET['inici']))?$_GET['inici']:0;
$limit = (is_numeric($_GET['limit']))?$_GET['limit']:50;

if(!$lang){
    die('USAGE: /cataleg/?lang=LANG{&limit=LIMIT}{&inici=INICI}');
}


$ccm = CtrlClicCataleg::getInstance();

$llista = $ccm->getAllClics($inici, $limit);

$res = array();
foreach($llista as $clic){
	array_push($res, $clic->getPublicClass($lang));
}

$json = json_encode($res);

if($_GET['pretty']){
    echo PrettyJSON::pretty_json($json);
}
else{
    echo $json;
}

?>
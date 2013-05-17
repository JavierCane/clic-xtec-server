<?
include "class/includes.php";

header('Content-Type: text/plain; charset=utf-8');
//error_reporting(E_ERROR | E_PARSE);

$lang = $_REQUEST['lang'];
$inici = (is_numeric($_REQUEST['inici']))?$_REQUEST['inici']:0;
$limit = (is_numeric($_REQUEST['limit']))?$_REQUEST['limit']:50;

if(!$lang){
	die("Falta parametre lang");
}

$ccm = CtrlClicCataleg::getInstance();

$llista = $ccm->getAllClics($inici, $limit);

$res = array();
foreach($llista as $clic){
	array_push($res, $clic->getPublicClass($lang));
}
echo pretty_json(json_encode($res));

function pretty_json($json) {
    $result = '';
    $pos = 0;
    $strLen = strlen($json);
    $indentStr = ' ';
    $newLine = "\n";
    $prevChar = '';
    $outOfQuotes = true;
     
    for ($i=0; $i<=$strLen; $i++) {
     
    // Grab the next character in the string.
    $char = substr($json, $i, 1);
     
    // Are we inside a quoted string?
    if ($char == '"' && $prevChar != '\\') {
    $outOfQuotes = !$outOfQuotes;
     
    // If this character is the end of an element,
    // output a new line and indent the next line.
    } else if(($char == '}' || $char == ']') && $outOfQuotes) {
    $result .= $newLine;
    $pos --;
    for ($j=0; $j<$pos; $j++) {
    $result .= $indentStr;
    }
    }
     
    // Add the character to the result string.
    $result .= $char;
     
    // If the last character was the beginning of an element,
    // output a new line and indent the next line.
    if (($char == ',' || $char == '{' || $char == '[') && $outOfQuotes) {
    $result .= $newLine;
    if ($char == '{' || $char == '[') {
    $pos ++;
    }
     
    for ($j = 0; $j < $pos; $j++) {
    $result .= $indentStr;
    }
    }
     
    $prevChar = $char;
    }
     
    return $result;
}
?>
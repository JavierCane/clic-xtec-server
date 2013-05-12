<?
include "class/includes.php";

header('Content-Type: text/html; charset=utf-8');
error_reporting(E_ALL ^ E_NOTICE);//E_ERROR | E_PARSE);
ini_set('max_execution_time', 0);

echo "<pre>";

$id = $_REQUEST['id'];
$lang = $_REQUEST['lang'];
$lang2 = $_REQUEST['lang2'];

$ccm = ClicCatalegManager::getInstance();
if(!$id){
	$list = $ccm->obtenirListIdClicXTEC($id);
	
	// Esborrem tots els antics:
	$ccm->deleteAllClics();
	$i = 1;
	$array_batch = array();
	foreach($list as $item){
		$id = $item['id'];
		echo $id . " lang: ".implode(",", $item['lang'])."<br>";
		foreach($item['lang'] as $lang){
			$list_clic = $ccm->obtenirClicXTEC($id,$lang);
			foreach($list_clic as $clic){
				echo $i ." Guardem el clic ".$clic->titol['es']." (id: ".$clic->id.")<br>";
				array_push($array_batch, $clic->getPreparedStatementsArray());
				$i++;
			}
		}
		if($i % 100 == 0){
			// Cada 100  guardem els clics
			$ccm->guardarBatchClics($array_batch);
			$array_batch = array();
		}
	}
	$ccm->guardarBatchClics($array_batch);
}
else{
	$list_clic = $ccm->obtenirClicXTEC($id,$lang);
	$res = array();
	foreach($list_clic as $clic){
		$ccm->guardarClic($clic);
		array_push($res, $clic->getPublicClass($lang2));
	}
	echo pretty_json(json_encode($res));
}


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
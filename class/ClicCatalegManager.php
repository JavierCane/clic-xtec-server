<?
class ClicCatalegManager{
	public $conversio = array(	"el"=>"grec","ca"=>"català","eu"=>"basc","es"=>"espanyol",
								"gl"=>"gallec","en"=>"anglès","sv"=>"suec","rmq"=>"caló","de"=>"alemany",
								"pt"=>"portuguès", "fr"=>"francès","eo"=>"esperanto","it"=>"italià",
								"la"=>"llatí","oc"=>"occità","zh"=>"xinès","arn"=>"araucà","ro"=>"romanès"
							);
    public $idiomes = array("es","ca","en");
    public $url_base = "http://clic.xtec.cat/db/";
	public $url_llista = "http://clic.xtec.cat/db/listact_ca.jsp?num=100000";

    public function __construct() {
    }

	public function getAllClics($inici = 0, $limit = 50){
		$db = DBhelper::getInstance();
		$sql = "SELECT * FROM ".ClicCataleg::$TAULA." WHERE 1 LIMIT ".intval($inici).", ".intval($limit)."";
		$list = $db->fetchAllPreparedStatement($sql, array());
		$res = array();
		foreach($list as $row){
			$clic = new ClicCataleg();
			$clic->rowMapper($row);
			array_push($res, $clic);
		}
		return $res;
	}
	
	public function obtenirListIdClicXTEC(){
		$res = array();
		
		$html = HTML::do_GET($this->url_llista);
		$dom = new DOMDocument();
		libxml_use_internal_errors(true);
		$dom->loadHTML($html);
		$xpath = new DOMXPath($dom);

		$links = $xpath->query('//*[@class="taulaSenar" or class="taulaParell"]/td/a');
		foreach ($links as $link) {
			$id = $this->getIdFromUrl($link->getAttribute("href"));
			$parentNode = $link->parentNode->parentNode;
			$langs = $xpath->query('td[3]', $parentNode)->item(0)->textContent;
			// L'string d'idioma té està separat per caràcters estranys (en hex: c2a0)
			// Per tant partim per aquests caracters, i ens quedem amb tots els idiomes excepte
			// el primer.
			$lang_arr = explode($langs[0].$langs[1],trim($langs));
			array_shift($lang_arr);
			array_push($res,array("id"=>$id,"lang"=>$lang_arr));
		}
		return $res;
	}
	
	public function getIdFromUrl($url){
		preg_match('/(id=)(.*)/', $url, $res);
		return $res[2];
	}
	
	public function obtenirClicXTEC($id,$lang){
		$res = array();
		$llista_clic_inst = array();
		$clic = new ClicCataleg();
		$clic->id = $id;
		$primer_cop = true;
		foreach($this->idiomes as $idioma){
			$html = HTML::do_GET($this->url_base."act_".$idioma.".jsp?id=".$id);
			$dom = new DOMDocument();
			libxml_use_internal_errors(true);
			$dom->loadHTML($html);
			$xpath = new DOMXPath($dom);

			$main = $xpath->query('//td[@class="main"]')->item(0);

			$clic->addTitol($idioma, $xpath->query('*[@class="titol"]', $main)->item(0)->nodeValue);
			$clic->addDescripcio($idioma, $xpath->query('*[@class="desc"]', $main)->item(0)->nodeValue);
			
			// Com que obtenim les dades dels tres idiomes, les dades que no canvien les obtenim només el primer cop
			if($primer_cop){
				$primer_cop = false;
				$clic->autors = $xpath->query('*[@class="autors"]', $main)->item(0)->nodeValue;
				$dom_logo = $xpath->query('*[@class="container"]/img', $main)->item(0);
				if($dom_logo){
					$clic->logoUrl = $dom_logo->getAttribute("src");
				}
				$clic->area = $xpath->query('table/tr/td[@class="taulaInfoCol"]', $main)->item(0)->nodeValue;
				$clic->nivell = $xpath->query('table/tr/td[@class="taulaInfoCol"]', $main)->item(1)->nodeValue;
				$clic->llicencia = $xpath->query('table/tr/td[@class="taulaInfoCol"]', $main)->item(3)->nodeValue;
			}
			// Els .inst del clic només l'obtenim un cop, i ha de ser de la pàgina en català
			// ja que l'array $this->conversio el tenim amb el valor en català
			if($idioma == "ca"){
				$list_url_items = $xpath->query('//*/td[@class="url"]', $main);

				foreach($list_url_items as $url_items){
					$node_arrel = $url_items->parentNode->parentNode;
					$title_parts = explode(" - ",$xpath->query('tr/td[@class="fBox"]', $node_arrel)->item(0)->nodeValue);
					$clic_idioma = $title_parts[count($title_parts)-1];
					if($clic_idioma == $this->conversio[$lang]){
						if(count($title_parts) > 2){
							$titol_adicional = $title_parts[count($title_parts)-3];
						}
						$url_clic_element = $xpath->query('tr[2]/td[1]/a[3]', $node_arrel)->item(0);
						if($url_clic_element){
							$url_clic = $url_clic_element->getAttribute("href");
							$url_clic = str_replace("http://clic.xtec.cat/jnlp/jclic/install.jnlp?argument=","",$url_clic);
							$llista_clic_inst[$url_clic] = $titol_adicional;
						}
					}
				}
			}
		}
		foreach($llista_clic_inst as $clic_inst => $titol){
			$t = clone $clic;
			$t->urlBase = $this->extractBaseURLFromInst($clic_inst);
			$t->inst = str_replace($t->urlBase,"",$clic_inst);
			if($titol){
				$t->appendTitol(" - ".$titol);
			}
			$this->obtenirClicsFromXMLInst($t);
			array_push($res,$t);
		}
set_time_limit(10000);
		return $res;
	}
	
	public function extractBaseURLFromInst($url){
		preg_match('/(.*\/)/', $url, $parts);
		return $parts[0];
	}
	
	public function obtenirClicsFromXMLInst($clic){
		$html = HTML::do_GET($clic->urlBase . $clic->inst);
		$dom = new DOMDocument();
		$dom->loadHTML($html);
		$xpath = new DOMXPath($dom);

		$clic->clicPrincipal = $xpath->query('//shortcut')->item(0)->getAttribute("project");
		$list_clic = $xpath->query('//file');
		foreach($list_clic as $t){
			$src = $t->getAttribute("src");
			if(!$this->compareEndString($src, ".inst") && $src != $clic->clicPrincipal){
				$clic->addClicAdicional($src);
			}
		}
	}
    
	public function guardarClic($clic){
		$db = DBhelper::getInstance();
		
		try {
			$num = $db->exec($clic->getSQL());
		} catch (Exception $e) {
			echo "Failed: " . $e->getMessage();
		}
	}
	
	public function guardarBatchClics($array_batch){
		$db = DBhelper::getInstance();
		try {
			$db->execBatchPreparedStatement(ClicCataleg::$SQL, $array_batch);
		} catch (Exception $e) {
			echo "Failed: " . $e->getMessage();
		}
	}
	
	public function deleteAllClics(){
		$db = DBhelper::getInstance();
		$db->exec("DELETE FROM ".ClicCataleg::$TAULA."");
	}
	
	private function compareEndString($str, $end){
		return substr_compare($str, $end, -strlen($end), strlen($end)) === 0;
	}
	
	private function trimUnicode($s){
		return preg_replace('/^\p{Z}+|\p{Z}+$/u', '', $s);
	}
	
    /*  -----  Singleton pattern ----- */

    // singleton instance (es crida: $t = CLASSNAME::getInstance();)
    private static $instance;

    // getInstance method
    public static function getInstance() {

            if(!self::$instance) {
                    self::$instance = new self();
            }
            return self::$instance; 
    }
}
?>
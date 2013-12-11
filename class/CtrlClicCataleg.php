<?
class CtrlClicCataleg{
	public $conversio = array(	
								"el"=>"grec","ca"=>"català","eu"=>"basc","es"=>"espanyol",
								"gl"=>"gallec","en"=>"anglès","sv"=>"suec","rmq"=>"caló","de"=>"alemany",
								"pt"=>"portuguès", "fr"=>"francès","eo"=>"esperanto","it"=>"italià",
								"la"=>"llatí","oc"=>"occità","zh"=>"xinès","arn"=>"araucà","ro"=>"romanès"
							);

	public $areas = array(
							"lleng"=>"Llengües",
							"mat"=>"Matemàtiques",
							"soc"=>"Ciències socials",
							"exp"=>"Ciències experimentals",
							"mus"=>"Música",
							"vip"=>"Visual i plàstica",
							"ef"=>"Educació física",
							"tec"=>"Tecnologies",
							"div"=>"Diversos"
					);

	public $nivells = array(
							"INF"=>"Infantil (3-6)",
							"PRI"=>"Primària (6-12)",
							"SEC"=>"Secundària (12-16)",
							"BTX"=>"Batxillerat (16-18)"
						);

    public $idiomes = array("es","ca","en");
    public $url_base = "http://clic.xtec.cat/db/";
    public $url_llista = "http://clic.xtec.cat/db/listact_ca.jsp?num=100&from=";

    public function __construct() {
    }

    /** Function to get clics by search filters
     * @param $lang Clics language
     * @param $inici First row to select
     * @param $limit Limit the number of results
     * @param $nivell Clic grade
     * @param $area Clic area
     * @param $cerca Search by clic name
     *
     *
     * @return array Array of all clics matching the query
     */
    public function getClicsFiltres($lang, $inici, $limit, $nivell, $area, $cerca) {

    	$db = DBhelper::getInstance();
		
		//creem el filtre per recuperar les instancies
		$where_conditions_array_nivel = array();
		$where_conditions_array_area = array();
		$where_conditions_array = array();
		$where_conditions_values = array();

        if ( !empty ( $nivell ) ) {
            $nivells = explode( ',', $nivell );
            foreach( $nivells as $niv ){
                $where_conditions_array_nivel[] = 'nivel_' . $niv . ' = :nivell' . $niv;
                $where_conditions_values[':nivell'.$niv] = 1;
            }
            $where_conditions_array[] = '(' . implode( $where_conditions_array_nivel, ' OR ' ) . ')';

        }

        if ( !empty ( $area ) ) {
            $areas = explode( ',', $area );
            foreach( $areas as $a ){
                $where_conditions_array_area[] = 'area_' . $a . ' = :area' . $a;
                $where_conditions_values[':area'.$a] = 1;
            }
            $where_conditions_array[] = '(' . implode( $where_conditions_array_area, ' OR ' ) . ')';
        }

        if ( !empty ( $lang ) ) {
            $langs = explode( ',', $lang );
            $where_conditions_array[] = 'lang in (\'' . implode( "','", $langs ) . '\')';
        }

        if ( !empty ( $cerca ) ) {
            $where_conditions_array[] = '(titol like :cerca or descripcio like :cerca)';
            $where_conditions_values[':cerca'] = '%' . $cerca . '%';
        }

        $where = implode( $where_conditions_array, " AND " );

		$sql = "SELECT * FROM " . ClicCataleg::$TAULA . " WHERE " . $where . " LIMIT " . intval( $inici ) . ", " . intval( $limit );
		$list = $db->fetchAllPreparedStatement( $sql, $where_conditions_values );
		$res = array();
		foreach( $list as $row ){
			$clic = new ClicCataleg();
			$clic->rowMapper( $row );
			array_push( $res, $clic );
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
		$clic->lang = $lang;
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
				
				$area = $xpath->query('table/tr/td[@class="taulaInfoCol"]', $main)->item(0)->nodeValue;
				$clic->area = $this->extractArea($area);

				$nivell = $xpath->query('table/tr/td[@class="taulaInfoCol"]', $main)->item(1)->nodeValue;
				$clic->nivell = $this->extractNivell($nivell);

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
		return $res;
	}

	public function extractArea($area){
		return $area;
		$match = explode(",", $area);
		array_walk_recursive($match, "trim");
		return $match;
	}

	public function extractNivell($nivell){
		return $nivell;
		$match = explode(",", $nivell);
		array_walk_recursive($match, "trim");
		return $match;
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
		$anchor = $xpath->query('//shortcut')->item(0);
		if($anchor){
			$clic->clicPrincipal = $anchor->getAttribute("project");
		}
		else{
			echo "No s'ha trobat fitxer principal del clic id:".$clic->id." titol".$clic->titol['ca'];
		}
		$list_clic = $xpath->query('//file');
		foreach($list_clic as $t){
			$src = $t->getAttribute("src");
			if(!$this->compareEndString($src, ".inst") && $src != $clic->clicPrincipal){
                if( $this->compareEndString($src, ".zip") ){
				    $clic->addClicAdicional($src);
			    }
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

    /** Function to insert clics into the database, and update the rows to allow filtering
     *
     * @param $array_values Values to insert into the database
     *
     * @return int Number of rows added
     */
    public function guardarInsertMutliple($array_values){
		$clic = new ClicCataleg();
		$header = $clic->getSQLValues(1);
		$values = implode($array_values, ",");
		$sql = "REPLACE INTO ".ClicCataleg::$TAULA." ".$header." VALUES ".$values ." ";
		$db = DBhelper::getInstance();
		
		try {
			$num = $db->exec($sql);
			if($num < count($array_values)){
				echo "S'han intentat insertar ".count($array_values)." quan realment s'han fet $num canvisa<br>";
				print_r($db->errorInfo());
			}
		} catch (Exception $e) {
			echo "Failed: " . $e->getMessage();
		}

        try{
            /**
             * Query to update the database setting the booleans to filter the clics by area and grade
             */
            $db->exec(" UPDATE " . ClicCataleg::$TAULA . " SET area_lleng = b'1' WHERE area LIKE '%Lenguas%';
                        UPDATE " . ClicCataleg::$TAULA . " SET area_mat = b'1' WHERE area LIKE '%Matemáticas%';
                        UPDATE " . ClicCataleg::$TAULA . " SET area_soc = b'1' WHERE area LIKE '%Ciencias sociales%';
                        UPDATE " . ClicCataleg::$TAULA . " SET area_exp = b'1' WHERE area LIKE '%Ciencias experimentales%';
                        UPDATE " . ClicCataleg::$TAULA . " SET area_mus = b'1' WHERE area LIKE '%Música%';
                        UPDATE " . ClicCataleg::$TAULA . " SET area_vip = b'1' WHERE area LIKE '%Plástica y visual%';
                        UPDATE " . ClicCataleg::$TAULA . " SET area_ef = b'1' WHERE area LIKE '%Educación física%';
                        UPDATE " . ClicCataleg::$TAULA . " SET area_tec = b'1' WHERE area LIKE '%Tecnología%';
                        UPDATE " . ClicCataleg::$TAULA . " SET area_div = b'1' WHERE area LIKE '%Diversos%';

                        UPDATE " . ClicCataleg::$TAULA . " SET nivel_INF = b'1' WHERE nivell LIKE '%Infantil (3-6)%';
                        UPDATE " . ClicCataleg::$TAULA . " SET nivel_PRI = b'1' WHERE nivell LIKE '%Primaria (6-12)%';
                        UPDATE " . ClicCataleg::$TAULA . " SET nivel_SEC = b'1' WHERE nivell LIKE '%Secundaria (12-16)%';
                        UPDATE " . ClicCataleg::$TAULA . " SET nivel_BTX = b'1' WHERE nivell LIKE '%Bachillerato (16-18)%';
                        ");

        } catch (Exception $e) {
            echo "Failed: " . $e->getMessage();
        }

		return $num;
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
<?
class ClicCatalegManager{
    public $idiomes = array("es","ca","en");
    public $url_base = "http://clic.xtec.cat/db/";
	public $url_llista = "http://clic.xtec.cat/db/listact_ca.jsp?num=100000";

    function __construct() {
    }

	public function obtenirListIdClicXTEC(){
		$res = array();
		
		$html = HTML::do_GET($this->url_llista);
		$dom = new DOMDocument();
		$dom->loadHTML($html);
		$xpath = new DOMXPath($dom);

		$links = $xpath->query('//*[@class="taulaSenar" or class="taulaParell"]/td/a');
		foreach ($links as $link) {
			$id = $this->getIdFromUrl($link->getAttribute("href"));
			//echo "<a href='?id=".$id."'>".$link->nodeValue."</a><br>";
			array_push($res,$id);
		}
		return $res;
	}
	
	public function getIdFromUrl($url){
		preg_match('/(id=)(.*)/', $url, $res);
		return $res[2];
	}
	
	public function obtenirClicXTEC($id){
		$clic = new ClicCataleg();
		$clic->id = $id;
		$primer_cop = 1;
		foreach($this->idiomes as $lang){
			$html = HTML::do_GET($this->url_base."act_".$lang.".jsp?id=".$id);
			$dom = new DOMDocument();
			$dom->loadHTML($html);
			$xpath = new DOMXPath($dom);

			$main = $xpath->query('//td[@class="main"]')->item(0);

			$clic->addTitol($lang, $xpath->query('*[@class="titol"]', $main)->item(0)->nodeValue);
			$clic->addDescripcio($lang, $xpath->query('*[@class="desc"]', $main)->item(0)->nodeValue);
			
			if($primer_cop){
				$primer_cop = 0;
				$clic->autors = $xpath->query('*[@class="autors"]', $main)->item(0)->nodeValue;
				$clic->logoUrl = $xpath->query('*[@class="container"]/img', $main)->item(0)->getAttribute("src");
				$clic->area = $xpath->query('table/tr/td[@class="taulaInfoCol"]', $main)->item(0)->nodeValue;
				$clic->nivell = $xpath->query('table/tr/td[@class="taulaInfoCol"]', $main)->item(1)->nodeValue;
				$clic->llicencia = $xpath->query('table/tr/td[@class="taulaInfoCol"]', $main)->item(3)->nodeValue;

				$list_url_items = $xpath->query('//*/td[@class="url"]', $main);
				
				foreach($list_url_items as $url_items){
					$node_arrel = $url_items->parentNode->parentNode;
					$title_url = $xpath->query('tr/td[@class="fBox"]', $node_arrel)->item(0)->nodeValue;
					$url_clic = $xpath->query('input[@type="text"]', $url_items)->item(1)->getAttribute("value");
					$clic->addJClic(array($url_clic => $title_url));
				}
			}
		}

		return $clic;
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
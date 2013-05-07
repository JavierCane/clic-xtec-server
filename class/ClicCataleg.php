<?
class ClicCataleg{
	public static $TAULA = "DROIDCLIC_clics";
	private $id;
	private $titol;
	private $descripcio;
	
	private $autors;
	private $llicencia;
	private $nivell;
	private $area;
	private $logoUrl;
	private $urlBase;
	private $inst;
	private $clicPrincipal;
	private $clicsAdicionals;
	
	function __construct() {
	    $this->titol = array();
	    $this->descripcio = array();
	    $this->clicsAdicionals = array();
	}
	
    public function __set($name, $value){
        $this->$name = trim($value);
    }

    public function __get($name){
        return $this->$name;
    }
	
	public function addClicAdicional($o){
		array_push($this->clicsAdicionals, $o);
	}
	
	public function addTitol($lang, $o){
		$this->titol[$lang] = $o;
	}
	
	public function appendTitol($s){
		foreach($this->titol as $k => $t){
			$this->titol[$k] .= $s;
		}
	}
	
	public function addDescripcio($lang, $o){
		$this->descripcio[$lang] = $o;
	}
	
	public function getJSONEncode() {
		return json_encode(get_object_vars($this));
	}
	
	public function getSQL(){

		foreach($this as $var => $value) {
			$part1 .= $var ."," ;
			if(!is_array($value)){
				$part2 .= "'".addslashes($value)."',";
			}
			else{
				$part2 .= "'".addslashes(json_encode($value))."',";
			}
		}
		$part1 = substr($part1, 0, -1);
		$part2 = substr($part2, 0, -1);
		
		$sql = "REPLACE INTO ".ClicCataleg::$TAULA." (".$part1.") VALUES (".$part2 .") ";
		return $sql;
	}
	
}
?>
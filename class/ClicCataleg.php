<?
class ClicCataleg{
	public static $TAULA = "DROIDCLIC_clics";
	public static $SQL = "REPLACE INTO DROIDCLIC_clics (id,titol,descripcio,autors,llicencia,nivell,area,logoUrl,urlBase,inst,clicPrincipal,clicsAdicionals,tipusActivitats) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?) ";
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
	private $tipusActivitats;
	
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
	
	public function getPublicClass($lang) {
		$t = new T();
		$t->id = $this->id;
		$t->titol = $this->titol[$lang];
		$t->descripcio = $this->descripcio[$lang];
		$t->autors = $this->autors;
		$t->llicencia = $this->llicencia;
		$t->nivell = $this->nivell;
		$t->area = $this->area;
		$t->logoUrl = $this->logoUrl;
		$t->urlBase = $this->urlBase;
		//$t->inst = $this->inst;
		$t->clicPrincipal = $this->clicPrincipal;
		$t->clicsAdicionals = $this->clicsAdicionals;
		$t->tipusActivitats = $this->tipusActivitats;
		return $t;
	}
	
	public function getPreparedStatementsArray(){
		return array(
			addslashes($this->id), 
			addslashes(json_encode($this->titol)), 
			addslashes(json_encode($this->descripcio)), 
			addslashes($this->autors),
			addslashes($this->llicencia), 
			addslashes($this->nivell),
			addslashes($this->area),
			addslashes($this->logoUrl),
			addslashes($this->urlBase),
			addslashes($this->inst),
			addslashes($this->clicPrincipal),
			addslashes(json_encode($this->clicsAdicionals)),
			addslashes(json_encode($this->tipusActivitats))
		);
	}

	public function getSQLValues($header = 0){
		if($header){
			foreach($this as $var => $value) {
				$res .= $var ."," ;
			}
		}
		else{
			foreach($this as $var => $value) {
				if(!is_array($value)){
					$res .= "'".addslashes($value)."',";
				}
				else{
					$res .= "'".addslashes(json_encode($value))."',";
				}
			}
		}
		return "(".substr($res, 0, -1).")";
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
	public function rowMapper($row){
		$this->id = $row['id'];
		$this->titol = json_decode($row['titol'], true);
		$this->descripcio = json_decode($row['descripcio'], true);
		$this->autors = $row['autors'];
		$this->llicencia = $row['llicencia'];
		$this->nivell = $row['nivell'];
		$this->area = $row['area'];
		$this->logoUrl = $row['logoUrl'];
		$this->urlBase = $row['urlBase'];
		$this->inst = $row['inst'];
		$this->clicPrincipal = $row['clicPrincipal'];
		$this->clicsAdicionals = json_decode($row['clicsAdicionals'], true);
		$this->tipusActivitats = json_decode($row['tipusActivitats'], true);
	}
}
?>
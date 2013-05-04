<?
class ClicCataleg{
	private $id;
	private $titol;
	private $descripcio;
	
	private $autors;
	private $llicencia;
	private $nivell;
	private $area;
	private $logoUrl;
	private $llistaJClics;
	
	function __construct() {
	    $this->titol = array();
	    $this->descripcio = array();
	    $this->llistaJClics = array();
	}
	
    public function __set($name, $value){
        $this->$name = trim($value);
    }

    public function __get($name){
        return $this->$name;
    }
	
	public function addJClic($o){
		array_push($this->llistaJClics, $o);
	}
	
	public function addTitol($lang, $o){
		array_push($this->titol, $o);
	}
	
	public function addDescripcio($lang, $o){
		array_push($this->descripcio, $o);
	}
	
	public function getJSONEncode() {
		return json_encode(get_object_vars($this));
	}
}
?>
<?
include_once "constants.php";



class DBhelper{
    private $db;
	
	private $DB_DRIVER = "mysql";
	private $DB_HOST = "db468423667.db.1and1.com";
	private $DB_DBNAME = "db468423667";
	private $DB_USERNAME = "dbo468423667";
	private $DB_PASSWORD = "FIB_GPS_SERVER";
    
    function __construct() {
		$this->db = new PDO(''.$this->DB_DRIVER.':host='.$this->DB_HOST.';dbname='.$this->DB_DBNAME.';charset=utf8', 
							$this->DB_USERNAME, $this->DB_PASSWORD);
		$this->db -> exec("SET CHARACTER SET utf8");
    }

    public function query($sql){
        return $this->db->query($sql); // Pot tirar exec
    }
   
    public function lastInsertId(){
        return $this->db->lastInsertId();
    }   
   
    public function exec($sql){
        return $this->db->exec($sql); // Retorna el numero de rows modificades
    }
	
	/*
	$array_values pot ser:
	- SELECT * FROM table WHERE id=? AND name=? -> array($id, $name)
	- SELECT * FROM table WHERE id=:id AND name=:name -> array(':name' => $name, ':id' => $id)
	*/
	public function fetchAllPreparedStatement($sql, $array_values){
		$stmt = $this->db->prepare($sql);
		$stmt->execute($array_values);
		return $stmt->fetchAll(PDO::FETCH_ASSOC);
	}
	
	/* $array_values és similar al de fetchAllPreparedStatement */
	public function execPreparedStatement($sql, $array_values){
		$stmt = $this->db->prepare($sql);
		$stmt->execute($array_values);
		return $stmt->rowCount();
	}
	
	public function commit(){
        return $this->db->commit();
	}
	
	public function rollback(){
        return $this->db->rollback();
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
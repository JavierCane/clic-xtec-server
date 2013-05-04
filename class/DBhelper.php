<?
include_once "constants.php";

$DB_DRIVER = "mysql";
$DB_HOST = "db468423667.db.1and1.com";
$DB_DBNAME = "db468423667";
$DB_USERNAME = "dbo468423667";
$DB_PASSWORD = "FIB_GPS_SERVER";

class DBhelper{
    private $db;
    
    function __construct() {
		$this->db = new PDO(''.$DB_DRIVER.':host='.$DB_HOST.';dbname='.$DB_DBNAME.';charset=utf8', $DB_USERNAME, $DB_PASSWORD);
    }

    public function query($sql){
        return $db->query($sql); // Pot tirar exec
    }
   
    public function lastInsertId(){
        return $db->lastInsertId();
    }   
   
    public function exec($sql){
        return $db->exec($sql); // Retorna el numero de rows modificades
    }
	
	/*
	$array_values pot ser:
	- SELECT * FROM table WHERE id=? AND name=? -> array($id, $name)
	- SELECT * FROM table WHERE id=:id AND name=:name -> array(':name' => $name, ':id' => $id)
	*/
	public function fetchAllPreparedStatement($sql, $array_values){
		$stmt = $db->prepare($sql);
		$stmt->execute($array_values);
		return $stmt->fetchAll(PDO::FETCH_ASSOC);
	}
	
	/* $array_values és similar al de fetchAllPreparedStatement */
	public function execPreparedStatement($sql, $array_values){
		$stmt = $db->prepare($sql);
		$stmt->execute($array_values);
		return $stmt->rowCount();
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
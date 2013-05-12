<?
include_once "constants.php";

/*
Les constants han d'estar definides al fitxer /class/constants.php:

<?

define("DB_DRIVER", "mysql");
define("DB_HOST", "xxxx");
define("DB_DBNAME", "xxxx");
define("DB_USERNAME", "xxxxx");
define("DB_PASSWORD", "xxxx");

?>

*/


class DBhelper{
    private $db;

    
    function __construct() {
		$this->db = new PDO(''.DB_DRIVER.':host='.DB_HOST.';dbname='.DB_DBNAME.';charset=utf8', 
							DB_USERNAME, DB_PASSWORD);
		$this->db->exec("SET CHARACTER SET utf8");
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
	
	/* $array_values és un array d'arrays similars al de execPreparedStatement */
	public function execBatchPreparedStatement($sql, $array_batch){
		$stmt = $this->db->prepare($sql);
		foreach($array_batch as $array_values){
			$stmt->execute($array_values);
		}
	}

	public function beginTransaction(){
        return $this->db->beginTransaction();
	}
	
	public function commit(){
        return $this->db->commit();
	}
	
	public function rollback(){
        return $this->db->rollback();
	}
/*
    echo "\nPDO::errorInfo():\n";
    print_r($stmt->errorInfo());
*/
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
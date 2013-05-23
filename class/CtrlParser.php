<?
class CtrlParser{
    public $ctrlClicCataleg;

    public function __construct() {
		$this->ctrlClicCataleg = CtrlClicCataleg::getInstance();
    }

	public function run(){
		$commit = true;
		$this->startRun();
		$start = (float) array_sum(explode(' ',microtime())); 
		$ccm = $this->ctrlClicCataleg;
		$list = $ccm->obtenirListIdClicXTEC();
		// Esborrem tots els antics:
		$ccm->deleteAllClics();
		$i = 1;
		$array_batch = array();
		foreach($list as $item){
			$id = $item['id'];
			foreach($item['lang'] as $lang){
				$list_clic = $ccm->obtenirClicXTEC($id,$lang);
				foreach($list_clic as $clic){
					array_push($array_batch, $clic->getSQLValues());
					$i++; 
					echo "$i [". sprintf("%.4f", (((float) array_sum(explode(' ',microtime())))-$start))."]<br>";
                    ob_flush();
				}
			}
			if(count($array_batch) > 50){
				$num = $ccm->guardarInsertMutliple($array_batch);
				echo "Guardem "+$num;
				$array_batch = array();
			}
		}
		$ccm->guardarInsertMutliple($array_batch);
		$this->endRun($commit);
	}

	private function startRun(){
		$db = DBhelper::getInstance();
		$db->beginTransaction();
	}
	
	private function endRun($commit){
		$db = DBhelper::getInstance();
		if($commit){
			$db->commit();
		}
		else{
			$db->rollback();
		}
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
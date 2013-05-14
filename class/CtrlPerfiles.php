<?php
require_once('Perfil.php');

class CtrlPerfiles{

    private $perfiles;
    private $ruta_json = "/Users/danielbalasteguijulian/Desktop/UNIVERSIDAD/GPS/clic-xtec-server/class/perfiles.json";
    private static $instance;

    public static function getInstance() {
        if(!self::$instance) {
                self::$instance = new self();
        }
        return self::$instance; 
    }

    function __construct() {
        $perfiles = array();
        $json = file_get_contents($this->ruta_json);
        $json_a = json_decode($json, true);
        foreach ($json_a as $extension => $p) {
            $perfil = new Perfil($p['width'],$p['height'],$extension);
            array_push($perfiles, $perfil);
        }
        usort($perfiles, array("Perfil", "cmp_obj"));
        $this->perfiles = $perfiles;
    }

    //Selecciona el perfil más apropiado para
    //el dispositivo con resolucion Width x Height
    public function getDadesPerfil($width, $height){
        if(!$width or !$height){
            die('Faltan las resoluciones de pantalla');
        }
        $perfiles = $this->perfiles;
        if (!$perfiles) {
            die('No existen perfiles');
        }
        for($i = 0; $i < count($perfiles); $i += 1){
            $anterior = $perfiles[$i];
            if($width <= $perfiles[$i]->width) break;
        }
        return array($anterior->width,$anterior->height,$anterior->extension);
    }
}
?>
<?php
require_once('class/includes.php');

require_once('class/pclzip.lib.php');
require_once('class/functionsDroid.php');

//Calidad de la imagen final
$CALIDAD = 70;


if (!$_GET['url'] || !$_GET['width'] || !$_GET['height']) {
    die('USAGE: /clic/?url=URL&width=WIDTH&height=HEIGHT');
}


//Recogemos parametros
$file = $_GET['url'];
$perfiles = CtrlPerfiles::getInstance();
list($width,$height,$ext) = $perfiles->getDadesPerfil($_GET['width'],$_GET['height']);

//Extraemos el nombre del archivo de la url
$name = explode("/", $file);

//Nombre del archivo temporal zip
$tmpFile = $name[count($name)-1];

//Extraemos el nombre del archivo sin extensiones
$name = substr($name[count($name)-1],0,-10);

//Añadimos nuestra extensión dependiendo del tipo de optimizacion
$finalName = $name.".".$ext.".zip";

mkdir($name, 0700);

//Descargamos el archivo copiandolo en el disco
if (!copy($file, $tmpFile)) {
    die('Fallo al descargar el archivo');
}

$zip = new PclZip($tmpFile);
$nuevo_zip = new PclZip($finalName);
//Extraemos los archivos
$zip->extract(PCLZIP_OPT_PATH , $name);

$dir = opendir($name);
    // Leo todos los ficheros de la carpeta
while ($elemento = readdir($dir)){
    // Tratamos los elementos . y .. que tienen todas las carpetas
    if( $elemento != "." && $elemento != ".."){
        //Si no es una carpeta
        if( !is_dir($name.$elemento) ){
            $extension = pathinfo($elemento,PATHINFO_EXTENSION);
            //Comprobamos que es una imagen
            if(preg_match("/^(gif|jpg|jpeg|png)$/i",$extension)){
                //Optimizamos la imagen
                resizeAndOptimize($name."/".$elemento,$width,$height,$CALIDAD);
            }
        } 
    }
}
//Eliminamos el archivo temporal
unlink($tmpFile);
rmdir($name);

//Comprimimos los archivos optimizados
$nuevo_zip->create($name);

// Set headers
header("Cache-Control: public");
header("Content-Description: File Transfer");
header("Content-Disposition: attachment; filename=$finalName");
header("Content-Type: application/zip");
header("Content-Transfer-Encoding: binary");
// Read the file from disk
readfile($finalName);

?>
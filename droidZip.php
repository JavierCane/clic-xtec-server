<?php

require_once('pclzip.lib.php');
require_once('functionsDroid.php');

/* ---------CONSTANTES-----------*/
$CALIDAD = 70; //En porcentage
/* ----------MOVIL----------*/
$MOBILEWIDTH = 480;
$MOBILEHEIGHT = 320;
$EXTENSION_MOBILE = "dclic-m";
/* ----------TABLET----------*/
$TABLETWIDTH=1024;
$TABLETHEIGHT = 760;
$EXTENSION_TABLET = "dclic-t";
/* --------------------*/

if (!$_GET['url']) {
    die('Falta la url - Ej: droidZip.php?url=URL&tipus=NUM');
}
else if (!$_GET['tipus']) {
    die('Falta el tipus - Ej: droidZip.php?url=URL&tipus=NUM');
}

//Recogemos parametros
$file = $_GET['url'];
$tipus = $_GET['tipus'];

//Segun el tipo optimizamos para mobil o para tablet
switch ($tipus) 
{
    case 1: 
    $width = $MOBILEWIDTH;
    $height = $MOBILEHEIGHT;
    $ext = $EXTENSION_MOBILE;
    break;  

    case 2: 
    $width = $TABLETWIDTH;
    $height = $TABLETHEIGHT;
    $ext = $EXTENSION_TABLET;
    break;   
}

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
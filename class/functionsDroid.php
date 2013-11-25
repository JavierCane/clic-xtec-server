<?php
function resizeAndOptimize($srcFile,$maxSize = -1,$maxSizeH = -1,$quality = 70, $newName = '') {  
    list($width_orig, $height_orig, $type) = getimagesize($srcFile);        
    if (!$newName) $newName = $srcFile;
    // Get the aspect ratio
    $ratio_orig = $width_orig / $height_orig;
    if($maxSize == -1)$width  = $width_orig;
    else{
        $width = $maxSize;
    } 
    if ($maxSizeH == -1)$height = $height_orig;
    else{
        $height = $maxSizeH;
    }
    
    if (($width_orig > $width AND $height_orig <= $height)
     OR ($height_orig > $height AND $width_orig <= $width)
     OR ($width_orig > $width AND $height_orig > $height)) {
    // Resize to height (original is portrait) 
        if ($ratio_orig < 1) {
            $width = $height * $ratio_orig;
        } 
    // Resize to width (original is landscape)
        else {
            $height = $width / $ratio_orig;
        }

    // Segun el tipo de imagen (jpg,gif,png)
        switch ($type) 
        {
            case IMAGETYPE_GIF: 
            $image = imagecreatefromgif($srcFile); 
            break;   
            case IMAGETYPE_JPEG: 
            $image = imagecreatefromjpeg($srcFile); 
            break;   
            case IMAGETYPE_PNG: 
            $image = imagecreatefrompng($srcFile);
            break; 
            default:
            throw new Exception('Unrecognized image type ' . $type);
        }
    //Redimensiona creando una imagen en blanco y copiandola.
        $newImage = imagecreatetruecolor($width, $height);
        imagecopyresampled($newImage, $image, 0, 0, 0, 0, $width, $height, $width_orig, $height_orig);
        imagejpeg($newImage, $newName,$quality);                           
        imagedestroy($newImage);
    }
    
}
?>
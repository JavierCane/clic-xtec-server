<?php
class Perfil{

    private $width;
    private $height;
    private $extension;


    function __construct($width,$height,$extension) {
        $this->width = $width;
        $this->height = $height;
        $this->extension = $extension;

    }

    public function __get($name){
        return $this->$name;
    }

    public function getDades(){
        return array($width, $height, $extension);
    }

    static function cmp_obj($a, $b)
    {
        $al = strtolower($a->width);
        $bl = strtolower($b->width);
        if ($al == $bl) {
            return 0;
        }
        return ($al > $bl) ? +1 : -1;
    }
}
?>
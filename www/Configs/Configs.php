<?php

class Configs{

    public static function GetConfigs($fileName){
    //le dossier configs est situé au même niveau que le dossier WWWW

        $path = $_SERVER['DOCUMENT_ROOT']."../../configs/";
        
        return json_decode(file_get_contents($path.$fileName));
    }

}



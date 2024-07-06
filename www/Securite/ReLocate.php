<?php

Class ReLocate{

    public static function To($url){
        header("Location: $url");
        exit();
    }
    
}

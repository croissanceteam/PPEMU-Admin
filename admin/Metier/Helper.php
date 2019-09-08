<?php


Class Helper{

    public static function ngonga($format =  'Y-m-d H:i:s'){
        $tz = "Africa/Kinshasa";
        $date = new DateTime($tz);
        $date->setTimezone(new DateTimeZone($tz));
        return $date->format($format);
    }
}
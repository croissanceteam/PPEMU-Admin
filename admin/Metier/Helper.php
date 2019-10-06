<?php


Class Helper{

    public static function ngonga($format =  'Y-m-d H:i:s'){
        $tz = "Africa/Kinshasa";
        $date = new DateTime($tz);
        $date->setTimezone(new DateTimeZone($tz));
        return $date->format($format);  
    }

    public static function getURL($deepLoss = 0){
        if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') 
        $url = "https"; 
        else
            $url = "http"; 

            // Ajoutez // à l'URL.
        $url .= "://"; 
            
        // Ajoutez l'hôte (nom de domaine, ip) à l'URL.
        $url .= $_SERVER['HTTP_HOST']; 
        
        // Ajouter l'emplacement de la ressource demandée à l'URL
        $tab = explode('/',$_SERVER['REQUEST_URI']);
        $deep =  count($tab);
        $deep -= $deepLoss;
        //$url .= $_SERVER['REQUEST_URI']; 
        for ($i=1; $i < $deep; $i++) { 
            $url .= '/'.$tab[$i];
        }
        // Afficher l'URL    
        return $url; 
    }
}
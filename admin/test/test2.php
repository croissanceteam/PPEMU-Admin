<?php
include_once '../Metier/Database.php';
//$database = new Database();
function get(){
    $database = new Database();
    $rs = $database->query("SELECT * FROM t_user");
    return $rs->rowCount();
}

function set(){
    $database = new Database();
    $rs = $database->query("UPDATE t_user SET town=?,phone=? WHERE username=?",['KINSAHSA','093939390','joh']);
    return $rs->rowCount();
}

function getURI($deepLoss = 0){
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
    echo $url; 
    
}

//echo var_dump(set());
$deepless = 0;
echo getURI(2);
//echo $_SERVER['REQUEST_URI'];
$tab = explode('/',$_SERVER['REQUEST_URI']);
$deep =  count($tab);
$deep -= $deepless;
//echo  $deep;
$uri = "";
for ($i=1; $i < $deep; $i++) { 
    $uri .= '/'.$tab[$i];
}

//print_r($tab);
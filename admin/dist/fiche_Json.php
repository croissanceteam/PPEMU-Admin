<?php
include('../../sync/db.php');
session_start();

if(isset($_GET['listVilles'])){
        //TODO: Affichage Liste Villes 
        
    $json= array();

    $pays=htmlentities($_GET['pays'], ENT_QUOTES);

    $req1="select nom, token from villes where pays='$pays' " ;

    $resulta=$db->query($req1);
    
    while($donn=$resulta->fetch(PDO::FETCH_ASSOC)){
        $json[$donn['token']][]=utf8_encode($donn['nom']);
    }

    echo json_encode($json);
        
}
else
{
    echo 'Please fill in all required fields. 2';
    die();
}

?>
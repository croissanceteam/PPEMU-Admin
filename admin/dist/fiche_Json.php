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
else if(isset($_GET['nbComment'])){
        //TODO: Affichage Nombre Commentaire
        
    $json= array();

    $obj=htmlentities($_GET['obj'], ENT_QUOTES);
    $type=htmlentities($_GET['type'], ENT_QUOTES);

    $req1="select count(*) as nb from commentaire where type='$type' and objet='$obj' " ;

    $resulta=$db->query($req1);
    
    while($donn=$resulta->fetch(PDO::FETCH_ASSOC)){
        $json[$donn['nb']][]=utf8_encode($donn['nb']);
    }

    echo json_encode($json);
        
}
else if(isset($_GET['obj_interesse'])){

    $rdv=htmlentities($_GET['rdv'], ENT_QUOTES);
    $user=htmlentities($_GET['user'], ENT_QUOTES);

    $QFav=$db->query("SELECT * FROM favoris WHERE rdv = '$rdv'");
    
    if ($QFav->rowCount() > 0){
        $req = $db->query("DELETE FROM favoris WHERE rdv = '$rdv';") or die ('Erreur '.$db->errorInfo());
    }
    else {
        $token="p".time();

        $req = $db->query("INSERT INTO `favoris` (`id`, `user`, `rdv`, `lastModif`) VALUES (null, '$user','$rdv', ".(time()*1000).");");
    }
        
}
else if(isset($_GET['obj_abonnement'])){

    $rdv=htmlentities($_GET['rdv'], ENT_QUOTES);
    $user=htmlentities($_GET['user'], ENT_QUOTES);

    $QFav=$db->query("SELECT * FROM abonnement WHERE rdv = '$rdv'");
    
    if ($QFav->rowCount() > 0){
        $req = $db->query("DELETE FROM abonnement WHERE rdv = '$rdv';") or die ('Erreur '.$db->errorInfo());
    }
    else {
        $token="p".time();
    
        $req = $db->query("INSERT INTO `abonnement` (`id`, `rdv`, `utilisateur`, `dateAbonn`, `status`, `lastModif`, `token`, `etat`) VALUES (null, '$rdv','$user', ".(time()*1000).", '', ".(time()*1000).", '$token', 1);");
    }
        
}
else
{
    echo 'Please fill in all required fields. 2';
    die();
}

?>
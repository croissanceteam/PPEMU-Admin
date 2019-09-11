<?php
include_once '../Metier/Autoloader.php';
Autoloader::register();
$user = new User();
if(isset($_POST['email']) && isset($_POST['sendmail'])){
    try {
        //$retour = 1;
        $retour = $user->sendToken($_POST['email']);
        if($retour == 6){
            echo json_encode(['response'    =>  "Adresse email incorrect", 'number' =>  6]);
        }else{
            echo json_encode(['response'    =>  "Un mail a été envoyé à votre adresse email. Veuillez consulter vos mails", 'number' =>  1]);
        }
    } catch (\Throwable $th) {
        echo json_encode(['response'    =>  "Echec de l'opération", 'bug' =>  $th->getMessage(), 'number'=>0]);
    }
    
}else if(isset($_POST['email']) && isset($_POST['token'])){
    
    try {
        $retour = $user->validateToken($_POST['email'],$_POST['token']);
        
        if($retour == 1){
            echo json_encode(['response'=>'Tapez maintenant votre nouveau mot de passe','number'=>1]);
        }else{
            echo json_encode(['response'=>'Code invalide','number'=>0]);
        }
    } catch (\Throwable $th) {
        echo json_encode(['response'    =>  "Echec de l'opération", 'bug' =>  $th->getMessage(), 'number'=>0]);
    }

}else if(isset($_POST['newpass'])){
    try {
        $retour = $user->setPassword(isset($_POST['newpass']));
        echo json_encode(['response'=>$retour,'number'=>1]);
    } catch (\Throwable $th) {
        echo json_encode(['response'    =>  "Echec de l'opération", 'bug' =>  $th->getMessage(), 'number'=>0]);
    }
}
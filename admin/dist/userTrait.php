<?php
include_once '../Metier/Autoloader.php';
Autoloader::register();
$user = new User();
if(isset($_POST['email']) && isset($_POST['sendmail'])){
    try {
        //$retour = 1;
        $retour = $user->sendToken($_POST['email']);
        if($retour == 1){
            echo json_encode(['response'    =>  "Un mail a été envoyé à votre adresse email. Veuillez consulter vos mails", 'number' =>  1]);
        }else if($retour == 6){
            echo json_encode(['response'    =>  "Adresse email incorrect", 'number' =>  6]);
        }else{
            echo json_encode(['response'    =>  "Le mail n'a pas pu être envoyé", 'number' =>  0]);
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

}else if(isset($_POST['newpass']) && isset($_POST['set'])){
    try {
        $rs = $user->setPassword($_POST['newpass'],$_SESSION['usrname']);
        if($rs == 1)
            echo json_encode(['response'=>"Mot de passe enregistré",'number'=>$rs]);
        else    
            echo json_encode(['response'=>"L'enregistrement a échoué",'number'=>$rs]);
        
    } catch (\Throwable $th) {
        echo json_encode(['response'    =>  "Echec de l'opération", 'bug' =>  $th->getMessage(), 'number'=>0]);
    }
}else if(isset($_POST['username']) && isset($_POST['actual-password'])){
    try {
        $rs = $user->changePassword($_POST);
        if($rs == 1)
            echo json_encode(['response'=>"Votre mot de passe a été changé avec succès. Nous vous demandons de vous reconnecter avec le nouveau mot  de passe.",'number'=>$rs]);
        else if($rs == 0)
            echo json_encode(['response'=>"Aucun changement effectué",'number'=>$rs]);
        else if($rs == 4){
            $_SESSION['nbr_changepass_try']--;
            $text = $_SESSION['nbr_changepass_try'] === 1 ? " essai." : " essais.";
            if($_SESSION['nbr_changepass_try'] === 0){
                //add a script to lock the user before logging him out.
                echo json_encode(['response'=>"Vous avez épuisé vos tentatives.",'number'=>$rs]);
            }else
                echo json_encode(['response'=>"Le mot de passe est incorrect. Veuillez retaper. Il vous reste ".$_SESSION['nbr_changepass_try'].$text,'number'=>$rs]);

        }else if($rs == 5)
            echo json_encode(['response'=>"Vous avez mal retapé le nouveau mot de passe. Veuillez réessayer.",'number'=>$rs]);
            
    } catch (\PDOException $ex) {
        echo json_encode(['response'=>"L'enregistrement a échoué",'bug'=>$ex->getMessage(),'number'=>0]);
    } catch (\Throwable $th) {
        echo json_encode(['response'=>"L'enregistrement a échoué",'bug'=>$th->getMessage(),'number'=>0]);
    }
        
}
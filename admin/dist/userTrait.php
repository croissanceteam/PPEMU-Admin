<?php
include_once '../Metier/Autoloader.php';
Autoloader::register();
$user = new User();

if(isset($_POST['usr']) && isset($_POST['pwd'])){
    try {
        $rs = $user->signin($_POST['usr'],$_POST['pwd']);
        if($rs == 1)
            echo json_encode(['response'    =>  "", 'number' =>  $rs]);
        else if ($rs == 6)
            echo json_encode(['response'    =>  "Votre compte est actuellement verrouillé", 'number' =>  $rs]);
        else if ($rs == 0)
            echo json_encode(['response'    =>  "Nom d'utilisateur ou mot de passe incorrect", 'number' =>  $rs]);
        
    } catch (\Throwable $th) {
        echo json_encode(['response'    =>  "Echec de l'opération", 'bug' =>  $th->getMessage(), 'number'=>0]);
    }
}else if(isset($_POST['email']) && isset($_POST['sendmail'])){
    try {
        //$retour = 1;
        $retour = $user->sendToken($_POST['email']);
        if($retour == 1){
            echo json_encode(['response'    =>  "Un mail a été envoyé à votre adresse email. Veuillez consulter vos mails", 'number' =>  1]);
        }else if($retour == 7){
            echo json_encode(['response'    =>  "Adresse email incorrect", 'number' =>  7]);
        }else if($retour == 6){
            echo json_encode(['response'    =>  "Votre compte est actuellement verrouillé", 'number' =>  6]);
        }else{
            echo json_encode(['response'    =>  "Le mail n'a pas pu être envoyé", 'number' =>  $retour]);
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
            echo json_encode(['response'=>"Votre mot de passe a été changé avec succès. Nous vous demandons de vous reconnecter avec le nouveau mot de passe.",'number'=>$rs]);
        else if($rs == 0)
            echo json_encode(['response'=>"Aucun changement effectué",'number'=>$rs]);
        else if($rs == 4){
            $_SESSION['nbr_changepass_try']--;
            $text = $_SESSION['nbr_changepass_try'] === 1 ? " essai." : " essais.";
            if($_SESSION['nbr_changepass_try'] === 0){
                //add a script to lock the user before logging him out.
                $rs = $user->lock($_SESSION['pseudoPsv']);
                echo json_encode(['response'=>"Vous avez épuisé vos tentatives. Votre compte est désormais vérrouillé.",'number'=>6]);
            }else
                echo json_encode(['response'=>"Le mot de passe est incorrect. Veuillez retaper. Il vous reste ".$_SESSION['nbr_changepass_try'].$text,'number'=>$rs]);

        }else if($rs == 5)
            echo json_encode(['response'=>"Vous avez mal retapé le nouveau mot de passe. Veuillez réessayer.",'number'=>$rs]);
            
    } catch (\PDOException $ex) {
        echo json_encode(['response'=>"L'enregistrement a échoué",'bug'=>$ex->getMessage(),'number'=>0]);
    } catch (\Throwable $th) {
        echo json_encode(['response'=>"L'enregistrement a échoué",'bug'=>$th->getMessage(),'number'=>0]);
    }
        
}else if(isset($_GET['list'])){
    $rs = $user->all();
    $list = [];
    $i = 0;
    while ($data = $rs->fetch()) {
        $status = ($data->status == 0)? '<i class="fa fa-lock text-danger" text-danger"></i>':'';
        $actions = ($data->priority != 'root')? '<a href="#modifier" id="'.$data->username.'" class="update" title="Modifier" data-placement="top" data-toggle="tooltip" style="margin-right:11px;" >
                                                    <i class="glyphicon glyphicon-pencil text-warning"></i>
                                                </a>
                                                <a href="#supprimer" id="'.$data->username.'" class="delete" title="Supprimer" data-placement="top" data-toggle="tooltip" style="margin-right:11px;">
                                                    <i class="glyphicon glyphicon-trash text-danger"></i>
                                                </a>':'';
        $list [] = [
            'position'  => ++$i,
            'username'  =>  $status.' '.$data->username,
            'fullname'  =>  $data->fullname,
            'email'  =>  $data->mailaddress,
            'phone'  =>  $data->phone,
            'actions'  => $actions
        ];
        
    }

    echo json_encode($list);
}else if(isset($_POST['fullname']) && isset($_POST['add'])){
    try {
        $rs = $user->add($_POST);
        if($rs == 1)
            echo json_encode(['response'=>"Utilisateur créé",'number'=>$rs]);
        else if($rs == 2)
            echo json_encode(['response'=>"Ce nom d'utilisateur n'est pas disponible",'number'=>$rs]);
        else if($rs == 3)
            echo json_encode(['response'=>"Cette adresse e-mail est déjà utilisée par un autre utilisateur",'number'=>$rs]);
        else
            echo json_encode(['response'=>"Echec",'number'=>$rs]);
    
    }catch (\Throwable $th) {
        echo json_encode(['response'=>"L'enregistrement a échoué",'bug'=>$th->getMessage(),'number'=>0]);
    }
    
}
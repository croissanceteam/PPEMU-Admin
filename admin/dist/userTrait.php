<?php
include_once '../Metier/Autoloader.php';
Autoloader::register();

if(isset($_POST['email'])){
    $user = new User();
    try {
        $retour = $user->sendToken($_POST['email']);
        echo $retour;
    } catch (\Throwable $th) {
        echo $th->getMessage();
    }
    
}
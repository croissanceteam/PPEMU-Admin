<?php

//$servername = 'mysql.hostinger.com';
//$usr = "u510270851_kapo";
//$psw = "c4conf4flicker";
//$dbname = "u510270851_kapo";

//$servername = '192.185.81.156';
//$usr = "wendylab_urapp";
//$psw = "urapp@123";
//$dbname = "wendylab_psouvenir";


$servername = 'localhost';
$usr = "root";
$psw = "root";
$dbname = "db_portal_test2";

try {
    	header("Access-Control-Allow-Origin: *");
        $db = new PDO("mysql:host=$servername;dbname=$dbname", $usr, $psw);
    	$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    	$db->exec('SET NAMES utf8');
		
		$mysqli =mysqli_connect($servername, $usr, $psw, $dbname); 
    }
catch(PDOException $e)
    {
    	die("Erreur de connexion à la BD3_upload");
    }

$link = new PDO("mysql:host=$servername;dbname=$dbname", $usr, $psw, [PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES UTF8"]);
$link->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$link->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);

?>
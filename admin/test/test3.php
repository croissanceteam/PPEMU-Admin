<?php

include_once '../Metier/Database.php';

    $db = new Database();

    $res = $db->query("INSERT INTO t_test VALUES('rat','23')");
    echo $res->rowCount();
/*
    $var = 3;
    
    if($var)
       echo 'c different';
    else
        echo 'c zero';
*/
    
//    $rep_import = $db->query("SELECT * FROM t_reperage_import");
//    $imp = $rep_import->fetchAll();
//    $t = 0;
//    
//    
//    $tab = [];
    
//    foreach ($imp as $value) {
//        //$stmt = $db->query("SELECT client,avenue,refclient FROM t_root WHERE TRIM(client) LIKE :name AND TRIM(avenue) LIKE :street",[
//        $stmt = $db->query("SELECT client,avenue,refclient FROM t_root WHERE TRIM(client) LIKE :name",[
//            'name'  =>  trim($value->name_client)."%"
//            ]);
//        if($stmt->rowCount() > 0){
//            $tab [] = $value->name_client;
//            //print $value->name_client;
//            print "<br>";
//        }else{
//            
//        }
//    }
//    
//    $get_root = $db->query("SELECT client,avenue,refclient FROM t_root");
//    $root_data = $get_root->fetchAll();
//    
//    foreach ($root_data as $root) {
//       $root_stmt = $db->query("SELECT * FROM t_reperage_import WHERE TRIM(name_client) LIKE :name_root LIMIT 1",[
//           'name_root'  =>  trim($root->client)."%"
//       ]);
//        
//       if($root_stmt->rowCount() > 0){
//           $data = $root_stmt->fetch();
//           $tab [] = $data->name_client;
//           
//            //print $root->client;
//            print "<br>";
//        }
//    }
   
//    $tab = array_unique($tab);
//    
//    
//    foreach ($tab as $value) {
//        $get_avenue = $db->query("SELECT avenue FROM t_reperage_import WHERE name_client LIKE ?",[$value]);
//        
//        print_r ($value);
//        echo "<br>";
//    }
    
    
/*
 * 
 
$var = "233";
echo "$var <br>";
echo 'Entier ? ';
var_dump(is_int($var));
echo "<br>";
echo 'Numérique ? ';
var_dump(is_int($var));
echo "<br><br>";
echo 'Chaine ? ';
var_dump(is_string($var));
echo "<br>";
echo 'Chaine mais entier ? ';
var_dump(ctype_digit(trim($var)));
echo "<br><br>";
if(is_string($var) && ctype_digit(trim($var)))
    echo "C'est une chaine composée seulement de chiffres.";
 * 
 */



// echo "Initial".date('s@W')."$";
//echo password_hash('Initial123',PASSWORD_BCRYPT);

// $date_export = "2019-09-28 19:10:06";
// echo date('d/m/Y à H:i', strtotime($date_export));

// $txt= '{"rep_updated":"'.date('d F Y, H:i:s').'"}';
// //        $txt= '{"updated":"27 Juillet 2019, 12:00:30"}';
// try {
//     $filen="../../mobile/date.json";
//     $fp = fopen($filen, 'w');
//     fwrite($fp, $txt);
//     fclose($fp);
    
//     $file=file_get_contents("../../mobile/date.json");
//     echo $file;
// } catch (\Throwable $th) {
//     echo $th->getMessage();
// }



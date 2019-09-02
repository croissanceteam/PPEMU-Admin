<?php
include('../../sync/db.php');
session_start();

class parLot1
{
    private $_lot_num;
    private $_lot_qte;
    private $_lot_dateExp;
    private $_lot_type;

    function __construct($num, $qte, $dateExp, $typeD)
    {
        $this->_lot_num = $num;
        $this->_lot_qte = $qte;
        $this->_lot_dateExp = $dateExp;
        $this->_lot_type = $typeD;
    }
}
//$monChat = new Chat("Vert","Calico");

$link_reperage = array(
                1 => "aBjGWHgmQQfrwfeKsHCnby",
                     "aT6K37chVpJ63i9zoh8YG3",
//                     "au2pD2CP4VRoqcwB5fvLzD",
//                     "ah93htLqf3qRVaidr2Payz",
//                     "a3eQQCvANCG74mvnB2erAS",
//                     "aW7Ra9JevzWbK7mEkgkTWT",
//                     "aAPHjntRb2pUnkUBHvuYox",
//                     "aLG8oDKUD5PrBFUX7VgAFe",
//                     "aDqBSG9cVvCqvk6wFdUA7T",
//                     "aqWecgKFFSsyyMDoDABKbR",
);
$link_realisation = array(
                1 => "ad9PwJdM5hgDAwv29cVvcu",
                     "avUNvXEojKeygMSJm2f45C",
//                     "aSF7cuRkL9arwcBRH94H6H",
//                     "aJPGN8NpzfUtYtwQeHC4cA",
//                     "apmdksKV6H7pYy4PwBYpZA",
//                     "a5s8yaHvohqm4p2M3F4hrS",
//                     "aWbUZrhofqDHKueW9ABojR",
//                     "abNsoE3WRpfz57d3UoCJu3",
//                     "anqTZ3bsBSHhtia3XNeGE4",
//                     "a753uDACAv9fFi6UVYhwVw",
);

//$link='https://kf.kobotoolbox.org/assets/'.$linkSub.'/submissions/?format=json';
////$link='https://kf.kobotoolbox.org/assets/aN9oVWSGeVTGhyuAxxcyr5/submissions/?format=json';
//$opts = ["http" => ["header" => "Authorization: Token 1d99e5378a5924e824d30a09d08ab26bdeb4dfe1 "]];
//
//$context = stream_context_create($opts);
//$data = file_get_contents($link, false, $context);
//
//$parsed_json = json_decode($data,true);

if(isset($_GET['traitement_api'])){
    
    if(isset($_GET['btn']) && $_GET['btn']=='api_actualise'){
        
        $json= array();
        
        $i=0;
        foreach($link_reperage as $cle=>$valeur){
            $i++;
            $link='https://kf.kobotoolbox.org/assets/'.$valeur.'/submissions/?format=json';
            $opts = ["http" => ["header" => "Authorization: Token 1d99e5378a5924e824d30a09d08ab26bdeb4dfe1 "]];
            $context = stream_context_create($opts);
            $data = file_get_contents($link, false, $context);

            $parsed_json = json_decode($data,true);
            
            $json[$i] =array($cle, count($parsed_json), 'dateExp', 'Reperage');
        }
        
        foreach($link_realisation as $cle=>$valeur){
            $i++;
            $link='https://kf.kobotoolbox.org/assets/'.$valeur.'/submissions/?format=json';
            $opts = ["http" => ["header" => "Authorization: Token 1d99e5378a5924e824d30a09d08ab26bdeb4dfe1 "]];
            $context = stream_context_create($opts);
            $data = file_get_contents($link, false, $context);

            $parsed_json = json_decode($data,true);
            
            $json[$i] =array($cle, count($parsed_json), 'dateExp', 'Realisation');
        }
        
        echo json_encode($json);
        
    }
    
    else if(isset($_GET['btn']) && $_GET['btn']=='api_downAll'){ //TELECHARGE TOUT LE DONNEE REPERAGE ET REALISATION
        
        $json= array();
        
        $i=0;
        foreach($link_reperage as $cle=>$valeur){
            $i++;
            $link='https://kf.kobotoolbox.org/assets/'.$valeur.'/submissions/?format=json';
            $opts = ["http" => ["header" => "Authorization: Token 1d99e5378a5924e824d30a09d08ab26bdeb4dfe1 "]];
            $context = stream_context_create($opts);
            $data = file_get_contents($link, false, $context);

            $parsed_json = json_decode($data,true);
            
            foreach ($parsed_json as $v) {
                $name_client=@htmlentities($v['Nom_Client'], ENT_QUOTES);
                $avenue=@htmlentities($v['Avenue_Quartier'], ENT_QUOTES);
                $num_home=@htmlentities($v['Num_ro_parcelle'], ENT_QUOTES);
                $commune=@htmlentities($v['Commune'], ENT_QUOTES);
                $phone=@htmlentities($v['Num_ro_t_l_phone'], ENT_QUOTES);
                $category=@htmlentities($v['Cat_gorie_Client'], ENT_QUOTES);
                $ref_client=@htmlentities($v['Ref_Client'], ENT_QUOTES);
                $pt_vente=@htmlentities($v['Etat_du_point_de_vente'], ENT_QUOTES);
                $geopoint=@htmlentities($v['G_olocalisation'], ENT_QUOTES);
                $lat=@htmlentities($v['_geolocation'][0], ENT_QUOTES);
                $lng=@htmlentities($v['_geolocation'][1], ENT_QUOTES);
                
                list($lat_2, $lng_2, $altitude, $precision)=explode(' ', $geopoint);
                
                $controller_name=@htmlentities($v['Nom_du_Contr_leur'], ENT_QUOTES);
                $comments=@htmlentities($v['Commentaires'], ENT_QUOTES);
                $submission_time=@htmlentities($v['_submission_time'], ENT_QUOTES);
                $town='';
                $lot=$cle;
                $date_export=date('d/m/Y');//htmlentities($v['Ref_Client'], ENT_QUOTES);

                $req = $db->query("INSERT INTO `t_reperage_import` (`id`, `name_client`, `avenue`, `num_home`, `commune`, `phone`, `category`, `ref_client`, `pt_vente`, `geopoint`, `lat`, `lng`, `altitude`, `precision`, `controller_name`, `comments`, `submission_time`, `town`, `lot`, `date_export`, `secteur`, `matching`, `error_matching`) VALUES (NULL, '$name_client', '$avenue', '$num_home', '$commune', '$phone', '$category', '$ref_client', '$pt_vente', '$geopoint', '$lat', '$lng', '$altitude', '$precision', '$controller_name', '$comments', '$submission_time', '$town', '$lot', '$date_export' , '', 0, 0 );");
                
            }
            
            
            
            $json[$i] =array($cle, count($parsed_json), 'dateExp', 'Reperage');
        }
        
//        foreach($link_realisation as $cle=>$valeur){
//            $i++;
//            $link='https://kf.kobotoolbox.org/assets/'.$valeur.'/submissions/?format=json';
//            $opts = ["http" => ["header" => "Authorization: Token 1d99e5378a5924e824d30a09d08ab26bdeb4dfe1 "]];
//            $context = stream_context_create($opts);
//            $data = file_get_contents($link, false, $context);
//
//            $parsed_json = json_decode($data,true);
//            
//            $json[$i] =array($cle, count($parsed_json), 'dateExp', 'Realisation');
//        }
        
        echo json_encode($json);
        
    }
    
    else if(isset($_GET['btn']) && $_GET['btn']=='api_afficheLot'){ //Lire et Affiche le données d un lot
        
        $json= array();
        
        $lot=htmlentities($_GET['lot'], ENT_QUOTES);
        $typeDonnee=htmlentities($_GET['typeDonnee'], ENT_QUOTES);
        
        if($typeDonnee=='Reperage')$link_sub=$link_reperage[$lot];
        else if($typeDonnee=='Realisation')$link_sub=$link_realisation[$lot];
        //die (" L ".$link_reperage[$lot]." L2".$link_sub);
        $link='https://kf.kobotoolbox.org/assets/'.$link_sub.'/submissions/?format=json';
        $opts = ["http" => ["header" => "Authorization: Token 1d99e5378a5924e824d30a09d08ab26bdeb4dfe1 "]];

        $context = stream_context_create($opts);
        $data = file_get_contents($link, false, $context);

        $parsed_json = json_decode($data,true);
        
        echo json_encode($parsed_json);
        
    }
    
    else if(isset($_GET['btn']) && $_GET['btn']=='api_TelechargeLot'){ //Télécharge le données d un lot
        
        $json= array();
        
        $lot=htmlentities($_GET['lot'], ENT_QUOTES);
        $typeDonnee=htmlentities($_GET['typeDonnee'], ENT_QUOTES);
        
        if($typeDonnee=='Reperage')$link_sub=$link_reperage[$lot];
        else if($typeDonnee=='Realisation')$link_sub=$link_realisation[$lot];
        //die (" L ".$link_reperage[$lot]." L2".$link_sub);
        $link='https://kf.kobotoolbox.org/assets/'.$link_sub.'/submissions/?format=json';
        $opts = ["http" => ["header" => "Authorization: Token 1d99e5378a5924e824d30a09d08ab26bdeb4dfe1 "]];

        $context = stream_context_create($opts);
        $data = file_get_contents($link, false, $context);

        $parsed_json = json_decode($data,true);
        
            
        foreach ($parsed_json as $v) {
            $name_client=@htmlentities($v['Nom_Client'], ENT_QUOTES);
            $avenue=@htmlentities($v['Avenue_Quartier'], ENT_QUOTES);
            $num_home=@htmlentities($v['Num_ro_parcelle'], ENT_QUOTES);
            $commune=@htmlentities($v['Commune'], ENT_QUOTES);
            $phone=@htmlentities($v['Num_ro_t_l_phone'], ENT_QUOTES);
            $category=@htmlentities($v['Cat_gorie_Client'], ENT_QUOTES);
            $ref_client=@htmlentities($v['Ref_Client'], ENT_QUOTES);
            $pt_vente=@htmlentities($v['Etat_du_point_de_vente'], ENT_QUOTES);
            $geopoint=@htmlentities($v['G_olocalisation'], ENT_QUOTES);
            $lat=@htmlentities($v['_geolocation'][0], ENT_QUOTES);
            $lng=@htmlentities($v['_geolocation'][1], ENT_QUOTES);

            list($lat_2, $lng_2, $altitude, $precision)=explode(' ', $geopoint);

            $controller_name=@htmlentities($v['Nom_du_Contr_leur'], ENT_QUOTES);
            $comments=@htmlentities($v['Commentaires'], ENT_QUOTES);
            $submission_time=@htmlentities($v['_submission_time'], ENT_QUOTES);
            $town='';
            $lot=$lot;
            $date_export=date('d/m/Y');//htmlentities($v['Ref_Client'], ENT_QUOTES);

            $req = $db->query("INSERT INTO `t_reperage_import` (`id`, `name_client`, `avenue`, `num_home`, `commune`, `phone`, `category`, `ref_client`, `pt_vente`, `geopoint`, `lat`, `lng`, `altitude`, `precision`, `controller_name`, `comments`, `submission_time`, `town`, `lot`, `date_export`, `secteur`, `matching`, `error_matching`) VALUES (NULL, '$name_client', '$avenue', '$num_home', '$commune', '$phone', '$category', '$ref_client', '$pt_vente', '$geopoint', '$lat', '$lng', '$altitude', '$precision', '$controller_name', '$comments', '$submission_time', '$town', '$lot', '$date_export' , '', 0, 0 );");

        }

        $json[$i] =array($lot, count($parsed_json), 'dateExp', $typeDonnee);
        
        
        echo json_encode($json);
        
    }
    
    else if(isset($_GET['btn']) && $_GET['btn']=='api_afficheTout0'){ 
        //Lire Affiche tout le données (Par reperage ou Realisation)
        
        $json= array();
        
        $typeDonnee=htmlentities($_GET['typeDonnee'], ENT_QUOTES);
        $i=0;
        
        if($typeDonnee=='Reperage'){
            
            foreach($link_reperage as $cle=>$valeur){
                $i++;
                $link='https://kf.kobotoolbox.org/assets/'.$valeur.'/submissions/?format=json';
                $opts = ["http" => ["header" => "Authorization: Token 1d99e5378a5924e824d30a09d08ab26bdeb4dfe1 "]];
                $context = stream_context_create($opts);
                $data = file_get_contents($link, false, $context);

                $parsed_json = json_decode($data,true);

                foreach ($parsed_json as $v) {
                    $name_client=@htmlentities($v['Nom_Client'], ENT_QUOTES);
                    $avenue=@htmlentities($v['Avenue_Quartier'], ENT_QUOTES);
                    $num_home=@htmlentities($v['Num_ro_parcelle'], ENT_QUOTES);
                    $commune=@htmlentities($v['Commune'], ENT_QUOTES);
                    $phone=@htmlentities($v['Num_ro_t_l_phone'], ENT_QUOTES);
                    $category=@htmlentities($v['Cat_gorie_Client'], ENT_QUOTES);
                    $ref_client=@htmlentities($v['Ref_Client'], ENT_QUOTES);
                    $pt_vente=@htmlentities($v['Etat_du_point_de_vente'], ENT_QUOTES);
                    $geopoint=@htmlentities($v['G_olocalisation'], ENT_QUOTES);
                    $lat=@htmlentities($v['_geolocation'][0], ENT_QUOTES);
                    $lng=@htmlentities($v['_geolocation'][1], ENT_QUOTES);

                    list($lat_2, $lng_2, $altitude, $precision)=explode(' ', $geopoint);

                    $controller_name=@htmlentities($v['Nom_du_Contr_leur'], ENT_QUOTES);
                    $comments=@htmlentities($v['Commentaires'], ENT_QUOTES);
                    $submission_time=@htmlentities($v['_submission_time'], ENT_QUOTES);
                    $town='';
                    $lot=$cle;
                    $date_export=date('d/m/Y');//htmlentities($v['Ref_Client'], ENT_QUOTES);

                    $req = $db->query("INSERT INTO `t_reperage_import` (`id`, `name_client`, `avenue`, `num_home`, `commune`, `phone`, `category`, `ref_client`, `pt_vente`, `geopoint`, `lat`, `lng`, `altitude`, `precision`, `controller_name`, `comments`, `submission_time`, `town`, `lot`, `date_export`, `secteur`, `matching`, `error_matching`) VALUES (NULL, '$name_client', '$avenue', '$num_home', '$commune', '$phone', '$category', '$ref_client', '$pt_vente', '$geopoint', '$lat', '$lng', '$altitude', '$precision', '$controller_name', '$comments', '$submission_time', '$town', '$lot', '$date_export' , '', 0, 0 );");

                }



                $json[$i] =array($cle, count($parsed_json), 'dateExp', 'Reperage');
            }
        }
        else if($typeDonnee=='Realisation'){
//            foreach($link_realisation as $cle=>$valeur){
//                $i++;
//                $link='https://kf.kobotoolbox.org/assets/'.$valeur.'/submissions/?format=json';
//                $opts = ["http" => ["header" => "Authorization: Token 1d99e5378a5924e824d30a09d08ab26bdeb4dfe1 "]];
//                $context = stream_context_create($opts);
//                $data = file_get_contents($link, false, $context);
//
//                $parsed_json = json_decode($data,true);
//                $json[$i] =$parsed_json;
//            }
        }
        
        echo json_encode($json);
        
    }
    
    else if(isset($_GET['btn']) && $_GET['btn']=='api_telechargeTout0'){ 
        //TELECHARGE tout le données (Par reperage ou Realisation)
        
        $json= array();
        
        $typeDonnee=htmlentities($_GET['typeDonnee'], ENT_QUOTES);
        $i=0;
        
        if($typeDonnee=='Reperage'){
            
            foreach($link_reperage as $cle=>$valeur){
                $i++;
                $link='https://kf.kobotoolbox.org/assets/'.$valeur.'/submissions/?format=json';
                $opts = ["http" => ["header" => "Authorization: Token 1d99e5378a5924e824d30a09d08ab26bdeb4dfe1 "]];
                $context = stream_context_create($opts);
                $data = file_get_contents($link, false, $context);

                $parsed_json = json_decode($data,true);
                $json[$i] =$parsed_json;
            }
        }
        else if($typeDonnee=='Realisation'){
            foreach($link_realisation as $cle=>$valeur){
                $i++;
                $link='https://kf.kobotoolbox.org/assets/'.$valeur.'/submissions/?format=json';
                $opts = ["http" => ["header" => "Authorization: Token 1d99e5378a5924e824d30a09d08ab26bdeb4dfe1 "]];
                $context = stream_context_create($opts);
                $data = file_get_contents($link, false, $context);

                $parsed_json = json_decode($data,true);
                $json[$i] =$parsed_json;
            }
        }
        
        
        //die (" L ".$link_reperage[$lot]." L2".$link_sub);
//        $link='https://kf.kobotoolbox.org/assets/'.$link_sub.'/submissions/?format=json';
//        $opts = ["http" => ["header" => "Authorization: Token 1d99e5378a5924e824d30a09d08ab26bdeb4dfe1 "]];
//
//        $context = stream_context_create($opts);
//        $data = file_get_contents($link, false, $context);
//
//        $parsed_json = json_decode($data,true);
        
        echo json_encode($json);
        
    }
    
    else if(isset($_GET['btn']) && $_GET['btn']=='api_actualise2'){ //Lire le données d un lot
        
        $json= array();
        $link='https://kf.kobotoolbox.org/assets/aN9oVWSGeVTGhyuAxxcyr5/submissions/?format=json';
        $opts = ["http" => ["header" => "Authorization: Token 1d99e5378a5924e824d30a09d08ab26bdeb4dfe1 "]];

        $context = stream_context_create($opts);
        $data = file_get_contents($link, false, $context);

        $parsed_json = json_decode($data,true);
        $i=0;
        
//        foreach ($parsed_json as $v) {
//            //echo $v['Nom_du_Contr_leur'].'<br>';
//            $json[$i][]=$v['Ref_Client'];
//            $i++;
//         }
        
        
//        $i=0;
//        $parsed_json = json_decode($data);
//        foreach ($parsed_json as $v ) {
//            //echo $v['Ref_Client'].'<br>';
//            $json[$i][]=$v['Ref_Client'];
//            $i++;
//        }
        
        
//        for ($i=1;$i>=3; $i++){
//            $json[$i][]="Nombre de ligne "
//        }
        echo count($parsed_json);
        //echo json_encode($parsed_json);
        
    }
}
else
{
    echo 'Please fill in all required fields. 2';
    die();
}

?>
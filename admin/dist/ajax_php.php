<?php
@session_start();
@include"../../sync/db.php";

function random_Code($length) 
{
    $key = '';
    $keys = @array_merge(@range(0, 10));

    for ($i = 0; $i < $length; $i++) 
    {
        $key .= $keys[@array_rand($keys)];
    }   

    return $key;
}


if(@isset($_POST['pst']) && $_POST['pst']=="save_ImportCSV")
{ 
    //require_once 'excel/PHPExcel/IOFactory.php';
    
   if($_FILES['csv']['type']=="application/vnd.ms-excel" || $_FILES['csv']['type']=="text/comma-separated-values" || $_FILES['csv']['type']=="text/csv")
      { 
        $target_path = "../../uploads/";
        $target_path = $target_path . basename( $_FILES['csv']['name']); 
        if(move_uploaded_file($_FILES['csv']['tmp_name'], $target_path)) 
        {
//            $excel = PHPExcel_IOFactory::load($target_path);
//            $writer= PHPExcel_IOFactory::createWriter($excel, 'CSV');
//            $writer->setDelimiter(";");
//            $writer->setEnclosure("");
//            $writer->save($target_path.date('dmyhis').'pemu.csv');

            //$filePath=$target_path.date('dmyhis').'pemu.csv';

            if (($handle = @fopen("$target_path", "r")) !== FALSE) 
            {
                $req=false;
                $ct=0;
                $compt=0;
                while (($data = @fgetcsv($handle, 1000, ";")) !== FALSE) 
                {
                    if($ct<1){ } else {

                        $name_client=htmlentities($data[1], ENT_QUOTES);
                        $avenue=htmlentities($data[2], ENT_QUOTES);
                        $num_home=htmlentities($data[3], ENT_QUOTES);
                        $commune=htmlentities($data[4], ENT_QUOTES);
                        $phone=htmlentities($data[5], ENT_QUOTES);
                        $category=htmlentities($data[6], ENT_QUOTES);
                        $ref_client=htmlentities($data[7], ENT_QUOTES);
                        $pt_vente=htmlentities($data[8], ENT_QUOTES);
                        $geopoint=htmlentities($data[9], ENT_QUOTES);
                        $lat=htmlentities($data[10], ENT_QUOTES);
                        $lng=htmlentities($data[11], ENT_QUOTES);
                        $altitude=htmlentities($data[12], ENT_QUOTES);
                        $precision=htmlentities($data[13], ENT_QUOTES);
                        $controller_name=htmlentities($data[14], ENT_QUOTES);
                        $comments=htmlentities($data[15], ENT_QUOTES);
                        $submission_time=htmlentities($data[16], ENT_QUOTES);
                        $town=htmlentities($data[17], ENT_QUOTES);
                        $lot=htmlentities($data[18], ENT_QUOTES);
                        $date_export=htmlentities($data[19], ENT_QUOTES);
                        $secteur=htmlentities($data[20], ENT_QUOTES);
                        
                        $req = $db->query("INSERT INTO `t_reperage_import` (`id`, `name_client`, `avenue`, `num_home`, `commune`, `phone`, `category`, `ref_client`, `pt_vente`, `geopoint`, `lat`, `lng`, `altitude`, `precision`, `controller_name`, `comments`, `submission_time`, `town`, `lot`, `date_export`, `secteur`, `matching`, `error_matching`) VALUES (NULL, '$name_client', '$avenue', '$num_home', '$commune', '$phone', '$category', '$ref_client', '$pt_vente', '$geopoint', '$lat', '$lng', '$altitude', '$precision', '$controller_name', '$comments', '$submission_time', '$town', '$lot', '$date_export' , '$secteur', 0, 0 );");
                        
                        if($req) $compt++;
                    }
                    $ct++;
                } 
                @fclose($handle);
                
                if($req)
                {
                    echo '<div class="alert alert-success alert-dismissible" role="alert">
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                <h3><strong>SUCCESS !!</strong> IMPORTATION avec succès. </h3>
                                
                                <h4> '.$compt.' Données Importée(s) </h4>
                            </div> ';
                }
                else
                {
                    echo '<div class="alert alert-warning alert-dismissible" role="alert">
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                <h3><strong>ECHEC !!</strong> FICHIER non Importés... Re-essayer plus tard. </h3>
                            </div> ';
                }
                
            }
//            @unlink($filePath);
//            @unlink($target_path);
        }
      }else{ 
          echo '<div class="block margin-bottom">
            <h3 class="block-title red-gradient glossy">ECHEC</h3>
            <div class="with-padding">
                <h3>TYPE DE FICHIER INCORECT... RE-ESSAYER en changeant de fichier</h3>
            </div>
        </div>';
      }

}

else if(@isset($_POST['pst']) && $_POST['pst']=="save_publicite")
{ 
//    $titre=htmlentities($_POST['titre'], ENT_QUOTES);
//    $lien=htmlentities($_POST['lien'], ENT_QUOTES);
//    $duree=htmlentities($_POST['duree'], ENT_QUOTES);
//    
//    $dossier = '../../uploads/pubs/';
//    
//    if(!is_uploaded_file($_FILES["photoAdd_1"]["tmp_name"]))$affich="pub.png";
//    else {
//        $affich = 'pub'.time().$_FILES['photoAdd_1']['type'];
//        $affich = str_replace("image/",".",$affich);
//        move_uploaded_file($_FILES['photoAdd_1']['tmp_name'], $dossier . $affich);
//    }
//    
//    $duree=$duree*3600;
//    $token="p".time();
//    
//    $pubQ="INSERT INTO `publicite`(`id`, `titre`, `lien`, `avatar`, `datePub`, `duree`, `par`, `lastModif`, `token`, `etat` ) VALUES (NULL,'$titre','$lien', '".$affich."', ".(time()*1000).", '$duree', '$_SESSION[pseudoPsv]', ".time().", '".$token."', 1);";
//    $req = $db->query($pubQ);
//    
//    if($req)
//    {
//        echo '<div class="alert alert-success alert-dismissible" role="alert">
//                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
//                    <h4><strong>SUCCESS !!</strong> PUBLICITE Ajouter avec succès. Veuillez actualiser la Page</h4>
//                </div> ';
//    }
//    else
//    {
//        echo '<div class="alert alert-warning alert-dismissible" role="alert">
//                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
//                    <h4><strong>ECHEC !!</strong> PUBLICITE non Enregistrer... Re-essayer plus tard. </h4>
//                </div> ';
//    }

}

else echo 'ERROR 000, Re-essayer plus tard.';

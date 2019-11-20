<?php
session_start();

include_once '../Metier/Autoloader.php';
Autoloader::register();

$link_reperage = array(
                1 => "aBjGWHgmQQfrwfeKsHCnby",
                     "au2pD2CP4VRoqcwB5fvLzD",
    		         "aT6K37chVpJ63i9zoh8YG3",
                     "ah93htLqf3qRVaidr2Payz",
                     "a3eQQCvANCG74mvnB2erAS",
                     "aW7Ra9JevzWbK7mEkgkTWT",
                     "aAPHjntRb2pUnkUBHvuYox",
                     "aLG8oDKUD5PrBFUX7VgAFe",
                     "aDqBSG9cVvCqvk6wFdUA7T",
                     "aqWecgKFFSsyyMDoDABKbR",
);
$link_realisation = array(
                1 => "ad9PwJdM5hgDAwv29cVvcu",
                     "avUNvXEojKeygMSJm2f45C",
                     "aSF7cuRkL9arwcBRH94H6H",
                     "aJPGN8NpzfUtYtwQeHC4cA",
                     "apmdksKV6H7pYy4PwBYpZA",
                     "a5s8yaHvohqm4p2M3F4hrS",
                     "aWbUZrhofqDHKueW9ABojR",
                     "abNsoE3WRpfz57d3UoCJu3",
                     "anqTZ3bsBSHhtia3XNeGE4",
                     "a753uDACAv9fFi6UVYhwVw",
);

$helper = new Helper();

$reperage = new Reperage();
$realisation = new Realisation();
$rapportOp = new RapportOperation();

if(isset($_GET['traitement_api'])){
    
    $lastDate = 0;
    $lastDate_2 = 0;  // Dernier date Realisation
    
    //ACTUALISATION PAR LOT REPERAGE ET REALISATION
    if(isset($_GET['btn']) && $_GET['btn']=='api_actualiseLot'){ 
        $json= array();
        $nbrligne;
        $lot=htmlentities($_GET['lot'], ENT_QUOTES);
        $typeDonnee=htmlentities($_GET['typeDonnee'], ENT_QUOTES);
        
        try {
            if($typeDonnee=='Reperage'){
                if(array_key_exists($lot, $link_reperage))
                    $link_sub=$link_reperage[$lot];
                else 
                    throw new Exception("Lot $lot Non trouvé! ");
                
                $lastDate=$reperage->getLastSubmissionTime($lot);
            }
            else if($typeDonnee=='Realisation'){
                if(array_key_exists($lot, $link_realisation))
                    $link_sub=$link_realisation[$lot];
                else 
                    throw new Exception("Lot $lot Non trouvé! ");
                
                $lastDate=$realisation->getLastSubmissionTime($lot);;
            }
            
            $lastTIMESTAMP = strtotime($lastDate);
            $link='https://kf.kobotoolbox.org/assets/'.$link_sub.'/submissions/?format=json';
            $opts = ["http" => ["header" => "Authorization: Token 1d99e5378a5924e824d30a09d08ab26bdeb4dfe1 "]];
            $context = stream_context_create($opts);
            //$data = file_get_contents($link, false, $context);
            
            if(!@file_get_contents($link, false, $context))
                throw new Exception("Pas de Connexion Internet ! ");
            else
                $data = @file_get_contents($link, false, $context);

            $parsed_json = json_decode($data,true);
            
            $nbrligne=0;
            $jsonKobo= array();
            // FILTRING DATA FOR ELIMINATING THOSE ONE WAS PREVIOUSLY STORED
            foreach ($parsed_json as $v) {
                $submission_time=@htmlentities($v['_submission_time'], ENT_QUOTES);
                if(strtotime($lastDate) < strtotime($submission_time)){
                    $jsonKobo[]=$v;
                    //$jsonKobo[]=$v;
                    $nbrligne++; //echo $v['_submission_time'];
                }
            }
            
            $lastDatAffichE = ($lastDate == 0) ? date('d/m/Y', strtotime($parsed_json[0]['_submission_time'])) : date('d/m/Y', $lastTIMESTAMP);
            
            $json[] =array($lot, $nbrligne, $lastDatAffichE, $typeDonnee, $jsonKobo);
            //throw new Exception(" contains the word name");
            echo json_encode($json);
            $type = ($typeDonnee == 'Reperage') ? 'référencements' : 'branchements';
            //$detailOp="Synchronisation API $typeDonnee par $_SESSION[nomsPsv], resultat : $nbrligne Importé(s)";

            $req = $rapportOp->saveRapport([ //ok
                'user' => $_SESSION['nomsPsv'],
                'operation' => "Synchronisation des $type",
                'detail_operation' => "$nbrligne ligne(s) à importer",
                'lot' => $lot,
                'total_reper_before' => NULL,
                'total_reperImport_before' => NULL,
                'total_cleaned_found' => NULL,
                'total_cleaned_afected' => NULL,
                'total_reper_after' =>NULL,
                'total_reperImport_after' => NULL,
                'total_match_found' => NULL,
                'total_match_afected' => NULL,
                'total_noObs' => NULL,
                'total_doublon' => NULL,
                'total_noObs_doublon' => NULL,
                'total_noMatch' => NULL,
                'dateOperation' => $helper->ngonga('d/m/Y à H:i:s'),
            ]);
        } catch (Exception $e) {
            //echo json_encode(array($lot, "Error", $typeDonnee, "Echec Synchronisation!" ));
            echo json_encode(array($lot, "Error", $typeDonnee, $e->getMessage() ));
        }
        
    }
    
    //Télécharge le données d un lot REPERAGE ok (REALISATION pas de table prevue)
    else if(isset($_GET['btn']) && $_GET['btn']=='api_TelechargeLot'){ 
        
        $json= array();
        $nbrligne;
        
        $lot=htmlentities($_GET['lot'], ENT_QUOTES);
        $typeDonnee=htmlentities($_GET['typeDonnee'], ENT_QUOTES);
        $finTour=htmlentities($_GET['finTour'], ENT_QUOTES); //Pour savoir la fin du tour de telechargement
            
        if($typeDonnee=='Reperage'){

            $nbrligne=0;
            $v = json_decode($_GET['row'],true);
                    
            $geopoint= isset($v['G_olocalisation']) ? $v['G_olocalisation'] : $v['Golocalisation'];

            $name_client= isset($v['Nom_Client']) ? $v['Nom_Client'] : $v['NomClient'];

            $avenue= isset($v['Avenue_Quartier']) ? $v['Avenue_Quartier'] : $v['AvenueQuartier'];
            $secteur= isset($v['secteur']) ? $v['secteur'] : "";

            $num_home= isset($v['Num_ro_parcelle']) ? $v['Num_ro_parcelle'] : $v['Numparcelle'];
            $phone= isset($v['Num_ro_t_l_phone']) ? $v['Num_ro_t_l_phone'] : $v['Numphone'];
            $category= isset($v['Cat_gorie_Client']) ? $v['Cat_gorie_Client'] : $v['CatgorieClient'];
            $pt_vente= isset($v['Etat_du_point_de_vente']) ? $v['Etat_du_point_de_vente'] : $v['Etatpvente'];
            $idkobo = isset($v['_id']) ? $v['_id'] : NULL;

            if(isset($v['Ref_Client'])) $ref_client=@htmlentities($v['Ref_Client'], ENT_QUOTES);
            else if(isset($v['Numero_site'])) $ref_client=@htmlentities($v['Numero_site'], ENT_QUOTES);
            else $ref_client=@htmlentities($v['numsite'], ENT_QUOTES);

            if(isset($v['Nom_du_Contr_leur'])) $controller_name=@htmlentities($v['Nom_du_Contr_leur'], ENT_QUOTES);
            else if(isset($v['consultant'])) $controller_name=@htmlentities($v['consultant'], ENT_QUOTES);
            else $controller_name="";

            list($lat_2, $lng_2, $altitude, $precision)=explode(' ', $geopoint);

            $req = $reperage->tempSave([
                    'name_client'   =>  $name_client,
                    'avenue'        =>  $avenue,
                    'num_home'      =>  $num_home,
                    'commune'       =>  @htmlentities($v['Commune'], ENT_QUOTES),
                    'phone'         =>  $phone,
                    'category'      =>  $category,
                    'ref_client'    =>  $ref_client,
                    'pt_vente'      =>  $pt_vente,
                    'geopoint'      =>  $geopoint,
                    'lat'           =>  $lat_2,
                    'lng'           =>  $lng_2,
                    'altitude'      =>  $altitude, 
                    'precision'     =>  $precision,
                'controller_name'   =>  $controller_name,
                'comments'          =>  @htmlentities($v['Commentaires'], ENT_QUOTES),
                'submission_time'   =>  @htmlentities($v['_submission_time'], ENT_QUOTES),
                'town'              =>  '', 
                'date_export'       =>  $helper->ngonga(),
                'secteur'               =>  $secteur,
                'lot'               =>  $lot,
                'idkobo'            =>  $idkobo               
            ]);
        }
            
        if($typeDonnee=='Realisation'){

            $nbrligne=0;
            $v = json_decode($_GET['row'],true);
                    
            $geopoint= $v['Emplacement_du_branchement_r_alis'];
            $idkobo = isset($v['_id']) ? $v['_id'] : NULL;

            list($lat_2, $lng_2, $altitude, $precision)=explode(' ', $geopoint);
            try {
                $req = $realisation->tempSave([
                    'commune'       =>  @htmlentities($v['Commune'], ENT_QUOTES),
                    'address'       =>  @htmlentities($v['Quartier'], ENT_QUOTES),
                    'avenue'        =>  @htmlentities($v['Avenue'], ENT_QUOTES),
                    'num_home'      =>  @htmlentities($v['Num_ro'], ENT_QUOTES),
                    'phone'         =>  @htmlentities($v['T_l_phone'], ENT_QUOTES),
                    'town'          =>  @htmlentities($v['Ville'], ENT_QUOTES),
                    'type_branch'   =>  @htmlentities($v['Branchement_Social_ou_Appropri'], ENT_QUOTES),
                    'water_given'   =>  "",
                    'entreprise'    =>  @htmlentities($v['Entreprise_qui_a_r_alis_le_branchement'], ENT_QUOTES),
                    'consultant'    =>  @htmlentities($v['Consultant_qui_a_suivi_l_ex_cution_KIN'], ENT_QUOTES),
                    'geopoint'      =>  $geopoint,
                    'lat'           =>  $lat_2,
                    'lng'           =>  $lng_2,
                    'altitude'      =>  $altitude, 
                    'precision'     =>  $precision,
                'comments'          =>  @htmlentities($v['Commentaires'], ENT_QUOTES),
                'submission_time'   =>  @htmlentities($v['_submission_time'], ENT_QUOTES),
                'ref_client'        =>  @htmlentities($v['num_site'], ENT_QUOTES),
                'client'            =>  @htmlentities($v['Nom_du_Client'], ENT_QUOTES),
                'date_export'       =>  $helper->ngonga(),
                'lot'               =>  $lot,
                'idkobo'            =>  $idkobo 
            ]);
            } catch (\Throwable $th) {
                echo json_encode($th->getMessage());
            }
            
        }

        $json[] =array($lot, date('d/m/Y'), $typeDonnee);

        echo json_encode($json);
        
        
        /*
         * Enregistrement de l operation dans le journal des operations
         */
        if($finTour>0){
            $detailOp="Recuperation automatique $typeDonnee par $_SESSION[nomsPsv], resultat : $finTour Importé(s)";
            $type = ($typeDonnee == 'Reperage') ? 'référencements' : 'branchements';
            $req = $rapportOp->saveRapport([
                'user' => $_SESSION['nomsPsv'],
                'operation' => "Recuperation des $type",
                'detail_operation' => "$finTour ligne(s) importée(s)",
                'lot' => $lot,
                'total_reper_before' => NULL,
                'total_reperImport_before' => NULL,
                'total_cleaned_found' => NULL,
                'total_cleaned_afected' => NULL,
                'total_reper_after' =>NULL,
                'total_reperImport_after' => NULL,
                'total_match_found' => NULL,
                'total_match_afected' => NULL,
                'total_noObs' => NULL,
                'total_doublon' => NULL,
                'total_noObs_doublon' => NULL,
                'total_noMatch' => NULL,
                'dateOperation' => $helper->ngonga('d/m/Y à H:i:s'),
            ]);
        }
        
    }
    
}
else
{
    echo 'Please fill in all required fields. 2';
    die();
}

?>

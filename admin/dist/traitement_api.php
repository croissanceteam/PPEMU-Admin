<?php
session_start();

include_once '../Metier/Autoloader.php';
Autoloader::register();

// $link_reperage = [
//                 1 => "aBjGWHgmQQfrwfeKsHCnby",
//                      "au2pD2CP4VRoqcwB5fvLzD",
//     		         "aT6K37chVpJ63i9zoh8YG3",
//                      "ah93htLqf3qRVaidr2Payz",
//                      "a3eQQCvANCG74mvnB2erAS",
//                      "aW7Ra9JevzWbK7mEkgkTWT",
//                      "aAPHjntRb2pUnkUBHvuYox",
//                      "aLG8oDKUD5PrBFUX7VgAFe",
//                      "aDqBSG9cVvCqvk6wFdUA7T",
//                      "aqWecgKFFSsyyMDoDABKbR",
//                  ];
$link_realisation = [
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
                    ];

$helper = new Helper();

$reperage = new Reperage();
$realisation = new Realisation();
$rapportOp = new RapportOperation();

if(isset($_GET['traitement_api'])){
    
    $lastDate = 0;
    $lastDate_2 = 0;  // Dernier date Realisation
    
    //ACTUALISATION PAR LOT REPERAGE ET REALISATION
    if(isset($_GET['btn']) && $_GET['btn']=='api_synchroniseLot'){ 
        $json= [];
        $nbrligne;
        $lot=htmlentities($_GET['lot'], ENT_QUOTES);
        $typeDonnee=htmlentities($_GET['typeDonnee'], ENT_QUOTES);
        
        try {
            
            if(array_key_exists($lot, $link_realisation))
                $link_sub=$link_realisation[$lot];
            else 
                throw new Exception("Lot $lot non trouvé! ");
            
            $lastDate=$realisation->getLastSubmissionTime($lot);
            $last_date_export = $realisation->getLastDateExport($lot);
            
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
            
            $nbrligne = 0;
            $jsonKobo = [];
            // FILTRING DATA FOR ELIMINATING THOSE ONE WAS PREVIOUSLY STORED
            if($lastDate == 0){
                foreach ($parsed_json as $v) {
                    $submission_time=@htmlentities($v['_submission_time'], ENT_QUOTES);
                    $geopoint= $v['Emplacement_du_branchement_r_alis'];
                    $idkobo = isset($v['_id']) ? $v['_id'] : NULL;
                    
                    list($lat_2, $lng_2, $altitude, $precision)=explode(' ', $geopoint);
                    
                    $req = $realisation->tempSave([
                        'commune'       =>  $v['Commune'],
                        'address'       =>  $v['Quartier'],
                        'avenue'        =>  $v['Avenue'],
                        'num_home'      =>  $v['Num_ro'],
                        'phone'         =>  $v['T_l_phone'],
                        'town'          =>  $v['Ville'],
                        'type_branch'   =>  $v['Branchement_Social_ou_Appropri'],
                        'water_given'   =>  "",
                        'entreprise'    =>  $v['Entreprise_qui_a_r_alis_le_branchement'],
                        'consultant'    =>  $v['Consultant_qui_a_suivi_l_ex_cution_KIN'],
                        'geopoint'      =>  $geopoint,
                        'lat'           =>  $lat_2,
                        'lng'           =>  $lng_2,
                        'altitude'      =>  $altitude, 
                        'precision'     =>  $precision,
                        'comments'          =>  $v['Commentaires'],
                        'submission_time'   =>  $v['_submission_time'],
                        'ref_client'        =>  $v['num_site'],
                        'client'            =>  $v['Nom_du_Client'],
                        'date_export'       =>  $helper->ngonga(),
                        'lot'               =>  $lot,
                        'idkobo'            =>  $idkobo 
                    ]);
                    //$jsonKobo[]=$v;
                    $nbrligne++; //echo $v['_submission_time'];
                    
                }
            }
            else
            {
                foreach ($parsed_json as $v) {
                    $submission_time=@htmlentities($v['_submission_time'], ENT_QUOTES);
                    $geopoint= $v['Emplacement_du_branchement_r_alis'];
                    $idkobo = isset($v['_id']) ? $v['_id'] : NULL;
                    
                    list($lat_2, $lng_2, $altitude, $precision)=explode(' ', $geopoint);
                    
                    if(strtotime($lastDate) < strtotime($submission_time)){
                        $req = $realisation->tempSave([
                            'commune'       =>  $v['Commune'],
                            'address'       =>  $v['Quartier'],
                            'avenue'        =>  $v['Avenue'],
                            'num_home'      =>  $v['Num_ro'],
                            'phone'         =>  $v['T_l_phone'],
                            'town'          =>  $v['Ville'],
                            'type_branch'   =>  $v['Branchement_Social_ou_Appropri'],
                            'water_given'   =>  "",
                            'entreprise'    =>  $v['Entreprise_qui_a_r_alis_le_branchement'],
                            'consultant'    =>  $v['Consultant_qui_a_suivi_l_ex_cution_KIN'],
                            'geopoint'      =>  $geopoint,
                            'lat'           =>  $lat_2,
                            'lng'           =>  $lng_2,
                            'altitude'      =>  $altitude, 
                            'precision'     =>  $precision,
                            'comments'          =>  $v['Commentaires'],
                            'submission_time'   =>  $v['_submission_time'],
                            'ref_client'        =>  $v['num_site'],
                            'client'            =>  $v['Nom_du_Client'],
                            'date_export'       =>  $helper->ngonga(),
                            'lot'               =>  $lot,
                            'idkobo'            =>  $idkobo 
                        ]);
                        //$jsonKobo[]=$v;
                        $nbrligne++;
                    }
                }
            }
            // $lastDatAffichE = ($lastDate == 0) ? date('d/m/Y', strtotime($parsed_json[0]['_submission_time'])) : date('d/m/Y', $lastTIMESTAMP);
            $last_date_to_display = ($lastDate == 0) ? 'aucune' : $last_date_export;
            $json[] = [$lot, $nbrligne, $last_date_to_display, $typeDonnee];
            
            echo json_encode($json);
            //$type = ($typeDonnee == 'Reperage') ? 'référencements' : 'branchements';
            
            $req = $rapportOp->saveRapport([ //ok
                'user' => $_SESSION['nomsPsv'],
                'operation' => "Synchronisation des branchements",
                'detail_operation' => "$nbrligne ligne(s) importée(s)",
                'lot' => $lot,
                'total_data_cleaned' => NULL,
                'total_doubl_rela' => NULL,
                'total_doubl_absol' => NULL,
                'total_doublon' => NULL,
                'dateOperation' => $helper->ngonga('d/m/Y à H:i:s'),
            ]);
        } catch (Exception $e) {
            //echo json_encode([$lot, "Error", $typeDonnee, "Echec Synchronisation!"]);
            echo json_encode([$lot, "Error", 'branchements', $e->getMessage()]);
        }
        
    }
    
    //Télécharge le données d un lot REPERAGE ok (REALISATION pas de table prevue)
    else if(isset($_GET['btn']) && $_GET['btn']=='api_TelechargeLot'){ 
        
        $json = [];
        $nbrligne;
        
        $lot=htmlentities($_GET['lot'], ENT_QUOTES);
        $typeDonnee=htmlentities($_GET['typeDonnee'], ENT_QUOTES);
        $finTour=htmlentities($_GET['finTour'], ENT_QUOTES); //Pour savoir la fin du tour de telechargement
        
        $nbrligne=0;
        $v = json_decode($_GET['row'],true);
                
        $geopoint= $v['Emplacement_du_branchement_r_alis'];
        $idkobo = isset($v['_id']) ? $v['_id'] : NULL;
        
        list($lat_2, $lng_2, $altitude, $precision) = explode(' ', $geopoint);
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
            // echo json_encode($th->getMessage());
            echo json_encode([$lot, "Error", $typeDonnee, $e->getMessage()]);
        }
        
        if($idkobo == NULL)
            $json[] = [$lot, date('d/m/Y'), $typeDonnee,$v];
        else
            $json[] = [$lot, date('d/m/Y'), $typeDonnee];

        

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
                'total_data_cleaned' => NULL,
                'total_doubl_rela' => NULL,
                'total_doubl_absol' => NULL,
                'total_doublon' => NULL,
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
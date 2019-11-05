<?php
@session_start();

//include_once '../Metier/Reperage.php';
//include_once '../Metier/RapportOperation.php';

include_once '../Metier/Autoloader.php';
Autoloader::register();

$helper = new Helper();

$reperage = new Reperage();
$realisation = new Realisation();
$rapport = new RapportOperation();

//TRAITEMENT IMPORTATION CSV OU EXCEL XLS
if(@isset($_POST['pst']) && $_POST['pst']=="save_ImportCSV")
{ 
    $typeDonnee=htmlentities($_POST['typeDonnee'], ENT_QUOTES);
    
   if($_FILES['csv']['type']=="application/vnd.ms-excel" || $_FILES['csv']['type']=="text/comma-separated-values" || $_FILES['csv']['type']=="text/csv")
      { 
        $target_path = "../partials/";
        $target_path = $target_path . basename( $_FILES['csv']['name']); 
        if(move_uploaded_file($_FILES['csv']['tmp_name'], $target_path)) 
        {

            if ($typeDonnee=="Reperage") {
                
                if (($handle = @fopen("$target_path", "r")) !== FALSE) 
                {
                    $req=false;
                    $ct=0;
                    $compt=0;
                    while (($data = @fgetcsv($handle, 1000, ";")) !== FALSE) 
                    {
                        if($ct<1){ } else {

                            $id=@htmlentities($data[0], ENT_QUOTES);
                            $name_client=@htmlentities($data[1], ENT_QUOTES);
                            $avenue=@htmlentities($data[2], ENT_QUOTES);
                            $num_home=@htmlentities($data[3], ENT_QUOTES);
                            $commune=@htmlentities($data[4], ENT_QUOTES);
                            $phone=@htmlentities($data[5], ENT_QUOTES);
                            $category=@htmlentities($data[6], ENT_QUOTES);
                            $ref_client=@htmlentities($data[7], ENT_QUOTES);
                            $pt_vente=@htmlentities($data[8], ENT_QUOTES);
                            $geopoint=@htmlentities($data[9], ENT_QUOTES);
                            $lat=@htmlentities($data[10], ENT_QUOTES);
                            $lng=@htmlentities($data[11], ENT_QUOTES);
                            $altitude=@htmlentities($data[12], ENT_QUOTES);
                            $precision=@htmlentities($data[13], ENT_QUOTES);
                            $controller_name=@htmlentities($data[14], ENT_QUOTES);
                            $comments=@htmlentities($data[15], ENT_QUOTES);
                            $submission_time=@htmlentities($data[16], ENT_QUOTES);
                            $town=@htmlentities($data[17], ENT_QUOTES);
                            $lot=@htmlentities($data[18], ENT_QUOTES);
                            $date_export=@htmlentities($data[19], ENT_QUOTES);
                            $secteur=@htmlentities($data[20], ENT_QUOTES);
                            $issue=@htmlentities($data[21], ENT_QUOTES);

                            $req = $reperage->tempSaveImportCSV([
                            'id'            =>  $id,
                            'name_client'   =>  $name_client,
                            'avenue'        =>  $avenue,
                            'num_home'      =>  $num_home,
                            'commune'       =>  $commune,
                            'phone'         =>  $phone,
                            'category'      =>  $category,
                            'ref_client'    =>  $ref_client,
                            'pt_vente'      =>  $pt_vente,
                            'geopoint'      =>  $geopoint,
                            'lat'           =>  $lat,
                            'lng'           =>  $lng,
                            'altitude'      =>  $altitude, 
                            'precision'     =>  $precision,
                        'controller_name'   =>  $controller_name,
//                        'comments'          =>  $comments,
                        'submission_time'   =>  $submission_time,
                        'town'              =>  $town, 
                        'lot'               =>  $lot, 
                        ]);
                            
                            if($req) $compt++;
                            
                            if($issue!=1 && $issue!=0){
                                $req = $reperage->deleteDoublon($ref_client, $id);
                            }

                        }
                        $ct++;
                    } 
                    @fclose($handle);

                    /*
                     * Enregistrement de l operation dans le journal des operations
                     */
                    $detailOp="Correction Anomalie(s) Referencement $typeDonnee par $_SESSION[nomsPsv], resultat : $compt Données Corrigé(s)";

                    $req = $rapport->saveRapport([
                        'user' => $_SESSION['nomsPsv'],
                        'operation' => "Correction Referencement CSV",
                        'detail_operation' => $detailOp,
                        'lot' => $lot,
                        'total_reper_before' => NULL,
                        'total_reperImport_before' => NULL,
                        'total_cleaned_found' => NULL,
                        'total_cleaned_afected' => NULL,
                        'total_reper_after' => NULL,
                        'total_reperImport_after' => NULL,
                        'total_match_found' => NULL,
                        'total_match_afected' => NULL,
                        'total_noObs' => NULL,
                        'total_doublon' => NULL,
                        'total_noObs_doublon' => NULL,
                        'total_noMatch' => NULL,
                        'dateOperation' => $helper->ngonga('d-m-Y, H:i:s'),
                    ]);
                    

                    if($compt>0)
                    {
                        $txt= '{"updated":"'.$helper->ngonga('d F Y, H:i:s').'"}';
                        $filen="../../mobile/date.json";
                        $fp = fopen($filen, 'w');
                        fwrite($fp, $txt);
                        fclose($fp);

                        echo '<div class="alert alert-success alert-dismissible" role="alert">
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                    <h3><strong>SUCCESS !!</strong> IMPORTATION REFERENCEMENT avec succès. </h3>

                                    <h4> '.$compt.' Données Importée(s) </h4>
                                </div> ';
                        @unlink($target_path);
                    }
                    else
                    {
                        echo '<div class="alert alert-warning alert-dismissible" role="alert">
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                    <h3><strong>ECHEC !!</strong> FICHIER non Importés... Re-essayer plus tard. </h3>
                                </div> ';
                        @unlink($target_path);
                    }

                }
                
            }
            
            else if ($typeDonnee=="Realisation") {
                if (($handle = @fopen("$target_path", "r")) !== FALSE) 
                {
                    $req=false;
                    $ct=0;
                    $compt=0;
                    while (($data = @fgetcsv($handle, 1000, ";")) !== FALSE) 
                    {
                        if($ct<1){ } else {

                            $id=@htmlentities($data[0], ENT_QUOTES);
                            $commune=@htmlentities($data[1], ENT_QUOTES);
                            $address=@htmlentities($data[2], ENT_QUOTES);
                            $avenue=@htmlentities($data[3], ENT_QUOTES);
                            $num_home=@htmlentities($data[4], ENT_QUOTES);
                            $phone=@htmlentities($data[5], ENT_QUOTES);
                            $town=@htmlentities($data[6], ENT_QUOTES);
                            $type_branch=@htmlentities($data[7], ENT_QUOTES);
                            $water_given=@htmlentities($data[8], ENT_QUOTES);
                            $entreprise=@htmlentities($data[9], ENT_QUOTES);
                            $consultant=@htmlentities($data[10], ENT_QUOTES);
                            $geopoint=@htmlentities($data[11], ENT_QUOTES);
                            $lat=@htmlentities($data[12], ENT_QUOTES);
                            $lng=@htmlentities($data[13], ENT_QUOTES);
                            $altitude=@htmlentities($data[14], ENT_QUOTES);
                            $precision=@htmlentities($data[15], ENT_QUOTES);
                            $submission_time=@htmlentities($data[17], ENT_QUOTES);
                            $ref_client=@htmlentities($data[20], ENT_QUOTES);
                            $client=@htmlentities($data[21], ENT_QUOTES);
                            $lot=@htmlentities($data[18], ENT_QUOTES);
                            $date_export=@htmlentities($data[19], ENT_QUOTES);
                            $issue=@htmlentities($data[22], ENT_QUOTES);
    
                            $req = $realisation->tempSaveImportCSV([
                            'id'            =>  $id,
                            'commune'       =>  $commune,
                            'address'       =>  $address,
                            'avenue'        =>  $avenue,
                            'num_home'      =>  $num_home,
                            'phone'         =>  $phone,
                            'town'          =>  $town,
                            'type_branch'   =>  $type_branch,
                            'water_given'   =>  $water_given,
                            'entreprise'    =>  $entreprise,
                            'consultant'    =>  $consultant,
                            'geopoint'      =>  $geopoint,
                            'lat'           =>  $lat,
                            'lng'           =>  $lng,
                            'altitude'      =>  $altitude, 
                            'precision'     =>  $precision,
                        'submission_time'   =>  $submission_time,
                        'ref_client'        =>  $ref_client, 
                        'client'            =>  $client, 
                        'lot'               =>  $lot, 
                        ]);

                            if($req) $compt++;
                            
                            if($issue!=1 && $issue!=0){
                                $req = $realisation->deleteDoublon($ref_client, $id);
                            }
                        }
                        $ct++;
                    } 
                    @fclose($handle);

                    /*
                     * Enregistrement de l operation dans le journal des operations
                     */
                    $detailOp="Correction Anomalie(s) Branchement $typeDonnee par $_SESSION[nomsPsv], resultat : $compt Données Corrigé(s)";

                    $req = $rapport->saveRapport([
                        'user' => $_SESSION['nomsPsv'],
                        'operation' => "Correction Branchement CSV",
                        'detail_operation' => $detailOp,
                        'lot' => $lot,
                        'total_reper_before' => NULL,
                        'total_reperImport_before' => NULL,
                        'total_cleaned_found' => NULL,
                        'total_cleaned_afected' => NULL,
                        'total_reper_after' => NULL,
                        'total_reperImport_after' => NULL,
                        'total_match_found' => NULL,
                        'total_match_afected' => NULL,
                        'total_noObs' => NULL,
                        'total_doublon' => NULL,
                        'total_noObs_doublon' => NULL,
                        'total_noMatch' => NULL,
                        'dateOperation' => $helper->ngonga('d-m-Y, H:i:s'),
                    ]);
                    

                    if($compt>0)
                    {
                        $txt= '{"updated":"'.$helper->ngonga('d F Y, H:i:s').'"}';
                        $filen="../../mobile/date.json";
                        $fp = fopen($filen, 'w');
                        fwrite($fp, $txt);
                        fclose($fp);
                        
                        echo '<div class="alert alert-success alert-dismissible" role="alert">
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                    <h3><strong>SUCCESS !!</strong> IMPORTATION BRANCHEMENTs avec succès. </h3>

                                    <h4> '.$compt.' Données Importée(s) </h4>
                                </div> ';
                        @unlink($target_path);
                    }
                    else
                    {
                        echo '<div class="alert alert-warning alert-dismissible" role="alert">
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                    <h3><strong>ECHEC !!</strong> FICHIER non Importés... Re-essayer plus tard. </h3>
                                </div> ';
                        @unlink($target_path);
                    }

                }
            }
            
        }
      }else{ 
          echo '<div class="block margin-bottom">
            <h3 class="block-title red-gradient glossy">ECHEC</h3>
            <div class="with-padding">
                <h3>TYPE DE FICHIER INCORECT... RE-ESSAYER avec un autre format.</h3>
            </div>
        </div>';
      }

}


//AFFICHAGE JOURNAL ANOMALIE
else if(@isset($_GET['journalAnomalie']) )
{
    $json= array();
    $lot=htmlentities($_GET['lot'], ENT_QUOTES);
    $typeDonnee=htmlentities($_GET['typeDonnee'], ENT_QUOTES);
    $anomalie=htmlentities($_GET['anomalie'], ENT_QUOTES);
    
    $where=" 1 ";
    
    if(trim($typeDonnee)=="Reperage") {
        if(trim($lot)!="") $where=$where." AND lot='$lot' ";
        if(trim($anomalie)!="") $where=$where." AND issue='$anomalie' ";
        
        $resData=$reperage->getAnomalies_1($where);
        
        if($resData){
            foreach ($resData as $cus)
                $json[]=$cus;
            
            echo json_encode($json);
        }
        else echo "0";
    }
    else if(trim($typeDonnee)=="Realisation") {
        if(trim($lot)!="") $where=$where." AND lot='$lot' ";
        if(trim($anomalie)!="") $where=$where." AND issue='$anomalie' ";
        
        $resData=$realisation->getAnomalies_1($where);
        
        if($resData){
            foreach ($resData as $cus)
                $json[]=$cus;
            
            echo json_encode($json);
        }
        else echo "0";
    }
    else echo "0";
    
}


//AFFICHAGE RAPPORT TRAITEMENT CLEAN
else if(@isset($_GET['rapportClean']) )
{
    $json= array();
    $lot=htmlentities($_GET['lot'], ENT_QUOTES);
    $typeDonnee=htmlentities($_GET['typeDonnee'], ENT_QUOTES);
    
    $where=" 1=1 ";
    
    if(trim($typeDonnee)=="Reperage") {
        if(trim($lot)!="") $where=$where." AND lot='$lot' AND operation='Cleaning Referencement' ";
        else $where=$where." AND operation='Cleaning Referencement' ";
        
        $resData=$rapport->getJournaleByWhere($where);
        if($resData){
            foreach ($resData as $cus)
                $json[]=$cus;
            
            echo json_encode($json);
        }
        else echo "0";
    }
    else if(trim($typeDonnee)=="Realisation") {
        if(trim($lot)!="") $where=$where." AND lot='$lot' AND operation='Cleaning Branchement' ";
        else $where=$where." AND operation='Cleaning Branchement' ";
        
        $resData=$rapport->getJournaleByWhere($where);
        if($resData){
            foreach ($resData as $cus)
                $json[]=$cus;
            
            echo json_encode($json);
        }
        else echo "0";
    }
    else {
        if(trim($lot)!="") $where=$where." AND lot='$lot' AND (operation='Cleaning Branchement' OR operation='Cleaning Referencement') ";
        else $where=$where." AND (operation='Cleaning Branchement' OR operation='Cleaning Referencement') ";
        
        $resData=$rapport->getJournaleByWhere($where);
        if($resData){
            foreach ($resData as $cus)
                $json[]=$cus;
            
            echo json_encode($json);
        }
        else echo "0";
    }
    
}


//EXPORTATION JOURNAL ANOMALIE
else if(@isset($_GET['exporter']) )
{
    $lot=htmlentities($_GET['lot'], ENT_QUOTES);
    $typeDonnee=htmlentities($_GET['typeDonnee'], ENT_QUOTES);
    $anomalie=htmlentities($_GET['anomalie'], ENT_QUOTES);
    
    $fname="";
    $data= array();
    
    $where=" 1=1 ";
    
    if(trim($typeDonnee)=="Reperage") {
        
        if(trim($lot)!="") $where=$where." AND lot='$lot' ";
        if(trim($anomalie)!="") $where=$where." AND issue='$anomalie' ";
        
        $resData=$reperage->getAnomalies($where);
        if($resData){
            
            $fname="referencement_anomlaies";
            
            header('Content-Type:text/Excel; charset=utf-8');
			header('Content-Disposition:attachment;filename='.$fname.'.csv');
	       
            $entete= array();
            
            for($i=0; $i<$resData->columnCount(); $i++ ){
                $col=$resData->getColumnMeta($i);
                $entete[]=$col['name'];
            }
            
            imprcsv($entete);
            
            foreach ($resData as $cus)
                imprcsv($cus);
            
        }
    }
    else if(trim($typeDonnee)=="Realisation") {
        
        if(trim($lot)!="") $where=$where." AND lot='$lot' ";
        if(trim($anomalie)!="") $where=$where." AND issue='$anomalie' ";
        
        $resData=$realisation->getAnomalies($where);
        if($resData){
            
            $fname="branchement_anomlaies";
            
            header('Content-Type:text/Excel; charset=utf-8');
			header('Content-Disposition:attachment;filename='.$fname.'.csv');
	       
            $entete= array();
            
            for($i=0; $i<$resData->columnCount(); $i++ ){
                $col=$resData->getColumnMeta($i);
                $entete[]=$col['name'];
            }
            
            imprcsv($entete);
            
            foreach ($resData as $cus)
                imprcsv($cus);
            
        }
    }
    
    //echo $fname;
}

else echo 'ERROR 000, Re-essayer plus tard.';

function imprcsv($chs){
    $separat='';
    foreach($chs as $i=>$ch){
        
        $ch=html_entity_decode($ch);
        
        if(preg_match('/\\r|\\n|;|"/', $ch )){
            $ch = '"'.str_replace('"','""',$ch).'"';
        }
        echo $separat.$ch;
        $separat=';';
    }
    echo "\r\n";
}
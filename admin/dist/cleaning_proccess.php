<?php

include_once '../Metier/Autoloader.php';
Autoloader::register();
session_start();

$helper = new Helper();

$reperage = new Reperage();
$realisation = new Realisation();
$rapportOperation = new RapportOperation();

if (isset($_GET['cleanDataReal'])) {
    //TODO: CLEANING PROCESS REALISATION

    $lot = htmlentities($_GET['lot'], ENT_QUOTES);
    $total_data = htmlentities($_GET['total_data'], ENT_QUOTES);

    print("<u><b>CLEANING DES BRANCHEMENTS REALISES DU LOT $lot </b></u><br/>");

    $date_exportClean = $helper->ngonga();
    //$date_exportClean=date('Y-m-d H:i:s');

    $date_export = $realisation->getLastExportDate($lot);
    print("Date de la dernière exportation : <b>" . date('d/m/Y', strtotime($date_export)) . "</b><br/>");
    
    print("<br/><u>Nombre de lignes à traiter : <b>$total_data</b></u><br/>");
    
    $total_clean_data = 0;
    try {
        /*
        * First step: count the types of branchements
        */
        $data = $realisation->countByType($lot);
        $typeOp = "";
        echo "<table width='100%'>";
        foreach ($data as $key => $value) {
            
            if($value->type_branch == "Appropriation_"){
                echo "<tr><td width='30%'>&nbsp;&nbsp; Appropriation </td><td> : <b>".$value->nombre."</b><br></td></tr>";
                $typeOp .= "Appropriations : $value->nombre <br>";
            }elseif($value->type_branch == "branchement_so"){
                echo "<tr><td width='30%'>&nbsp;&nbsp; Branchement social </td><td> : <b>".$value->nombre."</b><br></td></tr>";
                $typeOp .= "Branchements sociaux : $value->nombre <br>";
            }elseif($value->type_branch == "pose_compteur"){
                echo "<tr><td width='30%'>&nbsp;&nbsp; Pose compteur </td><td> : <b>".$value->nombre."</b><br></td></tr>";
                $typeOp .= "Poses compteur : $value->nombre <br>";
            }elseif(trim($value->type_branch) == ""){
                $rs = $realisation->getControlersWhereTypeIsEmpty($lot);
                $agent = "";
                if($rs->rowCount() == 1){
                    $agent = $rs->fetch()->consultant;
                }
                    
                $texte = $agent == '' ? "Nom du consultant non renseigné" : "Nom du consultant : $agent";
                echo "<tr style='color:red'><td width='30%'>&nbsp;&nbsp; Type non précisé </td><td> : <b>".$value->nombre."</b> <br>&nbsp; <em>$texte</em><br></td></tr>";
            }else{
                echo "<tr><td width='30%'>&nbsp;&nbsp; ".$value->type_branch." </td><td> : <b>".$value->nombre."</b><br></td></tr>";
                $typeOp .= "$value->type_branch : $value->nombre";
            }
            
        }
        echo "</table><br>";
        /*
        * Second step: detect doublons relatifs
        */
        $res_rela = $realisation->getRelativeDuplicates($lot);
        $doublon_rela = $res_rela->rowCount();
        
        $res_absol = $realisation->getAbsoluteDuplicates($lot);
        $doublon_absol = $res_absol->rowCount();

        if($doublon_rela > 0 || $doublon_absol > 0){
            print ("<br/><b><u>ANOMALIES TROUVEES</u></b><br/>");
            print ("<ul>");
            
            if($doublon_rela > 0){
                foreach ($res_rela as $cus) {
                    $realisation->markDuplicate($cus->id,4);
                }
                print ("<li><b><span style='color:red'>" . $doublon_rela. "</span></b> doublons relatifs <br/></li>");
            }

            if($doublon_absol > 0){
                foreach ($res_absol as $cus) {
                    $realisation->markDuplicate($cus->id,2);
                }
                print ("<li><b><span style='color:red'>" . $doublon_absol. "</span></b> doublons absolus <br/></li>");
            }

            print ("</ul>");
        }
        
        $realisation->markCleanData($lot);

        

//         $txt = '{"updated":"' . $helper->ngonga('d F Y, H:i:s') . '"}';
// //        $txt= '{"updated":"27 Juillet 2019, 12:00:30"}';
//         $filen = "../../mobile/date.json";
//         $fp = fopen($filen, 'w');
//         fwrite($fp, $txt);
//         fclose($fp);

        /*
        * Enregistrement de l operation dans le journal des operations
        */
        $total_doublons = $doublon_rela + $doublon_absol;

        $rapportOperation->saveRapport([
            'user' => $_SESSION['nomsPsv'],
            'operation' => "Cleaning branchements",
            'detail_operation' => $typeOp,
            'lot' => $lot,
            'total_data_cleaned' => $total_data,
            'total_doubl_rela' => $doublon_rela,
            'total_doubl_absol' => $doublon_absol,
            'total_doublon' => $total_doublons,
            'dateOperation' => $helper->ngonga('d/m/Y à H:i:s')
        ]);

    } catch (PDOException $ex) {
        echo "<br><i>Un problème est survenu lors de l'exécution de la commande</i><br>";
        echo $ex->getMessage(). "<br/>";
        echo $ex->getTraceAsString();
        //$_SESSION['leaveProcess'] = TRUE;
    } catch (Exception $ex) {
        echo "<br><i>Un problème est survenu lors de l'exécution de la commande</i><br>";
        echo $ex->getMessage(). "<br/>";
        echo $ex->getTraceAsString();
        //$_SESSION['leaveProcess'] = TRUE;
    }
    
    
} else if (isset($_GET['cleanDataReal_suite'])) {

    $lot = htmlentities($_GET['lot'], ENT_QUOTES);
    try {
        /* UPDATE OF THE JOURNAL DES OPERATIONS */
        $reqlastOperation = $rapportOperation->getLastOperation($lot);
        foreach ($reqlastOperation as $cus)
            $lastOpId = $cus->id;

        $resDataAnomalie = $realisation->getDurtyData($lot);

        //It's just the initializing count of anomalies
        $total_anomalie = $resDataAnomalie->rowCount();

        $total_doublon = 0;
        $total_noObs = 0;
        $total_noObs_doublon = 0;
        $total_exist = 0;
        $total_nomatch = 0;
        
        if ($total_anomalie > 0) {
            foreach ($resDataAnomalie as $cus) {

                $issue = 0;
                if ($cus->noObs != NULL || $cus->doublon != NULL || $cus->exist != NULL || $cus->noMatching != NULL) {

                    if ($cus->noObs != null) {
                        $issue = 1;
                        $total_noObs++;
                    } else if ($cus->doublon != null) {
                        $issue = 2;
                        $total_doublon++;
                    } else if ($cus->exist != null) {
                        $issue = 4;
                        $total_exist++;
                    } else if ($cus->noMatching != null) {
                        $issue = 5;
                        $total_nomatch++;
                    }

                    $realisation->setIssue([$issue, 0, $cus->id]);
                } else {
                    $total_anomalie--;
                }
            }
        }

        $rapportOperation->setStatIssues([$total_noObs, $total_doublon, $total_noObs_doublon, $total_nomatch, $lastOpId]);
        
    } catch (PDOException $ex) {
        echo "<br><i>Un problème est survenu lors de l'exécution de la commande</i><br>";
        echo $ex->getMessage() . "<br/>";
        echo $ex->getTraceAsString();
        $_SESSION['leaveProcess'] = TRUE;
    } catch (Exception $exc) {
        echo "<br><i>Un problème est survenu lors de l'exécution de la commande</i><br>";
        echo $ex->getMessage() . "<br/>";
        echo $exc->getTraceAsString();
        $_SESSION['leaveProcess'] = TRUE;
    }

    print ("<br/><br/><b><u>RAPPORT SUR LES ANOMALIES </u></b><br/>");
    print ("* <b><span style='color:red'>" . $total_anomalie . "</span></b> ligne(s) trouvée(s) avec anomalie. <br/>");

    if ($total_anomalie > 0) {
        print ("<br/><b>ANOMALIES </u></b><br/>");
        print ("* Saisie sans OBS &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; : <b><span style='color:red'>" . $total_noObs . "</span></b> ligne(s) ;<br/>");
        print ("* Doublons  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; : <b><span style='color:red'>" . $total_doublon . "</span></b> ligne(s).<br/>");
        print ("* Existe déjà  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; : <b><span style='color:red'>" . $total_exist . "</span></b> ligne(s).<br/>");
        print ("* Client non trouvé dans la base source  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; : <b><span style='color:red'>" . $total_nomatch . "</span></b> ligne(s).<br/>");
        //print ("* Erreurs sur Référence et Doublons : <b>" . $total_noObs_doublon . " lignes</b>.<br/><br/>");

        print (" <b>FIN DU CLEANING DES BRANCHEMENTS DU LOT $lot.</b><br/>");
    }
}
?>

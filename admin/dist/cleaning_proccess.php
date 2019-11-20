<?php

include_once '../Metier/Autoloader.php';
Autoloader::register();
session_start();

$helper = new Helper();

$reperage = new Reperage();
$realisation = new Realisation();
$rapportOperation = new RapportOperation();

if (isset($_GET['cleanDataReper'])) {
    //TODO: CLEANING PROCESS REPERAGE

    $lot = htmlentities($_GET['lot'], ENT_QUOTES);
    $total_data = htmlentities($_GET['total_data'], ENT_QUOTES);

    print("<u><b>CLEANING DES REFERENCEMENTS DU LOT $lot </b></u><br/>");

    $date_exportClean = $helper->ngonga();
    //$date_exportClean=date('Y-m-d H:i:s');

    $total_reperage_before = $reperage->getReperageByLot($lot)->rowCount();
//    print("total_reperage_before : $total_reperage_before <br/>");
    $total_import_before = $reperage->getNotCleanedReperageImportByLot($lot)->rowCount();
//    print("total_import_before : $total_import_before <br/>");
    $date_export = $reperage->getLastExportDate($lot);
    print("Date de la dernière exportation : <b>" . date('d/m/Y', strtotime($date_export)) . "</b><br/>");

    // print ("Total des lignes à nettoyer trouvées : <b>" . $total_reperage_before . "</b><br/>Total de lignes des données exportées le " . date('d/m/Y à H:i', strtotime($date_export)) ." de KoboToolbox : <b>" . $total_import_before . "</b><br/>");
    /*
     * First step: find clean data in reperage_import
     *
     * Une données correcte "clean" est une données dont le numéro du client est unique et se termine par OBS et qui a un seul referencement
     */
    
    print("<br/><u>Nombre de lignes à traiter : <b>$total_data</b></u><br/>");

    
    $total_clean_data = 0;
    try {
        $reperage->runLikelihoodControl();
        
        $resCleanData = $reperage->findCleanDataByLot($lot);
        $total_clean_data = $resCleanData->rowCount();
    } catch (PDOException $ex) {
        echo $ex->getMessage();
        echo $ex->getTraceAsString();
        $_SESSION['leaveProcess'] = TRUE;
    } catch (Exception $ex) {
        echo $ex->getTraceAsString();
        $_SESSION['leaveProcess'] = TRUE;
    }

    $total_inserted = 0;
    $total_reperage_after = 0;
    $total_import_after = 0;

    if ($total_clean_data > 0) {

        /*
         * Second step: transfer clean data into reperage table and update them from reperage_import by changing the clean state at 1 and issue at NULL
         */

        $total_inserted = $alreadyExist = 0;

        foreach ($resCleanData as $cus) {

            try {
                $response = $reperage->insert([
                    'name' => $cus->name_client,
                    'street' => $cus->avenue,
                    'home' => $cus->num_home,
                    'commune' => $cus->commune,
                    'phone' => $cus->phone,
                    'cat' => $cus->category,
                    'ref_client' => $cus->ref_client,
                    'pt_vente' => $cus->pt_vente,
                    'geo' => $cus->geopoint,
                    'lat' => $cus->lat,
                    'lng' => $cus->lng,
                    'alt' => $cus->altitude,
                    'precision' => $cus->precision,
                    'ctrl_name' => $cus->controller_name,
                    'comments' => $cus->comments,
                    'submission_time' => $cus->submission_time,
                    'town' => $cus->town,
                    'lot' => $cus->lot,
                    'date_export' => $date_exportClean, // Je PENSE qu'ici il faut GARDER la date export de chaque donnée
                    //'date_export' => date('Y-m-d'),
                    'secteur' => $cus->secteur,
                    'matching' => $cus->matching,
                    'error_matching' => $cus->error_matching,
                    'idkobo' => $cus->_id
                ]);

                if ($response == 0)
                    $total_inserted += 1;
                else if ($response == 1)
                    $alreadyExist++;
            } catch (PDOException $ex) {
                echo "<br><i>Un problème est survenu lors de l'exécution de la commande</i><br>";
                echo $ex->getMessage();
                $_SESSION['leaveProcess'] = TRUE;
                break;
            } catch (Exception $ex) {
                echo "<br><i>Un problème est survenu lors de l'exécution de la commande</i><br>";
                echo $ex->getMessage();
                $_SESSION['leaveProcess'] = TRUE;
                break;
            }
        }

        $txt = '{"updated":"' . $helper->ngonga('d F Y, H:i:s') . '"}';
//        $txt= '{"updated":"27 Juillet 2019, 12:00:30"}';
        $filen = "../../mobile/date.json";
        $fp = fopen($filen, 'w');
        fwrite($fp, $txt);
        fclose($fp);

        $total_reperage_after = $reperage->getReperageByLot($lot)->rowCount(); //$link->query($reqTotalReperage)->fetch()->total;
        $total_import_after = $reperage->getNotCleanedReperageImportByLot($lot)->rowCount(); //$link->query($reqTotalImport)->fetch()->total;
    }

    $durty_data = $total_data - $total_clean_data;
    print("<br/><u><b>STATISTIQUES APRES LE CLEANING</b></u><br/>");
    print ("Total des données correctes : <b>$total_clean_data</b><br/>Total des données incorrectes : <b>$durty_data </b><br/>");

    //$uninserted_data = $total_clean_data - $total_inserted;
    if ($alreadyExist > 0)
        print ("<br/><span style='color:red'>Exception relevée : Il se trouve que <b>$alreadyExist</b> clients ont des numéro site déjà enregistré(s) précédemment.</span><br/>");

    /*
     * Enregistrement de l operation dans le journal des operations
     */
    try {
        //$detailOp = "Operation de Cleaning par $_SESSION[nomsPsv], result : $total_inserted correctes sur $total_import_before";
        $rapportOperation->saveRapport([
            'user' => $_SESSION['nomsPsv'],
            'operation' => "Cleaning référencement",
            'detail_operation' => "$total_inserted correctes sur $total_data",
            'lot' => $lot,
            'total_reper_before' => $total_reperage_before,
            'total_reperImport_before' => $total_import_before,
            'total_cleaned_found' => $total_clean_data,
            'total_cleaned_afected' => $total_inserted,
            'total_reper_after' => $total_reperage_after,
            'total_reperImport_after' => $total_import_after,
            'total_match_found' => NULL, //$countRootMatching,
            'total_match_afected' => NULL, //$updated_rows,
            'total_noObs' => NULL,
            'total_doublon' => NULL,
            'total_noObs_doublon' => NULL,
            'total_noMatch' => NULL,
            'dateOperation' => $helper->ngonga('d/m/Y à H:i:s'),
        ]);
    } catch (PDOException $ex) {
        echo "<br><i>Un problème est survenu lors de l'exécution de la commande</i><br>";
        echo $ex->getMessage() . "<br/>";
        echo $ex->getTraceAsString();
        $_SESSION['leaveProcess'] = TRUE;
    } catch (Exception $ex) {
        echo "<br><i>Un problème est survenu lors de l'exécution de la commande</i><br>";
        echo $ex->getMessage() . "<br/>";
        echo $ex->getTraceAsString();
        $_SESSION['leaveProcess'] = TRUE;
    }
} else if (isset($_GET['cleanDataReal'])) {
    //TODO: CLEANING PROCESS REALISATION

    $lot = htmlentities($_GET['lot'], ENT_QUOTES);
    $total_data = htmlentities($_GET['total_data'], ENT_QUOTES);

    print("<u><b>CLEANING DES BRANCHEMENTS REALISES DU LOT $lot </b></u><br/>");

    $date_exportClean = $helper->ngonga();
    //$date_exportClean=date('Y-m-d H:i:s');

    $total_realisation_before = $realisation->getRealisationByLot($lot)->rowCount();
//    print("total_realisation_before : $total_realisation_before <br/>");
    $total_import_before = $realisation->getNotCleanedRealisationImportByLot($lot)->rowCount();
//    print("total_import_before : $total_import_before <br/>");
    $date_export = $realisation->getLastExportDate($lot);
    print("Date de la dernière exportation : <b>" . date('d/m/Y', strtotime($date_export)) . "</b><br/>");

    /*
     * First step: find clean data in realised_import
     *
     * Une données correcte "clean" est une données dont le numéro du client est unique et se termine par OBS et qui a un seul referencement
     */
    print("<br/><u>Nombre de lignes à traiter : <b>$total_data</b></u><br/>");
    
    $total_clean_data = 0;
    try {
        $realisation->runLikelihoodControl();
        
        $resCleanData = $realisation->findCleanDataByLot($lot);
        $total_clean_data = $resCleanData->rowCount();
    } catch (PDOException $ex) {
        echo $ex->getMessage();
        echo $ex->getTraceAsString();
        $_SESSION['leaveProcess'] = TRUE;
    } catch (Exception $ex) {
        echo $ex->getTraceAsString();
        $_SESSION['leaveProcess'] = TRUE;
    }
    
    $total_inserted = 0;
    $total_reperage_after = 0;
    $total_import_after = 0;
    
    if ($total_clean_data > 0) {

        /*
         * Second step: transfer clean data into realisation table and delete them from reperage_import
         */

        $total_inserted = $alreadyExist = 0;
        
        foreach ($resCleanData as $cus) {
            try {
                $response = $realisation->insert([
                    'commune' => $cus->commune,
                    'address' => $cus->address,
                    'avenue' => $cus->avenue,
                    'num_home' => $cus->num_home,
                    'phone' => $cus->phone,
                    'town' => $cus->town,
                    'type_branch' => $cus->type_branch,
                    'water_given' => $cus->water_given,
                    'entreprise' => $cus->entreprise,
                    'consultant' => $cus->consultant,
                    'geopoint' => $cus->geopoint,
                    'lat' => $cus->lat,
                    'lng' => $cus->lng,
                    'altitude' => $cus->altitude,
                    'precision' => $cus->precision,
                    'comments' => $cus->comments,
                    'submission_time' => $cus->submission_time,
                    'ref_client' => $cus->ref_client,
                    'client' => $cus->client,
                    'lot' => $cus->lot,
                    'date_export' => $date_exportClean,
                    'idkobo' => $cus->_id
                        //'date_export' => '2019-07-22',
                ]);

                if ($response == 0)
                    $total_inserted += 1;
                else
                    $alreadyExist++;
            } catch (PDOException $ex) {
                echo $ex->getTraceAsString();
                break;
            } catch (Exception $exc) {
                echo $exc->getTraceAsString();
                break;
            }
        }
        
        $txt = '{"updated":"' . $helper->ngonga('d F Y, H:i:s') . '"}';
//        $txt= '{"updated":"27 Juillet 2019, 12:00:30"}';
        $filen = "../../mobile/date.json";
        $fp = fopen($filen, 'w');
        fwrite($fp, $txt);
        fclose($fp);

        $total_realisation_after = $realisation->getRealisationByLot($lot)->rowCount();
        $total_import_after = $realisation->getNotCleanedRealisationImportByLot($lot)->rowCount();
    }

    $durty_data = $total_data - $total_clean_data;
    print("<br/><u><b>STATISTIQUES DU CLEANING</b></u><br/>");
    print ("Total des données correctes : <b>$total_clean_data</b><br/>Total des données incorrectes : <b>$durty_data </b><br/>");

    if ($alreadyExist > 0)
        print ("<br/><span style='color:red'>Exception relevée : <b>$alreadyExist</b> clés de référencement corrigées se retrouvent déjà dans les données correctes.</span><br/>");

    /*
     * Enregistrement de l operation dans le journal des operations
     */
    try {
        //$detailOp = "Operation Cleaning par $_SESSION[nomsPsv], result : $total_inserted correctes sur $total_import_before";
        $rapportOperation->saveRapport([
            'user' => $_SESSION['nomsPsv'],
            'operation' => "Cleaning branchements",
            'detail_operation' => "$total_inserted correctes sur $total_data",
            'lot' => $lot,
            'total_reper_before' => $total_realisation_before,
            'total_reperImport_before' => $total_import_before,
            'total_cleaned_found' => $total_clean_data,
            'total_cleaned_afected' => $total_inserted,
            'total_reper_after' => $total_realisation_after,
            'total_reperImport_after' => $total_import_after,
            'total_match_found' => NULL,
            'total_match_afected' => NULL,
            'total_noObs' => NULL,
            'total_doublon' => NULL,
            'total_noObs_doublon' => NULL,
            'total_noMatch' => NULL,
            'dateOperation' => $helper->ngonga('d/m/Y à H:i:s'),
        ]);
    } catch (PDOException $ex) {
        echo "<br><i>Un problème est survenu lors de l'exécution de la commande</i><br>";
        echo $ex->getMessage() . "<br/>";
        echo $ex->getTraceAsString();
        $_SESSION['leaveProcess'] = TRUE;
    } catch (Exception $ex) {
        echo "<br><i>Un problème est survenu lors de l'exécution de la commande</i><br>";
        echo $ex->getMessage() . "<br/>";
        echo $ex->getTraceAsString();
        $_SESSION['leaveProcess'] = TRUE;
    }
    
} else if (isset($_GET['cleanDataReper_suite'])) {

    $lot = htmlentities($_GET['lot'], ENT_QUOTES);
    try {
        /* UPDATE OF THE JOURNAL DES OPERATIONS */
        $reqlastOperation = $rapportOperation->getLastOperation($lot);
        foreach ($reqlastOperation as $cus)
            $lastOpId = $cus->id;

        $resDataAnomalie = $reperage->getDurtyData($lot);

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

                    $reperage->setIssue([$issue, 0, $cus->id]);
                } else {
                    $total_anomalie--;
                }
            }
        }

        $rapportOperation->setStatIssues([$total_noObs, $total_doublon, $total_noObs_doublon,$total_nomatch,$lastOpId]);

        /*
         * UPDATING WRONG REF OF MATCHING DATA (matching of cus name and avenue)
         */
        //METHOD INVOCATION HERE...
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
        print ("<br/><b>ANOMALIES : </u></b><br/>");
        print ("* Saisie sans OBS &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; : <b><span style='color:red'>" . $total_noObs . "</span></b> ligne(s) ;<br/>");
        print ("* Doublons  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; : <b><span style='color:red'>" . $total_doublon . "</span></b> ligne(s).<br/>");
        print ("* Existe déjà  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; : <b><span style='color:red'>" . $total_exist . "</span></b> ligne(s).<br/>");
        print ("* Client non trouvé dans la base source  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; : <b><span style='color:red'>" . $total_nomatch . "</span></b> ligne(s).<br/>");
        //print ("* Erreurs sur Référence et Doublons : <b>" . $total_noObs_doublon . " lignes</b>.<br/><br/>");


        print ("<br/> <b>FIN DU CLEANING DES REFERENCEMENTS DU LOT $lot.</b><br/>");
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

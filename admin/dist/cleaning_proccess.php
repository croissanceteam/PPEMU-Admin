<?php

include_once '../Metier/Autoloader.php';
Autoloader::register();
session_start();

if(isset($_GET['cleanDataReper'])){
        //TODO: CLEANING PROCESS

    $reperage = new Reperage();

    $lot=htmlentities($_GET['lot'], ENT_QUOTES);

    print("<u><b>CLEANING REPERAGE LOT $lot </b></u><br/>");


    $total_reperage_before = $reperage->getReperageByLot($lot)->rowCount();
    $total_import_before = $reperage->getNotCleanedReperageImportByLot($lot)->rowCount();
    $date_export = $reperage->getLastExportDate()->fetch();

    print ("Total de lignes dans reperage : " . $total_reperage_before . ".<br/>Total de lignes des données exportées le " . $date_export . " de Kobo : " . $total_import_before . "<br/>");
    /*
     * First step: found clean data in reperage_import
     *
     * Une données correcte "clean" est une données dont le numéro du client est unique et se termine par OBS
     */


    print("<br/><u><b>Recherche des données clean à la date du $date_export </b></u><br/>");


    $resCleanData = $reperage->findCleanDataByLot($lot);
    $total_clean_data = $resCleanData->rowCount();

    if ($total_clean_data > 0) {
        print ("<b>".$total_clean_data . " clean data trouvées dans la table reperage import</b> <br/><br/>");

        print("<b><u>Transfert des clean data dans la table des referencements 'reperages'</u></b> <br/>");
        print("Début du transfert des données <br/>");

        /*
         * Second step: transfer clean data into reperage table and delete them from reperage_import
         */

        $total_inserted = $uninserted_data = 0;

        foreach ($resCleanData as $cus) {
    //    print($cus->name_client);
    //    print "\n";

            try {
                $reperage->insert([
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
                    //'date_export' => $data->date_export,
                    'date_export' => '2019-07-22',
                    'secteur' => $cus->secteur,
                    'matching' => $cus->matching,
                    'error_matching' => $cus->error_matching
                ],$cus->ref_client);

                $total_inserted += 1;
            } catch (PDOException $ex) {
                echo $ex->getMessage();
                break;
            } catch (Exception $exc) {
                echo $exc->getTraceAsString();
                break;
            }
        }


        $total_reperage_after = $link->query($reqTotalReperage)->fetch()->total;
        $total_import_after = $link->query($reqTotalImport)->fetch()->total;

        //$nouvelles_data = $total_reperage_before - $total_reperage_after;

        print("<b>Fin du transfert des données</b> <br/><br/>");
        print("<u><b>RAPPORT DE TRANSFERT</b></u><br>");
        print ("* " . $total_inserted . " lignes sur " . $total_clean_data . " ont été transferées <br/>");
        
        print ("<b>Total de lignes dans reperage après transfert : $total_reperage_after.<br/>Total de lignes des données sales à la date du $date_export : $total_import_after </b><br/>");
        $uninserted_data = $total_clean_data - $total_inserted;
        if ($uninserted_data > 0)
            print ($uninserted_data . " lignes n'ont pas pu être transferées");

        /*
         * Deleting copy of identical records
         *  Means data with 100% of coherence
         */
    }else {
        print ("<br/><b>Aucune données clean trouvée dans les données en provenance de Kobo à la date du $date_export</b><br/>");
    }


    /*
     * Matching data between reperage and root
     */

    $reqFoundRootMatching = "SELECT rt.secteur AS secteur_root,rt.refclient FROM t_reperage rep INNER JOIN t_root rt ON rep.ref_client = rt.refclient WHERE rep.lot= '$lot' ";
    $resRootMatching = $link->query($reqFoundRootMatching);
    $countRootMatching = $resRootMatching->rowCount();

    $reqUpdateRepMatching = "UPDATE t_reperage SET secteur=:secteur, matching=:matching, error_matching=:error WHERE ref_client=:refClient";
    $stmtUpdate = $link->prepare($reqUpdateRepMatching);

    print ("<br/><b>Recheche des lignes dans root qui matchent avec les reperages...</b><br/>");
    print ("" . $countRootMatching . " lignes trouvées.");

    print ("<br/>Début du processus d'attribution des secteurs aux données de reperage matchées... <br/>");
    $updated_rows = $rs = 0;
    foreach ($resRootMatching as $row) {
        try {
            //$link->beginTransaction();

            $stmtUpdate->bindParam('secteur', $row->secteur_root, PDO::PARAM_STR);
            $stmtUpdate->bindValue('matching', 1, PDO::PARAM_BOOL);
            $stmtUpdate->bindValue('error', 0, PDO::PARAM_BOOL);
            $stmtUpdate->bindParam('refClient', $row->refclient, PDO::PARAM_STR);
            $stmtUpdate->execute();
            $rs = $stmtUpdate->rowCount();

            //$link->commit();

            $updated_rows += $rs;

        } catch (PDOException $ex) {
            //$link->rollBack();
            echo $ex->getMessage();
            break;
        } catch (Exception $exc) {
            //$link->rollBack();
            echo $exc->getTraceAsString();
            break;
        }
    }
    print ("<b>Fin du processus</b><br/>");

    $notupdated = $countRootMatching - $updated_rows;
    $reqCountNotMatchingReperage = "SELECT COUNT(*) AS total FROM t_reperage rep LEFT JOIN t_root rt ON rep.ref_client = rt.refclient WHERE rt.refclient IS NULL";
    $totalNotMatchingReperage = $link->query($reqCountNotMatchingReperage)->fetch()->total;


    print ("<br/><b><u>RAPPORT DE MATCHING</u></b><br/>");

    print ("* " . $updated_rows . " lignes sur " . $countRootMatching . " ont été mises à jour;<br/>");

    if ($notupdated > 0)
        print ("* " . $notupdated . " lignes n'ont pas pu être mises à jour;<br/>");

    print ("<br/>Exception : " . $totalNotMatchingReperage . " lignes dans reperages ne matchent pas avec les données de Wambe.");

    /*
     * Enregistrement de l operation dans le journal des operations
     */
    try {
        $link->beginTransaction();

        $reqInsert = "INSERT INTO `journal_operations` (`id`, `user`, `operation`, `detail_operation`, `lot`, `total_reper_before`, `total_reperImport_before`, `total_cleaned_found`, `total_cleaned_afected`, `total_reper_after`, `total_reperImport_after`, `total_match_found`, `total_match_afected`, `total_noObs`, `total_doublon`, `total_noObs_doublon`, `dateOperation`) "

                . "VALUES (NULL, :user, :operation, :detail_operation, :lot, :total_reper_before, :total_reperImport_before, :total_cleaned_found, :total_cleaned_afected, :total_reper_after, :total_reperImport_after, :total_match_found, :total_match_afected, :total_noObs, :total_doublon, :total_noObs_doublon, sysdate() )";

        $detailOp="Cleaning Operation par $_SESSION[nomsPsv], result : $total_inserted traité sur $total_import_before";

        $statement = $link->prepare($reqInsert);
        $statement->execute([
            'user' => $cus->name_client,
            'operation' => "Cleaning Data",
            'detail_operation' => $detailOp,
            'lot' => $lot,
            'total_reper_before' => $total_reperage_before,
            'total_reperImport_before' => $total_import_before,
            'total_cleaned_found' => $total_clean_data,
            'total_cleaned_afected' => $total_inserted,
            'total_reper_after' => $total_reperage_after,
            'total_reperImport_after' => $total_import_after,
            'total_match_found' => $countRootMatching,
            'total_match_afected' => $updated_rows,
            'total_noObs' => 0,
            'total_doublon' => 0,
            'total_noObs_doublon' => 0,
        ]);

        $link->commit();

    } catch (PDOException $ex) {
        $link->rollBack();
        echo $ex->getMessage();
        break;
    } catch (Exception $exc) {
        $link->rollBack();
        echo $exc->getTraceAsString();
        break;
    }

    $link = NULL;

}

else if(isset($_GET['cleanDataReper_suite'])){

    $lot=htmlentities($_GET['lot'], ENT_QUOTES);

//    $reqlastOperation = "SELECT id from journal_operations where lot='$lot' order by id desc limit 1 ";
    $reqGetDataAnomalie = "SELECT id, ref_client, (select id from t_reperage_import t1 where t1.id=t.id and t1.ref_client NOT LIKE '%OBS') as noObs, (select id from t_reperage_import t1 where t1.id=t.id and ref_client IN (SELECT ref_client FROM t_reperage_import t1 GROUP BY t1.ref_client  HAVING COUNT(*) > 1) ) as doublon FROM t_reperage_import t WHERE lot='$lot' ";

    $reqlastOperation = $link->query("SELECT id from journal_operations where lot='$lot' order by id desc limit 1 ");
    foreach ($reqlastOperation as $cus)
        $lastOpId=$cus->id;

    $resDataAnomalie = $link->query($reqGetDataAnomalie);
    $total_anomalie = $resDataAnomalie->rowCount();

    $total_doublon = 0;
    $total_noObs = 0;
    $total_noObs_doublon = 0;

    if ($total_anomalie > 0) {
        foreach ($resDataAnomalie as $cus) {

            try {
                $link->beginTransaction();

                $issue=0;
                if ($cus->noObs !=null && $cus->doublon !=null){
                    $issue=3;
                    $total_noObs_doublon++;
                }
                else if ($cus->noObs !=null ){
                    $issue=1;
                    $total_noObs++;
                }
                else if ($cus->doublon !=null){
                    $issue=2;
                    $total_doublon++;
                }

                $reqUpdate = "UPDATE t_reperage_import SET issue=? WHERE id = ?";
                $stmt = $link->prepare($reqUpdate);
                $stmt->execute([$issue, $cus->id]);

                $link->commit();

            } catch (PDOException $ex) {
                $link->rollBack();
                echo $ex->getMessage();
                break;
            } catch (Exception $exc) {
                $link->rollBack();
                echo $exc->getTraceAsString();
                break;
            }
        }
    }

    try {
        $link->beginTransaction();

        $reqUpdate = "UPDATE journal_operations SET total_noObs=?, total_doublon=?, total_noObs_doublon=? WHERE id = ?";
        $stmt = $link->prepare($reqUpdate);
        $stmt->execute([$total_noObs, $total_doublon, $total_noObs_doublon, $lastOpId]);

        $link->commit();

    } catch (PDOException $ex) {
        $link->rollBack();
        echo $ex->getMessage();
        break;
    } catch (Exception $exc) {
        $link->rollBack();
        echo $exc->getTraceAsString();
        break;
    }

    print ("<br/><br/><b><u>RAPPORT SUR LES ANOMALIES </u></b><br/>");
    print ("* <b>" . $total_anomalie . " lignes<b> trouvée(s) avec <b> anomalie </b> ; <br/>");

    if ($total_anomalie > 0){
        print ("<br/><b>ANOMALIES </u></b><br/>");
        print ("* Erreurs sur les Références &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; : <b>" . $total_noObs . " lignes </b> ;<br/>");
        print ("* Références avec Doublons  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; : <b>" . $total_doublon . " lignes </b>;<br/>");
        print ("* Erreurs sur Référence et Doublons : <b>" . $total_noObs_doublon . " lignes</b>.<br/><br/>");


        print (" <b>FIN OPERATION CLEANING DU LOT $lot.</b><br/>");
    }
}

else
{
    echo 'Please fill in all required fields. 2';
    die();
}

?>

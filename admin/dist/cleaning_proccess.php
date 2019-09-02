<?php
include('../../sync/db.php');
session_start();

if(isset($_GET['cleanDataReper'])){
        //TODO: Affichage Liste Villes 
        
    $json= array();

    $lot=htmlentities($_GET['lot'], ENT_QUOTES);
    
    print("<u><b>CLEANING REPERAGE $lot </b></u><br/><br/>");
    
    $reqTotalReperage = "SELECT COUNT(*) AS total FROM t_reperage WHERE lot='$lot' ";
    $reqTotalImport = "SELECT COUNT(*) AS total FROM t_reperage_import WHERE lot='$lot' ";
    $reqGetDateExport = "SELECT DISTINCT(date_export) FROM t_reperage_import ORDER BY date_export DESC LIMIT 1";

    $total_reperage_before = $link->query($reqTotalReperage)->fetch()->total;
    $total_import_before = $link->query($reqTotalImport)->fetch()->total;
    $date_export = $link->query($reqGetDateExport)->fetch()->date_export;

    print ("Total de lignes dans reperage : " . $total_reperage_before . ".<br/>Total de lignes des données exportées le " . $date_export . " de Kobo : " . $total_import_before . "<br/>");
    /*
     * First step: found clean data in reperage_import
     * 
     * Une données correcte "clean" est une données dont le numéro du client est unique et se termine par OBS
     */


    print("<br/><u><b>Recherche des données clean à la date du $date_export </b></u><br/>");

    $reqGetCleanData = "SELECT * FROM t_reperage_import WHERE lot='$lot' AND ref_client LIKE '%OBS' AND ref_client IN (SELECT ref_client FROM t_reperage_import GROUP BY ref_client  HAVING COUNT(*) = 1)";

    $resCleanData = $link->query($reqGetCleanData);
    $total_clean_data = $resCleanData->rowCount();

    if ($total_clean_data > 0) {
        print ("<b>".$total_clean_data . " clean data trouvées dans la table reperage import</b> <br/><br/>");

        print("<b><u>Transfert des clean data dans la table des referencements 'reperages'</u></b> <br/><br/>");
        print("Début du transfert des données <br/><br/>");

        /*
         * Second step: transfer clean data into reperage table and delete them from reperage_import
         */

        $total_inserted = $uninserted_data = 0;

        foreach ($resCleanData as $cus) {
    //    print($cus->name_client);
    //    print "\n";

            try {
                $link->beginTransaction();

                $reqInsert = "INSERT INTO `t_reperage` (`name_client`,`avenue`,`num_home`,`commune`,`phone`,`category`,`ref_client`,`pt_vente`,`geopoint`,`lat`,`lng`,`altitude`,`precision`,`controller_name`,`comments`,`submission_time`,`town`,`lot`,`date_export`,`secteur`,`matching`,`error_matching`) "
                        . "VALUES(:name,:street,:home,:commune,:phone,:cat,:ref_client,:pt_vente,:geo,:lat,:lng,:alt,:precision,:ctrl_name,:comments,:submission_time,:town,:lot,:date_export,:secteur,:matching,:error_matching)";
                $statement = $link->prepare($reqInsert);
                $statement->execute([
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
                ]);

                $reqDelete = "DELETE FROM t_reperage_import WHERE ref_client = ?";
                $stmt = $link->prepare($reqDelete);
                $stmt->execute([$cus->ref_client]);

                $link->commit();
                $total_inserted += 1;
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


        $total_reperage_after = $link->query($reqTotalReperage)->fetch()->total;
        $total_import_after = $link->query($reqTotalImport)->fetch()->total;

        //$nouvelles_data = $total_reperage_before - $total_reperage_after;

        print(" <b>Fin du transfert des données</b> <br/><br/>");
        print("<u><b>RAPPORT DE TRANSFERT</b></u><br><br>");
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
    print ("<br/>" . $countRootMatching . " lignes trouvées.");

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
    print ("<br/><b>Fin du processus</b><br/>");

    $notupdated = $countRootMatching - $updated_rows;
    $reqCountNotMatchingReperage = "SELECT COUNT(*) AS total FROM t_reperage rep LEFT JOIN t_root rt ON rep.ref_client = rt.refclient WHERE rt.refclient IS NULL";
    $totalNotMatchingReperage = $link->query($reqCountNotMatchingReperage)->fetch()->total;


    print ("<br/><b><u>RAPPORT DE MATCHING</u></b><br/><br/>");

    print ("* " . $updated_rows . " lignes sur " . $countRootMatching . " ont été mises à jour;<br/>");

    if ($notupdated > 0)
        print ("* " . $notupdated . " lignes n'ont pas pu être mises à jour;<br/>");

    print ("<br/>Exception : " . $totalNotMatchingReperage . " lignes dans reperages ne matchent pas avec les données de Wambe.");

    $link = NULL;
        
        
        
}
else
{
    echo 'Please fill in all required fields. 2';
    die();
}

?>
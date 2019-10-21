<?php


$link = new PDO('mysql:host=31.207.34.223;dbname=db_portal_test', 'root', 'ppemu', [PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES UTF8"]);
$link->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$link->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);

/*
 * Begin 
 */

 /**
  *     1) without OBS, with only numerical ref
  */

  $reqGetNumericRefFromRep = "SELECT id,ref_client FROM t_reperage_import WHERE issue = '1' AND ref_client NOT LIKE '%BS%' OR ref_client NOT LIKE '%BS'";
  $reqGetNumericRefFromRea = "SELECT id,ref_client FROM t_realised_import WHERE issue = '1' AND ref_client NOT LIKE '%BS%' OR ref_client NOT LIKE '%BS'";
  $reqUpdateRef = "UPDATE t_reperage_import SET ref_client=:ref WHERE id=:id";
  
  $i = $j = 0;
  try {
      $getNumericRefFromRep = $link->query($reqGetNumericRefFromRep);
      if($getNumericRefFromRep->rowCount() > 0){
          $dataNumericRef = $getNumericRefFromRep->fetchAll();

          foreach ($dataNumericRef as $key => $rep_imp) {
              
              $new_ref = trim($rep_imp->ref_client)."OBS";
              
              $stmt = $link->prepare($reqUpdateRef);
              $stmt->execute([
                  'ref'   =>  $new_ref,
                  'id'   =>  $rep_imp->id
              ]);
              $i++;
          }
      }
      
      $getNumericRefFromRea = $link->query($reqGetNumericRefFromRea);
      if($getNumericRefFromRea->rowCount() > 0){
          $dataNumericRef = $getNumericRefFromRea->fetchAll();

          foreach ($dataNumericRef as $key => $rep_imp) {
              
              $new_ref = trim($rep_imp->ref_client)."OBS";
              
              $stmt = $link->prepare($reqUpdateRef);
              $stmt->execute([
                  'ref'   =>  $new_ref,
                  'id'   =>  $rep_imp->id
              ]);
              $j++;
          }
      }

      
  } catch (\Throwable $th) {
      print $th->getMessage();
      print "\n";
  }
      
      print $i. " ref corrected from rep_imp \n";
      print $j. " ref corrected from realised_imp \n";

      print "\n";

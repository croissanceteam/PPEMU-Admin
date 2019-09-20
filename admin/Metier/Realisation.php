<?php

Class Realisation{
    private $dbLink;
    public function __construct(){
        $this->dbLink = new Database();
    }
    /**
     * this function save realisation from kobo into t_realised_import table
     *
     * @param [type] $params
     * @return bool
     */
    public function tempSave($params){
        $query = "INSERT INTO `t_realised_import` (`id`, `commune`, `address`, `avenue`, `num_home`, `phone`, `town`, `type_branch`, `water_given`, `entreprise`, `consultant`, `geopoint`, `lat`, `lng`, `altitude`, `precision`, `comments`, `submission_time`, `lot`, `date_export`, `ref_client`, `client`, `issue`) VALUES (NULL, :commune, :address, :avenue, :num_home, :phone, :town, :type_branch, :water_given, :entreprise, :consultant, :geopoint, :lat, :lng, :altitude, :precision, :comments, :submission_time, :lot, sysdate(), :ref_client, :client, '0')";
        return $this->dbLink->query($query, $params);
    }



    public function tempSaveImportCSV($params){
        $query = "UPDATE `t_realised_import` SET `commune`=:commune, `address`=:address, `avenue`=:avenue, `num_home`=:num_home, `phone`=:phone, `town`=:town, `type_branch`=:type_branch, `water_given`=:water_given, `entreprise`=:entreprise, `consultant`=:consultant, `geopoint`=:geopoint, `lat`=:lat, `lng`=:lng, `altitude`=:altitude, `precision`=:precision, `submission_time`=:submission_time, `ref_client`=:ref_client, `client`=:client, `issue`='0' WHERE lot=:lot and id=:id";
        return $this->dbLink->query($query, $params);
    }

    public function getLastDateTIMESTAMP($lot) {
        $dateQ = $this->dbLink->query("SELECT UNIX_TIMESTAMP(date_export) as lastDate FROM t_realised_import WHERE lot=? ORDER BY id DESC LIMIT 1", [$lot] );
        if ($dateQ->rowCount()>0) {
            $mylastD = $dateQ->fetch();
                return $mylastD->lastDate;
        }
        return 0;
    }

    public function getNoCleanByLot() {
        $query = $this->dbLink->query("SELECT MAX(lot) as lot, max(date_export) as date_export, count(*) as ligne FROM t_realised_import t1 WHERE issue='0' GROUP BY lot" );

        if ($query->rowCount()>0)
            return $query;

        return 0;
    }

    public function getLot() {
        $query = $this->dbLink->query("SELECT DISTINCT lot FROM t_realised_import ");

        if ($query->rowCount()>0)
            return $query;

        return 0;
    }

    public function getAnomalies($where) {
        $query = $this->dbLink->query("SELECT `id`, `commune`, `address`, `avenue`, `num_home`, `phone`, `town`, `type_branch`, `water_given`, `entreprise`, `consultant`, `geopoint`, `lat`, `lng`, `altitude`, `precision`, `comments`, `submission_time`, `lot`, `date_export`, `ref_client`, `client`, `issue`, `label` FROM t_realised_import t LEFT JOIN (t_issue i) ON t.issue=i.valeur WHERE ".$where." AND issue !='0' ORDER BY client" );

        if ($query->rowCount()>0)
            return $query;

        return 0;
    }

    public function deleteDoublon($ref, $id) {
        $query = $this->dbLink->query("DELETE FROM t_realised_import WHERE ref_client=? and id!=? and issue!=?", [$ref, $id, 0] );

        if ($query->rowCount()>0)
            return $query;

        return 0;
    }

    public function getRealisationByLot($lot){
      $query = $this->dbLink->query("SELECT * FROM t_realised WHERE lot=? ",[$lot]);
      return $query;
    }

    public function getNotCleanedRealisationImportByLot($lot){
      $query = $this->dbLink->query("SELECT * FROM t_realised_import WHERE lot=? and issue=? ",[$lot,0]);
      return $query;
    }

    public function getLastExportDate($lot){
      $query = $this->dbLink->query("SELECT DISTINCT(date_export) FROM t_realised_import WHERE lot=? ORDER BY date_export DESC LIMIT 1",[$lot])->fetch()->date_export;
      return $query;
    }

    public function findCleanDataByLot($lot){
      $query = $this->dbLink->query("SELECT * FROM t_realised_import WHERE lot=? AND issue=? AND ref_client LIKE '%OBS' AND ref_client IN (SELECT ref_client FROM t_realised_import GROUP BY ref_client  HAVING COUNT(*) = 1)",[$lot,0]);
      return $query;
    }

    public function insert($params,$refclient){
      try{
        $this->dbLink->getLink()->beginTransaction();

        $queryInsert = "INSERT INTO `t_realised` (`id`, `commune`, `address`, `avenue`, `num_home`, `phone`, `town`, `type_branch`, `water_given`, `entreprise`, `consultant`, `geopoint`, `lat`, `lng`, `altitude`, `precision`, `comments`, `submission_time`, `lot`, `date_export`, `ref_client`, `client`) VALUES(NULL, :commune, :address, :avenue, :num_home, :phone, :town, :type_branch, :water_given, :entreprise, :consultant, :geopoint, :lat, :lng, :altitude, :precision, :comments, :submission_time, :lot, :date_export, :ref_client, :client)";
        $this->dbLink->query($queryInsert,$params);

        $queryDelete = "DELETE FROM t_realised_import WHERE ref_client = ?";
        $this->dbLink->query($queryDelete,[$refclient]);

        $this->dbLink->getLink()->commit();

      }catch (PDOException $ex) {
        $this->dbLink->getLink()->rollBack();
        throw new \PDOException($ex->getMessage());

      } catch (Exception $exc) {
        $this->dbLink->getLink()->rollBack();
        throw new \Exception($exc->getTraceAsString());
      }

    }
    /*
    *
    */
    public function get(){
      $req = "SELECT * FROM t_realised";
    }

    public function getDurtyData($lot)
    {
      $req = "SELECT id, ref_client, (select id from t_realised_import t1 where t1.id=t.id and t1.ref_client NOT LIKE '%OBS') as noObs, (select id from t_realised_import t1 where t1.id=t.id and ref_client IN (SELECT ref_client FROM t_realised_import t1 GROUP BY t1.ref_client  HAVING COUNT(*) > 1) ) as doublon FROM t_realised_import t WHERE lot=? ";
      return $this->dbLink->query($req,[$lot]);
    }

    public function setIssue($params)
    {
      $req = "UPDATE t_realised_import SET issue=? WHERE id = ?";
      return $this->dbLink->query($req,$params);
    }

}

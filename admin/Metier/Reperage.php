<?php

Class Reperage{
    private $dbLink;
    public function __construct(){
        $this->dbLink = new Database();
    }
    /**
     * this function save reperage from kobo into t_reperage_import table
     *
     * @param [type] $params
     * @return bool
     */
    public function tempSave($params){
        $query = "INSERT INTO `t_reperage_import` (`id`, `name_client`, `avenue`, `num_home`, `commune`, `phone`, `category`, `ref_client`, `pt_vente`, `geopoint`, `lat`, `lng`, `altitude`, `precision`, `controller_name`, `comments`, `submission_time`, `town`, `lot`, `date_export`, `issue`) VALUES (NULL, :name_client, :avenue, :num_home, :commune, :phone, :category, :ref_client, :pt_vente, :geopoint, :lat, :lng, :altitude, :precision, :controller_name, :comments, :submission_time, :town, :lot, sysdate(), '0')";
        return $this->dbLink->query($query, $params);
    }



    public function tempSaveImportCSV($params){
        $query = "UPDATE `t_reperage_import` SET `name_client`=:name_client, `avenue`=:avenue, `num_home`=:num_home, `commune`=:commune, `phone`=:phone, `category`=:category, `ref_client`=:ref_client, `pt_vente`=:pt_vente, `geopoint`=:geopoint, `lat`=:lat, `lng`=:lng, `altitude`=:altitude, `precision`=:precision, `controller_name`=:controller_name, `submission_time`=:submission_time, `town`=:town, `issue`='0' WHERE lot=:lot and id=:id";
        return $this->dbLink->query($query, $params);
    }

    public function getLastDateTIMESTAMP($lot) {
        $dateQ = $this->dbLink->query("SELECT UNIX_TIMESTAMP(date_export) as lastDate FROM t_reperage_import WHERE lot=? ORDER BY id DESC LIMIT 1", [$lot] );
        if ($dateQ->rowCount()>0) {
            $mylastD = $dateQ->fetch();
                return $mylastD->lastDate;
        }
        return 0;
    }

    public function getNoCleanByLot() {
        $query = $this->dbLink->query("SELECT MAX(lot) as lot, max(date_export) as date_export, count(*) as ligne FROM t_reperage_import t1 WHERE issue='0' GROUP BY lot" );

        if ($query->rowCount()>0)
            return $query;

        return 0;
    }

    public function getLot() {
        $query = $this->dbLink->query("SELECT DISTINCT lot FROM t_reperage_import ");

        if ($query->rowCount()>0)
            return $query;

        return 0;
    }

    public function getAnomalies($where) {
        $query = $this->dbLink->query("SELECT `id`, `name_client`, `avenue`, `num_home`, `commune`, `phone`, `category`, `ref_client`, `pt_vente`, `geopoint`, `lat`, `lng`, `altitude`, `precision`, `controller_name`, `comments`, `submission_time`, `town`, `lot`, `date_export`, `secteur`, `issue`, `label` FROM t_reperage_import t LEFT JOIN (t_issue i) ON t.issue=i.valeur WHERE ".$where." AND issue !='0' ORDER BY name_client" );

        if ($query->rowCount()>0)
            return $query;

        return 0;
    }

    public function getAnomalies_1($where) {
        $query = $this->dbLink->query("SELECT `name_client`, `avenue`, `num_home`, `commune`, `ref_client`, `controller_name`, `lot`, `label` FROM t_reperage_import t LEFT JOIN (t_issue i) ON t.issue=i.valeur WHERE ".$where." AND issue !='0' ORDER BY name_client" );

        if ($query->rowCount()>0)
            return $query;

        return 0;
    }

    public function deleteDoublon($ref, $id) {
        $query = $this->dbLink->query("DELETE FROM t_reperage_import WHERE ref_client=? and id!=? and issue!=?", [$ref, $id, 0] );

        if ($query->rowCount()>0)
            return $query;

        return 0;
    }

    public function getReperageByLot($lot){
      $query = $this->dbLink->query("SELECT * FROM t_reperage WHERE lot=? ",[$lot]);
      return $query;
    }

    public function getNotCleanedReperageImportByLot($lot){
      $query = $this->dbLink->query("SELECT * FROM t_reperage_import WHERE lot=? and issue=? ",[$lot,0]);
      return $query;
    }

    public function getLastExportDate($lot){
      $query = $this->dbLink->query("SELECT DISTINCT(date_export) FROM t_reperage_import WHERE lot=? ORDER BY date_export DESC LIMIT 1",[$lot])->fetch()->date_export;
      return $query;
    }

    public function findCleanDataByLot($lot){
      $query = $this->dbLink->query("SELECT * FROM t_reperage_import WHERE lot=? AND issue=? AND ref_client LIKE '%OBS' AND ref_client IN (SELECT ref_client FROM t_reperage_import GROUP BY ref_client  HAVING COUNT(*) = 1)",[$lot,0]);
      return $query;
    }

    public function insert($params,$refclient){
      try{
        $this->dbLink->getLink()->beginTransaction();

        $queryInsert = "INSERT INTO `t_reperage` (`name_client`,`avenue`,`num_home`,`commune`,`phone`,`category`,`ref_client`,`pt_vente`,`geopoint`,`lat`,`lng`,`altitude`,`precision`,`controller_name`,`comments`,`submission_time`,`town`,`lot`,`date_export`,`secteur`,`matching`,`error_matching`) VALUES(:name,:street,:home,:commune,:phone,:cat,:ref_client,:pt_vente,:geo,:lat,:lng,:alt,:precision,:ctrl_name,:comments,:submission_time,:town,:lot,:date_export,:secteur,:matching,:error_matching)";
        $this->dbLink->query($queryInsert,$params);

        $queryDelete = "DELETE FROM t_reperage_import WHERE ref_client = ?";
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
      $req = "SELECT * FROM t_reperage";
    }

    public function findRootMatching($lot)
    {
      $req = "SELECT rt.secteur AS secteur_root,rt.refclient FROM t_reperage rep INNER JOIN t_root rt ON rep.ref_client = rt.refclient WHERE rep.lot=? ";
      return $this->dbLink->query($req,[$lot]);
    }

    public function updateMatchingRep($params)
    {
      $req  = "UPDATE t_reperage SET secteur=:secteur, matching=:matching, error_matching=:error WHERE ref_client=:refClient";
      return $this->dbLink->query($req,$params);
    }

    public function findNotMatchingReperage()
    {
      $req = "SELECT * FROM t_reperage rep LEFT JOIN t_root rt ON rep.ref_client = rt.refclient WHERE rt.refclient IS NULL";
      return $this->dbLink->query($req);
    }
    public function getDurtyData($lot)
    {
      $req = "SELECT id, ref_client, (select id from t_reperage_import t1 where t1.id=t.id and t1.ref_client NOT LIKE '%OBS') as noObs, (select id from t_reperage_import t1 where t1.id=t.id and ref_client IN (SELECT ref_client FROM t_reperage_import t1 GROUP BY t1.ref_client  HAVING COUNT(*) > 1) ) as doublon FROM t_reperage_import t WHERE lot=? ";
      return $this->dbLink->query($req,[$lot]);
    }

    public function setIssue($params)
    {
      $req = "UPDATE t_reperage_import SET issue=? WHERE id = ?";
      return $this->dbLink->query($req,$params);
    }

}

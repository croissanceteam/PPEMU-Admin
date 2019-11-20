<?php

Class Reperage {

    private $dbLink;

    public function __construct() {
        $this->dbLink = new Database();
    }

    public function getLastSubmissionTime($lot) {
        $stmt = $this->dbLink->query("SELECT MAX(submission_time) as last_date FROM t_reperage_import WHERE lot=?", [$lot]);
        if ($stmt->rowCount() > 0)
            return $stmt->fetch()->last_date;

        return 0;
    }

    /**
     * 
     * Check whether the customer have a correspondance on root
     * If so, update its ref_client
     *
     * @return void
     */
    public function runLikelihoodControl() {
        $queryCorresp = "SELECT t.id,r.refclient,t.ref_client FROM t_reperage_import t, t_root r WHERE (TRIM(r.client) LIKE CONCAT(TRIM(t.name_client),'%') OR TRIM(t.name_client) LIKE CONCAT(TRIM(r.client),'%')) AND (TRIM(r.avenue) LIKE CONCAT(TRIM(t.avenue),'%') OR TRIM(t.avenue) LIKE CONCAT(TRIM(r.avenue),'%')) AND t.ref_client NOT LIKE r.refclient";
        $corresp_stmt = $this->dbLink->query($queryCorresp);

        if ($corresp_stmt->rowCount()) {
            foreach ($corresp_stmt as $data) {
                $queryUpdateCus = "UPDATE t_reperage_import SET ref_client=?, matching=? WHERE id=?";
                if($data->refclient != $data->ref_client)
                    $this->dbLink->query($queryUpdateCus, [$data->refclient, 1,$data->id]);
            }
        }
    }

    /**
     * this function save reperage from kobo into t_reperage_import table
     *
     * @param [type] $params
     * @return bool
     */
    public function tempSave($params) {
        $query = "INSERT INTO `t_reperage_import` (`id`, `name_client`, `avenue`, `num_home`, `commune`, `phone`, `category`, `ref_client`, `pt_vente`, `geopoint`, `lat`, `lng`, `altitude`, `precision`, `controller_name`, `comments`, `submission_time`, `town`, `lot`, `date_export`, `secteur`, `issue`, `_id`) VALUES (NULL, :name_client, :avenue, :num_home, :commune, :phone, :category, :ref_client, :pt_vente, :geopoint, :lat, :lng, :altitude, :precision, :controller_name, :comments, :submission_time, :town, :lot, :date_export, :secteur, 0,:idkobo)";
        return $this->dbLink->query($query, $params);
    }

    public function tempSaveImportCSV($params) {
        $query = "UPDATE `t_reperage_import` SET `name_client`=:name_client, `avenue`=:avenue, `num_home`=:num_home, `commune`=:commune, `phone`=:phone, `category`=:category, `ref_client`=:ref_client, `pt_vente`=:pt_vente, `geopoint`=:geopoint, `lat`=:lat, `lng`=:lng, `altitude`=:altitude, `precision`=:precision, `controller_name`=:controller_name, `submission_time`=:submission_time, `town`=:town, `issue`='0', `clean`=null WHERE lot=:lot and id=:id";
        return $this->dbLink->query($query, $params);
    }

    public function getLastDateTIMESTAMP($lot) {
        $dateQ = $this->dbLink->query("SELECT UNIX_TIMESTAMP(date_export) as lastDate FROM t_reperage_import WHERE lot=? ORDER BY id DESC LIMIT 1", [$lot]);
        if ($dateQ->rowCount() > 0) {
            $mylastD = $dateQ->fetch();
            return $mylastD->lastDate;
        }
        return 0;
    }
    

    /**
     * Return the number of not yet cleaned data grouped by lot
     *
     * @return void
     */
    public function getNotCleanedData() {
        $query = $this->dbLink->query("SELECT MAX(lot) as lot, max(date_export) as date_export, count(*) as ligne FROM t_reperage_import t1 WHERE `issue`=0 AND (clean IS NULL OR clean = 0) GROUP BY lot");

        if ($query->rowCount() > 0)
            return $query;

        return 0;
    }

    public function getLot() {
        $query = $this->dbLink->query("SELECT DISTINCT lot FROM t_reperage_import ");

        if ($query->rowCount() > 0)
            return $query;

        return 0;
    }

    public function getAnomalies($where) {
        $query = $this->dbLink->query("SELECT `id`, `name_client`, `avenue`, `num_home`, `commune`, `phone`, `category`, `ref_client`, `pt_vente`, `geopoint`, `lat`, `lng`, `altitude`, `precision`, `controller_name`, `comments`, `submission_time`, `town`, `lot`, `date_export`, `secteur`, `issue`, `label` FROM t_reperage_import t LEFT JOIN (t_issue i) ON t.issue=i.valeur WHERE " . $where . " AND issue !='0' ORDER BY name_client");

        if ($query->rowCount() > 0)
            return $query;

        return 0;
    }

    public function getAnomalies_1($where) {
        $query = $this->dbLink->query("SELECT `name_client`, `avenue`, `num_home`, `commune`, `ref_client`, `controller_name`, `lot`, `label` FROM t_reperage_import t LEFT JOIN (t_issue i) ON t.issue=i.valeur WHERE " . $where . " AND issue !='0' ORDER BY name_client");

        if ($query->rowCount() > 0)
            return $query;

        return 0;
    }

    public function deleteDoublon($ref, $id) {
        $query = $this->dbLink->query("DELETE FROM t_reperage_import WHERE ref_client=? and id!=? and issue!=?", [$ref, $id, 0]);

        if ($query->rowCount() > 0)
            return $query;

        return 0;
    }

    public function getReperageByLot($lot) {
        $query = $this->dbLink->query("SELECT * FROM t_reperage WHERE lot=? ", [$lot]);
        return $query;
    }

    /**
     * Looking for not cleaned data by lot
     *
     * @param [type] $lot
     * @return void
     */
    public function getNotCleanedReperageImportByLot($lot) {
        //$query = $this->dbLink->query("SELECT * FROM t_reperage_import WHERE lot=? and issue=? ",[$lot,0]);
        $query = $this->dbLink->query("SELECT * FROM t_reperage_import WHERE lot=? and `issue`=0 AND clean IS NULL", [$lot]);
        return $query;
    }

    public function getLastExportDate($lot) {
        $query = $this->dbLink->query("SELECT DISTINCT(date_export) FROM t_reperage_import WHERE lot=? ORDER BY date_export DESC LIMIT 1", [$lot])->fetch()->date_export;
        return $query;
    }

    public function findCleanDataByLot($lot) {
        $query = $this->dbLink->query("SELECT * FROM t_reperage_import WHERE lot=? AND `issue`=? AND ref_client LIKE '%OBS' AND ref_client IN (SELECT ref_client FROM t_reperage_import GROUP BY ref_client  HAVING COUNT(*) = 1) AND ref_client IN (SELECT t.ref_client FROM t_reperage_import t, t_root r WHERE (TRIM(r.client) LIKE CONCAT(TRIM(t.name_client),'%') OR TRIM(t.name_client) LIKE CONCAT(TRIM(r.client),'%')) AND (TRIM(r.avenue) LIKE CONCAT(TRIM(t.avenue),'%') OR TRIM(t.avenue) LIKE CONCAT(TRIM(r.avenue),'%')))", [$lot, 0]);
        return $query;
    }

    /**
     * return the number of occurence that already exist in the reperage table
     *
     * @param [type] $params
     * @return int
     */
    public function insert($params) {
        try {
            //$this->dbLink->getLink()->setAttribute(PDO::ATTR_AUTOCOMMIT,0);

            /*
             * Check whether the cus have ref different from his correspondance onto root
             * If so, update it in reperage and leave it in import as so
             */
            $ref_from_kobo = $params['ref_client'];
            $queryCorresp = "SELECT r.refclient,r.secteur FROM t_reperage_import t, t_root r WHERE t.ref_client LIKE ? AND (TRIM(r.client) LIKE CONCAT(TRIM(t.name_client),'%') OR TRIM(t.name_client) LIKE CONCAT(TRIM(r.client),'%')) AND (TRIM(r.avenue) LIKE CONCAT(TRIM(t.avenue),'%') OR TRIM(t.avenue) LIKE CONCAT(TRIM(r.avenue),'%')) AND t.ref_client NOT LIKE r.refclient;";
            $corresp_stmt = $this->dbLink->query($queryCorresp, [$ref_from_kobo]);

            if ($corresp_stmt->rowCount()) {
                $data = $corresp_stmt->fetch();
                $params['ref_client'] = $data->refclient;
                if (trim($params['secteur']) === '')
                    $params['secteur'] = $data->secteur;
            }

            /* checking the existence of the occurence in the reperage table */
            $querySelect = "SELECT id FROM t_reperage WHERE ref_client = ?";
            $tuplet = $this->dbLink->query($querySelect, [$params['ref_client']])->rowCount();
            
            if ($tuplet == 0) {
                if (!$this->dbLink->getLink()->inTransaction())
                    $this->dbLink->getLink()->beginTransaction();

                $queryInsert = "INSERT INTO `t_reperage` (`name_client`,`avenue`,`num_home`,`commune`,`phone`,`category`,`ref_client`,`pt_vente`,`geopoint`,`lat`,`lng`,`altitude`,`precision`,`controller_name`,`comments`,`submission_time`,`town`,`lot`,`date_export`,`secteur`,`matching`,`error_matching`) VALUES(:name,:street,:home,:commune,:phone,:cat,:ref_client,:pt_vente,:geo,:lat,:lng,:alt,:precision,:ctrl_name,:comments,:submission_time,:town,:lot,:date_export,:secteur,:matching,:error_matching)";
                $res_insert = $this->dbLink->query($queryInsert, $params);

                $queryUpdate = "UPDATE t_reperage_import SET issue=?, clean=? WHERE ref_client = ?";
                $res_update = $this->dbLink->query($queryUpdate, [NULL, 1, $ref_from_kobo]);

                if ($res_insert->rowCount() && $res_update->rowCount()) {
                    $this->dbLink->getLink()->commit();
                    return 0;
                } else
                    $this->dbLink->getLink()->rollBack();
            } else {
                /* The customer's key already exists in the reperage table  */
                $res_update = $queryUpdate = "UPDATE t_reperage_import SET issue=?, clean=? WHERE ref_client = ?";
                $this->dbLink->query($queryUpdate, [4, 0, $ref_from_kobo]);
                return 1;
            }

            //$this->dbLink->getLink()->setAttribute(PDO::ATTR_AUTOCOMMIT,1);
        } catch (PDOException $ex) {
            $this->dbLink->getLink()->rollBack();
            throw new PDOException($ex->getTraceAsString() . ' ||| ' . $ex->getMessage());
        } catch (Exception $ex) {
            $this->dbLink->getLink()->rollBack();
            throw new Exception($ex->getTraceAsString() . ' ||| ' . $ex->getMessage());
        }
    }

    /*
     *
     */

    public function get() {
        $req = "SELECT * FROM t_reperage";
    }

    public function findRootMatching($lot) {
        $req = "SELECT rt.secteur AS secteur_root,rt.refclient FROM t_reperage rep INNER JOIN t_root rt ON rep.ref_client = rt.refclient WHERE rep.lot=? ";
        return $this->dbLink->query($req, [$lot]);
    }

    public function updateMatchingRep($params) {
        $req = "UPDATE t_reperage SET secteur=:secteur, matching=:matching, error_matching=:error WHERE ref_client=:refClient";
        return $this->dbLink->query($req, $params);
    }

    public function findNotMatchingReperage() {
        $req = "SELECT * FROM t_reperage rep LEFT JOIN t_root rt ON rep.ref_client = rt.refclient WHERE rt.refclient IS NULL";
        return $this->dbLink->query($req);
    }

    public function getDurtyData($lot) {
        //$req = "SELECT id, ref_client, (select id from t_reperage_import t1 where t1.id=t.id and t1.ref_client NOT LIKE '%OBS') as noObs, (select id from t_reperage_import t1 where t1.id=t.id and ref_client IN (SELECT ref_client FROM t_reperage_import t1 GROUP BY t1.ref_client HAVING COUNT(*) > 1) ) as doublon,(SELECt id FROM t_reperage WHERE ref_client=t.ref_client) as exist, (SELECT name_client FROM t_reperage_import t1 WHERE t1.id=t.id AND t1.id NOT IN (SELECT t.id FROM t_reperage_import t, t_root r WHERE (TRIM(r.client) LIKE CONCAT(TRIM(t.name_client),'%') OR TRIM(t.name_client) LIKE CONCAT(TRIM(r.client),'%')) AND (TRIM(r.avenue) LIKE CONCAT(TRIM(t.avenue),'%') OR TRIM(t.avenue) LIKE CONCAT(TRIM(r.avenue),'%'))) LIMIT 1) AS noMatching FROM t_reperage_import t WHERE lot=? AND clean IS NULL";
        $req = "SELECT id, ref_client, (select id from t_reperage_import t1 where t1.id=t.id and t1.ref_client NOT LIKE '%OBS') as noObs, (select id from t_reperage_import t1 where t1.id=t.id and ref_client IN (SELECT ref_client FROM t_reperage_import t1 GROUP BY t1.ref_client HAVING COUNT(*) > 1) ) as doublon, (SELECT name_client FROM t_reperage_import t1 WHERE t1.id=t.id AND t1.id NOT IN (SELECT t.id FROM t_reperage_import t, t_root r WHERE (TRIM(r.client) LIKE CONCAT(TRIM(t.name_client),'%') OR TRIM(t.name_client) LIKE CONCAT(TRIM(r.client),'%')) AND (TRIM(r.avenue) LIKE CONCAT(TRIM(t.avenue),'%') OR TRIM(t.avenue) LIKE CONCAT(TRIM(r.avenue),'%'))) LIMIT 1) AS noMatching FROM t_reperage_import t WHERE lot=? AND clean IS NULL";
        return $this->dbLink->query($req, [$lot]);
    }

    public function setIssue($params) {
        $req = "UPDATE t_reperage_import SET `issue`=?, clean=? WHERE id = ?";
        return $this->dbLink->query($req, $params);
    }

}

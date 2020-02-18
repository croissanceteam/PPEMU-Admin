<?php

Class RapportOperation{
    private $dbLink;
    public function __construct(){
        $database = new Database();
        $this->dbLink = $database;
    }
    /**
     * this function save reperage from kobo into t_reperage_import table
     *
     * @param [type] $params
     * @return bool
     */
    public function saveRapport($params){
        $query = "INSERT INTO `t_journal_operations` (`id`, `user`, `operation`, `detail_operation`, `lot`, total_data_cleaned,`total_doubl_rela`, total_doubl_absol, `total_doublon`, `dateOperation`) "

                    . "VALUES (NULL, :user, :operation, :detail_operation, :lot,:total_data_cleaned,:total_doubl_rela,:total_doubl_absol, :total_doublon, :dateOperation)";
        return $this->dbLink->query($query, $params);
    }

    public function getJournales() {
        $query = $this->dbLink->query("SELECT * FROM t_journal_operations ORDER BY dateOperation DESC" );

        if ($query->rowCount()>0)
            return $query;

        return 0;
    }

    public function getJournaleByWhere($where) {
        $query = $this->dbLink->query("SELECT * FROM t_journal_operations j WHERE ".$where." ORDER BY dateOperation DESC Limit 250" );

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

    public function getLastOperation($lot)
    {
        $req = "SELECT id from t_journal_operations where lot=? order by id desc limit 1 ";
        return $this->dbLink->query($req,[$lot]);
    }

    public function setStatIssues($params)
    {
        $req = "UPDATE t_journal_operations SET total_noObs=?, total_doublon=?, total_noObs_doublon=?,total_noMatch=? WHERE id = ?";
        return $this->dbLink->query($req,$params);
    }

}

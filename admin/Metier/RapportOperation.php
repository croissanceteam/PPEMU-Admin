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
        $query = "INSERT INTO `t_journal_operations` (`id`, `user`, `operation`, `detail_operation`, `lot`, `total_reper_before`, `total_reperImport_before`, `total_cleaned_found`, `total_cleaned_afected`, `total_reper_after`, `total_reperImport_after`, `total_match_found`, `total_match_afected`, `total_noObs`, `total_doublon`, `total_noObs_doublon`, `dateOperation`) "

                    . "VALUES (NULL, :user, :operation, :detail_operation, :lot, :total_reper_before, :total_reperImport_before, :total_cleaned_found, :total_cleaned_afected, :total_reper_after, :total_reperImport_after, :total_match_found, :total_match_afected, :total_noObs, :total_doublon, :total_noObs_doublon, :dateOperation )";
        return $this->dbLink->query($query, $params);
    }

    public function getJournales() {
        $query = $this->dbLink->query("SELECT * FROM t_journal_operations j ORDER BY dateOperation DESC" );

        if ($query->rowCount()>0)
            return $query;

        return 0;
    }

    public function getJournaleByWhere($where) {
        $query = $this->dbLink->query("SELECT * FROM t_journal_operations j WHERE ".$where." ORDER BY dateOperation DESC" );

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
        $req = "UPDATE t_journal_operations SET total_noObs=?, total_doublon=?, total_noObs_doublon=? WHERE id = ?";
        return $this->dbLink->query($req,$params);
    }

}

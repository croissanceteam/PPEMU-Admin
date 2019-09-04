<?php

require_once '../sync/Database.php';
Class Reperage{
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
    public function tempSave($params){
        $query = "INSERT INTO `t_reperage_import` (`id`, `name_client`, `avenue`, `num_home`, `commune`, `phone`, `category`, `ref_client`, `pt_vente`, `geopoint`, `lat`, `lng`, `altitude`, `precision`, `controller_name`, `comments`, `submission_time`, `town`, `lot`, `date_export`) VALUES (NULL, :name_client, :avenue, :num_home, :commune, :phone, :category, :ref_client, :pt_vente, :geopoint, :lat, :lng, :altitude, :precision, :controller_name, :comments, :submission_time, :town, :lot, :date_export)";
        return $this->dbLink->query($query, $params);
    }
}
<?php

class Database {

    private $_link;
    private static $driver = 'mysql';
    private static $server = 'localhost';
    private static $username = "root";
    private static $password = "root";
    private static $dbname = "db_portal_test2";
    private $errorMessage = "Un probleme est survenu dans le serveur, veillez conctacter l'administrateur";

    public function __construct() {
        try {
            $this->_link = new PDO(self::$driver . ':host=' . self::$server . ';dbname=' . self::$dbname, self::$username, self::$password, [\PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES UTF8"]);
            $this->_link->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->_link->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);
        } catch (PDOException $e) {
            throw new Exception($this->errorMessage);
            //throw new Exception($e->getMessage());
        }
    }

    public function query($sql, $parameters = NULL) {

        if ($parameters) {
            $req = $this->_link->prepare($sql);
            $req->execute($parameters);
        } else
            $req = $this->_link->query($sql);

        return $req;
    }

    public function lastInsertId() {
        return $this->_link->lastInsertId();
    }

    public function getLink() {
        return $this->_link;
    }

}

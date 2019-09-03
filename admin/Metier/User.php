<?php
session_start();
require_once '../sync/Database.php';
Class User {
    private $dbLink;
    public function __construct(){
        $database = new Database();
        $this->dbLink = $database;
    }

    public function signin($username, $password) {
        $user = $this->dbLink->query("SELECT * FROM t_user WHERE username = ?", [$username]);
        if ($user->rowCount()) {
            $myuser = $user->fetch();
            
            if (password_verify($password, $myuser->password)) {
                
                $_SESSION['pseudoPsv'] = $myuser->username;
                $_SESSION['nomsPsv'] = $myuser->fullname;
                $_SESSION['avatarPsv'] = $myuser->avatar;
                $_SESSION['tokenPsv'] = $myuser->token;

                return TRUE;
            }
        }
        return FALSE;
    }
}
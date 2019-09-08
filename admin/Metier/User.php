<?php
session_start();
//require_once '../sync/Database.php';
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

    public function sendToken($email){
        $str = "123456789NBVCXWMLKJHGFDSQPOUYTREZA";
        $token = substr(str_shuffle(str_repeat($str,2)),0,4);
        
        $exist = $this->dbLink->query("SELECT COUNT(*) AS nbr FROM t_user WHERE mailaddress= ?",[$email])->fetch();
        if($exist->nbr == 1){
            $rs = $this->dbLink->query("UPDATE t_user SET token = ? WHERE mailaddress = ?", [$token,$email]);
            //return $rs;

            if ($rs) {

                $headers = "MIME-Version: 1.0" . "\r\n";
                $headers .= "Content-Type: text/html; charset='utf-8'" . "\r\n";
                $headers .= "Content-Transfer-Encoding: 8bit" . "\r\n";
                $headers .= "From: NO-REPLY<no-reply@obspemu.org>" . "\r\n";
                $subject = "Confirmation de la creation de votre compte Namoni";
                $message = "<html>
                                <body>
                                    <p>
                                        <font size=50px>
                                        <strong>CEP-O</strong> Portail
                                        </font>
                                    </p>
                                    <p>
                                        <font size=30px>
                                        Voici votre code de réinitialisation de mot de passe: $token
                                        
                                        </font>
                                    </p>
                                </body>
                            </html>";
                            $retour = mail($email, $subject, $message, $headers);
                if($retour === TRUE)
                    return 1;
                else
                    return 2;
            } else {
                return 0;
                
            }
        }else{
            return 6;
        }
        
        
        
    }
}
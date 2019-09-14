<?php
@session_start();
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

Class User {
    private $dbLink;
    public function __construct(){
      $this->dbLink = new Database();
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
                $_SESSION['nbr_changepass_try'] = 3;

                return TRUE;
            }
        }
        return FALSE;
    }

    public function sendToken($email){
        require '../../vendor/autoload.php';
        $str = "123456789NBVCXWMLKJHGFDSQPOUYTREZA";
        $token = substr(str_shuffle(str_repeat($str,2)),0,4);

        $exist = $this->dbLink->query("SELECT COUNT(*) AS nbr FROM t_user WHERE mailaddress= ?",[$email])->fetch();
        if($exist->nbr == 1){
            $rs = $this->dbLink->query("UPDATE t_user SET token = ? WHERE mailaddress = ?", [$token,$email]);
            //return $rs;
            
            $baseUrl = Helper::getURL(1);
            $image_src = $baseUrl.'/img/code-fill-page.png';
            //$resquest_uri = $_SERVER['REQUEST_URI'];

            $mail = new PHPMailer(true);

            try {
                //Server settings
                //$mail->SMTPDebug = 2;
                //$mail->SMTPDebug = SMTP::DEBUG_SERVER (2);                                  // Enable verbose debug output
                $mail->isSMTP();                                            // Set mailer to use SMTP
                $mail->Host       = 'mail42.lwspanel.com;mail42.lwspanel.com';  // Specify main and backup SMTP se$
                $mail->SMTPAuth   = true;                                   // Enable SMTP authentication
                $mail->Username   = 'no-reply@obspemu.org';                     // SMTP username
                $mail->Password   = 'uK9$f_rpuC';                               // SMTP password
                $mail->SMTPSecure = 'tls';                                // Enable TLS encryption, `ssl` also a$
                $mail->Port       = 587;                                    // TCP port to connect to

                //Recipients
                $mail->setFrom('no-reply@obspemu.org', 'CEP-O PEMU');
                $mail->addAddress($email, 'PORTAIL User');     // Add a recipient
                //$mail->addAddress('ellen@example.com');               // Name is optional
                //$mail->addReplyTo('info@example.com', 'Information');
                //$mail->addCC('cc@example.com');
                //$mail->addBCC('bcc@example.com');

                // Attachments
                //$mail->addAttachment('/var/tmp/file.tar.gz');         // Add attachments
                //$mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name

                // Content
                $mail->isHTML(true);                                  // Set email format to HTML
                $mail->Subject = 'CODE DE RECUPERATION DU COMPTE';
                $mail->Body    = "<html>
                                    <head>
                                        <meta charset='utf-8'>
                                    </head>
                                    <body>
                                        <p><strong>CEP-O PEMU</strong> Portail</p>
                                        <p>Voici votre code de récupération de compte : <strong>$token</strong> </p>
                                        <p>Une fois retourné sur la page d'où vous étiez, tapez ce code afin de procéder à la récupération de votre compte pour ainsi définir un nouveau mot de passe.</p>
                                        <div style='text-align:center'>
                                            <img width='50%' src=$image_src alt='Illustration de la page'/>
                                        </div>
                                    </body>
                                </html>";
                $mail->AltBody = "<html>
                                    <head>
                                        <meta charset='utf-8'>
                                    </head>
                                    <body>
                                        <p>CEP-O Portail</p>
                                        <p>Voici votre code de réinitialisation de mot de passe: $token</p>
                                        <p>Une fois retourné sur la page d'où vous étiez, tapez ce code afin de procéder à la récupération de votre compte pour ainsi définir un nouveau mot de passe.</p>
                                    </body>
                                </html>";
                $mail->CharSet = 'UTF-8';
                $retour = $mail->send();
                $mail->SmtpClose();

                if($retour){
                    return 1;
                }else{
                    return 0;
                }
            } catch (phpmailerException $e) {
                return "Le message ne peut pas être envoyé. Mailer Error: ".$e->errorMessage();
            } catch (Exception $e) {
                return "Le message ne peut pas être envoyé. Exception Error: ".$e->getMessage();
            }
            
        }else{
            return 6;
        }

    }

    public function validateToken($email,$token){

        $myuser = $this->dbLink->query("SELECT * FROM t_user WHERE mailaddress=? AND `token`= ?",[$email,$token]);
        $rs =  $myuser->rowCount();
        if($rs == 1)
            $_SESSION['usrname'] = $myuser->fetch()->username;

        return $rs;
    }

    public function setPassword($newpass,$username){
        $pass = password_hash($newpass, PASSWORD_BCRYPT);
        $rs = $this->dbLink->query("UPDATE `t_user` SET `password`=?,`token`=? WHERE `username`=?",[$pass,NULL,$username]);
        return $rs->rowCount();
    }

    public function changePassword($param)
    {
        if($param['new-password'] === $param['new-password-again']){
          
          $req = "SELECT username,`password` FROM t_user WHERE t_user.username=?";
          $result = $this->dbLink->query($req,[$param['username']])->fetch();

          if($result && password_verify($param['actual-password'],$result->password))
            return $this->setPassword($param['new-password'],$param['username']);
          else
            return 4;
        }else{
          return 5;
        }
    }
}

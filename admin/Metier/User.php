<?php
session_start();
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

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
        require '../../vendor/autoload.php';
        $str = "123456789NBVCXWMLKJHGFDSQPOUYTREZA";
        $token = substr(str_shuffle(str_repeat($str,2)),0,4);
        
        $exist = $this->dbLink->query("SELECT COUNT(*) AS nbr FROM t_user WHERE mailaddress= ?",[$email])->fetch();
        if($exist->nbr == 1){
            $rs = $this->dbLink->query("UPDATE t_user SET token = ? WHERE mailaddress = ?", [$token,$email]);
            //return $rs;

            $mail = new PHPMailer(true);

            try {
                //Server settings
                $mail->SMTPDebug = 2;                                       // Enable verbose debug output
                $mail->isSMTP();                                            // Set mailer to use SMTP
                $mail->Host       = 'mail42.lwspanel.com;mail42.lwspanel.com';  // Specify main and backup SMTP se$
                $mail->SMTPAuth   = true;                                   // Enable SMTP authentication
                $mail->Username   = 'no-reply@obspemu.org';                     // SMTP username
                $mail->Password   = 'uK9$f_rpuC';                               // SMTP password
                $mail->SMTPSecure = 'tls';                                  // Enable TLS encryption, `ssl` also a$
                $mail->Port       = 587;                                    // TCP port to connect to

                //Recipients
                $mail->setFrom('no-reply@obspemu.org', 'CEP-O Portail');
                $mail->addAddress($email, 'PORTAIL User');     // Add a recipient
                //$mail->addAddress('ellen@example.com');               // Name is optional
                //$mail->addReplyTo('info@example.com', 'Information');
                //$mail->addCC('cc@example.com');
                //$mail->addBCC('bcc@example.com');é

                // Attachments
                //$mail->addAttachment('/var/tmp/file.tar.gz');         // Add attachments
                //$mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name

                // Content
                $mail->isHTML(true);                                  // Set email format to HTML
                $mail->Subject = utf8_encode('Récupération du mot de passe');
                $mail->Body    = "<html>
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
                $mail->AltBody = "<html>
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

                $mail->send();
                return 1;
            } catch (Exception $e) {
                return "Le message ne peut pas être envoyé. Mailer Error: {$mail->ErrorInfo}";
            }
            /*
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
            */
        }else{
            return 6;
        }
        
        
        
    }
}
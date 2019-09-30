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
        $rs = $this->dbLink->query("SELECT * FROM t_user WHERE username = ?", [$username]);
        
        if ($rs->rowCount()) {
            $myuser = $rs->fetch();
            if($myuser->status == 1){
                if (password_verify($password, $myuser->password)) {

                    $_SESSION['pseudoPsv'] = $myuser->username;
                    $_SESSION['nomsPsv'] = $myuser->fullname;
                    $_SESSION['avatarPsv'] = $myuser->avatar;
                    $_SESSION['tokenPsv'] = $myuser->token;
                    $_SESSION['usrpriority'] = $myuser->priority;
                    $_SESSION['nbr_changepass_try'] = 3;
    
                    return 1;
                }
                return 0;
            }
            return 6;
        }
        return 0;
    }

    public function sendToken($email){
        require '../../vendor/autoload.php';
        $str = "123456789NBVCXWMLKJHGFDSQPOUYTREZA";
        $token = substr(str_shuffle(str_repeat($str,2)),0,4);

        $rs = $this->dbLink->query("SELECT * FROM t_user WHERE mailaddress= ?",[$email]);
        if($rs->rowCount() == 1){
            if($rs->fetch()->status == 0)
                return 6;
            $rs = $this->dbLink->query("UPDATE t_user SET token = ? WHERE mailaddress = ?", [$token,$email]);
            return $rs->rowCount();
            
            $baseUrl = Helper::getURL(1);
            $image_src = $baseUrl.'/img/code-fill-page.png';
            //$resquest_uri = $_SERVER['REQUEST_URI'];

            
            $subject = 'CODE DE RECUPERATION DU COMPTE';
            $content = "<p><strong>CEP-O PEMU</strong> Portail</p>
                        <p>Voici votre code de récupération de compte : <strong>$token</strong> </p>
                        <p>Une fois retourné sur la page d'où vous étiez, tapez ce code afin de procéder à la récupération de votre compte pour ainsi définir un nouveau mot de passe.</p>
                        <div style='text-align:center'>
                            <img width='50%' src=$image_src alt='Illustration de la page'/>
                        </div>";
            $altContent = "<p>CEP-O Portail</p>
                            <p>Voici votre code de réinitialisation de mot de passe: $token</p>
                            <p>Une fois retourné sur la page d'où vous étiez, tapez ce code afin de procéder à la récupération de votre compte pour ainsi définir un nouveau mot de passe.</p>";
            
            return $this->sendMail($email,$subject,$content,$altContent);
            
            
        }else{
            return 7;
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

    public function lock($username)
    {
        return $this->dbLink->query("UPDATE `t_user` SET `status`=? WHERE `username`=?",[0,$username])->rowCount();
    }
    
    public function isLocked($email)
    {
        $rs = $this->dbLink->query("SELECT `status` FROM t_user WHERE mailaddress=?",[$email]);
        if($rs->rowCount() == 1)
            return $rs->fetch()->status;
        else
            return NULL;
    }

    public function all()
    {
        return $this->dbLink->query("SELECT * FROM t_user WHERE priority <> 'root' ORDER BY userID DESC ");
    }

    public function getList()
    {
        $rs = $this->dbLink->query("SELECT * FROM t_user ORDER BY userID DESC");
        $list = [];
        $i = 0;
        while ($data = $rs->fetch()) {
            $status = ($data->status == 0)? '<i class="fa fa-lock text-danger" text-danger"></i>':'';
            $actions = ($data->priority != 'root')? '<a href="#modifier" id="'.$data->userID.'" class="update" title="Modifier" data-placement="top" data-toggle="tooltip" style="margin-right:11px;" >
                                                        <i class="glyphicon glyphicon-pencil text-warning"></i>
                                                    </a>
                                                    <a href="#supprimer" id="'.$data->userID.'" class="delete" title="Supprimer" data-placement="top" data-toggle="tooltip" style="margin-right:11px;">
                                                        <i class="glyphicon glyphicon-trash text-danger"></i>
                                                    </a>':'';
            $list [] = [
                'position'  => ++$i,
                'username'  =>  $status.' '.$data->username,
                'fullname'  =>  $data->fullname,
                'email'  =>  $data->mailaddress,
                'phone'  =>  $data->phone,
                'actions'  => $actions
            ];
            
        }

        return $list;
    }

    public function add($param)
    {
        $username = $param['username'];
        $email = $param['email'];
        $rs = $this->dbLink->query("SELECT COUNT(*) AS nbr FROM t_user WHERE username=? ",[$param['username']]);
        if($rs->fetch()->nbr == 1)
            return 2;
        $rs = $this->dbLink->query("SELECT COUNT(*) AS nbr FROM t_user WHERE mailaddress=? ",[$param['email']]);
        if($rs->fetch()->nbr == 1)
            return 3;
        try {
            $this->dbLink->getLink()->beginTransaction();
            $pass = "Initial".date('s@W')."$";
            $req = "INSERT INTO t_user(username,password,fullname,phone,mailaddress,avatar,town,`status`) VALUES(:username,:password,:fullanme,:phone,:email,:avatar,:town,:status)";
            $this->dbLink->query($req,[
                'username'  =>  $username,
                'password'  =>  password_hash($pass, PASSWORD_BCRYPT),
                'fullanme'  =>  $param['fullname'],
                'phone'  =>  $param['phone'],
                'email'  =>  $email,
                'avatar'  =>  NULL,
                'town'  =>  $param['town'],
                'status'  =>  (isset($param['status']))? 1:0
            ]);

            $subject = 'VOTRE COMPTE DU PORTAIL PEMU';
            $content = "<p><strong>CEP-O PEMU</strong> Portail</p>
                        <p>Votre compte a été créé avec succès. </p>
                        <p>Votre nom d'utilisateur c'est $username et votre mot de passe c'est $pass</p>
                        <p>Utilisez cet identifiant pour vous connecter. Une fois connecté au portail, veuillez changer ce mot de passe.</p>
                        ";
            $altContent = $content;
            
            if($this->sendMail($email,$subject,$content,$altContent)){
                $this->dbLink->getLink()->commit();
                return 1;
            }
                
        } catch (\PDOException $e) {
            $this->dbLink->getLink()->rollBack();
            throw $e;
        } catch (\Exception $e) {
            $this->dbLink->getLink()->rollBack();
            throw $e;
        }
        
    }

    public function getById($id)
    {
        return $this->dbLink->query("SELECT * FROM t_user WHERE userID=?",[$id]);
    }

    public function update($user)
    {
        $req = "UPDATE t_user SET fullname=:fullname,phone=:phone,mailaddress=:email,town=:town,`status`=:etat WHERE username=:username";
        $rs = $this->dbLink->query($req,[
                                            'fullname'  =>  $user['fullname'],
                                            'phone'     =>  $user['phone'],
                                            'email'     =>  $user['email'],
                                            'town'      =>  $user['town'],
                                            'etat'      =>  (isset($user['status']) && $user['status'] == 'on')? 1:0,
                                            'username'  =>  $user['username'],
                                        ]);
        return $rs->rowCount();
    }

    public function sendMail($email,$subject,$content,$altContent)
    {
        require '../../vendor/autoload.php';
        $mail = new PHPMailer(true);

            try {
                //Server settings
                //$mail->SMTPDebug = 2;
                //$mail->SMTPDebug = SMTP::DEBUG_SERVER (2);                                  // Enable verbose debug output
                $mail->isSMTP();                                            // Set mailer to use SMTP
                $mail->Host       = 'mail42.lwspanel.com;mail42.lwspanel.com';  // Specify main and backup SMTP se$
                $mail->SMTPAuth   = true;                                   // Enable SMTP authentication
                $mail->Username   = 'no-reply@obspemu.org';                     // SMTP username
                $mail->Password   = 'yC3*!fes7k';                               // SMTP password
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
                $mail->Subject = $subject;
                $mail->Body    = "<html>
                                    <head>
                                        <meta charset='utf-8'>
                                    </head>
                                    <body>
                                        $content
                                    </body>
                                </html>";
                $mail->AltBody = "<html>
                                    <head>
                                        <meta charset='utf-8'>
                                    </head>
                                    <body>
                                    $altContent
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
                throw new Exception("Le message ne peut pas être envoyé. Mailer Error: ".$e->errorMessage(), 1);
            } catch (Exception $e) {
                throw new Exception("Le message ne peut pas être envoyé. Exception Error: ".$e->getMessage(), 1);
            }
    }
}

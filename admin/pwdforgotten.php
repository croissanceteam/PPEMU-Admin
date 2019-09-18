<?php 
    session_start();
    if (isset($_SESSION['pseudoPsv']) ) {
        header("location: dashboard.php") ;
    } //else die("ok");
    
    include_once 'Metier/Autoloader.php';
    Autoloader::register();
    
    //include_once 'Metier/User.php';
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>PEMU | Reset password</title>
  <!-- Tell the browser to be responsive to screen width -->
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <!-- Bootstrap 3.3.7 -->
  <link rel="stylesheet" href="bower_components/bootstrap/dist/css/bootstrap.min.css">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="bower_components/font-awesome/css/font-awesome.min.css">
  <!-- Ionicons -->
  <link rel="stylesheet" href="bower_components/Ionicons/css/ionicons.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="dist/css/AdminLTE.min.css">
  <!-- iCheck -->
  <link rel="stylesheet" href="plugins/iCheck/square/blue.css">
  <!-- Alertify -->
  <link rel="stylesheet" href="vendor/alertify/themes/alertify.css" />
  <link rel="stylesheet" href="dist/css/chstyle.css">

  <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
  <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
  <!--[if lt IE 9]>
  <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
  <![endif]-->

  <!-- Google Font -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
  
</head>

<body class="hold-transition login-page">
<div class="login-box">
  <div class="loader" style="display:none"></div>
  <div class="login-logo">
    <a href="index.php"><b>CEP-O PEMU</b> Admin</a>
  </div>
  <!-- /.login-logo -->
  <div class="login-box-body">
<?php 
//echo password_hash("123", PASSWORD_BCRYPT);
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['username']) && isset($_POST['password'])) {

  try {
    $user = new User();
    $log = $user->signin($_POST['username'],$_POST['password']);
    if($log){
      echo "<meta http-equiv='refresh' content='0; url = dashboard.php' />";
    }else{
      echo "<span style='color:red'>Mot de passe ou nom d'utilisateur</span> ";
    }
  } catch (\Throwable $th) {
      echo $th->getMessage();
      echo "<span style='color:red'>Echec de connexion</span> ";
  }  
}

?>
    <p class="login-box-msg" id="box-msg">Reinitialisation du mot de passe</p>

    <form method="post" >
      <div class="form-group has-feedback" id="mail-field">
        <input type="email" name="email" id="email" class="form-control" placeholder="Adresse email" required="required">
        <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
      </div>
      <div class="form-group has-feedback" id="token-field" style="display:none">
        <input type="text" name="token" id="token" class="form-control" placeholder="Code de récupération" autocomplete="off" required="required">
        <span class="glyphicon glyphicon-lock form-control-feedback"></span>
      </div>
      <div class="form-group has-feedback" id="newpass-field" style="display:none">
        <input type="password" name="newpass" id="newpass" class="form-control" placeholder="Tapez le nouveau mot de passe" autocomplete="off" required="required">
        <span class="glyphicon glyphicon-lock form-control-feedback"></span>
      </div>
      <div class="form-group has-feedback" id="newpass-field2" style="display:none">
        <input type="password" name="newpass2" id="newpass2" class="form-control" placeholder="Retapez-le ici" autocomplete="off" required="required">
        <span class="glyphicon glyphicon-lock form-control-feedback"></span>
      </div>
      <div class="row">
        <div class="hidden col-xs-8">
          <div class="checkbox icheck">
            <label>
              <input type="checkbox"> Remember Me
            </label>
          </div>
        </div>
        <!-- /.col -->
        <div class="col-xs-12" id="sending">
          <button id="send-token" class="btn btn-primary btn-block btn-flat">Envoyer</button>
        </div>
        <div class="col-xs-12" id="checking" style="display:none">
          <button id="check-token" class="btn btn-primary btn-block btn-flat">Valider</button>
        </div>
        <div class="col-xs-12" id="saving" style="display:none">
          <button type="button" id="save-pass" class="btn btn-primary btn-block btn-flat">Enregistrer</button>
        </div>
        <!-- /.col -->
      </div>
    </form>

    <div class="" style="margin-top: 2em"></div>
    <a href="index.php" id="login-url" style="display:none">Aller à la page de connexion</a>
    <!-- /.social-auth-links -->

<!--    <a href="inscription.php" class="text-center">S'inscrire ...</a>-->
<!--    <a href="../index.php" class="text-center pull-right" style="color:orange" >Accueil</a>-->

  </div>
  <!-- /.login-box-body -->
</div>
<!-- /.login-box -->

<!-- jQuery 3 -->
<script src="bower_components/jquery/dist/jquery.min.js"></script>
<!-- Bootstrap 3.3.7 -->
<script src="bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
<!-- iCheck -->
<script src="plugins/iCheck/icheck.min.js"></script>
<!-- alertify -->
<script src="vendor/alertify/lib/alertify.min.js"></script>
<!-- -->
<script src="dist/js/chscript-pwdfgt.js">
<script>
  $(function () {
    $('input').iCheck({
      checkboxClass: 'icheckbox_square-blue',
      radioClass: 'iradio_square-blue',
      increaseArea: '20%' /* optional */
    });
  });
  
</script>
</body>
</html>

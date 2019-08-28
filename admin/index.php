<?php 
    session_start(); 
    if (isset($_SESSION['pseudoPsv']) ) {
        header("location: dashboard.php") ;
    } //else die("ok");
    include'../sync/db.php';
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>PEMU | Log in</title>
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
  <div class="login-logo">
    <a href="index.php"><b>Croissance</b> HUB</a>
  </div>
  <!-- /.login-logo -->
  <div class="login-box-body">
<?php 

if (isset($_POST['submit'])) {

  $username = htmlspecialchars($_POST['psodo']);
  $password = htmlspecialchars(sha1($_POST['password']));

  $stmt_login = $db->prepare("SELECT * FROM utilisateurs WHERE pseudo=:username and psword=:password");
  $stmt_login->bindParam (':username' , $username , PDO::PARAM_STR );
  $stmt_login->bindParam (':password' , $password , PDO::PARAM_STR );
  $stmt_login->execute();

  if ($stmt_login->rowCount() == 1) {
    
    $row = $stmt_login->fetch() ;
    $noms = $row ['noms'];
    $user = $row ['pseudo'];
    $pass = $row ['psword'];
    $avatar = $row ['avatar'];
    $role = $row ['role'];
    $token = $row ['token'];

    if ($user == $username && $pass == $password) {

      $_SESSION['pseudoPsv'] = $user;
      $_SESSION['nomsPsv'] = $noms;
      $_SESSION['avatarPsv'] = $avatar;
      $_SESSION['rolePsv'] = $role;

      $_SESSION['tokenPsv'] = $token;

      //header ("location:dashboard");
      echo "<meta http-equiv='refresh' content='0; url = dashboard.php' />";
      
    }
    
  }

  else { echo "<span style='color:red'>MOT DE PASSE ou Nom d'Utilisateur</span> "; //$user; echo $pass;
/*   header ("location: login?login=error");   
   echo "<meta http-equiv='refresh' content='0; url = login?login=error' />";      
 */ }
  
}

?>
    <p class="login-box-msg">Sign in to start your session</p>

    <form action="index.php" method="post" >
      <div class="form-group has-feedback">
        <input type="text" name="psodo" class="form-control" placeholder="Pseudo" autocomplete="off" >
        <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
      </div>
      <div class="form-group has-feedback">
        <input type="password" name="password" class="form-control" placeholder="Mot de passe" autocomplete="off">
        <span class="glyphicon glyphicon-lock form-control-feedback"></span>
      </div>
      <div class="row">
        <div class="col-xs-8">
          <div class="checkbox icheck">
            <label>
              <input type="checkbox"> Remember Me
            </label>
          </div>
        </div>
        <!-- /.col -->
        <div class="col-xs-4">
          <button type="submit" name="submit" class="btn btn-primary btn-block btn-flat">Connexion</button>
        </div>
        <!-- /.col -->
      </div>
    </form>

    <!-- /.social-auth-links -->

    <a href="#">Mot de passe oublié.</a><br><br>
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

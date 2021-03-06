<?php
    session_start();
    if (isset($_SESSION['pseudoPsv']) ) {
        header("location: dashboard.php") ;
        //echo "<meta http-equiv='refresh' content='0; url = dashboard.php' />";
    }
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
<div id="cover-spin"></div>

  <div class="login-logo">
    <a href="index.php"><b>CEP-O PEMU</b> Admin</a>
  </div>
  <!-- /.login-logo -->
  <div class="login-box-body">
    <p class="login-box-msg">Connectez-vous</p>
    <a href="dashboard.php" id="home" style="display:none"></a>
    <form id="form-login" method="post">
      <div class="form-group has-feedback">
        <input type="text" name="username" id="username" class="form-control" placeholder="Nom d'utilisateur" autocomplete="off" required="required">
        <span class="glyphicon glyphicon-user form-control-feedback"></span>
      </div>
      <div class="form-group has-feedback">
        <input type="password" name="password" id="password" class="form-control" placeholder="Mot de passe" autocomplete="off" required="required">
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
        <div class="col-xs-12">
          <button type="submit" id="connect" class="btn btn-primary btn-block btn-flat">Connexion</button>
        </div>
        <!-- /.col -->
      </div>
    </form>

    <div class="" style="margin-top: 2em"></div>
    <!-- /.social-auth-links -->

    <!-- <a href="pwdforgotten.php">J'ai oublié mon mot de passe</a><br> -->
    <a href="#" data-toggle="modal" data-target="#pwdforgottenModal">J'ai oublié mon mot de passe</a><br>
<!--    <a href="inscription.php" class="text-center">S'inscrire ...</a>-->
<!--    <a href="../index.php" class="text-center pull-right" style="color:orange" >Accueil</a>-->

  </div>
  <!-- /.login-box-body -->
</div>
<!-- /.login-box -->

<div class="modal" id="pwdforgottenModal" tabindex="-1" role="dialog" data-backdrop="static">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
          
        </div>
        <div class="modal-body">
          <p>Veuillez contacter l'administrateur de l'application au <span style="font-weight:bold" id="admin-phone">(+243) 816 060 961</span> et lui addresser un mail sur <span style="font-weight:bold" id="admin-email">jpinshi@croissancehub.com</span></p>
        </div>
      </div>
    </div>
</div>

<!-- jQuery 3 -->
<script src="bower_components/jquery/dist/jquery.min.js"></script>
<!-- Bootstrap 3.3.7 -->
<script src="bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
<!-- iCheck -->
<script src="plugins/iCheck/icheck.min.js"></script>
<!-- alertify -->
<script src="vendor/alertify/lib/alertify.min.js"></script>
<script>
// Set the cursor ASAP to "Wait"
//document.body.style.cursor='wait';

// When the window has finished loading, set it back to default...
window.onload=function(){document.querySelector('#cover-spin').style.display="none";}

  $(document).ready(function(){
    $.ajax({
        type: 'POST',
        url: 'dist/userTrait.php',
        data: 'ad=',
        dataType: 'json',
        success: function(result){
          
          if(result.number == 1){
            document.getElementById('admin-phone').textContent = result.response.phone;
            document.getElementById('admin-email').textContent = result.response.email;
          }else{
            console.log('RESULT : ',result);
            
          }
        },
        error: function(result, statut, error){
          console.log('Resultat error :',result);
          console.log('Erreur :',error);
          console.log('Statut error : ',statut);
        }
      });


    $('#form-login').on('submit',function(e){
      e.preventDefault();
      var user = $('#username').val();
      var pwd = $('#password').val();
      document.querySelector('#cover-spin').style.display="block";
      $.ajax({
        type: 'POST',
        url: 'dist/userTrait.php',
        data: 'usr=' + user + '&pwd='+pwd,
        dataType: 'json',
        success: function(result){
          console.log('LOGIN RESULT : ', result);
          
          if(result.number == 1){
            document.getElementById('home').click();
          }else{
            alertify.error(result.response);
            document.querySelector('#cover-spin').style.display="none";
          }
        },
        error: function(result, statut, error){
          console.log('Resultat error :',result);
          console.log('Erreur :',error);
          console.log('Statut error : ',statut);
          alertify.error("L'opération n'a pas abouti.");
          document.querySelector('#cover-spin').style.display="none";
        }
      });
    });
  });
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

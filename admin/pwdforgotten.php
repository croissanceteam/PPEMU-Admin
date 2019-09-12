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

  <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
  <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
  <!--[if lt IE 9]>
  <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
  <![endif]-->

  <!-- Google Font -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
  <style>
  .loader{
    background: url('dist/img/preloader3.gif') 50% 50% no-repeat rgba(255, 255, 255, 0.8);
    cursor: wait;
    height: 100%;
    left: 0;
    position: fixed;
    top: 0;
    width: 100%;
    z-index: 9999;
  }
  </style>
</head>

<body class="hold-transition login-page">
<div class="login-box">
  <div class="loader" style="display:none">

  </div>
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
<script>
  $(function () {
    $('input').iCheck({
      checkboxClass: 'icheckbox_square-blue',
      radioClass: 'iradio_square-blue',
      increaseArea: '20%' /* optional */
    });
  });
  $(document).ready(function () {
      $('#send-token').on('click',function(e){
          //console.log('Hello');
            var email = document.getElementById('email').value;
                       
            console.log('EMAIL :',  email.trim() != "");
            
            if(email.trim() != ""){
              document.querySelector('.loader').style.display="block";
              $.ajax({
                type: 'POST',
                url: 'dist/userTrait.php',
                data: 'email=' + email + '&sendmail',
                dataType: 'json',
                success : function(result){
                    console.log('SENDING RESULT : ', result);
                    console.log('RESULT RESPONSE : ', result.response);
                    document.querySelector('.loader').style.display="none";
                    if(result.number == 1){
                      
                      document.querySelector('#token-field').style.display="block";
                      document.querySelector('#mail-field').style.display="none";
                      document.querySelector('#sending').style.display="none";
                      document.querySelector('#checking').style.display="block";
                      document.querySelector('#box-msg').textContent = "Saisissez le code qui vous a été envoyé par mail";
                      //document.querySelector('#send-token').text = "Valider";
                      alertify.alert(result.response);
                    }else{
                      alertify.error(result.response);
                    }
                },
                error: function (error) {
                    document.querySelector('.loader').style.display="none";
                    alertify.error("L'opération n'a pas abouti!");
                    console.log("ERROR :",error);
                    console.log("ERROR :",error.responseText);
                }
              });
            }else{
               document.getElementById('email').value = "";
            }
            
            
      });
      $('#check-token').on('click',function(e){
          //alertify.alert('ZELA');
          var email = document.getElementById('email').value;
          var token = document.getElementById('token').value;
          console.log(email,token);
          
          console.log('TOKEN LENGTH', token.length);
          if(token.length == 4){
            document.querySelector('.loader').style.display="block";
              $.ajax({
                type: 'POST',
                url: 'dist/userTrait.php',
                data: 'email=' + email+'&token='+token,
                dataType: 'json',
                success : function(result){
                    console.log('TOKEN VALIDATING RESULT : ', result);
                    console.log('RESULT RESPONSE : ', result.response);
                    document.querySelector('.loader').style.display="none";
                    if(result.number == 1){
                      document.querySelector('#box-msg').textContent = "Nouveau mot de passe";
                      document.querySelector('#newpass-field').style.display="block";
                      document.querySelector('#newpass-field2').style.display="block";
                      document.querySelector('#token-field').style.display="none";
                      document.querySelector('#checking').style.display="none";
                      document.querySelector('#saving').style.display="block";
                      document.querySelector('#saving2').style.display="block";
                      
                      //document.querySelector('#send-token').text = "Enregistrer";
                      alertify.log(result.response);
                    }else{
                      alertify.error(result.response);
                    }
                },
                error: function (error) {
                    document.querySelector('.loader').style.display="none";
                    alertify.error("L'opération n'a pas abouti!");
                    console.log("ERROR :",error.responseText);
                    
                }
              });
          }else{
            alertify.error("Code incorrect");
          }
      });
      $('#save-pass').on('click',function(e){
        var newpass = document.getElementById('newpass').value;
        var newpass2 = document.getElementById('newpass2').value;
        console.log('Pass:',newpass);
        console.log('Pass2:',newpass2);
        if(newpass.trim() != "" && newpass2.trim() != ""){
          if(newpass == newpass2){
            $.ajax({
                type: 'POST',
                url: 'dist/userTrait.php',
                data: 'newpass=' + newpass,
                dataType: 'json',
                success : function(result){
                    //console.log('NEW PASS RESULT : ', result);
                    //console.log('RESULT RESPONSE : ', result.response);
                    //alert('RESULT RESPONSE : ', result.response);
                    document.querySelector('.loader').style.display="none";
                    if(result.number == 1){
                      document.getElementById('login-url').click();
                      
                      //alertify.log(result.response);
                    }else{
                      alertify.error(result.response);
                    }
                },
                error: function (error) {
                    console.log("ERROR :",error);
                    document.querySelector('.loader').style.display="none";
                    //document.getElementById('login-url').click();
                    alert(error);
                    alertify.error("L'opération n'a pas abouti!");
                    console.log("ERROR :",error.responseText);
                    
                }
              });
          }else{
            alertify.error("Attention, veuillez taper la même chose dans les deux zones de saisie!");
          }
        }
        
      });
  });
</script>
</body>
</html>

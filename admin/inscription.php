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
  <title>Photos Souvenir | Log in</title>
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
    <a href="index.php"><b>Photos</b> Souvenir</a>
  </div>
  <!-- /.login-logo -->
  <div class="login-box-body" style="color: orange">
<?php 

if (isset($_POST['submit'])) {

    $psdo = htmlspecialchars($_POST['pseudo']);
    $noms = htmlspecialchars($_POST['noms']);
    $ville = htmlspecialchars($_POST['ville']);
    $password = htmlspecialchars(md5($_POST['password']));
    
    $token="p".time();

    $QInscr=$db->query("SELECT * FROM utilisateurs WHERE pseudo = '$psdo'");
    $response = array();
    
    if ($QInscr->rowCount() > 0){
		 echo "<span style='color:red'>Pseudo ou Téléphone déjà Enregistré ... Ré-essayer un autre numero.</span> "; 
    }
    else {
    $query_save = $db->query( "INSERT INTO utilisateurs (`id`, `noms`, `pseudo`, `psword`, `telephone`, `email`, `ville`, `avatar`, `role`, `lastModif`, `token`, `etat`) VALUES (null, '$noms', '$psdo', '$password', '$psdo', '', '$ville', '', 'user', ".(time()*1000).", '".$token."', 1)") or die ('Erreur SQL ! '.'<br>'.$db->errorInfo()); 

    if($query_save){
        $_SESSION['pseudoPsv'] = $psdo;
        $_SESSION['nomsPsv'] = $noms;
        $_SESSION['avatarPsv'] = "";
        $_SESSION['rolePsv'] = 'user';

        $_SESSION['tokenPsv'] = $token;
        //echo "<meta http-equiv='refresh' content='0; url = dashboard.php' />";
    }

  else { 
      echo "<span style='color:red'>Information Non Enregistrée ... Ré-essayer Plus tards.</span> "; //$user; echo $pass;
    }
    }
  
}

?>
    <p class="login-box-msg">Création Compte.</p>
    
    <?php 
        if (isset($_POST['submit']) && isset($_SESSION['pseudoPsv']) ) {
    ?>
    <p class="login-box-msg">INSCRIPTION REUSSIE !!</p>
    <h1 style="text-align: center"><span class="glyphicon glyphicon-ok"></span></h1>
                
            <h6 style="text-align: justify; margin: 0 5%; color: black">
                Un sms de confirmation a été envoyé a votre numéro de téléphone pour confirmation.
            </h6><br>
                <a href="dashboard.php">Continuer</a> <br><br><br>
                
            <h6 style="text-align: justify; margin: 0 5%">
                Votre compte Photos Souvenir vous donne la possibilité de s'abonner à un evantuel rendez-vous de séance photo, ou avoir une image memoriale avec une Personalité, Autorité, Artiste, Star, ou votre Idole ...
            </h6><br>
                <a href="rdv.php">Prendre un Rendez-Vous</a> <br><br>
    <?php 
        } else {
    ?>
    <form action="inscription.php" method="post" >
      <div class="form-group has-feedback">
        <input type="text" name="pseudo" class="form-control" placeholder="Téléphone, ex:2438xxxxxxxxx" autocomplete="off">
        <span class="glyphicon glyphicon-phone form-control-feedback"></span>
      </div>
      <div class="form-group has-feedback">
        <input type="text" name="noms" class="form-control" placeholder="Votre nom ou Prénom" autocomplete="off">
        <span class="glyphicon glyphicon-user form-control-feedback"></span>
      </div>
      <div class="form-group has-feedback">
        <input type="password" name="password" class="form-control" placeholder="Mot de passe" autocomplete="off">
        <span class="glyphicon glyphicon-lock form-control-feedback"></span>
      </div>
      <div class="form-group has-feedback">
        <select id="pays" name="pays" class="form-control">
            <option value="0">--- Pays ---</option>
            <option value="180">République Démocratique du Congo</option>
            <?php 
                $rdvP=$db->query("SELECT nom, abr, token FROM pays ");

                $nb=0;
                while($rR=$rdvP->fetch(PDO::FETCH_ASSOC)){
                    $nb++;
            ?>
                <option value="<?php echo $rR['token']; ?>"> <?php echo $rR['nom']; ?></option>
            <?php 
                }
            ?>
        </select>
      </div>
      <div class="form-group has-feedback">
        <select id="ville" name="ville" class="form-control">
            <option value="0">--- Villes ---</option>
        </select>
      </div>
      <div class="row">
        <div class="col-xs-8">
          <div class="checkbox icheck">
          </div>
        </div>
        <!-- /.col -->
        <div class="col-xs-4">
          <button type="submit" name="submit" class="btn btn-primary btn-block btn-flat">Valider</button>
        </div>
        <!-- /.col -->
      </div>
    </form>
    
    <a href="index.php">J'ai déjà un compte.</a><br>
    <?php 
        }
    ?>
    <!-- /.social-auth-links -->


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
      
    //TODO:: Liste des villes par pays
    $('#pays').on('change', function(){
        $('#ville').empty();
        $('#ville').append('<option value="0"> --- Villes --- </option>');
        if($(this).val()!='0'){
            $.ajax({
                type:'get',
                url:'dist/fiche_Json.php',
                data:'listVilles' + '&pays=' + $(this).val(),
                dataType:'json',
                success: function(json){
                    $.each(json, function(index, value){
                        $('#ville').append('<option value="'+ index + '">'+ value + '</option>');
            });}});
        }
    });
      
    $('input').iCheck({
      checkboxClass: 'icheckbox_square-blue',
      radioClass: 'iradio_square-blue',
      increaseArea: '20%' /* optional */
    });
  });
</script>
</body>
</html>

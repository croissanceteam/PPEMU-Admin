<?php 
    session_start(); 
    if (!isset($_SESSION['pseudoPsv']) && $_SESSION['usrpriority'] != 'root') {
        header("location: index.php") ;
    }
    include_once 'Metier/Autoloader.php';
    Autoloader::register();
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>PPEMU | Gestion d'utilisateurs</title>
  <!-- Tell the browser to be responsive to screen width -->
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <!-- Bootstrap 3.3.7 -->
  <link rel="stylesheet" href="bower_components/bootstrap/dist/css/bootstrap.min.css">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="bower_components/font-awesome/css/font-awesome.min.css">
  <!-- Ionicons -->
  <link rel="stylesheet" href="bower_components/Ionicons/css/ionicons.min.css">
  <!-- jvectormap -->
  <link rel="stylesheet" href="bower_components/jvectormap/jquery-jvectormap.css">
  <!-- DataTables -->
  <link rel="stylesheet" href="plugins/datatables/dataTables.bootstrap.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="dist/css/AdminLTE.min.css">
  <link rel="stylesheet" href="dist/css/chstyle.css">
<!--  <link rel="stylesheet" href="DataTables/DataTables-1.10.18/css/jquery.dataTables.css">-->
  <!-- AdminLTE Skins. Choose a skin from the css/skins
       folder instead of downloading all of them to reduce the load. -->
  <link rel="stylesheet" href="dist/css/skins/_all-skins.min.css">
  <!-- Alertify -->
  <link rel="stylesheet" href="vendor/alertify/themes/alertify.css" />
  <!-- Switchery -->
  <link rel="stylesheet" href="plugins/switchery/dist/switchery.min.css" />

  <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
  <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
  <!--[if lt IE 9]>
  <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
  <![endif]-->
  

  <!-- Google Font -->
  <link rel="stylesheet"
        href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
</head>
<body class="hold-transition skin-blue sidebar-mini">
<div id="cover-spin"></div>
<div class="wrapper">

<?php include_once 'partials/header.php' ?>
  <!-- Left side column. contains the logo and sidebar -->
  <aside class="main-sidebar">
    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">
      <!-- Sidebar user panel -->
      <div class="user-panel">
        <div class="pull-left image">
          <img src="dist/img/user2-160x160.jpg" class="img-circle" alt="User Image">
        </div>
        <div class="pull-left info">
          <p><?php echo $_SESSION['nomsPsv'] ?></p>
          <a href="#"><i class="fa fa-circle text-success"></i> Connecté</a>
        </div>
      </div>
      <!-- search form -->
      <form action="#" method="get" class="sidebar-form">
        <div class="input-group">
          <input type="text" name="q" class="form-control" placeholder="rechercher...">
          <span class="input-group-btn">
                <button type="submit" name="search" id="search-btn" class="btn btn-flat">
                  <i class="fa fa-search"></i>
                </button>
              </span>
        </div>
      </form>
      <!-- /.search form -->
      <!-- sidebar menu: : style can be found in sidebar.less -->
      <ul class="sidebar-menu" data-widget="tree">
        <li class="header">MENU</li>
        <li>
          <a href="dashboard.php"><i class="fa fa-dashboard"></i> Tableau de bord</a>
        </li>
        <li >
          <a href="import.php">
            <i class="fa fa-cloud-download active"></i> <span>Récupération automatique</span>
          </a>
        </li>
        <li >
          <a href="cleaning.php">
            <i class="fa fa-check-square-o"></i> <span>Cleaning Data</span>
          </a>
        </li>
        
        <li >
          <a href="clean.php">
            <i class="fa fa-check-square-o"></i> <span>Résumé du Cleaning</span>
          </a>
        </li>
        <li>
          <a href="journal.php">
            <i class="fa fa-list"></i> <span>Journal d'anomalies</span>
          </a>
        </li >
        <?php if(isset($_SESSION['usrpriority']) && $_SESSION['usrpriority'] == 'root') : ?>
        <li class="header">PARAMETRES</li>
        <li class="active">
            <a href="sresu.php"><i class="fa fa-users"></i> <span>Gestion d'utilisateurs</span></a>
        </li>

        <?php endif ?>
        
      </ul>
    </section>
    <!-- /.sidebar -->
  </aside>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        Gestion d'utilisateurs
      </h1>
      
    </section>

    <!-- Main content -->
    <section class="content">
      <!-- Info boxes -->
      
      <!-- /.row -->
        
      <?php $user = new User() ?>
      <div class="row">
       
        <div class="col-md-12">
          <div class="box box-info">
            <div class="box-header with-border">
              <h3 class="box-title">
                <button type="button" class="btn btn-primary add" data-toggle="modal" data-target="#newUserModal"><i class="fa fa-user-plus"></i>&nbsp;Ajouter</button>
              </h3>

              <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                </button>
                <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
              </div>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
              <table id="users-table"  class="table table-hover">
                  <thead>
                      <tr>
                          <th>#</th>
                          <th></th>
                          <th>Nom d'utilisateur</th>
                          <th>Nom complet</th>
                          <th>Adresse e-mail</th>
                          <th>Téléphone</th>
                          <th>Ville</th>
                          <th>Etat</th>
                      </tr>
                  </thead>
                  <tbody style="cursor:pointer">
                  
                  </tbody>
                  
              </table>
            </div>
            <!-- ./box-body -->
          </div>
          <!-- /.box -->
        </div>
       
        
        <!-- /.col -->
      </div>
      
      <!-- /.row -->
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->

<!-- MODALS -->

  <!-- NEW USER-->
<div class="modal" id="newUserModal" tabindex="-1" role="dialog" data-backdrop="static">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Nouvel utilisateur</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form id="new-user-form">
        <div class="modal-body">
          <div class="row">
          <div class="form-group col-md-6">
          <input type="hidden" class="form-control" name="add" >
            <label class="col-form-label required">Nom d'utilisateur</label>
            <input type="text" class="form-control" name="username" placeholder="Nom d'utilisateur"  required>
          </div>
          <div class="form-group col-md-6">
            <label class="col-form-label required">Nom complet</label>
            <input type="text" class="form-control" name="fullname" placeholder="Nom complet" required>
          </div>
          <div class="form-group col-md-6">
            <label class="col-form-label">Numéro de téléphone</label>
            <div class="input-group">
              <div class="input-group-addon">
                <i class="fa fa-phone"></i>
              </div>
              <input type="text" class="form-control" name="phone" data-inputmask='"mask": "(999) 999-999-999"' data-mask>
            </div>
          </div>
          <div class="form-group col-md-6">
            <label class="col-form-label required">Adresse e-mail</label>
            <input type="email" class="form-control" name="email" placeholder="Adresse e-mail" required>
          </div>
          <div class="form-group col-md-6">
            <label class="col-form-label">Ville</label>
            <select class="form-control" name="town" required>
              <option value="KINSHASA" selected="selected">KINSHASA</option>
              <option value="MATADI">MATADI</option>
              <option value="LUBUMBASHI">LUBUMBASHI</option>
            </select>
            
          </div>
          <div class="form-group col-md-6">
            
            <input type="checkbox" name="status" class="js-switch"  checked />
            <label class="col-form-label" id="status-label">Actif</label>
          </div>
          </div>
          <!-- /.row -->
          
        </div>
        <!-- /.modal-body -->
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Enregistrer</button>
          <button type="reset" class="btn btn-default" data-dismiss="modal" onclick="this.form.reset();">Annuler</button>
        </div>
        <!-- /.modal-footer -->
      </form>
    </div>
  </div>
</div>

<!-- UPDATE-->
<div class="modal" id="updateUserModal" tabindex="-1" role="dialog" data-backdrop="static">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Modification des informations de l'utilisateur</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form id="update-user-form">
        <div class="modal-body">
          <div class="row">
          <div class="form-group col-md-6">
          <input type="hidden" class="form-control" name="update" id="update" >
            <label class="col-form-label required">Nom d'utilisateur</label>
            <input type="text" class="form-control" name="username" id="username2" readonly>
          </div>
          <div class="form-group col-md-6">
            <label class="col-form-label required">Nom complet</label>
            <input type="text" class="form-control" name="fullname" id="fullname" required>
          </div>
          <div class="form-group col-md-6">
            <label class="col-form-label">Numéro de téléphone</label>
            <input type="text" class="form-control" name="phone" id="phone">
          </div>
          <div class="form-group col-md-6">
            <label class="col-form-label required">Adresse e-mail</label>
            <input type="email" class="form-control" name="email" id="email" required>
          </div>
          <div class="form-group col-md-6">
            <label class="col-form-label">Ville</label>
            <select class="form-control" name="town" id="town" required>
              <option value="KINSHASA" selected="selected">KINSHASA</option>
              <option value="MATADI">MATADI</option>
              <option value="LUBUMBASHI">LUBUMBASHI</option>
            </select>
          </div>
          <div class="form-group col-md-6">
            
            <input type="checkbox" name="status" id="status" class="js-switch-update"  checked />
            <label class="col-form-label" id="status-label2">Actif</label>
          </div>
          </div>
          <!-- /.row -->
          
        </div>
        <!-- /.modal-body -->
        <div class="modal-footer">
          <button type="button" class="btn btn-default pull-left" id="reset-pass">Reinitialiser le mot de passe</button>
          <button type="submit" class="btn btn-primary">Enregistrer</button>
          <button type="button" class="btn btn-info" data-dismiss="modal">Annuler</button>
        </div>
        <!-- /.modal-footer -->
      </form>
    </div>
  </div>
</div>

<!-- MODALS -->
  <?php include_once 'partials/footer.php' ?>

  <!-- /.control-sidebar -->
  <!-- Add the sidebar's background. This div must be placed
       immediately after the control sidebar -->
  <div class="control-sidebar-bg"></div>

</div>
<!-- ./wrapper -->

<!-- jQuery 3 -->
<script src="bower_components/jquery/dist/jquery.min.js"></script>
<!-- Bootstrap 3.3.7 -->
<script src="bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
<!-- DataTables -->
<script src="plugins/datatables/jquery.dataTables.min.js"></script>
<script src="plugins/datatables/dataTables.bootstrap.min.js"></script>
<!-- InputMask -->
<script src="plugins/input-mask/jquery.inputmask.js"></script>
<script src="plugins/input-mask/jquery.inputmask.date.extensions.js"></script>
<script src="plugins/input-mask/jquery.inputmask.extensions.js"></script>
<!-- FastClick -->
<script src="bower_components/fastclick/lib/fastclick.js"></script>
<!-- AdminLTE App -->
<script src="dist/js/adminlte.min.js"></script>
<!-- Sparkline -->
<script src="bower_components/jquery-sparkline/dist/jquery.sparkline.min.js"></script>
<!-- jvectormap  -->
<script src="plugins/jvectormap/jquery-jvectormap-1.2.2.min.js"></script>
<script src="plugins/jvectormap/jquery-jvectormap-world-mill-en.js"></script>
<!-- SlimScroll -->
<script src="bower_components/jquery-slimscroll/jquery.slimscroll.min.js"></script>
<!-- ChartJS -->
<script src="bower_components/chart.js/Chart.js"></script>
<!-- alertify -->
<script src="vendor/alertify/lib/alertify.min.js"></script>
<!-- Switchery -->
<script src="plugins/switchery/dist/switchery.min.js"></script>
<!-- AdminLTE dashboard demo (This is only for demo purposes) -->
<script src="dist/js/pages/dashboard2.js"></script>
<!-- AdminLTE for demo purposes -->
<script src="dist/js/demo.js"></script>
<!-- AdminLTE for demo purposes -->
<script src="dist/js/chscript.js"></script>
<!-- AdminLTE for demo purposes -->
<script src="dist/js/chscript-usr.js"></script>
<script>
    window.onload=function(){document.querySelector('#cover-spin').style.display="none";}
</script>
</script>
</body>
</html>

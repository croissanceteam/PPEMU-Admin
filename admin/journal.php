<?php 
    session_start(); 
    if (!isset($_SESSION['pseudoPsv']) ) {
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
  <title>PPEMU | Journal d'anomalies</title>
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
  <!-- Theme style -->
  <link rel="stylesheet" href="dist/css/AdminLTE.min.css">
<!--  <link rel="stylesheet" href="DataTables/DataTables-1.10.18/css/jquery.dataTables.css">-->
  <!-- AdminLTE Skins. Choose a skin from the css/skins
       folder instead of downloading all of them to reduce the load. -->
  <link rel="stylesheet" href="dist/css/skins/_all-skins.min.css">
  <!-- Alertify -->
  <link rel="stylesheet" href="vendor/alertify/themes/alertify.css" />

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
        <!--
        <li class="active treeview menu-open">
          <a href="#">
            <i class="fa fa-dashboard"></i> <span>MENU</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li ><a href="dashboard.php"><i class="fa fa-circle-o"></i> Tableau de bord</a></li>
          </ul>
        </li>
        -->
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
        <li class="active">
          <a href="journal.php">
            <i class="fa fa-list"></i> <span>Journal d'anomalies</span>
          </a>
        </li>
        <?php if(isset($_SESSION['usrpriority']) && $_SESSION['usrpriority'] == 'root') : ?>
        <li class="header">PARAMETRES</li>
        <li>
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
        Journal d'anomalies
      </h1>
      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Acceuil</a></li>
        <li class="active">Journal d'anomalies</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">
      <!-- Info boxes -->
      
      <!-- /.row -->
        <?php 
            $reperage = new Reperage();
        ?>
      
      <div class="row">
        <div class="col-md-12">
          <div class="box box-info">
            <div class="box-header with-border">
              <h3 class="box-title">Traitement</h3>
            </div>
            <!-- /.box-header -->
            <!-- form start -->
            <form id="formPhotographe" method="post" role="form">
                
              <div class="box-body">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="typeDonnee">Type des données</label>
                            <select id="typeDonnee" class="selectAnomalie form-control" >
                                <option value="">Séléctionnez</option>
                                <option value="Reperage">Parcelles géo-référencées</option>
                                <option value="Realisation">Branchements réalisés</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="lot">Lot</label>
                            <select id="lot" class="selectAnomalie form-control"  disabled>
                                <option value="">Séléctionnez Lot</option>
                                    <option value="1">Lot 1</option>
                                    <option value="2">Lot 2</option>
                                    <option value="3">Lot 3</option>
                                    <option value="4">Lot 4</option>
                                    <option value="5">Lot 5</option>
                                    <option value="6">Lot 6</option>
                                    <option value="7">Lot 7</option>
                                    <option value="8">Lot 8</option>
                                    <option value="9">Lot 9</option>
                                    <option value="10">Lot 10</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="anomalie">Type Anomalies</label>
                            <select id="anomalie" class="selectAnomalie form-control" disabled >
                                <option value="">Séléctionnez Anomalie</option>
                                <option value="1">Brchnt réalisé saisi avec clé sans OBS</option>
                                <option value="2">Doublon</option>
                                <option value="3">Le deux</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <br>
                            <button type="button" class="btn btn-info" dir="" id="btn_export" style="display:none" >
                                <img src="./dist/img/ajax-loader.gif" align="center" class="loading" style="display:none" width="20">
                                <i class="fa fa-share-square-o" class="iExport"></i> &nbsp;&nbsp;
                                Exporter la liste &nbsp;&nbsp;
                            </button>
                        </div>
                    </div>
                </div>
                
              </div>
              <!-- /.box-footer -->
            </form>
          </div>
          <!-- /.box -->
        </div>
        <!-- /.col -->
      </div>
      

      <div class="row">
       
        <div class="col-md-12">
          <div class="box">
            <div class="box-header with-border">
              <h3 class="box-title">Liste détaillée</h3>

              <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                </button>
                <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
              </div>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
              <div class="row">
                <div class="col-md-12">
                    <div class="box-body table-responsive no-padding">
                      <table id="example2" class="table table table-bordered table-striped table-hover">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th width="7%">Lot</th>
                            <th>Client</th>
                            <th>Réf. Client</th>
                            <th>Numéro site erroné</th>
                            <th>Agent contrôleur</th>
                            <th width="17%">Anomalie trouvée</th>
                        </tr>
                        </thead>
                        <tbody id="listDataAnomalies"></tbody>
                      </table>
                    </div>
                </div>
                <!-- /.col -->
              </div>
              <!-- /.row -->
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
<script src="bower_components/datatables.net/js/jquery.dataTables.min.js"></script>
<script src="bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js"></script>
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
<!-- AdminLTE dashboard demo (This is only for demo purposes) -->
<script src="dist/js/pages/dashboard2.js"></script>
<!-- AdminLTE for demo purposes -->
<script src="dist/js/demo.js"></script>
<!-- AdminLTE for demo purposes -->
<script src="dist/js/chscript.js"></script>
<script src="dist/script.js"></script>
<script>
  $(function () {
    $('#example1').DataTable()
    $('#example2').DataTable({
      'paging'      : true,
      'lengthChange': true,
      'searching'   : true,
      'ordering'    : true,
      'info'        : true,
      'autoWidth'   : true
    })
  })
</script>
</body>
</html>

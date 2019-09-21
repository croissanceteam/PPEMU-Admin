<?php 
    session_start(); 
    if (!isset($_SESSION['pseudoPsv']) ) {
        header("location: index.php") ;
    }
    include_once 'Metier/Autoloader.php';
    Autoloader::register();
    
?>
 <?php
     $rapport = new RapportOperation();
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>PEMU | Tableau de bord</title>
  <!-- Tell the browser to be responsive to screen width -->
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <!-- Bootstrap 3.3.7 -->
  <link rel="stylesheet" href="bower_components/bootstrap/dist/css/bootstrap.min.css">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="bower_components/font-awesome/css/font-awesome.min.css">
  <!-- Ionicons -->
  <link rel="stylesheet" href="bower_components/Ionicons/css/ionicons.min.css">
  <!-- DataTables -->
  <link rel="stylesheet" href="bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="dist/css/AdminLTE.min.css">
  <link rel="stylesheet" href="dist/css/chstyle.css">
  <!-- AdminLTE Skins. Choose a skin from the css/skins
       folder instead of downloading all of them to reduce the load. -->
  <link rel="stylesheet" href="dist/css/skins/_all-skins.min.css">

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
<div id="cover-spin"></div>
<?php include_once 'partials/header.php' ?>
  <!-- Left side column. contains the logo and sidebar -->
  <aside class="main-sidebar">
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
            <li class="active"><a href="dashboard.php"><i class="fa fa-dashboard"></i> Tableau de bord</a></li>
          </ul>
        </li>
        -->
        <li class="active">
          <a href="dashboard.php"><i class="fa fa-dashboard"></i> Tableau de bord</a>
        </li>
        <li>
          <a href="import.php">
            <i class="fa fa-cloud-download"></i> <span>Récupération automatique</span>
          </a>
        </li>
        <li>
          <a href="cleaning.php">
            <i class="fa fa-check-square-o"></i> <span>Cleaning Data</span>
          </a>
        </li>
        <li>
          <a href="clean.php">
            <i class="fa fa-check-square-o"></i> <span>Résumé du Cleaning</span>
          </a>
        </li>
        <li>
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
  </aside>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        PPEMU&nbsp;
        <small>Version 1.0</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Accueil</a></li>
        <li class="active">Tableau de bord</li>
      </ol>
    </section>
    <!-- Main content -->
    <section class="content">
    <div class="row">
        <div class="col-md-3 col-sm-6 col-xs-12">
          <div class="info-box">
            <span class="info-box-icon bg-aqua"><i class="fa fa-cloud-download"></i></span>

            <div class="info-box-content">
              <!-- <span class="info-box-text">Import Data</span>-->
              <span class="info-box-number">
                  <small><a href="import.php">Récupération automatique </a></small>
              </span>
            </div>
            <!-- /.info-box-content -->
          </div>
          <!-- /.info-box -->
        </div>
        <div class="col-md-3 col-sm-6 col-xs-12">
          <div class="info-box">
            <span class="info-box-icon bg-red"><i class="fa fa-check-square-o"></i></span>

            <div class="info-box-content">
               <!-- <span class="info-box-text">Cleaning Data</span>-->
              <span class="info-box-number">
                <small><a href="cleaning.php">Cleaning Data</a></small>
            </span>
            </div>
            <!-- /.info-box-content -->
          </div>
          <!-- /.info-box -->
        </div>
        <!-- /.col -->

        <!-- fix for small devices only -->
        <div class="clearfix visible-sm-block"></div>

        <div class="col-md-3 col-sm-6 col-xs-12">
          <div class="info-box">
            <span class="info-box-icon bg-green"><i class="fa fa-list"></i></span>

            <div class="info-box-content">
               <!--  <span class="info-box-text">Journal</span>-->
              <span class="info-box-number"><small><a href="journal.php">Journal d'anomalies</a></small></span>
            </div>
            <!-- /.info-box-content -->
          </div>
          <!-- /.info-box -->
        </div>
        <!-- /.col -->
        <div class="col-md-3 col-sm-6 col-xs-12">
          <div class="info-box">
            <span class="info-box-icon bg-yellow"><i class="fa fa-history"></i></span>

            <div class="info-box-content">
            <!--    <span class="info-box-text">Correction</span>-->
              <span class="info-box-number">
                 <small><a href="import.php#Correction">Correction des données erronées</a></small>
                </span>
            </div>
            <!-- /.info-box-content -->
          </div>
          <!-- /.info-box -->
        </div>
        <!-- /.col -->
      </div>
      <div class="row">
        <div class="col-xs-12">

          <div class="box">
            <div class="box-header">
              <h3 class="box-title">Résumé des Opérations</h3>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
              <table id="example1" class="table table-bordered table-striped">
                <thead>
                <tr>
                          <th>#</th>
                          <th>Opérations</th>
                          <th>Description</th>
                          <th>Date</th>
                </tr>
                </thead>
                <tbody>
                <?php
                            $resData=$rapport->getJournales(); 
    
                            if ($resData) {
                                $nb=0;
                                foreach ($resData as $cus) {
                                $nb++;
                        ?>
                        <tr>
                            <td><?php echo $nb; ?></td>
                            <td><?php echo $cus->operation; ?></td>
                            <td><?php echo $cus->detail_operation; ?></td>
                            <td>
                                <?php 
                                    echo $cus->dateOperation;
                                ?>
                            </td>
                        </tr>
                        <?php 
                                }
                            }
                        
                        ?>
                </tbody>
                <tfoot>
                <tr>
                          <th>#</th>
                          <th>Opérations</th>
                          <th>Description</th>
                          <th>Date</th>
                </tr>
                </tfoot>
              </table>
            </div>
            <!-- /.box-body -->
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
</div>
<!-- ./wrapper -->

<!-- jQuery 3 -->
<script src="bower_components/jquery/dist/jquery.min.js"></script>
<!-- Bootstrap 3.3.7 -->
<script src="bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
<!-- DataTables -->
<script src="bower_components/datatables.net/js/jquery.dataTables.min.js"></script>
<script src="bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js"></script>
<!-- SlimScroll -->
<script src="bower_components/jquery-slimscroll/jquery.slimscroll.min.js"></script>
<!-- FastClick -->
<script src="bower_components/fastclick/lib/fastclick.js"></script>
<!-- AdminLTE App -->
<script src="dist/js/adminlte.min.js"></script>
<!-- AdminLTE for demo purposes -->
<script src="dist/js/demo.js"></script>
<!-- page script -->
<script>
  window.onload=function(){document.querySelector('#cover-spin').style.display="none";}
  $(function () {
    $('#example1').DataTable()
    $('#example2').DataTable({
      'paging'      : true,
      'lengthChange': false,
      'searching'   : false,
      'ordering'    : true,
      'info'        : true,
      'autoWidth'   : false
    })
  })
</script>
</body>
</html>

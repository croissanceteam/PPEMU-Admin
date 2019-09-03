<?php 
    session_start(); 
    if (!isset($_SESSION['pseudoPsv'])) {
        header("location: index.php") ;
    }//else die ('dfddf');
    //include'../sync/db.php';
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>PEMU | Importation</title>
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
          <a href="#"><i class="fa fa-circle text-success"></i> Online</a>
        </div>
      </div>
      <!-- search form -->
      <form action="#" method="get" class="sidebar-form">
        <div class="input-group">
          <input type="text" name="q" class="form-control" placeholder="Search...">
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
        <li class="header">MAIN NAVIGATION</li>
        <li class="active treeview menu-open">
          <a href="#">
            <i class="fa fa-dashboard"></i> <span>Dashboard</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li ><a href="dashboard.php"><i class="fa fa-circle-o"></i> Dashboard</a></li>
          </ul>
        </li>
        <?php //if($_SESSION['rolePsv']=='admin'){ ?>
        <li class="active">
          <a href="import.php">
            <i class="fa fa-cloud-download active"></i> <span>Import Data</span>
          </a>
        </li>
        <li>
          <a href="cleaning.php">
            <i class="fa fa-check-square-o"></i> <span>Cleaning Data</span>
          </a>
        </li>
        <li>
          <a href="clean.php">
            <i class="fa fa-check-square-o"></i> <span>Données CLean</span>
          </a>
        </li>
        <li>
          <a href="journal.php">
            <i class="fa fa-list"></i> <span>Journal des Anomalies</span>
          </a>
        </li>
        <li>
          <a href="cron01.php">
            <i class="fa fa-history"></i> <span>Cron Jobs</span>
          </a>
        </li>
        <?php //} ?>
        <li class="header">AUTRES</li>
        <li><a href="utilisateur.php"><i class="fa fa-circle-o text-red"></i> <span>Gestion d'Utilisateur</span></a></li>
        
      </ul>
    </section>
    <!-- /.sidebar -->
  </aside>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        Importation des Données
      </h1>
      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">Dashboard</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">
      <!-- Info boxes -->
      
      <!-- /.row -->

      <div class="row">
       
        <div class="col-md-12">
          <div class="box">
            <div class="box-header with-border">
              <h3 class="box-title">Importation API KOBO.</h3>

              <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                </button>
                <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
              </div>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
             
              <div class="row">
                
                <div class="col-md-7">
                    <button type="button" class="btn btn-warning" dir="" id="api_actualise" >
                        <i class="fa fa-sync"></i>
                        Actualise la Synchronisation
                    </button>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    <button type="button" class="btn btn-success" dir="" id="api_downAll" >
                        <i class="fa fa-cloud-download"></i>
                        Télécharger Tous les données 
                    </button><br>
                </div>
                <div class="col-md-5">
                    <div id="loadingImport02" style="display:none">
                        <img src="./dist/img/ajax-loader.gif" align="left"> <b>&nbsp;&nbsp; <span class="ldText"></span>...</b>
                    </div>
                </div>
                
                
                <div class="col-md-6">
                    <h2>Listes des Répérages par Lot</h2>   
                    <div class="box-body table-responsive no-padding">
                      <table class="table table-bordered table-striped table-hover">
                        <tr>
                          <th></th>
                          <th>Lot</th>
                          <th>Date</th>
                          <th>Détails</th>
                          <th></th>
                        </tr>
                        <tbody id="lotApi_reperage">
                            
                        </tbody>
                      </table>
                       <br>
                        <div class="reper_BtnAll" style="display:none">
                            <div class="col-md-8"  >
                                <button type="submit" class="btn btn-warning btn_affiche0" dir="Reperage" >
                                    <i class="fa fa-eye"></i>
                                    Tous Afficher
                                </button>
                                <button type="submit" class="btn btn-success btn_telecharge0" dir="Reperage" >
                                    <i class="fa fa-cloud-download"></i>
                                    Télécharger Tous
                                </button>
                            </div>
                            <div class="col-md-4 ldReper_BtnAll" style="display:none">
                                <img src="./dist/img/ajax-loader.gif" align="left"> <b>&nbsp;&nbsp; <span class="ldText2"></span>...</b>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <h2>Listes des Réalisations par Lot</h2>
                    <div class="box-body table-responsive no-padding">
                      <table class="table table-bordered table-striped table-hover">
                        <tr>
                          <th></th>
                          <th>Lot</th>
                          <th>Date</th>
                          <th>Détails</th>
                          <th ></th>
                        </tr>
                        <tbody id="lotApi_realisation"></tbody>
                      </table>
                       <br>
                        <div class="real_BtnAll" style="display:none">
                            <div class="col-md-8"  >
                                <button type="submit" class="btn btn-warning btn_affiche0" dir="Realisation" >
                                    <i class="fa fa-eye"></i>
                                    Tous Afficher
                                </button>
                                <button type="submit" class="btn btn-success btn_telecharge0" dir="Realisation" >
                                    <i class="fa fa-cloud-download"></i>
                                    Télécharger Tous
                                </button>
                            </div>
                            <div class="col-md-4 ldReal_BtnAll" style="display:none">
                                <img src="./dist/img/ajax-loader.gif" align="left"> <b>&nbsp;&nbsp; <span class="ldText2"></span>...</b>
                            </div>
                        </div>
                    </div>
                </div>
                
                <br><br>
                
                <div class="col-md-12 tableau_affichage" style="display:none">
                    <h2>Affichage</h2>
                    <div class="box-body table-responsive no-padding">
                      <table class="table table-bordered table-striped table-hover">
                        <tr>
                          <th></th>
                          <th width="5%">Lot</th>
                          <th>Client</th>
                          <th>Adresses</th>
                          <th>Géolocalisation</th>
                          <th>Catégorie</th>
                          <th>Pt Vente</th>
                          <th>Commentaires</th>
                          <th>Date</th>
                        </tr>
                        <tbody id="lotApi_affichage"></tbody>
                      </table>
                    </div>
                </div><br><br><br>
                
              </div>
              
            </div>
          </div>
        </div>
       
        <div class="col-md-12">
          <div class="box">
            <div class="box-header with-border">
              <h3 class="box-title">Importation CSV / EXCEL.</h3>

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
                    <div class="box-body no-padding">
                        <form id="formImport01" action="dist/ajax_php.php" method="post" enctype="multipart/form-data" >
                        <div class="row">
                            <div class="col-md-1"></div>
                            <div class="col-md-4">
                                <input type="file" value="Valider" class="btn btn-primary" name="csv" /><br>
                            </div>
                            <div class="col-md-4">
                                <input type="hidden" value="save_ImportCSV" name="pst">
                                <button type="submit" class="btn btn-success" dir="" id="" >IMPORTER LE FICHIER</button>
                            </div>
                        </div>
                     
                        <div class="row">
                            <div class="col-md-1"></div>
                            <div class="col-md-8" id="">
                                <div id="loadingImport01" style="display:none">
                                    <img src="./dist/img/ajax-loader.gif" align="left"> <b>&nbsp;&nbsp; Importation en cours...</b>
                                </div>
                                <div id="messageImport01"></div>
                            </div>
                        </div>
                        
                        </form>
                     <br>
<!--
                      <table class="table table-hover">
                       
                        <tr>
                          <th>Exécution de l'Importation : </th>
                        </tr>
                        <tr>
                          <th></th>
                        </tr>
                        
                      </table>
-->
                      <br>
                      <br>
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

  <footer class="main-footer">
    <div class="pull-right hidden-xs">
      <b>Version</b> 1.0.0
    </div>
    <strong>Copyright &copy; 2016-2019 <a href="http://www.demande-audience.com">Croissance Hub</a>.</strong> All rights
    reserved.
  </footer>

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
<!-- AdminLTE dashboard demo (This is only for demo purposes) -->
<script src="dist/js/pages/dashboard2.js"></script>
<!-- AdminLTE for demo purposes -->
<script src="dist/js/demo.js"></script>
<script src="dist/script.js"></script>
</body>
</html>

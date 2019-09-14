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
  <title>PPEMU | Clean</title>
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
<!--  <link rel="stylesheet" href="DataTables/DataTables-1.10.18/css/jquery.dataTables.css">-->
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
       <!-- <li class="header">MAIN NAVIGATION</li>-->
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
        
        <li class="active">
          <a href="clean.php">
            <i class="fa fa-check-square-o"></i> <span>Résumé du Cleaning</span>
          </a>
        </li>
        <li >
          <a href="journal.php">
            <i class="fa fa-list"></i> <span>Journal d'anomalies</span>
          </a>
        </li>
        <li class="header">AUTRES</li>
        <li><a href="utilisateur.php"><i class="fa fa-circle-o text-red"></i> <span>Gestion d'utilisateur</span></a></li>
        
      </ul>
    </section>
    <!-- /.sidebar -->
  </aside>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        Journal sommaire de traitement
      </h1>
      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Acceuil</a></li>
        <li class="active">Journal Sommaire</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">
      <!-- Info boxes -->
      
      <!-- /.row -->
        <?php
            $rapport = new RapportOperation();
        ?>
      
      <div class="row">
        <div class="col-md-12">
          <div class="box box-info">
            <div class="box-header with-border">
            <!--  <h3 class="box-title">Filtrer les données</h3>-->
            </div>
            <!-- /.box-header -->
            <!-- form start -->
            <form id="formPhotographe" method="post" role="form">
                
              <div class="box-body">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="typeDonnee">Type des données</label>
                            <select id="typeDonnee" class="form-control selectTraitement">
                                <option value="">Séléctionnez</option>
                                <option value="Reperage">Parcelles géo-référencées</option>
                                <option value="Realisation">Branchements réalisés</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="lot">Lot</label>
                            <select id="lot" class="form-control selectTraitement">
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
                    <div class="col-md-2">
                        <div class="form-group">
                          <label for="albumDate">Traitement du  </label>
                           <input type="date" id="date_1" class="form-control selectTraitement" placeholder="jj/mm/yyyy" > 
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                          <label for="albumDate">au </label>
                          <input type="date" id="date_2" class="form-control selectTraitement" placeholder="jj/mm/yyyy">
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
              <h3 class="box-title">Synthèse de traitement</h3>

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
                      <table class="table table table-bordered table-striped table-hover" id="example2">
                       <thead>
                        <tr>
                            <th>#</th>
                            <th width="5%">Lot</th>
                            <th>Donnée Brutes</th>
                            <th>Données traitées</th>
                            <th>Cleaned</th>
                            <th>Matching</th>
                            <th>Anomalies trouvées</th>
                            <th>Date</th>
                        </tr>
                       </thead>
                        <tbody id="listTraitementClean">
                        <?php
                            $resData=$rapport->getJournaleByWhere(" operation='Cleaning Data' "); 
    
                            if ($resData) {
                                $nb=0;
                                foreach ($resData as $cus) {
                                $nb++;
                        ?>
                        <tr>
                            <td><?php echo $nb; ?></td>
                            <td><?php echo "Lot ".$cus->lot; ?></td>
                            <td>
                                <?php 
                                    echo "Avant : ".$cus->total_reperImport_before." </br>"; 
                                    echo "Aprés : ".$cus->total_reperImport_after; 
                                ?>
                            </td>
                            <td>
                                <?php 
                                    echo "Avant : ".$cus->total_reper_before." </br>"; 
                                    echo "Aprés : ".$cus->total_reper_after; 
                                ?>
                            </td>
                            <td>
                                <?php 
                                    echo "Trouvé : ".$cus->total_cleaned_found." </br>"; 
                                    echo "Traité : ".$cus->total_cleaned_afected; 
                                ?>
                            </td>
                            <td>
                                <?php 
                                    echo "Trouvé : ".$cus->total_match_found." </br>"; 
                                    echo "Secteur Affecté : ".$cus->total_match_afected; 
                                ?>
                            </td>
                            <td>
                                <?php 
                                    echo "No Obs : ".$cus->total_noObs." </br>"; 
                                    echo "Doublon : ".$cus->total_doublon." </br>"; 
                                    echo "No Obs et Doublon : ".$cus->total_noObs_doublon; 
                                ?>
                            </td>
                            <td>
                                <?php 
                                    echo $cus->dateOperation;
                                ?>
                            </td>
                        </tr>
                        <?php 
                                }
                            }
                            else echo "<tr><td colspan='5'><h3 style='color:#d44d06'>Aucune  données trouvées dans le Journal</h3></td></tr>"
                        ?>
                        </tbody>
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

  <footer class="main-footer">
    <div class="pull-right hidden-xs">
      <b>Version</b> 1.0.0
    </div>
    <strong>Copyright &copy; 2016-2019 <a href="http://www.croissancehub.com">Croissance Hub</a>.</strong> All rights
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
<!-- DataTables -->
<script src="bower_components/datatables.net/js/jquery.dataTables.min.js"></script>
<script src="bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js"></script>

<!--
<script src="./DataTables/DataTables-1.10.18/js/jquery.dataTables.js"></script>
<script src="./DataTables/DataTables-1.10.18/js/dataTables.bootstrap.js"></script>
-->

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
<script>
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

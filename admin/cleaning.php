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
  <title>PPEMU | Cleaning DATA</title>
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

    <style>
        .okTD{
            font-size: 28px;
            color:#27a203;
            display: none
        }
        .failTD{
            font-size: 28px;
            color:red;
            display: none
        }
    </style>

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
       <!--  <li class="header">MAIN NAVIGATION</li>-->
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
        <li class="active">
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
        Cleaning DATA
      </h1>
      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Accueil</a></li>
        <li class="active">Tableau de bord</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">
      <!-- Info boxes -->

      <!-- /.row -->

      <div class="row">

        <div class="col-md-6">
          <div class="box">
            <div class="box-header with-border">
              <h3 class="box-title">Parcelles géo-référencées</h3>

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
                      <table class="table table table-bordered table-striped table-hover">
                        <tr>
                          <th>#</th>
                          <th>Lot</th>
                          <th>Date d'export</th>
                          <th>Description</th>
                          <th width="80"></th>
                        </tr>
                        <?php
                            $reperage = new Reperage();

                            $resData=$reperage->getNoCleanByLot();

                            if ($resData) {

                                foreach ($resData as $cus) {
                        ?>
                        <tr>
                            <td>
                                <img src="./dist/img/ajax-loader.gif" align="center" class="loading" style="display:none">
                                <i class="okTD fa fa-check" ></i>
                                <i class="failTD fa fa-remove" ></i>
                            </td>
                            <td><?php echo 'Lot '.$cus->lot; ?></td>
                            <td><?php echo date('d/m/Y', strtotime($cus->date_export)); ?></td>
                            <td class="lot_detail">
                                <?php
                                    echo 'Nombre de Ligne : '.$cus->ligne;
                                ?>
                            </td>
                            <td>
                                <a class="btn btn-warning cleanDataReper grize" dir="<?php echo $cus->lot; ?>" >Clean</a>
                            </td>
                        </tr>
                        <?php
                                }
                            }
                            else echo "<tr><td colspan='5'><h3 style='color:#d44d06'>Aucune donnée à Nétoyer</h3></td></tr>"
                        ?>
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

        <div class="col-md-6">
          <div class="box">
            <div class="box-header with-border">
              <h3 class="box-title">Rapport d'exécution</h3>

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
                    <div class="box-body table-responsive no-padding" id="rapportCleaningReper">

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

      <div class="row">

        <div class="col-md-6">
          <div class="box">
            <div class="box-header with-border">
              <h3 class="box-title">Branchements réalisés</h3>

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
                      <table class="table table table-bordered table-striped table-hover">
                        <tr>
                          <th>#</th>
                          <th>Lot</th>
                          <th>Date d'export</th>
                          <th>Description</th>
                          <th width="80"></th>
                        </tr>
                        <?php
//                            $cleanQ=$db->query("SELECT DISTINCT lot, date_export, (select count(*) from t_reperage_import t2 where t2.lot=t1.lot) as ligne  FROM t_reperage_import t1 where 1=3");
//
//                            $nb=0;
//                            if ($cleanQ->rowCount() > 0) {
//                            while($rClean=$cleanQ->fetch(PDO::FETCH_ASSOC)){
//                                $nb++;
                        ?>

                        <?php
                            echo "<tr><td colspan='5'><h3 style='color:#d44d06'>Aucune donnée à Nétoyer</h3></td></tr>"
                        ?>
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

        <div class="col-md-6">
          <div class="box">
            <div class="box-header with-border">
              <h3 class="box-title">Rapport d'exécution</h3>

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
                    <div class="box-body table-responsive no-padding" id="rapportCleaningReal">

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

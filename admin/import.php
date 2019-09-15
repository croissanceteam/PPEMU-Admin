<?php 
    session_start();
    if (!isset($_SESSION['pseudoPsv'])) {
        header("location: index.php") ;
    }
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>PPEMU | Récupération automatique</title>
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
          <a href="#"><i class="fa fa-circle text-success"></i>Connecté</a>
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
        <li class="active">
          <a href="import.php">
            <i class="fa fa-cloud-download active"></i> <span>Récupération automatique</span>
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
        <li><a href="#"><i class="fa fa-dashboard"></i> Accueil</a></li>
        <li class="active">Tableau de bord</li>
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
            <!--   <h3 class="box-title">Importation API KOBO.</h3>-->

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
                    <button type="button" class="btn btn-warning grize" dir="" id="api_actualise" >
                        <i class="fa fa-refresh"></i>
                        Synchroniser
                    </button>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    <button type="button" class="btn btn-success grize" dir="" id="api_downAll" >
                        <i class="fa fa-cloud-download"></i>
                        Importer les données
                    </button><br>
                </div>
                <div class="col-md-5">
                    <div id="loadingImport02" style="display:none">
                        <img src="./dist/img/ajax-loader.gif" align="left"> <b>&nbsp;&nbsp; <span class="ldText"></span>...</b>
                    </div>
                </div>


                <div class="col-md-6">
                    <h2>Synchronisation des parcelles référencées par lot</h2>
                    <div class="box-body table-responsive no-padding">
                      <table class="table table-bordered table-striped table-hover">
                        <tr>
                          <th>#</th>
                          <th>Lot</th>
                          <th>Dernière date</th>
                          <th width="33%">Détail</th>
                          <th></th>
                        </tr>
                        <tbody id="lotApi_reperage">
                            <?php for($i=1;$i<=10; $i++){ ?>
                            <tr class="lign_1<?php echo $i; ?>">
                                <td>
                                    <img src='./dist/img/ajax-loader.gif' class='ldTD' style='display:none'>
                                    <i class="okTD fa fa-check" ></i>
                                    <i class="failTD fa fa-remove" ></i>
                                </td>
                                <td>Lot <?php echo $i; ?></td>
                                <td class="lot_date"></td>
                                <td class="lot_detail"> </td>
                                <td>
                                    <button name='<?php echo $i; ?>' class='btn btn-warning api_actualiseLot grize' dir='Reperage' title='Actualise' >
                                        <i class="fa fa-refresh"></i>
                                    </button>
                                    <button name='<?php echo $i; ?>' class='btn btn-info api_affichLot grize' dir='Reperage' title="Affichage" >
                                        <i class="fa fa-eye"></i>
                                    </button>
                                    <button name='<?php echo $i; ?>' class='btn btn-success api_TelechargeLot grize' dir='Reperage' title="Télécharge" >
                                        <i class="fa fa-cloud-download"></i>
                                    </button>
                                </td>
                            </tr>
                            <?php } ?>
                        </tbody>
                      </table>
                       <br>
                    </div>
                </div>

                <div class="col-md-6">
                    <h2>Synchronisation des branchements réalisés par lot</h2>
                    <div class="box-body table-responsive no-padding">
                      <table class="table table-bordered table-striped table-hover">
                        <tr>
                          <th>#</th>
                          <th>Lot</th>
                          <th>Dernière date</th>
                          <th width="33%">Détail</th>
                          <th ></th>
                        </tr>
                        <tbody id="lotApi_realisation">
                            <?php for($i=1;$i<=10; $i++){ ?>
                            <tr class="lign_2<?php echo $i; ?>">
                                <td>
                                    <img src='./dist/img/ajax-loader.gif' class='ldTD' style='display:none'>
                                    <i class="okTD fa fa-check" ></i>
                                    <i class="failTD fa fa-remove" ></i>
                                </td>
                                <td>Lot <?php echo $i; ?></td>
                                <td class="lot_date"></td>
                                <td class="lot_detail"> </td>
                                <td>
                                    <button name='<?php echo $i; ?>' class='btn btn-warning api_actualiseLot grize' dir='Realisation' value='Affiche' >
                                        <i class="fa fa-refresh"></i>
                                    </button>
                                    <button name='<?php echo $i; ?>' class='btn btn-info api_affichLot grize' dir='Realisation' title="Affichage" >
                                        <i class="fa fa-eye"></i>
                                    </button>
                                    <button name='<?php echo $i; ?>' class='btn btn-success api_TelechargeLot grize' dir='Realisation' >
                                        <i class="fa fa-cloud-download"></i>
                                    </button>
                                </td>
                            </tr>
                            <?php } ?>
                        </tbody>
                      </table>
                       <br>
                    </div>
                </div>

                <br><br>

                <div class="col-md-12 tableau_affichage" style="display:none">
                    <h2>Liste détaillée</h2>
                    <div class="box-body table-responsive no-padding">
                      <table class="table table-bordered table-striped table-hover">
                        <tr>
                          <th>#</th>
                          <th width="5%">Lot</th>
                          <th>Client</th>
                          <th>Adresse</th>
<!--                          <th>Géolocalisation</th>-->
                          <th>Catégorie</th>
                          <th>Etat Pt</th>
                          <th>Agent Contrôleur</th>
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

        <div class="col-md-12" id="Correction">
          <div class="box">
            <div class="box-header with-border">
              <h3 class="box-title">Importer un fichier CSV / EXCEL. &nbsp;&nbsp; :: &nbsp;&nbsp;  <!--Correction des données erronées--></h3>

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
                            <div class="col-md-9">
                                <p style="font-size:16px; font-style:italic">
                                    « Dans cette section importer les données erronées (doublons, branchements réalisés avec clé sans obs, etc) corrigées par l’utilisateur et disponible au format Excel ou CSV pour leur prise en charge dans le portail public. »
                                </p> <br>
                            </div>
                            <div class="col-md-12"></div>

                            <div class="col-md-1"></div>

                            <div class="col-md-3">
                                <div class="form-group">
                                  <!--  <label for="typeDonnee">Type de données</label>-->
                                    <select id="typeDonnee" name="typeDonnee" class="form-control" >
                                        <option value="">Séléctionnez</option>
                                        <option value="Reperage">Parcelles géo-référencées</option>
                                        <option value="Realisation">Branchements réalisés</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-4"><br>
                                <input type="file" value="Valider" class="btn btn-primary grize_1" name="csv" disabled /><br>
                            </div>
                            <div class="col-md-3"><br>
                                <input type="hidden" value="save_ImportCSV" name="pst">
                                <button type="submit" class="btn btn-success grize_1" disabled >importer</button>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-1"></div>
                            <div class="col-md-8" id="">
                                <div id="loadingImport01" style="display:none">
                                    <img src="./dist/img/ajax-loader.gif" align="left"> <b>&nbsp;&nbsp; Importation en cours...</b>
                                </div>
                                <div id="msgImport01"></div>
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
</body>
</html>

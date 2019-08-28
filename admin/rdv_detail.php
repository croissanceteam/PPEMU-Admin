<?php 
    session_start(); 
    include'../sync/db.php';
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>AdminLTE 2 | Top Navigation</title>
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
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
</head>
<!-- ADD THE CLASS layout-top-nav TO REMOVE THE SIDEBAR. -->
<body class="hold-transition skin-blue layout-top-nav">
<div class="wrapper">
<?php if(isset($_SESSION['pseudoPsv']))$page="dashboard.php"; else $page="../web/index.php";  ?>

  <header class="main-header">
    <nav class="navbar navbar-static-top">
      <div class="container">
        <div class="navbar-header">
          <a href="<?php echo $page; ?>" class="navbar-brand"><b>Photos </b>Souvenirs</a>
          <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar-collapse">
            <i class="fa fa-bars"></i>
          </button>
        </div>

        <!-- Collect the nav links, forms, and other content for toggling -->
        <div class="collapse navbar-collapse pull-left" id="navbar-collapse">
          <ul class="nav navbar-nav">
            <li class="active">
                <a href="<?php echo $page; ?>">Retour <span class="sr-only">(current)</span></a>
            </li>
          </ul>
          <form class="navbar-form navbar-left" role="search">
            <div class="form-group">
              <input type="text" class="form-control" id="navbar-search-input" placeholder="Search">
            </div>
          </form>
        </div>
        <!-- /.navbar-collapse -->
        <!-- Navbar Right Menu -->
        <?php if(isset($_SESSION['pseudoPsv'])){ ?>
        <div class="navbar-custom-menu">
          <ul class="nav navbar-nav">
            
            <li class="dropdown user user-menu">
              <!-- Menu Toggle Button -->
              <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                <!-- The user image in the navbar-->
                <img src="dist/img/user2-160x160.jpg" class="user-image" alt="User Image">
                <!-- hidden-xs hides the username on small devices so only the image appears. -->
                <span class="hidden-xs"><?php echo $_SESSION['nomsPsv'] ?></span>
              </a>
              <ul class="dropdown-menu">
                <!-- The user image in the menu -->
                <li class="user-header">
                  <img src="dist/img/user2-160x160.jpg" class="img-circle" alt="User Image">

                    <p>
                        <?php echo $_SESSION['nomsPsv']." - ".$_SESSION['rolePsv'] ?>.
                        <small>Membre depuis </small>
                    </p>
                </li>
                <!-- Menu Body -->
                <li class="user-body">
                  <div class="row">
                    <div class="col-xs-4 text-center">
                      <a href="#">Followers</a>
                    </div>
                    <div class="col-xs-4 text-center">
                      <a href="#">Sales</a>
                    </div>
                    <div class="col-xs-4 text-center">
                      <a href="#">Friends</a>
                    </div>
                  </div>
                  <!-- /.row -->
                </li>
                <!-- Menu Footer-->
                <li class="user-footer">
                  <div class="pull-left">
                    <a href="#" class="btn btn-default btn-flat">Profile</a>
                  </div>
                  <div class="pull-right">
                    <a href="deconnection.php" class="btn btn-default btn-flat">Se Déconnecter</a>
                  </div>
                </li>
              </ul>
            </li>
          </ul>
        </div>
        <?php } ?>
        <!-- /.navbar-custom-menu -->
      </div>
      <!-- /.container-fluid -->
    </nav>
  </header>
  <!-- Full Width Column -->
  
    <?php 
        $rdv=htmlentities($_GET['rdv'], ENT_QUOTES);
        if(isset($_SESSION['pseudoPsv']))
            $rdvQ=$db->query("SELECT *, (select count(*) from favoris where rdv=r.token) as nbfav, (select count(*) from favoris where rdv=r.token and user='$_SESSION[pseudoPsv]') as nbfav_1, (select count(*) from commentaire where objet=r.token and type='rdv' ) as nbComent, (select count(*) from abonnement where rdv=r.token and utilisateur='$_SESSION[pseudoPsv]') as nbComm_1, (select count(*) from abonnement where rdv=r.token) as abonn FROM rdv r WHERE token='$rdv' ");
        else
            $rdvQ=$db->query("SELECT *, (select count(*) from favoris where rdv=r.token) as nbfav, (select count(*) from commentaire where objet=r.token and type='rdv' ) as nbComent, (select count(*) from abonnement where rdv=r.token) as abonn FROM rdv r WHERE token='$rdv' ");
        $row=$rdvQ->fetch(PDO::FETCH_ASSOC);
    ?>
  
  <div class="content-wrapper">
    <div class="container">
      <!-- Content Header (Page header) -->
      <section class="content-header">
        <h1>
          <?php echo $row['titre'] ?>
          <small> avec <?php echo $row['person'] ?></small>
        </h1>
        <ol class="breadcrumb">
          <li><a href="<?php echo $page; ?>"><i class="fa fa-dashboard"></i> Accueil</a></li>
          <li><a href="rdv.php">Rendez-Vous</a></li>
          <li class="active"><?php echo $row['titre'] ?></li>
        </ol>
      </section>

      <!-- Main content -->
      <section class="content">
        
        <div class="box box-default">
          <div class="box-header with-border">
            <h3 class="box-title">
                <?php echo $row['titre'] ?> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            </h3>
            <?php if(isset($_SESSION['pseudoPsv'])){ ?>
            <div class="pull-right">
                <a href="#" class="btn btn-warning cl_interesse" dir="<?php echo $row['token'] ?>" id="<?php echo $_SESSION['pseudoPsv'] ?>" ><?php if($row['nbfav_1']>0) echo trim("DESINTERESSE(E)"); else echo trim("INTERESSE(E)");?></a> &nbsp;
                <a href="#" class="btn btn-info cl_abonne" dir="<?php echo $row['token'] ?>" id="<?php echo $_SESSION['pseudoPsv'] ?>"><?php if($row['nbComm_1']>0) echo "ME DESABONNER(E)";else echo "S'ABONNE(E)";?></a> &nbsp;
                <a href="#" class="btn btn-success photoComment" title="Commentaire"  dir="<?php echo $row['token']; ?>" id="rdv">Commenter </a>
            </div>
            <?php } else { ?>
            <div class="pull-right">
                <a href="index.php" class="btn btn-warning" dir="" id="" >INTERESSE(E)</a> &nbsp;
                <a href="index.php" class="btn btn-info " dir="" id="">S'ABONNE(E)</a> &nbsp;
                <a href="index.php" class="btn btn-success " title="Commentaire">Commenter </a>
            </div>
            <?php } ?>
          </div>
            
          <div class="box-body">
            <div class="row">
                <div class="col-md-5">
                    <img <?php echo  "src='../uploads/rdv_$row[token]/$row[image]'" ?> class="img-responsive">
                </div>
                <div class="col-md-7">
                    <div class="callout callout-info">
                      <h4>Description</h4>
                      <p><?php echo $row['detail'] ?></p>
                    </div>
                       
                    <a href="#" style="color:#f45323" title="Interessé" > <i class="fa fa-heart"></i> <?php echo $row['nbfav'] ?></a> &nbsp;&nbsp; 
                    <a href="#" style="color:blue" title="Abonnés" > <i class="fa fa-check"></i> <?php echo $row['abonn'] ?></a> &nbsp;&nbsp;
                    <a href="#" style="color:green" class="photoComment" title="Commentaire" dir="<?php echo $row['token']; ?>" id="rdv" > <i class="fa fa-comments"></i> <?php echo $row['nbComent'] ?></a> &nbsp;&nbsp; 
                    <br><br>
                    
                    <table class="table table-striped table-hover">
                        <tr>
                            <th>Invité(e) </th><th><?php echo $row['person'] ?></th>
                        </tr>
                        <tr>
                            <th>Date </th><th><?php echo date('d-m-Y', ($row['dateRdv']/1000)) ?></th>
                        </tr>
                        <tr>
                            <th>Heure </th><th><?php echo $row['heureRdv'] ?></th>
                        </tr>
                        <tr>
                            <th>Lieu </th><th><?php echo $row['lieuRdv'] ?></th>
                        </tr>
<!--
                        <tr>
                            <th>Paf </th><th><?php //echo "" ?></th>
                        </tr>
-->
                        <tr>
                            <th>Condition </th><th><?php echo $row['conditionRdv'] ?></th>
                        </tr>
                    </table>
                </div>
            </div>
          </div>
          <!-- /.box-body -->
        </div>
        
        <!-- /.box -->
      </section>
      <!-- /.content -->
    </div>
    <!-- /.container -->
  </div>
  <!-- /.content-wrapper -->
  <footer class="main-footer">
    <div class="container">
      <div class="pull-right hidden-xs">
        <b>Version</b> 1.0.0
      </div>
      <strong>Copyright &copy; 2014-2019 <a href="https://www.wendylab.com">WendyLab</a>.</strong> All rights
      reserved.
    </div>
    <!-- /.container -->
  </footer>
</div>
<!-- ./wrapper -->




<div class="modal fade" id="modalComment">
    <div class="modal-dialog">
        <div class="modal-content modal-sm">
                <div class="modal-body marg">
                    <div class="col-md-12">
          <!-- DIRECT CHAT SUCCESS -->
          <div class="box box-success direct-chat direct-chat-success">
            <div class="box-header with-border">
              <h3 class="box-title">Commentaire(s)</h3>

              <div class="box-tools pull-right">
                <span data-toggle="tooltip" title="Commentaires" class="badge bg-green nbComment">3</span>
                <button type="button" class="btn btn-box-tool" data-toggle="tooltip" title="Contacts" data-widget="chat-pane-toggle">
                  <i class="fa fa-comments"></i></button>
<!--                <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>-->
              </div>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
              <!-- Conversations are loaded here -->
              <div class="direct-chat-messages" id="direct-chat-messages"></div>
              <!--/.direct-chat-messages-->

              <!-- Contacts are loaded here -->
              <div class="direct-chat-contacts">
                <ul class="contacts-list">
                  <li>
                    <a href="#">
                      <img class="contacts-list-img" src="../dist/img/user1-128x128.jpg" alt="User Image">

                      <div class="contacts-list-info">
                            <span class="contacts-list-name">
                              Count Dracula
                              <small class="contacts-list-date pull-right">2/28/2015</small>
                            </span>
                        <span class="contacts-list-msg">How have you been? I was...</span>
                      </div>
                      <!-- /.contacts-list-info -->
                    </a>
                  </li>
                  <!-- End Contact Item -->
                </ul>
                <!-- /.contatcts-list -->
              </div>
              <!-- /.direct-chat-pane -->
            </div>
            <!-- /.box-body -->
            <div class="box-footer">
                <?php if(isset($_SESSION['pseudoPsv'])){ ?>
              <form action="dist/ajax_php.php" id="formAddComment" method="post">
                <div class="input-group">
                  <input type="text" name="comment" placeholder="Tapez votre commentaire ..." class="form-control">
                      <span class="input-group-btn">
                        <input type="hidden" value="" name="objet" id="objet"   >
                        <input type="hidden" value="rdv" name="type" id="type"   >
                        <input type="hidden" value="<?php echo $_SESSION['pseudoPsv'] ?>" name="user" >
                        <input type="hidden" value="save_CommentObjet" name="pst"  >
                        <button type="submit" class="btn btn-warning btn-flat">Envoyer</button>
                      </span>
                </div>
              </form>
              <?php } ?>
            </div>
            <!-- /.box-footer-->
          </div>
          <!--/.direct-chat -->
        </div>
        <!-- /.col -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-dismiss="modal">Fermer</button>
                </div>
        </div>
    <!-- /.modal-content -->
    </div>
<!-- /.modal-dialog -->
</div>




<!-- jQuery 3 -->
<script src="bower_components/jquery/dist/jquery.min.js"></script>
<!-- Bootstrap 3.3.7 -->
<script src="bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
<!-- SlimScroll -->
<script src="bower_components/jquery-slimscroll/jquery.slimscroll.min.js"></script>
<!-- FastClick -->
<script src="bower_components/fastclick/lib/fastclick.js"></script>
<!-- AdminLTE App -->
<script src="dist/js/adminlte.min.js"></script>
<!-- AdminLTE for demo purposes -->
<script src="dist/js/demo.js"></script>
<script src="dist/script.js"></script>
</body>
</html>

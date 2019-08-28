<?php 
    session_start(); 
    include('../../sync/db.php');
?>
 
    <!--TODO: Container Information details Etudiant-->
<?php if(isset($_POST['ajxTp']) && $_POST['ajxTp']=='lst_comment'){ ?>

<div id="liste_comment">
    <?php
        $objet=htmlentities($_POST['obj'], ENT_QUOTES);
        $type=htmlentities($_POST['type'], ENT_QUOTES);

        $commentQ=$db->query("SELECT contenu, pseudo, avatar, noms, dateComment FROM commentaire c LEFT JOIN (utilisateurs u) ON u.pseudo=c.utilisateur WHERE type='$type' and objet='$objet' ORDER BY c.lastModif");
        $nbr=0; $c=""; 
        if(@$commentQ->rowCount() > 0){
        while(@$rC=$commentQ->fetch(PDO::FETCH_ASSOC)){
            $nbr++;
            $cc="right";
            $cc_n="pull-right";
            $cc_d="pull-left";
            if($c==$rC['pseudo'] || $nbr==1){
                $cc="";
                $cc_n="pull-left";
                $cc_d="pull-right";
            }
            
    ?>
    <div class="direct-chat-msg <?php echo $cc ?>">
        <div class="direct-chat-info clearfix">
            <?php if($c!=$rC['pseudo'] || $nbr==1){ ?>
                <span class="direct-chat-name <?php echo $cc_n ?>"><?php echo $rC['noms'] ?></span>
            <?php } ?>
            <span class="direct-chat-timestamp <?php echo $cc_d ?>"><?php echo date('d-m-Y h:m',($rC['dateComment']/1000)) ?></span>
        </div>
        <?php if($c!=$rC['pseudo'] || $nbr==1){ ?>
            <img class="direct-chat-img" src="dist/img/user1-128x128.jpg" alt="Message User Image">
        <?php } ?>
        <div class="direct-chat-text"><?php echo $rC['contenu'] ?></div>
    </div>
    <?php 
        $c=$rC['pseudo'];
        }
        }else {
            echo "<div class='text-center' colspan='5'>Aucun Commentaire</div>";
        } ?>
</div>

<?php } ?> 
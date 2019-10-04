    
$(document).ready(function (e){
    var jsonKobo= new Array(20);
    var incr=0;
    var incrAll= new Array(20);
    
    $(document).on('click', '.grize', function(e){
        $('.grize').attr('disabled', 'on');
        $(this).removeAttr('disabled');
    });
    
    $(document).on('click', '.grize', function(e){
        $('.grize').attr('disabled', 'on');
        $(this).removeAttr('disabled');
    });
    
    

    //TODO:: SELECT TYPE DONNES IMPORTATION CSV XLS
    $(document).on('change', '#typeDonnee', function(e){
        if($(this).val()!="")
            $('.grize_1').removeAttr('disabled');
        else
            $('.grize_1').attr('disabled', 'on');
        
    });
    //TODO:: Envoi du FOrmulaire + Reponse IMPORTATION CSV XLS
    $("#formImport01").on('submit',(function(e){
        e.preventDefault();
        
        var typeD = $("#typeDonnee").val();
        
        if(typeD!=""){
            $("#loadingImport01").show();
            $.ajax({
                url: "dist/ajax_php.php",
                type: "POST",
                data:  new FormData(this),
                contentType: false,
                cache: false,
                processData:false,
                success: function(data){
                    $("#loadingImport01").hide();
                    $("#msgImport01").html(data);
                    $("#formImport01")[0].reset();
                },
                error: function(){} 	        
            });
        }
        
    }));
    
    
    //TODO:: Envoi + Reponse ACTUALISATION API QTE LIGNE PAR LOT
    $(document).on('click', '.api_actualiseLot', function(e){
        
        var lot =$(this).attr('name');
        var typeD =$(this).attr('dir');
        var ligne=$(this).parent('td').parent('tr');
        
        $("#loadingImport02").hide();
        $(".reper_BtnAll").hide();
        $(".real_BtnAll").hide();
        
        ligne.find('.btn_display').hide();
        ligne.find('.okTD').hide();
        ligne.find('.failTD').hide();
        ligne.find('.ldTD').show();
        
        $('.tableau_affichage #lotApi_affichage').empty();
        $('.tableau_affichage').hide();
        
        $.ajax({
            type:'get',
            url:'dist/traitement_api.php',
            data:'traitement_api' + '&btn=' + 'api_actualiseLot' + '&lot=' + lot + '&typeDonnee=' + typeD,
            dataType:'json',
            success: function(json){
                if(json[1]=="Error"){
                    ligne.find('.btn_display').hide();
                    ligne.find('.ldTD').hide();
                    ligne.find('.okTD').hide();
                    ligne.find('.failTD').show();
                    ligne.find('.lot_detail').text(json[3]);
                }
                else {
                    var nbrEnreg=0;
                    $.each(json, function(index, value){
                        if(value[3]=='Reperage'){
                            ligne.find('.lot_date').text(value[2]);
                            ligne.find('.lot_detail').text("Enregistrement(s) : "+value[1]);

                            $(".reper_BtnAll").show();
                        }
                        else if(value[3]=='Realisation'){
                            ligne.find('.lot_date').text(value[2]);
                            ligne.find('.lot_detail').text("Enregistrement(s) : "+value[1]);

                            $(".real_BtnAll").show();
                        }
                        
                        jsonKobo[value[0]-1]=value[4];
                        
                        //if(value[1]==0)ligne.find('.api_TelechargeLot').remove();
                        nbrEnreg=value[1];
                        
                    });
                    ligne.find('.ldTD').hide();    
                    ligne.find('.okTD').show();  
                    
                    if(nbrEnreg>0) ligne.find('.btn_display').show();    
                }
                $('.grize').removeAttr('disabled');
        }});
    });
    
    
    //TODO:: Envoi + Reponse ACTUALISATION API QTE LIGNE POUR TOUT REPERAGE ET REALISATION
    $(document).on('click', '#api_actualise', function(e){
        incr=0;
        var lot=0;
        var typeD='Reperage';
        var lign_class='.lign_1';
        
        $("#loadingImport02").hide();
        $(".btn_display").hide();
        $('.tableau_affichage #lotApi_affichage').empty();
        $('.tableau_affichage').hide();
        
        for (var i = 1; i < 21; i++) {
            lot++;
            //if(lot==3) {
            if(lot==11) {
                lot=1;
                typeD='Realisation';
                lign_class='.lign_2';
            }
            
            var ligne=$(lign_class+lot);

            ligne.find('.okTD').hide();
            ligne.find('.failTD').hide();
            ligne.find('.ldTD').show();

            $.ajax({
            type:'get',
            url:'dist/traitement_api.php',
            data:'traitement_api' + '&btn=' + 'api_actualiseLot' + '&lot=' + lot + '&typeDonnee=' + typeD,
            dataType:'json',
            success: function(json){
                //$("#api_downAll").show();
            }})
            
            .done(function(data) {
                incr++;
                
                var ligne1;
                if(data[1]=="Error"){
                    if(data[2]=='Reperage') ligne1=$('.lign_1'+data[0]);
                    else if(data[2]=='Realisation') ligne1=$('.lign_2'+data[0]);
                    
                    ligne1.find('.btn_display').hide();
                    ligne1.find('.ldTD').hide();
                    ligne1.find('.failTD').show();
                    ligne1.find('.lot_detail').text(data[3]);
                }
                else {
                    var nbrEnreg=0;
                    $.each(data, function(index, value){
                        if(value[3]=='Reperage'){
                            ligne1=$('.lign_1'+value[0]);
                            ligne1.find('.lot_date').text(value[2]);
                            ligne1.find('.lot_detail').text("Enregistrement(s) : "+value[1]);

                        }
                        else if(value[3]=='Realisation'){
                            ligne1=$('.lign_2'+value[0]);
                            ligne1.find('.lot_date').text(value[2]);
                            ligne1.find('.lot_detail').text("Enregistrement(s) : "+value[1]);

                        }
                        
                        jsonKobo[value[0]-1]=value[4];
                        
                        nbrEnreg=value[1];
                        
                    });
                    
                    ligne1.find('.ldTD').hide();
                    ligne1.find('.failTD').hide();
                    ligne1.find('.okTD').show();
                    if(nbrEnreg>0) ligne1.find('.btn_display').show(); //ligne1.find('.btn_display').show();
                    //ligne1.find('.grize').removeAttr('disabled');
                }
                if(incr==20){
                    $('.grize').removeAttr('disabled');
                    $('#api_downAll').removeAttr('disabled');
                } 
            })
            .fail(function(data) {
                incr++;
                var ligne1;
                if(incr<11) ligne1=$('.lign_1'+incr);
                else ligne1=$('.lign_2'+(incr-10));
                
                if(data[2]=='Reperage') ligne1=$('.lign_1'+data[0]);
                else if(data[2]=='Realisation') ligne1=$('.lign_2'+data[0]);
                if(ligne1!==undefined){
                    ligne1.find('.btn_display').hide();
                    ligne1.find('.okTD').hide();
                    ligne1.find('.ldTD').hide();
                    ligne1.find('.failTD').show();
                    ligne1.find('.lot_detail').html("<span style='color:red'>Echec Synchronisation !</span>");
                    //ligne1.find('.grize').removeAttr('disabled');
                    
                }else if(incr==20) {
                    $( ".ldTD" ).each(function() {
                        if ($(this).is(':visible') === true){
                            var ligne0=$(this).parent('td').parent('tr');;
                            $(this).hide();
                            ligne0.find('.failTD').show();
                            ligne0.find('.okTD').hide();
                            ligne0.find('.lot_detail').html("<span style='color:red'>Echec Synchronisation !</span>");
                            ligne0.find('.grize').removeAttr('disabled');
                            $('.grize').removeAttr('disabled');
                            //$('#api_downAll').removeAttr('disabled');
                        }
                    });
                    
                }
                
               // $('.grize').removeAttr('disabled');
//                if(incr==20){
//                }
            });
            
            //if(i==2) break;
        }
        
    });
    
    //alert(";kl");
    
    //TODO:: Envoi + Reponse IMPORTATION TELECHARGEMENT DIRECT API QTE LIGNE PAR LOT (DE TOUS)
    $(document).on('click', '#api_downAll', function(e){
        var lot=0;
        var typeD='Reperage';
        var lign_class='.lign_1';
        
        $("#loadingImport02").hide();
        $('.tableau_affichage #lotApi_affichage').empty();
        $('.tableau_affichage').hide();
        
        for (var i = 1; i < 21; i++) {
            lot++;
            incrAll[lot-1]=0;
            //if(lot==3) {
            if(lot==11) {
                lot=1;
                typeD='Realisation';
                lign_class='.lign_2';
            }
            
            var ligne=$(lign_class+lot);

            ligne.find('.okTD').hide();
            ligne.find('.failTD').hide();
            ligne.find('.ldTD').show();
            
            if(jsonKobo[lot-1].length>0){

            $.each(jsonKobo[lot-1], function(index, value){

                var finTour=0;
                if(jsonKobo[lot-1].length==incrAll[lot-1]) finTour=jsonKobo[lot-1].length;

                $.ajax({
                    type:'get',
                    url:'dist/traitement_api.php',
                    data:'traitement_api' + '&btn=' + 'api_TelechargeLot' + '&lot=' + lot + '&typeDonnee=' + typeD+ '&row='+JSON.stringify(value)+'&finTour='+finTour,
                    dataType:'json',
                    success: function(json){
                    }})
                    .done(function(data) {
                        var ligne1;
                        if(data[1]=="Error"){
                            ligne.find('.ldTD').hide();
                            ligne.find('.okTD').hide();
                            ligne.find('.failTD').show();
                            ligne.find('.btn_display').hide();
                            ligne.find('.lot_detail').text(data[3]);
                            $('.grize').removeAttr('disabled');
                        }
                        else {
                            
                            $.each(data, function(index, value){
                                incrAll[value[0]-1]++;
                                //alert(incrAll[value[0]-1]);
                                if(value[2]=='Reperage'){
                                    ligne1=$('.lign_1'+value[0]);
                                    //ligne1.find('.lot_date').text(value[2]);
                                    ligne1.find('.lot_detail').text("Récuperation : "+incrAll[value[0]-1]+ "/"+ jsonKobo[value[0]-1].length);
                                    
                                    if(incrAll[value[0]-1]==jsonKobo[value[0]-1].length){
                                        ligne1.find('.ldTD').hide();
                                        ligne1.find('.api_TelechargeLot').hide();
                                        ligne1.find('.okTD').show();
                                    }
                                }
                                else if(value[2]=='Realisation'){
                                    ligne1=$('.lign_2'+value[0]);
                                    //ligne1.find('.lot_date').text(value[2]);
                                    ligne1.find('.lot_detail').text("Récuperation : "+incrAll[value[0]-1]+ "/"+ jsonKobo[value[0]-1].length);
                                    
                                    if(incrAll[value[0]-1]==jsonKobo[value[0]-1].length){
                                        ligne1.find('.ldTD').hide();
                                        ligne1.find('.api_TelechargeLot').hide();
                                        ligne1.find('.okTD').show();
                                    }

                                }
                                
                            });
                            $('.grize').removeAttr('disabled');
                        }
                        //$('.grize').removeAttr('disabled');
                    })
                    .fail(function(data) {
                        $('.grize').removeAttr('disabled');
                    });
            });
            
            }
            
            //if(i==2) break;
        }
        
    });
    
    //TODO:: Envoi + Reponse AFFICHAGE DONNE API PAR LOT
    $(document).on('click', '.api_affichLot', function(e){
        var lot =$(this).attr('name');
        var typeD =$(this).attr('dir');
        var ligne=$(this).parent('td').parent('tr');
        
        ligne.find('.okTD').hide();
        ligne.find('.failTD').hide();
        ligne.find('.ldTD').show();
        
        $('.tableau_affichage #lotApi_affichage').empty();
        
        $('.tableau_affichage').show();
        $('.tableau_affichage h2').text("Liste de "+typeD+" par Lot");
        
        var i=0;
        $.each(jsonKobo[lot-1], function(index, value){
            i++;
            if(typeD=='Reperage'){
                if(value.Nom_Client === undefined ) var nomClient=value.NomClient;
                else var nomClient=value.Nom_Client;

                if(value.Avenue_Quartier === undefined ) var avenue=value.AvenueQuartier;
                else var avenue=value.Avenue_Quartier;

                if(value.Ref_Client === undefined ) var refClient=value.numsite;
                else var refClient=value.Ref_Client;

                if(value.Num_ro_parcelle === undefined ) var numParcel=value.Numparcelle;
                else var numParcel=value.Num_ro_parcelle;

                if(value.Etat_du_point_de_vente === undefined ) var etPVente=value.Etatpvente;
                else var etPVente=value.Etat_du_point_de_vente;

                if(value.Cat_gorie_Client === undefined ) var catClient=value.CatgorieClient;
                else var catClient=value.Cat_gorie_Client;

                if(value.Nom_du_Contr_leur !== undefined ) var controller=value.Nom_du_Contr_leur;
                else if(value.consultant !== undefined ) var controller=value.consultant;
                else var controller="";

                $("#lotApi_affichage").append("<tr><td>"+i+"</td>"
                        +"<td>Lot "+lot+"</td>"
                        +"<td>"+nomClient+"<br/>Réf.:"+refClient+"</td>"
                        +"<td>"+numParcel+", "+avenue+",<br/>"+value.Commune+"</td>"
                        +"<td>"+catClient+"</td>"
                        +"<td>"+etPVente+"</td>"
                        +"<td>"+controller+"</td>"
                        +"<td>"+value._submission_time+"</td>"
                                                );
            }
            else if(typeD=='Realisation'){
                $("#lotApi_affichage").append("<tr><td>"+i+"</td>"
                        +"<td>Lot "+lot+"</td>"
                        +"<td>"+value.Nom_du_Client+"<br/>Réf.:"+value.num_site+"</td>"
                        +"<td>"+value.Num_ro+", "+value.Avenue +", "+value.Quartier +",<br/>"+value.Commune+"</td>"
                        +"<td>"+value.Branchement_Social_ou_Appropri+"</td>"
                        +"<td></td>" 
                        +"<td>"+value.Consultant_qui_a_suivi_l_ex_cution_KIN+"</td>"
                        +"<td>"+value.Date+"</td>"
                                            );
            }
            ligne.find('.lot_detail').text("Affichage : "+i+"/"+jsonKobo[lot-1].length);
        });

        ligne.find('.ldTD').hide();
        ligne.find('.okTD').show();
        $('.grize').removeAttr('disabled');
        
    });
    
    //TODO:: Envoi + Reponse TELECHARGEMENT DONNE API PAR LOT
    $(document).on('click', '.api_TelechargeLot', function(e){
        incr=0;
        var btnTelecharge=$(this);
        var lot =$(this).attr('name');
        var typeD =$(this).attr('dir');
        var ligne=$(this).parent('td').parent('tr');
        
        ligne.find('.okTD').hide();
        ligne.find('.failTD').hide();
        ligne.find('.ldTD').show();
        
        $('.tableau_affichage #lotApi_affichage').empty();
        
        $('.tableau_affichage').hide();
        
        var i=0;
        $.each(jsonKobo[lot-1], function(index, value){
            i++;
            
            var finTour=0;
            //alert(jsonKobo[lot-1].length+' '+jsonKobo.length);
            if(jsonKobo[lot-1].length==incr) finTour=jsonKobo[lot-1].length;
            
            $.ajax({
                type:'get',
                url:'dist/traitement_api.php',
                data:'traitement_api' + '&btn=' + 'api_TelechargeLot' + '&lot=' + lot + '&typeDonnee=' + typeD+ '&row='+JSON.stringify(value)+'&finTour='+finTour,
                dataType:'json',
                success: function(json){
                    if(json[1]=="Error"){
                        ligne.find('.ldTD').hide();
                        ligne.find('.okTD').hide();
                        ligne.find('.failTD').show();
                        ligne.find('.lot_detail').text(json[3]);
                    }
                    else {
                        $.each(json, function(index, value){
                            //alert('Fin Téléchargement \n OPERATION : '+typeD+' \n LOT : '+lot+' \n Nombres de Lignes Téléchargé : '+i+' \n Date Exportation : '+value[1]);
                        });

                        ligne.find('.ldTD').hide();
                        ligne.find('.api_TelechargeLot').hide();
                        ligne.find('.okTD').show();
                    }
                    //$('.grize').removeAttr('disabled');
                }})
                .done(function(data) {
                    var ligne1;
                    if(data[1]=="Error"){
                        ligne.find('.ldTD').hide();
                        ligne.find('.okTD').hide();
                        ligne.find('.failTD').show();
                        ligne.find('.btn_display').hide();
                        ligne.find('.lot_detail').text(data[3]);
                        $('.grize').removeAttr('disabled');
                    }
                    else {
                        incr++;
                        $.each(data, function(index, value){
                            ligne.find('.lot_detail').text("Récuperation : "+incr+"/"+jsonKobo[value[0]-1].length);

                            //jsonKobo[value[0]-1]=value[4];
                        });
                        
                        $('.grize').removeAttr('disabled');
                    }
                })
                .fail(function(data) {
                    ligne.find('.ldTD').hide();
                    ligne.find('.okTD').hide();
                    ligne.find('.failTD').show();
                    ligne.find('.btn_display').hide();
                    ligne.find('.lot_detail').text(data[3]);
                    $('.grize').removeAttr('disabled');
                });
        });
        
    });
    
    
    
    
    
    // Cleaning Data Reperage Process par lot
    $(document).on('click', '.cleanDataReper', function(e){
        e.preventDefault();
        console.log('.cleanDataReper clicked');
        
        var lot = $(this).attr('dir');
        var ligne=$(this).parent('td').parent('tr');
        var totalData = ligne.find('.lot_detail').text().split(':')[1];
        ligne.find('.grize').hide();
        ligne.find('.okTD').hide();
        ligne.find('.loading').show();
        
         $("#rapportCleaningReper").empty();
        
        $.ajax({
            type:'get',
            url:'dist/cleaning_proccess.php',
            data:'cleanDataReper' + '&lot=' + lot + '&total_data=' + totalData,
            dataType:'Text',
            success: function(json){
                
                $("#rapportCleaningReper").append(json);
                
                $.ajax({
                    type:'get',
                    url:'dist/cleaning_proccess.php',
                    data:'cleanDataReper_suite' + '&lot=' + lot,
                    dataType:'Text',
                    success: function(json){
                        $("#rapportCleaningReper").append(json);
                        
                        ligne.find('.lot_detail').text("Nombre de Ligne : 0");
                        
                        ligne.find('.loading').hide();
                        ligne.find('.okTD').show();
                        $('.grize').removeAttr('disabled');

                }});

        }});
        
    });
    
    
    
    // Cleaning Data Realisation Process par lot
    $(document).on('click', '.cleanDataReal', function(e){
        e.preventDefault();
        
        var lot = $(this).attr('dir');
        var ligne=$(this).parent('td').parent('tr');
        var totalData = ligne.find('.lot_detail').text().split(':')[1];
        ligne.find('.grize').hide();
        ligne.find('.okTD').hide();
        ligne.find('.loading').show();
        
         $("#rapportCleaningReal").empty();
        
        $.ajax({
            type:'get',
            url:'dist/cleaning_proccess.php',
            data:'cleanDataReal' + '&lot=' + lot + '&total_data=' + totalData,
            dataType:'Text',
            success: function(json){
                
                $("#rapportCleaningReal").append(json);
                
                $.ajax({
                    type:'get',
                    url:'dist/cleaning_proccess.php',
                    data:'cleanDataReal_suite' + '&lot=' + lot,
                    dataType:'Text',
                    success: function(json){
                        $("#rapportCleaningReal").append(json);
                        
                        ligne.find('.lot_detail').text("Nombre de Ligne : 0");
                        
                        ligne.find('.loading').hide();
                        ligne.find('.okTD').show();
                        $('.grize').removeAttr('disabled');

                }});

        }});
        
    });
    
    
    
    // AFFICHAGE RAPPORT TRAITEMENT CLEANING
    $(document).on('change', '.selectTraitement', function(e){
        e.preventDefault();
        
        var typeD = $("#typeDonnee").val();
        
        //if(typeD!=""){
            var lot = $("#lot").val();
            
            $('#typeDonnee').attr('disabled', 'on');
            $('#lot').attr('disabled', 'on');
            
            $(this).removeAttr('disabled');
        
            $("#listTraitementClean").empty();

            $.ajax({
                type:'get',
                url:'dist/ajax_php.php',
                data:'rapportClean'+'&typeDonnee='+typeD+'&lot='+lot,
                dataType:'json',
                success: function(json){
                    
                    if(json=="0"){
                        $("#listTraitementClean").append("<tr><td colspan='8'><h3 style='color:#d44d06'>Aucune information trouvée dans le Résumé</h3></td></tr>");
                        
                        $('.loading').hide();
                        $("#typeDonnee").removeAttr('disabled');
                        $("#lot").removeAttr('disabled');
                    }
                    else{
                    
                        $.each(json, function(i, v){
                            if(v.operation=='Cleaning Referencement'){
                                var typ= "Référencement"; 
                                var match="Affecté : "+v.total_match_afected;
                            } 
                            else if(v.operation=='Cleaning Branchement'){
                                var typ= "Branchement";
                                var match=" Non applicable ";
                            } 
                            
                            $("#listTraitementClean").append("<tr><td>"+(i+1)+"</td><td>"+typ+"</td><td>Lot "+v.lot+"</td>"
                                    +"<td>"+v.total_reperImport_before+"</td>"
                                    +"<td>"+v.total_reper_after+ "</td>"
                                    +"<td>Affecté : "+match+"</td>"
                                    +"<td> No Obs : "+ v.total_noObs+"</br> Doublon : "+ v.total_doublon+"</td>"
                                    +"<td>"+v.dateOperation+"</td>"
                                                            );
                        });
                        
                        $('.loading').hide();
                        $("#typeDonnee").removeAttr('disabled');
                        $("#lot").removeAttr('disabled');
                    }
            }});
//        }
//        else {
//        }
        
    });
    
    // AFFICHAGE JOURNAL ANOMALIE
    $(document).on('change', '.selectAnomalie', function(e){
        e.preventDefault();
        
        var typeD = $("#typeDonnee").val();
        
        if(typeD!=""){
            var lot = $("#lot").val();
            var anomalie=$("#anomalie").val();
            
            $('#typeDonnee').attr('disabled', 'on');
            $('#lot').attr('disabled', 'on');
            $('#anomalie').attr('disabled', 'on');
            
            $(this).removeAttr('disabled');
        
            $('.loading').show();
            $('#btn_export').hide();

            $("#listDataAnomalies").empty();
            
            $.ajax({
                type:'get',
                url:'dist/ajax_php.php',
                data:'journalAnomalie' + '&typeDonnee=' + typeD + '&lot=' + lot + '&anomalie=' + anomalie,
                dataType:'json',
                success: function(json){
                    if(json=="0"){
                        $("#listDataAnomalies").append("<tr><td colspan='7'><h3 style='color:#d44d06'>Aucune Anomalie trouvée ...</h3></td></tr>");
                        
                        $('.loading').hide();
                        $('#btn_export').hide();
                        $("#typeDonnee").removeAttr('disabled');
                        $("#lot").removeAttr('disabled');
                        $("#anomalie").removeAttr('disabled');
                    }
                    else{
                        $.each(json, function(i, v){
                            if(typeD=='Reperage'){
                                $("#listDataAnomalies").append("<tr><td>"+(i+1)+"</td><td>Lot "+v.lot+"</td>"
                                +"<td>"+v.name_client+"</td><td><b>"+v.ref_client+"</b> </td>" 
                                +"<td>"+v.num_home+", "+v.avenue+", <br/>"+v.commune+"</td>"
                                +"<td>"+v.controller_name+"</td><td>"+v.label+"</td></tr>");
                            }
                            else if(typeD=='Realisation'){
                                $("#listDataAnomalies").append("<tr><td>"+(i+1)+"</td><td>Lot "+v.lot+"</td>"
                                    +"<td>"+v.client+"</td><td><b>"+v.ref_client+"</b> </td>" 
                                    +"<td>"+v.num_home+", "+v.avenue+", <br/>"+v.address+", "+v.commune+"</td>"
                                    +"<td>"+v.consultant+"</td><td>"+v.label+"</td></tr>");
                            }
                        });

                        $('.loading').hide();
                        $('#btn_export').show();
                        $("#typeDonnee").removeAttr('disabled');
                        $("#lot").removeAttr('disabled');
                        $("#anomalie").removeAttr('disabled');
                    }
            }});
        }
        else {
            $("#listDataAnomalies").empty();
            $('#btn_export').hide();
            $('#lot').attr('disabled', 'on');
            $('#anomalie').attr('disabled', 'on');
        }
        
    });
    
    
    // EXPORTATION JOURNAL ANOMALIE
    $(document).on('click', '#btn_export', function(e){
        e.preventDefault();
        
        var typeD = $("#typeDonnee").val();
        var lot = $("#lot").val();
        var anomalie=$("#anomalie").val();
        
        window.open("dist/ajax_php.php?exporter=export&typeDonnee="+typeD+"&lot="+lot+"&anomalie="+anomalie);
        
    });
    
});

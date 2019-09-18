    
$(document).ready(function (e){
    var jsonKobo= new Array(20);
    
    $(document).on('click', '.grize', function(e){
        $('.grize').attr('disabled', 'on');
        $(this).removeAttr('disabled');
    });
    
    //$('#example').DataTable();
//    $('#tablePage').DataTable({
//        "paginType":"full_numbers"
//    });
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
                        
                        jsonKobo[value[0]]=JSON.parse(value[4]);
                        
                    });
                    ligne.find('.ldTD').hide();    
                    ligne.find('.okTD').show();    
                    ligne.find('.btn_display').show();    
                }
                $('.grize').removeAttr('disabled');
        }});
    });
    
    
    
    //TODO:: Envoi + Reponse ACTUALISATION API QTE LIGNE POUR TOUT REPERAGE ET REALISATION
    $(document).on('click', '#api_actualise', function(e){
        var lot=0;
        var typeD='Reperage';
        var lign_class='.lign_1';
        
        $("#loadingImport02").hide();
        $(".btn_display").hide();
        $(".reper_BtnAll").hide();
        $(".real_BtnAll").hide();
        $('.tableau_affichage #lotApi_affichage').empty();
        $('.tableau_affichage').hide();
        
        for (var i = 1; i < 21; i++) {
            lot++;
            //if(lot==4) {
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
                $(".btn_display").show();
            }})
            
            .done(function(data) {
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
                    $.each(data, function(index, value){
                        if(value[3]=='Reperage'){
                            ligne1=$('.lign_1'+value[0]);
                            ligne1.find('.lot_date').text(value[2]);
                            ligne1.find('.lot_detail').text("Enregistrement(s) : "+value[1]);

                            $(".reper_BtnAll").show();
                        }
                        else if(value[3]=='Realisation'){
                            ligne1=$('.lign_2'+value[0]);
                            ligne1.find('.lot_date').text(value[2]);
                            ligne1.find('.lot_detail').text("Enregistrement(s) : "+value[1]);

                            $(".real_BtnAll").show();
                        }
                        
                        jsonKobo[value[0]-1]=value[4];
                    });
                    
                    ligne1.find('.ldTD').hide();
                    ligne1.find('.okTD').show();
                    ligne1.find('.btn_display').show();
                }
                $('.grize').removeAttr('disabled');
            })
            .fail(function(data) {
                var ligne1;
                if(data[2]=='Reperage') ligne1=$('.lign_1'+data[0]);
                else if(data[2]=='Realisation') ligne1=$('.lign_2'+data[0]);

                ligne1.find('.btn_display').hide();
                ligne1.find('.ldTD').hide();
                ligne1.find('.failTD').show();
                $('.grize').removeAttr('disabled');
            });
            
            //if(i==6) break;
        }
        
    });
    
    
    //TODO:: Envoi + Reponse IMPORTATION TELECHARGEMENT DIRECT API QTE LIGNE PAR LOT (DE TOUS)
    $(document).on('click', '#api_downAll', function(e){
        var lot=0;
        var typeD='Reperage';
        var lign_class='.lign_1';
        
//        $("#loadingImport02").hide();
//        $(".reper_BtnAll").hide();
//        $(".real_BtnAll").hide();
//        $('.tableau_affichage #lotApi_affichage').empty();
//        $('.tableau_affichage').hide();
        
//        for (var i = 1; i < 21; i++) {
//            lot++;
//            //if(lot==4) {
//            if(lot==11) {
//                lot=1;
//                typeD='Realisation';
//                lign_class='.lign_2';
//            }
//            
//            var ligne=$(lign_class+lot);
//
//            ligne.find('.okTD').hide();
//            ligne.find('.ldTD').show();
//
//            $.ajax({
//            type:'get',
//            url:'dist/traitement_api.php',
//            data:'traitement_api' + '&btn=' + 'api_TelechargeLot' + '&lot=' + lot + '&typeDonnee=' + typeD,
//            dataType:'json',
//            success: function(json){}})
//            
//            .done(function(data) {
//                var ligne1;
//                if(data[1]=="Error"){
//                    if(data[2]=='Reperage') ligne1=$('.lign_1'+data[0]);
//                    else if(data[2]=='Realisation') ligne1=$('.lign_2'+data[0]);
//                    
//                    ligne1.find('.ldTD').hide();
//                    ligne1.find('.failTD').show();
//                    ligne1.find('.lot_detail').text(data[3]);
//                }
//                else {
//                    $.each(data, function(index, value){
//
//                        if(value[3]=='Reperage'){
//                            ligne1=$('.lign_1'+value[0]);
//                            ligne1.find('.lot_date').text(value[2]);
//                            ligne1.find('.lot_detail').text("Téléchargé(s) : "+value[1]);
//
//                            $(".reper_BtnAll").show();
//                        }
//                        else if(value[3]=='Realisation'){
//                            ligne1=$('.lign_2'+value[0]);
//                            ligne1.find('.lot_date').text(value[2]);
//                            ligne1.find('.lot_detail').text("Téléchargé(s) : "+value[1]);
//
//                            $(".real_BtnAll").show();
//                        }
//                    });
//                    ligne1.find('.ldTD').hide();
//                    ligne1.find('.okTD').show();
//                }
//                $('.grize').removeAttr('disabled');
//            })
//            .fail(function(data) {
//                var ligne1;
//                if(data[2]=='Reperage') ligne1=$('.lign_1'+data[0]);
//                else if(data[2]=='Realisation') ligne1=$('.lign_2'+data[0]);
//
//                ligne1.find('.ldTD').hide();
//                ligne1.find('.failTD').show();
//                $('.grize').removeAttr('disabled');
//            });
//            
//            //if(i==2) break;
//        }
        
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
        $('.tableau_affichage h2').text("Affichage Liste "+typeD+" par Lot");
        
        var i=0;
        $.each(jsonKobo[lot-1], function(index, value){
            i++;
            if(typeD=='Reperage'){
                if(value.Nom_Client === undefined ) var nomClient=value.NomClient;
                else var nomClient=value.Nom_Client;

                if(value.Avenue_Quartier === undefined ) var avenue=value.AvenueQuartier;
                else var nomClient=value.Avenue_Quartier;

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
                        +"<td>"+value.Emplacement_du_branchement_r_alis+"</td>"
                        +"<td></td>"
                        +"<td>"+value.Branchement_Social_ou_Appropri+"</td>"
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
            if(jsonKobo.length==i) finTour=jsonKobo.length;
            
            $.ajax({
                type:'get',
                url:'dist/traitement_api.php',
                async: false,
                data:'traitement_api' + '&btn=' + 'api_TelechargeLot' + '&lot=' + lot + '&typeDonnee=' + typeD+ '&row='+JSON.stringify(value)+'&finTour='+finTour,
                dataType:'json',
                complete: function(data){
                    ligne.find('.lot_detail').text("Récuperation : "+i+"/"+jsonKobo[lot-1].length);
                },
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
                        ligne.find('.okTD').show();
                    }
                    $('.grize').removeAttr('disabled');
                }});
        });
        
    });
    
    
    
    
    
    // Cleaning Data Reperage Process par lot
    $(document).on('click', '.cleanDataReper', function(e){
        e.preventDefault();
        
        var lot = $(this).attr('dir');
        var ligne=$(this).parent('td').parent('tr');
        
        ligne.find('.okTD').hide();
        ligne.find('.loading').show();
        
         $("#rapportCleaningReper").empty();
        
        $.ajax({
            type:'get',
            url:'dist/cleaning_proccess.php',
            data:'cleanDataReper' + '&lot=' + lot,
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
        
        ligne.find('.okTD').hide();
        ligne.find('.loading').show();
        
         $("#rapportCleaningReal").empty();
        
        $.ajax({
            type:'get',
            url:'dist/cleaning_proccess.php',
            data:'cleanDataReal' + '&lot=' + lot,
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
        
        if(typeD!=""){
            var lot = $("#lot").val();
            var date_1=$("#date_1").val();
            var date_2=$("#date_2").val();
        
            $("#listTraitementClean").empty();

            $.ajax({
                type:'get',
                url:'dist/ajax_php.php',
                data:'rapportClean'+'&typeDonnee='+typeD+'&lot='+lot+'&date_1=' + date_1 + '&date_2=' + date_2,
                dataType:'json',
                success: function(json){
                    
                    $.each(json, function(i, v){
                        
                        $("#listTraitementClean").append("<tr><td>"+(i+1)+"</td><td>Lot "+v.lot+"</td>"
                                +"<td>Avant :"+v.total_reperImport_before+" </br>Aprés : "+v.total_reperImport_after+"</td>"
                                +"<td>Avant : "+ v.total_reper_before + "</br>Aprés : "+v.total_reper_after+ "</td>"
                                +"<td>Trouvé : "+ v.total_cleaned_found + "</br>Traité : "+ v.total_cleaned_afected+ "</td>"
                                +"<td>Trouvé : "+ v.total_match_found+" </br>Affecté : "+v.total_match_afected+"</td>"
                                +"<td> No Obs : "+ v.total_noObs+"</br> Doublon : "+ v.total_doublon+" </br>No Obs et Doublon : "+ v.total_noObs_doublon+"</td>"
                                +"<td>"+v.dateOperation+"</td>"
                                                        );
                    });
                    
            }});
        }
        else {
        }
        
    });
    
    // AFFICHAGE JOURNAL ANOMALIE
    $(document).on('change', '.selectAnomalie', function(e){
        e.preventDefault();
        
        var typeD = $("#typeDonnee").val();
        
        if(typeD!=""){
            var lot = $("#lot").val();
            var anomalie=$("#anomalie").val();
        
            $('#btn_export').hide();

            $("#listDataAnomalies").empty();
            
            $.ajax({
                type:'get',
                url:'dist/ajax_php.php',
                data:'journalAnomalie' + '&typeDonnee=' + typeD + '&lot=' + lot + '&anomalie=' + anomalie,
                dataType:'json',
                success: function(json){
                    
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
                                +"<td>"+v.entreprise+"</td><td>"+v.label+"</td></tr>");
                        }
                    });
                    
                    $('#btn_export').show();
                    $("#lot").removeAttr('disabled');
                    $("#anomalie").removeAttr('disabled');
                    $('#example').DataTable().ajax.reload();
                    //$('#example').DataTable().ajax.reload();alert("mmlm");
                    //$('#example').reload(); 
            }});
        }
        else {
            $("#listDataAnomalies").empty();
            $('#btn_export').hide();
            $("#lot").removeAttr('disabled');
            $("#anomalie").removeAttr('disabled');
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

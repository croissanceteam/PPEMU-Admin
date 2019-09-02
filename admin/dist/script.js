$(document).ready(function (e){
    
    //TODO:: Envoi du FOrmulaire + Reponse IMPORTATION CSV XLS
    $("#formImport01").on('submit',(function(e){
        e.preventDefault();
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
    }));
    
    
    //TODO:: Envoi + Reponse IMPORTATION API 
//    $(document).on('click', '#api_actualise', function(e){
//        e.preventDefault();
//        $.ajax({
//            url: "https://kf.kobotoolbox.org/assets/au2pD2CP4VRoqcwB5fvLzD/submissions/?format=json",
//            contentType: 'json',
//            dataType:'json',
//            headers:{"Authorization":"Token 1d99e5378a5924e824d30a09d08ab26bdeb4dfe1"},
//            //headers:{"Authorization":'Basic ' + btoa('Token 1d99e5378a5924e824d30a09d08ab26bdeb4dfe1')},
//            success: function(data){
//                alert(data);
//            },
//            error: function(){} 	        
//        });
//    });
    
    
    //TODO:: Envoi + Reponse IMPORTATION API QTE LIGNE PAR LOT (DE TOUS)
    $(document).on('click', '#api_actualise', function(e){
        $(".ldText").text(" Actualisation Synchronisation ...");
        $("#loadingImport02").show();
        $(".reper_BtnAll").hide();
        $(".real_BtnAll").hide();
        $("#lotApi_reperage").empty();
        $("#lotApi_realisation").empty();
        $('.tableau_affichage #lotApi_affichage').empty();
        $('.tableau_affichage').hide();
        
        $.ajax({
            type:'get',
            url:'dist/traitement_api.php',
            data:'traitement_api' + '&btn=' + 'api_actualise',
            dataType:'json',
            success: function(json){
                //alert(json.length);
                //alert(json.Ref_client);
                //alert(json);
                
                $.each(json, function(index, value){
                    //alert(value[0]+' '+value[1]);
                    if(value[3]=='Reperage'){
                        $("#lotApi_reperage").append("<tr><td><img src='./dist/img/ajax-loader.gif' class='ldTD' style='display:none'></td>"
                                    +"<td>Lot "+value[0]+"</td>"
                                    +"<td>"+value[2]+"</td><td>Enregistrement(s) : "+value[1]+"</td>"
                                    +"<td><input type='button' name='"+value[0]+"' class='btn btn-warning api_affichLot' dir='"+value[3]+"' value='Affiche' />"
                                    +"<input type='button' name='"+value[0]+"' class='btn btn-success api_TelechargeLot' dir='"+value[3]+"' value='Sync' />"
                                                        +"</td>");
                        $(".reper_BtnAll").show();
                    }
                    else if(value[3]=='Realisation'){
                        $("#lotApi_realisation").append("<tr><td><img src='./dist/img/ajax-loader.gif' class='ldTD' style='display:none'></td>"
                                    +"<td>"+value[2]+"</td><td>Enregistrement(s) : "+value[1]+"</td>"
                                    +"<td><input type='button' name='"+value[0]+"' class='btn btn-warning api_affichLot' dir='"+value[3]+"' value='Affiche' />"
                                    +"<input type='button' name='"+value[0]+"' class='btn btn-success api_TelechargeLot' dir='"+value[3]+"' value='Sync' />"
                                                        +"</td>");
                        $(".real_BtnAll").show();
                    }
                });
                
                $("#loadingImport02").hide();    
        }});
    });
    
    
    //TODO:: Envoi + Reponse IMPORTATION TELECHARGEMENT DIRECT API QTE LIGNE PAR LOT (DE TOUS)
    $(document).on('click', '#api_downAll', function(e){
        $(".ldText").text(" Téléchargement de tous les Données ...");
        $("#loadingImport02").show();
        
        $(".reper_BtnAll").hide();
        $(".real_BtnAll").hide();
        $("#lotApi_reperage").empty();
        $("#lotApi_realisation").empty();
        $('.tableau_affichage #lotApi_affichage').empty();
        $('.tableau_affichage').hide();
        
        $.ajax({
            type:'get',
            url:'dist/traitement_api.php',
            data:'traitement_api' + '&btn=' + 'api_downAll',
            dataType:'json',
            success: function(json){
                $.each(json, function(index, value){
                    alert('Fin Téléchargement \n Nombres de Lignes Téléchargé : '+value[1]+' n\ Date Exportation ');
                });
                
                $("#loadingImport02").hide();    
        }});
    });
    
    
    //TODO:: Envoi + Reponse AFFICHAGE DONNE API PAR LOT
    $(document).on('click', '.api_affichLot', function(e){
        var lot =$(this).attr('name');
        var typeD =$(this).attr('dir');
        var ligne=$(this).parent('td').parent('tr');
        
        ligne.find('.ldTD').show();
        
        $('.tableau_affichage #lotApi_affichage').empty();
        
        $('.tableau_affichage').show();
        $('.tableau_affichage h2').text("Affichage Liste "+typeD+" par Lot");
        
        $.ajax({
            type:'get',
            url:'dist/traitement_api.php',
            data:'traitement_api' + '&btn=' + 'api_afficheLot' + '&lot=' + lot + '&typeDonnee=' + typeD,
            dataType:'json',
            success: function(json){
                var i=0;
                $.each(json, function(index, value){
                    i++;
                    if(typeD=='Reperage'){
                        $("#lotApi_affichage").append("<tr><td>"+i+"</td>"
                                +"<td>Lot "+lot+"</td>"
                                +"<td>"+value.Nom_Client+"<br/>Réf.:"+value.Ref_Client+"</td>"
                                +"<td>"+value.Num_ro_parcelle+", "+value.Avenue_Quartier+",<br/>"+value.Commune+"</td>"
                                +"<td>"+value.G_olocalisation+"</td>"
                                +"<td>"+value.Cat_gorie_Client+"</td>"
                                +"<td>"+value.Etat_du_point_de_vente+"</td>"
                                +"<td>"+value.Commentaires+"</td>"
                                +"<td>"+value._submission_time+"</td>"
                                                        );
                    }
                    else if(typeD=='Realisation'){
                        $("#lotApi_affichage").append("<tr><td>"+i+"</td>"
                                +"<td>Lot "+iLot+"</td>"
                                +"<td>"+val1.Nom_du_Client+"<br/>Réf.:"+val1.Ref_Client+"</td>"
                                +"<td>"+val1.Num_ro+", "+val1.Avenue +", "+val1.Quartier +",<br/>"+val1.Commune+"</td>"
                                +"<td>"+val1.Emplacement_du_branchement_r_alis+"</td>"
                                +"<td></td>"
                                +"<td>"+val1.Branchement_Social_ou_Appropri+"</td>"
                                +"<td>"+val1.Commentaires+"</td>"
                                +"<td>"+val1.Date+"</td>"
                                                    );
                    }
                });
                
                ligne.find('.ldTD').hide();
        }});
    });
    
    
    //TODO:: Envoi + Reponse TELECHARGEMENT DONNE API PAR LOT
    $(document).on('click', '.api_TelechargeLot', function(e){
        var lot =$(this).attr('name');
        var typeD =$(this).attr('dir');
        var ligne=$(this).parent('td').parent('tr');
        
        ligne.find('.ldTD').show();
        
        $('.tableau_affichage #lotApi_affichage').empty();
        
        $('.tableau_affichage').show();
        $('.tableau_affichage h2').text("Affichage Liste "+typeD+" par Lot");
        
        $.ajax({
            type:'get',
            url:'dist/traitement_api.php',
            data:'traitement_api' + '&btn=' + 'api_TelechargeLot' + '&lot=' + lot + '&typeDonnee=' + typeD,
            dataType:'json',
            success: function(json){
                var i=0;
                $.each(json, function(index, value){
                    i++;
                    alert('Fin Téléchargement \n OPERATION : '+typeD+' \n LOT : '+lot+' \n Nombres de Lignes Téléchargé : '+value[1]+' n\ Date Exportation ');
                });
                
                ligne.find('.ldTD').hide();
        }});
    });
    
    
    //TODO:: Envoi + Reponse AFFICHAGE DONNE TOUS LE LOT API PAR REPERAGE OU REALISATION
    $(document).on('click', '.btn_affiche0', function(e){
        var typeD =$(this).attr('dir');
        
        if(typeD=='Reperage') $(".ldReper_BtnAll").show();
        else $(".ldReal_BtnAll").show();
        
        $(".ldText2").text(" Affichage");
        
        $('.tableau_affichage #lotApi_affichage').empty();
        
        $('.tableau_affichage').show();
        $('.tableau_affichage h2').text("Affichage Liste "+typeD+" (tous)");
        
        $.ajax({
            type:'get',
            url:'dist/traitement_api.php',
            data:'traitement_api' + '&btn=' + 'api_afficheTout0' + '&typeDonnee=' + typeD,
            dataType:'json',
            success: function(json){
                var i=0;
                var iLot=0;
                $.each(json, function(index, value){
                    iLot++;
                    $.each(value, function(ind, val1){
                    i++;
                        if(typeD=='Reperage'){
                            $("#lotApi_affichage").append("<tr><td>"+i+"</td>"
                                    +"<td>Lot "+iLot+"</td>"
                                    +"<td>"+val1.Nom_Client+"<br/>Réf.:"+val1.Ref_Client+"</td>"
                                    +"<td>"+val1.Num_ro_parcelle+", "+val1.Avenue_Quartier+",<br/>"+val1.Commune+"</td>"
                                    +"<td>"+val1.G_olocalisation+"</td>"
                                    +"<td>"+val1.Cat_gorie_Client+"</td>"
                                    +"<td>"+val1.Etat_du_point_de_vente+"</td>"
                                    +"<td>"+val1.Commentaires+"</td>"
                                    +"<td>"+val1._submission_time+"</td>"
                                                        );
                        }
                        else if(typeD=='Realisation'){
                            $("#lotApi_affichage").append("<tr><td>"+i+"</td>"
                                    +"<td>Lot "+iLot+"</td>"
                                    +"<td>"+val1.Nom_du_Client+"<br/>Réf.:"+val1.Ref_Client+"</td>"
                                    +"<td>"+val1.Num_ro+", "+val1.Avenue +", "+val1.Quartier +",<br/>"+val1.Commune+"</td>"
                                    +"<td>"+val1.Emplacement_du_branchement_r_alis+"</td>"
                                    +"<td></td>"
                                    +"<td>"+val1.Branchement_Social_ou_Appropri+"</td>"
                                    +"<td>"+val1.Commentaires+"</td>"
                                    +"<td>"+val1.Date+"</td>"
                                                        );
                        }
                    });
                });
                
                $(".ldReper_BtnAll").hide();
                $(".ldReal_BtnAll").hide();
        }});
    });
    
    
    //TODO:: Envoi + Reponse AFFICHAGE DONNE TOUS LE LOT API PAR REPERAGE OU REALISATION
    $(document).on('click', '.btn_telecharge0', function(e){
        var typeD =$(this).attr('dir');
        
        if(typeD=='Reperage') $(".ldReper_BtnAll").show();
        else $(".ldReal_BtnAll").show();
        
        $(".ldText2").text(" Affichage");
        
        $('.tableau_affichage #lotApi_affichage').empty();
        
        $('.tableau_affichage').show();
        $('.tableau_affichage h2').text("Affichage Liste "+typeD+" (tous)");
        
        $.ajax({
            type:'get',
            url:'dist/traitement_api.php',
            data:'traitement_api' + '&btn=' + 'api_telechargeTout0' + '&typeDonnee=' + typeD,
            dataType:'json',
            success: function(json){
                var i=0;
                var iLot=0;
                $.each(json, function(index, value){
                    iLot++;
                    $.each(value, function(ind, val1){
                    i++;
                        alert('Fin Téléchargement \n OPERATION : '+typeD+' \n Nombres de Lignes Téléchargé : '+value[1]+' n\ Date Exportation ');
                    });
                });
                
                $(".ldReper_BtnAll").hide();
                $(".ldReal_BtnAll").hide();
        }});
    });
    
    
    
    
    $(document).on('click', '.cleanDataReper', function(e){
        // pour Clean Data Reperage d 'un lot
        e.preventDefault();
        //alert("1");
        var lot = $(this).attr('dir');
        var ligne=$(this).parent('td').parent('tr');
        
        ligne.find('.loading').show();
        
         $("#rapportCleaningReper").empty();
        
        $.ajax({
            type:'get',
            url:'dist/cleaning_proccess.php',
            data:'cleanDataReper' + '&lot=' + lot,
            dataType:'Text',
            success: function(json){
//                $("#rapportCleaningReper").append("json");
                $("#rapportCleaningReper").append(json);
                //alert(json);
                ligne.find('.loading').hide();
//                $('#contPrintPaie .dateRec').text(json[0].dateP);
//                $('#contPrintPaie .montlettrRec').text(json[1]);

        }});
        
    });
    
});

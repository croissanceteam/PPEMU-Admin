$(document).ready(function (e) {
    var jsonKobo = new Array(20);
    var incr = 0;
    var incrAll = new Array(20);

    $(document).on('click', '.grize', function (e) {
        incr = 0;
        $('.grize').attr('disabled', 'on');
        // $(this).removeAttr('disabled');
    });


    //TODO:: SELECT TYPE DONNES IMPORTATION CSV XLS
    $(document).on('change', '#typeDonnee', function (e) {
        if ($(this).val() != "")
            $('.grize_1').removeAttr('disabled');
        else
            $('.grize_1').attr('disabled', 'on');

    });
    //TODO:: Envoi du FOrmulaire + Reponse IMPORTATION CSV XLS
    $("#formImport01").on('submit', (function (e) {
        incr = 0;
        e.preventDefault();

        var typeD = $("#typeDonnee").val();

        if (typeD != "") {
            $("#loadingImport01").show();
            $.ajax({
                url: "dist/ajax_php.php",
                type: "POST",
                data: new FormData(this),
                contentType: false,
                cache: false,
                processData: false,
                success: function (data) {
                    $("#loadingImport01").hide();
                    $("#msgImport01").html(data);
                    $("#formImport01")[0].reset();
                },
                error: function () {
                }
            });
        }

    }));

    $(document).on('click', '#api_actualise', function(e){
        incr=0;
        var lot=0;
        var typeD='Realisation';
        var line_class='.line';
        
        $("#loadingImport02").hide();
        $('.tableau_affichage #lotApi_affichage').empty();
        $('.tableau_affichage').hide();
        
        for (var i = 1; i < 11; i++) {
            lot++;
            
            var ligne=$(line_class+lot);

            ligne.find('.okTD').hide();
            ligne.find('.failTD').hide();
            ligne.find('.ldTD').show();
            ligne.find('.lot_date').text('');
            ligne.find('.lot_detail').text('');

            $.ajax({
                type:'get',
                url:'dist/traitement_api.php',
                data:'traitement_api' + '&btn=' + 'api_synchroniseLot' + '&lot=' + lot + '&typeDonnee=' + typeD,
                dataType:'json',
            })
            .done(function(data) {
                console.log(data);
                
                incr++;
                var ligne1;
                if(data[1]=="Error"){
                    ligne1=$('.line'+data[0]);
                    
                    ligne1.find('.ldTD').hide();
                    ligne1.find('.failTD').show();
                    ligne1.find('.lot_detail').text(data[3]);
                }
                else {
                    var nbrEnreg=0;
                    $.each(data, function(index, value){
                        ligne1=$('.line'+value[0]);
                        ligne1.find('.lot_date').text(value[2]);
                        ligne1.find('.lot_detail').text("Enregistrement(s) : "+value[1]);
                        //jsonKobo[value[0]-1]=value[4];
                        nbrEnreg=value[1];
                    });
                    
                    ligne1.find('.ldTD').hide();
                    ligne1.find('.failTD').hide();
                    ligne1.find('.okTD').show();
                }
                if(incr==10){
                    $('.grize').removeAttr('disabled');
                } 
            })
            .fail(function(data) {
                incr++;
                var ligne1;
                
                ligne1=$('.line'+(incr));
                
                ligne1=$('.line'+data[0]);
//                console.log(data);
//                alert(JSON.stringify(data));
                if(ligne1!==undefined){
                    ligne1.find('.okTD').hide();
                    ligne1.find('.ldTD').hide();
                    ligne1.find('.failTD').show();
                    ligne1.find('.lot_detail').html("<span style='color:red'>Echec Synchronisation !</span>");
                    //ligne1.find('.grize').removeAttr('disabled');
                    
                }else if(incr==10) {
                    $( ".ldTD" ).each(function() {
                        if ($(this).is(':visible') === true){
                            var ligne0=$(this).parent('td').parent('tr');;
                            $(this).hide();
                            ligne0.find('.failTD').show();
                            ligne0.find('.okTD').hide();
                            ligne0.find('.lot_detail').html("<span style='color:red'>Echec Synchronisation !</span>");
                            ligne0.find('.grize').removeAttr('disabled');
                            $('.grize').removeAttr('disabled');
                            $('#api_downAll').removeAttr('disabled');
                        }
                    });
                    
                }
            });
        }
    });

    //TODO:: Envoi + Reponse ACTUALISATION API QTE LIGNE PAR LOT
    $(document).on('click', '.api_synchroniseLot', function (e) {
        incr = 0;
        var lot = $(this).attr('name');
        var typeD = $(this).attr('dir');
        var ligne = $(this).parent('td').parent('tr');

        $("#loadingImport02").hide();

        ligne.find('.okTD').hide();
        ligne.find('.failTD').hide();
        ligne.find('.ldTD').show();
        ligne.find('.lot_date').text('');
        ligne.find('.lot_detail').text('');

        $('.tableau_affichage #lotApi_affichage').empty();
        $('.tableau_affichage').hide();

        $.ajax({
            type: 'get',
            url: 'dist/traitement_api.php',
            data: 'traitement_api' + '&btn=' + 'api_synchroniseLot' + '&lot=' + lot + '&typeDonnee=' + typeD,
            dataType: 'json',
            success: function (json) {
                if (json[1] == "Error") {
                    ligne.find('.ldTD').hide();
                    ligne.find('.okTD').hide();
                    ligne.find('.failTD').show();
                    ligne.find('.lot_detail').text(json[3]);
                } else {
                   //console.log('Retour json : ',json[0][4][0]);
                   //console.log('Retour json : ',json);
                   
                    var nbrEnreg = 0;
                    $.each(json, function (index, value) {
                        if (value[3] == 'Realisation') {
                            ligne.find('.lot_date').text(value[2]);
                            ligne.find('.lot_detail').text("Enregistrement(s) : " + value[1]);
                        }
                        jsonKobo[value[0] - 1] = value[4];
                        nbrEnreg = value[1];
                    });
                    ligne.find('.ldTD').hide();
                    ligne.find('.okTD').show();
                }
                $('.grize').removeAttr('disabled');
            },
            error: function (result, statut, error) {
                console.log('Resultat error :', result);
                console.log('Erreur :', error);
                console.log('Statut error : ', statut);
                ligne.find('.ldTD').hide();
                    ligne.find('.okTD').hide();
            }
        });
    });

    // Cleaning Data Realisation Process par lot
    $(document).on('click', '.cleanDataReal', function (e) {
        e.preventDefault();

        var lot = $(this).attr('dir');
        var ligne = $(this).parent('td').parent('tr');
        var totalData = ligne.find('.lot_detail').text().split(':')[1];
        ligne.find('.grize').hide();
        ligne.find('.okTD').hide();
        ligne.find('.loading').show();

        $("#rapportCleaningReal").empty();

        $.ajax({
            type: 'get',
            url: 'dist/cleaning_proccess.php',
            data: 'cleanDataReal' + '&lot=' + lot + '&total_data=' + totalData,
            dataType: 'Text',
            success: function (json) {

                console.log(json);

                $("#rapportCleaningReal").append(json);

                ligne.find('.lot_detail').text("Nombre de Ligne : 0");

                ligne.find('.loading').hide();
                ligne.find('.okTD').show();
                $('.grize').removeAttr('disabled');

                // $.ajax({
                //     type: 'get',
                //     url: 'dist/cleaning_proccess.php',
                //     data: 'cleanDataReal_suite' + '&lot=' + lot,
                //     dataType: 'Text',
                //     success: function (json) {
                //         $("#rapportCleaningReal").append(json);

                //         ligne.find('.lot_detail').text("Nombre de Ligne : 0");

                //         ligne.find('.loading').hide();
                //         ligne.find('.okTD').show();
                //         $('.grize').removeAttr('disabled');

                //     }
                // });

            }
        });

    });


    // AFFICHAGE RAPPORT TRAITEMENT CLEANING
    $(document).on('change', '.selectTraitement', function (e) {
        e.preventDefault();

        // var typeD = $("#typeDonnee").val();
        var typeD = "Realisation";

        //if(typeD!=""){
        var lot = $("#lot").val();

        // $('#typeDonnee').attr('disabled', 'on');
        // $('#lot').attr('disabled', 'on');

        $(this).removeAttr('disabled');

        $("#listTraitementClean").empty();

        $.ajax({
            type: 'get',
            url: 'dist/ajax_php.php',
            data: 'rapportClean' + '&typeDonnee=' + typeD + '&lot=' + lot,
            dataType: 'json',
            success: function (json) {

                if (json == "0") {
                    $("#listTraitementClean").append("<tr><td colspan='8'><h3 style='color:#d44d06'>Aucune information trouvée dans le résumé</h3></td></tr>");

                    $('.loading').hide();
                    $("#typeDonnee").removeAttr('disabled');
                    $("#lot").removeAttr('disabled');
                } else {

                    $.each(json, function (i, v) {
                        if (v.operation == 'Cleaning référencement') {
                            var typ = "Référencement";
                            var match = "Affecté : " + v.total_match_afected;
                        } else if (v.operation == 'Cleaning branchements') {
                            var typ = "Branchement";
                            var match = " Non applicable ";
                        }
                        console.log('Voici le type : ',typ);
                        $("#listTraitementClean").append("<tr><td>" + (i + 1) + "</td><td>Lot " + v.lot + "</td>"
                            + "<td>" + v.total_data_cleaned + "</td>"
                            + "<td>" + v.detail_operation + "</td>"
                            + "<td> Doublons : " + v.total_doublon + "</td>"
                            + "<td>" + v.dateOperation + "</td>"
                        );
                    });

                    $('.loading').hide();
                    $("#typeDonnee").removeAttr('disabled');
                    $("#lot").removeAttr('disabled');
                }
            }
        });
//        }
//        else {
//        }

    });

    // AFFICHAGE JOURNAL ANOMALIE
    $(document).on('change', '.selectAnomalie', function (e) {
        e.preventDefault();

        // var typeD = $("#typeDonnee").val();
        var typeD = "Realisation";

        if (typeD != "") {
            var lot = $("#lot").val();
            var anomalie = $("#anomalie").val();

            // $('#typeDonnee').attr('disabled', 'on');
            // $('#lot').attr('disabled', 'on');
            // $('#anomalie').attr('disabled', 'on');

            $(this).removeAttr('disabled');

            $('.loading').show();
            $('#btn_export').hide();

            $("#listDataAnomalies").empty();

            $.ajax({
                type: 'get',
                url: 'dist/ajax_php.php',
                data: 'journalAnomalie' + '&typeDonnee=' + typeD + '&lot=' + lot + '&anomalie=' + anomalie,
                dataType: 'json',
                success: function (json) {
                    if (json == "0") {
                        $("#listDataAnomalies").append("<tr><td colspan='7'><h3 style='color:#d44d06'>Aucune Anomalie trouvée ...</h3></td></tr>");

                        $('.loading').hide();
                        $('#btn_export').hide();
                        $("#typeDonnee").removeAttr('disabled');
                        $("#lot").removeAttr('disabled');
                        $("#anomalie").removeAttr('disabled');
                    } else {
                        $.each(json, function (i, v) {
                            
                            $("#listDataAnomalies").append("<tr><td>" + (i + 1) + "</td><td>Lot " + v.lot + "</td>"
                                + "<td>" + v.client + "</td><td><b>" + v.ref_client + "</b> </td>"
                                + "<td>" + v.num_home + ", " + v.avenue + ", <br/>" + v.address + ", " + v.commune + "</td>"
                                + "<td>" + v.consultant + "</td><td>" + v.label + "</td></tr>");
                            
                        });

                        $('.loading').hide();
                        $('#btn_export').show();
                        $("#typeDonnee").removeAttr('disabled');
                        $("#lot").removeAttr('disabled');
                        $("#anomalie").removeAttr('disabled');
                    }
                }
            });
        } else {
            $("#listDataAnomalies").empty();
            $('#btn_export').hide();
            $('#lot').attr('disabled', 'on');
            $('#anomalie').attr('disabled', 'on');
        }

    });


    // EXPORTATION JOURNAL ANOMALIE
    $(document).on('click', '#btn_export', function (e) {
        e.preventDefault();
        incr = 0;

        var typeD = $("#typeDonnee").val();
        var lot = $("#lot").val();
        var anomalie = $("#anomalie").val();

        window.open("dist/ajax_php.php?exporter=export&typeDonnee=" + typeD + "&lot=" + lot + "&anomalie=" + anomalie);

    });

});

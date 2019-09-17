$(document).ready(function(){
    var dataTable = $('#users-table').DataTable({
        //"processing": true,
        //"serverSide": true,
        //"ordering":false,
        "ajax": {
            url: "dist/userTrait.php?list",
            dataSrc: '',
        },
        responsive: 'true',
        columns: [
            {"data": "position"},        
            {"data": "statusicon"}, 
            {"data": "username"},
            {"data": "fullname"},
            {"data": "email"},
            {"data": "phone"},
            {"data": "town"},
            {"data": "status"}
        ],
        "language": {
            "sProcessing": "Traitement en cours...",
            "sSearch": "Rechercher&nbsp;:",
            "sLengthMenu": "Afficher _MENU_ &eacute;l&eacute;ments",
            "sInfo": "Affichage de l'&eacute;l&eacute;ment _START_ &agrave; _END_ sur _TOTAL_ &eacute;l&eacute;ments",
            "sInfoEmpty": "Affichage de l'&eacute;l&eacute;ment 0 &agrave; 0 sur 0 &eacute;l&eacute;ment",
            "sInfoFiltered": "(filtr&eacute; de _MAX_ &eacute;l&eacute;ments au total)",
            "sInfoPostFix": "",
            "sLoadingRecords": "Chargement en cours...",
            "sZeroRecords": "Aucun &eacute;l&eacute;ment &agrave; afficher",
            "sEmptyTable": "Aucune donn&eacute;e disponible dans le tableau",
            "oPaginate": {
                "sFirst": "Premier",
                "sPrevious": "Pr&eacute;c&eacute;dent",
                "sNext": "Suivant",
                "sLast": "Dernier"
            },
            "oAria": {
                "sSortAscending": ": activer pour trier la colonne par ordre croissant",
                "sSortDescending": ": activer pour trier la colonne par ordre d&eacute;croissant"
            }
        }
    });

    var elem = document.querySelector('.js-switch');
    var switcheryUpdate = document.querySelector('.js-switch-update');
    var init = new Switchery(elem, { size: 'small' });
    var init2 = new Switchery(switcheryUpdate, { size: 'small' });
    var statusLabel = document.getElementById('status-label');
    var statusLabel2 = document.getElementById('status-label2');
    
    console.log('Status checked : ',elem.checked);
    elem.onchange = function() {
        //alert(elem.checked);
        if(elem.checked){
            statusLabel.textContent = "Actif";
        }else{
            statusLabel.textContent = "Vérouillé";
        }
    };
    switcheryUpdate.onchange = function() {
        //alert(elem.checked);
        if(switcheryUpdate.checked){
            statusLabel2.textContent = "Actif";
        }else{
            statusLabel2.textContent = "Vérouillé";
        }
    };
    function setTrue(checkbox,ref){
        if(checkbox.checked == false){
            $(ref).click();
        }
    }
    function setFalse(checkbox,ref){
        if(checkbox.checked == true){
            $(ref).click();
        }
    }
    
    $('#new-user-form').on('submit',function(e){
        e.preventDefault();
        var mydata = $('#new-user-form').serialize();
        $.ajax({
            url : 'dist/userTrait.php',
            type : 'POST',
            data : mydata,
            dataType: 'json',
            success : function(result, statut){
               console.log('Resultat success :',result);
               console.log('Statut success : ',statut);
               
               
               if (result.number == 1){
                $('#newUserModal').modal('hide');
                 alertify.alert(result.response);
                 dataTable.ajax.reload();
               }else if(result.number == 2 || result.number == 3){
                   alert(result.response);
                   document.querySelector('#longwa').click();
               }else{
                 alertify.alert(result.response);
             }
             
            },
            error : function(result, statut, error){
              console.log('Resultat error :',result);
              console.log('Erreur :',error);
              console.log('Statut error : ',statut);
              alertify.error("L'opération n'a pas abouti.");
            }
        });
    });

    $('.update').on('click',function(){
        
        $.ajax({
            url: "dist/userTrait.php",
            method: "POST",
            data: {id: $(this).attr('id'),op:'get'},
            dataType: "json",
            success: function (data) {
                //console.log('DATA: ',data);

                if(data.username != undefined){
                    $('#username2').val(data.username);
                    $('#fullname').val(data.fullname);
                    $('#phone').val(data.phone);
                    $('#email').val(data.mailaddress);
                    $('#town').val(data.town);
                    $('#update').val(data.userID);
                    if(data.status == 1){
                        if(switcheryUpdate.checked == false){
                            $('.js-switch-update').click();
                        }                                
                    }else{
                        if(switcheryUpdate.checked == true){
                            $('.js-switch-update').click();
                        }
                    }
                    $('#updateUserModal').modal('show');
                }
            },
            error: function (error) {
                console.log("ERROR :",error);
                //console.log("ERROR :",error.responseText);
            }
        });
        
    });
    $('#update-user-form').on('submit',function(e){
        e.preventDefault();
        var mydata = $('#update-user-form').serialize();
        $.ajax({
            url : 'dist/userTrait.php',
            type : 'POST',
            data : mydata,
            dataType: 'json',
            success : function(result, statut){
               console.log('Resultat success :',result);
               console.log('Statut success : ',statut);

               if (result.number == 1){
                    $('#updateUserModal').modal('hide');
                    alertify.success(result.response);
                    dataTable.ajax.reload();
                }else{
                     alertify.alert(result.response);
                }
             
            },
            error : function(result, statut, error){
              console.log('Resultat error :',result);
              console.log('Erreur :',error);
              console.log('Statut error : ',statut);
              alertify.error("L'opération n'a pas abouti.");
            }
        });
    });

    $('#users-table').on('click','tr',function(e){
        var data = dataTable.data();
        var i = e.target._DT_CellIndex.row;
        var user = data[i];
        //alert(user.position);
        $('#username2').val(user.username);
        $('#fullname').val(user.fullname);
        $('#phone').val(user.phone);
        $('#email').val(user.email);
        $('#town').val(user.town);
        if(user.status == 'Actif'){
            setTrue(switcheryUpdate,'.js-switch-update');
        }else{
            setFalse(switcheryUpdate,'.js-switch-update');
        }
        $('#updateUserModal').modal('show');
    });
});
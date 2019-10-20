$(document).ready(function(){
    $('[data-mask]').inputmask();
    
    var dataTable = $('#users-table').DataTable({
        //"processing": true,
        //"serverSide": true,
        //"ordering":false,
        "ajax": {
            url: "dist/userTrait.php?list",
            dataSrc: '',
        },
        "responsive": 'true',
        "columns": [
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
        document.querySelector('#cover-spin').style.display="block";
        $.ajax({
            url : 'dist/userTrait.php',
            type : 'POST',
            data : mydata,
            dataType: 'json',
            success : function(result, statut){
               console.log('Resultat success :',result);
               console.log('Statut success : ',statut);
               
               
               if (result.number == 1){
                document.querySelector('#cover-spin').style.display="none";
                $('#newUserModal').modal('hide');
                 alertify.alert(result.response);
                 dataTable.ajax.reload();
               }else{
                    document.querySelector('#cover-spin').style.display="none";
                    alertify.alert(result.response);
                }
             
            },
            error : function(result, statut, error){
                document.querySelector('#cover-spin').style.display="none";
                console.log('Resultat error :',result);
                console.log('Erreur :',error);
                console.log('Statut error : ',statut);
                alertify.error("L'opération n'a pas abouti.");
            }
        });
    });

    $('#update-user-form').on('submit',function(e){
        e.preventDefault();
        var mydata = $('#update-user-form').serialize();
        document.querySelector('#cover-spin').style.display="block";
        $.ajax({
            url : 'dist/userTrait.php',
            type : 'POST',
            data : mydata,
            dataType: 'json',
            success : function(result, statut){
               console.log('Resultat success :',result);
               console.log('Statut success : ',statut);

               if (result.number == 1){
                    document.querySelector('#cover-spin').style.display="none";
                    $('#updateUserModal').modal('hide');
                    alertify.success(result.response);
                    dataTable.ajax.reload();
                }else{
                    document.querySelector('#cover-spin').style.display="none";
                     alertify.alert(result.response);
                }
             
            },
            error : function(result, statut, error){
                document.querySelector('#cover-spin').style.display="none";
              console.log('Resultat error :',result);
              console.log('Erreur :',error);
              console.log('Statut error : ',statut);
              alertify.error("L'opération n'a pas abouti.");
            }
        });
    });

    $('#users-table tbody').on('click','tr',function(e){
        var data = dataTable.data();
        console.log(data);
        var i = dataTable.row(this).index();
        console.log(i);
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

    $('#reset-pass').on('click',function(e){
        var user = $('#username2').val(); 
        document.getElementById('new-pass').innerHTML ='';
        $.ajax({
            type: 'POST',
            url: 'dist/userTrait.php',
            data: 'reset='+ user,
            dataType: 'json',
            success: function(result){
                $('#updateUserModal').modal('hide');
                console.log('RESULT : ',result);
              if(result.number == 1 && result.response != null){
                alertify.alert(result.response);
              }else{
                alertify.error(result.response);
              }
            },
            error: function(result, statut, error){
                $('#updateUserModal').modal('hide');
              console.log('Resultat error :',result);
              console.log('Erreur :',error);
              console.log('Statut error : ',statut);
              alertify.error("L'opération n'a pas abouti.");
              document.querySelector('#cover-spin').style.display="none";
            }
          });
        
        
    });
    
});
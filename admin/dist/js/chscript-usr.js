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
            {"data": "username"},
            {"data": "fullname"},
            {"data": "email"},
            {"data": "phone"},
            {"data": "actions"}
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
    var init = new Switchery(elem, { size: 'small' });
    var statusLabel = document.getElementById('status-label');
    
    console.log('Status checked : ',elem.checked);
    elem.onchange = function() {
        //alert(elem.checked);
        if(elem.checked){
            statusLabel.textContent = "Actif";
        }else{
            statusLabel.textContent = "Vérouillé";
        }
    };
    
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
        $('#updateUserModal').modal('show');
    });
});
$(document).ready(function(){
    $('#newPassModal').on('shown.bs.modal', function () {
        $('#actual-password').trigger('focus');
      })
      $('#change-password-form').on('submit',function(e){
          e.preventDefault();
      
          var newPass = $('#new-password').val();
          var againPass = $('#new-password-again').val();
          var mydata = $('#change-password-form').serialize();
      
          if(newPass !== againPass){
            alert('Vous avez mal retapé le nouveau mot de passe. Veuillez réessayer.');
            $('#new-password-again').trigger('focus');
          }else{
            // alert('Ok');
            $.ajax({
             url : 'dist/userTrait.php',
             type : 'POST',
             data : mydata,
             dataType: 'json',
             success : function(result, statut){
                console.log('Resultat success :',result);
                console.log('Statut success : ',statut);
                
                if (result.number == 1){
                  alertify.alert(result.response);
                  document.querySelector('#longwa').click();
                }else{
                    alertify.alert(result.response);
                  $('#newPassModal').modal('hide');
                }
                
      
             },
             error : function(result, statut, error){
               console.log('Resultat error :',result);
               console.log('Erreur :',error);
               console.log('Statut error : ',statut);
               alertify.error("L'opération n'a pas abouti.");
             }
      
          });
      
        }
      });
});
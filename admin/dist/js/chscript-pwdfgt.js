$(document).ready(function () {
    $('#send-token').on('click',function(e){
        //console.log('Hello');
          var email = document.getElementById('email').value;
                     
          console.log('EMAIL :',  email.trim() != "");
          
          if(email.trim() != ""){
            document.querySelector('.loader').style.display="block";
            $.ajax({
              type: 'POST',
              url: 'dist/userTrait.php',
              data: 'email=' + email + '&sendmail',
              dataType: 'json',
              success : function(result){
                  console.log('SENDING RESULT : ', result);
                  console.log('RESULT RESPONSE : ', result.response);
                  document.querySelector('.loader').style.display="none";
                  if(result.number == 1){
                    
                    document.querySelector('#token-field').style.display="block";
                    document.querySelector('#mail-field').style.display="none";
                    document.querySelector('#sending').style.display="none";
                    document.querySelector('#checking').style.display="block";
                    document.querySelector('#box-msg').textContent = "Saisissez le code qui vous a été envoyé par mail";
                    //document.querySelector('#send-token').text = "Valider";
                    alertify.alert(result.response);
                  }else{
                    alertify.error(result.response);
                  }
              },
              error: function (error) {
                  document.querySelector('.loader').style.display="none";
                  alertify.error("L'opération n'a pas abouti!");
                  console.log("ERROR :",error);
                  console.log("ERROR :",error.responseText);
              }
            });
          }else{
             document.getElementById('email').value = "";
          }
          
          
    });
    $('#check-token').on('click',function(e){
        //alertify.alert('ZELA');
        var email = document.getElementById('email').value;
        var token = document.getElementById('token').value;
        console.log(email,token);
        
        console.log('TOKEN LENGTH', token.length);
        if(token.length == 4){
          document.querySelector('.loader').style.display="block";
            $.ajax({
              type: 'POST',
              url: 'dist/userTrait.php',
              data: 'email=' + email+'&token='+token,
              dataType: 'json',
              success : function(result){
                  console.log('TOKEN VALIDATING RESULT : ', result);
                  console.log('RESULT RESPONSE : ', result.response);
                  document.querySelector('.loader').style.display="none";
                  if(result.number == 1){
                    document.querySelector('#box-msg').textContent = "Nouveau mot de passe";
                    document.querySelector('#newpass-field').style.display="block";
                    document.querySelector('#newpass-field2').style.display="block";
                    document.querySelector('#token-field').style.display="none";
                    document.querySelector('#checking').style.display="none";
                    document.querySelector('#saving').style.display="block";
                    
                    //document.querySelector('#send-token').text = "Enregistrer";
                    alertify.log(result.response);
                  }else{
                    alertify.error(result.response);
                  }
              },
              error: function (error) {
                  document.querySelector('.loader').style.display="none";
                  alertify.error("L'opération n'a pas abouti!");
                  console.log("ERROR :",error.responseText);                  
              }
            });
        }else{
          alertify.error("Code incorrect");
        }
    });
    $('#save-pass').on('click',function(e){
      var newpass = document.getElementById('newpass').value;
      var newpass2 = document.getElementById('newpass2').value;
      console.log('Pass:',newpass);
      console.log('Pass2:',newpass2);
      if(newpass.trim() != "" && newpass2.trim() != ""){
        if(newpass == newpass2){
          $.ajax({
              type: 'POST',
              url: 'dist/userTrait.php',
              data: 'newpass=' + newpass +'&set',
              dataType: 'json',
              success : function(result){
                  console.log('NEW PASS RESULT : ', result);
                  //console.log('RESULT RESPONSE : ', result.response);
                  //alert('RESULT RESPONSE : ', result.response);
                  document.querySelector('.loader').style.display="none";
                  if(result.number == 1){
                    document.getElementById('login-url').click();
                    
                    //alertify.log(result.response);
                  }else{
                    alertify.error(result.response);
                  }
              },
              error: function (error) {
                  console.log("ERROR :",error);
                  document.querySelector('.loader').style.display="none";
                  //document.getElementById('login-url').click();
                  alert(error);
                  alertify.error("L'opération n'a pas abouti!");
                  console.log("ERROR :",error.responseText);
                  
              }
            });
        }else{
          alertify.error("Attention, veuillez taper la même chose dans les deux zones de saisie!");
        }
      }
      
    });
});
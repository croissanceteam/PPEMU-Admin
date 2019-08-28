$(document).ready(function (e){
    
    //TODO:: Envoi du FOrmulaire + Reponse SAVE PHOTOGRAPHE
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
    
});

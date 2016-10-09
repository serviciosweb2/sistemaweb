$(".fancybox-wrap").ready(function() {
    
    
    $(".btn-success").click(function(){
        
                    $.ajax({
            url: BASE_URL + 'matriculas/bajaMatriculasCtaCte',
            data: $("#frm-ctacte-baja").serialize(),
            cache: false,
            type: 'POST',
            dataType: 'json',
            success: function(respuesta) {
                
                console.log(respuesta);
                
                
            }} );
        
        
        
        
        
    });
        
        
        
        
    
    
    
    
    
});
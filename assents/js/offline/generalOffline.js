
function iniciarSinConexion(){
    $('#moduloSeccionesOffline').removeClass('hide');
    $('#moduloMsjSinConexion').addClass('hide');
    
}

function intentarConectar(){
    
    window.location.href= BASE_URL+'dashboard';
    
}
$(document).ready(function(){
    
    console.log('CONTROL GENERAL CARGADO')
})



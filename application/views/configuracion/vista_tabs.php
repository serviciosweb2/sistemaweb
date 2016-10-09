
<script>

$(document).ready(function(){
   
    $('#myTab a').click(function () {
        
       var direccionUrl=$(this).attr('href');
       
        window.location.href=direccionUrl;
        
        
        return false;
    });  
    
    
});
  


</script>

<?php 
//GENERA EL MENU
$ci=&get_instance(); 

$session=$ci->session->all_userdata();

    $iconos=array(

        'configplan' => 'green icon-print bigger-110',
        'vistaFiliales' => 'green icon-print bigger-110',
        'vistaUsuarios' => 'green icon-group bigger-110',
        'configFacturacion'=> 'green icon-administrativo bigger-110',
        'configPlanPago'=> 'green icon-credit-card bigger-110',
        'config_academico'=> 'green icon-book bigger-110',
        'config_compras'=> 'green icon-ok bigger-110',
        'config_igacloud'=> 'green icon-cog bigger-110'
    );

session_menu_tab($session,'tab',$seccion='','ajustes','configuracion',$tab_activo,$iconos);

?>
<!--CARGA CONTROLADOR DE REDIRECCION-->

$('#contenedorMenu').ready(function(){
  
    // EVENTO QUE CONTROLA EL DESPLIEGUE DEL MENU PRINCIPAL QUE FLOTA A LA IZQUIERDA
    $('.drop').on('click','a',function(){
      var submenu=$(this).attr('menu');
      var flecha=$(this).attr('flecha');
      $(this).find('i').toggle();
      $('#'+submenu).toggle('fast');
      
     
//      if(flecha==='abajo'){
//          $(this).parent().html('<a href="#" flecha="arriba" menu="'+submenu+'"><i class="icon-chevron-up icon-white"></i></a>');
//      }else{
//           $(this).parent().html('<a href="#" flecha="abajo" menu="'+submenu+'"><i class="icon-chevron-down icon-white"></i></a>');
//      }
      return false;
  });
});


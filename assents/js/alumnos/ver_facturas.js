 var langFRM='';    
 var oTableFacturas = '';

 function crearBody(){
    var arrFacturas = JSON.parse(facturasAlumno);
    $(arrFacturas).each(function(k,valor){
        oTableFacturas.row.add([valor.razon_social, valor.punto_venta, valor.factura, valor.nrofact, valor.total]).draw();
    });
    $.fancybox.update();
}
$('.fancybox-wrap').ready(function(){
    var clavesFRM=Array("validacion_ok");
    $.ajax({
        url:BASE_URL+'entorno/getLang',
        data:"claves=" + JSON.stringify(clavesFRM),
        type:"POST",
        dataType:"JSON",
        async:false,
        cache:false,
        success:function(respuesta){
            langFRM=respuesta;
            initFRM();
        }
    });

    function initFRM(){
        oTableFacturas =$('#ver_facturas').DataTable({
            "aaSorting": [[ 3,"desc"]]
       } );
       $('select[name="ver_facturas_length"]').on('change',function(){
           $.fancybox.update();
       });
       crearBody();
    }
});
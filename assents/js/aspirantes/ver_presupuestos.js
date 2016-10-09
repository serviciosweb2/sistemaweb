function imprimirPresupuesto(codigo){
    printers_jobs(1,codigo);
}

$('.fancybox-wrap').ready(function(){    
    var clavesFRM=Array("validacion_ok","formatearcuotas_cuota","orden","valor","presupuesto_sin_valores","upps");    
    var langFRM='';    
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
    
    function tablaDetallePresupuesto(rows){
        var tabla='<table class="table">';       
        tabla+='<thead><th>'+langFRM.formatearcuotas_cuota+'</th><th>'+langFRM.orden+'</th><th>'+langFRM.valor+'</th></thead>';
        $(rows).each(function(k,row){            
            tabla+='<tr><td>'+row.nro_cuota+'</td><td>'+row.orden+'</td><td>'+row.valor+'</td></tr>';            
        });
        
        tabla+='<tbody></table>';        
        $('#modalDetalle .table-responsive').html(tabla);        
        $('#modalDetalle').modal();        
    }
    
    function initFRM(){       
        $(document).on('keydown',function(){           
            $('#modalDetalle').modal('hide');
        });
       
        $('.fancybox-wrap select').chosen({
            width:'100%',
            allow_single_deselect: true
        });
        
        $('.fancybox-wrap').on('click','[data-detalle]',function(){            
            $.ajax({
                url: BASE_URL+"aspirantes/getDetallePresupuestoPlan",
                type: "POST",
                data: 'codigos='+$(this).attr('data-detalle'),
                dataType:"JSON",
                cache:false,
                async:false,
                success:function(respuesta){                        
                    if (respuesta.length != 0){
                        tablaDetallePresupuesto(respuesta);
                    } else {
                        $.gritter.add({
                            title: langFRM.upps,
                            text: langFRM.presupuesto_sin_valores,
                            sticky: false,
                            time: '3000',
                            class_name:'gritter-error'
                        });
                    }
                }
            });
            
            return false;
        });    
    }    
});
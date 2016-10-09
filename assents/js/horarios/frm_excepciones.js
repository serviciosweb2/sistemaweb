$(".fancybox-wrap").ready(function()
{
    $(".chosen-select").chosen({
         width: "100%"
});
  
    oTableExcep=$('#tablaHorariosCambiar').dataTable({
        "bProcessing": true,
        "bServerSide": true,
         "aLengthMenu": [[5, 10, 25], [5,10, 25]],
         "iDisplayLength":5,
          "sDom": 'T<"clear">lrtip',
        "sAjaxSource": BASE_URL + 'horarios/listarHorariosCambiar', 
        "aoColumns": [
            null,{"bSortable": false},{"bSortable": false},{"bSortable": false}
        ],
//        "aoColumnDefs"  : [{
//    aTargets: [0],   
//    fnRender: function (o, v) {  
//      
//        return '<div class="radio"><label><input name="codigo_horario" value="' +  v + '" type="radio" class="ace"><span class="lbl"></span></label></div>';
//    }
//}],
        "sServerMethod": "POST",
           "fnServerData": function(sSource, aoData, fnCallback) {
                aoData.push( { "name": "codigo_horario", "value": $("#codigo_horario").val()  } );
            $.ajax({
                "dataType": 'json',
                "type": "POST",
                "url": sSource,
                "data": aoData,
                "async": false,
                "success": fnCallback
            });
    
        },
        "fnRowCallback": function(nRow, aData, iDisplayIndex, iDisplayIndexFull) {     
               
                $('td',nRow).eq(0).html('<div class="radio"><label><input name="codigo_horario" value="' + aData[0] + '" type="radio" class="ace"><span class="lbl"></span></label></div>');
            }
 
    });
    
    $("#enviar-excepciones").click(function()
    {
        var dataPOST=oTableExcep.$('input[type="radio"]').serialize()+'&'+$('select[name="cod_matricula_horario[]"]').serialize();

        $.ajax({
            url:BASE_URL +'horarios/guardarExcepcion' ,
            type:'POST',
            data:dataPOST,
            dataType: 'JSON',
            success:function(respuesta){

                switch (respuesta.codigo){
                    case 1:

                                $.gritter.add({
                                title: langFRM.BIEN,
                                text: langFRM.EXCEPCION_GUARDADA_CORRECTAMENTE ,
                                sticky: false,
                                time: '3000',
                                class_name: 'gritter-success'
                            });
                        $.fancybox.close();

                        break;


                        default :

                            $.gritter.add({

                                text: respuesta.respuesta ,
                                sticky: false,
                                time: '3000',
                                class_name: 'gritter-error'

                            });





                }

            }

        });


    });
    
});

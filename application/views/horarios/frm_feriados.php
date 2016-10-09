<script>
    var cod_feriado = '-1';
    function mostarHoraFeriado(element){
        if ($(element).is(':checked')) {
            $(".hora-feriado").addClass("hide");
        } else {
            $(".hora-feriado").removeClass("hide");
        }            
    }
    
    function llenarModal(respuesta){
        var checked = respuesta.repite == 1 ? 'checked' : '';
        var fecha = respuesta.dia+"/"+respuesta.mes+"/"+respuesta.anio;
        $('input[name="nombre"]').val(respuesta.nombre);
        $('input[name="repetir"]').prop('checked',checked);
        $('input[name="repetir"]').prop('readonly',true);
        $('div[name="fecha"').datepicker( "setDate", fecha);
        $('div[name="fecha"').datepicker( "option", "disabled", true );
        if(respuesta.hora_desde == null && respuesta.hora_hasta == null){           
            $('input[name="dia-completo"]').prop('checked',true);
            $('input[name="dia-completo"]').prop('readonly',true);
            $("#nuevo-feriado").ready(function(){
                
            });            
        } else {           
            $('input[name="dia-completo"]').prop('checked',false);
            $('input[id="hora-desde-feriado"]').val(respuesta.hora_desde);
            $('input[id="hora-hasta-feriado"]').val(respuesta.hora_hasta);
            $('input[name="repetir"]').prop('readonly',false);
            $('input[name="dia-completo"]').prop('readonly',false);
            $("#nuevo-feriado").ready(function(){
            });
       }       
    }
    
    function modificarFeriado(codigo){
        cod_feriado = codigo;
        $.ajax({
            url: BASE_URL + 'horarios/modificar_feriado',
            data: 'codigo='+codigo,
            type: 'POST',
            cache: false,
            success: function(respuesta) {
                var feriado = JSON.parse(respuesta);
                llenarModal(feriado);
                $("#nuevo-feriado").modal("show");
            }
        });
        return false;
    }
   
    function refrescarFancy(){
        cod_feriado = -1;        
        $('input[name="nombre"]').val("");
        $('input[name="repetir"]').prop('checked',true);
        $('div[name="fecha"').datepicker("option", "disabled",false);
        $('div[name="fecha"').datepicker("setDate", new Date());     
        $('#nuevo-feriado').find('input[type="checkbox"]').prop('readonly',false);            
    }
   
    function cambiarEstado(element){
        if(!$(element).is(":checked")){
            var cod_feriado = $(element).val();
            $.ajax({
                url: BASE_URL + 'horarios/cambiarEstadoFeriado',
                type:'POST',
                data:'cod_feriado='+cod_feriado,
                dataType: 'JSON',
                success:function(respuesta){                       
                    if(respuesta.codigo == 1){
                        $(element).closest("tr").remove();
                        $.gritter.add({
                            title: 'Upps!',
                            text: "Borrado Correctamente",
                            sticky: false,
                            time: '3000',
                            class_name:'gritter-success'
                        });
                    }                       
                }
            });
        }
    }
    
    $(".fancybox-wrap").ready(function() {        
        $('#nuevo-feriado').on('click','input[type="checkbox"]',function(){
            var elemento= $(this);            
            var id = $(this).attr('name');            
            if(cod_feriado==-1){
                if(id == 'dia-completo'){
                    mostarHoraFeriado(elemento);
                }                
            } else {
                return false;            
            }                 
        });

        $('#nuevo-feriado').on('hidden.bs.modal', function (e) {
            refrescarFancy();
        });

        $("#btn-alta").click(function() {
            refrescarFancy();
            $("#nuevo-feriado").modal("show");
        });

        var calendario = $('div[name="fecha"]').datepicker({
            onSelect: function(date) {                
               $("#input-fecha").val(date);
                $(this).change();
            },
            onBeforeShow: function(){ }
        });

        $("#btn-new-feriado").click(function() {
            var datos = $("#frm-feriado").serializeArray();            
            $.ajax({
                url: BASE_URL + 'horarios/guardarFeriado',
                data: $("#frm-feriado").serialize()+'&cod_feriado='+cod_feriado,
                type: 'POST',
                dataType: 'json',
                cache: false,
                success: function(respuesta) {
                    switch(respuesta.codigo){
                        case 1:
                            $("#nuevo-feriado").modal("hide");
                                $.gritter.add({
                                title: langFRM.BIEN,
                                text: langFRM.FERIADO_GUARDADO_CORRECTAMENTE ,
                                sticky: false,
                                time: '3000',
                                class_name: 'gritter-success'
                            });

                            $.ajax({
                                url: BASE_URL + 'horarios/frm_feriados',
                                data: 'codigo=-1',
                                type: 'POST',
                                cache: false,
                                success: function(respuesta) {
                                    $.fancybox.open(respuesta, {
                                        scrolling: 'auto',
                                        width: '50%',
                                        height: 'auto',
                                        minHeight: '300',
                                        maxWidth: '600',
                                        autoSize: false,
                                        autoResize: false,
                                        openEffect: 'none',
                                        closeEffect: 'none',
                                        padding: 1,
                                        helpers: {
                                            overlay: null
                                        },
                                         beforeClose: function() {
                                            location.reload();
                                        }
                                    });
                                }
                            });                            
                            break;

                        default:                        
                           $.gritter.add({
                                title: langFRM.ERROR,
                                text: respuesta.respuesta ,
                                sticky: false,
                                time: '3000',
                                class_name: 'gritter-error'
                            });
                    }
                }
            });
        });        

        $('#hora-hasta-feriado').timepicker({
            minuteStep: 1,
            secondStep: 5,
            showInputs: false,
            template: false,
            modalBackdrop: true,
            showSeconds: true,
            showMeridian: false
        });

        $('#hora-desde-feriado').timepicker({
            minuteStep: 1,
            secondStep: 5,
            showInputs: false,
            template: false,
            modalBackdrop: true,
            showSeconds: true,
            showMeridian: false
        });

        oTableFacturas =$('#tabla_feriados').DataTable({
            "lengthMenu" : [5,10,25,50,100],
            "aoColumns" : [
                null,
                { "sType": "uk_date"},
                null,
                null,
                null,
                null
            ]
        });

        $('input[aria-controls="tabla_feriados"]').on('keyup',function(){
            $.fancybox.update();
        });
    });
    
    jQuery.fn.dataTableExt.oSort['uk_date-asc']  = function(a,b) {     
        var ukDatea = a.split('/');
        var ukDateb = b.split('/');               
        if (isNaN(parseInt(ukDatea[0]))) {
            return -1;
        }     
        if (isNaN(parseInt(ukDateb[0]))) {
            return 1;
        }     
        var x = (ukDatea[2] + ukDatea[1] + ukDatea[0]) * 1;
        var y = (ukDateb[2] + ukDateb[1] + ukDateb[0]) * 1;     
        return ((x < y) ? -1 : ((x > y) ?  1 : 0));
    };

    jQuery.fn.dataTableExt.oSort['uk_date-desc'] = function(a,b) {
        var ukDatea = a.split('/');
        var ukDateb = b.split('/');             
        if (isNaN(parseInt(ukDatea[0]))) {
            return 1;
        }     
        if (isNaN(parseInt(ukDateb[0]))) {
            return -1;
        }     
        var x = (ukDatea[2] + ukDatea[1] + ukDatea[0]) * 1;
        var y = (ukDateb[2] + ukDateb[1] + ukDateb[0]) * 1;     
        return ((x < y) ? 1 : ((x > y) ?  -1 : 0));
    };
</script>

<div id="nuevo-feriado" class="modal     " tabindex="-1" data-focus-on="input:first" >
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h3><?php echo lang('nuevo_feriado'); ?></h3>
    </div>
    <div class="modal-body" >
        <form id="frm-feriado">
            <input  type="hidden" id="input-fecha" name="fecha" value="<?php echo $fecha_actual; ?>"/>
            <div class="row">
                <div class="form-group col-md-12">
                    <label><?php echo lang('nombre_feriado'); ?></label>
                    <input class="form-control" name="nombre" />
                </div>
            </div>
            <div class="row">
                <div class="form-group col-md-6">
                    <div name="fecha"></div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="row">
                            <div class=" form-group col-md-12 ">
                                <div class="checkbox">
                                    <label>
                                        <input name="repetir" class="ace ace-checkbox-2" id="repetir" type="checkbox">
                                        <span class="lbl"> <?php echo lang('repetir_anualmente'); ?></span>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class=" form-group col-md-12 ">
                                <div class="checkbox">
                                    <label>
                                        <input name="dia-completo" class="ace ace-checkbox-2" id="check-box-completo" type="checkbox" checked="" >
                                        <span class="lbl"> <?php echo lang('feriado_completo'); ?></span>
                                    </label>
                                </div>
                            </div>
                        </div>                       
                        <div class="row hora-feriado hide ">
                            <div class=" col-md-12 ">
                                <div class="row">
                                    <div class=" form-group col-md-12">
                                        <div class="input-group bootstrap-timepicker"><div class="bootstrap-timepicker-widget dropdown-menu"><table><tbody><tr><td><a href="#" data-action="incrementHour"><i class="icon-chevron-up"></i></a></td><td class="separator">&nbsp;</td><td><a href="#" data-action="incrementMinute"><i class="icon-chevron-up"></i></a></td><td class="separator">&nbsp;</td><td><a href="#" data-action="incrementSecond"><i class="icon-chevron-up"></i></a></td></tr><tr><td><input type="text" name="hour" class="bootstrap-timepicker-hour" maxlength="2"></td> <td class="separator">:</td><td><input type="text" name="minute" class="bootstrap-timepicker-minute" maxlength="2"></td> <td class="separator">:</td><td><input type="text" name="second" class="bootstrap-timepicker-second" maxlength="2"></td></tr><tr><td><a href="#" data-action="decrementHour"><i class="icon-chevron-down"></i></a></td><td class="separator"></td><td><a href="#" data-action="decrementMinute"><i class="icon-chevron-down"></i></a></td><td class="separator">&nbsp;</td><td><a href="#" data-action="decrementSecond"><i class="icon-chevron-down"></i></a></td></tr></tbody></table></div>
                                            <input id="hora-desde-feriado" name="hora-desde" type="text" class="form-control">
                                            <span class="input-group-addon">
                                                <i class="icon-time bigger-110"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group col-md-12">
                                        <div class="input-group bootstrap-timepicker"><div class="bootstrap-timepicker-widget dropdown-menu"><table><tbody><tr><td><a href="#" data-action="incrementHour"><i class="icon-chevron-up"></i></a></td><td class="separator">&nbsp;</td><td><a href="#" data-action="incrementMinute"><i class="icon-chevron-up"></i></a></td><td class="separator">&nbsp;</td><td><a href="#" data-action="incrementSecond"><i class="icon-chevron-up"></i></a></td></tr><tr><td><input type="text" name="hour" class="bootstrap-timepicker-hour" maxlength="2"></td> <td class="separator">:</td><td><input type="text" name="minute" class="bootstrap-timepicker-minute" maxlength="2"></td> <td class="separator">:</td><td><input type="text" name="second" class="bootstrap-timepicker-second" maxlength="2"></td></tr><tr><td><a href="#" data-action="decrementHour"><i class="icon-chevron-down"></i></a></td><td class="separator"></td><td><a href="#" data-action="decrementMinute"><i class="icon-chevron-down"></i></a></td><td class="separator">&nbsp;</td><td><a href="#" data-action="decrementSecond"><i class="icon-chevron-down"></i></a></td></tr></tbody></table></div>
                                            <input type="text"  name="hora-hasta" class="form-control ui-timepicker-input" id="hora-hasta-feriado" >
                                            <span class="input-group-addon">
                                                <i class="icon-time bigger-110"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-success"  id="btn-new-feriado"  ><?php echo lang('guardar'); ?></button>
    </div>
</div>
<div class="modal-content" >
    <div class="modal-header">
        <h3 class="blue bigger"><?php echo lang('feriados'); ?> </h3>
    </div>
    <div class="modal-body overflow-visible" >
        <div><div class="btn btn-info" id="btn-alta"><?php echo lang('nuevo'); ?></div></div>
        <table class="table table-striped table-bordered dataTable" id="tabla_feriados">
            <thead>
            <th><?php echo lang('nombre'); ?></th>
            <th><?php echo lang('fecha'); ?> </th>
            <th><?php echo lang('horarios'); ?> </th>
            <th><?php echo lang('repite'); ?></th>
            <th><?php echo lang('desactivar'); ?></th>
            <th></th>
            </thead>
        <?php foreach ($feriados as $feriado) { ?>
            <tr>
                <td><?php echo $feriado["nombre"] ?></td>
                <td><?php echo $feriado["fecha"] ?></td>
                <td><?php echo $feriado["horario"] ?></td>
                <td><?php echo $feriado["repite"] === "1" ? lang("SI") : lang("NO"); ?></td>
                <td><label>
                        <input name="estado" value="<?php echo $feriado['codigo']?>" class="ace ace-switch ace-switch-6" type="checkbox" onchange="cambiarEstado(this);"  <?php echo $feriado['baja'] == 0 ? "checked" : ''?>>
                        <span class="lbl"></span>
                    </label>
                </td>
                <td>
                    <label>
                        <span  class="label label-success arrowed" onclick="modificarFeriado(<?php echo $feriado['codigo']?>)">
                            <?php echo lang('modificar_feriado');?>
                        </span>
                    </label>
               </td>
            </tr>
        <?php } ?>
        </table>
    </div>
</div>
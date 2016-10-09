var frmNuevoMovimiento = langFrm;
$($("[name=frm_nuevo_movimiento]")).ready(function(){
    init();
});

function init(){
    var mindia = $("[name=min_date_dia]").val();
    var minmes = $("[name=min_date_mes]").val();
    var minanio = $("[name=min_date_anio]").val();
    var minhis = $("[name=min_date_his]").val();
    var maxdia = $("[name=max_date_dia]").val();
    var maxmes = $("[name=max_date_mes]").val();
    var maxanio = $("[name=max_date_anio]").val();
    var maxhis = $("[name=max_date_his]").val();
    $("[name=nuevo_movimiento_fecha]").datetimepicker({
        maxDate: 0,
        minDate:  minanio + "-" + minmes + "-" + mindia,
        formatDate: 'Y-m-d',
        format: "d/m/Y H:i:s",
        step: 10,
        onChangeDateTime:function(dp, input){
            var fechasel1 = $("[name=nuevo_movimiento_fecha]").val();
            var d = fechasel1.substring(0, 2);
            var m = fechasel1.substring(3, 5);
            var a = fechasel1.substring(6, 10);
            var his = fechasel1.substring(11, 19);
            var fechasel = a + "-" + m + "-" + d + " " + his;
            var fechamin = minanio + "-" + minmes + "-" + mindia + " " + minhis;
            var fechamax = maxanio + "-" + maxmes + "-" + maxdia + " " + maxhis;
            if (fechasel > fechamax){
                input.val(maxdia + "/" + maxmes + "/" + maxanio + " " + maxhis);
            } else if (fechasel < fechamin){
                input.val(mindia + "/" + minmes + "/" + minanio + " " + minhis);
            }
        }
    });
    $(".select-chosen").chosen();
}

function guardarNuevoMovimiento() {
    var codigo_caja = $("[name=nuevo_movimiento_codigo_caja]").val();
    var fecha = $("[name=nuevo_movimiento_fecha]").val();
    var hora = $("[name=nuevo_movimiento_hora]").val();
    var codigo_medio = $("[name=nuevo_movimiento_medio_pago]").val();
    var observacion = $.trim($("[name=nuevo_movimiento_descripcion]").val());
    var importe = $.trim($("[name=nuevo_movimiento_importe]").val());
    var importe_comparar = importe.replace(/,/g, '.');
    var tipo_movimiento = $("[name=nuevo_movimiento_metodo]").val();
    var subrubroNombre = $("[name=subrubro] option:selected").text();
    ;
    var subrubro = $("[name=subrubro]").val();
    var tipo = $("[name=nuevo_movimiento_metodo]").val();

    if (tipo === 'salida') {
        if (subrubro === '0') {
            var msj = frmNuevoMovimiento.template_error_titulo;
            msj += " " + frmNuevoMovimiento.subrubro;
            msj += ". ";
            msj += frmNuevoMovimiento.template_error_vacio_descripcion;
            gritter(msj, false);
        }
    }

    var mensaje = '';;
    if (importe == '' || isNaN(parseFloat(importe))){
        mensaje += frmNuevoMovimiento.debe_ingresar_un_importe_valido;
    } else if (parseFloat(importe_comparar) == 0){
        mensaje += frmNuevoMovimiento.el_importe_debe_ser_mayor_a_cero + "<br>";
    }
    if (observacion == ''){
        mensaje += frmNuevoMovimiento.debe_especificar_una_descripcion_para_el_nuevo_movimiento_de_caja + "<br>";        
    }
    console.log(mensaje);
    if(tipo === 'salida'){
    if (importe == '' || observacion == '' || subrubro == '0'){
        gritter(mensaje, false);
    }else{
        $.ajax({
            url: BASE_URL + 'caja/guardarNuevoMovimiento',
            type: 'POST',
            dataType: 'json',
            data: {
                codigo_caja: codigo_caja,
                fecha: fecha,
                hora: hora,
                codigo_medio: codigo_medio,
                observacion: observacion,
                importe: importe,
                tipo_movimiento: tipo_movimiento,
                subrubro: subrubro,
                subrubroNombre: subrubroNombre
            },
            success: function(_json){
                if (_json.codigo == 0){
                    gritter(_json.respuesta, false);
                } else {
                    gritter(frmNuevoMovimiento.movimiento_guardado_correctamente + "<br>", true);
                    $.fancybox.close();
                }
            }
        });
    }
    } else {
        $.ajax({
            url: BASE_URL + 'caja/guardarNuevoMovimiento',
            type: 'POST',
            dataType: 'json',
            data: {
                codigo_caja: codigo_caja,
                fecha: fecha,
                hora: hora,
                codigo_medio: codigo_medio,
                observacion: observacion,
                importe: importe,
                tipo_movimiento: tipo_movimiento,
                subrubro: subrubro,
                subrubroNombre: subrubroNombre
            },
            success: function(_json){
                if (_json.codigo == 0){
                    gritter(_json.respuesta, false);
                } else {
                    gritter(frmNuevoMovimiento.movimiento_guardado_correctamente + "<br>", true);
                    $.fancybox.close();
                }
            }
        });
    }
}

$("[name=nuevo_movimiento_metodo]").change(function(){
    if($("[name=nuevo_movimiento_metodo]").val() === 'salida'){
        $("#row_rubro").show();
    } else {
        $("#row_rubro").hide();
        $("#row_rubro").val('');
    }
});

$("[name=rubro]").change(function(){
    var rubro = this.value;
    $.ajax({
        url: BASE_URL + 'caja/getSubRubros',
        type: 'POST',
        dataType: 'json',
        data: {
            rubro: rubro
        },
        success: function(_json){
            $("[name=subrubro]").empty();
            if (_json.length != 1){
                var option = $('<option/>');
                option.attr({ 'value': '0' }).text(frmNuevoMovimiento.seleccione_opcion);
                $("[name=subrubro]").append(option);
            }
            
            $.each(_json,function(){
                option = $('<option/>');
                option.attr({ 'value': this.codigo }).text(this.nombre);
                $("[name=subrubro]").append(option);
            });
            $("[name=subrubro]").trigger("chosen:updated");
        }
    });
});
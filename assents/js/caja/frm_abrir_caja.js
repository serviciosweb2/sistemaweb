var oTableCtacte = '';
var frmAbrirCaja = langFrm;
var claves = Array(
            "caja_abierta_correctamente"
        );


$(".fancybox-wrap").ready(function(){
    init();
});

function init(){
    $(".select-chosen").chosen({
        create_option: true,
        persistent_create_option: true
    });
    oTableCtacte = $("#detalle_saldos_caja").dataTable({
        "bFilter": false,
        "bLengthChange": false
    });
    listarDatosCaja();
}

function listarDatosCaja(){
    var codigo_caja = $("[name=cajas_abrir]").val();
    $.ajax({
        url: BASE_URL + 'caja/get_saldos_de_cierre',
        type: 'post',
        dataType: 'json',
        data: {
            codigo_caja: codigo_caja
        },
        success: function(_json){            
            oTableCtacte.fnClearTable();
            $(_json).each(function(){
                oTableCtacte.fnAddData([
                    this.medio,
                    this.saldo_concepto
                ]);
            });
        }
    });
}

function abrirCaja(){
    var ejecutar_script = $("[name=ejecutar_script]").val();
    var codigo_caja = $("[name=cajas_abrir]").val();
    $.ajax({
        url: BASE_URL + 'caja/abrirCaja',
        type: 'POST',
        dataType: 'json',
        data: {
            codigo: codigo_caja
        },
        success: function(_json){
            if (_json.codigo == 0){
                gritter(_json.msgerror);
            } else {
                gritter(frmAbrirCaja.caja_abierta_correctamente, true);               
                if (ejecutar_script){ // si el frm_abrir_caja es llamado de otro lugar (ejemplo nuevo cobro)
                    eval(ejecutar_script);
                } else {
                    setSelectCajasAbiertas(_json.custom);
                    $.fancybox.close();
                    cambiarVista_Abierta_Cerrada(codigo_caja,'abierta');
                }
            }
        }
    });
    
}
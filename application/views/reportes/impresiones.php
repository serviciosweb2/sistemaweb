<!DOCTYPE html>
<head>

<meta http-equiv="no-cache">
<meta http-equiv="Expires" content="-1">
<meta http-equiv="Cache-Control" content="no-cache">

    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <meta charset="UTF-8"/>
    <script src="<?php echo base_url('assents/js/librerias/jquery/jquery-2.1.0.min.js')?>"></script>
    <script src="<?php echo base_url("assents/js/ajax_controler.js")?>"></script>
    <script src="<?php echo base_url('assents/js/librerias/bootstrap/bootstrap.min.js')?>"></script>

    <script src="<?php echo base_url('assents/theme/assets/js/jquery-ui.min.js')?>"></script>

    <script src="<?php echo base_url("assents/theme/assets/js/chosen.jquery.min.js")?>"></script>
    
    
    
    <script src="<?php echo base_url('assents/js/librerias/bootstrap-modal/bootstrap-modalmanager.js') ?>"></script>


    <style>
        #notifySINCRO {
            position: fixed;
            bottom: 0;
        }

        #notifySINCRO {
            background: #0070FF;
            line-height: 2;
            text-align: center;
            color: white;
            font-size: 10px;
            font-family: sans-serif;
            font-weight: bold;
            text-shadow: 0 1px 0 #84BAFF;
            box-shadow: 0 0 15px #00214B
        }

        .footer {
            padding-top: 75px;
            height: 0;
            width: 0;
        }

        .sidebar~.footer .footer-inner {
            left: 190px;
        }

        .footer .footer-inner {
            text-align: center;
            position: absolute;
            z-index: auto;
            left: 0;
            right: 0;
            bottom: 0;
        }

        .footer .footer-inner .footer-content {
            position: absolute;
            left: 12px;
            right: 12px;
            bottom: 4px;
            padding: 8px;
            line-height: 36px;
            border-top: 3px double #E5E5E5;
        }

        .sidebar.menu-min~.footer .footer-inner {
            left: 43px;
        }

        .sidebar~.footer .footer-inner {
            left: 190px;
        }

        #detallesDeErrorAjax.modal.fade.in {
            top: 20% !important;
        }
    </style>
    <link rel="stylesheet" type="text/css" href="<?php echo base_url("/assents/css/vistaPanelGeneral.css")?>">
    <link rel="stylesheet" type="text/css" href="<?php echo base_url("assents/css/fancy/jquery.fancybox.css")?>">
    <link rel="stylesheet" href="<?php echo base_url('assents/theme/assets/css/jquery.gritter.css')?>"/>

    <!---->
    <!-- basic styles -->
    <link rel="stylesheet" href="<?php echo base_url("assents/theme/assets/css/jquery-ui-1.10.3.full.min.css")?>"/>
    <!--<link href="<?php echo base_url("assents/theme/assets/css/bootstrap.min.css")?>" rel="stylesheet"/>-->
    <link href="<?php echo base_url("assents/theme/assets/css/uncompressed/bootstrap.css")?>" rel="stylesheet"/>
    <link rel="stylesheet" href="<?php echo base_url("assents/theme/assets/css/font-awesome.min.css")?>"/>

    <!-- page specific plugin styles -->
    <!-- fonts -->
    <link rel="stylesheet" href="<?php echo base_url("assents/theme/assets/css/ace-fonts.css")?>"/>
    <!-- ace styles -->
    <link rel="stylesheet" href="<?php echo base_url("assents/theme/assets/css/uncompressed/ace.css")?>"/>
    <link rel="stylesheet" href="<?php echo base_url("assents/theme/assets/css/ace-rtl.min.css")?>"/>
    <link rel="stylesheet" href="<?php echo base_url("assents/theme/assets/css/ace-skins.min.css")?>"/>
    <link rel="stylesheet" href="<?php echo base_url("assents/theme/assets/css/ace.min.css")?>"/>
    <link rel="stylesheet" href="<?php echo base_url('assents/css/bootstrap-modal/bootstrap-modal.css');?>"/>
    <link rel="stylesheet" href="<?php echo base_url('assents/css/bootstrap-modal/bootstrap-modal-bs3patch.css');?>"/>
    <!-- inline styles related to this page -->
    <!-- ace settings handler -->

    <script src="<?php echo base_url("assents/theme/assets/js/ace-extra.min.js")?>"></script>
    <script src="<?php echo base_url()?>assents/js/librerias/moment/moment-with-langs.min.js"></script>
    
    <!--[if lte IE 8]>
 <script src="path/to/assets/js/excanvas.min.js"></script>
<![endif]-->
 


<link rel="stylesheet" href="<?php echo base_url('assents/css/bootstrap-modal/bootstrap-modal.css');?>"/>
<link rel="stylesheet" href="<?php echo base_url('assents/css/bootstrap-modal/bootstrap-modal-bs3patch.css');?>"/>

<script src="<?php echo base_url()?>assents/js/librerias/bootstrap-modal/bootstrap-modal.js"></script>
<script src="<?php echo base_url()?>assents/js/librerias/bootstrap-modal/bootstrap-modalmanager.js"></script>

<script src="<?php echo base_url()?>assents/theme/assets/js/flot/jquery.flot.min.js"></script>
<script src="<?php echo base_url()?>assents/theme/assets/js/flot/jquery.flot.pie.min.js"></script>
<script src="<?php echo base_url()?>assents/theme/assets/js/flot/jquery.flot.resize.min.js"></script>

<link rel="stylesheet" href="<?php echo base_url('assents/theme/assets/css/jquery.datetimepicker.css'); ?>"/>
<script src="<?php echo base_url('assents/theme/assets/js/jquery.datetimepicker.js'); ?>"></script>

<link rel="stylesheet" href="<?php echo base_url('assents/theme/assets/css/jquery.datetimepicker.css'); ?>"/>
<script src="<?php echo base_url('assents/theme/assets/js/jquery.datetimepicker.js'); ?>"></script>
 
<style>
    #gritter-notice-wrapper{
        z-index: 90000  !Important;
    }
    
    table .texto {
        /*position: absolute !important;*/
        display: block !important;
    }
    
    table .test {
        position: absolute !important;
        z-index: 30 !important;
        display: none;
    }
    
    .popover{
        max-width: none!important;
    }
    
    .table_filter{
        -moz-border-bottom-colors: none;
        -moz-border-left-colors: none;
        -moz-border-right-colors: none;
        -moz-border-top-colors: none;
        background-color: #ffffff;
        border-bottom: 1px solid #afafb6;
        border-image: none;
        border-radius: 4px;
        border-top: 1px solid #afafb6;
        box-shadow: 0 1px 4px rgba(0, 0, 0, 0.3);
        cursor: default;
        padding: 8px 0;
        position: absolute;
        right: 20px;
        text-align: left;
        top: 18px;
        z-index: 1000;
    }
    
    
</style>

<script>

var BASE_IDIOMA = '<?php echo get_idioma()?>';

$(document).ready(function(){
   
   var fechaDesde = '<?php echo $fechaDesde?>';
   $("#fecha_desde").val(fechaDesde);
   
   var fechaHasta = '<?php echo $fechaHasta?>';
   $("#fecha_hasta").val(fechaHasta);
   
   var periodo = '<?php echo $periodo?>';
   $("#periodo").val(periodo);
   
   var src = '<?echo $gastoEingreso?>';
   $("#gastos_ingresos").find(".flot-base").remove();
   $("#gastos_ingresos").find(".flot-overlay").remove();
   $('#gastos_ingresos').append('<img src="' + src + '" />');
   
   var src = '<?echo $gastos?>';
   $("#gastos").find(".flot-base").remove();
   $("#gastos").find(".flot-overlay").remove();
   $('#gastos').append('<img src="' + src + '" />');
   
   var src = '<?echo $ingresos?>';
   $("#ingresos").find(".flot-base").remove();
   $("#ingresos").find(".flot-overlay").remove();
   $('#ingresos').append('<img src="' + src + '" />');
   
   var src = '<?echo $rentabilidad?>';
   $("#rentabilidad").find(".flot-base").remove();
   $("#rentabilidad").find(".flot-overlay").remove();
   $('#rentabilidad').append('<img src="' + src + '" />');
   
   $('#dataRentabilidad').show();
   $('#dataIngresos').show();
   $('#dataGastos').show();
   
   window.print();
   
});

</script>

<div class="col-md-12">
    <div class="col-md-1"></div>
    <div class="col-md-10">
        <?php echo $html;?>
    </div>
    <div class="col-md-1"></div>
</div>
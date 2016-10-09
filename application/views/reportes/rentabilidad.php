<script>
var idioma = '<? echo get_idioma()?>';

</script>
<!-- CSS links -->
<!-- De xCharts -->
<link rel="stylesheet" href="<?php echo base_url('assents/css/reportes/xcharts.css')?>"/>
<!-- De Datepicker -->
<link rel="stylesheet" href="<?php echo base_url('assents/css/daterangepicker.css')?>" >
<link rel="stylesheet" href="<?php echo base_url('assents/css/jquery.dataTables.css')?>" >






<!-- ver -->
<style ></style>


<div id="responsive" class="modal fade" tabindex="-1" data-width="760" style="display: none;">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h4 class="modal-title"><?php echo lang("editar_movimiento_caja_subrubro")?> </h4>
        <span id="cod_mov"></span>
    </div>
    <div class="modal-body">
        <div class="row">
            <div class="col-md-12">
                <h4><?php echo lang("subrubro")?></h4>
                <select id="mySelect" class="select-chosen" style="width: 208px;">
                    <option value="nada"><?php echo lang('seleccione_opcion') ?></option>
                </select>

            </div>

        </div>
    </div>
    <div class="modal-footer">
        <button type="button" data-dismiss="modal" class="btn btn-default"><?php echo lang('cerrar') ?></button>
        <button type="button" class="btn btn-primary" id="aceptarCambioSub"><?php echo lang('guardar') ?></button>
    </div>
</div>

<div id="graficos_impresion" class="col-md-12 col-xs-12">
    <div class="row">
        <div class="col-md-12 col-xs-12">
            <div class="col-md-9 col-xs-9">
                    <p style="margin-top:29px" hidden id="botonera">
                    <button class="btn btn-sm btn-yellow" id='barras'>

                        Barras
                    </button>

                    <button class="btn btn-sm btn-inverse" id='lineas'>
                        Lineas
                    </button>

                    <button class="btn btn-sm btn-light" id='acum'>
                        Acumulativo
                    </button>

                </p>
                <p style="margin-top:29px" hidden id="botoneraAtras">
                <button class="btn btn-sm btn-danger" hidden id="back">Atras</button>
                </p>

            </div>
            <div class="col-md-3 col-xs-3" style="margin-top:15px;min-height: 75px">
                <div>
                    <span><?php echo lang('filtrar_fechas');?></span>
                    <div id="reportrange" class="pull-right" name = "reportAtt" style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc; width: 100%">
                        <i class="glyphicon glyphicon-calendar fa fa-calendar"></i>&nbsp;
                        <span></span> <b class="caret"></b>
                    </div>



                </div>
            </div>
            <div class="col-md-12 col-xs-12" style="margin-bottom: 15px;">
                <figure style="width: 100%; height: 400px;" id="myChart"></figure>
            </div>
            
            <div class="col-md-12 col-xs-12" style="margin-bottom: 15px;" id="tableDivInicial" hidden>
                <table id="tableRentabilidad" class="cell-border" width="100%" hidden>
                    <thead>
                    <tr>
                        <th>Mes</th>
                        <th>Ingreso</th>
                        <th>Egreso</th>
                        <th>Rentabilidad</th>
                    </tr>
                    </thead>
                </table>
            </div>
            <div class="col-md-12 col-xs-12" style="margin-bottom: 15px;" id="tableDivFechas" hidden>

            <table id="tableRentabilidadFechas" class="display" width="100%" hidden>
                    <thead>
                    <tr>
                        <th>Periodo</th>
                        <th>Ingreso</th>
                        <th>Egreso</th>
                        <th>Rentabilidad</th>
                    </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>


    <div class="row" style="margin-top: 10px; margin-bottom: 25px;" hidden id="etiquetaGasto">
        <p>
            <h1><?php echo lang('gastos')?></h1>
            <span style="font-weight:bold; margin-left: 10px" id="fechaGasto"></span>
        </p>
        <div class="col-xs-6 col-md-4">
            <p>

                <button class="btn btn-sm btn-yellow" id="barrasGas">

                    Barras
                </button>
                <button class="btn btn-sm btn-inverse" id="lineasGas">
                    Lineas
                </button>

            </p>
        </div>
            <figure style="width: 100%; height: 400px;" id="gastosChart"></figure>

    </div>

    <div class="row" style="margin-top: 10px; margin-bottom: 25px;" hidden id="etiquetaIngreso">
        <p>
            <h1>  <?php echo lang('ingresos')?></h1>
        <span style="font-weight:bold; margin-left: 10px" id="fechaIngreso"></span>
        </p>
        <div class="col-xs-6 col-md-4">
            <p>

            <button class="btn btn-sm btn-yellow" id="barrasIng">

                Barras
            </button>
                <button class="btn btn-sm btn-inverse" id="lineasIng">
                    Lineas
                </button>

        </p>
        </div>


        <figure style="width: 100%; height: 400px;" id="ingresosChart"></figure>


    
    </div>

    <div class="row" style="margin-top: 10px;" hidden id="dataTableGasto">

        <p>
        <h1>Egresos Detalle</h1>
        
        </p>


        <table id="tableGasto" class="cell-border" width="100%">
            <thead>
                    <tr>
                        <th>Cod Movimiento Caja</th>
                        <th>Fecha</th>
                        <th>Observacion</th>
                        <th>Gasto</th>
                        <th>Monto</th>
                    </tr>
                </thead>
            </table>
    </div>
    <div class="row" style="margin-top: 10px;" hidden id="dataTableIngreso">

        <p>
        <h1>Ingreso Detalle</h1>

        </p>

        <table id="tableIngreso" class="cell-border" width="100%">
            <thead>
            <tr>
                <th>Código Cobro</th>
                <th>Código Alumno</th>
                <th>Fecha</th>
                <th>Ingreso</th>
                <th>Monto</th>
            </tr>
            </thead>
        </table>
    </div>
</div>


<!-- JavaScript -->
<script src="<?php echo base_url()?>assents/js/reportes/rentabilidad.js"></script>

<!-- De xCharts -->
<script src="<?php echo base_url()?>assents/D3/d3.js"></script>
<script src="<?php echo base_url()?>assents/D3/xcharts.js"></script>

<!-- De Datepicker -->
<script src="<?php echo base_url()?>assents/js/moment.js"></script>
<script src="<?php echo base_url()?>assents/js/daterangepicker.js"></script>

<!-- Para Scrollear en un on click o show -->
<script src="<?php echo base_url()?>assents/js/librerias/scrollto/jquery-scrollto.js"></script>

<!-- Bootstrap-Modal porque no esta en el bootstrap principal -->

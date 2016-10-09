<?php

class Model_reserva_inscripciones extends CI_Model {
     var $codigo = 0;
    var $codigo_filial = 0;

    public function __construct($arg) {
        parent::__construct();
        $this->codigo = isset($arg["codigo"]) ? $arg["codigo"] : 0;
        $this->codigo_filial = $arg["codigo_filial"];
    }
    
    public function listarReservasIncripcionDataTable($arrFiltros){
        $conexion = $this->load->database($this->codigo_filial, true,null,TRUE);
         $arrCondindiciones = array();
        $nombreCurso = 'cursos.nombre_'.get_idioma();
        if ($arrFiltros["sSearch"] != "") {
            $arrCondindiciones = array(
                'inscripcionesweb.reserva_inscripciones.id'=>$arrFiltros["sSearch"],
                'inscripcionesweb.reserva_inscripciones.nombre'=>$arrFiltros["sSearch"],
                'inscripcionesweb.reserva_inscripciones.email'=>$arrFiltros["sSearch"],
                'inscripcionesweb.reserva_inscripciones.fecha' => $arrFiltros["sSearch"],
                'comisiones.nombre'=>$arrFiltros["sSearch"],
                "$nombreCurso"=>$arrFiltros["sSearch"]
            );
        }

        $arrLimit = array();
        if ($arrFiltros["iDisplayStart"] != "" and $arrFiltros["iDisplayLength"] != "") {

            $arrLimit = array(
                "0" => $arrFiltros["iDisplayStart"],
                "1" => $arrFiltros["iDisplayLength"]
            );
        }
        $arrSort = array();

        if ($arrFiltros["SortCol"] != "" and $arrFiltros["sSortDir"] != "") {

            $arrSort = array(
                "0" => $arrFiltros["SortCol"],
                "1" => $arrFiltros["sSortDir"]
            );
        }
        $datos = Vreserva_inscripciones::listarReservarInscripcionesDataTable($conexion, $arrCondindiciones, $arrLimit, $arrSort, false, $this->codigo_filial);
        $contar = Vreserva_inscripciones::listarReservarInscripcionesDataTable($conexion, $arrCondindiciones, null, null, true, $this->codigo_filial);
        
        $retorno = array(
            "sEcho" => $arrFiltros["sEcho"],
            "iTotalRecords" => $contar,
            "iTotalDisplayRecords" => $contar,
            "aaData" => array()
        );

        $rows = array();
        foreach ($datos as $row) {
            $rows[] = array(
                $row['id'],                
                formatearFecha_pais($row['fecha']),
                ucwords(strtolower($row['nombre'])),
                $row['email'],
                $row['telefono'],
                $row['nombre_comision'],
                $row['nombre_curso'],
                $row['estado']
            );
        }
        $retorno['aaData'] = $rows;

        return $retorno;
    }
    
    public function getObjReserva($cod_reserva){
        $conexion = $this->load->database($this->codigo_filial,true,null,true);
        
        $myReservaInscripcion = new Vreserva_inscripciones($conexion, $cod_reserva);
        return $myReservaInscripcion;
    }
    
    public function getDetalleReservaInscripcion($cod_reserva,$cod_plan_pago){
        $conexion = $this->load->database($this->codigo_filial,true,null,true);
        $arrDetalle = Vreserva_inscripciones::getInformacionReservaInscripcion($conexion,$cod_reserva,$cod_plan_pago);
        foreach($arrDetalle as $key=>$detalle){
            $arrDetalle[$key]['valor_nro_cuotas'] = $detalle['nro_cuotas'].' '.'cuotas de '.$detalle['valor_cuota'];
            $arrDetalle[$key]['fechavigencia'] = formatearFecha_pais($detalle['fechavigencia']);
        }
        return $arrDetalle;
    }
}
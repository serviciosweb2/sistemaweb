<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * Model_rematriculaciones
 *
 * ...
 *
 * @package model_rematriculaciones
 * @author manu <manu.pajon@gmail.com>
 * @version 1.0.0
 */
class Model_rematriculaciones extends CI_Model {
    var $codigofilial = 0;
    var $codigo = 0;

    public function __construct($arg) {
        parent::__construct();

        $this->codigofilial = $arg["filial"]["codigo"];
        $this->codigo = isset($arg["codigo"]) ? $arg["codigo"] : 0;
    }


    public function listarRematriculaciones($arrFiltros, $muestraperiodo = 1, $idioma, $separador, $comision, $todos, $fechaDesde, $fechaHasta, $pasarLibre = false) {
        $conexion = $this->load->database($this->codigofilial, true);
        $arrCondindiciones = array();
        $this->load->helper('alumnos');

        if ($arrFiltros["sSearch"] != "") {
            $arrCondindiciones = array(
                "matricula" => $arrFiltros["sSearch"],
                "nombre_alumno" => $arrFiltros["sSearch"],
                "ciclo" => $arrFiltros["sSearch"],
                "codigo_alumno" => $arrFiltros["sSearch"],
                "documento" => $arrFiltros["sSearch"]
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

        $datos = Vmatriculas::listarRematriculacionesDataTable($conexion, $fechaDesde, $fechaHasta, $comision, $separador, $arrCondindiciones, $todos?null:$arrLimit, $arrSort);
        $contar = Vmatriculas::listarRematriculacionesDataTable($conexion, $fechaDesde, $fechaHasta, $comision, $separador, $arrCondindiciones, "", "", true);
        $retorno = array(
            "sEcho" => $arrFiltros["sEcho"],
            "iTotalRecords" => $contar,
            "iTotalDisplayRecords" => $contar,
            "aaData" => array()
        );

        $rows = array();

        foreach ($datos as $row) {
            $estadoCuentaBoletos = Vctacte::getEstadoRematriculacion($conexion, $row['matricula']);
            $row["cuotas_debe"] = 0;
            $row["valor_debe"] = 0;
            $boletosEmitidos = 0;
            $vencimientos = 0;
            foreach($estadoCuentaBoletos as $estado){
                if(($estado['vencida'] == 1) && ($estado['saldo_cobrar'] > 0)){
                    $row["valor_debe"] += $estado['saldo_cobrar'];
                    $row["cuotas_debe"]++;
               }
               if($estado['fechavenc'] >= $fechaDesde && $estado['fechavenc'] <= $fechaHasta && $estado['saldo_cobrar'] > 0){
                    $vencimientos++;
                    if($estado["con_boleto"] == "1"){
                        $boletosEmitidos++;
                    }
               }
            }
            $row["boletos_emitidos"] = ($vencimientos == $boletosEmitidos)?1:0;
            if($vencimientos == 0)
                $row["boletos_emitidos"] = 2;
            $estadoBoletos = array(
                lang("boletos_no_emitidos"),
                lang("boletos_emitidos"),
                lang("sin_vencimientos_pendientes")
            );
            $habilitacion = $this->habilitaRematricular($row["matricula"], $fechaDesde, $fechaHasta);
            if($row["cuotas_debe"] < 3)
                $habilitado = $habilitacion == "nil" || $habilitacion == "Habilitado";
            else
                $habilitado = $habilitacion == "Habilitado";

            $rows[] = array(
                ($row["boletos_emitidos"] == 2)?2:($habilitado?1:-1),
                $row["matricula"],
                $row["nombre_alumno"],
                $row["documento"],
                $row["fecha_matricula"],
                $row["valor_debe"],
                $row["cuotas_debe"],
                $estadoBoletos[$row["boletos_emitidos"]],
                $habilitado?lang('habilitada'):lang('inhabilitada'),
                $row["codigo_alumno"]
            );
        }

        $retorno['aaData'] = $rows;
        return $retorno;
    }

    function habilitaRematricular($matricula, $fechaDesde, $fechaHasta){
        $conexion = $this->load->database($this->codigofilial, true);
        $conexion->select('*');
        $conexion->from('general.habilitaciones_rematriculacion');
        $conexion->where('cod_matricula', $matricula);
        $conexion->where('cod_filial', $this->codigofilial);
        $conexion->where("fecha_desde <= '$fechaDesde'");
        $conexion->where("fecha_hasta >= '$fechaHasta'");
        $conexion->order_by("fecha_hora");
        $query = $conexion->get();
        $habilitaciones = $query->result_array();
        $tipo = "nil";
        if(count($habilitaciones) > 0){
            $tipo = $habilitaciones[count($habilitaciones) - 1]["tipo"];
        }
        return $tipo;
    }


    //El argumento trimestre esta por si es un curso corto.
    public function getCursosRematricular($anio, $trimestre, $cursosFiltrar, $comisionesFiltrar, $comisionesActivas){
        $conexion = $this->load->database($this->codigofilial, true);
        $cursosConComisiones = Vcursos::getCursosConComisionesActivas($conexion);
        $fechaDesde = '';
        $fechaHasta = '';
        switch($trimestre){
            case 1:
                $fechaDesde = $anio . "-01-01";
                $fechaHasta = $anio . "-03-31";
                break;

            case 2:
                $fechaDesde = $anio . "-04-01";
                $fechaHasta = $anio . "-06-30";
                break;

            case 3:
                $fechaDesde = $anio . "-07-01";
                $fechaHasta = $anio . "-09-30";
                break;

            case 4:
                $fechaDesde = $anio . "-10-01";
                $fechaHasta = $anio . "-12-31";
                break;

            default:
                $fechaDesde = $anio . "-01-01";
                $fechaHasta = $anio . "-12-31";
                break;
        }
        $cursos = array();
        foreach($cursosConComisiones as $curso){
           if($cursosFiltrar != null && !in_array($curso['codigo'], $cursosFiltrar)){
               continue;
           }
           $vcurso = new Vcursos($conexion, $curso['codigo']);
           $comisionesActivasCurso = array();
           foreach($comisionesActivas as $comision){
                if($comision['cod_curso'] == $curso['codigo']){
                    $comisionesActivasCurso[] = $comision;
                }
           }
           $comisiones = array();
           foreach($comisionesActivasCurso as $comision){
              if($comisionesFiltrar != null && !in_array($comision['codigo'], $comisionesFiltrar))
                  continue;
              $ciclo = new Vciclos($conexion, $comision['ciclo']);
              if($ciclo->fecha_inicio_ciclo <= $fechaDesde && $ciclo->fecha_fin_ciclo >= $fechaHasta){
                 $comisiones[] = $comision;
              }
           }
           if(count($comisiones) == 0)
               continue;
           $curso['comisiones'] = $comisiones;
           $cursos[] = $curso;
        }
        return $cursos;
    }

    public function getBoletosReimprimir($matriculas, $desde, $hasta){
        $conexion = $this->load->database($this->codigofilial, true);
        $boletos = array();
        foreach($matriculas as $matricula){
            $resp = Vmatriculas::getBoletosEmitidos($conexion, $matricula, $desde, $hasta, $this->codigofilial);
            foreach($resp['boletos'] as $boleto){
                $boletos[] = $boleto['codigo'];
            }
        }
        return $boletos;
    }

    public function getComisionesRematricular(){
        $conexion = $this->load->database($this->codigofilial, true);
        $cursosConComisiones = Vcursos::getCursosConComisionesActivas($conexion);
        $cursos = array();
        foreach($cursosConComisiones as $curso){
            $cursos[] = $curso['codigo'];
        }
        $comisiones = Vcomisiones::getComisionesRematricular($conexion, $cursos);
        return $comisiones;
    }

    public function habilitarRematriculacion($matricula, $motivo, $fechaDesde, $fechaHasta, $usuario, $cod_curso, $cod_comision, $tipo) {
        $filial = $this->codigofilial;
        $conexion = $this->load->database($this->codigofilial, true);
        $habilitacion = new Vhabilitaciones_rematriculacion($conexion);
        $habilitacion->cod_filial = $filial;
        $habilitacion->cod_matricula = $matricula;
        $habilitacion->cod_usuario = $usuario;
        $habilitacion->motivo = $motivo;
        $habilitacion->fecha_desde = $fechaDesde;
        $habilitacion->fecha_hasta = $fechaHasta;
        $habilitacion->cod_curso = $cod_curso;
        $habilitacion->cod_comision = $cod_comision;
        $habilitacion->tipo = $tipo;
        $habilitacion->guardarHabilitaciones_rematriculacion();
        return $habilitacion->getCodigo();
    }

    public function getFirmaRematricula($arrFiltros){
        $conexion = $this->load->database($this->codigofilial, true);

        $arrCondiciones = array();
        if ($arrFiltros["sSearch"] != "") {
            $arrCondiciones = array(
                "alumnos.documento" => $arrFiltros["sSearch"],
                "alumnos.nombre"=>$arrFiltros["sSearch"],
                "alumnos.apellido" => $arrFiltros["sSearch"],
                "matriculas.codigo"=>$arrFiltros["sSearch"],
                "comisiones.nombre"=>$arrFiltros["sSearch"],
                "general.ciclos.nombre"=>$arrFiltros["sSearch"],
                "control_rematricula.firmo"=>$arrFiltros["sSearch"],
                "control_rematricula.ano"=>$arrFiltros["sSearch"]
            );
        }

        $arrFiltros_nuevos = array();
        if ($arrFiltros["documento"] != "") {
            $arrCondiciones["alumnos.documento"] = $arrFiltros["documento"];
        }
        if ($arrFiltros["nombre"] != "") {
            $arrCondiciones["alumnos.nombre"] = $arrFiltros["nombre"];
        }
        if ($arrFiltros["apellido"] != "") {
            $arrCondiciones["alumnos.apellido"] = $arrFiltros["apellido"];
        }
        if ($arrFiltros["matricula"] != "") {
            $arrCondiciones["matriculas.codigo"] = $arrFiltros["matricula"];
        }
        if ($arrFiltros["comision"] != "") {
            $arrCondiciones["comisiones.nombre"] = $arrFiltros["comision"];
        }
        if ($arrFiltros["ciclo"] != "") {
            $arrCondiciones["general.ciclos.nombre"] = $arrFiltros["ciclo"];
        }
        if ($arrFiltros["firmo"] != "") {
            $arrCondiciones["control_rematricula.firmo"] = $arrFiltros["firmo"];
        }
        if ($arrFiltros["ano"] != "") {
            $arrCondiciones["control_rematricula.ano"] = $arrFiltros["ano"];
        }
        if ($arrFiltros["trimestre"] != "") {
            $arrCondiciones["control_rematricula.trimestre"] = $arrFiltros["trimestre"];
        }
        if ($arrFiltros["fecha"] != "") {
            $arrCondiciones["control_rematricula.fecha"] = $arrFiltros["fecha"];
        }

        if($arrFiltros["condiciones_doc"] != "") {
            $arrFiltros_nuevos["condic_doc"] = $arrFiltros["condiciones_doc"];
        }
        if($arrFiltros["condiciones_nom"] != "") {
            $arrFiltros_nuevos["condic_nom"] = $arrFiltros["condiciones_nom"];
        }
        if($arrFiltros["condiciones_ape"] != "") {
            $arrFiltros_nuevos["condic_ape"] = $arrFiltros["condiciones_ape"];
        }
        if($arrFiltros["condiciones_mat"] != "") {
            $arrFiltros_nuevos["condic_mat"] = $arrFiltros["condiciones_mat"];
        }
        if($arrFiltros["condiciones_com"] != "") {
            $arrFiltros_nuevos["condic_com"] = $arrFiltros["condiciones_com"];
        }
        if($arrFiltros["condiciones_cic"] != "") {
            $arrFiltros_nuevos["condic_cic"] = $arrFiltros["condiciones_cic"];
        }
        if($arrFiltros["condiciones_fir"] != "") {
            $arrFiltros_nuevos["condic_fir"] = $arrFiltros["condiciones_fir"];
        }
        if($arrFiltros["condiciones_ano"] != "") {
            $arrFiltros_nuevos["condic_ano"] = $arrFiltros["condiciones_ano"];
        }
        if($arrFiltros["condiciones_tri"] != "") {
            $arrFiltros_nuevos["condic_tri"] = $arrFiltros["condiciones_tri"];
        }
        if($arrFiltros["condiciones_fec"] != "") {
            $arrFiltros_nuevos["condic_fec"] = $arrFiltros["condiciones_fec"];
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

            $datos = Vfirma_rematricula::listarFirmaRematriculaDataTable($conexion, $arrCondiciones, $arrLimit, $arrSort, false, $arrFiltros_nuevos);
            $contar = Vfirma_rematricula::listarFirmaRematriculaDataTable($conexion, $arrCondiciones, null, null, true, $arrFiltros_nuevos);

            $retorno = array(
                "sEcho" => $arrFiltros["sEcho"],
                "iTotalRecords" => $contar,
                "iTotalDisplayRecords" => $contar,
                "aaData" => array()
            );
            $rows = array();
            foreach ($datos as $row) {
                $rows[] = array(
                    $row["documento"],
                    $row["nombre"],
                    $row["apellido"],
                    $row["codigo"],
                    $row["comision"],
                    $row["ciclo"],
                    $row["firmo"],
                    $row["ano"],
                    $row["trimestre"],
                    $row["fecha"],
                );
            }
            $retorno['aaData'] = $rows;
            return $retorno;
    }

    public function guardarFirma($conexion, $arrFirma) {
        $myFirma = array(
            'cod_matricula' => $arrFirma['cod_matricula'],
            'firmo' => $arrFirma['firmo'],
            'trimestre' => $arrFirma['trimestre'],
            'fecha' => $arrFirma['fecha'],
            'codigo_usuario' =>$arrFirma['codigo_usuario'],
            'ano' => $arrFirma['ano']
        );
        Vfirma_rematricula::insertar($conexion, $myFirma);
        return true;
    }

}

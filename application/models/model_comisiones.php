<?php

/**
 * Model_comisiones
 * 
 * Description...
 * 
 * @package model_comisiones
 * @author ivan <ivan.sys@gmail.com>
 * @version 1.0.0
 */
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Model_comisiones extends CI_Model {

    var $codigo = 0;
    var $codigo_filial = 0;

    public function __construct($arg) {
        parent::__construct();
        $this->codigo = isset($arg["codigo"]) ? $arg["codigo"] : 0;
        $this->codigo_filial = $arg["codigo_filial"];
    }

    /**
     * retorna todas las comisiones
     * @access public
     * @return Array de comisiones.
     */
    public function getComision($codigo) {
        $conexion = $this->load->database($this->codigo_filial, true);
        return $comision = new Vcomisiones($conexion, $codigo);
    }

    /**
     * retorna todos las comisiones activas.
     * @access public
     * @return Array comisiones activas.
     */
    public function getComisionesActivas($incluirComison = null) {
        $conexion = $this->load->database($this->codigo_filial, true);
        $arrcondiciones = array("estado" => Vcomisiones::getEstadoHabilitada());
        $orden = array(array('campo' => 'codigo', 'orden' => 'desc'));
        $comisiones = Vcomisiones::listarComisiones($conexion, $arrcondiciones, null, $orden, null, false, $incluirComison);
        $nombreviejo = Vconfiguracion::getValorConfiguracion($conexion, null, 'verNombreViejoComision');
        if ($nombreviejo == '1') {
            foreach ($comisiones as $key => $comision) {
                $comisiones[$key]['nombre'] = $comision['nombre'] . ' ' . '('  . $comision['descripcion'] . ')'; 
            }
        }
        return $comisiones;
    }
    
        /**
     * retorna todos las comisiones activas.
     * @access public
     * @return Array comisiones que cursan o cursaron una materia.
     */
    public function getComisionesMateria($cod_materia) {
        $conexion = $this->load->database($this->codigo_filial, true);
        $comisiones = Vcomisiones::listarComisionesMateria($conexion, $cod_materia);
        $nombreviejo = Vconfiguracion::getValorConfiguracion($conexion, null, 'verNombreViejoComision');
        if ($nombreviejo == '1') {
            foreach ($comisiones as $key => $comision) {
                $comisiones[$key]['nombre'] = $comision['nombre'] . ' ' . '('  . $comision['descripcion'] . ')'; 
            }
        }
        return $comisiones;
    }

    /**
     * retorna todas las comisiones para el datatable
     * @access public
     * @return Array de comisiones
     */
    public function listaComisionesDatable($arrFiltros){//, $arrFiltrosavanzado) {
        $conexion = $this->load->database($this->codigo_filial, true);
        $this->load->helper('comisiones');
        $arrCondiciones = array();
        if ($arrFiltros["sSearch"] != "") {
            $arrCondiciones = array(
                "nombre" => $arrFiltros["sSearch"],
                "general.cursos.nombre_" . get_idioma() => $arrFiltros["sSearch"],
                "comisiones.codigo" => $arrFiltros['sSearch'],
                "comisiones.estado" => $arrFiltros['sSearch']
            );
        }
       // modificacion ticket 5149 -> agrego el arrFiltros_nuevos ya que alli voy a poner a los valores que indican que hacer con los datos obtenidos
        $arrFiltros_nuevos = array();
        if ($arrFiltros["codigo"] != "") {
            $arrCondiciones["comisiones.codigo"] = $arrFiltros["codigo"];
        }
        if ($arrFiltros["nombre"] != "") {
            $arrCondiciones["comisiones.nombre"] = $arrFiltros["nombre"];
        }
        if ($arrFiltros["curso"] != "") {
            $arrCondiciones["general.cursos.nombre_".get_idioma()] = $arrFiltros["curso"];
        }
        if ($arrFiltros["cant_inscriptos"] != "") {
            $arrCondiciones["inscriptos"] = $arrFiltros["cant_inscriptos"];
        }
        if ($arrFiltros["capacidad"] != "") {
            $arrCondiciones["cupo_disponible"] = $arrFiltros["capacidad"];
        }
        if ($arrFiltros["estado"] != "") {
            $arrCondiciones["comisiones.estado"] = $arrFiltros["estado"];
        }
        if($arrFiltros["condiciones_cod"] != "") {
            $arrFiltros_nuevos["condic_cod"] = $arrFiltros["condiciones_cod"];
        }
        if($arrFiltros["condiciones_nom"] != "") {
            $arrFiltros_nuevos["condic_nom"] = $arrFiltros["condiciones_nom"];
        }
        if($arrFiltros["condiciones_cur"] != "") {
            $arrFiltros_nuevos["condic_cur"] = $arrFiltros["condiciones_cur"];
        }
        if($arrFiltros["condiciones_cant_ins"] != "") {
            $arrFiltros_nuevos["condic_cant_ins"] = $arrFiltros["condiciones_cant_ins"];
        }
        if($arrFiltros["condiciones_capac"] != "") {
            $arrFiltros_nuevos["condic_capac"] = $arrFiltros["condiciones_capac"];
        }
        if($arrFiltros["condiciones_est"] != "") {
            $arrFiltros_nuevos["condic_est"] = $arrFiltros["condiciones_est"];
        }
        /*if(isset($arrCondiciones["inscriptos"]))
            die(print_r($arrCondiciones["inscriptos"]));*/
        // <-modificacion ticket 5149
        $arrLimit = array();
        if ($arrFiltros["iDisplayStart"] != "" && $arrFiltros["iDisplayLength"] != "") {
            $arrLimit = array(
                "0" => $arrFiltros["iDisplayStart"],
                "1" => $arrFiltros["iDisplayLength"]
            );
        }
        $arrSort = array();
        if ($arrFiltros["SortCol"] != "" && $arrFiltros["sSortDir"] != "") {
            $arrSort = array(
                "0" => $arrFiltros["SortCol"],
                "1" => $arrFiltros["sSortDir"]
            );
        }
        $datos = Vcomisiones::getAllComisionesDatatable($conexion, $arrCondiciones, $arrLimit, $arrSort, false, $this->codigo_filial, $arrFiltros_nuevos);/*, $nombre, $curso, $cant_inscriptos, $capacidad);*/
        $contar = Vcomisiones::getAllComisionesDatatable($conexion, $arrCondiciones, null, null, TRUE, $this->codigo_filial, $arrFiltros_nuevos);/*, $nombre, $curso, $cant_inscriptos, $capacidad);*/
        $retorno = array(
            "sEcho" => $arrFiltros["sEcho"],
            "iTotalRecords" => $contar,
            "iTotalDisplayRecords" => $contar,
            "aaData" => array()
        );
        $vernombreviejo = Vconfiguracion::getValorConfiguracion($conexion, null, 'verNombreViejoComision');
        $rows = array();
        foreach ($datos as $row) {
            $nombrePeriodo = lang($row['nombre_periodo']) . '[' . $row['modalidad'] . ']';
            $condicion = array('cod_curso' => $row['cod_curso']);
            $planescurso = Vplanes_academicos::listarPlanes_academicos($conexion, $condicion, null, null, null, $contar);
            $nombrecurso = $row["nombre_" . get_idioma()] != '' ? $row["nombre_" . get_idioma()] : '';
            $nombrecurso.= $planescurso > 1 != '' ? ' / ' . $row["nombre_plan"] : '';
            $nombrecurso.= ' (' . $nombrePeriodo . ')';
            $nombrecomision = $row["nombre"];
            if ($vernombreviejo == 1 && $row['descripcion'] != '' && $row['descripcion'] != '0'){
                $nombrecomision.= ' ('.$row['descripcion'].')';
            }
            $inscriptos = $row['inscriptos'] == '' ? 0 : $row['inscriptos'];
            $rows[] = array(
                $row["codigo"],
                $nombrecomision,
                $nombrecurso,
                $inscriptos,
                $row['cupo_disponible'],
                '',
                $row["estado"],
            );
        }
        $retorno['aaData'] = $rows;
        return $retorno;
    }

    /**
     * Guarda la comision.
     * @access public
     * @return guardar comision.
     */
    public function guardarComision($arrComision, $cod_comision) {
        $conexion = $this->load->database($this->codigo_filial, true);
        $this->load->helper('comisiones');
        $conexion->trans_begin();
        $estado = '';
        $myComision = new Vcomisiones($conexion, $cod_comision);
        $nombreModificado = $this->getPrefijo($cod_comision, $arrComision['cod_plan_academico'], $arrComision['cod_tipo_periodo'], $arrComision['ciclo'], $arrComision['modalidad']);
        $myComision->nombre = $nombreModificado." ".$arrComision['nombre'];
        $myComision->cod_tipo_periodo = $arrComision['cod_tipo_periodo'];
        $myComision->cod_plan_academico = $arrComision['cod_plan_academico'];
        $myComision->ciclo = $arrComision['ciclo'];
        $myComision->modalidad = $arrComision['modalidad'];
        if ($cod_comision == -1){
            $myComision->fecha_creacion = $arrComision['fecha_creacion'];
            $myComision->usuario_creador = $arrComision['usuario_creador'];
            $myComision->descripcion = $arrComision['descripcion'];
            $myComision->estado = Vcomisiones::getEstadoHabilitada();
        }
        $estado = $myComision->guardarComisiones();
        $estadotran = $conexion->trans_status();
        if ($estadotran === FALSE){
            $conexion->trans_rollback();
        } else {
            $conexion->trans_commit();
        }
        $arrRespuesta = array("cod_comision" => $myComision->getCodigo());
        return class_general::_generarRespuestaModelo($conexion, $estadotran, $arrRespuesta);
    }

    /**
     * Cambia el estado de la comision.
     * @access public
     * @return respuesta cambiar estado.
     */
    public function cambiarEstado($cod_comision, $fechaDesde = null) {
        $conexion = $this->load->database($this->codigo_filial, true);
        $comision = new Vcomisiones($conexion, $cod_comision);
        if ($comision->estado == Vcomisiones::getEstadoInhabilitada()) {
            $estado = $comision->activar();
            return class_general::_generarRespuestaModelo($conexion, $estado);
        } else {
            $condiciones = array("cod_comision" => $cod_comision, "baja" => 0);
            $cantidadInscripciones = Vmatriculas_inscripciones::listarMatriculas_inscripciones($conexion, $condiciones);
            if  (count($cantidadInscripciones) > 0){
                return array("error" => "cambiar_comision");// si la comision tiene inscriptos debe cambiarse la comision
            } else {
                if ($fechaDesde == null){
                    return array("error" => "falta_fecha_desde_baja");
                } else {
                    $estado = $comision->baja();
                    return class_general::_generarRespuestaModelo($conexion, $estado);
                }
            }
        }        
    }

    /**
     * retorna todos los planes asignados de una comision.
     * @access public
     * @return Array planes asignados.
     */
    public function getPlanesAsignados($codigo) {
        $conexion = $this->load->database($this->codigo_filial, true);
        $planesAsignados = new Vcomisiones($conexion, $codigo);
        return $planesAsignados->getPlanesAsignados();
    }

    /**
     * retorna todos los planes no asignados de la comision.
     * @access public
     * @return Array Planes no asignados.
     */
    public function getPlanesNoAsignados($codigo) {
        $conexion = $this->load->database($this->codigo_filial, true);
        $planesNoAsignados = new Vcomisiones($conexion, $codigo);
        $planesAsignar = $planesNoAsignados->getPlanesNoAsignados();
        $nombrePeridos = Vtipos_periodos::listarTipos_periodos($conexion);
        foreach ($planesAsignar as $key => $planAsignar) {
            $plan = new Vplanes_pago($conexion, $planAsignar['cod_plan_pago']);
            $periodosPlan = $plan->getPeriodosCurso($planesNoAsignados->getCurso());
            $nombre = '';
            if (count($periodosPlan) > 1) {
                $nombre = lang('TODOS');
            } else {
                foreach ($nombrePeridos as $nombrePeriodo) {
                    if ($planAsignar['cod_tipo_periodo'] == $nombrePeriodo['codigo']) {
                        $nombre = lang($nombrePeriodo['nombre']);
                    }
                }
            }
            $planesAsignar[$key]['nombre'] = $planesAsignar[$key]['nombre'] . ' (' . $nombre . ')';
        }
        return $planesAsignar;
    }

    /**
     * Guarda planes a la comision.
     * @access public
     * @return guardar planes.
     */
    public function guardarPlanes($data_post) {
        $conexion = $this->load->database($this->codigo_filial, TRUE);
        $conexion->trans_start();
        $comision = new Vcomisiones($conexion, $data_post['cod_comision']);
        $msgerror = '';
        $validacion = array("codigo" => 1, 'msgerror' => '');
        $plan = new Vplanes_pago($conexion, $data_post['cod_plan_pago']);
        $periodosPlan = $plan->getPeriodosCurso($comision->getCurso());
        if (count($periodosPlan) == 1) {
            if ($comision->cod_tipo_periodo != $periodosPlan[0]['cod_tipo_periodo']) {
                $validacion['codigo'] = 0;
                $msgerror.= lang('planes_validacion_plan') . ' ' . $plan->nombre . ' .' . lang('planes_periodos_incorrectos');
            }
        }
        if ($validacion['codigo'] == 0) {
            $validacion['msgerror'] = $msgerror;
            return $validacion;
        } else {
            $Mandar = '';
            switch ($data_post['accion']) {
                case 'checked':
                    $Mandar = $comision->SetPlan($data_post['cod_plan_pago']);
                    break;
                
                case 'deschecked':
                    $Mandar = $comision->unSetPlan($data_post["cod_plan_pago"]);
                    break;

                default:
                    break;
            }
            $conexion->trans_complete();
            $resultado = $conexion->trans_status();
            return class_general::_generarRespuestaModelo($conexion, $resultado, $Mandar);
        }
    }

    public function mostrarPlanWeb($data_post) {
        $conexion = $this->load->database($this->codigo_filial, true);
        $myComision = new Vcomisiones($conexion, $data_post['cod_comision']);
        $myComision->unSetMostrarWeb();
        $resultado = '';        
        switch ($data_post['accion']) {
            case 'checked':
                $resultado = $resultado = $myComision->setMostrarWeb($data_post['cod_plan_pago']);
                break;

            case 'deschecked':
                $resultado = $myComision->unSetMostrarWeb($data_post['cod_plan_pago']);
                break;
        }
        if ($resultado) {
            $retorno = array(
                "codigo" => 1
            );
        } else {
            $retorno = array(
                "codigo" => 0,
                "msgerror" => lang('ocurrio_error')
            );
        }
        return $retorno;
    }

    /**
     * retorna todas las materias sin horario definido.
     * @access public
     * @return Array materias sin horarios definidos
     */
    public function getMateriasSinHorarioDefinido($codigo) {
        $conexion = $this->load->database($this->codigo_filial, TRUE);
        $comision = new Vcomisiones($conexion, $codigo);
        return $comision->getMateriasSinHorarioDefinido();
    }

    public function getPlanes($idFilial, $idComision) {
        $conexion = $this->load->database($idFilial, true);
        $myComision = new Vcomisiones($conexion, $idComision);
        return $myComision->getPlanesAsignados();
    }

    public function getMateriasHorariosComision($cod_comision) {
        $conexion = $this->load->database($this->codigo_filial, true);
        $comision = new Vcomisiones($conexion, $cod_comision);
        $materiasComision = $comision->getMateriasHorariosComision();
        return $materiasComision;
    }

    public function getComisiones($idFilial, $activas = null) {
        $conexion = $this->load->database($idFilial, true);
        $condiciones = array();
        if ($activas !== null){
            $condiciones = array("estado" => $activas);
        }
        $arrRegistros = Vcomisiones::listarComisiones($conexion, $condiciones);
        $arrResp = array();
        $arrResp['rows'] = $arrRegistros;
        $arrResp['total_rows'] = count($arrRegistros);
        return $arrResp;
    }
    
    public function getHorario($codmateria = null) {
        $conexion = $this->load->database($this->codigo_filial, true);
        $comision = new Vcomisiones($conexion, $this->codigo);
        $horario = $comision->getHorarios($codmateria);
        $arrHorario = array();
        foreach ($horario as $value) {
            $dia = formatearFecha_descripciondia($value["DIA_SEMANA"]);
            $arrHorario[][$dia] = array(
                "desde" => $value["horadesde"],
                "hasta" => $value["horahasta"]
            );
        }
        return $arrHorario;
    }

    public function getDiasCursadoComision($cod_comision, $cod_materia) {
        $conexion = $this->load->database($this->codigo_filial, true);
        $objComision = new Vcomisiones($conexion, $cod_comision);
        $diasCursadoCom = $objComision->getDiasCursadoComision($cod_materia);

        $arrDias = array();
        $arrDias['dias'] = array();
        $hoy = date('Y-m-d');
        $cantdias = 10000;
        $parar = '';
        foreach ($diasCursadoCom as $dias) {
            $arrDias['dias'][] = $dias;
            $datetime1 = new DateTime($hoy);
            $datetime2 = new DateTime($dias['dia']);
            $interval = date_diff($datetime1, $datetime2);
            if ($interval->days <= $cantdias) {
                $parar = $dias['dia'];
                $cantdias = $interval->days;
            }
        }
        $arrDias['dia_parar'] = $parar;
        return $arrDias;
    }

    public function getObjComision($cod_comision) {
        $conexion = $this->load->database($this->codigo_filial, true);
        $objcomision = new Vcomisiones($conexion, $cod_comision);
        return $objcomision;
    }

    public function getPrefijo($cod_comision, $cod_plan_academico, $cod_periodo, $ciclo, $modalidad = '') {
        $conexion = $this->load->database($this->codigo_filial, true);
        $this->load->helper('comisiones');
        $nombre = '';
        $condiciones = array();
        if ($cod_comision != -1) {
            $condiciones = array(
                "cod_plan_academico" => $cod_plan_academico,
                "codigo <>" => $cod_comision
            );
        } else {
            $condiciones = array(
                "cod_plan_academico" => $cod_plan_academico);
        }
        $condicionCiclo = array(
            "codigo" => $ciclo
        );
        $arrCicloAcademico = Vciclos::listarCiclos($conexion, $condicionCiclo);
        $arrdatos['cod_tipo_periodo'] = $cod_periodo != FALSE ? $cod_periodo : '';
        $arrdatos['cod_plan_academico'] = $cod_plan_academico != FALSE ? $cod_plan_academico : '';
        $arrdatos['ciclo'] = $ciclo != FALSE ? $arrCicloAcademico[0]['abreviatura'] : '';
        $arrdatos['nombre'] = $nombre != FALSE ? $nombre : '';
        if ($modalidad != '') {
            $arrdatos['modalidad'] = $modalidad;
        }
        $prefijo = formatearNombreComision(null, $arrdatos);
        return $prefijo;
    }

    public function getComisionesconHorarios() {
        $conexion = $this->load->database($this->codigo_filial, true);
        $comisiones = Vcomisiones::getComisionesconHorarios($conexion);
        foreach ($comisiones as $key => $comision) {
            $comisiones[$key]['nombre'] = $comision['nombre'] . ' ' . '(' . ' ' . $comision['descripcion'] . ')';
        }
        return $comisiones;
    }

    public function getMateriasComision($cod_comision) {
        $conexion = $this->load->database($this->codigo_filial, true);
        $comision = new Vcomisiones($conexion, $cod_comision);
        $materiasComision = $comision->getMateriasComision();
        return $materiasComision;
    }

    public function getAlumnosMateriaComision($cod_comision, $cod_materia) {
        $conexion = $this->load->database($this->codigo_filial, TRUE);
        $this->load->helper('alumnos');
        $alumnosComMat = Vmatriculas_inscripciones::getInscripcionesComision($conexion, $cod_comision, $cod_materia, TRUE);
        $patron = "/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/";
        foreach ($alumnosComMat as $key => $alumno) {
            $alumnosComMat[$key]['nombre_apellido'] = inicialesMayusculas($alumno['nombre_apellido']);
            if (preg_match($patron, $alumno['email'])){
                $alumnosComMat[$key]['enviarComunicado'] = 'Si';
            } else {
                $alumnosComMat[$key]['enviarComunicado'] = 'No';
                $alumnosComMat[$key]['motivo'] = lang('el_alumno_no_tiene_email');
            }
        }
        return $alumnosComMat;
    }

    public function getAlumnosComision($cod_comision) {
        $conexion = $this->load->database($this->codigo_filial, true);
        $objComision = new Vcomisiones($conexion, $cod_comision);
        $cantAlumnos = $objComision->getAlumnosComision();
        return $cantAlumnos;
    }

    
    public function getCodigoCursoWeb($conexion, $cod_curso, $cod_filial){
        $filial = new Vfiliales($conexion, $cod_filial);
        $pais = $filial->pais;
        $conexion->select('codigo_web');
        $conexion->from('general.codigo_cursos_web');
        $conexion->where('cod_curso', $cod_curso);
        $conexion->where('cod_pais', $pais);
        $query = $conexion->get();
        $codigo = $query->result_array();
        if(count($codigo) == 0)
            return $cod_curso;
        return $codigo[0]['codigo_web'];
    }

    public function reporteComisionesActivas($cod_filial, $simbolo, $moneda) {
        $conexion = $this->load->database($cod_filial, true);
        $reporte = Vcomisiones::getReportesComisionesActivas($conexion);
        foreach ($reporte as $key => $valor) {
            if($cod_filial == '90')
            {
                $reporte[$key]['valormatricula'] = $reporte[$key]['valormatriculaneto'];
            }
            $reporte[$key]['simbolo_moneda'] = $simbolo;
            $reporte[$key]['id_moneda'] = $moneda;
            $reporte[$key]['dia'] = '';
            $reporte[$key]['esquema_cuotas_mostrar'] = 0;
            $reporte[$key]['codigocurso'] = $this->getCodigoCursoWeb($conexion, $reporte[$key]['codigocurso'], $cod_filial);
            $myComision = new Vcomisiones($conexion, $valor['codigo']);
            $horarios = $myComision->getHorarios();
            foreach ($horarios as $value) {
                $horarioCursado = array(
                    "dia" => $value["DIA_SEMANA"] + 1,
                    "horadesde" => $value["horadesde"],
                    "horahasta" => $value["horahasta"]
                );
                $reporte[$key]['horarios'][] = $horarioCursado;
            }
            $myPlan = new Vplanes_pago($conexion, $valor['id_plan']);
            $arrDetallesPlan = $myPlan->getEsquemaCuotas($valor['nro_cuotas']);
            $detalleCuotas = array();
            //mesaje solo para miami, después se vera :(
            $mensajeDescuentos = false;
            if (is_array($arrDetallesPlan)){
                $iniciar = true;
                foreach ($arrDetallesPlan as $detalle){
                    if ($iniciar){
                        if($cod_filial == '90')
                        {
                            //A pedido de miami mostramos valor neto
                            $valorActual = $detalle['valor_neto'];
                        }
                        else
                        {
                            $valorActual = $detalle['valor'];
                        }
                        $cuotaInicio = $detalle['nro_cuota'];
                        $iniciar = false;
                    } else if ($cod_filial != '90' && $detalle['valor'] <> $valorActual){
                        
                        $detalleCuotas[] = array("cuota_inicio" => $cuotaInicio, 
                            "cuota_fin" => $detalle['nro_cuota'] -1, 
                            "valor" => $valorActual);
                        
                        $cuotaInicio = $detalle['nro_cuota'];
                        $valorActual = $detalle['valor'];
                    }
                    else if($cod_filial == '90' && $detalle['valor_neto'] <> $valorActual)
                    {
                        $detalleCuotas[] = array("cuota_inicio" => $cuotaInicio,
                            "cuota_fin" => $detalle['nro_cuota'] -1,
                            "valor" => $valorActual);

                        $cuotaInicio = $detalle['nro_cuota'];
                        $valorActual = $detalle['valor_neto'];
                    }

                    if($mensajeDescuentos === false && $cod_filial == '90' && $detalle['valor_neto'] <> $detalle['valor'])
                    {
                        $mensajeDescuentos = 'mensaje_descuento';//'Ask for discounts';
                    }
                }

                $detalleCuotas[] =  array(
                    "cuota_inicio" => $cuotaInicio,
                    "cuota_fin" => count($arrDetallesPlan),
                    "valor" => $valorActual);
                $reporte[$key]['detalle_cuotas'] = $detalleCuotas;
                $reporte[$key]['mensaje_descuento'] = $mensajeDescuentos;
            } else {
                $reporte[$key]['detalle_cuotas'] = "";
            }
        }
        return $reporte;
    }

    public function formatearHorarioComisionPlanes($id_comision) {
        $conexion = $this->load->database($this->codigo_filial, true);
        $comision = new Vcomisiones($conexion, $id_comision);
        $horario = $comision->getHorarios();
        $string = '';
        $retorno = '';
        if (count($horario) > 0) {
            foreach ($horario as $row) {
                $dia = formatearFecha_descripciondia($row["DIA_SEMANA"]);
                $string .=$dia . ' Desde: ' . $row['horadesde'] . ' ' . 'Hasta: ' . $row['horahasta'] . ', ';
            }
        } else {
            $string = lang('no_tiene_horarios');
        }
        $retorno['horarios'] = $string;
        return $retorno;
    }

    public function guardarPeriodoProrroga($cod_comision, $valor) {
        $conexion = $this->load->database($this->codigo_filial, true);
        $conexion->trans_start();
        $myComision = new Vcomisiones($conexion, $cod_comision);
        $myComision->dias_prorroga = $valor;
        $myComision->guardarComisiones();
        $estadotran = $conexion->trans_status();
        if ($estadotran === FALSE) {
            $conexion->trans_rollback();
        } else {
            $conexion->trans_commit();
        }
        return class_general::_generarRespuestaModelo($conexion, $estadotran);
    }

    public function getPlanesVigentesMatricular($periodos) {
        $conexion = $this->load->database($this->codigo_filial, true);
        $arrperiodos = array();
        $planes = array();
        $respuesta = array();
        $codigos = array();
        $i = 0;
        foreach ($periodos as $rowperiodo) {
            if (isset($rowperiodo['seleccionado'])) {
                $arrperiodos[] = $rowperiodo['seleccionado'];
                if (isset($rowperiodo['comision']) && $rowperiodo['comision'] != '') {
                    $comision = new Vcomisiones($conexion, $rowperiodo['comision']);
                    $arrplanes = $comision->getPlanesPago(true);
                    foreach ($arrplanes as $value) {
                        if (!in_array($value['codigo'], $codigos)) {
                            $planes[$i] = $value;
                            $codigos[$i] = $value['codigo'];
                            $i++;
                        }
                    }
                }
            }
        }
        foreach ($planes as $key => $plan) {


            $objplan = new Vplanes_pago($conexion, $plan['codigo']);
            $periodos = $objplan->getCursosPeriodosPlan();


            $asigno = true;
            foreach($arrperiodos as $value)
            {
                $esta = false;
                foreach ($periodos as $periodo){
                    $esta = $esta || ($periodo['cod_tipo_periodo'] == $value);
                }
                $asigno = $asigno && $esta;
            }

            if ($asigno) {
                $planes[$key]['nombre'].=$objplan->descon == '1' ? ' *' . lang('pierde_descuento') : '';
                $respuesta[] = $planes[$key];
            }
        }
        return $respuesta;
    }

    public function getCicloLectivosFilial($forzarCiclo = null, $fechaFinCicloDesde = null) {
        $conexion = $this->load->database($this->codigo_filial, true);
        $listadoCicloslectivos = Vcomisiones::getCiclosComisiones($conexion, $this->codigo_filial, $forzarCiclo, $fechaFinCicloDesde);
        $arrCiclosLectivosFilial = array();
        foreach ($listadoCicloslectivos as $ciclo) {
            $arrCiclosLectivosFilial[] = array(
                "codigo" => $ciclo['codigo'],
                "ciclo_lectivo" => $ciclo['nombre'] . ' (' . formatearFecha_pais($ciclo['fecha_inicio_ciclo']) . ' - ' . formatearFecha_pais($ciclo['fecha_fin_ciclo']) . ')'
            );
        }
        return $arrCiclosLectivosFilial;
    }

    public function getCicloComision($ciclo) {
        $conexion = $this->load->database($this->codigo_filial, true);
        $condiciones = array(
            "codigo" => $ciclo
        );
        $listaCiclo = Vciclos::listarCiclos($conexion, $condiciones);
        $arrCiclo = array();
        foreach ($listaCiclo as $ciclo_lectivo) {
            $arrCiclo = array(
                "codigo" => $ciclo_lectivo['codigo'],
                "ciclo_lectivo" => $ciclo_lectivo['nombre'] . ' (' . formatearFecha_pais($ciclo_lectivo['fecha_inicio_ciclo']) . '-' . formatearFecha_pais($ciclo_lectivo['fecha_fin_ciclo']) . ')'
            );
        }
        return $arrCiclo;
    }

    public function getInscriptosComision($cod_comision) {
        $conexion = $this->load->database($this->codigo_filial, true);
        $myComision = new Vcomisiones($conexion, $cod_comision);
        $inscriptos = $myComision->getInscriptosComision();
        $tieneInscriptos = 0;
        if (count($inscriptos) > 0) {
            $tieneInscriptos = 1;
        }
        return $tieneInscriptos;
    }

    public function getFechaInicioComision($id_comision) {
        $conexion = $this->load->database($this->codigo_filial, true);
        $myComision = new Vcomisiones($conexion, $id_comision);
        $fecha_inicio = $myComision->getFechaInicioComision();
        $fecha_inicio_comision = '';
        if ($fecha_inicio[0]['fecha_inicio'] == 'no_tiene_horarios') {
            $fecha_inicio_comision = 'no_tiene_horarios';
        } else {
            $fecha_inicio_comision = formatearFecha_pais($fecha_inicio[0]['fecha_inicio']);
        }
        return $fecha_inicio_comision;
    }

    public function getComisionesCambiar(CI_DB_mysqli_driver $conexion = null, $codComision = null){
    	
        if ($conexion == null){
            $conexion = $this->load->database($this->codigo_filial, true);
        }
        
        $myComision = new Vcomisiones($conexion, $codComision);
        
        if ($codComision == null || $myComision->estado == Vcomisiones::getEstadoAPasar()){
            $validarPeriodoVencido = true;
            $codTipoPeriodo = 1;
            $estadoEstadoAcademico = array(
                Vestadoacademico::getEstadoAprobado(),
                Vestadoacademico::getEstadoHomologado(),
                Vestadoacademico::getEstadoLibre(),
                Vestadoacademico::getEstadoRecursa(),
                Vestadoacademico::getEstadoRegular(),
                Vestadoacademico::getEstadoCursando()
            );
            
            $arrPlanesAcademicos = Vplanes_academicos::getPlanesAcademicosCantidadPeriodos($conexion, null, 2);
            $planesAcademicos = array();
            
            foreach ($arrPlanesAcademicos as $plan){
                $planesAcademicos[] = $plan['codigo'];
            }
            
        } else {
        	
            $validarPeriodoVencido = false;
            $codTipoPeriodo = null;
            $planesAcademicos = null;
            $estadoEstadoAcademico = Vestadoacademico::getEstadoCursando();
        }
        
		$arrComisiones = Vcomisiones::getComisionesCantidadesInscriptos($conexion, $planesAcademicos, null, $validarPeriodoVencido, $codTipoPeriodo, $estadoEstadoAcademico);

        
        $codMatriculaActual = 0;
        $comisiones = array();
        
        foreach ($arrComisiones as $comision){

            if ($comision['cod_matricula_periodo'] <> $codMatriculaActual){
            	
                $codMatriculaActual = $comision['cod_matricula_periodo'];

                if (!isset($comisiones[$comision['cod_comision']]['cantidad_estado_cursando'])){
                	
                    $comisiones[$comision['cod_comision']]['cantidad_estado_cursando'] = $comision['cantidad_estado_cursando'];
                
                } else {
                	
                    $comisiones[$comision['cod_comision']]['cantidad_estado_cursando'] += $comision['cantidad_estado_cursando'];
                }
                
                if (!isset($comisiones[$comision['cod_comision']]['cantidad'])){
                	
                    $comisiones[$comision['cod_comision']]['cantidad'] = 1;
                    
                } else {
                	
                    $comisiones[$comision['cod_comision']]['cantidad'] ++;
                }
            }

            $this->load->helper("alumnos");
            $test = Vmatriculas_inscripciones::getInscripcionesComision($conexion, $comision['cod_comision'], null, true, array(Vestadoacademico::getEstadoCursando() ));
            $comisiones[$comision['cod_comision']]['totaAlumnosOrigen'] = count($test);
        }      
          
        ksort($comisiones);
        foreach ($comisiones as $key => $comision){
            $myComision =  new Vcomisiones($conexion, $key);
            foreach ($myComision as $idx => $value){
                $comisiones[$key][$idx] = $value;
            }
        }
        if ($codComision != null){
           return isset($comisiones[$codComision]) ? array($codComision => $comisiones[$codComision]) : array();
        } else {
            return $comisiones;
                   
        }
    }
    //modificacion ticket 5149->
    public function listanombres(){
        $conexion = $this->load->database($this->codigo_filial, true);
        $arrnombres = Vcomisiones::listarnombres($conexion);
    }
    //<-modificacion ticket 5149
}

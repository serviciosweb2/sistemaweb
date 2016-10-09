<?php
/**
 * Class Vmatriculas_periodos
 *
 * Class  Vmatriculas_periodos maneja todos los aspectos de la matricula periodo
 *
 * @package  SistemaIGA
 * @subpackage Alumnos
 * @author   vane
 * @version  $Revision: 1.1 $
 * @access   private
 */
class Vmatriculas_periodos extends Tmatriculas_periodos {
    /* CONSTRUCTOR */

    static private $estadohabilitada = "habilitada";
    static private $estadoinhabilitada = "inhabilitada";
    static private $estadofinalizada = "finalizada";
    static private $estadocertificada = "certificada";
    static private $arrayEstados = array("habilitada", "inhabilitada", "finalizada", "certificada", "prematricula");

    function __construct(CI_DB_mysqli_driver $conexion, $codigo = null) {
        parent::__construct($conexion, $codigo);
    }

    public function guardar($codmatricula, $codperiodo, $codusuario, $fecha, $codfilial, $modalidad, $codcomision = null) {
        $this->cod_matricula = $codmatricula;
        $this->cod_tipo_periodo = $codperiodo;
        $this->cod_usuario_creador = $codusuario;
        $this->fecha_emision = $fecha;
        $this->estado = Vmatriculas_periodos::getEstadoHabilitada();
        $this->modalidad = $modalidad;
        $this->guardarMatriculas_periodos();

        $objmatricula = new Vmatriculas($this->oConnection, $codmatricula);
        $planacademico = new Vplanes_academicos($this->oConnection, $objmatricula->cod_plan_academico);
        $materias = $planacademico->getMaterias($this->cod_tipo_periodo);

        //GUARDO ESTADO ACADEMICO
        foreach ($materias as $rowMateria) {
            $estadoaca = new Vestadoacademico($this->oConnection);
            if ($codcomision != null) {
                $estadoaca->guardar($this->codigo, $rowMateria['codigo'], null, null, null, $this->cod_usuario_creador, $codcomision);
            } else {
                $estadoaca->guardar($this->codigo, $rowMateria['codigo']);
            }
        }

        //GUARDO CERTIFICADO
        $condiciones = array('cod_filial' => $codfilial, 'cod_tipo_periodo' => $this->cod_tipo_periodo, 'cod_plan_academico' => $planacademico->getCodigo(), 'opcional' => '0');
        $certificadosobligatorios = Vcertificados_plan_filial::listarCerfificados_plan_filial($this->oConnection, $condiciones);
        //print_r($certificadosobligatorios);
        if (count($certificadosobligatorios) > 0){
            foreach ($certificadosobligatorios as $certificado) {
                $objcertificado = new Vcertificados($this->oConnection, $this->codigo, $certificado['cod_certificante']);
                $objcertificado->guardar($codfilial, null, null, $codusuario);
            }
        } else {
            $objcertificado = new Vcertificados($this->oConnection, $this->codigo, 1); // el certificado iga siempre es obligatorio
            $objcertificado->guardar($codfilial, null, null, $codusuario);
        }
    }

    public function baja($motivo = null, $comentario = null, $codusuario = null) {

        $this->estado = Vmatriculas_periodos::getEstadoInhabilitada();
        $this->guardarMatriculas_periodos();

        $this->bajaCertificados($codusuario);

        $condicionea = array('cod_matricula_periodo' => $this->codigo);
        $estadoacademico = Vestadoacademico::listarEstadoacademico($this->oConnection, $condicionea);

        foreach ($estadoacademico as $materia) {
            //baja inscripciones
            $objestaca = new Vestadoacademico($this->oConnection, $materia['codigo']);
            $objestaca->bajaInscripciones($codusuario);
        }

        //completo estado historico
        $estadosHistoricos = new Vmatriculas_estado_historicos($this->oConnection);

        $arrayGuardarEstadoHistorico = array(
            "cod_matricula_periodo" => $this->codigo,
            "estado" => $this->estado,
            "motivo" => $motivo == null ? 3 : $motivo,
            "fecha_hora" => date('Y-m-d H:i:s'),
            "comentario" => $comentario,
            "cod_usuario" => $codusuario,
            "modalidad" => $this->modalidad
        );

        $estadosHistoricos->setMatriculas_estado_historicos($arrayGuardarEstadoHistorico);
        $respuesta = $estadosHistoricos->guardarMatriculas_estado_historicos();
        //si todas las matriculas_periodos estan inhabilitadas se inhabilita la matricula
        $objmatricula = new Vmatriculas($this->oConnection, $this->cod_matricula);
        $objmatricula->cambiarEstado();
        return $respuesta ? $estadosHistoricos->getCodigo() : false;
    }

    public function alta($motivo = null, $comentario = null, $codusuario = null) {

        $this->estado = Vmatriculas_periodos::getEstadoHabilitada();
        $this->guardarMatriculas_periodos();

        $this->altaCertificados($codusuario);

        $estadosHistoricos = new Vmatriculas_estado_historicos($this->oConnection);

        $arrayGuardarEstadoHistorico = array(
            "cod_matricula_periodo" => $this->codigo,
            "estado" => $this->estado,
            "motivo" => $motivo,
            "fecha_hora" => date('Y-m-d H:i:s'),
            "comentario" => $comentario,
            "cod_usuario" => $codusuario,
            "modalidad" => $this->modalidad
        );

        $estadosHistoricos->setMatriculas_estado_historicos($arrayGuardarEstadoHistorico);
        $respuesta = $estadosHistoricos->guardarMatriculas_estado_historicos();

        $objmatricula = new Vmatriculas($this->oConnection, $this->cod_matricula);
        $objmatricula->cambiarEstado();
        return $respuesta;
    }

    static function getEstadoHabilitada() {
        return self::$estadohabilitada;
    }

    static function getEstadoInhabilitada() {
        return self::$estadoinhabilitada;
    }

    static function getEstadoFinalizada() {
        return self::$estadofinalizada;
    }

    static function getEstadoCertificada() {
        return self::$estadocertificada;
    }

    static function getEstados() {
        return self::$arrayEstados;
    }

    public function getAntecesores() {
        $resultado = array();
        $matricula = new Vmatriculas($this->oConnection, $this->cod_matricula);
        $plan = new Vplanes_academicos($this->oConnection, $matricula->cod_plan_academico);
        $periodosantes = $plan->getPeriodosAnteceden($this->cod_tipo_periodo);
        $arr = array();
        foreach ($periodosantes as $value) {
            $arr[] = $value['cod_tipo_periodo'];
        }
        if (count($arr) > 0) {
            $this->oConnection->select('matriculas_periodos.codigo');
            $this->oConnection->from('matriculas_periodos');
            $this->oConnection->join('matriculas', 'matriculas.codigo = matriculas_periodos.cod_matricula');
            $this->oConnection->where_in('cod_tipo_periodo', $arr);
            $this->oConnection->where('matriculas.cod_alumno', $matricula->cod_alumno);
            $this->oConnection->where('matriculas_periodos.estado <>', 'migrado');
            $this->oConnection->where('matriculas.cod_plan_academico', $plan->getCodigo());
            $query = $this->oConnection->get();
            $resultado = $query->result_array();
        }

        return $resultado;
    }

    public function getEstadoAcademico($detalle = false) {
        $this->oConnection->select('MAX(codigo) cod');
        $this->oConnection->from('estadoacademico');
        $this->oConnection->where('estadoacademico.cod_matricula_periodo =  "'.$this->codigo.'"');
        $this->oConnection->group_by('codmateria');
        $subQueryRecursa = $this->oConnection->return_query();
        $this->oConnection->resetear();

        $this->oConnection->select('es1.*');
        $this->oConnection->from('estadoacademico es1');
        //$this->oConnection->where('estadoacademico.cod_matricula_periodo', $this->codigo);
        $this->oConnection->join("($subQueryRecursa) es2", "es1.codigo = es2.cod");
        if ($detalle) {
            $this->oConnection->select(' general.materias.nombre_es, general.materias.nombre_in, general.materias.nombre_pt');
            $this->oConnection->select('(select codigo from matriculas_inscripciones where matriculas_inscripciones.cod_estado_academico = es1.codigo and baja = 0) as inscripcion');
            $this->oConnection->join('general.materias', 'general.materias.codigo = es1.codmateria');
            $this->oConnection->order_by('general.materias.nombre_' . get_idioma(), 'asc');
        }
        $query = $this->oConnection->get();
        return $query->result_array();
    }

    public function getCtaCteConceptos($arrconceptos) {
        $objmatricula = new Vmatriculas($this->oConnection, $this->cod_matricula);
        $alumno = new Valumnos($this->oConnection, $objmatricula->cod_alumno);

        $condiciones = array('concepto' => $this->codigo);
        $wherein = array(array('campo' => 'cod_concepto', 'valores' => $arrconceptos));

        $ctacte = $alumno->getCtaCte(null, $condiciones, $wherein, 1);

        return $ctacte;
    }

    public function finalizada() {
        $this->estado = Vmatriculas_periodos::getEstadoFinalizada();
        $this->guardarMatriculas_periodos();

        $estadosHistoricos = new Vmatriculas_estado_historicos($this->oConnection);

        $arrayGuardarEstadoHistorico = array(
            "cod_matricula_periodo" => $this->codigo,
            "estado" => $this->estado,
            "motivo" => 5,
            "fecha_hora" => date('Y-m-d H:i:s'),
            "comentario" => '',
            "cod_usuario" => '',
            "modalidad" => $this->modalidad
        );

        $estadosHistoricos->setMatriculas_estado_historicos($arrayGuardarEstadoHistorico);
        $respuesta = $estadosHistoricos->guardarMatriculas_estado_historicos();
        $objmatricula = new Vmatriculas($this->oConnection, $this->cod_matricula);
        $objmatricula->cambiarEstado();
        return $respuesta;
    }

    public function certificada() {
        $this->estado = Vmatriculas_periodos::getEstadoCertificada();
        $this->guardarMatriculas_periodos();

        $estadosHistoricos = new Vmatriculas_estado_historicos($this->oConnection);

        $arrayGuardarEstadoHistorico = array(
            "cod_matricula_periodo" => $this->codigo,
            "estado" => $this->estado,
            "motivo" => '',
            "fecha_hora" => date('Y-m-d H:i:s'),
            "comentario" => '',
            "cod_usuario" => '',
            "modalidad" => $this->modalidad
        );

        $estadosHistoricos->setMatriculas_estado_historicos($arrayGuardarEstadoHistorico);
        $respuesta = $estadosHistoricos->guardarMatriculas_estado_historicos();
        $objmatricula = new Vmatriculas($this->oConnection, $this->cod_matricula);
        $objmatricula->cambiarEstado();
        return $respuesta;
    }

    public function getCertificadosProcesados() {
        $this->oConnection->select('');
        $this->oConnection->from('certificados');
        $this->oConnection->where('cod_matricula_periodo', $this->codigo);
        $this->oConnection->where('estado <>', Vcertificados::getEstadoPendiente());
        $this->oConnection->where('estado <>', Vcertificados::getEstadoPendienteAprobar());
        $this->oConnection->where('estado <>', Vcertificados::getEstadoPendienteImpresion());
        $this->oConnection->where('estado <>', Vcertificados::getEstadoCancelado());
        $query = $this->oConnection->get();
        return $query->result_array();
    }

    public function bajaCertificados($codusuario) {
        $condiciones = array('cod_matricula_periodo' => $this->codigo);
        $wherein = array('campo' => 'estado', 'valores' => array(Vcertificados::getEstadoPendiente(), Vcertificados::getEstadoPendienteAprobar(), Vcertificados::getEstadoPendienteImpresion()));
        $certificados = Vcertificados::listarCerfificados($this->oConnection, $condiciones, null, null, null, null, $wherein);

        foreach ($certificados as $rowcertificado) {
            $objcertificado = new Vcertificados($this->oConnection, $this->codigo, $rowcertificado['cod_certificante']);
            $objcertificado->setCancelado($codusuario, null, 5); //motivo matricula baja
        }
    }

    public function altaCertificados($codusuario) {
        $condiciones = array('cod_matricula_periodo' => $this->codigo, 'estado' => Vcertificados::getEstadoCancelado());
        $certificados = Vcertificados::listarCerfificados($this->oConnection, $condiciones);

        foreach ($certificados as $rowcertificado) {
            $objcertificado = new Vcertificados($this->oConnection, $this->codigo, $rowcertificado['cod_certificante']);

            $condiciones2 = array('cod_matricula_periodo' => $objcertificado->getCodigoMatriculaPeriodo(), 'cod_certificante' => $objcertificado->getCodigoCertificante(), 'estado' => Vcertificados::getEstadoCancelado());
            $orden = array(array('campo' => 'codigo', 'orden' => 'desc'));
            $historico = Vcertificados_estado_historico::listarCertificados_estado_historico($this->oConnection, $condiciones2, null, $orden);
            if (isset($historico) && isset($historico[0]['motivo']) && $historico[0]['motivo'] == '5') {//se cancelo por baja de matricula
                $objcertificado->setPendiente($codusuario, null, 6); //motivo alta matricula
            }
        }
    }

    public function setModalidad($modalidad, $cod_usuario = null) {
        $this->modalidad = $modalidad;
        $this->guardarMatriculas_periodos();

        $estadosHistoricos = new Vmatriculas_estado_historicos($this->oConnection);

        $arrayGuardarEstadoHistorico = array(
            "cod_matricula_periodo" => $this->codigo,
            "estado" => $this->estado,
            "motivo" => '',
            "fecha_hora" => date('Y-m-d H:i:s'),
            "comentario" => '',
            "cod_usuario" => $cod_usuario,
            "modalidad" => $this->modalidad
        );

        $estadosHistoricos->setMatriculas_estado_historicos($arrayGuardarEstadoHistorico);
        $respuesta = $estadosHistoricos->guardarMatriculas_estado_historicos();
        return $respuesta;
    }

    static function getMatriculasPeriodosFueraDeCiclo(CI_DB_mysqli_driver $conexion, $mesesVencido = 4) {
        $conexion->select("matriculas_periodos.*");
        $conexion->from("estadoacademico");
        $conexion->join("matriculas_inscripciones", "matriculas_inscripciones.cod_estado_academico = estadoacademico.codigo and matriculas_inscripciones.baja = 0");
        $conexion->join("comisiones", "comisiones.codigo = matriculas_inscripciones.cod_comision");
        $conexion->join("matriculas_periodos", "matriculas_periodos.codigo = estadoacademico.cod_matricula_periodo");
        $conexion->join("general.ciclos", "general.ciclos.codigo = comisiones.ciclo AND general.ciclos.fecha_fin_ciclo <= DATE_ADD(CURDATE(), INTERVAL -$mesesVencido MONTH)");
        $conexion->where("estadoacademico.estado", Vestadoacademico::getEstadoCursando());
        $conexion->where("matriculas_periodos.estado", Vmatriculas_periodos::getEstadoHabilitada());
        $conexion->group_by("matriculas_periodos.codigo");
        $query = $conexion->get();
        return $query->result_array();
    }

    /**
     * Realiza el pasaje de la matricula_periodo de una comision a otra contenplando si en el pasaje hay cambio de perido
     * 
     * @param integer $codComision          La comision de destino
     * @param integer $idUsuarioCreador     El usuario que intenta realizar el cambio
     * @param integer $comisionOrigen       Si el pasaje se realiza entre comisiones del mismo periodo, este campo es obligatorio
     * @return boolean
     */
    public function pasarDeComision($codComision, $idUsuarioCreador, $comisionOrigen = null, $fechaDesde = null) {
        $resp = true;
        $myComision = new Vcomisiones($this->oConnection, $codComision);
        $pasaDeAnio = $myComision->cod_tipo_periodo == $this->cod_tipo_periodo + 1;
        if (!$pasaDeAnio) { // cambia de comision dentro del mismo periodo
            // buscar estadoacademico cursando o no_curso, dar de baja las inscripciones y generar inscripcionse nuevas
            $this->oConnection->join("matriculas_inscripciones", "matriculas_inscripciones.cod_estado_academico = estadoacademico.codigo");
            $this->oConnection->where("matriculas_inscripciones.cod_comision", $comisionOrigen);
            $this->oConnection->where("estadoacademico.cod_matricula_periodo", $this->codigo);
            $this->oConnection->where_in("estadoacademico.estado", array(Vestadoacademico::getEstadoCursando(), Vestadoacademico::getEstadoNoCursado()));
            $arrEstadoAcademico = Vestadoacademico::listarEstadoacademico($this->oConnection);
            foreach ($arrEstadoAcademico as $estadoAcademico) {
                $codigo = $estadoAcademico['codigo'];
                $myInscripcion = new Vmatriculas_inscripciones($this->oConnection);
                $myInscripcion->guardar($codigo, $codComision, $idUsuarioCreador, $fechaDesde, 'cambio_comision'); // la function guardar debería retornar true o false pero no lo hace
            }
        } else { // cambia a una comision del siguiente año
            // buscar el periodo siguiente si existe, buscar los estados academicos de esta nueva matricula periodo, 
            // realizar las inscripciones de matriculas_inscripciones
            // cambiar el estado de estadoacademico a cursando para la matricula_periodo que se inscribe
            $myMatricula = new Vmatriculas($this->oConnection, $this->cod_matricula);
            $estados = array(Vmatriculas_periodos::getEstadoHabilitada(), Vmatriculas_periodos::getEstadoCertificada(), Vmatriculas_periodos::getEstadoFinalizada());
            $arrPeriodos = $myMatricula->getPeriodosMatricula($estados, $myComision->cod_tipo_periodo);
            if (count($arrPeriodos) > 0) {
                $codMatriculaPeriodo = $arrPeriodos[0]['codigo'];
                $arrEstadoAcademico = Vestadoacademico::listarEstadoacademico($this->oConnection, array("cod_matricula_periodo" => $codMatriculaPeriodo));
                foreach ($arrEstadoAcademico as $estadoAcademico) {
                    $myEstadoAcademico = new Vestadoacademico($this->oConnection, $estadoAcademico['codigo']);
                    $myEstadoAcademico->guardarCambioEstado(Vestadoacademico::getEstadoCursando(), $idUsuarioCreador, 3, null);
                    $myInscripcion = new Vmatriculas_inscripciones($this->oConnection);
                    $myInscripcion->guardar($estadoAcademico['codigo'], $codComision, $idUsuarioCreador, $fechaDesde, 'cambio_comision'); // la function guardar debería retornar true o false pero no lo hace
                }
                $this->oConnection->where("cod_matricula_periodo", $codMatriculaPeriodo);
            }
            $this->oConnection->where("estado", "no_curso");
            $resp = $resp && $this->oConnection->update("estadoacademico", array("estado" => Vestadoacademico::getEstadoCursando()));
        }
        return $resp;
    }
    
    public function tieneDeudasAcademicas($validarParaAnioPeriodo = false){
        $myMatricula = new Vmatriculas($this->oConnection, $this->cod_matricula);
        $arrEstados = array(Vmatriculas_periodos::getEstadoCertificada(), Vmatriculas_periodos::getEstadoFinalizada(), Vmatriculas_periodos::getEstadoHabilitada(), Vmatriculas_periodos::getEstadoInhabilitada());
        $arrPeriodos = $myMatricula->getPeriodosMatricula($arrEstados);
        $cantidadPeriodos = count($arrPeriodos);
        if ($cantidadPeriodos > 1 && $this->cod_tipo_periodo == 1 && $validarParaAnioPeriodo){
            /* calculo las deudas por anio de forma proporcional cantidad cuotas anio a cursar */
            $this->oConnection->select("MAX(nrocuota) AS cantidad_cuotas", false);
            $this->oConnection->from("ctacte");
            $this->oConnection->where("concepto", $this->cod_matricula);
            $this->oConnection->where_in("cod_concepto", array(1, 2, 4, 5));
            $this->oConnection->where_in("habilitado", array(1, 2));
            $query = $this->oConnection->get();
            $arrCuotas = $query->result_array();
            if (isset($arrCuotas[0], $arrCuotas[0]['cantidad_cuotas'])){
                $cuota = (integer)($arrCuotas[0]['cantidad_cuotas'] / $cantidadPeriodos); // 
                $this->oConnection->where("ctacte.nrocuota <=", $cuota);   // ejemplo: si son 26 cuotas, 13 pertenecen a primer anio y 13 a segundo             
            }
        }
        $this->oConnection->select("ctacte.codigo");
        $this->oConnection->from("ctacte");
        $this->oConnection->where("concepto", $this->cod_matricula);
        $this->oConnection->where_in("cod_concepto", array(1, 2, 4, 5));
        $this->oConnection->where_in("habilitado", array(1, 2));
        $this->oConnection->where("pagado < importe");
        $this->oConnection->where("fechavenc < '".date('Y-m-d')."'");
        $this->oConnection->where("financiacion IN (SELECT MAX(ct1.financiacion) FROM ctacte AS ct1 WHERE ct1.cod_alumno = ctacte.cod_alumno AND ct1.concepto = ctacte.concepto)");
        $query = $this->oConnection->get();
        $arrCtacte = $query->result_array();
        return count($arrCtacte) > 0;
    }
    
    public function getFechaInicioFin(){
        $this->oConnection->select("MIN(dia) AS fecha_inicio", false);
        $this->oConnection->select("MAX(dia) AS fecha_fin", false);
        $this->oConnection->from("horarios");
        $this->oConnection->join("matriculas_inscripciones", "matriculas_inscripciones.cod_comision = horarios.cod_comision");
        $this->oConnection->join("estadoacademico", "estadoacademico.codigo = matriculas_inscripciones.cod_estado_academico");
        $this->oConnection->where("horarios.baja = 0");
        $this->oConnection->where("estadoacademico.cod_matricula_periodo", $this->codigo);
        $query = $this->oConnection->get();
        $arrResp = $query->result_array();
        return isset($arrResp[0]) ? $arrResp[0] : array();
    }

    /**
     * Funcion para obtener todos los estados academicos relacionados con una matricula periodo
     */
    public function getEstadosAcademicos(){
        $conexion = $this->oConnection;
        $conexion->select('*');
        $conexion->select('estado');
        $conexion->from('estadoacademico');
        $conexion->where('cod_matricula_periodo ='. $this->codigo);
        $query = $conexion->get();
        $arrResp = $query->result_array();
        return $arrResp;
    }

    /**
     * @param $estados
     * @return bool true se tiene estado los estados, false se tiene alguno estadoacademico con estado distincto
     */
    public function todosEstadosAcademicosTieneEstado($estados) {
        $conexion = $this->oConnection;
        $conexion->select('count(*) as cant');
        $conexion->from('estadoacademico ea');
        $conexion->join('matriculas_periodos mp', 'mp.codigo = ea.cod_matricula_periodo');
        $conexion->where_not_in('ea.estado', $estados);
        $conexion->where('mp.codigo', $this->codigo);
        $query = $conexion->get();
        $result = $query->result_array();
        if(isset($result[0]['cant']) && $result[0]['cant'] > 0) {
            return false;
        }
        return true;
    }

    public function getEstadosAcademicosConPorcAsisMayor($porc) {
        $conexion = $this->oConnection;
        $conexion->select('ea.*');
        $conexion->from('estadoacademico ea');
        $conexion->join('matriculas_periodos mp', 'mp.codigo = ea.cod_matricula_periodo');
        $conexion->where('mp.codigo', $this->codigo);
        $conexion->where('ea.porcasistencia >=', $porc);
        $query = $conexion->get();
        $result = $query->result_array();
        return $result;
    }
}
<?php

/**
 * Class Vmatriculas
 *
 * Class  Vmatriculas maneja todos los aspectos de matriculas
 *
 * @package  SistemaIGA
 * @subpackage Matriculas
 * @author   Ivan berthillod <ivan.sys@gmail.com>
 * @author   Aquiles Gonzalez <sistemas1@iga-la.net>
 * @version  $Revision: 1.1 $
 * @access   private
 */
class Vmatriculas extends Tmatriculas {

    static private $estadohabilitada = "habilitada";
    static private $estadoinhabilitada = "inhabilitada";
    static private $estadocertificada = "certificada";
    static private $estadofinalizada = "finalizada";
    static private $estadoPrematricula = 'prematricula';

    function __construct(CI_DB_mysqli_driver $conexion, $codigo = null) {
        parent::__construct($conexion, $codigo);
    }
    
    static function getEstadoPrematricula(){
        return self::$estadoPrematricula;
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




    /**
     * retorna lista de matriculas
     * @access public
     * @return array de matriculas
     */


    static function getBoletosRematriculacion($conexion, $matriculas, $desde, $hasta, $filial){
        $condicionTrimestre = "ctacte.fechavenc between '$desde' and '$hasta'";
        $conexion->select("boletos_bancarios.codigo");
        $conexion->from("bancos.boletos_bancarios");
        $conexion->where("cod_filial = $filial");
        $conexion->where("numero_documento in (
                              SELECT codigo FROM `$filial`.ctacte
                              WHERE cod_concepto = 1
                              AND concepto in (". implode(',', $matriculas) . ")
                              AND $condicionTrimestre
                          )");
        $query = $conexion->get();
        return $query->result_array();
    }

    static function listarRematriculacionesDataTable(CI_DB_mysqli_driver $conexion, $fechaDesde, $fechaHasta, $comision, $separador, $arrCondindicioneslike, $arrLimit, $arrSort, $contar = false) {
        $conexion->select("`matriculas`.cod_alumno as codigo_alumno");
        $conexion->select("`matriculas`.codigo as matricula");
        $conexion->select("`alumnos`.documento as documento");
        $conexion->select("`matriculas`.fecha_emision as fecha_matricula");
        $conexion->select("CONCAT_WS(' ', `alumnos`.`nombre`, `alumnos`.`apellido`) as nombre_alumno", false);
        $conexion->from("matriculas_inscripciones");
        $conexion->join("estadoacademico","matriculas_inscripciones.cod_estado_academico = estadoacademico.codigo", false);
        $conexion->join("matriculas_periodos","matriculas_periodos.codigo = estadoacademico.cod_matricula_periodo", false);
        $conexion->join("matriculas", "matriculas.codigo = matriculas_periodos.cod_matricula", false);
        $conexion->join("alumnos","alumnos.codigo = matriculas.cod_alumno", false);
        $conexion->where("`matriculas_inscripciones`.`cod_comision` = $comision");
        $conexion->where("`matriculas_inscripciones`.`baja` = 0");
        $conexion->group_by('matricula');

        if (count($arrCondindicioneslike) > 0) {
            foreach ($arrCondindicioneslike as $key => $value) {
                if ($key == 'nombre_apellido') {
                    $arrTemp[] = "REPLACE(nombre_apellido, '$separador ',' ') LIKE REPLACE('%$value%', '$separador ',' ')";
                } else {
                    $arrTemp[] = "$key LIKE '%$value%'";
                }

            }
            if (count($arrTemp) > 0) {
                $having = "(" . implode(" OR ", $arrTemp) . ")";
                $conexion->having($having);
            }
        }

        if ($arrLimit != null) {
            $conexion->limit($arrLimit[1], $arrLimit[0]);
        }


        if ($arrSort != null) {
            $conexion->order_by($arrSort["0"], $arrSort["1"]);
        }

        if ($contar) {
            $query = $conexion->return_query();
            $conexion->resetear();
            $conexion->select('COUNT(query.codigo_alumno) as numero from (' . $query . ') as query', false);
            $query2 = $conexion->get();
            $cantidad = $query2->result_array();
            return $cantidad[0]['numero'];
        } else {
            $query = $conexion->get();
            return $query->result_array();
        }
    }




    /**
     * retorna lista de matriculas
     * @access public
     * @return array de matriculas
     */
    static function listarMatriculaDataTable(CI_DB_mysqli_driver $conexion, $separador, $arrCondindicioneslike, $arrLimit, $arrSort, $contar = false) {
        $nombreApellido = formatearNomApeQuery();
        $conexion->select('alumnos.codigo as cod_alumno');
        $conexion->select('matriculas.codigo as cod_matricula');
        $conexion->select("CONCAT($nombreApellido) as nombre_apellido, general.planes_academicos.cod_curso, matriculas.cod_plan_academico, "
                . "CONCAT(LPAD(DAY(matriculas.fecha_emision), 2, 0), '/', LPAD(MONTH(matriculas.fecha_emision), 2, 0), '/', YEAR(matriculas.fecha_emision)) AS fecha_emision, matriculas.estado, GROUP_CONCAT(matriculas_periodos.cod_tipo_periodo)  AS periodos_matriculada, "
                . "GROUP_CONCAT(matriculas_periodos.estado) AS estados_periodos", false);
        $conexion->select("IFNULL(COUNT(matriculas_comentarios.comentario),0) as observaciones", false);
        $conexion->from('matriculas');
        $conexion->join('matriculas_comentarios', 'matriculas_comentarios.cod_alumno = matriculas.cod_alumno 
                                                   AND matriculas_comentarios.cod_plan_academico = matriculas.cod_plan_academico 
                                                   AND baja = 0', 'left');
        $conexion->join('matriculas_periodos', 'matriculas_periodos.cod_matricula = matriculas.codigo');
        $conexion->join('alumnos', 'alumnos.codigo = matriculas.cod_alumno');
        $conexion->join('general.planes_academicos', 'general.planes_academicos.codigo = matriculas.cod_plan_academico');
        $conexion->where('matriculas.estado <>', 'migrado');
        $conexion->where('matriculas_periodos.estado <>', 'migrado');
        $conexion->group_by('general.planes_academicos.cod_curso');
        $conexion->group_by('alumnos.codigo');
        if (count($arrCondindicioneslike) > 0) {
            foreach ($arrCondindicioneslike as $key => $value) {
                if ($key == 'nombre_apellido') {
                    $arrTemp[] = "REPLACE(nombre_apellido, '$separador ',' ') LIKE REPLACE('%$value%', '$separador ',' ')";
                } else {
                    $arrTemp[] = "$key LIKE '%$value%'";
                }
            }
            if (count($arrTemp) > 0) {
                $having = "(" . implode(" OR ", $arrTemp) . ")";
                $conexion->having($having);
            }
        }

        if ($arrLimit != null) {
            $conexion->limit($arrLimit[1], $arrLimit[0]);
        }

        if ($arrSort != null) {
            $conexion->order_by($arrSort["0"], $arrSort["1"]);
        }

        if ($contar) {
            $query = $conexion->return_query();
            $conexion->resetear();
            $conexion->select('COUNT(query.cod_alumno) as numero from (' . $query . ') as query', false);
            $query2 = $conexion->get();
            $cantidad = $query2->result_array();
            return $cantidad[0]['numero'];
        } else {
            $query = $conexion->get();
            return $query->result_array();
        }
    }

    /* La siguiente function esta siendo accedida desde un Web Services NO MODICAR, ELIMINAR NI MODIFICAR */

    public function reinsertarCertificado($codTipoPeriodo, $codCertificante) {
        $arrPeriodos = $this->getPeriodosMatricula(null, $codTipoPeriodo);
        $codMatriculaPeriodo = $arrPeriodos[0]['codigo'];
        $myCertificado = new Vcertificados($this->oConnection, $codMatriculaPeriodo, $codCertificante);
        return $myCertificado->setPendienteAprobar(null, null, 9);
    }

    public function getClases(){
        $this->oConnection->select("clases.*", false);
        $this->oConnection->from("matriculas_clases");
        $this->oConnection->join("general.clases", "general.clases.id = matriculas_clases.id_clase");
        $this->oConnection->where("matriculas_clases.id_matricula", $this->codigo);
        $query = $this->oConnection->get();
        return $query->result_array();
    }
    
    public function getCantidadClasesInscriptas(){
        $filial = $this->oConnection->database;
        $this->oConnection->select("COUNT(matriculas_clases.id_clase)", false);
        $this->oConnection->from("matriculas_clases");
        $this->oConnection->where("matriculas_clases.id_matricula", $this->codigo);
        $sqClasesInscriptas = $this->oConnection->return_query();
        $this->oConnection->resetear();
        $this->oConnection->select("COUNT(id) AS clases_disponibles", false);
        $this->oConnection->select("($sqClasesInscriptas) AS clases_inscriptas");
        $this->oConnection->from("general.clases");
        $this->oConnection->where("general.clases.id_plan_academico", $this->cod_plan_academico);
        $this->oConnection->where("general.clases.id_filial", $filial);
        $query = $this->oConnection->get();
        $arrTemp = $query->result_array();
        $arrResp = array();
        if (isset($arrTemp[0], $arrTemp[0]['clases_disponibles'])){
            $arrResp = $arrTemp[0];
        } else {
            $arrResp = array("clases_disponibles" => 0, "clases_inscriptas" => 0);
        }
        return $arrResp;
        /*
         select count(id) AS clases_disponibles,
(select count(matriculas_clases.id_clase) from matriculas_clases where matriculas_clases.id_matricula = 2001) AS clases_inscriptas
 from general.clases where id_plan_academico = 6 and id_filial = 90;
         */
    }
    
    public function asociarCupon($numeroDeCupon){
        $arrCupon = Vcupones::listarCupones($this->oConnection, array("codigo" => $numeroDeCupon));
        if (count($arrCupon) > 0){
            $idCupon = $arrCupon[0]['id'];
            $myCupon = new Vcupones($this->oConnection, $idCupon);
            $myCupon->estado = 'concretado';
            $resp = $myCupon->guardarCupones();
            $arrField = array(
                "codigo_cupon" => $myCupon->getCodigo(),
                "cod_matricula" => $this->codigo
            );
            return $resp && $this->oConnection->insert("cupones_canje", $arrField);
        } else {
            return false;
        }
    }
    
    
    /**
     * matricula al alumno
     * @access public
     * @return 
     */
    public function matricular($arrmatricula, $modelCobros = null) {
        //
        if ($arrmatricula['cobrarmatricula'] == "true") {
            $arrmatricula['cobrarmatricula'] = true;
        } else {
            $arrmatricula['cobrarmatricula'] = false;
        }

        //datos a pasar a matricula
        $codigoUsuario = $arrmatricula['cod_usuario_creador'];
        $fechaCreacion = date("Y-m-d H:i:s");
        $estado = $arrmatricula['medio_pago'] == 2 ? Vmatriculas::getEstadoPrematricula() : Vmatriculas::getEstadoHabilitada(); // solo las matriculas con medio de pago boleto son prematriculas (el resto se paga la matricula en el momento)
        $arrMatricular = array('cod_alumno' => $arrmatricula['cod_alumno'],
            'fecha_emision' => $fechaCreacion,
            'observaciones' => trim($arrmatricula['observaciones']),
            'cod_plan_pago' => $arrmatricula['ctacte']['cod_plan'],
            'estado' => $estado,
            'cod_plan_academico' => $arrmatricula['cod_plan_academico'],
            'usuario_creador' => $arrmatricula['cod_usuario_creador']
        );
        
        //GUARDO MATRICULA
        $this->setMatriculas($arrMatricular);
        $this->guardarMatriculas();
        $fechaemision = $this->fecha_emision;
        if (trim($arrmatricula['observaciones']) != '') {
            $arrMatComentario = array(
                'fecha_hora' => date("Y-m-d H:i:s"),
                'comentario' => $arrmatricula['observaciones'],
                'usuario_creador' => $arrmatricula['cod_usuario_creador'],
                "cod_alumno" => $arrmatricula['cod_alumno'],
                "cod_plan_academico" => $arrmatricula['cod_plan_academico'],
                "baja" => 0,
                "cod_matricula" => $this->getCodigo()
            );
            $matriculaComentario = new Vmatriculas_comentarios($this->oConnection);
            $matriculaComentario->setMatriculas_comentarios($arrMatComentario);
            $matriculaComentario->guardarMatriculas_comentarios();
        }
        //GUARDO CTA CTE
        $periodo = $arrmatricula['ctacte']['periodo']['valor'] . ' ' . $arrmatricula['ctacte']['periodo']['unidadTiempo'];
        foreach ($arrmatricula['ctacte']['financiaciones'] as $financiacion) {
            $condiciones = array(
                'codigo_plan' => $arrmatricula['ctacte']['cod_plan'],
                'codigo_concepto' => $financiacion['cod_concepto'],
                'codigo_financiacion' => $financiacion['cod_financiacion']
            );

            $planfinanciacion = Vplanes_financiacion::listarPlanes_financiacion($this->oConnection, $condiciones);
            $myPlan = new Vplanes_pago($this->oConnection, $arrmatricula['ctacte']['cod_plan']);
            $plandesc = $myPlan->getPlanFinanciacionDescuento($financiacion['cod_financiacion'], $financiacion['cod_concepto']);
            $descuento = count($plandesc) > 0 ? $plandesc[0]['descuento'] : 0;
            $limite = count($plandesc) > 0 ? $plandesc[0]['limite_primer_cuota'] : 'sin_fecha_limite';
            $vencimiento = $financiacion['fecha_primer_pago'] == null ? date('Y-m-d') : $financiacion['fecha_primer_pago'];
            foreach ($planfinanciacion as $rowfinanciacion) {
                $venciminetotest = strtotime($vencimiento);
                if ($rowfinanciacion['nro_cuota'] == 1) {
                    $vencimiento = $limite != 'al_momento' ? $financiacion['fecha_primer_pago'] : date('Y-m-d');
                } else {
                    $vencimiento = date("Y-m-d", strtotime('+' . $periodo, $venciminetotest));
                }
                //Si hay fechas modificadas desde el front (ver-planes)
                if($financiacion['cod_concepto'] == 1 && count($arrmatricula['filas_vencimientos']) > 0 && $arrmatricula['filas_vencimientos'][0] != '')
                {
                    $vencimiento = date("Y-m-d", strtotime(explode('/',$arrmatricula['filas_vencimientos'][$rowfinanciacion['nro_cuota']])[2].'-'.explode('/',$arrmatricula['filas_vencimientos'][$rowfinanciacion['nro_cuota']])[1].'-'.explode('/',$arrmatricula['filas_vencimientos'][$rowfinanciacion['nro_cuota']])[0]));
                }

                $vencimientoValido = getPrimerFechaHabil($this->oConnection, $vencimiento);
                $ctacte = new Vctacte($this->oConnection);
                $ctacte->guardar($this->cod_alumno, $rowfinanciacion['nro_cuota'], $rowfinanciacion['valor'], $vencimientoValido, 1, 0, $financiacion['cod_concepto'], $this->codigo, 1);
                if ($arrmatricula['medio_pago'] <> 2 && $rowfinanciacion['nro_cuota'] == 1 && $financiacion['cod_concepto'] == 5 && $arrmatricula['cobrarmatricula'] == true){ // matriculas no realizadas en medio pago que no sea boleto bancario deben abonar la primer cuota del concepto matricula en el momento (pedido por Alejandro)
                    $data_post['cobrar']['codigo'] = -1;
                    $data_post['cobrar']['fecha_cobro'] = date('Y-m-d');
                    $data_post['cobrar']['cod_alumno'] = $ctacte->cod_alumno;
                    $myAlumno = new Valumnos($this->oConnection, $ctacte->cod_alumno);
                    $arrRazones = $myAlumno->getRazonSocialDefault();
                    $data_post['cobrar']['razones_sociales'] = $arrRazones[0]['cod_razon_social'];
                    $data_post['cobrar']['checkctacte'] = array($ctacte->getCodigo());
                    $data_post['cobrar']['total_cobrar'] = $ctacte->importe;
                    $data_post['cobrar']['medio_cobro'] = $arrmatricula['medio_pago'];
                    $data_post['cobrar']['cod_usuario'] = $arrmatricula['cod_usuario_creador'];
                    $data_post['cobrar']['caja'] = $arrmatricula['pago']['cod_caja'];
                    $data_post['cobrar']['estado'] = '1';
                    $data_post['medio_cobro'] = $arrmatricula['pago']['medio_cobro'];
                    $modelCobros->guardarCobro($data_post, $this->oConnection);
                }
                if ($descuento == 0) {
                    //    $matctacte = array('cod_ctacte' => $ctacte->getCodigo(), "descuento" => $descuento);
                } else {
                    $dias = 0;
                    if ($myPlan->descon == 1) {
                        $tipoDescuento = "condicionado";
                        $conf_dto = Vconfiguracion::getValorConfiguracion($this->oConnection, null, 'descuentosCondicionados');
                        $dias = $conf_dto['dias_prorroga'];
                    } else {
                        $tipoDescuento = "no_condicionado";
                    }
                    
                    $importeDescontado = $descuento < 100 ? $rowfinanciacion['valor'] * 100 / (100 - $descuento) : 0;
                    $matctacte = array(
                        'cod_ctacte' => $ctacte->getCodigo(), 
                        "descuento" => $descuento, 
                        "estado" => $tipoDescuento, 
                        "dias_vencido" => $dias,
                        "cod_usuario" => $codigoUsuario,
                        "fecha" => $fechaCreacion,
                        "forma_descuento" => "plan_pago",
                        "activo" => "1",
                        "importe" => $importeDescontado
                    );
                    $this->oConnection->insert('matriculaciones_ctacte_descuento', $matctacte);
                }
            }
        }
//        die("si paso por aqui todo estubo OK");
        //GUARDO MATRICULA PERIODO
        foreach ($arrmatricula['periodos'] as $key => $value) {
            $periodo = isset($value['seleccionado']) ? $key : '';
            if ($periodo != '') {
                $comision = isset($value['comision']) && $value['comision'] != '' ? $value['comision'] : '';
                $modalidad = isset($value['modalidad']) && $value['modalidad'] != '' ? $value['modalidad'] : ''; //buscar la unica modalidad si no la trae
                $matriculaperiodo = new Vmatriculas_periodos($this->oConnection);
                if ($comision != '') {
                    $matriculaperiodo->guardar($this->getCodigo(), $periodo, $arrmatricula['cod_usuario_creador'], $fechaemision, $arrmatricula['filial'], $modalidad, $comision);
                } else {
                    $matriculaperiodo->guardar($this->getCodigo(), $periodo, $arrmatricula['cod_usuario_creador'], $fechaemision, $arrmatricula['filial'], $modalidad);
                }
            }
        }
    }

    /**
     * guarda el canje de un cupon promocional.
     * @access public
     * @param int $cod_cupon codigo de cupon canje
     * @param int $cod_filial codigo de la filial que lo va a canjear.
     * @return boolean estado de la transac
     */
    public function setCupon($cod_cupon, $cod_filial) {
        $cupones = new Vcupones_canje($this->oConnection);
        $arrCamposValores = array(
            "codigo_cupon" => $cod_cupon,
            "cod_matricula" => $this->codigo,
            "cod_filial" => $cod_filial
        );
        $cupones->setCupones_canje($arrCamposValores);
        $cupones->guardarCupones_canje();
        $cupon = new Vcupones($this->oConnection, $cod_cupon);
        $cupon->estado = 'efectivo';
        $cupon->guardarCupones();
    }

    public function getAlumno() {
        return $alumno = new Valumnos($this->oConnection, $this->cod_alumno);
    }

    public function getHorarios(){
        $this->oConnection->select("horarios.codigo");
        $this->oConnection->select("horarios.dia");
        $this->oConnection->select("horarios.horadesde");
        $this->oConnection->select("horarios.horahasta");
        $this->oConnection->from("horarios");
        $this->oConnection->join("matriculas_horarios", "matriculas_horarios.cod_horario = horarios.codigo AND matriculas_horarios.baja = 0");
        $this->oConnection->join("estadoacademico", "estadoacademico.codigo = matriculas_horarios.cod_estado_academico");
        $this->oConnection->join("matriculas_periodos", "matriculas_periodos.codigo = estadoacademico.cod_matricula_periodo");
        $this->oConnection->where("matriculas_periodos.cod_matricula", $this->codigo);
        $this->oConnection->where("horarios.baja = 0");
        $this->oConnection->order_by("horarios.dia", "ASC");
        $query = $this->oConnection->get();
        return $query->result_array();
    }
    
    public function getCurso() {
        $this->oConnection->select('general.cursos.*', false);
        $this->oConnection->from('general.cursos');
        $this->oConnection->join("general.planes_academicos", "general.planes_academicos.cod_curso = general.cursos.codigo");
        $this->oConnection->where('general.planes_academicos.codigo', $this->cod_plan_academico);
        $query = $this->oConnection->get();
        return $query->result_array();
    }

    /* STATIC FUNCTIONS */

    /* La siguiente fnuction está siendo accedida desde un Web Services NO MODIFICAR, COMENTAR NI ELIMINAR */

    static function getReporteAlumnosActivos(CI_DB_mysqli_driver $conexion) {
        $conexion->select("cod_plan_academico");
        $conexion->select("cod_tipo_periodo");
        $conexion->select("modalidad");
        $conexion->select("nombre_categoria");
        $conexion->select("cod_categoria");
        $conexion->select("COUNT(codigo) AS cantidad");
        $conexion->from("(SELECT matriculas_periodos.codigo, 
                                    matriculas.cod_plan_academico, 
                                    matriculas_periodos.cod_tipo_periodo,
                                    matriculas_periodos.modalidad, 
                                    general.cursos_categorias.nombre AS nombre_categoria,
                                    general.cursos_categorias.id AS cod_categoria FROM estadoacademico 
                                INNER JOIN matriculas_inscripciones ON matriculas_inscripciones.cod_estado_academico = estadoacademico.codigo and matriculas_inscripciones.baja = 0
                                INNER JOIN comisiones ON comisiones.codigo = matriculas_inscripciones.cod_comision
                                INNER JOIN general.ciclos ON general.ciclos.codigo = comisiones.ciclo and general.ciclos.fecha_fin_ciclo > CURDATE()
                INNER JOIN matriculas_periodos ON matriculas_periodos.codigo = estadoacademico.cod_matricula_periodo 
                        AND matriculas_periodos.estado = 'habilitada'
                INNER JOIN matriculas ON matriculas.codigo = matriculas_periodos.cod_matricula
                                INNER JOIN general.planes_academicos ON general.planes_academicos.codigo = matriculas.cod_plan_academico
                INNER JOIN general.cursos ON general.cursos.codigo = general.planes_academicos.cod_curso
                INNER JOIN general.cursos_categorias ON general.cursos_categorias.id = general.cursos.cod_categoria
                            WHERE estadoacademico.estado = 'cursando'
                            GROUP BY estadoacademico.cod_matricula_periodo
                        ) AS tb1");
        $conexion->group_by("cod_plan_academico");
        $conexion->group_by("cod_tipo_periodo");
        $conexion->group_by("modalidad");
        $query = $conexion->get();
        $arrTemp = $query->result_array();
        $conexion->resetear();
        $conexion->select("tb1.cod_plan_academico");
        $conexion->select("COUNT(tb1.cod_plan_academico) AS cantidad");
        $conexion->from("(SELECT matriculas.cod_plan_academico,
                                (SELECT COUNT(matriculas_periodos.codigo) 
                                        FROM matriculas_periodos WHERE matriculas_periodos.cod_matricula = matriculas.codigo
                                    AND (SELECT count(estadoacademico.codigo) 
                                        FROM estadoacademico 
                                        INNER JOIN matriculas_inscripciones ON matriculas_inscripciones.cod_estado_academico = estadoacademico.codigo AND matriculas_inscripciones.baja = 0
                                        INNER JOIN comisiones ON comisiones.codigo = matriculas_inscripciones.cod_comision
                                        INNER JOIN general.ciclos ON general.ciclos.codigo = comisiones.ciclo and general.ciclos.fecha_fin_ciclo > CURDATE()
                                        WHERE estadoacademico.estado = 'cursando' AND
                                            estadoacademico.cod_matricula_periodo = matriculas_periodos.codigo) > 0
                                    AND matriculas_periodos.estado = 'habilitada'
                                ) AS cantidad_matriculas
                            FROM matriculas
                            HAVING cantidad_matriculas > 1) AS tb1");
        $conexion->group_by("tb1.cod_plan_academico");
        $query = $conexion->get();
        $arrCursandioDosPeriodos = $query->result_array();
        for ($i = 0; $i < count($arrTemp); $i++) {
            if ($arrTemp[$i]['cod_tipo_periodo'] == 1) { // buscar matriculas cursando 1ero y segundo
                $codPlanAcademico = $arrTemp[$i]['cod_plan_academico'];
                foreach ($arrCursandioDosPeriodos as $dosPeriodos) {
                    if ($dosPeriodos['cod_plan_academico'] == $codPlanAcademico) {
                        $arrTemp[$i]['cantidad'] -= $dosPeriodos['cantidad'];       // se restan al periodo 1 los que estan cursando tambien el periodo 2
                    }
                }
            }
        }
        return $arrTemp;
    }

    /* esta function esta siendo utilizada desde un web services */
    static function getReporteSeguimientoFiliales(CI_DB_mysqli_driver $conexion) {
        $anioAnterior = date("Y") - 1;
        $anioActual = date("Y");
        $conexion->select("general.cursos.nombre_es");
        $conexion->select("COUNT(general.planes_academicos.cod_curso) AS cantidad");
        $conexion->from("matriculas_periodos");
        $conexion->join("matriculas", "matriculas.codigo = matriculas_periodos.cod_matricula");
        $conexion->join("general.planes_academicos", "general.planes_academicos.codigo = matriculas.cod_plan_academico");
        $conexion->join("general.cursos", "general.cursos.codigo = general.planes_academicos.cod_curso");
        $conexion->where_in("matriculas.cod_plan_academico", array(1, 12));
        $conexion->where("matriculas.fecha_emision >=", "{$anioAnterior}-06-01");
        $conexion->where("matriculas.fecha_emision <", "{$anioActual}-07-01");
        $conexion->where("matriculas_periodos.cod_tipo_periodo", 1);
        $conexion->where("matriculas_periodos.estado", Vmatriculas_periodos::getEstadoHabilitada());
        $conexion->group_by("general.planes_academicos.cod_curso");
        $query = $conexion->get();
        return $query->result_array();
    }

    /* esta function esta siendo accedida desde un web services */
    static function getDeserciones(CI_DB_mysqli_driver $conexion, $fechaDesde = null) {
        $conexion->select("general.planes_academicos.cod_curso");
        $conexion->from("matriculas");
        $conexion->join('general.planes_academicos', 'general.planes_academicos.codigo = matriculas.cod_plan_academico');
        $conexion->join('matriculas_periodos', 'matriculas_periodos.cod_matricula = matriculas.codigo');
        $conexion->where("matriculas_periodos.codigo = matriculas_estado_historicos.cod_matricula_periodo");
        $subQuery = $conexion->return_query();
        $conexion->resetear();
        $conexion->select("($subQuery) AS cod_curso");
        $conexion->select("COUNT(codigo) AS cantidad");
        $conexion->select("DATE(fecha_hora) AS fecha");
        $conexion->from("matriculas_estado_historicos");
        $conexion->where("matriculas_estado_historicos.estado", "inhabilitada");
        if ($fechaDesde != null)
            $conexion->where("DATE(fecha_hora) >= '$fechaDesde'");
        $conexion->group_by("DATE(fecha_hora), cod_curso");
        $query = $conexion->get();
        $arrTemp = $query->result_array();
        return $arrTemp;
    }

    static function getMatriculasFechaCantidad(CI_DB_mysqli_driver $conexion, $fechaDesde = null) {
        $conexion->select("COUNT(matriculas.codigo) AS cantidad");
        $conexion->select("general.planes_academicos.cod_curso");
        $conexion->select("DATE(matriculas.fecha_emision) AS fecha_emision");
        $conexion->from("matriculas");
        $conexion->join("general.planes_academicos", "general.planes_academicos.codigo = matriculas.cod_plan_academico");
        if ($fechaDesde != null)
            $conexion->where(array("DATE(fecha_emision) >=" => $fechaDesde));
        $conexion->group_by(array("DATE(fecha_emision)", "cod_curso"));
        $query = $conexion->get();
        $arrTemp = $query->result_array();
        return $arrTemp;
    }

    static function getReporteMatriculas(CI_DB_mysqli_driver $conexion, $arrLimit = null, $arrSort = null, $contar = false, $search = null, array $searchFields = null, $fechaDesde = null, $fechaHasta = null, array $arrCursos = null, $estado = null) {
        $aColumns = array();
        $aColumns['codigo']['order'] = "matriculas.codigo";
        $aColumns['apellido']['order'] = "alumnos.apellido";
        $aColumns['nombre']['order'] = "alumnos.nombre";
        $aColumns['fecha_emision']['order'] = "matriculas.fecha_emision";
        $aColumns['curso_nombre_es']['order'] = "general.cursos.nombre_es";
        $aColumns['comision_nombre']['order'] = "comisiones.nombre";
        $aColumns['estado']['order'] = 'matriculas.estado';
        $aColumns['fecha_baja']['order'] = 'fecha_baja';
        $aColumns['talle']['order'] = 'general.talles.talle';
        $aColumns['codigo']['having'] = "matriculas.codigo";
        $aColumns['apellido']['having'] = "alumnos.apellido";
        $aColumns['nombre']['having'] = "alumnos.nombre";
        $aColumns['fecha_emision']['having'] = "fecha_emision";
        $aColumns['curso_nombre_es']['having'] = "general.cursos.nombre_es";
        $aColumns['comision_nombre']['having'] = "comision_nombre";
        $aColumns['estado']['having'] = 'estado';
        $aColumns['fecha_baja']['having'] = 'fecha_baja';
        $aColumns['talle']['having'] = 'general.talles.talle';

        $conexion->select("CONCAT(LPAD(DAY(max(fecha_hora)), 2, 0) , '/', LPAD(MONTH(MAX(fecha_hora)), 2, 0), '/', YEAR(MAX(fecha_hora)))", false);
        $conexion->from("matriculas_estado_historicos");
        $conexion->join("matriculas_periodos", 'matriculas_periodos.codigo = matriculas_estado_historicos.cod_matricula_periodo');
        $conexion->where(array("matriculas_estado_historicos.estado" => "inhabilitada"));
        $conexion->where("matriculas_periodos.cod_matricula = matriculas.codigo");
        $queryFechaBaja = $conexion->return_query();
        $conexion->resetear();

        $conexion->select("GROUP_CONCAT(DISTINCT comisiones.nombre)");
        $conexion->from("comisiones");
        $conexion->join("matriculas_inscripciones", 'matriculas_inscripciones.cod_comision = comisiones.codigo');
        $conexion->join("estadoacademico", 'estadoacademico.codigo = matriculas_inscripciones.cod_estado_academico');
        $conexion->join("matriculas_periodos", "matriculas_periodos.codigo = estadoacademico.cod_matricula_periodo");
        $conexion->where("matriculas_periodos.cod_matricula = matriculas.codigo");
        $queryComisiones = $conexion->return_query();
        $conexion->resetear();

        $conexion->select("matriculas.codigo");
        $conexion->select("alumnos.apellido");
        $conexion->select("alumnos.nombre");
        $conexion->select("CONCAT(LPAD(DAY(matriculas.fecha_emision), 2, 0), '/', LPAD(MONTH(matriculas.fecha_emision), 2, 0), '/', YEAR(matriculas.fecha_emision)) AS fecha_emision", false);
        $conexion->select("general.cursos.nombre_es AS curso_nombre_es");
        $conexion->select("($queryComisiones) AS comision_nombre");
        $conexion->select("IF(matriculas.estado = 'habilitada','" . lang('habilitada') . "',
                 IF(matriculas.estado = 'inhabilitada','" . lang('inhabilitada') . "',
                      IF(matriculas.estado = 'certificada','" . lang('certificada') . "',
                           IF(matriculas.estado = 'finalizada','" . lang('finalizada') . "',
                                    matriculas.estado)))) AS baja", false);
        $conexion->select("IF (matriculas.estado = 'inhabilitada', ($queryFechaBaja), '') AS fecha_baja", false);
        $conexion->select("general.talles.talle");
        $conexion->from("matriculas");
        $conexion->join("alumnos", "alumnos.codigo = matriculas.cod_alumno");
        $conexion->join("general.planes_academicos", "general.planes_academicos.codigo = matriculas.cod_plan_academico");
        $conexion->join("general.cursos", "general.cursos.codigo = general.planes_academicos.cod_curso");
        $conexion->join("general.talles", "general.talles.codigo = alumnos.id_talle", "left");
        if ($fechaDesde != null)
            $conexion->where("DATE(matriculas.fecha_emision) >=", $fechaDesde);
        if ($fechaHasta != null)
            $conexion->where("DATE(matriculas.fecha_emision) <=", $fechaHasta);
        if ($arrCursos != null)
            $conexion->where("general.planes_academicos.cod_curso IN (" . implode(",", $arrCursos) . ")");
        if ($estado !== null)
            $conexion->where("matriculas.estado =", $estado);
        if ($search != null) {
            foreach ($aColumns AS $key => $tableFields) {
                if ($searchFields == null || in_array($key, $searchFields)) {
                    $conexion->or_having($tableFields['having'] . " LIKE ", "%$search%");
                }
            }
        }
        if (!$contar) {
            if ($arrLimit != null && is_array($arrLimit))
                $conexion->limit($arrLimit[1], $arrLimit[0]);
            if ($arrSort != null && is_array($arrSort) && isset($aColumns[$arrSort[0]]['order']))
                $conexion->order_by($aColumns[$arrSort[0]]['order'], $arrSort[1]);
        }
        $query = $conexion->get();
        if ($contar)
            return $query->num_rows();
        else
            return $query->result_array();
    }

    public function setInhabilitada() {
        $this->estado = Vmatriculas::getEstadoInhabilitada();
        $respuesta = $this->guardarMatriculas();
        return $respuesta;
    }

    public function setHabilitada($altactacte = false) {
        $this->estado = Vmatriculas::getEstadoHabilitada();
        $respuesta = $this->guardarMatriculas();
        if ($altactacte) {
            $respuesta = $this->altaCtacteAcademica();
        }
        return $respuesta;
    }

    public function setFinalizada() {
        $this->estado = Vmatriculas::getEstadoFinalizada();
        $respuesta = $this->guardarMatriculas();
        return $respuesta;
    }

    public function setCertificada() {
        $this->estado = Vmatriculas::getEstadoCertificada();
        $respuesta = $this->guardarMatriculas();
        return $respuesta;
    }

    /**
     * Retorna las comisiones donde la matricula se encuentra inscripta
     * 
     * @return array
     */
    public function getComisiones() {
        $this->oConnection->select("comisiones.*");
        $this->oConnection->from("matriculas_inscripciones");
        $this->oConnection->join("estadoacademico", "estadoacademico.codigo = matriculas_inscripciones.cod_estado_academico");
        $this->oConnection->join("matriculas_periodos", "matriculas_periodos.codigo = estadoacademico.cod_matricula_periodo");
        $this->oConnection->join("comisiones", "comisiones.codigo = matriculas_inscripciones.cod_comision");
        $this->oConnection->where("matriculas_periodos.cod_matricula", $this->codigo);
        //Ticket 4788 -mmori- agrego condicion para traer solo comisiones activas
        
        $this->oConnection->where("matriculas_inscripciones.baja = 0");
        $this->oConnection->group_by("comisiones.codigo");
        $this->oConnection->order_by("comisiones.ciclo", "desc");
        $query = $this->oConnection->get();
        
        return $query->result_array();
    }

    /**
     * Retorna los registros de ctacte que se generaron al momento de matricular
     * 
     * @return array
     */
    public function getDetalleCtacteMatriculacion() {
        $this->oConnection->select("ctacte.*");
        $this->oConnection->select("conceptos.key");
        $this->oConnection->from("ctacte");
        $this->oConnection->join("conceptos", "conceptos.codigo = ctacte.cod_concepto");
        $this->oConnection->where("ctacte.concepto", $this->codigo);
        $this->oConnection->where("ctacte.cod_alumno", $this->cod_alumno);
        $this->oConnection->where('ctacte.cod_concepto IN (SELECT codigo FROM conceptos WHERE codigo IN (select conceptos.codigo_padre from conceptos where conceptos.key = "TIPO" and  conceptos.valor = "ACADEMICO"))');
        $this->oConnection->where('ctacte.habilitado IN (1,2)');
        $this->oConnection->order_by('ctacte.cod_concepto', 'desc');
        $this->oConnection->order_by('ctacte.fechavenc', 'asc');
        $query = $this->oConnection->get();
        return $query->result_array();
    }

    public function getPeriodosMatricula($estado = null, $codTipoPeriodo = null) 
    {
        if($this->codigo != -1)
        {
            $conexion = $this->oConnection;
        
            $conexion->select('matriculas_periodos.codigo, matriculas_periodos.cod_matricula, matriculas_periodos.fecha_emision, matriculas_periodos.estado, matriculas_periodos.cod_tipo_periodo, matriculas_periodos.modalidad');
            $conexion->select('(SELECT IFNULL((SELECT nombre_periodo FROM general.planes_academicos_filiales 
                    WHERE cod_plan_academico = ' . $this->cod_plan_academico . ' AND cod_tipo_periodo = matriculas_periodos.cod_tipo_periodo
                    AND cod_filial = ' . $conexion->database . ' AND modalidad = matriculas_periodos.modalidad) ,(SELECT general.tipos_periodos.nombre
                    FROM general.tipos_periodos WHERE codigo = matriculas_periodos.cod_tipo_periodo))) AS nombre', false);
            $conexion->from("matriculas_periodos");
            $conexion->where("matriculas_periodos.cod_matricula", $this->codigo);
            if ($estado !== null) {
                if (is_array($estado)){
                    $conexion->where_in("matriculas_periodos.estado", $estado);
                } else {
                    $conexion->where("matriculas_periodos.estado", $estado);
                }
            }
            if ($codTipoPeriodo != null) {
                $tipoFiltro = is_array($codTipoPeriodo) ? "where_in" : "where";
                $conexion->$tipoFiltro("matriculas_periodos.cod_tipo_periodo", $codTipoPeriodo);
            }
            $query = $conexion->get();
            return $query->result_array();
        }    
    }

    public function bajaCtaCte($motivo = null) {
        $condiciones = array('fechavenc >' => date('Y-m-d'), 'habilitado <' => 3, 'habilitado >' => 0, 'pagado' => 0);
        $ctacte = $this->getCtaCte(true, $condiciones);
        foreach ($ctacte as $rowctacte) {
            $cuenta = new Vctacte($this->oConnection, $rowctacte['codigo']);
            $cuenta->baja(true, $motivo);
        }
        $condiciones1 = array('fechavenc <=' => date('Y-m-d'), 'habilitado' => 1, 'pagado' => 0);
        $ctacte = $this->getCtaCte(true, $condiciones1);
        foreach ($ctacte as $rowctacte) {
            $cuenta = new Vctacte($this->oConnection, $rowctacte['codigo']);
            $cuenta->setPasiva(true, $motivo);
        }
        //deudas pasivas: si tienen algo pago pero no todo
        $condiciones3 = array('habilitado' => 1, 'pagado >' => 0, 'importe >' => 'pagado');
        $ctacte = $this->getCtaCte(true, $condiciones3);
        foreach ($ctacte as $rowctacte) {
            $cuenta = new Vctacte($this->oConnection, $rowctacte['codigo']);
            $cuenta->setPasiva(true, $motivo);
        }
        //cambiar moras para pasiva
        $moras = self::getMoras($this->oConnection, $this->codigo, array('1'));
        foreach ($moras as $mora) {
            $cuenta = new Vctacte($this->oConnection, $mora['codigo']);
            $cuenta->setPasiva(true, $motivo);
        }
    }

    public function altaCtaCte() {
        $condiciones = array('habilitado >=' => 0, 'habilitado <' => 3);
        $ctacte = $this->getCtaCte(null, $condiciones);
        foreach ($ctacte as $rowctacte) {
            $cuenta = new Vctacte($this->oConnection, $rowctacte['codigo']);
            $cuenta->alta();
        }
        $moras = self::getMoras($this->oConnection, $this->codigo, array('2'));
        foreach ($moras as $mora) {
            $cuenta = new Vctacte($this->oConnection, $mora['codigo']);
            $cuenta->alta();
        }
    }

    public function tieneDeudasAcademicas() {
        $conceptos = new Vconceptos($this->oConnection);
        $conceptosAcademicos = $conceptos->getConceptosAcademicos();
        $valores = array();
        foreach ($conceptosAcademicos as $conceptoAca) {
            $valores[] = $conceptoAca['codigo'];
        }

        $alumno = new Valumnos($this->oConnection, $this->cod_alumno);
        $condiciones = array('concepto' => $this->codigo);
        $wherein = array(array('campo' => 'cod_concepto', 'valores' => $valores));
        $ctacte = $alumno->getCtaCte(TRUE, $condiciones, $wherein, 1);
        foreach ($ctacte as $rowctacte) {
            if ($rowctacte['fechavenc'] < date('Y-m-d')) {
                return true;
            }
        }
        return false;
    }

    public function getCodCobroConceptoMatricula(){
        $this->oConnection->select("ctacte_imputaciones.cod_cobro");
        $this->oConnection->join("ctacte_imputaciones", "ctacte_imputaciones.cod_ctacte = ctacte.codigo");
        $this->oConnection->where("ctacte.cod_alumno", $this->cod_alumno);
        $this->oConnection->where("ctacte.cod_concepto", "5");
        $this->oConnection->where("ctacte.nrocuota", "1");
        $this->oConnection->where("ctacte.concepto", $this->codigo);
        $arrResp = Vctacte::listarCtacte($this->oConnection);
        $codCobro = isset($arrResp[0]) && isset($arrResp[0]['cod_cobro']) ? $arrResp[0]['cod_cobro'] : false;
        return $codCobro;
    }
    
    public function getCtaCte($debe = null, $condiciones = array(), $ultimafinanciacion = null) {
        $conceptos = new Vconceptos($this->oConnection);
        $conceptosAcademicos = $conceptos->getConceptosAcademicos();
        $valores = array();
        foreach ($conceptosAcademicos as $conceptoAca) {
            $valores[] = $conceptoAca['codigo'];
        }
        $wherein = array(array('campo' => 'cod_concepto', 'valores' => $valores));
        $condiciones['concepto'] = $this->codigo;
        $objalumno = new Valumnos($this->oConnection, $this->cod_alumno);
        $ctacte = $objalumno->getCtaCte($debe, $condiciones, $wherein, $ultimafinanciacion);
        return $ctacte;
    }

    public function cambiarEstado() {
        $contar = $this->getPeriodosMatricula();
        $arrHabilitadas = $this->getPeriodosMatricula(Vmatriculas::getEstadoHabilitada());
        if (count($arrHabilitadas) == count($contar)) {
            return $this->setHabilitada();
        }
        $arrInhabilitadas = $this->getPeriodosMatricula(Vmatriculas::getEstadoInhabilitada());
        if (count($arrInhabilitadas) == count($contar)) {
            return $this->setInhabilitada();
        }
        $arrFinalizadas = $this->getPeriodosMatricula(Vmatriculas::getEstadoFinalizada());
        if (count($arrFinalizadas) == count($contar)) {
            return $this->setFinalizada();
        }
        $arrCertificadas = $this->getPeriodosMatricula(Vmatriculas::getEstadoCertificada());
        if (count($arrCertificadas) == count($contar)) {
            return $this->setCertificada();
        }
        if (count($arrHabilitadas) > 0) {
            return $this->setHabilitada();
        }
    }

    public function existenMasNoInhabilitadas($arrMatPer = null) {
        $this->oConnection->select('*');
        $this->oConnection->from('matriculas_periodos');
        $this->oConnection->join('matriculas', 'matriculas_periodos.cod_matricula = matriculas.codigo');
        $this->oConnection->where('matriculas.codigo', $this->codigo);
        if ($arrMatPer != null) {
            $this->oConnection->where_not_in('matriculas_periodos.codigo', $arrMatPer);
        }
        $this->oConnection->where('matriculas_periodos.estado', Vmatriculas_periodos::getEstadoHabilitada());        
        $query = $this->oConnection->get();
        $resultado = $query->result_array();
        if (count($resultado) > 0) {
            return true;
        } else {
            return false;
        }
    }

    static function getMatriculasSugerenciaBajaGeneral(CI_DB_mysqli_driver $conexion, $cantCuotasVencidas, $cantMesesVencidas, $limitInf = null, $limitCant = null, $contar = false) {
        $query = "SELECT *,
            IF (tipo_motivo = 'ciclo_vencido', IF (motivo >= 3, 'alta', IF (motivo = 2, 'media', 'baja')), 
            IF (tipo_motivo = 'cuotas_vencidas', IF (motivo = $cantCuotasVencidas OR motivo = $cantCuotasVencidas + 1, 'baja', 'media'), 
            IF (tipo_motivo = 'meses_vencidas', IF (motivo = $cantMesesVencidas OR motivo = $cantMesesVencidas + 1, 'baja', 'media'), 'sin prioridad') )) AS prioridad
                    FROM (
            SELECT * FROM (
                            SELECT matriculas.codigo as cod_matricula, 
                                    alumnos.nombre, 
                                    alumnos.apellido, 
                                    general.planes_academicos.cod_curso, 
                                    (SELECT TIMESTAMPDIFF(MONTH,MIN(ctacte.fechavenc),CURDATE())
                                        FROM (`ctacte`)
                                        WHERE `ctacte`.`cod_concepto` IN (SELECT codigo FROM conceptos WHERE codigo IN (SELECT codigo_padre FROM conceptos WHERE valor = 'ACADEMICO'))
                                            AND `ctacte`.`concepto` = matriculas.codigo
                                            AND `ctacte`.`cod_alumno` = matriculas.cod_alumno
                                            AND `ctacte`.`importe` > ctacte.pagado
                                            AND `ctacte`.`habilitado` = 1
                                            AND `ctacte`.`fechavenc` < curdate()) as motivo,
                                    'meses_vencidas' AS tipo_motivo
                                FROM (`matriculas`)
                                JOIN `alumnos` ON `matriculas`.`cod_alumno` = `alumnos`.`codigo`
                                JOIN `general`.`planes_academicos` ON `general`.`planes_academicos`.`codigo` = `matriculas`.`cod_plan_academico`
                                JOIN `matriculas_periodos` ON `matriculas_periodos`.`cod_matricula` = `matriculas`.`codigo`
                                WHERE `matriculas_periodos`.`estado` =  'habilitada'
                                HAVING motivo >= $cantMesesVencidas
            ) as tb_meses_vencidas
                    UNION
            SELECT * FROM (
                            SELECT matriculas.codigo as cod_matricula, 
                                    alumnos.nombre, 
                                    alumnos.apellido, 
                                    general.planes_academicos.cod_curso, 
                                    (SELECT count(ctacte.codigo)
                                        FROM (`ctacte`)
                                        WHERE `ctacte`.`cod_concepto` IN (SELECT codigo FROM conceptos WHERE codigo IN (SELECT codigo_padre FROM conceptos WHERE valor = 'ACADEMICO'))
                                            AND `ctacte`.`concepto` = matriculas.codigo
                                            AND `ctacte`.`cod_alumno` = matriculas.cod_alumno
                                            AND `ctacte`.`importe` > ctacte.pagado
                                            AND `ctacte`.`habilitado` = 1
                                            AND `ctacte`.`fechavenc` < CURDATE()) AS motivo,
                                    'cuotas_vencidas' AS tipo_motivo
                                FROM (`matriculas`)
                                JOIN `alumnos` ON `matriculas`.`cod_alumno` = `alumnos`.`codigo`
                                JOIN `general`.`planes_academicos` ON `general`.`planes_academicos`.`codigo` = `matriculas`.`cod_plan_academico`
                                JOIN `matriculas_periodos` ON `matriculas_periodos`.`cod_matricula` = `matriculas`.`codigo`
                                WHERE `matriculas_periodos`.`estado` =  'habilitada'
                                GROUP BY `matriculas`.`codigo`
                                HAVING `motivo` >= $cantCuotasVencidas 
                        ) AS tb_cuotas_vencidas ".
//                   "UNION
//                         SELECT * FROM (
//                            SELECT matriculas.codigo AS cod_matricula, 
//                                    alumnos.nombre, 
//                                    alumnos.apellido, 
//                                    general.planes_academicos.cod_curso,
//                                    (SELECT TIMESTAMPDIFF(MONTH,MIN(general.ciclos.fecha_fin_ciclo),CURDATE())
//                                        FROM general.ciclos
//                                        INNER JOIN comisiones ON comisiones.ciclo = general.ciclos.codigo
//                                        INNER JOIN matriculas_inscripciones ON matriculas_inscripciones.cod_comision = comisiones.codigo AND matriculas_inscripciones.baja = 0
//                                        INNER JOIN estadoacademico ON estadoacademico.codigo = matriculas_inscripciones.cod_estado_academico  AND estadoacademico.estado = 'cursando'
//                                        INNER JOIN matriculas_periodos ON matriculas_periodos.codigo = estadoacademico.cod_matricula_periodo AND matriculas_periodos.estado = 'habilitada'
//                                        WHERE matriculas_periodos.cod_matricula = matriculas.codigo) AS motivo,
//                                    'ciclo_vencido'
//                                FROM matriculas
//                                INNER JOIN alumnos ON alumnos.codigo = matriculas.cod_alumno
//                                INNER JOIN general.planes_academicos ON general.planes_academicos.codigo = matriculas.cod_plan_academico
//                                HAVING motivo >= 1
//                        ) AS tb_ciclo_vencido
//                        " .
            
            "ORDER BY motivo DESC
                    ) AS tabla_final
        ORDER BY FIELD(prioridad, 'alta', 'media', 'baja'), motivo DESC";
        if (!$contar && $limitInf != null && $limitCant != null)
            $query .= " LIMIT $limitInf, $limitCant";
        $query = $conexion->query($query);
        if ($contar) {
            return $query->num_rows();
        } else {
            return $query->result_array();
        }
    }

    static function getMatriculasSugerenciaBaja(CI_DB_mysqli_driver $conexion, $cantMesesConfig, $mesesvencida, $idioma, $arrLimit = null, $arrSort = null, $contar = false) {
        $conexion->select('count(ctacte.codigo)', false);
        $conexion->from('ctacte');
        $conexion->where("ctacte.cod_concepto IN (select codigo from conceptos where codigo in (select codigo_padre from conceptos where valor = 'ACADEMICO'))");
        $conexion->where("ctacte.concepto = matriculas.codigo");
        $conexion->where("ctacte.cod_alumno = matriculas.cod_alumno");
        $conexion->where("ctacte.importe  > ctacte.pagado");
        $conexion->where('ctacte.habilitado', 1);
        $conexion->where("ctacte.fechavenc < curdate()");
        $subQuery = $conexion->return_query();
        $conexion->resetear();
        $conexion->select('MIN(ctacte.fechavenc)', false);
        $conexion->from('ctacte');
        $conexion->where("ctacte.cod_concepto IN (select codigo from conceptos where codigo in (select codigo_padre from conceptos where valor = 'ACADEMICO'))");
        $conexion->where("ctacte.concepto = matriculas.codigo");
        $conexion->where("ctacte.cod_alumno = matriculas.cod_alumno");
        $conexion->where("ctacte.importe  > ctacte.pagado");
        $conexion->where('ctacte.habilitado', 1);
        $conexion->where("ctacte.fechavenc < curdate()");
        $subQuery2 = $conexion->return_query();
        $conexion->resetear();
        if (!$contar) {
            $conexion->select("matriculas.codigo as cod_matricula, alumnos.nombre, alumnos.apellido,general.planes_academicos.cod_curso", false);
            $conexion->join('alumnos', 'matriculas.cod_alumno = alumnos.codigo');
            $conexion->join('general.planes_academicos', 'general.planes_academicos.codigo = matriculas.cod_plan_academico');
            if ($arrLimit != null) {
                $conexion->limit($arrLimit[1], $arrLimit[0]);
            }
            if ($arrSort != null) {
                $conexion->order_by($arrSort["0"], $arrSort["1"]);
            }
        }
        $conexion->select("($subQuery) as cantMesesVencidos, ($subQuery2) as vencimientoviejo", false);
        $conexion->from('matriculas');
        $conexion->join('matriculas_periodos', 'matriculas_periodos.cod_matricula = matriculas.codigo');
        $conexion->where("matriculas_periodos.estado", Vmatriculas::getEstadoHabilitada());
        $conexion->group_by('matriculas.codigo');
        $conexion->having("cantMesesVencidos >=  $cantMesesConfig OR vencimientoviejo < DATE_ADD(curdate(), INTERVAL -" . $mesesvencida . " MONTH)");
        $query = $conexion->get();
        if ($contar) {
            return $query->num_rows();
        } else {
            return $query->result_array();
        }
    }

    public function baja($conexion = null, $codusuario = null, $bajactacte = true) {
        if ($conexion == null) {
            $conexion2 = $this->oConnection;
            $this->oConnection->trans_begin();
        } else {
            $conexion2 = $conexion;
        }
        $estado = Vmatriculas_periodos::getEstadoHabilitada();
        $matper = $this->getPeriodosMatricula($estado);
        foreach ($matper as $codmatper) {
            $objmatper = new Vmatriculas_periodos($conexion2, $codmatper['codigo']);
            $objmatper->baja(null, null, $codusuario);
        }
        if ($bajactacte) {
            $estadotran = $this->bajaCtaCte();
        }
        if ($conexion == null) {
            $estadotran = $conexion2->trans_status();
            if ($estadotran === FALSE) {
                $conexion2->trans_rollback();
            } else {
                $conexion2->trans_commit();
            }
        }
        return class_general::_generarRespuestaModelo($conexion2, $estadotran);
    }

    public function getTitulos($cod_periodo = null) {
        $conexion = $this->oConnection;

        $conexion->select('IFNULL((SELECT general.titulos.nombre FROM general.titulos 
                JOIN general.planes_academicos_filiales ON general.planes_academicos_filiales.cod_titulo = general.titulos.codigo 
                WHERE general.planes_academicos_filiales.cod_plan_academico = matriculas.cod_plan_academico AND general.planes_academicos_filiales.cod_filial = ' . $conexion->database . ' 
                AND general.planes_academicos_filiales.cod_tipo_periodo = matriculas_periodos.cod_tipo_periodo AND general.planes_academicos_filiales.modalidad = matriculas_periodos.modalidad
                ), (SELECT general.titulos.nombre FROM general.titulos 
                JOIN general.planes_academicos_periodos ON general.planes_academicos_periodos.cod_titulo = general.titulos.codigo
                WHERE planes_academicos_periodos.cod_tipo_periodo = matriculas_periodos.cod_tipo_periodo AND general.planes_academicos_periodos.cod_plan_academico =  matriculas.cod_plan_academico
                )) as titulo', false);
        $conexion->from("matriculas");
        $conexion->join("matriculas_periodos", "matriculas.codigo = matriculas_periodos.cod_matricula");
        $conexion->join("general.planes_academicos_periodos", "general.planes_academicos_periodos.cod_plan_academico = matriculas.cod_plan_academico AND general.planes_academicos_periodos.cod_tipo_periodo = matriculas_periodos.cod_tipo_periodo");
        $conexion->where("matriculas.codigo", $this->codigo);
        if ($cod_periodo !== null) {
            $conexion->where("matriculas_periodos.cod_tipo_periodo", $cod_periodo);
        }
        $conexion->order_by("general.planes_academicos_periodos.orden", "desc");
        $query = $conexion->get();
        return $query->result_array();
    }

    static function getNombrePeriodoModalidadCurso(CI_DB_mysqli_driver $conexion, $cod_plan_academico, $cod_tipo_periodo, $modalidad, $cod_filial) {
        $conexion->select('general.tipos_periodos.nombre');
        $conexion->from('general.tipos_periodos');
        $conexion->where('general.tipos_periodos.codigo = general.planes_academicos_filiales.cod_tipo_periodo');
        $subquery2 = $conexion->return_query();
        $conexion->resetear();
        $conexion->select("IFNULL(general.planes_academicos_filiales.nombre_periodo, ($subquery2)) as nombre_periodo", false);
        $conexion->select("general.planes_academicos_filiales.modalidad");
        $conexion->from('general.planes_academicos_filiales');
        $conexion->where('general.planes_academicos_filiales.cod_plan_academico', $cod_plan_academico);
        $conexion->where('general.planes_academicos_filiales.cod_tipo_periodo', $cod_tipo_periodo);
        $conexion->where('general.planes_academicos_filiales.modalidad', $modalidad);
        $conexion->where('general.planes_academicos_filiales.cod_filial', $cod_filial);
        $query = $conexion->get();
        return $query->result_array();
    }

    public function getUltimaCuota($concepto) {
        $this->oConnection->select('ctacte.nrocuota');
        $this->oConnection->from('ctacte');
        $this->oConnection->where('cod_concepto', $concepto);
        $this->oConnection->where('concepto', $this->codigo);
        $this->oConnection->where('habilitado IN (1,2)');
        $this->oConnection->order_by('nrocuota','desc');
        $query = $this->oConnection->get();
        $resultado = $query->result_array();
        $ultima = count($resultado) > 0 ? $resultado[0]['nrocuota'] : 0;
        return $ultima;
    }

    /**
     * Retorna los registros de descuento sobre un matricula (matriculaciones_ctacte_descuento)
     * 
     * @param string $estado            Estado del descuento (matriculaciones_ctacte_descuetno::estado)
     * @param boolean $ctacteSinPago    determina si el registro de ctacte al que pertenece el descuento debe poseer pago o no
     * @return array
     */
    public function getDescuentos($estado = null, $ctacteSinPago = null){
        $arrResp = Vmatriculaciones_ctacte_descuento::getDescuentosMatricula($this->oConnection, null, $estado, false, $this->codigo, $ctacteSinPago);
        return $arrResp;
    }
    
    /**
     * Recupera los descuentos condicionados de una matricula
     * 
     * @param integer $ctacteDesde      El codigo de ctacte desde el cual debe buscarse una fecha de recuperacion
     * @return boolean
     */
    public function recuperarDescuentoCondicionado($ctacteDesde = null){
        $arrDescuentos = $this->getDescuentos(Vmatriculaciones_ctacte_descuento::getEstadoCondicionadoPerdido());
        if ($ctacteDesde != null){
            $myCtacte = new Vctacte($this->oConnection, $ctacteDesde);
            $fechaDesde = $myCtacte->fechavenc;
        }
        $resp = true;
        foreach ($arrDescuentos as $descuento){
            $myDescuento = new Vmatriculaciones_ctacte_descuento($this->oConnection, $descuento['codigo_descuento']);
            if ($ctacteDesde != null){
                $myCtacteDescuento = new Vctacte($this->oConnection, $myDescuento->cod_ctacte);
            }
            if ($ctacteDesde != null && $myCtacteDescuento->fechavenc < $fechaDesde){
                $resp = $resp && $myDescuento->descartarCondicionado();
            } else {
                $resp = $resp && $myDescuento->recuperarCondicionadoPerdido();
            }
        }
        return $resp;
    }
    
    static function getReporteBajas(CI_DB_mysqli_driver $conexion, $clausulaFechas, $separador, $fechaDesde, $fechaHasta, $codCurso = null, $cod_plan_academico = null, $codigo_alumno = null, $titulo = null, $cod_tipo_periodo = null,  $cod_mat_periodo = null, $nombreApellido = '', $contar = false, $arrFiltros = null, $arrCondindiciones = null, $arrLimit=null, $arrSort=null)
    {
        $extencion = lang("_idioma");
        
        
        
        $conexion->select("matriculas.cod_alumno");
        $conexion->select("matriculas_periodos.codigo");
        $conexion->select("CONCAT($nombreApellido) as nombre_apellido", false);
        $conexion->select("matriculas_estado_historicos.fecha_hora");
        $conexion->select("general.cursos.nombre_$extencion AS nombre_curso");
        $conexion->select("general.cursos.codigo AS cod_curso_");
        $conexion->select("matriculas_estado_historicos.comentario AS comentario");
        $conexion->select("matriculas_estado_historicos.motivo AS motivo_id");
        $conexion->from("matriculas_periodos");
        $conexion->join("matriculas", "matriculas.codigo = matriculas_periodos.cod_matricula");
        $conexion->join("alumnos", "alumnos.codigo = matriculas.cod_alumno");
        
        $conexion->join("general.planes_academicos", "general.planes_academicos.codigo = matriculas.cod_plan_academico");
        $conexion->join("general.cursos", "general.cursos.codigo = general.planes_academicos.cod_curso");
        
        $conexion->join("matriculas_estado_historicos", "matriculas_estado_historicos.cod_matricula_periodo = matriculas_periodos.codigo");
        $conexion->where("matriculas_periodos.estado", "inhabilitada");
        
        if($cod_plan_academico != null)
        {    
            $conexion->where("matriculas.cod_plan_academico = " . $cod_plan_academico);
        }
        if($cod_tipo_periodo != null)
        {    
            $conexion->where("matriculas_periodos.cod_tipo_periodo = " . $cod_tipo_periodo);
        }    
        
        if (count($arrCondindiciones) > 0) {
            $arrTemp = array();
            foreach ($arrCondindiciones as $key => $value) {
                if ($key == 'nombre_apellido') {
                    $arrTemp[] = "REPLACE(nombre_apellido, '$separador ',' ') LIKE REPLACE('%$value%', '$separador ',' ')";
                } else {
                    $arrTemp[] = "$key LIKE '%$value%'";
                }
            }
            if (count($arrTemp) > 0) {
                $having = "(" . implode(" OR ", $arrTemp) . ")";
                $conexion->having($having);
            }
        }
        
        if($clausulaFechas != null)
        {
            switch ($clausulaFechas)
            {
                case "1":
                    
                    $fechaDesde = formatearFecha_mysql($fechaDesde);
                    $conexion->where("DATE(fecha_hora) >=", $fechaDesde);
                    
                break;
                
                case "2":
                    
                    $fechaDesde = formatearFecha_mysql($fechaDesde);
                    $conexion->where("DATE(fecha_hora) <=", $fechaDesde);
                    
                break;
                
                case "3":
                
                    //$fechaDesde = formatearFecha_mysql($fechaDesde);
                    $conexion->where("DATE(fecha_hora) >=", $fechaDesde);
                    
                    //$fechaHasta = formatearFecha_mysql($fechaHasta);
                    $conexion->where("DATE(fecha_hora) <=", $fechaHasta);
                    
                break;    
            
                case "4":
                    
                    $fechaDesde = formatearFecha_mysql($fechaDesde);
                    $conexion->where("DATE(matriculas_estado_historicos.fecha_hora) =", $fechaDesde);
                    
                break;
            }
        }
        
        if($codigo_alumno!=null)
        {
            $conexion->where("matriculas.cod_alumno = ", $codigo_alumno);
        }
        if($cod_mat_periodo!=null)
        {
            $conexion->where("matriculas_periodos.codigo = ", $cod_mat_periodo);
        }
        
        if ($arrLimit != null && $arrLimit[1] != -1) {
            $conexion->limit($arrLimit[1], $arrLimit[0]);
        }
        
        if($arrSort!=null)
        {
            $conexion->order_by($arrSort["0"], $arrSort["1"]);
        }
        
        if($codCurso!=null)
        {
            $conexion->having("cod_curso_ = ", $codCurso);
        }
        $conexion->group_by("matriculas.codigo");
        $query = $conexion->get();
        
        if ($contar) {
            //die($conexion->last_query());
            return $query->num_rows();
        } else {
            //die($conexion->last_query());
            //die('<pre>'.print_r($query->result_array()).'</pre>');
            return $query->result_array();
        }
    }

//    static function getReporteInscripcionesYBajas(CI_DB_mysqli_driver $conexion, $fechaDesde = null, $fechaHasta = null){
//        $codFilial = $conexion->database;
//        $conexion->select("COUNT(matriculas_periodos.codigo)", false);
//        $conexion->from("matriculas_periodos");
//        $conexion->join("matriculas", "matriculas.codigo = matriculas_periodos.cod_matricula AND matriculas.estado <> 'migrado'");
//        $conexion->where("matriculas_periodos.cod_tipo_periodo = general.planes_academicos_filiales.cod_tipo_periodo");
//        $conexion->where("general.planes_academicos_filiales.cod_plan_academico = matriculas.cod_plan_academico");
//        $conexion->where("matriculas_periodos.estado <>", "migrado");
//        if ($fechaDesde != null){
//            $conexion->where("DATE(matriculas.fecha_emision) >=", $fechaDesde);
//        }
//        if ($fechaHasta != null){
//            $conexion->where("DATE(matriculas.fecha_emision) <=", $fechaHasta);
//        }
//        $sqCantidadInscriptos = $conexion->return_query();
//        $conexion->resetear();
//        
//        $conexion->select("COUNT(matriculas_periodos.codigo)");
//        $conexion->from("matriculas_periodos");
//        $conexion->join("matriculas", "matriculas.codigo = matriculas_periodos.cod_matricula AND matriculas.estado = 'inhabilitada'");
//        $conexion->join("matriculas_estado_historicos", "matriculas_estado_historicos.cod_matricula_periodo = matriculas_periodos.codigo");
//        $conexion->where("matriculas_periodos.estado", "inhabilitada");
//        $conexion->where("matriculas.cod_plan_academico = general.planes_academicos_filiales.cod_plan_academico");
//        $conexion->where("matriculas_periodos.cod_tipo_periodo = general.planes_academicos_filiales.cod_tipo_periodo");
//        if ($fechaDesde != null){
//            $conexion->where("DATE(matriculas_estado_historicos.fecha_hora) >=", $fechaDesde);
//        }
//        if ($fechaHasta != null){
//            $conexion->where("DATE(matriculas_estado_historicos.fecha_hora) <=", $fechaHasta);
//        }
//        $sqCantidadBajas = $conexion->return_query();
//        $conexion->resetear();
//        
//        $conexion->select("general.titulos.codigo");
//        $conexion->select("general.titulos.nombre");
//        $conexion->select("general.planes_academicos.cod_curso");
//        $conexion->select("general.planes_academicos_filiales.cod_plan_academico");
//        $conexion->select("general.planes_academicos_filiales.cod_tipo_periodo");
//        $conexion->select("($sqCantidadInscriptos) AS cantidad_inscriptos", false);
//        $conexion->select("($sqCantidadBajas) AS cantidad_bajas", false);
//        $conexion->from("general.planes_academicos_filiales");
//        $conexion->join("general.titulos", "general.titulos.codigo = general.planes_academicos_filiales.cod_titulo");
//        $conexion->join("general.planes_academicos", "general.planes_academicos.codigo = general.planes_academicos_filiales.cod_plan_academico ".
//                "AND general.planes_academicos_filiales.cod_filial = $codFilial");
//        $conexion->group_by("general.titulos.codigo");
//        $conexion->having("(cantidad_bajas > 0 OR cantidad_inscriptos > 0)");
//        $query = $conexion->get();
//        echo $conexion->last_query();die();
//        return $query->result_array();
//    }
    
    static function getReporteInscripcionesYBajas(CI_DB_mysqli_driver $conexion, $fechaDesde = null, $fechaHasta = null){
        $conexion->select("COUNT(matriculas.codigo)", false);
        $conexion->from("matriculas");
        $conexion->where("matriculas.cod_plan_academico = general.planes_academicos.codigo");
        $conexion->where("matriculas.estado <>", "migrado");
        if ($fechaDesde != null){
            $conexion->where("DATE(matriculas.fecha_emision) >=", $fechaDesde);
        }
        if ($fechaHasta != null){
            $conexion->where("DATE(matriculas.fecha_emision) <=", $fechaHasta);
        }
        $sqInscriptos = $conexion->return_query();
        file_put_contents('/var/www/html/iga-cloud/logs', '-----------------------Inscriptos' . PHP_EOL, FILE_APPEND);
        file_put_contents('/var/www/html/iga-cloud/logs', $sqInscriptos . PHP_EOL, FILE_APPEND);
        $conexion->resetear();
        $conexion->select("COUNT(DISTINCT matriculas.codigo)", false);
        $conexion->from("matriculas_periodos");
        $conexion->join("matriculas", "matriculas.codigo = matriculas_periodos.cod_matricula AND matriculas.estado = 'inhabilitada'");
        $conexion->join("matriculas_estado_historicos", "matriculas_estado_historicos.cod_matricula_periodo = matriculas_periodos.codigo AND matriculas_estado_historicos.estado = 'inhabilitada'");
        $conexion->where("matriculas_periodos.estado", "inhabilitada");
        $conexion->where("matriculas.cod_plan_academico = general.planes_academicos.codigo");
        if ($fechaDesde != null){
            $conexion->where("DATE(matriculas_estado_historicos.fecha_hora) >=", $fechaDesde);
        }
        if ($fechaHasta != null){
            $conexion->where("DATE(matriculas_estado_historicos.fecha_hora) <=", $fechaHasta);
        }
        $sqBajas = $conexion->return_query();
        file_put_contents('/var/www/html/iga-cloud/logs', '-----------------------Bajas' . PHP_EOL, FILE_APPEND);
        file_put_contents('/var/www/html/iga-cloud/logs', $sqBajas . PHP_EOL, FILE_APPEND);
        $conexion->resetear();
        $conexion->select("general.planes_academicos.codigo");
        $conexion->select("general.planes_academicos.cod_curso");
        $conexion->select("general.cursos.nombre_es");
        $conexion->select("general.cursos.tipo_curso");
        $conexion->select("general.planes_academicos.nombre as plan");
        $conexion->select("($sqInscriptos) AS cantidad_inscriptos", false);
        $conexion->select("($sqBajas) AS cantidad_bajas", false);
        $conexion->from("general.planes_academicos");
        $conexion->join("general.cursos", "general.cursos.codigo = general.planes_academicos.cod_curso");
        $conexion->having("(cantidad_inscriptos > 0 OR cantidad_bajas > 0)");
        $conexion->order_by("general.cursos.nombre_es ASC");
        $query = $conexion->get();
        file_put_contents('/var/www/html/iga-cloud/logs', '-----------------------Query' . PHP_EOL, FILE_APPEND);
        file_put_contents('/var/www/html/iga-cloud/logs', $conexion->last_query() . PHP_EOL, FILE_APPEND);
        return $query->result_array();
    }
    
    /* documentacion entregada por el alumno */
    public function getDocumentacionEntragada(){
        $this->oConnection->select("documentacion");
        $this->oConnection->from("documentacion_alumnos");
        $this->oConnection->where("cod_matricula", $this->codigo);
        $query = $this->oConnection->get();
        $arrTemp = $query->result_array();
        $arrResp = array();
        foreach ($arrTemp as $temp){
            $arrResp[] = $temp['documentacion'];
        }
        return $arrResp;
    }
    
    public function setDocumentacionEntragada(array $doc){
        $this->oConnection->where("cod_matricula", $this->codigo);        
        $resp = $this->oConnection->delete("documentacion_alumnos");
        if (count($doc) > 0){
            foreach ($doc as $documentacion){
                $resp = $resp && $this->oConnection->insert('documentacion_alumnos', array('cod_matricula' => $this->codigo, 'documentacion' => $documentacion));
            }
        }
        return $resp;
    }
    
    
    /* Materiales de estudio entregados al alumno */
    public function getMaterialesEntregados(){
        $this->oConnection->select("id_material");
        $this->oConnection->from("materiales_alumnos");
        $this->oConnection->where("cod_matricula", $this->codigo);
        $query = $this->oConnection->get();
        $arrTemp = $query->result_array();
        $arrResp = array();
        foreach ($arrTemp as $temp){
            $arrResp[] = $temp['id_material'];
        }
        return $arrResp;        
    }
    
    public function setMaterialEntregado(array $doc){
        $this->oConnection->where("cod_matricula", $this->codigo);        
        $resp = $this->oConnection->delete("materiales_alumnos");
        if (count($doc) > 0){
            foreach ($doc as $material){
                $resp = $resp && $this->oConnection->insert('materiales_alumnos', array('cod_matricula' => $this->codigo, 'id_material' => $material));
            }
        }
        return $resp;
    }
    
    public function set_medio_pago_cuotas($codMedio){
        $this->oConnection->where("cod_matricula", $this->codigo);
        $resp = $this->oConnection->delete("matriculas_mediospago");
        return $resp && $this->oConnection->insert("matriculas_mediospago", array("cod_matricula" => $this->codigo, "cod_medio" => $codMedio));
    }
    
    public function get_medio_pago_cuotas(){
        $this->oConnection->select("cod_medio");
        $this->oConnection->from("matriculas_mediospago");
        $this->oConnection->where("cod_matricula", $this->codigo);
        $query = $this->oConnection->get();
        $arrResp = $query->result_array();
        return isset($arrResp[0], $arrResp[0]['cod_medio']) ? $arrResp[0]['cod_medio'] : false;
    }
    
    static function getPrematriculas(CI_DB_mysqli_driver $conexion, $horasDesde = null){
        if ($horasDesde != null){
            $conexion->where("fecha_emision <= DATE_ADD(now(), INTERVAL '-$horasDesde' HOUR)");
        }
        return self::listarMatriculas($conexion, array("estado" => "prematricula"));
    }


    static function getBoletosEmitidos($conexion, $matricula, $desde, $hasta, $filial){
        $conexion->resetear();
        $conexion->select('ctacte.*');
        $conexion->from('ctacte');
        $conexion->join('matriculas', "matriculas.cod_alumno = ctacte.cod_alumno AND ctacte.concepto = $matricula AND matriculas.codigo = $matricula");
        $conexion->where('cod_concepto', '1');
        $conexion->where("(ctacte.fechavenc between '$desde' and '$hasta')");
        $query = $conexion->get();
        $ctacte = $query->result_array();
        $codigos = array();
        foreach($ctacte as $codigo){
            $codigos[] = $codigo['codigo'];
        }
        $boletos = array();
        if(count($codigos) > 0){
            $conexion->resetear();
            $conexion->select('codigo');
            $conexion->from('bancos.boletos_bancarios');
            $conexion->where('cod_filial', $filial);
            $conexion->where('numero_documento in (' . implode(', ', $codigos) . ')');
            $query = $conexion->get();
            $boletos = $query->result_array();
        }
        return array('ctacte' => $ctacte, 'boletos' => $boletos);
    }

    public static function getMatriculasSelect($conexion, $codigo_alumno){
        $conexion->select('matriculas.codigo, matriculas.cod_alumno');
        $conexion->from('matriculas');
        $conexion->join('alumnos', "matriculas.cod_alumno = alumnos.codigo");
        $conexion->where('alumnos.codigo', $codigo_alumno);
        $query = $conexion->get();
        $matriculas = $query->result_array();
        return $matriculas;
    }


    public static function buscaMatriculasPorEstado(CI_DB_mysqli_driver $conexion, $estado) {
        $conexion->select('m.*');
        $conexion->from('matriculas m');
        $conexion->join('matriculas_periodos mp', 'mp.cod_matricula = m.codigo');
        $conexion->where('mp.estado', $estado);
        $conexion->group_by('m.codigo');
        $query = $conexion->get();
        $matriculas = $query->result_array();
        return $matriculas;
    }

    public static function getMoras(CI_DB_mysqli_driver $conexion, $cod_matricula, $habilitado = array('1', '2')) {
        $conexion->select('(c.importe - c.pagado) AS saldo');
        $conexion->select('c.*');
        $conexion->from('ctacte c');
        $conexion->join('ctacte c2', 'c2.codigo = c.concepto');
        $conexion->where('c.cod_concepto', '3');
        $conexion->where('c.importe > c.pagado');
        $conexion->where_in('c.habilitado', $habilitado);
        $conexion->where('c2.concepto', $cod_matricula);
        $conexion->where('c2.cod_concepto', '1');
        $query = $conexion->get();
        $moras = $query->result_array();
        return $moras;
    }

    //version mas simple de la funcion getCtacte
    public static function getCtacteCorrecionDeudasPasivas(CI_DB_mysqli_driver $conexion, $cod_matricula, $cod_alumno) {
        $conexion->select('(ctacte.importe - ctacte.pagado) AS saldo');
        $conexion->select('ctacte.*');
        $conexion->from('ctacte');
        $conexion->where('ctacte.pagado < ctacte.importe');
        $conexion->where('cod_concepto IN (1, 2, 4, 5)');
        $conexion->where('habilitado', '1');
        $conexion->where('concepto', $cod_matricula);
        $conexion->where('cod_alumno', $cod_alumno);
        $query = $conexion->get();
        $ctacte = $query->result_array();
        return $ctacte;
        }

    function isOnline($filial){
        //consulta que arma una tabla donde se relaciona la matrica con planes academicos y luego con plan academico filial donde esta el atributo online.
        //count puede ser 1 o 0

        $conexion = $this->oConnection;
        $conexion->resetear();
        $conexion->select('COUNT(*) as cant');
        $conexion->from('matriculas');
        $conexion->join('general.planes_academicos', 'general.planes_academicos.codigo = matriculas.cod_plan_academico');
        $conexion->join('general.planes_academicos_filiales', 'general.planes_academicos_filiales.cod_plan_academico = general.planes_academicos.codigo AND general.planes_academicos_filiales.cod_filial = '.$filial);
        $conexion->where('matriculas.codigo = '.$this->codigo.' AND general.planes_academicos_filiales.online = 1');

        $query = $conexion->get();

        $cantidad = $query->result_array();
        return($cantidad[0]['cant'] > 0);

    }
    
    /**
     * Funcion que devuelve las matriculas periodos relacionadas a una matricula
     *
     */
    function getMatriculasPeriodo(){
        $conexion = $this->oConnection;
        $conexion->resetear();
        $conexion->select('codigo');
        $conexion->select('estado');
        $conexion->from('matriculas_periodos');
        $conexion->where('cod_matricula ', $this->codigo);
        $query = $conexion->get();
        return ($query->result_array());
    }
}

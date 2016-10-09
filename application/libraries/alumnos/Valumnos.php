<?php

/**
 * Class Valumnos
 *
 * Class  Valumnos maneja todos los aspectos de alumnos
 *
 * @package  SistemaIGA
 * @subpackage Alumnos
 * @author   Ivan berthillod <ivan.sys@gmail.com>
 * @author   Aquiles Gonzalez <sistemas1@iga-la.net>
 * @version  $Revision: 1.1 $
 * @access   private
 */
class Valumnos extends Talumnos {
    /* CONSTRUCTOR */

    function __construct(CI_DB_mysqli_driver $conexion, $codigo = null) {
        parent::__construct($conexion, $codigo);
    }

    /* PRIVATE FUNCTIONS */

    /* PUBLIC FUNCTIONS */

    /**
     * Retorna todos los telefonos de alumno
     * @access public
     * @return Array de telefonos
     */
    function getTelefonos($soloPrincipal = false) {
        $this->oConnection->select('telefonos.*,alumnos_telefonos.default');
        $this->oConnection->from("alumnos_telefonos");
        $this->oConnection->join('telefonos', 'alumnos_telefonos.cod_telefono = telefonos.codigo');
        $this->oConnection->where('alumnos_telefonos.cod_alumno', $this->codigo);
        $this->oConnection->where('telefonos.baja', 0);
        if ($soloPrincipal) {
            $this->oConnection->where("alumnos_telefonos.default", 1);
        }
        $query = $this->oConnection->get();
        return $arrResp = $query->result_array();
    }

    /**
     * Retorna todas las razones sociales de un alumno
     * @access public
     * @return Array razones sociales.
     */
    function getRazonesSociales() {
        $this->oConnection->select('razones_sociales.*');
        $this->oConnection->select('general.condiciones_sociales.condicion as nombrecondicion');
        $this->oConnection->select('alumnos_razones.default');
        $this->oConnection->select('(SELECT nombre FROM general.documentos_tipos WHERE general.documentos_tipos.codigo = razones_sociales.tipo_documentos)as tipoid');
        $this->oConnection->select('alumnos_razones.default_facturacion');
        $this->oConnection->from('alumnos_razones', 'alumnos_razones.cod_alumno = alumnos.codigo');
        $this->oConnection->join('razones_sociales', 'alumnos_razones.cod_razon_social = razones_sociales.codigo');
        $this->oConnection->join('general.condiciones_sociales', 'general.condiciones_sociales.codigo = razones_sociales.condicion');
        $this->oConnection->where('alumnos_razones.cod_alumno', $this->codigo);
        $this->oConnection->where('razones_sociales.baja', 0);
        $query = $this->oConnection->get();
        $arrResp = $query->result_array();
        return $arrResp;
    }

    /**
     * Retorna responsables de un alumno
     * @access public
     * @return Array responsables.
     */
    function getResponsables() {
        $this->oConnection->select('alumnos_responsables.relacion_alumno,responsables.*, razones_sociales.razon_social,razones_sociales.tipo_documentos,razones_sociales.codigo as cod_razon_social, razones_sociales.documento, razones_sociales.email');
        $this->oConnection->select("CONCAT(razones_sociales.direccion_calle,' ',razones_sociales.direccion_numero) as direccion", false);
        $this->oConnection->select("CONCAT(general.documentos_tipos.nombre,' ',razones_sociales.documento) as nombre_identificacion", false);
        $this->oConnection->select("general.condiciones_sociales.condicion as condicion");
        $this->oConnection->from('alumnos_responsables');
        $this->oConnection->join('responsables', 'responsables.codigo = alumnos_responsables.cod_responsable');
        $this->oConnection->join('responsables_razones', 'responsables_razones.cod_responsable = responsables.codigo');
        $this->oConnection->join('razones_sociales', 'razones_sociales.codigo = responsables_razones.cod_razon_social');
        $this->oConnection->join('general.documentos_tipos', 'general.documentos_tipos.codigo = razones_sociales.tipo_documentos');
        $this->oConnection->join('general.condiciones_sociales', 'general.condiciones_sociales.codigo = razones_sociales.condicion');
        $this->oConnection->where('alumnos_responsables.cod_alumno', $this->codigo);
        $this->oConnection->where('responsables.baja', 0);
        $query = $this->oConnection->get();
        return $query->result_array();
    }

    /*     * NO VA
     * asigna a razon social a alumno.
     * @access public
     */

    function setRazonesSociales($arrRazones) {
        $conexion = $this->oConnection;
        $conexion->insert('alumnos_razones', $arrRazones);
    }

    public function setRazonDefault($codRazonScoial) {
        $this->oConnection->where('alumnos_razones.cod_alumno', $this->codigo);
        $arrResp = $this->oConnection->update('alumnos_razones', array("default" => 0));
        $this->oConnection->where('alumnos_razones.cod_alumno', $this->codigo);
        $this->oConnection->where('alumnos_razones.cod_razon_social', $codRazonScoial);
        return $arrResp && $this->oConnection->update('alumnos_razones', array("default" => 1));
    }

    public function setRazonDefaultFacturacion($codRazonScoial) {
        $this->oConnection->where('alumnos_razones.cod_alumno', $this->codigo);
        $arrResp = $this->oConnection->update('alumnos_razones', array("default_facturacion" => 0));
        $this->oConnection->where('alumnos_razones.cod_alumno', $this->codigo);
        $this->oConnection->where('alumnos_razones.cod_razon_social', $codRazonScoial);
        return $arrResp && $this->oConnection->update('alumnos_razones', array("default_facturacion" => 1));
    }

    /**
     * asigna responsable a alumno
     * @param array $arrResponble array de responsable que va a asignar
     * @access public
     */
    function setResponsable($arrResponble) {
        $conexion = $this->oConnection;
        $conexion->insert('alumnos_responsables', $arrResponble);
    }

    function setImagen($imagen){
        if ($imagen && strlen($imagen) > 1){
            $this->oConnection->where("id_alumno", $this->codigo);
            $resp = $this->oConnection->delete("alumnos_imagenes");
            return $resp && $this->oConnection->insert("alumnos_imagenes", array("id_alumno" => $this->codigo, "imagen" => $imagen));
        } else {
            return false;
        }
    }

    function getImagen(){
        $this->oConnection->select("imagen");
        $this->oConnection->from("alumnos_imagenes");
        $this->oConnection->where("id_alumno", $this->codigo);
        $query = $this->oConnection->get();
        $arrTemp = $query->result_array();
        if (isset($arrTemp[0], $arrTemp[0]['imagen'])){
            return $arrTemp[0]['imagen'];
        } else {
            return '';
        }
    }

    /**
     * asigna telefono a alumno
     *  @param int $cod_telefono codigo de telefono a asignar.
     * @access public
     */
    function setTelefono($cod_telefono, $default) {
        $arrtel = array(
            "cod_alumno" => $this->codigo,
            "cod_telefono" => $cod_telefono,
            "default" => $default
        );
        $this->oConnection->insert('alumnos_telefonos', $arrtel);
    }

    function updateTelefonoAlumno($cod_telefono, $default) {
        $arrtel = array(
            "cod_alumno" => $this->codigo,
            "cod_telefono" => $cod_telefono,
            "default" => $default
        );
        $this->oConnection->where('alumnos_telefonos.cod_alumno', $this->codigo);
        $this->oConnection->where('alumnos_telefonos.cod_telefono', $cod_telefono);
        $this->oConnection->update('alumnos_telefonos', $arrtel);
    }

    function getRazonSocialDefault() {
        $conexion = $this->oConnection;
        $conexion->select('cod_razon_social');
        $conexion->from('alumnos_razones');
        $conexion->where('cod_alumno', $this->codigo);
        $conexion->where('default', 1);
        $query = $conexion->get();
        return $query->result_array();
    }

    public function getCtaCte($debe = false, $condiciones = array(), $wherein = array(), $ultimafinanciacion = null) {
        $condiciones['cod_alumno'] = $this->codigo;
        $ctacte = Vctacte::getCtaCte($this->oConnection, $debe, $condiciones, null, null, null, false, $wherein, $ultimafinanciacion);
        return $ctacte;
    }

    public function getRazonesAlumno($arrCondiciones = '', $arrCondicionesIn = '') {
        $response = array();
        $this->oConnection->select('*');
        $this->oConnection->from('razones_sociales');
        $this->oConnection->join('alumnos_razones', 'alumnos_razones.cod_razon_social = razones_sociales.codigo');
        $this->oConnection->where('alumnos_razones.cod_alumno', $this->codigo);
        if ($arrCondiciones != '') {
            $this->oConnection->where($arrCondiciones);
        }
        if($arrCondicionesIn != '')
        {
            foreach($arrCondicionesIn as $key => $value)
            {
                $this->oConnection->where_in($key, $value);
            }
        }
        $this->oConnection->order_by('alumnos_razones.default_facturacion', 'desc');
        $query = $this->oConnection->get();
        $razonAlumno = $query->result_array();

        if(count($query->result_array()) > 0)
        {
            $response['error'] = false;
            $response['razon_alumno'] = $razonAlumno;
        }
        else
        {
            $response['error'] = true;
        }

        return $response;

    }

    public function getCtaCteSinFacturar($condiciones, $orden, $separador, $soloCobradas = false) {
        $rowsCtacte = Vctacte::getCtaCteSinFacturar($this->oConnection, $this->codigo, $condiciones, $orden, null, null, null, false, null, null, $separador, null, false, $soloCobradas);
        return $rowsCtacte;
    }

    public function getCtaCteFacturarCobrar($condiciones) {
        $condiciones['cod_alumno'] = $this->codigo;
        $rowsCtacte = Vctacte::getCtaCteFacturarCobrar($this->oConnection, $condiciones);
        return $rowsCtacte;
    }

    public function getCtaCteImputar() {
        $rowsCtaCte = Vctacte::getCtaCteImputar($this->oConnection, $this->codigo);
        return $rowsCtaCte;
    }

    function getRazonSocialDefaultFacturar() {
        $conexion = $this->oConnection;
        $conexion->select('cod_razon_social');
        $conexion->from('alumnos_razones');
        $conexion->where('cod_alumno', $this->codigo);
        $conexion->where('default_facturacion', 1);
        $query = $conexion->get();
        return $query->result_array();
    }

    /**
     * retorna toas las matriculas de un alumno
     *
     * @return array
     * ticket 4333 - mmori - se agrega fecha de baja
     */
    public function getMatriculas() {
        $conexion = $this->oConnection;
        $conexion->select("matriculas.*");
        $conexion->select("general.cursos.nombre_es AS curso_nombre");
        $conexion->select("matriculas_estado_historicos.fecha_hora AS fecha_hora");
        $conexion->from("matriculas");
        $conexion->join("general.planes_academicos", "general.planes_academicos.codigo = matriculas.cod_plan_academico");
        $conexion->join("general.cursos", "general.cursos.codigo = general.planes_academicos.cod_curso");
        $conexion->join("matriculas_periodos", "matriculas_periodos.cod_matricula = matriculas.codigo");
        $conexion->join("matriculas_estado_historicos", "matriculas_estado_historicos.cod_matricula_periodo = matriculas_periodos.codigo", "LEFT");
        $conexion->where("matriculas.cod_alumno =", $this->codigo);
        $conexion->group_by("matriculas.codigo");
        $query = $conexion->get();
        return $query->result_array();
    }

    /* STATIC FUNCTIONS */

    /**
     * lista todos los alumnos con distintos filtros para datatable plugin
     * @access public
     * @param CI_DB_mysqli_driver $conexion conexion que viene del Modelo
     * @param  Array $arrCondindicioneslike condiciones del buscar de datatable
     * @param  Array $arrLimit limite del paginado.
     * @param Boolean $contar devuelve un contar de tabla o un array-
     * @return Array de alumno
     */
    static function listarAlumnosDataTable(CI_DB_mysqli_driver $conexion, $arrCondindicioneslike, $arrLimit = null, $arrSort = null, $contar = false, $separador = null/* modificacion franco ticket 5053->  (cambiar por los valores de la tabla alumnos)*/ ,
                $fechaaltaDesde = null, $fechaaltaHasta = null, $talle = null, /*$tipoContacto = null,*/ $provincia = null, $localidad = null, $como_nos_conocio = null, $estado = null/* modificacion franco ticket 5053->  */) {
        $idioma = get_idioma();
        if ($idioma == 'en'){
            $idioma = "in";
        }
        $nombreApellido = formatearNomApeQuery();
        $conexion->select('alumnos.login_filial.cod_filial');
        $conexion->from('alumnos.login_filial');
        $conexion->where('alumnos.login_filial.cod_alumno = alumnos.codigo');
        $conexion->where("alumnos.login_filial.cod_filial = $conexion->database");
        $conexion->limit(1);
        $reenviarMail = $conexion->return_query();
        $conexion->resetear();
        $conexion->select("CONCAT($nombreApellido) as nombre_apellido, concat(alumnos.calle,CASE alumnos.calle_numero when 0 THEN '' ELSE concat(' ',alumnos.calle_numero) END, concat(' ',alumnos.calle_complemento)) as calle2, alumnos.*, general.localidades.nombre as localidad, general.como_nos_conocio.descripcion_$idioma, "
                . "dt1.nombre as tipo_doc, razones_sociales.documento as razon_doc, razones_sociales.razon_social as razon_social, general.talles.talle as talle", false);
        $conexion->select("IFNULL(($reenviarMail),0) as reenviar_mail", false);
        $conexion->from("alumnos");
        $conexion->join("general.como_nos_conocio", "general.como_nos_conocio.codigo = alumnos.comonosconocio");
        $conexion->join("general.localidades", "general.localidades.id = alumnos.id_localidad");
        $conexion->join("alumnos_razones", "alumnos_razones.cod_alumno = alumnos.codigo AND alumnos_razones.`default` = 1");
        $conexion->join("razones_sociales", "razones_sociales.codigo = alumnos_razones.cod_razon_social");
        $conexion->join("general.documentos_tipos AS dt1", "dt1.codigo = razones_sociales.tipo_documentos");
        $conexion->join("general.talles", "general.talles.codigo = alumnos.id_talle", "left");
        if ($fechaaltaDesde != null){
            $conexion->where("DATE(alumnos.fechaalta) >=", $fechaaltaDesde);
        }
        if ($fechaaltaHasta != null){
            $conexion->where("DATE(alumnos.fechaalta) <=", $fechaaltaHasta);
        }
        if ($como_nos_conocio != null){
            $conexion->where("alumnos.comonosconocio", $como_nos_conocio);
        }
        if ($talle != null){
            $conexion->where("general.talles.codigo", $talle);
        }
        if ($provincia != null){
            $conexion->where("general.localidades.provincia_id", $provincia);
        }
        if ($localidad != null){
            $conexion->where("alumnos.id_localidad", $localidad);
        }
        if ($estado != null){
            $conexion->where("alumnos.baja", $estado);
        }
        if (count($arrCondindicioneslike) > 0) {
            $arrTemp = array();
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
        $query = $conexion->get();
        if ($contar) {
            $arrResp = $query->num_rows();
        } else {
            $arrResp = $query->result_array();
        }
        return $arrResp;
    }

    //reporte deuda por alumno
    static function listarDeudaAlumnosDataTable(CI_DB_mysqli_driver $conexion, $filtro, $arrCondindicioneslike, $arrLimit = null, $arrSort = null, $contar = false, $separador = null) {
        $nombreApellido = formatearNomApeQuery();
        $idioma = get_idioma();
        $conexion->select("alumnos.codigo AS cod_alumno");
        $conexion->select("matriculas.codigo AS cod_matricula");
        $conexion->select("CONCAT($nombreApellido) as nombre", false);
        $conexion->select("alumnos.documento AS documento");
        if($filtro["periodo"])
        {
            if($filtro['anio'])
            {
                $periodo_principio = "01/".$filtro["periodo"]."/".$filtro['anio'];
                $periodo_fin = "01/".($filtro["periodo"]+1)."/".$filtro['anio'];
                $conexion->select("(SELECT COUNT(*) FROM ctacte WHERE ctacte.cod_alumno = alumnos.codigo AND ctacte.importe > ctacte.pagado AND STR_TO_DATE(ctacte.fechavenc, '%Y-%m-%d') >= STR_TO_DATE('".$periodo_principio."', '%d/%m/%Y') AND STR_TO_DATE(ctacte.fechavenc, '%Y-%m-%d') < STR_TO_DATE('".$periodo_fin."', '%d/%m/%Y')) AS periodo,", false);
            }

        }
        $conexion->select("(SELECT MAX(CONCAT(LPAD(DAY(cobros.fechareal), 2, 0), '/', LPAD(MONTH(cobros.fechareal), 2, 0), '/', YEAR(cobros.fechareal))) FROM cobros WHERE cobros.cod_alumno = alumnos.codigo) AS fechapago", false);
        $conexion->select("(SELECT SUM(ctacte.importe - ctacte.pagado) FROM ctacte WHERE ctacte.cod_alumno = alumnos.codigo AND ctacte.importe > ctacte.pagado AND ctacte.concepto = matriculas.codigo AND ctacte.fechavenc < CURRENT_DATE()) AS total");
        $conexion->select("general.cursos.nombre_".$idioma." AS curso");
        $conexion->select("comisiones.nombre AS comision");
        $conexion->select("IF((SELECT (TIME_TO_SEC(horarios.horadesde)) FROM horarios WHERE horarios.cod_comision = comisiones.codigo GROUP BY horarios.horadesde LIMIT 1) BETWEEN TIME_TO_SEC('00:00:00') AND TIME_TO_SEC('12:00:00') , '1',
                            IF((SELECT (TIME_TO_SEC(horarios.horadesde)) FROM horarios WHERE horarios.cod_comision = comisiones.codigo GROUP BY horarios.horadesde LIMIT 1) BETWEEN TIME_TO_SEC('12:00:01') AND TIME_TO_SEC('19:00:00'),'2',
                            IF((SELECT (TIME_TO_SEC(horarios.horadesde)) FROM horarios WHERE horarios.cod_comision = comisiones.codigo GROUP BY horarios.horadesde LIMIT 1) BETWEEN TIME_TO_SEC('19:00:01') AND TIME_TO_SEC('23:59:59'),'3',''))) AS turno",false);
        $conexion->select("IF((SELECT GROUP_CONCAT(DISTINCT ctacte.habilitado)
                           FROM ctacte
                           WHERE ctacte.cod_alumno = alumnos.codigo
                           AND ctacte.importe > ctacte.pagado
                           AND ctacte.concepto = matriculas.codigo) = 0
                           OR
                           (SELECT GROUP_CONCAT(DISTINCT ctacte.habilitado)
                           FROM ctacte
                           WHERE ctacte.cod_alumno = alumnos.codigo
                           AND ctacte.importe > ctacte.pagado
                           AND ctacte.concepto = matriculas.codigo) = 3, '".lang('inhabilitada')."',
                           IF ((SELECT GROUP_CONCAT(DISTINCT ctacte.habilitado)
                           FROM ctacte
                           WHERE ctacte.cod_alumno = alumnos.codigo
                           AND ctacte.importe > ctacte.pagado
                           AND ctacte.concepto = matriculas.codigo) = 2, '".lang('deuda_pasiva')."',
                           IF ((SELECT GROUP_CONCAT(DISTINCT ctacte.habilitado)
                           FROM ctacte
                           WHERE ctacte.cod_alumno = alumnos.codigo
                           AND ctacte.importe > ctacte.pagado
                           AND ctacte.concepto = matriculas.codigo) = 1, '".lang('deuda_activa')."','sin dato')))
                           AS tipo_de_deuda", false);
        $conexion->select("IF((SELECT COUNT(*)
                            FROM ctacte
                            WHERE ctacte.cod_alumno = alumnos.codigo
                            AND ctacte.importe > ctacte.pagado
                            AND ctacte.concepto = matriculas.codigo) = 1,'".lang("a_una_cuota_adeudada")."',
                            IF((SELECT COUNT(*)
                            FROM ctacte
                            WHERE ctacte.cod_alumno = alumnos.codigo
                            AND ctacte.importe > ctacte.pagado
                            AND ctacte.concepto = matriculas.codigo) = 2,'".lang("b_dos_cuotas_adeudadas")."',
                            IF((SELECT COUNT(*)
                            FROM ctacte
                            WHERE ctacte.cod_alumno = alumnos.codigo
                            AND ctacte.importe > ctacte.pagado
                            AND ctacte.concepto = matriculas.codigo) = 3,'".lang("c_tres_cuotas_adeudadas")."',
                            IF((SELECT COUNT(*)
                            FROM ctacte
                            WHERE ctacte.cod_alumno = alumnos.codigo
                            AND ctacte.importe > ctacte.pagado
                            AND ctacte.concepto = matriculas.codigo) > 3,'".lang("d_tres_o_mas_cuotas_adeudadas")."',''))))
                            AS cant_cuotas_debe", false);
        $conexion->from("alumnos");
        $conexion->join("matriculas","matriculas.cod_alumno = alumnos.codigo");
        $conexion->join("general.planes_academicos","general.planes_academicos.codigo = matriculas.cod_plan_academico");
        $conexion->join("general.cursos","general.cursos.codigo = general.planes_academicos.cod_curso");
        $conexion->join("matriculas_periodos","matriculas_periodos.cod_matricula = matriculas.codigo AND matriculas_periodos.codigo = (SELECT MAX(matper.codigo) FROM matriculas_periodos AS matper WHERE matper.cod_matricula = matriculas.codigo)");
        $conexion->join("estadoacademico","estadoacademico.cod_matricula_periodo = matriculas_periodos.codigo AND estadoacademico.codigo = (SELECT MAX(estaaca.codigo) FROM estadoacademico AS estaaca WHERE estaaca.cod_matricula_periodo = matriculas_periodos.codigo)");
        $conexion->join("matriculas_inscripciones","matriculas_inscripciones.cod_estado_academico = estadoacademico.codigo");
        $conexion->join("comisiones", "comisiones.codigo = matriculas_inscripciones.cod_comision");

        if (count($arrCondindicioneslike) > 0) {
            $arrTemp = array();
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

        switch ($filtro['cant_cuotas']){
            case 1:
                $conexion->having("cant_cuotas_debe = '". lang("a_una_cuota_adeudada")."'");
                break;
            case 2:
                $conexion->having("cant_cuotas_debe = '". lang("b_dos_cuotas_adeudadas")."'");
                break;
            case 3:
                $conexion->having("cant_cuotas_debe = '". lang("c_tres_cuotas_adeudadas")."'");
                break;
            case 4:
                $conexion->having("cant_cuotas_debe = '". lang("d_tres_o_mas_cuotas_adeudadas")."'");
                break;
        }

        switch ($filtro["ultimo_pago_select"]){
            case 1:
                if($filtro["fecha_pago_desde"] != ''){
                    $conexion->having("STR_TO_DATE(fechapago, '%d/%m/%Y') > STR_TO_DATE('".$filtro["fecha_pago_desde"]."','%d/%m/%Y')");
                }
                break;
            case 2:
                if($filtro["fecha_pago_desde"] != ''){
                    $conexion->having("STR_TO_DATE(fechapago, '%d/%m/%Y') < STR_TO_DATE('".$filtro["fecha_pago_desde"]."','%d/%m/%Y')");
                }
                break;
            case 3:
                if($filtro["fecha_pago_desde"] != '' && $filtro["fecha_pago_hasta"] != ''){
                    $conexion->having("STR_TO_DATE(fechapago, '%d/%m/%Y') >= STR_TO_DATE('".$filtro["fecha_pago_desde"]."','%d/%m/%Y')");
                    $conexion->having("STR_TO_DATE(fechapago, '%d/%m/%Y') <= STR_TO_DATE('".$filtro["fecha_pago_hasta"]."','%d/%m/%Y')");
                }
                break;
            case 4:
                if($filtro["fecha_pago_desde"] != ''){
                    $conexion->having("STR_TO_DATE(fechapago, '%d/%m/%Y') = STR_TO_DATE('".$filtro["fecha_pago_desde"]."','%d/%m/%Y')");
                }
                break;
        }

        switch ($filtro["saldo_acumulado"]){
            case 1:
                if($filtro["hast"] != ''){
                    $conexion->having("total > '" . $filtro["hast"] ."'");
                } else {
                    $conexion->having("total <> 0");
                }
                break;
            case 2:
                if($filtro["hast"] != ''){
                    $conexion->having("total < '" . $filtro["hast"] ."'");
                } else {
                    $conexion->having("total <> 0");
                }
                break;
            case 3:
                if($filtro["hast"] != '' && $filtro["desd"] != ''){
                    $conexion->having("total >= " . $filtro["desd"]);
                    $conexion->having("total <= " . $filtro["hast"]);
                } else {
                    $conexion->having("total <> 0");
                }
                break;
            case 4:
                if($filtro["hast"] != ''){
                    $conexion->having("total = '" . $filtro["hast"] ."'");
                } else {
                    $conexion->having("total <> 0");
                }
                break;
            case -1:
                $conexion->having("total <> 0");
                break;
        }
        if($filtro["cursos"]){
            $conexion->having("curso = '".$filtro["cursos"]."'");
        }
        if($filtro["periodo"]){
            $conexion->having("periodo > 0");
        }
        if($filtro["turno"]){
            $conexion->having("turno = '".$filtro["turno"]."'");
        }
        if($filtro["comision"]){
            $conexion->having("comision = '".$filtro["comision"]."'");
        }
        if($filtro["tipo_deuda"]){
            $conexion->having("tipo_de_deuda = '".$filtro["tipo_deuda"]."'");
        } else {
            $conexion->having("tipo_de_deuda <> 'inhabilitada'");
        }
        if ($arrLimit != null && $arrLimit[1] != -1) {
            $conexion->limit($arrLimit[1], $arrLimit[0]);
        }
        if($arrSort!=null){
            if($arrSort["0"] != "cant_cuotas_debe"){
                $conexion->order_by("cant_cuotas_debe", "asc");
            } else {
                $conexion->order_by("cant_cuotas_debe", $arrSort["1"]);
            }
            $conexion->order_by($arrSort["0"], $arrSort["1"]);
        } else {
            $conexion->order_by("cant_cuotas_debe", "asc");
        }
        $query = $conexion->get();
        if ($contar) {
            $filas = $query->num_rows();
            $arrResp2 = $query->result_array();
            $totalAcumulado = 0;
            foreach ($arrResp2 as $row)
            {
                $totalAcumulado += $row['total'];
            }
            $arrResp['totalAcumulado'] = $totalAcumulado;
            $arrResp['filas'] = $filas;
        } else {
            $arrResp = $query->result_array();
        }
        return $arrResp;
    }

    static function listarAlumnosDataTableCtaCte(CI_DB_mysqli_driver $conexion, $arrCondindicioneslike, $arrLimit = null, $arrSort = null, $contar = false, $debe = false, $separador = null, $separadorDecimal = null) {
        $nombreApellido = formatearNomApeQuery();
        $conexion->select('ifnull(sum(ctacte.importe - ctacte.pagado),0)', false);
        $conexion->from('ctacte');
        $conexion->where('ctacte.fechavenc < curdate()');
        $conexion->where('ctacte.habilitado IN(1)');
        $conexion->where('(ctacte.importe - ctacte.pagado)>', 0);
        $conexion->where('ctacte.cod_alumno = alumnos.codigo');
        $subquery = $conexion->return_query();
        $conexion->resetear();
        $conexion->select('min(ctacte.fechavenc)', false);
        $conexion->from('ctacte');
        $conexion->where('ctacte.habilitado IN(1)');
        $conexion->where('(ctacte.importe - ctacte.pagado)>', 0);
        $conexion->where('ctacte.cod_alumno = alumnos.codigo');
        $subquery2 = $conexion->return_query();
        $conexion->resetear();
        $conexion->select("alumnos.codigo, CONCAT($nombreApellido) as nombre_apellido,($subquery) as saldo,($subquery2) as proxvenc", false);
        $conexion->from('alumnos');
        if (count($arrCondindicioneslike) > 0) {
            $arrTemp = array();
            foreach ($arrCondindicioneslike as $key => $value) {
                if ($key == 'nombre_apellido' || $key == 'saldo') {
                    $arrTemp[] = "REPLACE(nombre_apellido, '$separador ',' ') LIKE REPLACE('%$value%', '$separador ',' ')";
                    $arrTemp[] = "REPLACE(saldo, '$separadorDecimal ',' ') LIKE REPLACE('%$value%', '$separadorDecimal ',' ')";
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
        $query = $conexion->get();
        if ($contar) {
            $arrResp = $query->num_rows();
        } else {
            $arrResp = $query->result_array();
        }
        return $arrResp;
    }

    static function getListadoCentroReportes(CI_DB_mysqli_driver $conexion, $arrLimit = null, $arrSort = null, $contar = false, $search = null, array $searchFields = null, $fechaDesde = null, $fechaHasta = null) {
        $aColumns = array();
        $aColumns['apellido']['order'] = "alumnos.apellido";
        $aColumns['nombre']['order'] = "alumnos.nombre";
        $aColumns['email']['order'] = "alumnos.email";
        $aColumns['localidad_nombre']['order'] = "general.localidades.nombre";
        $aColumns['como_nos_conocio_nombre_es']['order'] = "general.como_nos_conocio.descripcion_es";
        $aColumns['documento']['order'] = 'documento';
        $aColumns['direccion']['order'] = 'direccion';
        $aColumns['telefono']['order'] = 'telefono';
        $aColumns['fechaalta']['order'] = "alumnos.fechaalta";
        $aColumns['fechaalta']['having'] = "fechaalta";
        $aColumns['apellido']['having'] = "alumnos.apellido";
        $aColumns['nombre']['having'] = "alumnos.nombre";
        $aColumns['email']['having'] = "alumnos.email";
        $aColumns['localidad_nombre']['having'] = "general.localidades.nombre";
        $aColumns['como_nos_conocio_nombre_es']['having'] = "general.como_nos_conocio.descripcion_es";
        $aColumns['documento']['having'] = 'documento';
        $aColumns['direccion']['having'] = 'direccion';
        $aColumns['telefono']['having'] = 'telefono';
        $conexion->select("CONCAT(telefonos.cod_area, ' ', telefonos.numero)", false);
        $conexion->from("telefonos");
        $conexion->join("alumnos_telefonos", "alumnos_telefonos.cod_telefono = telefonos.codigo");
        $conexion->where("alumnos_telefonos.cod_alumno = alumnos.codigo");
        $conexion->order_by("telefonos.codigo", "ASC");
        $conexion->limit(1, 0);
        $subquery = $conexion->return_query();
        $conexion->resetear();
        $conexion->select("alumnos.codigo");
        $conexion->select("alumnos.apellido");
        $conexion->select("alumnos.nombre");
        $conexion->select("CONCAT(general.documentos_tipos.nombre, ' ',alumnos.documento) AS documento", false);
        $conexion->select("CONCAT(alumnos.calle, ' ', alumnos.calle_numero, ' ', alumnos.calle_complemento) AS direccion", false);
        $conexion->select("alumnos.email");
        $conexion->select("CONCAT(LPAD(DAY(alumnos.fechaalta), 2, 0), '/', LPAD(MONTH(alumnos.fechaalta), 2, 0), '/', YEAR(alumnos.fechaalta)) AS fechaalta", false);
        $conexion->select("general.localidades.nombre AS localidad_nombre");
        $conexion->select("general.como_nos_conocio.descripcion_es AS como_nos_conocio_nombre_es");
        $conexion->select("($subquery) AS telefono", false);
        $conexion->from("alumnos");
        $conexion->join("general.localidades", "general.localidades.id = alumnos.id_localidad");
        $conexion->join("general.como_nos_conocio", "general.como_nos_conocio.codigo = alumnos.comonosconocio");
        $conexion->join("general.documentos_tipos", "general.documentos_tipos.codigo = alumnos.tipo");
        if ($fechaDesde != null)
            $conexion->where("DATE(alumnos.fechaalta) >=", $fechaDesde);
        if ($fechaHasta != null)
            $conexion->where("DATE(alumnos.fechaalta) <=", $fechaHasta);
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

    public function getExamenesAlumnos($cod_examen = null) {
        $this->oConnection->select('examenes.codigo, examenes.fecha,examenes.hora, examenes_estado_academico.codigo as codInscripcion, examenes_estado_academico.fechadeinscripcion, examenes_estado_academico.estado');
        $this->oConnection->from('alumnos');
        $this->oConnection->join('matriculas', 'matriculas.cod_alumno = alumnos.codigo');
        $this->oConnection->join('matriculas_periodos', 'matriculas_periodos.cod_matricula = matriculas.codigo');
        $this->oConnection->join('estadoacademico', 'estadoacademico.cod_matricula_periodo = matriculas_periodos.codigo');
        $this->oConnection->join('examenes_estado_academico', 'examenes_estado_academico.cod_estado_academico = estadoacademico.codigo');
        $this->oConnection->join('examenes', 'examenes.codigo = examenes_estado_academico.cod_examen');
        $this->oConnection->where('alumnos.codigo', $this->codigo);
        $this->oConnection->where('examenes_estado_academico.estado <>', 'baja');
        if ($cod_examen != null) {
            $this->oConnection->where('examenes.codigo', $cod_examen);
        }
        $query = $this->oConnection->get();
        return $query->result_array();
    }

    public function getNotasExamenesAlumno() {
        $this->oConnection->select('examenes_estado_academico.cod_examen, notas_resultados.tipo_resultado, notas_resultados.nota');
        $this->oConnection->from('alumnos');
        $this->oConnection->join('matriculas', 'matriculas.cod_alumno = alumnos.codigo');
        $this->oConnection->join('matriculas_periodos', 'matriculas_periodos.cod_matricula = matriculas.codigo');
        $this->oConnection->join('estadoacademico', 'estadoacademico.cod_matricula_periodo = matriculas_periodos.codigo');
        $this->oConnection->join('examenes_estado_academico', 'examenes_estado_academico.cod_estado_academico = estadoacademico.codigo');
        $this->oConnection->join('notas_resultados', 'notas_resultados.cod_inscripcion = examenes_estado_academico.codigo');
        $this->oConnection->where('alumnos.codigo', $this->codigo);
        $query = $this->oConnection->get();
        return $query->result_array();
    }

    public function bajaAlumno() {
        $this->baja = 'inhabilitada';
        $estado = $this->guardarAlumnos();
        return $estado;
    }

    public function altaAlumno() {
        $this->baja = 'habilitada';
        $estado = $this->guardarAlumnos();
        return $estado;
    }

    public function getConceptosCtaCteDebe($refinanciar = true, $arrmatriculas = null) {
        $this->oConnection->select('conceptos.*, ctacte.concepto');
        $this->oConnection->distinct();
        $this->oConnection->from('ctacte');
        $this->oConnection->join('conceptos', 'conceptos.codigo = ctacte.cod_concepto');
        $this->oConnection->where('ctacte.cod_alumno', $this->codigo);
        $this->oConnection->where('ctacte.habilitado IN (1,2)');
        $this->oConnection->where('ctacte.importe > ctacte.pagado');
        if ($arrmatriculas != null) {
            $this->oConnection->where('conceptos.codigo IN (SELECT codigo from conceptos where codigo IN (SELECT conceptos.codigo_padre from conceptos where conceptos.key = "TIPO" and  conceptos.valor = "ACADEMICO"))');
            $this->oConnection->where_in('ctacte.concepto', $arrmatriculas);
        } else {
            if ($refinanciar) {
                $this->oConnection->where('pagado = 0');
                $this->oConnection->where('conceptos.codigo NOT IN (select conceptos.codigo_padre from conceptos where conceptos.key = "NO_REFINANCIAR" and  conceptos.valor = "1")');
            }
        }
        $query = $this->oConnection->get();
        $result = $query->result_array();
        return $result;
    }

    /**
     * Retorna el domicilio del alumno formateado (listo para ser impreso)
     *
     * @return string
     */
    public function getDomicilioFormateado() {
        return funciones::formatearDomicilio($this->calle, $this->calle_numero, $this->calle_complemento);
    }

    public function getMatriculasPeriodosPlanAcademico($codplan, $estado = null, $periodo = null, $agruparMatricula = false, $sinEstadoMigrado = false, $validarPorCurso = false) {
        $conexion = $this->oConnection;
        if ($validarPorCurso){
            $myPlanAcademico = new Vplanes_academicos($conexion, $codplan);
            $codCurso = $myPlanAcademico->cod_curso;
        }
        $conexion->select("MAX(matriculas_estado_historicos.codigo)");
        $conexion->from("matriculas_estado_historicos");
        $conexion->where("matriculas_estado_historicos.cod_matricula_periodo = matriculas_periodos.codigo");
        $sqCodigoHistorico = $conexion->return_query();
        $conexion->resetear();
        $conexion->select('matriculas_periodos.codigo AS cod_matricula_periodo');
        $conexion->select('matriculas_periodos.cod_matricula');
        $conexion->select('matriculas_periodos.fecha_emision');
        $conexion->select('matriculas_periodos.estado');
        $conexion->select('matriculas_periodos.cod_tipo_periodo');
        $conexion->select('matriculas_periodos.modalidad');
        $conexion->select('general.tipos_periodos.nombre');
        $conexion->select("($sqCodigoHistorico) AS codigo_estado_historico");
        $conexion->from("matriculas_periodos");
        $conexion->join("matriculas", "matriculas.codigo = matriculas_periodos.cod_matricula");
        $conexion->join("general.tipos_periodos", "general.tipos_periodos.codigo = matriculas_periodos.cod_tipo_periodo");
        if ($validarPorCurso){
            $conexion->join("general.planes_academicos", "general.planes_academicos.codigo = matriculas.cod_plan_academico");
            $conexion->where("general.planes_academicos.cod_curso", $codCurso);
        } else {
            $conexion->where("matriculas.cod_plan_academico", $codplan);
        }
        $conexion->where("matriculas.cod_alumno", $this->codigo);
        if ($sinEstadoMigrado) {
            $conexion->where('matriculas_periodos.estado <>', 'migrado');
        }
        $conexion->order_by("general.tipos_periodos.codigo", "asc");
        if ($estado !== null) {
            $conexion->where("matriculas_periodos.estado", $estado);
        }
        if ($periodo != null) {
            $conexion->where('matriculas_periodos.cod_tipo_periodo', $periodo);
        }
        if ($agruparMatricula) {
            $conexion->group_by("matriculas_periodos.cod_matricula");
        }
        $query = $conexion->get();
        return $query->result_array();
    }

    public function getPeriodosMatricularPlanAcademico($cod_plan, $cod_filial, $orden = null) {
        $conexion = $this->oConnection;
        $myPlanAcademico = new Vplanes_academicos($conexion, $cod_plan);
        $cod_curso = $myPlanAcademico->cod_curso;
        $conexion->select('matriculas_periodos.cod_tipo_periodo');
        $conexion->from('matriculas_periodos');
        $conexion->join('matriculas', 'matriculas.codigo = matriculas_periodos.cod_matricula');
        $conexion->join('general.planes_academicos', 'general.planes_academicos.codigo = matriculas.cod_plan_academico');
        $conexion->where('matriculas.cod_alumno', $this->codigo);
        $conexion->where('general.planes_academicos.cod_curso', $cod_curso);
        $subquery = $conexion->return_query();
        $conexion->resetear();
        $conexion->select('*');
        $conexion->from('general.tipos_periodos');
        $conexion->join('general.planes_academicos_periodos', 'general.planes_academicos_periodos.cod_tipo_periodo = general.tipos_periodos.codigo');
        $conexion->join('general.planes_academicos_filiales', 'general.planes_academicos_filiales.cod_tipo_periodo = general.tipos_periodos.codigo AND general.planes_academicos_filiales.cod_plan_academico = ' . $cod_plan . ' AND general.planes_academicos_filiales.cod_filial = ' . $cod_filial . '');
        $conexion->where('general.planes_academicos_periodos.cod_plan_academico', $cod_plan);
        $conexion->where('general.tipos_periodos.codigo NOT IN (' . $subquery . ')');
        $conexion->where('general.planes_academicos_filiales.estado', 'habilitado');
        $conexion->group_by('general.tipos_periodos.codigo');
        if ($orden != null) {
            $conexion->order_by($orden['campo'], $orden['orden']);
        }
        $query = $conexion->get();
        return $query->result_array();
    }

    public function setAspirante_Alumno($arrayAspAlu) {
        $this->oConnection->insert('aspirantes_alumnos', $arrayAspAlu);
    }

    /**
     * retorna todas las materias de un alumno de una plan
     * @access public
     * @return Array materias
     */
    public function getEstadoAcademico($cod_plan_academico, $codMateria = null, $cod_periodo = null) {
        $this->oConnection->select('estadoacademico.*, general.materias.nombre_es, general.materias.nombre_in, general.materias.nombre_pt, general.materias_plan_academico.cod_tipo_periodo');
        $this->oConnection->select('(SELECT codigo FROM matriculas_inscripciones WHERE matriculas_inscripciones.cod_estado_academico = estadoacademico.codigo AND baja = 0 ORDER BY codigo DESC LIMIT 0, 1) AS inscripcion', false);
        $this->oConnection->from('estadoacademico');
        $this->oConnection->join('general.materias', 'general.materias.codigo = estadoacademico.codmateria');
        $this->oConnection->join('general.materias_plan_academico', 
                                    'general.materias_plan_academico.cod_materia = estadoacademico.codmateria');
        $this->oConnection->where('cod_matricula_periodo IN (select matriculas_periodos.codigo from matriculas_periodos 
                                                             join matriculas on matriculas.codigo = matriculas_periodos.cod_matricula where matriculas.cod_alumno = ' . $this->codigo . ' and matriculas.cod_plan_academico = ' . $cod_plan_academico . ')');
        $this->oConnection->where('general.materias_plan_academico.cod_plan', $cod_plan_academico);
        $this->oConnection->where('estadoacademico.estado <>', 'migrado');
        if ($codMateria != null)
            $this->oConnection->where("estadoacademico.codmateria", $codMateria);
        if ($cod_periodo != null)
            $this->oConnection->where("general.materias_plan_academico.cod_tipo_periodo", $cod_periodo);
        $this->oConnection->order_by('general.materias_plan_academico.cod_tipo_periodo', 'asc');
        $this->oConnection->order_by('general.materias.nombre_' . get_idioma(), 'asc');
        $query = $this->oConnection->get();
        return $query->result_array();
    }

    public function getMatriculasPlanAcademico($cod_plan_academico, $estado = null) {
        $conexion = $this->oConnection;
        $conexion->select('*');
        $conexion->from("matriculas");
        $conexion->where("matriculas.cod_alumno", $this->codigo);
        $conexion->where("matriculas.cod_plan_academico", $cod_plan_academico);
        if ($estado != null) {
            $conexion->where('matriculas.estado', $estado);
        }
        $query = $conexion->get();
        return $query->result_array();
    }

    static function getAlumnos($conexion, $arrCondindiciones, $buscar, $separador, $limit = 10) {
        $nombreApellido = formatearNomApeQuery();
        $conexion->select("CONCAT($nombreApellido) as nombre_apellido", false);
        $conexion->select("CONCAT(general.documentos_tipos.nombre, ' ', alumnos.documento) as documento_completo", false);
        $conexion->join('general.documentos_tipos', 'alumnos.tipo = general.documentos_tipos.codigo');
        $conexion->or_having("REPLACE(nombre_apellido, '$separador ',' ') LIKE REPLACE('%$buscar%', '$separador ',' ')", false);
        $conexion->or_having("documento_completo LIKE '%$buscar%'", false);
        $conexion->or_having("alumnos.codigo LIKE '%$buscar%'", false);
        if ($limit != null) {
            $conexion->limit($limit);
        }
        $conexion->order_by("alumnos.codigo DESC");
        $alumnos = Valumnos::listarAlumnos($conexion, $arrCondindiciones);
        return $alumnos;
    }

    public function getCertificadosCertificar($cod_filial) {
        $this->oConnection->select('general.certificados_plan_filial.cod_plan_academico, general.certificados_plan_filial.cod_tipo_periodo,general.certificados_plan_filial.cod_certificante, general.certificados_plan_filial.opcional,  general.certificados_certificantes.nombre');
        $this->oConnection->select('(SELECT certificados.cod_matricula_periodo FROM certificados '
                . 'INNER JOIN matriculas_periodos ON matriculas_periodos.codigo = certificados.cod_matricula_periodo '
                . 'INNER JOIN matriculas ON matriculas.codigo = matriculas_periodos.cod_matricula '
                . 'WHERE certificados.cod_certificante = general.certificados_plan_filial.cod_certificante '
                . 'AND matriculas_periodos.cod_tipo_periodo = general.certificados_plan_filial.cod_tipo_periodo '
                . 'AND matriculas.cod_plan_academico = general.certificados_plan_filial.cod_plan_academico '
                . 'AND general.certificados_plan_filial.cod_filial = ' . $cod_filial . ' AND matriculas.cod_alumno = ' . $this->codigo . ') AS certificado_pedido');
        $this->oConnection->select('(SELECT general.planes_academicos_periodos.titulo from general.planes_academicos_periodos '
                . 'where general.planes_academicos_periodos.cod_plan_academico = general.certificados_plan_filial.cod_plan_academico '
                . 'and general.planes_academicos_periodos.cod_tipo_periodo = general.certificados_plan_filial.cod_tipo_periodo) AS titulo');
        $this->oConnection->from('general.certificados_plan_filial');
        $this->oConnection->join('general.certificados_certificantes', 'general.certificados_certificantes.codigo = general.certificados_plan_filial.cod_certificante');
        $this->oConnection->where('general.certificados_plan_filial.cod_filial', $cod_filial);
        $this->oConnection->where('general.certificados_plan_filial.cod_tipo_periodo IN (SELECT matriculas_periodos.cod_tipo_periodo FROM matriculas_periodos INNER JOIN matriculas ON matriculas.codigo = matriculas_periodos.cod_matricula AND matriculas.cod_alumno = ' . $this->codigo . ')');
        $this->oConnection->where('general.certificados_plan_filial.cod_plan_academico IN (SELECT matriculas.cod_plan_academico FROM matriculas where cod_alumno = ' . $this->codigo . ')');
        $this->oConnection->having('certificado_pedido IS NULL');
        $query = $this->oConnection->get();
        return $query->result_array();
    }

    public function getMatriculasPeriodos($estado = null, $sinestado = null) {
        $conexion = $this->oConnection;
        $conexion->select('matriculas_periodos.codigo as cod_matricula_periodo, matriculas_periodos.cod_matricula, matriculas_periodos.fecha_emision, matriculas_periodos.estado, matriculas_periodos.cod_tipo_periodo, matriculas.cod_plan_academico');
        $conexion->from("matriculas_periodos");
        $conexion->join("matriculas", "matriculas.codigo = matriculas_periodos.cod_matricula");
        $conexion->where("matriculas.cod_alumno", $this->codigo);
        if ($estado != null) {
            $conexion->where("matriculas_periodos.estado", $estado);
        }
        if ($sinestado != null) {
            $conexion->where("matriculas_periodos.estado <>", $sinestado);
        }
        $query = $conexion->get();
        return $query->result_array();
    }

    public function ver_facturas_alumno() {
        $this->oConnection->select('razones_sociales.razon_social,facturas.punto_venta,general.tipos_facturas.factura,facturas_propiedades.valor as nrofact,facturas.total');
        $this->oConnection->from('alumnos');
        $this->oConnection->join('alumnos_razones', 'alumnos_razones.cod_alumno = alumnos.codigo');
        $this->oConnection->join('razones_sociales', 'razones_sociales.codigo = alumnos_razones.cod_razon_social');
        $this->oConnection->join('ctacte', 'ctacte.cod_alumno = alumnos.codigo');
        $this->oConnection->join('facturas_renglones', 'facturas_renglones.cod_ctacte = ctacte.codigo');
        $this->oConnection->join('facturas', 'facturas.codigo = facturas_renglones.cod_factura');
        $this->oConnection->join("facturas_propiedades", "facturas_propiedades.cod_factura = facturas.codigo AND facturas_propiedades.propiedad = 'numero_factura'");
        $this->oConnection->join('general.puntos_venta', 'general.puntos_venta.codigo = facturas.punto_venta');
        $this->oConnection->join('general.tipos_facturas', 'general.tipos_facturas.codigo = general.puntos_venta.tipo_factura');
        $this->oConnection->where('alumnos.codigo', $this->codigo);
        $this->oConnection->group_by('facturas.codigo');
        $query = $this->oConnection->get();
        return $query->result_array();
    }

    public function getSacado() {
        $mySacado = new Vsacados();
        $arrRazones = $this->getRazonSocialDefault();
        $myRazonSocial = new Vrazones_sociales($this->oConnection, $arrRazones[0]['cod_razon_social']);
        $mySacado = new Vsacados();
        $mySacado->cpf_cnpj = $myRazonSocial->documento;
        $mySacado->nombre = $myRazonSocial->razon_social;
        $mySacado->direccion = $myRazonSocial->direccion_calle . " " . $myRazonSocial->direccion_numero;
        if ($myRazonSocial->direccion_complemento <> '')
            $mySacado->direccion .= "({$myRazonSocial->direccion_complemento})";
        $mySacado->cod_postal = $myRazonSocial->codigo_postal;
        $mySacado->ciudad = new Vlocalidades($this->oConnection, $myRazonSocial->cod_localidad);
        $mySacado->provincia = new Vprovincias($this->oConnection, $mySacado->ciudad->provincia_id);
        return $mySacado;
    }

    public function getCtaCteSinMora($debe = null, $cod_concepto = null, $concepto = null) {
        $this->oConnection->select('ctacte.*, (ctacte.importe - ctacte.pagado) AS saldo, (SELECT COUNT(t2.codigo) FROM ctacte AS t2 WHERE t2.cod_concepto = 3 AND t2.concepto = ctacte.codigo AND t2.habilitado IN (1,2)) AS cantmoras', FALSE);
        $this->oConnection->from('ctacte');
        $this->oConnection->where('ctacte.cod_alumno', $this->codigo);
        $this->oConnection->where('ctacte.habilitado IN (1,2)');
        if ($debe) {
            $this->oConnection->where('ctacte.importe > ctacte.pagado');
        }
        if ($cod_concepto != null && $concepto != null) {
            $this->oConnection->where('ctacte.cod_concepto', $cod_concepto);
            $this->oConnection->where('ctacte.concepto', $concepto);
        }
        $this->oConnection->having('cantmoras <', 1);
        $query = $this->oConnection->get();
        $arrCtaCte = $query->result_array();
        return $arrCtaCte;
    }

    /**
     * asigna razon social a alumno.
     * @access public
     */
    function setRazonSocial($delete, $cod_razon, $default, $defaultfact) {
        if ($delete) {
            $arrDatos = array('cod_alumno' => $this->codigo, 'cod_razon_social' => $cod_razon);
            $this->oConnection->delete('alumnos_razones', $arrDatos);
        }
        $arrDatos = array('cod_alumno' => $this->codigo, 'cod_razon_social' => $cod_razon, 'default' => $default, 'default_facturacion' => $defaultfact);
        $this->oConnection->insert('alumnos_razones', $arrDatos);
    }

    public function updateRazonSocial($cod_razon, $default, $defaultfact) {
        $arrUpdate = array(
            "default" => $default,
            "default_facturacion" => $defaultfact
        );
        $this->oConnection->where('alumnos_razones.cod_alumno', $this->codigo);
        $this->oConnection->where('alumnos_razones.cod_razon_social', $cod_razon);
        $this->oConnection->update('alumnos_razones', $arrUpdate);
    }

    /**
     * desasigna todas las razones
     * @access public
     */
    function desetearRazones() {
        $this->oConnection->delete('alumnos_razones', array('cod_alumno' => $this->codigo));
    }

    function setLoginEnvio($md5, $filial, $conexion = null) {
        $arrDatos = array(
            "cod_alumno" => $this->codigo,
            "cod_filial" => $filial,
            "md5" => $md5
        );
        if($conexion == null){
            $this->oConnection->insert('alumnos.login_envio', $arrDatos);
        } else {
            $conexion->insert('alumnos.login_envio', $arrDatos);
        }
    }

    public function getProximasMesasExamenes($campus = null) {
        $this->oConnection->select('count(examenes_estado_academico.cod_estado_academico)', false);
        $this->oConnection->from('examenes_estado_academico');
        $this->oConnection->where('examenes.codigo = examenes_estado_academico.cod_examen');
        $this->oConnection->where('examenes_estado_academico.estado', 'pendiente');
        $subquery = $this->oConnection->return_query();
        $this->oConnection->resetear();
        $this->oConnection->select('examenes_estado_academico.codigo');
        $this->oConnection->from('examenes_estado_academico');
        $this->oConnection->where('examenes_estado_academico.cod_examen = examenes.codigo');
        $this->oConnection->where('examenes_estado_academico.cod_estado_academico = estadoacademico.codigo');
        $this->oConnection->where('examenes_estado_academico.estado <>', 'baja');
        $subquery2 = $this->oConnection->return_query();
        $this->oConnection->resetear();
        $this->oConnection->select('general.materias.codigo as cod_materia');
        $this->oConnection->select('general.materias.nombre_es');
        $this->oConnection->select('general.materias.nombre_pt');
        $this->oConnection->select('general.materias.nombre_in');
        $this->oConnection->select('examenes.codigo as cod_examen');
        $this->oConnection->select("concat(examenes.fecha,' ',examenes.hora) fecha_hora", false);
        $this->oConnection->select("examenes.cupo - ($subquery) as cupo_disponible", false);
        $this->oConnection->select("EXISTS($subquery2) as inscripto", false);

        $this->oConnection->select("(SELECT COUNT(esta1.codigo) "
            . "FROM examenes_estado_academico AS esta1 "
            . "JOIN examenes AS exa1 ON exa1.codigo = esta1.cod_examen "
            . "WHERE esta1.cod_estado_academico = estadoacademico.codigo "
            . "AND esta1.estado IN ('aprobado', 'pendiente') "
            . "AND esta1.cod_examen = examenes.codigo AND exa1.materia = examenes.materia ) AS countIns", false);
        $this->oConnection->from('matriculas');
        $this->oConnection->join('matriculas_periodos', 'matriculas_periodos.cod_matricula = matriculas.codigo');
        $this->oConnection->join('estadoacademico', "estadoacademico.cod_matricula_periodo = matriculas_periodos.codigo AND estadoacademico.estado IN ('reprobado', 'libre', 'cursando', 'regular')");
        $this->oConnection->join('examenes', 'examenes.materia = estadoacademico.codmateria');
        $this->oConnection->join('general.materias', 'general.materias.codigo = examenes.materia');
        $this->oConnection->where("(examenes.tipoexamen = 'FINAL' OR examenes.tipoexamen = 'RECUPERATORIO_FINAL')");
        $this->oConnection->where('matriculas.cod_alumno', $this->codigo);
        if($campus != null){
        $this->oConnection->where('examenes.ver_campus', 1);
        }
        $this->oConnection->where('examenes.baja', 0);
        $this->oConnection->where('examenes.fecha > date_add(curdate(), interval 0 day)');
        $this->oConnection->having("countIns = 0");
        $query = $this->oConnection->get();
        return $query->result_array();
    }

    public function getUltimasNotasCargadas() {
        $this->oConnection->select('examenes_estado_academico.codigo as cod_inscripcion');
        $this->oConnection->select("concat(examenes.fecha,' ',examenes.hora) fecha_hora", false);
        $this->oConnection->select('general.materias.nombre_es');
        $this->oConnection->from('notas_resultados');
        $this->oConnection->join('examenes_estado_academico', 'examenes_estado_academico.codigo = notas_resultados.cod_inscripcion');
        $this->oConnection->join('examenes', 'examenes.codigo = examenes_estado_academico.cod_examen');
        $this->oConnection->join('estadoacademico', 'estadoacademico.codigo = examenes_estado_academico.cod_estado_academico');
        $this->oConnection->join('matriculas_periodos', 'matriculas_periodos.codigo = estadoacademico.cod_matricula_periodo');
        $this->oConnection->join('matriculas', 'matriculas.codigo = matriculas_periodos.cod_matricula');
        $this->oConnection->join('alumnos', 'alumnos.codigo = matriculas.cod_alumno');
        $this->oConnection->join('general.materias', 'general.materias.codigo = examenes.materia');
        $this->oConnection->where('examenes.baja', 0);
        $this->oConnection->where('alumnos.codigo', $this->codigo);
        $this->oConnection->group_by('examenes.codigo');
        $query = $this->oConnection->get();
        return $query->result_array();
    }

    public function getProximasClases() {
        $this->oConnection->select('horarios.codigo');
        $this->oConnection->select('salones.salon');
        $this->oConnection->select('general.materias.nombre_es');
        $this->oConnection->select('general.materias.nombre_pt');
        $this->oConnection->select('general.materias.nombre_in');
        $this->oConnection->select("concat(horarios.dia,' ',horarios.horadesde) as fecha_hora", false);
        $this->oConnection->from('horarios');
        $this->oConnection->join('matriculas_horarios', 'matriculas_horarios.cod_horario = horarios.codigo');
        $this->oConnection->join('estadoacademico', 'estadoacademico.codigo = matriculas_horarios.cod_estado_academico');
        $this->oConnection->join('matriculas_periodos', 'matriculas_periodos.codigo = estadoacademico.cod_matricula_periodo');
        $this->oConnection->join('matriculas', 'matriculas.codigo = matriculas_periodos.cod_matricula');
        $this->oConnection->join('salones', 'salones.codigo = horarios.cod_salon');
        $this->oConnection->join('general.materias', 'general.materias.codigo = horarios.cod_materia');
        $this->oConnection->where('matriculas.cod_alumno', $this->codigo);
        $this->oConnection->where('matriculas_periodos.estado', 'habilitada');
        $this->oConnection->where('estadoacademico.estado', 'cursando');
        $this->oConnection->where('horarios.baja', 0);
        $this->oConnection->where('horarios.dia > CURDATE()');
        $this->oConnection->group_by("general.materias.codigo");
        $this->oConnection->group_by("horarios.dia");
        $this->oConnection->group_by("horarios.horadesde");
        $this->oConnection->order_by('fecha_hora', 'ASC');        
        $this->oConnection->limit(10);
        $query = $this->oConnection->get();
        return $query->result_array();
    }

    static function getLoginFilial(CI_DB_mysqli_driver $conexion, $userName = null, $passmd5 = null, $codlogin = null, $cod_filial = null) {
        $conexion->select("*");
        $conexion->from("alumnos.login_filial");
        $conexion->join("alumnos.login", "alumnos.login.codigo = alumnos.login_filial.cod_login");
        if ($codlogin != null) {
            $conexion->where("alumnos.login_filial.cod_login", $codlogin);
        } else {
            if ($userName != null) {
                $conexion->where("alumnos.login.user", $userName);
            }if ($passmd5 != null) {
                $conexion->where("alumnos.login.pass", $passmd5);
            }
        }
        if ($cod_filial != null) {
            $conexion->where("alumnos.login_filial.cod_filial", $cod_filial);
        }
        $query = $conexion->get();
        $arrResp = $query->result_array();
        return $arrResp;
    }

    public function getLogin($cod_filial) {
        $this->oConnection->select("alumnos.login_filial.*, alumnos.login.user, alumnos.login.pass", false);
        $this->oConnection->from("alumnos.login_filial");
        $this->oConnection->join("alumnos.login", "alumnos.login.codigo = alumnos.login_filial.cod_login");
        $this->oConnection->where("alumnos.login_filial.cod_alumno", $this->codigo);
        $this->oConnection->where("alumnos.login_filial.cod_filial", $cod_filial);
        $query = $this->oConnection->get();
        $arrResp = $query->result_array();
        return $arrResp;
    }

    public function getMatriculasHorarios($condiciones) {//muy lento
        $conexion = $this->oConnection;
        $conexion->select('matriculas_horarios.*, horarios.dia, horarios.cod_comision');
        $conexion->from('matriculas_horarios');
        $conexion->join('horarios', 'horarios.codigo = matriculas_horarios.cod_horario');
        $conexion->where($condiciones);
        $conexion->where("matriculas_horarios.cod_estado_academico IN (SELECT estadoacademico.codigo  FROM estadoacademico JOIN matriculas_periodos ON estadoacademico.cod_matricula_periodo = matriculas_periodos.codigo
        JOIN matriculas ON matriculas.codigo = matriculas_periodos.cod_matricula WHERE matriculas.cod_alumno = $this->codigo)");
        $query = $conexion->get();
        return $query->result_array();
    }

    static function getLoginEnvio(CI_DB_mysqli_driver $conexion, $md5 = null) {
        $conexion->select("alumnos.login_envio.*", false);
        $conexion->from("alumnos.login_envio");
        if ($md5 != null) {
            $conexion->where("alumnos.login_envio.md5", $md5);
        }
        $query = $conexion->get();
        $arrResp = $query->result_array();
        return $arrResp;
    }

    function setLoginAConfirmar($md5, $user, $pass, $cod_filial, $fecha_hora) {
        $arrDatos = array(
            "md5" => $md5,
            "user" => $user,
            "pass" => $pass,
            "cod_alumno" => $this->codigo,
            "cod_filial" => $cod_filial,
            "fecha_hora" => $fecha_hora,
            "estado" => 'no_confirmado'
        );
        return $this->oConnection->insert('alumnos.login_aconfirmar', $arrDatos);
    }

    static function setCaducadoLoginAConfirmar(CI_DB_mysqli_driver $conexion, $md5) {
        $arrDatos = array(
            "estado" => 'caducado'
        );
        $conexion->where('alumnos.login_aconfirmar.md5', $md5);
        return $conexion->update('alumnos.login_aconfirmar', $arrDatos);
    }

    static function setConfirmadoLoginAConfirmar(CI_DB_mysqli_driver $conexion, $md5) {
        $arrDatos = array(
            "estado" => 'confirmado'
        );
        $conexion->where('alumnos.login_aconfirmar.md5', $md5);
        return $conexion->update('alumnos.login_aconfirmar', $arrDatos);
    }

    static function getLoginAConfirmar(CI_DB_mysqli_driver $conexion, $md5 = null, $estado = null, $cod_alumno = null, $cod_filial = null) {
        $conexion->select("alumnos.login_aconfirmar.*", false);
        $conexion->from("alumnos.login_aconfirmar");
        if ($md5 != null) {
            $conexion->where("alumnos.login_aconfirmar.md5", $md5);
        }
        if ($estado != null) {
            $conexion->where("alumnos.login_aconfirmar.estado", $estado);
        }
        if ($cod_alumno != null) {
            $conexion->where("alumnos.login_aconfirmar.cod_alumno", $cod_alumno);
        }
        if ($cod_filial != null) {
            $conexion->where("alumnos.login_aconfirmar.cod_filial", $cod_filial);
        }
        $query = $conexion->get();
        $arrResp = $query->result_array();
        return $arrResp;
    }

    static function setLogin(CI_DB_mysqli_driver $conexion, $user, $pass) {
        $arrDatos = array(
            "user" => $user,
            "pass" => $pass
        );
        $conexion->insert('alumnos.login', $arrDatos);
        return $conexion->insert_id();
    }

    static function setLoginHistorico(CI_DB_mysqli_driver $conexion, $user, $pass, $cod_login, $fecha_hora, $md5) {
        $arrDatos = array(
            "user" => $user,
            "pass" => $pass,
            "cod_login" => $cod_login,
            "fecha_hora" => $fecha_hora,
            "md5" => $md5
        );
        $conexion->insert('alumnos.login_historico', $arrDatos);
        return $conexion->insert_id();
    }

    static function getAnteultimoLoginHistorico(CI_DB_mysqli_driver $conexion, $cod_login) {
        $conexion->select("*");
        $conexion->from("alumnos.login_historico");
        $conexion->where("cod_login", $cod_login);
        $conexion->order_by("codigo", "desc");
        $conexion->limit(1, 1);
        $query = $conexion->get();
        $arrResp = $query->result_array();
        return $arrResp[0];
    }

    function setLoginFilial($cod_login, $cod_filial) {
        $arrDatos = array(
            "cod_login" => $cod_login,
            "cod_filial" => $cod_filial,
            "cod_alumno" => $this->codigo
        );
        return $this->oConnection->insert('alumnos.login_filial', $arrDatos);
    }

    function setIntentoFallido($cod_filial) {
        $arrDatos = array(
            "cod_filial" => $cod_filial,
            "fecha_hora" => date('Y-m-d H:i:s'),
            "cod_alumno" => $this->codigo
        );
        return $this->oConnection->insert('alumnos.intentos_login', $arrDatos);
    }

    function getCantidadIntentosLogin($cod_filial) {
        $this->oConnection->select("*");
        $this->oConnection->from("alumnos.intentos_login");
        $this->oConnection->where("cod_alumno", $this->codigo);
        $this->oConnection->where("cod_filial", $cod_filial);
        $query = $this->oConnection->get();
        $respuesta = $query->num_rows();
        return $respuesta;
    }

    static function setLoginRecuperaPass(CI_DB_mysqli_driver $conexion, $md5, $cod_login, $fecha_hora) {
        $arrDatos = array(
            "md5" => $md5,
            "cod_login" => $cod_login,
            "fecha_hora" => $fecha_hora,
            "estado" => 'no_usado'
        );
        return $conexion->insert('alumnos.login_recupera_pass', $arrDatos);
    }

    static function getLoginRecuperaPass(CI_DB_mysqli_driver $conexion, $md5 = null) {
        $conexion->select("*");
        $conexion->from("alumnos.login_recupera_pass");
        if ($md5 != null) {
            $conexion->where("alumnos.login_recupera_pass.md5", $md5);
        }
        $query = $conexion->get();
        $arrResp = $query->result_array();
        return $arrResp;
    }

    static function setUsadoRecuperaPass(CI_DB_mysqli_driver $conexion, $md5) {
        $arrDatos = array(
            "estado" => 'usado'
        );
        $conexion->where('md5', $md5);
        return $conexion->update('alumnos.login_recupera_pass', $arrDatos);
    }

    static function setUserLogin(CI_DB_mysqli_driver $conexion, $cod_login, $user) {
        $arrDatos = array(
            "user" => $user
        );
        $conexion->where('cod_login', $cod_login);
        return $conexion->update('alumnos.login', $arrDatos);
    }

    static function updateLogin(CI_DB_mysqli_driver $conexion, $cod_login, $user = null, $pass = null) {
        $arrDatos = array();
        if ($user != null) {
            $arrDatos['user'] = $user;
        }
        if ($pass != null) {
            $arrDatos['pass'] = $pass;
        }
        $conexion->where('codigo', $cod_login);
        return $conexion->update('alumnos.login', $arrDatos);
    }

    static function getLoginHistorico(CI_DB_mysqli_driver $conexion, $md5) {
        $conexion->select("*");
        $conexion->from("alumnos.login_historico");
        $conexion->where("md5", $md5);
        $query = $conexion->get();
        $arrResp = $query->result_array();
        return $arrResp;
    }

    public function getDetalleExamenAlumnno($cod_examen, $cod_materia) {
        $this->oConnection->select('examenes_estado_academico.fechadeinscripcion');
        $this->oConnection->from('examenes_estado_academico');
        $this->oConnection->join('estadoacademico', 'estadoacademico.codigo = examenes_estado_academico.cod_estado_academico');
        $this->oConnection->join('matriculas_periodos', 'matriculas_periodos.codigo = estadoacademico.cod_matricula_periodo');
        $this->oConnection->join('matriculas', 'matriculas.codigo = matriculas_periodos.cod_matricula');
        $this->oConnection->where('examenes_estado_academico.cod_examen', $cod_examen);
        $this->oConnection->where('examenes_estado_academico.estado <>', 'baja');
        $this->oConnection->where('matriculas.cod_alumno', $this->codigo);
        $this->oConnection->group_by('examenes_estado_academico.cod_examen');
        $subquery = $this->oConnection->return_query();
        $this->oConnection->resetear();
        $this->oConnection->select("GROUP_CONCAT(CONCAT(profesores.nombre,', ',profesores.apellido) SEPARATOR '/')", false);
        $this->oConnection->from('profesores');
        $this->oConnection->join('examenes_profesor', 'examenes_profesor.codprofesor = profesores.codigo');
        $this->oConnection->where('examenes_profesor.codexamen', $cod_examen);
        $this->oConnection->where('examenes_profesor.baja', 0);
        $this->oConnection->group_by('examenes_profesor.codexamen');
        $subquery1 = $this->oConnection->return_query();
        $this->oConnection->resetear();
        $this->oConnection->select("GROUP_CONCAT(concat(salones.salon) SEPARATOR '/')", false);
        $this->oConnection->from('salones');
        $this->oConnection->join('examenes_salones', 'examenes_salones.cod_salon = salones.codigo');
        $this->oConnection->where('examenes_salones.cod_examen', $cod_examen);
        $this->oConnection->where('examenes_salones.baja', 0);
        $this->oConnection->group_by('examenes_salones.cod_examen');
        $subquery2 = $this->oConnection->return_query();
        $this->oConnection->resetear();
        $this->oConnection->select('examenes_estado_academico.codigo');
        $this->oConnection->from('examenes_estado_academico');
        $this->oConnection->join('estadoacademico', 'estadoacademico.codigo = examenes_estado_academico.cod_estado_academico');
        $this->oConnection->join('matriculas_periodos', 'matriculas_periodos.codigo = estadoacademico.cod_matricula_periodo');
        $this->oConnection->join('matriculas', 'matriculas.codigo = matriculas_periodos.cod_matricula');
        $this->oConnection->where('examenes_estado_academico.cod_examen', $cod_examen);
        $this->oConnection->where('examenes_estado_academico.estado <>', 'baja');
        $this->oConnection->where('matriculas.cod_alumno', $this->codigo);
        $subquery3 = $this->oConnection->return_query();
        $this->oConnection->resetear();
        $this->oConnection->select('examenes_estado_academico.codigo');
        $this->oConnection->from('examenes_estado_academico');
        $this->oConnection->join('estadoacademico', 'estadoacademico.codigo = examenes_estado_academico.cod_estado_academico');
        $this->oConnection->join('matriculas_periodos', 'matriculas_periodos.codigo = estadoacademico.cod_matricula_periodo');
        $this->oConnection->join('matriculas', 'matriculas.codigo = matriculas_periodos.cod_matricula');
        $this->oConnection->where('examenes_estado_academico.cod_examen', $cod_examen);
        $this->oConnection->where('examenes_estado_academico.estado <>', 'baja');
        $this->oConnection->where('matriculas.cod_alumno', $this->codigo);
        $subquery4 = $this->oConnection->return_query();
        $this->oConnection->resetear();
        $this->oConnection->select('examenes_estado_academico.estado');
        $this->oConnection->from('examenes_estado_academico');
        $this->oConnection->join('estadoacademico', 'estadoacademico.codigo = examenes_estado_academico.cod_estado_academico');
        $this->oConnection->join('matriculas_periodos', 'matriculas_periodos.codigo = estadoacademico.cod_matricula_periodo');
        $this->oConnection->join('matriculas', 'matriculas.codigo = matriculas_periodos.cod_matricula');
        $this->oConnection->where('examenes_estado_academico.cod_examen', $cod_examen);
        $this->oConnection->where('examenes_estado_academico.estado <>', 'baja');
        $this->oConnection->where('matriculas.cod_alumno', $this->codigo);
        $subquery5 = $this->oConnection->return_query();
        $this->oConnection->resetear();
        $this->oConnection->select("IFNULL(($subquery4),'') as cod_inscripcion", false);
        $this->oConnection->select('estadoacademico.codigo as cod_estado_academico');
        $this->oConnection->select('general.materias.nombre_es');
        $this->oConnection->select("IFNULL(($subquery),'') as fecha_inscripcion", false);
        $this->oConnection->select("($subquery1) as profesor", false);
        $this->oConnection->select("($subquery2) as salon", false);
        $this->oConnection->select("EXISTS($subquery3) as inscripto", false);
        $this->oConnection->select("IFNULL(($subquery5),'') as estado_inscripcion", false);
        $this->oConnection->from('matriculas');
        $this->oConnection->join('matriculas_periodos', 'matriculas_periodos.cod_matricula = matriculas.codigo');
        $this->oConnection->join('estadoacademico', 'estadoacademico.cod_matricula_periodo = matriculas_periodos.codigo');
        $this->oConnection->join('general.materias', 'general.materias.codigo = estadoacademico.codmateria');
        $this->oConnection->where('matriculas.cod_alumno', $this->codigo);
        $this->oConnection->where('estadoacademico.codmateria', $cod_materia);
        $query = $this->oConnection->get();
        return $query->result_array();
    }

    public function getDescuentosCondicionas($estado = null) {
        $this->oConnection->join("ctacte", "ctacte.codigo = matriculaciones_ctacte_descuento.cod_ctacte AND ctacte.cod_alumno = {$this->codigo} AND ctacte.importe > ctacte.pagado");
        if ($estado != null) {
            $tipoFiltro = is_array($estado) ? "where_in" : "where";
            $this->oConnection->$tipoFiltro("matriculaciones_ctacte_descuento.estado", $estado);
        }
        return Vmatriculaciones_ctacte_descuento::listarMatriculaciones_ctacte_descuento($this->oConnection);
    }

    public function getCtaCteCobrar($condiciones) {
        $condiciones['cod_alumno'] = $this->codigo;
        $rowsCtacte = Vctacte::getCtaCteCobrar($this->oConnection, $condiciones);
        return $rowsCtacte;
    }

    function desetearResponsables() {
        $this->oConnection->delete('alumnos_responsables', array('cod_alumno' => $this->codigo));
    }

    public function getDetallesAlertasEmailCampus() {
        $this->oConnection->select('alertas_alumnos.cod_alerta, alertas_alumnos.estado,alertas.fecha_hora');
        $this->oConnection->from('alertas_alumnos');
        $this->oConnection->join('alertas', 'alertas.codigo = alertas_alumnos.cod_alerta');
        $this->oConnection->where('alertas_alumnos.cod_alumno', $this->codigo);
        $this->oConnection->order_by('alertas_alumnos.cod_alerta', 'desc');
        $query = $this->oConnection->get();
        return $query->result_array();
    }

    public function getFacturas() {
        $conexion = $this->oConnection;
        $conexion->select('facturas.*, facturas_propiedades.valor as numero, general.puntos_venta.prefijo, general.tipos_facturas.factura as tipo');
        $conexion->from("facturas");
        $conexion->join("alumnos_razones", "alumnos_razones.cod_razon_social = facturas.codrazsoc");
        $conexion->join("facturas_propiedades", "facturas_propiedades.cod_factura = facturas.codigo and facturas_propiedades.propiedad = 'numero_factura'");
        $conexion->join("general.puntos_venta", "general.puntos_venta.codigo = facturas.punto_venta");
        $conexion->join("general.tipos_facturas", "general.tipos_facturas.codigo = general.puntos_venta.tipo_factura");
        $conexion->where("alumnos_razones.cod_alumno", $this->codigo);
        $conexion->where("facturas.estado", 'habilitada');
        $conexion->order_by("facturas.codigo", "desc");
        $query = $conexion->get();
        return $query->result_array();
    }

    public function getCursosDisponibles(){
        $conexion = $this->oConnection;
        $conexion->select("`general`.`cursos`.*");
        $conexion->from("matriculas");
        $conexion->join("general.planes_academicos", "general.planes_academicos.codigo = matriculas.cod_plan_academico");
        $conexion->join("general.cursos", "general.cursos.codigo = general.planes_academicos.cod_curso");
        $conexion->join("matriculas_periodos", "matriculas_periodos.cod_matricula = matriculas.codigo");
        $conexion->join("estadoacademico", "estadoacademico.cod_matricula_periodo = matriculas_periodos.codigo");
        $conexion->join("general.materias", "general.materias.codigo = estadoacademico.codmateria");
        $conexion->join("general.clases", "general.clases.id_materia = general.materias.codigo");
        $conexion->where("matriculas.cod_alumno", $this->codigo);
        $conexion->group_by("general.cursos.codigo");
        $query = $conexion->get();
        return $query->result_array();
    }

    public function getMateriasDisponiblesDeCurso($id_curso = null){
        $conexion = $this->oConnection;
        $conexion->select("`general`.`materias`.*");
        $conexion->from("matriculas");
        $conexion->join("general.planes_academicos", "general.planes_academicos.codigo = matriculas.cod_plan_academico");
        $conexion->join("general.cursos", "general.cursos.codigo = general.planes_academicos.cod_curso");
        $conexion->join("matriculas_periodos", "matriculas_periodos.cod_matricula = matriculas.codigo");
        $conexion->join("estadoacademico", "estadoacademico.cod_matricula_periodo = matriculas_periodos.codigo");
        $conexion->join("general.materias", "general.materias.codigo = estadoacademico.codmateria");
        $conexion->join("general.clases", "general.clases.id_materia = general.materias.codigo");
        $conexion->where("matriculas.cod_alumno", $this->codigo);
        if (!is_null($id_curso)) {
            $conexion->where("general.cursos.codigo", $id_curso);
        }
        $conexion->group_by("general.materias.codigo");
        $query = $conexion->get();
        return $query->result_array();
    }

    public function getMateriasDisponibles(){
        return $this->getMateriasDisponiblesDeCurso();
    }

    public function getClasesDisponiblesDeMateria($config_fecha, $id_materia = null, $id_filial = null){
        $conexion = $this->oConnection;
        $conexion->select("general.clases.*", false);
        $conexion->select("(IF((SELECT count(*)
                            FROM ctacte
                            WHERE ctacte.cod_alumno = $this->codigo
                            AND ctacte.importe > ctacte.pagado
                            AND ctacte.cod_concepto IN ('1','5')
                            AND ctacte.fechavenc <  ".date('Y-m-d').") > $config_fecha, 'no','si')) AS alumno_puede_ver", false);

        $conexion->from("matriculas");
        $conexion->join("general.planes_academicos", "general.planes_academicos.codigo = matriculas.cod_plan_academico");
        $conexion->join("general.cursos", "general.cursos.codigo = general.planes_academicos.cod_curso");
        $conexion->join("matriculas_periodos", "matriculas_periodos.cod_matricula = matriculas.codigo");
        $conexion->join("estadoacademico", "estadoacademico.cod_matricula_periodo = matriculas_periodos.codigo");
        $conexion->join("general.materias", "general.materias.codigo = estadoacademico.codmateria");
        $conexion->join("general.clases", "general.clases.id_materia = general.materias.codigo");
        $conexion->where("matriculas.cod_alumno", $this->codigo);
        if (!is_null($id_materia)) {
            $conexion->where("general.materias.codigo", $id_materia);
        }
        if (!is_null($id_filial)) {
            $conexion->where("general.clases.id_filial", $id_filial);
        }
        $conexion->where("general.clases.estado", "habilitada");
        $query = $conexion->get();
        return $query->result_array();
    }

    public function getMaterialesDidacticosDeClaseParaAlumnoDeFilial($clase, $config_fecha){
        $hoy = date('Y-m-d');
        $conexion = $this->oConnection;
        $arrMateriales = Vmateriales_didacticos::listarMateriales_didacticos($conexion, array('id_clase' => $clase));
        $resp = array();
        foreach ($arrMateriales as $material){
            $arrMaterial = array();
            switch ($material['tipo']){
                case 'video':
                    $myMaterial = new Vvideos($conexion, $material['id_material']);
                    $tieneAcceso = $this->tieneAccesoAlmaterial($material['id_material']);
                    $arrMaterial['id'] = $myMaterial->getCodigo();
                    $arrMaterial['tieneAcceso'] = $tieneAcceso;
                    $ahora = date("Y-m-d H:m:s");
                    foreach ($myMaterial as $key => $value){
                        $arrMaterial[$key] = $value;
                    }
                    $propiedades = $myMaterial->getPropiedades();
                    foreach ($propiedades as $propiedad){
                        $arrMaterial['propiedades'][$propiedad['propiedad']] = $propiedad['valor'];
                    }
                    $inicio = strtotime($myMaterial->fecha_publicacion);
                    $duracion = $myMaterial->duracion;
                    $fin = $inicio + $duracion;
                    $arrMaterial['en_vivo'] = false;
                    if(date("Y-m-d H:m:s", $inicio) >= $ahora && $ahora <= $fin){
                        $arrMaterial['en_vivo'] = true;
                        $arrMaterial['propiedades']['video_id'] = '';
                    } else {
                        if(!isset($arrMaterial['propiedades']['video_id']) || $arrMaterial['propiedades']['video_id'] == ''){
                            $account = 7366263;
                            $live = new livestream($account);
                            $event = $live->get_event($arrMaterial['propiedades']['evento_id']);
                            foreach ($event->feed->data as $data){
                                $arrMaterial['propiedades']['video_id'] = $data->data->id . '';
                                $query = "INSERT INTO material_didactico.videos_propiedades (id_video, propiedad, valor) values('{$myMaterial->getCodigo()}', 'video_id', '{$data->data->id}') ON DUPLICATE KEY UPDATE valor = '{$data->data->id}'";
                                $conexion->query($query);
                            }
                        }
                    }
                    break;

                case 'pdf':
                    break;
            }
            $resp['materiales'][$material['tipo']][] = $arrMaterial;
        }
        $conexion->select("(IF((SELECT count(*)
                            FROM ctacte
                            WHERE ctacte.cod_alumno = {$this->codigo}
                            AND ctacte.importe > ctacte.pagado
                            AND ctacte.cod_concepto IN (1,5)
                            AND ctacte.fechavenc <  '{$hoy}') > {$config_fecha}, 0,1)) AS alumno_puede_ver_clase", false);
        $query = $conexion->get();
        $puedeVer = $query->result_array();
        $resp['puede_ver_clase'] = $puedeVer[0]['alumno_puede_ver_clase'];
        return $resp;
    }

    public function getProximosVideos($id_clase, $config_fecha){
        $conexion = $this->oConnection;
        $hoy = date('Y-m-d');
        $conexion->select("(IF((SELECT count(*)
                            FROM ctacte
                            WHERE ctacte.cod_alumno = {$this->codigo}
                            AND ctacte.importe > ctacte.pagado
                            AND ctacte.cod_concepto IN (1,5)
                            AND ctacte.fechavenc <  '{$hoy}') > {$config_fecha}, 0,1)) AS alumno_puede_ver_clase", false);
        $query = $conexion->get();
        $puedeVer = $query->result_array();
        $resp['puede_ver_clase'] = $puedeVer[0]['alumno_puede_ver_clase'];
        $conexion->select('*');
        $conexion->from('material_didactico.videos');
        $conexion->where('material_didactico.videos.fecha_publicacion >', "{$hoy}");
        $conexion->where("material_didactico.videos.id IN (SELECT material_didactico.materiales_didacticos.id_material FROM material_didactico.materiales_didacticos WHERE material_didactico.materiales_didacticos.id_clase = {$id_clase})");
        $query = $conexion->get();
        $videos = $query->result_array();
        foreach ($videos as $keyVideo => $video){
            $resp['videos'][$keyVideo] = $video;
            $myVideo = new Vvideos($conexion, $video['id']);
            $propiedades = $myVideo->getPropiedades();
            foreach ($propiedades as $valor){
                $resp['videos'][$keyVideo]['propiedades'][$valor['propiedad']] = $valor['valor'];
            }
        }
        return $resp;
    }

    public function getProximoVideoEnVivo($config_fecha){
        $conexion = $this->oConnection;
        $hoy = date('Y-m-d');
        $conexion->select("(IF((SELECT count(*)
                            FROM ctacte
                            WHERE ctacte.cod_alumno = {$this->codigo}
                            AND ctacte.importe > ctacte.pagado
                            AND ctacte.cod_concepto IN (1,5)
                            AND ctacte.fechavenc <  '{$hoy}') > {$config_fecha}, 0,1)) AS alumno_puede_ver_clase", false);
        $query = $conexion->get();
        $puedeVer = $query->result_array();
        $resp['puede_ver_clase'] = $puedeVer[0]['alumno_puede_ver_clase'];
        $conexion->select("DISTINCT material_didactico.videos.*", false);
        $conexion->from("matriculas");
        $conexion->join("general.clases", "general.clases.id_plan_academico = matriculas.cod_plan_academico", "left");
        $conexion->join("material_didactico.materiales_didacticos", "material_didactico.materiales_didacticos.id_clase = general.clases.id", "left");
        $conexion->join("material_didactico.videos", "material_didactico.videos.id = material_didactico.materiales_didacticos.id_material AND material_didactico.materiales_didacticos.tipo = 'video' AND material_didactico.videos.fecha_publicacion > curdate() AND material_didactico.videos.fecha_publicacion < date_add(curdate(), interval material_didactico.videos.duracion second)");
        $conexion->where("matriculas.cod_alumno", $this->codigo);
        $query = $conexion->get();
        $videos = $query->result_array();
        foreach ($videos as $keyVideo => $video){
            $resp['videos'][$keyVideo] = $video;
            $myVideo = new Vvideos($conexion, $video['id']);
            $propiedades = $myVideo->getPropiedades();
            foreach ($propiedades as $valor){
                $resp['videos'][$keyVideo]['propiedades'][$valor['propiedad']] = $valor['valor'];
            }
        }
        return $resp;
    }

    public function getVideosDeMateriaParaAlumnoDeFilial($id_materia, $config_fecha){
        $conexion = $this->oConnection;
        $hoy = date('Y-m-d');
        $conexion->select("(IF((SELECT count(*)
                            FROM ctacte
                            WHERE ctacte.cod_alumno = {$this->codigo}
                            AND ctacte.importe > ctacte.pagado
                            AND ctacte.cod_concepto IN (1,5)
                            AND ctacte.fechavenc <  '{$hoy}') > {$config_fecha}, 0,1)) AS alumno_puede_ver_clase", false);
        $query = $conexion->get();
        $puedeVer = $query->result_array();
        $resp['puede_ver_clase'] = $puedeVer[0]['alumno_puede_ver_clase'];
        $conexion->select("material_didactico.videos.*", false);
        $conexion->select("general.clases.id as id_clase", false);
        $conexion->from("material_didactico.videos", false);
        $conexion->join("material_didactico.materiales_didacticos", "material_didactico.materiales_didacticos.id_material = material_didactico.videos.id");
        $conexion->join("general.clases", "general.clases.id = material_didactico.materiales_didacticos.id_clase");
        $conexion->join("general.materias", "general.materias.codigo = general.clases.id_materia");
        $conexion->where("general.materias.codigo", $id_materia);
        $conexion->group_by("material_didactico.videos.id");
        $conexion->order_by("general.clases.nro_clase", "DESC");
        $conexion->order_by("material_didactico.videos.fecha_publicacion", "DESC");
	$query = $conexion->get();
        $videos = $query->result_array();
        foreach ($videos as $keyVideo => $video){
            $resp['videos'][$keyVideo] = $video;
            $myVideo = new Vvideos($conexion, $video['id']);
            $propiedades = $myVideo->getPropiedades();
            foreach ($propiedades as $valor){
                $resp['videos'][$keyVideo]['propiedades'][$valor['propiedad']] = $valor['valor'];
            }
        }
        return $resp;
    }

    public function getVideosAnteriores($id_clase, $config_fecha){
        $conexion = $this->oConnection;
        $hoy = date('Y-m-d');
        $conexion->select("(IF((SELECT count(*)
                            FROM ctacte
                            WHERE ctacte.cod_alumno = {$this->codigo}
                            AND ctacte.importe > ctacte.pagado
                            AND ctacte.cod_concepto IN (1,5)
                            AND ctacte.fechavenc <  '{$hoy}') > {$config_fecha}, 0,1)) AS alumno_puede_ver_clase", false);
        $query = $conexion->get();
        $puedeVer = $query->result_array();
        $resp['puede_ver_clase'] = $puedeVer[0]['alumno_puede_ver_clase'];
        $conexion->select('*');
        $conexion->from('material_didactico.videos');
        $conexion->where('material_didactico.videos.fecha_publicacion <=', "{$hoy}");
        $conexion->where("material_didactico.videos.id IN (SELECT material_didactico.materiales_didacticos.id_material FROM material_didactico.materiales_didacticos WHERE material_didactico.materiales_didacticos.id_clase = {$id_clase})");
        $query = $conexion->get();
        $videos = $query->result_array();
        foreach ($videos as $keyVideo => $video){
            $resp['videos'][$keyVideo] = $video;
            $myVideo = new Vvideos($conexion, $video['id']);
            $propiedades = $myVideo->getPropiedades();
            foreach ($propiedades as $valor){
                $resp['videos'][$keyVideo]['propiedades'][$valor['propiedad']] = $valor['valor'];
            }
        }
        return $resp;
    }

    public function tieneAccesoPlataformaElearning(){
        $conexion = $this->oConnection;
        $conexion->select("`general`.`cursos`.`codigo`");
        $conexion->from("matriculas");
        $conexion->join("general.planes_academicos", "general.planes_academicos.codigo = matriculas.cod_plan_academico");
        $conexion->join("general.cursos", "general.cursos.codigo = general.planes_academicos.cod_curso");
        $conexion->join("matriculas_periodos", "matriculas_periodos.cod_matricula = matriculas.codigo");
        $conexion->join("estadoacademico", "estadoacademico.cod_matricula_periodo = matriculas_periodos.codigo");
        $conexion->join("general.materias", "general.materias.codigo = estadoacademico.codmateria");
        $conexion->join("general.clases", "general.clases.id_materia = general.materias.codigo");
        $conexion->where("matriculas.cod_alumno", $this->codigo);
        $conexion->limit(1);
        $resultado = $conexion->get()->result_array();
        // si esta en al menos un curso con clases, tiene acceso a E-Learning
        if (is_array($resultado) && count($resultado) > 0) {
            return true;
        }
        return false;
    }

    private function tieneAccesoAlmaterial($cod_concepto){
        //esta funcion se fija si el alumno tiene una linea en cuenta corriente
        //asociada al material didactico
        //aun no implementado - para implementar hace falta asociar un cod_concepto al tipo de material
        //realizar la logica para incluirlo en la cuenta corriente
        //y cambiar el ultimo true por un false
        $condiciones = array("ctacte.cod_concepto" => $cod_concepto, "ctacte.importe" => "ctacte.pagado");
        $resp = $this->getCtaCte($condiciones);
        if($resp > 0){
            return true;
        }
        return true;
    }
    
    public function get_materias($estado = null){
        $this->oConnection->select("estadoacademico.codmateria");
        $this->oConnection->from("estadoacademico");
        $this->oConnection->join("matriculas_periodos", "matriculas_periodos.codigo = estadoacademico.cod_matricula_periodo");
        $this->oConnection->join("matriculas", "matriculas.codigo = matriculas_periodos.cod_matricula");
        $this->oConnection->where("matriculas.cod_alumno", $this->codigo);
        if ($estado != null){
            $tipo = is_array($estado) ? "where_in" : "where";
            $this->oConnection->$tipo("estadoacademico.estado", $estado);
        }
        $query = $this->oConnection->get();
        return $query->result_array();
    }
    
    public function get_materias_plan_academico($idioma = "es", $estado = null){
        if ($idioma == "en"){
            $idioma = "in";
        }
        $this->oConnection->select("matriculas.codigo");
        $this->oConnection->select("matriculas.cod_plan_academico");
        $this->oConnection->select("general.cursos.nombre_$idioma AS nombre_curso");
        $this->oConnection->select("general.materias.nombre_$idioma AS nombre_materia");
        $this->oConnection->select("estadoacademico.codmateria");
        $this->oConnection->from("general.materias");
        $this->oConnection->join("estadoacademico", "estadoacademico.codmateria = general.materias.codigo");
        $this->oConnection->join("matriculas_periodos", "matriculas_periodos.codigo = estadoacademico.cod_matricula_periodo");
        $this->oConnection->join("matriculas", "matriculas.codigo = matriculas_periodos.cod_matricula");
        $this->oConnection->join("general.planes_academicos", "general.planes_academicos.codigo = matriculas.cod_plan_academico");
        $this->oConnection->join("general.cursos", "general.cursos.codigo = general.planes_academicos.cod_curso");
        $this->oConnection->where("matriculas.cod_alumno", $this->codigo);
        if ($estado != null){
            $tipo = is_array($estado) ? "where_in" : "where";
            $this->oConnection->$tipo("estadoacademico.estado", $estado);
        }
        $query = $this->oConnection->get();
        return $query->result_array();
    }
    
    public function get_medios_pago_cuotas(){
        $this->oConnection->select("matriculas_mediospago.cod_medio");
        $this->oConnection->from("matriculas_mediospago");
        $this->oConnection->join("matriculas", "matriculas.codigo = matriculas_mediospago.cod_matricula");
        $this->oConnection->where("matriculas.cod_alumno", $this->codigo);
        $this->oConnection->order_by("matriculas_mediospago.cod_matricula", "DESC");
        $query = $this->oConnection->get();
        return $query->result_array();
    }

    
    public static function getDatosAcademicosAlumno($conexion, $alumno){
        $conexion->select("matriculas.cod_plan_academico");
        $conexion->select("estadoacademico.codmateria");
        $conexion->from("alumnos");
        $conexion->join("matriculas", "matriculas.cod_alumno = alumnos.codigo");
        $conexion->join("matriculas_periodos", "matriculas_periodos.cod_matricula = matriculas.codigo");
        $conexion->join("estadoacademico", "estadoacademico.cod_matricula_periodo = matriculas_periodos.codigo");
        $conexion->where("alumnos.codigo", $alumno);
        $conexion->where("matriculas.estado", 'habilitada');
        $conexion->where("matriculas_periodos.estado", 'habilitada');
        $conexion->where("estadoacademico.estado in ('cursando', 'regular')");
        $query = $conexion->get();
        return $query->result_array();
    }

    public function getPeriodosMatricular($cod_plan, $cod_filial, $orden = null) {
        $conexion = $this->oConnection;
        $myPlanAcademico = new Vplanes_academicos($conexion, $cod_plan);
        $cod_curso = $myPlanAcademico->cod_curso;
        $year = date("Y");
        $conexion->select('matriculas_periodos.cod_tipo_periodo');
        $conexion->from('matriculas_periodos');
        $conexion->join('matriculas', 'matriculas.codigo = matriculas_periodos.cod_matricula');
        $conexion->join('general.planes_academicos', 'general.planes_academicos.codigo = matriculas.cod_plan_academico');
        $conexion->where('matriculas.cod_alumno', $this->codigo);
        $conexion->where('general.planes_academicos.cod_curso', $cod_curso);
        $conexion->where('YEAR(matriculas_periodos.fecha_emision)', $year);

        $subquery = $conexion->return_query();

        $conexion->resetear();
        $conexion->select('*');
        $conexion->from('general.tipos_periodos');
        $conexion->join('general.planes_academicos_periodos', 'general.planes_academicos_periodos.cod_tipo_periodo = general.tipos_periodos.codigo');
        $conexion->join('general.planes_academicos_filiales', 'general.planes_academicos_filiales.cod_tipo_periodo = general.tipos_periodos.codigo AND general.planes_academicos_filiales.cod_plan_academico = ' . $cod_plan . ' AND general.planes_academicos_filiales.cod_filial = ' . $cod_filial . '');
        $conexion->where('general.planes_academicos_periodos.cod_plan_academico', $cod_plan);
        $conexion->where('general.tipos_periodos.codigo NOT IN (' . $subquery . ')');
        $conexion->where('general.planes_academicos_filiales.estado', 'habilitado');
        $conexion->group_by('general.tipos_periodos.codigo');
        if ($orden != null) {
            $conexion->order_by($orden['campo'], $orden['orden']);
        }
        $query = $conexion->get();

        return $query->result_array();
    }

    public static function getAlumnosInhabilitados(CI_DB_mysqli_driver $conexion) {
        $conexion->select('*');
        $conexion->from('alumnos');
        $conexion->where('baja', 'inhabilitada');
        $query = $conexion->get();
        return $query->result_array();
    }

    public static function getAlumnosComision(CI_DB_mysqli_driver $conexion, $cod_comision){
        $conexion->select("alu.codigo 'codAlumno'");
        $conexion->from("alumnos alu");
        $conexion->join("matriculas mat", "mat.cod_alumno = alu.codigo");
        $conexion->join("matriculas_periodos mp", "mp.cod_matricula = mat.codigo");
        $conexion->join("estadoacademico ea", "ea.cod_matricula_periodo = mp.codigo");
        $conexion->join("matriculas_inscripciones mi", "mi.cod_estado_academico = ea.codigo");
        $conexion->where("mi.cod_comision", $cod_comision);
        $conexion->group_by("alu.codigo");

        $query = $conexion->get();
        return $query->result_array();
    }

    //busca alumnos que por lo menos 1 materia no esta aprovada o homologada en primero periodo y las materias de segundo periodo no tienen comision
    public static function buscaAlumnosCursandoDosPeriodoSinComision(CI_DB_mysqli_driver $conexion, $ano) {
        $conexion->select('sum(if(matriculas_periodos.cod_tipo_periodo = 1 and matriculas_inscripciones.baja = 0, 1, null)) as primeroQtd', false);
        $conexion->select('sum(if((estadoacademico.estado = \'homologado\' or estadoacademico.estado = \'aprobado\') and matriculas_periodos.cod_tipo_periodo = 1 and matriculas_inscripciones.baja = 0, 1, null)) as primeroAprHom', false);
        $conexion->select('sum(if(estadoacademico.estado = \'cursando\' and matriculas_periodos.cod_tipo_periodo = 2 and matriculas_inscripciones.codigo is null, 1, null)) as segundoCursando', false);
        $conexion->select('SUM(IF(estadoacademico.estado = \'cursando\' AND matriculas_periodos.cod_tipo_periodo = 2 AND matriculas_inscripciones.baja = 0 AND matriculas_inscripciones.codigo IS NOT NULL, 1, NULL)) AS segundoCursandoConComision', false);
        $conexion->select('alumnos.*');
        $conexion->select('matriculas.codigo as mat_codigo');
        $conexion->join('matriculas_periodos', 'matriculas_periodos.codigo = estadoacademico.cod_matricula_periodo');
        $conexion->join('matriculas', 'matriculas.codigo = matriculas_periodos.cod_matricula');
        $conexion->join('alumnos', 'alumnos.codigo = matriculas.cod_alumno');
        $conexion->join('matriculas_inscripciones', 'matriculas_inscripciones.cod_estado_academico = estadoacademico.codigo', 'left');
        $conexion->where('year(matriculas.fecha_emision)', $ano);
        $conexion->where('matriculas_periodos.estado != ', 'inhabilitada');
        $conexion->group_by('matriculas.codigo');
        $conexion->having('segundoCursando is not null', null, null);
        $conexion->having('segundoCursandoConComision is null', null, null);
        $conexion->having('(primeroQtd != primeroAprHom or primeroAprHom is null)', null, null);
        return Testadoacademico::listarEstadoacademico($conexion);
    }

    //busca alumnos que tengan materias en primero y segundo periodo y que tenga comisiones en los dos
    public static function buscaAlumnosCursandoDosPeriodoConComision(CI_DB_mysqli_driver $conexion, $ano) {
        $conexion->select('sum(if(estadoacademico.estado = \'cursando\' and matriculas_periodos.cod_tipo_periodo = 2,1,null)) as periodo2', false);
        $conexion->select('sum(if(estadoacademico.estado != \'homologado\' and estadoacademico.estado != \'aprobado\' and matriculas_periodos.cod_tipo_periodo = 1,1,null)) as periodo1', false);
        $conexion->select('alumnos.*');
        $conexion->select('matriculas.codigo as mat_codigo');
        $conexion->join('matriculas_periodos', 'matriculas_periodos.codigo = estadoacademico.cod_matricula_periodo');
        $conexion->join('matriculas', 'matriculas.codigo = matriculas_periodos.cod_matricula');
        $conexion->join('alumnos', 'alumnos.codigo = matriculas.cod_alumno');
        $conexion->join('matriculas_inscripciones', 'matriculas_inscripciones.cod_estado_academico = estadoacademico.codigo');
        $conexion->where('year(matriculas.fecha_emision)', $ano);
        $conexion->where('matriculas_periodos.estado != ', 'inhabilitada');
        $conexion->where('matriculas_inscripciones.baja', '0');
        $conexion->group_by('matriculas.codigo');
        $conexion->having('periodo2 is not null', null, null);
        $conexion->having('periodo1 is not null', null, null);
        return Testadoacademico::listarEstadoacademico($conexion);
    }
}

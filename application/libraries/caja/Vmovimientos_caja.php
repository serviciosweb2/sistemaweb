            <?php

/**
 * Class Vmovimientos_caja
 *
 * Class  Vmovimientos_caja maneja todos los aspectos de alumnos
 *
 * @package  SistemaIGA
 * @subpackage Alumnos
 * @author   Ivan berthillod <ivan.sys@gmail.com>
 * @author   Aquiles Gonzalez <sistemas1@iga-la.net>
 * @version  $Revision: 1.1 $
 * @access   private
 */
class Vmovimientos_caja extends Tmovimientos_caja {

    static private $tipopagos = "PAGOS";
    static private $tipocobros = "COBROS";
    static private $tipoparticulares = "PARTICULARES";
    static private $tipoapertura = "APERTURA";
    static private $tipocierre = "CIERRE";
    static private $tipotransferencia = "TRANSFERENCIA";

    static private $arrayConceptos = array("PAGOS", "COBROS", "PARTICULARES", "APERTURA", "CIERRE", "TRANSFERENCIA");


    function __construct(CI_DB_mysqli_driver $conexion, $codigo = null) {
        parent::__construct($conexion, $codigo);
    }

    /* STATIC FUNCTIONS */

    static function getConceptoPagos(){
        return self::$tipopagos;
    }

    static function getConceptoCobros(){
        return self::$tipocobros;
    }

    static function getConceptoParticulares(){
        return self::$tipoparticulares;
    }

    static function getConceptoApertura(){
        return self::$tipoapertura;
    }

    static function getConceptoCierre(){
        return self::$tipocierre;
    }

    static function getConceptoTransferencia(){
        return self::$tipotransferencia;
    }

    static function getMovimientosCaja(CI_DB_mysqli_driver $conexion, $arrLimit = null, $arrSort = null, $contar = false, $search = null, array $searchFields = null, $fechaDesde = null, $fechaHasta = null, $codCaja = null, $codUser = null, $medioPago = null) {
        $aColumns = array();
        $aColumns['codigo']['order'] = "movimientos_caja.codigo";
        $aColumns['fecha']['order'] = "movimientos_caja.fecha_hora";
        $aColumns['hora']['order'] = "TIME(movimientos_caja.fecha_hora)";
        $aColumns['debe']['order'] = "movimientos_caja.debe";
        $aColumns['haber']['order'] = "movimientos_caja.haber";
        $aColumns['saldo']['order'] = 'movimientos_caja.saldo';
        $aColumns['usuario_nombre']['order'] = 'usuario_nombre';
        $aColumns['medio']['order'] = 'general.medios_pago.medio';
        $aColumns['caja_nombre']['order'] = "caja.nombre";
        $aColumns['observacion']['order'] = "movimientos_caja.observacion";
        $aColumns['descripcion']['order'] = "descripcion";

        $aColumns['codigo']['having'] = "movimientos_caja.codigo";
        $aColumns['fecha']['having'] = "fecha";
        $aColumns['hora']['having'] = "hora";
        $aColumns['debe']['having'] = "movimientos_caja.debe";
        $aColumns['haber']['having'] = "movimientos_caja.haber";
        $aColumns['saldo']['having'] = 'movimientos_caja.saldo';
        $aColumns['usuario_nombre']['having'] = 'usuario_nombre';
        $aColumns['medio']['having'] = 'general.medios_pago.medio';
        $aColumns['caja_nombre']['having'] = "caja.nombre";
        $aColumns['observacion']['having'] = "movimientos_caja.observacion";
        $aColumns['descripcion']['having'] = "descripcion";


        $conexion->select("movimientos_caja.codigo");
        $conexion->select("CONCAT(LPAD(DAY(movimientos_caja.fecha_hora), 2, 0), '/', LPAD(MONTH(movimientos_caja.fecha_hora), 2, 0), '/', YEAR(movimientos_caja.fecha_hora)) AS fecha", false);
        $conexion->select("TIME(movimientos_caja.fecha_hora) AS hora");
        $conexion->select("movimientos_caja.debe");
        $conexion->select("movimientos_caja.haber");
        $conexion->select("movimientos_caja.saldo");
        $conexion->select("CONCAT(general.usuarios_sistema.apellido, ' ', general.usuarios_sistema.nombre) AS usuario_nombre", false);
        $conexion->select("general.medios_pago.medio");
        $conexion->select("caja.nombre AS caja_nombre");
        $conexion->select("movimientos_caja.observacion");
        $conexion->select("IF (movimientos_caja.cod_concepto = 'PAGOS', CONCAT('" . lang("compra_a_proveedor") . ' ' . "',(SELECT razones_sociales.razon_social FROM razones_sociales JOIN proveedores ON proveedores.cod_razon_social = razones_sociales.codigo JOIN pagos ON pagos.concepto = 'PROVEEDOR' AND pagos.cod_concepto = proveedores.codigo WHERE movimientos_caja.concepto = pagos.codigo)),
                IF(movimientos_caja.cod_concepto = 'COBROS',CONCAT('" . lang("cobro_alumno") . ' ' . "',(SELECT CONCAT(alumnos.nombre, ' ' , alumnos.apellido) FROM alumnos JOIN cobros ON cobros.cod_alumno = alumnos.codigo  WHERE movimientos_caja.concepto = cobros.codigo)),
                IF (movimientos_caja.cod_concepto = 'APERTURA','" . lang("apertura_de_caja") . "',
                IF (movimientos_caja.cod_concepto = 'PARTICULARES','" . lang("movimiento_particular") . "',
                IF (movimientos_caja.cod_concepto = 'CIERRE','" . lang("cierre_de_caja") . "',
                IF (movimientos_caja.cod_concepto = 'TRANFERENCIA','" . lang("transferencia_entre_cajas") . "','-')))))) AS descripcion", false);
        $conexion->from("movimientos_caja");
        $conexion->join("general.usuarios_sistema", "general.usuarios_sistema.codigo = movimientos_caja.cod_user");
        $conexion->join("general.medios_pago", "general.medios_pago.codigo = movimientos_caja.cod_medio");
        $conexion->join("caja", "caja.codigo = movimientos_caja.cod_caja");
        if ($fechaDesde != null)
            $conexion->where("DATE(movimientos_caja.fecha_hora) >=", $fechaDesde);
        if ($fechaHasta != null)
            $conexion->where("DATE(movimientos_caja.fecha_hora) <=", $fechaHasta);
        if ($codCaja != null)
            $conexion->where("movimientos_caja.cod_caja =", $codCaja);
        if ($codUser != null)
            $conexion->where("movimientos_caja.cod_user =", $codUser);
        if ($medioPago != null)
            $conexion->where("movimientos_caja.cod_medio =", $medioPago);
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
        if ($contar){
            return $query->num_rows();
        } else {
//            echo $conexion->last_query();die();
            return $query->result_array();
        }
    }

    /**
     * lista todos los movientos de caja con distintos filtros para datatable plugin
     * @access public
     * @param CI_DB_mysqli_driver $conexion conexion que viene del Modelo
     * @param  Array $arrCondindicioneslike condiciones del buscar de datatable
     * @param  Array $arrLimit limite del paginado.
     * @param Boolean $contar devuelve un contar de tabla o un array-
     * @return Array de movimientos de caja
     */
    static function listarMovimientosDataTable(CI_DB_mysqli_driver $conexion, $arrCondindicioneslike, $arrLimit = null, $arrSort = null,
            $contar = false, $arrCondiciones = null, $limitApertura = false, $estadoCaja = null) {

        $formatoFecha = getFechaFormat();
        $codCaja = null;
        foreach ($arrCondiciones as $condicion){    // los filtros deberian aplicarse por separado en lugar de enviar un unico array;
            if (isset($condicion['cod_caja'])){
                $codCaja = $condicion['cod_caja'];
            }
        }
        $conexion->select("value");
        $conexion->from("configuracion");
        $conexion->where("key = 'codigoFilial'");
        $subqueryCodigoFilial = $conexion->return_query();
        $conexion->resetear();

        $conexion->select("id_moneda");
        $conexion->from("general.filiales");
        $conexion->where("codigo = ($subqueryCodigoFilial)");
        $subqueryMoneda = $conexion->return_query();
        $conexion->resetear();

        $conexion->select("simbolo");
        $conexion->from("general.cotizaciones");
        $conexion->where("id = ($subqueryMoneda)");
        $subquerySimbolo = $conexion->return_query();
        $conexion->resetear();

        $conexion->select("value");
        $conexion->from("configuracion");
        $conexion->where("key = 'SeparadorDecimal'");
        $subquerySeparadorDecimal = $conexion->return_query();
        $conexion->resetear();

        $conexion->select("value");
        $conexion->from("configuracion");
        $conexion->where("key = 'NombreSeparador'");
        $subquerySeparador = $conexion->return_query();
        $conexion->resetear();

        $conexion->select("value");
        $conexion->from("configuracion");
        $conexion->where("key = 'NombreFormato'");
        $subqueryNombreFormato = $conexion->return_query();
        $conexion->resetear();

        $conexion->select("CONCAT(nombre, @separador, ' ', apellido)", false);
        $conexion->from("general.usuarios_sistema");
        $conexion->where("codigo = movimientos_caja.cod_user");
        $subqueryFormato1 = $conexion->return_query();
        $conexion->resetear();

        $conexion->select("CONCAT(apellido, @separador, ' ', nombre)", false);
        $conexion->from("general.usuarios_sistema");
        $conexion->where("codigo = movimientos_caja.cod_user");
        $subqueryFormato0 = $conexion->return_query();
        $conexion->resetear();

        if ($codCaja != null && $limitApertura){
            $conexion->query("SET @codigoApertura = (SELECT MAX(codigo_apertura) FROM movimientos_caja WHERE cod_caja = $codCaja and cod_medio = 1)");
        }
        $conexion->query("SET @separador = ($subquerySeparador)");
        $conexion->query("SET @nombreFormato = ($subqueryNombreFormato)");
        $conexion->query("SET @separadorDecimal = ($subquerySeparadorDecimal)");
        $conexion->query("SET @simboloMoneda = ($subquerySimbolo)");
        $conexion->select('movimientos_caja.*');
        $conexion->select("CONCAT(@simboloMoneda, ' ', REPLACE(CAST(movimientos_caja.debe AS BINARY), '.', @separadorDecimal)) AS debe_format", false);
        $conexion->select("CONCAT(@simboloMoneda, ' ', REPLACE(CAST(movimientos_caja.haber AS BINARY), '.', @separadorDecimal)) AS haber_format", false);
        $conexion->select("DATE_FORMAT(movimientos_caja.fecha_hora, '$formatoFecha') AS fecha_hora_movimiento", false);
        $conexion->select('general.medios_pago.medio');
        $conexion->select("IF(@nombreFormato = 1, ($subqueryFormato1), ($subqueryFormato0)) AS user_name", false);
        $conexion->from('movimientos_caja');
        $conexion->join('general.medios_pago', "general.medios_pago.codigo = movimientos_caja.cod_medio");
        if ($estadoCaja != null)
            $conexion->join("caja", "caja.codigo = movimientos_caja.cod_caja AND caja.estado = '$estadoCaja'");

        if (count($arrCondindicioneslike) > 0) {
            $having = array();
            foreach ($arrCondindicioneslike as $key => $value) {
                $having[] = "$key LIKE '%$value%'";
            }
            $conexion->having("(".implode(" OR ", $having) .")");
        }
        if ($arrCondiciones != null) {
            foreach ($arrCondiciones as $condicion){
                $conexion->where($condicion);
            }
        }

        if ($limitApertura && $codCaja !== null) {
            $conexion->where('movimientos_caja.codigo >= @codigoApertura');
        }

        if ($arrLimit != null) {
            $conexion->limit($arrLimit[1], $arrLimit[0]);
        }

        if ($arrSort != null) {
            $conexion->order_by($arrSort["0"], $arrSort["1"]);
        }

        $query = $conexion->get();
        if ($contar) {
            return $query->num_rows();
        } else {
            return $query->result_array();
        }
    }

    public function guardar($fechahora = null, $codmedio = null, $debe = null, $haber = null, $observacion = null, $coduser = null, $codcaja = null, $codconcepto = null, $concepto = null, $fechahorareal = null, $saldo = null) {
        $this->cod_caja = $codcaja != null ? $codcaja : $this->cod_caja;
        $this->fecha_hora = $fechahora != null ? $fechahora : $this->fecha_hora;
        $this->fecha_hora_real = $fechahorareal != null ? $fechahorareal : date("Y-m-d H:i:s");
        $this->cod_medio = $codmedio != null ? $codmedio : $this->cod_medio;
        $this->cod_user = $coduser != null ? $coduser : $this->cod_user;
        $this->cod_concepto = $codconcepto != null ? $codconcepto : $this->cod_concepto;
        $this->haber = $haber != null ? $haber : $this->haber;
        $this->debe = $debe != null ? $debe : $this->debe;
        $this->observacion = $observacion != null ? $observacion : $this->observacion;
        $this->concepto = $concepto != null ? $concepto : $this->concepto;
        $myCaja = new Vcaja($this->oConnection, $codcaja);
        $this->codigo_apertura = $myCaja->getCodigoApertura($codmedio);

        $caja = new Vcaja($this->oConnection, $this->cod_caja);
        if ($saldo !== null){
            $this->saldo = $saldo;
        } else {
            $saldo = $caja->getUltimoSaldo($this->cod_medio);
            $this->saldo = $saldo + $this->haber - $this->debe; //EL SALDO NO PUEDE SER NEGATIVO
        }
        $this->guardarMovimientos_caja();
    }

    static function getDescripcion(CI_DB_mysqli_driver $conexion, $codConcepto, $concepto){

        $descripcion = '';
        switch ($codConcepto){
            case "PAGOS": // compras
                $myPagos = new Vpagos($conexion, $concepto);
                $myProveedor = new Vproveedores($conexion, $myPagos->cod_concepto);
                $myRazonSocial = new Vrazones_sociales($conexion,$myProveedor->cod_razon_social);
                $descripcion = lang("compra_a_proveedor")." ".inicialesMayusculas($myRazonSocial->razon_social);
                break;

            case "COBROS": // cobros
                $myCobro = new Vcobros($conexion, $concepto);
                $myAlumno = new Valumnos($conexion, $myCobro->cod_alumno);
                $descripcion = lang("cobro_alumno")." ".inicialesMayusculas($myAlumno->nombre)." ".inicialesMayusculas($myAlumno->apellido);
                break;

            case "PARTICULARES": //particulares
                $descripcion = lang("movimiento_particular");
                break;

            case "APERTURA": // apertura
                $descripcion = lang("apertura_de_caja");
                break;

            case "CIERRE": // cierre
                $descripcion = lang("cierre_de_caja");
                break;

            case "TRANSFERENCIA":
                $descripcion = lang("transferencia_entre_cajas");
                break;

            default:
                break;
        }
        return $descripcion;
    }

    static function getConceptos(){
        return self::$arrayConceptos;
    }

    //mmori - nuevas funciones para reportes de rentabilidad
    static public function getReporteRentabiliadGastosEingresos(CI_DB_mysqli_driver $conexion, $cod_concepto, $fecha_desde = false, $fecha_hasta = false)
    {
        switch($cod_concepto)
        {
            case 'INGRESOS':
                $conexion->select("IFNULL(SUM(movimientos_caja.haber), 0) AS total", false);
                $conexion->where_not_in("cod_concepto", array('CIERRE', 'APERTURA'));
                break;
            case 'GASTOS':
                $conexion->select("IFNULL(sum(debe), 0) AS total", false);
                $conexion->where("(cod_concepto = 'PAGOS' OR (cod_concepto = 'PARTICULARES' AND concepto <> 19))");
                break;
        }

        $conexion->from("movimientos_caja");


        if($fecha_desde != null){
            $conexion->where("DATE(movimientos_caja.fecha_hora) >=", $fecha_desde);
        }
        if ($fecha_hasta != null){
            $conexion->where("DATE(movimientos_caja.fecha_hora) <=", $fecha_hasta);
        }

        $query = $conexion->get();
//        die(" ".$conexion->last_query());
        return $query->result_array();
    }

    static public function getReporteGastos(CI_DB_mysqli_driver $conexion, $gasto, $fecha_desde, $fecha_hasta, $cod_sub){
        $conexion->select("movimientos_caja.codigo as 'codigo mov caja'");
        $conexion->select("movimientos_caja.fecha_hora as 'fecha'");
        if((string)$gasto == 'REDEFINIR') {

            $conexion->select("IF(movimientos_caja.concepto > 28,'Prov: ',(SELECT razones_sociales.razon_social FROM razones_sociales JOIN proveedores ON proveedores.cod_razon_social = razones_sociales.codigo JOIN pagos ON pagos.concepto = 'PROVEEDOR' AND pagos.cod_concepto = proveedores.codigo WHERE movimientos_caja.concepto = pagos.codigo),' Art.: ',(SELECT articulos.nombre FROM articulos JOIN compras_renglones ON compras_renglones.cod_articulo = articulos.codigo JOIN compras ON compras.codigo = compras_renglones.cod_compra JOIN compras_imputaciones ON compras_imputaciones.cod_compra = compras.codigo JOIN pagos ON pagos.codigo = compras_imputaciones.cod_pago where movimientos_caja.concepto = pagos.codigo group by articulos.codigo)))    , movimientos_caja.observacion) as 'observacion'", false);
            $conexion->select("IF(movimientos_caja.concepto > 28, ('REDEFINIR'), rubros_caja.subrubro) as tipo ", false);
        }else{
            $conexion->select("IF(movimientos_caja.concepto > 28, CONCAT('Prov: ', (CONCAT( (SELECT razones_sociales.razon_social FROM razones_sociales JOIN proveedores ON proveedores.cod_razon_social = razones_sociales.codigo JOIN pagos ON pagos.concepto = 'PROVEEDOR' AND pagos.cod_concepto = proveedores.codigo WHERE movimientos_caja.concepto = pagos.codigo), (CONCAT(' Art.: ',(SELECT articulos.nombre FROM articulos JOIN compras_renglones ON compras_renglones.cod_articulo = articulos.codigo JOIN compras ON compras.codigo = compras_renglones.cod_compra JOIN compras_imputaciones ON compras_imputaciones.cod_compra = compras.codigo JOIN pagos ON pagos.codigo = compras_imputaciones.cod_pago where movimientos_caja.concepto = pagos.codigo group by articulos.codigo)))))), movimientos_caja.observacion) as 'observacion'", false);

            //      $conexion->select("movimientos_caja.observacion as 'observacion'");
            $conexion->select("rubros_caja.subrubro as tipo");

        }
        $conexion->select("movimientos_caja.debe as debe");
        $conexion->from("movimientos_caja");
        $conexion->join("rubros_caja", "rubros_caja.codigo = movimientos_caja.concepto", "LEFT");
        $conexion->join("compras_imputaciones", "compras_imputaciones.cod_pago = movimientos_caja.concepto", "LEFT");
        $conexion->join("compras_renglones","compras_renglones.cod_compra = compras_imputaciones.cod_compra","LEFT");
        $conexion->join("articulos","articulos.codigo = compras_renglones.cod_articulo","LEFT");
        $conexion->join("articulos_categorias","articulos_categorias.codigo = articulos.cod_categoria","LEFT");
            if((string)$gasto == 'REDEFINIR'){
                

                $conexion->where("DATE(movimientos_caja.fecha_hora) >= "."'" . $fecha_desde."'"."
			AND DATE(movimientos_caja.fecha_hora) <= "."'" . $fecha_hasta."'"."
			AND movimientos_caja.concepto <> 19
			AND movimientos_caja.debe != 0
			AND rubros_caja.subrubro is null
			AND `movimientos_caja` . `cod_concepto` = 'PARTICULARES'
			OR(
				DATE(movimientos_caja.fecha_hora) >= "."'" . $fecha_desde."'"."
				AND DATE(movimientos_caja.fecha_hora) <= "."'" . $fecha_hasta."'"."	
				AND	rubros_caja.subrubro = 'REDEFINIR'
			)
			OR(DATE(movimientos_caja.fecha_hora) >= "."'" . $fecha_desde."'"."
				AND DATE(movimientos_caja.fecha_hora) <= "."'" . $fecha_hasta."'"."	
				and	movimientos_caja.cod_concepto = 'PAGOS' and movimientos_caja.concepto > 28 AND articulos_categorias.cod_rubros_caja = 17)");

            }
            else{
                $conexion->where("DATE(movimientos_caja.fecha_hora) >=", $fecha_desde);
                $conexion->where("DATE(movimientos_caja.fecha_hora) <=", $fecha_hasta);
                $conexion->where("rubros_caja.subrubro = " ."'" . $gasto."'
                OR(DATE(movimientos_caja.fecha_hora) >= "."'" . $fecha_desde."'"."
				AND DATE(movimientos_caja.fecha_hora) <= "."'" . $fecha_hasta."'"."	
				and	movimientos_caja.cod_concepto = 'PAGOS' and movimientos_caja.concepto > 28 and articulos_categorias.cod_rubros_caja = "."'" . $cod_sub."'".")");


            }




        $query = $conexion->get();

        $respuesta['data']=array();
        foreach ($query->result_array() as $row)
        {
            $unarow = array($row['codigo mov caja'],$row['fecha'],$row['observacion'],lang($row['tipo']),"$".number_format((float)$row['debe'],2,',','.'));
            array_push($respuesta['data'],$unarow);
        }

        return $respuesta;

    }


    static public function getReporteGastos2(CI_DB_mysqli_driver $conexion, $gasto, $fecha_desde, $fecha_hasta, $cod_sub, $filial){
        $conexion->select("movimientos_caja.codigo as 'cod_mov_caja'");
        $conexion->select("movimientos_caja.fecha_hora as 'fecha'");
        if((string)$gasto == 'REDEFINIR') {

            $conexion->select("IF(movimientos_caja.concepto > 28, CONCAT('Prov: ', (CONCAT( (SELECT razones_sociales.razon_social FROM razones_sociales JOIN proveedores ON proveedores.cod_razon_social = razones_sociales.codigo JOIN pagos ON pagos.concepto = 'PROVEEDOR' AND pagos.cod_concepto = proveedores.codigo WHERE movimientos_caja.concepto = pagos.codigo), (CONCAT(' Art.: ',(SELECT articulos.nombre FROM articulos JOIN compras_renglones ON compras_renglones.cod_articulo = articulos.codigo JOIN compras ON compras.codigo = compras_renglones.cod_compra JOIN compras_imputaciones ON compras_imputaciones.cod_compra = compras.codigo JOIN pagos ON pagos.codigo = compras_imputaciones.cod_pago where movimientos_caja.concepto = pagos.codigo group by articulos.codigo)))))), movimientos_caja.observacion) as 'observacion'", false);
            $conexion->select("IF(movimientos_caja.concepto > 28, ('REDEFINIR'), rubros_caja.subrubro) as tipo ", false);
        }else{
            $conexion->select("IF(movimientos_caja.concepto > 28, CONCAT('Prov: ', (CONCAT( (SELECT razones_sociales.razon_social FROM razones_sociales JOIN proveedores ON proveedores.cod_razon_social = razones_sociales.codigo JOIN pagos ON pagos.concepto = 'PROVEEDOR' AND pagos.cod_concepto = proveedores.codigo WHERE movimientos_caja.concepto = pagos.codigo), (CONCAT(' Art.: ',(SELECT articulos.nombre FROM articulos JOIN compras_renglones ON compras_renglones.cod_articulo = articulos.codigo JOIN compras ON compras.codigo = compras_renglones.cod_compra JOIN compras_imputaciones ON compras_imputaciones.cod_compra = compras.codigo JOIN pagos ON pagos.codigo = compras_imputaciones.cod_pago where movimientos_caja.concepto = pagos.codigo group by articulos.codigo)))))), movimientos_caja.observacion) as 'observacion'", false);

            //      $conexion->select("movimientos_caja.observacion as 'observacion'");
            $conexion->select("rubros_caja.subrubro as tipo");

        }
        $conexion->select("movimientos_caja.debe as debe");
        $conexion->from("movimientos_caja");
        $conexion->join("rubros_caja", "rubros_caja.codigo = movimientos_caja.concepto", "LEFT");
        if($filial != '20'){
            $conexion->join("compras_imputaciones", "compras_imputaciones.cod_pago = movimientos_caja.codigo", "LEFT");
        }else{
            $conexion->join("compras_imputaciones", "compras_imputaciones.cod_pago = movimientos_caja.concepto", "LEFT");
        }
        $conexion->join("compras_renglones","compras_renglones.cod_compra = compras_imputaciones.cod_compra","LEFT");
        $conexion->join("articulos","articulos.codigo = compras_renglones.cod_articulo","LEFT");
        $conexion->join("articulos_categorias","articulos_categorias.codigo = articulos.cod_categoria","LEFT");
            if((string)$gasto == 'REDEFINIR'){


                $conexion->where("DATE(movimientos_caja.fecha_hora) >= "."'" . $fecha_desde."'"."
			AND DATE(movimientos_caja.fecha_hora) <= "."'" . $fecha_hasta."'"."
			AND movimientos_caja.concepto <> 19
			AND movimientos_caja.debe != 0
			AND rubros_caja.subrubro is null
			AND `movimientos_caja` . `cod_concepto` = 'PARTICULARES'
			OR(
				DATE(movimientos_caja.fecha_hora) >= "."'" . $fecha_desde."'"."
				AND DATE(movimientos_caja.fecha_hora) <= "."'" . $fecha_hasta."'"."	
				AND	rubros_caja.subrubro = 'REDEFINIR'
			)
			OR(DATE(movimientos_caja.fecha_hora) >= "."'" . $fecha_desde."'"."
				AND DATE(movimientos_caja.fecha_hora) <= "."'" . $fecha_hasta."'"."	
				and	movimientos_caja.cod_concepto = 'PAGOS' and movimientos_caja.concepto > 28 AND articulos_categorias.cod_rubros_caja = 17)");

            }
            else{
                $conexion->where("DATE(movimientos_caja.fecha_hora) >=", $fecha_desde);
                $conexion->where("DATE(movimientos_caja.fecha_hora) <=", $fecha_hasta);
                $conexion->where("rubros_caja.subrubro = " ."'" . $gasto."'
                OR(DATE(movimientos_caja.fecha_hora) >= "."'" . $fecha_desde."'"."
				AND DATE(movimientos_caja.fecha_hora) <= "."'" . $fecha_hasta."'"."	
				and	movimientos_caja.cod_concepto = 'PAGOS' and movimientos_caja.concepto > 28 and articulos_categorias.cod_rubros_caja = "."'" . $cod_sub."'".")");
            }




        $query = $conexion->get();

        $respuesta['data']=array();
        foreach ($query->result_array() as $row)
        {
            $unarow = array(date("d/m/Y",strtotime(str_replace('-','/',$row['fecha']))).', '.$row['observacion'],"$".number_format((float)$row['debe'],2,',','.'),$row['cod_mov_caja']);
            array_push($respuesta['data'],$unarow);
        }

        return $respuesta['data'];

    }



    static public function getReporteRentabilidadIngresos(CI_DB_mysqli_driver $conexion, $total = false, $fecha_desde = false, $fecha_hasta = false)
    {
        $conexion->select("SUM(table1.numeros) AS data");
        $conexion->select("table1.tipo AS label");

        $from = "(SELECT
                  movimientos_caja.haber AS numeros,
                  conceptos.`key` AS tipo
                  FROM
                  movimientos_caja
                  LEFT JOIN ctacte_imputaciones ON ctacte_imputaciones.cod_cobro = movimientos_caja.concepto
                  LEFT JOIN ctacte ON ctacte.codigo = ctacte_imputaciones.cod_ctacte
                  LEFT JOIN conceptos ON conceptos.codigo = ctacte.cod_concepto
                  WHERE
                  movimientos_caja.haber <> 0 ";

        if($fecha_desde)
        {
            $from .= "AND movimientos_caja.fecha_hora_real BETWEEN '" . $fecha_desde . "' AND '" . $fecha_hasta . "'";
        }

        $from .= ") AS table1";

        $conexion->from($from);

        if(!$total)
        {
            $conexion->group_by("table1.tipo");
        }

        $query = $conexion->get();
        //die($conexion->last_query());
        return $query->result_array();
    }

    static public function  getReporteRentabilidadGastos(CI_DB_mysqli_driver $conexion, $discriminar = false, $fecha_desde = false, $fecha_hasta = false, $filial)    {
        if ($discriminar){
            $conexion->select("IF (movimientos_caja.cod_concepto = 'PAGOS' AND movimientos_caja.concepto > 28, (SELECT rubros_caja.subrubro
		from rubros_caja
		where rubros_caja.codigo = articulos_categorias.cod_rubros_caja),".
                                "IFNULL((SELECT rubros_caja.subrubro ".
                                "FROM rubros_caja where rubros_caja.codigo = movimientos_caja.concepto)".
                                ", 'REDEFINIR')) AS label, 
                                IF(movimientos_caja.concepto >27 ,(SELECT rubros_caja.codigo
		from rubros_caja
		where rubros_caja.codigo = articulos_categorias.cod_rubros_caja),(SELECT rubros_caja.codigo ".
                "FROM rubros_caja where rubros_caja.codigo = movimientos_caja.concepto)) as sub", false);

            $conexion->group_by("label");
        }
        $conexion->select("SUM(movimientos_caja.debe) AS data", false);
        $conexion->from("movimientos_caja");
       if($filial != '20'){
           $conexion->join("compras_imputaciones", "compras_imputaciones.cod_pago = movimientos_caja.codigo","LEFT");
       }else{
        $conexion->join("pagos","movimientos_caja.concepto = pagos.codigo","LEFT");
        $conexion->join("compras_imputaciones", "compras_imputaciones.cod_pago = pagos.codigo","LEFT");
       }
        $conexion->join("compras","compras.codigo = compras_imputaciones.cod_compra", "LEFT");
        $conexion->join("compras_renglones","compras_renglones.cod_compra = compras.codigo", "LEFT");
        $conexion->join("articulos","articulos.codigo = compras_renglones.cod_articulo", "LEFT");
        $conexion->join("articulos_categorias","articulos_categorias.codigo = articulos.cod_categoria", "LEFT");
        $conexion->where("(movimientos_caja.cod_concepto = 'PAGOS' OR (movimientos_caja.cod_concepto = 'PARTICULARES' AND movimientos_caja.concepto <> 19 AND movimientos_caja.haber = 0))");
        if ($fecha_desde != null){
            $conexion->where("DATE(movimientos_caja.fecha_hora) >=", $fecha_desde);
        }
        if ($fecha_hasta != null){
            $conexion->where("DATE(movimientos_caja.fecha_hora) <=", $fecha_hasta);
        }
        $query = $conexion->get();
        return $query->result_array();
    }

    function actualizar_importe($importe){
        if ($this->haber > 0){
            $saldoActualizar = $importe - $this->haber;
            $this->haber = $importe;
        } else {
            $saldoActualizar = $this->debe - $importe;
            $this->debe = $importe;
        }
        $resp = $this->guardarMovimientos_caja();
        $condiciones = array(
            "codigo >=" => $this->codigo,
            "cod_caja" => $this->cod_caja,
            "cod_medio" => $this->cod_medio
        );
        $movimientos = self::listarMovimientos_caja($this->oConnection, $condiciones, null, array(array("campo" => "codigo", "orden" => "ASC")));
        foreach ($movimientos as $movimiento){
            $myNuevoMovimiento = new Vmovimientos_caja($this->oConnection, $movimiento['codigo']);
            $myNuevoMovimiento->saldo += $saldoActualizar;
            $resp = $resp && $myNuevoMovimiento->guardarMovimientos_caja();
        }
        return $resp;
    }

    static function updateConcepto(CI_DB_mysqli_driver $conexion, $cod_mov_caja, $subrubro){
        $myNuevoMovimiento = new Vmovimientos_caja($conexion, $cod_mov_caja);
        if($myNuevoMovimiento->concepto < 27){
            $myNuevoMovimiento->concepto = $subrubro;
            $resp = $myNuevoMovimiento->guardarMovimientos_caja();

        }else{
            $resp = $conexion->query("UPDATE articulos_categorias ac
JOIN articulos a
   ON a.cod_categoria = ac.codigo
JOIN compras_renglones cr
   ON cr.cod_articulo = a.codigo
JOIN compras c 
	ON c.codigo = cr.cod_compra
JOIN compras_imputaciones ci 
	ON ci.cod_compra = c.codigo
JOIN pagos p
	ON p.codigo = ci.cod_pago
SET ac.cod_rubros_caja = ".$subrubro."
WHERE p.codigo = ".$myNuevoMovimiento->concepto);
        }
        return $resp;
    }
}

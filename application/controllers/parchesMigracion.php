<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class ParchesMigracion extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model("Model_parchesMigracion", "", false, array('codigo_filial' => 0));        
    }

    public function verificarCobrosSinImputaciones($codFilial){
        $conexion = $this->load->database($codFilial, true);
        $arrCobros = $conexion->query("SELECT * FROM cobros WHERE codigo NOT IN (SELECT ctacte_imputaciones.cod_cobro FROM ctacte_imputaciones)")->result_array();
        $conexion->trans_begin();
        foreach ($arrCobros as $cobro){
            $codCobro = $cobro['codigo'];
            $codAlumno = $cobro['cod_alumno'];
            $importe = $cobro['importe'];
            $fecha = date("Y-m-d H:i:s");
            $arrField = array(
                "cod_concepto" => 32,
                "cod_usuario" => 19,
                "fecha_hora" => $fecha
            );
            if ($conexion->insert("ctacte_otros", $arrField)){
                $concepto = $conexion->insert_id();
                $arrField = array(
                    "cod_alumno" => $codAlumno,
                    "nrocuota" => "1",
                    "importe" => $importe,
                    "pagado" => $importe,
                    "habilitado" => 1,
                    "cod_concepto" => 32,
                    "concepto" => $concepto,
                    "financiacion" => 1,
                    "fecha_creacion" => $fecha
                );
                if ($conexion->insert("ctacte", $arrField)){
                    $codCtacte = $conexion->insert_id();
                    $arrField = array(
                        "cod_ctacte" => $codCtacte,
                        "valor" => $importe,
                        "cod_cobro" => $codCobro,
                        "fecha" => $fecha,
                        "cod_usuario" => 19,
                        "estado" => "confirmado",
                        "tipo" => "cobro"
                    );
                    if ($conexion->insert("ctacte_imputaciones", $arrField)){
                        $arrFactura = $conexion->query("SELECT cod_factura FROM facturas_cobros WHERE cod_cobro = $codCobro")->result_array();
                        if (isset($arrFactura[0]) && isset($arrFactura[0]['cod_factura']) && $arrFactura[0]['cod_factura'] <> ''){
                            $codFactura = $arrFactura[0]['cod_factura'];
                            $arrField = array(
                                "cod_ctacte" => $codCtacte,
                                "cod_factura" => $codFactura,
                                "importe" => $importe,
                                "anulada" => 0
                            );
                            if (!$conexion->insert("facturas_renglones", $arrField)){
                                $conexion->trans_rollback();
                                die("ERROR al insertar en facturas_rengloens");
                            } else {
                                $conexion->trans_commit();
                            }
                        }
                    } else {
                        $conexion->trans_rollback();
                        die("ERROR al insertar ctacte_imputaciones");
                    }
                } else {
                    $conexion->trans_rollback();
                    die("ERROR al insertar ctacte");
                }
            } else {
                $conexion->trans_rollback();
                die("ERROR al insertar ctacte_otros");
            }
        }
    }
    
    public function facturasMigradasDesc($codFilial) {
        $this->Model_parchesMigracion->facturasMigradasDesc($codFilial);
    }
    
    public function cobrosMigradosDesc($codFilial) {
        $this->Model_parchesMigracion->cobrosMigradosDesc($codFilial);
    }

    public function facturasMigradasRecargo($codFilial) {
        $this->Model_parchesMigracion->facturasMigradasRecargo($codFilial);
    }

//    public function facturasMigradasPagosParciales($codFilial) {
//        $this->Model_parchesMigracion->facturasMigradasPagosParciales($codFilial);
//    }

    public function cobrosPeriodoErrado($codFilial) {
        $this->Model_parchesMigracion->cobrosPeriodoErrado($codFilial);
    }

    public function examenesEstadosAcademicosAprobados($codFilial) {
        $this->Model_parchesMigracion->examenesEstadosAcademicosAprobados($codFilial);
    }
    
    public function completarCobrosConSaldo($codFilial){
        $conexion = $this->load->database($codFilial, true);
        $arrCobros = $conexion->query("SELECT cobros.*,
                                            (SELECT SUM(ctacte_imputaciones.valor) 
                                            FROM ctacte_imputaciones 
                                            WHERE ctacte_imputaciones.cod_cobro = cobros.codigo) AS total_imputado
                                        FROM cobros
                                        HAVING cobros.importe - 0.3 > total_imputado ")->result_array();
        $conexion->trans_begin();
        foreach ($arrCobros as $cobro){
            $codCobro = $cobro['codigo'];
            $codAlumno = $cobro['cod_alumno'];
            $importe = $cobro['importe'];
            $imputado = $cobro['total_imputado'];
            $diferencia = $importe - $imputado;
            $fecha = date("Y-m-d H:i:s");
            $arrField = array(
                "cod_concepto" => 32,
                "cod_usuario" => 19,
                "fecha_hora" => $fecha
            );
            if ($conexion->insert("ctacte_otros", $arrField)){
                $concepto = $conexion->insert_id();
                $arrField = array(
                    "cod_alumno" => $codAlumno,
                    "nrocuota" => "1",
                    "importe" => $diferencia,
                    "pagado" => $diferencia,
                    "habilitado" => 1,
                    "cod_concepto" => 32,
                    "concepto" => $concepto,
                    "financiacion" => 1,
                    "fecha_creacion" => $fecha
                );
                if ($conexion->insert("ctacte", $arrField)){
                    $codCtacte = $conexion->insert_id();
                    $arrField = array(
                        "cod_ctacte" => $codCtacte,
                        "valor" => $diferencia,
                        "cod_cobro" => $codCobro,
                        "fecha" => $fecha,
                        "cod_usuario" => 19,
                        "tipo" => "COBRO"
                    );
                    if ($conexion->insert("ctacte_imputaciones", $arrField)){
                        $conexion->trans_commit();
                    } else {
                        $conexion->trans_rollback();
                        die("ERROR al insertar ctacte_imputaciones");
                    }
                } else {
                    $conexion->trans_rollback();
                    die("ERROR al insertar ctacte");
                }
            } else {
                $conexion->trans_rollback();
                die("ERROR al insertar ctacte_cobros");
            }
        }
    }
    
    public function modificarCtasCtes($codFilial) {
        $respuesta=$this->Model_parchesMigracion->modificarCtasCtes($codFilial);
         echo "/***************************** ejecuto modificarCtasCtes -$respuesta- *****************************/<br>";
    }

    public function rectificarCtacteSinSaldo($codFilial){
        $conexion = $this->load->database($codFilial, true);
        $conexion->trans_begin();
        $conexion->query("DELETE FROM matriculaciones_ctacte_descuento 
                            WHERE cod_ctacte in (SELECT codigo FROM (
                                SELECT ctacte.*, ctacte.importe - ctacte.pagado AS saldo_migrado,
                                        (SELECT ctacte_original.saldo
                                            FROM ctacte_original 
                                            WHERE ctacte_original.codigo = ctacte.codigo) AS saldo_original
                                    FROM ctacte
                                    WHERE ctacte.habilitado = 1 AND ctacte.cod_concepto = 1
                                    HAVING saldo_original = 0 AND (saldo_migrado < -0.1 OR saldo_migrado > 0.1))
                            AS tb1)");
        $conexion->query("UPDATE ctacte SET importe = pagado 
                            WHERE codigo IN (SELECT codigo FROM (
                                SELECT ctacte.*, ctacte.importe - ctacte.pagado AS saldo_migrado,
                                        (SELECT ctacte_original.saldo
                                            FROM ctacte_original 
                                            WHERE ctacte_original.codigo = ctacte.codigo) AS saldo_original
                                    FROM ctacte
                                    WHERE ctacte.habilitado = 1 AND ctacte.cod_concepto = 1
                                    HAVING saldo_original = 0 AND  saldo_migrado <> 0)
                            AS tb1)");
        $conexion->query("DROP TABLE IF EXISTS ctacte_original");
        if ($conexion->trans_status()){
            $conexion->trans_commit();
            return true;
        } else {
            $conexion->trans_rollback();
            return false;
        }
    }
    
    public function actualizarCtacteDeudaPasiva($codFilial){
        $conexion = $this->load->database($codFilial, true);
        return $conexion->query("UPDATE ctacte 
                                    SET habilitado = 2 
                                    WHERE cod_concepto IN (1, 5) 
                                    AND concepto IN (
                                        SELECT codigo 
                                            FROM matriculas 
                                            WHERE estado = 'inhabilitada')
                                            AND habilitado = 1 
                                            AND pagado > 0 
                                            AND (pagado < importe - 0.1 or pagado > importe + 0.1)");
    }
    
    public function ejecutar_parches($codFilial){
        ini_set("max_execution_time", "1000000");
        $start = microtime(true);
        $arrMetodos = get_class_methods($this);
        $arrSkip = array("__construct", "get_instance", "ejecutar_parches");
        foreach ($arrMetodos as $metodo){
            $startMethod = microtime(true);
            if (!in_array($metodo, $arrSkip)){
                echo "/***************************** ejecutando $metodo *****************************/<br>";
                $this->$metodo($codFilial);
                $endMethod = microtime(true);
                $timeMethod = round($endMethod - $startMethod, 2);
                echo "[$timeMethod s]";
                echo "<br><br><br>";
            }
        }
        $end = microtime(true);
        $timeScript = round($end - $start, 2);
        echo "TIEMPO TOTAL [$timeScript s]<br>";
        echo "[DB $codFilial]";
    }
}
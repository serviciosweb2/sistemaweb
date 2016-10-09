<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Crons extends CI_Controller {

    public function __construct() {
        parent::__construct();
    }

    /**
     * Elimina archivos de impresion que ya se han impreso (google cloud print)
     */
    public function eliminar_archivos_impresion() {
        $this->load->model("Model_impresiones", "", false);
        $this->Model_impresiones->eliminar_archivos_impresion();
    }

    /*
     * Busca registros de ctacte que se han vencido y que poseen descuento condicionado. Estos registros son cancelados
     * y generados nuevamente con la perdida del descuento
     */

    public function actualizar_descuentos_condicionados() {
        $arr = array("codigo_filial" => 0);
        $this->load->model("Model_ctacte", "", false, $arr);
        $this->Model_ctacte->actualizarDescuentoCondicionado();
    }

    public function perdida_regularidad_materia() {
        $config = array("filial" => array("codigo" => 0));
        $this->load->model("Model_estadoacademico", "", false, $config);
        $this->Model_estadoacademico->perdidaRegularidadMateria();
    }

    /**
     * calcula la mora de ctacte para todas las filiales con el sistema nuevo
     */
    public function calcular_mora() {
        $arr = array("codigo_filial" => 0);
        $this->load->model("Model_ctacte", "", false, $arr);
        $this->Model_ctacte->calcular_mora_crons();
    }

    public function recordatorio_examen() {
        $config = array("codigo_filial" => 0);
        $this->load->model("Model_examenes", "", false, $config);
        $this->Model_examenes->recordatorioExamenes();
    }

    public function ejecutar_tareas_crons() {
        set_time_limit(1500);
        $config = array("codigo_filial" => 0);
        $this->load->model("Model_tareas_crons", "", false, $config);
        $this->Model_tareas_crons->ejecutarTareasCrons();
    }

    public function enviar_alertas_alumnos() {
        set_time_limit(15000);
        $config = array("codigo_filial" => 0);
        $this->load->model("Model_alertas", "", false, $config);
        $this->Model_alertas->enviarAlertasAlumnos();
    }

    /**
     * actualiza los registor del reporte de cierres de cajas para todas las filiales
     */
    public function crons_reportes_caja() {
        $this->load->model("Model_reportes", "", false, 0);
        $this->Model_reportes->crons_reportes_caja();
    }

    public function cumplimiento_requerimientos_certificados() {
        set_time_limit(0);
        $config = array();
        $config["filial"]["codigo"] = 0;
        $this->load->model("Model_certificados", "", false, $config);
        $this->Model_certificados->requerimientosCertificadosPendientes();
    }

    public function finalizar_matriculas_periodos() {
        $config["filial"]["codigo"] = 0;
        $config = array();
        $this->load->model("Model_matriculas_periodos", "", false, $config);
        $this->Model_matriculas_periodos->finalizarMatriculasPeriodos();
    }

    public function certificar_matriculas_periodos() {
        $config["filial"]["codigo"] = 0;
        $config = array();
        $this->load->model("Model_matriculas_periodos", "", false, $config);
        $this->Model_matriculas_periodos->certificarMatriculasPeriodos();
    }

    public function getNuevosUsuariosSincronizacion() {
        $this->load->model("Model_usuario", "", false, null);
        $this->load->library('email');
        $arrUsuarios = $this->Model_usuario->getUsuariosSincronizacionNuevos();
        foreach ($arrUsuarios as $usuario) {
            $idioma = $usuario['idioma'];
            $email = $usuario['email'];
            $this->lang->load($idioma, $idioma);
            $this->Model_usuario->recuperarPassword($email);
        }
    }

    public function sincronizarInboxesExternas() {
        $this->load->model("Model_inboxexterna", "", false, array("codigo_filial" => 0));
/*
        $this->load->model("Model_inboxexterna", "", false, array("codigo_filial" => '20'));
        $this->Model_inboxexterna->sincronizarInboxConDB();
        die();
*/

        $conexion = $this->load->database("default", true);
        $filiales = Vfiliales::listarFiliales($conexion, array("version_sistema" => 2));

        //unset($filiales[18]);
        //print_r($filiales);

        foreach ($filiales as $current_filial) {
            //echo "\n\ncurrent_filial: ";
            //print_r($current_filial);

            //$this->load->model("Model_inboxexterna", "", false, array("codigo_filial" => $current_filial['codigo']));
            //$this->Model_inboxexterna->codigo_filial = $current_filial['codigo'];

            //$this->Model_inboxexterna->setCodigoFilial($current_filial['codigo']);
            $inbox_externa = new Model_inboxexterna(array("codigo_filial" => $current_filial['codigo']));
            $inbox_externa->sincronizarInboxConDB();

            //$this->Model_inboxexterna->sincronizarInboxConDB();
        }

        die();
    }

    /* se comenta la function por cambio en servidores y conexion a base de datos BULI (imposible conectar desde igacloud a buli) */

//    public function sincronizarMailsConsultas() {
//        $this->load->model("Model_consultasweb", "", false, array("codigo_filial" => 0));
//        $this->Model_consultasweb->sincronizarMailsConsultas();
//    }

    public function enviarRespuestaConsultasWeb() {
        $this->load->library('email');
        $this->load->model("Model_consultasweb", "", false, array("codigo_filial" => 0));
        $this->Model_consultasweb->envirRespuestaConsultasCrons();
    }

    public function baja_matricula_automatica() {
        /*
        $this->load->model("Model_matriculas", "", false, array("filial" => array("codigo" => 0)));
        $this->Model_matriculas->baja_matricula_automatica();
        */
    }

    public function regularizar_anteriores() {
        $config = array();
        $filial = $this->session->userdata('filial');
        $config["filial"]["codigo"] = $filial['codigo'];
        $this->load->model("Model_estadoacademico", "", false, $config);
        $this->Model_estadoacademico->regularizarComisionesViejas();
    }

    public function calcular_primer_asistencia() {
        echo "set_time_limit inicio";
        set_time_limit(1500);
        echo "set_time_limit fin";
        //Ticket 4587 -mmori- Se aumenta el memory_limit
        echo "memory_limit inicio";
        ini_set('memory_limit', '-1');
        echo "memory_limit fin";
        $config = array();
        $config["filial"]["codigo"] = 0;
        $this->load->model("Model_estadoacademico", "", false, $config);
        $this->Model_estadoacademico->calcularPrimerAsistencia();
    }

    public function controlar_matriculas_inscripciones() {//no esta aun
        $config = array();
        $config["codigo_filial"] = '20'; //le llega por get la filial
        $this->load->model("Model_matriculas_inscripciones", "", false, $config);
        $this->Model_matriculas_inscripciones->controlarBajaMatriculasInscripciones();
    }

    public function matriculasActivasSistemaViejo() {//no esta aun
        $config = array();
        $config["codigo_filial"] = '20'; //le llega por get la filial
        $this->load->model("Model_matriculas", "", false, $config);
        $this->Model_matriculas->matriculasActivasSistemaViejo();
    }

    /**
     * genera alerta por falta de carga de asistencia
     */
    public function falta_carga_asistencia() {
        $config["codigo_filial"] = 0;
        $this->load->model("Model_matriculas_horarios", "", false, $config);
        $this->Model_matriculas_horarios->alertarCargarAsistencia();
    }

    /**
     * Recalcula % de asistencias de todos los alumnos de todas las filiales
     */
    public function recalcular_porcentajes_asistencias() {
        echo "<pre>";
        echo "function\n";

        try {
            echo "-> try\n";
            $conexion = $this->load->database("default", true);
            //$arrFiliales = Vfiliales::listarFiliales($conexion, array("version_sistema" => 2, ""));

            $arrFiliales = [26];

            //die("\n\nUltima query:\n".$conexion->last_query()."\n\n");

            foreach ($arrFiliales as $filial)
            {
                //echo "--> Filial: ".$filial['codigo']."\n";
                echo "\nFilial: ".$filial;
                //error_log("+ Recalculando asistencias de filial \"".$filial['codigo']."\".");
                error_log("+ Recalculando asistencias de filial \"".$filial."\".");

                //$conexion = $this->load->database($filial['codigo'], true);
                $conexion = $this->load->database("".$filial, true);

                //SELECT estadoacademico.codigo FROM `estadoacademico` WHERE `porcasistencia` IS NOT NULL;
                $conexion->select("estadoacademico.codigo");
                $conexion->from('estadoacademico');
                $conexion->where('porcasistencia IS NOT NULL');
                $query = $conexion->get();

                //echo "<pre>";
                //echo "\nActualizando porcentajes de asistencia...\n\n";

                $query_result = $query->result_array();

                $cantidad = 0;
                $time_ini = time();
                foreach ($query_result as $obj_estado_academico) {
                    //echo "---> foreach, codigo de estado acad: ".$obj_estado_academico['codigo']."\n";
                    //echo "---> foreach, codigo de estado acad: ".$obj_estado_academico['codigo']."\n";
                    $model_vestado_academico = new Vestadoacademico($conexion, $obj_estado_academico['codigo']);

                    $resultado_calculo = $model_vestado_academico->calcular_porcentaje_asistencia();

                    /*
                    echo "\n";
                    print_r($resultado_calculo);
                    echo "\n";
                     */

                    $cantidad++;
                }

                echo "\nTotal procesados: ".$cantidad."\nTiempo de calculo: ".(time() - $time_ini)."\n";
                //echo "</pre>";

                /*
                echo "<pre>";
                print_r($query_result);
                echo "</pre>";
                 */
            }
        } catch (Exception $exc) {
            echo $exc->getTraceAsString();
        }

        echo "</pre>";
    }

    public function envir_facturas_toolsnfe() {
        $conexion = $this->load->database("default", true);
        $arrToolsNFE = Vprestador_toolsnfe::listarPrestador_toolsnfe($conexion, array("estado" => "habilitado"));
        foreach ($arrToolsNFE as $toolsnfe) {
            $codigoPrestador = $toolsnfe['codigo'];
            echo "codigo prestaddor $codigoPrestador<br>";
            $myTools = new Vprestador_toolsnfe($conexion, $codigoPrestador);
            $myPuntoVenta = new Vpuntos_venta($conexion, $myTools->cod_punto_venta);
            $arrFiliales = $myPuntoVenta->getFiliales();
            foreach ($arrFiliales as $filial) {
                $codFilial = $filial['cod_filial'];
                echo "codigo Filial $codFilial<br>";
                $conexionFilial = $this->load->database($codFilial, true);
                $condiciones = array("punto_venta" => $myPuntoVenta->getCodigo(), "estado" => Vfacturas::getEstadoPendiente());
                $arrFacturas = Vfacturas::listarFacturas($conexionFilial, $condiciones, array(0, 50));
                if (count($arrFacturas) > 0) {
                    echo "envia ".count($arrFacturas)."<br>";
                    $myTools->enviarFacturas($conexionFilial, $arrFacturas);
                }
                echo "<br>";
            }
        }
    }

    public function validar_facturas_toolsnfe() {
        $conexion = $this->load->database("default", true);
        $arrToolsNFE = Vprestador_toolsnfe::listarPrestador_toolsnfe($conexion, array("estado" => "habilitado"));
        foreach ($arrToolsNFE as $toolsnfe) {
            $codigoPrestador = $toolsnfe['codigo'];
            echo "codigo prestaddor $codigoPrestador<br>";
            $myTools = new Vprestador_toolsnfe($conexion, $codigoPrestador);
            $myPuntoVenta = new Vpuntos_venta($conexion, $myTools->cod_punto_venta);
            $myFacturante = new Vfacturantes($conexion, $myPuntoVenta->cod_facturante);
            $myCertificado = $myFacturante->getCertificado();
            $arrFiliales = $myPuntoVenta->getFiliales();
            foreach ($arrFiliales as $filial) {
                $codFilial = $filial['cod_filial'];
                echo "codigo Filial $codFilial<br>";
                $conexionFilial = $this->load->database($codFilial, true);
                $arrFacturas = Vseguimiento_toolsnfe::getFacturasConsultar($conexionFilial, $codFilial);
                if (count($arrFacturas) > 0){
                    echo "valida ".count($arrFacturas)."<br>";
                    foreach ($arrFacturas as $factura) {
                        $nRec = $factura['nRec'];
                        $cUF = $factura['cUF'];
                        Vprestador_toolsnfe::verificar($conexionFilial, $nRec, $myCertificado->pry_key, $myCertificado->pub_key, $cUF);
                    }
                }
                echo "<br>";
            }
        }
    }

//    public function testCancelar(){ // test de cancelar factura de productos brasil
//        $conexion = $this->load->database("999", true);
//        $myPrestador = new Vprestador_toolsnfe($conexion, "1");
//        $myFactura = new Vfacturas($conexion, 2);
//        $myPrestador->cancelarFactura($conexion, $myFactura, "Cancelamiento de test de servicio");
//
//    }

    public function enviar_facturas_dsf() {
        $conexion = $this->load->database("default", true);
        $arrDSF = Vprestador_dsf::listarPrestador_dsf($conexion);
        foreach ($arrDSF as $dsf) {
            $codigoPrestador = $dsf['codigo'];
            $myTools = new Vprestador_dsf($conexion, $codigoPrestador);
            $myPuntoVenta = new Vpuntos_venta($conexion, $myTools->cod_punto_venta);
            $arrFiliales = $myPuntoVenta->getFiliales();
            foreach ($arrFiliales as $filial) {
                $codFilial = $filial['cod_filial'];
                $conexionFilial = $this->load->database($codFilial, true);
                $condiciones = array("punto_venta" => $myPuntoVenta->getCodigo(), "estado" => Vfacturas::getEstadoPendiente());
                $arrFacturas = Vfacturas::listarFacturas($conexionFilial, $condiciones);
                if (count($arrFacturas) > 0) {
                    $myTools->enviarFacturas($conexionFilial, $arrFacturas);
                }
            }
        }
    }

    public function validar_facturas_dsf() {
        $conexion = $this->load->database("default", true);
        $arrDSF = Vprestador_dsf::listarPrestador_dsf($conexion);
        foreach ($arrDSF as $dsf) {
            $codigoPrestador = $dsf['codigo'];
            $myTools = new Vprestador_dsf($conexion, $codigoPrestador);
            $myPuntoVenta = new Vpuntos_venta($conexion, $myTools->cod_punto_venta);
            $arrFiliales = $myPuntoVenta->getFiliales();
            foreach ($arrFiliales as $filial) {
                $codFilial = $filial['cod_filial'];
                $conexionFilial = $this->load->database($codFilial, true);
                $arrFacturas = Vseguimiento_dsf::getFacturasPendientesVerificar($conexionFilial, $myTools->cod_punto_venta);
                foreach ($arrFacturas as $factura) {
                    $numeroLote = $factura['numero_lote'];
                    $myTools->verificar($conexionFilial, $numeroLote);
                }
            }
        }
    }

    public function enviar_facturas_abrasf() {
        $conexion = $this->load->database("default", true);
        $arrABRASF = Vprestador_abrasf::listarPrestador_abrasf($conexion);
        foreach ($arrABRASF as $abrasf) {
            $codigoPrestador = $abrasf['codigo'];
            $myTools = new Vprestador_abrasf($conexion, $codigoPrestador);
            $myPuntoVenta = new Vpuntos_venta($conexion, $myTools->cod_punto_venta);
            $arrFiliales = $myPuntoVenta->getFiliales();
            foreach ($arrFiliales as $filial) {
                $codFilial = $filial['cod_filial'];
                $conexionFilial = $this->load->database($codFilial, true);
                $condiciones = array("punto_venta" => $myPuntoVenta->getCodigo(), "estado" => Vfacturas::getEstadoPendiente());
                $arrFacturas = Vfacturas::listarFacturas($conexionFilial, $condiciones);
                if (count($arrFacturas) > 0) {
                    $myTools->enviarFacturas($conexionFilial, $arrFacturas);
                }
            }
        }
    }

    public function validar_facturas_abrasf() {
        $conexion = $this->load->database("default", true);
        $arrDSF = Vprestador_abrasf::listarPrestador_abrasf($conexion);
        foreach ($arrDSF as $dsf) {
            $codigoPrestador = $dsf['codigo'];
            $myTools = new Vprestador_abrasf($conexion, $codigoPrestador);
            $myPuntoVenta = new Vpuntos_venta($conexion, $myTools->cod_punto_venta);
            $arrFiliales = $myPuntoVenta->getFiliales();
            foreach ($arrFiliales as $filial) {
                $codFilial = $filial['cod_filial'];
                $conexionFilial = $this->load->database($codFilial, true);
                $arrFacturas = Vseguimiento_abrasf::getFacturasPendientesVerificar($conexionFilial, $myPuntoVenta->getCodigo());
                foreach ($arrFacturas as $factura) {
                    $protocolo = $factura['protocolo'];
                    $myTools->verificar($conexionFilial, $protocolo);
                }
            }
        }
    }

//    public function textCancelarAbraf(){
//        $conexion = $this->load->database("999", true);
//        $myPrestador = new Vprestador_abrasf($conexion, 3);
//        $myFactura = new Vfacturas($conexion, 13);
//        $myPrestador->cancelarFactura($conexion, $myFactura);
//    }


    public function enviar_facturas_ginfes() {
    $conexion = $this->load->database("default", true);
    $arrGINFES = Vprestador_ginfes::listarPrestador_ginfes($conexion);

    //echo "CRON - arrGINFES..: ";
    //print_r($arrGINFES);

        foreach ($arrGINFES as $ginfes) {

        $codigoPrestador = $ginfes['codigo'];
        $myTools = new Vprestador_ginfes($conexion, $codigoPrestador);

        //echo "CRON - myTools..: ";
        //print_r($myTools);

        $myPuntoVenta = new Vpuntos_venta($conexion, $myTools->cod_punto_venta);

        //echo "CRON - myPuntoVenta..: ";
        //print_r($myPuntoVenta);
        $arrFiliales = $myPuntoVenta->getFiliales();

        echo "CRON - arrFiliales..: ";
        print_r($arrFiliales);

        foreach ($arrFiliales as $filial) {
            $codFilial = $filial['cod_filial'];
            $conexionFilial = $this->load->database($codFilial, true);
            $condiciones = array("punto_venta" => $myPuntoVenta->getCodigo(), "estado" => Vfacturas::getEstadoPendiente());
            
            echo "CRON - condiciones..: ";
            print_r($condiciones);
            $arrFacturas = Vfacturas::listarFacturas($conexionFilial, $condiciones);
            echo "CRON - arrFacturas..: ";
            print_r($arrFacturas);
            if (count($arrFacturas) > 0) {
                $myTools->enviarFacturas($conexionFilial, $arrFacturas);
            }
        }
    }
}
    public function enviar_facturas_paulistanas() {

        $webserviceHitLimit = 1; // La cuantidade de facturas emitidas por lote - cambiar SE necesario - no me gusta de variables "hardcoded"
        $conexion = $this->load->database("default", true);
        $arrPrestador = Vprestador_paulistana::listarPrestador_paulistana($conexion);

        foreach ($arrPrestador as $paulista) {
            //var_dump($paulista);
            $codigoPrestador = $paulista['codigo'];
            $condicionesPrestador = array("codigo" => $codigoPrestador);
            $myPrestador = Vprestador_paulistana::listarPrestador_paulistana($conexion, $condicionesPrestador);
            $thePrestador = new Vprestador_paulistana($conexion, $codigoPrestador);
            $myPuntoVenta = new Vpuntos_venta($conexion, $myPrestador[0]['cod_punto_venta']);
            $arrFiliales = $myPuntoVenta->getFiliales();

            foreach ($arrFiliales as $filial) {
                $codFilial = $filial['cod_filial'];
                var_dump($filial);
                $conexionFilial = $this->load->database($codFilial, true);
                $condiciones = array("punto_venta" => $myPuntoVenta->getCodigo(), "estado" => Vfacturas::getEstadoPendiente());
                $arrFacturas = Vfacturas::listarFacturas($conexionFilial, $condiciones);

                $myPuntoVenta = new Vpuntos_venta($conexion, $myPrestador[0]['cod_punto_venta']);
                $myFacturante = new Vfacturantes($conexion, $myPuntoVenta->cod_facturante);
                $myRazonSocial = new Vrazones_sociales_general($conexion, $myFacturante->cod_razon_social);

                if (count($arrFacturas) > 0 && count($arrFacturas) <= $webserviceHitLimit) {
                    $thePrestador->enviarFacturas($conexionFilial, $myPrestador[0], $arrFacturas);
                } elseif (count($arrFacturas) > $webserviceHitLimit) {
                    $blocksFacturas = array();
                    
                    while (count($arrFacturas) > 0) {
                        $aBlock = array();
                        for ($i = 0; $i < $webserviceHitLimit; $i++){
                            $aBlock[] = array_pop($arrFacturas);
                        }
                        $blocksFacturas[] = $aBlock;
                    }
                    foreach ($blocksFacturas as $block) {
                        $thePrestador->enviarFacturas($conexionFilial, $myPrestador[0], $block);
                    }
                }
            }
        }
    }

    public function validar_facturas_paulistanas(){
        // TODO
    }

    public function validar_facturas_ginfes() {
        $conexion = $this->load->database("default", true);
        $arrDSF = Vprestador_ginfes::listarPrestador_ginfes($conexion);
        foreach ($arrDSF as $dsf) {
            $codigoPrestador = $dsf['codigo'];
            $myTools = new Vprestador_ginfes($conexion, $codigoPrestador);
            $myPuntoVenta = new Vpuntos_venta($conexion, $myTools->cod_punto_venta);
            $arrFiliales = $myPuntoVenta->getFiliales();
            foreach ($arrFiliales as $filial) {
                $codFilial = $filial['cod_filial'];
//                if ($codFilial == 79){
                    $conexionFilial = $this->load->database($codFilial, true);
                    $arrFacturas = Vseguimiento_ginfes::getFacturasPendientesVerificar($conexionFilial, $myPuntoVenta->getCodigo());
                    echo "CRONS - VALIDA GINFES";
                    print_r($arrFacturas);
                    foreach ($arrFacturas as $factura) {
                        $protocolo = $factura['protocolo'];
                        $myTools->verificar($conexionFilial, $protocolo);
                    }
//                }
            }
        }
    }

//    public function textCancelarGinfes(){
//        $conexion = $this->load->database("999", true);
//        $myPrestador = new Vprestador_ginfes($conexion, 2);
//        $myFactura = new Vfacturas($conexion, 9);
//        $error = '';
//        if (!$myPrestador->cancelarFactura($conexion, $myFactura, $error)){
//            echo "Ha ocurrido un error al cancelar la factura con el mensaje $error";
//        } else {
//            echo "Factura cancelada correctamente";
//        }
//    }

    public function sincronizar_tablas_generales() {
        $this->load->model("Model_sincronizacion", "", false);
        $this->Model_sincronizacion->sincronizarTablas();
    }

    public function emitir_alerta_secuencia_boleto_bancario() {
        $conexion = $this->load->database("default", true);
        $arrFacturantes = Vfacturantes::listarFacturantes($conexion);
        foreach ($arrFacturantes as $facturante) {
            $myFacturante = new Vfacturantes($conexion, $facturante['codigo']);
            $conexion->trans_begin();
            $arrSecuenciasFaltantes = Vretornos::getSecuenciasFaltantes($conexion, $myFacturante->getCodigo());
            if (count($arrSecuenciasFaltantes) > 0) {
                $arrFiliales = $myFacturante->getFiliales();
                foreach ($arrFiliales as $filial) {
                    $codFilial = $filial['cod_filial'];
                    $conexionFilial = $this->load->database($codFilial, true);
                    $conexionFilial->trans_begin();
                    $mensaje = "[!--faltan_cargar_las_secuencias_de_archivos_de_boleto_bancario_con_numero--] " . implode(", ", $arrSecuenciasFaltantes);
                    $myAlerta = new Valertas($conexionFilial);
                    $myAlerta->fecha_hora = date("Y-m-d H:i:s");
                    $myAlerta->mensaje = $mensaje;
                    $myAlerta->tipo_alerta = "secuencia_de_boleto_bancario";
                    $myAlerta->guardarAlertas();
                    $usuarios = Vusuarios_sistema::getUsuariosPermisos($conexionFilial, $codFilial, null, null, array('boletos'));
                    foreach ($usuarios as $rowusuario) {
                        $myAlerta->setUsuario($rowusuario['id_usuario']);
                    }
                    if ($conexionFilial->trans_status()) {
                        $conexionFilial->trans_commit();
                    } else {
                        $conexionFilial->trans_rollback();
                    }
                }
                $ultimoCodigoAlertar = $arrSecuenciasFaltantes[count($arrSecuenciasFaltantes) - 1];
                Vretornos::marcarAlertadas($conexion, $myFacturante->getCodigo(), $ultimoCodigoAlertar);
                $ultimoNumeroSecuenciaIncorrecto = $arrSecuenciasFaltantes[0];
                Vretornos::marcarSecuenciasCorrectas($conexion, $myFacturante->getCodigo(), $ultimoNumeroSecuenciaIncorrecto);
            }
        }
    }

    public function conciliar_cobros() {
        $config["codigo_filial"] = 0;
        $this->load->model("Model_cobros", "", false, $config);
        $this->Model_cobros->conciliarCobros();
    }

    public function baja_matriculas_fuera_ciclo(){ // se deshabilita hasta tener una configuracion de la filial para baja automatica y con la cantidad de meses
//        $conexion = $this->load->database("default", true);
//        $arrFiliales = Vfiliales::listarFiliales($conexion, array("version_sistema" => 2));
//        foreach ($arrFiliales as $filial){
//            $codFilial = $filial['codigo']; echo $filial['codigo']."<br>";
//            $conexion = $this->load->database($codFilial, true);
//            $arrMatriculasPeriodos = Vmatriculas_periodos::getMatriculasPeriodosFueraDeCiclo($conexion, 4);
//            echo "filial ".$filial['codigo']." ".count($arrMatriculasPeriodos)." registros<br>";
//            foreach ($arrMatriculasPeriodos as $matriculaPeriodo){
//                $myMatriculaPeriodo = new Vmatriculas_periodos($conexion, $matriculaPeriodo['codigo']);
//                $myMatricula = new Vmatriculas($conexion, $myMatriculaPeriodo->cod_matricula);
//                $conexion->trans_begin();
//                $myMatriculaPeriodo->baja(6, null, 1);
//                $arrPeriodos = $myMatricula->getPeriodosMatricula(Vmatriculas_periodos::getEstadoHabilitada());
//                if (count($arrPeriodos) == 0){
//                    $myMatricula->baja($conexion, 1, true);
//                }
//                if ($conexion->trans_status()){
//                    $conexion->trans_commit();
//                } else {
//                    $conexion->trans_rollback();
//                }
//            }
//        }
    }

    public function enviar_bienvenida_aspirantes(){
        $this->load->library('email');
        $config = array();
        $config['charset'] = 'iso-8859-1';
        $conexion = $this->load->database("default", true);
        $arrFiliales = Vfiliales::listarFiliales($conexion, array("version_sistema" => 2));
        $myTemplate = new Vtemplates($conexion, 87);
        $template = $myTemplate->html;
        foreach ($arrFiliales as $filial){
            $codFilial = $filial['codigo'];
            $fromEmail = $filial['email'];
            $fromName = "IGA {$filial['nombre']}";
            $idioma = $filial['idioma'];
            $conexion = $this->load->database($codFilial, true);
            $this->lang->load($idioma, $idioma);
            $asunto = lang("bienvenido_a")." {$fromName}";
            $condiciones = array();
            $condiciones['email_enviado'] = 0;
            $condiciones['TRIM(email) <>'] = '';
            $arrAspirantes = Vaspirantes::listarAspirantes($conexion, $condiciones);
            foreach ($arrAspirantes as $aspirante){
                $cuerpomail = $template;
                $myAspirante = new Vaspirantes($conexion, $aspirante['codigo']);
                maquetados::desetiquetarAspirantes($conexion, $myAspirante->getCodigo(), $cuerpomail);
                maquetados::desetiquetarDatosFilial($conexion, null, $cuerpomail, $codFilial);
                maquetados::desetiquetarIdioma($cuerpomail, true);
                $this->email->initialize($config);
                $this->email->from($fromEmail, $fromName);
                $this->email->to($myAspirante->email);
                $this->email->subject(utf8_decode($asunto));
                $this->email->message(utf8_decode($cuerpomail));
                if ($this->email->send()){
                    $myAspirante->marcarEnvioBienvenida();
                }
            }
        }
    }

    public function set_comisiones_desuso(){
        $conexion = $this->load->database("default", true);
        $condiciones = array(
            "version_sistema" => 2
        );
        $arrFiliales = Vfiliales::listarFiliales($conexion, $condiciones);
        $arrPlanesPeriodos = Vplanes_academicos::getPlanesAcademicosCantidadPeriodos($conexion);
        $arrPlanes2Periodos = array();
        $arrPlanes1Periodo = array();
        foreach ($arrPlanesPeriodos as $planesPeriodos){
            if ($planesPeriodos['cantidad'] == 1){
                $arrPlanes1Periodo[] = $planesPeriodos['codigo'];
            } else {
                $arrPlanes2Periodos[] = $planesPeriodos['codigo'];
            }
        }
        foreach ($arrFiliales as $filial){
            $codFilial = $filial['codigo'];
            $conexion = $this->load->database($codFilial, true);
            $arrComisiones = Vcomisiones::getComisiones($conexion, array("comisiones.estado" => Vcomisiones::getEstadoHabilitada()), null, true, true);
            foreach ($arrComisiones as $comision){
                $myComision = new Vcomisiones($conexion, $comision['codigo']);
                if (($comision['cod_tipo_periodo'] == 1 && in_array($comision['cod_plan_academico'], $arrPlanes1Periodo))
                        || ($comision['cod_tipo_periodo']) == 2 && in_array($comision['cod_plan_academico'], $arrPlanes2Periodos)
                        || $comision['cantidad_cursantes'] == 0){
                    $myComision->setDesuso();
                } else {
                    $myComision->setAPasar();
                }
            }
        }
    }

    public function comparar_lang(){
        $lang = array();
        include APPPATH."language/es/es_lang.php";
        $es_lang = $lang;
        $lang = array();
        include APPPATH."language/pt/pt_lang.php";
        $pt_lang = $lang;
        $arrFaltantes = array();
        foreach ($es_lang as $key => $value){
            if (!isset($pt_lang[$key])){
                $arrFaltantes[] = array("key" => $key, "value" => htmlentities($value));
            }
        }
        foreach ($arrFaltantes as $faltante){
            echo "\$lang['{$faltante['key']}'] = '{$faltante['value']}';<br>";
        }
    }

    public function enviar_mail_reserva_inscripcion(){
        $this->load->library('email');
        $config = array();
        $config['charset'] = 'iso-8859-1';
        $conexionDefault = $this->load->database("default", true);
        $arrReservas = Vreserva_inscripciones::listarReserva_inscripciones($conexionDefault, array("confirmacion_enviada" => 0));
        foreach ($arrReservas as $reserva){
            $myReserva = new Vreserva_inscripciones($conexionDefault, $reserva['id']);
            if ($myReserva->confirmacion_enviada == 0){ // previene dos ejecuciones del crons en estado desfazado
                $myFilial = new Vfiliales($conexionDefault, $myReserva->id_filial);
                $this->lang->load($myFilial->idioma, $myFilial->idioma);
                $conexion = $this->load->database($myReserva->id_filial, true);
                $myComision = new Vcomisiones($conexion, $myReserva->id_comision);
                $arrHorarios = $myComision->getHorarios();
                $myPlanAcademico = new Vplanes_academicos($conexionDefault, $myComision->cod_plan_academico);
                $myCurso = new Vcursos($conexionDefault, $myPlanAcademico->cod_curso);
                $nombreExtension = "nombre_".$myFilial->idioma;
                $body = "<center>";
                $body .= "<table style='color: #666666'>";
                $body .= "<tr>";
                $body .= "<td>".mb_strtoupper(lang("informacion_de_reserva_para"))."</td>";
                $body .= "</tr>";
                $body .= "<tr>";
                $body .= "<td>".$myCurso->$nombreExtension."</td>";
                $body .= "</tr>";
                $body .= "<tr>";
                $body .= "<td>".lang("nombre")." ".ucwords(strtolower($myReserva->nombre))."</td>";
                $body .= "</tr>";
                $body .= "<tr>";
                $body .= "<td>".lang("telefono")." ".$myReserva->telefono."</td>";
                $body .= "</tr>";
                $body .= "<tr>";
                $body .= "<td>".lang("email")." ".$myReserva->email."<td>";
                $body .= "</tr>";
                $body .= "<tr>";
                $body .= "<td>";
                $body .= "<table border='0' cellspacing='0' cellpadding='0' style='color: #666666'>";
                $body .= "<tr>";
                $body .= "<td style='border-right: 1px white solid; background-color: #1C5FA6; color: white; font-weight: bold; width: 180px; text-align: center'>".mb_strtoupper(lang("comision"))."</td>";
                $body .= "<td>";
                $body .= "<table border='0' cellspacing='0' cellpadding='0' style='color: #666666'>";
                $body .= "<tr>";
                $body .= "<td colspan='3' style='background-color: #1C5FA6; color: white; font-weight: bold; text-align: center; padding: 6px 0px;'>".mb_strtoupper(lang("cursado"))."</td>";
                $body .= "</tr>";
                $body .= "<tr>";
                $body .= "<td style='border-top: 1px #cccccc solid; border-right: 1px #cccccc solid; background-color: #1C5FA6; color: #9EB6D2; width: 100px; font-size: 12px; text-align: center; padding: 6px 0px;'>".mb_strtoupper(lang("inicio_de_clases"))."</td>";
                $body .= "<td style='border-top: 1px #cccccc solid; border-right: 1px #cccccc solid; background-color: #1C5FA6; color: #9EB6D2; width: 100px; font-size: 12px; text-align: center; padding: 6px 0px;'>".mb_strtoupper(lang("dias"))."</td>";
                $body .= "<td style='border-top: 1px #cccccc solid; background-color: #1C5FA6; color: #9EB6D2; width: 100px; font-size: 12px; text-align: center; padding: 6px 0px;'>".mb_strtoupper(lang("horario"))."</td>";
                $body .= "</tr>";
                $body .= "</table>";
                $body .= "</td>";
                $body .= "</tr>";
                $body .= "<tr>";
                $body .= "<td style='border-top: 1px #cccccc solid; border-bottom: 1px #cccccc solid; border-left: 1px #cccccc solid; text-align: center; padding: 0'>";
                $body .= mb_strtoupper(lang("comision"))." {$myComision->getCodigo()}";
                $body .= "</td>";
                $body .= "<td>";
                $body .= "<table border='0' cellspacing='0' cellpadding='0' style='color: #666666'>";
                $body .= "<tr>";
                $body .= "<td style='border: 1px #cccccc solid; width: 99px; font-size: 12px; text-align: center; padding: 6px 0px;'>".formatearFecha_pais($myComision->getFechaInicio(), $myReserva->id_filial)."</td>";
                $body .= "<td colspan='2'>";
                $body .= "<table border='0' cellspacing='0' cellpadding='0' style='color: #666666'>";
                if (count($arrHorarios) == 0){
                    $body .= "<tr>";
                    $body .= "<td style='border-right: 1px #cccccc solid; border-bottom: 1px #cccccc solid; width: 99px; font-size: 12px; text-align: center; padding: 6px 0px;'>(".lang("a_definir").")</td>";
                    $body .= "<td style='border-right: 1px #cccccc solid; border-bottom: 1px #cccccc solid; width: 99px; font-size: 12px; text-align: center; padding: 6px 0px;'>(".lang("a_definir").")</td>";
                    $body .= "</tr>";
                } else {
                    for ($i = 0; $i < count($arrHorarios); $i++){
                        $border = $i == count($arrHorarios) - 1 ? "border-bottom: 1px #cccccc solid;" : '';
                        $body .= "<tr>";
                        $body .= "<td style='border-right: 1px #cccccc solid; $border width: 99px; font-size: 12px; text-align: center; padding: 6px 0px;'>".getDiaNombre($arrHorarios[$i]['DIA_SEMANA'])."</td>";
                        $body .= "<td style='border-right: 1px #cccccc solid; $border width: 99px; font-size: 12px; text-align: center; padding: 6px 0px;'>de ".substr($arrHorarios[$i]['horadesde'], 0, 5)." a ".substr($arrHorarios[$i]['horahasta'], 0, 5)."</td>";
                        $body .= "</tr>";
                    }
                }
                $body .= "</table>";
                $body .= "</td>";
                $body .= "</tr>";
                $body .= "</table>";
                $body .= "</td>";
                $body .= "</tr>";
                $body .= "</table>";
                $body .= "</td>";
                $body .= "</tr>";
                $body .= "<tr style='height: 10px;'>";
                $body .= "<td>&nbsp;</td>";
                $body .= "</tr>";
                $body .= "<tr>";
                $body .= "<td>".lang("para_confirmar_la_reserva_puede_comunicarse_a").":</td>";
                $body .= "</tr>";
                $body .= "<tr>";
                $body .= "<td style='font-weight: bold; color: #1C5FA6;'>IGA {$myFilial->nombre}</td>";
                $body .= "</tr>";
                $body .= "<tr>";
                $body .= "<td style='color: #1C5FA6;'>{$myFilial->domicilio}</td>";
                $body .= "</tr>";
                $body .= "<tr>";
                $body .= "<td style='color: #1C5FA6;'>Tel: {$myFilial->telefono}</td>";
                $body .= "</tr>";
                $body .= "<tr>";
                $body .= "<td style='color: #1C5FA6;'>{$myFilial->email}</td>";
                $body .= "</tr>";
                $body .= "</table>";
                $body .= "</center>";
                $body = utf8_decode($body);
                $this->email->initialize($config);
                $this->email->from($myFilial->email, "IGA ".$myFilial->nombre);
                $this->email->to($myReserva->email);
                $this->email->subject(utf8_decode(lang("confirmacion_de_reserva_inscripcion")));
                $this->email->message($body);
                if ($this->email->send()){
                    $myReserva->setConfirmacionEnviada();
                }
            }
        }
    }

//    public function testFileTemp(){
//        $contenido = "prueba de guardar archivo en temporal";
//        $tempDir = sys_get_temp_dir();
//        file_put_contents("$tempDir/test.txt", $contenido);
//        $lectura = file_get_contents("$tempDir/test.txt");
//        echo "el contenido leido desde archivo es: $lectura";
//    }

    public function actualizar_tablas(){
//        $conexion = $this->load->database("default", true);
//        $condiciones = array(
//            "version_sistema" => 2 // agregar "codigo" => 999 para ejecutar sobre una base de datos específica
//        );
//        $arrFiliales = Vfiliales::listarFiliales($conexion, $condiciones);
//        foreach ($arrFiliales as $filial){
//            $conexion = $this->load->database($filial['codigo'], true);
//            $conexion->trans_begin();
//
//            $query = "ALTER TABLE matriculaciones_ctacte_descuento
//                            MODIFY COLUMN estado  enum('condicionado','no_condicionado','condicionado_perdido','condicionado_descartado')
//                            CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL AFTER descuento";
//            $conexion->query($query);
//            /* ejecutar otras querys de ser necesario */
//
//
//            if ($conexion->trans_status()){
//                $conexion->trans_commit();
//                echo "filial ".$filial['codigo']." ok.<br>";
//            } else {
//                $conexion->trans_rollback();
//                echo "error al ejecutar:<br>$query<br>con el mensaje<br>[".$conexion->_error_number()."] ".$conexion->_error_message();
//                echo "en la filial ".$filial['codigo'];
//            }
//        }
    }

    public function actualizar_puntos_venta_facturantes_argentina() {
        $conexion = $this->load->database("default", true);

        $facturantes = Vfacturantes::getFacturantes($conexion, null, 0, true, null, false, null, true);

        foreach ($facturantes as $rowfacturante) {
            if ($rowfacturante['cod_pais'] == '1') {
                $objcertificado = new Vfacturantes_certificados($conexion, $rowfacturante['codigo']);
                if ($objcertificado->getActivo()) {
                    $objfacturante = new Vfacturantes($conexion, $rowfacturante['codigo']);
                    $objfacturante->actualizarPuntosVentaElectronico($this->config->item('ws_afip_testing'));
                }
            }
        }
    }

    public function reporte_alumnos_activos(){
        $conexion = $this->load->database("general", true);
        $arrFiliales = Vfiliales::listarFiliales($conexion, array("baja" => 0));
        $fecha = date("Y-m-d");
        foreach ($arrFiliales as $filial){
            $codFilial = $filial['codigo'];
            $param = array("filial" => array("codigo" => $codFilial));
            $this->load->model("Model_matriculas", "", false, $param);
            $arrResp = $this->Model_matriculas->getReporteAlumnosActivos($codFilial);
            foreach ($arrResp as $reporte){
                $idPlanAcademico = $reporte['cod_plan_academico'];
                $idPeriodo = $reporte['cod_tipo_periodo'];
                $modalidad = $reporte['modalidad'];
                $myReporte = new reporte_alumnos_activos($conexion, $codFilial, $fecha, $idPlanAcademico, $idPeriodo, $modalidad);
                $myReporte->cantidad = $reporte['cantidad'];
                $myReporte->nombre_categoria = $reporte['nombre_categoria'];
                $myReporte->cod_categoria = $reporte['cod_categoria'];
                if (!$myReporte->guardar()){
                    echo "[".$conexion->_error_number()."] ".$conexion->_error_message();
                    die();
                }
            }
        }
    }

    public function parche_certificados_1($codFilial = null){
//
//        try {
//
//
//
//        $arrTipo1 = array(1,22,95,115,116,32,34,102,33,24,107,123,124,101,125,126,127,130); // primer año
//        $arrTipo2 = array(1,30,57,95); // 1 y 2 año
//        $arrTipo3 = array(3,4,5,6,7,8,9,10,11,12,13,14,15,16,21,23,25,26,27,28,29,36,37,38,39,40,41,51,52,53,54,55,58,59,60,61,62,64,65,35,75,76,77,78,79,83,84,88,89,90,94,96,110,117,114,113); // 1
//        $arrTipo4 = array(17,18,19); // 1 año
//        $arrTipo5 = array(2,20,31); // 1 año
//        $arrTipo6 = array(66,42,43,44,45,46,47,48,49,50,56,67,68,69,70,71,72,73,74,80,81,82,85,86,87,91,92,93,97,98,99,100,103,104,105,106,108,109,111,112,118,119,120,121,122,128,129); // 1primer año
//        $arrtipo7 = array(63); // 1 año
//        $conexion = $this->load->database("default", true);
//        if ($codFilial != null){
//            $arrFiliales = Vfiliales::listarFiliales($conexion, array("codigo" => "$codFilial","version_sistema" => 2, "baja" => "0"));
//        } else {
//            $arrFiliales = Vfiliales::listarFiliales($conexion, array("version_sistema" => 2, "baja" => "0"));
//        }
//        foreach ($arrFiliales as $filial){
//            $cantiadadModificados = 0;
//            $cantidadModificados1 = 0;
//            $cantidadModificados2 = 0;
//            $cantidadModificados3 = 0;
//            $cantidadModificados4 = 0;
//            $codFilial = $filial['codigo'];
//            $conexion = $this->load->database($codFilial, true);
//            $arrCertificados = Vcertificados::listarCerfificados($conexion, array("cod_certificante" => 1));
//            foreach ($arrCertificados as $certificado){
//                $codMatriculaPeriodo = $certificado['cod_matricula_periodo'];
//                $myCertificado = new Vcertificados($conexion, $codMatriculaPeriodo, 1);
//                $myMatriculaPeriodo = new Vmatriculas_periodos($conexion, $codMatriculaPeriodo);
//                $codMatricula = $myMatriculaPeriodo->cod_matricula;
//                $myMatricula = new Vmatriculas($conexion, $myMatriculaPeriodo->cod_matricula);
//                $codTipoPeriodo = $myMatriculaPeriodo->cod_tipo_periodo;
//                $myPlanAcademico = new Vplanes_academicos($conexion, $myMatricula->cod_plan_academico);
//                $codCurso = $myPlanAcademico->cod_curso;
//                $codTipoCertificado = 0;
//                if ($codTipoPeriodo == 2){
//                    if (in_array($codCurso, $arrTipo2)) $codTipoCertificado = 2;
//                } else {
//                    if (in_array($codCurso, $arrTipo1)){
//                        $codTipoCertificado = 1;
//                    } else if (in_array($codCurso, $arrTipo3)){
//                        $codTipoCertificado = 3;
//                    } else if (in_array($codCurso, $arrTipo4)){
//                        $codTipoCertificado = 4;
//                    } else if (in_array($codCurso, $arrTipo5)){
//                        $codTipoCertificado = 5;
//                    } else if (in_array($codCurso, $arrTipo6)){
//                        $codTipoCertificado = 0; // no vertificar certificado de asistencia
//                    } else if (in_array($codCurso, $arrtipo7)){
//                        $codTipoCertificado = 7;
//                    }
//                }
//                if ($codTipoCertificado > 0){
//                    $conexion->select("*");
//                    $conexion->from("bancos.certificados");
//                    $conexion->where("bancos.certificados.codmatri", $codMatricula);
//                    $conexion->where("bancos.certificados.codcurso", $codCurso);
//                    $conexion->where("bancos.certificados.codtipocertificado", $codTipoCertificado);
//                    $conexion->where("bancos.certificados.codfilial", $codFilial);
//                    $conexion->where("emitido", "1");
//                    $query = $conexion->get();
//                    $arrResp = $query->result_array();
//                    if (count($arrResp) > 0){
//                        if ($myCertificado->estado <> Vcertificados::getEstadoFinalizado()){
//                            $myCertificado->estado = Vcertificados::getEstadoFinalizado();
//                            $myCertificado->guardarCertificados();
//                            $cantiadadModificados ++;
//                            $cantidadModificados1 ++;
//                        }
//                    } else {
//                        $conexion->select("*");
//                        $conexion->from("bancos.ac_certificados");
//                        $conexion->where("bancos.ac_certificados.cod_filial", $codFilial);
//                        $conexion->where("bancos.ac_certificados.cod_matricula", $codMatricula);
//                        $conexion->where("bancos.ac_certificados.cod_plan_academico", $myMatricula->cod_plan_academico);
//                        $conexion->where("bancos.ac_certificados.cod_certificante", "1");
//                        $conexion->where("bancos.ac_certificados.cod_tipo_periodo", $codTipoCertificado);
//                        $query = $conexion->get();
//                        $arrResp = $query->result_array();
//                        if (count($arrResp) > 0){
//                            $estado = $arrResp[0]['estado'];
//                            if ($estado == 'finalizado'){
//                                if ($myCertificado->estado <> Vcertificados::getEstadoFinalizado()){
//                                    $myCertificado->estado = Vcertificados::getEstadoFinalizado();
//                                    $myCertificado->guardarCertificados();
//                                    $cantiadadModificados ++;
//                                    $cantidadModificados2 ++;
//                                }
//                            } else {
//                                $myCertificado->estado = Vcertificados::getEstadoEnProceso();
//                                $myCertificado->guardarCertificados();
//                                $cantiadadModificados ++;
//                                $cantidadModificados3 ++;
//                            }
//                        } else {
//                            if ($myCertificado->estado == Vcertificados::getEstadoFinalizado() ||
//                                    $myCertificado->estado == Vcertificados::getEstadoEnProceso() ||
//                                    $myCertificado->estado == Vcertificados::getEstadoPendienteImpresion()){
//                                $myCertificado->estado = Vcertificados::getEstadoPendiente();
//                                $myCertificado->guardarCertificados();
//                                $cantiadadModificados ++;
//                                $cantidadModificados4 ++;
//                            }
//                        }
//                    }
//                }
//            }
//            echo "Filial $codFilial cantidad modificados $cantiadadModificados<br>";
//            echo "modificados1 $cantidadModificados1<br>";
//            echo "modificados2 $cantidadModificados2<br>";
//            echo "modificados3 $cantidadModificados3<br>";
//            echo "modificados4 $cantidadModificados4<br><br>";
//        }
//        } catch (Exception $exc) {
//            echo $exc->getTraceAsString();
//        }
    }

    public function parche_certificados_2($codigoFilial = null){
//        $arrTipo1 = array(1,22,95,115,116,32,34,102,33,24,107,123,124,101,125,126,127,130); // primer año
//        $arrTipo2 = array(1,30,57,95); // 1 y 2 año
//        $arrTipo3 = array(3,4,5,6,7,8,9,10,11,12,13,14,15,16,21,23,25,26,27,28,29,36,37,38,39,40,41,51,52,53,54,55,58,59,60,61,62,64,65,35,75,76,77,78,79,83,84,88,89,90,94,96,110,117,114,113); // 1
//        $arrTipo4 = array(17,18,19); // 1 año
//        $arrTipo5 = array(2,20,31); // 1 año
//        $arrTipo6 = array(66,42,43,44,45,46,47,48,49,50,56,67,68,69,70,71,72,73,74,80,81,82,85,86,87,91,92,93,97,98,99,100,103,104,105,106,108,109,111,112,118,119,120,121,122,128,129); // 1primer año
//        $arrtipo7 = array(63); // 1 año
//        $conexion = $this->load->database("default", true);
//        if ($codigoFilial != null){
//            $arrFiliales = Vfiliales::listarFiliales($conexion, array("codigo" => "$codigoFilial", "version_sistema" => 2, "baja" => "0"));
//        } else {
//            $arrFiliales = Vfiliales::listarFiliales($conexion, array("version_sistema" => 2, "baja" => "0"));
//        }
//        foreach ($arrFiliales as $filial){
//            $cantiadadModificados = 0;
//            $cantidadModificados1 = 0;
//            $cantidadModificados2 = 0;
//            $cantidadModificados3 = 0;
//            $cantidadModificados4 = 0;
//            $codFilial = $filial['codigo'];
//            $conexion = $this->load->database($codFilial, true);
//            $arrCertificados = Vcertificados::listarCerfificados($conexion, array("cod_certificante" => 2));
//            foreach ($arrCertificados as $certificado){
//                $codMatriculaPeriodo = $certificado['cod_matricula_periodo'];
//                $myCertificado = new Vcertificados($conexion, $codMatriculaPeriodo, 2);
//                $myMatriculaPeriodo = new Vmatriculas_periodos($conexion, $codMatriculaPeriodo);
//                $codMatricula = $myMatriculaPeriodo->cod_matricula;
//                $myMatricula = new Vmatriculas($conexion, $myMatriculaPeriodo->cod_matricula);
//                $codTipoPeriodo = $myMatriculaPeriodo->cod_tipo_periodo;
//                $myPlanAcademico = new Vplanes_academicos($conexion, $myMatricula->cod_plan_academico);
//                $codCurso = $myPlanAcademico->cod_curso;
//                $codTipoCertificado = 0;
//                if ($codTipoPeriodo == 2){
//                    if (in_array($codCurso, $arrTipo2)) $codTipoCertificado = 2;
//                } else {
//                    if (in_array($codCurso, $arrTipo1)){
//                        $codTipoCertificado = 1;
//                    } else if (in_array($codCurso, $arrTipo3)){
//                        $codTipoCertificado = 3;
//                    } else if (in_array($codCurso, $arrTipo4)){
//                        $codTipoCertificado = 4;
//                    } else if (in_array($codCurso, $arrTipo5)){
//                        $codTipoCertificado = 5;
//                    } else if (in_array($codCurso, $arrTipo6)){
//                        $codTipoCertificado = 0; // no vertificar certificado de asistencia
//                    } else if (in_array($codCurso, $arrtipo7)){
//                        $codTipoCertificado = 7;
//                    }
//                }
//                if ($codTipoCertificado > 0){
//                    $conexion->select("*");
//                    $conexion->from("bancos.certificados");
//                    $conexion->where("bancos.certificados.codmatri", $codMatricula);
//                    $conexion->where("bancos.certificados.codcurso", $codCurso);
//                    $conexion->where("bancos.certificados.codtipocertificado", $codTipoCertificado);
//                    $conexion->where("bancos.certificados.codfilial", $codFilial);
//                    $conexion->where("bancos.certificados.es_certificacion", "1");
//                    $conexion->where("emitido", "1");
//                    $query = $conexion->get();
//                    $arrResp = $query->result_array();
//                    if (count($arrResp) > 0){
//                        if ($myCertificado->estado <> Vcertificados::getEstadoFinalizado()){
//                            $myCertificado->estado = Vcertificados::getEstadoFinalizado();
//                            $myCertificado->guardarCertificados();
//                            $cantiadadModificados ++;
//                            $cantidadModificados1 ++;
//                        }
//                    } else {
//                        $conexion->select("*");
//                        $conexion->from("bancos.ac_certificados");
//                        $conexion->where("bancos.ac_certificados.cod_filial", $codFilial);
//                        $conexion->where("bancos.ac_certificados.cod_matricula", $codMatricula);
//                        $conexion->where("bancos.ac_certificados.cod_plan_academico", $myMatricula->cod_plan_academico);
//                        $conexion->where("bancos.ac_certificados.cod_certificante", "2");
//                        $conexion->where("bancos.ac_certificados.cod_tipo_periodo", $codTipoCertificado);
//                        $query = $conexion->get();
//                        $arrResp = $query->result_array();
//                        if (count($arrResp) > 0){
//                            $estado = $arrResp[0]['estado'];
//                            if ($estado == 'finalizado'){
//                                if ($myCertificado->estado <> Vcertificados::getEstadoFinalizado()){
//                                    $myCertificado->estado = Vcertificados::getEstadoFinalizado();
//                                    $myCertificado->guardarCertificados();
//                                    $cantiadadModificados ++;
//                                    $cantidadModificados2 ++;
//                                }
//                            } else {
//                                $myCertificado->estado = Vcertificados::getEstadoEnProceso();
//                                $myCertificado->guardarCertificados();
//                                $cantiadadModificados ++;
//                                $cantidadModificados3 ++;
//                            }
//                        } else {
//                            if ($myCertificado->estado == Vcertificados::getEstadoFinalizado() ||
//                                    $myCertificado->estado == Vcertificados::getEstadoEnProceso() ||
//                                    $myCertificado->estado == Vcertificados::getEstadoPendienteImpresion()){
//                                $myCertificado->estado = Vcertificados::getEstadoPendiente();
//                                $myCertificado->guardarCertificados();
//                                $cantiadadModificados ++;
//                                $cantidadModificados4 ++;
//                            }
//                        }
//                    }
//                }
//            }
//            echo "Filial $codFilial cantidad modificados $cantiadadModificados<br>";
//            echo "modificados1 $cantidadModificados1<br>";
//            echo "modificados2 $cantidadModificados2<br>";
//            echo "modificados3 $cantidadModificados3<br>";
//            echo "modificados4 $cantidadModificados4<br><br>";
//        }
    }
    
    //revisar funcionalidad para evitar recuperar todos los videos
    //se puede recuperar solo los videos sin video_id para actualizar 
    //revisar si tiene sentido actualizar la propiedad in_progress
    public function actualizarDatosVideosLiveStream()
    {
        $account = 7366263;
        $live = new livestream($account);
        $resp = array();

        $conexion = $this->load->database("material_didactico", true);
        $ahora = date("Y-m-d H:m:s");
        
        $condiciones = array("ADDDATE(material_didactico.videos.fecha_publicacion,INTERVAL material_didactico.videos.duracion SECOND ) <= "=>$ahora);
                
        $arrvideos = Vvideos::listar($conexion, $condiciones);

        foreach ($arrvideos as $arrvideo)
        {
            $video = new Vvideos($conexion, $arrvideo['id']);
            
            $cond = array ("material_didactico.videos_propiedades.propiedad"=>"evento_id");
            $propiedad = $video->getPropiedades($cond);
            
            if($propiedad[0]['propiedad'] == 'evento_id')
            {
                $id_evento = $propiedad[0]['valor'];
                
                $event = $live->get_event($id_evento);
                
                if(isset($event->feed->data))
                {
                    foreach ($event->feed->data as $data)
                    {
                        $query = "INSERT INTO material_didactico.videos_propiedades (id_video, propiedad, valor) values('{$arrvideo['id']}', 'video_id', '{$data->data->id}') ON DUPLICATE KEY UPDATE valor = '{$data->data->id}'";
                        $conexion->query($query);
                    }
                }
            }
        }
    }
    
    public function baja_prematricula(){
        $conexion = $this->load->database("general", true);
        $arrFiliales = Vfiliales::listarFiliales($conexion, array("baja" => 0));
        foreach ($arrFiliales as $filial){
            $conexion = $this->load->database($filial['codigo'], true);
            $this->load->model("Model_matriculas", "", false, array("filial" => array("codigo" => $filial['codigo'])));
            $arrPrematriculas = Vmatriculas::getPrematriculas($conexion, 48);
            if (count($arrPrematriculas) > 0){
                echo $filial['nombre']." ".count($arrPrematriculas)."<br>";
                foreach ($arrPrematriculas as $matricula){
                    $arrDatos = array( 0 => array(
                            "cod_matricula" => $matricula['codigo'],
                            "motivo" => 1,
                            "comentario" => "Baja por vencimiento de la prematricula",
                            "cod_usuario" => 1
                        )
                    );
                    $this->Model_matriculas->bajaMatriculas($arrDatos, $conexion);
                }
            }
        }
    }
    //$conexion = $this->load->database($filial, true);
    //$resultSet = Vcomisiones::getReportesComisionesActivas($conexion);
    //$resultSet = Vcomisiones::getAllComisionesDatatable($conexion, $arrCondiciones, null, $arrSort, false, $this->codigo_filial, $arrFiltros_nuevos);
    //$arrCondindicioneslike
    //$resultSet = Vcursos::getAllCursosDatatable($conexion, null, null, null, null, $filial);

    //($idFilial, $arrLimit = null, $arrSort = null, $search = null, $searchFields = null)

    ///////
    //$this->load->model("Model_comisiones", "", false, $config);

    //die(var_dump($resultSet1));
    //$nombreComissao = $resultSet1[0]['nombre'];

    //$resultSet2 = array();
    //$comission = new Vcomisiones($conexion, $nombreComissao);
    //$resultSet2 = $comission->getDiasCursadoComision($nombreComissao, false);

    
    public function FTPtoRetorno() {

        $archivos = scandir('/ftp/retorno/');
        $retornos = array();
        $filial = $_GET['filial'];
        foreach($archivos as $retorno){
            if(is_dir($retorno)){
                continue;
            }
            $a = array();
            $a['path'] = '/ftp/retorno/'.$retorno;
            $a['archivo'] = $retorno;
            $retornos[] = $a;
        }
        $config = array("codigo_filial" => $filial);
        $this->load->model("Model_facturantes", "", false, $config);
        foreach($retornos as $retorno) { 
            $ret[] = $this->Model_facturantes->confirmarRetorno($retorno['path'], $retorno['archivo'], $filial, true);
        }
        echo json_encode($ret);
    }

    public function corregirHorarios(){
        $filial = isset($_GET['filial'])?$_GET['filial']:die("Debe dar un numero de filial");
        $conexion = $this->load->database("$filial", true);
        $config = array("codigo_filial" => $filial);
        $this->load->model("Model_asistencias", "", false, $config);
        $alumnos = $this->Model_asistencias->arreglarInscriptosSinHorarios($conexion);
        
    }

    public function getGruposCampus(){
        $filial = isset($_GET['filial'])?$_GET['filial']:die("Debe dar el numero de filial");
        $alumno = isset($_GET['alumno'])?$_GET['alumno']:die("Debe dar el codigo de alumno");
        $conexion = $this->load->database("$filial",true);
        //CUIDADO CON BOBBY TABLES!!!!!!!!!!!!!!!!!!!!!
        $query = $conexion->query(
        "select grupos_campus.id_grupo from matriculas_periodos
        join matriculas on matriculas_periodos.cod_matricula = matriculas.codigo
        join general.grupos_campus on 
        general.grupos_campus.idioma = 'es' 
        and matriculas.cod_plan_academico = grupos_campus.cod_plan_academico
        and grupos_campus.cod_tipo_periodo = matriculas_periodos.cod_tipo_periodo
        join alumnos on matriculas.cod_alumno = alumnos.codigo
        left join matriculas_comentarios on matriculas_comentarios.cod_alumno = matriculas.cod_alumno
        where matriculas_periodos.estado in ('habilitada')
        and alumnos.codigo = $alumno;"        
        );
        echo json_encode($query->result_array());
    }
    
    public function sincronizaTablaFacebookDatos() {
        require_once APPPATH.'libraries/facebook-php-ads-sdk/vendor/autoload.php';
        $filial = $this->session->userdata('filial');
        $arrConf = array('codigo_filial' => $filial["codigo"]);  
        $this->load->model('Model_facebook_ads_datos', "", false, $arrConf);
        $this->Model_facebook_ads_datos->sincroniza_tabla();
    }
    
    public function AlumnosInativosDeudasPasivas() {
        $this->load->model('Model_filiales', "", false, '');
        if(isset($_GET['filial'])) {
            $filiales = array(array('codigo' => $_GET['filial']));
        }
        else {
            $filiales = $this->Model_filiales->getFilialesActivas();
        }
        $this->load->helper('database');
        $conexionGeneral = $this->load->database('general', true);
        $count = 0;
        
        foreach ($filiales as $filial) {
            if(databaseExists($conexionGeneral, $filial['codigo']) && $filial['codigo'] != 90) {
                echo 'filial: '.$filial['codigo'] . '<br>';
                $conexion = $this->load->database($filial['codigo'], true);
                $matriculas = Vmatriculas::buscaMatriculasPorEstado($conexion, Vmatriculas::getEstadoInhabilitada());
                foreach ($matriculas as $matricula) {
                    $ctacteMat = Vmatriculas::getCtacteCorrecionDeudasPasivas($conexion, $matricula['codigo'], $matricula['cod_alumno']);
                    $morasMat = Vmatriculas::getMoras($conexion, $matricula['codigo'], array('1'));//buscar moras de esta matricula
                    $deudas = array_merge($ctacteMat, $morasMat);
                    $count = $count + count($deudas);
                    if(!empty($deudas)) {
                        foreach ($deudas as $deuda) {
                            $cc = new Vctacte($conexion, $deuda['codigo']);
                            $cc->setPasiva(false, null, 'Alumno inhabilitado!');
                        }
                    }
//                    if(!empty($deudas)) {
//                        echo "<pre>";
//                        print_r($deudas);
//                    }
                }
            }
        }
        echo '<br>Deudas Cambiadas: '.$count;
    }

    public function buscaConsultasWebFacebook() {
        $conexion = $this->load->database("general", true);
        $conexion_mails_consultas = $this->load->database("mails_consultas", true);

        $ultimaConsulta = Vmails_consultas::ultimaFechaConsultaFacebook($conexion_mails_consultas);

        require_once APPPATH.'libraries/facebook-php-ads-sdk/vendor/autoload.php';
        $faceApi = new facebook_api();
        $datos = $faceApi->getFacebookLeadsByDate($ultimaConsulta['fecha']);

        foreach ($datos as $dato) {
            $filial = new Vfiliales($conexion, $dato['id_filial']);
            $idioma = $filial->idioma;
            if($idioma == 'in') {
                $idioma = 'en';
            }
            $this->lang->load($idioma, $idioma);

            $id_consulta_facebook = $dato['id'];
            if(!Vmails_consultas::contarMailsConsultasIdFacebookLead($conexion_mails_consultas, $id_consulta_facebook)) {
                $curso = $dato['id_curso'];
                $nombre = $dato['nombre'];
                $telefono = isset($dato['telefono'])&&!empty($dato['telefono'])?$dato['telefono']:'00 0000 0000';//undefined index
                $email = $dato['email'];
                $asunto = null;
                $consulta = lang('consulta_creada_por_publicidad_en_facebook').'<br>'.lang('cursos').':<br>';
                foreach ($dato['cursos'] as $key => $curso_id) {
                    $cursoObj = new Vcursos($conexion, (int)$curso_id);
                    $consulta .= $cursoObj->getNombreIdioma($idioma).'<br>';
                    if(count($dato['cursos']) == 1 && $key == 0) {
                        $asunto = $cursoObj->getNombreIdioma($idioma);
                    }
                }
                $asunto = ($asunto == null ? lang('cursos_en_la_descripcion_de_la_mensaje') : $asunto);
                $arrConf = array('codigo_filial' => (string)$dato['id_filial']);
                $this->load->model('Model_consultasweb', "", false, $arrConf);
                $this->Model_consultasweb->codigo_filial = (string)$dato['id_filial'];
                $this->Model_consultasweb->guardarNuevaConsulta($curso, $nombre, $telefono, $email, $consulta, $asunto, $id_consulta_facebook, 69);//como nos conoció = facebook
            }
        }
    }
}



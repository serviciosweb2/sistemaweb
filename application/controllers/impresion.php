<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Impresion extends CI_Controller {

    public function __construct() {
        parent::__construct();
    }

    /**
     * retorna el metodo de impresion(cloud, navegador) y el id de impresora configurado para un determinado script de impresion
     * 
     * @param int $idScriptImpresion
     */
    function get_metodo_imprimir($idScriptImpresion) {
        session_method();
        $filial = $this->session->userdata('filial');
        $codFilial = $filial["codigo"];
        $arrConf = array('codigo_filial' => $codFilial);
        $this->load->model("Model_impresiones", "", false, $arrConf);
        $arrResp = $this->Model_impresiones->getMetodoImprimirScript($codFilial, $idScriptImpresion);
        echo json_encode($arrResp);
    }

    /**
     * Muestra el cuadro de dialogo ¿imprimir? para seleccionar si debe imprimirse, en que impresoras y cuantas copias deben imprimirse
     */
    function preguntar_imprimir() {
        session_method();
        $filial = $this->session->userdata('filial');
        $codFilial = $filial["codigo"];
        $arrConf = array('codigo_filial' => $codFilial);
        $this->load->model("Model_impresiones", "", false, $arrConf);
        $this->load->model("Model_configuraciones", "", false, $arrConf);
        $this->lang->load(get_idioma(), get_idioma());
        $idScriptImpresion = $_POST['id_script_impresion'];
        $arrImpresoras = $this->Model_impresiones->getImpresorasUtilizadas($codFilial);
        $printerID = $this->Model_impresiones->getPrinterScript($codFilial, $idScriptImpresion);
        $arrConfiguracion = $this->Model_configuraciones->getValorConfiguracion(20, null, $idScriptImpresion);
        $cantidadCopias = isset($arrConfiguracion['copias']) ? $arrConfiguracion['copias'] : 1;
        $data = array();
        $data['printer_default'] = $printerID;
        $data['arrImpresoras'] = $arrImpresoras;
        $data['param'] = $this->input->post("parametros");
        $data['id_script_inicio'] = $idScriptImpresion;
        $data['cantidad_copias'] = $cantidadCopias;
        
        if ($idScriptImpresion == 5) {
            $this->load->model("Model_filiales", "", false, $codFilial);
            $reglamentos = $this->Model_filiales->getReglamentosFilial('matriculas');
            $data['reglamentos'] = $reglamentos;
        }
        $this->load->view("impresiones/preguntar_imprimir", $data);
    }

    /**
     * imprime por la impresora configurada el detalle de la matricula y/o el reglamento
     * 
     * @param int $idMatricula          
     * @param int $imprimirMatricula    (0 = false, 1 = true)
     * @param int $imprimirReglamento   (0 = false, 1 = true)
     * @param string $printerID
     * @param int $cantidadCopias
     */
    function imprimir_matricula() {
        session_method();
        $filial = $this->session->userdata('filial');
        $arrConf = array('codigo_filial' => $filial["codigo"]);
        $this->load->model("Model_configuraciones", "", false, $arrConf);
        $this->load->model("Model_impresiones", "", false, $arrConf);
        $this->load->helper("filial");
        $this->lang->load(get_idioma(), get_idioma());
        $reimprimir = false;
        $cod_matricula = '';
        $string = $this->input->post("parametros");

        if ($this->is_json($string) || substr_count($string, ",") > 2) 
        {
            if ($this->is_json($string)) {
                $array = json_decode($this->input->post("parametros"), true);
            } else {
                $array = explode(",", $string);
            }
            if (count($array) > 1) {
                $reimprimir = $array[3] == 'reimprimir' ? true : '';
                $cod_matricula = $array[0];
            } else {
                $cod_matricula = $array;
            }
            $idImpresora = null;
            $copias = $this->input->post("copias") ? $this->input->post("copias") : null;
            $imprimirMatricula = isset($_POST['imprimir_matricula']) ? $_POST['imprimir_matricula'] : true;

            if (isset($_POST['imprimir_reglamento'])) {
                if (is_array($_POST['imprimir_reglamento'])) {
                    $imprimirReglamento = $_POST['imprimir_reglamento'];
                } else {
                    if ($this->is_json($_POST['imprimir_reglamento'])) {
                        $imprimirReglamento = json_decode($this->input->post("imprimir_reglamento"), true);
                    } else {
                        $imprimirReglamento = $_POST['imprimir_reglamento'] == 1 || $_POST['imprimir_reglamento'] == '["1"]' ? array(1) : array();
                    }
                }
            } else {
                $imprimirReglamento = isset($array[2]) && $array[2] == 1 ? array(1) : array();
            }
            //$imprimirCtacte FORZADO A CERO PEDIDO TICKET
            $imprimirCtacte = false;//isset($_POST['imprimir_resumen_cuenta']) ? $_POST['imprimir_resumen_cuenta'] : false;
            $imprimirCurso = $this->Model_configuraciones->getValorConfiguracion(null, 'imprimirMatriculaPlanAcademico');
            $imprimirTitulo = $this->Model_configuraciones->getValorConfiguracion(null, 'imprimirMatriculaTitulo');
            $imprimir_observaciones = true;
            if (isset($_POST["imprimir_observaciones"]))
            {
                $imprimir_observaciones = ($_POST["imprimir_observaciones"] == 1) ? true : false;
            }
        }
        else
        {
            $cod_matricula = $this->input->post("parametros");
            $idImpresora = $this->input->post("id_impresora") ? $this->input->post("id_impresora") : null;
            $copias = $this->input->post("copias") ? $this->input->post("copias") : null;
            $imprimirMatricula = $this->input->post("imprimir_matricula");
            $imprimirReglamento = json_decode($this->input->post("imprimir_reglamento"));
            //$imprimirCtacte FORZADO A CERO PEDIDO TICKET
            $imprimirCtacte = false;//$this->input->post("imprimir_resumen_cuenta");
            $imprimirCurso = $this->Model_configuraciones->getValorConfiguracion(null, 'imprimirMatriculaPlanAcademico');
            $imprimirTitulo = $this->Model_configuraciones->getValorConfiguracion(null, 'imprimirMatriculaTitulo');
            //imprimir observaciones
            $imprimir_observaciones = true;
            $imprimir_observaciones = $this->input->post("imprimir_observaciones");
        }
        $imprimirReciboCobroMatricula = $this->input->post("imprimir_recibo_cobro") == 1;
        
        // Por defecto se imprimen siempre las observaciones

        $imprimir_observaciones = true;

        $arrResp = $this->Model_impresiones->imprimirMatriculas($cod_matricula, get_idioma(), $imprimirMatricula, $imprimirReglamento, $idImpresora,
                $copias, $reimprimir, $imprimirCtacte, $imprimirCurso, $imprimirTitulo, $imprimir_observaciones, $imprimirReciboCobroMatricula);
        echo json_encode($arrResp);
    }

    /**
     * imprime el formulario de baja de la matricula
     * 
     * @param int $idMatriculaEstadoHistorico el id de matriculas_estado_historicos
     * @param string $printerID printerID de la impresora
     * @param int $cantidadCopias
     */
    function imprimir_formulario_baja() {
        session_method();
        $filial = $this->session->userdata('filial');
        $this->lang->load(get_idioma(), get_idioma());
        $arrConf = array('codigo_filial' => $filial["codigo"]);
        $this->load->model("Model_configuraciones", "", false, $arrConf);
        $this->load->model("Model_impresiones", "", false, $arrConf);
        $this->load->helper("filial");
        $idMatriculaEstadoHistorico = $this->input->post("parametros");
        $idImpresora = $this->input->post("id_impresora") ? $this->input->post("id_impresora") : null;
        $copias = $this->input->post("copias") ? $this->input->post("copias") : null;
        $arrResp = $this->Model_impresiones->imprimirBajaMatricula($idMatriculaEstadoHistorico, $idImpresora, $copias);
        echo json_encode($arrResp);
    }

    /**
     * Imprime el detalle de un presupuesto
     * 
     * @param int $codPresupuesto
     * @param string $printerID
     * @param int $cantidadCopias
     */
    public function imprimir_presupuesto() {
        session_method();
        $filial = $this->session->userdata('filial');
        $arrConf = array('codigo_filial' => $filial["codigo"]);
        $this->load->model("Model_configuraciones", "", false, $arrConf);
        $this->load->model("Model_impresiones", "", false, $arrConf);
        $this->lang->load(get_idioma(), get_idioma());
        $this->load->helper("filial");
        $codPresupuesto = $this->input->post("parametros");
        $idImpresora = $this->input->post("id_impresora") ? $this->input->post("id_impresora") : null;
        $copias = $this->input->post("copias") ? $this->input->post("copias") : null;
        $arrResp = $this->Model_impresiones->imprimir_presupuestos($codPresupuesto, $idImpresora, $copias);
        echo json_encode($arrResp);
    }

    /**
     * Imprime Detalle del resumen de cuenta de un alumno
     * 
     * @param int $codAlumno
     * @param string $printerID
     * @param int $cantidadCopias
     */
    public function resumen_ctacte_alumno() {
        session_method();
        $this->lang->load(get_idioma(), get_idioma());
        $filial = $this->session->userdata('filial');
        $arrConf = array('codigo_filial' => $filial["codigo"]);
        $this->load->model("Model_configuraciones", "", false, $arrConf);
        $this->load->model("Model_impresiones", "", false, $arrConf);
        $this->load->helper("filial");
        $param = explode("|", $this->input->post("parametros"));
        $codAlumno = $param[0];
        $consaldo = isset($param[1]) && $param[1] == 1 ? true : false;
        $idImpresora = $this->input->post("id_impresora") ? $this->input->post("id_impresora") : null;
        $copias = $this->input->post("copias") ? $this->input->post("copias") : null;
        $arrResp = $this->Model_impresiones->resumen_ctacte_alumno($codAlumno, $idImpresora, $copias, $consaldo);
        echo json_encode($arrResp);
    }

    /**
     * Imprime el estado academico para una matricula
     * 
     * @param int $codMatricula
     * @param string $printerID
     * @param int $cantidadCopias
     */
    public function estado_academico() {
        session_method();
        $this->lang->load(get_idioma(), get_idioma());
        $filial = $this->session->userdata('filial');
        $arr = array("filial" => $filial);
        $arrConf = array('codigo_filial' => $filial["codigo"]);
        $this->load->model("Model_impresiones", "", false, $arrConf);
        $this->load->model("Model_matriculas", "", false, $arr);
        $this->load->helper("alumnos");
        $this->load->model("Model_configuraciones", "", false, $arrConf);
        $this->load->model("Model_estadoacademico", "", false, $arr);
        $this->load->model("Model_alumnos", "", false, $arrConf);
        $this->load->model("Model_planes_academicos", "", false, $arrConf);
        $arrayCodigos = json_decode($this->input->post("parametros"), true);
        $idImpresora = $this->input->post("id_impresora") ? $this->input->post("id_impresora") : null;
        $copias = $this->input->post("copias") ? $this->input->post("copias") : null;
        $arrResp = $this->Model_impresiones->estado_academico($arrayCodigos, $idImpresora, $copias);
        echo json_encode($arrResp);
    }

    /**
     * imprime la lista de los inscriptos a un examen en particular
     * 
     * @param int $codExamen
     * @param string $printerID
     * @param int $cantidadCopias
     */
    public function inscriptos_a_examenes() {
        session_method();
        $this->lang->load(get_idioma(), get_idioma());
        $filial = $this->session->userdata('filial');
        $arrConf = array('codigo_filial' => $filial["codigo"]);
        $this->load->model("Model_configuraciones", "", false, $arrConf);
        $this->load->model("Model_impresiones", "", false, $arrConf);
        $this->load->model("Model_examenes", "", false, $arrConf);
        $codExamen = $this->input->post("parametros");
        $idImpresora = $this->input->post("id_impresora") ? $this->input->post("id_impresora") : null;
        $copias = $this->input->post("copias") ? $this->input->post("copias") : null;
        $arrResp = $this->Model_impresiones->inscriptos_a_examenes($codExamen, $idImpresora, $copias);
        echo json_encode($arrResp);
    }

    /**
     * Imprime las constancias de examenes para el/los codigos de inscripcion solicitados(separados por el caracter "-" ) (inscripcion es por matricula y examen)
     * 
     * @param int $codigosInscripcion
     * @param string $printerID
     * @param int $cantidadCopias
     */
    public function constancia_examen() {
        session_method();
        $this->lang->load(get_idioma(), get_idioma());
        $filial = $this->session->userdata('filial');
        $arrConf = array('codigo_filial' => $filial["codigo"]);
        $this->load->model("Model_configuraciones", "", false, $arrConf);
        $this->load->model("Model_impresiones", "", false, $arrConf);
        $this->load->model("Model_examenes", "", false, $arrConf);
        $arrCodigos = explode("-", $this->input->post("parametros"));
        $idImpresora = $this->input->post("id_impresora") ? $this->input->post("id_impresora") : null;
        $copias = $this->input->post("copias") ? $this->input->post("copias") : null;
        $arrResp = $this->Model_impresiones->constancia_examen($arrCodigos, $idImpresora, $copias);
        echo json_encode($arrResp);
    }

    /**
     * Imprime Acta Volante para el codigo de examen solicitado
     * 
     * @param int $codExamen
     * @param string $printerID
     * @param int $cantidadCopias
     */
    function acta_volante() {
        session_method();
        $this->lang->load(get_idioma(), get_idioma());
        $filial = $this->session->userdata('filial');
        $arrConf = array('codigo_filial' => $filial["codigo"]);
        $this->load->model("Model_configuraciones", "", false, $arrConf);
        $this->load->model("Model_impresiones", "", false, $arrConf);
        $this->load->model("Model_examenes", "", false, $arrConf);
        $notaAprueba = $this->Model_configuraciones->getValorConfiguracion(null, 'NotaAprueba');
        $codExamen = $this->input->post("parametros");
        $idImpresora = $this->input->post("id_impresora") ? $this->input->post("id_impresora") : null;
        $copias = $this->input->post("copias") ? $this->input->post("copias") : null;
        $impresion_parciales = $this->input->post("imprimir_notas_parciales") ? true : false;
        $impresion_estado_deuda = $this->input->post("imprimir_estado_deuda") ? true : false;
        $arrResp = $this->Model_impresiones->acta_volante($codExamen, $idImpresora, $copias, $notaAprueba, $impresion_parciales, $impresion_estado_deuda);
        echo json_encode($arrResp);
    }

    /**
     * Imprime la planilla de asistencias para un curso, comision, materia
     * 
     * @param int $curso
     * @param int $comision
     * @param int $materia
     * @param date $fecha               Este parametro se encuentra actualmente en desuso y se reserva para usos futuros
     * @param string $printerID
     * @param int $cantidadCopias
     */
    function asistencias() {
        session_method();
        $this->lang->load(get_idioma(), get_idioma());
        $filial = $this->session->userdata('filial');
        $arrConf = array('codigo_filial' => $filial["codigo"]);
        $this->load->model("Model_configuraciones", "", false, $arrConf);
        $this->load->model("Model_impresiones", "", false, $arrConf);
        $this->load->model("Model_horarios", "", false, $arrConf);
        $arrTemp = explode("/", $this->input->post("parametros"));
        $vista = isset($arrTemp[4]) && $arrTemp[4] == "vacia" ? true : false;
        $horizontalmente = isset($arrTemp[5]) && $arrTemp[5] == "horizontalmente" ? true : false;
        $periodo = isset($arrTemp[6]) && $arrTemp[6] != "nada" ? $arrTemp[6] : false;
        $cod_horario = isset($arrTemp[7]) ? explode("," , $arrTemp[7]) : false;
        //var_dump($cod_horario);die();
        $curso = $arrTemp[0];
        $comision = $arrTemp[1];
        $materia = $arrTemp[2];
        $fecha = $arrTemp[3];
        $fechaDesde = '';
        $fechaHasta = '';        
        if($periodo){
            $fechaDesde = strtotime ( "-$periodo" , strtotime ( $fecha ) ) ;
            $fechaDesde = date ( 'Y-m-j' , $fechaDesde );
            $fechaHasta = strtotime ( "+$periodo" , strtotime ( $fecha ) ) ;
            $fechaHasta = date ( 'Y-m-j' , $fechaHasta );
        } else {
            $fechaDesde = isset($_POST['fecha_desde']) && $_POST['fecha_desde'] <> '' ? formatearFecha_mysql($_POST['fecha_desde']) : '';
            $fechaHasta = isset($_POST['fecha_hasta']) && $_POST['fecha_hasta'] <> '' ? formatearFecha_mysql($_POST['fecha_hasta']) : '';
        }
        $printerID = $this->input->post("id_impresora") ? $this->input->post("id_impresora") : null;
        $copias = $this->input->post("copias") ? $this->input->post("copias") : null;
        $arrResp = $this->Model_impresiones->imprimir_asistencias($curso, $comision, $fecha, $materia, $fechaDesde, $fechaHasta, $printerID, $copias, $vista, $horizontalmente, $cod_horario, true);
        echo json_encode($arrResp);
    }

    /**
     * Imprime los detalles de un pago recibido
     * 
     * @param int $codRecibo
     * @param string $printerID
     * @param int $cantidadCopias
     */
    function recibo_cobros() {
        session_method();
        $this->lang->load(get_idioma(), get_idioma());
        $filial = $this->session->userdata('filial');
        $arrConf = array('codigo_filial' => $filial["codigo"]);
        $this->load->model("Model_configuraciones", "", false, $arrConf);
        $this->load->model("Model_impresiones", "", false, $arrConf);
        $this->load->helper("filial");
        $this->load->helper("cuentacorriente");
        $codRecibo = $this->input->post("parametros");
        $idImpresora = $this->input->post("id_impresora") ? $this->input->post("id_impresora") : null;
        $copias = $this->input->post("copias") ? $this->input->post("copias") : null;
        $arrResp = $this->Model_impresiones->recibo_cobros(array($codRecibo), $idImpresora, $copias);
        echo json_encode($arrResp);
    }

    function facturacion() {
        session_method();
        $this->lang->load(get_idioma(), get_idioma());
        $filial = $this->session->userdata('filial');
        $arrConf = array('codigo_filial' => $filial["codigo"]);
        $this->load->model("Model_impresiones", "", false, $arrConf);
        $this->load->model("Model_configuraciones", "", false, $arrConf);
        $this->load->helper("cuentacorriente");
        $arrCodigoFactura = explode("-", $this->input->post("parametros"));
        $idImpresora = $this->input->post("id_impresora") ? $this->input->post("id_impresora") : null;
        $copias = $this->input->post("copias") ? $this->input->post("copias") : null;
        $arrResp = $this->Model_impresiones->facturacion($arrCodigoFactura, $idImpresora, $copias);
        echo json_encode($arrResp);
    }

    function is_json($string) {
        try {
            json_decode($string);
        } catch (ErrorException $e) {
            return FALSE;
        }
        return (json_last_error() == JSON_ERROR_NONE);
    }

    function imprimir_reporte_facturacion() {
        session_method();
        $cod_usuario = $this->session->userdata('codigo_usuario');
        $this->lang->load(get_idioma(), get_idioma());
        $filial = $this->session->userdata('filial');
        $arrConf = array('codigo_filial' => $filial["codigo"]);
        $this->load->model("Model_reportes", "", false, $filial["codigo"]);
        $this->load->model("Model_impresiones", "", false, $arrConf);
        $this->load->model("Model_configuraciones", "", false, $arrConf);
        $parametros = json_decode($this->input->post("parametros"), true);
        $filtros = $parametros['filters'] == '' ? '' : $parametros['filters'];
        $id_impresora = $this->input->post("id_impresora") ? $this->input->post("id_impresora") : null;
        $copias = $this->input->post("copias") ? $this->input->post("copias") : null;
        $currentPage = $parametros['iCurrentPage'] == '' ? null : $parametros['iCurrentPage'];
        $pageDisplay = $parametros['iPaginationLength'] == '' ? null : $parametros['iPaginationLength'];
        $sSearch = $parametros['sSearch'] == '' ? '' : $parametros['sSearch'];
        $arrColumsVisibles = $parametros['iFieldView'];
        $nombreReporte = $parametros['report_name'];
        $sortDir = $parametros['iSortDir'] == '' ? '' : $parametros['iSortDir'];
        $sortName = $parametros['iSortCol'] == '' ? '' : $parametros['iSortCol'];
        $aplyCommonFilters = $parametros['apply_common_filters'];
        $arrResp = $this->Model_impresiones->imprimir_reporte_factura($cod_usuario, $filtros, $id_impresora, $copias, $currentPage, $pageDisplay, $sSearch, $arrColumsVisibles, $nombreReporte, $sortDir, $sortName, $aplyCommonFilters);
//        echo json_encode($arrResp);
    }

    function imprimir_remessa_boleto_bancario() {
        $codRemesa = $this->input->post('parametros');
        $idImpresora = $this->input->post("id_impresora");
        $copias = $this->input->post("copias");
        $filial = $this->session->userdata('filial');
        $arrConf = array('codigo_filial' => $filial["codigo"]);
        $this->load->model("Model_impresiones", "", false, $arrConf);
        $this->Model_impresiones->imprimir_remessa_boleto_bancario($codRemesa, $idImpresora, $copias);
    }

    function imprimir_boleto_bancario(){
        $codBoleto = $this->input->post('parametros');
        $idImpresora = $this->input->post("id_impresora");
        $copias = $this->input->post("copias");
        $filial = $this->session->userdata('filial');
        $arrConf = array('codigo_filial' => $filial["codigo"]);
        $this->load->model("Model_impresiones", "", false, $arrConf);
        $arrResp = $this->Model_impresiones->imprimir_boleto_bancario($codBoleto, $idImpresora, $copias);
        echo json_encode($arrResp);
    }

    function imprimir_inscriptos_seminario(){
        $idSeminario = $_POST['parametros'] == -1 ? null : $_POST['parametros'];
        $idImpresora = $_POST['id_impresora'];
        $copias = $_POST['copias'];
        $this->load->model("Model_seminarios", "", false, array());
        $filial = $this->session->userdata('filial');
        $arrConf = array('codigo_filial' => $filial["codigo"]);
        $this->load->model("Model_impresiones", "", false, $arrConf);
        $arrResp = $this->Model_impresiones->imprimir_inscriptos_seminarios($idSeminario, $idImpresora, $copias);
        echo json_encode($arrResp);
    }


    function imprimir_boletos_bancarios(){
        $boletos = $_POST['parametros'] == -1 ? null : json_decode($_POST['parametros']);
        $idImpresora = $_POST['id_impresora'];
        $filial = $this->session->userdata('filial');
        $arrConf = array('codigo_filial' => $filial["codigo"]);
        $this->load->model("Model_impresiones", "", false, $arrConf);
        $arrResp = $this->Model_impresiones->imprimir_boletos_bancarios($boletos, $idImpresora);
        echo json_encode($arrResp);
    }
}

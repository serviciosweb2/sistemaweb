<?php
#Comentario para commitear de nuevo porque puedo.
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Model_impresiones extends CI_Model {

    private $codigofilial;
    private $arg;

    /* CONSTRUCTOR */

    public function __construct($arg = null) {
        parent::__construct();

        $this->arg = $arg;
        if ($arg != null) {
            $this->codigofilial = $arg["codigo_filial"];
        }
    }

    /* STATIC FUNCTION */

    /**
     * Agrega el pie de pagina al documento pdf actual
     *
     * @param PDF_AutoPrint $pdf
     * @param CI_DB_mysqli_driver $conexion
     */
    private function agregarPiePagina(PDF_AutoPrint &$pdf, CI_DB_mysqli_driver $conexion) {
        $agregaPie = $this->Model_configuraciones->getValorConfiguracion(null, 'imprimir_pie_pagina_hoja_membretada');
        if ($agregaPie == 1) {
            $filialDomicilio = $this->session->userdata['filial']['domicilio'];
            $filialCodigoPostal = $this->session->userdata['filial']['codigo_postal'];
            $myFilialLocalidad = new Vlocalidades($conexion, $this->session->userdata['filial']['localidad']);
            $filialLocalidad = ucwords(strtolower($myFilialLocalidad->nombre));
            $myFilialProvincia = new Vprovincias($conexion, $myFilialLocalidad->provincia_id);
            $filialProvincia = ucwords(strtolower($myFilialProvincia->nombre));
            $filialTelefono = $this->session->userdata['filial']['telefono'];
            $filialEmail = $this->session->userdata['filial']['email'];
            $pdf->SetFont("arial", '', 10);
            $pdf->SetY(-12);
            $pdf->Cell(0, 4, utf8_decode("{$filialDomicilio} | {$filialCodigoPostal} {$filialLocalidad}/{$filialProvincia}"), 0, 0, "R");
            $pdf->Ln();
            $pdf->Cell(0, 4, "Tel: {$filialTelefono} | {$filialEmail}", 0, 0, "R");
        }
    }

    /**
     * Setea margenes de papel membretado para el documento actual
     *
     * @param PDF_AutoPrint $pdf
     */
    private function setPapelMembretado(PDF_AutoPrint &$pdf) {
        $pdf->SetAutoPageBreak(true, 3);
        $pdf->SetMargins(20, 37, 5);
    }

    /**
     * Retorna la cantidad de copias configuradas para un script de impresion en particular o
     * valida que la cantidad seleccionada en la ventana de confirmacion de impresion sea la correcta
     *
     * @param int $scriptID
     * @param int $cantidadCopiasSugerido
     * @return int
     */
    private function getCantidadCopias($scriptID, $cantidadCopiasSugerido = null) {
        if ($cantidadCopiasSugerido == null || !is_numeric($cantidadCopiasSugerido) || $cantidadCopiasSugerido < 1 || $cantidadCopiasSugerido > 5) {
            $filial = $this->session->userdata('filial');
            $arrConfig = array(
                "codigo_filial" => $filial["codigo"]
            );
            $this->load->model("Model_configuraciones", "", false, $arrConfig);
            $arrConfiguracion = $this->Model_configuraciones->getValorConfiguracion(20, null, $scriptID);
            $resp = isset($arrConfiguracion['copias']) ? $arrConfiguracion['copias'] : 1;
        } else {
            $resp = $cantidadCopiasSugerido;
        }
        return $resp;
    }

    /* PUBLIC FUNCTION */

    /**
     * Elimina los archivos temporales de impresion utilizados por google cloud print (verificando primeramente que el documento ya se ha imprimido)
     * Esta funcion es llamada actualmente desde un crons
     */
    public function eliminar_archivos_impresion() {
        $conexion = $this->load->database("default", true);
        $arrGoogleAccount = cuentas_google::listar($conexion);
        foreach ($arrGoogleAccount as $googleAccount) {
            $idFilial = $googleAccount['id_filial'];
            $myImpresion = new impresiones($conexion, $idFilial);
            $arrImpresoras = impresoras_cloud_print::listarImpresoras($conexion, $idFilial);
            foreach ($arrImpresoras as $impresora) {
                $printer_id = $impresora['printer_id'];
                if ($printer_id <> '__google__docs') {
                    $myImpresora = new impresoras_cloud_print($conexion, 1, $printer_id);
                    $arrJobs = $myImpresora->getJobs();
                    foreach ($arrJobs as $job) {
                        if ($job['status'] == "DONE" || $job['status'] == "ABORTED") {
                            $file = $myImpresion->getFilesDir() . $job['title'];
                            $id = $job['id'];
                            if (file_exists($file)) {
                                if (unlink($file)) {
                                    echo "se ha eliminado el archivo $file<br>";
                                    if (!$myImpresora->cancelJobs($id)) {
                                        echo "Error al cancelar el trabajo de impresion $id con el mensaje<br>" . $myImpresion->getError();
                                    }
                                } else {
                                    echo "Error al borrar el archivo $file<br>";
                                }
                            } else {
                                if ($_SERVER['HTTP_HOST'] == "localhost") {
                                    echo "no se encuentra el archivo $file<br>";
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    /**
     * Retorna el printer_id de la impresora utilizada por la filial en el script de impresion dado
     *
     * @param int $idFilial
     * @param int $idScript
     * @return string
     */
    function getPrinterScript($idFilial, $idScript) {
        $conexion = $this->load->database("default", true);
        $arrPrinter = impresiones::listarImpresorasScript($conexion, $idFilial, $idScript);
        if (isset($arrPrinter[0]['printer_id']))
            return $arrPrinter[0]['printer_id'];
        else
            return '';
    }

    /**
     * Retorna las impresoras cloud print utilizadas en los scripts de impresion
     *
     * @param int $idFilial
     * @return array
     */
    function getImpresorasUtilizadas($idFilial) {
        $conexion = $this->load->database("default", true);
        return impresoras_cloud_print::getImpresorasUtilizadas($conexion, $idFilial);
    }

    /**
     * Retorna las impresoras registradas en filiales_impresoras (impresoras cloud print)
     *
     * @param int $idFilial
     * @param boolean
     * @return array
     */
    function getImpresorasRegistradasEnLaCuentaGCP($idFilial, $default = false) {
        $conexion = $this->load->database("default", true);
        return impresoras_cloud_print::listarImpresoras($conexion, $idFilial, $default);
    }

    /**
     * Lista las impresoras asociadas a una cuenta de google cloud print
     *
     * @param int $idGoogleAccount
     * @return array
     */
    function getPrintersList($idGoogleAccount) {

      //  require_once APPPATH.'libraries/impresion/cloudprint/GoogleCloudPrint.php';
        session_start();
        // Create object
        $gcp = new GoogleCloudPrint2();
        $token = $this->session->userdata('accessToken');
        $gcp->setAuthToken($token);


        return $gcp->getPrinters();

        /*$conexion = $this->load->database("default", true);
        $myGoogleAccount = new cuentas_google($conexion, $idGoogleAccount);
        if (!$myGoogleAccount->accountExists()) {
            return array("error" => "Cuenta inexistente");
        } else {
            $arrResp = impresoras_cloud_print::getGoogleCloudPrinters($myGoogleAccount);
            if ($arrResp === false) {
                return array("error" => "La cuenta de Google no es correcta o no posee permiso para utilizar Google Cloud Print");
            } else if (count($arrResp) > 0) {
                $arrPrintersReg = impresoras_cloud_print::listarImpresoras($conexion, $idGoogleAccount, 1);
                if (count($arrPrintersReg) > 0) {
                    $idDefault = $arrPrintersReg[0]['printer_id'];
                    for ($i = 0; $i < count($arrResp); $i++) {
                        if ($arrResp[$i]['id'] == $idDefault) {
                            $arrResp[$i]['default'] = 1;
                        }
                    }
                }
            }
            return $arrResp;
        }*/
    }

    /* guarda un impresora cloud print en la base de datos */

    function guardarPrinterCloud($idFilial, $printerID, $nombre, $display, $proxy, $default = 0) {
        $conexion = $this->load->database("default", true);
        $conexion->trans_begin();
        $myPrinter = new impresoras_cloud_print($conexion, $idFilial, $printerID);
        $myPrinter->nombre = $nombre;
        $myPrinter->display = $display;
        $myPrinter->proxy = $proxy;
        $myPrinter->guardar($conexion);
        if ($default == 1) {
            $myPrinter->setDefault($conexion);
        }
        if ($conexion->trans_status() === false) {
            $conexion->trans_rollback();
            return false;
        } else {
            $conexion->trans_commit();
            return true;
        }
    }

    /**
     * Retorna los script de impresion actuales
     *
     * @param int $codFilial
     * @return array
     */
    function getScriptsImpresiones($codFilial = null) {
        $conexion = $this->load->database("default", true);
        return Vscript_impresiones::listarScript_impresiones_filiales($conexion, $codFilial);
    }

    /**
     * asigna una impresora para ser utilizada en un script de impresion determinado
     *
     * @param int $idFilial
     * @param array $arrayScriptPrinterData un array en formato idScript => printerData
     * @return boolean
     */
    function setPrinterScript($idFilial, $arrayScriptPrinterData) {
        $conexion = $this->load->database("default", true);
        $conexion->trans_begin();
        impresoras_cloud_print::unsetDefault($conexion, $idFilial);
        $myImpresion = new impresiones($conexion, $idFilial);
        foreach ($arrayScriptPrinterData as $idScript => $printerData) {
            if ($printerData['printer_id'] <> -1) {
                $myImpresora = new impresoras_cloud_print($conexion, $idFilial, $printerData['printer_id']);
                $myImpresora->display = $printerData['display'];
                $myImpresora->nombre = $printerData['name'];
                $myImpresora->proxy = $printerData['proxy'];
                $myImpresora->guardar($conexion);
            }
            $myImpresion->setPrinterScript($conexion, $printerData['printer_id'], $idScript, $printerData['metodo']);
        }
        if ($conexion->trans_status() === false) {
            $conexion->trans_rollback();
            return false;
        } else {
            $conexion->trans_commit();
            return true;
        }
    }

    /**
     * Lista las impresoras asociadas a los script de impresion para una filial dada
     *
     * @param int $idFilial
     * @return array
     */
    function listarImpresorasScripts($idFilial) {
        $conexion = $this->load->database("default", true);
        return impresiones::listarImpresorasScript($conexion, $idFilial);
    }

    /**
     * Retorna un array indicando el metodo de impresion y la impresora configurado ([preguntar, imprimir, no_imprimir] y [Cloud, navegador])
     *
     * @param integer $idFilial El id de la filial
     * @param integer $idScript El id de script de impresion (ver tabla general.script_impresiones)
     * @return array            En formato metodo(preguntar, imprimir, no_imprimir), impresora(Cloud, Navegador)
     */
    function getMetodoImprimirScript($idFilial, $idScript) {
        $arrResp = array();
        $conexion = $this->load->database("default", true);
        $arrMetodo = impresiones::listarImpresorasScript($conexion, $idFilial, $idScript);
        $arrResp['metodo'] = isset($arrMetodo[0]['metodo']) ? $arrMetodo[0]['metodo'] : "preguntar";
        $arrResp['impresora'] = isset($arrMetodo[0]['printer_id']) && $arrMetodo[0]['printer_id'] != -1 ? "Cloud" : "Navegador";
        return $arrResp;
    }

    /**
     * retorna el codigo de matricula en el formato de impresion (ejemplo: 0001-00000001);
     *
     * @param integer $idFilial
     * @param integer $idMatricula
     * @return string
     */
    function modelarNumeroMatricula($idFilial, $idMatricula) {
        return str_pad($idFilial, 4, "0", STR_PAD_LEFT) . "-" . str_pad($idMatricula, 8, "0", STR_PAD_LEFT);
    }

    /**
     * Impresion de baja de matricula
     *
     * @param integer $codigoMatriculaHistorico
     * @param string $printerID
     * @param integger $cantidadCopias
     * @return array
     */
    function imprimirBajaMatricula($codigoMatriculaHistorico, $printerID = null, $cantidadCopias = null) {
        $cantidadCopias = $this->getCantidadCopias(2, $cantidadCopias);
        $conexion = $this->load->database($this->codigofilial, true);
        $this->load->helper('alumnos');
        $myMatriculaHistorico = new Vmatriculas_estado_historicos($conexion, $codigoMatriculaHistorico);
        $myMatriculaPeriodo = new Vmatriculas_periodos($conexion, $myMatriculaHistorico->cod_matricula_periodo);
        $myMatricula = new Vmatriculas($conexion, $myMatriculaPeriodo->cod_matricula);
        $nombrePeriodo = Vmatriculas::getNombrePeriodoModalidadCurso($conexion, $myMatricula->cod_plan_academico, $myMatriculaPeriodo->cod_tipo_periodo, $myMatriculaPeriodo->modalidad, $this->codigofilial);
        $myPlanAcademico = new Vplanes_academicos($conexion, $myMatricula->cod_plan_academico);
        $myCurso = $myPlanAcademico->getCurso();
        $myAlumno = new Valumnos($conexion, $myMatricula->cod_alumno);
        $myLocalidad = new Vlocalidades($conexion, $myAlumno->id_localidad);
        $idioma = get_idioma();
        $arrMotivo = $myMatriculaHistorico->getmotivos(false, true, $myMatriculaHistorico->motivo);
        $motivo = isset($arrMotivo[0]) && isset($arrMotivo[0]['motivo']) ? $arrMotivo[0]['motivo'] : $arrMotivo;
        if ($idioma == "es") {
            $nombreCurso = $myCurso->nombre_es . ' ' . lang($nombrePeriodo[0]['nombre_periodo']) . '[' . lang($nombrePeriodo[0]['modalidad']) . ']';
        } else if ($idioma == "in") {
            $nombreCurso = $myCurso->nombre_in . ' ' . lang($nombrePeriodo[0]['nombre_periodo']) . '[' . lang($nombrePeriodo[0]['modalidad']) . ']';
        } else {
            $nombreCurso = $myCurso->nombre_pt . ' ' . lang($nombrePeriodo[0]['nombre_periodo']) . '[' . lang($nombrePeriodo[0]['modalidad']) . ']';
        }
        $pdf = new PDF_AutoPrint("P", "mm", "A4");
        $pdf->AutoPrint(false);
        for ($cantCopias = 0; $cantCopias < $cantidadCopias; $cantCopias++) {
            $this->setPapelMembretado($pdf);
            $pdf->SetFont('arial', 'B', 12);
            $pdf->AddPage('P', 'A4');
            $pdf->Cell(180, 6, utf8_decode(lang("formulario_de_baja_matricula")), 0, 0, "C");
            $pdf->Ln(8);
            $pdf->SetFont("arial", "", 10);
            $pdf->Cell(180, 6, utf8_decode(lang("fecha_emision")) . " " . formatearFecha_pais(substr($myMatriculaHistorico->fecha_hora, 0, 10)), "", "", "R");
            $pdf->Ln(6);
            $pdf->Cell(180, 6, $this->modelarNumeroMatricula($this->codigofilial, $myMatricula->getCodigo()), 0, 0, "R");
            $pdf->Ln();
            $pdf->SetFont("arial", "B", 10);
            $pdf->Cell(30, 6, "Alumno", "LT");
            $pdf->SetFont("arial", "", 10);
            $apellido = inicialesMayusculas($myAlumno->apellido);
            $nombre = inicialesMayusculas($myAlumno->nombre);
            $pdf->Cell(150, 6, utf8_decode("{$apellido}, {$nombre}"), "TR");
            $pdf->Ln();
            $pdf->SetFont("arial", "B", 10);
            $pdf->Cell(30, 6, utf8_decode(lang("domicilio")), "L");
            $pdf->SetFont("arial", "", 10);
            $pdf->Cell(150, 6, utf8_decode($myAlumno->getDomicilioFormateado()), "R");
            $pdf->Ln();
            $pdf->SetFont("arial", "B", 10);
            $pdf->Cell(30, 6, utf8_decode(lang("telefono")), "L");
            $pdf->SetFont("arial", "", 10);
            $pdf->Cell(150, 6, Vtelefonos::formatearNumero($myAlumno->getTelefonos(true)), "R");
            $pdf->Ln();
            $pdf->SetFont("arial", "B", 10);
            $pdf->Cell(30, 6, utf8_decode(lang("documento")), "L");
            $pdf->SetFont("arial", "", 10);
            $pdf->Cell(150, 6, funciones::formatearDocumentos($conexion, $myAlumno->tipo, $myAlumno->documento), "R");
            $pdf->Ln();
            $pdf->SetFont("arial", "B", 10);
            $pdf->Cell(30, 6, "localidad", "LB");
            $pdf->SetFont("arial", "", 10);
            $pdf->Cell(150, 6, $myLocalidad->nombre, "RB");
            $pdf->Ln(10);
            $pdf->SetFont("arial", "B", 10);
            $pdf->Cell(50, 6, utf8_decode(lang("nombre_del_curso")), "LBT");
            $pdf->SetFont("arial", "", 10);
            $pdf->Cell(130, 6, utf8_decode($nombreCurso), "TRB");
            $pdf->Ln();
            $pdf->Ln(10);
            $pdf->SetFont("arial", "B", 10);
            $pdf->Cell(50, 10, "Motivo", "LTB");
            $pdf->SetFont("arial", "", 10);
            $pdf->Cell(130, 10, utf8_decode(lang($motivo)), "RTB");
            if (trim($myMatriculaHistorico->comentario) <> '') {
                $pdf->Ln(16);
                $pdf->SetFont("arial", "B", 10);
                $pdf->Cell(180, 6, utf8_decode(lang("observaciones")), "LTR");
                $pdf->Ln();
                $pdf->SetFont("arial", "", 10);
                $pdf->Cell(180, 6, utf8_decode(trim($myMatriculaHistorico->comentario)), "RBL");
            }
            $pdf->Ln(45);
            $pdf->cell(10, 3);
            $pdf->Cell(41, 3, '', 'T');
            $pdf->Cell(24);
            $pdf->Cell(41, 3, '', 'T');
            $pdf->Cell(23);
            $pdf->Cell(41, 3, '', 'T');
            $pdf->Ln();
            $pdf->cell(10, 6);
            $pdf->Cell(41, 0, utf8_decode(lang('asesor')), '', 0, 'C');
            $pdf->Cell(24);
            $pdf->Cell(41, 0, utf8_decode(lang('padre') . "/" . lang('madre') . "/" . lang('tutor')), '', 0, 'C');
            $pdf->Cell(23);
            $pdf->Cell(41, 0, utf8_decode(lang('Alumno')), '', 0, 'C');
            $this->agregarPiePagina($pdf, $conexion);
        }
        $conexion = $this->load->database("default", true);
        $arrResp = array();
        $myImpresion = new impresiones($conexion, $this->codigofilial);
        if (!$myImpresion->printerPDF($conexion, 2, $pdf, $printerID)) {
            $arrResp["error"] = "ha ocurrido un error al enviar el trabajo de impresion con el mensaje {$myImpresion->getError()}";
        } else {
            $arrResp['success'] = "success";
        }
        return $arrResp;
    }

    /**
     * imprime presupuestos
     *
     * @param int $codPresupuesto
     * @param string $printerID
     * @param int $cantidadCopias
     * @return string
     */
    function imprimir_presupuestos($codPresupuesto, $printerID = null, $cantidadCopias = null) {
        $cantidadCopias = $this->getCantidadCopias(1, $cantidadCopias);
        $filial = $this->session->userdata('filial');
        $this->load->helper('alumnos');
        $this->load->helper('comisiones');
        $arrConfig = array(
            "codigo_filial" => $filial["codigo"]
        );
        $idioma = get_idioma();
        $this->load->model("Model_planes_financiacion", "", false, $arrConfig);
        $conexion = $this->load->database($this->codigofilial, true);
        $myPresupuesto = new Vpresupuestos($conexion, $codPresupuesto);
        $myAspirante = $myPresupuesto->getAspirante();
        $myComision = new Vcomisiones($conexion, $myPresupuesto->codcomision);
        $nombreComision = $myComision->nombre;
        $myPlanAcademico = new Vplanes_academicos($conexion, $myComision->cod_plan_academico);
        $arrMaterias = $myPlanAcademico->getMaterias();
        $myPlanPago = new Vplanes_pago($conexion, $myPresupuesto->cod_plan);
        $myCurso = new Vcursos($conexion, $myPlanAcademico->cod_curso);
        $periodos = $myPlanPago->getPeriodosCurso($myPlanAcademico->cod_curso);
        $arrPeriodos = array();
        foreach ($periodos as $periodo) {
            $arrPeriodos[] = $periodo['cod_tipo_periodo'];
        }
        $cant_horas = $myPlanAcademico->getCantHorasPlanAcademico($conexion, $myComision->cod_plan_academico, $arrPeriodos);
        $myLocalidad = new Vlocalidades($conexion, $myAspirante->cod_localidad);
        $arrHorarios = $myComision->getHorarios();
        $arrPresupuestoDetalle = $myPresupuesto->getPresupuestoDetalles();
        $detalles = array();
        $nombre = formatearNombreApellido($myAspirante->nombre, $myAspirante->apellido);
        $nombreApellido = inicialesMayusculas($nombre);
        $detalles['cod_plan'] = $myPresupuesto->cod_plan;
        $detalles['moneda'] = $filial['moneda'];
        for ($i = 0; $i < count($arrPresupuestoDetalle); $i++) {
            $detalles['financiaciones'][$i]['cod_financiacion'] = $arrPresupuestoDetalle[$i]['codigo_financiacion'];
            $detalles['financiaciones'][$i]['cod_concepto'] = $arrPresupuestoDetalle[$i]['codigo_concepto'];
            $arrDetalleTemp = $myPlanPago->getPlanFinanciacionDescuento($arrPresupuestoDetalle[$i]['codigo_financiacion'], $arrPresupuestoDetalle[$i]['codigo_concepto']);
            if (isset($arrDetalleTemp[0]) && isset($arrDetalleTemp[0]['cod_plan'])) {
                $arrDetalleDescuentos[$arrPresupuestoDetalle[$i]['codigo_concepto']] = $arrDetalleTemp[0];
            }
        }
        $datosdetalle = $this->Model_planes_financiacion->getDetallesFinanciaciones($detalles);
        if ($idioma == "es") {
            $nombreCurso = $myCurso->nombre_es;
            $idxMateria = "nombre_es";
        } else if ($idioma == "in") {
            $nombreCurso = $myCurso->nombre_in;
            $idxMateria = "nombre_in";
        } else {
            $nombreCurso = $myCurso->nombre_pt;
            $idxMateria = "nombre_pt";
        }
        $myConfiguracion = new Vconfiguracion($conexion, 16);
        $textoPie = trim($myConfiguracion->value);

        $pdf = new PDF_AutoPrint("P", "mm", "A4");
        $pdf->AutoPrint(false);
        for ($cantCopias = 0; $cantCopias < $cantidadCopias; $cantCopias++) {
            $this->setPapelMembretado($pdf);
            $pdf->SetFont('arial', 'B', 10);
            $pdf->AddPage('P', 'A4');
            $pdf->Cell(30, 6, utf8_decode(lang("nombre")), "LT");
            $pdf->SetFont("arial", "", 10);
            $pdf->Cell(100, 6, utf8_decode($nombreApellido), "T");
            $pdf->setFont("arial", "B", 12);
            $pdf->Cell(50, 6, utf8_decode(lang("titulo_presu_as") . ' ' . $myPresupuesto->getCodigo()), "TR");
            $pdf->SetFont("arial", "B", 10);
            $pdf->Ln();
            $pdf->Cell(30, 6, utf8_decode(lang("Tel")), "L");
            $pdf->SetFont("arial", "", 10);
            $pdf->Cell(100, 6, Vtelefonos::formatearNumero($myAspirante->getTelefonos(true)));
            $pdf->Cell(50, 6, utf8_decode(lang("fecha_emision") . ' ' . formatearFecha_pais($myPresupuesto->fecha)), "R");
            $pdf->Ln();
            $pdf->SetFont("arial", "B", 10);
            $pdf->Cell(30, 6, utf8_decode(lang("domicilio")), "L");
            $pdf->SetFont("arial", "", 10);
            $pdf->Cell(100, 6, utf8_decode($myAspirante->getDomicilioFormateado()));
            $pdf->Cell(50, 6, "", "R");
            $pdf->Ln();
            $pdf->SetFont("arial", "B", 10);
            $pdf->Cell(30, 6, utf8_decode(lang("localidad")), "L");
            $pdf->SetFont("arial", "", 10);
            $pdf->Cell(100, 6, utf8_decode($myLocalidad->nombre));
            $pdf->Cell(50, 6, "", "R");
            $pdf->Ln();
            $pdf->SetFont("arial", "B", 10);
            $pdf->Cell(30, 6, utf8_decode(lang("frm_nuevaMatricula_nombreCurso")), "LB");
            $pdf->SetFont("arial", "", 10);
            $pdf->Cell(50, 6, utf8_decode($nombreCurso), "B");
            $pdf->SetFont("arial", "B", 10);
            $pdf->Cell(50, 6, utf8_decode(lang('cant_hs_catedra').' **'), "B", 0, "R");
            $pdf->SetFont("arial", "", 10);
            $pdf->Cell(50, 6, round(($cant_horas[0]['cant_horas'])*1.5), "BR");
            $pdf->Ln();
            $pdf->Ln(4);
            $pdf->SetFont("arial", "B", 10);
            $pdf->Cell(180, 6, utf8_decode(lang("materias")), "TLBR");
            $pdf->SetFont("arial", '', 10);
            $pdf->Ln();
            $materias = array();
            foreach ($arrMaterias as $materia) {
                $materias[] = $materia[$idxMateria];
            }
            for ($i = 0; $i < count($materias); $i += 2) {
                $materiasMostrar = '';
                $materiasMostrar .= isset($materias[$i]) ? $materias[$i] : '';
                $materiasMostrar .= isset($materias[$i + 1]) ? ", {$materias[$i + 1]}" : '';
                $pdf->Cell(180, 6, utf8_decode($materiasMostrar), "LR");
                $pdf->Ln();
            }
            $pdf->Cell(180, 0, "", "T");
            $pdf->Ln(4);
            $pdf->SetFont("arial", "B", 10);
            $pdf->Cell(30, 6, utf8_decode(lang("comision")), "TL");
            $pdf->SetFont("arial", "", 10);
            $pdf->Cell(150, 6, utf8_decode($nombreComision), "TR");
            if (count($arrHorarios) > 0) {
                $pdf->Ln();
                $pdf->SetFont("arial", "B", 10);
                $pdf->Cell(180, 6, utf8_decode(lang("horario")), "TLR");
                $pdf->Ln();
                $pdf->SetFont("arial", "", 10);
                foreach ($arrHorarios as $horario) {
                    $descripcionHorario[] = formatearFecha_descripciondia($horario['DIA_SEMANA']) . " de " . substr($horario['horadesde'], 0, 5) .
                            " a " . substr($horario['horahasta'], 0, 5);
                }
                for ($x = 0; $x <= count($descripcionHorario); $x+=3) {
                    $horariosMostrar = "";
                    $horariosMostrar .= isset($descripcionHorario[$x]) ? $descripcionHorario[$x] : '';
                    $horariosMostrar .= isset($descripcionHorario[$x + 1]) ? ", {$descripcionHorario[$x + 1]}" : '';
                    $horariosMostrar .= isset($descripcionHorario[$x + 2]) ? ", {$descripcionHorario[$x + 2]}" : '';
                    $pdf->Cell(180, 6, utf8_decode($horariosMostrar), "LR");
                    $pdf->Ln();
                }
            } else {
                $pdf->Ln();
            }
            $pdf->Cell(180, 0, "", "T");
            $pdf->Ln();
            $pdf->SetFont("arial", "B", 10);
            $pdf->Cell(30, 6, utf8_decode(lang("frm_nuevaMatricula_PlanDePago")), "TL");
            $pdf->SetFont("arial", "", 10);
            $pdf->Cell(50, 6, utf8_decode($myPlanPago->nombre), "T");
            $pdf->SetFont("arial", "B", 10);
            $pdf->Cell(50, 6, utf8_decode(lang("vigencia")), "T", 0, "R");
            $pdf->SetFont("arial", "", 10);
            $pdf->Cell(50, 6, formatearFecha_pais($myPresupuesto->fecha_vigencia), "TR");
            $pdf->Ln();
            $mostrarPrecioListaYDescuento = Vconfiguracion::getValorConfiguracion($conexion, null, "mostrarPrecioListaYDescuento");
            if ($mostrarPrecioListaYDescuento == 1) {
                $mostrarDetalleDescuentoCondicionado = false;
                foreach ($arrDetalleDescuentos as $concepto => $descuentos) {
                    switch ($concepto) {
                        case 1:
                            $nombreConcepto = lang("precio_lista_curso");
                            break;

                        case 5:
                            $nombreConcepto = lang("precio_lista_matricula");
                            break;

                        default:
                            $nombreConcepto = '';
                            break;
                    }
                    if ($nombreConcepto <> '') {
                        $pdf->Cell(10, 6, "", "L");
                        $pdf->Cell(40, 6, utf8_decode($nombreConcepto));
                        $pdf->Cell(40, 6, formatearImporte($descuentos['precio_lista'], true, $conexion));
                        if ($myPlanPago->descon == 1 && $descuentos['descuento'] <> 0) {
                            $pdf->Cell(90, 6, utf8_decode("descuento_condicionado") . " " . $descuentos['descuento'] . " % (*)", "R");
                            $mostrarDetalleDescuentoCondicionado = true;
                        } else {
                            $pdf->Cell(90, 6, "", "R");
                        }
                        $pdf->Ln();
                    }
                }
                if ($mostrarDetalleDescuentoCondicionado) {
                    $diasProrroga = Vconfiguracion::getValorConfiguracion($conexion, null, "descuentosCondicionados", "dias_prorroga");
                    $pdf->SetFont("arial", '', 8);
                    $textoCondicionado = lang("el_descuento_condicionado_se_pierde_a_los_dias_de_vencida_la_cuota");
                    $textoCondicionado = str_replace("###", $diasProrroga, $textoCondicionado);
                    $pdf->Cell(180, 6, "(*) " . utf8_decode($textoCondicionado), "LR");
                    $pdf->Ln();
                }
            }
            $pdf->SetFont("arial", "B", 10);
            $pdf->Cell(180, 6, utf8_decode(lang("detalles")), "LR");
            $pdf->SetFont("arial", "", 10);
            $pdf->Ln();
            $pdf->SetFont("arial", "B", 8);
            $pdf->Cell(30, 6, '', "L", 0, "C");
            $pdf->Cell(30, 6, utf8_decode(lang("concepto")), "");
            $pdf->Cell(30, 6, utf8_decode(lang("cuota")), "", 0, "C");
            $pdf->Cell(30, 6, utf8_encode(lang("valor")), "", 0, "C");
            $pdf->Cell(60, 6, "", "R");
            $pdf->Ln();
            $pdf->SetFont("arial", "", 10);
            $tieneMatricula = false;
            foreach ($datosdetalle as $detalle) {
                $complementoDetalle = $detalle['cod_concepto'] == 5 ? " *" : "";
                if ($detalle['cod_concepto'] == 5)
                    $tieneMatricula = true;
                $pdf->Cell(30, 6, '', "L", 0, "C");
                $pdf->Cell(30, 6, utf8_decode($detalle['concepto']) . $complementoDetalle);
                $pdf->Cell(30, 6, $detalle['nrocuota'], "", 0, "C");
                $pdf->Cell(30, 6, $detalle['valor'], "", 0, "C");
                $pdf->Cell(60, 6, "", "R");
                if ($pdf->GetY() >= 260) {
                    $this->agregarPiePagina($pdf, $conexion);
                    $pdf->AddPage("P", "A4");
                } else {
                    $pdf->Ln();
                }
            }
            $pdf->Cell(180, 0, "", "T");
            $pdf->Ln();
            if ($tieneMatricula) {
                $pdf->SetFont("arial", '', 8);
                $pdf->Cell(180, 6, utf8_decode(lang("pie_de_presupuesto")));
                $pdf->Ln();
                $pdf->SetFont("arial", '', 10);
            }

            $pdf->SetFont("arial", '', 8);
            $pdf->Cell(180, 6, utf8_decode(lang("obs_hs_catedras")));

            $pdf->Ln(10);
            $pdf->SetFont("arial", "B", 10);
            $pdf->Cell(180, 6, utf8_decode(lang("observaciones")), "TLBR");
            $pdf->Ln();
            $pdf->SetFont("arial", '', 10);
            $pdf->Cell(180, 6, utf8_decode($myPresupuesto->observaciones), "TLBR");
            $pdf->Ln(10);

            if ($textoPie <> '') {
                $arrTexto = explode("\n", $textoPie);
                foreach ($arrTexto as $texto) {
                    $pdf->Cell(0, 6, utf8_decode($texto));
                    if ($pdf->GetY() >= 260) {
                        $this->agregarPiePagina($pdf, $conexion);
                        $pdf->AddPage("P", "A4");
                    } else {
                        $pdf->Ln();
                    }
                }
            }
            $this->agregarPiePagina($pdf, $conexion);
        }
        $conexion = $this->load->database("default", true);
        $arrResp = array();
        $myImpresion = new impresiones($conexion, $this->codigofilial);
        if (!$myImpresion->printerPDF($conexion, 1, $pdf, $printerID)) {
            $arrResp["error"] = "ha ocurrido un error al enviar el trabajo de impresion con el mensaje {$myImpresion->getError()}";
        } else {
            $arrResp['success'] = "success";
        }
        return $arrResp;
    }

    /**
     * Imprime detalle de una matricula y/o el reglamento correspondiente
     *
     * @param int $idMatricula
     * @param string $idioma
     * @param bollean $imprimirMatricula
     * @param boolean $imprimirReglamento
     * @return arrayRespuesta
     */
    function imprimirMatriculas($idMatricula, $idioma, $imprimirMatricula = true, $imprimirReglamento = null, $printerID = null, $cantidadCopias = null,
            $reimprimir = false, $imprimirCtacte = false, $imprimirCurso = false, $imprimirTitulo = false, $imprimir_observaciones = true, $imprimirReciboCobroMatricula = false) {
        $cantidadCopias = $this->getCantidadCopias(5, $cantidadCopias);
        $this->load->helper('alumnos');
        $this->load->helper('comisiones');
        $conexion = $this->load->database($this->codigofilial, true);


        $myMatricula = new Vmatriculas($conexion, $idMatricula);

        $this->load->helper('cuentacorriente');
        if ($imprimirCtacte) {
            $arrCtacte = Vctacte::getCtaCte($conexion, false, array("concepto" => $idMatricula), null, null, null, false, array(array("campo" => "cod_concepto", "valores" => array(1, 5)), array("campo" => "habilitado", "valores" => array(1, 2))));
            formatearCtaCte($conexion, $arrCtacte);
        }

        $myAlumno = new Valumnos($conexion, $myMatricula->cod_alumno);
        $tipodoc = new Vdocumentos_tipos($conexion, $myAlumno->tipo);
        $myLocalidad = new Vlocalidades($conexion, $myAlumno->id_localidad);
        $arrComisiones = $myMatricula->getComisiones();
        $nombreComision = "sin comision";
        $ciclo = "sin comision";
        $fechaInicio = "";

        if(isset($arrComisiones[0]))
        {
            $myComision = new Vcomisiones($conexion, $arrComisiones[0]['codigo']);
            $nombreComision = $myComision->nombre;
            $ciclo = new Vciclos($conexion, $myComision->ciclo);
            $fechaInicio = $myComision->getFechaInicio();
        }
        $arrPeriodos = $myMatricula->getPeriodosMatricula();
        $cantPeriodos = count($arrPeriodos);
        $periodo = $arrPeriodos[$cantPeriodos - 1]['nombre'];
        $codTipoPeriodo = $arrPeriodos[$cantPeriodos - 1]['cod_tipo_periodo'];
        $arrDetallesPago = $myMatricula->getDetalleCtacteMatriculacion($codTipoPeriodo);

        if ($imprimirCurso) {
            $myPlanAcademico = new Vplanes_academicos($conexion, $myMatricula->cod_plan_academico);
            $arrPerPlan = $myPlanAcademico->getPeriodos();
            $myCurso = new Vcursos($conexion, $myPlanAcademico->cod_curso);
            if ($idioma == "es") {
                $nombreCurso = $myCurso->nombre_es;
            } else if ($idioma == "pt") {
                $nombreCurso = $myCurso->nombre_pt;
            } else {
                $nombreCurso = $myCurso->nombre_in;
            }
            if (count($arrPerPlan) != $cantPeriodos) {
                $nombreCurso .= " " . lang($periodo);
            }
        }
        if ($imprimirTitulo) {
            $arrTitulos = $myMatricula->getTitulos();
            $tituloCurso = $arrTitulos[0]['titulo'];
            if (count($arrTitulos) > 1) {
                $tituloCurso.=' (' . lang('intermedios') . ': ';
                for ($i = 1; $i < count($arrTitulos); $i++) {
                    $tituloCurso.=$arrTitulos[$i]['titulo'] . ' ';
                }
                $tituloCurso.=')';
            }
        }

        if ($imprimirReciboCobroMatricula){
            $codCobro = $myMatricula->getCodCobroConceptoMatricula();
            if ($codCobro){
                $pdf = $this->recibo_cobros(array($codCobro), null, 1, true);
            }
        }
        if (!isset($pdf)){
            $pdf = new PDF_AutoPrint('P', 'mm', 'A4');
            $pdf->AutoPrint(false);
        } else {
        }
        for ($cantCopias = 0; $cantCopias < $cantidadCopias; $cantCopias++) {
            $this->setPapelMembretado($pdf);
            $conanexo = false;
            if (count($imprimirReglamento) > 0) {
                foreach ($imprimirReglamento as $reglamento) {
                    if ($reglamento == 2) {
                        $conanexo = true;
                    }
                }
            }
            if ($imprimirMatricula || $imprimirCtacte) {
                $myConfiguracion = new Vconfiguracion($conexion, 11);
                $textoPie = $myConfiguracion->value;
                $nombreTemp = $this->codigofilial."_".$myAlumno->getCodigo().".png";
                $img = $myAlumno->getImagen();
                if ($img <> ''){
                    $img = str_replace('data:image/png;base64,', '', $img);
                    $img = str_replace(' ', '+', $img);
                    $data = base64_decode($img);
                    $tempfile = tmpfile();
                    fwrite($tempfile, $data);
                    $metaDatas = stream_get_meta_data($tempfile);
                    $tmpFilename = $metaDatas['uri'];
                }
                $pdf->SetFont('arial', '', 10);
                $pdf->AddPage('P', 'A4');
                if ($img <> ''){
                    $pdf->Image($tmpFilename, 20, 20, 30, 25,'PNG');
                    $pdf->Cell(100, 4, "");
                    $pdf->Ln(4);
                    $xFecha = 100;
                }
                $pdf->Cell(130, 3, "", 0);
                $pdf->Cell(100, 3, utf8_decode(lang("fecha_emision")) . " " . date("d/m/Y"));
                $pdf->Ln();
                $pdf->Cell(130, 9, "", 0);
                $pdf->Cell(100, 9, utf8_decode(lang("matricula") . ' ' . lang('nro')) . " " . $this->codigofilial . "-" .$idMatricula);
                $pdf->Ln();
                $pdf->SetFont('arial', "B", "18");
                $anexo = $conanexo ? 'Anexo I - ' : '';
                $pdf->Cell(0, 6, utf8_decode($anexo . lang("matricula")), 0, 0, "C");
                $pdf->Ln();
                $pdf->Cell(0, 0, "", "TB", 1, '', false);
                $pdf->Ln(0);
                $pdf->SetFont("arial", "B", 14);
                $pdf->Cell(0, 9, utf8_decode(lang("datos_del_alumno")));
                $pdf->Ln();
                $pdf->cell(1, 6);
                $pdf->Cell(179, 0, "", "B");
                $pdf->Ln();
                $pdf->cell(1, 5, '', "R");
                $pdf->cell(3);
                $pdf->SetFont("arial", "B", 10);
                $pdf->Cell(50, 5, utf8_decode(lang("cod_alumno")));
                $pdf->SetFont("arial", "", 10);
                $pdf->Cell(126, 5, $this->codigofilial . "-" . utf8_decode($myAlumno->getCodigo()), 'R');
                $pdf->Ln();
                $pdf->cell(1, 5, '', "R");
                $pdf->cell(3);
                $pdf->SetFont("arial", "B", 10);
                $pdf->Cell(50, 5, utf8_decode(lang("nombre")));
                $pdf->SetFont("arial", "", 10);
                $apellido = inicialesMayusculas($myAlumno->apellido);
                $nombre = inicialesMayusculas($myAlumno->nombre);
                $pdf->Cell(126, 5, utf8_decode("{$apellido}, {$nombre}"), 'R');
                $pdf->Ln();
                $pdf->cell(1, 5, '', "R");
                $pdf->cell(3);
                $pdf->SetFont("arial", "B", 10);
                $pdf->Cell(50, 5, utf8_decode(lang("localidad")));
                $pdf->SetFont("arial", "", 10);
                $pdf->Cell(126, 5, utf8_decode($myLocalidad->nombre), 'R');
                $pdf->Ln();
                $pdf->cell(1, 5, '', "R");
                $pdf->cell(3);
                $pdf->SetFont("arial", "B", 10);
                $pdf->Cell(50, 5, utf8_decode(lang("domicilio")));
                $pdf->SetFont("arial", "", 10);
                $pdf->Cell(126, 5, utf8_decode($myAlumno->getDomicilioFormateado()), 'R');
                $pdf->Ln();
                $pdf->cell(1, 5, '', "R");
                $pdf->cell(3);
                $pdf->SetFont("arial", "B", 10);
                $pdf->Cell(50, 5, utf8_decode(lang("Tel")));
                $pdf->SetFont("arial", "", 10);
                $arrTelefono = $myAlumno->getTelefonos(true);
                if(count($arrTelefono) == 0)
                $arrTelefono[] = array(
                    "cod_area" => "",
                    "numero" => ""
                );
                $pdf->Cell(126, 5, $arrTelefono[0]["cod_area"]."-".$arrTelefono[0]["numero"], 'R');
                $pdf->Ln();
                $pdf->cell(1, 5, '', "R");
                $pdf->cell(3);
                //modificado ticket 5070 por Franco ->
                $pdf->SetFont("arial", "B", 10);
                $pdf->Cell(50, 5, utf8_decode("Email"));
                $pdf->SetFont("arial", "", 10);
                $pdf->Cell(126, 5, $myAlumno->email, 'R');
                $pdf->Ln();
                $pdf->cell(1, 5, '', "R");
                $pdf->cell(3);
                //<- modificado ticket 5070 por Franco 
                $pdf->SetFont("arial", "B", 10);
                $pdf->Cell(50, 5, utf8_decode(lang("fecha_nacimiento")));
                $pdf->SetFont("arial", "", 10);
                $pdf->Cell(126, 5, formatearFecha_pais($myAlumno->fechanaci), 'R');
                $pdf->Ln();
                $pdf->cell(1, 5, '', "R");
                $pdf->cell(3);
                $pdf->SetFont("arial", "B", 10);
                $pdf->Cell(50, 5, utf8_decode($tipodoc->nombre));
                $pdf->SetFont("arial", "", 10);
                $pdf->Cell(126, 5, $myAlumno->documento, 'R');
                $pdf->Ln();
                $pdf->cell(1, 5, '', "R");
                $pdf->cell(3, 5, '', "B");
                $pdf->SetFont("arial", "B", 10);
                $pdf->Cell(50, 5, utf8_decode(lang("datos_estadoCivil")), "B");
                $pdf->SetFont("arial", "", 10);
                $pdf->Cell(126, 5, utf8_decode($myAlumno->estado_civil), 'BR');
                

                //mmori inicio
                if($imprimir_observaciones)
                {
                    $pdf->Ln(7);
                    
                    $pdf->SetFont("arial", "B", 14);
                    $pdf->Cell(0, 8, utf8_decode(lang("observaciones")));
                    $pdf->Ln();
                    $pdf->cell(1, 5);
                    $pdf->Cell(179, 0, "", "B");
                    $pdf->Ln();

                    
                    /*** Documentacion ***/
                    
                    $condiciones = array("cod_matricula"=>$idMatricula);
                    $tmp_documentacion_entregada = Tdocumentacion_alumnos::listarDocumentacion_alumnos($conexion, $condiciones);
                    
                    // Obtenemos los tipos de documentos
                    $tmp_tipos_documentacion = Vdocumentacion::getDocumentacionPlan($conexion, 
                                                                    $this->codigofilial, 
                                                                    $myMatricula->cod_plan_academico
                                                               );
                    $tipos_documentacion = array();



                    $documentaciones =  Tdocumentacion_alumnos::listarDocumentacion($conexion);
                    $documentacionPlan = Vdocumentacion::getDocumentacionPlan($conexion, $this->codigofilial, $myMatricula->cod_plan_academico);
                    $tmp_tipos_documentacion = array();
                    foreach($documentaciones as $indice =>  $documento){
                        if(empty($documentacionPlan) && $documento['tipo'] == 1){
                            $tmp_tipos_documentacion[] = $documento;
                        } else {
                            $esta = false;
                            foreach($documentacionPlan as $docuPlan){
                                if($docuPlan['id_documentacion'] == $documento['codigo']){
                                    $esta = true;
                                    break;
                                }
                            }
                            if($esta)
                                $tmp_tipos_documentacion[] = $documento;
                        }
                    }


                    // Ordenamos el array para que se pueda obtener el nombre de la documentacion utilizando el indice
                    foreach ($tmp_tipos_documentacion as $current_tipo_documentacion) {
                        $tipos_documentacion[$current_tipo_documentacion['codigo']] = $current_tipo_documentacion['documentacion'];
                    }
                    //unset($tmp_tipos_documentacion);
                    
                    
                    $documentacion_entregada = array();
                    foreach ($tmp_documentacion_entregada as $current_documentacion)
                    {
                        if($current_documentacion["documentacion"] != '')
                            $documentacion_entregada[] = $tipos_documentacion[$current_documentacion["documentacion"]];
                    }
                    
                    // Obtenemos la documentacion que le falta entregar al alumno.
                    $documentacion_falta_entregar = array_diff($tipos_documentacion, $documentacion_entregada);
                    
                    
                    /*** Materiales ***/
                    
                    // Obtenemos los materiales entregados con una consulta.
                    // Para eso se deberia utilizar la clase Tmateriales_alumnos.
                    $tmp_materiales_entregados = $conexion
                        ->select('id_material')
                        ->from('materiales_alumnos')
                        ->where('cod_matricula', $idMatricula)
                        ->get();
                    
                    // Obtenemos los tipos de materiales
                    $tmp_tipos_materiales = Tmateriales::listarMateriales($conexion);
                    $tipos_materiales = array();
                    
                    // Ordenamos el array para que se pueda obtener el nombre de la documentacion utilizando el indice
                    foreach ($tmp_tipos_materiales as $current_tipo_material) {
                        $tipos_materiales[$current_tipo_material['id']] = $current_tipo_material['material'];
                    }
                    
                    $materiales_entregados = array();
                    if ($tmp_materiales_entregados !== false) {
                        $tmp_materiales_entregados = $tmp_materiales_entregados->result_array();
                        
                        if (is_array($tmp_materiales_entregados) && isset($tmp_materiales_entregados[0])) {
                            foreach ($tmp_materiales_entregados as $index => $current_material_entregado) {
                                $materiales_entregados[$index] = $tipos_materiales[$current_material_entregado['id_material']];
                            }
                        }
                    }
                    
                    
                    
                    /*** Inicio escritura PDF ***/
                    
                    
                    /*** Escritura documentacion ***/
                    
                    $charObservaciones = str_split($myMatricula->observaciones);
                    $sObservaciones = "";
                    $i = 0;

                    $pdf->cell(1, 5, '', "R");
                    $pdf->cell(3, 5, '', "");
                    $pdf->SetFont("arial", "B", 10);
                    $pdf->Cell(50, 5, utf8_decode(lang("observaciones")).":", "");
                    $pdf->SetFont("arial", "", 10);

                    foreach ($charObservaciones as $char)
                    {
                        $sObservaciones .= $char;
                        $i ++;
                        if($i == 68)
                        {
                            $pdf->Cell(126, 5, utf8_decode($sObservaciones), 'R');
                            $pdf->Ln();
                            $sObservaciones = "";
                            $i = 0;
                            $pdf->cell(1, 5, '', "R");
                            $pdf->cell(3, 5, '', "");
                            $pdf->Cell(50, 5, "", "");
                        }
                    }

                    if($i != 0)
                    {
                        $pdf->Cell(126, 5, utf8_decode($sObservaciones), 'R');
                        $pdf->cell(1, 5, '', "");
                        $pdf->cell(3, 5, '', "");
                        $pdf->Cell(50, 5, "", "");
                    }

                    $len = count($documentacion_entregada);
                    $len2 = count($documentacion_falta_entregar);

                    $pdf->Cell(126, 5, "", 'R');
                    $pdf->Ln();
                    $pdf->cell(1, 5, '', "R");
                    $pdf->cell(3, 5, '', "");
                    $pdf->SetFont("arial", "B", 10);

                    if($len == 0)
                    {
                        $documentacion_entregada[0]='nada';
                    }

                    $pdf->Cell(176, 5, utf8_decode(lang("documentacion_entregada")), "R");
                    $pdf->Ln();
                    $pdf->cell(1, 5, '', "R");
                    $pdf->cell(3, 5, '', "");
                    $pdf->Cell(50, 5, utf8_decode(lang("por_el_alumno")), "");
                    $pdf->SetFont("arial", "", 10);

                    $i = 0;
                    $primero = true;

                    foreach ($documentacion_entregada as $doc)
                    {
                        if(!$primero && !($i == $len - 1))
                        {
                            $pdf->cell(1, 5, '', "R");
                            $pdf->cell(3, 5, '', "");
                            $pdf->Cell(50, 5,"", "");
                        }
                        if(!$primero && $i == $len - 1)
                        {
                            $pdf->cell(1, 5, '', "R");
                            $pdf->cell(3, 5, '', "");
                            $pdf->Cell(50, 5, "", "");
                            $pdf->Cell(126, 5, utf8_decode(lang($doc)), 'R');
                            $pdf->Ln();
                        }
                        else
                        {
                            $pdf->Cell(126, 5, utf8_decode(lang($doc)), 'R');
                            $primero = false;
                            $pdf->Ln();
                        }
                    }

                    $pdf->Cell(1, 5, "", 'R');
                    $pdf->Cell(179, 5, "", 'R');
                    $pdf->Ln();
                    $pdf->cell(1, 5, '', "R");
                    $pdf->SetFont("arial", "B", 10);

                    if($len2 == 0)
                    {
                        $documentacion_falta_entregar[0]='nada';
                        $pdf->cell(3, 5, '', "");
                        $pdf->Cell(50, 5, utf8_decode(lang("documentacion_a_entregar")), "");
                    }

                    if($len2 >= 1)
                    {
                        $pdf->cell(3, 5, '', "");
                        $pdf->Cell(50, 5, utf8_decode(lang("documentacion_a_entregar")), "");
                    }

                    $pdf->SetFont("arial", "", 10);

                    $i = 0;
                    $primero = true;
                    foreach ($documentacion_falta_entregar as $doc)
                    {
                        if(!$primero && !($i == $len - 1))
                        {
                            $pdf->cell(1, 5, '', "R");
                            $pdf->cell(3, 5, '', "");
                            $pdf->Cell(50, 5,"", "");
                        }
                        if(!$primero && $i == $len - 1)
                        {
                            $pdf->cell(1, 5, '', "R");
                            $pdf->cell(3, 5, '', "");
                            $pdf->Cell(50, 5, "", "");
                            $pdf->Cell(126, 5, utf8_decode(lang($doc)), 'R');
                            $pdf->Ln();
                        }
                        else
                        {
                            $pdf->Cell(126, 5, utf8_decode(lang($doc)), 'R');
                            $primero = false;
                            $pdf->Ln();
                        }
                        $i++;

                    }

                    
                    /*** Escritura materiales ***/

                    $len2 = count($materiales_entregados);

                    $pdf->Cell(1, 5, "", 'R');
                    $pdf->Cell(179, 5, "", 'R');
                    $pdf->Ln();
                    $pdf->SetFont("arial", "B", 10);

                    if($len2 == 0)
                    {
                        $materiales_entregados[0]='nada';
                        $len2 = 1;
                    }

                    $pdf->cell(1, 5, '', "R");
                    $pdf->cell(3, 5, '', "");
                    $pdf->Cell(176, 5, utf8_decode(lang("material_entregado")), "R");
                    $pdf->Ln();
                    $pdf->cell(1, 5, '', "R");

                    if($len2 == 1)
                    {

                        $pdf->cell(3, 5, '', "B");
                        $pdf->Cell(50, 5, utf8_decode(lang("al_alumno")), "B");
                    }

                    if($len2 > 1)
                    {
                        $pdf->cell(3, 5, '', "");
                        $pdf->Cell(50, 5, utf8_decode(lang("al_alumno")), "");
                    }

                    $pdf->SetFont("arial", "", 10);

                    $i = 0;
                    $primero = true;
                    
                    foreach ($materiales_entregados as $current_material_entregado)
                    {
                        if(!$primero)
                        {
                            if ($i != $len2-1)
                            {
                                $pdf->cell(1, 5, '', "R");
                                $pdf->cell(3, 5, '', "");
                                $pdf->Cell(50, 5,"", "");
                                $pdf->Cell(126, 5, utf8_decode(lang($current_material_entregado)), 'R');
                                $pdf->Ln();
                            }
                            else
                            {
                                $pdf->cell(1, 5, '', "R");
                                $pdf->cell(3, 5, '', "B");
                                $pdf->Cell(50, 5, "", "B");
                                $pdf->Cell(126, 5, utf8_decode(lang($current_material_entregado)), 'BR');
                            }
                        }
                        else
                        {
                            if ($i == $len2-1)
                            {
                                $pdf->Cell(126, 5, utf8_decode(lang($current_material_entregado)), 'BR');
                                $primero = false;
                            }
                            else
                            {
                                $pdf->Cell(126, 5, utf8_decode(lang($current_material_entregado)), 'R');
                                $primero = false;
                                $pdf->Ln();
                            }
                        }

                        $i++;
                    }

                }
                //mmori fin

                $pdf->Ln(7);
                $pdf->SetFont("arial", "B", 14);
                $pdf->Cell(0, 8, utf8_decode(lang("datos_del_curso")));
                $pdf->Ln();
                $pdf->cell(1, 5);
                $pdf->Cell(179, 0, "", "B");
                $pdf->Ln();
                if ($imprimirCurso) {
                    $pdf->cell(1, 5, '', "R");
                    $pdf->cell(3);
                    $pdf->SetFont("arial", "B", 10);
                    $pdf->Cell(50, 5, utf8_decode(lang("nombre")));
                    $pdf->SetFont("arial", "", 10);
                    $pdf->Cell(126, 5, utf8_decode($nombreCurso), 'R');
                    $pdf->Ln();
                    $pdf->cell(1, 5, '', "R");
                    $pdf->cell(3);
                    $pdf->SetFont("arial", "B", 10);
                    $pdf->Cell(50, 5, utf8_decode(lang("codigo")));
                    $pdf->SetFont("arial", "", 10);
                    $pdf->Cell(126, 5, utf8_decode($myPlanAcademico->cod_curso), 'R');
                    $pdf->Ln();
                }
                if ($imprimirTitulo) {
                    $pdf->cell(1, 5, '', "R");
                    $pdf->cell(3);
                    $pdf->SetFont("arial", "B", 10);
                    $pdf->Cell(50, 5, utf8_decode(lang("certificado_s")));
                    $pdf->SetFont("arial", "", 10);
                    $pdf->Cell(126, 5, utf8_decode($tituloCurso), 'R');
                    $pdf->Ln();
                }
                $pdf->cell(1, 5, '', "R");
                $pdf->cell(3, 5, '', "");
                $pdf->SetFont("arial", "B", 10);
                $pdf->Cell(50, 5, utf8_decode(lang("comision")), "");
                $pdf->SetFont("arial", "", 10);
                $pdf->Cell(126, 5, utf8_decode($nombreComision), 'R');
                $pdf->Ln();

                $pdf->cell(1, 5, '', "R");
                $pdf->cell(3, 5, '', "");
                $pdf->SetFont("arial", "B", 10);
                $pdf->Cell(50, 5, utf8_decode(lang("fecha_inicio")), "");
                $pdf->SetFont("arial", "", 10);

                if ($fechaInicio <> '')
                {
                    $pdf->Cell(126, 5, formatearFecha_pais($fechaInicio), 'R');
                }
                else
                {
                    $pdf->Cell(126, 5, lang("a_confirmar"), 'R');
                }

                $pdf->Ln();
                $pdf->cell(1, 5, '', "R");
                $pdf->cell(3, 5, '', "");
                $pdf->SetFont("arial", "B", 10);
                $pdf->Cell(50, 5, utf8_decode(lang("monto_primer_cuota")), "");
                $pdf->SetFont("arial", "", 10);

                //siwakawa
                if (!$cantCopias)
                    $concepto = next($arrDetallesPago);

                while ($concepto["cod_concepto"] != 1 && $concepto = next($arrDetallesPago)){}

                $pdf->Cell(126, 5, $concepto["importe"], 'R');
                $pdf->Ln();
                $pdf->cell(1, 5, '', "R");
                $pdf->cell(3, 5, '', "B");
                $pdf->SetFont("arial", "B", 10);
                $pdf->Cell(50, 5, utf8_decode(lang("planpago_cantcuotas")), "B");
                $pdf->SetFont("arial", "", 10);
                $ultimo = end($arrDetallesPago);
                $pdf->Cell(126, 5, $ultimo["nrocuota"], 'BR');
                $pdf->Ln(7);

                //revisar si esto sigue aplicando
//                if (!$reimprimir) {
//
//                    $cantcuotas = count($arrDetallesPago);
//                    $mitad = ceil($cantcuotas / 2);
//                    $cuenta = 0;
//
//                    $pdf->SetFont("arial", "B", 14);
//                    $pdf->Cell(0, 8, utf8_decode(lang("financiacion")));
//                    $pdf->Ln();
//                    $pdf->cell(1, 5);
//
//                    if ($cantcuotas < 10) {
//                        $pdf->Cell(179, 0, "", "B");
//                        $pdf->Ln();
//                        $pdf->SetFont("arial", "B", 8);
//                        $pdf->Cell(1, 5, '', "R");
//                        $pdf->cell(3);
//                        $pdf->Cell(50, 5, utf8_decode(lang("concepto")));
//                        $pdf->Cell(42, 5, utf8_decode(lang("cuota")));
//                        $pdf->Cell(42, 5, utf8_decode(lang("vencimiento")));
//                        $pdf->Cell(42, 5, utf8_decode(lang("valor")), "R");
//                        $pdf->SetFont("arial", '', 10);
//                        $pdf->Ln();
//                        foreach ($arrDetallesPago as $k => $detalle) {
//                            $pdf->Cell(1, 5, '', "R");
//                            $pdf->cell(3);
//                            $pdf->Cell(50, 5, utf8_decode(lang($detalle['key'])));
//                            $pdf->Cell(42, 5, $detalle['nrocuota']);
//                            $pdf->Cell(42, 5, formatearFecha_pais($detalle['fechavenc']));
//                            $pdf->Cell(42, 5, formatearImporte($detalle['importe']), "R");
//                            if ($pdf->GetY() >= 260) {
//                                $this->agregarPiePagina($pdf, $conexion);
//                                $pdf->AddPage("P", "A4");
//                            } else {
//                                $pdf->Ln();
//                            }
//                        }
//                    } else {
//
//                        $pdf->Cell(179, 0, "", "B");
//                        $pdf->Ln();
//                        $segunda = false;
//                        foreach ($arrDetallesPago as $k => $detalle) {
//                            if ($k == 0) {
//                                $y = $pdf->GetY();
//                                $pdf->SetFont("arial", "B", 8);
//                                $pdf->Cell(1, 5, '', "R");
//                                $pdf->cell(3);
//                                $pdf->Cell(21, 5, utf8_decode(lang("concepto")));
//                                $pdf->Cell(20, 5, utf8_decode(lang("cuota")));
//                                $pdf->Cell(23, 5, utf8_decode(lang("vencimiento")));
//                                $pdf->Cell(21, 5, utf8_decode(lang("valor")), "R");
//                                $pdf->SetFont("arial", '', 10);
//                                $pdf->Ln();
//                            }
//                            if ($k == $mitad) {
//                                $segunda = true;
//                                $pdf->SetY($y);
//                                $pdf->SetX(111);
//                                $pdf->SetFont("arial", "B", 8);
//                                $pdf->Cell(1, 5, '', "");
//                                $pdf->cell(3);
//                                $pdf->Cell(21, 5, utf8_decode(lang("concepto")));
//                                $pdf->Cell(20, 5, utf8_decode(lang("cuota")));
//                                $pdf->Cell(23, 5, utf8_decode(lang("vencimiento")));
//                                $pdf->Cell(21, 5, utf8_decode(lang("valor")), "R");
//                                $pdf->SetFont("arial", '', 10);
//                                $pdf->Ln();
//                            }
//                            if ($segunda) {
//                                $cuenta = $cuenta + 1;
//                                $pdf->SetX(111);
//                                $pdf->Cell(1, 5, '', "");
//                            } else {
//                                $pdf->Cell(1, 5, '', "R");
//                            }
//                            $pdf->cell(3);
//                            $pdf->Cell(23, 5, utf8_decode(lang($detalle['key'])));
//                            $pdf->Cell(18, 5, '  ' . $detalle['nrocuota']);
//                            $pdf->Cell(23, 5, formatearFecha_pais($detalle['fechavenc']));
//                            $pdf->Cell(21, 5, formatearImporte($detalle['importe']), "R");
//                            if ($pdf->GetY() >= 260) {
//                                $this->agregarPiePagina($pdf, $conexion);
//                                $pdf->AddPage("P", "A4");
//                            } else {
//                                $pdf->Ln();
//                            }
//                        }
//                    }
//                    if ($cuenta < $mitad) {
//                        $pdf->Cell(179, 5, '', "R");
//                        $pdf->Ln();
//                    }
//                    $pdf->cell(1, 5);
//                    $pdf->Cell(179, 2, "", "T");
//                    $pdf->Ln();
//                }
                if ($imprimirCtacte && count($arrCtacte) > 0) {
                    if (!isset($cantcuotas))
                        $cantcuotas = count($arrCtacte);
                    $pdf->SetFont("arial", "B", 14);
                    $pdf->Cell(0, 8, utf8_decode(lang("resumen_de_cuenta_corriente")));
                    $pdf->Ln();
                    $pdf->cell(1, 5);
                    if ($cantcuotas < 10) {
                        $mitad = ceil($cantcuotas / 2);
                        $cuenta = 0;
                        $mitad = 5;
                        $pdf->Cell(179, 0, "", "B");
                        $pdf->Ln();
                        $pdf->SetFont("arial", "B", 8);
                        $pdf->Cell(1, 5, '', "R");
                        $pdf->cell(3);
                        $pdf->Cell(36, 5, utf8_decode(lang("concepto")));
                        $pdf->Cell(30, 5, utf8_decode(lang("cuota")));
                        $pdf->Cell(40, 5, utf8_decode(lang("vencimiento")));
                        $pdf->Cell(35, 5, utf8_decode(lang("valor")));
                        $pdf->Cell(35, 5, utf8_decode(lang("saldo")), "R");
                        $pdf->SetFont("arial", '', 10);
                        $pdf->Ln();
                        foreach ($arrCtacte as $k => $detalle) {
                            $pdf->Cell(1, 5, '', "R");
                            $pdf->cell(3);
                            $pdf->Cell(36, 5, utf8_decode($detalle['nombreconcepto']));
                            $pdf->Cell(30, 5, '  ' . $detalle['nrocuota']);
                            $pdf->Cell(40, 5, $detalle['fechavenc']);
                            $pdf->Cell(35, 5, utf8_decode($detalle['simbolo_moneda'] . $detalle['importeformateado']));
                            $pdf->Cell(35, 5, utf8_decode($detalle['simbolo_moneda'] . $detalle['saldoformateado']), "R");
                            if ($pdf->GetY() >= 260) {
                                $this->agregarPiePagina($pdf, $conexion);
                                $pdf->AddPage("P", "A4");
                            } else {
                                $pdf->Ln();
                            }
                        }
                    } else {
                        $mitad = ceil($cantcuotas / 2);
                        $cuenta = 0;
                        $pdf->Cell(179, 0, "", "B");
                        $pdf->Ln();
                        $segunda = false;
                        foreach ($arrCtacte as $k => $detalle) {
                            if ($k == 0) {
                                $y = $pdf->GetY();
                                $pdf->SetFont("arial", "B", 8);
                                $pdf->Cell(1, 5, '', "R");
                                $pdf->cell(3);
                                $pdf->Cell(20, 5, utf8_decode(lang("concepto")));
                                $pdf->Cell(10, 5, utf8_decode(lang("cuota")));
                                $pdf->Cell(20, 5, utf8_decode(lang("vencimiento")));
                                $pdf->Cell(18, 5, utf8_decode(lang("valor")));
                                $pdf->Cell(18, 5, utf8_decode(lang("saldo")), "R");
                                $pdf->SetFont("arial", '', 10);
                                $pdf->Ln();
                            }
                            if ($k == $mitad) {
                                $segunda = true;
                                $pdf->SetY($y);
                                $pdf->SetX(110);
                                $pdf->SetFont("arial", "B", 8);
                                $pdf->Cell(1, 5, '', "");
                                $pdf->cell(3);
                                $pdf->Cell(20, 5, utf8_decode(lang("concepto")));
                                $pdf->Cell(10, 5, utf8_decode(lang("cuota")));
                                $pdf->Cell(20, 5, utf8_decode(lang("vencimiento")));
                                $pdf->Cell(18, 5, utf8_decode(lang("valor")));
                                $pdf->Cell(18, 5, utf8_decode(lang("saldo")), "R");
                                $pdf->SetFont("arial", '', 10);
                                $pdf->Ln();
                            }
                            if ($segunda) {
                                $cuenta = $cuenta + 1;
                                $pdf->SetX(110);
                                $pdf->Cell(1, 5, '', "");
                            } else {
                                $pdf->Cell(1, 5, '', "R");
                            }
                            $pdf->cell(3);
                            $pdf->Cell(20, 5, utf8_decode($detalle['nombreconcepto']));
                            $pdf->Cell(10, 5, '  ' . $detalle['nrocuota']);
                            $pdf->Cell(20, 5, $detalle['fechavenc']);
                            $pdf->Cell(18, 5, utf8_decode($detalle['simbolo_moneda'] . $detalle['importeformateado']));
                            $pdf->Cell(18, 5, utf8_decode($detalle['simbolo_moneda'] . $detalle['saldoformateado']), "R");
                            if ($pdf->GetY() >= 260) {
                                $this->agregarPiePagina($pdf, $conexion);
                                $pdf->AddPage("P", "A4");
                            } else {
                                $pdf->Ln();
                            }
                        }
                    }
                    if ($cuenta < $mitad) {
                        $pdf->Cell(180, 5, '', "R");
                        $pdf->Ln();
                    }
                    $pdf->cell(1, 5);
                    $pdf->Cell(179, 2, "", "T");
                    $pdf->Ln();
                }
                /* LA LINEA DE ABAJO SE QUITA PARA RESOLUCION DEL TICKET 4528 (campo detalle  AL momento de imprimir que no salga en la matricula) */
//                if (trim($myMatricula->observaciones) <> ''){
//                    $pdf->SetFont('arial', "B", 14);
//                    $pdf->Cell(20, 6, lang("observaciones"));
//                    $pdf->Ln();
//                    $pdf->SetFont('arial', '', 10);
//                    $pdf->Cell(200, 6, utf8_decode(trim($myMatricula->observaciones)));
//                }
                if (trim($textoPie) <> '') {
                    $pdf->SetFont('arial', '', 8);
                    $pdf->cell(10, 5);
                    $pdf->Cell(170, 0, utf8_decode($textoPie));
                }
                $pdf->Ln(25);
                $pdf->cell(10, 3);
                $pdf->Cell(41, 3, '', 'T');
                $pdf->Cell(24);
                $pdf->Cell(41, 3, '', 'T');
                $pdf->Cell(23);
                $pdf->Cell(41, 3, '', 'T');
                $pdf->Ln();
                $pdf->cell(10, 6);
                $pdf->Cell(41, 0, utf8_decode(lang('asesor')), '', 0, 'C');
                $pdf->Cell(24);
                $pdf->Cell(41, 0, utf8_decode(lang('padre') . "/" . lang('madre') . "/" . lang('tutor')), '', 0, 'C');
                $pdf->Cell(23);
                $pdf->Cell(41, 0, utf8_decode(lang('Alumno')), '', 0, 'C');
                $this->agregarPiePagina($pdf, $conexion);
            }
            if (count($imprimirReglamento) > 0) {
                foreach ($imprimirReglamento as $reglamento) {
                    $this->imprimir_reglamento($conexion, $pdf, $reglamento, $idioma, $conanexo);
                }
            }
        }
        $pdf->Output();
        die();
        $conexion = $this->load->database("default", true);
        $arrResp = array();
        $myImpresion = new impresiones($conexion, $this->codigofilial);
        if (!$myImpresion->printerPDF($conexion, 5, $pdf, $printerID)) {
            $arrResp["error"] = "ha ocurrido un error al enviar el trabajo de impresion con el mensaje {$myImpresion->getError()}";
        } else {
            $arrResp['success'] = "success";
        }
        return $arrResp;
    }

    /**
     * imprime resumen de cuenta
     *
     * @param int $codAlumno
     * @param string $printerID
     * @param int $cantidadCopias
     * @return array
     */
    public function resumen_ctacte_alumno($codAlumno, $printerID = null, $cantidadCopias = null, $consaldo = false) {
        $cantidadCopias = $this->getCantidadCopias(3, $cantidadCopias);
        $filial = $this->session->userdata('filial');
        $this->load->helper();
        $config = array("codigo_filial" => $filial["codigo"]);
        $this->load->model("Model_alumnos", "", false, $config);
        $this->load->helper("cuentacorriente");
        $this->load->helper('alumnos');
        $order[] = array(
            'campo' => 'fechavenc',
            'orden' => 'asc'
        );
        $condiciones = array('habilitado >' => 0, 'habilitado <' => 3);
        $conexion = $this->load->database($this->codigofilial, true);
        $arrCtacte = $this->Model_alumnos->getCtaCte($codAlumno, $condiciones, $consaldo, $order);
        $conexion = $this->load->database($this->codigofilial, true);
        $myAlumno = new Valumnos($conexion, $codAlumno);
        $pdf = new PDF_AutoPrint('P', 'mm', 'A4');
        $pdf->AutoPrint(false);
        for ($cantCopias = 0; $cantCopias < $cantidadCopias; $cantCopias++) {
            $this->setPapelMembretado($pdf);
            $pdf->SetFont('arial', 'B', 12);
            $pdf->AddPage('P', 'A4');
            $pdf->SetMargins(20, 43, 5);
            $pdf->Cell(0, 6, utf8_decode(lang("Resumen_de_cuenta")), 0, 0, "C");
            $pdf->Ln(8);
            $pdf->SetFont("arial", "B", 10);
            $pdf->Cell(50, 6, utf8_decode(lang("cuenta_corriente_alumno")), 0, 0, "R");
            $pdf->Cell(10, 6);
            $pdf->SetFont("arial", "", 10);
            $apellido = inicialesMayusculas($myAlumno->apellido);
            $nombre = inicialesMayusculas($myAlumno->nombre);
            $pdf->Cell(80, 6, utf8_decode("{$apellido}, {$nombre}"));
            $pdf->Cell(30, 6, formatearFecha_pais(date("Y-m-d")), 0, 0, "R");
            $pdf->Ln(8);
            $pdf->SetFont("arial", "B", 10);
            $pdf->Cell(120, 6, utf8_decode(lang("descripcion")), "LT");
            $pdf->Cell(20, 6, utf8_decode(lang("importe")), "T", 0, "C");
            $pdf->Cell(20, 6, utf8_decode(lang("saldo")), "T", 0, "C");
            $pdf->Cell(25, 6, utf8_decode(lang("vencimiento")), "TR", 0, "C");
            $pdf->Ln();
            $pdf->SetFont("arial", "", 10);
            foreach ($arrCtacte as $ctacte) {
                $pdf->Cell(120, 6, utf8_decode(substr($ctacte['descripcion'], 0, 68)), "L");
                $pdf->Cell(20, 6, $ctacte['importe'], "", 0, "C");
                $pdf->Cell(20, 6, $ctacte['saldo'], "", 0, "C");
                $pdf->Cell(25, 6, $ctacte['fechavenc'], "R", 0, "C");
                if ($pdf->GetY() >= 260) {
                    $this->agregarPiePagina($pdf, $conexion);
                    $pdf->AddPage("P", "A4");
                    $pdf->SetFont('arial', '', 10);
                } else {
                    $pdf->Ln();
                }
            }
            $pdf->Cell(185, 0, "", "T");
            $this->agregarPiePagina($pdf, $conexion);
        }
        $conexion = $this->load->database("default", true);
        $arrResp = array();
        $myImpresion = new impresiones($conexion, $this->codigofilial);
        if (!$myImpresion->printerPDF($conexion, 3, $pdf, $printerID)) {
            $arrResp["error"] = "ha ocurrido un error al enviar el trabajo de impresion con el mensaje {$myImpresion->getError()}";
        } else {
            $arrResp['success'] = "success";
        }
        return $arrResp;
    }

    /**
     * Imprime estado academico
     *
     * @param int $arrayCodigos
     * @param string $printerID
     * @param int $cantidadCopias
     * @return array
     */
    public function estado_academico($arrayCodigos, $printerID = null, $cantidadCopias = null) {
        $cantidadCopias = $this->getCantidadCopias(4, $cantidadCopias);
        $conexion = $this->load->database($this->codigofilial, true);
        $cod_alumno = '';
        $cod_plan_academico = '';
        $cod_matricula_periodo = isset($arrayCodigos['cod_matricula_periodo']) ? $arrayCodigos['cod_matricula_periodo'] : '';
        $this->load->helper('impresiones');
        $data["estados"] = $this->Model_estadoacademico->getEstadosMaterias();
        if ($cod_matricula_periodo != '') {
            $myMatPer = new Vmatriculas_periodos($conexion, $cod_matricula_periodo);
            $myMatricula = new Vmatriculas($conexion, $myMatPer->cod_matricula);
            $cod_alumno = $myMatricula->cod_alumno;
            $cod_plan_academico = $myMatricula->cod_plan_academico;
            $cod_tipo_periodo = $myMatPer->cod_tipo_periodo;
            $data["curso"] = $this->Model_planes_academicos->getCurso($cod_plan_academico);
            $data["periodos"] = $this->Model_alumnos->getDetalleMateriasPlan($cod_alumno, $cod_plan_academico, $cod_tipo_periodo, true);
        } else {
            $cod_alumno = isset($arrayCodigos['cod_alumno']) ? $arrayCodigos['cod_alumno'] : '';
            $cod_plan_academico = isset($arrayCodigos['cod_plan_academico']) ? $arrayCodigos['cod_plan_academico'] : '';
            $data["curso"] = $this->Model_planes_academicos->getCurso($cod_plan_academico);
            $data["periodos"] = $this->Model_alumnos->getDetalleMateriasPlan($cod_alumno, $cod_plan_academico, null, true);
        }
        $objAlumno = $this->Model_alumnos->getAlumno($cod_alumno);
        $data["nombreAlumno"] = formatearNombreApellido($objAlumno->nombre, $objAlumno->apellido);
        $pdf = new PDF_AutoPrint('P', 'mm', 'A4');
        $pdf->AutoPrint(false);
        $pdf->SetLeftMargin(20);
        $pdf->SetRightMargin(25);
        for ($cantCopias = 0; $cantCopias < $cantidadCopias; $cantCopias++) {
            $this->setPapelMembretado($pdf);
            $pdf->SetFont('arial', 'B', 12);
            $pdf->AddPage('P', 'A4');
            $pdf->Cell(0, 6, utf8_decode(lang("estado_academico")), 0, 0, "C");
            $pdf->Ln(8);
            $pdf->SetFont("arial", "B", 10);
            $pdf->Cell(40, 6, utf8_decode(lang("nombre_curso") . ':'));
            $pdf->SetFont("arial", "", 10);
            switch (get_idioma()) {
                case 'es':
                    $pdf->Cell(40, 6, utf8_decode($data['curso']->nombre_es));
                    break;

                case 'pt':
                    $pdf->Cell(40, 6, utf8_decode($data['curso']->nombre_pt));
                    break;

                case 'in':
                    $pdf->Cell(40, 6, utf8_decode($data['curso']->nombre_in));
                    break;

                default:
                    break;
            }
            $pdf->Ln(8);
            $pdf->SetFont("arial", "B", 10);
            $pdf->Cell(40, 6, utf8_decode(lang("nombre_del_alumno") . ':'));
            $pdf->SetFont("arial", "", 10);
            $pdf->Cell(115, 6, utf8_decode($data['nombreAlumno']));
            $pdf->Cell(60, 6, formatearFecha_pais(date("Y-m-d")));
            $pdf->Ln(8);
            foreach ($data['periodos'] as $periodo => $estados) {
                $pdf->SetFont("arial", "B", 10);
                $pdf->Ln();
                $pdf->Cell(40, 6, utf8_decode(lang($periodo)));
                $pdf->Ln();
                $pdf->SetFont("arial", "", 10);
                foreach ($estados['materias'] as $materia) {
                    formatearStringCelda($materia, $pdf, 26);
                    if ($pdf->GetY() >= 260) {
                        $this->agregarPiePagina($pdf, $conexion);
                        $pdf->AddPage("P", "A4");
                        $pdf->SetFont('arial', '', 10);
                    } else {
                        $pdf->Ln();
                    }
                }
            }
            $this->agregarPiePagina($pdf, $conexion);
        }
        $conexion = $this->load->database("default", true);
        $arrResp = array();
        $myImpresion = new impresiones($conexion, $this->codigofilial);
        if (!$myImpresion->printerPDF($conexion, 4, $pdf, $printerID)) {
            $arrResp["error"] = "ha ocurrido un error al enviar el trabajo de impresion con el mensaje {$myImpresion->getError()}";
        } else {
            $arrResp['success'] = "success";
        }
        return $arrResp;
    }

    /**
     * Imprime inscriptos a examenes
     *
     * @param int $cod_examen
     * @param string $printerID
     * @param int $cantidadCopias
     * @return array
     */
    function inscriptos_a_examenes($cod_examen, $printerID = null, $cantidadCopias = null) {
        $cantidadCopias = $this->getCantidadCopias(6, $cantidadCopias);
        $this->load->helper('comisiones');
        $conexion = $this->load->database($this->codigofilial, true);
        $curso = $this->Model_examenes->getCursoInscriptos($cod_examen);
        $inscriptos = $this->Model_examenes->getInscriptosExamenes($cod_examen);
        $datosExamen = $this->Model_examenes->getDatosInscribirExamen($cod_examen);
        $alumnosInscribir = $this->Model_examenes->getAlumnosInscribirExamen($cod_examen);
        $myExamen = new Vexamenes($conexion, $cod_examen);
        if ($myExamen->tipoexamen == "PARCIAL") {
            $arrComision = $this->Model_examenes->getComisionCursoExamenParcial($cod_examen);
            $myComision = new Vcomisiones($conexion, $arrComision[0]['cod_comision']);
            $nombreComision = $myComision->nombre;
        }
        $idioma = get_idioma();
        if ($idioma == "es") {
            $idxNombre = "nombre_es";
        } else if ($idioma == "in") {
            $idxNombre = "nombre_in";
        } else {
            $idxNombre = "nombre_pt";
        }
        $data['curso'] = $curso;
        $data['inscriptos'] = $inscriptos;
        $data['datosExamen'] = $datosExamen;
        $data['alumnosInscribir'] = $alumnosInscribir;
        $pdf = new PDF_AutoPrint('P', 'mm', 'A4');
        $pdf->AutoPrint(false);
        for ($cantCopias = 0; $cantCopias < $cantidadCopias; $cantCopias++) {
            $this->setPapelMembretado($pdf);
            $pdf->SetFont('arial', '', 10);
            $pdf->AddPage('P', 'A4');
            $pdf->Cell(160, 6, utf8_decode(lang("fecha_emision")) . " " . formatearFecha_pais(date("Y-m-d")), 0, 0, "R");
            $pdf->Ln(12);
            if ($myExamen->tipoexamen == "PARCIAL") {
                $pdf->SetFont("arial", 'B', 10);
                $pdf->Cell(30);
                $pdf->Cell(20, 6, utf8_decode(lang("curso")));
                $pdf->SetFont("arial", '', 10);
                $pdf->Cell(0, 6, utf8_decode($data['curso'][0][$idxNombre]));
                $pdf->Ln();
            }
            $pdf->SetFont("arial", "B", 10);
            $pdf->Cell(30);
            $pdf->Cell(20, 6, utf8_decode(lang("materia")));
            $pdf->SetFont("arial", "", 10);
            $pdf->Cell(0, 6, utf8_decode($data['datosExamen'][0][$idxNombre]));
            $pdf->Ln();
            if ($myExamen->tipoexamen == "PARCIAL") {
                $pdf->SetFont("arial", 'B', 10);
                $pdf->Cell(30);
                $pdf->Cell(20, 6, utf8_decode(lang("comision")));
                $pdf->SetFont("arial", '', 10);
                $pdf->Cell(0, 6, utf8_decode($nombreComision));
                $pdf->Ln();
            }
            $pdf->SetFont("arial", "B", 10);
            $pdf->Cell(30);
            $pdf->Cell(20, 6, utf8_decode(lang("fecha")));
            $pdf->SetFont("arial", "", 10);
            $pdf->Cell(30, 6, formatearFecha_pais($data['datosExamen'][0]['fecha']));
            $pdf->SetFont("arial", "B", 10);
            $pdf->Cell(10, 6, utf8_decode(lang("hora")));
            $pdf->SetFont("arial", "", 10);
            $pdf->Cell(20, 6, formatearFecha_pais($data['datosExamen'][0]['hora']));
            $pdf->Ln(18);
            $pdf->SetFont("arial", "B", 12);
            $pdf->Cell(10);
            $pdf->Cell(0, 6, utf8_decode(lang("listado_de_alumnos")));
            $pdf->Ln(8);
            $pdf->SetFont("arial", "", 10);
            foreach ($data['inscriptos'] as $inscriptos) {
                $pdf->Cell(15);
                $pdf->Cell(0, 6, utf8_decode("{$inscriptos['nombre_apellido']},"));
                if ($pdf->GetY() >= 260) {
                    $this->agregarPiePagina($pdf, $conexion);
                    $pdf->AddPage("P", "A4");
                    $pdf->SetFont('arial', '', 10);
                } else {
                    $pdf->Ln();
                }
            }
            $this->agregarPiePagina($pdf, $conexion);
        }
        $conexion = $this->load->database("default", true);
        $myImpresion = new impresiones($conexion, $this->codigofilial);
        $arrResp = array();
        if (!$myImpresion->printerPDF($conexion, 6, $pdf, $printerID)) {
            $arrResp["error"] = "ha ocurrido un error al enviar el trabajo de impresion con el mensaje {$myImpresion->getError()}";
        } else {
            $arrResp['success'] = "success";
        }
        return $arrResp;
    }

    /**
     * Imprime constancia de examenes
     *
     * @param array $arrCodigosInscripcion
     * @param string $printerID
     * @param int $cantidadCopias
     * @return array
     */
    function constancia_examen(array $arrCodigosInscripcion, $printerID = null, $cantidadCopias = null) {
        $cantidadCopias = $this->getCantidadCopias(7, $cantidadCopias);
        $conexion = $this->load->database($this->codigofilial, true);
        $this->load->helper('alumnos');
        $idioma = get_idioma();
        if ($idioma == "es") {
            $idxNombre = "nombre_es";
        } else if ($idioma == "in") {
            $idxNombre = "in";
        } else {
            $idxNombre = "nombre_in";
        }
        $pdf = new PDF_AutoPrint('P', 'mm', 'A4');
        $pdf->AutoPrint(false);
        for ($cantCopias = 0; $cantCopias < $cantidadCopias; $cantCopias++) {
            $pdf->AddPage('P', 'A4');
            foreach ($arrCodigosInscripcion as $inscripcion) {
                if ($pdf->GetY() >= 260) {
                    $pdf->AddPage("P", "A4");
                }
                $pdf->SetFont('arial', 'B', 12);
                $myExamenInscripcion = new Vexamenes_estado_academico($conexion, $inscripcion);
                $myEstadoAcademico = new Vestadoacademico($conexion, $myExamenInscripcion->cod_estado_academico);
                $myMatriculaPeriodo = new Vmatriculas_periodos($conexion, $myEstadoAcademico->cod_matricula_periodo);
                $datosExamen = $this->Model_examenes->getDatosInscribirExamen($myExamenInscripcion->cod_examen);
                $myExamen = new Vexamenes($conexion, $myExamenInscripcion->cod_examen);
                if ($myExamen->tipoexamen == "PARCIAL" || $myExamen->tipoexamen == 'RECUPERATORIO PARCIAL') {
                    $curso = $this->Model_examenes->getCursoInscriptos($myExamenInscripcion->cod_examen);
                } else {
                    $arrProfesores = $myExamen->getProfesoresExamen();
                }
                $myMatricula = new Vmatriculas($conexion, $myMatriculaPeriodo->cod_matricula);
                $myAlumno = $myMatricula->getAlumno();
                $pdf->Cell(0, 6, utf8_decode(lang("inscripcion_mesas_de_examenes")), 0, 0, "C");
                $pdf->Ln(10);
                $pdf->SetFont('arial', "", 10);
                $pdf->Cell(140, 6, utf8_decode(lang("fecha_de_inscripcion")), 0, 0, "R");
                $pdf->SetFont("arial", "B", 10);
                $pdf->Cell(0, 6, formatearFecha_pais($myExamenInscripcion->fechadeinscripcion));
                $pdf->Ln(8);
                $pdf->Cell(35, 6, utf8_decode(lang("datos_del_alumno")));
                $pdf->SetFont("arial", "", 10);
                $ape = inicialesMayusculas($myAlumno->apellido);
                $nom = inicialesMayusculas($myAlumno->nombre);
                $pdf->Cell(90, 6, "{$ape}, {$nom}");
                $pdf->SetFont("arial", "B", 10);
                $pdf->Ln();
                $pdf->Cell(0, 6, funciones::formatearDocumentos($conexion, $myAlumno->tipo, $myAlumno->documento));
                $pdf->Ln();
                $pdf->SetFont('arial', "B", 10);
                $pdf->Cell(15, 6, utf8_decode(lang("materia")));
                $pdf->SetFont('arial', "", 10);
                $pdf->Cell(90, 6, utf8_decode($datosExamen[0][$idxNombre]));
                if ($myExamen->tipoexamen == 'FINAL' || $myExamen->tipoexamen == 'RECUPERATORIO FINAL') {
                    $pdf->SetFont("arial", "B", 10);
                    $pdf->Cell(20, 6, utf8_decode(lang("profesores")));
                    $pdf->SetFont("arial", "", 10);
                    $myProfesor = new Vprofesores($conexion, $arrProfesores[0]['codprofesor']);
                    $pdf->Cell(0, 6, utf8_decode("{$myProfesor->apellido}, {$myProfesor->nombre}"));
                    $pdf->Ln();
                    if (count($arrProfesores) > 1) {
                        for ($i = 1; $i < count($arrProfesores); $i++) {
                            $myProfesor = new Vprofesores($conexion, $arrProfesores[$i]['codprofesor']);
                            $pdf->Cell(125);
                            $pdf->Cell(0, 6, utf8_decode("{$myProfesor->apellido}, {$myProfesor->nombre}"));
                            $pdf->Ln();
                        }
                    }
                }
                $pdf->SetFont("arial", "B", 10);
                $pdf->Cell(25, 6, utf8_decode(lang("fecha_examen")));
                $pdf->SetFont("arial", "", 10);
                $pdf->Cell(80, 6, formatearFecha_pais($myExamen->fecha));
                $pdf->SetFont("arial", "B", 10);
                $pdf->Cell(20, 6, utf8_decode(lang("hora")));
                $pdf->SetFont("arial", "", 10);
                $pdf->Cell(0, 6, substr($myExamen->hora, 0, 5));
                $pdf->Ln();
                if ($myExamen->tipoexamen == "PARCIAL") {
                    $pdf->SetFont("arial", "B", 10);
                    $pdf->Cell(25, 6, utf8_decode(lang("curso")));
                    $pdf->SetFont("arial", "", 10);
                    $pdf->Cell(0, 6, utf8_decode($curso[0][$idxNombre]));
                }
                if ($pdf->GetY() >= 240) {
                    $pdf->AddPage("P", "A4");
                } else {
                    $pdf->Ln(10);
                    $pdf->Cell(0, 0, "", "T");
                    $pdf->Ln(8);
                }
                $pdf->SetFont("arial", "B", 12);
                $pdf->Cell(0, 6, utf8_decode(lang("constancia_de_inscripcion_para_el_alumno")), 0, 0, "C");
                $pdf->Ln(10);
                $pdf->SetFont("arial", "B", 10);
                $pdf->Cell(35, 6, utf8_decode(lang("datos_del_alumno")));
                $pdf->SetFont("arial", '', 10);
                $apellido = inicialesMayusculas($myAlumno->apellido);
                $nombre = inicialesMayusculas($myAlumno->nombre);
                $pdf->Cell(0, 6, utf8_decode("{$apellido}, {$nombre}"));
                $pdf->Ln();
                $pdf->SetFont("arial", "B", 10);
                $pdf->Cell(15, 6, utf8_decode(lang("materia")));
                $pdf->SetFont("arial", "", 10);
                $pdf->Cell(0, 6, utf8_decode($datosExamen[0][$idxNombre]));
                $pdf->Ln();
                $pdf->SetFont("arial", "B", 10);
                $pdf->Cell(30, 6, utf8_decode(lang("fecha_examen")));
                $pdf->SetFont("arial", "", 10);
                $pdf->Cell(80, 6, formatearFecha_pais($myExamen->fecha));
                $pdf->SetFont("arial", "B", 10);
                $pdf->Cell(10, 6, utf8_decode(lang("hora")));
                $pdf->SetFont("arial", "", 10);
                $pdf->Cell(0, 6, substr($myExamen->hora, 0, 5));
                $pdf->Ln(10);
                $pdf->Cell(0, 0, "", "T");
                $pdf->Ln(10);
            }
        }
        $conexion = $this->load->database("default", true);
        $arrResp = array();
        $myImpresion = new impresiones($conexion, $this->codigofilial);
        if (!$myImpresion->printerPDF($conexion, 7, $pdf, $printerID)) {
            $arrResp["error"] = "ha ocurrido un error al enviar el trabajo de impresion con el mensaje {$myImpresion->getError()}";
        } else {
            $arrResp['success'] = "success";
        }
        return $arrResp;
    }

    /**
     * imprime cata volante
     *
     * @param int $codExamen
     * @param string $printerID
     * @param int $cantidadCopias
     * @return array
     */
    function acta_volante($codExamen, $printerID = null, $cantidadCopias = null,$notaAprueba = null, $notas_parciales = false, $impresion_estado_deuda = false) {
        $cantidadCopias = $this->getCantidadCopias(8, $cantidadCopias);
        $conexion = $this->load->database($this->codigofilial, true);
        $this->load->helper('alumnos');
        $this->load->helper('comisiones');
        $idioma = get_idioma();
        if ($idioma == "es") {
            $idxNombre = "nombre_es";
        } else if ($idioma == "in") {
            $idxNombre = "in";
        } else {
            $idxNombre = "nombre_in";
        }
        $myExamen = new Vexamenes($conexion, $codExamen);
        $inscriptos = $this->Model_examenes->getInscriptosExamenes($codExamen);
        $materia = $myExamen->materia;
        $datosExamen = $this->Model_examenes->getDatosInscribirExamen($codExamen);
        if ($myExamen->tipoexamen == 'FINAL' || $myExamen->tipoexamen == 'RECUPERATORIO_FINAL') {
            $arrProfesores = $myExamen->getProfesoresExamen();
            $myProfesor = new Vprofesores($conexion, $arrProfesores[0]['codprofesor']);
        }
        if ($myExamen->tipoexamen == "PARCIAL" || $myExamen->tipoexamen == "RECUPERATORIO_PARCIAL") {
            $curso = $this->Model_examenes->getCursoInscriptos($codExamen);
            $comisionesCursoParcial = $this->Model_examenes->getComisionCursoExamenParcial($codExamen);
        }
        $pdf = new PDF_AutoPrint('P', 'mm', 'A4');
        $pdf->AutoPrint(false);
        for ($cantCopias = 0; $cantCopias < $cantidadCopias; $cantCopias++) {
            $this->setPapelMembretado($pdf);
            $pdf->AddPage('P', 'A4');
            $pdf->SetFont("arial", "", 10);
            $pdf->Cell(180, 6, utf8_decode(lang("fecha_emision")) . " " . formatearFecha_pais(date("Y-m-d")), 0, 0, "R");
            $pdf->Ln(10);
            $pdf->SetFont("arial", "B", 12);
            $pdf->Cell(0, 6, utf8_decode(lang("acta_volante")), 0, 0, "C");
            $pdf->Ln(10);
            $pdf->Cell(1);
            $pdf->SetFont("arial", "B", 10);
            $pdf->Cell(25, 6, utf8_decode(lang("codigo")));
            $pdf->SetFont("arial", "", 10);
            $pdf->Cell(0, 6, $myExamen->getCodigo());
            $pdf->Ln();
            if ($myExamen->tipoexamen == "PARCIAL" || $myExamen->tipoexamen == "RECUPERATORIO_PARCIAL") {
                $pdf->Cell(1);
                $pdf->SetFont("arial", "B", 10);
                $pdf->Cell(25, 6, utf8_decode(lang("curso")));
                $pdf->SetFont("arial", "", 10);
                $pdf->Cell(0, 6, inicialesMayusculas(utf8_decode($curso[0][$idxNombre])));
                $pdf->Ln();
            }
            $pdf->Cell(1);
            $pdf->SetFont("arial", "B", 10);
            $pdf->Cell(25, 6, utf8_decode(lang("materia")));
            $pdf->SetFont("arial", "", 10);
            $pdf->Cell(0, 6, utf8_decode($datosExamen[0][$idxNombre]));
            $pdf->Ln();
            if ($myExamen->tipoexamen == 'FINAL' || $myExamen->tipoexamen == 'RECUPERATORIO_FINAL') {
                $pdf->Cell(1);
                $pdf->SetFont("arial", "B", 10);
                $pdf->Cell(25, 6, utf8_decode(lang("profesores")));
                $pdf->SetFont("arial", "", 10);
                if($myProfesor->apellido == ""){ $nombre_apellido = $myProfesor->nombre;}
                else { $nombre_apellido = $myProfesor->apellido.", ".$myProfesor->nombre; }
                $pdf->Cell(0, 6, inicialesMayusculas(utf8_decode($nombre_apellido)));
                $pdf->Ln();
                if (count($arrProfesores) > 1) {
                    for ($i = 1; $i < count($arrProfesores); $i++) {
                        $myProfesor = new Vprofesores($conexion, $arrProfesores[$i]['codprofesor']);
                        $pdf->Cell(1);
                        if($myProfesor->apellido == ""){ $nombre_apellido = $myProfesor->nombre;}
                        else { $nombre_apellido = $myProfesor->apellido.", ".$myProfesor->nombre; }
                        $pdf->Cell(1, 6, utf8_decode($nombre_apellido));
                        $pdf->Ln();
                    }
                }
            } else {
                $pdf->Cell(1);
                $pdf->SetFont("arial", "B", 10);
                $pdf->Cell(25, 6, utf8_decode(lang("comision_comisiones")));
                $pdf->SetFont("arial", "", 10);
            }
            if ($myExamen->tipoexamen == 'PARCIAL' || $myExamen->tipoexamen == 'RECUPERATORIO_PARCIAL') {
                foreach ($comisionesCursoParcial as $comision) {
                    $myComision = new Vcomisiones($conexion, $comision['cod_comision']);
                    $nombreComision = $myComision->nombre;
                    $pdf->Cell(1);
                    $pdf->Cell(2, 6, utf8_decode($nombreComision));
                    $pdf->Ln();
                }
            }
            $pdf->Cell(1);
            $pdf->SetFont("arial", "B", 10);
            $pdf->Cell(30, 6, utf8_decode(lang("fecha_examen")));
            $pdf->SetFont("arial", "", 10);
            $pdf->Cell(0, 6, formatearFecha_pais($myExamen->fecha));
            $pdf->Ln(10);
            $pdf->SetFont("arial", 'B', 8);



            if(!$notas_parciales){
                $pdf->Cell(132);
                $pdf->Cell(45, 6, utf8_decode(lang("calificacion")), "LTR", 0, "C");
                $w = array(10,30,60,22,12,10,15,15,15);

            }
            else {
                $pdf->Cell(122);
                $pdf->Cell(22, 6, utf8_decode(lang("calificacion")), "LTR", 0, "C");
                $pdf->Cell(35, 6, utf8_decode(/*lang("calificacion")*/ "Calificación final"), "LTR", 0, "C");
                $w = array(10,30,60,12,10,10,12,8,15);
            }

            if($notas_parciales && $impresion_estado_deuda)
            {
                $w = array(8,25,51,10,8,10,11,10,14);
            }

            if(!$notas_parciales && $impresion_estado_deuda)
            {
                $w = array(10,30,50,14,10,10,15,15,15);
            }

            $pdf->Ln();
            $pdf->Cell($w[0], 6, utf8_decode(lang("nro")), "LT", 0, "C");
            $pdf->Cell($w[1], 6, utf8_decode(ucfirst(lang("documento"))), "LT", 0, "C");
            $pdf->Cell($w[2], 6, utf8_decode(lang("Alumno")), "LT", 0, "C");
            $pdf->Cell($w[3], 6, utf8_decode(strtoupper(lang("asis"))), "LT", 0, "C");
            //$pdf->Cell($w[4], 6, utf8_decode(strtoupper(lang("tipo"))), "LT", 0, "C");
            $pdf->Cell($w[5], 6, utf8_decode(strtoupper(lang("cond"))), "LT", 0, "C");
            if($impresion_estado_deuda) {$pdf->Cell(18, 6, utf8_decode("Deuda"), "LT", 0, "C");}
            if($notas_parciales) {$pdf->Cell(22, 6, utf8_decode("Parciales"), "LT", 0, "C");}
            $pdf->Cell($w[6], 6, utf8_decode(lang("escrito")), "LT", 0, "C");
            $pdf->Cell($w[7], 6, utf8_decode(lang("oral")), "LT", 0, "C");
            $pdf->Cell($w[8], 6, utf8_decode(lang("definitivo")), "LTR", 0, "C");
            $pdf->Ln();
            $pdf->SetFont("arial", '', 10);
            $cantidadAusentes = 0;
            $cantidadNoAprobaros = 0;
            $cantidadAprobaron = 0;
            foreach ($inscriptos as $key => $alumno) {
                $myAlumno = new Valumnos($conexion, $alumno['cod_alumno']);

                $myExamenEstadoAcademico = new Vexamenes_estado_academico($conexion, $alumno['codigo']);
                $myEstadoAcademico = new Vestadoacademico($conexion, $myExamenEstadoAcademico->cod_estado_academico);
                $notasParciales = $myEstadoAcademico->getNotasParciales($materia);
                $estadoAcademico = strtoupper(substr($myEstadoAcademico->estado, 0, 1));
                $estadoAcademico = $estadoAcademico=='L'?'NR':$estadoAcademico;//Ticket IGAC-492 Todos los estados Libres cambiar por No regular, el estado Libre no existe
                $estadoDeuda = Vctacte::checkMorasAlumnoCampusExamenes($conexion, $myAlumno->getCodigo());
                if(floatval($estadoDeuda[0]['saldo']) > 1)
                {
                    $estadoDeuda = lang('debe_ctacte');
                }
                else
                {
                    $estadoDeuda = lang('no_debe_ctacte');
                }

                $notaEscrito = isset($alumno['notas'][2]['nota']) ? $alumno['notas'][2]['nota'] : '';
                $notaOral = isset($alumno['notas'][1]['nota']) ? $alumno['notas'][1]['nota'] : '';
                $notaDefinitivo = isset($alumno['notas'][0]['nota']) ? $alumno['notas'][0]['nota'] : '';
                switch ($alumno['ausente']) {
                    case 'ausente':
                        $cantidadAusentes++;
                        break;

                    case 'aprobado':
                        $cantidadAprobaron++;
                        break;

                    case 'reprobado':
                        $cantidadNoAprobaros++;
                        break;
                }

                $pdf->Cell($w[0], 6, $key + 1, "LT", 0, "C");
                $pdf->Cell($w[1], 6, $myAlumno->documento, "LT", 0, "C");
                $apellido = inicialesMayusculas($myAlumno->apellido);
                $nombre = inicialesMayusculas($myAlumno->nombre);
                $pdf->Cell($w[2], 6, substr(utf8_decode("{$apellido}, {$nombre}"), 0, 32), "LT", 0, "C");
                $pdf->Cell($w[3], 6, $alumno['porcasistencia'], "LT", 0, "C");
                //$pdf->Cell($w[4], 6, "", "LT", 0, "C");
                $pdf->Cell($w[5], 6, $estadoAcademico, "LT", 0, "C");
                if($impresion_estado_deuda) {$pdf->Cell(18, 6, utf8_decode($estadoDeuda), "LT", 0, "C");}
                $notas = isset($notasParciales[0]['nota']) ? $notasParciales[0]['nota'] : "";
                $notas .= isset($notasParciales[1]['nota']) ? " | ".$notasParciales[1]['nota'] : "";
                if($notas_parciales) {$pdf->Cell(22, 6, $notas , "LT", 0, "C");}
                $pdf->Cell($w[6], 6, $notaEscrito, "LT", 0, "C");
                $pdf->Cell($w[7], 6, $notaOral, "LT", 0, "C");
                $pdf->Cell($w[8], 6, $notaDefinitivo, "LTRB", 0, "C");
                $pdf->Ln();
            }
            $pdf->Cell(175, 0, "", "T");
            $pdf->Ln();
            $referencia = 'Referencia: R =' . lang('regular') . ', ' . 'NR =' . lang('libre');
            $pdf->Cell(0, 6, utf8_decode($referencia), 0, 0, "C");
            $pdf->Ln(4);
            $pdf->Ln(4);
            $pdf->SetFont("arial", "B", 10);
            $pdf->Cell(12, 6, utf8_decode(lang("total")) . ":");
            $pdf->SetFont("arial", "", 10);
            $pdf->Cell(20, 6, count($inscriptos));
            if ($cantidadAprobaron <> 0 || $cantidadAusentes <> 0 || $cantidadNoAprobaros <> 0) {
                $pdf->SetFont("arial", "B", 10);
                $pdf->Cell(20, 6, utf8_decode(lang("aprobaron")) . ":");
                $pdf->SetFont("arial", "", 10);
                $pdf->Cell(20, 6, $cantidadAprobaron);
                $pdf->SetFont("arial", "B", 10);
                $pdf->Cell(27, 6, utf8_decode(lang("no_aprobaron")) . ":");
                $pdf->SetFont("arial", "", 10);
                $pdf->Cell(20, 6, $cantidadNoAprobaros);
                $pdf->SetFont("arial", "B", 10);
                $pdf->Cell(20, 6, utf8_decode(lang("ausentes")) . ":");
                $pdf->SetFont("arial", "", 10);
                $pdf->Cell(20, 6, $cantidadAusentes);
            }
            $pdf->Ln();
            $pdf->SetFont("arial", "B", 10);
            $pdf->Cell(30, 6, utf8_decode(lang("hora_inicio_examen")));
            $pdf->SetFont("arial", "", 10);
            $pdf->Cell(40, 6, substr($myExamen->hora, 0, 5));
            $pdf->Ln();
            $pdf->SetFont("arial", "B", 10);
            $pdf->Cell(30, 6, utf8_decode(lang("hora_finalizacion")));
            $pdf->SetFont("arial", "", 0);
            $pdf->Cell(40, 6, substr($myExamen->horafin, 0, 5));
            $pdf->Ln(30);
            $pdf->Cell(20, 0, "");
            $pdf->Cell(50, 0, "", "T");
            $pdf->Cell(30, 0, "");
            $pdf->Cell(50, 0, "", "T");
            $pdf->Ln(3);
            $pdf->Cell(20, 0, "");
            $pdf->Cell(50, 0, utf8_decode(lang("firma")), 0, 0, "C");
            $pdf->Cell(30, 0, "");
            $pdf->Cell(50, 0, utf8_decode(lang("aclaracion")), 0, 0, "C");
            $this->agregarPiePagina($pdf, $conexion);
        }
        $conexion = $this->load->database("default", true);
        $myImpresion = new impresiones($conexion, $this->codigofilial);
        $arrResp = array();
        if (!$myImpresion->printerPDF($conexion, 8, $pdf, $printerID)) {
            $arrResp["error"] = "ha ocurrido un error al enviar el trabajo de impresion con el mensaje {$myImpresion->getError()}";
        } else {
            $arrResp['success'] = "success";
        }
        return $arrResp;
    }

    /**
     * Imprime Recibo de cobro
     *
     * @param array $arrCodCobros
     * @param string $printerID
     * @param int $cantidadCopias
     * @return string
     */
    public function recibo_cobros(array $arrCodCobros, $printerID = null, $cantidadCopias = null, $retornarPDF = false) {
        $conexion = $this->load->database($this->codigofilial, true);
        $arrConfiguracion = $this->Model_configuraciones->getValorConfiguracion(null, "configuracionImpresiones", 10);
        $papel = isset($arrConfiguracion['papel']) ? $arrConfiguracion['papel'] : "A4";
        $imprimirRazon = !isset($arrConfiguracion['imprimir_razon']) || $arrConfiguracion['imprimir_razon'] == 1 ? 1 : 0;
        $muestraCuotasTotales = isset($arrConfiguracion['muestra_total_cuotas']) ? $arrConfiguracion['muestra_total_cuotas'] : 1;
        $html = array();
        foreach ($arrCodCobros as $codCobro) {
            $myCobro = new Vcobros($conexion, $codCobro);
            $myAlumno = new Valumnos($conexion, $myCobro->cod_alumno);
            $razonDefault = $myAlumno->getRazonSocialDefault();
            $myRazonSocial = new Vrazones_sociales($conexion, $razonDefault[0]['cod_razon_social']);

            $total = 0;
            $cantidadLineas = 0;
            $arrValores = array();
            $arrValores['[!--FECHAFACTURA--]'] = formatearFecha_pais($myCobro->fechareal);
            $arrValores['[!--RAZONSOCIALFACTURA--]'] = $imprimirRazon == 1 ? utf8_decode($myRazonSocial->razon_social) : "";
            $arrValores['[!--CONDICIONFACTURA--]'] = "";
            $arrValores['[!--DOMICILIOFACTURA--]'] = $imprimirRazon == 1 ? $myRazonSocial->direccion_calle . " " . $myRazonSocial->direccion_numero : "";
            switch ($myRazonSocial->tipo_documentos) {
                case "23":
                    $documento = $myRazonSocial->documento;
                    $documento = substr($documento, 0, strlen($documento) - 1) . "-" . substr($documento, strlen($documento) - 1);
                    break;

                default:
                    $documento = $myRazonSocial->documento;
                    break;
            }
            $arrValores['[!--NUMEROIDENTIFICADORFISCAL--]'] = $imprimirRazon == 1 ? funciones::formatearNumeroDocumneto($myRazonSocial->documento, $myRazonSocial->tipo_documentos) : "";
            $arrRenglones = $myCobro->getImputaciones();
            foreach ($arrRenglones as $renglon) {
                $condicion = array('codigo' => $renglon['cod_ctacte']);
                $ctacte = Vctacte::listarCtacte($conexion, $condicion);
                formatearCtaCte($conexion, $ctacte, $muestraCuotasTotales);
                $descripcion = utf8_decode($ctacte[0]['descripcion']);
                $descripcion = substr($descripcion, 0, 59);
                $importe = $renglon['valor'];
                $total += $importe;
                $cantidadLineas++;
                $arrValores["[!--DESCRIPCIONFACTURALINEA{$cantidadLineas}--]"] = ($descripcion);
                $arrValores["[!--MONTOFACTURALINEA{$cantidadLineas}--]"] = formatearImporte($importe);
            }
            for ($i = $cantidadLineas + 1; $i < 20; $i++) {
                $arrValores["[!--DESCRIPCIONFACTURALINEA{$i}--]"] = "";
                $arrValores["[!--MONTOFACTURALINEA{$i}--]"] = "";
            }
            for ($i = 0; $i < 10; $i++) {
                $arrValores["[!--NOMBREIMPUESTO{$i}--]"] = "";
                $arrValores["[!--VALORIMPUESTO{$i}--]"] = "";
            }
            $arrValores['[!--SUBTOTALFACTURA--]'] = formatearImporte($total);
            $arrValores['[!--TOTALFACTURA--]'] = formatearImporte($total);
            /* cambiar segun la filial */
            if ($this->codigofilial == 71) { // filial de cochabamba tiene un modelo propio
                $codTemplate = $printerID == -1 || $printerID == null ? 98 : 99;
            } else {
                $codTemplate = $printerID == -1 || $printerID == null ? 90 : 91;
            }
            $myTemplate = new Vtemplates($conexion, $codTemplate);
            $html[] = $myTemplate->html;
            maquetados::desetiquetar($arrValores, $html);
        }
        $papel = "A4"; // no se porque se cambio el tipo de papel es A4 en luager de A5 o A4 segun la configuración
        $myJSONPDF = new json_templates("P", "mm", $papel);
        $myJSONPDF->setJSON($html);
        $pdf = $myJSONPDF->Output("pdf", $cantidadCopias);
        if ($retornarPDF){
            return $pdf;
        } else {
            $myImpresion = new impresiones($conexion, $this->codigofilial);
            if (!$myImpresion->printerPDF($conexion, 11, $pdf, $printerID)) {
                $arrResp["error"] = "ha ocurrido un error al enviar el trabajo de impresion con el mensaje {$myImpresion->getError()}";
            } else {
                $arrResp['success'] = "success";
            }
            return $arrResp;
        }
    }

    private function getNroTemplateFacturacion($papel, $tipoFactura, $templateCompleto = false, $printerID = null, $medio = 'convencional', $modeloFacturaElectronica = "fe_inferior") {
        $arrTemp = array();
        if ($medio == 'electronico') {
            if ($printerID != null && $printerID == -1) { // para el navegador existen plantillas diferentes
                if ($this->codigofilial == 20) { // ROSARIO
                    $arrTemp['A5']['3'] = 117;
                    $arrTemp['A4']['3'] = 117;
                    $arrTemp['A5']['2'] = 117;
                    $arrTemp['A4']['2'] = 117;
                    $arrTemp['A5']['1'] = 117;
                    $arrTemp['A4']['1'] = 117;
                } else if ($this->codigofilial == 24) {  // POSADAS
                    $arrTemp['A5']['3'] = 103;
                    $arrTemp['A4']['3'] = 103;
                    $arrTemp['A5']['2'] = 103;
                    $arrTemp['A4']['2'] = 103;
                    $arrTemp['A5']['1'] = 103;
                    $arrTemp['A4']['1'] = 103;
                } else if ($this->codigofilial == 58) {  // 3 DE FEBRERO
                    $arrTemp['A5']['3'] = 105;
                    $arrTemp['A4']['3'] = 105;
                    $arrTemp['A5']['2'] = 105;
                    $arrTemp['A4']['2'] = 105;
                    $arrTemp['A5']['1'] = 105;
                    $arrTemp['A4']['1'] = 105;
                } else if ($this->codigofilial == 72) {  // ALMIRANTE BROWN
                    $arrTemp['A5']['3'] = 105;
                    $arrTemp['A4']['3'] = 105;
                    $arrTemp['A5']['2'] = 105;
                    $arrTemp['A4']['2'] = 105;
                    $arrTemp['A5']['1'] = 105;
                    $arrTemp['A4']['1'] = 105;
                } else if ($this->codigofilial == 34) { // LA PLATA
                    $arrTemp['A5']['3'] = 106;
                    $arrTemp['A4']['3'] = 106;
                    $arrTemp['A5']['2'] = 106;
                    $arrTemp['A4']['2'] = 106;
                    $arrTemp['A5']['1'] = 106;
                    $arrTemp['A4']['1'] = 106;
                } else if ($this->codigofilial == 11) { // RESISTENCIA
                    $arrTemp['A5']['3'] = 107;
                    $arrTemp['A4']['3'] = 107;
                    $arrTemp['A5']['2'] = 107;
                    $arrTemp['A4']['2'] = 107;
                    $arrTemp['A5']['1'] = 107;
                    $arrTemp['A4']['1'] = 107;
                } else if ($this->codigofilial == 4){
                    $arrTemp['A5']['3'] = 105;
                    $arrTemp['A4']['3'] = 105;
                    $arrTemp['A5']['2'] = 105;
                    $arrTemp['A4']['2'] = 105;
                    $arrTemp['A5']['1'] = 105;
                    $arrTemp['A4']['1'] = 105;
                    $arrTemp['folio3']['1'] = 105;
                    $arrTemp['folio3']['1'] = 105;
                    $arrTemp['folio3']['2'] = 105;
                    $arrTemp['folio3']['2'] = 105;
                    $arrTemp['folio3']['3'] = 105;
                    $arrTemp['folio3']['3'] = 105;
                } else {
                    $arrTemp['A5']['3'] = $modeloFacturaElectronica == 'fe_inferior' ? 101 : 100;
                    $arrTemp['A4']['3'] = $modeloFacturaElectronica == 'fe_inferior' ? 101 : 100;
                    $arrTemp['folio1']['3'] = $modeloFacturaElectronica == 'fe_inferior' ? 101 : 100;
                    $arrTemp['folio2']['3'] = $modeloFacturaElectronica == 'fe_inferior' ? 101 : 100;
                    $arrTemp['folio3']['3'] = $modeloFacturaElectronica == 'fe_inferior' ? 101 : 100;
                }
            } else {
                if ($this->codigofilial == 20) { // ROSARIO
                    $arrTemp['A5']['3'] = 116;
                    $arrTemp['A4']['3'] = 116;
                    $arrTemp['A5']['2'] = 116;
                    $arrTemp['A4']['2'] = 116;
                    $arrTemp['A5']['1'] = 116;
                    $arrTemp['A4']['1'] = 116;
                } else if ($this->codigofilial == 24) { // POSADAS
                    $arrTemp['A5']['3'] = 103;
                    $arrTemp['A4']['3'] = 103;
                    $arrTemp['A5']['2'] = 103;
                    $arrTemp['A4']['2'] = 103;
                    $arrTemp['A5']['1'] = 103;
                    $arrTemp['A4']['1'] = 103;
                } else if ($this->codigofilial == 58) { // 3 DE FEBRERO
                    $arrTemp['A5']['3'] = 105;
                    $arrTemp['A4']['3'] = 105;
                    $arrTemp['A5']['2'] = 105;
                    $arrTemp['A4']['2'] = 105;
                    $arrTemp['A5']['1'] = 105;
                    $arrTemp['A4']['1'] = 105;
                } else if ($this->codigofilial == 72) {  // ALMIRANTE BROWN
                    $arrTemp['A5']['3'] = 105;
                    $arrTemp['A4']['3'] = 105;
                    $arrTemp['A5']['2'] = 105;
                    $arrTemp['A4']['2'] = 105;
                    $arrTemp['A5']['1'] = 105;
                    $arrTemp['A4']['1'] = 105;
                } else if ($this->codigofilial == 34) { // LA PLATA
                    $arrTemp['A5']['3'] = 106;
                    $arrTemp['A4']['3'] = 106;
                    $arrTemp['A5']['2'] = 106;
                    $arrTemp['A4']['2'] = 106;
                    $arrTemp['A5']['1'] = 106;
                    $arrTemp['A4']['1'] = 106;
                } else if ($this->codigofilial == 11) { // RESISTENCIA
                    $arrTemp['A5']['3'] = 107;
                    $arrTemp['A4']['3'] = 107;
                    $arrTemp['A5']['2'] = 107;
                    $arrTemp['A4']['2'] = 107;
                    $arrTemp['A5']['1'] = 107;
                    $arrTemp['A4']['1'] = 107;
                } else if ($this->codigofilial == 4){
                    $arrTemp['A5']['3'] = 105;
                    $arrTemp['A4']['3'] = 105;
                    $arrTemp['A5']['2'] = 105;
                    $arrTemp['A4']['2'] = 105;
                    $arrTemp['A5']['1'] = 105;
                    $arrTemp['A4']['1'] = 105;
                    $arrTemp['folio3']['1'] = 105;
                    $arrTemp['folio3']['1'] = 105;
                    $arrTemp['folio3']['2'] = 105;
                    $arrTemp['folio3']['2'] = 105;
                    $arrTemp['folio3']['3'] = 105;
                    $arrTemp['folio3']['3'] = 105;
                } else {
                    $arrTemp['A5']['3'] = $modeloFacturaElectronica == 'fe_inferior' ? 101 : 100;
                    $arrTemp['A4']['3'] = $modeloFacturaElectronica == 'fe_inferior' ? 101 : 100;
                    $arrTemp['folio1']['3'] = $modeloFacturaElectronica == 'fe_inferior' ? 101 : 100;
                    $arrTemp['folio2']['3'] = $modeloFacturaElectronica == 'fe_inferior' ? 101 : 100;
                    $arrTemp['folio3']['3'] = $modeloFacturaElectronica == 'fe_inferior' ? 101 : 100;
                }
            }
        } else {
            if ($printerID != null && $printerID == -1) { // para el navegador existen plantillas diferentes
                if ($this->codigofilial == 36) { // ASUNCION
                    $arrTemp['A4']['13'] = 108;
                    $arrTemp['A5']['13'] = 108;
                } else if ($this->codigofilial == 41){
                    $arrTemp['A4']['2'] = 109;
                    $arrTemp['A5']['2'] = 109;
                } else if ($this->codigofilial == 80){
                    $arrTemp['A5']['3'] = 114;
                    $arrTemp['A4']['3'] = 114;
                    $arrTemp['A5']['1'] = 114;
                    $arrTemp['A4']['1'] = 114;
                    $arrTemp['A5']['2'] = 114;
                    $arrTemp['A4']['2'] = 114;
                } else if ($this->codigofilial == 71) {
                    $arrTemp['A5']['3'] = 115;
                    $arrTemp['A4']['3'] = 115;
                    $arrTemp['A5']['1'] = 115;
                    $arrTemp['A4']['1'] = 115;
                    $arrTemp['A5']['2'] = 115;
                    $arrTemp['A4']['2'] = 115;
                } else if ($this->codigofilial == 29){
                    $arrTemp['A5']['3'] = 69;
                    $arrTemp['A4']['3'] = 128;
                    $arrTemp['A5']['1'] = 71;
                    $arrTemp['A4']['1'] = 72;
                    $arrTemp['A5']['2'] = 73;
                    $arrTemp['A4']['2'] = 74;
                    $arrTemp['folio1']['3'] = 88;
                    $arrTemp['folio2']['3'] = 92;
                    $arrTemp['folio3']['3'] = 93;
                    $arrTemp['folio1']['3'] = 89;
                    $arrTemp['folio2']['3'] = 92;
                    $arrTemp['folio3']['3'] = 94;
                    $arrTemp['A4']['13'] = 97;
                    $arrTemp['A5']['13'] = 96;
                } else if ($this->codigofilial == 34){
                    $arrTemp['A5']['3'] = 106;
                    $arrTemp['A4']['3'] = 106;
                    $arrTemp['A5']['2'] = 106;
                    $arrTemp['A4']['2'] = 106;
                    $arrTemp['A5']['1'] = 106;
                    $arrTemp['A4']['1'] = 106;
                } else if ($this->codigofilial == 11) { // RESISTENCIA
                    $arrTemp['A5']['3'] = 107;
                    $arrTemp['A4']['3'] = 107;
                    $arrTemp['A5']['2'] = 107;
                    $arrTemp['A4']['2'] = 107;
                    $arrTemp['A5']['1'] = 107;
                    $arrTemp['A4']['1'] = 107;
                } else if ($this->codigofilial == 58) {  // 3 DE FEBRERO
                    $arrTemp['A5']['3'] = 105;
                    $arrTemp['A4']['3'] = 105;
                    $arrTemp['A5']['2'] = 105;
                    $arrTemp['A4']['2'] = 105;
                    $arrTemp['A5']['1'] = 105;
                    $arrTemp['A4']['1'] = 105;
                } else if ($templateCompleto) {
                    $arrTemp['A5']['3'] = 75;
                    $arrTemp['A4']['3'] = 76;
                    $arrTemp['A5']['1'] = 77;
                    $arrTemp['A4']['1'] = 78;
                    $arrTemp['A5']['2'] = 79;
                    $arrTemp['A4']['2'] = 80;
                    $arrTemp['folio1']['3'] = 88;
                    $arrTemp['folio2']['3'] = 92;
                    $arrTemp['folio3']['3'] = 93;
                    $arrTemp['A4']['13'] = 97;
                    $arrTemp['A5']['13'] = 96;
                } else if ($this->codigofilial == 4) {
                    $arrTemp['A5']['3'] = 105;
                    $arrTemp['A4']['3'] = 105;
                    $arrTemp['A5']['2'] = 105;
                    $arrTemp['A4']['2'] = 105;
                    $arrTemp['A5']['1'] = 105;
                    $arrTemp['A4']['1'] = 105;
                    $arrTemp['folio3']['1'] = 105;
                    $arrTemp['folio3']['1'] = 105;
                    $arrTemp['folio3']['2'] = 105;
                    $arrTemp['folio3']['2'] = 105;
                    $arrTemp['folio3']['3'] = 105;
                    $arrTemp['folio3']['3'] = 105;
                } else {
                    $arrTemp['A5']['3'] = 69;
                    $arrTemp['A4']['3'] = 70;
                    $arrTemp['A5']['1'] = 71;
                    $arrTemp['A4']['1'] = 72;
                    $arrTemp['A5']['2'] = 73;
                    $arrTemp['A4']['2'] = 74;
                    $arrTemp['folio1']['3'] = 88;
                    $arrTemp['folio2']['3'] = 92;
                    $arrTemp['folio3']['3'] = 93;
                    $arrTemp['folio1']['3'] = 89;
                    $arrTemp['folio2']['3'] = 92;
                    $arrTemp['folio3']['3'] = 94;
                    $arrTemp['A4']['13'] = 97;
                    $arrTemp['A5']['13'] = 96;
                }
            } else {
                if ($this->codigofilial == 36) { // ASUNCION
                    $arrTemp['A4']['13'] = 108;
                    $arrTemp['A5']['13'] = 108;
                } else if ($this->codigofilial == 41){
                    $arrTemp['A4']['2'] = 109;
                    $arrTemp['A5']['2'] = 109;
                } else if ($templateCompleto) {
                    $arrTemp['A5']['3'] = 62;
                    $arrTemp['A4']['3'] = 63;
                    $arrTemp['A5']['1'] = 64;
                    $arrTemp['A4']['1'] = 65;
                    $arrTemp['A5']['2'] = 66;
                    $arrTemp['A4']['2'] = 67;
                    $arrTemp['folio1']['3'] = 89;
                    $arrTemp['folio2']['3'] = 93;
                    $arrTemp['folio3']['3'] = 95;
                    $arrTemp['A4']['13'] = 97;
                    $arrTemp['A5']['13'] = 96;
                } else {
                    $arrTemp['A5']['3'] = 55;
                    $arrTemp['A4']['3'] = 56;
                    $arrTemp['A5']['1'] = 57;
                    $arrTemp['A4']['1'] = 58;
                    $arrTemp['A5']['2'] = 59;
                    $arrTemp['A4']['2'] = 60;
                    $arrTemp['folio1']['3'] = 89;
                    $arrTemp['folio2']['3'] = 93;
                    $arrTemp['folio3']['3'] = 95;
                    $arrTemp['A4']['13'] = 97;
                    $arrTemp['A5']['13'] = 96;
                }
            }
        }
        if (isset($arrTemp[$papel][$tipoFactura])) {
            return $arrTemp[$papel][$tipoFactura];
        } else {
            return $arrTemp[$papel]['3'];
        }
    }

    public function getPDFFacturas(CI_DB_mysqli_driver $conexion, $arrCodigoFactura, $cantidadCopias, $templateCompleto = false, $printerID = null) {

        $arrConfiguracion = $this->Model_configuraciones->getValorConfiguracion(20, null, 11);
        $imprimeRazon = $this->Model_configuraciones->getValorConfiguracion('', 'facturacionNominada', '') == 1;
        $papel = isset($arrConfiguracion['papel']) ? $arrConfiguracion['papel'] : "A4";
        $imprimirRazon = isset($arrConfiguracion['imprimir_razon']) ? $arrConfiguracion['imprimir_razon'] : 1;
        $muestraCuotasTotales = isset($arrConfiguracion['muestra_total_cuotas']) ? $arrConfiguracion['muestra_total_cuotas'] : 1;
        $imprimirRUC = isset($arrConfiguracion['mostrar_ruc']) ? $arrConfiguracion['mostrar_ruc'] : 1;
        $imprimirCOM = isset($arrConfiguracion['mostrar_com']) ? $arrConfiguracion['mostrar_com'] : 1;
        $html = array();
        $espdf = false;
        foreach ($arrCodigoFactura as $codigoFactura) {
            $myFactura = new Vfacturas($conexion, $codigoFactura);
            $myPuntoVenta = new Vpuntos_venta($conexion, $myFactura->punto_venta);
            $filial = new Vfiliales($conexion, $this->codigofilial);
            if (($myPuntoVenta->medio == 'electronico' && $filial->pais == 1 )|| ($myPuntoVenta->medio == 'electronico'  && $myPuntoVenta->tipo_factura == 15 || $myPuntoVenta->medio == 'electronico'  && $myPuntoVenta->tipo_factura == 16))  {
                if ($myPuntoVenta->tipo_factura == 15 || $myPuntoVenta->tipo_factura == 16) {
                    $metodos_facturacion = $filial->getMetodoFacturacion();
                    switch ($myPuntoVenta->tipo_factura) {
                        case 16://servicio
                            switch ($metodos_facturacion[0]['facturacion_servicios']) {
                                case 'ginfes':
                                    $NFGinfes = new NFSePHPGinfesPDF($conexion, 'P', 'cm', 'A4', $codigoFactura, '', BASEPATH . '../assents/img/logo.jpg');
                                    $NFGinfes->printNFSe();
                                    $myJSONPDF = $NFGinfes;
                                    $espdf = true;

                                    break;

                                case 'dsf':
                                    $NFDsf = new NFSePHPDsfPDF($conexion, 'P', 'cm', 'A4', $codigoFactura, '', BASEPATH . '../assents/img/logo.jpg');
                                    $myJSONPDF = $NFDsf->printNFSe($imprimirRazon, $muestraCuotasTotales);
                                    $espdf = true;
                                    break;

                                case 'paulistana':
                                    //require(APPPATH.'libraries/facturas/NFSePaulistanaPDF.php');

                                    $nfse = new NFSePaulistanaPDF();
                                    $myJSONPDF = $nfse->pNFSe($conexion, 'P', 'cm', 'A4', $codigoFactura, BASEPATH . '../assents/img/notapaulistana/prefeituradesp.gif');
                                    $espdf = true;

                                    break;
                            }

                            break;

                        case 15://productos
                            if ($metodos_facturacion[0]['facturacion_productos'] == 'toolsnfe') {
                                $myJSONPDF = Vprestador_toolsnfe::getPDF($conexion, $codigoFactura, $this->codigofilial);
                                $espdf = true;
                            }
                            break;
                    }
                } elseif ($filial->pais == 1) {
                    $factura_electronica = new WsFePDF($conexion, 'P', 'cm', 'A4', $codigoFactura, BASEPATH . '../assents/img/logo.jpg');
                    $myJSONPDF = $factura_electronica->printFe($imprimeRazon, null, $cantidadCopias);
                    $espdf = true;
                }
            } else {
                $modeloFacturaElectronica = isset($arrConfiguracion['modelo_factura_electronica']) ? $arrConfiguracion['modelo_factura_electronica'] : null;
                $tipoFactura = $myPuntoVenta->tipo_factura;
                $arrRenglones = $myFactura->getRenglones();
                formatearCtaCte($conexion, $arrRenglones, $muestraCuotasTotales);
                $myRazonSocial = $myFactura->getRazon();
                $myCondicion = new Vcondiciones_sociales($conexion, $myRazonSocial->condicion);
                if ($myCondicion->getCodigo() == 1){ // responsable inscripto siempre debe imprimir los datos indistintamente de la configuracion(pedido por Agustina)
                    $imprimirRazon = 1;
                }
                $total = 0;
                $cantidadLineas = 0;
                $arrValores = array();
                $arrImpuestos = array();
                $arrValores['[!--FECHAFACTURA--]'] = formatearFecha_pais($myFactura->fecha);
                $arrValores['[!--FECHAFACTURADIA--]'] = substr($myFactura->fecha, 8, 2);
                $arrValores['[!--FECHAFACTURAMES--]'] = substr($myFactura->fecha, 5, 2);
                $arrValores['[!--FECHAFACTURAANIO--]'] = substr($myFactura->fecha, 0, 4);
                $arrValores['[!--FECHAFACTURAANIO2--]'] = substr($myFactura->fecha, 2, 2);

                $arrValores['[!--RAZONSOCIALFACTURA--]'] = $imprimirRazon == 1 ? utf8_decode($myRazonSocial->razon_social) : "";
                $arrValores['[!--CONDICIONFACTURA--]'] = $myCondicion->condicion;
                $arrValores['[!--DOMICILIOFACTURA--]'] = $myRazonSocial->direccion_calle . " " . $myRazonSocial->direccion_numero;
                if ($myCondicion->getCodigo() == 2){
                    $arrValores['[!--NUMEROIDENTIFICADORFISCAL--]'] = "";
                } else {
                    if ($this->codigofilial <> 36 || $imprimirRUC == 1){
                        $arrValores['[!--NUMEROIDENTIFICADORFISCAL--]'] = funciones::formatearNumeroDocumneto($myRazonSocial->documento, $myRazonSocial->tipo_documentos);
                    } else {
                        $arrValores['[!--NUMEROIDENTIFICADORFISCAL--]'] = "";
                    }
                }
                if ($this->codigofilial <> 36 || $imprimirCOM == 1){
                    $matricula = $arrRenglones[0]['concepto'];
                    $comision = $myFactura->getComision($matricula);
                    $arrValores['[!--CODIGOCOMISION--]'] = $comision;
                } else {
                    $arrValores['[!--CODIGOCOMISION--]'] = "";
                }
                foreach ($arrRenglones as $renglon) {
                    $descripcion = substr($renglon['descripcion'], 0, 58);
                    $importe = $renglon['facturas_renglones_importe'];
                    $total += $importe;
                    $cantidadLineas++;
                    $arrValores["[!--CANTIDADLINEA{$cantidadLineas}--]"] = 1;
                    $arrValores["[!--DESCRIPCIONFACTURALINEA{$cantidadLineas}--]"] = utf8_decode($descripcion);
                    if ($tipoFactura == 1) { // Factura A discrimina impuestos (el monto de la linea es el importe - el impuesto `porque el impuesto se agrega al final)
                        $montoIVA = $importe - round($importe / 1.21, 2);

                        $arrValores["[!--MONTOFACTURALINEA{$cantidadLineas}--]"] = formatearImporte($importe - $montoIVA);
                        $impuesto = "IVA";
                        if (!isset($arrImpuestos[$impuesto])) {
                            $arrImpuestos[$impuesto] = $montoIVA;
                        } else {
                            $arrImpuestos[$impuesto] += $montoIVA;
                        }
                    } else {
                        $arrValores["[!--MONTOFACTURALINEA{$cantidadLineas}--]"] = formatearImporte($importe);
                    }
                }
                for ($i = $cantidadLineas + 1; $i < 20; $i++) {
                    $arrValores["[!--DESCRIPCIONFACTURALINEA{$i}--]"] = "";
                    $arrValores["[!--MONTOFACTURALINEA{$i}--]"] = "";
                    $arrValores["[!--CANTIDADLINEA{$i}--]"] = "";
                }
                if ($tipoFactura == 1) { // Factura A discrimina impuestos
                    $cantidadImpuestos = 0;
                    $totalImpuestos = 0;
                    foreach ($arrImpuestos as $nombre => $valor) {
                        $cantidadImpuestos++;
                        $arrValores["[!--NOMBREIMPUESTO{$cantidadImpuestos}--]"] = $nombre;
                        $arrValores["[!--VALORIMPUESTO{$cantidadImpuestos}--]"] = formatearImporte($valor);
                        $totalImpuestos += $valor;
                    }
                    for ($i = $cantidadImpuestos + 1; $i < 10; $i++) {
                        $arrValores["[!--NOMBREIMPUESTO{$i}--]"] = "";
                        $arrValores["[!--VALORIMPUESTO{$i}--]"] = "";
                    }
                    $arrValores['[!--SUBTOTALFACTURA--]'] = formatearImporte($total - $totalImpuestos);
                    $arrValores['[!--TOTALFACTURA--]'] = formatearImporte($total);
                } else {
                    for ($i = 0; $i < 10; $i++) {
                        $arrValores["[!--NOMBREIMPUESTO{$i}--]"] = "";
                        $arrValores["[!--VALORIMPUESTO{$i}--]"] = "";
                    }
                    $arrValores['[!--SUBTOTALFACTURA--]'] = formatearImporte($total);
                }
                $arrValores['[!--TOTALFACTURA--]'] = formatearImporte($total);
                 $V = new numberToLetter();
                $con_letra = strtoupper($V->ValorEnLetras($total,"con"));
                $arrValores['[!--MONTODETALLEIMPORTE--]'] = ucwords(strtolower(utf8_decode($con_letra)));
                $codigoTamplate = $this->getNroTemplateFacturacion($papel, $tipoFactura,
                                                                   $templateCompleto, $printerID, 
                                                                   $myPuntoVenta->medio, $modeloFacturaElectronica);
                $myTemplate = new Vtemplates($conexion, $codigoTamplate);
                $html[] = $myTemplate->html;
                maquetados::desetiquetar($arrValores, $html);
                if ($templateCompleto) {
                    maquetados::desetiquetarDatosFactura($conexion, $codigoFactura, $html);
                    maquetados::desetiquetarFacturante($conexion, $codigoFactura, $html);
                    maquetados::desetiquetarIdioma($html);
                }
            }
        }

        if ($papel == "folio1") {
            $papel = "folio1";
        } else {
            $papel = "A4"; // no se porque se cambio el tipo de papel es A4 en luager de A5 o A4 segun la configuración
        }
        if (!$espdf) {
            $myJSONPDF = new json_templates("P", "mm", $papel);
            $myJSONPDF->setJSON($html);
            $pdf = $myJSONPDF->Output("pdf", $cantidadCopias);
        } else {
            $pdf = $myJSONPDF;
        }



        return $pdf;
    }

    function facturacion($codigoFactura, $printerID = null, $cantidadCopias = null) {
        $conexion = $this->load->database($this->codigofilial, true);
        $cantidadCopias = $this->getCantidadCopias(11, $cantidadCopias);
        $pdf = $this->getPDFFacturas($conexion, $codigoFactura, $cantidadCopias, false, $printerID);
        $myImpresion = new impresiones($conexion, $this->codigofilial);
        if (!$myImpresion->printerPDF($conexion, 11, $pdf, $printerID)) {
            $arrResp["error"] = "ha ocurrido un error al enviar el trabajo de impresion con el mensaje {$myImpresion->getError()}";
        } else {
            $arrResp['success'] = "success";
        }
        return $arrResp;
    }

    function AddPageInforme(&$pdf, $nombreInforme, $imprimirEncabezado = true) {

        $pdf->addPage();
        $pdf->SetFont("arial", "BU", 14);
        $pdf->SetMargins(5, 5);
        if ($imprimirEncabezado) {
            $pdf->Cell(0, 6, utf8_decode(lang('reportes_de_' . $nombreInforme)), 0, 0, "C");
        }
        $pdf->SetFont("arial", "B", 10);
        $pdf->Ln(12);
    }

    function addFooterInforme(&$pdf, $nombreUsuario, $cantidadPaginas = 0) {
        $pdf->SetY(-18);
        $pdf->Cell(10, 6, utf8_decode(lang("fecha_emision")) . " " . formatearFecha_pais(date("Y-m-d")), 0, 0, "L");
        $pdf->Cell(0, 10, "Pagina {$pdf->PageNo()} / $cantidadPaginas", 0, 0, 'C');
        $pdf->Cell(0, 6, $nombreUsuario, 0, 0, "R");
    }

    function imprimir_reporte_factura($cod_usuario, $filtros, $id_impresora, $copias, $currentPage, $pageDisplay, $sSearch, $arrColumsVisibles, $nombreReporte, $sortDir, $sortName, $aplyCommonFilters) {
        $conexion = $this->load->database($this->codigofilial, true);
        $repetirEncabezado = $this->Model_configuraciones->getValorConfiguracion(null, 'repetirEncabezadoInformes') == 1;
        $this->load->helper('impresiones');
        $this->load->helper('alumnos');
        $reporte = $this->Model_reportes->getReporte($nombreReporte, true, $currentPage, $pageDisplay, $sortName, $sortDir, $sSearch, $arrColumsVisibles, $aplyCommonFilters, $filtros);

        /***************************************************************************/
        if ($nombreReporte == 'consultas_web'){
            $arrWidth = array();
            $arrTitle = array();
            $arrContent = array();
            foreach ($reporte['columns'] as $key => $columnas){
                if (in_array($key, $arrColumsVisibles)){
                    $arrWidth[] = $columnas->Pdfwidth;
                    if ($key == 'fecha_consulta'){
                        $arrTitle[] = substr($columnas->display, 0, 11);
                    } else {
                        $arrTitle[] = $columnas->display;
                    }
                }
            }
            foreach ($reporte['aaData'] as $key => $row){
                foreach ($arrColumsVisibles as $columna){
                    $cantidadCaracteres = 0;
                    if ($columna == 'asunto'){
                        $cantidadCaracteres = 40;
                    } else if ($columna == 'mensaje'){
                        $cantidadCaracteres = 42;
                    } else if ($columna == 'mail'){
                        $cantidadCaracteres = 23;
                    } else if ($columna == 'telefono' || $columna == 'usuario_respuesta'){
                        if (strpos($row[$columna], " ")){
                            $row[$columna] = str_replace(" ", "\n", $row[$columna]);
                        } else {
                            $cantidadCaracteres = 10;
                        }
                    }
                    if ($cantidadCaracteres > 0){
                        $temp = str_split($row[$columna], $cantidadCaracteres);
                        $texto = implode("\n", $temp);
                    } else {
                        $texto = $row[$columna];
                    }
                    $arrContent[$key][] = $texto;
                }
            }
            $fecha_desde = '';
            $fecha_hasta = '';
            foreach ($filtros as $filtro){
                if ($filtro['field'] == 'fecha_consulta'){
                    $fecha_desde = isset($filtro['value1']) && $filtro['value1'] <> '' ? $filtro['value1'] : '';
                    $fecha_hasta = isset($filtro['value2']) && $filtro['value2'] <> '' ? $filtro['value2'] : '';
                }
            }
            $periodo = '';
            if ($fecha_desde != ''){
                $periodo .= lang("desde")." ".$fecha_desde;
            }
            if ($fecha_hasta != ''){
                $periodo .= " ".lang("al")." ".$fecha_hasta;
            }
            if ($periodo == ''){
                $periodo = lang("todas_las_fechas");
            }
            $usuario = $this->session->userdata("nombre");
            $filial = $this->session->userdata("filial");
            $arrInfo = array(
                array("txt" => lang("fecha_emision").": ".date("d/m/Y"), "size" => "8", "align" => "R", "width" => 276, "height" => 4),
                array("txt" => lang("periodo").": ".$periodo, "size" => "8", "align" => "R", "width" => 276, "height" => 4),
                array("txt" => lang("usuario").": ".$usuario, "size" => "8", "align" => "R", "width" => 276, "height" => 4)
            );

            $exp = new export('pdf');
            $exp->setTitle($arrTitle);
            $exp->setContent($arrContent);
            $exp->setPDFFontSize(8);
            $exp->setColumnWidth($arrWidth);
            $file = FCPATH."assents\img\logo.jpg";
            $exp->setLogo($file);
            $exp->setInfo($arrInfo);
            $exp->setContentHeight(6);
            $exp->setReportTitle($filial['nombre']." - ".lang("reporte_de_consultas_web"));
            $pdf = $exp->exportar(null, true);
        } else if ($nombreReporte == 'reporte_alumnos_activos_por_comision'){
            $arrWidth = array();
            $arrTitle = array();
            $arrContent = array();
            foreach ($reporte['columns'] as $key => $columnas){
                if (in_array($key, $arrColumsVisibles)){
                    $arrWidth[] = $columnas->Pdfwidth;
                    if ($key == 'fecha_consulta'){
                        $arrTitle[] = substr($columnas->display, 0, 11);
                    } else {
                        $arrTitle[] = $columnas->display;
                    }
                }
            }
            $totalCupos = 0;
            $totalInscriptos = 0;
            $totalVacantes = 0;
            foreach ($reporte['aaData'] as $key => $row){
                foreach ($arrColumsVisibles as $columna){
                    $arrContent[$key][] = $row[$columna];
                    if ($columna == 'cupo'){
                        $totalCupos += $row[$columna];
                    } else if ($columna == 'inscriptos'){
                        $totalInscriptos += $row[$columna];
                    } else if ($columna == 'vacantes'){
                        $totalVacantes += $row[$columna];
                    }
                }
            }
            $arrAcumulables = array(5, 6, 7);
            $usuario = $this->session->userdata("nombre");
            $filial = $this->session->userdata("filial");
            $arrInfo = array(
                array("txt" => lang("fecha_emision").": ".date("d/m/Y"), "size" => "8", "align" => "R", "width" => 248, "height" => 4),
                array("txt" => lang("usuario").": ".$usuario, "size" => "8", "align" => "R", "width" => 248, "height" => 4)
            );

            $exp = new export('pdf');
            $exp->setTitle($arrTitle);
            $exp->setContent($arrContent);
            $exp->setContentAcumulable($arrAcumulables);
            $exp->setPDFFontSize(8);
            $exp->setColumnWidth($arrWidth);
            $file = FCPATH."assents\img\logo.jpg";
            $exp->setLogo($file);
            $exp->setInfo($arrInfo);
            $exp->setContentHeight(6);
            $exp->setReportTitle($filial['nombre']." - ".lang("reporte_alumnos_activos_por_comision"));
            $exp->setMargin(36);
            $pdf = $exp->exportar(null, true);



        } else { // ir agregando para reportes mas personalizados que el standar de impresion desde reportes

            $pdf = new PDF_AutoPrint('L', 'mm', 'A4');
            $pdf->SetAutoPageBreak(true, 1);
            $pdf->AutoPrint(false);
            $width = array();
            $cantCaracteres = 40;
            $MyUsuario = new Vusuarios_sistema($conexion, $cod_usuario);
            $nombre = inicialesMayusculas($MyUsuario->nombre);
            $nombreColumnaAcumulable = array();
            
            /*
            error_log(
                "\n\nreporte: ".
                print_r($reporte, true).
                "\n\n"
            );
            */
            
            for ($cantCopias = 0; $cantCopias < $copias; $cantCopias++) {
                $this->AddPageInforme($pdf, $nombreReporte, true);
                foreach ($reporte['columns'] as $key => $columnas) {
                    $width[$key][] = $columnas->Pdfwidth;
                    if (in_array($key, $arrColumsVisibles)) {
                        $pdf->SetFont("arial", "B", 10);
                        $pdf->Cell($columnas->Pdfwidth, 6, utf8_decode($columnas->display), "LTRB", 0, "L");
                    }
                    if ($columnas->acumulable == 1) {
                        $nombreColumnaAcumulable[$key] = array("total_acumulado" => '');
                    }
                }
                $pdf->Ln();
                $cantidadPaginas = ceil(count($reporte['aaData']) / 27);
                foreach ($reporte['aaData'] as $row) {
                    if (count($nombreColumnaAcumulable) > 0) {
                        foreach ($nombreColumnaAcumulable as $key => $valor) {
                            $nombreColumnaAcumulable[$key]['total_acumulado'] = $nombreColumnaAcumulable[$key]['total_acumulado'] + $row[$key];
                        }
                    }
                    foreach ($row as $tit => $value) {
                        if (in_array($tit, $arrColumsVisibles)) {
                            $ancho = $width[$tit][0];
                            $string = "";

                            if ($tit === 'descripcion') {
                                $string = substr($value, 0, ($cantCaracteres - 10)) . "...";
                            }
                            else
                            {
                                $string = cortarString($value, $cantCaracteres);

                                if ($ancho > 45 && $ancho <= 50 && strlen($string) > 25) {
                                    $string = substr($string, 0, 25);
                                }
                            }

                            $pdf->Cell($ancho, 6, utf8_decode(inicialesMayusculas($string)), "LTRB", 0, "L");
                        }
                    }
                    if ($pdf->GetY() >= 180) {
                        $this->addFooterInforme($pdf, $nombre, $cantidadPaginas);
                        $this->AddPageInforme($pdf, $nombreReporte, $repetirEncabezado);
                    } else {
                        $pdf->Ln();
                    }
                }

                if (count($nombreColumnaAcumulable) > 0) {
                    foreach ($arrColumsVisibles as $colum) {
                        if (array_key_exists($colum, $nombreColumnaAcumulable)) {
                            $pdf->SetFont("arial", "B", 10);
                            $pdf->Cell($width[$colum][0], 6, $nombreColumnaAcumulable[$colum]['total_acumulado'], "LTRB", 0, "L");
                        } else {

                            $pdf->Cell($width[$colum][0], 6, '', "LTRB", 0, "L");
                        }
                    }
                }

                $this->addFooterInforme($pdf, $nombre, $cantidadPaginas);
            }
        /***********************************************************************************/
        }

        $conexion = $this->load->database("default", true);
        $myImpresion = new impresiones($conexion, $this->codigofilial);
        $arrResp = array();
        if (!$myImpresion->printerPDF($conexion, 12, $pdf, $id_impresora)) {
            $arrResp["error"] = "ha ocurrido un error al enviar el trabajo de impresion con el mensaje {$myImpresion->getError()}";
        } else {
            $arrResp['success'] = "success";
        }
        return $arrResp;
    }

    function imprimir_asistencias($curso, $comision, $fecha, $materia, $fechaDesde, $fechaHasta, $printerID = null, $cantidadCopias = null, $vista = false, $horizontalmente = false, $cod_horario = false, $baja = false) {
        $conexion = $this->load->database($this->codigofilial, true);
        $cantidadCopias = $this->getCantidadCopias(9, $cantidadCopias);
        $this->load->helper('alumnos');
        $cantidadDeColumnasPorTabla = 12; // ES LA CANTIDAD DE COLUMNAS QUE CABEN EN LA HOJA, LUEGO DE ESTA CANTIDAD SE COMIENZA EN UNA NUEVA TABLA DEBAJO DE LA ACTUAL
        $codFilial = $this->codigofilial;
        $arrAsistencias = $this->Model_horarios->getAsistencias($codFilial, $materia, $comision, $fechaDesde, $fechaHasta, false, true, $baja, array("libre", "cursando"));

        $arrFechasTemp = $arrAsistencias['dias'];
        $profesorComisionMateria = Vhorarios::getProfesorComisionMateria($conexion, $comision, $materia, $fecha, $cod_horario);

        $ultimaAsistencia = Vmatriculas_horarios::getUltimaAsistenciaPasada($conexion, $materia, $comision);
        $ultimaFechaRegistrada = $arrFechasTemp[count($arrFechasTemp) - 1];
        $primerFechaRegistrada = $arrFechasTemp[0];
        $posFecha = 0;
        $posArray = 0;
        while ($posFecha == 0 && count($arrFechasTemp) > $posArray) { // si no se encuentra la ultima fecha de asistencia en el array es porque no se tomarion asistencias o porque ha ocurrido un error
            if ($arrFechasTemp[$posArray] == $ultimaAsistencia) {
                $posFecha = $posArray;
            }
            $posArray ++;
        }
        $posFechaFin = count($arrFechasTemp);
        $posFechaInicio = 0;
        if (isset($arrFechasTemp[$posFecha + 5])) { // planilla vacia debe mostrar cinco fechas desde la ultima registrada
            $ultimaFechaRegistrada = $arrFechasTemp[$posFecha + 5];
            $posFecha += 5;
            $posFechaFin = $posFecha;
        } else {
            $posFecha = count($arrFechasTemp);
        }
        if (isset($arrFechasTemp[$posFecha - $cantidadDeColumnasPorTabla + 1])) {
            $primerFechaRegistrada = $arrFechasTemp[$posFecha - $cantidadDeColumnasPorTabla + 1];
            $posFechaInicio = $posFecha - $cantidadDeColumnasPorTabla + 1;
        }

        $cantidadElementos = $posFechaFin - $posFechaInicio + 1;
        if ($cantidadElementos > $cantidadDeColumnasPorTabla) { // de ocurrir esto, la posicion de la primer fecha es 0
            $ultimaFechaRegistrada = isset($arrFechasTemp[$cantidadDeColumnasPorTabla]) ? $arrFechasTemp[$cantidadDeColumnasPorTabla]    // los registros necesarios si es que el array original tiene
                : $arrFechasTemp[count($arrFechasTemp)];         // o el array Total (que posee menos que la cantidad por tabla)
        }

        $arrFechas1 = array();
        foreach ($arrFechasTemp as $key => $value) {
            if ($value >= $primerFechaRegistrada && $value <= $ultimaFechaRegistrada) {
                $arrFechas1[] = $value;
            }
        }

        $nombre = "nombre_" . get_idioma();
        $myCurso = new Vcursos($conexion, $curso);
        $nombreCurso = $myCurso->$nombre;
        $myMateria = new Vmaterias($conexion, $materia);
        $nombreMateria = $myMateria->$nombre;
        $myComision = new Vcomisiones($conexion, $comision);
        if ($printerID == -1) {
            if ($horizontalmente == false) {
                $pdf = new PDF_Rotate('P', 'mm', 'A4');
                $cantidadDeColumnasPorTabla = 12;
            } else {
                $pdf = new PDF_AutoPrint('L', 'mm', 'A4');
                $pdf->AutoPrint(false);
                $cantidadDeColumnasPorTabla = 12;
            }
        } else {
            if ($horizontalmente == false) {
                $pdf = new PDF_Rotate('P', 'mm', 'A4');
                $cantidadDeColumnasPorTabla = 12;
            } else {
                $pdf = new PDF_AutoPrint('L', 'mm', 'A4');
                $pdf->AutoPrint(false);
                $cantidadDeColumnasPorTabla = 12;
            }
        }
        if ($printerID == -1) {
            $pdf->SetLeftMargin(5);
            $pdf->SetRightMargin(3);
        } else {
            $pdf->SetLeftMargin(20);
            $pdf->SetRightMargin(3);
        }
        for ($cantCopias = 0; $cantCopias < $cantidadCopias; $cantCopias++) {
            $pdf->AddPage();
            if ($horizontalmente == false) {
                $pdf->Rotate(270, 105, 105);
            }
            //$pdf->SetFont("arial", "B", 12);
            $pdf->Ln(3);
            $this->encabezadoListadoImpresion($conexion, $pdf, $nombreCurso, $nombreMateria, $myComision, $fecha, $profesorComisionMateria);
            $arrFechasDivididas = array();
            $ct = 0;
            foreach ($arrFechasTemp as $key => $fecha) {
                $arrFechasDivididas[$ct][] = $fecha;
                if (($key + 1) % $cantidadDeColumnasPorTabla == 0)  // $cantidadDeColumnasPorTabla ES LA CANTIDAD MAXIMA DE COLUMNAS DE FECHA ANTES DE LLEGAR AL MARGEN DE LA PAGINA
                    $ct++;                                          // $cantidadDeColumnasPorTabla llegar a 13 columnas se continua con una nueva tabla debajo
            }
            //Limpio y rapido.
            $primero = false;
            foreach($arrFechasDivididas as $fecha){
                if($primero){
                    $pdf->AddPage();
                }
                $this->headListadoImpresion($pdf, $fecha);
                $arrayFormateadoAsistencia = $this->formatearArrayBodyListado($arrAsistencias, $arrFechasTemp, $comision, $materia);
                $this->llenarBodyListadoAsistencia($arrayFormateadoAsistencia, $pdf, $fecha, $horizontalmente, $vista);
                $primero = true;
            }
        }
        $conexion = $this->load->database("default", true);
        $myImpresion = new impresiones($conexion, $this->codigofilial);
        $arrResp = array();
        if (!$myImpresion->printerPDF($conexion, 9, $pdf, $printerID)) {
            $arrResp["error"] = "ha ocurrido un error al enviar el trabajo de impresion con el mensaje {$myImpresion->getError()}";
        } else {
            $arrResp['success'] = "success";
        }
        return $arrResp;
    }

    private function encabezadoListadoImpresion($conexion, $pdf, $nombreCurso, $nombreMateria, $myComision, $fecha, $profesorComisionMateria) {
        $this->load->helper('comisiones');
        $fecha = '';
        $nombreProfesor = '';
        $nombreComision = $myComision->nombre;
        $pdf->SetFont("arial", "B", 12);
        $pdf->Cell(250, 6, utf8_decode(lang("listado_de_asistencias")), 0, 0, "C");
        $pdf->Ln();
        $pdf->Cell(20, 6, lang('curso_presu_as'));
        $pdf->Cell(80, 6, utf8_decode($nombreCurso));
        $totProf = count($profesorComisionMateria);
        $x = '';
        $separador = "-";
        foreach ($profesorComisionMateria as $profesor) {
            $x++;
            if ($x < 4) {
                $myProfesor = new Vprofesores($conexion, $profesor['cod_profesor']);
                $nombre = inicialesMayusculas($myProfesor->nombre);
                $apellido = inicialesMayusculas($myProfesor->apellido);
                if ($totProf == $x) {
                    $separador = " ";
                }
                $nombreProfesor.= utf8_decode($apellido . ', ' . $nombre) . $separador;
            }
        }
        if($totProf >= 3)
        {
            $pdf->Cell(80, 6, lang('profesor/es') . ': ' . $nombreProfesor, 0, 0, "R");
        }
        if($totProf == 2)
        {
            $pdf->Cell(130, 6, lang('profesor/es') . ': ' . $nombreProfesor, 0, 0, "R");
        }
        if($totProf < 2)
        {
            $pdf->Cell(150, 6, lang('profesor/es') . ': ' . $nombreProfesor, 0, 0, "R");
        }
        $pdf->Ln();
        $pdf->Cell(20, 6, utf8_decode(lang("comision")));
        $pdf->Cell(80, 6, utf8_decode($nombreComision));
        //se agrega la fecha del día al pdf -mmori-
        $pdf->Cell(150, 6, utf8_decode(lang('fecha_impression')). ': ' . date("d/m/Y"), 0, 0, "R");
        $pdf->Ln();
        $pdf->Cell(20, 6, utf8_decode(lang("materia")));
        $pdf->Cell(80, 6, utf8_decode($nombreMateria));
        $pdf->Cell(150, 6, "Firma Prof:_________________________", 0, 0, "R");
        $pdf->Ln();
        $pdf->ln(3);
    }

    private function headListadoImpresion($pdf, $arrFechas) {
        $pdf->SetFont('arial', '', 10);
        $pdf->ln(4);
        $pdf->Cell(65, 4, utf8_decode(lang("Alumno")), "LTRB", 0);
        $pdf->Cell(16, 4, "Estado", "LTRB", 0);
        $pdf->Cell(12, 4, utf8_decode('Asis'), "LTRB", 0);
        $pdf->Cell(12, 4, utf8_decode('Turno'), "LTRB", 0);
        foreach ($arrFechas as $fechaAsistencia) {
            $pdf->SetFont('arial', '', 8);
            $pdf->Cell(15, 4, formatearFecha_pais($fechaAsistencia), "TRB", 0, "C");
        }
    }

    private function formatearArrayBodyListado($arrAsistencias, $arrFechas1) {
        $cambioComision = 0;
        $arrFormateado = '';
        $arrayAsistencias = '';
        $arrMatriculas = '';
        $arrMatriculasAlumno = $arrAsistencias['matriculas'];
        $arrEstadosAcademicos = $arrAsistencias['estados_academicos'];
        $arrMatriculasAlumnoOrdenado = '';
        array_multisort($arrAsistencias['matriculas'], SORT_ASC);
        foreach ($arrAsistencias['matriculas'] as $k => $valor) {
            foreach ($arrMatriculasAlumno as $j => $value) {
                if ($valor === $value) {
                    $arrMatriculasAlumnoOrdenado[$j] = $value;
                }
            }
        }

        foreach ($arrMatriculasAlumnoOrdenado as $mat => $alumno) {
            $arrMatriculas[] = $mat;
            $arrFormateado[$mat]['alumno'] = $alumno;
            $arrFormateado[$mat]['estado_academico'] = isset($arrEstadosAcademicos[$mat]) && $arrEstadosAcademicos[$mat] <> ''
                    ? lang($arrEstadosAcademicos[$mat])
                    : "";
        }

        foreach ($arrFormateado as $key => $valor) {
            foreach ($arrAsistencias['porcasistencia'] as $k => $porAsistencia) {
                if ($key == $k) {
                    $arrFormateado[$key]['porAsistencia'] = $porAsistencia;
                }
            }
        }
        foreach ($arrFechas1 as $fecha_asistencia) {
            $arrayAsistencias[][$fecha_asistencia] = $arrAsistencias['asistencia'][$fecha_asistencia];
        }

        foreach ($arrayAsistencias as $asistencia) {
            foreach ($asistencia as $fecha => $val) {
                foreach ($val as $value) {
                    foreach ($value as $horarios) {
                        $turno = 0;
                        foreach ($horarios as $estado) {
                            $turno++;
                            foreach ($arrMatriculas as $cod_matricula) {
                                $nombre = '';
                                if (array_key_exists($cod_matricula, $estado)) {
                                    $nombre = $estado[$cod_matricula];
                                } else {
                                    $nombre = 'cambio_comision';
                                }
                                $text = '';
                                switch ($nombre) {
                                    case "presente":
                                        $text = lang("abreviatura_presente");
                                        break;
                                    case "ausente":
                                        $text = lang("abreviatura_ausente");
                                        break;
                                    case "justificado":
                                        $text = lang("abreviatura_justificado");
                                        break;
                                    case "cambio_comision":
                                        $text = "---";
                                        $cambioComision++;
                                        break;

                                    case 'media_falta':
                                        $text = lang('abreviatura_media_falta');
                                        break;

                                    case '':
                                        $text = '';
                                        break;

                                }
                                $arrFormateado[$cod_matricula]['asistencia'][$turno][$fecha] = $text;
                            }
                        }
                    }
                }
            }
        }
        $arrFormateado['cambio_comision'] = $cambioComision > 0 ? 1 : 0;
        return $arrFormateado;
    }

    private function llenarBodyListadoAsistencia($arrayFormateadoAsistencia, $pdf, $fecha, $horizontalmente, $vacia = false) {
//        echo "<pre>"; print_r($arrayFormateadoAsistencia); echo "</pre>"; die();
        $pdf->SetFont("arial", "", 9);
        $arrayTurnos = '';
        $arrFechas = '';
        $pdf->Ln();
        foreach ($arrayFormateadoAsistencia as $arrayAsis) {
            if (is_array($arrayAsis)){
                foreach ($arrayAsis as $turnos_asis) {
                    $arrayTurnos[] = count($turnos_asis);
                }
            }
        }
        foreach ($fecha as $fecha_asistencia) {
            $arrFechas[] = $fecha_asistencia;
        }
        $maxTurno = max($arrayTurnos);
        $ancho = $maxTurno > 1 ? 5 : 10;
        foreach ($arrayFormateadoAsistencia as $valor) {
            if ($valor['alumno'] <> '') {
                $pdf->Cell(65, $ancho, utf8_decode(inicialesMayusculas($valor['alumno'])), 'LBR');
                $pdf ->Cell(16, $ancho, $valor['estado_academico'], 'LB');
            }
            $i = 1;


            foreach ($valor['asistencia'] as $turnos_asistencia) {

                if ($i > 1) { // ver para habilitar varias lineas
//                    $pdf->Cell(65, $ancho, 'xxx', 'LB');
//                    $pdf ->Cell(16, $ancho, $valor['estado_academico'], 'LB');
                }  else {
                $pdf->Cell(12, $ancho, utf8_decode($valor['porAsistencia']), 'LTRB');
                $pdf->Cell(12, $ancho, $i, 'LTRB');

                foreach ($arrFechas as $fecha_asis) {
                    if (isset($turnos_asistencia[$fecha_asis]) && !$vacia) {
                        $pdf->Cell(15, $ancho, $turnos_asistencia[$fecha_asis], 'LTRB', null, 'C');
                    } else {
                        $pdf->Cell(15, $ancho, '', 'LTRB');
                    }
                }

                $i++;
                $pdf->Ln();
                }
            }

            if ($pdf->getY() >= 190) {
                if ($horizontalmente == false) {
                    $pdf->AddPage();
                    $pdf->Rotate(270, 105, 105);
                    $pdf->SetFont("arial", "", 8);
                }
                $this->headListadoImpresion($pdf, $fecha); // si hay una segunda pagina llamo al head de la tabla.
                $pdf->Ln();
            }
        }
    }

    function mask($val, $mask)
    {
        $maskared = '';
        $k = 0;
        for($i = 0; $i<=strlen($mask)-1; $i++)
        {
            if($mask[$i] == '#')
            {
                if(isset($val[$k]))
                    $maskared .= $val[$k++];
            }
            else
            {
                if(isset($mask[$i]))
                    $maskared .= $mask[$i];
            }
        }
        return $maskared;
    }

    function disclaimmerTextRemessaLayout($disclaimmer){

        $boletos = array();
        $fechas = array();
        $boletos = $disclaimmer['boletos'];
        $disclaimmer['filial']->documento = $this->mask($disclaimmer['filial']->documento, '##.###.###/####-##');
        $textFontEscaped = "";

        foreach ($boletos as $boleto){
            $fechas[] = date("m/y", strtotime($boleto['fecha_vencimiento']));
        }
        asort($fechas);
        $desde = $fechas[0];
        $hasta = end($fechas);

        setlocale(LC_TIME,'pt_BR.UTF-8');
        $timeNow = strftime('%A, %d de %B de %Y', strtotime('today'));
        $citydate = "{$disclaimmer['filial']->ciudad}, {$timeNow}";
        $aknowledge = "Eu, {$disclaimmer['nombre_alumno']}, aluno(a) devidamente matriculado no curso de {$disclaimmer['nombre_curso']}";
        $aknowledge .= " venho requerer a renovação de minha matricula trimestral correspondente ao periodo desde {$desde} até {$hasta}";
        $aknowledge .= " juntamente a empresa {$disclaimmer['filial']->razon_social}, CNPJ Nº {$disclaimmer['filial']->documento} ";
        $aknowledge .= " com endereço na {$disclaimmer['filial']->domicilio} na cidade {$disclaimmer['filial']->ciudad} - CEP {$disclaimmer['filial']->codigopostal}";
        $aknowledge .= " Assim, ante à esta solicitação, declaro ter sido rematriculado no curso contratado e neste mesmo ato ter recebido os boletos das parcelas que me comprometo a pagar com os seguintes dados: ";


        $layout = "<div id='container' class='disclaimmer' style='height: 906px; '>";
        $layout .= "<h2 style='padding-bottom: 100px; padding-top: 25px; font-size: 16px; font-family: tahoma'>"."{$citydate}"."</h2>";
        $layout .= "<p style='padding-bottom: 100px; font-size: 16px; font-family: tahoma'>"."{$aknowledge}"."</p>";

        foreach ($boletos as $boleto) {
            $vencimiento = date("d/m/y", strtotime($boleto['fecha_vencimiento']));
            $layout .= "<p style='padding-bottom: 30px; font-size: 16px; font-family: tahoma'>";
            $layout .= "Boleto: {$boleto['linea']}"."<br/>";
            $layout .= "Vencimento: {$vencimiento}"."<br/>";
            $layout .= "Valor : R$ {$boleto['valor_boleto']}"."<br/>";
            $layout .= "Correspondente à parcela : {$boleto['numero_cuota']} de {$disclaimmer['ultima_cuota']}"."<br/>";
            $layout .= "</p>";
        }
        $layout .= "<p style='padding-bottom: 100px; padding-top: 50px; font-size: 16px; font-family: tahoma'>Ass:____________________________________________<br/>Nome do Aluno:{$disclaimmer['nombre_alumno']}<br/>CPF: {$boletos[0]['sacado']}</p>";
        $layout .= "<p style='padding-bottom: 100px; font-size: 16px; font-family: tahoma'>Ass:____________________________________________<br/>Nome do Coordenador:</p>";
        $layout .= "</div>";


        //return "";
        return $layout;
    }

    function disclaimmerTextRemessa($remesa, $filial, $sacado){

        $escFilial = "`".$filial."`";
        $conexion = $this->load->database("bancos", true);
        $export = array();
        $arrBoletos = array();
        $boletos = array();

        // Trae los datos de la filial (nombre, CNPJ, dirección...)
        $filialQueryStatement = "SELECT DISTINCT rsg.razon_social, rsg.documento, fl.domicilio, fl.ciudad, fl.provincia, fl.codigopostal FROM general.facturantes_filiales ff JOIN general.facturantes fc ON fc.codigo = ff.cod_facturante JOIN general.razones_sociales_general rsg ON fc.cod_razon_social = rsg.codigo JOIN general.filiales fl ON ff.cod_filial = fl.codigo WHERE ff.cod_filial =\"" . $filial . "\";" ;
        $filialAnswer = $conexion->query($filialQueryStatement);
        $filialData = $filialAnswer->first_row();

        $boletosRemesaSacadoStatement = "SELECT bb.codigo FROM bancos.boletos_bancarios bb WHERE bb.cod_remesa = {$remesa} AND bb.sacado_cpf_cnpj = \"{$sacado}\";";
        $boletosQuery = $conexion->query($boletosRemesaSacadoStatement);
        $boletosAnswer = $boletosQuery->result_array();


        foreach ($boletosAnswer as $item){
            $boleto = new Vboletos_bancarios($conexion, $item['codigo']);
            $arrBoletos[] = $boleto;
        }

        //$myRemesa = new Vremesas($conexion, $remesa);
        //$arrBoletos = $myRemesa->getLineas();

        foreach ($arrBoletos as $boletum) {
            $boleto = array();
            $htmlReturn = $boletum->getHTMLBoleto();
            $pos = strpos($htmlReturn, "<td class=\"linha_digitavel\">");
            $linea = substr($htmlReturn, $pos, 82 );
            $linea = str_replace("<td class=\"linha_digitavel\">", "", $linea);
            $boleto['linea'] = $linea;

            $boleto['fecha_vencimiento'] = $boletum->fecha_vencimiento;
            $boleto['valor_boleto'] = $boletum->valor_boleto;
            $boleto['sacado'] = $boletum->sacado_cpf_cnpj;
            $boletos[] = $boleto;
        }

        $alumnoQueryStatement = "SELECT DISTINCT al.codigo, al.nombre as nombre, al.apellido as apellido FROM bancos.boletos_bancarios bb JOIN {$escFilial}.razones_sociales rs ON bb.sacado_cpf_cnpj = rs.documento JOIN {$escFilial}.alumnos_razones ar ON rs.codigo = ar.cod_razon_social JOIN {$escFilial}.alumnos al ON al.codigo = ar.cod_alumno WHERE bb.sacado_cpf_cnpj =\"". $boletos[0]['sacado'] ."\";";
        $alumnoAnswer = $conexion->query($alumnoQueryStatement);
        $codAlumno = $alumnoAnswer->first_row()->codigo;
        $nombreAlumno = $alumnoAnswer->first_row()->nombre;
        $nombreAlumno .= " ";
        $nombreAlumno .= $alumnoAnswer->first_row()->apellido;

        $cursoAlumnoQueryStatement = "SELECT gcc.nombre_pt FROM general.planes_academicos gpa JOIN {$escFilial}.matriculas mat ON mat.cod_plan_academico = gpa.codigo JOIN general.cursos gcc ON gpa.cod_curso = gcc.codigo WHERE mat.cod_alumno = {$codAlumno};";
        $cursoAlumnoAnswer = $conexion->query($cursoAlumnoQueryStatement);
        $nombreCurso = $cursoAlumnoAnswer->first_row()->nombre_pt;

        $nroUltimaCuotaStatement = "SELECT ct.nrocuota FROM {$escFilial}.ctacte ct WHERE ct.cod_alumno = {$codAlumno} AND fechavenc <> 0 ORDER BY ct.nrocuota DESC LIMIT 1;";
        $queryNroUltimaCuota = $conexion->query($nroUltimaCuotaStatement);
        $nroUltimaCuota = $queryNroUltimaCuota->first_row()->nrocuota;

        $boletosCuotas = array();

        foreach ($boletos as $boleto) {
            $nroCuotaStatement = "SELECT ct.nrocuota FROM {$escFilial}.ctacte ct WHERE ct.cod_alumno = {$codAlumno} AND ct.fechavenc = \"".$boleto['fecha_vencimiento']."\";";
            $queryNroCuota = $conexion->query($nroCuotaStatement);
            $boleto['numero_cuota'] = $queryNroCuota->first_row()->nrocuota;
            $boletosCuotas[] = $boleto;
            //$export['boletos'] = $boleto;
            //var_dump($boleto);
        }

        $export['nombre_alumno'] = ucwords(strtolower($nombreAlumno));
        $export['nombre_curso'] = $nombreCurso;
        $export['ultima_cuota'] = $nroUltimaCuota;
        $export['filial'] = $filialData;
        $export['boletos'] = $boletosCuotas;

        $disclaimmerText = $this->disclaimmerTextRemessaLayout($export);
        return $disclaimmerText;
    }


    function imprimir_remessa_boleto_bancario($codRemesa, $idImpresora) {
        $filial = $this->session->userdata('filial');
        $conexion = $this->load->database($filial['codigo'], true);
        $myRemesa = new Vremesas($conexion, $codRemesa);
        $arrBoletos = $myRemesa->getLineas();

        $arrGroupByCpf = array();
        foreach ($arrBoletos as $blt) {
            if (empty($arrGroupByCpf[$blt->sacado_cpf_cnpj])) {
                $arrElements = array();
                $arrElements[] = $blt;
                $arrGroupByCpf[$blt->sacado_cpf_cnpj] = $arrElements;
            } elseif ($arrGroupByCpf[$blt->sacado_cpf_cnpj] != null) {
                $tempArr = $arrGroupByCpf[$blt->sacado_cpf_cnpj];
                $tempArr[] = $blt;
                $arrGroupByCpf[$blt->sacado_cpf_cnpj] = $tempArr;
            }
        }

        $arrHTML = array();
        foreach ($arrGroupByCpf as $key => $item){
            $arrHTML[] = $this->disclaimmerTextRemessa($codRemesa, $filial['codigo'], $key);
            foreach ($item as $boleto) {
                $arrHTML[] = $boleto->getHTMLBoleto();
            }
        }

        $htmlContent = implode("<div style='height: 130px;'>&nbsp;</div>", $arrHTML);
//      echo "<pre>"; print_r($htmlContent); echo "</pre>"; die();
        $myImpresion = new impresiones($conexion, $this->codigofilial);
        $myImpresion->printerHTML($conexion, 13, $htmlContent, $idImpresora);
        return true;
    }

    function imprimir_boletos_bancarios($boletos, $idImpresora){
        $filial = $this->session->userdata('filial');
        $conexion = $this->load->database($filial['codigo'], true);
        $obt_cod = new Vboletos_bancarios($conexion, $boletos[0]);
        $codRemesa = $obt_cod->cod_remesa;

        $arrHTML = array();
        $myRemesa = new Vremesas($conexion, $codRemesa);
        $arrBoletos = $myRemesa->getLineas();
        $arrGroupByCpf = array();
        foreach ($arrBoletos as $blt) {
            if (empty($arrGroupByCpf[$blt->sacado_cpf_cnpj])) {
                $arrElements = array();
                $arrElements[] = $blt;
                $arrGroupByCpf[$blt->sacado_cpf_cnpj] = $arrElements;
            } elseif ($arrGroupByCpf[$blt->sacado_cpf_cnpj] != null) {
                $tempArr = $arrGroupByCpf[$blt->sacado_cpf_cnpj];
                $tempArr[] = $blt;
                $arrGroupByCpf[$blt->sacado_cpf_cnpj] = $tempArr;
            }
        }

            foreach ($arrGroupByCpf as $key => $item) {
                if(in_array($arrGroupByCpf[$key][0]->codigo, $boletos, true)) {
                    $arrHTML[] = $this->disclaimmerTextRemessa($codRemesa, $filial['codigo'], $key);
                }
            }

        if(is_array($boletos))
            foreach ($boletos as $codigo) {
                $boleto = new Vboletos_bancarios($conexion, $codigo);
                $arrHTML[] = $boleto->getHTMLBoleto();
            }

        $htmlContent = implode("<div style='height: 134px;'>&nbsp;</div>", $arrHTML);
        $myImpresion = new impresiones($conexion, $this->codigofilial);
        $myImpresion->printerHTML($conexion, 13, $htmlContent, $idImpresora);
        return true;
    }


    function imprimir_inscriptos_seminarios($idSeminario = null, $idImpresora = -1, $copias = 1) {
        $filial = $this->session->userdata('filial');
        $arrFiltros = array(
            "sSearch" => '',
            "iDisplayStart" => '',
            "SortCol" => '',
            "sEcho" => ''
        );
        $arrInscriptos = $this->Model_seminarios->listarSeminariosDatatable($arrFiltros, date("Y-m-d"), null, $filial['codigo'], $idSeminario);
        $pdf = new PDF_AutoPrint("P", "mm", "A4");
        for ($i = 1; $i <= $copias; $i++) {
            $pdf->AddPage("P", "A4");
            $pdf->setFont("arial", 'B', 9);
            $pdf->Cell(27, 6, 'Horario', 'LTRB');
            $pdf->Cell(10, 6, 'Cupo', 'TRB');
            $pdf->Cell(50, 6, 'Nombre', 'TRB');
            $pdf->Cell(30, 6, 'Telefono', 'TRB');
            $pdf->Cell(20, 6, 'Documento', 'TRB');
            $pdf->Cell(55, 6, 'Email', 'TRB');
            $pdf->Ln();
            $pdf->setFont("arial", '', 9);
            foreach ($arrInscriptos['aaData'] as $inscripto) {
                $pdf->cell(27, 6, $inscripto[0], 'LRB');
                $pdf->cell(10, 6, $inscripto[1], 'RB');
                $pdf->cell(50, 6, substr(utf8_decode($inscripto[2]), 0, 30), 'RB');
                $pdf->cell(30, 6, substr($inscripto[3], 0, 17), 'RB');
                $pdf->cell(20, 6, $inscripto[4], 'RB');
                $pdf->cell(55, 6, $inscripto[5], 'RB');
                $pdf->Ln();
            }
        }
        $conexion = $this->load->database("default", true);
        $myImpresion = new impresiones($conexion, $this->codigofilial);
        $arrResp = array();
        if (!$myImpresion->printerPDF($conexion, 15, $pdf, $idImpresora)) {
            $arrResp["error"] = "ha ocurrido un error al enviar el trabajo de impresion con el mensaje {$myImpresion->getError()}";
        } else {
            $arrResp['success'] = "success";
        }
        return $arrResp;
    }

    function imprimir_boleto_bancario($codBoleto,  $idImpresora = null) {
        $filial = $this->session->userdata('filial');
        $conexion = $this->load->database($filial['codigo'], true);
        $myBoleto = new Vboletos_bancarios($conexion, $codBoleto);

        $htmlContent = $myBoleto->getHTMLBoleto();

        $myImpresion = new impresiones($conexion, $this->codigofilial);

        $res = $myImpresion->printerHTML($conexion, 14, $htmlContent, $idImpresora);
        return true;
    }

    /**
     * imprime reglamento
     *
     * @param int $codAlumno
     * @param string $printerID
     * @param int $cantidadCopias
     * @return array
     */
    function imprimir_reglamento(CI_DB_mysqli_driver $conexion, PDF_AutoPrint &$pdf, $cod_reglamento, $idioma, $conanexo = null) {
        $myReglamento = new Vreglamentos($conexion, $cod_reglamento);
        if ($idioma == "es") {
            $arrReglamento = explode("\n", $myReglamento->reglamento_es);
        } else if ($idioma == "pt") {
            $arrReglamento = explode("\n", $myReglamento->reglamento_pt);
        } else {
            $arrReglamento = explode("\n", $myReglamento->reglamento_in);
        }
        switch ($cod_reglamento) {
            case '1'://reglamento interno
                if ($idioma != 'pt') {//EN ESPAÑOL
                    $pdf->SetTopMargin(10);
                    $pdf->AddPage("P", "A4");
                    $pdf->setMargins(10, 2, 6);
                    foreach ($arrReglamento as $key => $parrafoReglamento) {
                        if ($key == 0) {
                            $pdf->SetFont('arial', 'B', 10);
                            $pdf->MultiCell(190, 4, utf8_decode($parrafoReglamento), 0, 'C');
                        } else {
                            $negrita = strpos(substr($parrafoReglamento, 0, 9), 'CAPITULO') !== false ? 'B' : '';
                            $pdf->SetFont('arial', $negrita, 9);

                            $pdf->MultiCell(190, 4, utf8_decode($parrafoReglamento), 0, 'L');
                        }
                    }
                    $pdf->Ln(15);

                    $pdf->Cell(60, 6, utf8_decode('FIRMA ALUMNO/PADRE O TUTOR'), 'T', 0, 'C');
                } else { //EN PORTUGUES
                    $pdf->SetTopMargin(20);
                    $pdf->AddPage("P", "A4");
                    $pdf->setMargins(10, 2, 6);
                    foreach ($arrReglamento as $key => $parrafoReglamento) {
                        //  $pdf->SetFont('arial', '', 9);
                        if ($key == 0) {
                            $anexo = $conanexo ? 'Anexo II - ' : '';
                            $pdf->SetFont('arial', 'B', 10);
                            $pdf->MultiCell(180, 4, utf8_decode($anexo . $parrafoReglamento), 0, 'C');
                        } else {
                            $negrita = strpos(substr($parrafoReglamento, 0, 9), 'TÍTULO') !== false || strpos(substr($parrafoReglamento, 0, 9), 'Capítulo') !== false ? 'B' : '';
                            $pdf->SetFont('arial', $negrita, 9);

                            if (strpos($parrafoReglamento, '[!--') != false) {
                                maquetados::desetiquetarDatosReglamento($conexion, null, $parrafoReglamento, $this->codigofilial);
                            }
                            $pdf->MultiCell(190, 4, utf8_decode($parrafoReglamento), 0, 'L');
                        }
                        if ($key == '34' || $key == '74' || $key == '122') {

                            $pdf->SetTopMargin(20);
                            $pdf->AddPage("P", "A4");
                        }
                    }
                    $pdf->setMargins(30, 2, 30);
                    $pdf->SetY(190);
                    $pdf->Ln();
                    $localidad = '[!--FILIALLOCALIDAD--]';
                    maquetados::desetiquetarDatosReglamento($conexion, null, $localidad, $this->codigofilial);
                    $pdf->Cell(80, 6, utf8_decode($localidad) . ', ............. de ...................de ' . date('Y'));
                    $pdf->SetY(230);
                    $pdf->Cell(60, 6, utf8_decode('Instituto Gastronômico - IGA'), 'T', 0, 'C');
                    $pdf->SetX(120);
                    $pdf->Cell(60, 6, utf8_decode('Aluno(a)/responsável'), 'T', 0, 'C');
                }


                break;
            case '2':
                $pdf->SetTopMargin(20);
                $pdf->AddPage("P", "A4");
                $pdf->setMargins(6, 2, 6);
                //$pdf->SetFont('arial', '', 9);
                $columna = 1;
                $y = $pdf->GetY();
                foreach ($arrReglamento as $key => $parrafoReglamento) {

                    if ($columna == 2) {
                        $pdf->SetX(108);
                    }
                    if ($key == 0) {
                        $pdf->SetX(12);
                        $pdf->SetFont('arial', 'B', 9);
                        $pdf->MultiCell(80, 4, utf8_decode($parrafoReglamento), 0, '');
                    } else {
                        $negrita = strpos(substr($parrafoReglamento, 1, 3), '. -') !== false || strpos($parrafoReglamento, '10. -') !== false || strpos($parrafoReglamento, '11. -') !== false || strpos($parrafoReglamento, '12. -') !== false ? 'B' : '';
                        $pdf->SetFont('arial', $negrita, 9);
                        if (strpos($parrafoReglamento, '[!--') !== false) {
                            maquetados::desetiquetarDatosReglamento($conexion, null, $parrafoReglamento, $this->codigofilial);
                        }
                        //  $pdf->Write(6,utf8_decode($lineaReglamento),'FJ');
//                $xline = $pdf->GetY();
                        $pdf->MultiCell(90, 4, utf8_decode($parrafoReglamento));
//                $pdf->SetY($xline);
//                $pdf->Cell(80, 6);
                        //$pdf->Cell(80, 6, utf8_decode($lineaReglamento));
                    }


                    if ($key == 12 || $key == 23 || $key == 36 || $key == 47 || $key == 51) {//$pdf->GetY() >= 260) {
//                if ($pdf->GetY() == 260) {
                        $columna = $columna == 1 ? 2 : 1;
//                }

                        if ($columna == 1) {
                            $pdf->SetTopMargin(20);
                            $pdf->AddPage("P", "A4");
                        } else {
                            $pdf->SetXY(108, $y);
                        }
                        $pdf->SetFont('arial', '', 9);
                    } else {
                        $pdf->Ln(4);
                    }
                }
                $pdf->setMargins(30, 2, 30);
                $pdf->SetY(110);
                $pdf->Ln();
                $localidad = '[!--FILIALLOCALIDAD--]';
                maquetados::desetiquetarDatosReglamento($conexion, null, $localidad, $this->codigofilial);
                $pdf->Cell(80, 6, utf8_decode($localidad) . ', ............. de ...................de ' . date('Y'));
                $pdf->SetY(140);
                $pdf->Cell(60, 6, 'CONTRATANTE', 'T', 0, 'C');
                $pdf->SetX(120);
                $pdf->Cell(60, 6, 'CONTRATADA', 'T', 0, 'C');
                $pdf->SetY(180);
                $pdf->Cell(60, 6, 'Testemunhas:', '', 0, 'L');
                $pdf->Ln(18);
                $pdf->Cell(60, 6, '1.', '', 0, 'L');
                $pdf->Ln(6);
                $pdf->Cell(60, 6, 'Nome:', 'T', 0, 'L');
                $pdf->Ln(6);
                $pdf->Cell(60, 6, 'CPF:', '', 0, 'L');
                $pdf->SetXY(120, 198);
//         $pdf->Cell(60, 6, '', '', 0, 'C');
//        $pdf->Ln(18);
                $pdf->Cell(60, 6, '2.', '', 0, 'L');
                $pdf->SetXY(120, 204);
                $pdf->Cell(60, 6, 'Nome:', 'T', 0, 'L');
                $pdf->SetXY(120, 210);
                $pdf->Cell(60, 6, 'CPF:', '', 0, 'L');
//        $pdf->SetXY(30, 160);
//        $pdf->Cell(150, 6,'FORMULARIO DE MATRICULA','TLRB',0,'C' );
//        $pdf->Ln(6);
//        $pdf->Cell(150, 4,'Aluno:','TLRB',0,'L' );
//        $pdf->Ln(4);
//        $pdf->Cell(75, 6,'','TLRB',0,'L' );
//        $pdf->Cell(75, 6,'','TLRB',0,'L' );
//        $pdf->Ln(6);
//        $pdf->Cell(75, 4,'RG:','TLRB',0,'L' );
//        $pdf->Cell(75, 4,'CPF:','TLRB',0,'L' );
//        $pdf->Ln(4);
//        $pdf->Cell(75, 6,'','TLRB',0,'L' );
//        $pdf->Cell(75, 6,'','TLRB',0,'L' );
//        $pdf->Ln(6);
//        $pdf->Cell(75, 4,'Turma:','TLRB',0,'L' );
//        $pdf->Cell(75, 4,utf8_decode('Ano de início do curso:'),'TLRB',0,'L' );
//        $pdf->Ln(4);
//        $pdf->Cell(75, 6,'','TLRB',0,'L' );
//        $pdf->Cell(75, 6,'','TLRB',0,'L' );
//        $pdf->Ln(6);
//        $pdf->Cell(75, 4,'Curso:','TLRB',0,'L' );
//        $pdf->Cell(75, 4,'Forma de Pagamento:','TLRB',0,'L' );
//        $pdf->Ln(4);
//        $pdf->Cell(75, 12,'','TLRB',0,'L' );
//        $pdf->Cell(75, 12,'','TLRB',0,'L' );
//        $pdf->Ln(12);
//        $pdf->Cell(150,6 ,utf8_decode('Observações:'),'TLR',0,'L' );
//        $pdf->Ln(6);
//        $pdf->Cell(150,6 ,utf8_decode('Valor do curso e material didático: R$................'),'LR',0,'L' );
//        $pdf->Ln(6);
//        $pdf->Cell(150,6 ,'Plano de pagamento:.......parcelas de .......','LR',0,'L' );
//        $pdf->Ln(6);
//        $pdf->Cell(150,6 ,utf8_decode('Para pagamento até dia.... de cada mes, valor da parcela.....'),'BLR',0,'L' );

                break;

            default:
                break;
        }
        return false;
    }

}

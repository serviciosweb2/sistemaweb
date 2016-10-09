<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Facturantes extends CI_Controller {

    public function __construct() {
        parent::__construct();
        session_method();
        $this->lang->load(get_idioma(), get_idioma());
        $filial = $this->session->userdata('filial');
        $config = array("codigo_filial" => $filial["codigo"]);
        $this->load->model("Model_facturantes", "", false, $config);
    }

    public function guardarFacturante() {
        $this->load->library('form_validation');
        $resultado = '';
        $this->form_validation->set_rules('condicion', lang('razon_condicion'), 'required');
        $this->form_validation->set_rules('tipo_doc', lang('tipoDni'), 'required');
        $this->form_validation->set_rules('numero_Doc', lang('documento_alumno'), 'required');
        $this->form_validation->set_rules('razon', lang('Razon'), 'required');
        $this->form_validation->set_rules('inicioActividad', lang('inicioActividad'), 'required');
        $this->form_validation->set_rules('localidad', lang('localidad'), 'required');
        $this->form_validation->set_rules('empresa_tel', lang('empresa'), 'required');
        $this->form_validation->set_rules('cod_area', lang('cod_area'), 'required');
        $this->form_validation->set_rules('numero_tel', lang('numero'), 'required');
        if ($this->form_validation->run() == false) {
            $errors = validation_errors();
            $resultado = array(
                'codigo' => '0',
                'msgerror' => $errors,
                'errNo' => ''
            );
        } else {
            $data_post['cod_facturante'] = $this->input->post('cod_facturante');
            $data_post['cod_razon_social'] = $this->input->post('cod_razon_social');
            $data_post['condicion'] = $this->input->post('condicion');
            $data_post['tipo_doc'] = $this->input->post('tipo_doc');
            $data_post['numero_Doc'] = $this->input->post('numero_Doc');
            $data_post['razon'] = $this->input->post('razon');
            $data_post['inicioActividad'] = formatearFecha_mysql($this->input->post('inicioActividad'));
            $data_post['localidad'] = $this->input->post('localidad');
            $data_post['direccion'] = $this->input->post('direccion') ? $this->input->post('direccion') : '';
            $data_post['numero'] = $this->input->post('numero') ? $this->input->post('numero') : '';
            $data_post['complemento'] = $this->input->post('complemento') ? $this->input->post('complemento') : '';
            $data_post['cod_telefono'] = $this->input->post('codigo_tel');
            $data_post['empresa'] = $this->input->post('empresa_tel');
            $data_post['cod_area'] = $this->input->post('cod_area');
            $data_post['numero'] = $this->input->post('numero_tel');
            $data_post['estado'] = !$this->input->post('estado') ? Vfacturantes::getEstadoHabilitado() : $this->input->post('estado');
            $resultado = $this->Model_facturantes->guardarFacturante($data_post);
        }
        echo json_encode($resultado);
    }

    /* la siguiente funcion esta siendo utilizada por un web services ***** NO BORRAR, COMENTAR NI MODIFICAR ***** */
    function getFacturantes() {
        $codFacturante = $this->input->post('codigo_facturante') ? $this->input->post("codigo_facturante") : null;
        $conexion = $this->load->database("default", true);
        if ($codFacturante != null) {
            $this->load->model("Model_webservices", "", false);
            $myFacturante = new Vfacturantes($conexion, $codFacturante);
            $puntos_venta = $myFacturante->getPuntosVenta();
            $razonSocial = $myFacturante->getRazonSocial();
            $myCertificado = $myFacturante->getCertificado();
            $arrResp = $this->Model_webservices->modelarFacturantes($myFacturante, $razonSocial, $puntos_venta, $myCertificado);
        } else {
            $arrResp = Vfacturantes::getFacturantes($conexion, 0, 0, true);
        }
        echo json_encode($arrResp);
    }

    /* la siguiente funcion esta siendo utilizada por un web services ***** NO BORRAR, COMENTAR NI MODIFICAR ***** */
    function gerRepositorioAltaFacturante() {
        $conexion = $this->load->database("default", true);
        $arrResp = array();
        $arrResp['tipos_facturas'] = Vtipos_facturas::listarTipos_facturas($conexion, array("habilitado" => 1));
        $arrResp['documentos_tipos'] = Vdocumentos_tipos::listarDocumentos_tipos($conexion);
        $arrResp['condiciones_sociales'] = Vcondiciones_sociales::listarCondiciones_sociales($conexion);
        $arrResp['facturantes_matrices'] = Vfacturantes::getFacturantes($conexion, null, 0, true, true);
        echo json_encode($arrResp);
    }

    /* la siguiente funcion esta siendo utilizada por un web services ***** NO BORRAR, COMENTAR NI MODIFICAR ***** */
    function guardar_facturante() {
        // agrega form validation al terminar las pruebas
        $arrResp = array();
        $conexion = $this->load->database("default", true);
        $conexion->trans_begin();
        $arrRazon = $this->input->post("razon_social");
        $myRazonSocial = new Vrazones_sociales_general($conexion, $arrRazon['codigo']);
        $myRazonSocial->cod_localidad = $arrRazon['cod_localidad'];
        $myRazonSocial->condicion = $arrRazon['condicion'];
        $myRazonSocial->direccion_calle = $arrRazon['direccion_calle'];
        $myRazonSocial->direccion_complemento = $arrRazon['direccion_complemento'];
        $myRazonSocial->direccion_numero = $arrRazon['direccion_numero'];
        $myRazonSocial->documento = $arrRazon['documento'];
        $myRazonSocial->razon_social = $arrRazon['razon_social'];
        $myRazonSocial->telefono_cod_area = $arrRazon['telefono_cod_area'];
        $myRazonSocial->telefono_numero = $arrRazon['telefono_numero'];
        $myRazonSocial->tipo_documento = $arrRazon['tipo_documento'];
        if (!$myRazonSocial->guardarRazones_sociales_general()) { // paramos el script para no para no continuar con proceso de servidor
            $conexion->trans_rollback();
            $arrResp['error'] = "Error al guardar la razon social del facturante";
            echo json_encode($arrResp);
            die();
        }
        $myFacturante = new Vfacturantes($conexion, $this->input->post("codigo"));
        $myFacturante->inicio_actividades = $this->input->post("inicio_actividades");
        $myFacturante->cod_razon_social = $myRazonSocial->getCodigo();
        $myFacturante->cod_facturante_matriz = $this->input->post("cod_facturante_matriz");
        $myFacturante->estado = $this->input->post("estado");
        if (!$myFacturante->guardarFacturantes()) { // paramos el script para no para no continuar con proceso de servidor
            $conexion->trans_rollback();
            $arrResp['error'] = "Error al guardar el facturante";
            echo json_encode($arrResp);
            die();
        }
        if (isset($_POST['certificado']) && is_array($_POST['certificado'])){
            if (isset($_POST['certificado']['cert']) && $_POST['certificado']['cert'] <> '' && 
                    isset($_POST['certificado']['pry_key']) && $_POST['certificado']['pry_key'] <> '' && 
                    isset($_POST['certificado']['pub_key']) && $_POST['certificado']['pub_key'] <> ''){
                $arrCertificado = $_POST['certificado'];
                $myCertificado = new Vfacturantes_certificados($conexion, $myFacturante->getCodigo());
                $myCertificado->cert = $arrCertificado['cert'];
                $myCertificado->fecha_expiracion = $arrCertificado['fecha_expiracion'];
                $myCertificado->password = $arrCertificado['password'];
                $myCertificado->pry_key = $arrCertificado['pry_key'];
                $myCertificado->pub_key = $arrCertificado['pub_key'];
                if (!$myCertificado->guardar()){    // paramos el script para no continuar con proceso de servidor
                    $conexion->trans_rollback();
                    $arrResp['error'] = "Error al guardar certificado facturante";
                    echo json_encode($arrResp);
                    die();
                }
            }
        }
        
        $arrPuntosTemp = array();
        if ($this->input->post('puntos_venta') && is_array($this->input->post('puntos_venta'))) {
            foreach ($this->input->post('puntos_venta') as $puntoVenta) {
                $myPuntoVenta = new Vpuntos_venta($conexion, $puntoVenta['codigo']);
                $myPuntoVenta->cod_facturante = $myFacturante->getCodigo();
                $myPuntoVenta->estado = $puntoVenta['estado'];
                $myPuntoVenta->medio = $puntoVenta['medio'];
                $myPuntoVenta->nro = $puntoVenta['nro'];
                $myPuntoVenta->prefijo = $puntoVenta['prefijo'];
                $myPuntoVenta->tipo_factura = $puntoVenta['tipo_factura'];
                $myPuntoVenta->webservice = isset($puntoVenta['webservice']) ? $puntoVenta['webservice'] : "0";
                if (!$myPuntoVenta->guardarPuntos_venta()) { // paramos el script para no para no continuar con proceso de servidor
                    $conexion->trans_rollback();
                    $arrResp['error'] = "Error al guardar puntos de venta";
                    echo json_encode($arrResp);
                    die();
                }
                if (!$myPuntoVenta->setFiliales($puntoVenta['filiales'])) { // paramos el script para no para no continuar con proceso de servidor
                    $conexion->trans_rollback();
                    $arrResp['error'] = "Error al asignar puntos de venta a las filiales";
                    echo json_encode($arrResp);
                    die();
                }
                $arrPuntosTemp['codigo'] = $myPuntoVenta->getCodigo();
                if (isset($puntoVenta['configuracion_proveedor']) && is_array($puntoVenta['configuracion_proveedor'])) {
                    $proveedor = $puntoVenta['configuracion_proveedor'][0];
                    $proveedorNombre = $proveedor['proveedor'];
                    switch ($proveedorNombre) {
                        case 'toolsnfe':
                            $myProveedor = new Vprestador_toolsnfe($conexion, $proveedor['codigo']);
                            $myProveedor->cfop = $proveedor['cfop'];
                            $myProveedor->cnae = $proveedor['cnae'];
                            $myProveedor->cod_punto_venta = $myPuntoVenta->getCodigo();
                            $myProveedor->codigo_numerico = $proveedor['codigo_numerico'];
                            $myProveedor->codigo_producto = $proveedor['codigo_producto'];
                            $myProveedor->descripcion = $proveedor['descripcion'];
                            $myProveedor->forma_pago = $proveedor['forma_pago'];
                            $myProveedor->icms = $proveedor['icms'];
                            $myProveedor->ie = $proveedor['ie'];
                            $myProveedor->inscripcion_municipal = $proveedor['inscripcion_municipal'];
                            $myProveedor->motivo = $proveedor['motivo'];
                            $myProveedor->ncm = $proveedor['ncm'];
                            $myProveedor->nombre_configuracion = $proveedor['nombre_configuracion'];
                            $myProveedor->nombre_producto = $proveedor['nombre_producto'];
                            $myProveedor->porcentaje_facturar = $proveedor['porcentaje_facturar'];
                            $myProveedor->regimen_tributario = $proveedor['regimen_tributario'];
                            $myProveedor->situacion_tributaria = $proveedor['situacion_tributaria'];
                            $myProveedor->transporte = $proveedor['transporte'];
                            $myProveedor->cfop_fisico = $proveedor['cfop_fisico'];
                            $myProveedor->cfop_juridico = $proveedor['cfop_juridico'];
                            if (!$myProveedor->guardarPrestador_toolsnfe()) { // paramos el script para no para no continuar con proceso de servidor
                                $conexion->trans_rollback();
                                $arrResp['error'] = "Error al guardar configuracion toolsnfe";
                                echo json_encode($arrResp);
                                die();
                            }
                            break;

                        case 'abrasf':
                            $myProveedor = new Vprestador_abrasf($conexion, $proveedor['codigo']);
                            $myProveedor->alicuota = $proveedor['alicuota'];
                            $myProveedor->cod_punto_venta = $myPuntoVenta->getCodigo();
                            $myProveedor->codigo_actividad = $proveedor['codigo_actividad'];
                            $myProveedor->incentivador_cultural = $proveedor['incentivador_cultural'];
                            $myProveedor->inscripcion_municipal = $proveedor['inscripcion_municipal'];
                            $myProveedor->item_lista_servicio = $proveedor['item_lista_servicio'];
                            $myProveedor->nombre_configuracion = $proveedor['nombre_configuracion'];
                            $myProveedor->nombre_servicio = $proveedor['nombre_servicio'];
                            $myProveedor->numero_factura = $proveedor['numero_factura'];
                            $myProveedor->porcentaje_facturar = $proveedor['porcentaje_facturar'];
                            $myProveedor->regimen_especial_tributario = $proveedor['regimen_especial_tributario'];
                            $myProveedor->serie_factura = $proveedor['serie_factura'];
                            $myProveedor->tipo_nota = $proveedor['tipo_nota'];
                            $myProveedor->valor_cofins = $proveedor['valor_cofins'];
                            $myProveedor->valor_csll = $proveedor['valor_csll'];
                            $myProveedor->valor_inss = $proveedor['valor_inss'];
                            $myProveedor->valor_ir = $proveedor['valor_ir'];
                            $myProveedor->valor_pis = $proveedor['valor_pis'];
                            if (!$myProveedor->guardarPrestador_abrasf()) { // paramos el script para no para no continuar con proceso de servidor
                                $conexion->trans_rollback();
                                $arrResp['error'] = "Error al guardar configuracion abrasf";
                                echo json_encode($arrResp);
                                die();
                            }
                            break;

                        case 'dsf':
                            $myProveedor = new Vprestador_dsf($conexion, $proveedor['codigo']);
                            $myProveedor->alicuota = $proveedor['alicuota'];
                            $myProveedor->alicuota_cofins = $proveedor['alicuota_cofins'];
                            $myProveedor->alicuota_csll = $proveedor['alicuota_csll'];
                            $myProveedor->alicuota_inss = $proveedor['alicuota_inss'];
                            $myProveedor->alicuota_ir = $proveedor['alicuota_ir'];
                            $myProveedor->alicuota_pis = $proveedor['alicuota_pis'];
                            $myProveedor->cnae = $proveedor['cnae'];
                            $myProveedor->cod_punto_venta = $myPuntoVenta->getCodigo();
                            $myProveedor->codigo_actividad = $proveedor['codigo_actividad'];
                            $myProveedor->codigo_servicio = $proveedor['codigo_servicio'];
                            $myProveedor->inscripcion_municipal = $proveedor['inscripcion_municipal'];
                            $myProveedor->nombre_configuracion = $proveedor['nombre_configuracion'];
                            $myProveedor->nombre_servicio = $proveedor['nombre_servicio'];
                            $myProveedor->porcentaje_facturar = $proveedor['porcentaje_facturar'];
                            $myProveedor->tipo_nota = $proveedor['tipo_nota'];
                            $myProveedor->valor_cofins = $proveedor['valor_cofins'];
                            $myProveedor->valor_csll = $proveedor['valor_csll'];
                            $myProveedor->valor_inss = $proveedor['valor_inss'];
                            $myProveedor->valor_ir = $proveedor['valor_ir'];
                            $myProveedor->valor_pis = $proveedor['valor_pis'];
                            if (!$myProveedor->guardarPrestador_dsf()) { // paramos el script para no para no continuar con proceso de servidor
                                $conexion->trans_rollback();
                                $arrResp['error'] = "Error al guardar configuracion dsf";
                                echo json_encode($arrResp);
                                die();
                            }
                            break;

                        case 'ginfes':
                            $myProveedor = new Vprestador_ginfes($conexion, $proveedor['codigo']);
                            $myProveedor->alicuota = $proveedor['alicuota'];
                            $myProveedor->cod_punto_venta = $myPuntoVenta->getCodigo();
                            $myProveedor->codigo_tributacion_municipio = $proveedor['codigo_tributacion_municipio'];
                            $myProveedor->incentivador_cultural = $proveedor['incentivador_cultural'];
                            $myProveedor->inscripcion_municipal = $proveedor['inscripcion_municipal'];
                            $myProveedor->item_lista_servicio = $proveedor['item_lista_servicio'];
                            $myProveedor->nombre_configuracion = $proveedor['nombre_configuracion'];
                            $myProveedor->numero_serie = $proveedor['numero_serie'];
                            $myProveedor->optante_simples_nacional = $proveedor['optante_simples_nacional'];
                            $myProveedor->porcentaje_facturar = $proveedor['porcentaje_facturar'];
                            $myProveedor->regimen_especial_tibutario = $proveedor['regimen_especial_tributario'];
                            $myProveedor->tipo_nota = $proveedor['tipo_nota'];
                            $myProveedor->valor_cofins = $proveedor['valor_cofins'];
                            $myProveedor->valor_csll = $proveedor['valor_csll'];
                            $myProveedor->valor_inss = $proveedor['valor_inss'];
                            $myProveedor->valor_ir = $proveedor['valor_ir'];
                            $myProveedor->valor_pis = $proveedor['valor_pis'];
                            if (!$myProveedor->guardarPrestador_ginfes()) { // paramos el script para no para no continuar con proceso de servidor
                                $conexion->trans_rollback();
                                $arrResp['error'] = "Error al guardar configuracion ginfes";
                                echo json_encode($arrResp);
                                die();
                            }
                            break;
                    }
                }
            }
        }
        if ($conexion->trans_status()) {
            $conexion->trans_commit();
            $arrResp['success'] = "success";
            $arrResp['facturante'] = $myFacturante->getCodigo();
            $arrResp['puntos_venta'] = $arrPuntosTemp;
            $arrResp['razon_social'] = $myRazonSocial->getCodigo();
        } else {
            $conexion->trans_rollback();
            $arrResp['error'] = $conexion->_error_message();
            $arrResp['error_nro'] = $conexion->__error_number();
        }
        echo json_encode($arrResp);
    }

    /* esta funcion esta siendo accedida desde un web services ****** NO MODIFICAR, COMENTAR NI ELIMINAR  ****** */
    function get_configuraciones_facturacion_electronica() {
        $arrResp = array();
        $conexion = $this->load->database("default", true);
        $tipoFactura = $this->input->post("tipo_factura");
        $cod_punto_venta = $this->input->post("cod_punto_venta");
        $filiales = $this->input->post("filiales");
        $metodos_configuracion = array();
        if ($tipoFactura == 15) {
            $metodos_configuracion = Vfiliales_metodos_facturacion::get_metodos_facturacion_producto($conexion, $filiales);
        } else if ($tipoFactura == 16) {
            $metodos_configuracion = Vfiliales_metodos_facturacion::get_metodos_facturacion_servicio($conexion, $filiales);
        } else {
            $arrResp['error'] = "No se ha implementado configuracion para el tipo de factura buscado";
            $arrResp['error_numero'] = "1000";
        }
        if (count($metodos_configuracion) <> 1) {
            $arrResp['error'] = "Las filiales indicadas nos son compatibles con los metodos de facturacion";
            $arrResp['error_numero'] = "1001";
        } else if ($metodos_configuracion[0]['proveedor'] == Vfiliales_metodos_facturacion::getMetodoNoFactura() ||
                $metodos_configuracion[0]['proveedor'] == '') {
            $arrResp['error'] = "La o las filiales indicadas no facturan por el tipo de factura seleccionado";
            $arrResp['error_numero'] = "1002";
        } else {
            $this->load->model("Model_webservices", "", false);
            $myPuntoVenta = new Vpuntos_venta($conexion, $cod_punto_venta);
            $object = $myPuntoVenta->getConfiguracionFacturacionElectronica($metodos_configuracion[0]['proveedor']);
            $arrResp = $this->Model_webservices->modelarConfiguracionFacturacionElectronica($object, $metodos_configuracion[0]['proveedor']);

            //var_dump($arrResp);
        }
        echo json_encode($arrResp);
    }

    /* esta funcion esta siendo accedida desde un web services ****** NO MODIFICAR, COMENTAR NI ELIMINAR  ****** */
    public function subir_certificado_digital() {
        $arrResp = array();
        if (isset($_FILES)) {
            $filename = md5(date("Y-m-dH:i:s") . $_FILES['filedata']['tmp_name']);
            $input = S3::inputFile($_FILES['filedata']['tmp_name']);
            $nombreArchivo = Vfacturantes_certificados::getPathToFile() . "$filename.pfx";
            if (S3::putObject($input, "igacloud", $nombreArchivo, "private")){
                $arrResp['success'] = "success";
                $arrResp['file_name'] = $filename;
            } else {
                $arrResp['error'] = "Error al mover el archivo";
            }
//            if (copy($_FILES['filedata']['tmp_name'], Vfacturantes_certificados::getPathToFile() . "$filename.pfx")) {
//                $arrResp['success'] = "success";
//                $arrResp['file_name'] = $filename;
//            } else {
//                $arrResp['error'] = "Error al mover el archivo";
//            }
        } else {
            $arrResp['error'] = "No se encuentra el archivo de certificado digital";
        }
        echo json_encode($arrResp);
    }

    public function getRemessasDatatable() {
        $arrFiltros["iDisplayStart"] = $this->input->post("iDisplayStart");
        $arrFiltros["iDisplayLength"] = $this->input->post("iDisplayLength");
        $arrFiltros["sSearch"] = $this->input->post("sSearch");
        $arrFiltros["sEcho"] = $this->input->post("sEcho");
        echo json_encode($this->Model_facturantes->getRemesas($arrFiltros));
    }



    public function getRemessasEnviadasDatatable() {
        $arrFiltros["iDisplayStart"] = $this->input->post("iDisplayStart");
        $arrFiltros["iDisplayLength"] = $this->input->post("iDisplayLength");
        $arrFiltros["sSearch"] = $this->input->post("sSearch");
        $arrFiltros["sEcho"] = $this->input->post("sEcho");
        echo json_encode($this->Model_facturantes->getRemesasEnviadas($arrFiltros));
    }



    public function getRetornosDatable() {
        $arrFiltros["iDisplayStart"] = $this->input->post("iDisplayStart");
        $arrFiltros["iDisplayLength"] = $this->input->post("iDisplayLength");
        $arrFiltros["sSearch"] = $this->input->post("sSearch");
        $arrFiltros["sEcho"] = $this->input->post("sEcho");
        echo json_encode($this->Model_facturantes->getRetornos($arrFiltros));
    }

    public function descargarRemessa($codigo) {
        Header("Content-Disposition: attachment; filename=$codigo.rem");
        $arrRespuesta = $this->Model_facturantes->getRemesa($codigo);
        if ($arrRespuesta["codigo"] == 1) {
            echo $arrRespuesta["respuesta"];
        }
    }

    public function sendRetorno() {
        $retorno = array();
        if (count($_FILES['archivoretorno']) == 0) {
            $retorno["codigo"] = "0";
            $retorno["respuesta"] .= "NO_HAY_ARCHIVO";
        } else {
            foreach ($_FILES['archivoretorno']["tmp_name"] as $key => $archivo) {
                $retorno[] = $this->Model_facturantes->confirmarRetorno($archivo, $_FILES['archivoretorno']["name"][$key]);
            }
        }
        echo json_encode($retorno);
    }

    public function confirmarcionRetorno() {
        $retorno = array();
        if (count($_FILES['archivoretorno']) == 0) {
            $retorno["codigo"] = "0";
            $retorno["respuesta"] .= "NO_HAY_ARCHIVOS";
        } else {
            foreach ($_FILES['archivoretorno']["tmp_name"] as $archivo) {
                $retorno[] = $this->Model_facturantes->getConfirmarcionRetorno($archivo);
            }
        }
        echo json_encode($retorno);
    }

    public function sendResumenCobros() {
        $retorno = array();
        $arrErroresPrioridadMedia = Vvan_cielo::getErroresPrioridadMedia();
        $arrErroresPrioridadAlta = Vvan_cielo::getErroresPrioridadAlta();
        foreach ($_FILES['archivoretorno']["tmp_name"] as $key => $archivo) {            
            $retorno[$key]["respuesta"] = 0;
            $retorno[$key]["error"] = "";
            $retorno[$key]["prioridad"] = "";
            $retorno[$key]["archivo"] = $_FILES['archivoretorno']["name"][$key];
            try {
                $retorno[$key]["respuesta"] = $this->Model_facturantes->sendArchivoResumenCobro($_FILES['archivoretorno']["tmp_name"][$key], $this->input->post("provedores"), $_FILES['archivoretorno']["name"][$key]);
            } catch (Exception $exc) {
                $error = $exc->getMessage();
                $retorno[$key]['respuesta'] = false;
                $retorno[$key]["error"] = lang($error);
                if (in_array($error, $arrErroresPrioridadAlta)){
                    $retorno[$key]['prioridad'] = "alta";
                } else if (in_array($error, $arrErroresPrioridadMedia)){
                    $retorno[$key]['prioridad'] = "media";
                } else {
                    $retorno[$key]['prioridad'] = "baja";
                }
            }
        }
        echo json_encode($retorno);
    }
    
    public function getResumenesCobros(){
        $arrColumns = $this->getColumnsResumenesCargados();
        $arrSort = null;
        $arrSearch = null;
        $arrLimit = null;
        $sSearch = $this->input->post("sSearch") ? $this->input->post("sSearch") : null;
        if ($sSearch != null){
            foreach ($arrColumns as $columns){
                $arrSearch[$columns['field']] = $sSearch;
            }
        }
        $iSortCol = $this->input->post("iSortCol_0") ? $this->input->post("iSortCol_0") : null;
        if ($iSortCol != null){ 
            $arrSort = array($arrColumns[$iSortCol]['order'] => $this->input->post("sSortDir_0"));
        }
        if ($this->input->post('iDisplayLength')){
            $arrLimit[0] = $this->input->post('iDisplayStart');
            $arrLimit[1] = $this->input->post('iDisplayLength');
        }
        $arrResp = $this->Model_facturantes->getAchivosResumenCobros($arrSearch, $arrSort, $arrLimit);
        $arrResp['sEcho'] = $this->input->post("sEcho");
        echo  json_encode($arrResp);
    }

    /* La siguiente function esta siendo accedida desde un web services *** NO MODIFICAR, COMENAR NI ELIMINAR  *** */
    function getFilialesDisponiblesAFacturar(){
        $codFacturante = $this->input->post("cod_facturante") ? $this->input->post("cod_facturante") : null;
        $codMatriz = $this->input->post("cod_facturante_matriz") ? $this->input->post("cod_facturante_matriz") : null;
        $conexion = $this->load->database("default", true);//        Vvan_cielo::conciliar($conexion, $nsu, $coedigoAutorizacion, $terminal, $valorVenta, $tipoCaptura);
        $arrResp = Vfiliales::getFilialesDisponiblesAFacturar($conexion, $codFacturante, $codMatriz);
        echo json_encode($arrResp);
    }
    
    private function getColumnsResumenesCargados(){
        $arrResp = array();
        $arrResp[0] = array("order" => "nombre_archivo", "field" => "nombre_archivo", "nombre" => lang("nombre_archivo"));
        $arrResp[1] = array("order" => "establecimiento_matriz", "field" => "establecimiento_matriz", "nombre" => lang("matriz"));
        $arrResp[2] = array("order" => "secuencia", "field" => "secuencia", "nombre" => lang("secuencial"));
        $arrResp[3] = array("order" => "periodo_inicial", "field" => "periodo_inicial", "nombre" => lang("fecha_inicio"));
        $arrResp[4] = array("order" => "periodo_final", "field" => "periodo_final", "nombre" => lang("fecha_fin"));        
        return $arrResp;
    }
    
    public function getCertificado() {
        $facturante = $this->input->post('facturante');

        $resultado = $this->Model_facturantes->getCertificado($facturante);

        echo json_encode($resultado);
    }

    public function remessaToFTP() {
        $ids = isset($_POST['ids'])?$_POST['ids']:"";

        $response['estado'] = $this->Model_facturantes->moverAFTP($ids);
        echo json_encode($response);
    }

    public function todasLasRemessasToFTP () {
        $resultado = $this->Model_facturantes->moverTodoAFTP();
        echo $resultado;
    }

    public function FTPtoRetorno() {
    /*
        $archivos = scandir('/ftp/retorno/');
        $retornos = array(); //Vengo de Java, me encanta declarar variables.
        foreach($archivos as $retorno){
            if(is_dir($retorno)){
                continue;
            }
            $archivo = file_get_contents('/ftp/retorno/'.$retorno);
            $retornos[] = $this->Model_facturantes->getConfirmarcionRetorno('/ftp/retorno/' . $retorno);
        }
        echo json_encode($retornos);
        */
        $archivos = scandir('/ftp/retorno/');
        $retornos = array();
        foreach($archivos as $retorno){
            if(is_dir($retorno)){
                continue;
            }
            $a = array();
            $a['path'] = '/ftp/retorno/'.$retorno;
            $a['archivo'] = $retorno;
            $retornos[] = $a;
        }
        $ret = array();
        foreach($retornos as $retorno) {
            $ret[] = $this->Model_facturantes->confirmarRetorno($retorno['path'], $retorno['archivo']);
        }
        echo json_encode($ret);
    }
}

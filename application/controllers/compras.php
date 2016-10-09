<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Compras extends CI_Controller {

    public $columnas = array();

    public function __construct() {
        parent::__construct();
        session_method();
        $filial = $this->session->userdata('filial');
        $config = array("codigo_filial" => $filial["codigo"]);
        $this->load->model("Model_compras", "", false, $config);
        $this->lang->load(get_idioma(), get_idioma());
        $this->load->helper("datatables");
        $this->columnas = array(
            array("nombre" => lang('codigo'), "campo" => 'compras.codigo'),
            array("nombre" => lang('nombre'), "campo" => 'razones_sociales.razon_social'),
            array("nombre" => lang('fecha'), "campo" => 'compras.fecha'),
            array("nombre" => lang('totalCompra_compras'), "campo" => 'totalCompra'),
            array("nombre" => lang('baja_compras'), "campo" => 'baja', "sort" => false, 'bVisible' => false),
            array("nombre" => lang('estado_compra'), "campo" => 'estado', "sort" => false),
            array("nombre" => lang('usuario'), "campo" => 'usuario_creador')
        );
    }

    public function index() {
        $this->lang->load(get_idioma(), get_idioma());
        $valida_session = session_method();
        $claves = array("habilitar-factura",
                        "deshabilitar-factura",
                        "codigo",
                        "facturacion_estado",
                        "facturacion_anular",
                        "habilitada",
                        "inhabilitada",
                        "descripcion",
                        "importe",
                        "iva",
                        "INHABILITAR",
                        "HABILITAR",
                        "HABILITADO",
                        "INHABILITADO",
                        "error_habilitar_proveedor",
                        "errir_inhabilitar_proveedor",
                        "nuevo_articulo",
                        "compras",
                        "modificar_articulo",
                        "eliminar_articulo",
                        "nuevo_proveedor",
                        "validacion_ok",
                        "BIEN",
                        "ocurrio_error",
                        "ERROR"
        );
        
        $data['lang'] = getLang($claves);
        $data['menuJson'] = getMenuJson('compras');
        $data['columns'] = $this->getColumns();	
        $data['page_title'] = 'Título de la Página';
        $data['page'] = 'compras/vista_compras'; // pasamos la vista a utilizar como parámetro
        $data['seccion'] = $valida_session;
        $this->load->view('container', $data);
    }

    public function listar() {
        $arrFiltros["iDisplayStart"] = isset($_POST['iDisplayStart']) ? $_POST['iDisplayStart'] : "";
        $arrFiltros["iDisplayLength"] = isset($_POST['iDisplayLength']) ? $_POST['iDisplayLength'] : "";
        $arrFiltros["sSearch"] = isset($_POST['sSearch']) ? $_POST['sSearch'] : "";
        $arrFiltros["sEcho"] = isset($_POST['sEcho']) ? $_POST['sEcho'] : "";
        $arrFiltros["SortCol"] = isset($_POST['iSortCol_0']) ? $this->columnas[$_POST['iSortCol_0']]["campo"] : "";
        $arrFiltros["sSortDir"] = isset($_POST['sSortDir_0']) ? $_POST['sSortDir_0'] : "";

        $comprasProveedores = $this->Model_compras->listarComprasDataTable($arrFiltros);

        echo json_encode($comprasProveedores);
    }

    public function getColumns() {
        $aoColumnDefs = json_encode(getColumnsDatatable($this->columnas));
        return  $aoColumnDefs;
    }

    public function frm_compras() {
        $filial = $this->session->userdata('filial');
        $codUsuario = $this->session->userdata('codigo_usuario');
        $config = array("codigo_filial" => $filial['codigo']);
        $configUsuario = array("filial" => $filial['codigo']);
        $cod_compra = $this->input->post('codigo');
        $continuar = $this->input->post('continuar') != '' ? $this->input->post('continuar') : false;
        $data = '';
        //CARGO MODELOS
        $this->load->model("Model_caja", "", false, $config);
        $this->load->model("Model_usuario", "", false, $configUsuario);
        $this->load->model("Model_paises", "", false, $filial["pais"]);
        $this->load->model("Model_proveedores", "", false, $config);
        $this->load->model("Model_comprobantes", "", false, $config);
        $this->load->model("Model_facturas", "", false, $config);
        $this->load->model("Model_articulos", "", false, $config);
        $this->load->model("Model_articulos_categorias", "", false, $config);

        //LLENO ARRAY'S PARA LA VISTA
        $cantidadCajasAbiertas = $this->Model_usuario->getCajas($codUsuario, 0, 1, true);
        if ($cantidadCajasAbiertas > 0 || $continuar) {
            $validar_session = $validar_session = session_method();
                
            $claves=array("validacion_ok","categoria","producto","cantidad","precio_unitario","impuestos","total_impuestos","precio_total","agregar",
                        "punto_venta","nro_comprobante","fecha","tipo_comprobante","total","medio_de_pago","caja","seleccione_caja",'no_se_puede_borrar_compra_articulo',
                    "articulos");
            $data['langFrm'] = getLang($claves);
            $data['cajas'] = $this->Model_caja->getCajas();
            $data['mediosPago'] = $this->Model_paises->getMediosPagos(true);
            $data['proveedores'] = $this->Model_proveedores->getProveedores(true);
            $data['comprobantes'] = $this->Model_comprobantes->getComprobates($filial["pais"]);
           
            $data['tiposFactura'] = $this->Model_facturas->getTiposFacturasCompras();
            $data['categorias'] = $this->Model_articulos_categorias->getCategorias(); //ver si activas
            $data['impuestos'] = $this->Model_compras->getImpuestos();
            $data['moneda'] = $filial["moneda"];
            $data['cod_compra'] = $cod_compra;
            if ($cod_compra != -1) {//no deberia dejar modificar una compra con pagos confirmados y caja cerrada
                $data['objCompra'] = $this->Model_compras->getObjCompra($cod_compra);
                $data['renglones'] = $this->Model_compras->getCompraRenglones($cod_compra);
                $data['comprobantesCompra'] = $this->Model_compras->getComprobantes($cod_compra);
                $data['pagosCompra'] = $this->Model_compras->getPagosImputados($cod_compra);
            }

            $this->load->view('compras/frm_compras', $data);
        } else {
            $data['ejecutar_script'] = "nuevo_compra()"; //armar script
            $data['cod_compra'] = $cod_compra;
            $this->load->view('caja/adv_cajas_cerradas', $data);
        }
    }
    
    public function getCajasCompras(){
        $cod_medio = $this->input->post('medio_pago');
        $cod_compra = $this->input->post('cod_compra');
        $cod_usuario = $this->session->userdata('codigo_usuario');
        $resultado = $this->Model_compras->getCajasMedio($cod_medio, $cod_compra, $cod_usuario);
        echo json_encode($resultado);
    }

    public function guardarCompra() {
        $this->load->library('form_validation');
        $this->load->helper('formatearfecha');
        $resultado = '';
        $arrayComprobantes = '';
        //VALIDACIONES DATOS DE LA COMPRA
        $renglones = $this->input->post('renglones') != '' ? $this->input->post('renglones') : array();
        $comprobante = $this->input->post('comprobante') != '' ? $this->input->post('comprobante') : array();
        $pago = $this->input->post('pago') != '' ? $this->input->post('pago') : array();
        $cod_compra = $this->input->post('cod_compra');


        $this->form_validation->set_rules('cod_proveedor', lang('codigo_proveedor'), 'required|numeric');
        $this->form_validation->set_rules('fecha', lang('fecha_compra'), 'required');
        $i = 1;
        foreach ($renglones as $rowrenglon) {
            $_POST['cod_articulo' . $i] = $rowrenglon['cod_articulo'];
            $_POST['cantidad' . $i] = $rowrenglon['cantidad'];
            $_POST['precio_unitario' . $i] = $rowrenglon['precio_unitario'];
            $_POST['impuestos' . $i] = isset($rowrenglon['impuestos']) ? $rowrenglon['impuestos'] : array();

            $this->form_validation->set_rules('cod_articulo' . $i, lang('codigo_articulo') . ' ' . lang('linea') . ' ' . $i, 'required');
            $this->form_validation->set_rules('cantidad' . $i, lang('cantidad') . ' ' . lang('linea') . ' ' . $i, 'required');
            $this->form_validation->set_rules('precio_unitario' . $i, lang('precio_unitario') . ' ' . lang('linea') . ' ' . $i, 'required');
            $this->form_validation->set_rules('impuestos' . $i . '[]', lang('impuestos') . ' ' . lang('linea') . ' ' . $i, '');
            $i++;
        }
        $i = 1;

        foreach ($comprobante as $key => $rowcomprobante) {
            $_POST['tipo_comprobante' . $i] = $rowcomprobante['tipo_comprobante'];
            $_POST['precio_total' . $i] = $rowcomprobante['precio_total'];

            $this->form_validation->set_rules('tipo_comprobante' . $i, lang('tipo_comprobante') . ' ' . lang('linea') . ' ' . $i, 'required');
            $this->form_validation->set_rules('precio_total' . $i, lang('precio_total_comprobante') . ' ' . lang('linea') . ' ' . $i, 'required');
            $i++;
            $comprobante[$key]['jsonDecodeado'] = json_decode($rowcomprobante['tipo_comprobante'], true);
            $comprobante[$key]['fecha_comprobante'] = formatearFecha_mysql($rowcomprobante['fecha_comprobante']);
        }

        $j = 1;
        
        foreach ($pago as $key => $rowpago) {//validar que si es modificacion solo si la misma caja permanece abierta
            $_POST['fecha_pago' . $i] = $rowpago['fecha_pago'];
            $_POST['medio_pago' . $i] = $rowpago['medio_pago'];
            $_POST['precio_total' . $i] = $rowpago['precio_total'];
            $_POST['cod_caja'.$i] = $rowpago['cod_caja'];
            $this->form_validation->set_rules('fecha_pago' . $i, lang('fecha_pago') . ' ' . lang('linea') . ' ' . $i, 'required');
            $this->form_validation->set_rules('medio_pago' . $i, lang('medio_pago') . ' ' . lang('linea') . ' ' . $i, 'required');
            $this->form_validation->set_rules('precio_total' . $i, lang('precio_total_pago') . ' ' . lang('linea') . ' ' . $i, 'required');
            $this->form_validation->set_rules('cod_caja' . $i, lang('caja') . ' ' . lang('linea') . ' ' . $i, 'required|validarCaja');
            $pago[$key]['fecha_pago'] = $rowpago['fecha_pago'] != '' ? formatearFecha_mysql($rowpago['fecha_pago']) : '';
            $j++;
        }
        $errors='';
        $validarTotalCompraComprobate = $this->validarTotalCompras_Comprobate($renglones,$comprobante);
        $validarTotalCompraPago = $this->validarTotalCompras_Pagos($renglones, $pago);
        if($validarTotalCompraComprobate == FALSE){
            $errors = 'Total de compra tiene que ser menor al total de Comprobantes';
        }
        if($validarTotalCompraPago == FALSE){
            $errors = 'Total de compra tiene que ser menor al total de Pagos';
        }

        if ($this->form_validation->run() == FALSE) {
            $errors .= validation_errors();
            $resultado = array(
                'codigo' => '0',
                'msgerror' => $errors
            );
        } else {
            $compra['compras']['codigo'] = $cod_compra;
            $compra['compras']['cod_proveedor'] = $this->input->post('cod_proveedor');
            $compra['compras']['cod_caja'] = $this->input->post('cod_caja');
            $compra['compras']['cod_usuario_creador'] = $this->session->userdata('codigo_usuario');
            $compra['compras']['fecha'] = formatearFecha_mysql($this->input->post('fecha'));
            $compra['renglones'] = $renglones;
            $compra['comprobante'] = $comprobante;
            $compra['pago'] = $pago;
            $compra['renglonesBajas'] = $this->input->post('renglonesBajas') ? $this->input->post('renglonesBajas') : array();
            $compra['comprobanteBajas'] = $this->input->post('comprobanteBajas') ? $this->input->post('comprobanteBajas') : array();
            $compra['pagoBajas'] = $this->input->post('pagoBajas') ? $this->input->post('pagoBajas') : array();
            $resultado = $this->Model_compras->guardarCompra($compra);
        }
        echo json_encode($resultado);
    }

    public function getComprobantes() {
        $cod_compra = $this->input->post('cod_compra');
        $comprobantes = $this->Model_compras->getComprobantes($cod_compra);
        echo json_encode($comprobantes);
    }
    
    private function validarTotalCompras_Comprobate($renglones,$comprobante){
        $totalRenglonesCompras = $this->totalRenglonesCompras($renglones);
        $totalComprobanteCompras = $this->totalComprobantesCompras($comprobante);
        if($comprobante != ''){
            if($totalComprobanteCompras <= $totalRenglonesCompras){
                 return true;
            }else{
                return false;
            }
        }
        
        
    }
    
    private function validarTotalCompras_Pagos($renglones,$pago){
        $totalRenglonesCompras = $this->totalRenglonesCompras($renglones);
        $totalPagosCompras = $this->totalPagosCompra($pago);
        if($pago != ''){
            if($totalPagosCompras <= $totalRenglonesCompras){
                 return true;
            }else{
                return false;
            }
        }
    }
    
    private function totalRenglonesCompras($renglones){
        $total = 0;
        foreach($renglones as $rowRenglones){
            $total = $total + $rowRenglones['precio_total'];
        }
        return $total;
    }
    
    private  function totalComprobantesCompras($comprobante){
        $total = 0;
        foreach($comprobante as $rowComprobante){
            $total = $total + $rowComprobante['precio_total'];
        }
        return $total;
    }
    
    private function totalPagosCompra($pago){
         $total = 0;
        foreach($pago as $rowPago){
            $total = $total + $rowPago['precio_total'];
        }
        return $total;
    }

    public function getPagos() {
        $cod_compra = $this->input->post('cod_compra');
        $pagos = $this->Model_compras->getPagosImputados($cod_compra);
        echo json_encode($pagos);
    }

    public function getImportesRenglones() {//precio total, impuestos discriminados,total
//        $this->load->library('form_validation');
//        //$this->form_validation->set_rules('', lang(''), 'required');
//        $resultado = array();
//
//        if ($this->form_validation->run() == FALSE) {
//            $errors = validation_errors();
//            $resultado['codigo'] = '0';
//            $resultado['msgerror'] = $errors;
//            $resultado['errNo'] = '';
//        } else {
        $datos = $this->input->post("renglones");
        $resultado = $this->Model_compras->getImportesRenglones($datos);
//        }
        echo json_encode($resultado);
    }

    public function getImpuestoArticulo() {
        $filial = $this->session->userdata('filial');
        $config = array("codigo_filial" => $filial['codigo']);
        $this->load->model("Model_articulos", "", false, $config);
        $cod_articulo = $this->input->post('cod_articulo');
        $impuestos = $this->Model_articulos->getImpuestosAsignados($cod_articulo);
        echo json_encode($impuestos);
    }

    public function getArticulos() {
        $filial = $this->session->userdata('filial');
        $config = array("codigo_filial" => $filial['codigo']);
        $this->load->model("Model_articulos_categorias", "", false, $config);
        $cod_categoria = $this->input->post('cod_categoria');
        $impuestos = $this->Model_articulos_categorias->getArticulos($cod_categoria);
        echo json_encode($impuestos);
    }

    public function verarray() {

        print_r($this->input->post('renglones'));
    }

    public function cambiarEstado() {
        $this->load->library('form_validation');
        $cod_usuario = $this->session->userdata('codigo_usuario');
        $codigo = $this->input->post('codigo');
        $this->form_validation->set_rules('codigo', lang('codigo'), 'numeric|validarCambioEstadoCompra');
        if ($this->form_validation->run() == FALSE) {
            $respuesta = array('codigo' => 0,
                'errors' => validation_errors());
        } else {
            $respuesta = $this->Model_compras->cambiarEstado($codigo);
        }
        echo json_encode($respuesta);
    }

}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */
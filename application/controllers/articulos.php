<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Articulos extends CI_Controller {
private $seccion;
    public function __construct() {
        parent::__construct();
        $this->lang->load(get_idioma(), get_idioma());
        $this->seccion = session_method();
        $filial = $this->session->userdata('filial');
        $config = array("codigo_filial" => $filial["codigo"]);
        $this->load->model("Model_articulos", "", false, $config);
        
        $this->load->helper("datatables");
        
    }

    public function index() {
       $data['page_title'] = 'Título de la Página';
        $data['page'] = 'articulos/vista_articulos'; // pasamos la vista a utilizar como parámetro
        $data['seccion'] = $this->seccion;
        $this->load->view('container', $data);
    }
    public function crearColumnas(){
        $columnas= array(
            array("nombre" => lang('codigo'), "campo" => 'codigo'),
            array("nombre" => lang('nombre'), "campo" => 'nombre'),
            array("nombre" => lang('costo'), "campo" => 'costo'),
            array("nombre" => lang('unidad_de_medida'), "campo" => 'general.tipo_unidades_medida.unidad', "sort" => false),
            array("nombre" => lang('categoria'), "campo" => 'articulos_categorias.nombre'),
            array("nombre" => lang('stock'), "campo" => 'stock'),
            array("nombre" => lang('estado_articulo'), "campo" => 'estado', "sort" => false, 'bVisible' => false),
            array("nombre" => lang('estado'), "campo" => 'estado', "sort" => false)
        );
        return $columnas;
    }
   
    public function listar() {
       $crearColumnas = $this->crearColumnas();

        $arrFiltros["iDisplayStart"] = isset($_POST['iDisplayStart']) ? $_POST['iDisplayStart'] : "";
        $arrFiltros["iDisplayLength"] = isset($_POST['iDisplayLength']) ? $_POST['iDisplayLength'] : "";
        $arrFiltros["sSearch"] = isset($_POST['sSearch']) ? $_POST['sSearch'] : "";
        $arrFiltros["sEcho"] = isset($_POST['sEcho']) ? $_POST['sEcho'] : "";
        $arrFiltros["SortCol"] = isset($_POST['iSortCol_0']) ? $crearColumnas[$_POST['iSortCol_0']]["campo"] : "";
        $arrFiltros["sSortDir"] = isset($_POST['sSortDir_0']) ? $_POST['sSortDir_0'] : "";

        $articulos = $this->Model_articulos->listarArticulosDataTable($arrFiltros);

        echo json_encode($articulos);
    }
    
    

    public function getColumns() {
        $aoColumnDefs = json_encode(getColumnsDatatable($this->crearColumnas()));
        echo $aoColumnDefs;
    }

    public function frm_articulos() {
        
        $data = '';
        $filial = $this->session->userdata('filial');
        $this->load->helper('filial');
        $config = array("codigo_filial" => $filial["codigo"]);
        $this->lang->load(get_idioma(), get_idioma());
        $this->load->model("Model_tipos_unidades_medida", "", false, $config);
        $this->load->model("Model_articulos_categorias", "", false, $config);
        $this->load->model("Model_impuestos", "", false, $config);
        $this->load->model("Model_articulos", "", false, $config);
        $cod_articulo = $this->input->post('cod_articulo');

        $data['unidades'] = $this->Model_tipos_unidades_medida->getUnidades();
        $data['categorias'] = $this->Model_articulos_categorias->getCategorias();
        $data['impuestos'] = $this->Model_impuestos->getImpuestos();
        $claves=array("validacion_ok");
        $data['langFrm'] = getLang($claves);
        if ($cod_articulo != -1) {
            $objArticulo = $this->Model_articulos->getObjArticulo($cod_articulo);
            $data['objArticulo'] = $objArticulo;
            $data['articuloImpuestos'] = $this->Model_articulos->getImpuestosAsignados($cod_articulo);
            $stock= formatearImporte($objArticulo->stock);
            $stockFormateado = str_replace('$', '', $stock);
            $corto = formatearImporte($objArticulo->costo);
            $costoFormateado = str_replace('$', '', $corto);
           
            $data['importesFormateados'] = array(
                'stockformateado'=> $stockFormateado,
                'costoformateado'=> $costoFormateado
            );
        }
//        echo '<pre>';
//        print_r($data);
//        echo '</pre>';
        $this->load->view('articulos/frm_articulos', $data);
    }

    public function guardar() {
        $filial = $this->session->userdata('filial');
        $usuario = $this->session->userdata('codigo_usuario');
        $separador = $filial['moneda']['separadorDecimal'];
        $resultado = '';
        $this->load->library('form_validation');
        $this->form_validation->set_rules('nombre', lang('nombre'), 'required|max_length[255]');
        $this->form_validation->set_rules('costo', lang('costo'), 'validarExpresionTotal');
        $this->form_validation->set_rules('cod_unidad', lang('cod_unidad_medida'), 'integer');
        $this->form_validation->set_rules('stock', lang('stock'), 'validarExpresionTotal');
        //$this->form_validation->set_rules('impuestos',lang('impuestos'),'required');
        //$impuestos = isset($this->input->post('impuestos')) ? $this->input->post('impuestos') : array();

        if ($this->form_validation->run() == FALSE) {
            $errors = validation_errors();

            $resultado = array(
                'codigo' => '0',
                'msgerror' => $errors,
                'errNo' => '',
            );
        } else {

            $data_post['articulo']['codigo'] = $this->input->post('codigo');
            $data_post['articulo']['nombre'] = $this->input->post('nombre');
            $data_post['articulo']['costo'] = str_replace($separador,'.',$this->input->post('costo'));
            $data_post['articulo']['cod_unidad_medida'] = $this->input->post('cod_unidad');
            $data_post['articulo']['estado'] = 'habilitado';
            $data_post['articulo']['cod_categoria'] = $this->input->post('cod_categoria');
            $data_post['articulo']['stock'] = str_replace($separador,'.',$this->input->post('stock'));

            $data_post['impuestos'] = $this->input->post('impuestos') ? $this->input->post('impuestos') : array();
            
           
            $resultado = $this->Model_articulos->guardar($data_post);
        }

        echo json_encode($resultado);
    }
    
    public function cambiarEstado() {
       $cod_articulo = $this->input->post('cod_articulo');
       $cambiarEstado = $this->Model_articulos->validarBajaArticulo($cod_articulo);
       $resultado ='';
       if($cambiarEstado == false){
           $resultado = array(
               "codigo"=>0,
               "msgError"=>lang('articulo_no_se_da_baja')
           );
       }else{
           $resultado = $this->Model_articulos->cambiarEstado($cod_articulo);
       }
        

        echo json_encode($resultado);
    }
    
    public function getCategoriasSubcategorias(){
        $resultado = $this->Model_articulos->getArbolCategorias();
           
        echo json_encode($resultado);
    }
    
    public function agregarCategoriaSubcategoria(){
        $this->load->library('form_validation');
        $retorno = array();
        $cod_padre = $this->input->post('cod_padre') == -1? null : $this->input->post('cod_padre');
        $nombreCat = $this->input->post('nombre_categoria');
        $this->form_validation->set_rules('nombre_categoria',lang('nombre'),'required');
        if($this->form_validation->run() == false){
            $errors = validation_errors();
            $retorno = array(
                'codigo' => '0',
                'msgerror' => $errors,
                'errNo' => '',
            );
        }else{
            $arrGuardarCat = array(
                "nombre_categoria"=>$nombreCat,
                "cod_padre"=>$cod_padre
            );
            
            $resultado = $this->Model_articulos->agregarCategoriaSubcategoria($arrGuardarCat);
        }
        echo json_encode($resultado);
    }
    
    public function modificarCategoria(){
        $this->load->library('form_validation');
        $cod_categoria = $this->input->post('cod_padre');
        $nombre = $this->input->post('nombre_categoria');
        $retorno = '';
        $this->form_validation->set_rules('nombre_categoria',lang('nombre'),'required');
        if($this->form_validation->run() == false){
            $errors = validation_errors();
            $retorno = array(
                'codigo' => '0',
                'msgerror' => $errors,
                'errNo' => '',
            );
        }else{
            $data_post['cod_categoria'] = $cod_categoria;
            $data_post['nombre'] = $nombre;
            $retorno = $this->Model_articulos->modificarCategoria($data_post);
        }
        echo json_encode($retorno);
    }
    
    public function frm_categoria(){
        $cod_categoria = $this->input->post('cod_padre');
        $accion = $this->input->post('accion');
          $data = array();
         
               $data['objCategoria'] = $this->Model_articulos->getObjArtCat($cod_categoria);
                $data['cod_padre'] = $cod_categoria;
                $data['accion'] = $accion;
          
         
        $this->load->view('configuracion/frm_categoria',$data);
    }
    
    public function bajaCategoria(){
        $cod_categoria = $this->input->post('cod_padre');
        $baja = 1;
        $resultado = '';
        $hijosCategoria = $this->Model_articulos->getHijosCategoria($cod_categoria);
        if(count($hijosCategoria['hijosCategoria'])>0 || count($hijosCategoria['articulosCategoria'])>0){
            $resultado = array(
                "codigo"=>0,
                "msgerrors"=>lang('categoria_con_articulos')
            );
        }else{
            $resultado = $this->Model_articulos->bajaCategoria($cod_categoria,$baja);
        }
        
        echo json_encode($resultado);
    }
    
    
}
?>

<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Cupones extends CI_Controller {
    
    public function __construct() {
        parent::__construct();
        $this->lang->load(get_idioma(), get_idioma());
        $this->seccion = session_method();
        $this->load->helper("datatables");
    }
    
    public function index() {
        $this->lang->load(get_idioma(), get_idioma());
        $filial = $this->session->userdata("filial");
        $claves = array('detalle', 'comentario_es_requerido', 'validacion_ok');
        $arrConf = array('codigo_filial' => $filial["codigo"]);
        $this->load->model("Model_cursos", "", false, $arrConf);
        $data['page_title'] = 'Título de la Página';
        $data['page'] = 'cupones/vista_cupones';
        $data['session'] = $this->session->all_userdata();
        $data['seccion'] = $this->seccion;
        $data['helper'] = 'cupones';
        $data['columns'] = $this->getColumns();
        $data['arrCursos'] = $this->Model_cursos->listar();
        $data['filtro_fecha_hasta'] = formatearFecha_pais(date("Y-m-d"));
        $data['filtro_fecha_desde'] = formatearFecha_pais(sumarDias(date("Y-m-d"), -30));
        $data['lang'] = getLang($claves);
        $this->load->view('container',$data);
                
//            $this->lang->load(get_idioma(), get_idioma());
//            $data['page_title'] = 'Título de la Página';
//            $data['page'] = 'vista_cupones'; // pasamos la vista a utilizar como parámetro
//            $data['session']=$this->session->all_userdata();// PASO LOS VALORES DE LA SESSION
//            $data['helper']='cupones';
//            $this->load->view('container',$data);
    }
    
    public function guardar_comentario(){
        $arrResp = array();
        if ($this->input->post("comentario") && trim($this->input->post("comentario")) <> ''){
            $conexion = $this->load->database("default", true); 
            $myComentario = new Vcupones_landing_comentarios($conexion);
            $myComentario->comentario = trim($this->input->post("comentario"));
            $myComentario->fecha = date("Y-m-d H:i:s");
            $myComentario->id_cupon_landing = $this->input->post("id_cupon");
            $myComentario->id_usuario = $this->session->userdata("codigo_usuario");
            if ($myComentario->guardarCupones_landing_comentarios()){
                $arrResp['success'] = "success";
                $arrResp['id'] = $myComentario->getCodigo();
            } else {
                $arrResp['error'] = lang("no_se_ha_podido_realizar_la_accion")."<br>".lang("vuelva_a_intentar_mas_tarde");
            }
       } else {
           $arrResp['error'] = lang("comentario_es_requerido");
       }
       echo json_encode($arrResp);
       
    }
    
    public function ver_comentarios(){
        if ($this->input->post('id_cupon') && $this->input->post("id_cupon") > 0){
            $idCupon = $this->input->post("id_cupon");
            $conexion = $this->load->database("default", true);
            $arrComentarios = Vcupones_landing_comentarios::listar($conexion, $idCupon);
            $data['arrComentarios'] = $arrComentarios;
            $data['id_cupon'] = $idCupon;
            $this->load->view('cupones/cupones_landing_comentarios',$data);
        }
    }
    
    
    public function listar(){
        $columnas = $this->crearColumnas();
        $camposSearch = array();
        foreach ($columnas as $column){
            if ((!isset($column['search']) || $column['search']) && (!isset($column['bVisible']) || $column['bVisible'])){
                $camposSearch[] = $column['campo'];
            }
        }
        $conexion = $this->load->database("default", true);
        $filial = $this->session->userdata("filial");
        $idFilial = $filial['codigo'];
        $fechaDesde = $this->input->post("fecha_desde") ? formatearFecha_mysql($this->input->post("fecha_desde")) : null;
        $fechaHasta = $this->input->post("fecha_hasta") ? formatearFecha_mysql($this->input->post("fecha_hasta")) : null;
        $codCurso = $this->input->post("cod_curso") && $this->input->post("cod_curso") <> -1 ? $this->input->post("cod_curso") : null;
        $orden = $this->input->post("iSortCol_0") ? $this->input->post("iSortCol_0") : 0;
        $orderMethod = $this->input->post("sSortDir_0") ? $this->input->post("sSortDir_0") : "ASC";
        $order = array($columnas[$orden]['campo'], $orderMethod);
        $search = $this->input->post("sSearch") ? $this->input->post("sSearch") : null;
        $limitInf = $this->input->post("iDisplayStart") ? $this->input->post("iDisplayStart") : 0;
        $limitCant = $this->input->post("iDisplayLength") ? $this->input->post("iDisplayLength") : null;
        $sEcho = $this->input->post("sEcho") ? $this->input->post("sEcho") : null;
        
        $valores = $this->listarValores($conexion, $idFilial, $fechaDesde, $fechaHasta, $codCurso, $order, $search, $limitInf, $limitCant, $camposSearch, $sEcho);
        
        if (isset($_POST['action']) && $_POST['action'] == "exportar"){
            $this->load->helper('alumnos');
            $nombreApellido = formatearNombreColumnaAlumno();
            $exp = new export($_POST['tipo_reporte']);
            $arrTemp = array();
            foreach ($valores['aaData'] as $valor) 
            {
                $telefono = $valor[2];
                if (strlen($telefono) > 13)
                {
                    if (strpos($telefono, " "))
                    {
                        $telefono = str_replace(" ", "\n", $telefono);
                    }
                    else 
                    {
                        $temp = str_split($telefono, 13);
                        $telefono = implode("\n", $temp);
                    }
                }
                $documento = $valor[3];
                if (strlen($documento) > 13)
                {
                    if (strpos($documento, " "))
                    {
                        $documento = str_replace(" ", "\n", $documento);
                    }
                    else 
                    {
                        $temp = str_split($documento, 13);
                        $documento = implode("\n", $temp);
                    }
                }
                
                $temp = str_split($valor[1], 34);
                $email = implode("\n", $temp);
                $tmp = Vcupones_landing_comentarios::listar($conexion,  $valor[6]);
                //die(var_dump($tmp));
                $comentarios = '';
                foreach ($tmp as $tmpComentario)
                {
                    if(strlen($tmpComentario['comentario']) < 50)
                    {
                        $comentarios .= $tmpComentario['comentario'];
                        $comentarios .= "\n";
                    }
                    else
                    {
                        $temp = str_split($tmpComentario['comentario'], 50);
                        $comentarios .= implode("\n", $temp);
                        //$comentarios .= "\n\n";
                    }
                }
                $comentarios .= substr($comentarios, 0, -1);
                
                $arrTemp[] = array(
                                    $valor[0],  // Nombre
                                    $email,     // emial
                                    $telefono,  // telefono
                                    $documento, // documento                    
                                    $valor[4],  // fecha
                                    $valor[5],  // curso
                                    $comentarios);
            }
            
            $arrTitle = array(
                $nombreApellido,
                lang("email"),
                lang("telefono"),
                substr(lang("documento"), 0, 9),
                lang("fecha"),
                lang("VALORCURSO"),
                lang("comentario")
            );
            $arrWidth = array(40, 42, 20, 35, 30, 42, 82);
            $periodo = '';
            if (isset($_POST['fecha_desde']) && $_POST['fecha_desde'] != ''){
                $periodo .= lang("desde")." ".$_POST['fecha_desde'];
            }
            if (isset($_POST['fecha_hasta']) && $_POST['fecha_hasta'] != ''){
                $periodo .= lang("al")." ".$_POST['fecha_hasta'];
            }
            if ($periodo == ''){
                $periodo = lang("todas_las_fechas");
            }
            $usuario = $this->session->userdata("nombre");
            $filial = $this->session->userdata("filial");
            $arrInfo = array(
                array("txt" => lang("fecha_emision").": ".date("d/m/Y"), "size" => "8", "align" => "R", "width" => 286, "height" => 4),
                array("txt" => lang("periodo").": ".$periodo, "size" => "8", "align" => "R", "width" => 286, "height" => 4),
                array("txt" => lang("usuario").": ".$usuario, "size" => "8", "align" => "R", "width" => 286, "height" => 4)                
            );
            $exp->setTitle($arrTitle);
            $exp->setContent($arrTemp);
            $exp->setPDFFontSize(8);
            $exp->setColumnWidth($arrWidth);
            $file = FCPATH."assents\img\logo.jpg";
            $exp->setLogo($file);
            $exp->setInfo($arrInfo);
            $exp->setContentHeight(6);
            $exp->setReportTitle($filial['nombre']." - ".lang("informe_de_aspirantes"));
            $exp->setMargin(2, 8);
            $exp->exportar();
        }
        else 
        {
            echo json_encode($valores);
        }
        
    }
    
    public function listarValores(CI_DB_mysqli_driver $conexion, $idFilial = null, $fechaDesde = null, $fechaHasta = null, $codCurso = null, 
            array $order = null, $search = null, $limitInf = 0, $limitCant = null, array $camposSearch = null, $sEcho = null)
    {
        $arrCupones = Vcupones_landing::listar($conexion, $idFilial, $fechaDesde, $fechaHasta, $codCurso, $order, $search, $limitInf, $limitCant, false, $camposSearch);
        $cantidad = Vcupones_landing::listar($conexion, $idFilial, $fechaDesde, $fechaHasta, $codCurso, null, $search, null, null, true, $camposSearch);
        $aaData = array();
        foreach ($arrCupones as $cupon){
            $aaData[] = array(
                $cupon['nombre'],
                $cupon['email'],
                $cupon['telefono'],
                $cupon['documento'],
                $cupon['fecha_cupon'],
                $cupon['nombre_es'],
                $cupon['id']
            );
        }
        
        $retorno = array(
            "sEcho" => $sEcho,
            "iTotalRecords" => $cantidad,
            "iTotalDisplayRecords" => $cantidad,
            "aaData" => $aaData
        );
        
        return $retorno;
    }
    
    public function datos(){ // y esto que es????????
           $valores = array(
          "sEcho"=>"1",
           "iTotalRecords"=>"11",
          "iTotalDisplayRecords"=>"11",
          "aaData"=>array(array(
              "1",
             "Gecko",
             "Firefox 1.0",
             "Win 98+ / OSX.2+",
              "1.7",
                "A"
          ),array(
                 "2",
                 "Gecko",
               "Firefox 1.5",
                "Win 98+ / OSX.2+",
                "1.8",
                 "A"
                ),array(
                 "3",   
                 "Gecko",
               "Firefox 1.5",
                "Win 98+ / OSX.2+",
                "1.8",
                 "A"
                ),array(
                 "4",
                 "Gecko",
               "Firefox 1.5",
                "Win 98+ / OSX.2+",
                "1.8",
                 "A"
                ),array(
                 "5",   
                 "Gecko",
               "Firefox 1.5",
                "Win 98+ / OSX.2+",
                "1.8",
                 "A"
                ),array(
                 "6",
                 "Gecko",
               "Firefox 1.5",
                "Win 98+ / OSX.2+",
                "1.8",
                 "A"
                ),array(
                 "7",   
                 "Gecko",
               "Firefox 1.5",
                "Win 98+ / OSX.2+",
                "1.8",
                 "A"
                ),array(
                 "8",
                 "Gecko",
               "Firefox 1.5",
                "Win 98+ / OSX.2+",
                "1.8",
                 "A"
                ),array(
                 "9",   
                 "Gecko",
               "Firefox 1.5",
                "Win 98+ / OSX.2+",
                "1.8",
                 "A"
                ),array(
                 "10",
                 "Gecko",
               "Firefox 1.5",
                "Win 98+ / OSX.2+",
                "1.8",
                 "A"
                ),array(
                 "11",   
                 "Gecko",
               "Firefox 1.5",
                "Win 98+ / OSX.2+",
                "1.8",
                 "A"
                )


              )

      );

      echo json_encode($valores);
    }
        
    public function sincronizar(){
        $resp = array();
        $this->load->library('form_validation');
        $this->form_validation->set_rules('id', lang('id'), 'required');
        $this->form_validation->set_rules('nombre', lang('id'), 'required');
        $this->form_validation->set_rules('email', lang('id'), 'required');
        $this->form_validation->set_rules('telefono', lang('id'), 'required');
        $this->form_validation->set_rules('id_filial', lang('id'), 'required');
        $this->form_validation->set_rules('estado', lang('id'), 'required');
        $this->form_validation->set_rules('codigo', lang('id'), 'required');
        $this->form_validation->set_rules('fecha', lang('id'), 'required');
        $this->form_validation->set_rules('medio', lang('id'), 'required');
        $this->form_validation->set_rules('id_landing', lang('id'), 'required');
        $this->form_validation->set_rules('documento', lang('id'), 'required');
        if (!$this->form_validation->run()) {            
            $resp = array(
                'error' => 'Error de parametros'
            );            
        } else {
            $conexion = $this->load->database("general", true);
            $myCupon = new Vcupones($conexion);
            $myCupon->codigo = $this->input->post('codigo');
            $myCupon->documento = $this->input->post("documento");
            $myCupon->email = $this->input->post("email");
            $myCupon->estado = $this->input->post("estado");
            $myCupon->fecha = $this->input->post("fecha");
            $myCupon->id_filial = $this->input->post("id_filial");
            $myCupon->id_landing = $this->input->post('id_landing');
            $myCupon->medio = $this->input->post('medio');
            $myCupon->nombre = $this->input->post("nombre");
            $myCupon->telefono = $this->input->post("telefono");
            if ($myCupon->guardar($this->input->post("id"))){
                $resp['success'] = "success";
                $resp['id'] = $myCupon->getCodigo();
            } else {
                $resp['error'] = "[".$conexion->_error_number()."] ".$conexion->_error_message();
            }
        }
        echo json_encode($resp);
    }
    
    /**
     * actuializa o inserta un registro en la tabla publicidad.cupones_landing.
     * 
     * Utilizada desde un web services (no modificar, eliminar ni comentar)
     */
    public function guardar_cupones_landing(){
        $arrResp = array();
        if (isset($_POST['nombre']) && isset($_POST['email']) && isset($_POST['telefono']) && isset($_POST['id_filial'])
                && isset($_POST['id_curso']) && isset($_POST['documento'])){
            $id = isset($_POST['id']) && $_POST['id'] > 0 ? $_POST['id'] : null;
            $conexion = $this->load->database("default", true);
            $myCupon = new Vcupones_landing($conexion, $id);
            $myCupon->documento = $_POST['documento'];
            $myCupon->email = $_POST['email'];
            $myCupon->fecha = date("Y-m-d H:i:s");
            $myCupon->id_curso = $_POST['id_curso'];
            $myCupon->id_filial = $_POST['id_filial'];
            $myCupon->nombre = $_POST['nombre'];
            $myCupon->telefono = $_POST['telefono'];
            if (!$myCupon->guardarCupones_landing()){
                $arrResp['error'] = "[".$conexion->_error_number()."] ".$conexion->_error_message();                
            } else {
                $arrResp['success'] = "success";
                $arrResp['id'] = $myCupon->getCodigo();
            }
        } else {
            $arrResp['error'] = "Error de parametros";
        }
        echo json_encode($arrResp);
    }    
    
    private function getColumns() {
        $this->load->helper("datatables");
        $aoColumnDefs = json_encode(getColumnsDatatable($this->crearColumnas()));
        return $aoColumnDefs;
    }
    
    private function crearColumnas(){
        $complemento = get_idioma();
        $columnas = array( // 'bVisible' => false // "sort" => false
            array("nombre" => lang('nombre'), "campo" => 'nombre'),
            array("nombre" => lang("email"), "campo" => "email"),
            array("nombre" => lang("telefono"), "campo" => "telefono"),
            array("nombre" => lang('documento'), "campo" => 'documento'),
            array("nombre" => lang('fecha'), "campo" => "fecha_cupon"),
            array("nombre" => lang("curso"), "campo" => "nombre_$complemento"),
            array("nombre" => lang("comentarios"), "campo" => "", "sort" => false, "search" => false)
        );
        return $columnas;
    }
}
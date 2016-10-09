<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class tickets extends CI_Controller {
    
    public function __construct() {
        parent::__construct();
        $this->lang->load(get_idioma(), get_idioma());
        $this->seccion = session_method();
    }
    
    public function index(){
        $claves = Array("BIEN", "ERROR", "validacion_ok", "reportar_error");
        $data['langFrm'] = getLang($claves);
        $data['page'] = 'tickets/vista_tickets';
        $data['seccion'] = $this->seccion;
        $data['columns'] = $this->getColumns();
        $this->load->view('container', $data);
    }
    
    public function ver_ticket(){
        $id = $this->input->post("id_ticket");
        $filial = $this->session->userdata("filial");
        $wsc = new wsc();
        $arrTickets = Vtickets::listar($wsc, $filial['codigo'], null, null, null, null, null, null, null,
                null, null, null, $id);
        if (is_array($arrTickets) && isset($arrTickets['transport'], $arrTickets['transport']['aaData'], $arrTickets['transport']['aaData'][0])){
            $data['ticket'] = $arrTickets['transport']['aaData'][0];
            if (isset($arrTickets['transport']['aaData'][0]['id_usuario_igacloud']) && $arrTickets['transport']['aaData'][0]['id_usuario_igacloud'] <> ''){
                $conexion = $this->load->database($filial['codigo'], true);
                $myUsuario = new Vusuarios_sistema($conexion, $arrTickets['transport']['aaData'][0]['id_usuario_igacloud']);
                $data['usuario'] = $myUsuario->nombre." ".$myUsuario->apellido;
            }
            $this->load->view('tickets/vista_get_ticket', $data);
            
        } else if ($wsc->is_error()){
            echo $wsc->get_error();
        } else if (is_array($arrTickets['transport']['aaData'])){
            echo "No Encontrado";
        } else {
            echo $wsc->get_response();
        }
    }
    
    function generar_tickets(){
        $filial = $this->session->userdata("filial");
        $conexion = $this->load->database($filial['codigo'], true);
        $arrSecciones = Vsecciones::listarSecciones($conexion);
        $categorias = array();
        $subcategorias = array();
        foreach($arrSecciones as $seccion){
            if(!in_array($seccion['categoria'], $categorias)){
                $categorias[] = $seccion['categoria'];
            }
            $indice = array_search($seccion['categoria'], $categorias);
            if(!isset($subcategorias[$indice]))
                $subcategorias[$indice] = array();
            $subcategorias[$indice][] =  $seccion['slug'];
        }
        $data['categorias'] = $categorias;
        $data['subcategorias'] = $subcategorias;
        $this->load->view('tickets/generar_tickets', $data);
    }
    
    public function agregar_ticket(){

        $resultado = '';
        $errors = '';
        $this->load->library('form_validation');
        $this->form_validation->set_rules('nombre', lang('nombre'), 'required');
        $this->form_validation->set_rules('categoria', lang('categoria'), 'required');
        $this->form_validation->set_rules('subcategorias', "Subcategoria"/*lang('nombre')*/, 'required');
        
        if ($this->form_validation->run() == FALSE) {
            $errors .= validation_errors();
            $resultado = array(
                'codigo' => '0',
                'msgerror' => $errors,
                'errNo' => '',
            );
        } else {
            
            $filial = $this->session->userdata("filial");
            $descripcion = trim($this->input->post("categoria")).' :: '.$this->input->post("subcategorias"). ' :: ';
            $descripcion .= $this->input->post("descripcion");

            $param = array(
                "action" => "add_ticket",
                "id_filial" => $filial['codigo'],
                "id_usuario_igacloud" => $this->session->userdata("codigo_usuario"),
                "nombre" =>  $this->input->post("nombre"),
                "descripcion" => $descripcion
            );
            
            
            $wsc = new wsc($param);
            $resp = $wsc->exec();
            if ($resp){ //Todo OK
                $resp['codigo'] = 1;
                $resultado = $resp;
                
            } else if ($wsc->is_error()){ //Error
                $resultado = array(
                    'codigo' => '0',
                    'msgerror' => $wsc->get_error(),
                    'errNo' => '',
                );
                
            } else { //
                echo $wsc->get_response();
            }
        }
        echo json_encode($resultado);
    }
    
    public function listar(){
        $wsc = new wsc();
        $filial = $this->session->userdata("filial");
        $idFilial = $filial['codigo'];
        $columns = $this->crearColumnas();
        $seccion = $this->input->post("seccion") && $this->input->post("seccion") <> -1 ? $this->input->post("seccion") : null;
        $area = $this->input->post("area") && $this->input->post("area") <> -1 ? $this->input->post("area") : null;
        $prioridad = $this->input->post("prioridad") && $this->input->post("prioridad") <> -1 ? $this->input->post("prioridad") : null;
        $estado = $this->input->post("estado") && $this->input->post("estado") <> -1 ? $this->input->post("estado") : null;
        $fechaDesde = $this->input->post("fecha_desde") && $this->input->post("fecha_desde") <> '' ? formatearFecha_mysql($this->input->post("fecha_desde")) : null;
        $fechaHasta = $this->input->post("fecha_hasta") && $this->input->post("fecha_hasta") <> '' ? formatearFecha_mysql($this->input->post("fecha_hasta")) : null;
        $search = $this->input->post("sSearch") && trim($this->input->post("sSearch")) <> '' ? trim($this->input->post("sSearch")) : null;
        $iDisplayLength = $this->input->post("iDisplayLength") ? $this->input->post("iDisplayLength") : null;
        $iDisplayStart = $this->input->post("iDisplayStart") ? $this->input->post("iDisplayStart") : null;
        $order = null;
        if (isset($_POST["iSortCol_0"])){
            $iSortCol_0 = $columns[$this->input->post("iSortCol_0")]['campo'];
            $sSortDir_0 = $this->input->post("sSortDir_0") ? $this->input->post("sSortDir_0") : "asc";
            $order = array($iSortCol_0, $sSortDir_0);
        }
        $arrTickets = Vtickets::listar($wsc, $idFilial, $seccion, $area, $prioridad, $estado, $fechaDesde, $fechaHasta, $search,
                $order, $iDisplayStart, $iDisplayLength);
        if (is_array($arrTickets) && isset($arrTickets['transport'], $arrTickets['transport']['aaData'])){
            $idioma = get_idioma();
            $extension = "esp";
            if ($idioma == "pt"){
                $extension = "por";
            }
            $aaData = array();
            foreach ($arrTickets['transport']['aaData'] as $ticket){
                $aaData[] = array(
                    $ticket["seguimiento"],
                    $ticket["nombre"],
                    lang("tikets_estado_".$ticket['tickets_estado']),
                    $ticket["area_$extension"],
                    lang("tikets_prioridad_".$ticket['tickets_prioridad']),
                    formatearFecha_pais($ticket['ultima_modificacion']),
                    $ticket['nombre_usuario_toma_ticket'],
                    $ticket['id']
                );
            }
            $retorno = array(
                "sEcho" => $this->input->post("sEcho"),
                "iTotalRecords" => $arrTickets['transport']['iTotalRecords'],
                "iTotalDisplayRecords" => $arrTickets['transport']['iTotalRecords'],
                "aaData" => $aaData,
                    "extras" => array("campo1" => 2, "campo2" => 3)
            );
        } else {
            $retorno = array(
                "sEcho" => $this->input->post("sEcho"),
                "iTotalRecords" => "0",
                "iTotalDisplayRecords" => "0",
                "aaData" => array()
            );
        }
        echo json_encode($retorno);
    }
    
    private function getColumns() {
        $this->load->helper("datatables");
        $aoColumnDefs = json_encode(getColumnsDatatable($this->crearColumnas()));
        return $aoColumnDefs;
    }    
    
    private function crearColumnas() {
        $idioma = get_idioma();
        $extension = "esp";
        if ($idioma == "pt"){
            $extension = "por";
        }
        $columnas = array(
            array("nombre" => "ID", "campo" => "seguimiento"),
            array("nombre" => lang("asunto"), "campo" => "nombre"),
            array("nombre" => lang("estado"), "campo" => 'tickets_estado', "sort" => false),
            array("nombre" => lang("area"), "campo" => "area_$extension"),
            array("nombre" => lang("prioridad"), "campo" => "tickets_prioridad", "sort" => false),
            array("nombre" => lang("ultima_modificacion"), "campo" => "ultima_modificacion"),
            array("nombre" => lang("usuarios_asigando"), "campo" => "nombre_usuario_toma_ticket"),           
        );
        return $columnas;
    }
}
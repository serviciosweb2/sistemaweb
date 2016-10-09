<?php

/**
 * Control Alumnos.
 *
 * @package  SistemaIGA
 * @subpackage Alumnos
 * @author   Ivan berthillod <ivan.sys@gmail.com>
 * @author   Ariel Di Cesare   <sistemas4@iga-la.net>
 * @version  $Revision: 1.0 $
 * @access   public
 */
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * Control de seccion alumnos.
 */
class Responsables extends CI_Controller {

    private $seccion;

    public function __construct()
    {

        parent::__construct();
        $this->lang->load(get_idioma(), get_idioma());
    //  $this->seccion = session_method();
        
        $filial = $this->session->userdata('filial');
        
        $configResponsables = array("codigo_filial" => $filial["codigo"]);

        $this->load->model("Model_responsables", "", false, $configResponsables);
        
    }
    
    
    private function crearColumnas() 
    {
        $this->load->helper('alumnos');
        $nombreApellido = formatearNombreColumnaAlumno();
        $columnas = array(
            array("nombre" => lang('codigo'), "campo" => 'codigo'),
            array("nombre" => $nombreApellido, "campo" => 'nombre_apellido'),
            array("nombre" => lang('tipo_documento'), "campo" => 'nombre_identificacion'),
            array("nombre" => lang('email'), "campo" => 'email'),
            array("nombre"=> lang('direccion'), "campo"=>'direccion'),
            array("nombre" => lang('estado'), "campo" => 'baja',"sort" => FALSE),
            array("nombre" =>lang('estado'), "campo" => 'baja', 'bVisible' => false,"sort" => FALSE),
            array("nombre" =>lang('razones_sociales'), "campo" => 'cod_razon_social', 'bVisible' => false,"sort" => FALSE),
            array("nombre" =>lang('condicion_social'), "campo" => 'nombre_condicion', 'bVisible' => false,"sort" => FALSE)
            );
        return $columnas;
    }

    public function getColumns()
    {
        $this->load->helper("datatables");
        $aoColumnDefs = json_encode(getColumnsDatatable($this->crearColumnas()));
        return $aoColumnDefs;
        
        
    }

    public function listar() 
    {
        $filial = $this->session->userdata('filial');
        $separador = $filial['nombreFormato']['separadorNombre'];
        $crearColumnas = $this->crearColumnas();
        $arrFiltros["iDisplayStart"] = isset($_POST['iDisplayStart']) ? $_POST['iDisplayStart'] : "";
        $arrFiltros["iDisplayLength"] = isset($_POST['iDisplayLength']) ? $_POST['iDisplayLength'] : "";
        $arrFiltros["sSearch"] = isset($_POST['sSearch']) ? $_POST['sSearch'] : "";
        $arrFiltros["sEcho"] = isset($_POST['sEcho']) ? $_POST['sEcho'] : "";
        $arrFiltros["SortCol"] = isset($_POST['iSortCol_0']) ? $crearColumnas[$_POST['iSortCol_0']]["campo"] : "";
        $arrFiltros["sSortDir"] = isset($_POST['sSortDir_0']) ? $_POST['sSortDir_0'] : "";
        $valores = $this->Model_responsables->listarResponsablesDatatable($arrFiltros, $separador);
        echo json_encode($valores);
    }
    
    public function getResponsable()
    {
        
        $this->load->helper('formatearfecha');
        $codigo = $this->input->post('codigo');
        
        $tipo_identificacion = $this->input->post('tipo_identificacion');
        
        $numero_identificacion = $this->input->post('numero_identificacion');
        
        $responsable = $this->Model_responsables->getResponsable($codigo,$tipo_identificacion,$numero_identificacion);
         
        echo json_encode($responsable);
    }
    
    public function frm_responsable()
    {
        $cod_responsable = $this->input->post('codigo');
        $this->load->view('responsables/frm_responsable');
    }
    
    public function guardarResponsable()
    {   
        $this->load->helper('formatearfecha');
        $this->load->library('form_validation');
        $this->lang->load(get_idioma(), get_idioma());
        $filial = $this->session->userdata('filial');
        $pais = $filial['pais'];
        $cod_usuario = $this->session->userdata('codigo_usuario');

        
        
        $codigo = $this->input->post('codigo');
        //Ticket 4527 - se quita la obligatoriedad de los siguientes campos: Domicilio - Nº - Complemento - Provincia/Municipalidad - Localidad - Barrio - Teléfono - CP - Email
        $documento  = ($this->input->post('documento') != null) ? $this->input->post('documento') : 'nulo';
        $direccion_calle  = ($this->input->post('calle') != null) ? $this->input->post('calle') : 'nulo';
        $direccion_numero = ($this->input->post('calle_numero') != null) ? $this->input->post('calle_numero') : 'nulo';
        $direccion_complemento = ($this->input->post('calle_complemento') != null) ? $this->input->post('calle_complemento') : 'nulo';
        $domiciProvincia = ($this->input->post('domiciProvincia_responsable') != null) ? $this->input->post('domiciProvincia_responsable') : 'nulo';
        $domiciLocalidad = ($this->input->post('domiciLocalidad_responsable') != null) ? $this->input->post('domiciLocalidad_responsable') : 'nulo';
        $barrio = ($this->input->post('barrio') != null) ? $this->input->post('barrio') : 'nulo';
        $telefonos = ($this->input->post('telefonos') != null) ? json_decode($this->input->post('telefonos'),true) : 0;
        $cod_postal = ($this->input->post('cod_postal') != null) ? $this->input->post('cod_postal') : 'nulo';
        $email    = ($this->input->post('email') != null) ? $this->input->post('email') : 'nulo';
        $tipoDocu  = ($this->input->post('tipo_doc') != null) ? $this->input->post('tipo_doc') : 'nulo';
        $nombre   =  $this->input->post('nombre');
        $apellido = $this->input->post('apellido');
        $condicion = $this->input->post('condicion');
        $relacion_alumno = $this->input->post('relacion_alumno');
        $fecha_naci = $this->input->post('fecha_naci');
        $baja = $this->input->post('baja');
        

            $pos = '';//$key;
            $pos++;
            
            //Domicilio - Nº - Complemento - Provincia/Municipalidad - Localidad - Barrio - Teléfono - CP - Email
            $this->form_validation->set_rules('nombre',lang('Fnom'), 'required|max_length[50]|validarNombreApellido[' . 'nombreResponsableInvalido' . ']');
            $this->form_validation->set_rules('apellido',lang('Fape'), 'required|max_length[150]|validarNombreApellido[' . 'nombreResponsableInvalido' . ']');
          //  $this->form_validation->set_rules('condicion',lang('Fcond'), 'required');
            //$this->form_validation->set_rules('calle',lang('Fcalle'), 'max_length[50]');
            //$this->form_validation->set_rules('calle_numero',lang('FcalleNum'), 'max_length[30]|integer');
            //$this->form_validation->set_rules('tipo_doc',lang('FtipoDoc'), '');
            //$this->form_validation->set_rules('documento',lang('Fdoc'),'validarDocumentoIdentidad[' . $tipoDocu . ']');
            //$this->form_validation->set_rules('email',lang('Femail'), 'valid_email');
            $this->form_validation->set_rules('fecha_naci',lang('fecha_resp'), 'required');
            $this->form_validation->set_rules('relacion_alumno',lang('relacion_alumno'), 'required');
            //$this->form_validation->set_rules('cod_postal',lang('codigo_postal'), '');
            
            //$this->form_validation->set_rules('domiciProvincia_responsable',lang('provincia'), '');
            //$this->form_validation->set_rules('domiciLocalidad_responsable',lang('localidad'), '');


            $_POST['Teldefault'] = '';
            
            if($telefonos != 0)
            {
                foreach ($telefonos as $tel => $telefono)
                {
                    if (isset($telefono['default']))
                    {
                        $_POST['Teldefault'] = $telefono['default'];
                    }
                    $_POST['FcodArea'.$tel] = (isset($telefono['cod_area'])) ? $telefono['cod_area'] : "nulo";
                    $_POST['Fnum'.$tel] = (isset($telefono['numero'])) ? $telefono['numero'] : "nulo";
                    $_POST['Femp'.$tel] = (isset($telefono['empresa'])) ? $telefono['empresa'] : "nulo";
                    $_POST['FtipoTel'.$tel] = (isset($telefono['tipo_telefono'])) ? $telefono['tipo_telefono'] : "nulo";
                    $posi = $tel;
                    $posi++;
                    $this->form_validation->set_rules('FcodArea' .$tel, lang('FcodArea'), 'numeric|integer');
                    $this->form_validation->set_rules('Fnum' . $tel, lang('Fnum'), 'numeric|integer');
                    //$this->form_validation->set_rules('Femp' . $tel, lang('Femp'), '');

                    if($pais == 2 && $telefono['tipo_telefono'] == 'celular')
                    {
                        $this->form_validation->set_rules('Femp' . $tel, lang('tel_empresa') . ' ' . $posi . ' ' . lang('responsable'), 'required');
                    }

                    $this->form_validation->set_rules('FtipoTel' . $tel, lang('FtipoTel'), '');
                }
            }    
            //$this->form_validation->set_rules('Teldefault', lang('Teldefault'), 'required');

            if ($this->form_validation->run() == FALSE)
            {
                $errors = validation_errors();
                $resultado = array(
                    'codigo' => '0',
                    'respuesta' => $errors
                );
                echo json_encode($resultado);
            } 
            else 
            {
                //guardar responsable
               
               $arrResponsables = array(
                   'codigo'=>$codigo,
                   'documento' => $documento,
                   'nombre'=>$nombre,
                   'apellido'=>$apellido,
                   'email' => $email,
                   'direccion_calle' => $direccion_calle,
                   'direccion_numero' => $direccion_numero,
                   'direccion_complemento' => $direccion_complemento,
                   'condicion' => $condicion,
                   'barrio'=>$barrio,
                   'cod_postal'=>$cod_postal,
                   'cod_localidad'=>$domiciLocalidad,
                   'relacion_alumno'=>$relacion_alumno,
                   'fecha_naci'=> formatearFecha_mysql($fecha_naci),
                   'telefonos'=>$telefonos,
                   'tipo_documentos' =>$tipoDocu ,
                   'baja'=>$baja,
                   'usuario_creador'=>$cod_usuario
                );
                
               
               $resultado = $this->Model_responsables->guardarResponsables($arrResponsables);
               
               echo json_encode($resultado);
            }
       
       
    }
    
    
    public function scriptLang()
    {
//        $conexion = $this->load->database('79',true);
//       
//        $terminalesHabilitadas = Vpos_terminales::getTerminales($conexion,true);
//        
//        
//        
//        
//        echo '<pre>'; 
//        print_r($terminalesHabilitadas);
//        echo '</pre>';
        
        
        ///////////////////////////////////
        $this->lang->load('es','es');
        $ci = &get_instance();
        
        $es = $ci->lang->language;
        
        
        
        /////////////////////////////
        $ci->lang->language = array();
        $this->lang->load('pt','pt');
        $ci = &get_instance();
        
        $pt = $ci->lang->language;
        
        //////////////////////////////////////////////////
        
        $ci->lang->language = array();
        $this->lang->load('en','en');
        
        $ci = &get_instance();
        
        $en = $ci->lang->language;
        
        
        $langEs = array();
        $langEn = array();
        $langEnString = '';
        $langPtString = '';
        $langEsString = '';
        $langPt = array();
        
        foreach($es as $key => $lang)
        {
            
            if( !isset( $langEn[$key] ))
            {// ingles
                $langEn[ utf8_decode($key) ] = isset( $en[$key] ) ?    utf8_decode($en[$key]) : utf8_decode( 'SIN_TRADUCIR ( en español dice '.$lang.')');
                $valor = isset( $en[$key] ) ? utf8_decode($en[$key]) : '\';//'.utf8_decode( 'SIN_TRADUCIR ( en español dice => '.$lang.')');
                $langEnString.= "\$lang['".utf8_decode($key) ."']='".$valor."';<br>";
                
            }
            
            
            if( !isset($langPt[$key]))
            {// portugues
                $langPt[ utf8_decode($key) ] = isset( $pt[$key] ) ? utf8_decode($pt[$key]) : utf8_decode('SIN_TRADUCIR ( en español dice '.$lang.')');
                $valor = isset( $pt[$key] ) ? utf8_decode($pt[$key]) : '\';//'.utf8_decode( 'SIN_TRADUCIR ( en español dice => '.$lang.')');
                $langPtString .= "\$lang['".utf8_decode($key) ."']='".$valor."';<br>";
               
            }
            
            if( !isset($langEs[$key]))
            {// español
                $langEs[ utf8_decode($key) ] = isset( $es[$key] ) ? utf8_decode($es[$key]) : utf8_decode('SIN_TRADUCIR ( en español dice '.$lang.')');
                $valor = isset( $es[$key] ) ? utf8_decode($es[$key]) : '\';//'.utf8_decode( 'SIN_TRADUCIR ( en español dice => '.$lang.')');
                $langEsString .= "\$lang['".utf8_decode($key) ."']='".$valor."';<br>";
               
            }
            
        }
        // UNA VEZ EJECUTADO EL SCRIPT DEVUEÑVE UN PRINT LISTO PARA COPIAR Y PEGAR EN UN ARCHIVO. revisar sin hay errores despues de pegar
        //echo 'count "es" =>'.count($es).'<br>';
        //echo 'count "pt" =>'.count($langPt).'<br>';
        //echo 'count "en" =>'.count($langEn).'<br>';
        echo '<pre>'; 
        print_r($langEsString);
        echo '</pre>';
        
        echo '-----------PT---------------------------';
        
        echo '<pre>'; 
        print_r($langPtString);
        echo '</pre>';
        
        echo '-----------EN---------------------------';
        
        echo '<pre>'; 
        print_r($langEnString);
        echo '</pre>';
    }
    
    public function error()// test prueba error
    {
        echo $uno;
    }
    
    public function test()
    {
        $conexion = $this->load->database('999',true);
        $registrosFilial = Voffline_sincronizacion::getRegistrosSincronizarTest($conexion,3,50,'bancos');
        echo '<pre>'; 
        print_r($registrosFilial);
        echo '</pre>';

        
    }
}
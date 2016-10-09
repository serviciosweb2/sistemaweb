<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Offline extends CI_Controller {

    private $seccion;

    public function __construct() {
        parent::__construct();
        $this->load->library('form_validation');
        $this->lang->load(get_idioma(), get_idioma());
        $filial = $this->session->userdata('filial');
        $this->seccion = session_method();
        $configOffline = array("codigo_filial" => $filial["codigo"]);
        $this->load->model("Model_offline", "", false, $configOffline);
    }

    public function cache() {

        // Add the correct Content-Type for the cache manifest
        header('Content-Type: text/cache-manifest');

        // Write the first line
        echo "CACHE MANIFEST\n";

        echo "# Hash: " . md5(date('Y:m:d h:m:s')) . "\n";


        //create_manifest(".");
        // Write the $hashes string
        echo '/sistemasiga/assents/js/offline/generalOffline.js';
    }

    public function index() {
        //$data['nocachear']=true;
        $data['titulo_pagina'] = ''; //$validar_session;
        $data['page'] = 'offline/indexoffline'; // pasamos la vista a utilizar como parámetr
        $data['seccion'] = $this->seccion;
        //$data['columnas_boleto'] = $this->getColumnsBoleto();
        

        $this->load->view('offline/container', $data);
    }

    public function cobros()
    {
        $filial = $this->session->userdata('filial');
        $this->load->model("Model_paises", "", false, $filial["pais"]);
        $cheques = Vmedio_cheques::getTipos();
        $tipoCheque = json_encode($cheques);
        $mediosPago = $this->Model_paises->getMediosPagos();
        $data['titulo_pagina'] = ''; //$validar_session;
        $data['page'] = 'offline/cobros/cobros'; // pasamos la vista a utilizar como parámetr
        $data['seccion'] = $this->seccion;
        $data['mediosPago']=$mediosPago;
        $data['tipoCheque']=$tipoCheque;
        $lang=array(
                'validacion_ok'=>lang('validacion_ok'),
                'pin_incorrecto'=>lang('pin_incorrecto'),
                'orden'=>lang('orden'),
                'diferido'=>lang('diferido'),
                'seleccione_fecha'=>lang('seleccione_fecha'),
                'seleccione_alumno'=>lang('seleccione_alumno'),
                'seleccione_medio_pago'=>lang('seleccione_medio_pago'),
                'buscando'=>lang('buscando'),
                'no_hay_resultados'=>lang('no_hay_resultados'),
                'SELECCIONE_UNA_OPCION'=>lang('SELECCIONE_UNA_OPCION'),
                'recibo_de_cobros'=>lang('recibo_de_cobros'),
                'ALUMNO'=>lang('ALUMNO'),
                'medio_pago'=>lang('medio_pago'),
                'fecha'=>lang('fecha'),
                'facturar_importe'=>lang('facturar_importe'),
                'home'=>lang('home'),
                'cobros'=>lang('cobros'),
                'formato_esperado'=>lang('formato_esperado'),
                'campos_vacios'=>lang('campos_vacios'),
                'medio_caja_factura'=>lang('medio_caja_factura'),
                'terminales'=>lang('terminales'),
                'tipo_tarjeta'=>lang('tipo_tarjeta'),
                'codigo_cupon'=>lang('codigo_cupon'),
                'codigo_autorizacion'=>lang('codigo_autorizacion'),
                'medio-tajeta-banco-factura'=>lang('medio-tajeta-banco-factura'),
                'tipo_cheque'=>lang('tipo_cheque'),
                'medio_cheque_numero_factura'=>lang('medio_cheque_numero_factura'),
                'medio_cheque_emisor_factura'=>lang('medio_cheque_emisor_factura'),
                'medio-deposito-banco-factura'=>lang('medio-deposito-banco-factura'),
                'medio-tranferencia-nro-transaccion-factura'=>lang('medio-tranferencia-nro-transaccion-factura'),
                'medio_deposito_cuenta_factura'=>lang('medio_deposito_cuenta_factura'),
                'medio-tranferencia-banco-factura'=>lang('medio-tranferencia-banco-factura'),
               
                
            );
        $data['lang'] = json_encode($lang);
        $this->load->view('offline/container', $data);

    }

    public function ping() {

        $ping = array('codigo' => 1);

        echo json_encode($ping);
    }
    public function setearRegistros() {//url para que lo acceda algun servicio 
        $this->Model_offline->setearRegistros();
    }
    public function sincronizar()
    {
        $ultimoId = $this->input->post('ultimo_id');
        $ultimoIdBancos = $this->input->post('ultimo_id_bancos');
        $ultimoIdTarjetas = $this->input->post('ultimo_id_tarjetas');
        
        $respuesta = array();
        
        if ($this->Model_offline->comprobarRegistros_server())
        {
            $respuesta = $this->Model_offline->getRegistrosMayorQue($ultimoId,$ultimoIdBancos,$ultimoIdTarjetas);
        }
        else
        {
            echo 'error';
            die();
        }

        echo json_encode($respuesta);
    }

    public function test()
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
        
        
        echo '<pre>'; 
        print_r($langPtString);
        echo '</pre>';
        
        echo '<pre>'; 
        print_r($langEnString);
        echo '</pre>';
    }
    
    
    public function listarBancosTest()// solo de prueba
    {
        $conexion = $this->load->database('bancos',true);
        $arr =  Voffline_sincronizacion::listarOffline_sincronizacion($conexion);
        $registros = Voffline_sincronizacion::getRegistrosSincronizar($conexion, 1);
        echo '<pre>'; 
        print_r($registros);
        echo '</pre>';
    }
}

?>

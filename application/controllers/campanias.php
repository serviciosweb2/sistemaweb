<?php

/**
 * Control Email Marketing.
 *
 * @package  SistemaIGA comentario de prueba
 * @subpackage Publicidad
 * @version  $Revision: 1.0 $
 * @access   public
 */

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * Control de seccion email marketing.
 */
class Email_mkt extends CI_Controller {

    private $seccion;

    public function __construct() {
        parent::__construct();
        $this->seccion = session_method();
        $filial = $this->session->userdata('filial');
        $configEmail_mkt = array("codigo_filial" => $filial["codigo"]);
        $this->load->model("Model_email_mkt", "", false, $configEmail_mkt);
        $this->lang->load(get_idioma(), get_idioma());
    }

    /**
     * Retorna vista de email marketing main panel
     * @access public
     * @return vista de main ponel (email marketing)
     */
    public function index() {
        $this->lang->load(get_idioma(), get_idioma());
        $data['seccion'] = $this->seccion;
        $data['mensaje'] = 'Bienvenido al sistema de Email Marketing';
        $data['page'] = 'email_mkt/vista_email_mkt';
        $this->load->view('container', $data);
        //die(var_dump($data['seccion']));
    }


}
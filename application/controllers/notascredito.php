<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

Class Notascredito extends CI_Controller {

    private $seccion;

    public function __construct() {
        parent::__construct();
        $this->lang->load(get_idioma(), get_idioma());
        $this->seccion = session_method();
        $filial = $this->session->userdata('filial');
        $config = array("codigo_filial" => $filial["codigo"]);
        $this->load->model("Model_notas_credito", "", false, $config);
    }

    public function index() {
        $data['titulo_pagina'] = '';
        $data['page'] = 'notas_credito/vista_notas_credito'; // pasamos la vista a utilizar como parámetr
        $data['seccion'] = $this->seccion;
        $this->load->view('container', $data);
    }

    private function crearColumnas() {
        $this->load->helper('alumnos');
        $nombreApellido = formatearNombreColumnaAlumno();
        $columnas = array(
            array("nombre" => lang('codigo'), "campo" => 'codigo'),
            array("nombre" => $nombreApellido, "campo" => 'nombre_apellido'),
            array("nombre" => lang('importe'), "campo" => 'importe'),
            array("nombre" => lang('saldo'), "campo" => 'saldoRestante'),
            array("nombre" => lang('fecha'), "campo" => 'fechareal'),
            array("nombre" => lang('estado'), "campo" => 'estado', 'bVisible' => false),
            array("nombre" => lang('estado'), "campo" => 'baja', "sort" => FALSE));
        return $columnas;
    }

    public function getColumns() {
        $this->load->helper("datatables");
        $aoColumnDefs = json_encode(getColumnsDatatable($this->crearColumnas()));
        echo $aoColumnDefs;
    }

    public function listar() {
        $filial = $this->session->userdata('filial');
        $separador = $filial['nombreFormato']['separadorNombre'];
        $separadorDecimal = $filial['moneda']['separadorDecimal'];
        $crearColumnas = $this->crearColumnas();
        $arrFiltros["iDisplayStart"] = isset($_POST['iDisplayStart']) ? $_POST['iDisplayStart'] : "";
        $arrFiltros["iDisplayLength"] = isset($_POST['iDisplayLength']) ? $_POST['iDisplayLength'] : "";
        $arrFiltros["sSearch"] = isset($_POST['sSearch']) ? $_POST['sSearch'] : "";
        $arrFiltros["sEcho"] = isset($_POST['sEcho']) ? $_POST['sEcho'] : "";
        $arrFiltros["SortCol"] = isset($_POST['iSortCol_0']) ? $crearColumnas[$_POST['iSortCol_0']]["campo"] : "";
        $arrFiltros["sSortDir"] = isset($_POST['sSortDir_0']) ? $_POST['sSortDir_0'] : "";
        $valores = $this->Model_notas_credito->listarNotasCreditoDataTable($arrFiltros, $separador, $separadorDecimal);
        echo json_encode($valores);
    }

    public function getDetallesNotaCredito() {
        $this->load->library('form_validation');
        $codigo = $this->input->post('codigo');
        $this->form_validation->set_rules('codigo', lang('codigo'), 'numeric');
        if ($this->form_validation->run() == false) {
            $errors = validation_errors();
            echo $errors;
        } else {
            $detalles['nc'] = $this->Model_notas_credito->getArrNotaCredito($codigo);
            $detalles['imputaciones'] = $this->Model_notas_credito->getImputaciones($codigo);
            $detalles['facturas'] = $this->Model_notas_credito->getRenglonesNC($codigo);
            $detalles['historico'] = $this->Model_notas_credito->getHistorico($codigo);
            echo json_encode($detalles);
        }
    }

    public function frm_notacredito() {
        $this->load->library('form_validation');

        $codigo = $this->input->post('codigo');
        $cod_factura = $this->input->post('cod_factura');

        if ($codigo != '') {
            $this->form_validation->set_rules('codigo', lang('codigo'), '');

            $validacion = $this->form_validation->run();
        } else {
            $validacion = true;
        }

        if ($validacion == false) {
            $errors = validation_errors();
            $respuesta['codigo'] = 0;
            $respuesta['errors'] = $errors;
            echo json_encode($respuesta);
        } else {
            $filial = $this->session->userdata('filial');
            $arrConfig = array('codigo_filial' => $filial['codigo']);

            $this->load->model("Model_alumnos", "", false, $arrConfig);
            $this->load->model("Model_facturas", "", false, $arrConfig);
            $data['moneda'] = $filial["moneda"];

            $data['codigo'] = $codigo;
            $data['cod_factura'] = $cod_factura;
            $data['motivos'] = $this->Model_notas_credito->getMotivos();
            if ($data['codigo'] != '') {
                $nc = $this->Model_notas_credito->getNotaCredito($data['codigo']);
                $data['nc'] = $nc;
                $separador = $filial['moneda']['separadorDecimal'];
                $total = str_replace('.', $separador, $nc->importe);
                $data['total'] = $total;
                $data['cod_alumno'] = $nc->cod_alumno;
                $data['facturas'] = $this->Model_alumnos->getFacturasNC($data['cod_alumno']);
//                $data['ctacte_imputar'] = json_encode($this->Model_alumnos->getCtaCteCobro($data['cod_alumno']));
            }
            if ($data['cod_factura'] != '') {
                $alumno = $this->Model_facturas->getAlumno($data['cod_factura']);
                $data['cod_alumno'] = $alumno->getCodigo();
                $data['facturas'] = $this->Model_alumnos->getFacturasNC($data['cod_alumno']);

//                $data['ctacte_imputar'] = json_encode($this->Model_alumnos->getCtaCteCobro($data['cod_alumno']));
            }

            $this->load->view('notas_credito/frm_notacredito', $data);
        }
    }

    public function getFacturas() {
        $filial = $this->session->userdata('filial');
        $arrConfig = array('codigo_filial' => $filial['codigo']);
        $this->load->model("Model_alumnos", "", false, $arrConfig);

        $codigo = $this->input->post('cod_alumno');
        $respuesta = $this->Model_alumnos->getFacturasNC($codigo);

        echo json_encode($respuesta);
    }

    public function getCtacteImputarAlumno() {
        $filial = $this->session->userdata('filial');
        $arrConfig = array('codigo_filial' => $filial['codigo']);
        $this->load->model("Model_alumnos", "", false, $arrConfig);

        $codigo = $this->input->post('cod_alumno');
        $ctaCteSinImutar = $this->Model_alumnos->getCtaCteCobro($codigo);

        echo json_encode($ctaCteSinImutar);
    }

    public function guardar() {
        $filial = $this->session->userdata('filial');
        $usuario = $this->session->userdata('codigo_usuario');
        $this->load->library('form_validation');
        $this->load->helper('formatearfecha');
        $resultado = '';

        $separador = $filial['moneda']['separadorDecimal'];

        $codigo = $this->input->post('codigo');
        $ctactecheck = $this->input->post('checkctacte') ? $this->input->post('checkctacte') : array();
        $arrfacturas = array();
        $facturas = $this->input->post('facturas') ? $this->input->post('facturas') : array();
        $facturasimportes = $this->input->post('importe') ? $this->input->post('importe') : array();
        foreach ($facturas as $key => $row) {
            $importe = str_replace($separador, '.', $facturasimportes[$key]);
            $arrfacturas[] = array('factura' => $row, 'importe' => $importe);

            $_POST['cod_factura' . $key] = $row;
            $_POST['importe' . $key] = $importe;

            $this->form_validation->set_rules('cod_factura' . $key, lang('factura'), 'validarImporteFacturaNC[' . $importe . ']');
            $this->form_validation->set_rules('importe' . $key, lang('importe'), 'numeric');
        }

        $data_post = array();
        $this->form_validation->set_rules('facturas', lang('facturas'), 'required');
        $this->form_validation->set_rules('fecha_nota', lang('fecha'), 'required');
        $this->form_validation->set_rules('motivo', lang('motivo_nc'), 'required');

        if ($codigo == '') {
            $this->form_validation->set_rules('alumnos', lang('alumnos_cobro'), 'required');
        } else {
            $this->form_validation->set_rules('codigo', lang('cobro'), 'required');
        }

        if ($this->form_validation->run() == false) {
            $errors = validation_errors();
            $resultado = array(
                'codigo' => '0',
                'msgerror' => $errors,
                'errNo' => ''
            );
        } else {
            $data_post['codigo'] = $codigo != '' ? $codigo : -1;
            $data_post['fecha'] = formatearFecha_mysql($this->input->post('fecha_nota'));
            $data_post['cod_alumno'] = $this->input->post('alumnos');
            //$data_post['checkctacte'] = $ctactecheck;
            $data_post['facturas'] = $arrfacturas;
            $data_post['total'] = $this->input->post('total_nota');
            $data_post['cod_usuario'] = $usuario;
            $data_post['motivo'] = $this->input->post('motivo');
            $resultado = $this->Model_notas_credito->guardaNC($data_post);
        }
        echo json_encode($resultado);
    }

    public function frm_imputar() {
        $this->load->library('form_validation');

        $this->form_validation->set_rules('codigo', lang('codigo'), 'required');

        if ($this->form_validation->run() == false) {
            $errors = validation_errors();
            $respuesta['codigo'] = 0;
            $respuesta['errors'] = $errors;
            echo json_encode($respuesta);
        } else {
            $filial = $this->session->userdata('filial');
            $arrConfig = array('codigo_filial' => $filial['codigo']);

            $data['cobro_imputaciones'] = '';
            $data['ctacte_imputar'] = '';
            $data['medio_cobro'] = '';
            $this->load->model("Model_alumnos", "", false, $arrConfig);
            $data['moneda'] = $filial["moneda"];
            $data['codigo'] = $this->input->post('codigo');
            $nota = $this->Model_notas_credito->getNotaCredito($data['codigo']);
            $data['nc'] = $nota;
            $separador = $filial['moneda']['separadorDecimal'];
            $total = str_replace('.', $separador, $nota->importe);
            $data['total_nc'] = $total;
            $data['alumno'] = array('codigo' => $nota->cod_alumno, 'nombre' => $this->Model_alumnos->getNombreAlumno($nota->cod_alumno));

            $data['ctacte_imputar'] = json_encode($this->Model_alumnos->getCtaCteCobro($nota->cod_alumno));

            $this->load->view('notas_credito/frm_imputar', $data);
        }
    }

    public function getRestaImputar() {
        $codigo = $this->input->post('codigo');
        $respuesta = $this->Model_notas_credito->getRestaImputar($codigo);
        echo json_encode($respuesta);
    }

    public function getCtaCteImputar() {
        $codigo = $this->input->post('codigo');
        $ctaCteSinImutar = $this->Model_notas_credito->getCtaCteImputar($codigo);

        echo json_encode($ctaCteSinImutar);
    }

    public function eliminarImputacion() {
        $filial = $this->session->userdata('filial');
        $config = array('codigo_filial' => $filial['codigo']);
        $respuesta = '';
        $this->load->model("Model_imputaciones", "", false, $config);

        $this->load->library('form_validation');
        $codigo = $this->input->post('codigo');
        $usuario = $this->session->userdata('codigo_usuario');

        $this->form_validation->set_rules('codigo', lang('codigo'), 'numeric');

        if ($this->form_validation->run() == false) {

            $errors = validation_errors();
            $respuesta = array(
                'codigo' => '0',
                'msgerror' => $errors,
                'errNo' => ''
            );
        } else {
            $respuesta['codigo'] = $this->Model_imputaciones->eliminarImputacion($codigo, $usuario);
        }
        echo json_encode($respuesta);
    }

    public function guardarImputaciones() {
        $usuario = $this->session->userdata('codigo_usuario');
        $this->load->library('form_validation');
        $this->load->helper('formatearfecha');

        $ctactecheck = $this->input->post('checkctacte') ? $this->input->post('checkctacte') : array();

        $this->form_validation->set_rules('codigo', lang('codigo'), 'required');
        if ($this->form_validation->run() == false) {
            $errors = validation_errors();
            $resultado = array(
                'codigo' => '0',
                'msgerror' => $errors,
                'errNo' => ''
            );
        } else {

            $data_post['cod_nc'] = $this->input->post('codigo');
            $data_post['checkctacte'] = $ctactecheck;
            $data_post['cod_usuario'] = $usuario;
            $resultado = $this->Model_notas_credito->guardarImputaciones($data_post);
        }
        echo json_encode($resultado);
    }

    public function getTotalImputaciones() {
        $codigo = $this->input->post('codigo');
        $totImputaciones = $this->Model_notas_credito->getTotalImputaciones($codigo);
        echo json_encode($totImputaciones);
    }

    public function getImputaciones() {
        $this->load->library('form_validation');
        $codigo = $this->input->post('codigo');
        $this->form_validation->set_rules('codigo', lang('codigo'), 'numeric');
        if ($this->form_validation->run() == false) {
            $errors = validation_errors();
            echo $errors;
        } else {
            $imputacionesCobro = $this->Model_notas_credito->getImputaciones($codigo);
            echo json_encode($imputacionesCobro);
        }
    }

    public function frm_confirmar() {
        $this->load->library('form_validation');
        $respuesta = '';
        $usuario = $this->session->userdata('codigo_usuario');

        $this->form_validation->set_rules('codigo', lang('codigo'), 'numeric|validarConfirmarNC[' . $usuario . ']');

        if ($this->form_validation->run() == false) {
            $errors = validation_errors();
            $respuesta['codigo'] = 0;
            $respuesta['errors'] = $errors;
            echo json_encode($respuesta);
        } else {
            $codigo = $this->input->post('codigo');
            $datos['cod_nc'] = $codigo;
            $datos['alumno'] = $this->Model_notas_credito->getNombreAlumno($codigo);
            $this->load->view('notas_credito/frm_confirmar', $datos);
        }
    }

    public function confirmar() {
        $usuario = $this->session->userdata('codigo_usuario');
        $codigo = $this->input->post('codigo');

        $datos = array('cod_nc' => $codigo, 'cod_usuario' => $usuario);

        $respuesta = $this->Model_notas_credito->confirmarNC($datos);

        echo json_encode($respuesta);
    }

    public function frm_anular() {
        $this->load->library('form_validation');

        $usuario = $this->session->userdata('codigo_usuario');
        $this->form_validation->set_rules('codigo', lang('codigo'), 'numeric|validarAnularNC[' . $usuario . ']');

        if ($this->form_validation->run() == false) {
            $errors = validation_errors();
            $respuesta['codigo'] = 0;
            $respuesta['errors'] = $errors;
            echo json_encode($respuesta);
        } else {
            $codigo = $this->input->post('codigo');
            $data['movitos'] = $this->Model_notas_credito->getMotivosBaja();
            $data['cod_nc'] = $codigo;
            $data['objNC'] = $this->Model_notas_credito->getNotaCredito($codigo);
            $data['alumno'] = $this->Model_notas_credito->getNombreAlumno($codigo);
            $this->load->view('notas_credito/frm_anular', $data);
        }
    }

    public function anular() {
        $this->load->library('form_validation');
        $usuario = $this->session->userdata('codigo_usuario');
        $this->form_validation->set_rules('codigo', lang('codigo'), 'numeric|validarAnularNC[' . $usuario . ']');
        $this->form_validation->set_rules('motivo', lang('motivo'), 'required');

        if ($this->form_validation->run() == false) {
            $errors = validation_errors();
            $respuesta['codigo'] = 0;
            $respuesta['errors'] = $errors;
            echo json_encode($respuesta);
        } else {
            $codigo = $this->input->post('codigo');
            $motivo = $this->input->post('motivo');
            $comentarios = $this->input->post('comentario');

            $datos = array(
                'cod_nc' => $codigo,
                'motivo' => $motivo,
                'comentario' => $comentarios,
                'cod_usuario' => $usuario
            );
            $respuesta = $this->Model_notas_credito->anularNC($datos);

            echo json_encode($respuesta);
        }
    }

    public function calcularTotal() {
        $this->load->library('form_validation');
        $filial = $this->session->userdata('filial');
        $config = array("codigo_filial" => $filial['codigo']);
        $this->load->model("Model_configuraciones", "", false, $config);
        $separador = $this->Model_configuraciones->getValorConfiguracion(null, 'SeparadorDecimal');
        $arrValores = $this->input->post('importes') ? $this->input->post('importes') : array();

        $retornar = '';
        $monedaSimbolo = $filial['moneda']['simbolo'];
        foreach ($arrValores as $key => $valor) {
            $_POST['Valorctacte' . $key] = strlen($valor) > 0 ? str_replace($monedaSimbolo, '', $valor) : $valor;
            $this->form_validation->set_rules('Valorctacte' . $key, 'input' . $key, 'validarExpresionTotal');
        }
        if ($this->form_validation->run() == false) {
            $errors = validation_errors();
            $retornar = array(
                'codigo' => '0',
                'msgerror' => $errors,
                'errNo' => ''
            );
        } else {

            $retornar['codigo'] = 1;
            $retornar['total'] = $this->Model_cobros->calcularTotal($arrValores);
        }
        echo json_encode($retornar);
    }

}

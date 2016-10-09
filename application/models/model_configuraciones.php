<?php

/**
 * Model_configuraciones
 *
 * Description...
 *
 * @package model_configuraciones
 * @author ivan <ivan.sys@gmail.com>
 * @version 1.0.0
 */
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Model_configuraciones extends CI_Model {

    var $cod_filial = 0;

    public function __construct($arg) {
        parent::__construct();
        $this->cod_filial = $arg["codigo_filial"];
    }

//    function getConfiguracion($id = null, $key = null) {
//
//        $conexion = $this->load->database($this->cod_filial, true);
//        $arrcondiciones = array();
//        if ($id != null) {
//            $arrcondiciones["codigo"] = $id;
//        }
//        if ($key != null) {
//            $arrcondiciones["key"] = $key;
//        }
//
//        return Vconfiguracion::listarConfiguracion($conexion, $arrcondiciones);
//    }

    function getValorConfiguracion($id = null, $key = null, $index = null, $codigo = null) {

        $conexion = $this->load->database($this->cod_filial, true);

        $arrcondiciones = array();
        Vconfiguracion::listarConfiguracion($conexion, $arrcondiciones);
        if ($id == null) {
            if ($key != null) {
                $arrcondiciones["key"] = $key;
                $rowconf = Vconfiguracion::listarConfiguracion($conexion, $arrcondiciones);

                if ( !isset($rowconf[0]) ) {
                    return null;
                }

                $id = $rowconf[0]['codigo'];
            }
        }
        $configuracion = new Vconfiguracion($conexion, $id);
        if ($configuracion->json_is($configuracion->value)) {
            $arrValores = json_decode($configuracion->value, true);

            if ($index != null) {
                // agrega la posibilidad de que no exista el indice de configuracion y retorna un array vacio (ver scripot de configuracion de impresion)
                if (isset($arrValores[$index])) {
                    return $arrValores[$index];
                } else {
                    return array();
                }
            } elseif ($codigo != null) {
                $retorna = array();
                foreach ($arrValores as $a => $rowvalor) {
                    if (isset($rowvalor['codigo']) && $rowvalor['codigo'] == $codigo) {
                        $retorna = $arrValores[$a];
                    }
                }
                return $retorna;
            } else {

                return $arrValores;
            }
        } else {
            return $configuracion->value;
        }
    }

    public function traducirPeriodos($periodos) {

        foreach ($periodos as $key => $periodo) {
            $lang = '';
            switch ($periodo['unidadTiempo']) {
                case 'day':
                    $lang = $periodo['valor'] > 1 ? 'dias' : 'dia';
                    break;

                case 'month':
                    $lang = $periodo['valor'] > 1 ? 'meses' : 'mes';
                    break;

                case 'year':
                    $lang = $periodo['valor'] > 1 ? 'años' : 'año';
                    break;

                default:
                    break;
            }
            $periodos[$key]['traducido'] = lang($lang);
        }

        return $periodos;
    }

    public function get_horarios_atencion() {
        $conexion = $this->load->database($this->cod_filial, true);
        $myConfiguracion = new Vconfiguracion($conexion, 19);
        $arrHoras = json_decode($myConfiguracion->value, true);
        return $arrHoras;
    }

    public function guardar_horarios_atencion($arrHorarios) {
        foreach ($arrHorarios as $dia => $valores) {
            if (isset($valores['e2'])) {
                if ($valores['cerrado'] == 1) {
                    $arrHorarios[$dia]['e1'] = "";
                    $arrHorarios[$dia]['s1'] = "";
                    $arrHorarios[$dia]['e2'] = "";
                    $arrHorarios[$dia]['s2'] = "";
                } else if ($valores['e2'] == $valores['s2']) {
                    $arrHorarios[$dia]['e2'] = "";
                    $arrHorarios[$dia]['s2'] = "";
                }
            }
        }
        $configuracionHorarios = json_encode($arrHorarios);
        $conexion = $this->load->database($this->cod_filial, true);
        $myConfiguracion = new Vconfiguracion($conexion, 19);
        $myConfiguracion->value = $configuracionHorarios;
        $codUsuario = $this->session->userdata['codigo_usuario'];
        return $myConfiguracion->guardarConfiguracion($codUsuario);
    }

    function guardar_configuracion_impresion_extra($idScript, $cantidadCopias = null, $tamanioPapel = null,
            $imprimeRazonSocial = null, $muestraCuotasTotal = null, $texto = null, $imprimePlan = null,
            $imprimeTitulo = null, $localidadForo = null, $mostrarPrecioListaYDescuento = null,
            $modelo_factura_electronica = null, $mostrarRUC = null, $mostrarCOM = null) {
        $conexion = $this->load->database($this->cod_filial, true);
        $conexion->trans_begin();
        $myConfiguracion = new Vconfiguracion($conexion, 20);
        $arrConfiguraciones = json_decode($myConfiguracion->value, true);
        if ($cantidadCopias != null){
            $arrConfiguraciones[$idScript]['copias'] = $cantidadCopias;
        }
        if ($tamanioPapel != null){
            $arrConfiguraciones[$idScript]['papel'] = $tamanioPapel;
        }
        if ($imprimeRazonSocial !== null){
            $arrConfiguraciones[$idScript]['imprimir_razon'] = $imprimeRazonSocial;
        }
        if ($muestraCuotasTotal !== null){
            $arrConfiguraciones[$idScript]['muestra_total_cuotas'] = $muestraCuotasTotal;
        }
        if ($modelo_factura_electronica != null){
            $arrConfiguraciones[$idScript]['modelo_factura_electronica'] = $modelo_factura_electronica;
        }
        if ($mostrarRUC != null){
            $arrConfiguraciones[$idScript]['mostrar_ruc'] = $mostrarRUC;
        }
        if ($mostrarCOM != null){
            $arrConfiguraciones[$idScript]['mostrar_com'] = $mostrarCOM;
        }
        $myConfiguracion->value = json_encode($arrConfiguraciones);
        $codUsuario = $this->session->userdata['codigo_usuario'];
        $myConfiguracion->guardarConfiguracion($codUsuario);
        if ($texto != null) {
            if ($idScript == 5) {
                $myConfiguracion2 = new Vconfiguracion($conexion, 11);
                $myConfiguracion2->value = $texto;
                $myConfiguracion2->guardarConfiguracion($codUsuario);
            } else if ($idScript == 1) {
                $myConfiguracion2 = new Vconfiguracion($conexion, 16);
                $myConfiguracion2->value = $texto;
                $myConfiguracion2->guardarConfiguracion($codUsuario);
                $myConfiguracionDetalles = new Vconfiguracion($conexion, null, "mostrarPrecioListaYDescuento");
                $myConfiguracionDetalles->value = $mostrarPrecioListaYDescuento;
                $myConfiguracionDetalles->guardarConfiguracion($codUsuario);
            }
        }
        if ($imprimePlan !== null) {
            $myConfiguracion3 = new Vconfiguracion($conexion, 41);
            $myConfiguracion3->value = $imprimePlan;
            $myConfiguracion3->guardarConfiguracion($codUsuario);
        }

        if ($imprimeTitulo !== null) {
            $myConfiguracion4 = new Vconfiguracion($conexion, 42);
            $myConfiguracion4->value = $imprimeTitulo;
            $myConfiguracion4->guardarConfiguracion($codUsuario);
        }

        if ($localidadForo !== null) {
            $myConfiguracion4 = new Vconfiguracion($conexion, 45);
            $myConfiguracion4->value = $localidadForo;
            $myConfiguracion4->guardarConfiguracion($codUsuario);
        }
        if ($conexion->trans_status() === false) {
            $conexion->trans_rollback();
            return false;
        } else {
            $conexion->trans_commit();
            return true;
        }
    }

    public function getPuntosVenta() {
        $conexion = $this->load->database($this->cod_filial, true);
        $puntosVenta = Vpuntos_venta::getPuntosVentas($conexion, $this->cod_filial);
        return $puntosVenta;
    }

    public function guardarPuntoVenta($data_post) {
        $conexion = $this->load->database($this->cod_filial, true);
        $conexion->trans_begin();
        $objTalonario = new Vtalonarios($conexion, $data_post['tipofactura'], $data_post['facturante'], $data_post['puntoVenta']);
        $arrGuardarPtoVenta = array(
            "comentarios" => $data_post['observaciones'],
            "usuario_creador" => $data_post['cod_usuario'],
            "activo" => $data_post['activo'] != '' ? 1 : 0,
            "punto_venta" => $data_post['puntoVenta'],
            "fechahora" => date("Y-m-d H:i:s"),
            "ultimonumero" => $data_post['ultimoNumero']
        );
        $objTalonario->setTalonarios($arrGuardarPtoVenta);
        $objTalonario->guardarTalonarios();
        $objTalonario->unSetTalonariosUsuarios();
        foreach ($data_post['usuarios'] as $usuario) {

            $objTalonario->setTalonariosUsuarios($usuario);
        }
        $estadotran = $conexion->trans_status();
        if ($estadotran === FALSE) {
            $conexion->trans_rollback();
        } else {
            $conexion->trans_commit();
        }
        return class_general::_generarRespuestaModelo($conexion, $estadotran);
    }

    public function guardarConfiguracion($usuario, $nombre, $valor, $filial, $refrescarSesion = true) {

        $conexion = $this->load->database($this->cod_filial, true);
        $conexion->trans_begin();
        $condicion = array(
            "key" => $nombre
        );
        $config = Vconfiguracion::listarConfiguracion($conexion, $condicion);

        if ( isset($config[0]) ) {
            $config = $config[0];
        }

        /*
        echo "\n\nconfig post:";
        print_r($config);
        echo "\n\n";
        */

        $codigo = null;
        if ( isset($config['codigo']) ) {
            $codigo = $config['codigo'];

        }

        $objConfiguracion = new Vconfiguracion($conexion, $codigo);

        $arrayConfiguracion = array(
            "key" => $nombre,
            "value" => $valor,
            "fecha_hora" => date("Y-m-d H:m:s")
        );

        $objConfiguracion->setConfiguracion($arrayConfiguracion);
        $objConfiguracion->guardarConfiguracion($usuario);
        $estadotran = $conexion->trans_status();
        if ($estadotran === FALSE) {

            $conexion->trans_rollback();
        } else {

            $conexion->trans_commit();
            $config = array("codigo_filial" => $filial);
            $this->load->model("Model_usuario", "", false, $config);
            if ($refrescarSesion)
                $this->Model_usuario->refrescarSession($usuario);
        }
        return class_general::_generarRespuestaModelo($conexion, $estadotran);
    }

    public function guardarConfiguracionAlertaExamen($examenesAlerta, $nombre, $usuario) {
        $conexion = $this->load->database($this->cod_filial, true);
        $conexion->trans_begin();
        $condicion = array(
            "key" => $nombre
        );
        $config = Vconfiguracion::listarConfiguracion($conexion, $condicion);
        $objConfiguracion = new Vconfiguracion($conexion, $config[0]['codigo']);
        $guardarConfiguracion = array(
            "key" => 'ConfiguracionAlertaExamenes',
            "value" => json_encode($examenesAlerta),
            "fecha_hora" => date('Y-m-d H:i:s')
        );
        $objConfiguracion->setConfiguracion($guardarConfiguracion);
        $objConfiguracion->guardarConfiguracion($usuario);
        $estadotran = $conexion->trans_status();
        if ($estadotran === FALSE) {
            $conexion->trans_rollback();
        } else {
            $conexion->trans_commit();
        }
        return class_general::_generarRespuestaModelo($conexion, $estadotran);
    }

    public function getPeriodicidad() {
        $conexion = $this->load->database($this->cod_filial, true);
        $periodos = Vconfiguracion::getValorConfiguracion($conexion, null, 'PeriodoCtacte');
        $periodost = $this->traducirPeriodos($periodos);
        $arrperiodos = array();
        foreach ($periodost as $key => $row) {
            if ($row['baja'] == 0) {
                $arrperiodos[$key] = array('codigo' => $row['codigo'], 'nombre' => $row['valor'] . ' ' . $row['traducido']);
            }
        }
        return $arrperiodos;
    }

    public function guardarDiasCobroFilial($data_post) {
        $conexion = $this->load->database($this->cod_filial, true);
        $myConfiguracion = new Vconfiguracion($conexion, 38);
        $arrDiasCobroFilial = json_encode($data_post);
        $myConfiguracion->value = $arrDiasCobroFilial;
        return $myConfiguracion->guardarConfiguracion($this->session->userdata['codigo_usuario']);
    }

    public function getDiasCobrosFilial() {
        $conexion = $this->load->database($this->cod_filial, true);
        $diasCobrosFilial = Vconfiguracion::getValorConfiguracion($conexion, '', 'diasCobroFilial');

        return $diasCobrosFilial;
    }

    public function guardarConfiguracionNotasExamen($data_post) {
        $conexion = $this->load->database($this->cod_filial, true);
        $conexion->trans_begin();
        $arrGuardar = '';
        switch ($data_post['NombreFormato']) {
            case "alfabetico":
                $arrGuardar['formato_nota'] = $data_post['NombreFormato'];
                $arrGuardar['escala_nota'] = $data_post['escala_notas'];
                $arrGuardar['nota_aprueba_final'] = $data_post['nota_aprueba_final'];
                $arrGuardar['nota_aprueba_parcial'] = $data_post['nota_aprueba_parcial'];

                break;

            case "numerico":
                $arrGuardar['formato_nota'] = $data_post['NombreFormato'];
                $arrGuardar['numero_desde'] = $data_post['numero_desde'];
                $arrGuardar['numero_hasta'] = $data_post['numero_hasta'];
                $arrGuardar['nota_aprueba_final'] = $data_post['nota_aprueba_final'];
                $arrGuardar['nota_aprueba_parcial'] = $data_post['nota_aprueba_parcial'];
                break;
        }
        $arrGuardarConfiguracion = json_encode($arrGuardar);
        $myConfiguracion = new Vconfiguracion($conexion, 40);
        $myConfiguracion->value = $arrGuardarConfiguracion;
        $myConfiguracion->guardarConfiguracion($this->session->userdata['codigo_usuario']);
        $estadotran = $conexion->trans_status();
        if ($estadotran === FALSE) {
            $conexion->trans_rollback();
        } else {
            $conexion->trans_commit();
        }
        return class_general::_generarRespuestaModelo($conexion, $estadotran);
    }

    public function getArrayEscalaNotasExamen($key, $tipo_formato) {
        $arrDatosExamenConfiguracion = $this->getValorConfiguracion(null, $key);
        $arrEscalaNotas = '';
        switch ($tipo_formato) {
            case 'alfabetico':
                $arrEscalaNotas = explode(",", $arrDatosExamenConfiguracion['escala_nota']);
                foreach ($arrEscalaNotas as $key => $valor) {
                    $arrEscalaNotas[$key] = trim($valor);
                }

                break;

            case 'numerico':
                for ($i = $arrDatosExamenConfiguracion['numero_desde']; $i <= $arrDatosExamenConfiguracion['numero_hasta']; $i++) {
                    $arrEscalaNotas[$i] = $i;
                }
                break;
        }
        return $arrEscalaNotas;
    }

    public function guardarConfiguracionDescuentos($datos, $nombre, $usuario) {
        $conexion = $this->load->database($this->cod_filial, true);
        $conexion->trans_begin();
        $condicion = array(
            "key" => $nombre
        );
        $config = Vconfiguracion::listarConfiguracion($conexion, $condicion);
        $objConfiguracion = new Vconfiguracion($conexion, $config[0]['codigo']);
        $guardarConfiguracion = array(
            "key" => $nombre,
            "value" => json_encode($datos),
            "fecha_hora" => date('Y-m-d H:i:s')
        );
        $objConfiguracion->setConfiguracion($guardarConfiguracion);
        $objConfiguracion->guardarConfiguracion($usuario);
        $estadotran = $conexion->trans_status();
        if ($estadotran === FALSE) {
            $conexion->trans_rollback();
        } else {
            $conexion->trans_commit();
        }
        return class_general::_generarRespuestaModelo($conexion, $estadotran);
    }

//    public function getMediosCobros() {
//        $conexion = $this->load->database($this->cod_filial, true);
//        $medios = Vmedios_pago::listarMedios_pago($conexion);
//        $myConfiguracion = Vconfiguracion::getValorConfiguracion($conexion, null, 'confirmaAutoMediosCobro');
//        $arr = array();
//        foreach ($myConfiguracion as $rowconf) {
//            $arr[] = $rowconf['codigo'];
//        }
//        $arrMedios = array();
//        foreach ($medios as $value) {
//            $objmedio = new Vmedios_pago($conexion, $value['codigo']);
//            $conf = in_array($value['codigo'], $arr) ? 1 : 0;
//            $arrMedios[] = array('codigo' => $objmedio->getCodigo(), 'medio' => lang($objmedio->medio), 'conf_auto' => $conf);
//        }
//
//        return $arrMedios;
//    }
//    public function guardarConfirmacionMedios($datos) {
//        $conexion = $this->load->database($this->cod_filial, true);
//        $arrConf = array();
//        foreach ($datos as $value) {
//            $condicion = array('codigo' => $value);
//            $arrmedio = Vmedios_pago::listarMedios_pago($conexion, $condicion);
//            if (count($arrmedio) > 0) {
//                $arrConf[] = array('codigo' => $value);
//            }
//        }
//        $condicion = array('key' => 'confirmaAutoMediosCobro');
//        $configuracion = Vconfiguracion::listarConfiguracion($conexion, $condicion);
//
//        $objconf = new Vconfiguracion($conexion, $configuracion[0]['codigo']);
//        $objconf->value = json_encode($arrConf);
//        return $objconf->guardarConfiguracion($this->session->userdata['codigo_usuario']);
//    }

    public function generarCsrFacturante($facturante) {
        $conexion = $this->load->database('', true);
        $Vfacturante = new Vfacturantes($conexion, $facturante);

        $pkcs10 = $Vfacturante->generarCsr();

        return $pkcs10;
    }

    public function registrarCrtFacturante($facturante, $str_crt) {
        $conexion = $this->load->database('', true);
        $Vfacturante = new Vfacturantes($conexion, $facturante);
        $errorRegistroCrt = "";
        try {
            $registroCrt = $Vfacturante->registrarCrt($str_crt);

        } catch (Exception $exc) {
          $errorRegistroCrt = $exc->getMessage();

        }

        if ($registroCrt) {
           $resultado = $Vfacturante->actualizarPuntosVentaElectronico($this->config->item('ws_afip_testing'));
           $retorno['codigo'] = $registroCrt;
           $retorno['custom'] =  $resultado;
        }else {
             $retorno['custom']['comentarios'] = $errorRegistroCrt;
            $retorno['codigo']= 0;

        }
        return $retorno;
    }

    public function getInfoCrtFacturante($facturante) {
        $conexion = $this->load->database('', true);
        $Vfacturante = new Vfacturantes($conexion, $facturante);

        $certificado = $Vfacturante->getCertificado();

        return $certificado->getInfoCert();
    }

    public function guardarConfiguracionFacturacionSegmentada($data_post) {
        $conexion = $this->load->database($this->cod_filial, true);
        $conexion->trans_begin();

        $myConfiguracion = new Vconfiguracion($conexion, null, 'facturacionNominada');
        $myConfiguracion->value = empty($data_post['facturacion_nominada']) ? '0' : '1';
        $myConfiguracion->guardarConfiguracion($this->session->userdata['codigo_usuario']);

        $myConfiguracion = new Vconfiguracion($conexion, null, 'facturacionSegmentada');
        $myConfiguracion->value = empty($data_post['facturacion_segmentada']) ? '0' : '1';
        $myConfiguracion->guardarConfiguracion($this->session->userdata['codigo_usuario']);

        $myConfiguracion = new Vconfiguracion($conexion, null, 'montoSegmento');
        $myConfiguracion->value = $data_post['monto_segmento'];
        $myConfiguracion->guardarConfiguracion($this->session->userdata['codigo_usuario']);
        $estadotran = $conexion->trans_status();

        if ($estadotran === FALSE) {
            $conexion->trans_rollback();
        } else {
            $conexion->trans_commit();
        }
        return class_general::_generarRespuestaModelo($conexion, $estadotran);
    }
}

/* End of file model_configuraciones.php */
/* Location: ./application/models/model_configuraciones.php */

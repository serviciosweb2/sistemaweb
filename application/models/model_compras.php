<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Model_compras extends CI_Model {

    var $codigo_filial = 0;

    public function __construct($arg) {
        parent::__construct();
        $this->codigo_filial = $arg["codigo_filial"];
    }

    public function listarComprasDataTable($arrFiltros) {
        $conexion = $this->load->database($this->codigo_filial, true);
        $this->load->helper('filial');
        $arrCondiciones = array();
        if ($arrFiltros["sSearch"] != "") {
            $arrCondiciones = array(
                "razones_sociales.razon_social" => $arrFiltros["sSearch"],
                "compras.codigo"=>$arrFiltros['sSearch'],
            );
        }

        $arrLimit = array();
        if ($arrFiltros["iDisplayStart"] != "" and $arrFiltros["iDisplayLength"] != "") {

            $arrLimit = array(
                "0" => $arrFiltros["iDisplayStart"],
                "1" => $arrFiltros["iDisplayLength"]
            );
        }

        $arrSort = array();

        if ($arrFiltros["SortCol"] != "" and $arrFiltros["sSortDir"] != "") {

            $arrSort = array(
                "0" => $arrFiltros["SortCol"],
                "1" => $arrFiltros["sSortDir"]
            );
        }
        $datos = Vcompras::listarComprasDataTable($conexion, $arrCondiciones, $arrLimit, $arrSort);
        $contar = Vcompras::listarComprasDataTable($conexion, $arrCondiciones, "", "", true);

        $retorno = array(
            "sEcho" => $arrFiltros["sEcho"],
            "iTotalRecords" => $contar,
            "iTotalDisplayRecords" => $contar,
            "aaData" => array()
        );
        $rows = array();
        foreach ($datos as $row) {
            $total = $row["totalCompra"] == '' ? 0 : $row["totalCompra"];
            $rows[] = array(
                $row["codigo"],
                $row["nombre"],
                formatearFecha_pais($row["fecha"]),
                formatearImporte($total, true),
                '',
                $row['estado'],
                $row['usuario_creador']
            );
        }
        $retorno['aaData'] = $rows;
        return $retorno;
    }

    public function getObjCompra($cod_compra) {
        $conexion = $this->load->database($this->codigo_filial, true);

        $objCompra = New Vcompras($conexion, $cod_compra);
        return $objCompra;
    }

    public function getCompraRenglones($cod_compra) {//formatear importes
        $conexion = $this->load->database($this->codigo_filial, true);

        $objCompra = new Vcompras($conexion, $cod_compra);
        $renglones = $objCompra->getCompraRenglones();

        for ($i = 0; $i < count($renglones); $i++) {
            $condarticulo = array('codigo' => $renglones[$i]['cod_articulo']);
            $articulo = Varticulos::listarArticulos($conexion, $condarticulo);
            $renglones[$i]['articulos'] = $articulo;
            $condcategoria = array('codigo' => $articulo[0]['cod_categoria']);
            $renglones[$i]['categorias'] = Varticulos_categorias::listarArticulos_categorias($conexion, $condcategoria);

            $objRenglon = new Vcompras_renglones($conexion, $renglones[$i]['codigo']);
            $impuestos = $objRenglon->getImpuestos();
            $arrImpuestos = array();
            $impuestosaplicados = 0;
            foreach ($impuestos as $rowimpuesto) {
                $arrImpuestos[] = $rowimpuesto['codigo'];
                $impuestosaplicados = $impuestosaplicados + $rowimpuesto['valor'];
            }
            $renglones[$i]['impuestos'] = $arrImpuestos;
            $renglones[$i]['total_impuestos'] = $renglones[$i]['precio_total'] - $renglones[$i]['precio_total'] / (1 + $impuestosaplicados / 100);
        }

        return $renglones;
    }

    public function guardarCompra($compra) {
        $conexion = $this->load->database($this->codigo_filial, true);
        $conexion->trans_start();
        //GUARDO COMPRA
        $usuario_creador = '';
        $objCompra = new Vcompras($conexion, $compra['compras']['codigo']);
        if($compra['compras']['codigo'] == -1){
            $usuario_creador = $compra['compras']['cod_usuario_creador'];
        }else{
            $usuario_creador = $objCompra->cod_usuario_creador;
        }
        $objCompra->guardar($compra['compras']['cod_proveedor'],$usuario_creador , $compra['compras']['fecha']);

        //GUARDO RENGLONES
        foreach ($compra['renglones'] as $renglon) {
            $objCompraRenglo = new Vcompras_renglones($conexion, $renglon['codigo']);
            $total = str_replace(",", ".", $renglon['precio_unitario']) * $renglon['cantidad'];
            $impuestos = isset($renglon['impuestos']) ? $renglon['impuestos'] : array();
            $objCompraRenglo->guardar($objCompra->getCodigo(), $renglon['cod_articulo'], $renglon['cantidad'], str_replace(",", ".", $renglon['precio_unitario']), $total, $impuestos);
        }
        foreach ($compra['renglonesBajas'] as $comprarenglon) {
            $objCompraRenglon = new Vcompras_renglones($conexion, $comprarenglon);
            $objCompraRenglon->baja();
        }

        //GUARDO COMPROBANTE
        foreach ($compra['comprobante'] as $comprobante) {
            $objcomprobante = new Vcompras_comprobantes($conexion, $comprobante['codigo']);
            $objcomprobante->guardar($objCompra->getCodigo(), $compra['compras']['cod_usuario_creador'], $comprobante['jsonDecodeado']['tipoComprobante'], $comprobante['precio_total'], $comprobante['nro_comprobante'], null, $comprobante['fecha_comprobante'], $comprobante['jsonDecodeado']['tipoFactura'], $comprobante['punto_venta']);
        }
        foreach ($compra['comprobanteBajas'] as $comprobanteRenglon) {
            $objComprobante = new Vcompras_comprobantes($conexion, $comprobanteRenglon);
            $objComprobante->baja();
        }

        //GUARDO PAGO
//        echo '<pre>';
//        print_r($compra['pago']);
//        echo '<pre>';
//        die();
        foreach ($compra['pago'] as $pago) {
            $objpago = new Vpagos($conexion, $pago['codigo']);
            $cod_pago = $objpago->getCodigo();
            $estado = $objpago->estado;
            $objpago->guardar($pago['cod_caja'],str_replace(",", ".", $pago['precio_total']), $compra['compras']['cod_usuario_creador'], $pago['medio_pago'], Vpagos::getConceptoproveedor(), $objCompra->cod_proveedor, $estado, $pago['fecha_pago']);
            //CONFIRMO EL PAGO Y GUARDO EN CAJA
            if ($cod_pago == -1) {
                $objpago->confirmar($pago['cod_caja'], $compra['compras']['cod_usuario_creador']);
            }


            //GUARDO COMPRA IMPUTACION

            if ($pago['codigo'] == '-1') {
                $objCompra->imputarPago($objpago->getCodigo());
            }
        }
        foreach ($compra['pagoBajas'] as $pagoBaja) {
            $objPago = new Vpagos($conexion, $pagoBaja);
            $objPago->anular($objPago->cod_caja, $compra['compras']['cod_usuario_creador']);
            $objCompra->desimputarPago($objPago->getCodigo());
        }

        $estadotran = $conexion->trans_status();
        $conexion->trans_complete();
        return class_general::_generarRespuestaModelo($conexion, $estadotran);
    }

    public function getComprobantes($codcompra) {
        $conexion = $this->load->database($this->codigo_filial, true);
        $this->load->helper('formatearfecha');
        $this->load->helper('filial');
        $objcompra = new Vcompras($conexion, $codcompra);
        $comprobantes = $objcompra->getComprobantes('habilitado');
        foreach ($comprobantes as $key => $comprobante) {
            $comprobantes[$key]['fecha_comprobante'] = formatearFecha_pais($comprobante['fecha_comprobante']);
            $comprobantes[$key]['totalFormateado'] = formatearImporte($comprobante['total']);
        }
        return $comprobantes;
    }

    public function getPagosImputados($codcompra) {
        $conexion = $this->load->database($this->codigo_filial, true);
        $objcompra = new Vcompras($conexion, $codcompra);
        $pagos = $objcompra->getPagos('confirmado');

        foreach ($pagos as $i => $pago) {
            $pagos[$i]['fecha_pago'] = formatearFecha_pais($pago['fecha_pago']);
            $objpago = new Vpagos($conexion, $pago['codigo']);
            $movcaja = $objpago->getMovimientoCajaPago();
            $caja = new Vcaja($conexion, $movcaja[0]['codigo']);
            if ($caja->continuaAbierta($movcaja[0]['codigo'])) {
                $pagos[$i]['modificar'] = true;
            } else {
                $pagos[$i]['modificar'] = false;
            }
        }

        return $pagos;
    }

    public function getImportesRenglones($datos) {
//        echo '<pre>'; 
//print_r($datos);
//echo '</pre>';
        $conexion = $this->load->database($this->codigo_filial, true);
        $total = 0;
        $totalimpuestos = 0;
        $respuestas = $datos;
        $retorno = array();
        $i = 0;

        foreach ($respuestas as $key => $respuesta) {
            $precioUnitario = str_replace(",", ".", $respuesta['precio_unitario']);
            $totalart = $precioUnitario * $respuesta['cantidad'];
            $total = $total + $totalart;
            $totimpuestos = 0;
            if (isset($respuesta['impuestos'])) {
                foreach ($respuesta['impuestos'] as $codimpuesto) {
                    $objimpuesto = new Vimpuestos($conexion, $codimpuesto);
                    $impuesto = $objimpuesto->getValorImpuesto();
                    $totimpuestoart = $totalart * $impuesto / 100;
                    $totimpuestos = $totimpuestos + $totimpuestoart;
                }
            }

            $totalimpuestos = $totalimpuestos + $totimpuestos;
            $retorno[$i]['precio_total'] = str_replace(".", ",", $totalart);
            $retorno[$i]['total_impuestos'] = $totalimpuestos;
            $i++;
        }



        $retorno['totales']['importe'] = str_replace(".", ",", $total);
        $retorno['totales']['importesinimpuestos'] = str_replace(".", ",", $total - $totalimpuestos);
        $retorno['totales']['impuestos'] = $totalimpuestos;

        return $retorno;
    }

    public function getImpuestos() {
        $conexion = $this->load->database($this->codigo_filial, true);
        $condicion = array('tipo' => 'compras');
        $impuestos = Vimpuestos::listarImpuestos($conexion, $condicion);
        return $impuestos;
    }

    /**
     * cambia el estado de una compra
     * @access public
     * @return repuesta Guardar el cambio de estado.
     */
    public function cambiarEstado($cod_compra) {
        $conexion = $this->load->database($this->codigo_filial, true);
        $conexion->trans_begin();
        $compra = new Vcompras($conexion, $cod_compra);
        $respuesta = '';

        if ($compra->estado == 'confirmada') {
            $respuesta = $compra->anular();
        } else {
            $respuesta = $compra->confirmar();
        }

        $estadotran = $conexion->trans_status();
        if ($estadotran === FALSE) {

            $conexion->trans_rollback();
        } else {

            $conexion->trans_commit();
        }

        return class_general::_generarRespuestaModelo($conexion, $estadotran, $respuesta);
    }
    
    public function getCajasMedio($cod_medio, $cod_compra, $cod_usuario){
        $conexion = $this->load->database($this->codigo_filial,true);
       
          $usuarios = new Vusuarios_sistema($conexion, $cod_usuario);
            
          $cajas = $usuarios->getCajasMedio($cod_medio);
        
      
        return $cajas;
    }

}

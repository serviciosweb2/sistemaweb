<?php

/**
 * Model_notas_credito
 * 
 * Description...
 * 
 * @package model_notas_credito
 * @author vane
 * @version 1.0.0
 */
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Model_notas_credito extends CI_Model {

    var $codigo_filial = 0;

    public function __construct($arg) {
        parent::__construct();

        $this->codigo_filial = $arg["codigo_filial"];
    }

    public function listarNotasCreditoDataTable($arrFiltros, $separador, $separadorDecimal) {
        $conexion = $this->load->database($this->codigo_filial, true);
        $this->load->helper('filial');
        $this->load->helper('alumnos');
        $this->load->helper('formatearfecha');
        $arrCondiciones = array();

        if ($arrFiltros["sSearch"] != "") {
            $arrCondiciones = array(
                "notas_credito.codigo" => $arrFiltros["sSearch"],
                "nombre_apellido" => $arrFiltros["sSearch"],
                "notas_credito.importe" => $arrFiltros["sSearch"]
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
        $order = array();
        if ($arrFiltros["SortCol"] != '' and $arrFiltros["sSortDir"] != '') {

            $arrSort = array(
                "0" => $arrFiltros["SortCol"],
                "1" => $arrFiltros["sSortDir"]
            );
        } else {

            $order[] = array(
                0 => 'codigo',
                1 => 'desc'
            );
        }
        $datos = Vnotas_credito::listarNotasCreditoDataTable($conexion, $arrCondiciones, $arrLimit, $arrSort, '', $order, $separador, $separadorDecimal);
        $contar = Vnotas_credito::listarNotasCreditoDataTable($conexion, $arrCondiciones, '', '', TRUE, '', $separador, $separadorDecimal);

        $retorno = array(
            "sEcho" => $arrFiltros["sEcho"],
            "iTotalRecords" => $contar,
            "iTotalDisplayRecords" => $contar,
            "aaData" => array()
        );

        $rows = array();
        foreach ($datos as $row) {
            $rows[] = array(
                $row["codigo"],
                inicialesMayusculas($row["nombre_apellido"]),
                formatearImporte($row["importe"]),
                formatearImporte($row['saldoRestante']),
                $row['fechareal'] = $row['fechareal'] == '0000-00-00' ? '-' : formatearFecha_pais($row['fechareal']),
                $row["estado"],
                $row["baja"] = ''
            );
        }

        $retorno['aaData'] = $rows;

        return $retorno;
    }

    public function getImputaciones($codigo, $formato = true) {
        $conexion = $this->load->database($this->codigo_filial, true);
        $this->load->helper('cuentacorriente');
        $this->load->helper('filial');

        $objNC = new Vnotas_credito($conexion, $codigo);
        $imputaciones = $objNC->getImputaciones();

        if ($formato) {
            $arrRetornoImputaciones = '';
            foreach ($imputaciones as $valor) {
                $condicion = array(
                    'codigo' => $valor['cod_ctacte']
                );

                $ctactes = Vctacte::getCtaCte($conexion, null, $condicion);
                formatearCtaCte($conexion, $ctactes);
                $fecha = formatearFecha_pais(substr($valor['fecha'], 0, 10));
                $hora = substr($valor['fecha'], 10);

                $arrRetornoImputaciones[] = array(
                    'codigo' => $valor['codigo'],
                    'descripcion' => $ctactes[0]['descripcion'],
                    'valorImputacion' => formatearImporte($valor['valor']),
                    'vencimiento' => $ctactes[0]['fechavenc'],
                    'fecha_imputacion' => $fecha,
                    'cod_ctacte' => $valor['cod_ctacte'],
                    'estado' => lang($valor['estado'])
                );
            }

            return $arrRetornoImputaciones;
        } else {
            return $imputaciones;
        }
    }

    public function getNotaCredito($codigo) {
        $conexion = $this->load->database($this->codigo_filial, true);
        $nc = new Vnotas_credito($conexion, $codigo);
        return $nc;
    }

    public function guardaNC($datos) {
        $conexion = $this->load->database($this->codigo_filial, true);
        $conexion->trans_start();

        $objnc = new Vnotas_credito($conexion, $datos['codigo']);
        $fechacreacion = date("Y-m-d H:i:s");

        $cod_alumno = $datos['cod_alumno'];

        if ($objnc->getCodigo() == '-1') {//NUEVO
        } else {//MODIFICAR
            $fechacreacion = $objnc->fechaalta;
            $cod_alumno = $objnc->cod_alumno;
        }
        $total = 0;
        foreach ($datos['facturas'] as $rowfactura) {
            $total = $total + $rowfactura['importe'];
        }
        $objnc->cod_alumno = $cod_alumno;
        $objnc->cod_usuario = $datos['cod_usuario'];
        $objnc->estado = Vnotas_credito::getEstadoPendiente();
        $objnc->fechareal = $datos['fecha'];
        $objnc->fechaalta = $fechacreacion;
        $objnc->importe = $total;
        $objnc->motivo = $datos['motivo'];
        $objnc->guardarNotas_credito();

        $objnc->confirmar($datos['cod_usuario']); //ver configuracion si no confirma automaticamnte

        foreach ($datos['facturas'] as $rowfactura) {
            $objnc->guardarRenglon($rowfactura['factura'], $rowfactura['importe']);
        }

        $estadotran = $conexion->trans_status();

        $arrRespuesta = $objnc->getCodigo();
        $conexion->trans_complete();
        return class_general::_generarRespuestaModelo($conexion, $estadotran, $arrRespuesta);
    }

    public function getRestaImputar($cod_nc) {
        $conexion = $this->load->database($this->codigo_filial, true);
        $this->load->helper('filial');
        $objNC = new Vnotas_credito($conexion, $cod_nc);

        $total_imputaciones = $objNC->getSumValorImputaciones();

        $resto = formatearImporte($objNC->importe - $total_imputaciones[0]['totImputaciones']);
        return $resto;
    }

    public function getCtaCteImputar($cod_nc) {
        $conexion = $this->load->database($this->codigo_filial, true);
        $this->load->helper('cuentacorriente');

        $arrCondicion = array(
            'habilitado >' => '0',
            'habilitado <' => '3'
        );
        $nc = new Vnotas_credito($conexion, $cod_nc);
        $alumno = new Valumnos($conexion, $nc->cod_alumno);
        $ctaCteAlumno = $alumno->getCtaCteCobrar($arrCondicion);

        formatearCtaCte($conexion, $ctaCteAlumno);

        $CtaCteorder = Vctacte::ordenarCtaCte($ctaCteAlumno);

        return $CtaCteorder;
    }

    public function guardarImputaciones($datos) {
        $conexion = $this->load->database($this->codigo_filial, true);
        $conexion->trans_start();
        $respuesta = '';
        $this->load->helper('cuentacorriente');

        $objnc = new Vnotas_credito($conexion, $datos['cod_nc']);
        $total = $objnc->getSumValorImputaciones();
        $totalimputado = count($total) > 0 ? $total[0]['totImputaciones'] : 0;

        $rowsCtaCte = array();
        $codctacte = array();
        //Cargo un array con codig de ctacte para que me traiga el get......
        foreach ($datos['checkctacte'] as $value) {
            $codctacte[] = $value;
        }
        if (count($codctacte) != 0) {
            $rowsCtaCte = Vctacte::getCtaCteCobrar($conexion, '', '', $codctacte);
        }
        $cuentas = array();
        foreach ($rowsCtaCte as $value) {
            foreach ($codctacte as $key => $cta) {
                if ($cta == $value['codigo']) {
                    $cuentas[$key] = $value;
                }
            }
        }

        $totalImputar = $objnc->importe - $totalimputado;
        $resta = $totalImputar;
        if (count($cuentas) > 0) {
            ksort($cuentas);
        }

        foreach ($cuentas as $value) {
            $saldoCtacte = $value['saldocobrar'];
            $resta = $resta - $saldoCtacte;
            $importeRenglonImputacion = ($resta > 0) ? $saldoCtacte : $totalImputar;
            $totalImputar = $totalImputar - $importeRenglonImputacion;
            if ($importeRenglonImputacion > 0) {
                $cod_imp = $objnc->imputar($value['codigo'], $importeRenglonImputacion, $datos['cod_usuario']);
                $imputacion = new Vctacte_imputaciones($conexion, $cod_imp);
                $imputacion->confirmar($datos['cod_usuario']);
            }
        }

        $estadotran = $conexion->trans_status();

        $conexion->trans_complete();
        return class_general::_generarRespuestaModelo($conexion, $estadotran, $respuesta);
    }

    public function getTotalImputaciones($cod_nc) {
        $conexion = $this->load->database($this->codigo_filial, true);
        $this->load->helper('filial');
        $objnc = new Vnotas_credito($conexion, $cod_nc);

        $total_imputaciones = $objnc->getSumValorImputaciones();
        $total_imputaciones[0]['totImputaciones'] = formatearImporte($total_imputaciones[0]['totImputaciones']);
        return $total_imputaciones;
    }

    public function getRenglonesNC($cod_nc) {
        $conexion = $this->load->database($this->codigo_filial, true);
        $this->load->helper('filial');
        $objnc = new Vnotas_credito($conexion, $cod_nc);
        $renglones = $objnc->getRenglones();
        $respuesta = array();
        $i = 0;
        foreach ($renglones as $key => $rowfactura) {
            $respuesta[$key]['descripcion'] = $rowfactura['tipo'] . ' ' . $rowfactura['numero'];
            $respuesta[$key]['importe'] = formatearImporte($rowfactura['importe'], TRUE);
        }

        return $respuesta;
    }

    public function getNombreAlumno($codigo) {
        $conexion = $this->load->database($this->codigo_filial, true);
        $this->load->helper('alumnos');
        $objNC = new Vnotas_credito($conexion, $codigo);
        $objAlumno = new Valumnos($conexion, $objNC->cod_alumno);

        $nombreApellido = formatearNombreApellido($objAlumno->nombre, $objAlumno->apellido);
        $nombreFormateado = inicialesMayusculas($nombreApellido);
        return $nombreFormateado;
    }

    public function getMotivosBaja() {
        $conexion = $this->load->database($this->codigo_filial, true);
        $nchistorico = new Vnotas_credito_historico($conexion);
        return $nchistorico->getmotivos();
    }

    public function confirmarNC($datos) {
        $conexion = $this->load->database($this->codigo_filial, true);
        $objnc = new Vnotas_credito($conexion, $datos['cod_nc']);
        return $objnc->confirmar($datos['cod_usuario']);
    }

    public function anularNC($datos) {
        $conexion = $this->load->database($this->codigo_filial, TRUE);
        $conexion->trans_begin();

        $objnc = new Vnotas_credito($conexion, $datos['cod_nc']);
        if ($objnc->estado == Vnotas_credito::getEstadoConfirmado() || $objnc->estado == Vnotas_credito::getEstadoPendiente() || $objnc->estado == Vnotas_credito::getEstadoError()) {
            $respuesta = $objnc->anular($datos['motivo'], $datos['comentario'], $datos['cod_usuario'], true);
        }
        //ver los otros estados

        $estadoTran = $conexion->trans_status();
        if ($estadoTran === false) {
            $conexion->trans_rollback();
        } else {
            $conexion->trans_commit();
        }

        return class_general::_generarRespuestaModelo($conexion, $estadoTran, $respuesta);
    }

    public function getHistorico($codigo) {
        $conexion = $this->load->database($this->codigo_filial, true);
        $respuesta = array();
        $condicion = array('cod_nc' => $codigo);
        $orden = array(array('campo' => 'codigo', 'orden' => 'desc'));
        $historico = Vnotas_credito_historico::listarNotas_credito_historico($conexion, $condicion, null, $orden);
        foreach ($historico as $value) {
            if ($value['cod_usuario'] != null) {
                $usuario = new Vusuarios_sistema($conexion, $value['cod_usuario']);
                $nombre = $usuario->nombre . ' ' . $usuario->apellido;
            } else {
                $nombre = '-';
            }

            $respuesta[] = array('estado' => lang($value['estado']), 'fecha' => formatearFecha_pais($value['fecha_hora'], true), 'usuario' => $nombre);
        }

        return $respuesta;
    }

    public function getArrNotaCredito($codigo) {
        $conexion = $this->load->database($this->codigo_filial, true);
        $condicion = array('codigo' => $codigo);
        $notas = Vnotas_credito::listarNotas_credito($conexion, $condicion);
        $motivo = Vnotas_credito::getMotivos(array($notas[0]['motivo']));
        $notas[0]['motivo'] = $motivo[0]['motivo'];
        return $notas[0];
    }

    public function getMotivos() {
        $conexion = $this->load->database($this->codigo_filial, true);
        return Vnotas_credito::getMotivos();
    }

}

<?php

class Model_ctacte extends CI_Model {

    var $codigo = 0;
    var $codigo_filial = 0;

    public function __construct($arg) {
        parent::__construct();
        $this->codigo = isset($arg["codigo"]) ? $arg["codigo"] : 0;
        $this->codigo_filial = $arg["codigo_filial"];
    }

    /**
     * recupera imputaciones de una cuenta corrientes
     * @access public
     * @return array $imputaciones
     */
    public function getImputaciones($codigo_ctacte, $filial = null, $soloimpconfirmadas = true) {
        if ($filial == null) {
            $conexion = $this->load->database($this->codigo_filial, true);
        } else {
            $conexion = $this->load->database($filial, true);
        }
        $this->load->helper('filial');
        $this->load->helper('formatearfecha');
        $objImputaciones = new Vctacte($conexion, $codigo_ctacte);
        $imputaciones = array();
        $imputacionesconfirmadas = $objImputaciones->getImputacionesCtaCte('confirmado');
        for ($i = 0; $i < count($imputacionesconfirmadas); $i++) {
            if ($imputacionesconfirmadas[$i]['tipo'] == 'COBRO') {
                $cobro = new Vcobros($conexion, $imputacionesconfirmadas[$i]['cod_cobro']);
                $fechareal = $cobro->fechareal;
                $medio = new Vmedios_pago($conexion, $cobro->medio_pago);
                $medio_pago = $medio->medio;
            } elseif ($imputacionesconfirmadas[$i]['tipo'] == 'NOTA_CREDITO') {
                $notacredito = new Vnotas_credito($conexion, $imputacionesconfirmadas[$i]['cod_cobro']);
                $fechareal = $notacredito->fechareal;
                $medio_pago = $imputacionesconfirmadas[$i]['tipo'];
            }
            $imputaciones[$i]['importeformateado'] = formatearImporte($imputacionesconfirmadas[$i]['valor'], true, $conexion, $filial);
            $imputaciones[$i]['fechareal'] = formatearFecha_pais($fechareal, '', $filial);
            $imputaciones[$i]['medio'] = lang($medio_pago);
            $imputaciones[$i]['estado'] = lang($imputacionesconfirmadas[$i]['estadoImputacion']);
        }
        if (!$soloimpconfirmadas) {
            $imputacionespendientes = $objImputaciones->getImputacionesCtaCte('pendiente');
            foreach ($imputacionespendientes as $value) {
                if ($value['tipo'] == 'COBRO') {
                    $cobro = new Vcobros($conexion, $value['cod_cobro']);
                    $fechareal = $cobro->fechareal;
                    $medio = new Vmedios_pago($conexion, $cobro->medio_pago);
                    $medio_pago = $medio->medio;
                } elseif ($value['tipo'] == 'NOTA_CREDITO') {
                    $notacredito = new Vnotas_credito($conexion, $value['cod_cobro']);
                    $fechareal = $notacredito->fechareal;
                    $medio_pago = $value['tipo'];
                }
                $imputaciones[$i]['importeformateado'] = formatearImporte($value['valor'], true, $conexion, $filial);
                $imputaciones[$i]['fechareal'] = formatearFecha_pais($fechareal, '', $filial);
                $imputaciones[$i]['medio'] = lang($medio_pago);
                $imputaciones[$i]['estado'] = lang($value['estadoImputacion']);
                $i++;
            }
        }
        $facturasCta = $objImputaciones->getFacturas();
        foreach ($facturasCta as $fac => $facturaCta) {
            $medio = $facturaCta['ptovta_medio'] == 'electronico' ? 'e' : '';
            $facturasCta[$fac]['importeformateado'] = formatearImporte($facturaCta['importe'], true, $conexion, $filial);
            $facturasCta[$fac]['fecha'] = formatearFecha_pais($facturaCta['fecha'], '', $filial);
            $facturasCta[$fac]['tipo_numero'] = $facturaCta['factura'] . $medio . ' ' . $facturaCta['nrofact'];
        }
        $arrayDatos['imputaciones'] = $imputaciones;
        $arrayDatos['facturas'] = $facturasCta;
        return $arrayDatos;
    }

    public function getFacturas($cod_ctacte) {
        $conexion = $this->load->database($this->codigo_filial, true);
        $this->load->helper('filial');
        $objFactura = new Vctacte($conexion, $cod_ctacte);
        $facturasCta = $objFactura->getFacturas();
        foreach ($facturasCta as $key => $facturaCta) {
            $facturasCta[$key]['importeformateado'] = formatearImporte($facturaCta['importe']);
        }
        return $facturasCta;
    }

    /**
     * recupera moras de una cuenta corrientes
     * @access public
     * @return array $moras
     */
    public function getMoras($codigo_ctacte) {
        $conexion = $this->load->database($this->codigo_filial, true);
        $arrMoras = Vctacte::getMorasCtaCte($conexion, $codigo_ctacte);
        return $arrMoras;
    }

    public function getCtaCteSinFacturar($arrFiltros, $separador) {
        $conexion = $this->load->database($this->codigo_filial, true);
        $this->load->helper('alumnos');
        $arrCondiciones = array();
        $arrCondiciones['habilitado <'] = 3;
        $arrCondiciones['habilitado >'] = 0;
        $arrLike = array();
        if ($arrFiltros["sSearch"] != "") {
            $arrLike = array(
                "nombre_apellido" => $arrFiltros["sSearch"],
            );
        }
        $arrLimit = array();
        if ($arrFiltros["iDisplayStart"] != "" and $arrFiltros["iDisplayLength"] != "") {
            $arrLimit = array(
                "0" => $arrFiltros["iDisplayStart"],
                "1" => $arrFiltros["iDisplayLength"]
            );
        }
        $arrSort = null;
        if ($arrFiltros["SortCol"] != "" and $arrFiltros["sSortDir"] != "") {
            $arrSort = array(
                'campo' => $arrFiltros["SortCol"],
                'orden' => $arrFiltros["sSortDir"]
            );
        }
        $arrBetween = array();
        if ($arrFiltros["FechaIni"] != "" and $arrFiltros["FechaFin"] != "") {
            $arrBetween = array(
                'fechaini' => formatearFecha_mysql($arrFiltros["FechaIni"]),
                'fechafin' => formatearFecha_mysql($arrFiltros["FechaFin"])
            );
        }
        $medioCobro = isset($arrFiltros['medio_pago']) && $arrFiltros['medio_pago'] <> -1 && $arrFiltros['medio_pago'] <> ''
                ? $arrFiltros['medio_pago'] : null;
        if ($arrFiltros['tipoFactura'] != '') {
            $tipoFactura = $arrFiltros['tipoFactura'];
            $facturante = $arrFiltros["facturante"];
            $mostrarCobradas_nofacturadas = $arrFiltros['cobradas_nofacturadas'];
            $ctacte = Vctacte::getCtaCteSinFacturar($conexion, null, $arrCondiciones, $arrSort, $arrLimit, $arrLike, $arrBetween, false, null, $tipoFactura, $separador, $facturante, $mostrarCobradas_nofacturadas, false, $medioCobro);
            $contar = Vctacte::getCtaCteSinFacturar($conexion, null, $arrCondiciones, null, null, $arrLike, $arrBetween, true, null, $tipoFactura, $separador, $facturante, $mostrarCobradas_nofacturadas, false, $medioCobro);
        } else {
            $contar = 0;
            $ctacte = array();
        }
        $this->load->helper('cuentacorriente');
        formatearCtaCte($conexion, $ctacte);
        $ctaCteOrder = Vctacte::ordenarCtaCte($ctacte);
        $retorno = array(
            "sEcho" => $arrFiltros["sEcho"],
            "iTotalRecords" => $contar,
            "iTotalDisplayRecords" => $contar,
            "aaData" => array()
        );

        $rows = array();
        foreach ($ctaCteOrder as $row) {
            $rows[] = array(
                '',
                $row["codigo"],
                $row["codigo"],
                str_replace(",", ", ", $row['cod_cobro']),
                $row["nombre_apellido"],
                $row["descripcion"],
                $row["fechavenc"],
                $row["importe"],
                lang($row["medio_pago"]),
                $row["saldofacturar"],
                $row["razonsocial"]                
            );
        }
        $retorno['aaData'] = $rows;
        return $retorno;
    }

    public function getCtacteRematriculacionDatatable($desde, $hasta, $matriculasEmitir, $arrFiltros, $separador, array $arrCtacte = null, $fecha_desde = null, $fecha_hasta = null,
            $codAlumno = null, $codMatricula = null, $soloIds = false)
    {
        $conexion = $this->load->database($this->codigo_filial, true, true);
        $this->load->helper('alumnos');
        $arrCondiciones = array();
        if ($arrFiltros["sSearch"] != "") {
            $arrCondiciones = array(
                "nombre_apellido" => $arrFiltros["sSearch"],
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
                'campo' => $arrFiltros["SortCol"],
                'orden' => $arrFiltros["sSortDir"]
            );
        }
        $ctacte = Vctacte::getCtaCteRematriculaciones($conexion, $desde, $hasta, $matriculasEmitir, $codAlumno, null, $arrCtacte, $arrCondiciones, $arrLimit, false, $arrSort, true, null, false, $separador, $fecha_desde, $fecha_hasta, $codMatricula);
        if($soloIds){
            $rows = array();
            foreach ($ctacte as $row) {
                $rows[] = $row["codigo"];
            }
            return $rows;
        }
        $contar = Vctacte::getCtaCteRematriculaciones($conexion, $desde, $hasta, $matriculasEmitir, $codAlumno, null, $arrCtacte, $arrCondiciones, null, true, $arrSort, true, null, false, $separador, $fecha_desde, $fecha_hasta, $codMatricula);
        $this->load->helper('cuentacorriente');
        formatearCtaCte($conexion, $ctacte);
        $retorno = array(
            "sEcho" => $arrFiltros["sEcho"],
            "iTotalRecords" => $contar,
            "iTotalDisplayRecords" => $contar,
            "aaData" => array()
        );
        $rows = array();
        foreach ($ctacte as $row) {
            $rows[] = array(
                $row["codigo"],
                $row['nombre_apellido'],
                $row["descripcion"],
                $row['fechavenc'],
                formatearImporte($row["importe"]),
            );
        }
        $retorno['aaData'] = $rows;
        return $retorno;
    }

    public function getCtacteSinCobrarDatatable($arrFiltros, $separador, array $arrCtacte = null, $fecha_desde = null, $fecha_hasta = null,
            $codAlumno = null, $codMatricula = null) {
        $conexion = $this->load->database($this->codigo_filial, true, true);
        $this->load->helper('alumnos');
        $arrCondiciones = array();
        if ($arrFiltros["sSearch"] != "") {
            $arrCondiciones = array(
                "nombre_apellido" => $arrFiltros["sSearch"],
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
                'campo' => $arrFiltros["SortCol"],
                'orden' => $arrFiltros["sSortDir"]
            );
        }

        $ctacte = Vctacte::getCtaCteImputar($conexion, $codAlumno, null, $arrCtacte, $arrCondiciones, $arrLimit, false, $arrSort, true, null, false, $separador, $fecha_desde, $fecha_hasta, $codMatricula);
        $contar = Vctacte::getCtaCteImputar($conexion, $codAlumno, null, $arrCtacte, $arrCondiciones, null, true, $arrSort, true, null, false, $separador, $fecha_desde, $fecha_hasta, $codMatricula);
        $this->load->helper('cuentacorriente');
        formatearCtaCte($conexion, $ctacte);
        $retorno = array(
            "sEcho" => $arrFiltros["sEcho"],
            "iTotalRecords" => $contar,
            "iTotalDisplayRecords" => $contar,
            "aaData" => array()
        );
        $rows = array();
        foreach ($ctacte as $row) {
            $rows[] = array(
                $row["codigo"],
                $row['nombre_apellido'],
                $row["descripcion"],
                $row['fechavenc'],
                formatearImporte($row["importe"]),
            );
        }
        $retorno['aaData'] = $rows;
        return $retorno;
    }

    public function getComentarios($codctacte) {
        $conexion = $this->load->database($this->codigo_filial, true);
        $ctacte = new Vctacte($conexion, $codctacte);
        return $ctacte->getComentarios();
    }

    public function guardarComentario($comentario) {
        $conexion = $this->load->database($this->codigo_filial, true);
        $datoscomentario = array(
            'codigo' => $comentario['codigo'],
            'fecha_hora' => date("Y-m-d H:i:s"),
            'comentario' => $comentario['comentario'],
            'id_usuario' => $comentario['cod_usuario']
        );
        $ctacte = new Vctacte($conexion, $comentario['cod_ctacte']);
        $estadotran = $ctacte->guardarComentario($datoscomentario);
        $arrRespuesta = class_general::_generarRespuestaModelo($conexion, $estadotran['estado']);
        if ($estadotran['estado'] == true) {
            $arrRespuesta['obj'] = $estadotran['obj'];
        }
        return $arrRespuesta;
    }

    public function bajaComentario($arrCodigos) {
        $conexion = $this->load->database($arrCodigos['filial']['codigo'], true);
        $comentario = new Vctacte_comentarios($conexion, $arrCodigos['codigo']);
        $comentario->baja = 1;
        $estadotran = $comentario->guardarCtacte_comentarios();
        return class_general::_generarRespuestaModelo($conexion, $estadotran);
    }

    /* La siguiente function está siendo accedida desde un Web Services NO MODIFICAR, COMENTAR NI ELIMINAR */
    public function getEstadisticasCobro($idFilial, $fechaDesde = null, $fechaHasta = null, $idCurso = null, $idConcepto = null, $estado = null) {
        $conexion = $this->load->database($idFilial, true);
        $arrTotales = Vctacte::getReporteCobros($conexion, null, null, false, null, null, $fechaDesde, $fechaHasta, $idCurso, $idConcepto, false, null, $estado);
        $totalCobrado = 0;
        $totalAcobrar = 0;
        foreach ($arrTotales as $total) {
            $totalCobrado += $total['pagado'];
            $totalAcobrar += $total['importe'];
        }
        $arrResp = array();
        $arrResp['total_cobrado'] = $totalCobrado;
        $arrResp['total_a_cobrar'] = $totalAcobrar;
        return $arrResp;
    }

    /* La siguiente function está siendo accedida desde un Web Services NO MODIFICAR, COMENTAR NI ELIMINAR */
    public function getReporteCobros($idFilial, $arrLimit = null, $arrSort = null, $search = null, $searchFields = null, 
            $fechaDesde = null, $fechaHasta = null, $idCurso = null, $idConcepto = null, $soloConDeuda = null, array $estado = null) {
        $conexion = $this->load->database($idFilial, true);
        $this->load->helper("cuentacorriente");
        $cantRegistros = Vctacte::getReporteCobros($conexion, $arrLimit, $arrSort, true, $search, $searchFields, $fechaDesde, $fechaHasta, $idCurso, $idConcepto, $soloConDeuda, null, $estado);
        $registros = Vctacte::getReporteCobros($conexion, $arrLimit, $arrSort, false, $search, $searchFields, $fechaDesde, $fechaHasta, $idCurso, $idConcepto, $soloConDeuda, null, $estado);
        for ($i = 0; $i < count($registros); $i++) {
            $registros[$i]["concepto_nombre"] = lang(Vconceptos::getKey($conexion, $registros[$i]['cod_concepto']));
        }
        $arrResp = array();
        $arrResp['total_rows'] = $cantRegistros;
        $arrResp['rows'] = $registros;
        return $arrResp;
    }

    public function getDetalleRefinanciacion($datos) {
        $conexion = $this->load->database($this->codigo_filial, true);
        $afinanciar = 0;
        $afinanciar = $datos['valor_refinanciar'];
        $valorcuotas = round($afinanciar / $datos['cuotas'], $datos['detalle']['decimales']);
        if (isset($datos['interesporc']) && $datos['interesporc'] != FALSE && $datos['interesporc'] != 0) {
            $valorcuotas = $valorcuotas + $valorcuotas * $datos['interesporc'] / 100;
        }
        $detalle = array();
        $fechapago = $datos['fechapago'];
        $vencimiento = $fechapago;
        $suma = 0;
        for ($i = 0; $i < $datos['cuotas']; $i++) {
            $detalle['cuotas'][$i]['nrocuota'] = $i + 1;
            $detalle['cuotas'][$i]['valor'] = formatearImporte($valorcuotas);
            $detalle['cuotas'][$i]['valor_real'] = $valorcuotas;
            $venciminetotest = strtotime($vencimiento);
            $vencimiento = $detalle['cuotas'][$i]['nrocuota'] == 1 ? $fechapago : date("Y-m-d", strtotime('+' . $datos['detalle']['perioricidad']['valor'] . ' ' . $datos['detalle']['perioricidad']['unidadTiempo'], $venciminetotest));
            $vencimientoValido = getPrimerFechaHabil($conexion, $vencimiento);
            $detalle['cuotas'][$i]['fecha'] = formatearFecha_pais($vencimientoValido);
            $detalle['cuotas'][$i]['fecha_real'] = $vencimientoValido;
            $suma = $suma + $valorcuotas;
        }
        $detalle['total'] = $suma;
        return $detalle;
    }

    public function getSumaRefinanciar($arrCtaCte) {
        $conexion = $this->load->database($this->codigo_filial, true);
        $ctacte = Vctacte::getSumaCtaCte($conexion, $arrCtaCte);
        return $ctacte[0];
    }

    public function guardarRefinanciacion($datos) {
        $conexion = $this->load->database($this->codigo_filial, true);
        $conexion->trans_begin();
        $datosguardar = $this->getDetalleRefinanciacion($datos['detalle']);
        $ultFinancia = 0;
        foreach ($datos['detalle']['ctactes'] as $ctacte) {
            $objCtacte = new Vctacte($conexion, $ctacte);
            $objCtacte->bajaRefinanciacion(5, null, $datos['cod_usuario']);
            $ultFinancia = $objCtacte->financiacion > $ultFinancia ? $objCtacte->financiacion : $ultFinancia;
        }
        foreach ($datosguardar['cuotas'] as $rowCuota) {
            $ctacte = new Vctacte($conexion);
            $ctacte->guardar($datos['alumno'], $rowCuota['nrocuota'], $rowCuota['valor_real'], $rowCuota['fecha_real'], null, null, $datos['codconcepto'], $datos['concepto'], $ultFinancia + 1);
        }
        $parametroMora = array('cod_alumno' => $datos['alumno']);
        $objtarecron = new Vtareas_crons($conexion);
        $objtarecron->guardar('calcular_mora', $parametroMora, $this->codigo_filial);
        $estadotran = $conexion->trans_status();
        if ($estadotran === FALSE) {
            $conexion->trans_rollback();
        } else {
            $conexion->trans_commit();
        }
        return class_general::_generarRespuestaModelo($conexion, $estadotran);
    }

    public function getDeudoresCtaCte($wherein, $DiasAlertasConfiguracion, $separador) {
        $conexion = $this->load->database($this->codigo_filial, true);
        $this->load->helper('cuentacorriente');
        $this->load->helper('filial');
        $this->load->helper('alumnos');
        $this->load->helper('formatearfecha');
        $deudoresCtacte = Vctacte::getDeudoresCtacte($conexion, false, null, $wherein, null, null, null, false, $separador);
        foreach ($deudoresCtacte as $key => $deudorCtaCte) {
            $deudoresCtacte[$key]['alertar'] = $deudorCtaCte['CantidadAlertado'] >= $DiasAlertasConfiguracion ? 0 : 1;
        }
        formatearCtaCte($conexion, $deudoresCtacte);
        return $deudoresCtacte;
    }

    public function deudoresAgrupadosCtaCte($arrFiltros, $DiasAlertasConfiguracion, $separador) {
        $conexion = $this->load->database($this->codigo_filial, true);
        $agruparDeudas = 1;
        $this->load->helper('filial');
        $arrCondiciones = array();
        $this->load->helper('alumnos');
        if ($arrFiltros["sSearch"] != "") {
            $arrCondiciones = array(
                "nombre_apellido" => $arrFiltros["sSearch"],
                "deudaTotal" => $arrFiltros["sSearch"]
            );
        }
        $arrLimit = array();
        if ($arrFiltros["iDisplayStart"] != "" && $arrFiltros["iDisplayLength"] != "") {
            $arrLimit = array(
                "0" => $arrFiltros["iDisplayStart"],
                "1" => $arrFiltros["iDisplayLength"]
            );
        }

        $arrSort = array();
        if ($arrFiltros["SortCol"] != "" && $arrFiltros["sSortDir"] != "") {
            $arrSort = array(
                "0" => $arrFiltros["SortCol"],
                "1" => $arrFiltros["sSortDir"]
            );
        }
        $deudores = Vctacte::getDeudoresCtacte($conexion, $agruparDeudas, null, null, $arrCondiciones, $arrLimit, $arrSort, false, $separador);
        $contar = Vctacte::getDeudoresCtacte($conexion, $agruparDeudas, null, null, '', '', '', true, $separador);
        $rows = array();
        foreach ($deudores as $deudor) {
            $condiciones = array();
            $alertar = 0;
            $ctacteAlumnos = Vctacte::getDeudoresCtacte($conexion, null, $deudor['cod_alumno'], null, $condiciones, null, null, false, $separador);
            foreach ($ctacteAlumnos as $ctacteAlumno) {
                if ($ctacteAlumno['CantidadAlertado'] < $DiasAlertasConfiguracion) {
                    $alertar = 1;
                }
            }
            $nombre = $deudor['tienemail'] == '1' ? inicialesMayusculas($deudor['nombre_apellido']) : inicialesMayusculas($deudor['nombre_apellido']) . ' -' . lang('no_tiene_mail') . '-';
            $rows[] = array(
                $deudor['seleccionar_ctacte'] = '',
                $deudor['codigo'],
                $deudor['cod_alumno'],
                $nombre,
                formatearImporte($deudor['deudaTotal']),
                formatearFecha_pais($deudor['fechavenc']),
                $alertar,
                $deudor['detalle'] = '',
                $deudor['tienemail']
            );
        }
        $retorno = array(
            "sEcho" => $arrFiltros["sEcho"],
            "iTotalRecords" => $contar,
            "iTotalDisplayRecords" => $contar,
            "aaData" => array()
        );
        $retorno['aaData'] = $rows;
        return $retorno;
    }

    public function guardarAlertasDeudores($arrayCtactes, $usuario) {
        $conexion = $this->load->database($this->codigo_filial, true);
        $this->load->helper("cuentacorriente");
        $datos = '';
        $myTemplate = new Vtemplates($conexion, 61);
        $html = $myTemplate->html;
        $guardarAlerta = array(
            'tipo_alerta' => 'deuda_ctacte',
            'fecha_hora' => date("Y-m-d H:i:s"),
            'mensaje' => $html
        );
        $objAlerta = new Valertas($conexion);
        $objAlerta->setAlertas($guardarAlerta);
        $objAlerta->guardarAlertas();
        $asunto = lang('recordatorio_cuotas_impagas');
        $key = 'titulo';
        $objAlerta->setAlertaConfiguracion($key, $asunto);
        $key1 = 'cod_usuario_creador';
        $objAlerta->setAlertaConfiguracion($key1, $usuario);
        $cod_alumno = '';
        foreach ($arrayCtactes as $arrayCtacte) {
            $datos = json_decode($arrayCtacte, true);
            $cod_ctacte = $datos['codigo'];
            $cod_alumno = $datos['cod_alumno'];
            $arrayAluConfiguracion = array(
                'cod_alerta' => $objAlerta->getCodigo(),
                'cod_alumno' => $cod_alumno,
                'key' => 'cod_ctacte',
                'valor' => $cod_ctacte
            );
            $objAlerta->setAlertaAlumnoConfiguracion($arrayAluConfiguracion);
        }
        $objAlerta->setAlertaAlumno($cod_alumno);
        $estadotran = $conexion->trans_status();
        if ($estadotran === FALSE) {
            $conexion->trans_rollback();
        } else {
            $conexion->trans_commit();
        }
        return class_general::_generarRespuestaModelo($conexion, $estadotran);
    }

    public function guardarAlertasDeudoresGeneral($diasAlertaConfigurcion, $arrayCtactes, $usuario, $separador) {
        $conexion = $this->load->database($this->codigo_filial, true);
        $this->load->helper('cuentacorriente');
        $this->load->helper('alumnos');
        foreach ($arrayCtactes as $arrayCtaCte) {
            $datos = json_decode($arrayCtaCte, true);
            $ctacteDeudores = Vctacte::getDeudoresCtacte($conexion, null, null, $datos['2'], null, null, null, null, false, $separador);
            $cod_alumno = $datos['2'];
            $myTemplate = new Vtemplates($conexion, 61);
            $html = $myTemplate->html;
            $guardarAlerta = array(
                'tipo_alerta' => 'deuda_ctacte',
                'fecha_hora' => date("Y-m-d H:i:s"),
                'mensaje' => $html
            );
            $objAlerta = new Valertas($conexion);
            $objAlerta->setAlertas($guardarAlerta);
            $objAlerta->guardarAlertas();
            $objAlerta->setAlertaAlumno($cod_alumno);
            $asunto = lang('recordatorio_cuotas_impagas');
            $key = 'titulo';
            $objAlerta->setAlertaConfiguracion($key, $asunto);
            $key1 = 'cod_usuario_creador';
            $objAlerta->setAlertaConfiguracion($key1, $usuario);
            foreach ($ctacteDeudores as $cod_ctacte) {
                if ($cod_ctacte['CantidadAlertado'] < $diasAlertaConfigurcion) {
                    $arrayAluConfiguracion = array(
                        'cod_alerta' => $objAlerta->getCodigo(),
                        'cod_alumno' => $cod_alumno,
                        'key' => 'cod_ctacte',
                        'valor' => $cod_ctacte['codigo']
                    );
                    $objAlerta->setAlertaAlumnoConfiguracion($arrayAluConfiguracion);
                }
            }
        }
        $estadotran = $conexion->trans_status();
        if ($estadotran === FALSE) {
            $conexion->trans_rollback();
        } else {
            $conexion->trans_commit();
        }
        return class_general::_generarRespuestaModelo($conexion, $estadotran);
    }

    function actualizarDescuentoCondicionado() {
        $conexion = $this->load->database("default", true);
        $arrFiliales = Vfiliales::listarFiliales($conexion, array("version_sistema" => 2));
        foreach ($arrFiliales as $filial) {
            $arrUsuarios = Vusuarios_sistema::listarUsuarios_sistema($conexion, array("cod_filial" => $filial['codigo']));
            $conexion = $this->load->database($filial['codigo'], true);
            $arrDescuentosVencidos = Vctacte::getMatriculacionesCtacte($conexion, "condicionado", null, null, false, true, true);
            if (count($arrDescuentosVencidos) > 0) {
                $conexion->trans_begin();
                foreach ($arrDescuentosVencidos as $descuentoVencido) {
                    $codigoCtacte = $descuentoVencido['codigo'];
                    $importe = $descuentoVencido['importe'];
                    $porcentaje = $descuentoVencido['descuento'];
                    $importeOriginal = round($importe * 100 / (100 - $porcentaje), 2);
                    $myCtacteOrigen = new Vctacte($conexion, $codigoCtacte);
                    $myCtacteOrigen->importe = $importeOriginal;
                    $myAlumno = new Valumnos($conexion, $myCtacteOrigen->cod_alumno);
                    $myCtacteOrigen->perderDescuentoCondicionado();
                    $myCtacteOrigen->guardarCtacte();
                    $estadosHistoricos = new Vctacte_estado_historico($conexion);
                    $arrayGuardarEstadoHistorico = array(
                        "cod_ctacte" => $myCtacteOrigen->getCodigo(),
                        "estado" => $myCtacteOrigen->habilitado,
                        "motivo" => '6',
                        "fecha_hora" => date('Y-m-d H:i:s')
                    );
                    $estadosHistoricos->setCtacte_estado_historico($arrayGuardarEstadoHistorico);
                    $estadosHistoricos->guardarCtacte_estado_historico();
                    $myAlerta = new Valertas($conexion);
                    $myAlerta->fecha_hora = date("Y-m-d H:i:s");
                    $myAlerta->tipo_alerta = "descuento_condicionado_perdido";
                    $myAlerta->mensaje = "[!--el_alumno--] " . $myAlumno->nombre . " " . $myAlumno->apellido . " [!--ha_perdido_el_descuento_condicionado--]";
                    $myAlerta->guardarAlertas();
                    foreach ($arrUsuarios as $usuario) {
                        $myAlerta->setUsuario($usuario['codigo']);
                    }
                }
                if ($conexion->trans_status()) {
                    $conexion->trans_commit();
                } else {
                    $conexion->trans_rollback();
                }
            }
        }
    }

    function calcular_mora_crons() {
        $conexion = $this->load->database("default", true);
        $arrFiliales = Vfiliales::listarFiliales($conexion, array("version_sistema" => 2));
        //$ros = array("nombre" => "ROS", "codigo" => "20");//TEST ONLY
        //$arrFiliales = array($ros);//TEST ONLY
        $arrCondiciones = array(
            "fechavenc <" => date("Y-m-d"),
            "habilitado" => 1,
            "cod_concepto <>" => 3
        );
        foreach ($arrFiliales as $filial) {
            $conexion = $this->load->database($filial['codigo'], true);
            $arrMorasNormal = Vmoras::listarMoras($conexion, array("baja" => 0));
            $arrMorasCC = VmorasCursosCortos::listarMoras($conexion, array("baja" => 0));
            $arrCtaCteVencidos = Vctacte::getCtaCte($conexion, true, $arrCondiciones);
            foreach ($arrCtaCteVencidos as $ctacte) {
                $matricula = new Vmatriculas($conexion, $ctacte['concepto']);
                $curso = $matricula->getCurso();
                if ($curso[0]['tipo_curso'] == "curso" || $curso == null) {
                    // buscar para una ctacte todoas las moras que deben aplicarse (pueden aplicarse mas de una si es que se superponen las configuraciones en moras)
                    $myCtacte = new Vctacte($conexion, $ctacte['codigo']);
                    $conexion->trans_begin();
                    // la transaccion es entre registro de ctacte y no entre filiales (para una filial puede fallar una aplicacion de mora pero las otras no)
                    $myCtacte->aplicarMora($arrMorasNormal);
                    if ($conexion->trans_status()) {
                        $conexion->trans_commit();
                    } else {
                        $conexion->trans_rollback();
                    }
                } elseif ($curso[0]['tipo_curso'] == "curso_corto") {
                    // buscar para una ctacte todoas las moras que deben aplicarse (pueden aplicarse mas de una si es que se superponen las configuraciones en moras)
                    $myCtacte = new Vctacte($conexion, $ctacte['codigo']);
                    $conexion->trans_begin();
                    // la transaccion es entre registro de ctacte y no entre filiales (para una filial puede fallar una aplicacion de mora pero las otras no)
                    $myCtacte->aplicarMora($arrMorasCC);
                    if ($conexion->trans_status()) {
                        $conexion->trans_commit();
                    } else {
                        $conexion->trans_rollback();
                    }
                }
            }
        }
    }

    public function guardarCambioVencimiento($datos) {
        $conexion = $this->load->database($this->codigo_filial, true);
        $conexion->trans_begin();
        $cod_alumno = '';
        for ($i = 0; $i < count($datos['ctacte']); $i++) {
            $objCtacte = new Vctacte($conexion, $datos['ctacte'][$i]);
            $objCtacte->fechavenc = $datos['fechas'][$i];
            $objCtacte->guardarCtacte();
            $cod_alumno = $objCtacte->cod_alumno;
        }
        $parametroMora = array('cod_alumno' => $cod_alumno);
        $objtarecron = new Vtareas_crons($conexion);
        $objtarecron->guardar('calcular_mora', $parametroMora, $this->codigo_filial);
        $estadotran = $conexion->trans_status();
        if ($estadotran === FALSE) {
            $conexion->trans_rollback();
        } else {
            $conexion->trans_commit();
        }
        return class_general::_generarRespuestaModelo($conexion, $estadotran);
    }

    public function getCtaCtePagas($cod_alumno) {
        $conexion = $this->load->database($this->codigo_filial, true);
        $this->load->helper('cuentacorriente');
        $this->load->helper('filial');
        $ctaCtePagasAlumno = Vctacte::getCtaCteNotaCredito($conexion, $cod_alumno);
        formatearCtaCte($conexion, $ctaCtePagasAlumno);
        return $ctaCtePagasAlumno;
    }

    public function getSaldoFacturacion($datos, $ctactecheck) {
        $conexion = $this->load->database($this->codigo_filial, true);
        $this->load->helper('filial');
        $saldo = 0;
        if (count($ctactecheck) > 0) {
            if ($datos == 'facturar') {
                $saldo = Vctacte::getSumImporteFacturar($conexion, $ctactecheck);
            } else if ($datos == 'facturarcobrar') {
                $saldo = Vctacte::getSumImporteFacturarCobrar($conexion, $ctactecheck);
            }
        }
        return formatearImporte($saldo, false);
    }

    public function cambiarFechasVencimiento($arrCtacte, $fecha, $codigo_ctacte, $periodicidad) {
        $conexion = $this->load->database($this->codigo_filial, true);
        $periodos = $this->Model_configuraciones->getValorConfiguracion(1);
        $periodoSeleccionado = $periodos[$periodicidad];
        $fechaCambio = '';
        $comienzoCambio = false;
        for ($i = 0; $i < count($arrCtacte); $i++) {
            if ($arrCtacte[$i]['codigo'] == $codigo_ctacte) {
                $comienzoCambio = true;
            }
            if ($comienzoCambio) {
                if ($arrCtacte[$i]['codigo'] == $codigo_ctacte) {
                    $fechaCambio = $fecha;
                    $ultimaFecha = $fecha;
                } else {
                    $fecha = strtotime($ultimaFecha);
                    $fechaNueva = strtotime($periodoSeleccionado['valor'] . ' ' . $periodoSeleccionado['unidadTiempo'], $fecha);
                    $fechaCambio = date("Y-m-d", $fechaNueva);
                    $ultimaFecha = $fechaCambio;
                    $fechaCambio = getPrimerFechaHabil($conexion, $fechaCambio);
                }
                $arrCtacte[$i]['fechavenc'] = formatearFecha_pais($fechaCambio);
            }
        }
        return $arrCtacte;
    }

    public function guardarConfiguracion($codigo, $valor, $key, $usuario) {
        $conexion = $this->load->database($this->codigo_filial, true);
        $conexion->trans_begin();
        $objConfiguracion = new Vconfiguracion($conexion, $codigo);
        $arrayConfiguracion = array(
            "key" => $key,
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
        }
        return class_general::_generarRespuestaModelo($conexion, $estadotran);
    }

    public function enviarDeudasCtaCte($alerta, $objalerta, CI_DB_mysqli_driver $conexion = null, &$comentario = null) {
        if ($conexion != null) {
            $conexion = $this->load->database((string) $this->codigo_filial, true);
        }
        $this->load->helper("cuentacorriente");
        $this->load->helper('filial');
        $confalerta = $objalerta->getAlertaConfiguracion();
        $confalumnoalerta = $objalerta->getAlertaAlumnoConfiguracion($alerta['cod_alumno']); //traigo las ctasctes
        $arrctacte = array();
        foreach ($confalumnoalerta as $value) {
            $arrctacte[] = $value['valor'];
        }
        foreach ($confalerta as $value) {
            if ($value['key'] == 'titulo') {
                $asunto = $value['valor'];
            }
        }
        $cuerpomail = $alerta['mensaje'];
        if (count($arrctacte) > 0) {
            $config = array();
            $config['charset'] = 'iso-8859-1';
            maquetados::desetiquetarCtaCte($conexion, $arrctacte, $cuerpomail);
            maquetados::desetiquetarAlumnos($conexion, $alerta['cod_alumno'], $cuerpomail);
            maquetados::desetiquetarDatosFilial($conexion, null, $cuerpomail, $this->codigo_filial);
            maquetados::desetiquetarIdioma($cuerpomail, true);
            $objalumno = new Valumnos($conexion, $alerta['cod_alumno']);
            $this->email->initialize($config);
            $this->email->from('noreply@iga-la.net', 'iga noreply');
            $this->email->to($objalumno->email);
            $this->email->subject(utf8_decode($asunto));
            $this->email->message(utf8_decode($cuerpomail));
            $respuesta = $this->email->send();
            if (!$respuesta) {
                $comentario = $this->email->print_debugger();
            }
            $this->email->clear();
            return $respuesta;
        } else {
            $comentario = 'no hay registros de cuenta corriente';
            return false;
        }
    }

    public function getMotivosBaja() {
        $motivos = Vctacte_estado_historico::getmotivos(false, true);
        for ($i = 0; $i < count($motivos); $i++) {
            $motivos[$i]['motivo'] = lang($motivos[$i]['motivo']);
        }
        return $motivos;
    }

    public function baja($datos) {
        $conexion = $this->load->database($this->codigo_filial, true);
        $conexion->trans_begin();
        $respuesta = '';
        $objctacte = new Vctacte($conexion, $datos['ctacte']);
        $respuesta['ctas_ctes'][] = $objctacte->baja(null, $datos['motivo'], $datos['comentario'], $datos['cod_usuario']);
        $estadotran = $conexion->trans_status();
        if ($estadotran === FALSE) {
            $conexion->trans_rollback();
        } else {
            $conexion->trans_commit();
        }
        return class_general::_generarRespuestaModelo($conexion, $estadotran, $respuesta);
    }

    public function calcular_mora($cod_alumno = null) {
        $conexion = $this->load->database($this->codigo_filial, true);
        $conexion->trans_begin();
        if ($cod_alumno != null) {
            $arrCondiciones = array(
                "fechavenc <" => date("Y-m-d"),
                "habilitado" => 1,
                "cod_concepto <>" => 3,
                "cod_alumno" => $cod_alumno
            );
            $arrMoras = Vmoras::listarMoras($conexion, array("baja" => 0));
            $arrCtaCteVencidos = Vctacte::getCtaCte($conexion, true, $arrCondiciones);
            foreach ($arrCtaCteVencidos as $ctacte) { // buscar para una ctacte todoas las moras que deben aplicarse (pueden aplicarse mas de una si es que se superponen las configuraciones en moras)
                $myCtacte = new Vctacte($conexion, $ctacte['codigo']);
                $myCtacte->aplicarMora($arrMoras);
            }
        }
        $estadotran = $conexion->trans_status();
        if ($estadotran === FALSE) {
            $conexion->trans_rollback();
        } else {
            $conexion->trans_commit();
        }
        return class_general::_generarRespuestaModelo($conexion, $estadotran);
    }

    public function guardarCtaCte($datos) {
        $conexion = $this->load->database($this->codigo_filial, true);
        $conexion->trans_begin();
        if ($datos['accion'] == 'linea') {
            $valores = array();
            $valores['valor_refinanciar'] = $datos['valor_refinanciar'];
            $valores['cuotas'] = $datos['cuotas'];
            $valores['decimales'] = 2;
            $valores['cod_concepto'] = $datos['cod_concepto'];
            $valores['perioricidad'] = $datos['perioricidad'];
            $valores['fechapago'] = $datos['fechapago'];
            $datosguardar = $this->getDetalleNuevoConcepto($valores);
            $objOtroCta = new Vctacte_otros($conexion);
            $objOtroCta->guardar($datos['cod_concepto'], $datos['cod_usuario']);
            foreach ($datosguardar as $rowCuota) {
                $ctacte = new Vctacte($conexion);
                $respuesta = $ctacte->guardar($datos['alumno'], $rowCuota['nrocuota'], $rowCuota['valor_real'], $rowCuota['fecha_real'], null, null, $datos['cod_concepto'], $objOtroCta->getCodigo(), 1);
            }
        } else {
            $myPlan = new Vplanes_pago($conexion, $datos['plan']);
            $myMatricula = new Vmatriculas($conexion, $datos['matricula']);
            $periodicidad = Vconfiguracion::getValorConfiguracion($conexion, null, 'PeriodoCtacte');
            $valor = 0;
            $unidad = 'month';
            foreach ($periodicidad as $value) {
                if ($value['codigo'] == $myPlan->periodo) {
                    $valor = $value['valor'];
                    $unidad = $value['unidadTiempo'];
                }
            }
            $periodo = $valor . ' ' . $unidad;
            foreach ($datos['financiaciones'] as $financiacion) {
                $condiciones = array(
                    'codigo_plan' => $datos['plan'],
                    'codigo_concepto' => $financiacion['cod_concepto'],
                    'codigo_financiacion' => $financiacion['cod_financiacion']
                );
                $planfinanciacion = Vplanes_financiacion::listarPlanes_financiacion($conexion, $condiciones);
                $ultCuota = $myMatricula->getUltimaCuota($financiacion['cod_concepto']);
                $plandesc = $myPlan->getPlanFinanciacionDescuento($financiacion['cod_financiacion'], $financiacion['cod_concepto']);
                $descuento = count($plandesc) > 0 ? $plandesc[0]['descuento'] : 0;
                $limite = count($plandesc) > 0 ? $plandesc[0]['limite_primer_cuota'] : 'sin_fecha_limite';
                $vencimiento = $financiacion['fecha_primer_pago'] == null ? date('Y-m-d') : $financiacion['fecha_primer_pago'];
                foreach ($planfinanciacion as $rowfinanciacion) {
                    $venciminetotest = strtotime($vencimiento);
                    if ($rowfinanciacion['nro_cuota'] == 1) {
                        $vencimiento = $limite != 'al_momento' ? $financiacion['fecha_primer_pago'] : date('Y-m-d');
                    } else {
                        $vencimiento = date("Y-m-d", strtotime('+' . $periodo, $venciminetotest));
                    }
                    $vencimientoValido = getPrimerFechaHabil($conexion, $vencimiento);
                    $ctacte = new Vctacte($conexion);
                    $respuesta = $ctacte->guardar($myMatricula->cod_alumno, $rowfinanciacion['nro_cuota'] + $ultCuota, $rowfinanciacion['valor'], $vencimientoValido, 1, 0, $financiacion['cod_concepto'], $myMatricula->getCodigo(), 1);
                    if ($descuento != 0) {
                        $dias = 0;
                        if ($myPlan->descon == 1) {
                            $tipoDescuento = "condicionado";
                            $conf_dto = Vconfiguracion::getValorConfiguracion($conexion, null, 'descuentosCondicionados');
                            $dias = $conf_dto['dias_prorroga'];
                        } else {
                            $tipoDescuento = "no_condicionado";
                        }

                        $matctacte = array(
                            'cod_ctacte' => $ctacte->getCodigo(),
                            "descuento" => $descuento,
                            "estado" => $tipoDescuento,
                            "dias_vencido" => $dias,
                            "cod_usuario" => $datos['cod_usuario'],
                            "fecha" => date('Y-m-d'),
                            "forma_descuento" => "plan_pago"
                        );

                        $conexion->insert('matriculaciones_ctacte_descuento', $matctacte);
                    }
                }
            }
        }

        $estadotran = $conexion->trans_status();
        if ($estadotran === FALSE) {
            $conexion->trans_rollback();
        } else {
            $conexion->trans_commit();
        }
        return class_general::_generarRespuestaModelo($conexion, $estadotran, $respuesta);
    }

    public function getDescripcion($codctacte) {
        $conexion = $this->load->database($this->codigo_filial, true);
        $this->load->helper('cuentacorriente');
        $condiciones = array('codigo' => $codctacte);
        $arrCtaCte = Vctacte::listarCtacte($conexion, $condiciones);
        formatearCtaCte($conexion, $arrCtaCte);
        return $arrCtaCte[0]['descripcion'];
    }

    public function getImputacionesCtacte($idFilial, $idCtaCte) {
        $conexion = $this->load->database($idFilial, true);
        $myCtacte = new Vctacte($conexion, $idCtaCte);
        $arrImputaciones = $myCtacte->getImputacionesCtaCte();
        foreach ($arrImputaciones as $key => $imputacion) {
            $arrImputaciones[$key]['tipo'] = $imputacion['tipo'];
            if ($imputacion['tipo'] == 'COBRO') {
                $cobro = new Vcobros($conexion, $imputacion['cod_cobro']);
                $arrImputaciones[$key]['importe'] = $cobro->importe;
                $arrImputaciones[$key]['medio_pago'] = $cobro->medio_pago;
                $arrImputaciones[$key]['fechaalta'] = $cobro->fechaalta;
                $arrImputaciones[$key]['fechareal'] = $cobro->fechareal;
                $arrImputaciones[$key]['cod_alumno'] = $cobro->cod_alumno;
                $arrImputaciones[$key]['cod_caja'] = $cobro->cod_caja;
                $arrImputaciones[$key]['periodo'] = $cobro->periodo;
                $medio = new Vmedios_pago($conexion, $cobro->medio_pago);
                $arrImputaciones[$key]['medio'] = $medio->medio;
            } elseif ($imputacion['tipo'] == 'NOTA_CREDITO') {
                $notacredito = new Vnotas_credito($conexion, $imputacion['cod_cobro']);
                $arrImputaciones[$key]['importe'] = $notacredito->importe;
                $arrImputaciones[$key]['medio_pago'] = '5';
                $arrImputaciones[$key]['fechaalta'] = $notacredito->fechaalta;
                $arrImputaciones[$key]['fechareal'] = $notacredito->fechareal;
                $arrImputaciones[$key]['cod_alumno'] = $notacredito->cod_alumno;
                $arrImputaciones[$key]['cod_caja'] = '';
                $arrImputaciones[$key]['periodo'] = '';
                $arrImputaciones[$key]['medio'] = $imputacion['tipo'];
            }
        }
        return $arrImputaciones;
    }

    public function getDetalleNuevoConcepto($datos) {
        $conexion = $this->load->database($this->codigo_filial, true);
        $afinanciar = 0;
        $afinanciar = $datos['valor_refinanciar'];
        $valorcuotas = round($afinanciar / $datos['cuotas'], $datos['decimales']);
        if (isset($datos['interesporc']) && $datos['interesporc'] != FALSE && $datos['interesporc'] != 0) {
            $valorcuotas = $valorcuotas + $valorcuotas * $datos['interesporc'] / 100;
        }
        $detalle = array();
        $fechapago = $datos['fechapago'];
        $vencimiento = $fechapago;
        $suma = 0;
        for ($i = 0; $i < $datos['cuotas']; $i++) {
            $detalle[$i]['concepto'] = lang(Vconceptos::getKey($conexion, $datos['cod_concepto']));
            $detalle[$i]['nrocuota'] = $i + 1;
            $detalle[$i]['valor'] = formatearImporte($valorcuotas);
            $detalle[$i]['valor_real'] = $valorcuotas;
            $detalle[$i]['orden'] = 1;
            $detalle[$i]['cod_concepto'] = $datos['cod_concepto'];
            $venciminetotest = strtotime($vencimiento);
            $valor = isset($datos['perioricidad'][0]['valor']) ? $datos['perioricidad'][0]['valor'] : $datos['perioricidad']['valor'];
            $unidad = isset($datos['perioricidad'][0]['unidadTiempo']) ? $datos['perioricidad'][0]['unidadTiempo'] : $datos['perioricidad']['unidadTiempo'];
            $vencimiento = $detalle[$i]['nrocuota'] == 1 ? $fechapago : date("Y-m-d", strtotime('+' . $valor . ' ' . $unidad, $venciminetotest));
            $vencimientoValido = getPrimerFechaHabil($conexion, $vencimiento);
            $detalle[$i]['fecha'] = formatearFecha_pais($vencimientoValido);
            $detalle[$i]['fecha_real'] = $vencimientoValido;
            $suma = $suma + $valorcuotas;
        }
        return $detalle;
    }

    public function checkMoraCampusExamenes($cod_alumno, $filial)
    {
        $conexion = $this->load->database($filial, true);
        $this->load->helper('cuentacorriente');
        $arrCtaCte = Vctacte::checkMorasAlumnoCampusExamenes($conexion, $cod_alumno);
        return $arrCtaCte[0];
    }

    public function guardarTransaccionMoras($filial){
        $conexion = $this->load->database('general', true);
        $datetime = date("Y-m-d H:i:s");
        $morasTransactionStatement = "INSERT INTO `general`.`registro_calculo_mora` (cod_filial,fecha_hora) VALUES (".$filial.",'".$datetime."')";
        $conexion->query($morasTransactionStatement);
    }
}

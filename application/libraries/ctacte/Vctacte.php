<?php

/**
 * Class Vctacte
 *
 * Class  Vctacte maneja todos los aspectos de alumnos
 *
 * @package  SistemaIGA
 * @subpackage Alumnos
 * @author   Ivan berthillod <ivan.sys@gmail.com>
 * @author   Aquiles Gonzalez <sistemas1@iga-la.net>
 * @version  $Revision: 1.1 $
 * @access   private
 */
class Vctacte extends Tctacte {

    function __construct(CI_DB_mysqli_driver $conexion, $codigo = null) {
        parent::__construct($conexion, $codigo);
    }

    static function getCtaCte($conexion, $debe = false, $arrCondiciones = null, $arrCondindicioneslike = null, $arrLimit = null, $arrSort = null, $contar = false, $arrwhere_in = null, $ultimafinanciacion = null) {
        $conexion->select('(ctacte.importe - ctacte.pagado) AS saldo');
        if ($debe == true) {
            $conexion->where('ctacte.pagado < ctacte.importe');
        }
        if ($arrCondindicioneslike != null) {
            foreach ($arrCondindicioneslike as $key => $value) {
                $conexion->or_like($key, $value);
            }
        }
        if ($arrwhere_in != null) {
            foreach ($arrwhere_in as $wherein) {
                $conexion->where_in($wherein['campo'], $wherein['valores']);
            }
        }
        if ($ultimafinanciacion == 1) {
            $conexion->where('financiacion IN (select max(ct1.financiacion) from ctacte as ct1 where ct1.cod_alumno = ctacte.cod_alumno and ct1.concepto = ctacte.concepto)');
        }
        return Vctacte::listarCtacte($conexion, $arrCondiciones, $arrLimit, $arrSort, null, $contar);
    }

    static function getDescripcion($conexion, $codalumno, $nrocuota, $codconcepto, $concepto, $financiacion = null) {
        $descripcion = array();
        $cantcuotas = Vctacte::getCantCuotas($conexion, $codalumno, $codconcepto, $concepto, $financiacion);
        switch ($codconcepto) {
            case 3:
                $dias = 0;
                $condicion = array("codigo" => $concepto);
                $rowmora = Vctacte::getCtaCte($conexion, false, $condicion);
                $rowmora[0]['descripcion'] = Vctacte::getDescripcion($conexion, $rowmora[0]['cod_alumno'], $rowmora[0]['nrocuota'], $rowmora[0]['cod_concepto'], $rowmora[0]['concepto']);
                $pagotodo = $rowmora[0]['saldo'] <= 0 ? true : false;
                if (!$pagotodo) {
                    $dias = func_fechas::diferenciaEntreFechas(date("Y-m-d H:i:s"), $rowmora[0]['fechavenc']);
                } else {
                    $condimputaciones = array('cod_ctacte' => $concepto, 'estado' => 'confirmado');
                    $order = array(array('campo' => 'codigo', 'orden' => 'desc'));
                    $imputaciones = Vctacte_imputaciones::listarCtacte_imputaciones($conexion, $condimputaciones, null, $order);
                    if (isset($imputaciones[0])) {
                        $dias = func_fechas::diferenciaEntreFechas($imputaciones[0]['fecha'], $rowmora[0]['fechavenc']);
                    } else {
                        $dias = func_fechas::diferenciaEntreFechas(date("Y-m-d H:i:s"), $rowmora[0]['fechavenc']);
                    }
                }
                $descripcion['rowMora'] = $rowmora[0];
                $descripcion['dias_vencida'] = $dias - 1;
                $arrayMora_ctacte = Vctacte::getMorasCtaCte($conexion, $rowmora[0]['codigo']);
                $totMulta = '';
                $totMora = '';
                foreach ($arrayMora_ctacte as $mora) {
                    switch ($mora['tipo']) {
                        case 'MULTA':
                            $totMulta = $totMulta + $mora['precio'];
                            break;

                        case 'MORA':
                            $totMora = $totMora + $mora['precio'];
                            break;
                    }
                }
                $moraDes = lang('MORA');
                $multaDes = '';
                if ($totMora != '') {
                    if ($totMulta != '') {
                        $moraDes = lang('MORA') . ' ' . formatearImporte($totMora, true, $conexion);
                    } else {
                        $moraDes = lang('MORA');
                    }
                }
                if ($totMulta != '') {
                    if ($totMora != '') {
                        $multaDes = lang('MULTA') . ' ' . formatearImporte($totMulta, true, $conexion);
                    } else {
                        $multaDes = lang('MULTA');
                    }
                }
                $descripcion['nombreconcepto'] = $moraDes . ' ' . $multaDes;
                break;

            case 1:
            case 5:
                $matricula = new Vmatriculas($conexion, $concepto);
                $curso = $matricula->getCurso();
                $nbreperiodos = '';
                $periodos = $matricula->getPeriodosMatricula();
                $plan = new Vplanes_academicos($conexion, $matricula->cod_plan_academico);
                $periodosplan = $plan->getPeriodos();
                if (count($periodosplan) > 1 && count($periodos) < count($periodosplan)) {
                    foreach ($periodos as $row) {
                        $nbreperiodos.= ' (' . lang($row['nombre']) . ')';
                    }
                }
                //siwakawa
                $nrocuota = str_pad($nrocuota,2,"0",STR_PAD_LEFT);
                $descripcion = array(
                    'nombrecurso' => array('es' => $curso[0]['nombre_es'], 'pt' => $curso[0]['nombre_pt'], 'in' => $curso[0]['nombre_in']),
                    'nro_cuota' => $nrocuota,
                    'total_cuotas' => $cantcuotas,
                    'nombreconcepto' => Vconceptos::getKey($conexion, $codconcepto),
                    'financiacion' => $financiacion,
                    'nbreperiodos' => $nbreperiodos
                );
                break;

            default:
                //siwakawa
                $nrocuota = str_pad($nrocuota,2,"0",STR_PAD_LEFT);
                $descripcion = array(
                    'nro_cuota' => $nrocuota,
                    'total_cuotas' => $cantcuotas,
                    'nombreconcepto' => Vconceptos::getKey($conexion, $codconcepto),
                    'financiacion' => $financiacion
                );
                break;
        }
        return $descripcion;
    }

    static function getCantCuotas($conexion, $cod_alumno, $cod_concepto, $concepto, $financiacion) {
        $condiciones = array(
            'cod_alumno' => $cod_alumno,
            'cod_concepto' => $cod_concepto,
            'concepto' => $concepto,
            'habilitado <>' => 3,
            'financiacion' => $financiacion
        );
        return Vctacte::listarCtacte($conexion, $condiciones, NULL, NULL, NULL, TRUE);
    }

    static function ordenarCtaCte($arrCtaCte) {
        $moras = array();
        $ctacte = array();
        $temporal = array();
        $morasencontradas = array();
        $final = array();
        foreach ($arrCtaCte as $row) {
            if ($row['cod_concepto'] == 3) {
                $moras[] = $row;
            } else {
                $ctacte[] = $row;
            }
        }
        foreach ($ctacte as $rowctacte) {
            $bool = TRUE;
            $indice = 0;
            while ($bool && (count($moras) > $indice)) {
                if ($moras[$indice]['concepto'] == $rowctacte['codigo']) {
                    $temporal[] = $rowctacte;
                    $temporal[] = $moras[$indice];
                    $morasencontradas[$indice] = $moras[$indice];
                    $bool = FALSE;
                } else {
                    $indice++;
                }
            }
            if ($bool) {
                $temporal[] = $rowctacte;
            }
        }

        if (count($moras) == count($morasencontradas)) {
            return $temporal;
        } else {
            $diferencia = array_diff_key($moras, $morasencontradas);
            foreach ($diferencia as $rowmora) {
                $final[] = $rowmora;
            }
            foreach ($temporal as $value) {
                $final[] = $value;
            }
            return $final;
        }
    }

    public function getDescuentos($estado = null, $activo = null) {
        $this->oConnection->select("IF (matriculaciones_ctacte_descuento.estado = 'condicionado', (DATE_ADD(ctacte.fechavenc,INTERVAL matriculaciones_ctacte_descuento.dias_vencido day)), null) AS fecha_perdida_descuento", false);
        $this->oConnection->join("ctacte", "ctacte.codigo = matriculaciones_ctacte_descuento.cod_ctacte");
        $this->oConnection->where("matriculaciones_ctacte_descuento.cod_ctacte", $this->codigo);
        if ($estado != null) {
            $tipoFiltro = is_array($estado) ? "where_in" : "where";
            $this->oConnection->$tipoFiltro("matriculaciones_ctacte_descuento.estado", $estado);
        }
        if ($activo !== null){
            $this->oConnection->where("matriculaciones_ctacte_descuento.activo", $activo);
        }
        return Vmatriculaciones_ctacte_descuento::listarMatriculaciones_ctacte_descuento($this->oConnection);
    }

    public function baja($bajamoras = true, $motivo = null, $comentario = null, $cod_usuario = null) {
        $this->habilitado = '0';
        $this->guardarCtacte();
        $estadosHistoricos = new Vctacte_estado_historico($this->oConnection);
        $arrayGuardarEstadoHistorico = array(
            "cod_ctacte" => $this->codigo,
            "estado" => $this->habilitado,
            "motivo" => $motivo,
            "fecha_hora" => date('Y-m-d H:i:s'),
            "comentario" => $comentario,
            "cod_usuario" => $cod_usuario
        );
        $estadosHistoricos->setCtacte_estado_historico($arrayGuardarEstadoHistorico);
        $respuesta = $estadosHistoricos->guardarCtacte_estado_historico();
        if ($bajamoras) {
            $this->bajaMorasCtaCte();
        }
        return $respuesta;
    }

    public function getImputacionesCtaCte($estado = null) {
        $this->oConnection->select('*,ctacte_imputaciones.estado as estadoImputacion');
        $this->oConnection->from('ctacte_imputaciones');
        $this->oConnection->where('ctacte_imputaciones.cod_ctacte', $this->codigo);
        if ($estado != null) {
            $this->oConnection->where('ctacte_imputaciones.estado', $estado);
        }
        $query = $this->oConnection->get();
        $result = $query->result_array();
        return $result;
    }

    static function getMorasCtaCte($conexion, $cod_ctacte) {
        $conexion->select("*");
        $conexion->from("ctacte_moras");
        $conexion->join('moras', 'moras.codigo = ctacte_moras.cod_mora');
        $conexion->where("ctacte_moras.cod_ctacte", $cod_ctacte);
        $query = $conexion->get();
        $result = $query->result_array();
        return $result;
    }

    static function getSumImporteFacturarCobrar($conexion, $wherein) {
        $conexion->select('IFNULL(SUM(facturas_renglones.importe),0)', FALSE);
        $conexion->from('facturas_renglones');
        $conexion->where('ctacte.codigo = facturas_renglones.cod_ctacte ');
        $conexion->where('facturas_renglones.anulada', 0);
        $subQuery = $conexion->return_query();

        $conexion->resetear();
        $conexion->select('IFNULL(SUM(ctacte_imputaciones.valor),0)', FALSE);
        $conexion->from('ctacte_imputaciones');
        $conexion->where('ctacte_imputaciones.cod_ctacte = ctacte.codigo ');
        $conexion->where('ctacte_imputaciones.estado <>', "anulado");
        $subQuery2 = $conexion->return_query();

        $conexion->resetear();
        $conexion->select('(ctacte.importe - (' . $subQuery . ')) AS saldofacturar, (ctacte.importe - (' . $subQuery2 . ')) AS saldocobrar', FALSE);
        $conexion->select('ctacte.codigo');
        $conexion->from('ctacte');
        if ($wherein != null) {
            $conexion->where_in('codigo', $wherein);
        }
        $conexion->having('saldofacturar <>', '0');
        $conexion->having('saldocobrar <>', '0');
        $query = $conexion->get();
        $resultado = $query->result_array();
        $total = 0;
        foreach ($resultado as $value) {
            if ($value['saldofacturar'] <= $value['saldocobrar']) {
                $saldoFacCob = $value['saldofacturar'];
            } elseif ($value['saldofacturar'] > $value['saldocobrar']) {
                $saldoFacCob = $value['saldocobrar'];
            }
            $total = $total + $saldoFacCob;
        }
        return $total;
    }

    public function getFacturas() {
        $this->oConnection->select('facturas_renglones.*, general.tipos_facturas.factura,facturas.fecha, facturas_propiedades.valor as nrofact, general.puntos_venta.medio as ptovta_medio');
        $this->oConnection->from('facturas_renglones');
        $this->oConnection->join('facturas', 'facturas.codigo = facturas_renglones.cod_factura');
        $this->oConnection->join('facturas_propiedades', 'facturas_propiedades.cod_factura = facturas.codigo and facturas_propiedades.propiedad = "numero_factura"');
        $this->oConnection->join('general.puntos_venta', 'general.puntos_venta.codigo = facturas.punto_venta');
        $this->oConnection->join('general.tipos_facturas', 'general.tipos_facturas.codigo = general.puntos_venta.tipo_factura');
        $this->oConnection->where('facturas_renglones.cod_ctacte', $this->codigo);
        $this->oConnection->where('facturas_renglones.anulada', 0);
        $query = $this->oConnection->get();
        return $query->result_array();
    }

    static function getCtaCteSinFacturar($conexion = null, $cod_alumno = null, $condiciones = null, $orden = null, 
            $arrLimit = null, $arrLike = null, $arrBetween = null, $contar = FALSE, $wherein = NULL, $tipoFactura = null, 
            $separador = null, $facturante = null, $mostrarCobradas_nofacturadas = false, $soloCobradas = false, $medioCobro = null) {
        $conexion->select('IFNULL(ROUND(SUM(facturas_renglones.importe),2),0)', FALSE);
        $conexion->from('facturas_renglones');
        $conexion->where('ctacte.codigo = facturas_renglones.cod_ctacte ');
        $conexion->where('facturas_renglones.anulada', 0);
        $subQuery = $conexion->return_query();
        $conexion->resetear();
        
        $conexion->select("GROUP_CONCAT(cobros.codigo)", false);
        $conexion->from("ctacte_imputaciones");
        $conexion->join("cobros", "cobros.codigo = ctacte_imputaciones.cod_cobro AND cobros.estado = 'confirmado'");
        $conexion->where("ctacte_imputaciones.cod_ctacte = ctacte.codigo");
        $conexion->where("ctacte_imputaciones.estado", "confirmado");
        $sqCodCobro = $conexion->return_query();
        $conexion->resetear();
        
        $conexion->select("general.medios_pago.medio");
        $conexion->from("cobros");
        $conexion->join("ctacte_imputaciones", "ctacte_imputaciones.cod_cobro = cobros.codigo");
        $conexion->join("general.medios_pago", "general.medios_pago.codigo = cobros.medio_pago");
        $conexion->where("ctacte_imputaciones.cod_ctacte = ctacte.codigo");
        $conexion->where("ctacte_imputaciones.estado", "confirmado");
        $conexion->where("cobros.estado", "confirmado");
        if ($medioCobro != null){
            $conexion->where("cobros.medio_pago", $medioCobro);
        }
        $conexion->limit(1);
        $sqMedio = $conexion->return_query();
        $conexion->resetear();

        $conexion->select('ctacte.*, (ctacte.importe - (' . $subQuery . ')) AS saldofacturar', FALSE);
        $conexion->from('ctacte');
        if ($cod_alumno != null) {
            $conexion->where('ctacte.cod_alumno', $cod_alumno);
        } else {
            $nombreApellido = formatearNomApeQuery();
            $conexion->select("CONCAT($nombreApellido) as nombre_apellido, razones_sociales.razon_social as razonsocial", false);
            $conexion->select("($sqCodCobro) AS cod_cobro", false);
            $conexion->select("($sqMedio) AS medio_pago");
            $conexion->join('alumnos', 'ctacte.cod_alumno = alumnos.codigo');
            $conexion->join('alumnos_razones', 'alumnos_razones.cod_alumno = alumnos.codigo');
            $conexion->join('razones_sociales', 'razones_sociales.codigo = alumnos_razones.cod_razon_social');            
            if ($tipoFactura != null && $facturante != null) {
                if (is_array($tipoFactura)) {
                    $tipoFactura = implode(", ", $tipoFactura);
                }
                $conexion->select("IF((SELECT count(general.condiciones_facturacion.codigo)
                FROM general.condiciones_facturacion
                WHERE general.condiciones_facturacion.cod_tipo_factura IN ($tipoFactura)
                AND general.condiciones_facturacion.cod_cond_facturante = (SELECT general.razones_sociales_general.condicion 
                        FROM general.razones_sociales_general JOIN general.facturantes ON general.facturantes.cod_razon_social = general.razones_sociales_general.codigo 
                        WHERE general.facturantes.codigo = $facturante)
                AND general.condiciones_facturacion.cod_cond_facturado = (SELECT rz.condicion FROM razones_sociales AS rz where rz.codigo = razones_sociales.codigo)) > 0,1,0) AS puedefacturar", false);
                if ($mostrarCobradas_nofacturadas) {
                    $conexion->where('ctacte.codigo in (SELECT ctacte_imputaciones.cod_ctacte from ctacte_imputaciones where ctacte.codigo = ctacte_imputaciones.cod_ctacte) and ctacte.codigo not in (SELECT facturas_renglones.cod_ctacte from facturas_renglones where facturas_renglones.cod_ctacte = ctacte.codigo)');
                }
                $conexion->having('puedefacturar = 1');
            }
            $conexion->where('alumnos_razones.default_facturacion', 1);
        }
        if (count($condiciones) > 0) {
            foreach ($condiciones as $key => $value) {
                $conexion->where($key, $value);
            }
        }
        if ($wherein != null) {
            $conexion->where_in('ctacte.codigo', $wherein);
        }
        if ($arrBetween != NULL) {
            $conexion->where('ctacte.fechavenc >= "' . $arrBetween['fechaini'] . '" AND ctacte.fechavenc <="' . $arrBetween['fechafin'] . '"');
        }
        if ($soloCobradas){
            $conexion->where("ctacte.codigo IN (SELECT ctacte_imputaciones.cod_ctacte ".
                                "FROM ctacte_imputaciones ".
                                "INNER JOIN cobros ON cobros.codigo = ctacte_imputaciones.cod_cobro AND cobros.estado = 'confirmado' ".
                                "WHERE ctacte_imputaciones.estado = 'confirmado')");
        }
        $conexion->having('saldofacturar > 0');
        if ($arrLike != 0) {
            foreach ($arrLike as $key => $value) {
                $conexion->having("REPLACE(nombre_apellido, '$separador ',' ') like REPLACE('%$value%', '$separador ',' ')", false);
            }
        }
        if ($medioCobro != null){
            $conexion->having("medio_pago IS NOT NULL");
        }
        if ($orden > 0) {
            $conexion->order_by($orden['campo'], $orden['orden']);
        }
        $conexion->group_by("ctacte.codigo");
        if ($arrLimit != null) {
            $conexion->limit($arrLimit[1], $arrLimit[0]);
        }
        $query = $conexion->get();
        if ($contar) {
            $arrResp = $query->num_rows();
        } else {            
            $arrResp = $query->result_array();
        }
        return $arrResp;
    }

    static function getCtaCteImputar($conexion = null, $cod_alumno = null, $orden = null, $wherein = null, $arrCondiciones = null, 
            $arrLimit = null, $contar = false, $arrSort = null, $conrazonsocial = false, $whereInAlumnos = null, $sinBoletoEmitido = true, 
            $separador = null, $fecha_desde = null, $fecha_hasta = null, $codMatricula = null) {
        $conexion->select("(ctacte.importe - IFNULL((SELECT SUM(ctacte_imputaciones.valor)
                                        FROM ctacte_imputaciones
                                        WHERE ctacte_imputaciones.cod_ctacte = ctacte.codigo
                                        AND ctacte_imputaciones.estado IN ('confirmado', 'pendiente')),0)) AS saldocobrar", false);
        $conexion->having('saldoCobrar > 0');
        if (!$sinBoletoEmitido) {
            $conexion->select("IF ((SELECT bancos.boletos_bancarios.numero_documento 
                                        FROM bancos.boletos_bancarios 
                                        WHERE bancos.boletos_bancarios.numero_documento = ctacte.codigo
                                        AND bancos.boletos_bancarios.cod_filial = {$conexion->database} LIMIT 0, 1
                                        ) IS NULL, 0, 1) 
                                        AS con_boleto", false);
            $conexion->having("con_boleto = 0");
        }
        if ($conrazonsocial == true) {
            $nombreApellido = formatearNomApeQuery();
            $conexion->select("CONCAT($nombreApellido) as nombre_apellido", false);
            $conexion->join("alumnos", "alumnos.codigo = ctacte.cod_alumno");
            $conexion->join("alumnos_razones", "alumnos_razones.cod_alumno = ctacte.cod_alumno AND default_facturacion = 1");
        }
        if ($cod_alumno != '') {
            $conexion->where('ctacte.cod_alumno', $cod_alumno);
        }
        if ($codMatricula != null){
            $conexion->where("ctacte.concepto", $codMatricula);
        }
        if ($wherein != null) {
            $conexion->where_in('ctacte.codigo', $wherein);
        }
        if ($whereInAlumnos != null) {
            $conexion->where_in('ctacte.cod_alumno', $whereInAlumnos);
        }
        $conexion->where('ctacte.habilitado in (1,2)');
        if (count($arrCondiciones) > 0) {
            $arrTemp = array();
            foreach ($arrCondiciones as $key => $value) {
                if ($key == 'nombre_apellido') {
                    $arrTemp[] = "REPLACE(nombre_apellido, '$separador ',' ') LIKE REPLACE('%$value%', '$separador ',' ')";
                } else {
                    $arrTemp[] = "$key LIKE '%$value%'";
                }
            }
            if (count($arrTemp) > 0) {
                $having = "(" . implode(" OR ", $arrTemp) . ")";
                $conexion->having($having);
            }
        }
        if ($fecha_desde != null && $fecha_hasta != null) {
            $conexion->where('ctacte.fechavenc >= "' . $fecha_desde . '" AND ctacte.fechavenc <="' . $fecha_hasta . '"');
        }
        if (count($arrSort) > 0) {
            $conexion->order_by($arrSort['campo'], $arrSort['orden']);
        }
        $retorno = Vctacte::listarCtacte($conexion, null, $arrLimit, null, null, $contar);
        return $retorno;
    }

    /*
    * Este metodo tiene que emitir:
    *    -Los boletos por emitir. 
    *
    *    -Los juros?
    */

    static function getCtaCteRematriculaciones($conexion = null, $desde, $hasta, $alumnos,  $cod_alumno = null, $orden = null, $wherein = null, $arrCondiciones = null, 
            $arrLimit = null, $contar = false, $arrSort = null, $conrazonsocial = false, $whereInAlumnos = null, $sinBoletoEmitido = true, 
            $separador = null, $fecha_desde = null, $fecha_hasta = null, $codMatricula = null) {
        $conexion->select("(ctacte.importe - IFNULL((SELECT SUM(ctacte_imputaciones.valor)
                                        FROM ctacte_imputaciones
                                        WHERE ctacte_imputaciones.cod_ctacte = ctacte.codigo
                                        AND ctacte_imputaciones.estado IN ('confirmado', 'pendiente')),0)) AS saldocobrar", false);
        $conexion->having('saldoCobrar > 0');
        if (!$sinBoletoEmitido) {
            $conexion->select("IF ((SELECT bancos.boletos_bancarios.numero_documento 
                                        FROM bancos.boletos_bancarios 
                                        WHERE bancos.boletos_bancarios.numero_documento = ctacte.codigo
                                        AND bancos.boletos_bancarios.cod_filial = {$conexion->database} LIMIT 0, 1
                                        ) IS NULL, 0, 1) 
                                        AS con_boleto", false);
            $conexion->having("con_boleto = 0");
        }
        if ($conrazonsocial == true) {
            $nombreApellido = formatearNomApeQuery();
            $conexion->select("CONCAT($nombreApellido) as nombre_apellido", false);
            $conexion->join("alumnos", "alumnos.codigo = ctacte.cod_alumno");
            $conexion->join("alumnos_razones", "alumnos_razones.cod_alumno = ctacte.cod_alumno AND default_facturacion = 1");
        }
        if ($cod_alumno != '') {
            $conexion->where('ctacte.cod_alumno', $cod_alumno);
        }
        if ($codMatricula != null){
            $conexion->where("ctacte.concepto", $codMatricula);
        }
        if ($wherein != null) {
            $conexion->where_in('ctacte.codigo', $wherein);
        }
        if ($whereInAlumnos != null) {
            $conexion->where_in('ctacte.cod_alumno', $whereInAlumnos);
        }
        $conexion->where('ctacte.habilitado in (1,2)');
        $conexion->where('ctacte.cod_concepto = 1');
        $conexion->where("ctacte.fechavenc between '$desde' and '$hasta'");
        $conexion->where('ctacte.concepto in (' .  implode(',', $alumnos) . ')');
        if (count($arrCondiciones) > 0) {
            $arrTemp = array();
            foreach ($arrCondiciones as $key => $value) {
                if ($key == 'nombre_apellido') {
                    $arrTemp[] = "REPLACE(nombre_apellido, '$separador ',' ') LIKE REPLACE('%$value%', '$separador ',' ')";
                } else {
                    $arrTemp[] = "$key LIKE '%$value%'";
                }
            }
            if (count($arrTemp) > 0) {
                $having = "(" . implode(" OR ", $arrTemp) . ")";
                $conexion->having($having);
            }
        }
        if ($fecha_desde != null && $fecha_hasta != null) {
            $conexion->where('ctacte.fechavenc >= "' . $fecha_desde . '" AND ctacte.fechavenc <="' . $fecha_hasta . '"');
        }
        if (count($arrSort) > 0) {
            $conexion->order_by($arrSort['campo'], $arrSort['orden']);
        }
        $retorno = Vctacte::listarCtacte($conexion, null, $arrLimit, null, null, $contar);
        return $retorno;
    }

    static function getEstadoRematriculacion($conexion, $matricula){
        $conexion->select("(ctacte.importe - IFNULL((SELECT SUM(ctacte_imputaciones.valor)
                                        FROM ctacte_imputaciones
                                        WHERE ctacte_imputaciones.cod_ctacte = ctacte.codigo
                                        AND ctacte_imputaciones.estado IN ('confirmado', 'pendiente')),0)) AS saldo_cobrar", false);
        $conexion->select("IF ((SELECT bancos.boletos_bancarios.numero_documento 
                                    FROM bancos.boletos_bancarios 
                                    WHERE bancos.boletos_bancarios.numero_documento = ctacte.codigo
                                    AND bancos.boletos_bancarios.cod_filial = {$conexion->database} LIMIT 0, 1
                                    ) IS NULL, 0, 1) 
                                    AS con_boleto", false);
        $conexion->select("IF (ctacte.fechavenc < NOW(), 1 , 0) AS vencida", false);
        $conexion->where("ctacte.concepto", $matricula);
        $conexion->where('ctacte.habilitado in (1,2)');
        $conexion->where('ctacte.cod_concepto = 1');
//        $conexion->where("ctacte.fechavenc between '$desde' and '$hasta'");
        $conexion->having("saldo_cobrar > 0");
        $retorno = Vctacte::listarCtacte($conexion, null, null, null, null, null);
        return $retorno;

    }

    static function getCtaCteFacturarCobrar($conexion = null, $condiciones = null, $orden = null, $wherein = null) {
        $conexion->select('IFNULL(ROUND(SUM(facturas_renglones.importe),2),0)', FALSE);
        $conexion->from('facturas_renglones');
        $conexion->where('ctacte.codigo = facturas_renglones.cod_ctacte ');
        $conexion->where('facturas_renglones.anulada', 0);
        $subQuery = $conexion->return_query();
        $conexion->resetear();
        $conexion->select('IFNULL(ROUND(SUM(ctacte_imputaciones.valor),2),0)', FALSE);
        $conexion->from('ctacte_imputaciones');
        $conexion->where('ctacte_imputaciones.cod_ctacte = ctacte.codigo ');
        $conexion->where('ctacte_imputaciones.estado <>', "anulado");
        $subQuery2 = $conexion->return_query();
        $conexion->resetear();
        $conexion->select('ctacte.*, (ctacte.importe - (' . $subQuery . ')) AS saldofacturar, (ctacte.importe - (' . $subQuery2 . ')) AS saldocobrar', FALSE);
        $conexion->select('ctacte.codigo');
        $conexion->from('ctacte');
        if (count($condiciones) > 0) {
            foreach ($condiciones as $key => $value) {
                $conexion->where($key, $value);
            }
        }
        if ($wherein != null) {
            $conexion->where_in('codigo', $wherein);
        }
        $conexion->having('saldofacturar >', '0');
        $conexion->having('saldocobrar >', '0');
        if ($orden != null) {
            $arrOrder = array();
            foreach ($orden as $value) {
                $arrOrder[] = $value['campo'] . " " . $value['orden'];
            }
            $orderBy = implode(", ", $arrOrder);
            $conexion->order_by($orderBy);
        }
        $query = $conexion->get();
        $arrCtaCte = $query->result_array();
        return $arrCtaCte;
    }

    public function getSumImporteCobrar($conexion, $wherein) {
        $conexion->select('IFNULL(ROUND(SUM(ctacte_imputaciones.valor),2),0)', FALSE);
        $conexion->from('ctacte_imputaciones');
        $conexion->where('ctacte_imputaciones.cod_ctacte = ctacte.codigo');
        $conexion->where('ctacte_imputaciones.estado <> "cancelado"');
        $subQuery = $conexion->return_query();
        $conexion->resetear();
        $conexion->select('ctacte.importe -(' . $subQuery . ') as saldoCobrar', false);
        $conexion->from('ctacte');
        if ($wherein != null) {
            $conexion->where_in('ctacte.codigo', $wherein);
        }
        $conexion->having('saldoCobrar <> 0');
        $query = $conexion->get();

        $resultado = $query->result_array();
        $total = 0;
        foreach ($resultado as $value) {
            $saldoFacCob = $value['saldoCobrar'];
            $total = $total + $saldoFacCob;
        }
        return $total;
    }

    static function totalNotaCredito($conexion, $wherein) {
        $conexion->select('sum(ctacte.pagado)');
        $conexion->from('ctacte');
        $conexion->where('ctacte.habilitado', 1);
        $conexion->where_in('ctacte.codigo', $wherein);
        $query = $conexion->get();
        return $query->result_array();
    }

    /**
     * retorna el total imputado a un registro de ctacte
     * 
     * @param string $fechaHasta    fecha hasta donde debe considerarse el cobro
     * @return double
     */
    public function getNetoImputado($fechaHasta = null) {
        $this->oConnection->select("IFNULL(SUM(ctacte_imputaciones.valor), 0) AS imputado", false);
        $this->oConnection->from("ctacte_imputaciones");
        $this->oConnection->join("cobros", "cobros.codigo = ctacte_imputaciones.cod_cobro");
        $this->oConnection->where("ctacte_imputaciones.cod_ctacte", $this->codigo);
        $this->oConnection->where("ctacte_imputaciones.estado", "confirmado");
        $this->oConnection->where("cobros.estado", "confirmado");
        if ($fechaHasta != null)
            $this->oConnection->where("date(cobros.fechareal) <=", $fechaHasta);
        $query = $this->oConnection->get();
        $arrResp = $query->result_array();
        return $arrResp[0]['imputado'];
    }

    public function getComentarios() {
        $condicion = array('id_ctacte' => $this->codigo, 'baja' => 0);
        $comentarios = Vctacte_comentarios::listarCtacte_comentarios($this->oConnection, $condicion);
        return $comentarios;
    }

    public function guardarComentario($datos) {
        $comentario = new Vctacte_comentarios($this->oConnection);
        $datos['id_ctacte'] = $this->codigo;
        $datos['baja'] = 0;
        $comentario->setCtacte_comentarios($datos);
        $estado['estado'] = $comentario->guardarCtacte_comentarios();
        if ($estado == true) {
            $condicion = array('codigo' => $comentario->getCodigo(), 'baja' => 0);
            $arrComentarios = Vctacte_comentarios::listarCtacte_comentarios($this->oConnection, $condicion);
            $estado['obj'] = $arrComentarios[0];
        }
        return $estado;
    }

    public function getSaldo() {
        return $this->importe - $this->pagado;
    }

    public function bajaMorasCtaCte() {
        $morasasociadas = $this->getMoras();
        $resultado = '';
        foreach ($morasasociadas as $ctacte) {
            $ctacteas = new Vctacte($this->oConnection, $ctacte['codigo']);
            $resultado = $ctacteas->baja(false);
        }
        return $resultado;
    }

    public function getMoras($debe = false) {
        if ($this->cod_concepto != 3) {
            $this->oConnection->select('*, (ctacte.importe - ctacte.pagado) AS saldo');
            $this->oConnection->from('ctacte');
            $this->oConnection->where('ctacte.cod_concepto', 3);
            if ($debe) {
                $this->oConnection->where('ctacte.importe > ctacte.pagado');
            }
            $this->oConnection->where('ctacte.concepto', $this->codigo);
            $query = $this->oConnection->get();
            return $query->result_array();
        } else {
            return array();
        }
    }

    public function alta($motivo = null, $comentario = null, $cod_usuario = null) {
        $respuesta = '';
        if ($this->habilitado <> '1') {
            $this->habilitado = '1';
            $this->guardarCtacte();
            $estadosHistoricos = new Vctacte_estado_historico($this->oConnection);
            $arrayGuardarEstadoHistorico = array(
                "cod_ctacte" => $this->codigo,
                "estado" => $this->habilitado,
                "motivo" => $motivo,
                "fecha_hora" => date('Y-m-d H:i:s'),
                "comentario" => $comentario,
                "cod_usuario" => $cod_usuario
            );
            $estadosHistoricos->setCtacte_estado_historico($arrayGuardarEstadoHistorico);
            $respuesta = $estadosHistoricos->guardarCtacte_estado_historico();
        }
        return $respuesta;
    }

    public function guardar($codalumno, $nrocuota, $importe, $fechavenc, $habilitado = null, $pagado = null, $codconcepto = null, $concepto = null, $financiacion = null) {
        $this->cod_alumno = $codalumno;
        $this->nrocuota = $nrocuota;
        $this->importe = $importe;
        $this->fechavenc = $fechavenc;
        $this->habilitado = $habilitado != null ? $habilitado : 1;
        $this->pagado = $pagado != null ? $pagado : 0;
        $this->cod_concepto = $codconcepto;
        $this->concepto = $concepto;
        $this->financiacion = $financiacion;
        $this->fecha_creacion = date('Y-m-d H:i:s');
        return $this->guardarCtacte();
    }

    /* STATIC FUNCTIONS */

    static function getReporteCobros(CI_DB_mysqli_driver $conexion, $arrLimit = null, $arrSort = null, $contar = false, 
            $search = null, array $searchFields = null, $fechaDesde = null, $fechaHasta = null, $idCurso = null, 
            $idConcepto = null, $soloConDeuda = false, $codMatricula = null, $estado = null) {
        $aColumns = array();
        $aColumns['codigo']['order'] = "alumnos.codigo";
        $aColumns['alumno_nombre']['order'] = "alumno_nombre";
        $aColumns['nrocuota']['order'] = "ctacte.nrocuota";
        $aColumns['fecha_vencimiento']['order'] = "ctacte.fechavenc";
        $aColumns['fecha_ultimo_pago']['order'] = "fecha_ultimo_pago_n";
        $aColumns['importe']['order'] = 'ctacte.importe';
        $aColumns['pagado']['order'] = 'ctacte.pagado';
        $aColumns['saldo']['order'] = 'saldo';
        $aColumns['cod_concepto']['order'] = "ctacte.cod_concepto";
        $aColumns['concepto']['order'] = "ctacte.concepto";
        $aColumns['cod_alumno']['order'] = "ctacte.cod_alumno";
        $aColumns['fechavenc']['order'] = "ctacte.fechavenc";
        $aColumns['curso_nombre']['order'] = "curso_nombre";
        $aColumns['mora']['order'] = "mora";
        $aColumns['alumno_telefono']['order'] = "alumno_telefono";
        $aColumns['tipo_deuda']['order'] = "tipo_deuda";
        $aColumns['codigo']['having'] = "alumnos.codigo";
        $aColumns['alumno_nombre']['having'] = "alumno_nombre";
        $aColumns['nrocuota']['having'] = "ctacte.nrocuota";
        $aColumns['fecha_vencimiento']['having'] = "fechavenc";
        $aColumns['fecha_ultimo_pago']['having'] = "fecha_ultimo_pago";
        $aColumns['importe']['having'] = 'ctacte.importe';
        $aColumns['pagado']['having'] = 'ctacte.pagado';
        $aColumns['saldo']['having'] = 'saldo';
        $aColumns['cod_concepto']['having'] = "ctacte.cod_concepto";
        $aColumns['concepto']['having'] = "ctacte.concepto";
        $aColumns['cod_alumno']['having'] = "ctacte.cod_alumno";
        $aColumns['fechavenc']['having'] = "fecha_vencimiento";
        $aColumns['curso_nombre']['having'] = "curso_nombre";
        $aColumns['mora']['having'] = "mora";
        $aColumns['alumno_telefono']['having'] = "alumno_telefono";
        $aColumns['tipo_deuda']['having'] = "tipo_deuda";
 
        $conexion->select("COUNT(DISTINCT facturas_renglones.cod_factura)");
        $conexion->from("facturas_renglones");
        $conexion->where("facturas_renglones.cod_ctacte = ctacte.codigo");
        $queryCantidadFacturas = $conexion->return_query();
        $conexion->resetear();
        $conexion->select("CONCAT(telefonos.codigo, ' ', telefonos.numero)", false);
        $conexion->from("telefonos");
        $conexion->join("alumnos_telefonos", "alumnos_telefonos.cod_telefono = telefonos.codigo");
        $conexion->where("alumnos_telefonos.cod_alumno = ctacte.cod_alumno");
        $conexion->order_by("alumnos_telefonos.cod_telefono", "ASC");
        $conexion->limit(1, 0);
        $queryTelefono = $conexion->return_query();
        $conexion->resetear();

        $conexion->select("IFNULL(SUM(cta1.importe) - SUM(cta1.pagado), 0)", false);
        $conexion->from("ctacte AS cta1");
        $conexion->where("cta1.cod_concepto =", 3);
        $conexion->where("cta1.concepto = ctacte.codigo");
        $conexion->where("cta1.habilitado IN (1,2)");
        $queryMora = $conexion->return_query();
        $conexion->resetear();
        $conexion->select("CONCAT(LPAD(DAY(MAX(ctacte_imputaciones.fecha)), 2, 0), '/', LPAD(MONTH(MAX(ctacte_imputaciones.fecha)), 2, 0), '/', YEAR(MAX(ctacte_imputaciones.fecha)))", false);
        $conexion->from("ctacte_imputaciones");
        $conexion->where("ctacte_imputaciones.cod_ctacte = ctacte.codigo");
        $conexion->where("ctacte_imputaciones.estado =", "confirmado");
        $queryFechaUltimoPago = $conexion->return_query();
        $conexion->resetear();
        $conexion->select("MAX(ctacte_imputaciones.fecha)", false);
        $conexion->from("ctacte_imputaciones");
        $conexion->where("ctacte_imputaciones.cod_ctacte = ctacte.codigo");
        $conexion->where("ctacte_imputaciones.estado =", "confirmado");
        $queryFechaUltimoPagoN = $conexion->return_query();
         
        $conexion->resetear();
       
        $conexion->select("alumnos.codigo");
        $conexion->select("($queryMora) AS mora", false);
        $conexion->select("ctacte.importe");
        $conexion->select("ctacte.pagado");
        $conexion->select("CONCAT(alumnos.apellido, ', ', alumnos.nombre) AS alumno_nombre", false);
        $conexion->select("ctacte.nrocuota"); 
//        if(!$contar){
        $conexion->select("ctacte.codigo AS ctacte_codigo");       
          
        $conexion->select("CONCAT(LPAD(DAY(ctacte.fechavenc), 2, 0), '/', LPAD(MONTH(ctacte.fechavenc), 2, 0), '/', YEAR(ctacte.fechavenc)) AS fecha_vencimiento", false);
        $conexion->select("($queryFechaUltimoPago) AS fecha_ultimo_pago", false);
        $conexion->select("($queryFechaUltimoPagoN) AS fecha_ultimo_pago_n", false);
        $conexion->select("(ctacte.importe - ctacte.pagado + ($queryMora)) AS saldo", false);
        $conexion->select("ctacte.cod_concepto");
        $conexion->select("ctacte.concepto");
        $conexion->select("ctacte.habilitado");
        $conexion->select("ctacte.cod_alumno");
        $conexion->select("ctacte.financiacion");        
        $conexion->select("ctacte.fechavenc");
    
        $conexion->select('IF(ctacte.cod_concepto  = 1 OR ctacte.cod_concepto  = 5, 
            (SELECT general.cursos.nombre_es  FROM general.cursos 
            JOIN general.planes_academicos ON general.planes_academicos.cod_curso = general.cursos.codigo 
            JOIN matriculas on matriculas.cod_plan_academico = general.planes_academicos.codigo 
            WHERE matriculas.codigo = ctacte.concepto ),"-" )  AS curso_nombre', false);        
//        }
        $conexion->select('IF(ctacte.cod_concepto  = 1 OR ctacte.cod_concepto  = 5, 
            (SELECT general.planes_academicos.cod_curso 
                    FROM general.planes_academicos 
                    JOIN matriculas ON matriculas.cod_plan_academico = general.planes_academicos.codigo 
                    WHERE matriculas.codigo = ctacte.concepto ),"0" )  AS curso_codigo', false);
        $conexion->select("($queryTelefono) AS alumno_telefono", false);
        $conexion->select("($queryCantidadFacturas) AS cantidad_facturas");
        $conexion->select("IF(ctacte.habilitado = 1, 'Activa', IF(ctacte.habilitado = 2, 'Pasiva', 'Inactiva')) AS tipo_deuda", false);
                
        $conexion->from("ctacte");
        $conexion->join("alumnos", "alumnos.codigo = ctacte.cod_alumno");
        
        if ($codMatricula != null){
            $conexion->join("matriculas", "matriculas.cod_alumno = alumnos.codigo");
        }
        if ($estado === null){
            $conexion->where("ctacte.habilitado = 1"); // quita deuda pasiva
        } else {
            $conexion->where_in("ctacte.habilitado", $estado);
        }
        $conexion->where("ctacte.importe >", "0");
//        if ($soloConDeuda)
//            $conexion->where("ctacte.importe > ctacte.pagado");
        if ($fechaDesde != null)
            $conexion->where("ctacte.fechavenc >=", $fechaDesde);
        if ($fechaHasta != null)
            $conexion->where("ctacte.fechavenc <=", $fechaHasta);
        if ($idConcepto != null)
            $conexion->where("ctacte.cod_concepto =", $idConcepto);
        if ($codMatricula != null){
            $conexion->where("matriculas.codigo =", $codMatricula);
            $conexion->where("ctacte.concepto", $codMatricula);
        }
        if ($idCurso != null) $conexion->having("curso_codigo", $idCurso);
        $complementoHaving = '';
        
        if ($search != null) {
            $condicionesHaving = array();
            foreach ($aColumns AS $key => $tableFields) {
                if ($searchFields == null || in_array($key, $searchFields)) {
                    $condicionesHaving[] = "{$tableFields['having']} LIKE '%$search%'"; // a la antigua                    
                }
            }
            if (count($condicionesHaving) > 0)
                $complementoHaving = "(" . implode(" OR ", $condicionesHaving) . ")";
            $conexion->having($complementoHaving);
        }
        $conexion->having("mora >= 0");
        if ($soloConDeuda){
            $conexion->having("(importe > pagado OR mora > 0)");
        }
        if (!$contar) {
            if ($arrLimit != null && is_array($arrLimit))
                $conexion->limit($arrLimit[1], $arrLimit[0]);
            if ($arrSort != null && is_array($arrSort) && isset($aColumns[$arrSort[0]]['order']))
                $conexion->order_by($aColumns[$arrSort[0]]['order'], $arrSort[1]);
        }
        $query = $conexion->get();
        

        if ($contar)
            return $query->num_rows();
        else
            return $query->result_array();
    }

    static function getSumSaldo($conexion, $wherein) {
        $conexion->select('SUM(ctacte.importe - ctacte.pagado) as suma');
        $conexion->from('ctacte');
        $conexion->where_in('ctacte.codigo', $wherein);
        $query = $conexion->get();
        return $query->result_array();
    }

    static function getDeudoresCtacte(CI_DB_mysqli_driver $conexion, $agruparDeudas = null, $cod_alumno = null, $wherein = null, $arrCondindiciones = null, $arrLimit = null, $arrSort = null, $contar = false, $separador = null) {
        $nombreApellido = formatearNomApeQuery();
        if (!$contar) {
            $conexion->select("ifnull(count(alerta_alumno_configuracion.valor),0)", false);
            $conexion->from('alerta_alumno_configuracion');
            $conexion->where('alerta_alumno_configuracion.`key`', 'cod_ctacte');
            $conexion->where('ctacte.codigo = alerta_alumno_configuracion.valor');
            $subquery1 = $conexion->return_query();
            $conexion->resetear();
            $conexion->select("($subquery1) as CantidadAlertado, ctacte.codigo, ctacte.cod_alumno, CONCAT($nombreApellido) as nombre_apellido, ctacte.nrocuota, ctacte.importe, ctacte.pagado,ctacte.fechavenc, ctacte.cod_concepto, ctacte.concepto, ctacte.financiacion,(ctacte.importe - ctacte.pagado) as saldo", false);
            $conexion->select("IF(alumnos.email = ' ' or alumnos.email IS NULL,0,1) as tienemail", false);
        } else {
            $conexion->select("ctacte.codigo");
        }
        $conexion->from('ctacte');
        $conexion->join('alumnos', 'alumnos.codigo = ctacte.cod_alumno');
        $conexion->join('matriculas', 'matriculas.cod_alumno = alumnos.codigo AND matriculas.estado <> "inhabilitada"');
        $conexion->join('matriculas_periodos', 'matriculas_periodos.cod_matricula = matriculas.codigo 
                         AND (matriculas_periodos.estado == "habilitada" OR matriculas_periodos.estado == "finalizada")');
        if ($agruparDeudas != null) {
            $conexion->select("(SELECT SUM(ct1.importe) - SUM(ct1.pagado) FROM ctacte as ct1 WHERE ct1.fechavenc < curdate()  AND ct1.cod_alumno = ctacte.cod_alumno AND ct1.cod_concepto <> 3 AND ct1.habilitado IN(1,2)) as deudaTotal", FALSE);
        }
        if ($cod_alumno != null) {
            $conexion->where('ctacte.cod_alumno', $cod_alumno);
        }
        $conexion->where('ctacte.importe > ctacte.pagado ');
        $conexion->where('ctacte.fechavenc < curdate()');
        $conexion->where('ctacte.habilitado IN(1,2)');
        if ($wherein != null) {
            $conexion->where_in('ctacte.cod_alumno', $wherein);
            $conexion->group_by('ctacte.codigo');
        }
        if ($agruparDeudas != null) {
            $conexion->group_by('ctacte.cod_alumno');
        }
        if ($arrCondindiciones == null) {
            $arrCondindiciones = array();
        }
        if (count($arrCondindiciones) > 0) {
            $arrTemp = array();
            foreach ($arrCondindiciones as $key => $value) {
                if ($key == 'nombre_apellido') {
                    $arrTemp[] = "REPLACE(nombre_apellido, '$separador ',' ') LIKE REPLACE('%$value%', '$separador ',' ')";
                } else {
                    $arrTemp[] = "$key LIKE '%$value%'";
                }
            }
            if (count($arrTemp) > 0) {
                $having = "(" . implode(" OR ", $arrTemp) . ")";
                $conexion->having($having);
            }
        }
        if ($arrLimit != null) {
            $conexion->limit($arrLimit[1], $arrLimit[0]);
        }

        if ($arrSort != null) {
            $conexion->order_by($arrSort["0"], $arrSort["1"]);
        }
        $query = $conexion->get();
        if ($contar) {
            $arrResp = count($query->result_array());
        } else {
            $arrResp = $query->result_array();
        }
        return $arrResp;
    }

    public function perderDescuentoCondicionado() {
        $conexion = $this->oConnection;
        $arrTemp = array("estado" => "condicionado_perdido");
        $conexion->where("cod_ctacte", $this->codigo);
        $conexion->where("estado", "condicionado");
        return $conexion->update("matriculaciones_ctacte_descuento", $arrTemp);
    }

    static function getMatriculacionesCtacte(CI_DB_mysqli_driver $conexion, $estado = null, $fechaDesde = null, $fechaHasta = null, $conPagos = null, $conImporte = null, $perdidos = null) {
        $conexion->select("ctacte.*");
        $conexion->select("matriculaciones_ctacte_descuento.descuento");
        $conexion->select("matriculaciones_ctacte_descuento.estado");
        $conexion->select("matriculaciones_ctacte_descuento.dias_vencido");
        $conexion->from("matriculaciones_ctacte_descuento");
        $conexion->join("ctacte", "ctacte.codigo = matriculaciones_ctacte_descuento.cod_ctacte");
        $conexion->where("ctacte.habilitado IN (1,2)");
        if ($estado != null)
            $conexion->where("matriculaciones_ctacte_descuento.estado", $estado);
        if ($fechaDesde != null)
            $conexion->where("ctacte.fechavenc >=", $fechaDesde);
        if ($fechaHasta != null)
            $conexion->where("ctacte.fechavenc <=", $fechaHasta);
        if ($conPagos !== null) {
            if ($conPagos) {
                $conexion->where("ctacte.pagado >", 0);
            } else {
                $conexion->where("ctacte.pagado", 0);
            }
        }
        if ($conImporte !== null) {
            if ($conImporte) {
                $conexion->where("ctacte.importe >", 0);
            } else {
                $conexion->where("ctacte.importe =", 0);
            }
        }
        if ($perdidos !== null) {
            $conexion->where("(DATE_ADD(ctacte.fechavenc,INTERVAL matriculaciones_ctacte_descuento.dias_vencido DAY))  < CURDATE()");
        }
        $query = $conexion->get();
        return $query->result_array();
    }

    /**
     * Retorna un objeto ctacte representante de la mora del actual registro de ctacte
     * 
     * @return \Vctacte
     */
    public function getObjectMora() {
        $this->oConnection->select("codigo");
        $this->oConnection->from("ctacte");
        $this->oConnection->where("concepto", $this->codigo);
        $this->oConnection->where("cod_concepto", 3);
        $query = $this->oConnection->get();
        $arrTemp = $query->result_array();
        $codigo = isset($arrTemp[0]['codigo']) && $arrTemp[0]['codigo'] > 0 ? $arrTemp[0]['codigo'] : null;
        $myCtacte = new Vctacte($this->oConnection, $codigo);
        if ($myCtacte->codigo == -1) {
            $myCtacte->cod_alumno = $this->cod_alumno;
            $myCtacte->cod_concepto = 3;
            $myCtacte->concepto = $this->codigo;
            $myCtacte->financiacion = 1;
            $myCtacte->habilitado = 1;
            $myCtacte->nrocuota = 1;
            $myCtacte->pagado = 0;
            $myCtacte->fecha_creacion = date("Y-m-d H:i:s");
        }
        return $myCtacte;
    }

    public function getImporteNotaCredito() {        
        $this->oConnection->select("ctacte.pagado - ifnull(sum(notas_credito_renglones.valor),0) as SaldoGenerarCredito", false);
        $this->oConnection->from('ctacte');
        $this->oConnection->join('notas_credito_renglones', 'notas_credito_renglones.cod_cta_cte = ctacte.codigo', 'left');
        $this->oConnection->join('medio_notas_credito', 'medio_notas_credito.codigo = notas_credito_renglones.cod_nota_credito', 'left');
        $this->oConnection->where('ctacte.codigo', $this->codigo);
        $this->oConnection->where('ctacte.pagado > 0');
        $query = $this->oConnection->get();
        return $query->result_array();
    }

    public function getHabilitarFacturasCtaCte() {
        $this->oConnection->select('count(facturas_renglones.cod_factura) as habilitar');
        $this->oConnection->from('facturas_renglones');
        $this->oConnection->where('facturas_renglones.cod_ctacte', $this->codigo);
        $query = $this->oConnection->get();
        return $query->result_array();
    }

    public function getUltimaFechaMoraAplicada() {
        $this->oConnection->select("MAX(fecha) AS ultima_mora");
        $this->oConnection->from("ctacte_moras");
        $this->oConnection->where("cod_ctacte", $this->codigo);
        $query = $this->oConnection->get();
        $arrResp = $query->result_array();
        return $arrResp[0]['ultima_mora'];
    }

    /**
     * Culcula, aplica y registra la mora del registro de ctacte
     * 
     * @param boolean 
     */
    public function aplicarMora(array $arrMoras = null) {  // buscar para una ctacte todoas las moras que deben aplicarse (pueden aplicarse mas de una si es que se superponen las configuraciones en moras)
        if ($arrMoras == null) {
            $arrMoras = Vmoras::listarMoras($this->oConnection, array("baja" => 0));
        }
        $fechaVencimiento = $this->fechavenc;
        if ($fechaVencimiento < date("Y-m-d")) {
            $diferencia = func_fechas::diferenciaEntreFechas($fechaVencimiento, date("Y-m-d"), "DIAS", true);
            foreach ($arrMoras as $mora) {
                if ($mora['diariamente'] == 1) { // $aplica la mora diariamente                
                    if ($mora['dia_desde'] == $mora['dia_hasta']) { // cunado es diariamente y dia_desde = dia_hasta, debe calcularse mora todos los dias hasta que se paque la deuda
                        $fechaHasta = $diferencia;
                    } else {
                        $fechaHasta = $mora['dia_hasta'] > $diferencia ? $diferencia : $mora['dia_hasta'];
                    }
                    for ($i = 0; $i < $fechaHasta; $i++) { // debemos ver si existen todos los registros, pueden haber saltos si el crons no se ejecuto correctamente
                        $diaDesde = $mora['dia_desde'] + $i;
                        $fechaMora = date("Y-m-d", strtotime("$diaDesde day", strtotime($fechaVencimiento)));
                        if ($fechaMora < date("Y-m-d")) {
                            $myCtacteMora = new Vctacte_moras($this->oConnection, $this->codigo, $fechaMora, $mora['codigo']);
                            $valor = $mora['mora'];
                            if ($mora['es_porcentaje'] == 0) {
                                $precio = $valor;
                            } else {
                                $totalImputado = $this->getNetoImputado($fechaMora);
                                $precio = ($this->importe - $totalImputado) * $valor / 100; // el porcentaje debe aplicarse sobre la deuda (descontando la parte ya pagada)
                            }
                            if ($precio > 0) {
                                $myCtacteMora->precio = $precio;
                                $myCtacteMora->fecha_creacion = date("Y-m-d H:i:s");
                                $myCtacteMora->guardar();
                            }
                        }
                    }
                } else { // aplica la mora sobre el inicio del periodo de fechas
                    $diaDesde = $mora['dia_desde'];
                    $fechaMora = date("Y-m-d", strtotime("$diaDesde day", strtotime($this->fechavenc)));
                    $myCtacteMora = new Vctacte_moras($this->oConnection, $this->codigo, $fechaMora, $mora['codigo']);

                    if ($diferencia >= $mora['dia_desde'] ) { // si no se guardado la mora aun && !$myCtacteMora->exists()
                        $valor = $mora['mora'];
                        if ($mora['es_porcentaje'] == 0) {
                            $precio = $valor;
                        } else {
                            $totalImputado = $this->getNetoImputado($fechaMora);

                            $precio = ($this->importe - $totalImputado) * $valor / 100; // el porcentaje debe aplicarse sobre la deuda (descontando la parte ya pagada)
                        }
                        if ($precio > 0) {
                            $myCtacteMora->precio = $precio;
                            $myCtacteMora->fecha_creacion = date("Y-m-d H:i:s");
                            $myCtacteMora->guardar();
                        }
                    }
                }
            }
            $importeTotal = Vctacte_moras::getCtacteMora($this->oConnection, array("cod_ctacte" => $this->codigo), true);
            if ($importeTotal > 0) {
                $myCtacte = $this->getObjectMora();
                $myCtacte->importe = $importeTotal;
                $myCtacte->habilitado = 1;
                $myCtacte->fecha_creacion = date('Y-m-d H:i:s');
                $arrDescuentos = $myCtacte->getDescuentos();
                $importeDescontar = 0;
                foreach ($arrDescuentos as $descuento){
                    $importeDescontar += $descuento['importe'];
                }
                $myCtacte->importe -= $importeDescontar;
                if ($myCtacte->importe < 0){
                    $myCtacte->importe = 0;
                }
                $myCtacte->guardarCtacte();
            }
        }
    }

    public function getSaldoFacturar() {
        $conexion = $this->oConnection;
        $conexion->select('IFNULL(SUM(facturas_renglones.importe),0) as facturado', FALSE);
        $conexion->from('facturas_renglones');
        $conexion->where('facturas_renglones.cod_ctacte', $this->codigo);
        $conexion->where('facturas_renglones.anulada', 0);
        $query = $conexion->get();
        $arrResp = $query->result_array();
        return $this->importe - $arrResp[0]['facturado'];
    }

    static function getSumImporteFacturar($conexion, $wherein) {
        $conexion->select('IFNULL(SUM(facturas_renglones.importe),0)', FALSE);
        $conexion->from('facturas_renglones');
        $conexion->where('ctacte.codigo = facturas_renglones.cod_ctacte ');
        $conexion->where('facturas_renglones.anulada', 0);
        $subQuery = $conexion->return_query();
        $conexion->resetear();
        $conexion->select('(ctacte.importe - (' . $subQuery . ')) AS saldofacturar', FALSE);
        $conexion->select('ctacte.codigo');
        $conexion->from('ctacte');
        $conexion->where_in('codigo', $wherein);
        $query = $conexion->get();
        $resultado = $query->result_array();
        $total = 0;
        foreach ($resultado as $value) {
            $saldoFacturar = $value['saldofacturar'];
            $total = $total + $saldoFacturar;
        }
        return $total;
    }

    static function getCtaCteNotaCredito(CI_DB_mysqli_driver $conexion, $cod_alumno) {
        $conexion->select(' IFNULL(SUM(notas_credito_renglones.valor), 0)', false);
        $conexion->from('notas_credito_renglones');
        $conexion->join('medio_notas_credito', 'medio_notas_credito.codigo = notas_credito_renglones.cod_nota_credito and  medio_notas_credito.estado ="confirmado"');
        $conexion->where('notas_credito_renglones.cod_cta_cte = ctacte.codigo');
        $subquery = $conexion->return_query();
        $conexion->resetear();
        $conexion->select("ctacte.*, ctacte.pagado - ($subquery) as saldoNotaCredito", false);
        $conexion->from('ctacte');
        $conexion->where('ctacte.cod_alumno', $cod_alumno);
        $conexion->where('ctacte.pagado > 0');
        $query = $conexion->get();
        return $query->result_array();
    }

    /* esta fnuction esta siendo accedida desde un web services */
    static function getReporteSeguimientoFiliales(CI_DB_mysqli_driver $conexion) {
        $anioAnterior = date("Y") - 1;
        $anioActual = date("Y");
        $mesAnterior = sumarMeses(date("Y-m-01"), "-2");
        $conexion->select("ctacte.codigo");
        $conexion->from("matriculas");
        $conexion->join("matriculas_periodos", "matriculas_periodos.cod_matricula = matriculas.codigo");
        $conexion->join("ctacte", "ctacte.cod_alumno = matriculas.cod_alumno");
        $conexion->where_in("matriculas.cod_plan_academico", array(1, 12));
        $conexion->where("matriculas.fecha_emision >=", "{$anioAnterior}-06-01");
        $conexion->where("matriculas.fecha_emision <", "{$anioActual}-07-01");
        $conexion->where("matriculas_periodos.cod_tipo_periodo", 1);
        $conexion->where("matriculas_periodos.estado", Vmatriculas_periodos::getEstadoHabilitada());
        $conexion->where("ctacte.fechavenc >=", $mesAnterior);
        $conexion->where("ctacte.habilitado", 1);
        $conexion->where("ctacte.cod_concepto", 1);
        $conexion->where("ctacte.pagado = ctacte.importe");
        $conexion->group_by("matriculas.codigo");
        $query = $conexion->get();
        return $query->num_rows();
    }

    public function bajaRefinanciacion($motivo = null, $comentario = null, $cod_usuario = null) {
        $this->habilitado = '3';
        $this->guardarCtacte();
        $estadosHistoricos = new Vctacte_estado_historico($this->oConnection);
        $arrayGuardarEstadoHistorico = array(
            "cod_ctacte" => $this->codigo,
            "estado" => $this->habilitado,
            "motivo" => $motivo,
            "fecha_hora" => date('Y-m-d H:i:s'),
            "comentario" => $comentario,
            "cod_usuario" => $cod_usuario
        );
        $estadosHistoricos->setCtacte_estado_historico($arrayGuardarEstadoHistorico);
        $respuesta = $estadosHistoricos->guardarCtacte_estado_historico();
        return $respuesta;
    }

    public function setPasiva($morasPasivas = true, $motivo = null, $comentario = null, $cod_usuario = null) {
        $this->habilitado = '2';
        $this->guardarCtacte();
        $estadosHistoricos = new Vctacte_estado_historico($this->oConnection);
        $arrayGuardarEstadoHistorico = array(
            "cod_ctacte" => $this->codigo,
            "estado" => $this->habilitado,
            "motivo" => $motivo,
            "fecha_hora" => date('Y-m-d H:i:s'),
            "comentario" => $comentario,
            "cod_usuario" => $cod_usuario
        );
        $estadosHistoricos->setCtacte_estado_historico($arrayGuardarEstadoHistorico);
        $respuesta = $estadosHistoricos->guardarCtacte_estado_historico();
        if ($morasPasivas) {
            $this->MorasPasivasCtaCte();
        }
        return $respuesta;
    }

    public function MorasPasivasCtaCte() {
        $morasasociadas = $this->getMoras();
        $resultado = '';
        foreach ($morasasociadas as $ctacte) {
            $ctacteas = new Vctacte($this->oConnection, $ctacte['codigo']);
            $resultado = $ctacteas->setPasiva(false);
        }
        return $resultado;
    }

    /*
     * El orden de prioridad de imputacion se define con el modulo de boletos bancarios y esto es:
     * primero imputar a la cuenta corriente seleccionada. (si es que existe saldo a imputar)
     * luego imputar a su mora (si es que tiene)
     * luego imputar desde las primeras cuotas disponibles sobre el concepto de la ctacte buscada (ordenado por orden de registro ya que puede tener o no fecha de vencimiento)
     * por ultimo imputar desde cualquier otro concepto (ordenado por orden de registro)
     */
    static public function getCtaCteOredenPrioridadImputacion(CI_DB_mysqli_driver $conexion, $codigoAlumno, $codigoCtactePrioridad) {
        $arrResp = array();
        $arrRegistrados = array();
        $conexion->select("codigo");
        $conexion->select("importe");
        $conexion->select("pagado");
        $conexion->select("concepto");
        $conexion->from("ctacte");
        $conexion->where("codigo", $codigoCtactePrioridad);
        $query = $conexion->get();
        $arrTemp = $query->result_array();
        if (count($arrTemp) > 0) {
            $codigoCtacte = $arrTemp[0]['codigo'];
            $concepto = $arrTemp[0]['concepto'];
            if ((float) ($arrTemp[0]['pagado']) < (float) ($arrTemp[0]['importe'])) {
                $pos = count($arrResp);
                $arrResp[$pos]['codigo'] = $codigoCtacte;
                $arrResp[$pos]['importe'] = $arrTemp[0]['importe'];
                $arrResp[$pos]['pagado'] = $arrTemp[0]['pagado'];
                $arrRegistrados[] = $codigoCtacte;
            }
            /* bnuscando la deuda en mora correspondiente al registro de ctacte solicitado */
            $conexion->select("codigo");
            $conexion->select("importe");
            $conexion->select("pagado");
            $conexion->from("ctacte");
            $conexion->where("cod_concepto", 3);
            $conexion->where("concepto", $codigoCtacte);
            $conexion->where("pagado < importe");
            $query = $conexion->get();
            $arrTemp = $query->result_array();
            if (count($arrTemp) > 0) {
                foreach ($arrTemp as $ctacte) { // por si en algun momento se puede tener mas de una linea de mora por ctacte
                    $pos = count($arrResp);
                    $arrResp[$pos]['codigo'] = $ctacte['codigo'];
                    $arrResp[$pos]['importe'] = $ctacte['importe'];
                    $arrResp[$pos]['pagado'] = $ctacte['pagado'];
                    $arrRegistrados[] = $ctacte['codigo'];
                }
            }
            /* buscando las ctacte asociadas al concepto */
            $conexion->select("codigo");
            $conexion->select("importe");
            $conexion->select("pagado");
            $conexion->from("ctacte");
            $conexion->where("cod_alumno", $codigoAlumno);
            $conexion->where("concepto", $concepto);
            $conexion->where("pagado < importe");
            if (count($arrRegistrados) > 0) {
                $conexion->where_not_in("codigo", $arrRegistrados);
            }
            $query = $conexion->get();
            $arrTemp = $query->result_array();
            foreach ($arrTemp as $ctacte) {
                $pos = count($arrResp);
                $arrResp[$pos]['codigo'] = $ctacte['codigo'];
                $arrResp[$pos]['pagado'] = $ctacte['pagado'];
                $arrResp[$pos]['importe'] = $ctacte['importe'];
                $arrRegistrados[] = $ctacte['codigo'];
            }
            /* buscando las ctacte asociadas a otros conceptos */
            $conexion->select("codigo");
            $conexion->select("importe");
            $conexion->select("pagado");
            $conexion->from("ctacte");
            $conexion->where("cod_alumno", $codigoAlumno);
            $conexion->where("pagado < importe");
            if (count($arrRegistrados) > 0) {
                $conexion->where_not_in("codigo", $arrRegistrados);
            }
            $query = $conexion->get();
            $arrTemp = $query->result_array();
            foreach ($arrTemp as $ctacte) {
                $pos = count($arrResp);
                $arrResp[$pos]['codigo'] = $ctacte['codigo'];
                $arrResp[$pos]['importe'] = $ctacte['importe'];
                $arrResp[$pos]['pagado'] = $ctacte['pagado'];
            }
        }
        return $arrResp;
    }

    static function getCtaCteCobrar($conexion = null, $condiciones = null, $orden = null, $wherein = null, $whereInAlumnos = null) {
        $conexion->select('IFNULL(ROUND(SUM(ctacte_imputaciones.valor),2),0)', FALSE);
        $conexion->from('ctacte_imputaciones');
        $conexion->where('ctacte_imputaciones.cod_ctacte = ctacte.codigo ');
        $conexion->where('ctacte_imputaciones.estado <>', "anulado");
        $subQuery2 = $conexion->return_query();
        $conexion->resetear();
        $conexion->select('ctacte.*, (ctacte.importe - (' . $subQuery2 . ')) AS saldocobrar', FALSE);
        $conexion->select('ctacte.codigo');
        $conexion->from('ctacte');
        if ($condiciones != null) {
            foreach ($condiciones as $key => $value) {
                $conexion->where($key, $value);
            }
        }
        if ($wherein != null) {
            $conexion->where_in('codigo', $wherein);
        }
        if ($whereInAlumnos != null) {
            $conexion->where_in('ctacte.cod_alumno', $whereInAlumnos);
        }
        $conexion->having('saldocobrar >', '0');
        if ($orden != null) {
            $arrOrder = array();
            foreach ($orden as $value) {
                $arrOrder[] = $value['campo'] . " " . $value['orden'];
            }
            $orderBy = implode(", ", $arrOrder);
            $conexion->order_by($orderBy);
        }
        $query = $conexion->get();
        $arrCtaCte = $query->result_array();
        return $arrCtaCte;
    }

    public function recalcularImporteMora() {
        $importeTotal = Vctacte_moras::getCtacteMora($this->oConnection, array("cod_ctacte" => $this->codigo), true);
        $myCtacte = $this->getObjectMora();
        if ($myCtacte->getCodigo() == '-1') {
            if ($importeTotal != 0) {
                $myCtacte->importe = $importeTotal;
                $myCtacte->guardarCtacte();
            }
        }
        else 
        {
            if($myCtacte->habilitado == 1)
            {
                $condiciones = array('cod_ctacte' => $myCtacte->getCodigo(), 'estado <>' => 'anulado');
                $imputaciones = Vctacte_imputaciones::listarCtacte_imputaciones($this->oConnection, $condiciones);
                $total = 0;
                foreach ($imputaciones as $row) {
                    $total = $total + $row['valor'];
                }
                if ($importeTotal == 0) {
                    if ($total == 0) {
                        $myCtacte->baja(false);
                    } else {
                        $myCtacte->importe = $total;
                        if ($myCtacte->fecha_creacion == ''){
                            $myCtacte->fecha_creacion = date("Y-m-d H:i:s");
                        }
                        $myCtacte->guardarCtacte();
                    }
                } else {
                    $myCtacte->importe = $importeTotal > $total ? $importeTotal : $total;
                    if ($myCtacte->fecha_creacion == ''){
                        $myCtacte->fecha_creacion = date("Y-m-d H:i:s");
                    }
                    $myCtacte->guardarCtacte();
                }
            }
        }    
    }

    public function eliminarMoras($fechadesde = null) {
        $condicion = array("cod_ctacte" => $this->codigo);
        if ($fechadesde != null) {
            $condicion['fecha >='] = $fechadesde;
        }
        $moras = Vctacte_moras::getCtacteMora($this->oConnection, $condicion);
        foreach ($moras as $rowmora) {
            $objctamora = new Vctacte_moras($this->oConnection, $rowmora['cod_ctacte'], $rowmora['fecha'], $rowmora['cod_mora']);
            $objctamora->eliminar();
        }
    }

    public function getMatriculacionesCtacteDto($formaDescuento = null, $estado = null) {
        $this->oConnection->where("matriculaciones_ctacte_descuento.cod_ctacte", $this->codigo);
        if ($formaDescuento != null) {
            $tipoFiltro = is_array($formaDescuento) ? "where_in" : "where";
            $this->oConnection->$tipoFiltro("matriculaciones_ctacte_descuento.forma_descuento", $formaDescuento);
        }
        if ($estado != null) {
            $tipoFiltro = is_array($estado) ? "where_in" : "where";
            $this->oConnection->$tipoFiltro("matriculaciones_ctacte_descuento.estado", $estado);
        }
        return Vmatriculaciones_ctacte_descuento::listarMatriculaciones_ctacte_descuento($this->oConnection);
    }

    static function getCtaCteAFacturar($conexion = null, $condiciones = null, $orden = null, $wherein = null, $whereInAlumnos = null) {
        $conexion->select('IFNULL(ROUND(SUM(facturas_renglones.importe),2),0)', FALSE);
        $conexion->from('facturas_renglones');
        $conexion->where('ctacte.codigo = facturas_renglones.cod_ctacte ');
        $conexion->where('facturas_renglones.anulada', 0);
        $subQuery = $conexion->return_query();
        $conexion->resetear();
        $conexion->select('ctacte.*, (ctacte.importe - (' . $subQuery . ')) AS saldofacturar', FALSE);
        $conexion->from('ctacte');
        if ($condiciones != null) {
            foreach ($condiciones as $key => $value) {
                $conexion->where($key, $value);
            }
        }
        if ($wherein != null) {
            $conexion->where_in('codigo', $wherein);
        }
        if ($whereInAlumnos != null) {
            $conexion->where_in('ctacte.cod_alumno', $whereInAlumnos);
        }
        $conexion->having('saldofacturar > 0');
        if ($orden != null) {
            $arrOrder = array();
            foreach ($orden as $value) {
                $arrOrder[] = $value['campo'] . " " . $value['orden'];
            }
            $orderBy = implode(", ", $arrOrder);
            $conexion->order_by($orderBy);
        }
        $query = $conexion->get();
        $arrCtaCte = $query->result_array();
        return $arrCtaCte;
    }

    static public function getCtactePendiente(CI_DB_mysqli_driver $conexion, $fechaDesde = null, $fechaHasta = null, $habilitado = null, $soloImporte = false) {
        if ($soloImporte) {
            $conexion->select("IFNULL(SUM(importe - pagado), 0) AS pendiente", false);
        } else {
            $conexion->select("*");
        }
        $conexion->from("ctacte");
        $conexion->where("importe > pagado");
        if ($fechaDesde != null)
            $conexion->where("fechavenc >=", $fechaDesde);
        if ($fechaHasta != null)
            $conexion->where("fechavenc <=", $fechaHasta);
        if ($habilitado != null) {
            if (is_array($habilitado))
                $conexion->where_in("habilitado", $habilitado);
            else
                $conexion->where("habilitado", $habilitado);
        }
        $query = $conexion->get();
        $arrResp = $query->result_array();
        if ($soloImporte) {
            return $arrResp[0]['pendiente'];
        } else {
            return $arrResp;
        }
    }

    public function eliminarDescuentos() {
        $this->oConnection->delete('matriculaciones_ctacte_descuento', array('cod_ctacte' => $this->codigo));
    }
    
    static function get_reporte_morosidad(CI_DB_mysqli_driver $conexion, $fechaDesde, $fechaHasta){
        $conexion->_protect_identifiers = FALSE;
        $conexion->select("IFNULL(SUM(ctacte_imputaciones.valor), 0)", false);
        $conexion->from("ctacte_imputaciones");
        $conexion->where("ctacte_imputaciones.cod_ctacte = ctacte.codigo");
        $conexion->where("MONTH(ctacte_imputaciones.fecha) <= MONTH(ctacte.fechavenc)");
        $conexion->where("YEAR(ctacte_imputaciones.fecha) <= YEAR(ctacte.fechavenc)");
        $conexion->where("ctacte_imputaciones.estado", "confirmado");
        $sqImputado = $conexion->return_query();
        $conexion->resetear();
        $conexion->select("IFNULL(SUM(ctacte_imputaciones.valor), 0)", false);
        $conexion->from("ctacte_imputaciones");
        $conexion->where("ctacte_imputaciones.cod_ctacte = ctacte.codigo");
        $conexion->where("ctacte_imputaciones.estado", "confirmado");
        $sqImputadoTotal = $conexion->return_query();
        $conexion->resetear();
        
        $conexion->select("ctacte.importe");
        $conexion->select("ctacte.fechavenc");
        $conexion->select("($sqImputado) AS imputado");
        $conexion->select("($sqImputadoTotal) AS imputado_total");
        $conexion->from("ctacte");
        $conexion->where("ctacte.fechavenc >=", $fechaDesde);
        $conexion->where("ctacte.fechavenc <=", $fechaHasta);
        $conexion->where("ctacte.fechavenc <=", date("Y-m-d"));
        $conexion->where_in("ctacte.habilitado", array(1, 2));
        $sqGrupo = $conexion->return_query();
        $conexion->resetear();
        $query = "SELECT SUM(importe) AS importe, SUM(imputado) AS imputado, SUM(imputado_total) AS imputado_total, MONTH(fechavenc) AS mes ".
                "FROM ($sqGrupo) AS tb1 GROUP BY MONTH(fechavenc)";
        $result = $conexion->query($query);
        $conexion->_protect_identifiers = true;
        return $result->result_array();
    }
    
    static public function resetear_moras(CI_DB_mysqli_driver $conexion){
        $resp = $conexion->query("SET FOREIGN_KEY_CHECKS = 0");
        $resp = $resp && $conexion->query("DELETE FROM ctacte ".
                            "WHERE cod_concepto = 3 ".
                                "AND pagado = 0 ".
                                "AND habilitado = 1 ".
                                "AND codigo NOT IN (".
                                    "SELECT cod_ctacte ".
                                        "FROM facturas_renglones ".
                                        "WHERE habilitado = 1)");
        $resp = $resp && $conexion->query("SET FOREIGN_KEY_CHECKS = 1");
        return $resp;
    }

    static public function checkMorasAlumnoCampusExamenes (CI_DB_mysqli_driver $conexion, $cod_alumno)
    {
        $conexion->select("ifnull(sum(ctacte.importe - ctacte.pagado), 0) as saldo", false);
        $conexion->from("ctacte");
        $conexion->where("ctacte.fechavenc < curdate()");
        $conexion->where("ctacte.habilitado IN(1)");
        $conexion->where("(ctacte.importe - ctacte.pagado)> 0");
        $conexion->where("cod_alumno", $cod_alumno);
        $query = $conexion->get();
        $result = $query->result_array();
        return $result;
    }

    static function get_reporte_morosidad_nuevo(CI_DB_mysqli_driver $conexion, $fechaDesde, $fechaHasta){
        $conexion->_protect_identifiers = FALSE;
        $conexion->select("IFNULL(SUM(ctacte_imputaciones.valor), 0)", false);
        $conexion->from("ctacte_imputaciones");
        $conexion->where("ctacte_imputaciones.cod_ctacte = ctacte.codigo");
        $conexion->where("MONTH(ctacte_imputaciones.fecha) <= MONTH(ctacte.fechavenc)");
        $conexion->where("YEAR(ctacte_imputaciones.fecha) <= YEAR(ctacte.fechavenc)");
        $conexion->where("ctacte_imputaciones.estado", "confirmado");
        $sqImputado = $conexion->return_query();
        $conexion->resetear();
        $conexion->select("IFNULL(SUM(ctacte_imputaciones.valor), 0)", false);
        $conexion->from("ctacte_imputaciones");
        $conexion->where("ctacte_imputaciones.cod_ctacte = ctacte.codigo");
        $conexion->where("ctacte_imputaciones.estado", "confirmado");
        $sqImputadoTotal = $conexion->return_query();
        $conexion->resetear();

        $conexion->select("ctacte.importe");
        $conexion->select("ctacte.fechavenc");
        $conexion->select("($sqImputado) AS imputado");
        $conexion->select("($sqImputadoTotal) AS imputado_total");
        $conexion->from("ctacte");
        $conexion->where("ctacte.fechavenc >=", $fechaDesde);
        $conexion->where("ctacte.fechavenc <=", $fechaHasta);
        $conexion->where("ctacte.fechavenc <=", date("Y-m-d"));
        $conexion->where_in("ctacte.habilitado", array(1, 2));
        $sqGrupo = $conexion->return_query();
        $conexion->resetear();
        $query = "SELECT SUM(importe) AS importe, SUM(imputado) AS imputado, SUM(imputado_total) AS imputado_total, MONTH(fechavenc) AS mes ".
            "FROM ($sqGrupo) AS tb1 GROUP BY MONTH(fechavenc)";
        $result = $conexion->query($query);
        $conexion->_protect_identifiers = true;

        $reporte_morosidad = $result->result_array();
        $return = array();
        $meses = array(1 => 'enero', 2 => 'febrero', 3 => 'marzo', 4 => 'abril', 5 => 'mayo', 6 => 'junio',
            7 => 'julio', 8 => 'agosto', 9 => 'septiembre', 10 => 'octubre', 11 => 'noviembre', 12 => 'diciembre');
        $total_importe = 0;
        $total_imputado = 0;
        $total_saldo = 0;
        $total_imputado_acumulado = 0;

        foreach($reporte_morosidad as $mes){
            $linea = $mes;

            if (isset($meses[($linea['mes'])])) {
                $nombre_mes = lang($meses[($linea['mes'])]);
            }

            $saldo = $linea['importe'] - $linea['imputado'];

            $porcentaje =  intval(100 * (1 - ($linea['imputado'] / $linea['importe'])));
            $porcentaje_total = intval(100 * (1 - ($linea['imputado_total'] / $linea['importe'])));

            $ret['mes'] = $nombre_mes;
            $ret['importe'] = '$ ' . $linea['importe'];
            $ret['imputado'] = '$ ' . $linea['imputado'];
            $ret['saldo'] = '$ ' . $saldo;
            $ret['morosidad'] = "" + $porcentaje . ' %';
            $ret['imputado_total'] = '$ ' .$linea['imputado_total'];
            $ret['morosidad_total'] = "" + $porcentaje_total . ' %';
            array_push($return, $ret);

            $total_importe =  $total_importe + $linea['importe'];
            $total_imputado =  $total_imputado + $linea['imputado'];
            $total_saldo = $total_saldo + $saldo;
            $total_imputado_acumulado =  $total_imputado_acumulado + $linea['imputado_total'];

        }
        $ret['mes'] =  lang('total');
        $ret['importe'] = '$ ' . $total_importe;
        $ret['imputado'] = '$ ' . $total_imputado;
        $ret['saldo'] = '$' . $total_saldo;
        $ret['morosidad'] = "";
        $ret['imputado_total'] = '$ ' .$total_imputado_acumulado;
        $ret['morosidad_total'] = "";
        array_push($return, $ret);

        return $return;
    }
}


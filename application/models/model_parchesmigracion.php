<?php

/**
 * Model_parchesMigracion
 * 
 * Description...
 * 
 * @package model_facturas
 * @author ivan <ivan.sys@gmail.com>
 * @version 1.0.0
 */
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Model_parchesMigracion extends CI_Model {

    var $codigo_filial = 0;
    var $cobrosmodificados = '';

    public function __construct($arg) {
        parent::__construct();

        $this->codigo_filial = $arg["codigo_filial"];
    }

    public function facturasMigradasDesc($codFilial) {
        $conexion = $this->load->database($codFilial, true);
        $objFilial = new Vfiliales($conexion, $codFilial);

        $query = "SELECT *, (SELECT SUM(facturas_renglones.importe) 
                FROM facturas_renglones WHERE facturas_renglones.cod_factura = facturas.codigo) as cant_renglones,
                (SELECT facturas_propiedades.valor FROM facturas_propiedades WHERE facturas_propiedades.cod_factura = facturas.codigo 
                AND facturas_propiedades.propiedad = 'numero_factura') as nro,
                (SELECT alumnos_razones.cod_alumno FROM alumnos_razones WHERE alumnos_razones.cod_razon_social = facturas.codrazsoc) as cod_alumno
                FROM facturas HAVING cant_renglones > facturas.total + 0.3 AND facturas.estado = 'habilitada'";
        $query2 = $conexion->query($query);
        $arrFacturas = $query2->result_array();

        if ($objFilial->pais != 2) {
            $conexion->trans_begin();
            foreach ($arrFacturas as $factura) {
                $codigo_factura = $factura['codigo'];
                $condiciones = array('cod_factura' => $codigo_factura);
                $arrRenglones = Vfacturas_renglones::listarFacturas_renglones($conexion, $condiciones);
                $descuento = $factura['cant_renglones'] - $factura['total']; //FACTURAS CON DESCUENTOS
                $porc_desc = $descuento * 100 / $factura['cant_renglones'];
                $renglonesCobro = array();
                $renglonesCobro2 = array();
                foreach ($arrRenglones as $renglon) {
                    $codRenglon = $renglon['codigo'];
                    $importeRenglon = $renglon['importe'] - ($renglon['importe'] * $porc_desc / 100);
                    $query = "UPDATE facturas_renglones SET importe = $importeRenglon WHERE facturas_renglones.codigo = '$codRenglon'";
                    $query2 = $conexion->query($query);
                    $codctacte = $renglon['cod_ctacte'];
                    $objctacte = new Vctacte($conexion, $codctacte);
//                    if ($objctacte->importe > 0 && $importeRenglon > 0) {
                    if ($importeRenglon > 0) {
                        //$porc_facturado = 100 - ($importeRenglon * 100 / $renglon['importe']);
                        $porc_facturado = $renglon['importe'] * 100 / $objctacte->importe;
                        if ($porc_facturado < 100) {
                            //$importe = $objctacte->importe - ($objctacte->importe * $porc_facturado / 100);
//                            $importe=$objctacte->importe * (100-$porc_facturado) / 100 + $importeRenglon;
//                            $importeCambioPagado = $objctacte->pagado * $porc_facturado / 100;
//                            $importeSinCambioPagado = $objctacte->pagado * (100 - $porc_facturado) / 100;
//                            $pagado = $importeSinCambioPagado + $importeCambioPagado - $importeCambioPagado * $porc_desc / 100;
                            $importeviejo = $objctacte->importe;
                            $query = "SELECT matriculaciones_ctacte_descuento.* FROM matriculaciones_ctacte_descuento WHERE cod_ctacte = $codctacte";
                            $query2 = $conexion->query($query);
                            $arrdescuentos = $query2->result_array();
                            foreach ($arrdescuentos as $rowdto) {
                                $importeviejo = $importeviejo - $importeviejo * $rowdto['descuento'] / 100;
                            }
                            $porc_desc = $descuento * 100 / $importeviejo;
                        }
//                        else {
//                            $importe = $objctacte->importe - $objctacte->importe * $porc_desc / 100;
//                            $pagado = $importe;
//                        }
//                        $query = "UPDATE ctacte SET importe = '$importe', pagado = '$pagado' WHERE ctacte.codigo = '$codctacte'";
//                        $query2 = $conexion->query($query);
                        $query = "INSERT INTO matriculaciones_ctacte_descuento (cod_ctacte, descuento, estado, cod_usuario, forma_descuento) VALUES
                              ('$codctacte','$porc_desc','no_condicionado','19','manual')";
                        $query2 = $conexion->query($query);

                        $renglonesCobro[] = array('cod_ctacte' => $codctacte, 'valor' => $renglon['importe']);
                        $renglonesCobro2[] = array('cod_ctacte' => $codctacte, 'valor' => $renglon['importe'], 'valor_real' => $importeRenglon);
                    }
                }
                $cobroasociado = '';
                $renglonesasociados = array();
                $query = "SELECT * FROM facturas_cobros WHERE cod_factura = '$codigo_factura'";
                $query2 = $conexion->query($query);
                $arrRelacion = $query2->result_array();

                if (count($arrRelacion) > 0) {
                    $cobroasociado = $arrRelacion[0]['cod_cobro'];
                    $query = "SELECT * FROM ctacte_imputaciones WHERE cod_cobro = '$cobroasociado'";
                    $query2 = $conexion->query($query);
                    $arrImputaciones = $query2->result_array();
                    $renglones = array();
                    foreach ($arrImputaciones as $imputaciones) {
                        $renglones[] = array('cod_ctacte' => $imputaciones['cod_ctacte'], 'valor' => $imputaciones['valor']);
                    }
                    $renglonesasociados = $renglones;
                } else {
                    $fechafactura = $factura['fecha'];
                    $alumno = $factura['cod_alumno'];
                    $importecobro = $factura['total'];
                    $query = "SELECT * FROM cobros WHERE cobros.fechareal = '$fechafactura' AND cobros.cod_alumno = '$alumno' 
                      AND cobros.importe = $importecobro";
                    $query2 = $conexion->query($query);
                    $arrCobros = $query2->result_array();
                    foreach ($arrCobros as $rowcobro) {
                        $codigo = $rowcobro['codigo'];
                        $query = "SELECT * FROM ctacte_imputaciones WHERE cod_cobro = '$codigo'";
                        $query2 = $conexion->query($query);
                        $arrImputaciones = $query2->result_array();
                        $renglones = array();
                        foreach ($arrImputaciones as $imputaciones) {
                            $renglones[] = array('cod_ctacte' => $imputaciones['cod_ctacte'], 'valor' => $imputaciones['valor']);
                        }
                        if ($renglones == $renglonesCobro) {
                            $cobroasociado = $codigo;
                            $renglonesasociados = $renglones;
                        }
                    }
                }

                if ($cobroasociado != '') {
                    $totalCobro = $factura['total'];
                    $query = "UPDATE cobros SET importe = $totalCobro WHERE cobros.codigo = '$cobroasociado'";
                    $this->cobrosmodificados += $this->cobrosmodificados == '' ? $cobroasociado : ', ' . $cobroasociado;
                    $query2 = $conexion->query($query);
                    foreach ($renglonesasociados as $rowccimputaciones) {
                        foreach ($renglonesCobro2 as $value) {
                            if ($value['cod_ctacte'] == $rowccimputaciones['cod_ctacte'] && $value['valor'] == $rowccimputaciones['valor']) {
                                $imputado = $value['valor_real'];
                                $cod_ctacte = $rowccimputaciones['cod_ctacte'];
                            }
                        }
                        if (isset($imputado)) {
                            $query = "UPDATE ctacte_imputaciones SET valor = $imputado WHERE cod_ctacte = '$cod_ctacte' AND cod_cobro = '$cobroasociado'";

                            $query2 = $conexion->query($query);
                        }
                    }
                }
            }
            $conexion->trans_commit();
        }
    }

    public function cobrosMigradosDesc($codFilial) {
        $conexion = $this->load->database($codFilial, true);
        $modificados = 0;
        $cobrosyamodificados = $this->cobrosmodificados == '' ? "" : " AND cobros.codigo NOT IN ($this->cobrosmodificados)";
        $query = "SELECT cobros.*,
               (SELECT sum(valor) FROM ctacte_imputaciones WHERE cod_cobro = cobros.codigo) AS imputado 
               FROM cobros HAVING imputado > importe + 0.3 AND cobros.estado = 'confirmado' $cobrosyamodificados ";
        $query2 = $conexion->query($query);
        $arrCobros = $query2->result_array();
        foreach ($arrCobros as $cobro) {
            $conexion->trans_begin();
            $codigo_cobro = $cobro['codigo'];
            $condiciones = array('cod_cobro' => $codigo_cobro);
            $arrRenglones = Vctacte_imputaciones::listarCtacte_imputaciones($conexion, $condiciones);
            $descuento = $cobro['imputado'] - $cobro['importe'];
            $porc_desc = $descuento * 100 / $cobro['imputado'];
            foreach ($arrRenglones as $renglon) {
                $codRenglon = $renglon['codigo'];
                $importeRenglon = $renglon['valor'] - $renglon['valor'] * $porc_desc / 100;
                $query = "UPDATE ctacte_imputaciones SET valor = '$importeRenglon' WHERE ctacte_imputaciones.codigo = '$codRenglon'";
                $query2 = $conexion->query($query);
                $codctacte = $renglon['cod_ctacte'];
                $objctacte = new Vctacte($conexion, $codctacte);
                if ($objctacte->importe > 0) {
                    $porc_imputado = $renglon['valor'] * 100 / $objctacte->importe;
                    if ($porc_imputado < 100) {
                        $importeviejo = $objctacte->importe;
                        $query = "SELECT matriculaciones_ctacte_descuento.* FROM matriculaciones_ctacte_descuento WHERE cod_ctacte = $codctacte";
                        $query2 = $conexion->query($query);
                        $arrdescuentos = $query2->result_array();
                        foreach ($arrdescuentos as $rowdto) {
                            $importeviejo = $importeviejo - $importeviejo * $rowdto['descuento'] / 100;
                        }
                        if ($importeviejo <> 0){
                            $porc_desc = $descuento * 100 / $importeviejo;
                        }
                    }
                    $query = "INSERT INTO matriculaciones_ctacte_descuento (cod_ctacte, descuento, estado, cod_usuario, forma_descuento) VALUES
                              ('$codctacte','$porc_desc','no_condicionado','19','manual')";
                    $query2 = $conexion->query($query);
                }
            }
            $estadotran = $conexion->trans_status();
            $conexion->trans_commit();
            $modificados = $estadotran == 1 ? $modificados + 1 : $modificados;
        }
    }

    public function facturasMigradasRecargo($codFilial) {
        $conexion = $this->load->database($codFilial, true);
        $objFilial = new Vfiliales($conexion, $codFilial);
        if ($objFilial->pais != 2) {
            $query = "SELECT *, (SELECT SUM(facturas_renglones.importe) 
                FROM facturas_renglones WHERE facturas_renglones.cod_factura = facturas.codigo) as cant_renglones,
                (SELECT facturas_propiedades.valor FROM facturas_propiedades WHERE facturas_propiedades.cod_factura = facturas.codigo AND facturas_propiedades.propiedad = 'numero_factura') as nro,
                (SELECT alumnos_razones.cod_alumno FROM alumnos_razones WHERE alumnos_razones.cod_razon_social = facturas.codrazsoc) as cod_alumno
                FROM facturas HAVING cant_renglones < facturas.total ";
            $query2 = $conexion->query($query);
            $arrFacturas = $query2->result_array();
            foreach ($arrFacturas as $factura) {
                $conexion->trans_begin();
                $cod_factura = $factura['codigo'];
                $condiciones = array('cod_factura' => $cod_factura);
                $arrRenglones = Vfacturas_renglones::listarFacturas_renglones($conexion, $condiciones);
                $cod_ctacte = $arrRenglones[0]['cod_ctacte'];
                $recargo = $factura['total'] - $factura['cant_renglones']; //FACTURAS CON RECARGO
                $alumno = $factura['cod_alumno'];
                $renglonesCobro = array();
                foreach ($arrRenglones as $renglon) {
                    $renglonesCobro[] = array('cod_ctacte' => $renglon['cod_ctacte'], 'valor' => $renglon['importe']);
                }
                $cobroasociado = '';
                $query = "SELECT * FROM facturas_cobros WHERE cod_factura = '$cod_factura'";
                $query2 = $conexion->query($query);
                $arrRelacion = $query2->result_array();
                if (count($arrRelacion) > 0) {
                    $cobroasociado = $arrRelacion[0]['cod_cobro'];
                } else {
                    $fechafactura = $factura['fecha'];
                    $importecobro = $factura['total'];
                    $query = "SELECT * FROM cobros WHERE cobros.fechareal = '$fechafactura' AND cobros.cod_alumno = '$alumno' 
                      AND cobros.importe = $importecobro";
                    $query2 = $conexion->query($query);
                    $arrCobros = $query2->result_array();
                    foreach ($arrCobros as $rowcobro) {
                        $codigo = $rowcobro['codigo'];
                        $query = "SELECT * FROM ctacte_imputaciones WHERE cod_cobro = '$codigo'";
                        $query2 = $conexion->query($query);
                        $arrImputaciones = $query2->result_array();
                        $renglones = array();
                        foreach ($arrImputaciones as $imputaciones) {
                            $renglones[] = array('cod_ctacte' => $imputaciones['cod_ctacte'], 'valor' => $imputaciones['valor']);
                        }
                        if ($renglones == $renglonesCobro) {
                            $cobroasociado = $codigo;
                        }
                    }
                }
                if ($cobroasociado != '') {
                    $habilitado = $factura['estado'] == 'habilitada' ? 1 : 0;
                    $query = "INSERT INTO ctacte (cod_alumno, nrocuota, importe, pagado, habilitado, cod_concepto, concepto) VALUES
                          ('$alumno','1','$recargo','$recargo','$habilitado', '3', '$cod_ctacte')";
                    $query2 = $conexion->query($query);
                    $cod_insertado = $conexion->insert_id();
                    $anulada = $factura['estado'] == 'habilitada' ? 0 : 1;
                    $query = "INSERT INTO facturas_renglones (cod_ctacte, cod_factura, importe, anulada) VALUES
                          ('$cod_insertado','$cod_factura','$recargo','$anulada')";
                    $query2 = $conexion->query($query);
                    $totalCobro = $factura['total'];
                    $query = "UPDATE cobros SET importe = $totalCobro WHERE cobros.codigo = '$cobroasociado'";
                    $query2 = $conexion->query($query);
//                    echo "afected " . $conexion->affected_rows() . ' - cobro: ' . $cobroasociado . "<br>";
                    $estado = $factura['estado'] == 'habilitada' ? 'confirmado' : 'anulado';
                    $query = "INSERT INTO ctacte_imputaciones (cod_ctacte, valor, cod_cobro, cod_usuario, estado, tipo) VALUES
                          ('$cod_insertado','$recargo','$cobroasociado','1','$estado','COBRO')";
                    $query2 = $conexion->query($query);

                    if ($conexion->trans_status()) {
//                        echo 'factura: ' . $factura['nro'] . "<br>";
                        $conexion->trans_commit();
                    } else {
//                        echo 'no modifico: ' . $factura['nro'] . "<br>";
                        $conexion->trans_rollback();
                    }
                } else {
//                    echo 'no modifico: ' . $factura['nro'] . "<br>";
                    $conexion->trans_rollback();
                }
            }
        }
    }

//    public function facturasMigradasPagosParciales($codFilial) {
//        $conexion = $this->load->database($codFilial, true);
//        $query = "SELECT *, (SELECT SUM(ctacte_imputaciones.valor) FROM ctacte_imputaciones 
//            WHERE ctacte_imputaciones.cod_ctacte = ctacte.codigo) AS cantimputaciones 
//            FROM ctacte HAVING cantimputaciones <> pagado AND importe > cantimputaciones";
//        $query2 = $conexion->query($query);
//        $arrCtaCte = $query2->result_array();
//
//        foreach ($arrCtaCte as $rowcta) {
//            $codctacte = $rowcta['codigo'];
//            $pagado = $rowcta['cantimputaciones'];
//            $query = "UPDATE ctacte SET pagado = '$pagado' WHERE codigo = $codctacte";
//            $query2 = $conexion->query($query);
//        }
//    }

    public function cobrosPeriodoErrado($codFilial) {
        $conexion = $this->load->database($codFilial, true);
        $query = "SELECT t2.codigo FROM (SELECT *,CONCAT(YEAR(t1.fechareal),DATE_FORMAT(t1.fechareal,'%m' )) 
                FROM cobros AS t1 WHERE t1.periodo <> CONCAT(YEAR(t1.fechareal),DATE_FORMAT(t1.fechareal,'%m' )) ) AS t2";
        $query2 = $conexion->query($query);
        $arrCobros = $query2->result_array();
        foreach ($arrCobros as $cobro) {
            $cod_cobro = $cobro['codigo'];
            $query = "UPDATE cobros SET periodo = CONCAT(YEAR(cobros.fechareal),DATE_FORMAT(cobros.fechareal,'%m' ))  WHERE codigo = $cod_cobro";
            $query2 = $conexion->query($query);
        }
    }

    public function examenesEstadosAcademicosAprobados($codFilial) {
        $conexion = $this->load->database($codFilial, true);
        $query = "SELECT examenes_estado_academico.codigo 
                    FROM  examenes_estado_academico 
                    JOIN examenes ON examenes.codigo = examenes_estado_academico.cod_examen
                    JOIN notas_resultados ON notas_resultados.cod_inscripcion = examenes_estado_academico.codigo and notas_resultados.tipo_resultado ='definitivo'
                    WHERE examenes.tipoexamen IN ('FINAL','RECUPERATORIO_FINAL') 
                    AND notas_resultados.nota >= 6
                    AND examenes_estado_academico.estado <> 'aprobado' ";
        $query2 = $conexion->query($query);
        $arrExamenes = $query2->result_array();
        foreach ($arrExamenes as $examen) {
            $codigo = $examen['codigo'];
            $query = "UPDATE examenes_estado_academico SET examenes_estado_academico.estado = 'aprobado' WHERE codigo = $codigo ";
            $query2 = $conexion->query($query);
        }
        $query = "SELECT estadoacademico.codigo
                FROM examenes_estado_academico 
                JOIN examenes ON examenes.codigo = examenes_estado_academico.cod_examen
                JOIN estadoacademico ON estadoacademico.codigo = examenes_estado_academico.cod_estado_academico
                WHERE examenes_estado_academico.estado = 'aprobado' 
                AND examenes.tipoexamen IN ('FINAL','RECUPERATORIO_FINAL') 
                AND estadoacademico.estado NOT IN('aprobado','homologado')";

        $query2 = $conexion->query($query);
        $arrEstadosAca = $query2->result_array();
        foreach ($arrEstadosAca as $ea) {
            $codigo = $ea['codigo'];
            $query = "UPDATE estadoacademico SET estadoacademico.estado = 'aprobado' WHERE estadoacademico.codigo = $codigo ";
            $query2 = $conexion->query($query);
        }
    }

    public function modificarCtasCtes($codFilial) {
        $conexion = $this->load->database($codFilial, true);
        $modificados = 0;
        $query = "select *, (select sum(ctacte_imputaciones.valor) from ctacte_imputaciones where ctacte_imputaciones.cod_ctacte = ctacte.codigo and ctacte_imputaciones.estado = 'confirmado') as imputaciones from ctacte";
        $query2 = $conexion->query($query);
        $arrCtasCtes = $query2->result_array();
        foreach ($arrCtasCtes as $ctacte) {
            $conexion->trans_begin();
            $codigo = $ctacte['codigo'];
            $imputaciones = $ctacte['imputaciones'];

            if ($imputaciones <> $ctacte['pagado']) {
                $query = "UPDATE ctacte SET pagado = '$imputaciones' WHERE ctacte.codigo = '$codigo'";
                $query2 = $conexion->query($query);
            }

            $importe = $ctacte['importe'];
            $query = "SELECT matriculaciones_ctacte_descuento.* FROM matriculaciones_ctacte_descuento WHERE cod_ctacte = $codigo";
            $query2 = $conexion->query($query);
            $arrdescuentos = $query2->result_array();
            foreach ($arrdescuentos as $rowdto) {
                $importe = $importe - $importe * $rowdto['descuento'] / 100;
            }

            if ($importe <> $ctacte['importe']) {
                $query = "UPDATE ctacte SET importe = '$importe' WHERE ctacte.codigo = '$codigo'";
                $query2 = $conexion->query($query);
            }

            if ($imputaciones > $importe) {
                $query = "UPDATE ctacte SET importe = '$imputaciones' WHERE ctacte.codigo = '$codigo'";
                $query2 = $conexion->query($query);
            }

            $estadotran = $conexion->trans_status();
            $conexion->trans_commit(); // cambiar al terminar el debug
            $modificados = $estadotran == 1 ? $modificados + 1 : $modificados;
        }
        return true;
    }

}

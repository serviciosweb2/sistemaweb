<?php

class WsFePDF extends FPDF {

    public function WsFePDF(CI_DB_mysqli_driver $conexion, $orientacion = 'P', $unidad = 'mm', $formato = 'A4', $cod_factura = '', $gif_logo_empresa = '') {

        parent::FPDF($orientacion, $unidad, $formato);
        $this->oConnection = $conexion;
        $this->gif_logo_empresa = $gif_logo_empresa;
        $this->factura = new Vfacturas($conexion, $cod_factura);
    }

    public function printFe($imprimeRazon=true, $muestraCantCuotas = 1, $cant_copias = 1) {

        /* DATOS PARA LA IMPRESION */
        $facturaHabilitada = Vseguimiento_afip::listarSeguimiento_afip($this->oConnection, array('cod_factura' => $this->factura->getCodigo(), 'estado' => 'aprobado'), null, array(array('campo' => 'codigo', 'orden' => 'desc')));

        if (count($facturaHabilitada) > 0) {
            $aprobada = true;
            $facturaHabilitada = $facturaHabilitada[0];
        } else {
            $aprobada = false;
        }

        $numeroFactura = str_pad($this->factura->getPropiedad('numero_factura'), 8, "0", STR_PAD_LEFT);
        $fechaFactura = formatearFecha_pais($this->factura->fecha);
        $cae = $facturaHabilitada['cae'];
        $vencimientocae = formatearFecha_pais($facturaHabilitada['vencimiento_cae']);

        $ptovta = new Vpuntos_venta($this->oConnection, $this->factura->punto_venta);
        $numeroPtoVta = str_pad($ptovta->prefijo, 4, "0", STR_PAD_LEFT);
        $tipofactura = new Vtipos_facturas($this->oConnection, $ptovta->tipo_factura);
        $facturante = new Vfacturantes($this->oConnection, $ptovta->cod_facturante);
        $razonsocialfacturante = new Vrazones_sociales_general($this->oConnection, $facturante->cod_razon_social);

        $comprobante = $tipofactura->factura;
        $tipoComprobante = $tipofactura->tipo;
        $codAfipCbte = $tipofactura->cod_afip;
        $razonSocial = $razonsocialfacturante->razon_social;
        $cuit = $razonsocialfacturante->documento;
        $inicioActividades = formatearFecha_pais($facturante->inicio_actividades);
        $ingresosBrutos = $razonsocialfacturante->ingresos_brutos;
        $arrcondicion = Vcondiciones_sociales::listarCondiciones_sociales($this->oConnection, array('codigo' => $razonsocialfacturante->condicion));
        $condicionFacturante = $arrcondicion[0]['condicion'];

        $direccion = $razonsocialfacturante->direccion_calle . " " . $razonsocialfacturante->direccion_numero . " " . $razonsocialfacturante->direccion_complemento;
        $codigoPostal = $razonsocialfacturante->codigo_postal;
        $telefono = "(" . $razonsocialfacturante->telefono_cod_area . ") " . $razonsocialfacturante->telefono_numero;
        $localidad = Vlocalidades::listarLocalidades($this->oConnection, array('id' => $razonsocialfacturante->cod_localidad));
        $localidadFacturante = $localidad[0]['nombre'];
        
        $provicia =    Vprovincias::listarProvincias($this->oConnection, array('id' => $localidad[0]['provincia_id'])) ;
        $direccionFacturante = $direccion . ', ' . $localidadFacturante . ' ,' . $provicia[0]['nombre'];
        $codigoBarra = $razonsocialfacturante->documento . str_pad($codAfipCbte, 2, "0", STR_PAD_LEFT) . $numeroPtoVta . $cae . substr($facturaHabilitada['vencimiento_cae'], 0, 4) . substr($facturaHabilitada['vencimiento_cae'], 5, 2) . substr($facturaHabilitada['vencimiento_cae'], 8, 2);
        $digitoVerificador = $this->calcularDigitoVerificador($codigoBarra);
        $codigoBarra.=$digitoVerificador;


        $razonalumno = new Vrazones_sociales($this->oConnection, $this->factura->codrazsoc);
        $arrlocalidades = Vlocalidades::listarLocalidades($this->oConnection, array('id' => $razonalumno->cod_localidad));
        $localidadAlumno = $arrlocalidades[0]['nombre'];
        $provicia = Vprovincias::listarProvincias($this->oConnection, array('id' => $arrlocalidades[0]['provincia_id']));

        $alumnoRazonSocial = $razonalumno->razon_social;
        $alumnoTipoId = $razonalumno->tipo_documentos;
        $alumnoIdentificacion = $razonalumno->documento;
        $alumnoDireccion = $razonalumno->direccion_calle . " " . $razonalumno->direccion_numero . " " . $razonalumno->direccion_complemento;
        $alumnoBarrio = $razonalumno->barrio;
        $alumnoCodPostal = $razonalumno->codigo_postal;
        $alumnoEmail = $razonalumno->email;
        $arrcondicionalumno = Vcondiciones_sociales::listarCondiciones_sociales($this->oConnection, array('codigo' => $razonalumno->condicion));
        $alumnoCondicion = $arrcondicionalumno[0]['condicion'];

        $nombre = $imprimeRazon ? 'Nombre y Apellido / Razón Social: ' . $alumnoRazonSocial : '';
        $domicilio = $imprimeRazon ? 'Domicilio: ' . $alumnoDireccion : '';
        $cuitalumno = $imprimeRazon && $alumnoTipoId == 3 ? 'CUIT: ' . $alumnoIdentificacion : '';

        $cobroAsociado = $this->factura->getCobroAsociado();
        if (count($cobroAsociado) > 0) {
            $objCobro = new Vcobros($this->oConnection, $cobroAsociado[0]['cod_cobro']);
            $medios = Vmedios_pago::listarMedios_pago($this->oConnection, array('codigo' => $objCobro->medio_pago));
            $medioPago = lang($medios[0]['medio']);
        } else {
            $medioPago = lang('EFECTIVO');
        }
        $arrItems = array();
        $arrRenglones = $this->factura->getRenglonesDescripcion();
        $i = 0;
        foreach ($arrRenglones as $renglon) {
            $condicion = array('codigo' => $renglon['codigo']);
            $ctacte = Vctacte::listarCtacte($this->oConnection, $condicion);
            formatearCtaCte($this->oConnection, $ctacte, 0);
            $arrItems[$i]['item'] = $ctacte[0]['descripcion'];
            $arrItems[$i]['precio'] = number_format($renglon['importe_facturado'], 2, ",", "");
            if ($codAfipCbte == 1) {//discrimina iva
                $objRenglon = new Vfacturas_renglones($this->oConnection, $renglon['cod_renglon']);
                $netoRenglon = $objRenglon->getNeto();
                $arrItems[$i]['precio'] = number_format($netoRenglon, 2, ",", "");
            }
            $i++;
        }

        $arrImpuestos = $this->factura->getImpuestosFactura();
        $total = $this->factura->total;
        $total_neto = $this->factura->getNeto();


        /* FIN DE DATOS */

        $pdf = new eFPDF('P', 'mm', 'A4');

        for ($e = 0; $e < $cant_copias; $e++) {
            for ($i = 0; $i < 3; $i++) {
            $pdf->AddPage('P', 'A4');
            $pdf->SetFont('arial', 'B', 12);

            switch ($i) {
                case 0:
                    $copia = 'ORIGINAL';
                    break;
                case 1:
                    $copia = 'DUPLICADO';
                    break;
                case 2:
                    $copia = 'TRIPLICADO';
                    break;
                default:
                    $copia = '';
                    break;
            }
            $pdf->SetFillColor(255, 255, 255);
            $pdf->Cell(190, 7, $copia, "LRT", 0, "C", true);
            $pdf->Ln();

            $pdf->SetFont("arial", "B", 12);
            $pdf->Cell(85, 7, "", "LT", 0, "C");
            $x = $pdf->GetX();
            $pdf->Cell(20, 7, strtoupper($tipoComprobante), "LRT", 0, "C");
            $x2 = $pdf->GetX();
            $y = $pdf->GetY();
            $pdf->Ln();
            $pdf->SetX($x);
            $pdf->SetFont("arial", "B", 6);
            $pdf->Cell(20, 4, strtoupper("cod. " . $codAfipCbte), "LRB", 0, "C");
            $pdf->SetXY($x2, $y);
            $pdf->SetFont("arial", "B", 10);
            $pdf->Cell(85, 7, strtoupper($comprobante), "RT", 0, "C");

            $x = $pdf->GetX();
            $y = $pdf->GetY();
            $pdf->SetXY(45, 20);
            $pdf->Image($this->gif_logo_empresa);
            $pdf->SetXY($x, $y);
            $pdf->Ln();
            $pdf->Cell(5, 4, "", "L");
            $pdf->Ln();
            $pdf->Cell(5, 4, "", "L");
            $pdf->Ln();
            $pdf->Cell(5, 4, "", "L");
            $pdf->Ln();
            $pdf->SetFont("arial", "", 8);
            $pdf->Cell(5, 4, "", "L");
            $pdf->Cell(9, 4, utf8_decode("Razón Social: " . $razonSocial), "", 0);
            $pdf->Ln();
            $pdf->SetFont("arial", "", 8);
            $pdf->Cell(5, 4, "", "L");
            $pdf->Cell(9, 4, utf8_decode("Domicilio Comercial: " . $direccionFacturante), "", 0);
            $pdf->Ln();
            $pdf->SetFont("arial", "", 8);
            $pdf->Cell(5, 4, "", "L");
            $pdf->Cell(9, 4, utf8_decode("Condición: " . $condicionFacturante), "", 0);
            $pdf->Ln();
            $pdf->Cell(5, 4, "", "L");
            $pdf->Ln();
            $pdf->Cell(95, 0, "", "B", 0);

            $pdf->SetXY(105, 24);
            $pdf->SetFont("arial", "B", 8);
            $pdf->Cell(15, 4, "", "");
            $pdf->Cell(80, 4, utf8_decode("Nº Factura: " . $numeroPtoVta . " - " . $numeroFactura), "R", 0);
            $pdf->Ln();
            $pdf->SetX(105);
            $pdf->SetFont("arial", "B", 8);
            $pdf->Cell(15, 4, "", "L");
            $pdf->Cell(80, 4, utf8_decode("Fecha de emisión: " . $fechaFactura), "R", 0);
            $pdf->Ln();
            $pdf->SetX(105);
            $pdf->Cell(95, 4, "", "LR");
            $pdf->Ln();
            $pdf->SetX(105);
            $pdf->SetFont("arial", "", 8);
            $pdf->Cell(5, 4, "", "L");
            $pdf->Cell(90, 4, utf8_decode("CUIT: " . $cuit), "R", 0);
            $pdf->Ln();
            $pdf->SetX(105);
            $pdf->SetFont("arial", "", 8);
            $pdf->Cell(5, 4, "", "L");
            $pdf->Cell(90, 4, utf8_decode("Ingresos Brutos: " . $ingresosBrutos), "R", 0);
            $pdf->Ln();
            $pdf->SetX(105);
            $pdf->SetFont("arial", "", 8);
            $pdf->Cell(5, 4, "", "L");
            $pdf->Cell(90, 4, utf8_decode("Inicio Actividades: " . $inicioActividades), "R", 0);

            $pdf->Ln();
            $pdf->SetX(105);
            $pdf->Cell(95, 4, "", "LR");
            $pdf->Ln();
            $pdf->SetX(105);
            $pdf->Cell(95, 0, "", "B", 0);
            $pdf->Ln();
            $pdf->Cell(190, 7, "", "LRT", 0, "C");

            $pdf->Ln();
            $pdf->SetFont("arial", "", 8);
            $pdf->Cell(5, 4, "", "L");
            $y = $pdf->GetY();
            $pdf->Cell(9, 4, utf8_decode($nombre), "", 0);
            $pdf->Ln();
            $pdf->SetFont("arial", "", 8);
            $pdf->Cell(5, 4, "", "L");
            $pdf->Cell(9, 4, utf8_decode($domicilio), "", 0);
            $pdf->Ln();
            $pdf->SetFont("arial", "", 8);
            $pdf->Cell(5, 4, "", "L");
            $pdf->Cell(90, 4, utf8_decode($cuitalumno), "", 0);
            $pdf->Ln();
            $pdf->Cell(190, 6, "", "LB", 0);

            $pdf->SetXY(95, $y);
            $pdf->SetFont("arial", "", 8);
            $pdf->Cell(15, 4, "", "");
            $pdf->Cell(90, 4, utf8_decode("Condición: " . $alumnoCondicion), "R", 0);
            $pdf->Ln();
            $pdf->SetX(95);
            $pdf->SetFont("arial", "", 8);
            $pdf->Cell(15, 10, "", "");
            $pdf->Cell(90, 10, utf8_decode("Medio de Pago: " . $medioPago), "R", 0);

            $pdf->Ln();
            $pdf->SetX(105);
            $pdf->Cell(95, 4, "", "R");

            $pdf->Ln();
            $pdf->Ln(4);
            $pdf->SetFont("arial", "", 10);
            $pdf->SetFillColor(138, 138, 138);
            $pdf->Cell(150, 7, "Producto / Servicio", "LRTB", 0, "C", true);
            $pdf->Cell(40, 7, "Subtotal", "LRTB", 0, "C", true);

            $pdf->Ln();
            $pdf->SetFont("arial", "", 8);
            foreach ($arrItems as $item) {
                $pdf->Cell(5, 7, "", "");
                $pdf->Cell(145, 7, utf8_decode($item['item']), "", 0, "L");
                $pdf->Cell(25, 7, $item['precio'], "", 0, "R");
                $pdf->Ln();
            }

            $pdf->Ln();
            $pdf->SetY(190);
            $pdf->Cell(190, 7, "El presente documento expresa su monto en ARS", "LRT", 0, "L");
            $pdf->Ln();
            $pdf->Cell(120, 5, "", "L", 0, "R");
            $pdf->Cell(35, 5, "Subtotal: $", "", 0, "R");
            $subtotal = $codAfipCbte == 1 ? $total_neto : $total;
            $pdf->Cell(20, 5, number_format($subtotal, 2, ",", ""), "", 0, "R");
            $pdf->Cell(15, 5, "", "R", 0, "R");
            $pdf->Ln();
            if ($codAfipCbte == 1) {

                foreach ($arrImpuestos as $impuesto) {
                    if ($impuesto['cod_afip'] != 2) {
                        $pdf->Cell(120, 5, "", "L", 0, "R");
                        $pdf->Cell(35, 5, utf8_decode($impuesto['nombre'] . ": $"), "", 0, "R");
                        $pdf->Cell(20, 5, utf8_decode(number_format($impuesto['total'], 2, ",", "")), "", 0, "R");
                        $pdf->Cell(15, 5, "", "R", 0, "R");
                        $pdf->Ln();
                    }
                }
            }
            $pdf->Cell(120, 5, "", "L", 0, "R");
            $pdf->Cell(35, 5, "Otros tributos: $", "", 0, "R");
            $pdf->Cell(20, 5, "0,00", "", 0, "R");
            $pdf->Cell(15, 5, "", "R", 0, "R");
            $pdf->Ln();
            $pdf->Cell(120, 5, "", "L", 0, "R");
            $pdf->Cell(35, 5, "Total: $", "", 0, "R");
            $pdf->Cell(20, 5, number_format($total, 2, ",", ""), "", 0, "R");
            $pdf->Cell(15, 5, "", "R", 0, "R");
            $pdf->Ln();
            $pdf->Cell(190, 5, "", "LR", 0, "L");
            $pdf->Ln();
            $pdf->Cell(190, 0, "", "B", 0, "L");

            $pdf->Ln();
            $pdf->SetFont("arial", "B", 8);
            $pdf->Cell(30, 5, utf8_decode("CAE Nº: "), "", 0, "L");
            $pdf->SetFont("arial", "", 8);
            $pdf->Cell(45, 5, $cae, "", 0, "L");
            $pdf->Ln();
            $pdf->SetFont("arial", "B", 8);
            $pdf->Cell(30, 3, "Fecha Vto. CAE: ", "", 0, "L");
            $pdf->SetFont("arial", "", 8);
            $pdf->Cell(45, 3, $vencimientocae, "", 0, "L");

            Barcode::fpdf($pdf, '000000', $pdf->GetX() + 55, $pdf->GetY() + 5, 0, "int25", array('code' => $codigoBarra), 0.4, 12);

            $pdf->Ln();
            $pdf->Ln();
            $pdf->Cell(165, 15, $codigoBarra, "", 0, "R");
            $pdf->Ln();
            $pdf->SetFont("arial", "B", 8);
            $pdf->Cell(190, 5, "Comprobante Autorizado", "", 0, "L");
            $pdf->Ln();
            $pdf->SetFont("arial", "", 7);
            $pdf->Cell(190, 4, "Verifique la validez del comprobante accediendo a:", "", 0, "L");
            $pdf->Ln();
            $pdf->Cell(190, 4, "http://www.afip.gov.ar/genericos/consultacae/", "", 0, "L");
            }
        }
        return $pdf;
    }

    public function calcularDigitoVerificador($codigo) {
        $pares = 0;
        $impares = 0;
        for ($i = 0; $i < strlen($codigo); $i++) {
            if (($i+1) % 2 == 0) {
                $pares += (int)$codigo[$i];
                // es par
            } else {
                $impares += (int)$codigo[$i]; //impar
            }
            //echo $codigo{$i};
        }

        $suma = $pares * 3 + $impares;
        $digitoverificador = 10 - $suma % 10;

        return $digitoverificador;
    }
}

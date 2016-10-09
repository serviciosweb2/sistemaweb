<?php

class NFSePHPDsfPDF extends FPDF {

    protected $nota_fiscal;
    protected $gif_brasao_prefeitura;
    protected $gif_logo_empresa;
    protected $aParser;
    protected $oConnection;

    public function NFSePHPDsfPDF(CI_DB_mysqli_driver $conexion, $orientation = 'P', $unit = 'mm', $format = 'A4', $cod_factura = '', $gif_brasao_prefeitura = '', $gif_logo_empresa = '') {

        //parent::__construct($conexion, $orientation, $unit, $format);
        parent::FPDF($orientation, $unit, $format);
        $this->oConnection = $conexion;
        $this->gif_brasao_prefeitura = $gif_brasao_prefeitura;
        $this->gif_logo_empresa = $gif_logo_empresa;
        $this->nota_fiscal = new Vfacturas($conexion, $cod_factura);
    }

    public function printNFSe() {

//        $mySQLP = new mySQL("public", "nfe");
//        $myTomador = new nfe_tomador($mySQLP, $idFilial, null, null, $idFacturaCabecera);
        $total = 0; // no setear esta variable, dejarla siempre en cero
        $notaEnviada = Vseguimiento_dsf::listarSeguimiento_dsf($this->oConnection, array('cod_factura' => $this->nota_fiscal->getCodigo(), 'estado' => 'enviado'), null, array(array('campo' => 'id', 'orden' => 'desc')));
        $notaHabilitada = Vseguimiento_dsf::listarSeguimiento_dsf($this->oConnection, array('cod_factura' => $this->nota_fiscal->getCodigo(), 'estado' => 'habilitada'), null, array(array('campo' => 'id', 'orden' => 'desc')));
        /* CONFIGURACIONES PARA LA IMPRESION */

        //$myRPS = new nfe_rps(1, null, $mySQLP, null, $this->myConfiguracionFacturacion);
        $rps = $this->nota_fiscal->getPropiedad('numero_rps');
        $serie = "99";
        $numeroNota = $this->nota_fiscal->getPropiedad('numero_factura');
        $fechaEmision = count($notaEnviada) > 0 ? $notaEnviada[0]['fecha_envio'] : $this->nota_fiscal->fechareal;
        $codigoVerificacion = count($notaHabilitada) > 0 ? $notaHabilitada[0]['codigo_verificacion'] : '-';

        $ptovta = new Vpuntos_venta($this->oConnection, $this->nota_fiscal->punto_venta);
        $facturante = new Vfacturantes($this->oConnection, $ptovta->cod_facturante);
        $razonsocialfacturante = new Vrazones_sociales_general($this->oConnection, $facturante->cod_razon_social);
        $municipio = Vlocalidades::listarLocalidades($this->oConnection, array('id' => $razonsocialfacturante->cod_localidad));
        $municipioFacturante = $municipio[0]['nombre'];
        $provicia = Vprovincias::listarProvincias($this->oConnection, array('id' => $municipio[0]['provincia_id']));
        $objprovincia = new Vprovincias($this->oConnection, $provicia[0]['id']);
        $codestadoFacturante = $objprovincia->get_codigo_estado();
        $arrprestador = Vprestador_dsf::listarPrestador_dsf($this->oConnection, array('cod_punto_venta' => $this->nota_fiscal->punto_venta));
        $prestador = new Vprestador_dsf($this->oConnection, $arrprestador[0]['codigo']);

        $razonSocial = $razonsocialfacturante->razon_social; // "BASCOM DO BRASIL ESCOLA DE GASTRONOMIA E CULINARIA LTDA";
        $cnpj = $razonsocialfacturante->documento;
        $inscripcionMunicipal = $prestador->inscripcion_municipal;

        $direccion = $razonsocialfacturante->direccion_calle . " " . $razonsocialfacturante->direccion_numero . " " . $razonsocialfacturante->direccion_complemento;
        $barrio = " ";
        $cep = $razonsocialfacturante->codigo_postal;
        $cnae = $prestador->cnae;
        $municipio = $municipioFacturante;
        $descripcionActividad = "";
        $codigoServicio = $prestador->codigo_servicio;
        $uf = $codestadoFacturante;
        $telefono = "(" . $razonsocialfacturante->telefono_cod_area . ") " . $razonsocialfacturante->telefono_numero;

        $razonalumno = new Vrazones_sociales($this->oConnection, $this->nota_fiscal->codrazsoc);
        $arrmunicipio = Vlocalidades::listarLocalidades($this->oConnection, array('id' => $razonalumno->cod_localidad));
        $municipioTomador = $arrmunicipio[0]['nombre'];
        $provicia = Vprovincias::listarProvincias($this->oConnection, array('id' => $arrmunicipio[0]['provincia_id']));
        $objprovinciatomador = new Vprovincias($this->oConnection, $provicia[0]['id']);

        $tomadorRazonSocial = $razonalumno->razon_social;
        $tomadorCPFCNPJ = $razonalumno->documento;
        $tomadorDireccion = $razonalumno->direccion_calle . " " . $razonalumno->direccion_numero . " " . $razonalumno->direccion_complemento;
        $tomadorBarrio = $razonalumno->barrio;
        $tomadorCEP = $razonalumno->codigo_postal;
        $tomadorMunicipio = $municipioTomador;
        $tomadorUF = $objprovinciatomador->get_codigo_estado();
        $tomadorEmail = $razonalumno->email;

        $tomadorTelefono = ""; //$myTomador->DDD_telefono != '' ? "(" . $myTomador->DDD_telefono . ") " . $myTomador->telefono : $myTomador->telefono;
        $nombreServicio = ""; // $this->myConfiguracionFacturacion->nombre_servicio; // "SERVICIO 1";

        $pis = $prestador->valor_pis;
        $cofins = $prestador->valor_cofins;
        $inss = $prestador->valor_inss;
        $ir = $prestador->valor_ir;
        $csll = $prestador->valor_csll;

        $arrItems = array();
        $arrRenglones = $this->nota_fiscal->getRenglonesDescripcion();
        $i = 0;
        foreach ($arrRenglones as $renglon) {
            // se puede hacer una iteracion en el caso que se poseean varios servicios a facturar en la misma nota
            $condicion = array('codigo' => $renglon['codigo']);
            $ctacte = Vctacte::listarCtacte($this->oConnection, $condicion);
            formatearCtaCte($this->oConnection, $ctacte, 0);
            $arrItems[$i]['tributacion'] = "SIM";
            $arrItems[$i]['item'] = $ctacte[0]['descripcion'];
            $arrItems[$i]['cantidad'] = 1;
            $arrItems[$i]['uprecio'] = $renglon['importe_facturado'];
            $arrItems[$i]['precio_unitario'] = number_format($arrItems[$i]['uprecio'], 2, ",", "");
            $arrItems[$i]['total'] = number_format($arrItems[$i]['cantidad'] * $arrItems[$i]['uprecio'], 2, ",", "");
            //$total += $arrItems[$i]['cantidad'] * $arrItems[$i]['uprecio'];
            $i++;
            // fin de iteracion entre carios servicios 
        }
        $total = $this->nota_fiscal->total;


        $alicuota = $prestador->alicuota;
        $deducciones = 0.00;

        /* FIN DE CONFIGURACIONES */


        $numeroNota = str_pad($numeroNota, 8, "0", STR_PAD_LEFT);
        $fechaRps = substr(formatearFecha_pais($fechaEmision), 0, 10);
        $fecha = date('Y-m-j');
        $nuevafecha = strtotime('+20 day', strtotime(substr($fechaEmision, 0, 10)));
        $mesVencimiento = date('m', $nuevafecha);
        $mesCompetencia = substr($fechaEmision,5, 2);
        $fechaEmision = formatearFecha_pais($fechaEmision) . substr($fechaEmision, 10);
        
        $totalDeNota = number_format($total, 2, ",", "");
        $valorPis = number_format($total * $pis / 100, 2, ",", "");
        $valorCofins = number_format($total * $cofins / 100, 2, ",", "");
        $valorInss = number_format($total * $inss / 100, 2, ",", "");
        $valorIr = number_format($total * $ir / 100, 2, ",", "");
        $valorCsll = number_format($total * $csll / 100, 2, ",", "");

        $lineaDireccionTomador = "$tomadorDireccion - BARRIO $tomadorBarrio - CEP:$tomadorCEP";
        $lineaDireccionPrestador = "$direccion - CEP:$cep";

        $pis = number_format($csll, 4, ",", "");
        $cofins = number_format($cofins, 4, ",", "");
        $inss = number_format($inss, 4, ",", "");
        $ir = number_format($ir, 4, ",", "");
        $csll = number_format($csll, 4, ",", "");
        $issqnDevido = number_format($total * $alicuota / 100, 2, ",", "");
        $alicuota = number_format($alicuota, 2, ",", "");
        $deducciones = number_format($deducciones, 2, ",", "");


        $pdf = new FPDF('P', 'mm', 'A4');
        $pdf->AddPage('P', 'A4');
        $pdf->SetFont('arial', 'B', 10);

        $pdf->Cell(140, 7, $this->gif_brasao_prefeitura . "PREFEITURA MUNICIPAL DE " . strtoupper($municipio), "LRT", 0, "C");

        $pdf->Setfont("arial", '', 5);
        $pdf->Cell(40, 4, utf8_decode("Número de Nota"), "TR");
        $pdf->Ln();
        $pdf->Cell(140, 1, "", "LR");
        $pdf->SetFont("arial", "B", 8);
        $pdf->Cell(40, 3, $numeroNota, "RB", 0, "C");
        $pdf->Ln();
        $pdf->SetFont('arial', 'B', 10);
        $pdf->Cell(140, 7, utf8_decode("SECRETARIA MUNICIPAL DE FINANÇAS " . strtoupper($municipio)), "LR", 0, "C");
        $pdf->Setfont("arial", '', 5);
        $pdf->Cell(40, 4, utf8_decode("Data e Hora de Emissão"), "TR");
        $pdf->Ln();
        $pdf->Cell(140, 1, "", "LR");
        $pdf->SetFont("arial", "B", 8);
        $pdf->Cell(40, 3, $fechaEmision, "RB", 0, "C");
        $pdf->Ln();
        $pdf->SetFont('arial', 'B', 10);
        $pdf->Cell(140, 7, utf8_decode("NOTA FISCAL DE SERVICIOS ELECTRÔNICA - FNSe"), "LRB", 0, "C");
        $pdf->Setfont("arial", '', 5);
        $pdf->Cell(40, 4, utf8_decode("Código de verificaçao"), "TR");
        $pdf->Ln();
        $pdf->Cell(140, 1, "", "LR");
        $pdf->SetFont("arial", "B", 5);
        $pdf->Cell(40, 3, $codigoVerificacion, "RB", 0, "C");


        $pdf->Ln();
        $pdf->SetFont("arial", "B", 7);
        $pdf->Cell(180, 4, utf8_decode("PRESTADOR DE SERVIÇOS"), "LR", 0, "C");

        $pdf->Ln();
        $pdf->SetFont("arial", "", 5);
        $pdf->Cell(20, 4, "", "L");
        $pdf->Cell(17, 4, utf8_decode("Nome/Razão Social:"), "", 0);
        $pdf->SetFont("arial", "B", 7);
        $pdf->Cell(143, 4, utf8_decode($razonSocial), "R", 0, "L");

        $pdf->Ln();
        $pdf->SetFont("arial", "", 5);
        $pdf->Cell(20, 4, "", "L");
        $pdf->Cell(6, 4, utf8_decode("CNPJ:"), "", 0, "L");
        $pdf->SetFont("arial", "B", 7);
        $pdf->Cell(45, 4, utf8_decode($cnpj), "", 0, "L");
        $pdf->SetFont("arial", "", 5);
        $pdf->Cell(30, 4, utf8_decode("Inscrição Municipal:"), "", 0, "R");
        $pdf->SetFont("arial", "B", 7);
        $pdf->Cell(79, 4, utf8_decode($inscripcionMunicipal), "R", 0, "L");

        $pdf->Ln();
        $pdf->SetFont("arial", "", 5);
        $pdf->Cell(20, 4, "", "L");
        $pdf->Cell(9, 4, utf8_decode("Endereço:"), "", 0);
        $pdf->SetFont("arial", "B", 7);
        $pdf->Cell(151, 4, utf8_decode($lineaDireccionPrestador), "R", 0, "L");

        $pdf->Ln();
        $pdf->SetFont("arial", "", 5);
        $pdf->Cell(20, 4, "", "LB");
        $pdf->Cell(9, 4, utf8_decode("Município:"), "B", 0, "L");
        $pdf->SetFont("arial", "B", 7);
        $pdf->Cell(54, 4, utf8_decode($municipio), "B", 0, "L");
        $pdf->SetFont("arial", "", 5);
        $pdf->Cell(3, 4, utf8_decode("UF:"), "B", 0, "L");
        $pdf->SetFont("arial", "B", 7);
        $pdf->Cell(64, 4, utf8_decode($uf), "B", 0, "L");
        $pdf->SetFont("arial", "", 5);
        $pdf->Cell(8, 4, utf8_decode("Telefone:"), "B", 0, "L");
        $pdf->SetFont("arial", "B", 7);
        $pdf->Cell(22, 4, $telefono, "BR");


        $pdf->Ln();
        $pdf->SetFont("arial", "B", 7);
        $pdf->Cell(180, 4, utf8_decode("TOMADOR DE SERVIÇOS"), "LR", 0, "C");

        $pdf->Ln();
        $pdf->SetFont("arial", "", 5);
        $pdf->Cell(17, 4, utf8_decode("Nome/Razão Social:"), "L", 0);
        $pdf->SetFont("arial", "B", 7);
        $pdf->Cell(163, 4, utf8_decode($tomadorRazonSocial), "R", 0, "L");

        $pdf->Ln();
        $pdf->SetFont("arial", "", 5);
        $pdf->Cell(10, 4, utf8_decode("CPF/CNPJ:"), "L", 0);
        $pdf->SetFont("arial", "B", 7);
        $pdf->Cell(170, 4, utf8_decode($tomadorCPFCNPJ), "R", 0, "L");

        $pdf->Ln();
        $pdf->SetFont("arial", "", 5);
        $pdf->Cell(10, 4, utf8_decode("Endereço:"), "L", 0);
        $pdf->SetFont("arial", "B", 7);
        $pdf->Cell(170, 4, utf8_decode($lineaDireccionTomador), "R", 0, "L");

        $pdf->Ln();
        $pdf->SetFont("arial", "", 5);
        $pdf->Cell(10, 4, utf8_decode("Municipio:"), "LB", 0);
        $pdf->SetFont("arial", "B", 7);
        $pdf->Cell(32, 4, utf8_decode($tomadorMunicipio), "B", 0, "L");
        $pdf->SetFont("arial", "", 5);
        $pdf->Cell(3, 4, utf8_decode("UF:"), "B", 0);
        $pdf->SetFont("arial", "B", 7);
        $pdf->Cell(24, 4, utf8_decode($tomadorUF), "B", 0, "L");
        $pdf->SetFont("arial", "", 5);
        $pdf->Cell(6, 4, utf8_decode("E-mail:"), "B", 0);
        $pdf->SetFont("arial", "B", 7);
        $pdf->Cell(75, 4, utf8_decode($tomadorEmail), "B", 0, "L");
        $pdf->SetFont("arial", "", 5);
        $pdf->Cell(8, 4, '', "B", 0, "L");
        $pdf->SetFont("arial", "B", 7);
        $pdf->Cell(22, 4, $tomadorTelefono, "BR");


        $pdf->Ln();
        $pdf->SetFont("arial", "B", 7);
        $pdf->Cell(180, 4, utf8_decode("DISCRIMINAÇÃO DOS SERVIÇOS"), "LR", 0, "C");

        $pdf->Ln();
        $pdf->SetFont("arial", "B", 5);
        $pdf->Cell(10, 4, utf8_decode("Descrição:"), "L", 0);
        $pdf->SetFont("arial", "", 5);
        $pdf->Cell(170, 4, utf8_decode($nombreServicio), "R", 0, "L");

        $pdf->Ln();
        $pdf->Cell(180, 20, "", "RBL");


        $pdf->Ln();
        $pdf->setFont("arial", "B", 5);
        $pdf->Cell(12, 3, utf8_decode("Tributável"), "LR", 0, "L");
        $pdf->Cell(130, 3, utf8_decode("Item"), "R", 0, "L");
        $pdf->Cell(10, 3, utf8_decode("Qtde"), "R", 0, "R");
        $pdf->Cell(12, 3, utf8_decode("Unitário R$"), "R", 0, "R");
        $pdf->Cell(16, 3, utf8_decode("Total R$"), "R", 0, "R");
        $pdf->Ln();

        for ($i = 0; $i < 40; $i++) {
            $pdf->Cell(12, 3, isset($arrItems[$i]['tributacion']) ? utf8_decode($arrItems[$i]['tributacion']) : '', "LR", 0, "C");
            $pdf->Cell(130, 3, isset($arrItems[$i]['item']) ? utf8_decode($arrItems[$i]['item']) : '', "R", 0, "L");
            $pdf->Cell(10, 3, isset($arrItems[$i]['cantidad']) ? utf8_decode($arrItems[$i]['cantidad']) : '', "R", 0, "R");
            $pdf->Cell(12, 3, isset($arrItems[$i]['precio_unitario']) ? utf8_decode($arrItems[$i]['precio_unitario']) : '', "R", 0, "R");
            $pdf->Cell(16, 3, isset($arrItems[$i]['total']) ? utf8_decode($arrItems[$i]['total']) : '', "R", 0, "R");
            $pdf->Ln();
        }


        $pdf->SetFont("arial", "", 6);
        $pdf->Cell(28, 3, "PIS($pis%):", "LTR", 0, "C");
        $pdf->Cell(42, 3, "COFINS($cofins%):", "TR", 0, "C");
        $pdf->Cell(42, 3, "INSS($inss%):", "TR", 0, "C");
        $pdf->Cell(32, 3, "IR($ir%):", "TR", 0, "C");
        $pdf->Cell(36, 3, "CSLL($csll%):", "TR", 0, "C");

        $pdf->Ln();
        $pdf->SetFont("arial", "B", 6);
        $pdf->Cell(28, 3, "R$ $valorPis", "LBR", 0, "C");
        $pdf->Cell(42, 3, "R$ $valorCofins", "BR", 0, "C");
        $pdf->Cell(42, 3, "R$ $valorInss", "BR", 0, "C");
        $pdf->Cell(32, 3, "R$ $valorIr", "BR", 0, "C");
        $pdf->Cell(36, 3, "R$ $valorCsll", "BR", 0, "C");


        $pdf->Ln();
        $pdf->setFont("arial", "B", 8);
        $pdf->Cell(180, 5, "VALOR TOTAL DA NOTA = R$ $totalDeNota", "LBR", 0, "C");


        $pdf->Ln();
        $pdf->SetFont("arial", "", 6);
        $pdf->Cell(45, 3, utf8_decode("Deduções do ISSQN:"), "LR", 0, "L");
        $pdf->Cell(45, 3, utf8_decode("Base de Cálculo do ISSQN:"), "R", 0, "L");
        $pdf->Cell(45, 3, utf8_decode("Alíquota do ISSQN:"), "R", 0, "L");
        $pdf->Cell(45, 3, utf8_decode("ISSQN Devido:"), "R", 0, "L");

        $pdf->Ln();
        $pdf->SetFont("arial", "B", 6);
        $pdf->Cell(45, 3, "R$ $deducciones", "LBR", 0, "R");
        $pdf->Cell(45, 3, "R$ $totalDeNota", "BR", 0, "R");
        $pdf->Cell(45, 3, "R$ $alicuota", "BR", 0, "R");
        $pdf->Cell(45, 3, "R$ $issqnDevido", "BR", 0, "R");


        $pdf->Ln();
        $pdf->SetFont("arial", "B", 6);
        $pdf->Cell(180, 3, utf8_decode("OUTRAS INFORMAÇÕES"), "LR", 0, "C");

        $pdf->Ln();
        $pdf->SetFont("arial", "", 5);
        $pdf->Cell(100, 3, utf8_decode("Mês de Competência da Nota Fiscal: $mesCompetencia"), "L", 0, "L");
        $pdf->Cell(80, 3, utf8_decode("Local da Prestação do Serviço: $municipio/$uf"), "R", 0, "L");

        $pdf->Ln();
        $pdf->Cell(100, 3, utf8_decode("Recolhimento: ISS A RECOLHER PELO PRESTADOR"), "L", 0, "L");
        $pdf->Cell(80, 3, utf8_decode("Tributação: TRIBUTÁVEL"), "R", 0, "L");

        $pdf->Ln();
        $pdf->Cell(180, 3, utf8_decode("RPS/SÉRIE: $rps/$serie ($fechaRps)"), "LR", 0, "L");

        $pdf->Ln();
        $pdf->Cell(100, 3, utf8_decode("CNAE: $cnae"), "L", 0, "L");
        $pdf->Cell(80, 3, utf8_decode("Descrição da Atividade: $descripcionActividad"), "R", 0, "L");

        $pdf->Ln();
        $pdf->Cell(180, 3, utf8_decode("Data de vencimento do ISSQN referente à esta NFSe: $mesVencimiento"), "LR", 0, "L");

        $pdf->Ln();
        $pdf->Cell(180, 3, utf8_decode("Serviço: $codigoServicio - Instrução, treinamento, orientação pedagógica e educacional, avaliação de conhecimentos de qualquer natureza."), "LBR", 0, "L");

        return $pdf;
    }

}

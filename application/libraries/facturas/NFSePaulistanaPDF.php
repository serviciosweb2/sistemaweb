<?php
require (APPPATH . 'libraries/pdf/fpdf/fpdf.php');


class NFSePaulistanaPDF {

    protected $nota_fiscal;
    protected $gif_brasao_prefeitura;
    protected $gif_logo_empresa;
    protected $aParser;
    protected $oConnection;
    protected $pdf;
    protected $codigoServicio;

    function percent($number){
        return $number * 100 . '%';
    }
    
    function mask($val, $mask)
    {
        $maskared = '';
        $k = 0;
        for($i = 0; $i<=strlen($mask)-1; $i++)
        {
            if($mask[$i] == '#')
            {
                if(isset($val[$k]))
                    $maskared .= $val[$k++];
            }
            else
            {
                if(isset($mask[$i]))
                    $maskared .= $mask[$i];
            }
        }
        return $maskared;
    }

    function maskNroNfse($nroNfse){
        $sizeOfNfse = strlen($nroNfse);
        $loopSizeNfse = 8 - $sizeOfNfse;
        $strNfseMascarado = "";
        for ($i = 0; $i < $loopSizeNfse; $i++) {
            $strNfseMascarado .= "0";
        }
        $strNfseMascarado .= $nroNfse;
        return $strNfseMascarado;
    }

    public function pNFSe (CI_DB_mysqli_driver $conexion, $orientation = 'P', $unit = 'mm', $format = 'A4', $cod_factura = '', $gif_brasao_prefeitura = ''){
        $this->pdf = new FPDF($orientation, $unit, $format);
        $this->oConnection = $conexion;
        $this->gif_brasao_prefeitura = $gif_brasao_prefeitura;
        $this->nota_fiscal = new Vfacturas($conexion, $cod_factura);


        // Datos Facturante
        $ptovta = new Vpuntos_venta($this->oConnection, $this->nota_fiscal->punto_venta);
        $facturante = new Vfacturantes($this->oConnection, $ptovta->cod_facturante);
        $razonsocial = new Vrazones_sociales_general($this->oConnection, $facturante->cod_razon_social);
        $municipio = Vlocalidades::listarLocalidades($this->oConnection, array('id' => $razonsocial->cod_localidad));
        $provicia = Vprovincias::listarProvincias($this->oConnection, array('id' => $municipio[0]['provincia_id']));
        $objprovincia = new Vprovincias($this->oConnection, $provicia[0]['id']);
        $codestadoFacturante = $objprovincia->get_codigo_estado();
        $prestador = Vprestador_paulistana::listarPrestador_paulistana($this->oConnection, array('cod_punto_venta' => $this->nota_fiscal->punto_venta));
        $CnpjPrestador = $razonsocial->documento;
        $CnpjPrestador = substr($CnpjPrestador, 0, 2) . "." . substr($CnpjPrestador, 2, 3) . "." . substr($CnpjPrestador, 5, 3) . "/" . substr($CnpjPrestador, 8, 4) . "-" . substr($CnpjPrestador, 12, 2);
        $endereco = $razonsocial->direccion_calle . ", " . $razonsocial->direccion_numero;

        // Datos Tomador
        $razonalumno = new Vrazones_sociales($this->oConnection, $this->nota_fiscal->codrazsoc);
        $municipioAlumno = Vlocalidades::listarLocalidades($this->oConnection, array('id' => $razonalumno->cod_localidad));
        $municipioTomador = $municipioAlumno[0]['nombre'];
        $provinciaAlumno = Vprovincias::listarProvincias($this->oConnection, array('id' => $municipio[0]['provincia_id']));
        $objprovinciatomador = new Vprovincias($this->oConnection, $provinciaAlumno[0]['id']);
        //$cpfTomador = substr($cpfTomadorA, 0,3).".".substr($cpfTomadorA, 4,6).".".substr($cpfTomadorA, 6,8)."-".substr($cpfTomadorA, 9,10);
        $cpfTomador = $this->mask($razonalumno->documento, '###.###.###-##');
        $enderecoAlumno = $razonalumno->direccion_calle . ", " . $razonalumno->direccion_numero;

        // Datos Nota Fiscal
        $datos_factura = Vseguimiento_paulistana::getAllDataFactura($conexion, $cod_factura, $conexion->database);
        $valorISS = (float)$this->nota_fiscal->total * (float)$prestador[0]['alicuota'];
        $valorISS = number_format($valorISS, 2, '.', '');

        /*                    */
        $nro_rps = $this->nota_fiscal->getPropiedad('numero_rps');

        $this->codigoServicio = "0" . $prestador[0]['codigo_tributacion_municipio']. " Ensino regular pré-escolar, fundamental e médio, inclusive cursos profissionalisantes.";
        $dataFechaEnvio = date("d/m/Y H:i:s", strtotime($datos_factura['fecha_envio']));
        $fechaEnvio = date("d/m/Y", strtotime($datos_factura['fecha_envio']));

        $datosNFSe = array();
        $datosNFSe['data_hora'] = utf8_decode($dataFechaEnvio);
        $datosNFSe['cod_verificacao'] = utf8_decode($this->mask($datos_factura['codigo_verificacion'], "####-####"));
        $datosNFSe['nro_nfse'] = utf8_decode($this->maskNroNfse($datos_factura['numero_nfse']));

        $textLabels = array();
        $title['desc_rps'] = utf8_decode("RPS Nº ". $nro_rps . " Serie " . $prestador[0]['numero_serie'] . " emitido em: " . $fechaEnvio);
        $textLabels['suma_nota'] = utf8_decode("VALOR TOTAL DA NOTA = R$ ");

        $textValues = array();
        $textValues['desc_serv'] = utf8_decode($this->codigoServicio);
        $textValues['prestador_cnpj'] = utf8_decode($CnpjPrestador);
        $textValues['prestador_razaosocial'] = utf8_decode($razonsocial->razon_social);
        $textValues['prestador_endereco'] = utf8_decode($endereco);
        $textValues['prestador_municipio'] = utf8_decode($municipio[0]['nombre']);
        $textValues['prestador_inscricaomunicipal'] = utf8_decode($prestador[0]['inscripcion_municipal']);
        $textValues['prestador_uf'] = utf8_decode($codestadoFacturante);
        $textValues['tomador_cpf'] = utf8_decode($cpfTomador);
        $textValues['tomador_nome'] = utf8_decode($razonalumno->razon_social);
        $textValues['tomador_endereco'] = utf8_decode($enderecoAlumno);
        $textValues['tomador_municipio'] = utf8_decode($municipioTomador);
        $textValues['tomador_inscricaomunicipal'] = utf8_decode("---");
        $textValues['tomador_uf'] = utf8_decode($objprovinciatomador->get_codigo_estado());
        $textValues['tomador_email'] = utf8_decode($razonalumno->email);
        $textValues['intermediario_cpf'] = utf8_decode("---");
        $textValues['intermediario_razaosocial'] = utf8_decode("---");
        $textValues['discriminacao_servicos'] = utf8_decode("PAGAMENTO DE MENSALIDADE");
        $textValues['suma_nota'] = $this->nota_fiscal->total;
        $textValues['inss'] = utf8_decode(number_format((float)$prestador[0]['valor_inss'], 2, '.', ''));
        $textValues['irpf'] = utf8_decode(number_format((float)$prestador[0]['valor_ir'], 2, '.', ''));
        $textValues['csll'] = utf8_decode(number_format((float)$prestador[0]['valor_csll'], 2, '.', ''));
        $textValues['cofins'] = utf8_decode(number_format((float)$prestador[0]['valor_cofins'], 2, '.', ''));
        $textValues['pispasep'] = utf8_decode(number_format((float)$prestador[0]['valor_pis'], 2, '.', ''));
        $textValues['suma_deduciones'] = utf8_decode(number_format((float)"0.00", 2, '.', ''));
        $textValues['base_calculo'] = $this->nota_fiscal->total;
        $textValues['aliquota'] = utf8_decode($this->percent($prestador[0]['alicuota']));
        $textValues['valor_iss'] = $valorISS;
        $textValues['credito'] = number_format((float)($valorISS * 0.3), 2, '.', '');
        $textValues['mun_prestacao_serv'] = utf8_decode("---");
        $textValues['insc_obra'] = utf8_decode("---");
        $textValues['vlr_tributos-fonte'] = utf8_decode("---");
        $textValues['outras_informacoes'] = utf8_decode("(1) Esta NFS-e foi emitida com respaldo na Lei nº 14.097/2005 (2) O crédito gerado estará disponível somente após o recolhimento desta NFS-e (3) Esta NFS-e substitui o " . "RPS Nº ". $nro_rps . " Serie " . $prestador[0]['numero_serie'] . " emitido em " . $fechaEnvio);
        //

        //var_dump($strNfseMascarado);
        //die();

// Setup
        $pdf = new FPDF('P','mm','A4');
        $pdf->AliasNbPages();
        $pdf->SetAutoPageBreak(1, 1);
        $pdf->AddPage();
        $pdf->SetFont('Arial','',10);
        $pdf->SetMargins('15','14','14');
        $pdf->Image($gif_brasao_prefeitura, 15,15,22);

// Lineas y Bordas
        $pdf->SetLineWidth(0.5);
        $pdf->Line(14,14,195,14);
        $pdf->Line(14,14,14,252);
        $pdf->Line(14,252,195,252);
        $pdf->Line(195,14,195,252);
        $pdf->Line(14,40,195,40);
        $pdf->Line(14,66,195,66);
        $pdf->Line(14,89,195,89);
        $pdf->Line(14,100,195,100);
        $pdf->Line(14,227,195,227);
        $pdf->SetLineWidth(0.1);
        $pdf->Line(157,14,157,40);
        $pdf->Line(157,22,195,22);
        $pdf->Line(157,31,195,31);
        $pdf->Line(14,190,195,190);
        $pdf->Line(14,196,195,196);
        $pdf->Line(51,196,51,203.5);
        $pdf->Line(87,196,87,203.5);
        $pdf->Line(123,196,123,203.5);
        $pdf->Line(159,196,159,203.5);
        $pdf->Line(14,203.5,195,203.5);
        $pdf->Line(52,211.5,52,219);
        $pdf->Line(89,211.5,89,219);
        $pdf->Line(116,211.5,116,219);
        $pdf->Line(158,211.5,158,219);
        $pdf->Line(14,211.5,195,211.5);
        $pdf->Line(14,219,195,219);
        $pdf->Line(84,219,84,227);
        $pdf->Line(126,219,126,227);

        $pdf->SetFont('Arial', 'B', 14);
        $pdf->Text(42.5,20, utf8_decode("PREFEITURA DO MUNICÍPIO DE SÃO PAULO"));
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Text(62,25.5, utf8_decode("SECRETARIA MUNICIPAL DE FINANÇAS"));
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Text(44,33, utf8_decode("NOTA FISCAL ELETRÔNICA DE SERVIÇOS - NFS-e"));
        $pdf->SetFont('Arial','',7);
        $pdf->Text(65,36, $title['desc_rps']);

// Numero da Nota - Label
        $pdf->SetFont('Arial','',7);
        $pdf->Text(157.6,16.75, utf8_decode("Número da Nota"));
        $pdf->Text(157.6,24.60, utf8_decode("Data e Hora de Emissão"));
        $pdf->Text(157.6,33.60, utf8_decode("Código de Verificação"));
// Numero da Nota - Fields
        $pdf->SetXY(165,17.5);
        $pdf->SetFont('Arial','B',12);
        $pdf->Cell(20,4,$datosNFSe['nro_nfse'],0,1,'C');
//
        $pdf->SetXY(158,25.5);
        $pdf->SetFont('Arial','B',11);
        $pdf->Cell(35,4,$datosNFSe['data_hora'],0,1,'C');
//
        $pdf->SetXY(165,34.5);
        $pdf->SetFont('Arial','B',12);
        $pdf->Cell(20,4,$datosNFSe['cod_verificacao'],0,1,'C');
// Titles - Label
        $pdf->SetFont('Arial','B',8);
        $pdf->Text(85,43.5, utf8_decode("PRESTADOR DE SERVIÇOS"));
        $pdf->Text(87,69.5, utf8_decode("TOMADOR DE SERVIÇOS"));
        $pdf->Text(83.5,92.5, utf8_decode("INTERMEDIARIO DE SERVIÇOS"));
        $pdf->Text(82.5,103.5, utf8_decode("DISCRIMINAÇÃO DOS SERVIÇOS"));
        $pdf->Text(90,231, utf8_decode("OUTRAS INFORMAÇÕES"));
        $pdf->SetFont('Arial','',8);
        $pdf->Text(27,199, utf8_decode("INSS (R$)"));
        $pdf->Text(63,199, utf8_decode("IRPF (R$)"));
        $pdf->Text(98,199, utf8_decode("CSLL (R$)"));
        $pdf->Text(132,199, utf8_decode("COFINS (R$)"));
        $pdf->Text(166,199, utf8_decode("PIS/PASEP (R$)"));
        $pdf->Text(15,206.5, utf8_decode("Código do Serviço"));
        $pdf->SetFont('Arial', '',7);
        $pdf->Text(16,214, utf8_decode("Valor Total das Deduções (R$)"));
        $pdf->Text(58,214, utf8_decode("Base de Calculo (R$)"));
        $pdf->Text(96,214, utf8_decode("Aliquota (%)"));
        $pdf->Text(126,214, utf8_decode("Valor do ISS (R$)"));
        $pdf->Text(169,214, utf8_decode("Crédito (R$)"));
        $pdf->Text(28,221.5, utf8_decode("Municipio da Prestação do Serviço"));
        $pdf->Text(90,221.5, utf8_decode("Numero Inscrição da Obra"));
        $pdf->Text(138,221.5, utf8_decode("Valor Aproximado dos Tributos/Fonte"));

// Fields
//
// PRESTADOR
//
        $pdf->SetFont('Arial', '',7);
        $pdf->Text(18,50, utf8_decode("CPF/CNPJ: "));
        $pdf->SetXY(31,47.75);
        $pdf->SetFont('Arial','B',7);
        $pdf->Cell(50,3,$textValues['prestador_cnpj'],0,1,'L');
//
        $pdf->SetFont('Arial', '',7);
        $pdf->Text(18,54, utf8_decode("Nome/Razão Social: "));
        $pdf->SetXY(40.7,51.75);
        $pdf->SetFont('Arial','B',7);
        $pdf->Cell(153,3,$textValues['prestador_razaosocial'],0,1,'L');
//
        $pdf->SetFont('Arial', '',7);
        $pdf->Text(18,58, utf8_decode("Endereço: "));
        $pdf->SetXY(29.5,55.75);
        $pdf->SetFont('Arial','B',7);
        $pdf->Cell(164,3,$textValues['prestador_endereco'],0,1,'L');
//
        $pdf->SetFont('Arial', '',7);
        $pdf->Text(18,62, utf8_decode("Municipio: "));
        $pdf->SetXY(29.5,59.75);
        $pdf->SetFont('Arial','B',7);
        $pdf->Cell(80,3,$textValues['prestador_municipio'],0,1,'L');
//
        $pdf->SetFont('Arial', '',7);
        $pdf->Text(112,50, utf8_decode("Inscrição Municipal: "));
        $pdf->SetXY(134,47.75);
        $pdf->SetFont('Arial','B',7);
        $pdf->Cell(50,3,$textValues['prestador_inscricaomunicipal'],0,1,'L');
//
        $pdf->SetFont('Arial', '',7);
        $pdf->Text(112,62, utf8_decode("UF: "));
        $pdf->SetXY(116.5,59.75);
        $pdf->SetFont('Arial','B',7);
        $pdf->Cell(8,3,$textValues['prestador_uf'],0,1,'L');
//
// TOMADOR
//
        $pdf->SetFont('Arial', '',7);
        $pdf->Text(18,74, utf8_decode("Nome/Razão Social: "));
        $pdf->SetXY(40.7,71.75);
        $pdf->SetFont('Arial','B',7);
        $pdf->Cell(153,3,$textValues['tomador_nome'],0,1,'L');
//
        $pdf->SetFont('Arial', '',7);
        $pdf->Text(18,78, utf8_decode("CPF/CNPJ: "));
        $pdf->SetXY(31,75.75);
        $pdf->SetFont('Arial','B',7);
        $pdf->Cell(50,3,$textValues['tomador_cpf'],0,1,'L');
//
        $pdf->SetFont('Arial', '',7);
        $pdf->Text(18,82, utf8_decode("Endereço: "));
        $pdf->SetXY(29.5,79.75);
        $pdf->SetFont('Arial','B',7);
        $pdf->Cell(164,3,$textValues['tomador_endereco'],0,1,'L');
//
        $pdf->SetFont('Arial', '',7);
        $pdf->Text(18,86, utf8_decode("Municipio: "));
        $pdf->SetXY(29.5,83.75);
        $pdf->SetFont('Arial','B',7);
        $pdf->Cell(60,3,$textValues['tomador_municipio'],0,1,'L');
//
        $pdf->SetFont('Arial', '',7);
        $pdf->Text(112,78, utf8_decode("Inscrição Municipal: "));
        $pdf->SetXY(134,75.75);
        $pdf->SetFont('Arial','B',7);
        $pdf->Cell(50,3,$textValues['tomador_inscricaomunicipal'],0,1,'L');
//
        $pdf->SetFont('Arial', '',7);
        $pdf->Text(112,86, utf8_decode("E-mail: "));
        $pdf->SetXY(120,83.75);
        $pdf->SetFont('Arial','B',7);
        $pdf->Cell(73,3,$textValues['tomador_email'],0,1,'L');
//
        $pdf->SetFont('Arial', '',7);
        $pdf->Text(95,86, utf8_decode("UF: "));
        $pdf->SetXY(99.5,83.75);
        $pdf->SetFont('Arial','B',7);
        $pdf->Cell(8,3,$textValues['prestador_uf'],0,1,'L');

// Intermediario
        $pdf->SetFont('Arial', '',7);
        $pdf->Text(18,97, utf8_decode("CPF/CNPJ: "));
        $pdf->SetXY(31,94.75);
        $pdf->SetFont('Arial','B',7);
        $pdf->Cell(35,3,$textValues['intermediario_cpf'],0,1,'L');
//
        $pdf->SetFont('Arial', '',7);
        $pdf->Text(70,97, utf8_decode("Nome/Razão Social: "));
        $pdf->SetXY(93,94.75);
        $pdf->SetFont('Arial','B',7);
        $pdf->Cell(100,3,$textValues['intermediario_razaosocial'],0,1,'L');

// Discriminação Servicos
        $pdf->SetXY(18,108);
        $pdf->SetFont('Courier','',7);
        $pdf->MultiCell(170,3,$textValues['discriminacao_servicos'], 0);

        // Codigo do Serviço
        $pdf->SetXY(15,208);
        $pdf->SetFont('Arial','B',8);
        $pdf->Cell(178,3,$textValues['desc_serv'],0,1,'L');

// Suma Nota
        $pdf->SetFont('Arial','B',10);
        $pdf->SetXY(67.5,190.5);
        $pdf->Cell(80,5,$textLabels['suma_nota'].$textValues['suma_nota'],0,1,'C');

// Impuestos
        $pdf->SetXY(23,200);
        $pdf->SetFont('Arial','B',7);
        $pdf->Cell(20,3,$textValues['inss'],0,1,'C');
//
        $pdf->SetXY(60,200);
        $pdf->SetFont('Arial','B',7);
        $pdf->Cell(20,3,$textValues['irpf'],0,1,'C');
//
        $pdf->SetXY(95,200);
        $pdf->SetFont('Arial','B',7);
        $pdf->Cell(20,3,$textValues['csll'],0,1,'C');//
//
        $pdf->SetXY(130,200);
        $pdf->SetFont('Arial','B',7);
        $pdf->Cell(20,3,$textValues['cofins'],0,1,'C');
//
        $pdf->SetXY(167,200);
        $pdf->SetFont('Arial','B',7);
        $pdf->Cell(20,3,$textValues['pispasep'],0,1,'C');
// Deduciones
        $pdf->SetXY(23,215);
        $pdf->SetFont('Arial','B',7);
        $pdf->Cell(20,3,$textValues['suma_deduciones'],0,1,'C');
// Base de Calculo
        $pdf->SetXY(60,215);
        $pdf->SetFont('Arial','B',7);
        $pdf->Cell(20,3,$textValues['base_calculo'],0,1,'C');
// Aliquota
        $pdf->SetXY(93,215);
        $pdf->SetFont('Arial','B',7);
        $pdf->Cell(20,3,$textValues['aliquota'],0,1,'C');
// valor ISS
        $pdf->SetXY(126,215);
        $pdf->SetFont('Arial','B',7);
        $pdf->Cell(20,3,$textValues['valor_iss'],0,1,'C');
// Credito
        $pdf->SetXY(166,215);
        $pdf->SetFont('Arial','B',7);
        $pdf->Cell(20,3,$textValues['credito'],0,1,'C');

// Municipio Prestacao Servico
        $pdf->SetXY(36, 223);
        $pdf->SetFont('Arial','B',7);
        $pdf->Cell(20,3,$textValues['mun_prestacao_serv'],0,1,'C');
//
        $pdf->SetXY(94, 223);
        $pdf->SetFont('Arial','B',7);
        $pdf->Cell(20,3,$textValues['insc_obra'],0,1,'C');
//
        $pdf->SetXY(148, 223);
        $pdf->SetFont('Arial','B',7);
        $pdf->Cell(20,3,$textValues['vlr_tributos-fonte'],0,1,'C');
        //
        $pdf->SetFont('Arial','',8);
        $pdf->SetXY(15,233);
        $pdf->MultiCell(175,3,$textValues['outras_informacoes'],0);

        return $pdf;
    }
}

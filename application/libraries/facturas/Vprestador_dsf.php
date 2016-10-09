<?php

/**
* Class Vprestador_dsf
*
*Class  Vprestador_dsf maneja todos los aspectos de alumnos
*
* @package  SistemaIGA
* @subpackage Alumnos
* @author   Ivan berthillod <ivan.sys@gmail.com>
* @author   Aquiles Gonzalez <sistemas1@iga-la.net>
* @version  $Revision: 1.1 $
* @access   private
*/
class Vprestador_dsf extends Tprestador_dsf{

    private $valorTotalServicio;            
    private $fechaEnvioDesde;
    private $fechaEnvioHasta;
    private $urlWebService = "http://issdigital.campinas.sp.gov.br/WsNFe2/LoteRps.jws";
    private $valorTotalDeducciones = 0;
    private $version = 1;
    private $metodoEnvio = "WS";
    
    function __construct(CI_DB_mysqli_driver $conexion, $codigo = null) {
        parent::__construct($conexion, $codigo);
    }

    protected function enviar($xml, array $header = null, $certPub = null, $certPri = null){
        if ($header == null){
            $header = array('SOAPAction: ""; Content-Type: text/xml; charset=utf-8; ', 'Content-Length: '.strlen($xml));
        }
        $soap_do = curl_init();
        curl_setopt($soap_do, CURLOPT_URL, $this->urlWebService);
        curl_setopt($soap_do, CURLOPT_RETURNTRANSFER, 1 );
        curl_setopt($soap_do, CURLOPT_TIMEOUT, 1500 );
        curl_setopt($soap_do, CURLOPT_HTTPHEADER, $header);
        curl_setopt($soap_do, CURLOPT_POST, 1 );
        curl_setopt($soap_do, CURLOPT_POSTFIELDS,$xml);
        if ($certPub != null && $certPri != null){
            curl_setopt($soap_do, CURLOPT_SSLCERT, $certPub);
            curl_setopt($soap_do, CURLOPT_SSLKEY, $certPri);
            curl_setopt($soap_do, CURLOPT_SSLVERSION, 3);
            curl_setopt($soap_do, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($soap_do, CURLOPT_SSL_VERIFYPEER, 0);
        }
        
        $respuesta=curl_exec($soap_do);
        curl_close($soap_do);
        return $respuesta;
    }
    
    private function procesarRespuesta($stringRespuesta, $nombreCabeceraLeer){
        $startCabecera = "<$nombreCabeceraLeer>";
        $endCabecera = "</$nombreCabeceraLeer>";
        $stringRespuesta = html_entity_decode($stringRespuesta);
        $pos1 = strpos($stringRespuesta, $startCabecera);
        $pos2 = strpos($stringRespuesta, $endCabecera) + strlen($endCabecera);
        $xml = substr($stringRespuesta, $pos1, $pos2 - $pos1);
        $xmldoc = simplexml_load_string($xml);
        return $xmldoc;
    }
    
    private function getXMLHead($action){
        $xml = '';
        $xml .= "<?xml version='1.0' encoding='UTF-8'?>";
        $xml .= "<soapenv:Envelope xmlns:xsi='http://www.w3.org/2001/XMLSchema-instance' xmlns:xsd='http://www.w3.org/2001/XMLSchema' xmlns:soapenv='http://schemas.xmlsoap.org/soap/envelope/'	xmlns:dsf='http://dsfnet.com.br'>";
	$xml .= "<soapenv:Body>";
        $xml .= "<dsf:$action soapenv:encodingStyle='http://schemas.xmlsoap.org/soap/encoding/'>";
        $xml .= "<mensagemXml xsi:type='xsd:string'>";
        $xml .= "<![CDATA[";
        return $xml;
    }
    
    private function getXMLFoot($action){
        $xml = '';
        $xml .= "]]>";
	$xml .= "</mensagemXml>";
        $xml .= "</dsf:$action>";
	$xml .= "</soapenv:Body>";
        $xml .= "</soapenv:Envelope>";
        return $xml;
    }
    
    private function consultarSequenciaRPS($codigoMunicipioSiafi, $cnpj){
        $accionEnvio = "consultarSequencialRps";
        $xml = '';
        $xml .= $this->getXMLHead($accionEnvio);
        $xml .= "<ns1:ConsultaSeqRps xmlns:ns1='http://localhost:8080/WsNFe2/lote' xmlns:tipos='http://localhost:8080/WsNFe2/tp' xmlns:xsi='http://www.w3.org/2001/XMLSchema-instance' xsi:schemaLocation='http://localhost:8080/WsNFe2/lote http://localhost:8080/WsNFe2/xsd/ConsultaSeqRps.xsd'>";
        $xml .= "<Cabecalho>";
        $xml .= "<CodCid>{$codigoMunicipioSiafi}</CodCid>";
        $xml .= "<IMPrestador>{$this->inscripcion_municipal}</IMPrestador>";
        $xml .= "<CPFCNPJRemetente>{$cnpj}</CPFCNPJRemetente>";
        $xml .= "<SeriePrestacao>99</SeriePrestacao>";
        $xml .= "<Versao>1</Versao>";
        $xml .= "</Cabecalho>";
        $xml .= "</ns1:ConsultaSeqRps>";
        $xml .= $this->getXMLFoot($accionEnvio);
        $respuesta = $this->enviar($xml);
        if (strpos($respuesta, "Cabecalho")){
            $respuesta = $this->procesarRespuesta($respuesta, "Cabecalho");
            return $respuesta->NroUltimoRps + 1;
        } else {
            throw new Exception("No se ha obtenido respuesta del serividor DSF al consultar secuencia RPS");
        }
    }
    
    static private function getOperacaoValue($operacionID){
        $resp = "A";
        switch ($operacionID) {
            case 1:
                $resp = "A";
                break;            
            case 2:
                $resp = "B";
                break;            
            case 3:
                $resp = "C";
                break;            
            case 4:
                $resp = "D";
                break;            
            case 5:
                $resp = "J";
                break;
            
            default:
                break;
        }
        return $resp;
    }   
    
    static private function getTributacaoValue($tributacaoID){
        $resp = "C";
        switch ($tributacaoID) {
            case 1:
                $resp = "C";
                break;
            case 2:
                $resp = "E";
                break;
            case 3:
                $resp = "F";
                break;
            case 4:
                $resp = "K";
                break;
            case 5:
                $resp = "N";
                break;
            case 6:
                $resp = "T";
                break;
            case 7:
                $resp = "G";
                break;
            case 8:
                $resp = "H";
                break;
            case 9:
                $resp = "M";
                break;

            default:
                break;
        }
        return $resp;
        //C - Isenta de ISS
        //E - Não Incidência no Município
        //F - Imune
        //K - Exigibilidd Susp.Dec.J/Proc.A
        //N - Não Tributável
        //T – Tributável
        //G - Tributável Fixo
        //H - Tributável S.N.
        //M - Micro Empreendedor Individual (MEI)        
    }
    
    private function getXMLRPS(CI_DB_mysqli_driver $conexion, $numeroRPS, $importeServicio, Vrazones_sociales_general $myRazonsocialFacturante, Vrazones_sociales $myRazonSocialTomador){
        $myLocalidadFacturante = new Vlocalidades($conexion, $myRazonsocialFacturante->cod_localidad);
        $myLocalidadTomador = new Vlocalidades($conexion, $myRazonSocialTomador->cod_localidad);
        $xml = '';
        $im = $this->inscripcion_municipal;        
        $serieRPS = "NF";
        $tipoRPS = "RPS";
        $situacionRPS = "N";
        $seriePrestacion = "99";
        $dataEmissaoRPS = date("Y-m-d")."T".date("H:i:s");
        $dataEmisao = str_replace("-", "", substr($dataEmissaoRPS, 0, 10));
        $tributacao = self::getTributacaoValue($this->regimen_especial_tributario);
        $situacaoRPS = "N";
        $tipoRecolhimento = "A";
        $tipoRecolhimentoAssinar = $tipoRecolhimento == "A" ? "N" : "S"; // $tipoRecolhimento puede dejar de estar seteado fijo y tomar datos de algun lugar
        $valorServicio = (float)($importeServicio);
        if (strpos($valorServicio, ".")){
            if (strpos($valorServicio, '.') == strlen($valorServicio) - 2){
               $valorServicio = $valorServicio."0";
           }  
        } else {
            $valorServicio = $valorServicio .".00";
        }
        $valorServicio = str_replace(".", "", $valorServicio); 
        $valorDeducciones = 0;
        $cnpj_cpf = preg_replace('/[^0-9]/','',$myRazonsocialFacturante->codigo_postal);        
        $im = str_pad($im, 11, "0", STR_PAD_LEFT);
        $serieRPSAssinar = str_pad($serieRPS, 5, " ", STR_PAD_RIGHT);
        $nroRPS = str_pad($numeroRPS, 12, "0", STR_PAD_LEFT);
        $tributacaoAssinar = str_pad($tributacao, 2, " ", STR_PAD_RIGHT);
        $valorServicio = str_pad($valorServicio, 15, "0", STR_PAD_LEFT);
        $valorDeducciones = str_pad($valorDeducciones, 11, "0", STR_PAD_LEFT);
        $codigoActividad = str_pad($this->codigo_actividad, 14, "0", STR_PAD_LEFT);
        $codigoAtividade = str_pad($this->codigo_actividad, 9, '0', STR_PAD_LEFT);
        $cnpj_cpf = str_pad($cnpj_cpf, 14, "0", STR_PAD_LEFT);
        $operacao = self::getOperacaoValue($this->tipo_nota);
        $cantidadServicio = 1;
        $valorTotalServicio = $cantidadServicio * $importeServicio;
        $DDD_prestador = $myRazonsocialFacturante->telefono_cod_area == '' ? "000" : substr($myRazonsocialFacturante->telefono_cod_area, 0, 3);
        $campo = $im.$serieRPSAssinar.$nroRPS.$dataEmisao.$tributacaoAssinar.$situacaoRPS.$tipoRecolhimentoAssinar.$valorServicio.$valorDeducciones.$codigoActividad.$cnpj_cpf;
        $assinatura =  sha1($campo);
        $cpfTomador = preg_replace('/[^0-9]/','',$myRazonSocialTomador->documento);
        $imTomador = '0000000';
        $tipoDireccionTomador = '-';
        $tipoBarrioTomador = '';
        $barrioTomador = 'NO BARRIO';
        $CEP =  preg_replace('/[^0-9]/','',$myRazonSocialTomador->codigo_postal);
        $CEP = str_pad($CEP, 8, "8", STR_PAD_LEFT);
        $arrTelefono = $myRazonSocialTomador->telfonoRazonSocial();
        $DDD_tomador = isset($arrTelefono[0]) && $arrTelefono[0]['cod_area'] == '' || $arrTelefono[0]['cod_area'] == 0 ? "000" : substr($arrTelefono[0]['cod_area'], 0, 3);
        $telefonoTomador = isset($arrTelefono[0]) && $arrTelefono[0]['codigo'] <> '' ? $arrTelefono[0]['codigo'] : '00000000';
        $telefonoTomador = preg_replace('/[^0-9]/','',$telefonoTomador);
        $telefonoTomador = substr($telefonoTomador, 0, 8);
        $telefonoTomador = str_pad($telefonoTomador, 8, "0", STR_PAD_LEFT);                
        $xml .= "<RPS Id='rps:{$nroRPS}'>";
        $xml .= "<Assinatura>{$assinatura}</Assinatura>";
        $xml .= "<InscricaoMunicipalPrestador>{$this->inscripcion_municipal}</InscricaoMunicipalPrestador>";
        $xml .= "<RazaoSocialPrestador>{$myRazonsocialFacturante->razon_social}</RazaoSocialPrestador>";
        $xml .= "<TipoRPS>{$tipoRPS}</TipoRPS>";
        $xml .= "<SerieRPS>{$serieRPS}</SerieRPS>";
        $xml .= "<NumeroRPS>{$numeroRPS}</NumeroRPS>"; // ver de cambiar este valor
        $xml .= "<DataEmissaoRPS>{$dataEmissaoRPS}</DataEmissaoRPS>";
        $xml .= "<SituacaoRPS>$situacionRPS</SituacaoRPS>";
        $xml .= "<SeriePrestacao>{$seriePrestacion}</SeriePrestacao>";
        $xml .= "<InscricaoMunicipalTomador>{$imTomador}</InscricaoMunicipalTomador>";
        $xml .= "<CPFCNPJTomador>{$cpfTomador}</CPFCNPJTomador>";
        $xml .= "<RazaoSocialTomador>{$myRazonSocialTomador->razon_social}</RazaoSocialTomador>";
        $xml .= "<TipoLogradouroTomador>{$tipoDireccionTomador}</TipoLogradouroTomador>";
        $xml .= "<LogradouroTomador>{$myRazonSocialTomador->direccion_calle}</LogradouroTomador>";
        $xml .= "<NumeroEnderecoTomador>{$myRazonSocialTomador->direccion_numero}</NumeroEnderecoTomador>";
        $xml .= "<TipoBairroTomador>{$tipoBarrioTomador}</TipoBairroTomador>";
        $xml .= "<BairroTomador>{$barrioTomador}</BairroTomador>";
        $xml .= "<CidadeTomador>{$myLocalidadTomador->get_codigo_siafi()}</CidadeTomador>";
        $xml .= "<CidadeTomadorDescricao/>";
        $xml .= "<CEPTomador>{$CEP}</CEPTomador>";
        $xml .= "<EmailTomador>{$myRazonSocialTomador->email}</EmailTomador>";
        $xml .= "<CodigoAtividade>{$codigoAtividade}</CodigoAtividade>";
        $xml .= "<AliquotaAtividade>{$this->alicuota}</AliquotaAtividade>";
        $xml .= "<TipoRecolhimento>{$tipoRecolhimento}</TipoRecolhimento>";
        $xml .= "<MunicipioPrestacao>{$myLocalidadFacturante->get_codigo_siafi()}</MunicipioPrestacao>";
        $xml .= "<MunicipioPrestacaoDescricao>{$myLocalidadFacturante->nombre}</MunicipioPrestacaoDescricao>";
        $xml .= "<Operacao>{$operacao}</Operacao>";
        $xml .= "<Tributacao>{$tributacao}</Tributacao>";
        $xml .= "<ValorPIS>{$this->valor_pis}</ValorPIS>";
        $xml .= "<ValorCOFINS>{$this->valor_cofins}</ValorCOFINS>";
        $xml .= "<ValorINSS>{$this->valor_inss}</ValorINSS>";
        $xml .= "<ValorIR>{$this->valor_ir}</ValorIR>";
        $xml .= "<ValorCSLL>{$this->valor_csll}</ValorCSLL>";
        $xml .= "<AliquotaPIS>{$this->alicuota_pis}</AliquotaPIS>";
        $xml .= "<AliquotaCOFINS>{$this->alicuota_cofins}</AliquotaCOFINS>";
        $xml .= "<AliquotaINSS>{$this->alicuota_inss}</AliquotaINSS>";
        $xml .= "<AliquotaIR>{$this->alicuota_ir}</AliquotaIR>";
        $xml .= "<AliquotaCSLL>{$this->alicuota_csll}</AliquotaCSLL>";
        $xml .= "<DescricaoRPS>{$this->nombre_servicio}</DescricaoRPS>";
        $xml .= "<DDDPrestador>{$DDD_prestador}</DDDPrestador>";
        $xml .= "<TelefonePrestador>{$myRazonsocialFacturante->telefono_numero}</TelefonePrestador>";
        $xml .= "<DDDTomador>{$DDD_tomador}</DDDTomador>";
        $xml .= "<TelefoneTomador>{$telefonoTomador}</TelefoneTomador>";
        $xml .= "<Itens>";
        $xml .= "<Item>";
        $xml .= "<DiscriminacaoServico>{$this->nombre_servicio}</DiscriminacaoServico>";
        $xml .= "<Quantidade>{$cantidadServicio}</Quantidade>";
        $xml .= "<ValorUnitario>{$importeServicio}</ValorUnitario>";
        $xml .= "<ValorTotal>{$valorTotalServicio}</ValorTotal>";
        $xml .= "</Item>";
        $xml .= "</Itens>";
        $xml .= "</RPS>";
        return utf8_encode($xml);
    }
    
    private function getXMLXDATA($arrRPS, $nroLote, Vrazones_sociales_general $myRazonSocialFacturante, $codigoSiafiFacturante){
        $xmlCDATA = '';
        $cantidadRPS = count($arrRPS);
        $fechaInicio = substr($this->fechaEnvioDesde, 0, 10);
        $fechaFin = substr($this->fechaEnvioHasta, 0, 10);
        $valorTotalServicio = round($this->valorTotalServicio, 2);
        $valorTotalDeducciones = round($this->valorTotalDeducciones, 2);
        $xmlCDATA .= "<ns1:ReqEnvioLoteRPS xmlns:ns1='http://localhost:8080/WsNFe2/lote' xmlns:tipos='http://localhost:8080/WsNFe2/tp' xmlns:xsi='http://www.w3.org/2001/XMLSchema-instance' xsi:schemaLocation='http://localhost:8080/WsNFe2/lote http://localhost:8080/WsNFe2/xsd/ReqEnvioLoteRPS.xsd'>";
        $xmlCDATA .= "<Cabecalho>";
        $xmlCDATA .= "<CodCidade>{$codigoSiafiFacturante}</CodCidade>";
        $xmlCDATA .= "<CPFCNPJRemetente>{$myRazonSocialFacturante->documento}</CPFCNPJRemetente>";
        $xmlCDATA .= "<RazaoSocialRemetente>{$myRazonSocialFacturante->razon_social}</RazaoSocialRemetente>";
        $xmlCDATA .= "<transacao>true</transacao>";
        $xmlCDATA .= "<dtInicio>$fechaInicio</dtInicio>";
        $xmlCDATA .= "<dtFim>$fechaFin</dtFim>";
        $xmlCDATA .= "<QtdRPS>$cantidadRPS</QtdRPS>";
        $xmlCDATA .= "<ValorTotalServicos>{$valorTotalServicio}</ValorTotalServicos>";
        $xmlCDATA .= "<ValorTotalDeducoes>{$valorTotalDeducciones}</ValorTotalDeducoes>";
        $xmlCDATA .= "<Versao>{$this->version}</Versao>";
        $xmlCDATA .= "<MetodoEnvio>{$this->metodoEnvio}</MetodoEnvio>";
        $xmlCDATA .= "</Cabecalho>";
        $xmlCDATA .= "<Lote Id='lote:$nroLote'>";
        foreach ($arrRPS as $rps){
            $xmlCDATA .= $rps;
        }
        $xmlCDATA .= "</Lote>";
        $xmlCDATA .= "</ns1:ReqEnvioLoteRPS>";
        return $xmlCDATA;
    }
    
    private function getNumeroLote(){
        return "3309"; // ver este numero, segun el manual DSF ¿ es campo libre ????
    }
    
    static private function getCertificado($pathCertificadoPub){
        $pubKey = $pathCertificadoPub;
        $data = '';
        $arCert = explode("\n", $pubKey);
        foreach ($arCert AS $curData) {
            if (strncmp($curData, '-----BEGIN CERTIFICATE', 22) != 0 && strncmp($curData, '-----END CERTIFICATE', 20) != 0 ) {
                $data .= trim($curData);
            }
        }
        return $data;
    }
    
    static private function getSignature($xmlAssinar, $digestValue, $nombreNodo, $pathCertificadoPub, $pathCertificadoPri){
        $priv_key = $pathCertificadoPri;
        $pkeyid = openssl_get_privatekey($priv_key);
        $xmldoc = new DOMDocument('1.0', 'utf-8');
        $xmldoc->preservWhiteSpace = false; 
        $xmldoc->formatOutput = false;
        $order = array("\r\n", "\n", "\r", "\t");
        $replace = '';
        $docxml = str_replace($order, $replace, $xmlAssinar);
        if ($xmldoc->loadXML($docxml,LIBXML_NOBLANKS | LIBXML_NOEMPTYTAG)){
            $root = $xmldoc->documentElement;
        } else {
            $this->errMsg = "Erro ao carregar XML, provavel erro na passagem do parâmetro docXML!!\n";
            $this->errStatus = true;
            return false;
        }
        $node = $xmldoc->getElementsByTagName($nombreNodo)->item(0);
        $id = trim($node->getAttribute("Id"));
        $Signature = $xmldoc->createElementNS("http://www.w3.org/2000/09/xmldsig#",'Signature');
        $root->appendChild($Signature);
        $SignedInfo = $xmldoc->createElement('SignedInfo');
        $Signature->appendChild($SignedInfo);
        $newNode = $xmldoc->createElement('CanonicalizationMethod');
        $SignedInfo->appendChild($newNode);
        $newNode->setAttribute('Algorithm', "http://www.w3.org/TR/2001/REC-xml-c14n-20010315");
        $newNode = $xmldoc->createElement('SignatureMethod');
        $SignedInfo->appendChild($newNode);
        $newNode->setAttribute('Algorithm', "http://www.w3.org/2000/09/xmldsig#rsa-sha1");
        $Reference = $xmldoc->createElement('Reference');
        $SignedInfo->appendChild($Reference);
        $Reference->setAttribute('URI', '#'.$id);
        $Transforms = $xmldoc->createElement('Transforms');
        $Reference->appendChild($Transforms);
        $newNode = $xmldoc->createElement('Transform');
        $Transforms->appendChild($newNode);
        $newNode->setAttribute('Algorithm', "http://www.w3.org/2000/09/xmldsig#enveloped-signature");
        $newNode = $xmldoc->createElement('Transform');
        $Transforms->appendChild($newNode);
        $newNode->setAttribute('Algorithm', "http://www.w3.org/TR/2001/REC-xml-c14n-20010315");
        $newNode = $xmldoc->createElement('DigestMethod');
        $Reference->appendChild($newNode);
        $newNode->setAttribute('Algorithm', "http://www.w3.org/2000/09/xmldsig#sha1");
        $newNode = $xmldoc->createElement('DigestValue',$digestValue);
        $Reference->appendChild($newNode);
        $dados = $SignedInfo->C14N(false,false,NULL,NULL);
        $signature = '';
        $resp = openssl_sign($dados,$signature,$pkeyid);
        $signatureValue = base64_encode($signature);
        $newNode = $xmldoc->createElement('SignatureValue',$signatureValue);
        $Signature->appendChild($newNode);
        $KeyInfo = $xmldoc->createElement('KeyInfo');
        $Signature->appendChild($KeyInfo);
        $X509Data = $xmldoc->createElement('X509Data');
        $KeyInfo->appendChild($X509Data);
        $cert = self::getCertificado($pathCertificadoPub);
        $newNode = $xmldoc->createElement('X509Certificate',$cert);
        $X509Data->appendChild($newNode);
        $docxml = $xmldoc->saveXML();
        openssl_free_key($pkeyid);
        $docxml = str_replace('<?xml version="1.0"?>', "", $docxml);
        return $docxml;
    }
    
    static private function getDigestValue($docxml, $tagName){
        $order = array("\r\n", "\n", "\r", "\t");
        $replace = '';
        $docxml = str_replace($order, $replace, $docxml);
        $xmldoc = new DOMDocument('1.0', 'utf-8');
        $xmldoc->preservWhiteSpace = false;
        $xmldoc->formatOutput = false;

        if ($xmldoc->loadXML($docxml,LIBXML_NOBLANKS | LIBXML_NOEMPTYTAG)){
            $root = $xmldoc->documentElement;
        } else {
            echo "El xml Enviado para calculo de Digest Value es invalido<br>";
            return false;
        }

        $node = $xmldoc->getElementsByTagName($tagName)->item(0);
        $datos = $node->C14N(false,false,NULL,NULL);
        $hashValue = hash('sha1',$datos,true);
        $digValue = base64_encode($hashValue);
        return $digValue;
    }
    
    private function getXMLEnvioLoteRPS($arrRPS, Vrazones_sociales_general $myRazonSocialFacturante, $codigoSiafiFacturante, Vfacturantes_certificados $myCertificado){
        $metodoEnvio = "enviar";
        $nroLote = $this->getNumeroLote();
        $xmlCDATA = $this->getXMLXDATA($arrRPS, $nroLote, $myRazonSocialFacturante, $codigoSiafiFacturante);
        $digestValue = self::getDigestValue($xmlCDATA, "Lote");
        $xmlCDATA = $this->getSignature($xmlCDATA, $digestValue, "Lote", $myCertificado->pub_key, $myCertificado->pry_key);
        $xml = '';
        $xml .= $this->getXMLHead($metodoEnvio);
        $xml .= $xmlCDATA;
        $xml .= $this->getXMLFoot($metodoEnvio);
        return $xml;
    }
    
    public function getXMLFacturaAprobada(CI_DB_mysqli_driver $conexion, $codFactura){
        $myFactura = new Vfacturas($conexion, $codFactura);
        $myPuntoVenta = new Vpuntos_venta($conexion, $myFactura->punto_venta);        
        $myFacturante = new Vfacturantes($conexion, $myPuntoVenta->cod_facturante);
        $myCertificado = $myFacturante->getCertificado();
        $myRazonSocialFacturante = new Vrazones_sociales_general($conexion, $myFacturante->cod_razon_social);
        $myLocalidad = new Vlocalidades($conexion, $myRazonSocialFacturante->cod_localidad);
        $numeroRPS = $this->consultarSequenciaRPS($myLocalidad->get_codigo_siafi(), $myRazonSocialFacturante->documento);
        $arrRPS = array();
        $arrFacturasRPS = array();            
        $myRazonSocialTomador = new Vrazones_sociales($conexion, $myFactura->codrazsoc);
        $arrFacturasRPS[$myFactura->getCodigo()] = $numeroRPS;
        $arrRPS[] = $this->getXMLRPS($conexion, $numeroRPS, $myFactura->total, $myRazonSocialFacturante, $myRazonSocialTomador);
        $fechaEnvio = date("Y-m-d H:i:s");
        $this->fechaEnvioDesde = $fechaEnvio;
        $this->fechaEnvioHasta = $fechaEnvio;
        $xml = $this->getXMLEnvioLoteRPS($arrRPS, $myRazonSocialFacturante, $myLocalidad->get_codigo_siafi(), $myCertificado);
        return $xml;        
    }
    
    /* todas las facturas deben ser del mismo facturante y de la misma filial*/
    public function enviarFacturas(CI_DB_mysqli_driver $conexion, array $facturas){
        $myFactura = new Vfacturas($conexion, $facturas[0]['codigo']);
        $myPuntoVenta = new Vpuntos_venta($conexion, $myFactura->punto_venta);        
        $myFacturante = new Vfacturantes($conexion, $myPuntoVenta->cod_facturante);
        $myCertificado = $myFacturante->getCertificado();
        $myRazonSocialFacturante = new Vrazones_sociales_general($conexion, $myFacturante->cod_razon_social);
        $myLocalidad = new Vlocalidades($conexion, $myRazonSocialFacturante->cod_localidad);
        $numeroRPS = $this->consultarSequenciaRPS($myLocalidad->get_codigo_siafi(), $myRazonSocialFacturante->documento);
        $arrRPS = array();
        $arrFacturasRPS = array();
        foreach ($facturas as $factura){
            $myFactura = new Vfacturas($conexion, $factura['codigo']);
            $myRazonSocialTomador = new Vrazones_sociales($conexion, $myFactura->codrazsoc);
            $arrFacturasRPS[$myFactura->getCodigo()] = $numeroRPS;
            $arrRPS[] = $this->getXMLRPS($conexion, $numeroRPS, $myFactura->total, $myRazonSocialFacturante, $myRazonSocialTomador);
            $numeroRPS ++;
        }
        $fechaEnvio = date("Y-m-d H:i:s");
        $this->fechaEnvioDesde = $fechaEnvio;
        $this->fechaEnvioHasta = $fechaEnvio;
        $xml = $this->getXMLEnvioLoteRPS($arrRPS, $myRazonSocialFacturante, $myLocalidad->get_codigo_siafi(), $myCertificado);
        $respuesta = $this->enviar($xml);
        if (strpos($respuesta, "Sucesso")){
            $xmlResp = $this->procesarRespuesta($respuesta, "Cabecalho");
            $respuesta = str_replace("'", '"', $respuesta);
            if (isset($xmlResp->Sucesso) && $xmlResp->Sucesso == "true"){
                $nroLote = $xmlResp->NumeroLote;
                $fecha = str_replace("T", " ", substr($xmlResp->DataEnvioLote, 0, 19));
                $resp = true;
                foreach ($arrFacturasRPS as $codigoFactura => $numeroFactura){
                    $myFactura = new Vfacturas($conexion, $codigoFactura);
                    $resp = $resp && $myFactura->setEstado(Vfacturas::getEstadoEnviado());
                    $resp = $resp && $myFactura->setPropiedad("numero_factura", $numeroFactura);
                    $mySeguimientoDSF = new Vseguimiento_dsf($conexion);
                    $mySeguimientoDSF->cod_factura = $myFactura->getCodigo();
                    $mySeguimientoDSF->cod_filial = $conexion->database;
                    $mySeguimientoDSF->numero_lote = $nroLote;
                    $mySeguimientoDSF->fecha_envio_lote = $fecha;
                    $mySeguimientoDSF->numero_rps = $numeroFactura;
                    $mySeguimientoDSF->estado = Vfacturas::getEstadoEnviado();
                    $resp = $resp && $mySeguimientoDSF->guardarSeguimiento_dsf();
                }
                return true;
            } else {
                $respuesta = str_replace("'", '"', $respuesta);
                foreach ($arrFacturasRPS as $codigoFactura => $numeroFactura){
                    $myFactura = new Vfacturas($conexion, $codigoFactura);
                    $myFactura->setEstado(Vfacturas::getEstadoError());
                    $mySeguimientoDSF = new Vseguimiento_dsf($conexion);
                    $mySeguimientoDSF->cod_factura = $myFactura->getCodigo();
                    $mySeguimientoDSF->cod_filial = $conexion->database;
                    $mySeguimientoDSF->respuesta = $respuesta;
                    $mySeguimientoDSF->estado = Vfacturas::getEstadoError();
                    $mySeguimientoDSF->guardarSeguimiento_dsf();                    
                }
                return false;
            }
        } else {
            $respuesta = str_replace("'", '"', $respuesta);
            foreach ($arrFacturasRPS as $codigoFactura => $numeroFactura){
                $myFactura = new Vfacturas($conexion, $codigoFactura);
                $myFactura->setEstado(Vfacturas::getEstadoError());
                $mySeguimientoDSF = new Vseguimiento_dsf($conexion);
                $mySeguimientoDSF->cod_factura = $myFactura->getCodigo();
                $mySeguimientoDSF->cod_filial = $conexion->database;
                $mySeguimientoDSF->respuesta = $respuesta;
                $mySeguimientoDSF->estado = Vfacturas::getEstadoError();
                $mySeguimientoDSF->guardarSeguimiento_dsf();                    
            }
            return false;
        }
    }
    
    public function verificar(CI_DB_mysqli_driver $conexion, $numeroLote){
        $myPuntoVenta = new Vpuntos_venta($conexion, $this->cod_punto_venta);
        $myFacturante = new Vfacturantes($conexion, $myPuntoVenta->cod_facturante);
        $myRazonSocial = new Vrazones_sociales_general($conexion, $myFacturante->cod_razon_social);
        $myLocalidad = new Vlocalidades($conexion, $myRazonSocial->cod_localidad);
        $accionEnvio = "consultarLote";
        $xml = '';
        $xml .= $this->getXMLHead($accionEnvio);
        $xml .= "<ns1:ReqConsultaLote xmlns:ns1='http://localhost:8080/WsNFe2/lote' xmlns:tipos='http://localhost:8080/WsNFe2/tp' xmlns:xsi='http://www.w3.org/2001/XMLSchema-instance' xsi:schemaLocation='http://localhost:8080/WsNFe2/lote http://localhost:8080/WsNFe2/xsd/ReqConsultaLote.xsd'>";
	$xml .= "<Cabecalho>";
	$xml .= "<CodCidade>{$myLocalidad->get_codigo_siafi()}</CodCidade>";
	$xml .= "<CPFCNPJRemetente>{$myRazonSocial->documento}</CPFCNPJRemetente>";
	$xml .= "<Versao>1</Versao>";
	$xml .= "<NumeroLote>$numeroLote</NumeroLote>";
	$xml .= "</Cabecalho>";
        $xml .= "</ns1:ReqConsultaLote>";
        $xml .= $this->getXMLFoot($accionEnvio);
        $respuesta = $this->enviar($xml);
        
        $xmlResp = $this->procesarRespuesta($respuesta, "Cabecalho");
        if (isset($xmlResp->Sucesso) && $xmlResp->Sucesso == "true"){
            $xmlResp = $this->procesarRespuesta($respuesta, "ListaNFSe");
            $resp = true;
            foreach ($xmlResp->ConsultaNFSe as $item){
                $nroNfe = $item->NumeroNFe;
                $codigoVerificacion = $item->CodigoVerificacao;
                $idRPS = $item->NumeroRPS;                
                $condiciones = array("numero_lote" => $numeroLote, "numero_rps" => $idRPS);
                $arrFacturas = Vseguimiento_dsf::listarSeguimiento_dsf($conexion, $condiciones);
                if (count($arrFacturas) > 0 && $arrFacturas[0]['cod_factura'] > 0 && $arrFacturas[0]['cod_filial'] == $conexion->database){
                    $myFactura = new Vfacturas($conexion, $arrFacturas[0]['cod_factura']);
                    $resp = $resp && $myFactura->setEstado(Vfacturas::getEstadoHabilitado());
                    $mySeguimiento = new Vseguimiento_dsf($conexion);
                    $mySeguimiento->cod_factura = $myFactura->getCodigo();
                    $mySeguimiento->cod_filial = $conexion->database;
                    $mySeguimiento->numero_nfe = $nroNfe;
                    $mySeguimiento->codigo_verificacion = $codigoVerificacion;
                    $mySeguimiento->numero_rps = $idRPS;
                    $resp = $resp && $mySeguimiento->guardarSeguimiento_dsf();
                }
            }
            return $resp;                
        } else {
            if (strpos($respuesta, "Erros")){
                $xmlResp = $this->procesarRespuesta($respuesta, "Alertas");
                if (isset($xmlResp->Alerta->Codigo) && $xmlResp->Alerta->Codigo == 203){
                    echo  "la nota no ha sido procesada aun";
                } else {
                    $xmlResp = $this->procesarRespuesta($respuesta, "Erros");
                    if (isset($xmlResp->Erro) && isset($xmlResp->Erro->Descricao)){
                        $mensajeError = '';
                        foreach ($xmlResp->Erro as $errores){
                            $mensajeError .= "[$errores->Codigo] $errores->Descricao; <br>";
                        }
                        $condiciones = array("numero_lote" => $numeroLote);
                        $arrFacturas = Vseguimiento_dsf::listarSeguimiento_dsf($conexion, $condiciones);
                        foreach ($arrFacturas as $factura){
                            $myFactura = new Vfacturas($conexion, $factura['cod_factura']);
                            $myFactura->setEstado(Vfacturas::getEstadoError());
                            $mySeguimiento = new Vseguimiento_dsf($conexion);
                            $mySeguimiento->cod_factura = $myFactura->getCodigo();
                            $mySeguimiento->cod_filial = $conexion->database;
                            $mySeguimiento->respuesta = $mensajeError;
                            $mySeguimiento->estado = Vfacturas::getEstadoError();
                            $mySeguimiento->numero_lote = $factura['numero_lote'];
                            $mySeguimiento->guardarSeguimiento_dsf();
                        }
                        return false;
                    }
                }
            } else {
                $condiciones = array("numero_lote" => $numeroLote);
                $arrFacturas = Vseguimiento_dsf::listarSeguimiento_dsf($conexion, $condiciones);
                foreach ($arrFacturas as $factura){
                    $myFactura = new Vfacturas($conexion, $factura['cod_factura']);
                    $myFactura->setEstado(Vfacturas::getEstadoError());
                    $mySeguimiento = new Vseguimiento_dsf($conexion);
                    $mySeguimiento->cod_factura = $myFactura->getCodigo();
                    $mySeguimiento->cod_filial = $conexion->database;
                    $mySeguimiento->respuesta = $respuesta;
                    $mySeguimiento->estado = Vfacturas::getEstadoError();
                    $mySeguimiento->numero_lote = $factura['numero_lote'];
                    $mySeguimiento->guardarSeguimiento_dsf();                        
                }
                return false;
            }
        }
    }
    
    
    public function cancelarFactura(CI_DB_mysqli_driver $conexion, Vfacturas $myFactura){        
        $myPuntoVenta = new Vpuntos_venta($conexion, $myFactura->punto_venta);
        $myFacturante = new Vfacturantes($conexion, $myPuntoVenta->cod_facturante);
        $myRazonSocial = new Vrazones_sociales_general($conexion, $myFacturante->cod_razon_social);
        $myLocalidad = new Vlocalidades($conexion, $myRazonSocial->cod_localidad);
        $condiciones = array("cod_filial" => $conexion->database, "cod_factura" => $myFactura->getCodigo(), "estado" => Vfacturas::getEstadoHabilitado());
        $arrSeguimiento = Vseguimiento_dsf::listarSeguimiento_dsf($conexion, $condiciones);
        if (count($arrSeguimiento) > 0){
            $metodoEnvio = "Cancelar";
            $xmlCDATA .= "<ns1:ReqCancelamentoNFSe xmlns:ns1='http://localhost:8080/WsNFe2/lote' xmlns:tipos='http://localhost:8080/WsNFe2/tp' xmlns:xsi='http://www.w3.org/2001/XMLSchema-instance' xsi:schemaLocation='http://localhost:8080/WsNFe2/lote http://localhost:8080/WsNFe2/xsd/ReqCancelamentoNFSe.xsd'>";
            $xmlCDATA .= "<Cabecalho>";
            $xmlCDATA .= "<CodCidade>{$myLocalidad->get_codigo_siafi()}</CodCidade>";
            $xmlCDATA .= "<CPFCNPJRemetente>{$myRazonSocial->documento}</CPFCNPJRemetente>";
            $xmlCDATA .= "<transacao>true</transacao>";
            $xmlCDATA .= "<Versao>1</Versao>";
            $xmlCDATA .= "</Cabecalho>";
            $xmlCDATA .= "<Lote Id='lote:3309'>";
            $xmlCDATA .= "<Nota Id='nota:5'>";
            $xmlCDATA .= "<InscricaoMunicipalPrestador>{$this->inscripcion_municipal}</InscricaoMunicipalPrestador>";
            $xmlCDATA .= "<NumeroNota>{$arrSeguimiento[0]['numero_rps']}</NumeroNota>";
            $xmlCDATA .= "<CodigoVerificacao>{$arrSeguimiento[0]['codigo_verificacion']}</CodigoVerificacao>";
            $xmlCDATA .= "<MotivoCancelamento>Cancelamento por erros</MotivoCancelamento>";
            $xmlCDATA .= "</Nota>";
            $xmlCDATA .= "</Lote>";
            $xmlCDATA .= "</ns1:ReqCancelamentoNFSe>";
            $digestValue = $this->getDigestValue($xmlCDATA, "Lote");
            $xmlCDATA = $this->getSignature($xmlCDATA, $digestValue, "Lote", $this->certificadoPub, $this->certificadoPri);
            $xml = '';
            $xml .= $this->getXMLHead($metodoEnvio);
            $xml .= $xmlCDATA;
            $xml .= $this->getXMLFoot($metodoEnvio);
            $respuesta = $this->enviar($xml);
            $docxml = $this->procesarRespuesta($respuesta, "Cabecalho");
            if (isset($docxml->Sucesso)){
                if ($docxml->Sucesso == "true"){
                    $myFactura->setEstado(Vfacturas::getEstadoInhabilitado());
                    $mySeguimiento = new Vseguimiento_dsf($conexion);
                    $mySeguimiento->cod_factura = $myFactura->getCodigo();
                    $mySeguimiento->cod_filial = $conexion->database;
                    $mySeguimiento->numero_lote = $arrSeguimiento[0]['numero_lote'];
                    $mySeguimiento->numero_rps = $arrSeguimiento[0]['numero_rps'];
                    $mySeguimiento->codigo_verificacion = $arrSeguimiento[0]['codigo_verificacion'];
                    $mySeguimiento->estado = Vfacturas::getEstadoInhabilitado();
                    $mySeguimiento->guardarSeguimiento_dsf();
                } else {
                    $docxml = $this->procesarRespuesta($respuesta, "Alertas");
                    if (isset($docxml->Alerta)){
                        $mensaje = "ALERTA: [{$docxml->Alerta->Codigo}] {$docxml->Alerta->Descricao}";
                        $mySeguimiento = new Vseguimiento_dsf($conexion);
                        $mySeguimiento->cod_factura = $myFactura->getCodigo();
                        $mySeguimiento->cod_filial = $conexion->database;
                        $mySeguimiento->numero_lote = $arrSeguimiento[0]['numero_lote'];
                        $mySeguimiento->numero_rps = $arrSeguimiento[0]['numero_rps'];
                        $mySeguimiento->codigo_verificacion = $arrSeguimiento[0]['codigo_verificacion'];
                        $mySeguimiento->respuesta = $mensaje;
                        $mySeguimiento->estado = Vfacturas::getEstadoError();
                        $mySeguimiento->guardarSeguimiento_dsf();                        
                    } else {
                        $docxml = $this->procesarRespuesta($respuesta, "Erros");
                        if (isset($docxml->Erro) && isset($docxml->Erro->Descricao)){
                            $mensajeError = '';
                            foreach ($docxml->Erro as $errores){
                                $mensajeError .= "[$errores->Codigo] $errores->Descricao; ";
                            }
                            $mySeguimiento = new Vseguimiento_dsf($conexion);
                            $mySeguimiento->cod_factura = $myFactura->getCodigo();
                            $mySeguimiento->cod_filial = $conexion->database;
                            $mySeguimiento->numero_lote = $arrSeguimiento[0]['numero_lote'];
                            $mySeguimiento->numero_rps = $arrSeguimiento[0]['numero_rps'];
                            $mySeguimiento->codigo_verificacion = $arrSeguimiento[0]['codigo_verificacion'];
                            $mySeguimiento->respuesta = $mensajeError;
                            $mySeguimiento->estado = Vfacturas::getEstadoError();
                            $mySeguimiento->guardarSeguimiento_dsf(); 
                        }
                    }
                    return false;
                }
            }            
        } else {
            throw new Exception ("La factura indicada no se encuentra en estado habilitado");
        }
    }
    
}
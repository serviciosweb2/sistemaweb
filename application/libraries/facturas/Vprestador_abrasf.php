<?php

/**
* Class Vprestador_abrasf
*
*Class  Vprestador_abrasf maneja todos los aspectos de alumnos
*
* @package  SistemaIGA
* @subpackage Alumnos
* @author   Ivan berthillod <ivan.sys@gmail.com>
* @author   Aquiles Gonzalez <sistemas1@iga-la.net>
* @version  $Revision: 1.1 $
* @access   private
*/
class Vprestador_abrasf extends Tprestador_abrasf{

//    protected $urlWebService = "https://bhissdigital.pbh.gov.br/bhiss-ws/nfse?wsdl";  // produccion
    protected $urlWebService = "https://bhisshomologa.pbh.gov.br/bhiss-ws/nfse?wsdl";   // homologacion
    
    /* CONSTRUCTOR */
    
    function __construct(CI_DB_mysqli_driver $conexion, $codigo = null) {
        parent::__construct($conexion, $codigo);
    }
    
    /* PRIVATE FUNCTION */
    
    private function enviar($xml, array $header = null, $certPub = null, $certPri = null){
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
            /* como es un sistema distribuido debemos guardar el archivo de certificados en la catrpeta temportal para poder enviarlo por CURL */
            $tempDir = sys_get_temp_dir();
            $filePub = $tempDir."/".md5($certPub).".pem";    
            if (!file_exists($filePub)){
                file_put_contents($filePub, $certPub);
            }
            $filePry = $tempDir."/".md5($certPri).".pem";    // md5 para ver si el certificado ya fue guardado en el tempotral (el nombre es unico para cada certificado)
            if (!file_exists($filePry)){
                file_put_contents($filePry, $certPri);       // md5 para ver si el certificado ya fue guardado en el tempotral (el nombre es unico para cada certificado)
            }
            
            curl_setopt($soap_do, CURLOPT_SSLCERT, $filePub);
            curl_setopt($soap_do, CURLOPT_SSLKEY, $filePry);
            curl_setopt($soap_do, CURLOPT_SSLVERSION, 3);
            curl_setopt($soap_do, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($soap_do, CURLOPT_SSL_VERIFYPEER, 0);
        }        
        $respuesta = curl_exec($soap_do);
        curl_close($soap_do);
        return $respuesta;
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
        $xmldoc->preservWhiteSpace = false; //elimina espaços em branco
        $xmldoc->formatOutput = false;
        if ($xmldoc->loadXML($docxml,LIBXML_NOBLANKS | LIBXML_NOEMPTYTAG)){
            $root = $xmldoc->documentElement;
        } else {
            throw new Exception ("XML enviado para calculo de digest value inválido");
        }
        $node = $xmldoc->getElementsByTagName($tagName)->item(0);
        $datos = $node->C14N(false,false,NULL,NULL);
        $hashValue = hash('sha1',$datos,true);
        $digValue = base64_encode($hashValue);
        return $digValue;
    }
    
    static private function getFoot($nombreLlamada){
        $xml = '';
        $xml .= "]]>";
        $xml .= "</nfseDadosMsg>";
        $xml .= "</ns2:$nombreLlamada>";
        $xml .= "</S:Body>";
        $xml .= "</S:Envelope>";
        return $xml;
    }
    
    static private function getHead($nombreLlamada){
        $xml = '';
        $xml .= "<?xml version='1.0' encoding='UTF-8'?>";
        $xml .= "<S:Envelope xmlns:S='http://schemas.xmlsoap.org/soap/envelope/'>";
        $xml .= "<S:Body>";
        $xml .= "<ns2:$nombreLlamada xmlns:ns2='http://ws.bhiss.pbh.gov.br'>";
        $xml .= "<nfseCabecMsg>";
        $xml .= "<![CDATA[";
        $xml .= "<?xml version='1.0' encoding='UTF-8'?>";
        $xml .= "<cabecalho xmlns='http://www.abrasf.org.br/nfse.xsd' versao='1.00'>";
        $xml .= "<versaoDados>1.00</versaoDados>";
        $xml .= "</cabecalho>";
        $xml .= "]]>";
        $xml .= "</nfseCabecMsg>";
        $xml .= "<nfseDadosMsg>";
        $xml .= "<![CDATA["; 
        $xml .= "<?xml version='1.0' encoding='UTF-8'?>";
        return $xml;
    }
    
    private function getXMLEnvio(CI_DB_mysqli_driver $conexion, Vfacturas $myFactura, Vpuntos_venta $myPuntoVenta, 
            Vfacturantes $myFacturante = null, Vfacturantes_certificados $myCertificado = null){
        $valorServicio = $myFactura->total;
        if ($myFacturante == null){
            $myFacturante = new Vfacturantes($conexion, $myPuntoVenta->cod_facturante);
        }
        if ($myCertificado == null){
            $myCertificado = new Vfacturantes_certificados($conexion, $myFacturante->getCodigo());
        }
//        echo "<pre>"; print_r($this); echo "</pre>"; die();
        $myRazonSocialFacturante = new Vrazones_sociales_general($conexion, $myFacturante->cod_razon_social);
        $myRazonTomador = new Vrazones_sociales($conexion, $myFactura->codrazsoc);
        $myLocalidadTomador = new Vlocalidades($conexion, $myRazonTomador->cod_localidad);
        $myProvinciaTomador = new Vprovincias($conexion, $myLocalidadTomador->provincia_id);
        $myLocalidadFacturante = new Vlocalidades($conexion, $myRazonSocialFacturante->cod_localidad);
        $valorIss = $valorServicio - ($this->alicuota * 100);
        $valorLiquido = $valorServicio - ($valorServicio * $this->alicuota);
        $optanteSimpleNacional = 2;
        $incentivadorCultural = $this->incentivador_cultural;
        $dataEmisao = date("Y-m-d")."T".date("H:i:s");        
        $nombreLlamada = "GerarNfseRequest";        
        $conservar = '0-9a-z'; 
        $regex = sprintf('~[^%s]++~i', $conservar); 
        $cepTomador = preg_replace($regex, '', $myRazonTomador->codigo_postal);
        $arrTelefonoTomador = $myRazonTomador->telfonoRazonSocial();
        $telefono = preg_replace($regex, '', $arrTelefonoTomador[0]['cod_area'].$arrTelefonoTomador[0]['numero']);
        $telefono = substr($telefono, 0, 10); // por si tiene mas de 10 caracteres
        $telefono = str_pad($telefono, 10, 0, STR_PAD_RIGHT);    // por si tiene menos de 10 caracteres
        $numero = $myPuntoVenta->nro + 1;
        $numero = str_pad($numero, 11, "0", STR_PAD_LEFT);
        $numero = date("Y").$numero;
        $complementoTomador = $myRazonTomador->direccion_complemento == '' ? "sobreloja" : $myRazonTomador->direccion_complemento;
        $arrChar = array(".", "-");
        $documentoTomador = str_replace($arrChar, "", $myRazonTomador->documento);
        $xml1 = '';
        $xml1 .= "<Rps xmlns='http://www.abrasf.org.br/nfse.xsd'>";
        $xml1 .= "<InfRps Id='1'>";
        $xml1 .= "<IdentificacaoRps>";
        $xml1 .= "<Numero>{$numero}</Numero>";
        $xml1 .= "<Serie>{$this->serie_factura}</Serie>";
        $xml1 .= "<Tipo>{$this->tipo_nota}</Tipo>";
        $xml1 .= "</IdentificacaoRps>";
        $xml1 .= "<DataEmissao>{$dataEmisao}</DataEmissao>";
        $xml1 .= "<NaturezaOperacao>1</NaturezaOperacao>";
        $xml1 .= "<RegimeEspecialTributacao>{$this->regimen_especial_tributario}</RegimeEspecialTributacao>"; /* ejemplo de github no posee este tag*/
        $xml1 .= "<OptanteSimplesNacional>{$optanteSimpleNacional}</OptanteSimplesNacional>";
        $xml1 .= "<IncentivadorCultural>{$incentivadorCultural}</IncentivadorCultural>";
        $xml1 .= "<Status>1</Status>";
        $xml1 .= "<Servico>";
        $xml1 .= "<Valores>";
        $xml1 .= "<ValorServicos>$valorServicio</ValorServicos>";
        $xml1 .= "<ValorDeducoes>0</ValorDeducoes>";
        $xml1 .= "<ValorPis>{$this->valor_pis}</ValorPis>";
        $xml1 .= "<ValorCofins>{$this->valor_cofins}</ValorCofins>";
        $xml1 .= "<ValorInss>{$this->valor_inss}</ValorInss>";
        $xml1 .= "<ValorIr>{$this->valor_ir}</ValorIr>";
        $xml1 .= "<ValorCsll>{$this->valor_csll}</ValorCsll>";
        $xml1 .= "<IssRetido>2</IssRetido>";
        $xml1 .= "<ValorIss>{$valorIss}</ValorIss>";
        $xml1 .= "<ValorIssRetido>0</ValorIssRetido>";
        $xml1 .= "<OutrasRetencoes>0</OutrasRetencoes>";
        $xml1 .= "<BaseCalculo>$valorServicio</BaseCalculo>";
        $xml1 .= "<Aliquota>{$this->alicuota}</Aliquota>";
        $xml1 .= "<ValorLiquidoNfse>{$valorLiquido}</ValorLiquidoNfse>";
        $xml1 .= "<DescontoIncondicionado>0</DescontoIncondicionado>";
        $xml1 .= "<DescontoCondicionado>0</DescontoCondicionado>";
        $xml1 .= "</Valores>";
        $xml1 .= "<ItemListaServico>{$this->item_lista_servicio}</ItemListaServico>";
        $xml1 .= "<CodigoTributacaoMunicipio>{$this->codigo_actividad}</CodigoTributacaoMunicipio>";
        $xml1 .= "<Discriminacao>{$this->nombre_servicio}</Discriminacao>";
        $xml1 .= "<CodigoMunicipio>{$myLocalidadFacturante->get_codigo_municipio()}</CodigoMunicipio>";
        $xml1 .= "</Servico>";
        $xml1 .= "<Prestador>";
        $xml1 .= "<Cnpj>{$myRazonSocialFacturante->documento}</Cnpj>";
        $xml1 .= "<InscricaoMunicipal>{$this->inscripcion_municipal}</InscricaoMunicipal>";
        $xml1 .= "</Prestador>";
        $xml1 .= "<Tomador>";
        $xml1 .= "<IdentificacaoTomador>";
        $xml1 .= "<CpfCnpj>";
        if ($myRazonTomador->tipo_documentos == 6){
            $xml1 .= "<Cnpj>{$documentoTomador}</Cnpj>";
        } else {
            $xml1 .= "<Cpf>{$documentoTomador}</Cpf>";
        }
        $xml1 .= "</CpfCnpj>";
        $xml1 .= "</IdentificacaoTomador>";
        $xml1 .= "<RazaoSocial>{$myRazonTomador->razon_social}</RazaoSocial>";
        $xml1 .= "<Endereco>";
        $xml1 .= "<Endereco>{$myRazonTomador->direccion_calle}</Endereco>";
        $xml1 .= "<Numero>{$myRazonTomador->direccion_numero}</Numero>";
        $xml1 .= "<Complemento>{$complementoTomador}</Complemento>";
        $xml1 .= "<Bairro>NO BARRIO</Bairro>";
        $xml1 .= "<CodigoMunicipio>{$myLocalidadTomador->get_codigo_municipio()}</CodigoMunicipio>";
        $xml1 .= "<Uf>{$myProvinciaTomador->get_codigo_estado()}</Uf>";
        $xml1 .= "<Cep>{$cepTomador}</Cep>";
        $xml1 .= "</Endereco>";
        $xml1 .= "<Contato>";
        $xml1 .= "<Telefone>{$telefono}</Telefone>";
        $xml1 .= "<Email>prueba@pruebas.com</Email>";
        $xml1 .= "</Contato>";
        $xml1 .= "</Tomador>";
        $xml1 .= "</InfRps>";
        $xml1 .= "</Rps>";
        $xml1 = utf8_encode($xml1);
        $digestValue = self::getDigestValue($xml1, "InfRps");
        $xml1 = self::getSignature($xml1, $digestValue, "InfRps", $myCertificado->pub_key, $myCertificado->pry_key);
        $xml = '';
        $xml .= "<GerarNfseEnvio xmlns='http://www.abrasf.org.br/nfse.xsd'>";
        $xml .= "<LoteRps Id='123' versao='1.00'>";
        $xml .= "<NumeroLote>001</NumeroLote>";
        $xml .= "<Cnpj>{$myRazonSocialFacturante->documento}</Cnpj>";
        $xml .= "<InscricaoMunicipal>{$this->inscripcion_municipal}</InscricaoMunicipal>";
        $xml .= "<QuantidadeRps>1</QuantidadeRps>";
        $xml .= "<ListaRps>";
        $xml .= $xml1;
        $xml .= "</ListaRps>";
        $xml .= "</LoteRps>";
        $xml .= "</GerarNfseEnvio>";
        $digestValue = self::getDigestValue($xml, "LoteRps");
        $xml = self::getSignature($xml, $digestValue, "LoteRps", $myCertificado->pub_key, $myCertificado->pry_key);
        return self::getHead($nombreLlamada).$xml.self::getFoot($nombreLlamada);
    }
    
    static private function procesarRespuesta($stringRespuesta, $nombreCabeceraLeer){
        $startCabecera = "<$nombreCabeceraLeer";
        $endCabecera = "</$nombreCabeceraLeer>";
        $stringRespuesta = html_entity_decode($stringRespuesta);
        $pos1 = strpos($stringRespuesta, $startCabecera);
        $pos2 = strpos($stringRespuesta, $endCabecera) + strlen($endCabecera);
        $xml = substr($stringRespuesta, $pos1, $pos2 - $pos1);
        $xmldoc = @ simplexml_load_string($xml);
        return $xmldoc;
    }
    
    
    private function getXMLConsultarPorProtocolo($protocolo, $documentoFacturante){
        $nombreLlamada = "ConsultarSituacaoLoteRpsRequest";
        $xml = '';
        $xml .= "<ConsultarSituacaoLoteRpsEnvio xmlns='http://www.abrasf.org.br/nfse.xsd'>";
        $xml .= "<Prestador>";
        $xml .= "<Cnpj>{$documentoFacturante}</Cnpj>";
        $xml .= "<InscricaoMunicipal>{$this->inscripcion_municipal}</InscricaoMunicipal>";
        $xml .= "</Prestador>";
        $xml .= "<Protocolo>";
        $xml .= $protocolo;
        $xml .= "</Protocolo>";
        $xml .= "</ConsultarSituacaoLoteRpsEnvio>";
        return $this->getHead($nombreLlamada).$xml.$this->getFoot($nombreLlamada);
    }
    
    private function getXMLcancelar(CI_DB_mysqli_driver $conexion, Vfacturas $myFactura, Vfacturantes $myFacturante, Vfacturantes_certificados $myCertificado){
        $numeroNFE = Vseguimiento_abrasf::getNumeroSeguimineto($conexion, $myFactura->getCodigo());
        $myRazonSocial = new Vrazones_sociales_general($conexion, $myFacturante->cod_razon_social);
        $myLocalidad = new Vlocalidades($conexion, $myRazonSocial->cod_localidad);
        $certificadoPri = $myCertificado->pry_key;
        $certificadoPub = $myCertificado->pub_key;
        $nombreLlamada = "CancelarNfseRequest";
        $xml = '';
        $xml .= "<CancelarNfseEnvio xmlns='http://www.abrasf.org.br/nfse.xsd'>";
        $xml .= "<Pedido>";
        $xml .= "<InfPedidoCancelamento Id='23'>";
        $xml .= "<IdentificacaoNfse>";
        $xml .= "<Numero>$numeroNFE</Numero>";
        $xml .= "<Cnpj>{$myRazonSocial->documento}</Cnpj>";
        $xml .= "<InscricaoMunicipal>{$this->inscripcion_municipal}</InscricaoMunicipal>";
        $xml .= "<CodigoMunicipio>{$myLocalidad->get_codigo_municipio()}</CodigoMunicipio>";
        $xml .= "</IdentificacaoNfse>";
        $xml .= "<CodigoCancelamento>2</CodigoCancelamento>";
        $xml .= "</InfPedidoCancelamento>";    
        $xml .= "</Pedido>";
        $xml .= "</CancelarNfseEnvio>";
        $digestValue = $this->getDigestValue($xml, "InfPedidoCancelamento");
        $xml = $this->getSignature($xml, $digestValue, "InfPedidoCancelamento", $certificadoPub, $certificadoPri);
        $posini = strpos($xml, "<Signature xmlns");
        $posfin = strpos($xml, "</Signature>") + 12;
        $signature = substr($xml, $posini, $posfin - $posini);
        $xml = substr_replace($xml, "", $posini, $posfin - $posini);
        $xml = substr_replace($xml, $signature, strlen($xml) - 30, 0);
        return $this->getHead($nombreLlamada).$xml.$this->getFoot($nombreLlamada);
    }
    
    /* PUBLIC FUNCIOTNS */
    
    public function cancelarFactura(CI_DB_mysqli_driver $conexion, Vfacturas $myFactura){
        $myPuntoVenta = new Vpuntos_venta($conexion, $this->cod_punto_venta);
        $myFacturante = new Vfacturantes($conexion, $myPuntoVenta->cod_facturante);
        $myCertificado = $myFacturante->getCertificado();
        $xml = $this->getXMLcancelar($conexion, $myFactura, $myFacturante, $myCertificado);
        $certificadoPub = $myCertificado->pub_key;
        $certificadoPri = $myCertificado->pry_key;
        $respuesta = $this->enviar($xml, array('Content-Type: text/xml;charset=utf-8', 'Content-Length: '.strlen($xml)), $certificadoPub, $certificadoPri);
        $resp = self::procesarRespuesta($respuesta, "CancelarNfseResposta");        
        if (isset($resp->ListaMensagemRetorno->MensagemRetorno->Codigo) && $resp->ListaMensagemRetorno->MensagemRetorno->Codigo == "E79"){
             // ya se encuentra cancelada (si se debe hacer otra accion continuar aqui)
        } else if (isset($resp->RetCancelamento->NfseCancelamento->Confirmacao)){             // cancelacion normal
            $resp = $myFactura->setEstado(Vfacturas::getEstadoInhabilitado());
            $mySeguimineto = new Vseguimiento_abrasf($conexion);
            $mySeguimineto->cod_factura = $myFactura->getCodigo();
            $mySeguimineto->cod_filial = $conexion->database;
            $mySeguimineto->estado = Vfacturas::getEstadoInhabilitado();
            $mySeguimineto->fecha_envio_lote = date("Y-m-d H:i:s");
            $resp = $resp && $mySeguimineto->guardarSeguimiento_abrasf();
            return $resp;
        } else {
//            echo "<pre>"; print_r($resp); echo "</pre>";
            return false; // hay error en el proceso de cancelacion
        }    
    }
    
    public function verificar(CI_DB_mysqli_driver $conexion, $protocolo){
        $myPuntoVenta = new Vpuntos_venta($conexion, $this->cod_punto_venta);
        $myFacturante = new Vfacturantes($conexion, $myPuntoVenta->cod_facturante);
        $myCertificado = $myFacturante->getCertificado();
        $certificadoPri = $myCertificado->pry_key;
        $certificadoPub = $myCertificado->pub_key;
        $myRazonSocial = new Vrazones_sociales_general($conexion, $myFacturante->cod_razon_social);
        $xml = $this->getXMLConsultarPorProtocolo($protocolo, $myRazonSocial->documento);
        $respuestaOriginal = $this->enviar($xml, array('Content-Type: text/xml;charset=utf-8', 'Content-Length: '.strlen($xml)), $certificadoPub, $certificadoPri);
        if (strpos($respuestaOriginal, "ConsultarSituacaoLoteRpsResposta")){
            $respuesta = self::procesarRespuesta($respuestaOriginal, "ConsultarSituacaoLoteRpsResposta");
            $condiciones = array("protocolo" => $protocolo);
            $arrFacturas = Vseguimiento_abrasf::listarSeguimiento_abrasf($conexion, $condiciones);
            if (isset($respuesta->Situacao)){ // sobre la respuesta esperado)
                if ($respuesta->Situacao == 4){
                    foreach ($arrFacturas as $factura){
                        $myFactura = new Vfacturas($conexion, $factura['cod_factura']);
                        $resp = $myFactura->setEstado(Vfacturas::getEstadoHabilitado());
                        $mySeguimiento = new Vseguimiento_abrasf($conexion);
                        $mySeguimiento->cod_factura = $myFactura->getCodigo();
                        $mySeguimiento->cod_filial = $conexion->database;
                        $mySeguimiento->cod_filial = $conexion->database;
                        $mySeguimiento->estado = Vfacturas::getEstadoHabilitado();
                        $resp = $resp && $mySeguimiento->guardarSeguimiento_abrasf();
                        return $resp;
                    }
                } else if ($respuesta->Situacao == 3){
                    $errorMensaje = ' '; // ver si en algun momento aparece MensagemRetorno y procesar los errores (el web services de abrasf está presentando errores en las respuestas) 
                    foreach ($arrFacturas as $factura){
                        $myFactura = new Vfacturas($conexion, $factura['cod_factura']);
                        $resp = $myFactura->setEstado(Vfacturas::getEstadoError());
                        $mySeguimiento = new Vseguimiento_abrasf($conexion);
                        $mySeguimiento->cod_factura = $myFactura->getCodigo();
                        $mySeguimiento->cod_filial = $conexion->database;
                        $mySeguimiento->mensaje = $errorMensaje;
                        $mySeguimiento->estado = Vfacturas::getEstadoError();
                        $resp = $resp && $mySeguimiento->guardarSeguimiento_abrasf();
                        return $resp;
                    }
                } else {
                    // los otros dos estados posibles son 1: no recibido y 2: no procesado (en este último hay que seguir consultando hasta el procesamiento del web services de abrasf)
                    return false;
                }
            } else {
                if (isset($respuesta->ListaMensagemRetorno)){
                    $arrError = array();
                    foreach ($respuesta->ListaMensagemRetorno->MensagemRetorno as $mensajeRetorno){
                        $arrError[] = "[{$mensajeRetorno->Codigo}] {$mensajeRetorno->Mensagem}";
                        echo "<pre>"; print_r($mensajeRetorno); echo "</pre>";
                    }
                    $mensajeError = implode("<br>", $arrError);
                    foreach ($arrFacturas as $factura){
                        $mySeguimiento = new Vseguimiento_abrasf($conexion);
                        $myFactura = new Vfacturas($conexion, $factura['cod_factura']);
                        $resp = $myFactura->setEstado(Vfacturas::getEstadoError());
                        $mySeguimiento->cod_factura = $myFactura->getCodigo();
                        $mySeguimiento->cod_filial = $conexion->database;
                        $mySeguimiento->mensaje = $mensajeError;
                        $mySeguimiento->estado = Vfacturas::getEstadoError();
                        $resp = $resp && $mySeguimiento->guardarSeguimiento_abrasf();
                        return $resp;
                    }
                }
            }
        } else {
            echo "no se reconoce la respuesta deñ servidor";
            return false;
        }
    }
    

    /* todas las facturas deben pertenecer al mismo punto de venta y a la misma filial (se optimiza la ejecucion) */
    public function enviarFacturas(CI_DB_mysqli_driver $conexion, array $arrFacturas){
        $myPuntoVenta = new Vpuntos_venta($conexion, $arrFacturas[0]['punto_venta']);
        $myFacturante = new Vfacturantes($conexion, $myPuntoVenta->cod_facturante);
        $myCertificado = $myFacturante->getCertificado();
        foreach ($arrFacturas as $factura){
            $myFactura = new Vfacturas($conexion, $factura['codigo']);
            $xml = $this->getXMLEnvio($conexion, $myFactura, $myPuntoVenta, $myFacturante, $myCertificado);
            $respuesta = $this->enviar($xml, array('Content-Type: text/xml;charset=utf-8', 'Content-Length: '.strlen($xml)), $myCertificado->pub_key, $myCertificado->pry_key);
            if (strpos($respuesta, "ListaMensagemRetorno")){
                $respuesta = self::procesarRespuesta($respuesta, "ListaMensagemRetorno");
                $errores = array();
                foreach ($respuesta->MensagemRetorno as $error){
                    $errores[] = "[{$error->Codigo}] {$error->Mensagem}";
                }
                $errorMensaje = implode(" - ", $errores);
                if ($error->Codigo == "E10"){ // el numero de RPS ya ha sido informado, debemos incrementar este y dejar la factura pendiente para nuevos intentos de envio
                    $myPuntoVenta->nro ++;
                    $myPuntoVenta->guardarPuntos_venta();
                } else {
                    $mySeguimiento = new Vseguimiento_abrasf($conexion);
                    $mySeguimiento->cod_factura = $myFactura->getCodigo();
                    $mySeguimiento->cod_filial = $conexion->database;
                    $mySeguimiento->mensaje = $errorMensaje;
                    $mySeguimiento->estado = Vfacturas::getEstadoError();
                    $mySeguimiento->guardarSeguimiento_abrasf();
                    $myFactura->setEstado(Vfacturas::getEstadoError());
                    return false;
                }
            } else if (strpos($respuesta, "GerarNfseResposta")){
                $respuesta = self::procesarRespuesta($respuesta, "GerarNfseResposta");
                $mySeguimiento = new Vseguimiento_abrasf($conexion);
                $mySeguimiento->cod_factura = $myFactura->getCodigo();
                $mySeguimiento->cod_filial = $conexion->database;
                $mySeguimiento->estado = Vfacturas::getEstadoEnviado();
                $mySeguimiento->numero = $respuesta->ListaNfse->CompNfse->Nfse->InfNfse->Numero;
                $mySeguimiento->fecha_envio_lote = str_replace("T", " ", $respuesta->DataRecebimento);
                $mySeguimiento->protocolo = $respuesta->Protocolo;
                $mySeguimiento->numero_lote = $respuesta->NumeroLote;
                $resp = $mySeguimiento->guardarSeguimiento_abrasf();
                $resp = $resp && $myFactura->setEstado(Vfacturas::getEstadoEnviado());
                return $resp;
            } else {
                echo "no se reconoce la respuesta del servidor<br>";
                return false;
            }
        }
    }
    
    /* STATIC FUNCTIONS */
    
}
<?php

/**
 * Creates XMLs and Webservices communication
 *
 * Original names of Brazil specific abbreviations have been kept:
 * - CNPJ = Federal Tax Number
 * - CPF = Personal/Individual Taxpayer Registration Number
 * - CCM = Taxpayer Register (for service providers who pay ISS for local town/city hall)
 * - ISS = Service Tax
 *
 * @package   NFePHPaulista
 * @author    Rodrigo Gliksberg <xdieamd@gmail.com>
 * @author    Nailson Landim <nailson@nailson.me>
 *
 * @copyright Copyright (c) 2010, Reinaldo Nolasco Sanches
 * @license   http://www.opensource.org/licenses/mit-license.php MIT License
 */
class Vprestador_paulistana extends Tprestador_paulistana {

    private $cnpjPrestador; // Your CNPJ

    private $ccmPrestador; // Your CCM

    private $passphrase; // Cert passphrase

    private $pkcs12;

    private $certDir; // Dir for .pem certs

    private $privateKey;

    public $certDaysToExpire=0;

    private $publicKey;

    private $X509Certificate;

    private $key;

    private $connectionSoap;

    private $urlXsi = 'http://www.w3.org/2001/XMLSchema-instance';

    private $urlXsd = 'http://www.w3.org/2001/XMLSchema';

    private $urlNfe = 'http://www.prefeitura.sp.gov.br/nfe';

    private $urlDsig = 'http://www.w3.org/2000/09/xmldsig#';

    private $urlCanonMeth = 'http://www.w3.org/TR/2001/REC-xml-c14n-20010315';

    private $urlSigMeth = 'http://www.w3.org/2000/09/xmldsig#rsa-sha1';

    private $urlTransfMeth_1 = 'http://www.w3.org/2000/09/xmldsig#enveloped-signature';

    private $urlTransfMeth_2 = 'http://www.w3.org/TR/2001/REC-xml-c14n-20010315';

    private $urlDigestMeth = 'http://www.w3.org/2000/09/xmldsig#sha1';

    private $debug = 0;
    public $erronaclasse;

    public function __construct(CI_DB_mysqli_driver $conexion, $codigo = null, $debug='0')
    {
        parent::__construct($conexion, $codigo);

        $puntoVenta = new Vpuntos_venta($conexion, $this->cod_punto_venta);
        $facturante = new Vfacturantes($conexion, $puntoVenta->cod_facturante);
        $razonSocial = new Vrazones_sociales_general($conexion, $facturante->cod_razon_social);

        $this->cnpjPrestador = $razonSocial->documento;
        $objCert = $facturante->getCertificado();
        $this->passphrase = $objCert->password;

        $this->ccmPrestador = $this->inscripcion_municipal;
        $this->certDir = __DIR__.DIRECTORY_SEPARATOR."paulistana".DIRECTORY_SEPARATOR."certs";
        $this->pkcs12 = $this->certDir.DIRECTORY_SEPARATOR.$codigo.".pfx";

        $this->privateKey = $this->certDir  . DIRECTORY_SEPARATOR . $codigo.'_priKEY.pem';
        $this->publicKey = $this->certDir . DIRECTORY_SEPARATOR. $codigo . '_pubKEY.pem';
        $this->key = $this->certDir . DIRECTORY_SEPARATOR .$codigo. '_certKEY.pem';

        if ( $this->loadCert() ) {
            error_log( __METHOD__ . ': Certificado OK!' );
            $this->erronaclasse = false;
        } else {
            error_log( __METHOD__ . ': Certificado não OK!' );
            $this->erronaclasse = true;
        }
    }

    /*
    public function enviarFacturasBatch($conexion, $arrFacturas){

        $loteEnvioRPS = array();
        $rangeDate = array();
        $valorTotal = array();
        $rangosFechas = array();
        $rangeDate['inicio'] = 0;
        $rangeDate['fim'] = 0;
        $valorTotal['servicos'] = 0;
        $valorTotal['deducoes'] = 0;
        $webserviceAnswer = array();

        foreach ($arrFacturas as $factura) {

            if ($factura) {
                $razonSocialTomador = new Vrazones_sociales($conexion, $factura['codrazsoc'])
                $localidadTomador = new Vlocalidades($conexion, $razonSocialTomador->cod_localidad);
                $provinciaTomador = new Vprovincias($conexion, $localidadTomador->provincia_id);
                $myFactura = new Vfacturas($conexion, $factura['codigo']);

                $rps = new NFeRPS();
                $tomador = new ContractorRPS();
                $tomador->name = $razonSocialTomador->razon_social;
                $tomador->cnpjTomador = $razonSocialTomador->documento;
                $tomador->endereco = $razonSocialTomador->direccion_calle;
                $tomador->enderecoNumero = $razonSocialTomador->direccion_numero;
                $tomador->complemento = $razonSocialTomador->direccion_complemento;
                $tomador->cep = $razonSocialTomador->codigo_postal;
                //$tomador->cep = str_replace('-', '', $tomador->cep);
                $tomador->email = $razonSocialTomador->email;
                $tomador->cidade = $localidadTomador->get_codigo_municipio();
                $tomador->estado = $provinciaTomador->get_codigo_estado();
                $tomadorDocumentoLength = strlen($tomador->cnpjTomador);
                if ($tomadorDocumentoLength == 11) {
                    $tomador->type = 'F';
                } else {
                    $tomador->type = 'C';
                }
                //$rps->dataEmissao = $factura['fecha'];
                $rps->tributacao = 'T'; // Tributado no Municipio
                $rps->discriminacao = "PAGAMENTO DE MENSALIDADE";
                $rps->contractorRPS = $tomador;
                //$rps->CCM = $this->inscripcion_municipal;
                $rps->serie = $this->numero_serie;
                $rps->codigoServico = $this->codigo_tributacion_municipio;
                $rps->valorServicos = $factura['total'];
                $rps->valorDeducoes = 0;
                $rps->aliquotaServicos = $this->alicuota;
                // Las notas fiscales de servicio utilizan numero_rps si o si
                $rps->numero = $myFactura->getPropiedad(Vfacturas::getPropiedadNumeroRps());

                //$loteEnvioRPS[] = $rps; // Descomentar para envio en lotes
                $rangosFechas[] = strtotime($factura['fecha']);
                $valorTotal['servicos'] = $valorTotal['servicos'] + $rps->valorServicos;
                $valorTotal['deducoes'] = $valorTotal['deducoes'] + $rps->valorDeducoes;
            }
        }

        sort($rangosFechas);
        $topindex = (count($rangosFechas) - 1);
        $rangeDate['inicio'] = gmdate("Y-m-d", $rangosFechas[0]);
        $rangeDate['fim'] = gmdate("Y-m-d", $rangosFechas[$topindex]);
        // TODO ENVIO, SEGUIMIENTO & OTRAS COSAS MENORES

    } */
    public function getXMLFacturaAprobada($seguimiento, $prestador){
        //Ejecutar
        $xml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>';
        $xml .= '<RetornoEnvioRPS xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns="http://www.prefeitura.sp.gov.br/nfe">';
        $xml .= '<Cabecalho Versao="1" xmlns="">';
        $xml .= '<Sucesso>true</Sucesso>';
        $xml .= '</Cabecalho>';
        $xml .= '<ChaveNFeRPS xmlns="">';
        $xml .= '<ChaveNFe>';
        $xml .= '<InscricaoPrestador>'. $prestador->ccmPrestador .'</InscricaoPrestador>';
        $xml .= '<NumeroNFe>'. $seguimiento['numero_nfse'] .'</NumeroNFe>';
        $xml .= '<CodigoVerificacao>'. $seguimiento['codigo_verificacion'] .'</CodigoVerificacao>';
        $xml .= '</ChaveNFe>';
        $xml .= '<ChaveRPS>';
        $xml .= '<InscricaoPrestador>'. $prestador->ccmPrestador .'</InscricaoPrestador>';
        $xml .= '<SerieRPS>1</SerieRPS>';
        $xml .= '<NumeroRPS>'. $seguimiento['numero_lote'] .'</NumeroRPS>';
        $xml .= '</ChaveRPS>';
        $xml .= '</ChaveNFeRPS>';
        $xml .= '</RetornoEnvioRPS>';
        return $xml;
    }

    public function enviarFacturas($conexion, $prestador,  $arrFacturas){

        foreach ($arrFacturas as $factura) {

            if ($factura) {
                $razonSocialTomador = new Vrazones_sociales($conexion, $factura['codrazsoc']);
                $localidadTomador = new Vlocalidades($conexion, $razonSocialTomador->cod_localidad);
                $provinciaTomador = new Vprovincias($conexion, $localidadTomador->provincia_id);
                $myFactura = new Vfacturas($conexion, $factura['codigo']);

                $rps = new NFeRPS();
                $tomador = new ContractorRPS();
                $tomador->name = $razonSocialTomador->razon_social;
                $tomador->cnpjTomador = $razonSocialTomador->documento;
                $tomador->endereco = $razonSocialTomador->direccion_calle;
                $tomador->enderecoNumero = $razonSocialTomador->direccion_numero;
                $tomador->complemento = $razonSocialTomador->direccion_complemento;
                $tomador->cep = $razonSocialTomador->codigo_postal;
                $tomador->cep = str_replace('-', '', $tomador->cep);
                $tomador->email = $razonSocialTomador->email;
                $tomador->cidade = $localidadTomador->get_codigo_municipio();
                $tomador->estado = $provinciaTomador->get_codigo_estado();
                $tomadorDocumentoLength = strlen($tomador->cnpjTomador);
                if ($tomadorDocumentoLength == 11) {
                    $tomador->type = 'F';
                } else {
                    $tomador->type = 'C';
                }
                $rps->dataEmissao = $factura['fecha'];
                $rps->tributacao = 'T'; // Tributado no Municipio
                $rps->discriminacao = "PAGAMENTO DE MENSALIDADE";
                $rps->contractorRPS = $tomador;
                $rps->CCM = $prestador['inscripcion_municipal'];
                $rps->serie = $prestador['numero_serie'];
                $rps->codigoServico = $prestador['codigo_tributacion_municipio'];
                $rps->valorServicos = $factura['total'];
                $rps->valorDeducoes = 0;
                $rps->aliquotaServicos = $prestador['codigo_tributacion_municipio'];
                $rps->numero = $myFactura->getPropiedad(Vfacturas::getPropiedadNumeroRps());

                // Envia la NFSe
                $wsRespuesta = $this->sendRPS($rps);
                
                if ($wsRespuesta->Cabecalho->Sucesso == "true") {
                    var_dump($wsRespuesta);
                    $numeroNfse = $wsRespuesta->ChaveNFeRPS->ChaveNFe->NumeroNFe;
                    $codVerificacao = $wsRespuesta->ChaveNFeRPS->ChaveNFe->CodigoVerificacao;
                    //$serieRPS = $wsRespuesta->ChaveNFeRPS->ChaveRPS->SerieRPS;
                    $nroRPS = $wsRespuesta->ChaveNFeRPS->ChaveRPS->NumeroRPS;
                    $dataFechaEnvio = gmdate("Y-m-d H:i:s",time());
                    $mySeguimiento = new Vseguimiento_paulistana($conexion);
                    $myFactura->setEstado(Vfacturas::getEstadoHabilitado());
                    $myFactura->setPropiedad(Vfacturas::getPropiedadNumeroFactura(), $numeroNfse);
                    $mySeguimiento->cod_factura = $myFactura->getCodigo();
                    $mySeguimiento->cod_filial = $conexion->database;
                    $mySeguimiento->codigo_verificacion = (string)$codVerificacao;
                    $mySeguimiento->estado = Vfacturas::getEstadoHabilitado();
                    $mySeguimiento->numero_nfse = $numeroNfse;
                    $mySeguimiento->numero_lote = $nroRPS;
                    $mySeguimiento->fecha_envio = $dataFechaEnvio;
                    $mySeguimiento->guardarSeguimiento_paulistana();
                    log_message('info', serialize($wsRespuesta));

                } elseif ($wsRespuesta->Cabecalho->Sucesso == "false") {
                    $errArr = array();
                    for ($i = 0; $i < sizeof($wsRespuesta->Erro); $i++) {
                        $errStr = "COD: " . $wsRespuesta->Erro[$i]->Codigo . " ERRO: " . $wsRespuesta->Erro[$i]->Descricao . " ";
                        $errArr[] = $errStr;
                    }
                    $errStr = implode($errArr);
                    $myFactura->setEstado(Vfacturas::getEstadoError());
                    $mySeguimiento = new Vseguimiento_paulistana($conexion);
                    $mySeguimiento->cod_factura = $myFactura->getCodigo();
                    $mySeguimiento->cod_filial = $conexion->database;
                    $mySeguimiento->estado = Vfacturas::getEstadoError();
                    $mySeguimiento->mensaje = $errStr;
                    $mySeguimiento->guardarSeguimiento_paulistana();
                    log_message('error', serialize($wsRespuesta));
                }
            }
        }
    }

    public function validarRespuestaAndSeguimiento($respuestas) {
        // Hecha solamente para desarrollo.
        $errArray = array();
        foreach ($respuestas as $respuesta){
            $wasSucess = $respuesta->Cabecalho->Sucesso;
            if ($wasSucess == "true"){
                var_dump("SUCESS!");
            } elseif ($wasSucess == "false") {
                var_dump($respuesta->Erro);
            }
        }
    }
    
    private function validateCert( $cert )
    {
        $data = openssl_x509_read( $cert );
        $certData = openssl_x509_parse( $data );

        $certValidDate = gmmktime( 0, 0, 0, substr( $certData['validTo'], 2, 2 ), substr( $certData['validTo'], 4, 2 ), substr( $certData['validTo'], 0, 2 ) );

        // obtem o timestamp da data de hoje
        $dHoje = gmmktime(0,0,0,date("m"),date("d"),date("Y"));

        if ( $certValidDate < time() ){
            error_log( __METHOD__ . ': Certificado expirado em ' . date( 'Y-m-d', $certValidDate ) );
            return false;
        }

        //diferença em segundos entre os timestamp
        $diferenca = $certValidDate - $dHoje;

        // convertendo para dias
        $diferenca = round($diferenca /(60*60*24),0);
        //carregando a propriedade
        $this->certDaysToExpire = $diferenca;

        return true;
    }

    private function loadCert()
    {
        $x509CertData = array();

        if ( ! openssl_pkcs12_read( file_get_contents( $this->pkcs12 ), $x509CertData, $this->passphrase ) ) {
            error_log( __METHOD__ . ': Certificado não pode ser lido. O arquivo esta corrompido ou em formato invalido.' );
            if($this->debug=='1'){
                echo ': Certificado não pode ser lido. O arquivo esta corrompido ou em formato invalido.';
            }
            return false;
        }
        $this->X509Certificate = preg_replace( "/[\n]/", '', preg_replace( '/\-\-\-\-\-[A-Z]+ CERTIFICATE\-\-\-\-\-/', '', $x509CertData['cert'] ) );

        if ( ! self::validateCert( $x509CertData['cert'] ) ) {
            return false;
        }

        if ( ! is_dir( $this->certDir ) ) {
            if ( ! mkdir( $this->certDir, 0777 ) ) {
                error_log( __METHOD__ . ': Falha ao criar o diretorio ' . $this->certDir );
                return false;
            }
        }

        if ( ! file_exists( $this->privateKey ) ) {
            if ( ! file_put_contents( $this->privateKey, $x509CertData['pkey'] ) ) {
                error_log( __METHOD__ . ': Falha ao criar o arquivo ' . $this->privateKey );
                return false;
            }
        }

        if ( ! file_exists( $this->publicKey ) ) {
            if ( ! file_put_contents( $this->publicKey, $x509CertData['cert'] ) ) {
                error_log( __METHOD__ . ': Falha ao criar o arquivo ' . $this->publicKey );
                return false;
            }
        }

        if ( ! file_exists( $this->key ) ) {
            if ( ! file_put_contents( $this->key, $x509CertData['cert'] . $x509CertData['pkey'] ) ) {
                error_log( __METHOD__ . ': Falha ao criar o arquivo ' . $this->key );
                return false;
            }
        }

        return true;
    }

    public function start()
    {
        //versão do SOAP
        $soapver = SOAP_1_2;
        //$soapver = SOAP_1_1;
        $opts = array(
            'http'=>array(
                'user_agent' => 'PHPSoapClient'
            )
        );

        $context = stream_context_create($opts);

        //$wsdl = 'https://nfe.prefeitura.sp.gov.br/ws/lotenfe.asmx?WSDL';
        $wsdl =  __DIR__.DIRECTORY_SEPARATOR."paulistana".DIRECTORY_SEPARATOR.'lotenfe.asmx';

        $params = array(
            'local_cert' => $this->key,
            'passphrase' => $this->passphrase,
            'connection_timeout' => 300,
            'encoding' => 'UTF-8',
            'verifypeer'    => false,
            'verifyhost'    => false,
            'soap_version'  => $soapver,
            'trace'         => true,
            'stream_context' => $context,
            'cache_wsdl' => WSDL_CACHE_NONE
        );

        try {
            $this->connectionSoap = new SoapClient( $wsdl, $params );
        } catch (Exception $e ) {
            //var_dump($e);die;
            error_log( 'Exception: ' . $e->getMessage() );
            if($this->debug=='1')
            {
                echo "erro de conexão soap. Tente novamente mais tarde !<br>\n";
                echo $e->getMessage();
            }
            throw new Exception('Erro ao carregar WSDL prefeitura:'.$e->getMessage());
        }

        if($this->debug=='1')
        {
            echo 'Conexão ok! <br>'.$wsdl;
            print_r($this->connectionSoap);
        }
    }

    private function send( $operation, $xmlDoc )
    {
        try {

            self::start();
            $this->signXML( $xmlDoc );

            echo $xmlDoc->saveXML();

            //ControleErroSQL('Uteis/NFSeSP.class.php->send', $xmlDoc->saveXML());
            $params = array(
                'VersaoSchema' => 1,
                'MensagemXML' => $xmlDoc->saveXML()
            );
            if($this->debug=='1')
            {
                echo "<br />\n Paramentros send->:";
                print_r($params);
                echo "<br />\n operation->:";
                print_r($operation);
            }

            $result = $this->connectionSoap->$operation( $params );

            if($this->debug=='1')
            {
                echo "<br />\n RETORNO->:";
                print_r($result);
            }
        } catch( Exception $e ) {
            error_log( 'Exception: ' . $e->getMessage() );
            throw new Exception($e->getMessage());
        }

        return new SimpleXMLElement( $result->RetornoXML );
    }

    private function createXML( $operation )
    {
        $xmlDoc = new DOMDocument( '1.0', 'UTF-8' );
        $xmlDoc->preservWhiteSpace = false;
        $xmlDoc->formatOutput = false;

        $data = '<?xml version="1.0" encoding="UTF-8"?><Pedido' . $operation . ' xmlns:xsd="' . $this->urlXsd .'" xmlns="' . $this->urlNfe . '" xmlns:xsi="' . $this->urlXsi . '"></Pedido' . $operation . '>';
        $xmlDoc->loadXML( str_replace( array("\r\n", "\n", "\r"), '', $data ));

        $root = $xmlDoc->documentElement;

        $header = $xmlDoc->createElementNS( '', 'Cabecalho' );
        $root->appendChild( $header );
        $header->setAttribute( 'Versao', 1 );
        $cnpjSender = $xmlDoc->createElement( 'CPFCNPJRemetente' );
        $cnpjSender->appendChild( $xmlDoc->createElement( 'CNPJ', $this->cnpjPrestador ) );
        $header->appendChild( $cnpjSender );

        return $xmlDoc;
    }

    private function createXMLp1( $operation )
    {
        $xmlDoc = new DOMDocument( '1.0', 'UTF-8' );
        $xmlDoc->preservWhiteSpace = false;
        $xmlDoc->formatOutput = false;

        $data = '<?xml version="1.0" encoding="UTF-8"?><Pedido'.$operation.' xmlns="' . $this->urlNfe . '" xmlns:xsi="' . $this->urlXsi . '"></Pedido' . $operation . '>';

        $xmlDoc->loadXML( str_replace( array("\r\n", "\n", "\r"), '', $data ), LIBXML_NOBLANKS | LIBXML_NOEMPTYTAG );

        $root = $xmlDoc->documentElement;

        $header = $xmlDoc->createElementNS( '', 'Cabecalho' );
        $root->appendChild( $header );
        $header->setAttribute( 'Versao', 1 );
        $cnpjSender = $xmlDoc->createElement( 'CPFCNPJRemetente' );
        $cnpjSender->appendChild( $xmlDoc->createElement( 'CNPJ', $this->cnpjPrestador ) );
        $header->appendChild( $cnpjSender );

        return $xmlDoc;
    }

    private function signXML( &$xmlDoc )
    {
        $root = $xmlDoc->documentElement;

        // DigestValue is a base64 sha1 hash with root tag content without Signature tag
        $digestValue = base64_encode( hash( 'sha1', $root->C14N( false, false, null, null ), true ) );

        $signature = $xmlDoc->createElementNS( $this->urlDsig, 'Signature' );
        $root->appendChild( $signature );

        $signedInfo = $xmlDoc->createElement( 'SignedInfo' );
        $signature->appendChild( $signedInfo );
        $newNode = $xmlDoc->createElement( 'CanonicalizationMethod' );
        $signedInfo->appendChild( $newNode );
        $newNode->setAttribute( 'Algorithm', $this->urlCanonMeth );
        $newNode = $xmlDoc->createElement( 'SignatureMethod' );
        $signedInfo->appendChild( $newNode );
        $newNode->setAttribute( 'Algorithm', $this->urlSigMeth );
        $reference = $xmlDoc->createElement( 'Reference' );
        $signedInfo->appendChild( $reference );
        $reference->setAttribute( 'URI', '' );
        $transforms = $xmlDoc->createElement( 'Transforms' );
        $reference->appendChild( $transforms );
        $newNode = $xmlDoc->createElement( 'Transform' );
        $transforms->appendChild( $newNode );
        $newNode->setAttribute( 'Algorithm', $this->urlTransfMeth_1 );
        $newNode = $xmlDoc->createElement( 'Transform' );
        $transforms->appendChild( $newNode );
        $newNode->setAttribute( 'Algorithm', $this->urlTransfMeth_2 );
        $newNode = $xmlDoc->createElement( 'DigestMethod' );
        $reference->appendChild( $newNode );
        $newNode->setAttribute( 'Algorithm', $this->urlDigestMeth );
        $newNode = $xmlDoc->createElement( 'DigestValue', $digestValue );
        $reference->appendChild( $newNode );

        // SignedInfo Canonicalization (Canonical XML)
        $signedInfoC14n = $signedInfo->C14N( false, false, null, null );

        // SignatureValue is a base64 SignedInfo tag content
        $signatureValue = '';
        $pkeyId = openssl_get_privatekey( file_get_contents( $this->privateKey ) );
        openssl_sign( $signedInfoC14n, $signatureValue, $pkeyId );
        $newNode = $xmlDoc->createElement( 'SignatureValue', base64_encode( $signatureValue ) );
        $signature->appendChild( $newNode );
        $keyInfo = $xmlDoc->createElement('KeyInfo');
        $signature->appendChild($keyInfo);
        $x509Data = $xmlDoc->createElement( 'X509Data' );
        $keyInfo->appendChild( $x509Data );
        $newNode = $xmlDoc->createElement( 'X509Certificate', $this->X509Certificate );
        $x509Data->appendChild( $newNode );

        openssl_free_key( $pkeyId );
    }

    private function signRPS( NFeRPS $rps, &$rpsNode )
    {
        $content = sprintf( '%08s', $rps->CCM ) .
            sprintf('%-5s',$rps->serie ) . // 5 chars
            sprintf( '%012s', $rps->numero ) .
            str_replace("-","", $rps->dataEmissao ) .
            $rps->tributacao .
            $rps->status .
            ( ( $rps->comISSRetido ) ? 'S' : 'N' ) .
            sprintf( '%015s', str_replace( array( '.', ',' ),'', number_format( $rps->valorServicos, 2 ) ) ).
            sprintf( '%015s', str_replace( array( '.', ',' ), '', number_format( $rps->valorDeducoes, 2 ) ) ) .
            sprintf( '%05s', $rps->codigoServico ) .
            ( ( $rps->contractorRPS->type == 'F' ) ? '1' : '2' ) .
            sprintf( '%014s', $rps->contractorRPS->cnpjTomador );
        $signatureValue = '';
        $pkeyId = openssl_get_privatekey( file_get_contents( $this->privateKey ) );
        openssl_sign( $content, $signatureValue, $pkeyId, OPENSSL_ALGO_SHA1 );
        openssl_free_key( $pkeyId );

        $rpsNode->appendChild( new DOMElement( 'Assinatura', base64_encode( $signatureValue ) ) );
    }

    private function insertRPS( NFeRPS $rps, &$xmlDoc )
    {
        $rpsNode = $xmlDoc->createElementNS( '', 'RPS' );
        $xmlDoc->documentElement->appendChild( $rpsNode );

        $this->signRPS( $rps, $rpsNode );
        if($this->debug=='1')
        {
            echo "<br />\n RPSnode->:";
            print_r($xmlDoc);
            echo "<br />\n";

        }
        $rpsKey = $xmlDoc->createElement( 'ChaveRPS' ); // 1-1
        $rpsKey->appendChild( $xmlDoc->createElement( 'InscricaoPrestador', $rps->CCM ) ); // 1-1
        $rpsKey->appendChild( $xmlDoc->createElement( 'SerieRPS', $rps->serie ) ); // 1-1 DHC AAAAA / alog AAAAB
        $rpsKey->appendChild( $xmlDoc->createElement( 'NumeroRPS', $rps->numero ) ); // 1-1
        $rpsNode->appendChild( $rpsKey );

        /* RPS ­ Recibo Provisório de Serviços
         * RPS-M ­ Recibo Provisório de Serviços proveniente de Nota Fiscal Conjugada (Mista)
         * RPS-C ­ Cupom */
        $rpsNode->appendChild( $xmlDoc->createElement( 'TipoRPS', $rps->type ) ); // 1-1

        $rpsNode->appendChild( $xmlDoc->createElement( 'DataEmissao', $rps->dataEmissao ) ); // 1-1

        /* N ­ Normal
         * C ­ Cancelada
         * E ­ Extraviada */
        $rpsNode->appendChild( $xmlDoc->createElement( 'StatusRPS', $rps->status ) ); // 1-1

        /* T - Tributação no município de São Paulo
         * F - Tributação fora do município de São Paulo
         * I ­- Isento
         * J - ISS Suspenso por Decisão Judicial */
        $rpsNode->appendChild( $xmlDoc->createElement( 'TributacaoRPS', $rps->tributacao ) ); // 1-1

        $rpsNode->appendChild( $xmlDoc->createElement( 'ValorServicos', sprintf( "%s", $rps->valorServicos ) ) ); // 1-1
        $rpsNode->appendChild( $xmlDoc->createElement( 'ValorDeducoes', sprintf( "%s", $rps->valorDeducoes ) ) ); // 1-1

        $rpsNode->appendChild( $xmlDoc->createElement( 'CodigoServico', $rps->codigoServico ) ); // 1-1
        $rpsNode->appendChild( $xmlDoc->createElement( 'AliquotaServicos', $rps->aliquotaServicos ) ); // 1-1

        $rpsNode->appendChild( $xmlDoc->createElement( 'ISSRetido', ( ( $rps->comISSRetido ) ? 'true' : 'false' ) ) ); // 1-1

        $cnpj = $xmlDoc->createElement( 'CPFCNPJTomador' ); // 0-1
        if ($rps->contractorRPS->type == "F") {
            $cnpj->appendChild( $xmlDoc->createElement( 'CPF', sprintf( '%011s', $rps->contractorRPS->cnpjTomador ) ) );
        } else {
            $cnpj->appendChild( $xmlDoc->createElement( 'CNPJ', sprintf( '%014s', $rps->contractorRPS->cnpjTomador ) ) );
        }
        $rpsNode->appendChild( $cnpj );
        if ($rps->contractorRPS->ccmTomador <> "") {
            $rpsNode->appendChild( $xmlDoc->createElement( 'InscricaoMunicipalTomador', $rps->contractorRPS->ccmTomador ) ); // 0-1
        }

        /*Guilherme Calabria Filho - guiii - htmlspecialchars para aceitar &  */
        $rpsNode->appendChild( $xmlDoc->createElement( 'RazaoSocialTomador', htmlspecialchars($rps->contractorRPS->name) ) ); // 0-1

        $address = $xmlDoc->createElement( 'EnderecoTomador' ); // 0-1
        $address->appendChild( $xmlDoc->createElement( 'TipoLogradouro', $rps->contractorRPS->tipoEndereco ) );
        $address->appendChild( $xmlDoc->createElement( 'Logradouro', $rps->contractorRPS->endereco ) );
        $address->appendChild( $xmlDoc->createElement( 'NumeroEndereco', $rps->contractorRPS->enderecoNumero ) );
        if (trim($rps->contractorRPS->complemento) != "") {
            $address->appendChild( $xmlDoc->createElement( 'ComplementoEndereco', $rps->contractorRPS->complemento ) );
        }
        $address->appendChild( $xmlDoc->createElement( 'Bairro', $rps->contractorRPS->bairro ) );
        $address->appendChild( $xmlDoc->createElement( 'Cidade', $rps->contractorRPS->cidade ) );
        $address->appendChild( $xmlDoc->createElement( 'UF', $rps->contractorRPS->estado ) );
        $address->appendChild( $xmlDoc->createElement( 'CEP', $rps->contractorRPS->cep ) );
        $rpsNode->appendChild( $address );

        $rpsNode->appendChild( $xmlDoc->createElement( 'EmailTomador', $rps->contractorRPS->email ) ); // 0-1

        $rpsNode->appendChild( $xmlDoc->createElement( 'Discriminacao', $rps->discriminacao ) ); // 1-1
        if($this->debug=='1')
        {
            echo "<br />\n RPS node 2->:";
            print_r($xmlDoc);
            echo "<br />\n";

        }
    }

    /**
     * Send a RPS to replace for NF-e
     *
     * @param NFeRPS $rps
     */
    public function sendRPS( NFeRPS $rps )
    {
        try{
            $operation = 'EnvioRPS';
            $xmlDoc = $this->createXML( $operation );
            if($this->debug=='1')
            {
                echo "<br />\nsendRPS-> RPS->:";
                print_r($rps);
                echo "<br />\n";

            }
            $this->insertRPS( $rps, $xmlDoc );
            //echo $xmlDoc->saveXML();
            if($this->debug=='1')
            {
                echo "<br />\n RPS xmlDoc->:";
                print_r($xmlDoc);
                echo "<br />\n";

            }
            $returnXmlDoc = $this->send( $operation, $xmlDoc );

            return $returnXmlDoc;
        }catch( Exception $e ) {
            throw new Exception($e->getMessage());
        }
        
    }



    /**
     * Send a batch of RPSs to replace for NF-e
     *
     * @param array $rangeDate ( 'start' => start date of RPSs, 'end' => end date of RPSs )
     * @param array $valorTotal ( 'servicos' => total value of RPSs, 'deducoes' => total deductions on values of RPSs )
     * @param array $rps Collection of NFeRPS
     */
    public function sendRPSBatch( $rangeDate, $valorTotal, $rps )
    {
        $operation = 'EnvioLoteRPS';

        $xmlDoc = $this->createXML( $operation );

        $header = $xmlDoc->documentElement->getElementsByTagName( 'Cabecalho' )->item( 0 );

        $header->appendChild( $xmlDoc->createElement( 'transacao', 'false' ) );
        $header->appendChild( $xmlDoc->createElement( 'dtInicio', $rangeDate['inicio'] ) );
        $header->appendChild( $xmlDoc->createElement( 'dtFim', $rangeDate['fim'] ) );
        $header->appendChild( $xmlDoc->createElement( 'QtdRPS', count( $rps ) ) );
        $header->appendChild( $xmlDoc->createElement( 'ValorTotalServicos', $valorTotal['servicos'] ) );
        $header->appendChild( $xmlDoc->createElement( 'ValorTotalDeducoes', $valorTotal['deducoes'] ) );

        foreach ( $rps as $item ) {
            $this->insertRPS( $item, $xmlDoc );
        }

        return $this->send( $operation, $xmlDoc );
    }



    /**
     * Send a batch of RPSs to replace for NF-e for test only
     *
     * @param array $rangeDate ( 'start' => start date of RPSs, 'end' => end date of RPSs )
     * @param array $valorTotal ( 'servicos' => total value of RPSs, 'deducoes' => total deductions on values of RPSs )
     * @param array $rps Collection of NFeRPS
     */
    public function sendRPSBatchTest( $rangeDate, $valorTotal, $rps )
    {
        if($this->erronaclasse == true) return false;
        $operation = 'TesteEnvioLoteRPS';
        $xmlDoc = $this->createXML( $operation );

        $header = $xmlDoc->documentElement->getElementsByTagName( 'Cabecalho' )->item( 0 );

        $header->appendChild( $xmlDoc->createElement( 'transacao', 'false' ) );
        $header->appendChild( $xmlDoc->createElement( 'dtInicio', $rangeDate['inicio'] ) );
        $header->appendChild( $xmlDoc->createElement( 'dtFim', $rangeDate['fim'] ) );
        $header->appendChild( $xmlDoc->createElement( 'QtdRPS', count( $rps ) ) );
        $header->appendChild( $xmlDoc->createElement( 'ValorTotalServicos', $valorTotal['servicos'] ) );
        $header->appendChild( $xmlDoc->createElement( 'ValorTotalDeducoes', $valorTotal['deducoes'] ) );

        foreach ( $rps as $item ) {
            $this->insertRPS( $item, $xmlDoc );
        }

        //$docxml = $xmlDoc->saveXML();
        //echo "xml gerado[<br>\n";
        //print_r($docxml);
        //echo "]<br>\n";
        //exit();

        $return = $this->send( 'TesteEnvioLoteRPS', $xmlDoc );
        $xmlDoc->formatOutput = true;
        error_log( __METHOD__ . ': ' . $xmlDoc->saveXML() );

        return $return;
    }



    /**
     *
     * @param array $nfe Array of NFe numbers
     */
    public function cancelNFe( $nfeNumbers )
    {
        $operation = 'CancelamentoNFe';

        $xmlDoc = $this->createXML( $operation );

        $root = $xmlDoc->documentElement;
        $header = $root->getElementsByTagName( 'Cabecalho' )->item( 0 );

        $header->appendChild( $xmlDoc->createElement( 'transacao', 'false' ) );

        foreach ( $nfeNumbers as $nfeNumber )
        {
            $detail = $xmlDoc->createElementNS( '','Detalhe' );
            $root->appendChild( $detail );

            $nfeKey = $xmlDoc->createElement( 'ChaveNFe' ); // 1-1
            $nfeKey->appendChild( $xmlDoc->createElement( 'InscricaoPrestador', $this->ccmPrestador ) ); // 1-1
            $nfeKey->appendChild( $xmlDoc->createElement( 'NumeroNFe', $nfeNumber ) ); // 1-1

            $detail->appendChild( $nfeKey );

            $content = sprintf( '%08s', $this->ccmPrestador) .
                sprintf( '%012s', $nfeNumber );

            $signatureValue = '';
            $digestValue = base64_encode( hash( 'sha1', $content, true ) );
            $pkeyId = openssl_get_privatekey( file_get_contents( $this->privateKey ) );
//      openssl_sign( $digestValue, $signatureValue, $pkeyId );

            openssl_sign( $content, $signatureValue, $pkeyId, OPENSSL_ALGO_SHA1 );
            openssl_free_key( $pkeyId );

            $detail->appendChild( new DOMElement( 'AssinaturaCancelamento', base64_encode( $signatureValue ) ) );
        }

        $docxml = $xmlDoc->saveXML();
        return $this->send( $operation, $xmlDoc );
    }



    public function queryNFe( $nfeNumber, $rpsNumber, $rpsSerie )
    {
        $operation = 'ConsultaNFe';

        $xmlDoc = $this->createXMLp1( $operation );
        $root = $xmlDoc->documentElement;
        if ( $nfeNumber <= 0 && $rpsNumber <= 0 )
        {
            throw new Exception('Operação inválida, não foi recebido número da NFe nem o número do RPS');
            return FALSE;
        }
        if ($nfeNumber > 0) {
            $detailNfe = $xmlDoc->createElementNS( '', 'Detalhe' );
            $root->appendChild( $detailNfe );

            $nfeKey = $xmlDoc->createElement( 'ChaveNFe' ); // 1-1
            $nfeKey->appendChild( $xmlDoc->createElement( 'InscricaoPrestador', $this->ccmPrestador ) ); // 1-1
            $nfeKey->appendChild( $xmlDoc->createElement( 'NumeroNFe', $nfeNumber) ); // 1-1
            $detailNfe->appendChild( $nfeKey );
        }

        if ($rpsNumber > 0) {
            //$detailRps = $xmlDoc->createElement( 'Detalhe' );
            $detailRps = $xmlDoc->createElementNS('', 'Detalhe' );
            $root->appendChild( $detailRps );

            $rpsKey = $xmlDoc->createElement( 'ChaveRPS' ); // 1-1
            $rpsKey->appendChild( $xmlDoc->createElement( 'InscricaoPrestador', $this->ccmPrestador ) ); // 1-1
            $rpsKey->appendChild( $xmlDoc->createElement( 'SerieRPS', $rpsSerie ) ); // 1-1 DHC AAAAA / alog AAAAB
            $rpsKey->appendChild( $xmlDoc->createElement( 'NumeroRPS', $rpsNumber ) ); // 1-1
            $detailRps->appendChild( $rpsKey );
        }
        return $this->send( $operation, $xmlDoc );
    }


    /**
     * queryNFeReceived and queryNFeIssued have the same XML request model
     *
     * @param string $cnpj
     * @param string $ccm
     * @param string $startDate YYYY-MM-DD
     * @param string $endDate YYYY-MM-DD
     */
    private function queryNFeWithDateRange( $cnpj, $ccm, $startDate, $endDate )
    {
        $operation = 'ConsultaNFePeriodo';

        $xmlDoc = $this->createXML( $operation );

        $header = $xmlDoc->documentElement->getElementsByTagName( 'Cabecalho' )->item( 0 );

        $cnpjTaxpayer = $xmlDoc->createElement( 'CPFCNPJ' );
        $cnpjTaxpayer->appendChild( $xmlDoc->createElement( 'CNPJ', $cnpj ) );
        $header->appendChild( $cnpjTaxpayer );

        $ccmTaxpayer = $xmlDoc->createElement( 'Inscricao', $ccm );
        $header->appendChild( $ccmTaxpayer );

        $startDateNode = $xmlDoc->createElement( 'dtInicio', $startDate );
        $header->appendChild( $startDateNode );

        $endDateNode = $xmlDoc->createElement( 'dtFim', $endDate );
        $header->appendChild( $endDateNode );

        $pageNumber = $xmlDoc->createElement( 'NumeroPagina', 1 );
        $header->appendChild( $pageNumber );

        return $xmlDoc;
    }


    /**
     * Query NF-e's that CNPJ/CCM company received from other companies
     *
     * @param string $cnpj
     * @param string $ccm
     * @param string $startDate YYYY-MM-DD
     * @param string $endDate YYYY-MM-DD
     */
    public function queryNFeReceived( $cnpj, $ccm, $startDate, $endDate )
    {
        $operation = 'ConsultaNFeRecebidas';

        $xmlDoc = $this->queryNFeWithDateRange( $cnpj, $ccm, $startDate, $endDate );

        return $this->send( $operation, $xmlDoc );
    }


    /**
     * Query NF-e's that CNPJ/CCM company issued to other companies
     *
     * @param string $cnpj
     * @param string $ccm
     * @param string $startDate YYYY-MM-DD
     * @param string $endDate YYYY-MM-DD
     */
    public function queryNFeIssued( $cnpj, $ccm, $startDate, $endDate )
    {
        $operation = 'ConsultaNFeEmitidas';

        $xmlDoc = $this->queryNFeWithDateRange( $cnpj, $ccm, $startDate, $endDate );

        return $this->send( $operation, $xmlDoc );
    }



    public function queryBatch( $batchNumber )
    {
        $operation = 'ConsultaLote';

        $xmlDoc = $this->createXML( $operation );

        $header = $xmlDoc->documentElement->getElementsByTagName( 'Cabecalho' )->item( 0 );

        $header->appendChild( $xmlDoc->createElement( 'NumeroLote', $batchNumber ) );

        return $this->send( $operation, $xmlDoc );
    }


    /**
     * If $batchNumber param is null, last match info will be returned
     *
     * @param integer $batchNumber
     */
    public function queryBatchInfo( $batchNumber = null )
    {
        $operation = 'InformacoesLote';

        $xmlDoc = $this->createXML( $operation );

        $header = $xmlDoc->documentElement->getElementsByTagName( 'Cabecalho' )->item( 0 );

        $header->appendChild( $xmlDoc->createElement( 'InscricaoPrestador', $this->ccmPrestador ) );

        if ( $batchNumber ) {
            $header->appendChild( $xmlDoc->createElement( 'NumeroLote', $batchNumber ) );
        }

        return $this->send( $operation, $xmlDoc );
    }


    /**
     * Returns CCM for given CNPJ
     *
     * @param string $cnpj
     */
    public function queryCNPJ( $cnpj )
    {
        if($this->debug=='1') echo 'queryCNPJ<br />';

        $operation = 'ConsultaCNPJ';

        $xmlDoc = $this->createXMLp1( $operation );

        $root = $xmlDoc->documentElement;

        $cnpjTaxpayer = $xmlDoc->createElementNS('', 'CNPJContribuinte' );
        if (strlen($cnpj) == 11) {
            $cnpjTaxpayer->appendChild( $xmlDoc->createElement( 'CPF', (string) sprintf( '%011s', $cnpj ) ) );
        } else {
            $cnpjTaxpayer->appendChild( $xmlDoc->createElement( 'CNPJ', (string) sprintf( '%014s', $cnpj ) ) );
        }
        $root->appendChild( $cnpjTaxpayer );

        $docxml = $xmlDoc->saveXML();
        if($this->debug=='1')
        {
            echo 'XML queryCNPJ:<br>'.$docxml;
        }
        if ($return = $this->send( $operation, $xmlDoc ))
        {
            if($this->debug=='1')
            {
                echo 'Retorno queryCNPJ:<br>'.$return;
            }
            if ($return->Detalhe->InscricaoMunicipal <> "")
            {
                return $return->Detalhe->InscricaoMunicipal;
            }
            else
            {
                if ($return->Alerta->Codigo <> "")
                {
                    return $return->Alerta->Descricao;
                }
                else
                {
                    return false;
                }
            }
        }
        else {
            return false;
        }
    }



    /**
     * Create a line with RPS description for batch file
     *
     * @param unknown_type $rps
     * @param unknown_type $body
     */
    private function insertTextRPS( NFeRPS $rps, &$body )
    {
        if ( $rps->valorServicos > 0 ) {
            $line = "2" .
                sprintf( "%-5s", $rps->type ) .
                sprintf( "%-5s", $rps->serie ) .
                sprintf( '%012s', $rps->numero ) .
                str_replace("-","", $rps->dataEmissao ) .
                $rps->tributacao .
                sprintf( '%015s', str_replace( '.', '', sprintf( '%.2f', $rps->valorServicos ) ) ) .
                sprintf( '%015s', str_replace( '.', '', sprintf( '%.2f', $rps->valorDeducoes ) ) ) .
                sprintf( '%05s', $rps->codigoServico ) .
                sprintf( '%04s', str_replace( '.', '', $rps->aliquotaServicos ) ) .
                ( ( $rps->comISSRetido ) ? '1' : '2' ) .
                ( ( $rps->contractorRPS->type == 'F' ) ? '1' : '2' ) .
                sprintf( '%014s', $rps->contractorRPS->cnpjTomador ) .
                sprintf( '%08s', $rps->contractorRPS->ccmTomador ) .
                sprintf( '%012s', '' ) .
                sprintf( '%-75s', mb_convert_encoding( $rps->contractorRPS->name, 'ISO-8859-1', 'UTF-8' ) ) .
                sprintf( '%3s', ( ( $rps->contractorRPS->tipoEndereco == 'R' ) ? 'Rua' : '' ) ) .
                sprintf( '%-50s', mb_convert_encoding( $rps->contractorRPS->endereco, 'ISO-8859-1', 'UTF-8' ) ) .
                sprintf( '%-10s', $rps->contractorRPS->enderecoNumero ) .
                sprintf( '%-30s', mb_convert_encoding( $rps->contractorRPS->complemento, 'ISO-8859-1', 'UTF-8' ) ) .
                sprintf( '%-30s', mb_convert_encoding( $rps->contractorRPS->bairro, 'ISO-8859-1', 'UTF-8' ) ) .
                sprintf( '%-50s', mb_convert_encoding( $rps->contractorRPS->cidade, 'ISO-8859-1', 'UTF-8' ) ) .
                sprintf( '%-2s', $rps->contractorRPS->estado ) .
                sprintf( '%08s', $rps->contractorRPS->cep ) .
                sprintf( '%-75s', $rps->contractorRPS->email ) .
                str_replace( "\n", '|', mb_convert_encoding( $rps->discriminacao, 'ISO-8859-1', 'UTF-8' ) );

            $body .= $line . chr( 13 ) . chr( 10 );
        }
    }


    /**
     * Create a batch file with NF-e text layout
     *
     * @param unknown_type $rangeDate
     * @param unknown_type $valorTotal
     * @param unknown_type $rps
     */
    public function textFile( $rangeDate, $valorTotal, $rps )
    {
        $file = '';

        $header = "1" .
            "001" .
            $this->ccmPrestador .
            date( "Ymd", $rangeDate['inicio'] ) .
            date( "Ymd", $rangeDate['fim'] ) .
            chr( 13 ) . chr( 10 );

        $body = '';
        foreach ( $rps as $item ) {
            $this->insertTextRPS( $item, $body );
        }

        $footer = "9" .
            sprintf( "%07s", count( $rps ) ) .
            sprintf( "%015s", str_replace( '.', '', sprintf( '%.2f', $valorTotal['servicos'] ) ) ) .
            sprintf( "%015s", str_replace( '.', '', sprintf( '%.2f', $valorTotal['deducoes'] ) ) ) .
            chr( 13 ) . chr( 10 );

        $rpsDir = '/patch/for/rps/batch/file';
        $rpsFileName = date( "Y-m-d_Hi" ) . '.txt';
        $rpsFullPath = $rpsDir . '/' . $rpsFileName;
        if ( ! is_dir( $rpsDir ) ) {
            if ( ! mkdir( $rpsDir, 0777 ) ) {

            }
        }

        if ( ! file_put_contents( $rpsFullPath, $header . $body . $footer ) ) {
            error_log( __METHOD__ . ': Cannot create rps file ' . $rpsFullPath );
            return false;
        }

        return $rpsFullPath;
    }
}

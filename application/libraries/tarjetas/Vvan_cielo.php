<?php

class Vvan_cielo{
    
    static private $erroresPrioridadMedia = array(
        "no_se_puede_cargar_el_archivo_por_ser_un_archivo_de_recuperacion",
        "el_archivo_ya_ha_sido_cargado_o_la_secuencia_es_duplicada",
        "el_extracto_esperado_es_3",
        "el_archivo_posee_tipos_de_registros_no_reconocidos",
        "no_se_ha_localizado_ningun_registro_CV_o_RO_en_el_archivo"
    );
    
    static private $erroresPrioridadAlta = array(
        "se_esperaba_un_registro_header_y_un_registro_trailer",
        "la_cantidad_de_caracteres_esperados_por_linea_es_250",
        "no_se_ha_podido_procesar_el_header_de_archivo",
        "el_numero_informado_en_el_archivo_no_pertenece_a_la_matriz_habilitada_de_la_filial",
        "no_se_ha_podido_procesar_el_trailer",
        "se_esperaba_un_registro_header_de_archivo",
        "se_esperaba_un_registro_trailer_de_archivo",
        "cantidad_de_registros_informados_en_el_trailer_difiere_a_los_encontrados_en_el_archivo",
        "no_se_ha_podido_procesar_el_registro_RO",
        "no_se_ha_podido_procesar_el_registro_CV"
    );
    
    static private $erroresPrioridadBaja = array(); // definir o cambiar alguno de otras prioridades
    
    static private $identificadorTasaEmbarque = array(
        "TX" => "Taxa de embarque",
        "VE" => "Valor de entrada"
    );
    
    static private $indicadorTarjetaEmitido = array(
        "0000" => "Serviço não atribuído",
        "0001" => "Cartão emitido no Brasil",
        "0002" => "Cartão emitido no exterior"        
    );
    
    static private $anticipacionProductoFinanciero = array(
        " " => "Não antecipado",
        "A" => "Antecipado na Cielo - ARV",
        "C" => "Antecipado no banco - Cessão de Recebíveis"
    );
    
    /* tableta 1 */
    static private $tipoOperacion = array(
        "01" => "Venda com CV",
        "02" => "Venda sem CV",
        "03" => "Venda com CV + Parcelado Futuro",
        "04" => "Pagamento com CV",
        "05" => "Pagamento sem CV",
        "06" => "Antecipação de Recebíveis com CV ou sem CV",
        "07" => "Cessão de Recebíveis",
        "08" => "Parcelas Pendentes",
        "09" => "Saldo em aberto"
    );
    
    /* tableta 2 */
    static private $tipoTransaccion = array(
        "01" => "Venda",
        "02" => "Ajuste a crédito",
        "03" => "Ajuste a débito",
        "04" => "Pacote Cielo",
        "05" => "Reagendamento"
    );
    
    /* tableta 3 */
    static private $estadoPago = array(
        "00" => "Agendado",
        "01" => "Pago",
        "02" => "Enviado para o Banco",
        "03" => "A confirmar"
    );
    
    /* tableta 5 */
    static private $origenAjuste = array(
        "01" => "Acerto de correção monetária",
        "02" => "Acerto de data de pagamento",
        "03" => "Acerto de taxa de comissão",
        "04" => "Acerto de valores não processados",
        "05" => "Acerto de valores não recebidos",
        "06" => "Acerto de valores não reconhecidos",
        "07" => "Acerto de valores negociados",
        "08" => "Acerto de valores processados indevidamente",
        "09" => "Acerto de lançamento não compensado em conta-corrente",
        "10" => "Acerto referente valores contestados",
        "11" => "Acerto temporário de valores contestados",
        "12" => "Acertos diversos",
        "13" => "Acordo de cobrança",
        "14" => "Acordo jurídico",
        "15" => "Aplicação de multa Programa Monitoria Chargeback",
        "16" => "Bloqueio de valor por ordem judicial",
        "17" => "Cancelamento da venda",
        "18" => "Cobrança de tarifa operacional",
        "19" => "Cobrança mensal Lynx Comércio",
        "20" => "Cobrança Plano Cielo",
        "21" => "Contrato de caução",
        "22" => "Crédito de devolução do cancelamento - banco emissor",
        "23" => "Crédito EC - referente contestação portador",
        "24" => "Crédito por cancelamento rejeitado - Cielo",
        "25" => "Processamento do débito duplicado - Visa Pedágio",
        "26" => "Débito por venda realizada sem a leitura do chip",
        "27" => "Débito por venda rejeitada no sistema - Cielo",
        "28" => "Débito referente à contestação do portador",
        "29" => "Estorno de acordo jurídico",
        "30" => "Estorno de contrato de caução",
        "31" => "Estorno de acordo de cobrança",
        "32" => "Estorno de bloqueio de valor por ordem judicial",
        "33" => "Estorno de cancelamento de venda",
        "34" => "Estorno de cobrança de tarifa operacional",
        "35" => "Estorno de cobrança mensal Lynx Comércio",
        "36" => "Estorno de cobrança Plano Cielo",
        "37" => "Estorno de débito venda sem a leitura do Chip",
        "38" => "Estorno de incentivo comercial",
        "39" => "Estorno de Multa Programa Monitoria Chargeback",
        "40" => "Estorno de rejeição ARV",
        "41" => "Estorno de reversão de duplicidade do pagamento - ARV",
        "42" => "Estorno de tarifa de cadastro",
        "43" => "Estorno de extrato no papel",
        "44" => "Estorno de processamento duplicado de débito - Visa Pedágio",
        "45" => "Incentivo comercial",
        "46" => "Incentivo por venda de Recarga",
        "47" => "Regularização de rejeição ARV",
        "48" => "Reversão de duplicidade do pagamento - ARV",
        "49" => "Tarifa de cadastro",
        "50" => "Tarifa de extrato no papel",
        "51" => "Aceleração de débito de antecipação",
        "52" => "Débito por descumprimento de cláusula contratual",
        "53" => "Débito por cancelamento de venda",
        "54" => "Débito por não reconhecimento de compra",
        "55" => "Débito por venda com cartão com validade vencida",
        "56" => "Débito por não reconhecimento de compra",
        "57" => "Débito por cancelamento e/ou devolução dos serviços",
        "58" => "Débito por transação irregular",
        "59" => "Débito por não entrega da mercadoria",
        "60" => "Débito por serviços não prestados",
        "61" => "Débito efetuado por venda sem código de autorizacão",
        "62" => "Débito efetuado por venda com número de cartão inválido",
        "63" => "Débito por cópia de CV e/ou documento não atendido",
        "64" => "Débito por venda efetuada com autorização negada",
        "65" => "Débito por envio de CV e/ou documento ilegível",
        "66" => "Débito por venda efetuada sem leitura de chip",
        "67" => "Débito por venda em outra moeda",
        "68" => "Débito por venda processada incorretamente",
        "69" => "Débito por cancelamento de venda",
        "70" => "Débito por crédito em duplicidade",
        "71" => "Débito por documentos solicitados e não recebidos",
        "72" => "Débito por envio de CV e/ou documento incorreto",
        "73" => "Débito por envio de CV e/ou documento fora do prazo",
        "74" => "Débito por não reconhecimento de despesa",
        "75" => "Débito por documentação solicitada incompleta",
        "76" => "Débito por estabelecimento não possui CV e/ou doc.",
        "77" => "Programa de monitoria de chargeback",
        "78" => "Serviços Score",
        "79" => "Reagendamento do débito de antecipação",
        "80" => "Ajuste do débito de cessão",
    );
    
    /* tableta 7 */
    static private $mediosCapturas = array(
        "01" => "POS - Point of Sale",
        "02" => "PDV - Ponto de Venda ou TEF (Transferência Eletrônica de Fundos)",
        "03" => "E-Commerce - Comércio Eletrônico",
        "04" => "EDI - Troca Eletrônica de Dados",
        "05" => "ADP/BSP - Empresa Capturadora",
        "06" => "Manual",
        "07" => "URA/CVA",
        "08" => "Mobile",
        "09" => "Moedeiro eletrônico em rede",
    );
    
    /* tableta 6 */
    static private $bandeiras = array(
        "001" => "VISA",
        "002" => "Mastercard",
        "006" => "SoroCred",
        "007" => "ELO",
        "009" => "Diners",
        "011" => "Agiplan",
        "015" => "Banescard",
        "023" => "Cabal",
        "029" => "Credsystem",
        "035" => "Esplanada",
        "064" => "Credz"
    );
    
    /* tableta 8 */
    static private $motivoRechazo = array(
        "002" => "Cartão Inválido",
        "023" => "Outros Erros",
        "031" => "Transação de saque com cartão Electron valor zerado",
        "039" => "Banco emissor inválido",
        "044" => "Data da transação inválida",
        "045" => "Código de Autorização inválido",
        "055" => "Número de parcelas inválido",
        "056" => "Transação fi nanciada para estabelecimento não autorizado",
        "057" => "Cartão em boletim protetor",
        "061" => "Número de cartão inválido",
        "066" => "Transação não autorizada",
        "067" => "Transação não autorizada",
        "069" => "Transação não autorizada",
        "070" => "Transação não autorizada",
        "071" => "Transação não autorizada",
        "072" => "Transação não autorizada",
        "073" => "Transação inválida",
        "074" => "Valor de transação inválido",
        "075" => "Número de cartão inválido",
        "077" => "Transação não autorizada",
        "078" => "Transação não autorizada",
        "079" => "Transação não autorizada",
        "080" => "Transação não autorizada",
        "081" => "Cartão vencido",
        "082" => "Transação não autorizada",
        "083" => "Transação não autorizada",
        "084" => "Transação não autorizada",
        "086" => "Transação não autorizada",
        "092" => "Banco emissor sem comunicação",
        "093" => "Desbalanceamento no plano parcelado",
        "094" => "Venda parcelada para cartão emitido no exterior",
        "097" => "Valor de parcela menor do que o permitido",
        "099" => "Banco emissor inválido",
        "100" => "Transação não autorizada",
        "101" => "Transação duplicada",
        "102" => "Transação duplicada",
        "124" => "BIN não cadastrao",
        "126" => "Transação de saque com cartão Electron inválida",
        "128" => "Transação de saque com cartão Electron inválida",
        "129" => "Transação de saque com cartão Electron inválida",
        "130" => "Transação de saque com cartão Electron inválida",
        "133" => "Transação de saque com cartão Electron inválida",
        "134" => "Transação de saque com cartão Electron inválida",
        "145" => "Estabelecimento inválido para distribuição"
    );
    
    /* tableta 4 */
    static private $productos = array(
        "001" => "Agiplan crédito à vista",
        "002" => "Agiplan parcelado loja",
        "003" => "Banescard crédito à vista",
        "004" => "Banescard parcelado loja",
        "005" => "Esplanada crédito à vista",
        "006" => "Credz crédito à vista",
        "007" => "Esplanada parcelado loja",
        "008" => "Credz parcelado loja",
        "009" => "Elo Crediário",
        "010" => "Mastercard crédito à vista",
        "011" => "Maestro",
        "012" => "Mastercard parcelado loja",
        "013" => "Elo Construcard",
        "014" => "Elo Agro Débito",
        "015" => "Elo Agro Custeio",
        "016" => "Elo Agro Investimento",        
        "017" => "Elo Agro Custeio + Débito",
        "018" => "Elo Agro Investimento + Débito",
        "019" => "Discover crédito à vista",
        "020" => "Diners crédito à vista",
        "021" => "Diners parcelado loja",
        "022" => "Agro Custeio + Electron",
        "023" => "Agro Investimento + Electron",
        "024" => "FCO Investimento",
        "025" => "Agro Electron",
        "026" => "Agro Custeio",
        "027" => "Agro Investimento",
        "028" => "FCO Giro",
        "033" => "JCB",
        "036" => "Saque com cartão de Débito VISA",
        "037" => "Flex Car Visa Vale",
        "038" => "redsystem crédito à vista",
        "039" => "Credsystem parcelado loja",
        "040" => "Visa Crédito à Vista",
        "041" => "Visa Electron Débito à Vista",
        "042" => "Visa Pedágio",
        "043" => "Visa Parcelado Loja",
        "044" => "Visa Electron Pré-Datado",
        "045" => "Alelo Refeição (Bandeira Visa/Elo)",
        "046" => "Alelo Alimentação (Bandeira Visa/Elo)",
        "058" => "Elo Cultura",
        "059" => "Alelo Auto",
        "061" => "Sorocred crédito à vista",
        "062" => "Sorocred parcelado loja",
        "064" => "Visa Crediário",
        "065" => "Alelo Refeição (Bandeira Elo)",
        "066" => "Alelo Alimentação (Bandeira Elo)",
        "067" => "Visa Capital de Giro",
        "068" => "Visa Crédito Imobiliário",
        "069" => "Cultura Visa Vale",
        "070" => "Elo Crédito",
        "071" => "Elo Débito a Vista",
        "072" => "Elo Parcelado Loja",
        "079" => "Pagamento Carnê Visa Electron",
        "080" => "Visa Crédito Conversor de Moeda",
        "081" => "Elo Crédito Especializado",
        "089" => "Elo Crédito Imobiliário",
        "091" => "Mastercard Crédito Especializado",
        "094" => "Banescard Débito",
        "096" => "Cabal crédito à vista",
        "097" => "Cabal Débito",
        "098" => "Cabal parcelado loja",
        "342" => "Master Pedágio",
        "377" => "Elo Carnê",
        "378" => "Master Carnê",
        "380" => "Mastercard Crédito Conversor de Moeda"
    );
    
    /* PRIVATE FUNCTIONS */
    
    /* PUBLIC FUNCTIONS */
    
    /* STATIC FUNCTIONS */
    
    static public function getIdentificadorProductoFinanciero($codigoIdentificador = null){
        if ($codigoIdentificador != null){
            return isset(self::$anticipacionProductoFinanciero[$codigoIdentificador]) ? self::$anticipacionProductoFinanciero[$codigoIdentificador] : null;
        } else {
            return self::$anticipacionProductoFinanciero;
        }
    }
    
    static public function getTiposOperacion($codigoTipo = null){
        if ($codigoTipo != null){
            return isset(self::$tipoOperacion[$codigoTipo]) ? self::$tipoOperacion[$codigoTipo] : null;
        } else {
            return self::$tipoOperacion;
        }
    }
    
    static public function getTiposTransaccion($codigoTipo = null){
        if ($codigoTipo != null){
            return isset(self::$tipoTransaccion[$codigoTipo]) ? self::$tipoTransaccion[$codigoTipo]: null;
        } else {
            return self::$tipoTransaccion; 
        }
    }
    
    static public function getEtadosPago($codigoEstado = null){
        if ($codigoEstado != null){
            return isset(self::$estadoPago[$codigoEstado]) ? self::$estadoPago[$codigoEstado] : null;
        } else {
            return self::$estadoPago;
        }
    }
    
    static public function getOrigenAjuste($codigoOrigen = null){
        if ($codigoOrigen != null){
            return isset(self::$origenAjuste[$codigoOrigen]) ? self::$origenAjuste[$codigoOrigen] : null;
        } else {
            return self::$origenAjuste;
        }
    }
    
    static public function getMediosCapturas($codigoMedio = null){
        if ($codigoMedio != null){
            return isset(self::$mediosCapturas[$codigoMedio]) ? self::$mediosCapturas[$codigoMedio] : null;
        } else {
            return self::$mediosCapturas;
        }
    }
    
    static public function getBandeiras($codigoBandeira = null){
        if ($codigoBandeira != null){
            return isset(self::$bandeiras[$codigoBandeira]) ? self::$bandeiras[$codigoBandeira] : null;
        } else {
            return self::$bandeiras;
        }
    }
    
    static public function getProductos($codigoProducto = null){
        if ($codigoProducto != null){
            return isset(self::$productos[$codigoProducto]) ? self::$productos[$codigoProducto] : null;
        } else {
            return self::$productos;
        }
    }
    
    static public function getMotivosRechazo($codigoMotivo = null){
        if ($codigoMotivo != null){
            return isset(self::$motivoRechazo[$codigoMotivo]) ? self::$motivoRechazo[$codigoMotivo] : null;
        } else {
            return self::$motivoRechazo;
        }
    }
    
    static public function getIndicadorTarjetaEmitido($codigoIndicador = null){
        if ($codigoIndicador != null){
            return isset(self::$indicadorTarjetaEmitido[$codigoIndicador]) ? self::$indicadorTarjetaEmitido[$codigoIndicador] : null;
        } else {
            return self::$indicadorTarjetaEmitido;
        }
    }
    
    static public function getIdentificadorTasaEmbarque($codigoIdentificador = null){
        if ($codigoIdentificador != null){
            return isset(self::$identificadorTasaEmbarque[$codigoIdentificador]) ? self::$identificadorTasaEmbarque[$codigoIdentificador] : null;
        } else {
            return self::$identificadorTasaEmbarque;
        }
    }
    
    static public function getErroresPrioridadBaja(){
        return self::$erroresPrioridadBaja;
    }
    
    static public function getErroresPrioridadMedia(){
        return self::$erroresPrioridadMedia;
    }
    
    static public function getErroresPrioridadAlta(){
        return self::$erroresPrioridadAlta;
    }
    
    static function procesarArchivo(CI_DB_mysqli_driver $conexion, $filePath, $codFilial, $nombreArchivoOriginal){
        if (!file_exists($filePath)){
            throw new Exception ("no existe el archivo $filePath");
        }
        $string = file_get_contents($filePath);
        $arrLineas = explode("\n", $string);
        if (count($arrLineas) < 2){
            throw new Exception ("se_esperaba_un_registro_header_y_un_registro_trailer");
        } 
        $myFilial = new Vfiliales($conexion, $codFilial);
        $arrLineasRO = array();
        $arrLineasCV = array();
        foreach ($arrLineas as $linea){
            if (strlen($linea) > 0){
                if (strlen($linea) <> 251){
                    throw new Exception ("la_cantidad_de_caracteres_esperados_por_linea_es_250");
                }
                $tipoRegistro = substr($linea, 0, 1);
                switch ($tipoRegistro) {
                    case "0":   // header
                        $myHeader = new Vvan_cielo_header($conexion);
                        $myHeader->nombre_archivo = $nombreArchivoOriginal;
                        if (!$myHeader->loadFromString($linea)){
                            throw new Exception ("no_se_ha_podido_procesar_el_header_de_archivo");
                        }
                        if ($myHeader->secuencia == '9999999'){
                            throw new Exception ("no_se_puede_cargar_el_archivo_por_ser_un_archivo_de_recuperacion");
                        }
                        $arrTemp = Vvan_cielo_header::listarVan_cielo_header($conexion, array("secuencia" => $myHeader->secuencia));
                        if (count($arrTemp) > 0){
                            throw new Exception ("el_archivo_ya_ha_sido_cargado_o_la_secuencia_es_duplicada");
                        }
                        if (!$myFilial->validarEstablecimientoTarjeta($myHeader->establecimiento_matriz)){ 
                            throw new Exception ("el_numero_informado_en_el_archivo_no_pertenece_a_la_matriz_habilitada_de_la_filial");
                        }
                        if ($myHeader->opcion_extracto <> 3){
                            throw new Exception ("el_extracto esperado_es_3");
                        }
                        
                        break;

                    case "1":   // registro RO
                        $myRegistroRO = new Vvan_cielo_ro($conexion);
                        if (!$myRegistroRO->loadFromString($linea)){
                            throw new Exception ("no_se_ha_podido_procesar_el_registro_RO");
                        } else {
                            $arrLineasRO[] = $myRegistroRO;
                        }                    
                        break;

                    case "2":   // registro CV
                        $myRegistroCV = new Vvan_cielo_cv($conexion);
                        if (!$myRegistroCV->loadFromString($linea)){
                            throw new Exception ("no_se_ha_podido_procesar_el_registro_CV");
                        } else {
                            $arrLineasCV[] = $myRegistroCV;
                        }
                        break;

                    case "9": // trailer
                        $myTrailer = new Vvan_cielo_trailer($conexion);
                        if (!$myTrailer->loadFromString($linea)){
                            throw new Exception ("no_se_ha_podido_procesar_el_trailer");
                        }
                        break;

                    default:
                        throw new Exception ("el_archivo_posee_tipos_de_registros_no_reconocidos");
                        break;
                }                       
            }
        }
        if (!isset($myHeader)){
            throw new Exception ("se_esperaba_un_registro_header_de_archivo");
        }
        if (!isset($myTrailer)){
            throw new Exception ("se_esperaba_un_registro_trailer_de_archivo");
        }
//        if (count($arrLineasCV) == 0 && count($arrLineasRO) == 0){
//            throw new Exception ("no_se_ha_localizado_ningun_registro_CV_o_RO_en_el_archivo");
//        } 
        if ($myTrailer->total_registros <> count($arrLineasCV) + count($arrLineasRO)){
            throw new Exception ("cantidad_de_registros_informados_en_el_trailer_difiere_a_los_encontrados_en_el_archivo");
        }
        $conexion->trans_begin();
        $myHeader->guardarVan_cielo_header();
        $myTrailer->guardarVan_cielo_trailer();
        
        foreach ($arrLineasCV as $myRegistroCV){
            $myRegistroCV->cod_header = $myHeader->getCodigo();
            $myRegistroCV->cod_trailer = $myTrailer->getCodigo();
            $myRegistroCV->guardarVan_cielo_cv();
        }
        
        foreach ($arrLineasRO as $myRegistroRO){
            $myRegistroRO->cod_header = $myHeader->getCodigo();
            $myRegistroRO->cod_trailer = $myTrailer->getCodigo();
            $myRegistroRO->guardarVan_cielo_ro();            
        }
        
        if ($conexion->trans_status()){
            $conexion->trans_commit();
            return true;
        } else {
            $conexion->trans_rollback();
            return false;
        }
    }
    
    static function conciliar(CI_DB_mysqli_driver $conexion, $nsu, $codigoAutorizacion, $terminal, $fechaOperacion){
        $arrResp = array();
        $conexion->select("tarjetas.van_cielo_cv.valor_compra");
        $conexion->select("tarjetas.van_cielo_cv.fecha_venta");
        $conexion->select("tarjetas.van_cielo_cv.total_parcelas");
        $conexion->select("tarjetas.van_cielo_cv.valor_total_venta");
        $conexion->from("tarjetas.van_cielo_cv");
        $conexion->join("tarjetas.van_cielo_header", "tarjetas.van_cielo_header.codigo = tarjetas.van_cielo_cv.cod_header");
        $conexion->where("tarjetas.van_cielo_cv.fecha_venta", $fechaOperacion);
        $conexion->where("tarjetas.van_cielo_cv.codigo_autorizacion", $codigoAutorizacion);
        $conexion->where("tarjetas.van_cielo_cv.nsu_doc", $nsu);
        $conexion->where("tarjetas.van_cielo_cv.numero_logico_terminal", $terminal);
        $query = $conexion->get();
        $arrTemp = $query->result_array();
        if (count($arrTemp) > 0){
            if ($arrTemp[0]['total_parcelas'] == 0){
                $arrResp['valor'] = $arrTemp[0]['valor_compra'];            
            } else {
                $arrResp['valor'] = $arrTemp[0]['valor_total_venta'];
            }
            $arrResp['fecha'] = $arrTemp[0]['fecha_venta'];
        } else {
            $arrResp['valor'] = '0';
            $arrResp['fecha'] = null;
        }
        return $arrResp;
    }
}
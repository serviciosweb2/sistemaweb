<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
class Model_consultasweb extends CI_Model{
    var $codigo_filial = 0;

    public function __construct($arg) {
        parent::__construct();
        $this->codigo_filial = $arg["codigo_filial"];
    }

    public function listarMailsConsultas($idfilial, $tipoConsulta = null, $arrFiltros = null, array $arrCondiciones = null, $order = null){
        $conexion = $this->load->database($this->codigo_filial,true);
        $wherein = null;
        $arrLimit = null;
        $having = null;
        
        if ($arrFiltros != null){
            if (isset($arrFiltros['sSearch']) && $arrFiltros["sSearch"] != "") {
                if ($arrCondiciones == null)
                    $arrCondiciones = array();
                $arrCondiciones["mails_consultas.mails_consultas.nombre"] = $arrFiltros["sSearch"];
            }

            if (isset($arrFiltros['Estado']) && $arrFiltros["Estado"] != "-1") {
                if ($arrCondiciones == null)
                    $arrCondiciones = array();

                switch ($arrFiltros['Estado']) {
                    case 0:
                        //No leido
                        $arrCondiciones["mails_consultas.mails_consultas.notificar"] = "1";
                        break;
                    case 1:
                        //Leido
                        if ($having == null)
                            $having = array();
                        $having['cantidad_respuestas ='] = "0";
                        $arrCondiciones["mails_consultas.mails_consultas.notificar"] = "0";
                        break;
                    case 2:
                        //No Respondidos
                        if ($having == null)
                            $having = array();
                        $having['cantidad_respuestas ='] = "0";
                        break;
                    case 3:
                        //Respondidos
                        if ($having == null)
                            $having = array();
                        $having['cantidad_respuestas !='] = "0";
                        break;
                    default:
                }
            }

            if (isset($arrFiltros['Curso']) && $arrFiltros["Curso"] != "-1") {
                if ($arrCondiciones == null)
                    $arrCondiciones = array();
                $arrCondiciones["mails_consultas.mails_consultas.cod_curso_asunto"] = $arrFiltros["Curso"];
            }

            if (isset($arrFiltros['FechaDesde']) && $arrFiltros["FechaDesde"] != "") {
                if ($having == null)
                    $having = array();
                $having["DATE(fechahora) >="] = $arrFiltros["FechaDesde"];
                $having['cantidad_respuestas !='] = "0";

            }
            if (isset($arrFiltros['FechaHasta']) && $arrFiltros["FechaHasta"] != "") {
                if ($having == null)
                    $having = array();
                $having["DATE(fechahora) <="] = $arrFiltros["FechaHasta"];
                $having['cantidad_respuestas !='] = "0";
            }

            if (isset($arrFiltros['FechaDesdeConsulta']) && $arrFiltros["FechaDesdeConsulta"] != "") {
                if ($having == null)
                    $having = array();
                $having["DATE(fechahoraconsulta) >="] = $arrFiltros["FechaDesdeConsulta"];

            }
            if (isset($arrFiltros['FechaHastaConsulta']) && $arrFiltros["FechaHastaConsulta"] != "") {
                if ($having == null)
                    $having = array();
                $having["DATE(fechahoraconsulta) <="] = $arrFiltros["FechaHastaConsulta"];
            }
            if($order['field'] == 'fechahora') {
                $having['cantidad_respuestas !='] = "0";
            }


            $arrLimit = array();
            if ($arrFiltros["iDisplayStart"] !== "" && $arrFiltros["iDisplayLength"] !== "") {
                $arrLimit = array(
                    "0" => $arrFiltros["iDisplayStart"],
                    "1" => $arrFiltros["iDisplayLength"]
                );
            }
        }

        if ($tipoConsulta != null){
            switch ($tipoConsulta) {
                case 'inbox':
                    $wherein = array('pendiente','abierta');
                break;
                case 'cerradas':
                    $wherein = array('cerrado','noconcretada');
                    break;
                case 'eliminadas':
                    $wherein = array('eliminada');
                    break;
            }
        }

        $listaConsultas = Vmails_consultas::listarMailsConsultas($conexion, $wherein, $idfilial, $arrLimit, false, $arrCondiciones, $having, $order);
        foreach ($listaConsultas as $key=>$valor){
            if($valor['tipo_asunto'] == 'curso' && empty($listaConsultas[$key]['asunto'])){
                $nombre = "nombre_" . get_idioma();
                $myCurso = new Vcursos($conexion, $valor['cod_curso_asunto']);
                $listaConsultas[$key]['asunto'] = $myCurso->$nombre;
            }
        }
        $cantRegistros = Vmails_consultas::listarMailsConsultas($conexion, $wherein, $idfilial, null, true, $arrCondiciones, $having);
        $retorno = array(
            "iTotalRecords" => $cantRegistros,
            "aaData" => $listaConsultas
        );  
        return $retorno;
    }

    public function cambiarEstadoAsunto($asuntosCodigos,$estado){
        $conexion = $this->load->database($this->codigo_filial,true);
        foreach($asuntosCodigos as $asuntoCodigo){
             $objConsultasWeb = new Vmails_consultas($conexion, $asuntoCodigo);
             if ($estado == 'cerrado'){
                 $estado = Vmails_consultas::getEstadoConcretado();
             }
             $objConsultasWeb->estado = $estado;
             $retorno = $objConsultasWeb->guardarMails_consultas();
            }

        return class_general::_generarRespuestaModelo($conexion, $retorno);
    }

    public function destacarAsunto($destacar, $id_asunto){
        $conexion = $this->load->database($this->codigo_filial,true);
        $objConsultasweb = new Vmails_consultas($conexion, $id_asunto);
        $objConsultasweb->destacar = $destacar == 1 ? 1 : 0;
        $retorno = $objConsultasweb->guardarMails_consultas();
        return class_general::_generarRespuestaModelo($conexion, $retorno);
    }
    /**
     * retorna un array con el seguimiento de consultas y respuestas para un codigo de mails_consultas en particular
     *
     * @param int $codMailsConsulta
     * @return array:
     */
    public function listar_seguimiento_consulta($codMailsConsulta){
        $conexion = $this->load->database($this->codigo_filial,true);
        $myConsulta = new Vmails_consultas($conexion, $codMailsConsulta);
        $arrRespuestas = $myConsulta->getRespuestas();
        $arrResp = array();
        if (count($arrRespuestas) > 0){
            foreach ($arrRespuestas as $respuesta){                
                $date_time = new DateTime($respuesta["fecha_hora"]);
                $date_time->sub(new DateInterval('PT3H'));
                $respuesta["fecha_hora"] = $date_time->format('Y-m-d H:i:s');
                $codigo = $respuesta['cod_consulta'];
                $asunto = $respuesta['asunto'];
                $arrResp['data'][] = $respuesta;
            }
            $arrResp['codigo'] = $codigo;
            $arrResp['asunto'] = $asunto;
            
            if(!empty($myConsulta->como_nos_conocio_codigo)) {
                $cnc = new Vcomo_nos_conocio($conexion, $myConsulta->como_nos_conocio_codigo);
                $descricion = 'descripcion_'.get_idioma();
                $arrResp['como_nos_conocio'] = $cnc->$descricion;
            }
            else {
                $arrResp['como_nos_conocio'] = lang('no_definido');
            }
        } else {
            $arrResp['error'] = "error"; // ver como informar el error
        }
        return $arrResp;
    }

    /**
     * Marca una consulta como leida
     *
     * @param int $codMailsConsulta
     * @param boolean $marcarRespuestas
     * @return type
     */
    public function marcar_leida($codMailsConsulta, $marcarRespuestas = false){
        $conexion = $this->load->database($this->codigo_filial,true);
        $myConsulta = new Vmails_consultas($conexion, $codMailsConsulta);
        return $myConsulta->marcarLeida($marcarRespuestas);
    }

    /**
     * guarda una nueva consulta
     *
     * @param int $curso
     * @param string $nombreApellido
     * @param string $telefono
     * @param string $email
     * @param string $consulta
     * @param string $facebook_lead_id se la consulta viene de facebook hay que agregar su id
     * @return boolean
     */

    function guardarNuevaConsulta($curso, $nombreApellido, $telefono, $email, $consulta, $asunto = null, $facebook_lead_id = null, $como_nos_conocio_codigo = null){
        $conexion = $this->load->database($this->codigo_filial,true);
        $myConsulta = new Vmails_consultas($conexion);
        $dateTime = date("Y-m-d H:i:s");
        if($asunto == null) {
            $myCurso = new Vcursos($conexion, $curso);
            $nombreCurso = "nombre_".get_idioma();
            $myConsulta->asunto = $myCurso->$nombreCurso;
        }
        else {
            $myConsulta->asunto = $asunto;
        }
        $myConsulta->cod_curso_asunto = $curso;
        $myConsulta->cod_filial = $this->codigo_filial;
        $myConsulta->destacar = 0;
        $myConsulta->estado = Vmails_consultas::getEstadoPendiente();
        $myConsulta->fechahora = $dateTime;
        $myConsulta->generado_por_filial = 1;
        $myConsulta->mail = $email;
        $myConsulta->nombre = $nombreApellido;
        $myConsulta->notificar = 1;
        $myConsulta->respuesta_automatica_enviada = 1;
        $myConsulta->telefono = $telefono;
        $myConsulta->tipo_asunto = "curso";
        $myConsulta->como_nos_conocio_codigo = $como_nos_conocio_codigo;
        $myConsulta->id_facebook_lead = $facebook_lead_id;
        $conexion->trans_begin();
        $myConsulta->guardarMails_consultas();
        $myRespuesta = new Vmails_respuesta_consultas($conexion);
        $myRespuesta->cod_consulta = $myConsulta->getCodigo();
        $myRespuesta->emisor = 0;
        $myRespuesta->estado = "no_enviar";
        $myRespuesta->fecha_hora = $dateTime;
        $myRespuesta->html_respuesta = $consulta;
        $myRespuesta->guardarMails_respuesta_consultas();
        if (trim($email) <> ''){
            $condiciones = array("email" => $email);
            $cantidad = Vaspirantes::listarAspirantes($conexion, $condiciones, null, null, null, true);
            if ($cantidad == 0){
                $temp = $nombreApellido;
                $arrTemp = explode(" ", $temp);
                if (count($arrTemp) == 1){
                    $arrTemp = explode(",", $temp);
                }
                if (count($arrTemp) > 1){
                   $nombre = $arrTemp[0];
                   $apellido = $arrTemp[1];
                } else {
                    $nombre = $nombreApellido;
                    $apellido = ' ';
                }
                $myFilial = new Vfiliales($conexion, $this->codigo_filial);
                $tipoDocumento = Vpaises::getDocumentoDefaultPais($myFilial->pais);
                $myAspirante = new Vaspirantes($conexion);
                $myAspirante->apellido = $apellido;
                $myAspirante->calle = '';
                $myAspirante->calle_numero = 0;
                $myAspirante->comonosconocio = $como_nos_conocio_codigo;
                $myAspirante->documento = ' ';
                $myAspirante->email = $email;
                $myAspirante->email_enviado = 1;
                $myAspirante->fechaalta = $dateTime;
                $myAspirante->nombre = $nombre;
                $myAspirante->tipo = $tipoDocumento;
                $myAspirante->tipo_contacto = 'EMAIL';
                $myAspirante->usuario_creador = 868;
                $myAspirante->guardarAspirantes();
                if (trim($telefono) <> ''){
                    $myTelefono = new Vtelefonos($conexion);
                    $myTelefono->baja = 0;
                    $myTelefono->cod_area = 0;
                    $myTelefono->numero = $telefono;
                    $myTelefono->tipo_telefono = 'fijo';
                    $myTelefono->guardarTelefonos();
                    $myAspirante->setTelefonosAspirante($myTelefono->getCodigo(), 1);
                }
                $myAspirante->setCursosDeInteres(array($curso), array(4), array(0), array('normal'));                
            }
        }
        
        if ($conexion->trans_status()){
            $conexion->trans_commit();
            return true;
        } else {
            $conexion->trans_rollback();
            return false;
        }
    }

    /**
     * Retorna los templates disponibles para responder mails_consultas, agregando la cantidad de veces utilizados por la filial
     *
     * @param string $nombreLike
     * @return array
     */
    public function listarTemplatesCantidades($nombreLike = null, $cursoActivo = null){
        $conexion = $this->load->database($this->codigo_filial,true);
        //-mmori- modificaciones en respuestas a consultas web. Se cambia el lugar de donde recupera el nombre del template
        $arrCondicionesLike = null;
        if ($nombreLike != null){
            $arrCondicionesLike = array();
            /*$arrCondicionesLike['nombre_es'] = $nombreLike;
            $arrCondicionesLike['nombre_pt'] = $nombreLike;
            $arrCondicionesLike['nombre_in'] = $nombreLike;
            $arrCondicionesLike['nombre'] = "$nombreLike";*/
            $arrCondicionesLike['nombre_mostrar'] = "$nombreLike";
        }
        return Vmails_templates::getTemplatesCantidades($conexion, $this->codigo_filial, "cantidad DESC, orden_prioridad ASC", $arrCondicionesLike, $cursoActivo);
    }

    public function getHTMLTemplates($arrTemplates, $codConsulta, $modoInput = true){
        $conexion = $this->load->database($this->codigo_filial,true);
        $html = '';
        $html = Vmails_templates::armarTemplates($conexion, $arrTemplates, $this->codigo_filial, $codConsulta, $modoInput);
        return $html;
    }

    /**
     * guarda los valores utilizados por la filial los templates seleccionados
     *
     * @param array $arrInputsTipoValues    array en formato (tipoInput => array(template => nro_template, numero_campo => nro_campo, value => valor_utilizado))
     * @return boolean
     */
    public function guardar_valores_por_defecto(array $arrInputsTipoValues){
        $conexion = $this->load->database($this->codigo_filial,true);
        $conexion->trans_begin();

        foreach ($arrInputsTipoValues as $tipoInput => $arrValues){
            foreach ($arrValues as $values){
                $myDefaultValues = new Vmails_consultas_default_values($conexion, $values['template'], $this->codigo_filial, $values['numero_campo'], $tipoInput);
                $myDefaultValues->valor_campo = htmlentities($values['value']);
                $myDefaultValues->guardar();
            }
        }
        if ($conexion->trans_status()){
            $conexion->trans_commit();
            return true;
        } else {
            $conexion->trans_rollback();
            return false;
        }
    }

    /**
     * guardar una respuesta a mails_consultas a partir de los templates utilizados
     *
     * @param array $arrTemplates   array en formato [] = idTemplate;
     * @param int $codConsulta      el codigo de mails_consulta que se está respondiendo
     * @return boolean
     */
    public function guardar_respuesta_template($arrTemplates, $codConsulta, $codigoUsuario){
        $conexion = $this->load->database($this->codigo_filial,true);
        $conexion->trans_begin();
        $html = Vmails_templates::armarTemplates($conexion, $arrTemplates, $this->codigo_filial, $codConsulta, false);
        $myRespuesta = new Vmails_respuesta_consultas($conexion);
        $myRespuesta->cod_consulta = $codConsulta;
        $myRespuesta->emisor = 1;
        $myRespuesta->estado = "pendiente";
        $myRespuesta->fecha_hora = date("Y-m-d H:i:s");
        $myRespuesta->html_respuesta = html_entity_decode($html);
        $myRespuesta->vista = 1;
        $myRespuesta->id_usuario = $codigoUsuario;
        $myRespuesta->guardarMails_respuesta_consultas();
        $myConsultaWeb = new Vmails_consultas($conexion, $codConsulta);
        $myConsultaWeb->estado = 'abierta';
        $myConsultaWeb->guardarMails_consultas();
        foreach ($arrTemplates as $template){
            $myHistorico = new Vmails_respuesta_consultas_historico($conexion, $this->codigo_filial, $template);
            $myHistorico->sumarHistorico();
        }
        if ($conexion->trans_status()){
            $conexion->trans_commit();
            return true;
        } else {
            $conexion->trans_rollback();
            return false;
        }

    }

    public function getUltimosMailsConsultas($filial,$codigo){
        $conexion = $this->load->database($this->codigo_filial,true);
       $ultimosMailConsultas = '';

       if($codigo == ''){
             $condiciones = array(
                "mails_consultas.mails_consultas.cod_filial"=>$filial
              );

            $orden = array(array(
                "campo"=> 'mails_consultas.mails_consultas.fechahora',
                "orden"=> "desc"
            ));
        $limit = array(0,10);
        $ultimosMailConsultas = Vmails_consultas::listarMails_consultas($conexion, $condiciones, $limit, $orden);

        }else{
            $condiciones = array(
               "mails_consultas.mails_consultas.cod_filial"=>$filial,
                "mails_consultas.mails_consultas.codigo >"=>$codigo
             );

            $orden = array(array(
                "campo"=> 'mails_consultas.mails_consultas.fechahora',
                "orden"=> "asc"
             ));
             $ultimosMailConsultas = Vmails_consultas::listarMails_consultas($conexion, $condiciones, null, $orden);

        }

        foreach($ultimosMailConsultas as$key=>$mail){
             $ultimosMailConsultas[$key]['nombreFormateado'] = strtolower ($mail['nombre']);
            $ultimosMailConsultas[$key]['nombreFormateado'] = ucwords($ultimosMailConsultas[$key]['nombreFormateado']);
            if($mail['tipo_asunto'] == 'curso'){
                $nombre = "nombre_" . get_idioma();
                $myCurso = new Vcursos($conexion, $mail['cod_curso_asunto']);
                $ultimosMailConsultas[$key]['asunto'] = $myCurso->$nombre;
            }
        }

        return $ultimosMailConsultas;
    }

    /* SE omenta la siguiente function por cambio en la modalidad de sincronizacion de consultas web (se quita el crons
        y se utiliza Web services por cambio en servidor y conexion a base de datos BULI
        Se busca el uso de esta function y solo se encuentra en la function del cron quitada
)  */
//    public function sincronizarMailsConsultas(){
//        $conexion = $this->load->database("default", true);
//        $conexionBuli = $this->load->database("buli", true);
//        $arrFiliales = Vfiliales::listarFiliales($conexion, array("version_sistema" => 2));
//        foreach ($arrFiliales as $filial){
//            $conexion->trans_begin();
//            $codFilial = $filial['codigo'];
//            $maxCodigo = Vmails_consultas::getMaxCodigo($conexion, $codFilial);
//            $condiciones = array("codfilial" => $codFilial, "codigo >" => $maxCodigo);
//            $arrConsultas = Vmails_consultas::listarMails_consultas_sincronizar($conexionBuli, $condiciones);
//            foreach ($arrConsultas as $consulta){
//                $myMailsConsultas = new Vmails_consultas($conexion, null);
//                $myMailsConsultas->asunto = $consulta['asunto'];
//                $myMailsConsultas->cod_curso_asunto = $consulta['id_curso'] == '' ? $consulta['id_asunto'] : $consulta['id_curso'];
//                $myMailsConsultas->cod_filial = $codFilial;
//                $myMailsConsultas->destacar = 0;
//                $myMailsConsultas->estado = $consulta['estado'];
//                $myMailsConsultas->fechahora = $consulta['fechahora'];
//                $myMailsConsultas->generado_por_filial = $consulta['generado_por_filial'];
//                $myMailsConsultas->mail = $consulta['mail'];
//                $myMailsConsultas->nombre = $consulta['nombre'];
//                $myMailsConsultas->notificar = $consulta['notificar'];
//                $myMailsConsultas->respuesta_automatica_enviada = $consulta['respuesta_automatica_enviada'];
//                $myMailsConsultas->telefono = $consulta['telefono'];
//                $myMailsConsultas->tipo_asunto = $consulta['id_curso'] == '' ? "asunto" : "curso";
//                $myMailsConsultas->guardadoForzado($consulta['codigo']);
//                echo "se guardar el codigo {$consulta['codigo']}<br>";
//                $myMailsRespuesta = new Vmails_respuesta_consultas($conexion, null);
//                $myMailsRespuesta->cod_consulta = $consulta['codigo'];
//                $myMailsRespuesta->emisor = 0;
//                $myMailsRespuesta->estado = "no_enviar";
//                $myMailsRespuesta->fecha_hora = $consulta['fechahora'];
//                $myMailsRespuesta->html_respuesta = $consulta['mensaje'];
//                $myMailsRespuesta->vista = $consulta['notificar'] == 1 ? 0 : 1;
//                $myMailsRespuesta->guardarMails_respuesta_consultas();
//            }
//            if ($conexion->trans_status()){ echo "commit<br>";
//                $conexion->trans_commit();
//            } else { echo "rollback<br>";
//                $conexion->trans_rollback();
//            }
//        }
//    }

    function envirRespuestaConsultasCrons(){
        $conexion = $this->load->database("default", true);
        $condiciones = array("estado" => "pendiente", "emisor" => "1");
        $arrRespuestaConsultas = Vmails_respuesta_consultas::listarMails_respuesta_consultas($conexion, $condiciones);
        foreach ($arrRespuestaConsultas as $respuestaConsulta){
            $codigo = $respuestaConsulta['cod_consulta'];
            $myMailsConsultas = new Vmails_consultas($conexion, $codigo);
            $myFilial = new Vfiliales($conexion, $myMailsConsultas->cod_filial);
            $email = $myMailsConsultas->mail;
            $asunto = utf8_decode($myMailsConsultas->asunto);
            $html = $respuestaConsulta['html_respuesta'];
            $config= array();
            $config['charset'] = 'iso-8859-1';
            $this->email->initialize($config);
            $this->email->from($myFilial->email, 'IGA '.$myFilial->nombre);
            $this->email->to($email);
            $this->email->subject("RE: $asunto");
            $this->email->message_id("<iga.".$respuestaConsulta['cod_consulta']."@www.iga-la.net>");
            $this->email->reply_to("consultas-la@iga-la.net");
            $this->email->message(utf8_decode($html));
            $respuesta = $this->email->send();
            if ($respuesta){
                $codigoRespuesta = $respuestaConsulta['codigo'];
                $myMailsRespuesta = new Vmails_respuesta_consultas($conexion, $codigoRespuesta);
                $myMailsRespuesta->marcarEnviada();
            }
        }
    }

    function getMaxCodigo($codFilial){
        $conexion = $this->load->database("default", true);
        $maxCodigo = Vmails_consultas::getMaxCodigo($conexion, $codFilial);
        $arrResp = array("cod_filial" => $codFilial, "max_codigo" => $maxCodigo);
        return $arrResp;
    }

    public function listarDataTables($idFilial = null, $arrLimit = null, $arrSort = null, $search = null, $searchFields = null,
            $fechaDesde = null, $fechaHasta = null, $estado = null, $codConsulta = null){
        $conexion = $this->load->database("mails_consultas", true);
        $cantRegistros = Vmails_consultas::listarDataTables($conexion, $arrLimit, $arrSort, true, $search, $searchFields, $idFilial, $fechaDesde, $fechaHasta, $estado, $codConsulta);
        $registros = Vmails_consultas::listarDataTables($conexion, $arrLimit, $arrSort, false, $search, $searchFields, $idFilial, $fechaDesde, $fechaHasta, $estado, $codConsulta);
        $arrResp = array();
        $arrResp['total_rows'] = $cantRegistros;
        $arrResp['rows'] = $registros;
        return $arrResp;
    }

    public function listarConsultasWebWS($fechaDesde, $fechaHasta, $cursos,$cursosCortos, $tipo=null){
        $conexion = $this->load->database("mails_consultas", true); 
        $esacosa = Vmails_consultas::listarConsultasWebWS($conexion, 
            $fechaDesde, 
            $fechaHasta, 
            $cursos, 
            $cursosCortos,
            $tipo);
        $cantidades = $esacosa['mails'];
        $cursos = $esacosa['cursos'];
        $reporte = array();
        $categorias = array();
        $mapaCategorias = array();
        //Este lastId terminó quedando re pintado.
        $lastId = 0;
        foreach($cursos as $curso){
            if(!isset($mapaCategorias[$curso['codigo']])){
                $mapaCategorias[$curso['codigo']] = $lastId;
                $categorias[] = array(
                    'id' => $curso['codigo'],
                    'nombre' => $curso['nombre_es'],
                    'nombre_es' => $curso['nombre_es'],
                    'nombre_in' => $curso['nombre_in'],
                    'nombre_pt' => $curso['nombre_pt'],
                    'activo' => 1,
                    'cantidad' => 6,
                    'cantidad_subcategorias' => 2
                );
                $lastId++;
            }
        }



        if($cursosCortos != null && count($cursosCortos) > 0 ){
            $mapaCategorias[300] = $lastId;
            $categorias[] = array(
                    'id' => 300,
                    'nombre' => 'Cursos cortos',
                    'nombre_es' => 'Cursos cortos',
                    'nombre_in' => 'Cursos cortos',
                    'nombre_pt' => 'Cursos cortos',
                    'activo' => 1,
                    'cantidad' => 6,
                    'cantidad_subcategorias' => 2
            );
            $lastId++;
        }

        if($cursos != null && in_array(301, $cursos)){
            $mapaCategorias[301] = $lastId;
            $categorias[] = array(
                    'id' => 301,
                    'nombre' => 'Atencion al alumno',
                    'nombre_es' => 'Atencion al alumno',
                    'nombre_in' => 'Student Care',
                    'nombre_pt' => 'Eu nao falo portugues?',
                    'activo' => 1,
                    'cantidad' => 6,
                    'cantidad_subcategorias' => 2
            );
            $lastId++;
        }



        foreach($cantidades as $mails){
            if(!isset($mapaCategorias[$mails['codigo_curso']])){
                $mapaCategorias[$mails['codigo_curso']] = $lastId;
                $categorias[] = array(
                    'id' => $mails['codigo_curso'],
                    'nombre' => $mails['nombre_es'],
                    'nombre_es' => $mails['nombre_es'],
                    'nombre_in' => $mails['nombre_in'],
                    'nombre_pt' => $mails['nombre_pt'],
                    'pais' => $mails['pais_filial'],
                    'codigo_curso' => $mails['codigo_curso'],
                    'activo' => 1,
                    'cantidad' => 6,
                    'cantidad_subcategorias' => 2
                );
                $lastId++;
            }
            if($tipo != "por_pais"){
                if(!isset($reporte[$mails['codigo_filial']])){
                    $reporte[$mails['codigo_filial']] = array(
                        'nombre' => $mails['nombre_filial'],
                        'categorias' => array()
                    );
                }
                $catId = $mapaCategorias[$mails['codigo_curso']];
                $reporte[$mails['codigo_filial']]['categorias'][$mails['codigo_curso']] = $mails['cantidad'];
            }else{
                if(!isset($reporte[$mails['pais_filial']])){
                    $reporte[$mails['pais_filial']] = array();
                }
                $reportePais = $reporte[$mails['pais_filial']];
                if(!isset($reportePais[$mails['codigo_filial']])){
                    $reportePais[$mails['codigo_filial']] = array(
                        'nombre' => $mails['nombre_filial'],
                        'categorias' => array()
                    );
                }
                $catId = $mapaCategorias[$mails['codigo_curso']];
                $reportePais[$mails['codigo_filial']]['categorias'][$mails['codigo_curso']] = $mails['cantidad'];
                $reporte[$mails['pais_filial']] = $reportePais;
            }
        }
        $respuesta = array(
            'reporte' => $reporte,
            'categorias' => $categorias
        );
        return $respuesta;
    }

}

<?php

include APPPATH . 'third_party/imap.functions.php';
include APPPATH . 'third_party/encoding.php';
use \ForceUTF8\Encoding;

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Model_inboxexterna extends CI_Model{
    var $codigo_filial = 0;

    public function __construct($arg) {
        parent::__construct();
        $this->codigo_filial = $arg["codigo_filial"];
    }

    public function getEmailData($uid_email) {
        $conexion = $this->load->database($this->codigo_filial, true);

        $conexion->select("*");
        $conexion->from("mails_inbox_externa");
        $conexion->where("email_uid", $uid_email);
        $conexion->limit("1");

        $email_data = $conexion->get();

        if ($email_data) {
            $email_data = $email_data->result_array();
        }
        else {
            return false;
        }

        return $email_data[0];
    }

    public function listarMailsInboxExterna($idfilial, $arrFiltros = null, array $arrCondiciones = null, $soloContar = false) {
        $conexion = $this->load->database($this->codigo_filial, true);
        $wherein = null;
        $arrLimit = null;

        if ($arrFiltros != null){
            if (isset($arrFiltros['sSearch']) && $arrFiltros["sSearch"] != "") {
                if ($arrCondiciones == null)
                    $arrCondiciones = array();

                //$arrCondiciones["mails_consultas.mails_consultas.nombre"] = $arrFiltros["sSearch"];
                $arrCondiciones["mails_inbox_externa.from_name"] = $arrFiltros["sSearch"];
            }
            $arrLimit = array();
            if ($arrFiltros["iDisplayStart"] !== "" && $arrFiltros["iDisplayLength"] !== "") {
                $arrLimit = array(
                    "0" => $arrFiltros["iDisplayStart"],
                    "1" => $arrFiltros["iDisplayLength"]
                );
            }
        }

        $conexion->from("mails_inbox_externa");
        if (count($arrCondiciones) > 0) {
            $conexion->like($arrCondiciones);
        }
        $conexion->order_by('date_time','desc');

        if ($soloContar) {
            $conexion->select("COUNT(*) as emails_count");
            $listaEmails = $conexion->get();

            if ($listaEmails) {
                $row = $listaEmails->row_array();
                return $row['emails_count'];
            }
        }

        $conexion->select("*");

        if ( !is_null($arrLimit) ) {
            $conexion->limit($arrLimit[1], $arrLimit[0]);
        }

        $listaEmails = $conexion->get();

        if ($listaEmails) {
            $listaEmails = $listaEmails->result_array();
        }

        $retorno = array(
            "unreadedCount" => $this->contarMailsNoLeidos($idfilial),
            "iTotalRecords" => $this->contarMailsInboxExterna($idfilial, $arrFiltros, $arrCondiciones),
            "aaData" => $listaEmails
        );

        //die($conexion->last_query());
        return $retorno;
    }

    public function contarMailsInboxExterna($idfilial, $arrFiltros = null, array $arrCondiciones = null) {
        return $this->listarMailsInboxExterna($idfilial, $arrFiltros, $arrCondiciones, true);
    }

    public function contarMailsNoLeidos($idfilial) {
        return $this->contarMailsInboxExterna($idfilial, null, ['readed' => 0], true);
    }

    public function inboxExternaEstaHabilitada() {
        if ( $this->getImapAccountPassword() ) {
            return true;
        }

        return false;
    }

    public function testInboxLoginWithPassword($password) {
        $conexion = $this->load->database($this->codigo_filial, true);

        $accountConnectionData = $this->getAccountConnectionData();

        $imap_account_host = $accountConnectionData['imap_account_host'];
        $imap_account_port = $accountConnectionData['imap_account_port'];
        $imap_account_username = $accountConnectionData['imap_account_username'];

        $imap_stream = @imap_open("{".$imap_account_host.":".$imap_account_port."/novalidate-cert}", $imap_account_username, $password, 0, 0);
        imap_errors(); // llamo a esta funcion para hacer un flush de los errores pq sino los muestra

        // No se pudo conectar a la cuenta IMAP
        if (!$imap_stream) {
            return false;
        }

        imap_close($imap_stream);

        return true;
    }

    public function saveInboxExternaPassword($new_password) {
        $this->load->model("Model_configuraciones", "", false, array("codigo_filial" => $this->codigo_filial));
        $codigo_usuario = $this->session->userdata("codigo_usuario");

        $save_result = $this->Model_configuraciones->guardarConfiguracion($codigo_usuario, 'passwordInboxExterna', $new_password, $this->codigo_filial, false);
        if ( is_array($save_result) && isset($save_result['codigo']) && $save_result['codigo'] === 1 ) {
            return true;
        }

        return false;
    }

    /**
     * Marcar los emails con ids $array_uids como leidos.
     */
    public function marcarLeidos($array_uids) {
        $conexion = $this->load->database($this->codigo_filial, true);

        if (!is_array($array_uids)) {
            return true;
        }

        foreach ($array_uids as $current_uid) {
            $conexion->or_where('email_uid', $current_uid);
        }

        return $conexion->update('mails_inbox_externa', ['readed' => 1]);
    }

    /*
     * Obtiene el nombre de usuario para la cuenta IMAP de la filial
     */
    public function getImapAccountUsername() {
        $this->load->model("Model_filiales", "", false, $this->codigo_filial);
        $filial_data = $this->Model_filiales->getFilial();

        if (isset($filial_data["email"]) && $filial_data["email"] != "") {
            $email_parts = explode("@", $filial_data["email"]);

            if (isset($email_parts[0]) && $email_parts[0] != "") {
                return $email_parts[0];
            }
        }

        return false;
    }

    /*
     * Obtiene el password de usuario para la cuenta IMAP de la filial
     */
    public function getImapAccountPassword() {
        /*
        $this->load->model("Model_configuraciones", "", false, array("codigo_filial" => $this->codigo_filial));
        $imap_account_password = $this->Model_configuraciones->getValorConfiguracion(null, 'passwordInboxExterna');

        $this->Model_configuraciones->cod_filial = $this->codigo_filial;

        //echo "\n ".$this->codigo_filial.", getImapAccountPassword: --"; var_dump($imap_account_password); echo "--\n\n";
        echo "\n ".$this->codigo_filial.", getImapAccountPassword: --" . $imap_account_password . "--\n\n";

        if ( !is_null($imap_account_password) ) {
            return $imap_account_password;
        }

        return false;
        */


        $this->load->model("Model_configuraciones", "", false, array("codigo_filial" => $this->codigo_filial));
        $model_configuraciones = new Model_configuraciones(array("codigo_filial" => $this->codigo_filial));

        $imap_account_password = $model_configuraciones->getValorConfiguracion(null, 'passwordInboxExterna');

        //$this->Model_configuraciones->cod_filial = $this->codigo_filial;

        //echo "\n ".$this->codigo_filial.", getImapAccountPassword: --" . $imap_account_password . "--\n\n";

        if ( !is_null($imap_account_password) ) {
            return $imap_account_password;
        }

        return false;
    }

    /*
     * Obtiene los datos de conexion para la cuenta IMAP de la filial
     */
    public function getAccountConnectionData() {
        $return_array = array(
            'imap_account_host' => 'mail.iga-la.net',
            'imap_account_port' => '143'
        );

        $return_array['imap_account_username'] = $this->getImapAccountUsername();
        $return_array['imap_account_password'] = $this->getImapAccountPassword();

        if (!$return_array['imap_account_username']) {
            return false;
        }

        return $return_array;

        /*
        return [
            "imap_account_host" => 'mail.iga-la.net',
            "imap_account_port" => '143',
            "imap_account_username" => 'sistemas2',
            "imap_account_password" => 'oBjmV4t5'
        ];
        */
    }

    /*
     * Obtiene el UID del ultimo email sincronizado con la DB
     */
    public function getUltimoUidProcesado() {
        $conexion = $this->load->database($this->codigo_filial, true);

        $conexion->select('email_uid');
        $conexion->from('mails_inbox_externa');
        $conexion->order_by('email_uid', 'DESC');
        $conexion->limit('1');

        $result = $conexion->get();

        if ($result) {
            if ($result->num_rows() > 0) {
                return $result->row(0)->email_uid;
            }

            return 0;
        }

        return false;
    }

    public function setCodigoFilial($new_codigo_filial) {
        $this->codigo_filial = $new_codigo_filial;
    }

    public function sincronizarInboxConDB($display_output = false) {
        @ini_set('memory_limit','512M');

        $conexion = $this->load->database($this->codigo_filial, true);

        if ($display_output) {
            echo "<pre>\n\n<hr>\n\nSincronizando filial " . $this->codigo_filial . "\n\n";
        }

        if ( !$this->inboxExternaEstaHabilitada() ) {
            if ($display_output) {
                echo " - Inhabilitada!\n\n";
            }

            imap_errors(); // flush de errores
            return false;
        }

        $accountConnectionData = $this->getAccountConnectionData();

        $imap_account_host = $accountConnectionData['imap_account_host'];
        $imap_account_port = $accountConnectionData['imap_account_port'];
        $imap_account_username = $accountConnectionData['imap_account_username'];
        $imap_account_password = $accountConnectionData['imap_account_password'];

        //print_r($accountConnectionData);

        $imap_stream = @imap_open("{".$imap_account_host.":".$imap_account_port."/novalidate-cert}", $imap_account_username, $imap_account_password);
        imap_errors(); // flush de los errores para que no los muestre

        // No se pudo conectar a la cuenta IMAP
        if (!$imap_stream) {
            return false;
        }

        $months = array('Jan' => '01', 'Feb' => '02', 'Mar' => '03', 'Apr' => '04', 'May' => '05', 'Jun' => '06', 'Jul' => '07', 'Aug' => '08', 'Sep' => '09', 'Oct' => '10', 'Nov' => '11', 'Dec' => '12');

        $account_messages_count = imap_num_msg($imap_stream); // cantidad total de mensajes en la cuenta

        //echo "Leyendo ".$account_messages_count." mensajes.\n\n";
        $current_message_index = $account_messages_count;

        // Obtenemos el UID del ultimo email procesado y salimos si hay poblemas
        $limit_uid = $this->getUltimoUidProcesado();
        if ($limit_uid === false) {
            return false;
        }

        $last_email_uid = PHP_INT_MAX;

        $stop_flag = false;
        // Recorremos los mensajes hasta encontrarnos con el ultimo mensaje
        // sincronizado con la DB
        while ($current_message_index > 0 && !$stop_flag) {
            $email_uid = imap_uid($imap_stream, $current_message_index);

            if ($email_uid > $limit_uid) {
                $email_header = imap_headerinfo($imap_stream, $current_message_index);

                $from_name = false;
                $from_account = false;
                $subject = false;
                $date_time = false;
                $body = false;

                if (isset($email_header->subject)) {
                    $subject = $email_header->subject;
                }
                else {
                    if (isset($email_header->Subject)) {
                        $subject = $email_header->Subject;
                    }
                    else
                    {
                        $subject = "";
                    }
                }

                $subject = imap_mime_header_decode($subject);

                if (is_array($subject) && count($subject) == 1 && isset($subject[0]->text)) {
                    $subject = Encoding::toUTF8($subject[0]->text);
                }
                else {
                    $subject = "";
                }

                if (isset($email_header->from) && isset($email_header->from[0])) {
                    if (isset($email_header->from[0]->personal)) {
                        $from_name = imap_mime_header_decode($email_header->from[0]->personal);

                        if (is_array($from_name)) {
                            if (count($from_name) == 1 && isset($from_name[0]->text)) {
                                $from_name = Encoding::toUTF8($from_name[0]->text);
                            }
                            else {
                                if (is_array($from_name[0]) && isset($from_name[0]['text'])) {
                                    $from_name = $from_name[0]['text'];
                                }
                                else {
                                    if (is_object($from_name[0]) && isset($from_name[0]->text)) {
                                        $from_name = $from_name[0]->text;
                                    }
                                }
                            }
                        }
                    }

                    if (isset($email_header->from[0]->mailbox) && isset($email_header->from[0]->host)) {
                        $from_account = $email_header->from[0]->mailbox . "@" . $email_header->from[0]->host;
                    }
                }

                if (isset($email_header->date) && is_string($email_header->date)) {
                    $date_time = explode(', ', $email_header->date);

                    if (count($date_time) > 1) {
                        $date_time = $date_time[1];
                    }
                    else {
                        $date_time = $date_time[0];
                    }

                    $date_time = explode(' ', $date_time);
                    //echo "\n\n"; print_r($date_time);

                    $current_month = '';
                    if ( isset($months[$date_time[1]]) ) {
                        $current_month = $months[$date_time[1]];
                    }
                    else {
                        // Si el mes viene con formato '07'
                        if (strlen($date_time[1]) == 2 && (intval($date_time[1]) >= 1 || intval($date_time[1]) <= 12)) {
                            $current_month = $date_time[1];
                        }
                    }

                    $date_time = $date_time[2] . "-" . $current_month . "-" . substr('0'.intval($date_time[0]), 0, 2) . " " . $date_time[3];
                    //echo "\n".$date_time."\n\n";
                }

                $body = getBody($email_uid, $imap_stream);
                //$body = base64_encode(trim($body));
                $body = Encoding::fixUTF8(Encoding::toUTF8($body));

                /*
                echo "\n\nfrom_name: ";
                print_r($from_name);
                echo "\n";
                */

                if ($display_output) {
                    echo "\n\nUID: " . $email_uid . ": ";
                    echo "\nDate: " . $date_time;
                    echo "\nFrom: " . $from_name . " \"" . $from_account . "\"";
                    echo "\nSubject: " . $subject;
                    echo "\nBody (size): ";
                    echo (strlen($body)/1000) . "kb.";
                }

                $conexion->insert("mails_inbox_externa", array(
                        /*
                        "email_uid" => $email_header,
                        "email_body" => $body,
                        "from_name" => $from_name,
                        "from_account" => $from_account,
                        "subject" => $subject,
                        "date_time" => $date_time,
                        "readed" => 0,
                        "hidden" => 0
                         */
                        "email_uid" => $email_uid,
                        "email_body" => $body,
                        "from_name" => $from_name,
                        "from_account" => $from_account,
                        "subject" => $subject,
                        "date_time" => $date_time,
                        "readed" => 0,
                        "hidden" => 0
                ));
            }
            else {
                $stop_flag = true;
            }

            $last_email_uid = $email_uid;
            $current_message_index--;
        }

        if ($display_output) {
            echo " - Finalizado.";
            echo "</pre>";
        }

        imap_errors(); // flush de errores
        imap_close($imap_stream);

        return true;
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
}

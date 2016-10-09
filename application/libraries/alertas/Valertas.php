<?php
            
/**
 * Class Valertas
 *
 * Class  Valertas maneja todos los aspectos de alumnos
 *
 * @package  SistemaIGA
 * @subpackage Alumnos
 * @author   Ivan berthillod <ivan.sys@gmail.com>
 * @author   Aquiles Gonzalez <sistemas1@iga-la.net>
 * @version  $Revision: 1.1 $
 * @access   private
 */
class Valertas extends Talertas {
    /* CONSTRUCTOR */

    function __construct(CI_DB_mysqli_driver $conexion, $codigo = null) {
        parent::__construct($conexion, $codigo);
    }

    /* PUBLIC FUNCTIONS */

    public function marcarLeida($codigoUsuario) {
        return $this->oConnection->update("alertas_usuarios", array("visto" => 1), array("cod_alerta" => $this->codigo, "cod_usuario" => $codigoUsuario));
    }

    /**
     * Setea una alerta a un usuario en particular
     * 
     * @param int $codUsuario
     */
    public function setUsuario($codUsuario) {
        return $this->oConnection->insert("alertas_usuarios", array("cod_alerta" => $this->codigo, "cod_usuario" => $codUsuario));
    }

    /* STATIC FUNCTIONS */

    /**
     * retorna la cantidad de alertas agrupada por tipo de alerta 
     * 
     * @param CI_DB_mysqli_driver $conexion
     * @param int $codUsuario
     * @return array
     */
    static function getAlertasCantidadesUsuarios(CI_DB_mysqli_driver $conexion, $codUsuario = null, $tipoAlerta = null, $visto = null) {
        $conexion->select("alertas.tipo_alerta");
        $conexion->select("COUNT(alertas.tipo_alerta) AS cantidad");
        $conexion->select("(SELECT COUNT(au1.visto) FROM alertas_usuarios AS au1 JOIN alertas AS a1 ON a1.codigo = au1.cod_alerta WHERE a1.tipo_alerta = alertas.tipo_alerta AND au1.visto = $visto AND au1.cod_usuario = $codUsuario) as visto", false);
        $conexion->from("alertas");
        $conexion->join("alertas_usuarios", "alertas_usuarios.cod_alerta = alertas.codigo");
        if ($codUsuario != null)
            $conexion->where("alertas_usuarios.cod_usuario", $codUsuario);
        if ($tipoAlerta != null)
            $conexion->where("alertas.tipo_alerta", $tipoAlerta);
        if ($visto !== null)
            $conexion->having("visto >", 0);
        $conexion->group_by("alertas.tipo_alerta");
        $query = $conexion->get();
        return $query->result_array();
    }

    /**
     * lista alertas pudiendo filtrarse por usuario
     * 
     * @param CI_DB_mysqli_driver $conexion     objeto de acceso a la base de datos
     * @param int $codUsuario                   el codigo de usuario a utilizar como filtro
     * @param int $visto                        1 = visto, 0 = no visto, null = todos
     * @param string $tipoAlerta                El tipo de alerta como figura en el campo enum
     * @param array $order                      Array de orden en formato array(nombreCampo, metodo)
     * @return array
     */
    static function listarAlertasUsuarios(CI_DB_mysqli_driver $conexion, $codUsuario = null, $visto = null, $tipoAlerta = null, array $order = null, $limitMin = 0, $limitCant = null, $contar = false) {
        $conexion->select("alertas.*");
        $conexion->select("DATE(alertas.fecha_hora) AS fecha", false);
        $conexion->select("TIME(alertas.fecha_hora) AS hora", false);
        $conexion->from("alertas");
        $conexion->join("alertas_usuarios", "alertas_usuarios.cod_alerta = alertas.codigo");
        if ($codUsuario != null)
            $conexion->where("alertas_usuarios.cod_usuario", $codUsuario);
        if ($visto !== null)
            $conexion->where("alertas_usuarios.visto", $visto);
        if ($tipoAlerta != null)
            $conexion->where("alertas.tipo_alerta", $tipoAlerta);
        if ($order != null)
            $conexion->order_by($order[0], $order[1]);
        if ($limitCant !== null) {
            $conexion->limit($limitCant, $limitMin);
        }
        $query = $conexion->get();
        if ($contar) {
            $arrResp = $query->num_rows();
        } else {
            $arrResp = $query->result_array();
        }
        return $arrResp;
    }

    public function setAlertaAlumno($cod_alumno) {
        $arrayComunicadoAlumno = array(
            'cod_alerta' => $this->getCodigo(),
            'cod_alumno' => $cod_alumno,
            'estado' => 'noenviado'
        );
        $this->oConnection->insert('alertas_alumnos', $arrayComunicadoAlumno);
    }

    public function setAlertaAlumnoConfiguracion($arrayAluConfiguracion) {
        $this->oConnection->insert('alerta_alumno_configuracion', $arrayAluConfiguracion);
    }

    static function getComunicadosEmailComisionMateria(CI_DB_mysqli_driver $conexion, $cod_comision, $contar = false, $arrCondiciones = null, $arrLimit = null, $filtro = null) {

        $conexion->select('alertas.*, aac1.`key`, alerta_configuracion.valor AS asunto, aac1.valor AS cod_materia, general.materias.nombre_es, general.materias.nombre_pt, general.materias.nombre_in, aac2.valor as cod_comision');
        $conexion->from('alertas');
        $conexion->join('alerta_alumno_configuracion AS aac1', "aac1.cod_alerta = alertas.codigo AND aac1.`key` = 'cod_materia'", 'left');
        $conexion->join('alerta_alumno_configuracion AS aac2', "aac2.cod_alerta = alertas.codigo AND aac2.`key` = 'cod_comision'");
        $conexion->join('general.materias', 'general.materias.codigo =  aac1.valor', 'left');
        $conexion->join('alerta_configuracion', "alerta_configuracion.cod_alerta = alertas.codigo AND alerta_configuracion.`key` = 'titulo'");
        $conexion->where('alertas.tipo_alerta', 'comunicado_alumnos');
        $conexion->where('aac2.valor', $cod_comision);

        if ($filtro != null) {
            $conexion->where('aac1.valor', $filtro);
        }
        $conexion->group_by('alertas.codigo');
        if ($arrCondiciones != null) {
            $conexion->like($arrCondiciones);
        }
        if ($arrLimit != null) {
            $conexion->limit($arrLimit[1], $arrLimit[0]);
        }
        $conexion->order_by('alertas.codigo', 'desc');
        $query = $conexion->get();

        $arrRespuesta = $query->result_array();
        if ($contar) {
            return count($arrRespuesta);
        } else {
            return $arrRespuesta;
        }
    }

    public function getAlumnosMensajeComision($cod_comision, $cod_materia = null) {
        $this->oConnection->select('alumnos.nombre, alumnos.apellido, alumnos.email');
        $this->oConnection->from('alertas_alumnos');
        $this->oConnection->join('alumnos', 'alumnos.codigo = alertas_alumnos.cod_alumno');
        $this->oConnection->join('alerta_alumno_configuracion as aac1', "aac1.cod_alerta = alertas_alumnos.cod_alerta and aac1.`key` = 'cod_comision'");
        $this->oConnection->join('alerta_alumno_configuracion as aac2', "aac2.cod_alerta = alertas_alumnos.cod_alerta and aac2.`key` = 'cod_materia'", 'left');
        $this->oConnection->where('aac1.valor', $cod_comision);

        if ($cod_materia != null) {
            $this->oConnection->where('aac2.valor', $cod_materia);
        }

        $this->oConnection->group_by('alumnos.codigo');
        $query = $this->oConnection->get();
        return $query->result_array();
    }

    public function setAlertaConfiguracion($key, $valor) {
        $arrayGuardar = array(
            'cod_alerta' => $this->getCodigo(),
            'key' => $key,
            'valor' => $valor
        );
        $this->oConnection->insert('alerta_configuracion', $arrayGuardar);
    }

    static function getAlertasAlumnosNoEnviadas(CI_DB_mysqli_driver $conexion) {
        $conexion->select('*');
        $conexion->from('alertas');
        $conexion->join('alertas_alumnos', 'alertas_alumnos.cod_alerta = alertas.codigo');
        $conexion->where('alertas_alumnos.estado', 'noenviado');
        $conexion->order_by('alertas.codigo', 'asc');
        $query = $conexion->get();
        $arrRespuesta = $query->result_array();
        return $arrRespuesta;
    }

    public function getAlertaConfiguracion() {
        $conexion = $this->oConnection;
        $conexion->select('*');
        $conexion->from('alerta_configuracion');
        $conexion->where('cod_alerta', $this->codigo);
        $query = $conexion->get();
        return $query->result_array();
    }

    public function getAlertaAlumnoConfiguracion($codalumno) {
        $conexion = $this->oConnection;
        $conexion->select('*');
        $conexion->from('alerta_alumno_configuracion');
        $conexion->where('cod_alerta', $this->codigo);
        $conexion->where('cod_alumno', $codalumno);
        $query = $conexion->get();
        return $query->result_array();
    }

    public function setAlertaAlumnoEnviada($codalumno, $comentario = '') {
        $respuesta = $this->oConnection->update('alertas_alumnos', array('estado' => 'enviado'), array("cod_alerta" => $this->codigo, "cod_alumno" => $codalumno));
        $arrDatos = array('cod_alerta' => $this->codigo, 'cod_alumno' => $codalumno, 'estado' => 'enviado', 'fecha_hora' => date('Y-m-d H:i:s'), 'comentario' => $comentario);
        $this->oConnection->insert('alertas_alumnos_historico', $arrDatos);
        return $respuesta;
    }

    static function listarAlertasNoEnviadas(CI_DB_mysqli_driver $conexion, $limiteTeimpo = 4, $contar = false) {
        $conexion->select("alertas.*");
        $conexion->select("alumnos.codigo as cod_alumno");
        $conexion->select("CONCAT(alumnos.apellido, ', ', alumnos.nombre) AS nombre_alumno", false);
        $conexion->from("alertas");
        $conexion->join("alertas_alumnos", "alertas_alumnos.cod_alerta = alertas.codigo");
        $conexion->join("alumnos", "alumnos.codigo = alertas_alumnos.cod_alumno");
        $conexion->where("alertas_alumnos.estado = 'noenviado'");
        $conexion->where("alertas.fecha_hora < DATE_ADD(NOW(),INTERVAL -$limiteTeimpo HOUR)");
        $query = $conexion->get();
        if ($contar) {
            return $query->num_rows();
        } else {
            return $query->result_array();
        }
    }

    public function setAlertaAlumnoCancelada($cod_alumno, $comentario = '') {
        $arrDatos = array(
            "estado" => 'cancelado'
        );
        $this->oConnection->where('alertas_alumnos.cod_alerta', $this->codigo);
        $this->oConnection->where('alertas_alumnos.cod_alumno', $cod_alumno);
        $respuesta = $this->oConnection->update('alertas_alumnos', $arrDatos);
        
        $arrDatos2 = array('cod_alerta' => $this->codigo, 'cod_alumno' => $cod_alumno, 'estado' => 'cancelado', 'fecha_hora' => date('Y-m-d H:i:s'), 'comentario' => $comentario);
        $this->oConnection->insert('alertas_alumnos_historico', $arrDatos2);
        return $respuesta;
    }

    public function marcarNoLeida($codigoUsuario) {
        return $this->oConnection->update("alertas_usuarios", array("visto" => 0), array("cod_alerta" => $this->codigo, "cod_usuario" => $codigoUsuario));
    }

    public function existeAlertaUsuario($codUsuario) {
        $this->oConnection->select("*");
        $this->oConnection->from("alertas_usuarios");
        $this->oConnection->where("alertas_usuarios.cod_usuario", $codUsuario);
        $this->oConnection->where("alertas_usuarios.cod_alerta", $this->codigo);
        $query = $this->oConnection->get();

        $arrResp = $query->num_rows();
        $respuesta = $arrResp > 0 ? true : false;
        return $respuesta;
    }

    public function setAlertaAlumnoError($codalumno, $comentario = '') {
        $respuesta = $this->oConnection->update('alertas_alumnos', array('estado' => 'error'), array("cod_alerta" => $this->codigo, "cod_alumno" => $codalumno));
        $arrDatos = array('cod_alerta' => $this->codigo, 'cod_alumno' => $codalumno, 'estado' => 'error', 'fecha_hora' => date('Y-m-d H:i:s'), 'comentario' => $comentario);
        $this->oConnection->insert('alertas_alumnos_historico', $arrDatos);
        return $respuesta;
    }

    static public function listar_alertas_alumno(CI_DB_mysqli_driver $conexion, $codFilial, $cod_alumno, $codComunicado = null){
        $format = get_mascara_fecha($codFilial, false);
        $myFilial = new Vfiliales($conexion, $codFilial);
        $conexion->select("CONCAT(general.usuarios_sistema.nombre, ' ', general.usuarios_sistema.apellido)", false);
        $conexion->from("alerta_configuracion");
        $conexion->join("general.usuarios_sistema", "general.usuarios_sistema.codigo = alerta_configuracion.valor");
        $conexion->where("alerta_configuracion.key", "cod_usuario_creador");
        $conexion->where("alerta_configuracion.cod_alerta = alertas.codigo");
        $sqUsuarioCreador = $conexion->return_query();
        $conexion->resetear();

        $conexion->select("alertas.codigo");
        $conexion->select("DATE_FORMAT(alertas.fecha_hora, '$format') AS fecha", false);
        $conexion->select("alertas.mensaje");
        $conexion->select("alerta_configuracion.valor AS asunto", false);
        $conexion->select("IFNULL(($sqUsuarioCreador), 'IGA {$myFilial->nombre}') AS usuario_creador", false);
        $conexion->from("alertas");
        $conexion->join("alertas_alumnos", "alertas_alumnos.cod_alerta = alertas.codigo AND alertas_alumnos.cod_alumno = $cod_alumno AND alertas_alumnos.estado <> 'cancelada'");
        $conexion->join("alerta_configuracion", "alerta_configuracion.cod_alerta = alertas.codigo AND alerta_configuracion.key = 'titulo'");
        $conexion->order_by("alertas.fecha_hora", "DESC");
        if ($codComunicado != null){
            $conexion->where("alertas.codigo", $codComunicado);
        }
        $query = $conexion->get();        
        return $query->result_array();
    }
}
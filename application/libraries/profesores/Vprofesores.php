<?php

/**
 * Class Vprofesores
 *
 * Class  Vprofesores maneja todos los aspectos de alumnos
 *
 * @package  SistemaIGA
 * @subpackage Alumnos
 * @author   Ivan berthillod <ivan.sys@gmail.com>
 * @author   Aquiles Gonzalez <sistemas1@iga-la.net>
 * @version  $Revision: 1.1 $
 * @access   private
 */
class Vprofesores extends Tprofesores {

    function __construct(CI_DB_mysqli_driver $conexion, $codigo = null) {
        parent::__construct($conexion, $codigo);
    }
    
    public function get_cursos_dados($idioma = "es"){
        if ($idioma == 'en'){
            $idioma = 'in';
        }
        $this->oConnection->select("general.planes_academicos.codigo");
        $this->oConnection->select("general.cursos.nombre_$idioma AS nombre", false);
        $this->oConnection->from("horarios");
        $this->oConnection->join("horarios_profesores", "horarios_profesores.cod_horario = horarios.codigo");
        $this->oConnection->join("comisiones", "comisiones.codigo = horarios.cod_comision");
        $this->oConnection->join("general.planes_academicos", "general.planes_academicos.codigo = comisiones.cod_plan_academico");
        $this->oConnection->join("general.cursos", "general.cursos.codigo = general.planes_academicos.cod_curso");
        $this->oConnection->where("horarios.baja = 0");
        $this->oConnection->where("horarios_profesores.cod_profesor", $this->codigo);
        $this->oConnection->group_by("general.planes_academicos.codigo");
        $this->oConnection->order_by("nombre", "ASC");
        $query = $this->oConnection->get();
        return $query->result_array();
    }

    public function get_materias_dadas($idioma = "es", $cod_plan_academico = null){
        $this->oConnection->select("general.materias.codigo");
        if ($idioma == 'en'){
            $idioma = 'in';
        }
        $this->oConnection->select("general.materias.nombre_$idioma AS nombre");
        $this->oConnection->from("horarios_profesores");
        $this->oConnection->join("horarios", "horarios.codigo = horarios_profesores.cod_horario AND horarios.baja = 0");
        $this->oConnection->join("general.materias", "general.materias.codigo = horarios.cod_materia");
        if ($cod_plan_academico != null){
            $this->oConnection->join("general.materias_plan_academico", "general.materias_plan_academico.cod_materia = general.materias.codigo AND general.materias_plan_academico.cod_plan = $cod_plan_academico");
        }
        $this->oConnection->where("horarios_profesores.cod_profesor", $this->codigo);
        $this->oConnection->group_by("general.materias.codigo");
        $query = $this->oConnection->get();
        return $query->result_array();
    }
    
    static function listarProfesoresDataTable(CI_DB_mysqli_driver $conexion, $arrCondindicioneslike, $arrLimit, $arrSort, $contar = false,$separador=null) {
        $nombreApellido = formatearNomApeProf();

        $conexion->select("CONCAT($nombreApellido) as nombre_apellido, profesores.*, general.empresas_telefonicas.nombre as telefono_empresa, telefonos.cod_area as tel_cod_area, telefonos.numero as tel_numero",false);

        $conexion->join("profesores_telefonos", "profesores_telefonos.id_profesor = profesores.codigo", "left");
        $conexion->join("telefonos", "telefonos.codigo = profesores_telefonos.id_telefono", "left");
        $conexion->join("general.empresas_telefonicas", "general.empresas_telefonicas.codigo = telefonos.empresa", "left");

        $conexion->from("profesores");
        $conexion->group_by("codigo");
        if (count($arrCondindicioneslike) > 0) {
            $arrTemp = array();
            foreach ($arrCondindicioneslike as $key => $value) {
                if($key == 'nombre_apellido'){
                    $arrTemp[] = "REPLACE(nombre_apellido, '$separador ',' ') LIKE REPLACE('%$value%', '$separador ',' ')";                     
                }else{
                    $arrTemp[] = "$key LIKE '%$value%'";
                }
            }
            if (count($arrTemp) > 0){
                $having = "(".implode(" OR ", $arrTemp).")";
                $conexion->having($having);
            }
        }
        if ($arrLimit != null) {
            $conexion->limit($arrLimit[1], $arrLimit[0]);
        }
        if ($arrSort > 0) {

            $conexion->order_by($arrSort["0"], $arrSort["1"]);
        }
        $query = $conexion->get();

        if ($contar) {
            $arrResp = $query->num_rows();
        } else {
            $arrResp = $query->result_array();
        }
        return $arrResp;
    }

    /**
     * retorna lista de telefonos de profesores
     * @access public
     * @return array de telefonos
     */
    function getTelefonos() {
        $this->oConnection->select('telefonos.*, profesores_telefonos.default');
        $this->oConnection->from($this->nombreTabla);
        $this->oConnection->join('profesores_telefonos', 'profesores_telefonos.id_profesor = profesores.codigo');
        $this->oConnection->join('telefonos', 'profesores_telefonos.id_telefono = telefonos.codigo');
        $this->oConnection->where('profesores.codigo', $this->codigo);
        $this->oConnection->where('telefonos.baja', 0);
        $query = $this->oConnection->get();
        return $arrResp = $query->result_array();
    }

    /**
     * asigna un telefono a un profesor
     * @access public
     */
    function setTelefonos($cod_telefono, $default) {
        $arrtel = array(
            "id_profesor" => $this->codigo,
            "id_telefono" => $cod_telefono,
            'default' => $default
        );

        $this->oConnection->insert('profesores_telefonos', $arrtel);
    }

    function updateTelefonos($cod_telefono, $default) {
        $arrtel = array(
            "id_profesor" => $this->codigo,
            "id_telefono" => $cod_telefono,
            'default' => $default
        );
        $this->oConnection->where('profesores_telefonos.id_profesor', $this->codigo);
        $this->oConnection->where('profesores_telefonos.id_telefono', $cod_telefono);
        $this->oConnection->update('profesores_telefonos', $arrtel);
    }

    public function cambiarEstado($cambioprofesor) {

        $this->oConnection->trans_begin();
        
        
        $this->estado = $this->estado == 'habilitado' ? 'inhabilitado' : 'habilitado';

         $this->guardarProfesores();

        $estadosHistoricos = new Vprofesores_estado_historico($this->oConnection);

        $arrayGuardarEstadoHistorico = array(
            "cod_profesor" => $this->codigo,
            "estado" => $this->estado,
            "motivo" => $cambioprofesor['motivo'],
            "fecha_hora" => date("Y-m-d H:i:s"),
            "comentario" => $cambioprofesor['comentario'],
            "cod_usuario" => $cambioprofesor['cod_usuario']
        );

        $estadosHistoricos->setProfesores_estado_historico($arrayGuardarEstadoHistorico);
        $estadosHistoricos->guardarProfesores_estado_historico();
        $estadoTran = $this->oConnection->trans_status();

        if ($estadoTran === false) {
            $this->oConnection->trans_rollback();
        } else {
            $this->oConnection->trans_commit();
        }
        return $estadoTran;
    }

    function getRazonSocialDefault() {
        $conexion = $this->oConnection;
        $conexion->select('cod_razon');
        $conexion->from('profesores_razones');
        $conexion->where('cod_profesor', $this->codigo);
        $conexion->where('default', 1);
        $query = $conexion->get();
        return $query->result_array();
    }

    /**
     * asigna a razon social a profesor
     * @access public
     */
    function setRazonesSociales($arrRazones) {
        $conexion = $this->oConnection;
        $conexion->insert('profesores_razones', $arrRazones);
    }

    public function getRazonSocialprofesor() {

        $this->oConnection->select('razones_sociales.*, profesores_razones.default');
        $this->oConnection->from('razones_sociales');
        $this->oConnection->join('profesores_razones', 'profesores_razones.cod_razon = razones_sociales.codigo');
        $this->oConnection->where('profesores_razones.cod_profesor', $this->codigo);
        $this->oConnection->where('razones_sociales.baja', 0);
        $query = $this->oConnection->get();
        return $query->result_array();
    }

    /* STATIC FUNCTIONS */

    static function getReporteProfesores(CI_DB_mysqli_driver $conexion, $arrLimit = null, $arrSort = null, $contar = false, $search = null, array $searchFields = null, $fechaDesde = null, $fechaHasta = null) {

        $aColumns = array();
        $aColumns['apellido_nombre']['order'] = "apellido_nombre";
        $aColumns['documento']['order'] = "documento";
        $aColumns['mail']['order'] = "profesores.mail";
        $aColumns['domicilio']['order'] = "domicilio";
        $aColumns['localidad_nombre']['order'] = "general.localidades.nombre";
        $aColumns['telefono']['order'] = "telefono";
        $aColumns['apellido_nombre']['having'] = "apellido_nombre";
        $aColumns['documento']['having'] = "documento";
        $aColumns['mail']['having'] = "profesores.mail";
        $aColumns['domicilio']['having'] = "domicilio";
        $aColumns['localidad_nombre']['having'] = "general.localidades.nombre";
        $aColumns['telefono']['having'] = "telefono";


        $conexion->select("general.documentos_tipos.nombre");
        $conexion->from("general.documentos_tipos");
        $conexion->where("general.documentos_tipos.codigo = profesores.tipodocumento");
        $queryNombreDocumento = $conexion->return_query();
        $conexion->resetear();

        $conexion->select("CONCAT(telefonos.cod_area, ' ', telefonos.numero)", false);
        $conexion->from("telefonos");
        $conexion->join("profesores_telefonos", "profesores_telefonos.id_telefono = telefonos.codigo");
        $conexion->where("profesores_telefonos.id_profesor = profesores.codigo");
        $conexion->order_by("telefonos.codigo", "ASC");
        $conexion->limit(1, 0);
        $queryTelefono = $conexion->return_query();
        $conexion->resetear();

        $conexion->select("CONCAT(profesores.apellido, ', ', profesores.nombre) AS apellido_nombre", false);
        $conexion->select("CONCAT(($queryNombreDocumento), ' ', profesores.nrodocumento) AS documento", false);
        $conexion->select("mail");
        $conexion->select("CONCAT(calle, ' ', numero, ' ', complemento) AS domicilio", false);
        $conexion->select("general.localidades.nombre AS localidad_nombre");
        $conexion->select("($queryTelefono) AS telefono", false);
        $conexion->from("profesores");
        $conexion->join("general.localidades", "general.localidades.id = profesores.cod_localidad");
        if ($fechaDesde != null)
            $conexion->where("DATE(profesores.fechaalta) >=", $fechaDesde);
        if ($fechaHasta != null)
            $conexion->where("DATE(profesores.fechaalta) <=", $fechaHasta);


        if ($search != null) {
            foreach ($aColumns AS $key => $tableFields) {
                if ($searchFields == null || in_array($key, $searchFields)) {
                    $conexion->or_having($tableFields['having'] . " LIKE ", "%$search%");
                }
            }
        }
        if (!$contar) {
            if ($arrLimit != null && is_array($arrLimit))
                $conexion->limit($arrLimit[1], $arrLimit[0]);
            if ($arrSort != null && is_array($arrSort) && isset($aColumns[$arrSort[0]]['order']))
                $conexion->order_by($aColumns[$arrSort[0]]['order'], $arrSort[1]);
        }
        $query = $conexion->get();
        if ($contar)
            return $query->num_rows();
        else
            return $query->result_array();

        /*
         * SELECT CONCAT(profesores.apellido, ", ", profesores.nombre) AS apellido_nombre,
          CONCAT((subquery1), " ", profesores.nrodocumento) AS documento,
          mail,
          CONCAT(calle, " ", numero, " ", complemento) AS domicilio,
          general.localidades.nombre AS localidad_nombre,
         * 
         * 
          ($subquery2) AS telefono

          FROM profesores
          INNER JOIN general.localidades ON general.localidades.id = profesores.cod_localidad;
         */
    }

     static function getProfesoresconHorarios(CI_DB_mysqli_driver $conexion, $orden) {
        $conexion->select('profesores.*', FALSE);
        $conexion->from('profesores');
        $conexion->join('horarios_profesores', 'profesores.codigo = horarios_profesores.cod_profesor');
        $conexion->join('horarios', 'horarios.codigo = horarios_profesores.cod_horario');
        $conexion->where('horarios.baja', 0);
        $conexion->order_by($orden['campo'], $orden['orden']);
        $conexion->group_by('profesores.codigo');
        $query = $conexion->get();
        return$query->result_array();
    }
}


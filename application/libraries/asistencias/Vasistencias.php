<?php

/**
* Class Vasistencias
*
*Class  Vasistencias maneja todos los aspectos de alumnos
*
* @package  SistemaIGA
* @subpackage Alumnos
* @author   Ivan berthillod <ivan.sys@gmail.com>
* @author   Aquiles Gonzalez <sistemas1@iga-la.net>
* @version  $Revision: 1.1 $
* @access   private
*/
class Vasistencias extends Tasistencias{
    
    private static $asistencias = array(
        array('id' => 'ausente', 'nombre' => 'AUSENTE'),
        array('id' => 'presente', 'nombre' => 'PRESENTE'),
        array('id' => 'justificado', 'nombre' => 'JUSTIFICADO'),
        array('id' => 'media_falta', 'nombre' => 'MEDIA_FALTA'));

    function __construct(CI_DB_mysqli_driver $conexion, $codigo = null) {
        parent::__construct($conexion, $codigo);
    }
    
    static function getArrayEstadoAsistencias($id = false) {
        $devolver = '';
        
        if ($id != false) {
            $array = self::$asistencias;
            foreach ($array as $value) {
                if ($value['id'] == $id) {
                    
                $devolver= array(
                    'id'=>$id,
                    'nombre'=>lang($value['id']));   
                
                }
                
            }
        } else {
            $asistencias = self::$asistencias;
            foreach($asistencias as$key=>$asistencia){
                $asistencias[$key] = array('id'=>$asistencia['id'],'nombre'=>lang($asistencia['id']));
            }
            return $asistencias;
        }
       
        return $devolver;
    }

    /* esta function esta siendo accedida desde un web services */
    function getReporteSeguimientoFiliales(CI_DB_mysqli_driver $conexion){
        $anioAnterior = date("Y") - 1;
        $anioActual = date("Y");
        $mesAnterior = sumarMeses(date("Y-m-01"), "-1");
        $conexion->select("matriculas.codigo");
        $conexion->from("matriculas");
        $conexion->join("matriculas_periodos", "matriculas_periodos.cod_matricula = matriculas.codigo");
        $conexion->join("estadoacademico", "estadoacademico.cod_matricula_periodo = matriculas_periodos.codigo");
        $conexion->join("matriculas_horarios", "matriculas_horarios.cod_estado_academico = estadoacademico.codigo");
        $conexion->join("horarios", "horarios.codigo = matriculas_horarios.cod_horario");
        $conexion->where_in("matriculas.cod_plan_academico", array(1, 12));
        $conexion->where("matriculas.fecha_emision >=", "{$anioAnterior}-07-01");
        $conexion->where("matriculas.fecha_emision <", "{$anioActual}-07-01");
        $conexion->where("matriculas_periodos.cod_tipo_periodo", 1);
        $conexion->where("matriculas_periodos.estado", Vmatriculas_periodos::getEstadoHabilitada());
        $conexion->where("estadoacademico.estado", Vestadoacademico::getEstadoCursando());
        $conexion->where("horarios.dia >=", $mesAnterior);
        $conexion->where("horarios.baja", 0);
        $conexion->where("matriculas_horarios.estado IS NOT NULL");
        $conexion->group_by("matriculas.codigo");
        $query = $conexion->get();
        return $query->num_rows();
    }

    /**
     * Retorna array de las asistencias a las unidades en el campus.
     * @access public
     * @param int $id_materia : codigo de la materia equivalente al id del grupo en el campus
     * @param Array $arrUsuarios : codigos de usuarios en campus
     * @return json de asistencias web.
     */
    public function getAsistenciaPorUnidad(CI_DB_mysqli_driver $conexion, $cod_filial = null, $id_materia = null, $arrUsuarios = null){
        $this->client = new SoapClient('http://campus.iga-la.net/soap/?wsdl=true',
            ['trace' => true,
                'cache_wsdl' => WSDL_CACHE_MEMORY,
                'login' => 'webservice',
                'password' => 'Wu327Nx19c',
                'exceptions' => '1']);

        /*Seleccionar id grupo del campus equivalente al codigo de materia recibido*/
        $conexion->select('grupo_plataforma');
        $conexion->from('grupos_campus');
        $conexion->where('filial', $cod_filial);
        $conexion->where('materia', $id_materia);
        $conexion->group_by('materia');
        $grupo_campus = $conexion->get()->result();
        $id_grupo = $grupo_campus[0]->grupo_plataforma;

       $arrAsistenciasWeb = array();

        if (!empty($id_grupo)) {

            if (!empty($arrUsuarios)){
                //Get Unidades de la plataforma
                try {

                    $result = $this->client->obtener_unidades_grupo(array(
                        'id_grupo' => $id_grupo
                    ));
                    $unidades = $result->unidades;

                } catch (SoapFault $e) {
                    echo $e->faultcode;
                }

                foreach ($arrUsuarios as $key => $usuario){

                    try {

                        //Get avance
                        $result = $this->client->obtener_avance_usuario_unidad(array(
                            'id_grupo' => $id_grupo,
                            'id_usuario' => $usuario['id_usuario'],
                            'perfil' => 'A'
                        ));

                    } catch (SoapFault $e) {
                        //echo $e->faultcode;
                    }

                    //Si existe el usuario en e-ducativa
                    if(!empty($result->usuarios->id_usuario)) {

                        $result->usuarios->nombre = $usuario['nombre_apellido'];

                        foreach ($result->usuarios->unidades as $key => $unidadUsuario) {

                            foreach ($unidades as $key => $unidad) {
                                if ($unidadUsuario->id_unidad == $unidad->id_unidad) {
                                    $result->usuarios->unidades[$key]->nombre = $unidad->nombre;
                                    $result->usuarios->unidades[$key]->descripcion = $unidad->descripcion;

                                    if ($result->usuarios->unidades[$key]->avance > 0) {
                                        $result->usuarios->unidades[$key]->asistencia = 'P';
                                    } else {
                                        $result->usuarios->unidades[$key]->asistencia = 'A';
                                    }

                                }
                            }

                        }

                        $arrAsistencias[] = $result->usuarios;

                    } else {
                        //echo "Usuario no existe en el campus";
                    }

                    $result = [];

                }

            } else {
                $arrAsistencias = null;
            }

        } else {
            $arrAsistencias = null;
        }

        return $arrAsistencias;

    }
    
}
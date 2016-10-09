<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * Created by PhpStorm.
 * User: damian
 * Date: 24/08/16
 * Time: 16:06
 */
class scripts extends CI_Controller {
    private $seccion;
    protected $logIC;

    public function __construct() {
        parent::__construct();
        $this->lang->load(get_idioma(), get_idioma());
        $this->seccion = session_method();
        $this->client = new SoapClient('http://campus.iga-la.net/soap/?wsdl=true',
            ['trace' => true,
                'cache_wsdl' => WSDL_CACHE_MEMORY,
                'login' => 'webservice',
                'password' => 'Wu327Nx19c',
                'exceptions' => '1']);
        $this->setLogIC(true);
        //header('Content-Type: text/plain; charset=UTF-8');
        ob_implicit_flush(true);
    }

    public function setLogIC($log) {
        $this->logIC = $log;
    }

    public function getLogIC() {
        return $this->logIC;
    }
                                                     /********************
                                                     ********FASE 1*******
                                                     ********************/


    public function insertarGrupos() {
        $conexion = $this->load->database("general", true);
        try {
            $result = $this->client->consultar_grupos();
        } catch (SoapFault $e) {
            echo $e->faultcode;
        }
        try{
            if($result->grupos){
                foreach ($result->grupos as $grupo){
                    $conexion->query("INSERT
                                        INTO
                                            `general` . grupos_plataforma_educativa(
                                                id,
                                                nombre,
                                                descripcion,
                                                estado,
                                                id_usuario_administrador,
                                                idioma,
                                                responsables_acceden_admin,
                                                tipo
                                            )
                                        VALUES(
                                            {$grupo->id},
                                            '{$grupo->nombre}',
                                            '{$grupo->descripcion}',
                                            ".( empty($grupo->estado) ? "0" : "{$grupo->estado}" ).",
                                            '{$grupo->id_usuario_administrador}',
                                            {$grupo->idioma},
                                            ".($grupo->responsables_acceden_admin != 1 ? "NULL" : "{$grupo->responsables_acceden_admin}").",
                                            {$grupo->tipo}
                                        );");

                    //id_agrupacion, id_grupo_cabecera, orden_agrupado, dato_adicional, id_curso_externo, nombre agrupacion
                    //echo $grupo->id . "\t" .$grupo->nombre . "\t". $grupo->descripcion. "\t". $grupo->estado . "\t". $grupo->id_usuario_administrador .
                    //    "\t". $grupo->idioma ."\t". "RESPONSABLE: ". $grupo->responsables_acceden_admin ."ESTE ES   " . "\t".$grupo->tipo. "\t". "<br>";
                }
            }
        }catch(Exception $e){
            echo $e->getMessage();
        }
    }

    public function insertarGruposMaterias() {
        $grupos_materias=array(
            //seguridad e higiene
            array(
                'grupo' => array(22,88,84,54,69,106,96,119,60),
                'materia' => array(4) //160 y 302
            ),
            //Frances
            array(
                'grupo' => array(24,87,80,53,71,103,95,115,57),
                'materia' => array(3) //272
            ),
            //adm y mkg 1
            array(
                'grupo' => array(41,85,75,55,72,92,93,110,61),
                'materia' => array(5)
            ),
            //Enologia
            array(
                'grupo' => array(37,86,79,56,70,102,94,114,62),
                'materia' => array(6) //279, 161
            ),
            //Adm y mkg 2
            array(
                'grupo' => array(23,120,76,129,137,97,146,111,155),
                'materia' => array(10)
            ),
            //Ingles
            array(
                'grupo' => array(20,122,81,130,138,147,148,116,157),
                'materia' => array(9) //261
            ),
            //Org de eve
            array(
                'grupo' => array(38,123,82,131,139,67,126,83,134,142,105,149,117,159,107,152,118,168),
                'materia' => array(12,163) //15, 168
            ),
            //Admin de alim
            array(
                'grupo' => array(68,124,74,132,140,39,127,73,135,143,90,150,109,161,89,153,108,170),
                'materia' => array(11)
            ),
            //Cocteleria
            array(
                'grupo' => array(42,145,78,133,141,101,151,113,163),
                'materia' => array(13,172) //172
            ),
            //Apreciacion senso
            array(
                'grupo' => array(40,128,77,136,144,98,154,112,172),
                'materia' => array(208) //96
            )
        );
        $conexion = $this->load->database("general",true);
        foreach($grupos_materias as $grupo_materia){
            foreach ($grupo_materia['grupo'] as $grupo){
                foreach ($grupo_materia['materia'] as $materia){
                    $data = array(
                        'id_grupo' => $grupo,
                        'cod_materia' => $materia
                    );

                    $conexion->insert('grupos_plataforma_materias', $data);
                }
            }
        }
    }
    //?codFilial=10
    public function scriptGruposComisiones() {
        $conexion = $this->load->database("general", true);
        $arrFiliales = Vfiliales::listarFiliales($conexion, array("version_sistema" => 2, "baja" => "0"));
        $filial = $this->input->get('codFilial');
        $grupos = $this->crearArray($filial);
        foreach ($arrFiliales as $row_filial) {
            if($row_filial['codigo'] == $filial) {
                $conexion2 = $this->load->database($row_filial['codigo'], true);
                $conexion2->query("CREATE TABLE IF NOT EXISTS `grupos_comisiones` (
                                `id_grupo` int(11) NOT NULL,
                                `cod_comision` int(11) NOT NULL,
                                PRIMARY KEY (`id_grupo`,`cod_comision`),
                                KEY `grupos_comisiones_comisiones_FK` (`cod_comision`),
                                CONSTRAINT `grupos_comisiones_comisiones_FK` FOREIGN KEY (`cod_comision`) REFERENCES `comisiones` (`codigo`),
                                CONSTRAINT `grupos_comisiones_id_grupo_FK` FOREIGN KEY (`id_grupo`) REFERENCES `general`.`grupos_plataforma_educativa`(`id`)
                                ) ENGINE=InnoDB DEFAULT CHARSET=utf8
                                ENGINE=InnoDB
                                DEFAULT CHARSET=utf8
                                COLLATE=utf8_general_ci;");
                foreach ($grupos as $grupo) {
                    if($grupo['idioma'] == $row_filial['idioma']) {
                        $arrCondiciones = array(
                            'cursos.codigo' => $grupo['curso'],
                            'ciclos.codigo' => $grupo['ciclo'],
                            'comisiones.modalidad' => $grupo['modalidad'],
                            'comisiones.cod_tipo_periodo' => $grupo['periodo']
                        );
                        $comisiones = Vcomisiones::getComisionesFiltro($conexion2, $arrCondiciones);
                        foreach ($comisiones as $comision) {
                            $grupo_comision = new Tgrupos_comisiones($conexion2, $grupo['id_grupo'], $comision['codigo']);
                            $grupo_comision->guardarGrupos_comisiones();
                        }
                    }
                }
            }
        }
    }
    //?codFilial=10
    public function scriptGruposEstadosAcademicos() {
        $conexion = $this->load->database("general", true);
        $arrFiliales = Vfiliales::listarFiliales($conexion, array("version_sistema" => 2, "baja" => "0"));
        $filial = $this->input->get('codFilial');
        foreach($arrFiliales as $row_filial){
            if($row_filial['codigo'] == $filial) {
                $conexion2 = $this->load->database($row_filial['codigo'], true);
                $conexion2->query("ALTER TABLE estadoacademico
                                  ADD COLUMN id_grupo INT(11) DEFAULT NULL,
                                  ADD CONSTRAINT fk_grupos_plataforma_educativa_id_grupo 
                                  FOREIGN KEY (id_grupo) REFERENCES general.grupos_plataforma_educativa(id) ON DELETE NO ACTION;");
                $grupos_comisiones = Vgrupos_comisiones::listar($conexion2);
                foreach ($grupos_comisiones as $grupo_comision){
                    $estadosacademicos = Vestadoacademico::getEstadosGrupoDeComision($conexion2, $grupo_comision);
                    foreach ($estadosacademicos as $estadosacademico){
                        Vestadoacademico::setGrupo($conexion2, $estadosacademico['cod_ea'],$grupo_comision['id_grupo']);
                    }
                }
            }
        }
    }

    public function scriptGruposEstadosAcademicosPorAlumno($filial, $alumno) {
        if(empty($filial) && empty($alumno)) {
            $filial = $this->input->get('codFilial');
            $alumno = $this->input->get('codAlumno');
        }
        $conexion = $this->load->database($filial, true);
        $estadosacademicos = Vestadoacademico::getGruposEstadosAcademicos($conexion, $alumno);
        foreach ($estadosacademicos as $estadosacademico){
            if(empty($estadosacademico['grupo_actual'])) {
                Vestadoacademico::setGrupo($conexion, $estadosacademico['codigo'], $estadosacademico['id_grupo']);
            }
        }
        return true;
    }

                                                     /********************
                                                     ********FASE 2*******
                                                     ********************/

    //?codFilial=16&debug=0
    public function normalizarUsuariosTodos() {
        //ver filiales
        $start = new DateTime();
        echo  date("Y-m-d H:i:s"). " ---> comienzo \n";
        $arrFiliales = array(array('codigo' => $this->input->get('codFilial'), 'idioma' => 'es'));
        $debug = $this->input->get('debug');
        $arrAlumnosHechos = array();
        foreach ($arrFiliales as $filial) {
            $conexion = $this->load->database($filial['codigo'], true);
            $grupos_comisiones = Vgrupos_comisiones::listar($conexion);
                foreach ($grupos_comisiones as $grupo_comision) {
                    $codigosAlumnos = Valumnos::getAlumnosComision($conexion, $grupo_comision['cod_comision']);
                    foreach ($codigosAlumnos as $codigoAlumno) {
                        if (!in_array($codigoAlumno['codAlumno'], $arrAlumnosHechos)) {
                            $this->normalizarUnicoUsuario($conexion,$filial['codigo'], $codigoAlumno['codAlumno'], $debug);
                            $arrAlumnosHechos[] = $codigoAlumno['codAlumno'];
                        }
                }

            }
        }
        $end = new DateTime();
        $time = $start->diff($end);
        $time->format('%H horas, %i mins');
        echo date("Y-m-d H:i:s") . "---> fin  \n";
        echo "El script tardó: $time";
    }

    public function normalizaAlumno() {
        $alumno = $this->input->get('codAlumno');
        $filial = $this->input->get('codFilial');
        $debug = $this->input->get('debug');
        $conexion = $this->load->database($filial, true);

        $this->normalizarUnicoUsuario($conexion, $filial, $alumno, $debug);
    }

    public function normalizarUnicoUsuario(CI_DB_mysqli_driver $conexion, $codFilial, $codAlumno, $debug) {
        $usuario = "u-" . $codFilial . "-" . $codAlumno;
        if ($debug) {
            echo "Usuario $usuario <br>";
        } else {
            echo "Usuario: $usuario  \n";
        }
        $estadosAcadAlu = Vestadoacademico::getEstadosAcadConGrupos($conexion, $codAlumno);

        $filial = new Vfiliales($conexion, $codFilial);
        //get idioma
        switch ($filial->idioma) {
            case "es":
                $id_idioma = '1';
                $gruposDefaut = array(array('cod_ea'=>'0', 'id_grupo'=>'25'),array('cod_ea'=>'0', 'id_grupo'=>'26'),array('cod_ea'=>'0', 'id_grupo'=>'27'));
                break;
            case "pt":
                $id_idioma = '10';
                $gruposDefaut = array(array('cod_ea'=>'0', 'id_grupo'=>'174'),array('cod_ea'=>'0', 'id_grupo'=>'175'),array('cod_ea'=>'0', 'id_grupo'=>'27'));
                break;
            case "in":
                $id_idioma = '2';
                $gruposDefaut = array(array('cod_ea'=>'0', 'id_grupo'=>'27'));
                break;
            default:
                $id_idioma = '1';
                $gruposDefaut = array(array('cod_ea'=>'0', 'id_grupo'=>'25'),array('cod_ea'=>'0', 'id_grupo'=>'26'),array('cod_ea'=>'0', 'id_grupo'=>'27'));
        }
        $estadosAcadAlu = array_merge($estadosAcadAlu, $gruposDefaut);

        //se crea es nuevo, se no, no existe en la plataforma
        //crear usuario y asignar grupos
        $alumno = new Valumnos($conexion, $codAlumno);
        if ($debug) {
            echo "Crea usuario $usuario en la plataforma </br>";
        } else {
            $result = $this->registrarUsuario($usuario, $alumno->nombre, $alumno->apellido, $alumno->email, $id_idioma);
        }
        // Si el usuario se creo con exito, seteamos parametros cod_alumno, cod_filial
        if ($result->estado == 1) {
            if ($debug) {
                echo "Creando datos adicionales para el usuario $usuario,  $alumno->sexo </br>";
            } else {
                echo "Creando datos adicionales para el usuario $usuario, $alumno->sexo  \n";
                $this->establececerDatUsuar($usuario, 5000, $codFilial);
                $this->establececerDatUsuar($usuario, 5001, $codAlumno);
                $this->establececerDatUsuar($usuario, 15, $alumno->sexo);
            }
            //crear grupos para el usuario
            foreach ($estadosAcadAlu as $estadoAcadAlu) {
                if ($debug) {
                    echo "Asigna el grupo {$estadoAcadAlu['id_grupo']} para el usuario $usuario </br>";
                } else {
                    $this->asignarUsuarioGrupo((string)$estadoAcadAlu['id_grupo'], $usuario);
                }
            }
        } else {
            $gruposEducUsu = $this->consultarGrupos($usuario);
            foreach ($gruposEducUsu->usuarios->grupos as $grupoId) {
                if($grupoId->estado != false) {
                    $bienAsignado = false;
                    foreach ($estadosAcadAlu as $estadoAcadAlu) {
                        if ($grupoId->id_grupo == $estadoAcadAlu['id_grupo']) $bienAsignado = true;
                    }
                    if (!$bienAsignado) {
                        if ($debug) {
                            echo "Baja el usuario $usuario del grupo $grupoId->id_grupo </br>";
                        } else {
                            $estado = '0';
                            $this->modificarUsuarioGrupo($grupoId->id_grupo, $usuario, $estado);
                        }
                    }
                }
            }
            foreach ($estadosAcadAlu as $estadoAcadAlu) {
                $existeEnPlat = false;
                $estaDesactivado = false;
                foreach ($gruposEducUsu->usuarios->grupos as $grupoId) {
                    if ($estadoAcadAlu['id_grupo'] == $grupoId->id_grupo) {
                        $existeEnPlat = true;
                        if ($grupoId->estado == false) { //se hay, verifica se esta desactivado, para que así pueda activarlo
                            $estaDesactivado = true;
                        }
                    }
                }
                if (!$existeEnPlat) {
                    if ($debug) {
                        echo "Asigna el grupo {$estadoAcadAlu['id_grupo']} en el usuario $usuario </br>";
                    } else {
                        $this->asignarUsuarioGrupo((string)$estadoAcadAlu['id_grupo'], $usuario);
                    }
                } elseif ($estaDesactivado) { //si el grupo está inhabilitado en la plataforma, y debería estar habilitado, va a habilitarlo
                    if ($debug) {
                        echo "Habilita el grupo {$estadoAcadAlu['id_grupo']} en el usuario $usuario </br>";
                    } else {
                        $estado = '1';
                        $this->modificarUsuarioGrupo($estadoAcadAlu['id_grupo'], $usuario, $estado);
                    }
                }
            }
        }

        if($debug){
            echo "########################### </br>";
        } else {
            echo "###########################  \n";
        }
    }


                                                     /********************
                                                     ********FASE 3*******
                                                     ********************/


    public function obternerExamenesPlat() {

    }



    /** ABMC WEBSERVICE */
    public function modificarUsuarioGrupo($id_grupo, $usuario, $estado) {
        $usuarioGrupoAlta = array(
            'id_grupo' => $id_grupo,
            'administrador_grupo' => '0',
            'estado' => $estado,
            'perfil' => 'A'
        );
        try {
            $this->client->modificar_usuario_grupo(
                array('id_usuario' => $usuario,
                    'usuario_grupo' => $usuarioGrupoAlta
                ));
            if($estado == '1'){
                echo "Se ha activado el grupo: $id_grupo  \n";
            } else {
                echo "Se ha desactivado el grupo: $id_grupo  \n";

            }
        }catch(SoapFault $e) {
            error_log($e->faultcode);
            if($estado == '1'){
                echo "La activación del grupo $id_grupo ha fallado  \n";
            } else {
                echo "La desactivación del grupo $id_grupo ha fallado  \n";
            }
        }

    }
    public function asignarUsuarioGrupo($id_grupo, $usuario) {
        $usuarioGrupoAlta = array(
            'id_grupo' => $id_grupo,
            'administrador_grupo' => '0',
            'estado' => '1',
            'perfil' => 'A'
        );
        try{
            $this->client->asignar_usuario_grupo(
                array('id_usuario' => $usuario,
                    'usuario_grupo' => $usuarioGrupoAlta
                ));
            echo "Se ha asignado el grupo: $id_grupo  \n";
        } catch (SoapFault $ex) {
            error_log($ex->faultcode);
            echo "La asignación del grupo $id_grupo ha fallado  \n";
        }
    }
    public function establececerDatUsuar($usuario, $id_campo, $valor) {
       try{
                $this->client->establecer_datos_adicionales_usuarios(array(
                        'valor_da' => array(
                            'id_usuario' => $usuario,
                            'id_campo' => (int)$id_campo,
                            'valor' => $valor
                        )
                    )
                );
       } catch (SoapFault $e) {
           error_log($e->faultcode);
       }
    }
    public function registrarUsuario($usuario, $nombre, $apellido, $mail, $id_idioma) {
        $arrTempUsuario = array('administrador_usuario' => false,
            'id_usuario' => $usuario,
            'nombre' => $nombre,
            'apellido' => $apellido,
            'clave' => 'IGA_2016_ALUMNO',
            'email' => $mail,
            'id_idioma' => $id_idioma
        );
        try{
            $result = $this->client->registrar_usuario(
                array('usuario' => $arrTempUsuario,
                    'usuario_grupo' => array(
                        'id_grupo' => '27', /*$id_grupo*/
                        'estado' => '1',
                        'perfil' => 'A',
                        'administrador_grupo' => false
                    )
                )
            );
            echo "Se ha creado el nuevo usuario $usuario \n";
        } catch (SoapFault $e) {
            $result = new StdClass();
            $result->estado = false;
            error_log($e->faultcode);
            echo "La creacion del nuevo usuario $usuario ha fallado o el usuario ya existe\n";
        }
        return $result;
    }
    public function consultarGrupos($usuario) {
        $result = $this->client->consultar_usuarios(array('id_usuario' => $usuario));
        if(is_array($result->usuarios->grupos)){
        return $result;
        }else{
            $result->usuarios->grupos= array($result->usuarios->grupos);
            return $result;
        }

    }
    public function buscaAvances() {
        try {
            //$result = $this->client->consultar_usuarios_con_avances();//funciona
//            $result = $this->client->obtener_avance_usuarios(array('id_grupo' => 20));//funciona
//            $result = $this->client->obtener_avance_usuario_unidad(array('id_grupo' => 23, 'id_usuario' => 'u-18-1758'));//funciona
            $result = $this->client->obtener_notas_calificaciones(array('id_grupo' => 39));
            echo "<pre>";
            print_r($result);
            die();
        }
        catch (Exception $ex) {
            error_log($ex->getCode());
            error_log($ex->getMessage());
            die("error");
        }
    }

    /** FUNCIONES UTILES */
    public function isExistPlat($usuario)  {
        try {
            $this->client->autenticar_usuario_confiable(array('id_usuario' => $usuario));
            return true;
        }catch(SoapFault $e){
            return false;
        }
    }
    public function crearArray($filial) {
        switch($filial) {
            case '20':
                $grupos = array(
                    /**
                     * MARZO TRADICIONAL 
                     */
                    //PRIMER AÑO
                    array("id_grupo" => 22, "periodo" => 1 , "modalidad" => "normal", "ciclo" => 13, "idioma"=>"es", "curso" => array(1,30,57,95,2,31)),
                    array("id_grupo" => 24, "periodo" => 1 , "modalidad" => "normal", "ciclo" => 13, "idioma"=>"es", "curso" => array(1,30,57,95,2,31)),
                    array("id_grupo" => 41, "periodo" => 1 , "modalidad" => "normal", "ciclo" => 13, "idioma"=>"es", "curso" => array(1,30,57,95,2,31)),
                    array("id_grupo" => 37, "periodo" => 1 , "modalidad" => "normal", "ciclo" => 13, "idioma"=>"es", "curso" => array(1,30,57,95)),
                    array("id_grupo" => 67, "periodo" => 1 , "modalidad" => "normal", "ciclo" => 13, "idioma"=>"es", "curso" => array(2,31)),
                    //SEGUNDO AÑO
                    array("id_grupo" => 23, "periodo" => 2 ,"modalidad" => "normal", "ciclo" => 13, "idioma"=>"es", "curso" => array(1,30,57,95,2,31)),
                    array("id_grupo" => 20, "periodo" => 2 ,"modalidad" => "normal", "ciclo" => 13, "idioma"=>"es", "curso" => array(1,30,57,95,2,31)),
                    array("id_grupo" => 38, "periodo" => 2 ,"modalidad" => "normal", "ciclo" => 13, "idioma"=>"es", "curso" => array(1,30,57,95)),
                    array("id_grupo" => 39, "periodo" => 2 ,"modalidad" => "normal", "ciclo" => 13, "idioma"=>"es", "curso" => array(1,30,57,95)),
                    array("id_grupo" => 42, "periodo" => 2 ,"modalidad" => "normal", "ciclo" => 13, "idioma"=>"es", "curso" => array(1,30,57,95)),
                    array("id_grupo" => 39, "periodo" => 2 ,"modalidad" => "normal", "ciclo" => 13, "idioma"=>"es", "curso" => array(2,31)), //ex 39
                    array("id_grupo" => 40, "periodo" => 2 ,"modalidad" => "normal", "ciclo" => 13, "idioma"=>"es", "curso" => array(2,31)),
                    /**
                     * AGOSTO TRADICIONAL
                     */
                    //PRIMER AÑO
//                    array("id_grupo" => 84, "periodo" => 1 ,"modalidad" =>"normal", "ciclo" => 17, "idioma"=>"es", "curso" => array(1,30,57,95,2,31)),
//                    array("id_grupo" => 80, "periodo" => 1 ,"modalidad" =>"normal", "ciclo" => 17, "idioma"=>"es", "curso" => array(1,30,57,95,2,31)),
//                    array("id_grupo" => 75, "periodo" => 1 ,"modalidad" =>"normal", "ciclo" => 17, "idioma"=>"es", "curso" => array(1,30,57,95,2,31)),
//                    array("id_grupo" => 79, "periodo" => 1 ,"modalidad" =>"normal", "ciclo" => 17, "idioma"=>"es", "curso" => array(1,30,57,95)),
//                    array("id_grupo" => 83, "periodo" => 1 ,"modalidad" =>"normal", "ciclo" => 17, "idioma"=>"es", "curso" => array(2,31)),
//                    //SEGUNDO AÑO
//                    array("id_grupo" => 76, "periodo" => 2,"modalidad" =>"normal", "ciclo" => 17, "idioma"=>"es", "curso" => array(1,30,57,95,2,31)),
//                    array("id_grupo" => 81, "periodo" => 2,"modalidad" =>"normal", "ciclo" => 17, "idioma"=>"es", "curso" => array(1,30,57,95,2,31)),
//                    array("id_grupo" => 82, "periodo" => 2,"modalidad" =>"normal", "ciclo" => 17, "idioma"=>"es", "curso" => array(1,30,57,95)),
//                    array("id_grupo" => 74, "periodo" => 2,"modalidad" =>"normal", "ciclo" => 17, "idioma"=>"es", "curso" => array(1,30,57,95)),
//                    array("id_grupo" => 78, "periodo" => 2,"modalidad" =>"normal", "ciclo" => 17, "idioma"=>"es", "curso" => array(1,30,57,95)),
//                    array("id_grupo" => 73, "periodo" => 2,"modalidad" =>"normal", "ciclo" => 17, "idioma"=>"es", "curso" => array(2,31)),
//                    array("id_grupo" => 77, "periodo" => 2,"modalidad" =>"normal", "ciclo" => 17, "idioma"=>"es", "curso" => array(2,31)),
                    /**
                     * AGOSTO INTENSIVO 1  (SE UTILIZA SOLO PARA ROSARIO ANALIZAR)
                     */
                    //PRIMER AÑO
                    array("id_grupo" => 54, "periodo" => 1,"modalidad" =>"intensiva", "ciclo" => 18, "idioma"=>"es", "curso" => array(1,30,57,95,2,31)),
                    array("id_grupo" => 53, "periodo" => 1,"modalidad" =>"intensiva", "ciclo" => 18, "idioma"=>"es", "curso" => array(1,30,57,95,2,31)),
                    array("id_grupo" => 55, "periodo" => 1,"modalidad" =>"intensiva", "ciclo" => 18, "idioma"=>"es", "curso" => array(1,30,57,95,2,31)),
                    array("id_grupo" => 56, "periodo" => 1,"modalidad" =>"intensiva", "ciclo" => 18, "idioma"=>"es", "curso" => array(1,30,57,95)),
                    array("id_grupo" => 134,"periodo" => 1, "modalidad" =>"intensiva", "ciclo" => 18, "idioma"=>"es", "curso" => array(2,31)),
                    //SEGUNDO AÑO
                    array("id_grupo" => 129,"periodo" => 2, "modalidad" =>"intensiva", "ciclo" => 18, "idioma"=>"es", "curso" => array(1,30,57,95,2,31)),
                    array("id_grupo" => 130,"periodo" => 2, "modalidad" =>"intensiva", "ciclo" => 18, "idioma"=>"es", "curso" => array(1,30,57,95,2,31)),
                    array("id_grupo" => 131,"periodo" => 2, "modalidad" =>"intensiva", "ciclo" => 18, "idioma"=>"es", "curso" => array(1,30,57,95)),
                    array("id_grupo" => 132,"periodo" => 2, "modalidad" =>"intensiva", "ciclo" => 18, "idioma"=>"es", "curso" => array(1,30,57,95)),
                    array("id_grupo" => 133,"periodo" => 2, "modalidad" =>"intensiva", "ciclo" => 18, "idioma"=>"es", "curso" => array(1,30,57,95)),
                    array("id_grupo" => 135,"periodo" => 2, "modalidad" =>"intensiva", "ciclo" => 18, "idioma"=>"es", "curso" => array(2,31)),
                    array("id_grupo" => 136,"periodo" => 2, "modalidad" =>"intensiva", "ciclo" => 18, "idioma"=>"es", "curso" => array(2,31))
                );
                break;
            case '50':
                $grupos = array(
                    /**
                     * MARZO TRADICIONAL
                     */
                    //PRIMER AÑO
                    array("id_grupo" => 22, "periodo" => 1 , "modalidad" => "normal", "ciclo" => 13, "idioma"=>"es", "curso" => array(1,30,57,95,2,31)),
                    array("id_grupo" => 24, "periodo" => 1 , "modalidad" => "normal", "ciclo" => 13, "idioma"=>"es", "curso" => array(1,30,57,95,2,31)),
                    array("id_grupo" => 41, "periodo" => 1 , "modalidad" => "normal", "ciclo" => 13, "idioma"=>"es", "curso" => array(1,30,57,95,2,31)),
                    array("id_grupo" => 37, "periodo" => 1 , "modalidad" => "normal", "ciclo" => 13, "idioma"=>"es", "curso" => array(1,30,57,95)),
                    array("id_grupo" => 67, "periodo" => 1 , "modalidad" => "normal", "ciclo" => 13, "idioma"=>"es", "curso" => array(2,31)),
                    //SEGUNDO AÑO
                    array("id_grupo" => 23, "periodo" => 2 ,"modalidad" => "normal", "ciclo" => 13, "idioma"=>"es", "curso" => array(1,30,57,95,2,31)),
                    array("id_grupo" => 20, "periodo" => 2 ,"modalidad" => "normal", "ciclo" => 13, "idioma"=>"es", "curso" => array(1,30,57,95,2,31)),
                    array("id_grupo" => 38, "periodo" => 2 ,"modalidad" => "normal", "ciclo" => 13, "idioma"=>"es", "curso" => array(1,30,57,95)),
                    array("id_grupo" => 39, "periodo" => 2 ,"modalidad" => "normal", "ciclo" => 13, "idioma"=>"es", "curso" => array(1,30,57,95)),
                    array("id_grupo" => 42, "periodo" => 2 ,"modalidad" => "normal", "ciclo" => 13, "idioma"=>"es", "curso" => array(1,30,57,95)),
                    array("id_grupo" => 39, "periodo" => 2 ,"modalidad" => "normal", "ciclo" => 13, "idioma"=>"es", "curso" => array(2,31)), //ex 39
                    array("id_grupo" => 40, "periodo" => 2 ,"modalidad" => "normal", "ciclo" => 13, "idioma"=>"es", "curso" => array(2,31)),
                    /**
                     * AGOSTO TRADICIONAL
                     */
                    //PRIMER AÑO
                    array("id_grupo" => 84, "periodo" => 1 ,"modalidad" =>"normal", "ciclo" => 17, "idioma"=>"es", "curso" => array(1,30,57,95,2,31)),
                    array("id_grupo" => 80, "periodo" => 1 ,"modalidad" =>"normal", "ciclo" => 17, "idioma"=>"es", "curso" => array(1,30,57,95,2,31)),
                    array("id_grupo" => 75, "periodo" => 1 ,"modalidad" =>"normal", "ciclo" => 17, "idioma"=>"es", "curso" => array(1,30,57,95,2,31)),
                    array("id_grupo" => 79, "periodo" => 1 ,"modalidad" =>"normal", "ciclo" => 17, "idioma"=>"es", "curso" => array(1,30,57,95)),
                    array("id_grupo" => 83, "periodo" => 1 ,"modalidad" =>"normal", "ciclo" => 17, "idioma"=>"es", "curso" => array(2,31)),
                    //SEGUNDO AÑO
                    array("id_grupo" => 76, "periodo" => 2,"modalidad" =>"normal", "ciclo" => 17, "idioma"=>"es", "curso" => array(1,30,57,95,2,31)),
                    array("id_grupo" => 81, "periodo" => 2,"modalidad" =>"normal", "ciclo" => 17, "idioma"=>"es", "curso" => array(1,30,57,95,2,31)),
                    array("id_grupo" => 82, "periodo" => 2,"modalidad" =>"normal", "ciclo" => 17, "idioma"=>"es", "curso" => array(1,30,57,95)),
                    array("id_grupo" => 74, "periodo" => 2,"modalidad" =>"normal", "ciclo" => 17, "idioma"=>"es", "curso" => array(1,30,57,95)),
                    array("id_grupo" => 78, "periodo" => 2,"modalidad" =>"normal", "ciclo" => 17, "idioma"=>"es", "curso" => array(1,30,57,95)),
                    array("id_grupo" => 73, "periodo" => 2,"modalidad" =>"normal", "ciclo" => 17, "idioma"=>"es", "curso" => array(2,31)),
                    array("id_grupo" => 77, "periodo" => 2,"modalidad" =>"normal", "ciclo" => 17, "idioma"=>"es", "curso" => array(2,31)),
                    /**
                     * AGOSTO INTENSIVO 2
                     */
                    //PRIMER AÑO
                    array("id_grupo" => 69, "periodo" => 1,"modalidad" =>"intensiva", "ciclo" => 18, "idioma"=>"es", "curso" => array(1,30,57,95,2,31)),
                    array("id_grupo" => 71, "periodo" => 1,"modalidad" =>"intensiva", "ciclo" => 18, "idioma"=>"es", "curso" => array(1,30,57,95,2,31)),
                    array("id_grupo" => 72, "periodo" => 1,"modalidad" =>"intensiva", "ciclo" => 18, "idioma"=>"es", "curso" => array(1,30,57,95,2,31)),
                    array("id_grupo" => 70, "periodo" => 1,"modalidad" =>"intensiva", "ciclo" => 18, "idioma"=>"es", "curso" => array(1,30,57,95)),
                    array("id_grupo" => 142,"periodo" => 1, "modalidad" =>"intensiva", "ciclo" => 18, "idioma"=>"es", "curso" => array(2,31)),
                    //SEGUNDO AÑO
                    array("id_grupo" => 137,"periodo" => 2, "modalidad" =>"intensiva", "ciclo" => 18, "idioma"=>"es", "curso" => array(1,30,57,95,2,31)),
                    array("id_grupo" => 138,"periodo" => 2, "modalidad" =>"intensiva", "ciclo" => 18, "idioma"=>"es", "curso" => array(1,30,57,95,2,31)),
                    array("id_grupo" => 139,"periodo" => 2, "modalidad" =>"intensiva", "ciclo" => 18, "idioma"=>"es", "curso" => array(1,30,57,95)),
                    array("id_grupo" => 140,"periodo" => 2, "modalidad" =>"intensiva", "ciclo" => 18, "idioma"=>"es", "curso" => array(1,30,57,95)),
                    array("id_grupo" => 141,"periodo" => 2, "modalidad" =>"intensiva", "ciclo" => 18, "idioma"=>"es", "curso" => array(1,30,57,95)),
                    array("id_grupo" => 143,"periodo" => 2, "modalidad" =>"intensiva", "ciclo" => 18, "idioma"=>"es", "curso" => array(2,31)),
                    array("id_grupo" => 144,"periodo" => 2, "modalidad" =>"intensiva", "ciclo" => 18, "idioma"=>"es", "curso" => array(2,31)),
                    /**
                     * MARZO  SEMI-INTENSIVO 2
                     */
                    //PRIMER AÑO
                    array("id_grupo" => 56, "periodo" => 1,"modalidad" =>"intensiva", "ciclo" => 13, "idioma"=>"es", "curso" => array(1,30,57,95)),
                    array("id_grupo" => 54, "periodo" => 1,"modalidad" =>"intensiva", "ciclo" => 13, "idioma"=>"es", "curso" => array(1,30,57,95)),
                    array("id_grupo" => 64, "periodo" => 1,"modalidad" =>"intensiva", "ciclo" => 13, "idioma"=>"es", "curso" => array(1,30,57,95)),
                    array("id_grupo" => 41, "periodo" => 1,"modalidad" =>"intensiva", "ciclo" => 13, "idioma"=>"es", "curso" => array(1,30,57,95)),
                    array("id_grupo" => 24,"periodo" => 1, "modalidad" =>"intensiva", "ciclo" => 13, "idioma"=>"es", "curso" => array(1,30,57,95))
                );
                break;
            default:
                $grupos = array(
                    /**
                     * MARZO TRADICIONAL
                     */
                    //PRIMER AÑO
                    array("id_grupo" => 22, "periodo" => 1 , "modalidad" => "normal", "ciclo" => 13, "idioma"=>"es", "curso" => array(1,30,57,95,2,31)),
                    array("id_grupo" => 24, "periodo" => 1 , "modalidad" => "normal", "ciclo" => 13, "idioma"=>"es", "curso" => array(1,30,57,95,2,31)),
                    array("id_grupo" => 41, "periodo" => 1 , "modalidad" => "normal", "ciclo" => 13, "idioma"=>"es", "curso" => array(1,30,57,95,2,31)),
                    array("id_grupo" => 37, "periodo" => 1 , "modalidad" => "normal", "ciclo" => 13, "idioma"=>"es", "curso" => array(1,30,57,95)),
                    array("id_grupo" => 67, "periodo" => 1 , "modalidad" => "normal", "ciclo" => 13, "idioma"=>"es", "curso" => array(2,31)),
                    //SEGUNDO AÑO
                    array("id_grupo" => 23, "periodo" => 2 ,"modalidad" => "normal", "ciclo" => 13, "idioma"=>"es", "curso" => array(1,30,57,95,2,31)),
                    array("id_grupo" => 20, "periodo" => 2 ,"modalidad" => "normal", "ciclo" => 13, "idioma"=>"es", "curso" => array(1,30,57,95,2,31)),
                    array("id_grupo" => 38, "periodo" => 2 ,"modalidad" => "normal", "ciclo" => 13, "idioma"=>"es", "curso" => array(1,30,57,95)),
                    array("id_grupo" => 39, "periodo" => 2 ,"modalidad" => "normal", "ciclo" => 13, "idioma"=>"es", "curso" => array(1,30,57,95)),
                    array("id_grupo" => 42, "periodo" => 2 ,"modalidad" => "normal", "ciclo" => 13, "idioma"=>"es", "curso" => array(1,30,57,95)),
                    array("id_grupo" => 39, "periodo" => 2 ,"modalidad" => "normal", "ciclo" => 13, "idioma"=>"es", "curso" => array(2,31)), //ex 39
                    array("id_grupo" => 40, "periodo" => 2 ,"modalidad" => "normal", "ciclo" => 13, "idioma"=>"es", "curso" => array(2,31)),
                    /**
                     * AGOSTO TRADICIONAL
                     */
                    //PRIMER AÑO
                    array("id_grupo" => 84, "periodo" => 1 ,"modalidad" =>"normal", "ciclo" => 17, "idioma"=>"es", "curso" => array(1,30,57,95,2,31)),
                    array("id_grupo" => 80, "periodo" => 1 ,"modalidad" =>"normal", "ciclo" => 17, "idioma"=>"es", "curso" => array(1,30,57,95,2,31)),
                    array("id_grupo" => 75, "periodo" => 1 ,"modalidad" =>"normal", "ciclo" => 17, "idioma"=>"es", "curso" => array(1,30,57,95,2,31)),
                    array("id_grupo" => 79, "periodo" => 1 ,"modalidad" =>"normal", "ciclo" => 17, "idioma"=>"es", "curso" => array(1,30,57,95)),
                    array("id_grupo" => 83, "periodo" => 1 ,"modalidad" =>"normal", "ciclo" => 17, "idioma"=>"es", "curso" => array(2,31)),
                    //SEGUNDO AÑO
                    array("id_grupo" => 76, "periodo" => 2,"modalidad" =>"normal", "ciclo" => 17, "idioma"=>"es", "curso" => array(1,30,57,95,2,31)),
                    array("id_grupo" => 81, "periodo" => 2,"modalidad" =>"normal", "ciclo" => 17, "idioma"=>"es", "curso" => array(1,30,57,95,2,31)),
                    array("id_grupo" => 82, "periodo" => 2,"modalidad" =>"normal", "ciclo" => 17, "idioma"=>"es", "curso" => array(1,30,57,95)),
                    array("id_grupo" => 74, "periodo" => 2,"modalidad" =>"normal", "ciclo" => 17, "idioma"=>"es", "curso" => array(1,30,57,95)),
                    array("id_grupo" => 78, "periodo" => 2,"modalidad" =>"normal", "ciclo" => 17, "idioma"=>"es", "curso" => array(1,30,57,95)),
                    array("id_grupo" => 73, "periodo" => 2,"modalidad" =>"normal", "ciclo" => 17, "idioma"=>"es", "curso" => array(2,31)),
                    array("id_grupo" => 77, "periodo" => 2,"modalidad" =>"normal", "ciclo" => 17, "idioma"=>"es", "curso" => array(2,31)),
                    /**
                     * AGOSTO INTENSIVO 2
                     */
                    //PRIMER AÑO
                    array("id_grupo" => 69, "periodo" => 1,"modalidad" =>"intensiva", "ciclo" => 18, "idioma"=>"es", "curso" => array(1,30,57,95,2,31)),
                    array("id_grupo" => 71, "periodo" => 1,"modalidad" =>"intensiva", "ciclo" => 18, "idioma"=>"es", "curso" => array(1,30,57,95,2,31)),
                    array("id_grupo" => 72, "periodo" => 1,"modalidad" =>"intensiva", "ciclo" => 18, "idioma"=>"es", "curso" => array(1,30,57,95,2,31)),
                    array("id_grupo" => 70, "periodo" => 1,"modalidad" =>"intensiva", "ciclo" => 18, "idioma"=>"es", "curso" => array(1,30,57,95)),
                    array("id_grupo" => 142,"periodo" => 1, "modalidad" =>"intensiva", "ciclo" => 18, "idioma"=>"es", "curso" => array(2,31)),
                    //SEGUNDO AÑO
                    array("id_grupo" => 137,"periodo" => 2, "modalidad" =>"intensiva", "ciclo" => 18, "idioma"=>"es", "curso" => array(1,30,57,95,2,31)),
                    array("id_grupo" => 138,"periodo" => 2, "modalidad" =>"intensiva", "ciclo" => 18, "idioma"=>"es", "curso" => array(1,30,57,95,2,31)),
                    array("id_grupo" => 139,"periodo" => 2, "modalidad" =>"intensiva", "ciclo" => 18, "idioma"=>"es", "curso" => array(1,30,57,95)),
                    array("id_grupo" => 140,"periodo" => 2, "modalidad" =>"intensiva", "ciclo" => 18, "idioma"=>"es", "curso" => array(1,30,57,95)),
                    array("id_grupo" => 141,"periodo" => 2, "modalidad" =>"intensiva", "ciclo" => 18, "idioma"=>"es", "curso" => array(1,30,57,95)),
                    array("id_grupo" => 143,"periodo" => 2, "modalidad" =>"intensiva", "ciclo" => 18, "idioma"=>"es", "curso" => array(2,31)),
                    array("id_grupo" => 144,"periodo" => 2, "modalidad" =>"intensiva", "ciclo" => 18, "idioma"=>"es", "curso" => array(2,31)),
                    /**
                     * BRASIL AGOSTO INTENSIVO
                     */
                    //PRIMER AÑO
                    array("id_grupo" => 60, "periodo" => 1,"modalidad" => "intensiva", "ciclo" => 18, "idioma"=>"pt", "curso" => array(1,30,57,95,2,31)),
                    array("id_grupo" => 57, "periodo" => 1,"modalidad" => "intensiva", "ciclo" => 18, "idioma"=>"pt", "curso" => array(1,30,57,95,2,31)),
                    array("id_grupo" => 61, "periodo" => 1,"modalidad" => "intensiva", "ciclo" => 18, "idioma"=>"pt", "curso" => array(1,30,57,95,2,31)),
                    array("id_grupo" => 62, "periodo" => 1,"modalidad" => "intensiva", "ciclo" => 18, "idioma"=>"pt", "curso" => array(1,30,57,95)),
                    array("id_grupo" => 168,"periodo" => 1, "modalidad" => "intensiva", "ciclo" => 18, "idioma"=>"pt", "curso" => array(2,31)),
                    //SEGUNDO AÑO
                    array("id_grupo" => 155,"periodo" => 2, "modalidad" => "intensiva", "ciclo" => 18, "idioma"=>"pt", "curso" => array(1,30,57,95,2,31)),
                    array("id_grupo" => 157,"periodo" => 2, "modalidad" => "intensiva", "ciclo" => 18, "idioma"=>"pt", "curso" => array(1,30,57,95,2,31)),
                    array("id_grupo" => 159,"periodo" => 2, "modalidad" => "intensiva", "ciclo" => 18, "idioma"=>"pt", "curso" => array(1,30,57,95)),
                    array("id_grupo" => 161,"periodo" => 2, "modalidad" => "intensiva", "ciclo" => 18, "idioma"=>"pt", "curso" => array(1,30,57,95)),
                    array("id_grupo" => 163,"periodo" => 2, "modalidad" => "intensiva", "ciclo" => 18, "idioma"=>"pt", "curso" => array(1,30,57,95)),
                    array("id_grupo" => 170,"periodo" => 2, "modalidad" => "intensiva", "ciclo" => 18, "idioma"=>"pt", "curso" => array(2,31)),
                    array("id_grupo" => 172,"periodo" => 2, "modalidad" => "intensiva", "ciclo" => 18, "idioma"=>"pt", "curso" => array(2,31))
                );
        }
        //cursos: Gastronomia: 1,30,57,95 Pasteleria: 2,31   Ambos: 1,30,57,95,2,31
        //ciclos: c2016: 13 c2016/17: 17, c in2016: 18
        //idiomas: esp, in, pt
        //array("id_grupo" => 22, "modalidad" => "intensiva", "ciclo" => 18, "idioma"=>"pt", "curso" => array());
        return $grupos;
    }
    //Crear una funcion para desactivar un grupo
    public function desactivarFilial() {
        $codFilial = $this->input->get('codFilial');
        $conexion = $this->load->database((string)$codFilial, true);
        $arrCondiciones = array(
            'cursos.codigo' => array(1,30,57,63,95,2,31),
            'ciclos.codigo' => array(13,17,18),
            'comisiones.modalidad' => array("normal","intensiva"),
            'comisiones.cod_tipo_periodo' => array(1,2));
        $comisionesIntensivas = Vcomisiones::getComisionesFiltro($conexion,$arrCondiciones);
        foreach ($comisionesIntensivas as $comision){
            $codigosAlumnos = Valumnos::getAlumnosComision($conexion, $comision['codigo']);
            foreach ($codigosAlumnos as $codAlumno){
                $usuario = "u-" . $codFilial . "-" . $codAlumno['codAlumno'];
                if($this->isExistPlat($usuario)){
                    $gruposEducUsu = $this->consultarGrupos($usuario);
                    echo "Al usuario: $usuario se le daran de baja los siguientes grupos </br>";
                    foreach ($gruposEducUsu->usuarios->grupos as $grupoId){
                        if($grupoId->estado == 1 || $grupoId->estado == '1') {
                            echo "ID de grupo: $grupoId->id_grupo </br>";
                            $this->modificarUsuarioGrupo($grupoId->id_grupo,$usuario,0);
                        }
                    }
                }
            }
            echo "######################################### </br>";
        }
    }

    /**
     * los alumnos que estan en el grupo 68 cambiarlos para el 39
     */
    public function correccionGrupos() {
        if($this->input->get('debug') == 'false' || $this->input->get('debug') == '0') {
            $debug = false;
        }
        else {
            $debug = true;
        }
        $filial = $this->input->get('filial');
        $grupoNuevo = 39;
        $grupoViejo = 68;
        $conexion = $this->load->database($filial, true);
        $alumnos = Vestadoacademico::getEstadosAcadConGrupos($conexion, null, $grupoViejo);
        foreach ($alumnos as $alumno) {
            $usuario = 'u-'.$filial.'-'.$alumno['cod_alumno'];
            $tieneGrupo = false;
            $estadoGrupo = false;
            $usuarios_grupos = $this->consultarGrupos($usuario, $grupoNuevo);
            foreach ($usuarios_grupos->usuarios->grupos as $grupo) {
                if($grupo->id_grupo == $grupoNuevo) {
                    $tieneGrupo = true;
                    if($grupo->estado == false) {
                        $estadoGrupo = false;
                    }
                    else {
                        $estadoGrupo = true;
                    }
                }
            }
            $this->logger("Usuario $usuario");
            $this->logger("Desactiva grupo $grupoViejo");
            if(!$debug) {
                $this->modificarUsuarioGrupo($grupoViejo, $usuario, false);
            }
            if(!$tieneGrupo) {
                $this->logger("Asigna grupo $grupoNuevo");
                if(!$debug) {
                    $this->asignarUsuarioGrupo($grupoNuevo, $usuario);
                }
            }
            else if(!$estadoGrupo) {
                $this->logger("Activa grupo $grupoNuevo");
                if(!$debug) {
                    $this->modificarUsuarioGrupo($grupoNuevo, $usuario, true);
                }
            }
        }
    }

    public function logger($text) {
        if ($this->getLogIC()) {
            echo date('Y-m-d H:i:s') . ' - ' . $text . "\n";
        }
    }

    public function altaUnicoAlumno() {
        $filial = $this->input->post('filial');
        $alumno = $this->input->post('alumno');
        $conexion = $this->load->database($filial, true);
        $this->scriptGruposEstadosAcademicosPorAlumno($filial, $alumno);
        $this->normalizarUnicoUsuario($conexion, $filial, $alumno, 0);
    }



/*
 * DDL PARA LA GENERAL EN PROD.
 *
CREATE TABLE `general`.grupos_plataforma_educativa (
id int(11) NOT NULL,
nombre varchar(255) NULL,
descripcion text NULL,
estado BOOL NULL,
id_usuario_administrador varchar(255) NULL,
idioma varchar(64) NULL,
responsables_acceden_admin BOOL NULL,
tipo int NULL,
CONSTRAINT grupos_plataforma_educativa_PK PRIMARY KEY (id)
)
ENGINE=InnoDB
DEFAULT CHARSET=utf8
COLLATE=utf8_general_ci;


CREATE TABLE `grupos_comisiones` (
`id_grupo` int(11) NOT NULL,
`cod_comision` int(11) NOT NULL,
PRIMARY KEY (`id_grupo`,`cod_comision`),
KEY `grupos_comisiones_comisiones_FK` (`cod_comision`),
CONSTRAINT `grupos_comisiones_comisiones_FK` FOREIGN KEY (`cod_comision`) REFERENCES `comisiones` (`codigo`),
CONSTRAINT `grupos_comisiones_id_grupo_FK` FOREIGN KEY (`id_grupo`) REFERENCES `general`.`grupos_plataforma_educativa`(`id`)
)
ENGINE=InnoDB
DEFAULT CHARSET=utf8
COLLATE=utf8_general_ci;

CREATE TABLE `general`.grupos_plataforma_materias (
id_grupo int(11) NOT NULL,
cod_materia int(11) NOT NULL,
CONSTRAINT grupos_plataforma_materias_PK PRIMARY KEY (id_grupo,cod_materia),
CONSTRAINT grupos_plataforma_materias_grupos_plataforma_educativa_FK FOREIGN KEY (id_grupo) REFERENCES `general`.grupos_plataforma_educativa(id),
CONSTRAINT grupos_plataforma_materias_materias_FK FOREIGN KEY (cod_materia) REFERENCES `general`.materias(codigo)
)
ENGINE=InnoDB
DEFAULT CHARSET=utf8
COLLATE=utf8_general_ci;

*/

    /**
     * actualizar tablas de estadoacademico, reemplaza grupo 68 por el 39
     * update estadoacademico set id_grupo = 39 where id_grupo = 68
     * update grupos_comisiones set id_grupo = 39 where id_grupo = 68
     */
}
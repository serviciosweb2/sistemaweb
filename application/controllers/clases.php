<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Clases extends CI_Controller {
    
    public function __construct() {
        parent::__construct();
        $this->lang->load(get_idioma(), get_idioma());
    }
    
    public function guardar_materias_clases(){
        if ($this->input->post("filiales") && is_array($this->input->post("filiales")) && $this->input->post("planes_academicos")
                && is_array($this->input->post("planes_academicos")) && $this->input->post("materia") && $this->input->post("modalidad")
                && $this->input->post("clases") && is_array($this->input->post("clases"))
                && $this->input->post("frecuencia") && $this->input->post("duracion")){
            $arrResp = array();
            $arrFiliales = $this->input->post("filiales");
            $arrPlanes = $this->input->post("planes_academicos");
            $materia = $this->input->post("materia");
            $modalidad = $this->input->post("modalidad");
            $arrClases = $this->input->post("clases");
            $duracion = $this->input->post("duracion");
            $frecuencia = $this->input->post("frecuencia");
            $conexion = $this->load->database("default", true);
            $conexion->trans_begin();
            foreach ($arrFiliales as $filial){
                foreach ($arrPlanes as $plan){
                    Vclases::inhabilitarClases($conexion, $filial, $plan, $modalidad, $materia);
                    foreach ($arrClases as $clase){                        
                        if (isset($clase['id']) && $clase['id'] <> ''){
                            $idClase = $clase['id'];
                        } else  {
                            $param = array(
                                "id_filial" => $filial,
                                "id_plan_academico" => $plan,
                                "id_materia" => $materia,
                                "nro_clase" => $clase['numero'],
                                "modalidad" => $modalidad
                            );
                            $arrClasesTemp = Vclases::listarClases($conexion, $param);
                            $idClase = is_array($arrClasesTemp) && isset($arrClasesTemp[0], $arrClasesTemp[0]['id']) ? $arrClasesTemp[0]['id'] : null;
                        }
                        $myClase = new Vclases($conexion, $idClase);
                        $myClase->estado = isset($clase['estado']) ? $clase['estado'] : 'habilitada';
                        $myClase->id_filial = $filial;
                        $myClase->id_materia = $materia;
                        $myClase->id_plan_academico = $plan;
                        $myClase->modalidad = $modalidad;
                        $myClase->nombre = $clase['nombre'];
                        $myClase->nro_clase = $clase['numero'];
                        $myClase->tipo_clase = isset($clase['tipo_clase']) && $clase['tipo_clase'] <> '' ? $clase['tipo_clase'] : 'clase';
                        $myClase->guardarClases();
                    }
                }
                $myPlanAcademico = new Vplanes_academicos($conexion, $plan);
                $myPlanAcademico->setfilialClasesPropiedades($filial, $materia, $duracion, $frecuencia);
            }
            if ($conexion->trans_status()){
                $conexion->trans_commit();
                $arrResp['success'] = "success";
            } else {
                $conexion->trans_rollback();
                $arrResp['error'] = "[".$conexion->_error_number()."] ".$conexion->_error_message();
            }
            echo json_encode($arrResp);
        } else {
            header("Status: 400", true, 400); // Bad Request
        }
    }
    
    public function guardar_video(){
        if ($this->input->post("codigo") && $this->input->post("titulo") && $this->input->post("evento_id")
                && $this->input->post("estado") && $this->input->post("start_time")){
            $arrResp = array();
            $conexion = $this->load->database("default", true);
            $codigo = $this->input->post("codigo");
            $titulo = $this->input->post("titulo");
            $duracion = $this->input->post("duracion");
            $estado = $this->input->post("estado");
            $fechaPublicacion = $this->input->post("start_time");
            $myVideo = new Vvideos($conexion, $codigo);
            $myVideo->titulo = $titulo;
            $myVideo->duracion = $duracion;
            $myVideo->estado = $estado;
            $myVideo->fecha_publicacion = $fechaPublicacion;
            if ($codigo == -1 || $codigo == ''){
                $myVideo->fecha_creacion = $this->input->post("fecha_creacion");
                $myVideo->id_usuario = $this->input->post("id_usuario");
            }
            if ($myVideo->guardarVideos()){
                $arrResp['success'] = "success";
                $arrResp['id'] = $myVideo->getCodigo();
            } else {
                $arrResp['error'] = "[".$conexion->_error_number()."] ".$conexion->_error_message();
            }
            echo json_encode($arrResp);
        } else {
            header("Status: 400", true, 400); // Bad Request
        }        
    }
    
    public function listar_videos(){
        $conexion = $this->load->database("default", true);
        $idVideo = $this->input->post("codigo") ? $this->input->post("codigo") : null;
        $condiciones = array("material_didactico.videos.id" => $idVideo);
        $arrVideos = Vvideos::listar($conexion, $condiciones);
        $resp = array();
        $resp['transport']['aaData'] = $arrVideos;
        $resp['transport']['iTotalRecords'] = count($arrVideos);
        echo json_encode($resp);
    }
    
    public function listar_clases_materiales(){
        $conexion = $this->load->database("default", true);
        $cod_clase = $this->input->post("cod_clase") ? $this->input->post("cod_clase") : null;
        $arrClases = Vclases::listar($conexion, $cod_clase);
        $arrResp = array();
        $arrResp['transport']['clases'] = array(
            "aaData" => $arrClases,
            "iTotalRecords" => count($arrClases)
        );
        if ($cod_clase != null){ // seguir agregando otros materiales como pdf, libros, etc.
            $myClase = new Vclases($conexion, $cod_clase);
            $arrVideos = $myClase->getVideos();
            $arrResp['transport']['clases']['materiales']['videos'] = $arrVideos;
        }
        if ($this->input->post("add_materiales")){          // agregar otros materiales como pdf, libros, etc
            $arrVideos = Vvideos::listar($conexion);
//            echo $conexion->last_query(); die();
            $arrResp['transport']['videos'] = array(
                "aaData" => $arrVideos,
                "iTotalRecords" => count($arrVideos)
            );
        }
        if ($this->input->post("add_planes_academicos")){
            $arrPlanes = Vplanes_academicos::listar($conexion);
            $arrResp['transport']['planes_academicos'] = array(
                "aaData" => $arrPlanes,
                "iTotalRecords" => count($arrPlanes)
            );
        }
        if ($this->input->post("add_materias_plan_academico_clase")){
            $codPlan = null;
            if (isset($arrClases[0]) && isset($arrClases[0]['id_plan_academico'])){
                $codPlan = $arrClases[0]['id_plan_academico'];
            } else if (isset($arrPlanes) && isset($arrPlanes[0]) && isset($arrPlanes[0]['codigo'])){
                $codPlan = $arrPlanes[0]['codigo'];
            }
            $arrMaterias = Vplanes_academicos::listar_materias($conexion, $codPlan);
            $arrResp['transport']['materias'] = array(
                "aaData" => $arrMaterias,
                "iTotalRecords" => count($arrMaterias)
            );
        }
        echo json_encode($arrResp);
    }
    
    public function get_clases_materiales(){
        if ($this->input->post("cod_clase")){
            $arrResp = array();
            $conexion = $this->load->database("default", true);
            $myClase = new Vclases($conexion, $this->input->post("cod_clases"));
            $arrResp['clases']['id'] = $myClase->getCodigo();
            foreach ($myClase as $key => $value){
                $arrResp['clases'][$key] = $value;
            }
            $arrMateriales = Vmateriales_didacticos::listarMateriales_didacticos($conexion, array('id_clase' => $myClase->getCodigo()));
            foreach ($arrMateriales as $material){
                $arrTemp = array();
                $arrTemp['id'] = $material['id'];
                $arrTemp['tipo'] = $material['tipo'];
                $arrTemp['id_clase'] = $material['id_clase'];
                $arrTemp['id_material'] = $material['id_material'];
                $arrMaterial = array();
                if ($material['tipo'] == "video"){
                    $myMaterial = new Vvideos($conexion, $material['id_material']);
                    $arrMaterial['codigo'] = $myMaterial->getCodigo();
                    foreach ($myMaterial as $key => $value){
                        $arrMaterial[$key] = $value;
                    }
                }
                $arrTemp['material'] = $arrMaterial;
                $arrResp['materiales_didacticos'] = $arrTemp;
            }
            $retorno = array();
            $retorno['transport']['clases'] = $arrResp;
            $retorno['transport']['planes_academicos'] = Vplanes_academicos::listar($conexion);
            echo json_encode($retorno);
        } else {
            header("Status: 400", true, 400); // Bad Request  
        }
    }
    
    public function guardar_clase(){
        if ($this->input->post("codigo") && $this->input->post("nombre") && $this->input->post("id_filial") &&
                $this->input->post("id_plan_academico") && $this->input->post("id_materia") && 
                $this->input->post("modalidad") && $this->input->post("nro_clase") && $this->input->post("estado")){
            $conexion = $this->load->database("default", true);
            $conexion->trans_begin();
            $codigo = $this->input->post("codigo");
            $nombre = $this->input->post("nombre");
            $id_filial = $this->input->post("id_filial");
            $id_plan_academico = $this->input->post("id_plan_academico");
            $id_materia = $this->input->post("id_materia");
            $modalidad = $this->input->post("modalidad");
            $nroClase = $this->input->post("nro_clase");
            $estado = $this->input->post("estado");
            $tipoClase = $this->input->post("tipo_clase") ? $this->input->post("tipo_clase") : 'clase';
            $myClase = new Vclases($conexion, $codigo);
            $myClase->id_filial = $id_filial;
            $myClase->id_materia = $id_materia;
            $myClase->id_plan_academico = $id_plan_academico;
            $myClase->modalidad = $modalidad;
            $myClase->tipo_clase = $tipoClase;
            $myClase->nombre = $nombre;
            $myClase->nro_clase = $nroClase;
            $myClase->estado = $estado;
            $myClase->guardarClases();
            if ($this->input->post("agregar_videos") && is_array($this->input->post("agregar_videos"))){
                $arrVideos = $this->input->post("agregar_videos");
                foreach ($arrVideos as $video){
                    $myMaterial = new Vmateriales_didacticos($conexion, $video['id_material']);
                    $myMaterial->id_clase = $myClase->getCodigo();
                    $myMaterial->id_material = $video['id_video'];
                    $myMaterial->tipo = 'video';
                    $myMaterial->estado = $video['estado'];
                    $myMaterial->guardarMateriales_didacticos();
                }
            }
            if ($conexion->trans_status()){
                $conexion->trans_commit();
                $arrResp['success'] = "success";
                $arrResp['id'] = $myClase->getCodigo();                
            } else {
                $conexion->trans_rollback();
                $arrResp['error'] = "[".$conexion->_error_number()."] ".$conexion->_error_message();
            }
            echo json_encode($arrResp);
        } else {
            header("Status: 400", true, 400);
        }
    }
    
    public function guardar_clases(){
        if ($this->input->post("filiales") && $this->input->post("planes_academicos") && $this->input->post("materia")
                && $this->input->post("modalidad") && $this->input->post("nro_clase") && $this->input->post("nombre_clase")
                && is_array($this->input->post("filiales")) && is_array($this->input->post("planes_academicos"))){
            $conexion = $this->load->database("default", true);
            $conexion->trans_begin();
            $arrClases = array();
            $arrVideos = array();
            $arrMateriales = array();
            $arrResp = array();
            if ($this->input->post("id_clase") == -1){
                $filiales = $this->input->post("filiales");
                $planesAcademicos = $this->input->post("planes_academicos");
                $materia = $this->input->post("materia");
                foreach ($filiales as $filial){
                    foreach ($planesAcademicos as $planAcademico){
                        $myClase = new Vclases($conexion);
                        $myClase->id_filial = $filial;
                        $myClase->id_plan_academico = $planAcademico;
                        $myClase->id_materia = $materia;
                        $myClase->modalidad = $this->input->post("modalidad");
                        $myClase->nombre = $this->input->post("nombre_clase");
                        $myClase->nro_clase = $this->input->post("nro_clase");
                        $myClase->estado = $this->input->post('estado') ? $this->input->post('estado') : 'habilitada';
                        $myClase->guardarClases();
                        $arrClases[] = $myClase->getCodigo();
                    }
                }
                if ($this->input->post("materiales_didacticos") && is_array($this->input->post("materiales_didacticos"))){
                    $arrMaterialesDidacticos = $this->input->post("materiales_didacticos");
                    foreach ($arrMaterialesDidacticos as $materialDidactico){
                        if ($materialDidactico['tipo'] == 'video'){
                            $myVideo = new Vvideos($conexion);
                            $myVideo->duracion = $materialDidactico['duracion'];
                            $myVideo->fecha_creacion = $materialDidactico['fecha_creacion'];
                            $myVideo->fecha_publicacion = $materialDidactico['fecha_publicacion'];
                            $myVideo->id_usuario = $materialDidactico['id_usuario'];
                            $myVideo->titulo = $materialDidactico['titulo'];
                            $myVideo->guardarVideos();
                            $myVideo->addPropiedad(array("evento_id" => $materialDidactico['id']));
                            $arrVideos[] = $myVideo->getCodigo();
                            foreach ($arrClases as $clase){
                                $myMaterialDidactico = new Vmateriales_didacticos($conexion);
                                $myMaterialDidactico->id_clase = $clase;
                                $myMaterialDidactico->id_material = $myVideo->getCodigo();
                                $myMaterialDidactico->tipo = 'video';
                                $myMaterialDidactico->guardarMateriales_didacticos();
                                $arrMateriales[] = $myMaterialDidactico->getCodigo();
                            }
                        }
                    }
                }
                if ($conexion->trans_status()){
                    $conexion->trans_commit();
                    $arrResp['success'] = "success";
                    $arrResp['clases'] = $arrClases;
                    $arrResp['videos'] = $arrVideos;
                    $arrResp['materiales_didacticos'] = $arrMateriales;
                } else {
                    $conexion->trans_rollback();
                    $arrResp['error'] = "[".$conexion->_error_number()."] ".$conexion->_error_message();
                }
            } else {
                $arrResp['error'] = 'actualizacion de material didactico no implementado';
            }
            echo json_encode($arrResp);
        } else {
            header("Status: 400", true, 400); // Bad Request
        }
    }
}
<?php

/**
 * Model_aspirantes
 * 
 * Description...
 * 
 * @package model_aspirantes
 * @author ivan <ivan.sys@gmail.com>
 * @version 1.0.0
 */
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Model_aspirantes extends CI_Model {

    var $codigofilial = 0;

    public function __construct($arg) {
        parent::__construct();

        $this->codigofilial = $arg["filial"]["codigo"];
    }
    
    public function getAspirantes($codigo = null, $nombre = null, $apellido = null){
        $conexion = $this->load->database($this->codigofilial, true);
        $condiciones = array();
        if ($nombre != null) $condiciones['aspirantes.nombre'] = $nombre;
        if ($apellido != null) $condiciones['aspirantes.apellido'] = $apellido;
        if ($codigo != null) $condiciones['aspirantes.codigo'] = $codigo;

        /*
        $conexion->select("general.empresas_telefonicas.nombre", false);
        $conexion->from("general.empresas_telefonicas");
        $conexion->join("telefonos", "general.empresas_telefonicas.codigo = telefonos.empresa");
        $conexion->join("alumnos_telefonos", "alumnos_telefonos.cod_telefono = telefonos.codigo");
        $conexion->where("aspirantes_telefonos.cod_aspirante = aspirantes.codigo");
        $conexion->order_by("aspirantes_telefonos.default", "desc");
        $sqTelefonoEmpresa = $conexion->return_query();
        $conexion->resetear();
        $conexion->select("($sqTelefonoEmpresa) AS telefono_empresa", false);
        */

        $conexion->select("CONCAT(telefonos.cod_area,' ',telefonos.numero)", false);
        $conexion->from("telefonos");
        $conexion->join("aspirantes_telefonos", "aspirantes_telefonos.cod_telefono = telefonos.codigo");
        $conexion->where("aspirantes_telefonos.cod_aspirante = aspirantes.codigo");
        $conexion->order_by("aspirantes_telefonos.default", "desc");
        $conexion->limit(1);
        $sqTelefono = $conexion->return_query();
        $conexion->resetear();
        $conexion->select("($sqTelefono) AS telefono", false);
        $conexion->select("general.localidades.nombre AS localidad_nombre", false);
        $conexion->select("general.localidades.provincia_id");
        $conexion->join("general.localidades", "general.localidades.id = aspirantes.cod_localidad");
        $arrAspirantes = Vaspirantes::listarAspirantes($conexion, $condiciones);
        foreach ($arrAspirantes as $key => $aspirante){
            $arrAspirantes[$key]['_fechaalta'] = formatearFecha_pais($aspirante['fechaalta']);
            $arrAspirantes[$key]['_fechanaci'] = $aspirante['fechanaci'] == ''  || $aspirante['fechanaci'] == '0000-00-00' ? '' : formatearFecha_pais($aspirante['fechanaci']);
            $arrAspirantes[$key]['_nombre'] = ucwords(strtolower($aspirante['nombre']));
            $arrAspirantes[$key]['_apellido'] = ucwords(strtolower($aspirante['apellido']));
        }
        return $arrAspirantes;
    }
    
    /**
     * retorna un objeto aspirante.
     * @access public
     * @param int $codigo codigo de aspirante
     * @return Objeto Aspirante
     */
    public function getAspirante($codigo) {
        $conexion = $this->load->database($this->codigofilial, true);
        $aspirante = new Vaspirantes($conexion, $codigo);
        return $aspirante;
    }

    /**
     * guarda un aspirante con todo lo que corresponde
     * un aspirante tiene relacionado telefonos todo en trasaccion.
     * @access public
     * @param Array $arrAspirante todos los datos que salen del formulario aspirante
     * @return repuesta Guardar
     */
    public function guardarAspirante($arrAspirante) {
        $conexion = $this->load->database($this->codigofilial, true);
        $conexion->trans_begin();
        $arrTemp = $arrAspirante['aspirante'];
        $aspirante = new Vaspirantes($conexion, $arrTemp['codigo']);
        $aspirante->apellido = $arrTemp['apellido'];
        $aspirante->barrio = $arrTemp['barrio'];
        $aspirante->calle = $arrTemp['calle'];
        $aspirante->calle_complemento = $arrTemp['calle_complemento'];
        $aspirante->calle_numero = $arrTemp['calle_numero'];
        $aspirante->cod_localidad = $arrTemp['cod_localidad'];
        $aspirante->codpost = $arrTemp['codpost'];
        $aspirante->comonosconocio = $arrTemp['comonosconocio'];
        $aspirante->documento = $arrTemp['documento'];
        $aspirante->email = $arrTemp['email'];
        $aspirante->fechaalta = $arrTemp['fechaalta'];
        $aspirante->fechanaci = $arrTemp['fechanaci'];
        $aspirante->nombre = $arrTemp['nombre'];
        $aspirante->observaciones = $arrTemp['observaciones'];
        $aspirante->tipo = $arrTemp['tipo']==''?'0':$arrTemp['tipo'];
        $aspirante->tipo_contacto = $arrTemp['tipo_contacto'];
        $aspirante->usuario_creador = $arrTemp['usuario_creador'];
        $aspirante->guardarAspirantes();
        
        if (isset($arrAspirante['aspirante']['cursos_interes']) && is_array($arrAspirante['aspirante']['cursos_interes'])){
            $aspirante->setCursosDeInteres($arrAspirante['aspirante']['cursos_interes'], $arrAspirante['aspirante']['turnos'],$arrAspirante['aspirante']['periodos'],$arrAspirante['aspirante']['modalidades']);
        }
        
        /*
         * Habría que haberle puesto un ID incremental o algo así.
         * 
        $cod_telefono = '';
        if($arrAspirante['aspirante']['codigo'] == -1){
            $cod_telefono = -1;
        }else{
           $telefono = $aspirante->getCodigoTelefonoDefault();
           $cod_telefono = isset($telefono[0]) ? $telefono[0]['cod_telefono'] : -1;
        }
        */

        //GUARDO TELEFONO(S)
        if(count($arrAspirante['telefonos']) > 0){
            $aspirante->vaciarTelefonos();
            foreach($arrAspirante['telefonos'] as $tele){
                $objTelefono = new Vtelefonos($conexion, -1); //NUNCA HACE UPDATE DE TELEFONOS, JAJAJAJAJAJA. Soy cruel :v.
                $arrGuardarTelefono = array(
                    "cod_area"=>$tele['cod_area'],
                    "numero"=>$tele['numero'],
                    "tipo_telefono"=>isset($tele['tipo_telefono']) ? $tele['tipo_telefono'] : '',
                    "empresa"=>isset($tele['empresa']) ? $tele['empresa'] : '',
                    "baja"=>0,
                    "numero_old"=> '',
                    "cod_area_old"=> '',
                    "pais"=> ''
                );

                $objTelefono->setTelefonos($arrGuardarTelefono);
                $objTelefono->guardarTelefonos();
                //Dice "set", pero de joda, hace insert(s).
                $aspirante->setTelefonosAspirante($objTelefono->getCodigo(),$tele['default']);


            }
            /*
            $arrGuardarTelefono = array(
                "cod_area"=>$arrAspirante['telefonos']['cod_area'],
                "numero"=>$arrAspirante['telefonos']['numero'],
                "tipo_telefono"=>isset($arrAspirante['telefonos']['tipo_telefono']) ? $arrAspirante['telefonos']['tipo_telefono'] : '',
                "empresa"=>isset($arrAspirante['telefonos']['empresa']) ? $arrAspirante['telefonos']['empresa'] : '',
                "baja"=>0
            );
            
            $default = 1;
            if($cod_telefono == -1){
            }
            $aspirante->updateTelefonoAspirante($objTelefono->getCodigo(),$default);
             * 
             */
        }
       
        $estadotran = $conexion->trans_status();
        if ($estadotran === FALSE) {
            $conexion->trans_rollback();
        } else {
            $conexion->trans_commit();
        }
        $arrRespuesta = array("cod_aspirante" => $aspirante->getCodigo());
        return class_general::_generarRespuestaModelo($conexion, $estadotran, $arrRespuesta);
    }

    /**
     * retorna todos los telefonos de un aspirante.
     * @access public
     * @param int $codigo_aspirante codigo del aspirante
     * @return Array de telefonos.
     */
    public function getTelefonos($codigo_aspirante) {
        $conexion = $this->load->database($this->codigofilial, true);
        $aspirante = new Vaspirantes($conexion, $codigo_aspirante);
        return $arrTelefonos = $aspirante->getTelefonos();
    }

    /* retorna todos los telefonos del aspirante, no como el anterior que retorna
       nada mas los telefonos default. Este es la posta.
    */
    public function getTodosLosTelefonos($codigo_aspirante){
        $conexion = $this->load->database($this->codigofilial, true);
        $aspirante = new Vaspirantes($conexion, $codigo_aspirante);
        return $arrTelefonos = $aspirante->getTodosLosTelefonos();
    }
    /* Borra todos los telefonos del aspirante */
   
    public function borrarTelefonos($codigo_aspirante){
        $conexion = $this->load->database($this->codigofilial, true);
        $aspirante = new Vaspirantes($conexion, $codigo_aspirante);
        $arrTelefonos = $aspirante->getTodosLosTelefonos();
        $aspirante->vaciarTelefonosAspirante($codigo_aspirante);
        foreach($arrTelefonos as $tel){
            $aspirante->borrarTelefono($tel['cod_telefono']);
        }
    }

    /**
     * Recupera la lista de aspirante segun filtro de datatable-
     * @access public
     * @param Array $arrFiltros filtro de lista aspirantes.
     * @return Array de  aspirantes.
     */
    public function listarAspirantes($arrFiltros, $separador, $fechaDesde = null, $fechaHasta = null, $curso = null, 
                $tipoContacto = null, $medio = null, $turno = null, $esAlumno = null) {
        $conexion = $this->load->database($this->codigofilial, true);
        $arrCondindiciones = array();
        $this->load->helper('alumnos');

        $filial = $this->session->userdata('filial');
        $pais = $filial['pais'];

        if ($arrFiltros["sSearch"] != "") {
            $arrCondindiciones = array(
                "nombre_apellido" => $arrFiltros["sSearch"],
                "aspirantes.codigo"=>$arrFiltros["sSearch"],
                "aspirantes.documento"=>$arrFiltros["sSearch"],
                "email" => $arrFiltros["sSearch"],
                "aspirantes.tipo_contacto"=>$arrFiltros["sSearch"]
            );
        }
        $arrLimit = array();
        if ($arrFiltros["iDisplayStart"] != "" and $arrFiltros["iDisplayLength"] != "") {
            $arrLimit = array(
                "0" => $arrFiltros["iDisplayStart"],
                "1" => $arrFiltros["iDisplayLength"]
            );
        }
        $arrSort = array();
        if ($arrFiltros["SortCol"] != "" and $arrFiltros["sSortDir"] != "") {
            $arrSort = array(
            "0" => $arrFiltros["SortCol"],
            "1" => $arrFiltros["sSortDir"]
            );
        }        
        $datos = Vaspirantes::listarAspiranteDataTable($conexion, $arrCondindiciones, $arrLimit, $arrSort, false, $separador, 
                $fechaDesde, $fechaHasta, $curso, $tipoContacto, $medio, $turno, $esAlumno, $pais);
        $contar = Vaspirantes::listarAspiranteDataTable($conexion, $arrCondindiciones, null, null, true, $separador,
                $fechaDesde, $fechaHasta, $curso, $tipoContacto, $medio, $turno, $esAlumno);
        $retorno = array(
            "sEcho" => $arrFiltros["sEcho"],
            "iTotalRecords" => $contar,
            "iTotalDisplayRecords" => $contar,
            "aaData" => array()
        );
        $rows = array();

        if($pais == 2)
        {
            foreach ($datos as $row) {
                $rows[] = array(
                    $row["codigo"],
                    $row['pasado_alumno'] == " " ? "no es alumno" : $row['pasado_alumno'],
                    inicialesMayusculas($row["nombre_apellido"]),
                    $row['aspirante_documento'],
                    $row["email"],
                    $row['telefono_empresa'],
                    $row['telefono'],
                    lang($row["tipo_contacto"]),
                    $row['como_nos_conocio'],
                    $row['nombre_curso'],
                    $row['turno'],
                    formatearFecha_pais($row["fechaalta"]),
                    $row['nombre_usuario']
                );
            }
        }
        else
        {
            foreach ($datos as $row) {
                $rows[] = array(
                    $row["codigo"],
                    $row['pasado_alumno'] == " " ? "no es alumno" : $row['pasado_alumno'],
                    inicialesMayusculas($row["nombre_apellido"]),
                    $row['aspirante_documento'],
                    $row["email"],
                    //$row['telefono_empresa'],
                    $row['telefono'],
                    lang($row["tipo_contacto"]),
                    $row['como_nos_conocio'],
                    $row['nombre_curso'],
                    $row['turno'],
                    formatearFecha_pais($row["fechaalta"]),
                    $row['nombre_usuario']
                );
            }
        }

        $retorno['aaData'] = $rows;
        return $retorno;
    }
    public function getListadoCentroReportes($idFilial, $arrLimit = null, $arrSort = null, $search = null, $searchFields = null, $fechaDesde = null,
            $fechaHasta = null){
        $conexion = $this->load->database($idFilial, true);
        $cantRegistros = Vaspirantes::getListadoCentroReportes($conexion, $arrLimit, $arrSort, true, $search, $searchFields, $fechaDesde, $fechaHasta);
        $registros = Vaspirantes::getListadoCentroReportes($conexion, $arrLimit, $arrSort, false, $search, $searchFields, $fechaDesde, $fechaHasta);
        $arrResp = array();
        $arrResp['total_rows'] = $cantRegistros;
        $arrResp['rows'] = $registros;
        return $arrResp;        
    }
    
    public function getDetallePresupuestos($idFilial, $codAspirante){
        $conexion = $this->load->database($idFilial, true);
        $myAspirante = new Vaspirantes($conexion, $codAspirante);
        $arrResp = $myAspirante->getPresupuestosAspirante($conexion);
        return $arrResp;
    }
    
    public function getPresupuestosAspirante($cod_aspirante){
        $conexion = $this->load->database($this->codigofilial,true);
        $this->load->helper('filial');
        $objPresupuesto = new Vaspirantes($conexion, $cod_aspirante);
        $presupuestoAspirante = $objPresupuesto->getPresupuestosAspirante();
        $arrayCodPresupuesto = array();
        $presupuestoFormateado = '';
        foreach($presupuestoAspirante as $presu){
            $arrayCodPresupuesto[]=$presu['codigo_presupuesto'];
        }
        $codigos_presupuesto = array_unique($arrayCodPresupuesto);
        foreach($presupuestoAspirante as $presupuesto){               
            if(in_array($presupuesto['codigo_presupuesto'], $codigos_presupuesto)){                        
                $presupuestoFormateado[$presupuesto['codigo_presupuesto']]['detalle'][]= array(
                    'fecha'=>  formatearFecha_pais($presupuesto['fecha']),
                    'codigo_plan'=>$presupuesto['codigo_plan'],
                    'codigo_financiacion'=>$presupuesto['codigo_financiacion'],
                    'codigo_concepto'=>$presupuesto['concepto_codigo'],
                    'nombre_es'=>$presupuesto['nombre_es'],
                    'nombre_pt'=>$presupuesto['nombre_pt'],
                    'nombre_in'=>$presupuesto['nombre_in'],
                    'nombre_plan'=>$presupuesto['nombre_plan'],
                    'nombre_concepto'=>lang($presupuesto['nombre_concepto']),
                    'cantidad_cuotas'=>$presupuesto['cantidad_cuotas'],
                    'valor_total_concepto'=>  formatearImporte($presupuesto['valor_total_concepto'])
                );                        
            }                
        }
        return $presupuestoFormateado;
    }
    
    public function ConsultarAspiranteAlumno($cod_aspirante){
        $conexion = $this->load->database($this->codigofilial,true);
        $objAspirante = new Vaspirantes($conexion, $cod_aspirante);
        $consultaAspAlu = $objAspirante->consultarAspiranteAlumno();
        return $consultaAspAlu;
    }
    
    public function getNombreAspirante($cod_aspirante){
        $conexion = $this->load->database($this->codigofilial,true);
        $this->load->helper('alumnos');
        $objAspirante = new Vaspirantes($conexion, $cod_aspirante);
        $nombreAspirante = formatearNombreApellido($objAspirante->nombre, $objAspirante->apellido);
        $nombreApellido = inicialesMayusculas($nombreAspirante);
        return $nombreApellido;
    }
    //Ticket 4524 - agregar turnos
    public function getTurnos()
    {
        $conexion = $this->load->database($this->codigofilial,true);
        $estado = "habilitado";
        $arrTurnos = Vaspirantes::getTurnos($conexion, $estado);
        return $arrTurnos;
    }
    
    public function getModalidades($codigo)
    {
        $conexion = $this->load->database($this->codigofilial,true);
        $arrModalidades = Vaspirantes::getModalidades($conexion, $codigo);
        return $arrModalidades;
    }
    
    public function getTiposContacto(){
        $arrResp = array();
        $arrResp[] = array("id" => "PRESENCIAL", "value" => lang("PRESENCIAL"));
        $arrResp[] = array("id" => "EMAIL", "value" => lang("EMAIL"));
        $arrResp[] = array("id" => "TELEFONO", "value" => lang("TELEFONO"));
        return $arrResp;
    }
}

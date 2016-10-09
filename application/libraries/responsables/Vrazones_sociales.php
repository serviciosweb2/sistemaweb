<?php

/**
 * Class Vrazones_sociales
 *
 * Class  Vrazones_sociales maneja todos los aspectos de alumnos
 *
 * @package  SistemaIGA
 * @subpackage Alumnos
 * @author   Ivan berthillod <ivan.sys@gmail.com>
 * @author   Aquiles Gonzalez <sistemas1@iga-la.net>
 * @version  $Revision: 1.1 $
 * @access   private
 */
class Vrazones_sociales extends Trazones_sociales {

    function __construct(CI_DB_mysqli_driver $conexion, $codigo = null) {
        parent::__construct($conexion, $codigo);
    }

    public function getCursoRazonSocial() {
        $this->oConnection->select('razones_sociales.codigo as cod_razon, general.cursos.codigo as cod_curso, general.cursos.nombre_es, general.cursos.nombre_in, general.cursos.nombre_pt');
        $this->oConnection->from('alumnos');
        $this->oConnection->join('alumnos_razones', 'alumnos_razones.cod_alumno = alumnos.codigo');
        $this->oConnection->join('razones_sociales', 'razones_sociales.codigo = alumnos_razones.cod_razon_social');
        $this->oConnection->join('matriculas', 'matriculas.cod_alumno = alumnos.codigo');
        $this->oConnection->join('general.cursos', 'general.cursos.codigo = matriculas.cod_curso');
        $this->oConnection->join('cursos_habilitados', 'cursos_habilitados.cod_curso = general.cursos.codigo');
        $this->oConnection->where('razones_sociales.codigo', $this->codigo);
        $this->oConnection->where('cursos_habilitados.baja', 0);
        $this->oConnection->group_by('matriculas.codigo');
        $query = $this->oConnection->get();
        return $query->result_array();
    }

    public function updateRazonResponsables($cod_razon_social, $arrRazonesResponsable) {
        $this->oConnection->where('razones_sociales.codigo', $cod_razon_social);
        $this->oConnection->update('razones_sociales', $arrRazonesResponsable);
    }

    public function getDireccionFormal() {
        $retorno = $this->direccion_calle . " " . $this->direccion_numero;
        if ($this->direccion_complemento <> '') {
            $retorno .= " ({$this->direccion_complemento})";
        }
        return $retorno;
    }

    public function telfonoRazonSocial() {
        $this->oConnection->select('telefonos.*');
        $this->oConnection->from('telefonos');
        $this->oConnection->join('razones_sociales_telefonos', 'razones_sociales_telefonos.cod_telefono = telefonos.codigo');
        $this->oConnection->where('razones_sociales_telefonos.cod_razon_social', $this->codigo);
        $query = $this->oConnection->get();
        return $query->result_array();
    }

    public function setTelefonoRazon($cod_telefono) {
        $arrayTelRazon = array(
            "cod_razon_social" => $this->codigo,
            "cod_telefono" => $cod_telefono
        );
        $this->oConnection->insert('razones_sociales_telefonos', $arrayTelRazon);
    }

    static function listarRazonesDataTable(CI_DB_mysqli_driver $conexion, $arrCondindicioneslike, $arrLimit = null, $arrSort = null, $contar = false) {

        $conexion->select("general.empresas_telefonicas.nombre", false);
        $conexion->from("general.empresas_telefonicas");
        $conexion->join("telefonos", "general.empresas_telefonicas.codigo = telefonos.empresa");
        $conexion->join("razones_sociales_telefonos", "razones_sociales_telefonos.cod_telefono = telefonos.codigo");
        //$conexion->join("razones_sociales", "razones_sociales.codigo = razones_sociales_telefonos.cod_razon_social");
        $conexion->where("razones_sociales_telefonos.cod_razon_social = razones_sociales.codigo");
        $conexion->order_by("razones_sociales_telefonos.default", "desc");

        $sqTelefonoEmpresa = $conexion->return_query();
        $conexion->resetear();


        $conexion->select("CONCAT(telefonos.cod_area, ' ', telefonos.numero)", false);
        $conexion->from("telefonos");
        $conexion->join("razones_sociales_telefonos", "razones_sociales_telefonos.cod_telefono = telefonos.codigo");
        //$conexion->join("razones_sociales", "razones_sociales.codigo = razones_sociales_telefonos.cod_razon_social");
        $conexion->where("razones_sociales_telefonos.cod_razon_social = razones_sociales.codigo");
        $conexion->order_by("razones_sociales_telefonos.default", "DESC");
        //habia un error al devolver la consulta sin ningun filtro
        $conexion->limit(1);
        //habia un error al devolver la consulta sin ningun filtro
        $sqTelefono = $conexion->return_query();
        $conexion->resetear();


        $conexion->select("razones_sociales.*, general.documentos_tipos.nombre, general.condiciones_sociales.condicion as nbrecondicion, general.empresas_telefonicas.nombre as telefono_empresa, telefonos.cod_area as tel_cod_area, telefonos.numero as tel_numero");
        //$conexion->select("($sqTelefonoEmpresa) AS telefono_empresa", false);
        //$conexion->select("($sqTelefono) AS telefono", false);
        $conexion->from("razones_sociales");
        $conexion->join("general.documentos_tipos", "razones_sociales.tipo_documentos = general.documentos_tipos.codigo", "left");
        $conexion->join("general.condiciones_sociales", "razones_sociales.condicion = general.condiciones_sociales.codigo", "left");

        $conexion->join("razones_sociales_telefonos", "razones_sociales_telefonos.cod_razon_social = razones_sociales.codigo", "left");
        $conexion->join("telefonos", "telefonos.codigo = razones_sociales_telefonos.cod_telefono", "left");
        $conexion->join("general.empresas_telefonicas", "general.empresas_telefonicas.codigo = telefonos.empresa", "left");
/*
        LEFT JOIN `razones_sociales_telefonos` ON `razones_sociales_telefonos`.`cod_razon_social` = `razones_sociales`.`codigo`
LEFT JOIN `telefonos` ON `telefonos`.`codigo` = `razones_sociales_telefonos`.`cod_telefono`
LEFT JOIN `general`.`empresas_telefonicas` ON `general`.`empresas_telefonicas`.`codigo` = `telefonos`.`empresa`
  */

        if ($arrCondindicioneslike != null) {
            foreach ($arrCondindicioneslike as $key => $value) {
                $conexion->or_like($key, $value);
            }
        }

        if ($arrLimit != null) {
            $conexion->limit($arrLimit[1], $arrLimit[0]);
        }
        if ($arrSort != null) {
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

    public function guardar($razon_social, $fecha_alta, $tipo_identificacion, $nro_identificacion, $cod_localidad, $direccion, $numero, 
            $complemento, $email, $cod_postal, $condicion, $inicioActividades, $usuarioCreador) {
        $this->razon_social = $razon_social;
        $this->fecha_alta = $fecha_alta != null ? $fecha_alta : date('Y-m-d H:i:s');
        $this->tipo_documentos = $tipo_identificacion;
        $this->documento = $nro_identificacion;
        $this->cod_localidad = $cod_localidad;
        $this->direccion_calle = $direccion;
        $this->direccion_numero = $numero;
        $this->direccion_complemento = $complemento;
        $this->email = $email;
        $this->codigo_postal = $cod_postal;
        $this->condicion = $condicion;
        $this->inicio_actividades = $inicioActividades;
        $this->usuario_creador = $usuarioCreador;        
        $this->guardarRazones_sociales();
    }

    public function baja() {
        $this->baja = '1';
        $this->guardarRazones_sociales();
    }

    public function alta() {
        $this->baja = '0';
        $this->guardarRazones_sociales();
    }

    public function validarAFacturar(array &$arrListaErrores = null){
        if ($arrListaErrores == null){
            $arrListaErrores = array();
        }
        $retorno = true;
        $arrTiposDocumentosNoValidos = array(5, 12, 13, 14, 15, 16, 17, 18, 19, 20); // agregar documentos no validos para receptor de facturas (ejemplo RG)
        if (in_array($this->tipo_documentos, $arrTiposDocumentosNoValidos)){ // 
            $retorno = false;
            $arrListaErrores[] = lang("el_identificador_fiscal_de_la_razon_social_no_es_apto_para_facturar");
        }
        $myTipoDocumento = new Vdocumentos_tipos($this->oConnection, $this->tipo_documentos);
        $arrTiposDocumentosEmpresas = array(3, 4, 6); // seguir agregando para no conciderar inicio_actividades como fecha nacimiento
        if (!in_array($this->tipo_documentos, $arrTiposDocumentosEmpresas)){ // se considera persona fisica y debe verificarse la edad            
            $myPais = new Vpaises($this->oConnection, $myTipoDocumento->pais);
            $edadMayoria = $myPais->getAniosMayoriaEdad();
            $fechaNacimiento = $this->inicio_actividades;
            $date_parts2 = explode("-", $fechaNacimiento);
            $start_date = gregoriantojd(date("m"), date("d"), date("Y"));
            $end_date = gregoriantojd($date_parts2[1], $date_parts2[2], $date_parts2[0]);
            $edad = ($start_date - $end_date) / 365;
            if ($edadMayoria > $edad){
                $retorno = false;
                $arrListaErrores[] = lang("la_razon_social_pertenece_a_un_menor_de_edad");
            }
        }
        $arrCondiciones = $myTipoDocumento->getCondicionesSociales();
        $condicionPermitida = false;
        $i = 0;
        while ($i < count($arrCondiciones) && $condicionPermitida == false){
            $condicionPermitida = $arrCondiciones[$i]['codigo'] == $this->condicion;
            $i++;
        }
        if (!$condicionPermitida){
            $arrListaErrores[] = lang("el_identificador_fiscal_y_la_condicion_de_la_rzon_social_no_coinciden");
            $retorno = false;
        }
        return $retorno;
    }
    
    static function getRazonesSocialesNoDefault(CI_DB_mysqli_driver $conexion, $cod_alumno = null, $cod_razon = null) {
        $conexion->select("razones_sociales.codigo, razones_sociales.razon_social");
        $conexion->from("razones_sociales");
//        $conexion->where("razones_sociales.codigo NOT IN ((SELECT alumnos_razones.cod_razon_social FROM alumnos_razones WHERE alumnos_razones.`default` = 1))
//        AND razones_sociales.codigo NOT IN ((SELECT profesores_razones.cod_razon FROM profesores_razones WHERE profesores_razones.`default` = 1))
//        AND razones_sociales.codigo NOT IN ((SELECT proveedores.cod_razon_social FROM proveedores))");
        if ($cod_alumno != null) {
            $conexion->where("razones_sociales.codigo NOT IN ((SELECT alumnos_razones.cod_razon_social FROM alumnos_razones WHERE cod_alumno = $cod_alumno))");
        }
        if ($cod_razon != null) {
            $conexion->where("razones_sociales.codigo", $cod_razon);
        }
        $conexion->where('baja', 0);
        $query = $conexion->get();// echo $conexion->last_query();
        $arrResp = $query->result_array();
        return $arrResp;
    }

    static function getRazonSocial(CI_DB_mysqli_driver $conexion, $cod_razon) {
        $conexion->select('razones_sociales.*, general.documentos_tipos.nombre as tipoid, general.condiciones_sociales.condicion as nombrecondicion');
        $conexion->from('razones_sociales');
        $conexion->join('general.documentos_tipos', 'general.documentos_tipos.codigo = razones_sociales.tipo_documentos');
        $conexion->join('general.condiciones_sociales', 'general.condiciones_sociales.codigo = razones_sociales.condicion');
        $conexion->where('razones_sociales.codigo', $cod_razon);
        $query = $conexion->get();
        $arrResp = $query->result_array();
        return $arrResp;
    }

    public function actualizar_facturacion(array $razonesAnteriores){
        if (count($razonesAnteriores) > 0){
            $this->oConnection->where_in("estado", array(Vfacturas::getEstadoError(), Vfacturas::getEstadoPendiente()));
            $this->oConnection->where_in("codrazsoc", $razonesAnteriores);
            return $this->oConnection->update("facturas", array("codrazsoc" => $this->codigo));            
        } else {
            return true;
        }
    }    
}
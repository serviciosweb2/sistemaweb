<?php

/**
* Class Vresponsables
*
*Class  Vresponsables maneja todos los aspectos de alumnos
*
* @package  SistemaIGA
* @subpackage Alumnos
* @author   Ivan berthillod <ivan.sys@gmail.com>
* @author   Aquiles Gonzalez <sistemas1@iga-la.net>
* @version  $Revision: 1.1 $
* @access   private
*/
class Vresponsables extends Tresponsables{

    function __construct(CI_DB_mysqli_driver $conexion, $codigo = null) {
        parent::__construct($conexion, $codigo);
    }
    
     /**
     * retorna los telefonos de los responsables de los alumnos.
     *@access public
     * return array con los telefonos.
     */
    function getTelefonos(){
        $this->oConnection->select('telefonos.*, responsables_telefonos.default');
        $this->oConnection->from($this->nombreTabla);
        $this->oConnection->join('responsables_telefonos', 'responsables_telefonos.id_responsable = '   . $this->nombreTabla . '.codigo');
        $this->oConnection->join('telefonos', 'responsables_telefonos.id_telefono= telefonos.codigo');
        $this->oConnection->where($this->nombreTabla .'.codigo', $this->codigo); 
        $this->oConnection->where('telefonos.baja', 0); 
        $query = $this->oConnection->get();    
        return $query->result_array();  
    }
    
     /**
     * setea telefono a responsable por el id_responsable
     *@access public
     * return array con telefono del responsable
     */
    function  setTelefono($cod_telefono,$default){
            $arrtel =  array(
                    "id_responsable"=>  $this->codigo,
                    "id_telefono" =>$cod_telefono,
                    "default"=>$default
            );
        $this->oConnection->insert('responsables_telefonos', $arrtel); 
     }
     
     function updateTelefonosResponsables($cod_telefono, $default){
         $arrtel =  array(
            "id_responsable"=>  $this->codigo,
            "id_telefono" =>$cod_telefono,
            "default"=>$default
         );
         $this->oConnection->where('responsables_telefonos.id_responsable',  $this->codigo);
         $this->oConnection->where('responsables_telefonos.id_telefono',$cod_telefono);
         $this->oConnection->update('responsables_telefonos',$arrtel);
         
     }
    
    public function setRazonSocialResponsable($razResponsable){
        $this->oConnection->insert('responsables_razones',$razResponsable);
    }
    
    static function buscarPorIdentificacion($conexion,$tipo_identificacion,$numero_identificacion)
    {
        $conexion->select('razones_sociales.*');
        $conexion->select('responsables_razones.*');
        $conexion->select('responsables.*');
        
        $conexion->from('razones_sociales');
        
        $conexion->join('responsables_razones','responsables_razones.cod_razon_social = razones_sociales.codigo');
        $conexion->join('responsables','responsables_razones.cod_responsable = responsables.codigo');
        
        $conexion->where('razones_sociales.tipo_documentos',$tipo_identificacion);
        $conexion->where('razones_sociales.documento', $numero_identificacion);
        $conexion->where('responsables.baja',0);
        
        $query = $conexion->get();
        $arrResp = $query->result_array();
        return $arrResp;
        
    }
    
    public function getRazonSocial()
    {
        $this->oConnection->select('responsables_razones.*');
        $this->oConnection->select('razones_sociales.*');
        $this->oConnection->select("CONCAT(razones_sociales.direccion_calle, ' ', razones_sociales.direccion_numero) as direccion",false);
        
        $this->oConnection->from('responsables_razones');
        
        $this->oConnection->join('razones_sociales','responsables_razones.cod_razon_social = razones_sociales.codigo');
        
        $this->oConnection->where('responsables_razones.cod_responsable', $this->codigo);
        $this->oConnection->where('razones_sociales.baja',0);
        
        $query = $this->oConnection->get();
        $arrResp = $query->result_array();
        return $arrResp;
    }
    
    static function listarResponsablesDataTable(CI_DB_mysqli_driver $conexion, $arrCondindicioneslike, $arrLimit = null, $arrSort = null, $contar = false, $separador = null) {
        
        $nombreApellido = formatearNomApeResponQuery();
        $conexion->select("CONCAT($nombreApellido) as nombre_apellido, responsables.*", false)->from("responsables");
        $conexion->select("responsables.baja as responsable_baja");
        $conexion->select("responsables_razones.*");
        $conexion->select("razones_sociales.email,razones_sociales.tipo_documentos");
        $conexion->select("CONCAT(documentos_tipos.nombre,' ',razones_sociales.documento) as nombre_identificacion",false);
        $conexion->select("CONCAT(razones_sociales.direccion_calle,' ',razones_sociales.direccion_numero) as direccion",false);
        $conexion->select("condiciones_sociales.condicion as nombre_condicion");
        $conexion->select("razones_sociales.codigo as cod_razon_social");
        $conexion->join("responsables_razones","responsables_razones.cod_responsable = responsables.codigo");
        $conexion->join("razones_sociales","razones_sociales.codigo = responsables_razones.cod_razon_social");
        $conexion->join("general.documentos_tipos" ,"general.documentos_tipos.codigo = razones_sociales.tipo_documentos");
        $conexion->join("general.condiciones_sociales" ,"general.condiciones_sociales.codigo = razones_sociales.condicion");
        
        if (count($arrCondindicioneslike) > 0) 
        {
            $arrTemp = array();
            foreach ($arrCondindicioneslike as $key => $value)
            {
                if ($key == 'nombre_apellido') {
                    $arrTemp[] = "REPLACE(nombre_apellido, '$separador ',' ') LIKE REPLACE('%$value%', '$separador ',' ')";
                } else {
                    $arrTemp[] = "$key LIKE '%$value%'";
                }
            }
            if (count($arrTemp) > 0) {
                $having = "(" . implode(" OR ", $arrTemp) . ")";
                $conexion->having($having);
            }
        }
        if ($arrLimit != null) {
            $conexion->limit($arrLimit[1], $arrLimit[0]);
        }
        if ($arrSort != null) {
            $conexion->order_by($arrSort["0"], $arrSort["1"]);
        }
        $query = $conexion->get();
//       echo $conexion->last_query();
        if ($contar) {
            $arrResp = $query->num_rows();
        } else {
            $arrResp = $query->result_array();
        }
        return $arrResp;
    }
}


<?php
/**
* Class class_general
*
* Clase Base para la creacion de las clases de comportamiento comun
*
* @package  SistemaIGA
* @subpackage Alumnos
* @author   Ivan berthillod <ivan.sys@gmail.com>
* @author   Aquiles Gonzalez <sistemas1@iga-la.net>
* @version  $Revision: 1.1 $
* @access   private
*/
class class_general{
    
    /* PROTECTED FUNCTIONS */
    
    /**
     * Guarda el nuevo objeto en la base de datos y le asigna el codigo obtenido
     * 
     * @return boolean
     */
    protected function _insertar(){
        if ($this->oConnection->insert($this->nombreTabla, $this->_getArrayDeObjeto())){
            $primary = $this->primaryKey;
            $this->$primary = $this->oConnection->insert_id();
            return true;
        } else {
            return false;
        }
    }

    /**
     * Actualiza los valores de las propiedades del objeto en la base de daots
     * 
     * @return boolean
     */
    protected function _actualizar(){
        $primary = $this->primaryKey;
        $primaryVal = $this->$primary;
        return $this->oConnection->update($this->nombreTabla, $this->_getArrayDeObjeto(), "$primary = $primaryVal");
    }    
    
    
    /**
     * Devuelve los campos de la tabla correspondiente al codigo enviado por parametros
     * 
     * @param integer $codigo   el codigo del objeto 
     * @return array
     */
    protected function _constructor($codigo){
        $query = $this->oConnection->select('*')
            ->from($this->nombreTabla)
            ->where(array(
                $this->primaryKey => "$codigo"
            ))->get();
          
         $arrResp = $query->result_array();
   
        return $arrResp;
    }
    
    /* PUBLIC FUNCTIONS */
    
    /**
     * Guarda un objeto nuevo o actualiza uno ya existente en la base de datos 
     * 
     * @return boolean
     */
    protected function _guardar(){
        $primary = $this->primaryKey;
        if ($this->$primary == '' || $this->$primary < 1){
            return $this->_insertar();
        } else {
            return $this->_actualizar();
        }
    }
    
    /**
     * Retorna el codigo del objeto
     * 
     * @return integer
     */
    public function getCodigo(){
        $primary = $this->primaryKey;
        return $this->$primary;
    }
    
    /* STATIC FUNTIONS */
    
    /**
     * retorna los campos presentes en la tabla en formato array
     * 
     * @param CI_DB_mysqli_driver $connection   La conexion actual
     * @param string $nombreTabla El nombre de la tabla en donde realizar la busqueda
     * @return array
     */
    static protected function _campos(CI_DB_mysqli_driver $connection, $nombreTabla){
        $query = $connection->field_data($nombreTabla);
        $arrResp = array();
        foreach ($query as $filed){
            $arrResp[] = $filed->name;
        }
        return $arrResp;
    }
    
    
    /**
     * Buscar registros en la tabla
     *
     * @param CI_DB_mysqli_driver $connection   parametro de conexion actual.
     * @param string $nombreTabla El nombre de la tabla en donde realizar la busqueda
     * @param array $condiciones    (opcional) un array en formato campo => valor con las restricciones de busqueda
     * @return array    Retorna las registros obtenidos
     */
    static protected function _listar(CI_DB_mysqli_driver $connection, $nombreTabla, array $condiciones = null, array $limite = null, 
            array $orden = null, array $grupo = null, $contar = false){
        if ($orden != null){
            $arrOrder = array();
            foreach ($orden as $value){
                $arrOrder[] = $value['campo']." ".$value['orden'];
            }
            $orderBy = implode(", ", $arrOrder);
            $connection->order_by($orderBy);
        }
        
        if ($limite != null){
            $connection->limit($limite[1], $limite[0]);
        }
       
        if ($grupo != null){
            $connection->group_by($grupo);
        }
       
        if($condiciones == null){
            $query = $connection->select($nombreTabla . '.*',false)->from($nombreTabla)->get();
         } else{
           $query = $connection->select($nombreTabla .'.*',false)->from($nombreTabla)->where($condiciones)->get();  
         }
         
        if ($contar){
            $arrResp = $query->num_rows();
        } else {
             $arrResp = $query->result_array();
        }
        
        return $arrResp;            
    }
    
    static function _generarRespuestaModelo(CI_DB_mysqli_driver $conexion,$estado,$respuestaCustom= null){
        $respuesta = array();
        if($estado ==1){
            $respuesta = array(
            "codigo"=>1,
         
                );
            
            
        }else {
            
               $respuesta = array(
            "codigo"=>$estado,
            "msgerror"=>$conexion->_error_message(),
            "errNo"=>$conexion->_error_number()   
                );
            
        }
        if($respuestaCustom != null){
        $respuesta["custom"] = $respuestaCustom;
        
        }
        return $respuesta ;
        
    }
//    static function _generarColumnsDatatable($arrColumnos){
//        
//        $columnas = array();
//        $defColums = array();
//        $i = 0;
//        foreach ($arrColumnos as $key=>$value) {
//           $columnas[] = array("sName"=>$key);
//           $visible = isset($value["visible"]) ? $value["visible"] : true;
//           $seach = isset($value["seach"]) ? $value["seach"] : true;
//           $sort = isset($value["sort"]) ? $value["sort"] : true;
//           $class = isset($value["class"]) ? $value["class"] : "";
//           $mRender = isset($value["mRender"]) ? $value["mRender"] : null;
//           $sWidth = isset($value["sWidth"]) ? $value["sWidth"] : null;
//           $bVisible = isset($value["bVisible"]) ? $value["bVisible"] : true;
//           $defColums[] =  array(
//               "sTitle"=>$value["nombre"],
//               "sName"=>$key,
//               "aTargets"=>array($i),
//               "bVisible"=>$visible,
//               "bSearchable" =>$seach,
//               "bSortable" =>$sort,
//               "sClass"=>$class,
//               "mRender"=>$mRender,
//               "sWidth"=>$sWidth,
//               "bVisible"=>$bVisible
//               );
//           
//           $i++;
//           
//        }
//
//        return $defColums;
//    }
    
    
    
        }


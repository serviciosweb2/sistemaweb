 <?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
class Vusuarios_estado_historico extends Tusuarios_estado_historico{
    private static $motivos = array(
            array("id" => 'motivo1', "motivo" => 'usuario no pertenece mas a la filial'),
            array("id" => 'motivo2', "motivo" => 'usurario ha sido relegado de su puesto'));
    
    function __construct(CI_DB_mysqli_driver $conexion, $codigo = null) {
        parent::__construct($conexion, $codigo);
    }
    
    function getmotivosUsuarios($id = false) {
       $devolver = '';
        if ($id != false) {
            $array = self::$motivos;
            foreach ($array as $value) {
                foreach($id as $tipoMotivo){
                if ($value['id'] == $tipoMotivo) {
                    
                $devolver[]=array(
                    'id'=>$value['id'], 
                    'motivo'=>lang($value['id'])
                    );    
                }
                }
            }
        } else {
            
            $motivos = self::$motivos;
           
            foreach($motivos as $key=>$motivo){
                $motivos[$key] = array('id'=>$motivo['id'], 'motivo'=>lang($motivo['id']));
              }
            
            return $motivos;
        }
        //print_r($devolver);
        return $devolver;
    
    }
}
?>

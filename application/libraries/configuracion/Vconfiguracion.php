<?php

/**
 * Class Vconfiguracion
 *
 * Class  Vconfiguracion maneja todos los aspectos de alumnos
 *
 * @package  SistemaIGA
 * @subpackage Alumnos
 * @author   Ivan berthillod <ivan.sys@gmail.com>
 * @author   Aquiles Gonzalez <sistemas1@iga-la.net>
 * @version  $Revision: 1.1 $
 * @access   private
 */
class Vconfiguracion extends Tconfiguracion {

    /* CONSTRUCTOR */

    function __construct(CI_DB_mysqli_driver $conexion, $codigo = null, $key = null) {
        if (!is_null($codigo)) {
            parent::__construct($conexion, $codigo);
        } else {
            $this->oConnection = $conexion;
            $this->nombreTabla = "configuracion";
            $this->primaryKey = "codigo";
            $conexion->select("*");
            $conexion->from("configuracion");
            $conexion->where("key", $key);
            $query = $conexion->get();
            $arrTemp = $query->result_array();
            if (count($arrTemp) > 0){
                $this->codigo = $arrTemp[0]['codigo'];
                $this->fecha_hora = $arrTemp[0]['fecha_hora'];
                $this->key = $arrTemp[0]['key'];
                $this->value = $arrTemp[0]['value'];
            } else {
                $this->codigo = -1;
                $this->key = $key;

            }
        }
    }

    /* PROTECTED FUNCTIONS */

    protected function _guardar($codUsuario){
        $primary = $this->primaryKey;
        if ($this->$primary == '' || $this->$primary < 1){
            return $this->_insertar($codUsuario);
        } else {
            return $this->_actualizar($codUsuario);
        }
    }

    protected function _insertar($codUsuario){
        $resp = parent::_insertar();

        $insert_id = $this->oConnection->insert_id();

        return $resp && $this->oConnection->query("INSERT INTO configuracion_historicos (codigo_configuracion, `key`, `value`, fecha_hora, cod_usuario)
            SELECT codigo, `key`, `value`, fecha_hora, $codUsuario FROM configuracion WHERE codigo = {$insert_id}");
    }

    protected function _actualizar($codUsuario){
        $resp = $this->oConnection->query("INSERT INTO configuracion_historicos (codigo_configuracion, `key`, `value`, fecha_hora, cod_usuario)
            SELECT codigo, `key`, `value`, fecha_hora, $codUsuario FROM configuracion WHERE codigo = {$this->codigo}");
        return $resp && parent::_actualizar();
    }

    /* PUBLIC FUNCTION */

    public function guardarConfiguracion($codUsuario){
        return $this->_guardar($codUsuario);
    }

    public function json_is($string) {
        try {
            // try to decode string
            json_decode($string);
        } catch (ErrorException $e) {
            // exception has been caught which means argument wasn't a string and thus is definitely no json.
            return FALSE;
        }
        // check if error occured
        return (json_last_error() == JSON_ERROR_NONE);
    }

    /* STATIC FUNCTIONS */

    static function getValorConfiguracion($conexion, $codigo = null, $key = null, $index = null) {
        if ($codigo != null) {
            $arrcondiciones["codigo"] = $codigo;
        }
        if ($key != null) {
            $arrcondiciones["key"] = $key;
        }

        $valores = Vconfiguracion::listarConfiguracion($conexion, $arrcondiciones);
        $configuracion = new Vconfiguracion($conexion, $valores[0]["codigo"]);

        if ($configuracion->json_is($configuracion->value)) {
            $arrValores = json_decode($configuracion->value, true);
            $retorno = $index == null ? $arrValores : $arrValores[$index];
            return $retorno;
        } else {
            return $configuracion->value;
        }
    }
}

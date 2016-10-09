<?php

/**
 * Class Vferiados
 *
 * Class  Vferiados maneja todos los aspectos de alumnos
 *
 * @package  SistemaIGA
 * @subpackage Alumnos
 * @author   Ivan berthillod <ivan.sys@gmail.com>
 * @author   Aquiles Gonzalez <sistemas1@iga-la.net>
 * @version  $Revision: 1.1 $
 * @access   private
 */
class Vferiados extends Tferiados {

    function __construct(CI_DB_mysqli_driver $conexion, $codigo = null) {
        parent::__construct($conexion, $codigo);
    }

    function guardar($nombre, $dia, $mes, $usuario, $anio, $repite, $horadesde, $horahasta) {
        $this->nombre = $nombre;
        $this->dia = $dia;
        $this->mes = $mes;
        $this->anio = $anio;
        $this->repite = $repite;
        $this->baja = '0';
        $this->fecha_hora = date('Y-m-d H:i:s');
        $this->hora_desde = $horadesde;
        $this->hora_hasta = $horahasta;
        $this->cod_usuario = $usuario;
        $respuesta = $this->guardarFeriados();

        $this->bajaHorarios();
        return $respuesta;
    }

    function baja($datos) {
        $this->oConnection->trans_begin();
        $this->baja = $datos['estado'];
        $this->guardarFeriados();
        $this->bajaHorarios();
        $estadosHistoricos = new Vferiados_estado_historico($this->oConnection);
        $arrayGuardarEstadoHistorico = array(
            "cod_feriado" => $this->codigo,
            "baja" => $this->baja,
            "motivo" => 1,
            "fecha_hora" => date("Y-m-d H:i:s"),
            "comentario" => $datos['comentario'],
            "cod_usuario" => $datos['cod_usuario']
        );
        $estadosHistoricos->setFeriados_estado_historico($arrayGuardarEstadoHistorico);
        $estadosHistoricos->guardarFeriados_estado_historico();
        $estadoTran = $this->oConnection->trans_status();

        if ($estadoTran === false) {
            $this->oConnection->trans_rollback();
        } else {
            $this->oConnection->trans_commit();
        }
        return $estadoTran;
    }

    function bajaHorarios() {
        $fechafer = date('Y-m-d', strtotime($this->anio . '-' . $this->mes . '-' . $this->dia));
        
        $arrhorariosbaja = Vhorarios::getHorarios($this->oConnection, $fechafer, $this->hora_desde, $this->hora_hasta, $this->repite, '0');
        
        foreach ($arrhorariosbaja as $rowhorario) {
            $horario = new Vhorarios($this->oConnection, $rowhorario['codigo']);
            $horario->unSetHorario();
        }
    }

    static function isFeriado(CI_DB_mysqli_driver $conexion, $fechaMySQL, $validarHoraMedioFeriado = true) {
        $arrTemp = explode(" ", $fechaMySQL);
        $fecha = $arrTemp[0];
        $hora = isset($arrTemp[1]) ? $arrTemp[1] : false;
        $arrTemp = explode("-", $fecha);
        $anio = $arrTemp[0];
        $mes = $arrTemp[1];
        $dia = $arrTemp[2];
        $conexion->select("*");
        $conexion->from("feriados");
        $conexion->where("((dia = '$dia' AND mes = '$mes' AND repite = 1) OR (dia = '$dia' AND mes = '$mes' AND anio = '$anio'))");
        if ($validarHoraMedioFeriado && $hora) {
            $conexion->where("(hora_desde <= '$hora' AND hora_hasta >= '$hora')");
        }
        return $conexion->count_all_results() > 0;
    }

}

/*
    SELECT * FROM `feriados` 
    WHERE 
    ((dia = @dia AND mes = @mes AND repite = 1)
    OR (dia = @dia AND mes = @mes AND anio = @anio))
    AND
    (hora_desde <= @hora AND hora_hasta >= @hora)
 */

<?php

class certificados_ucel_impresos_fecha_matriculacion extends CI_Controller
{
    public function __construct() {
        parent::__construct();
    }

    public function index()
    {
        //Setear fecha
        $fecha = '2016-05-12';
        //Array de filiales (Obtener con script "cert_UCEL_por_fecha_impresion" en DBeaver [GROUP BY cod_filial])
        $filiales = array('3','4','8','11','18','20','21','23','25','28','29','39','40','48','50','51','53','56','58','64','66','67','69','70','73');

        $condiciones = array(
            'certificados_estado_historico.estado' => 'finalizado',
            'cod_certificante' => 2,
            'fecha_hora LIKE' => $fecha.'%'
        );

        //Genera la "vista"
        echo '<meta charset="utf-8" />';
        echo '<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">';
        echo '<h1>Fecha impresión certificados UCEL <small>'. $fecha . '</small></h1><hr>';

        //Encabezado tabla
        $header = '<thead><tr>
                    <th>Filial</th>
                    <th>Alumno</th>
                    <th>Matrícula</th>
                    <th>Matrícula periodo</th>
                    <th>Fecha matriculacion</th>
                  </tr></thead>';

        //Filas tabla
        $filas = '';
        foreach ($filiales as $filial){
            $conexion = $this->load->database($filial, true);
            $data = $this->getData($conexion, $condiciones, $filial);

            foreach ($data as $certificado) {
                //die(var_dump($certificado));
                $filas .= '<tr>
                        <td>' . $certificado->filial . '</td>
                        <td>' . $certificado->codigo_alumno . ' - ' . $certificado->apellido_alumno . ',' . $certificado->nombre_alumno . '</td>
                        <td>' . $certificado->codigo_matricula . '</td>
                        <td>' . $certificado->codigo_mat_periodo . '</td>
                        <td>' . $certificado->fecha_matriculacion . '</td>
                      </tr>';
            }
        }

        //Cuerpo tabla
        $body = '<tbody>';
        $body .= $filas;
        $body .= '</tbody>';

        //Tabla
        $tabla = '<div class="table-responsive">
                    <table class="table table-striped table-hover table-condensed">'.$header . $body .'</table>
                  </div>';

        echo $tabla;

    }

    //Obtiene array de fecha de matriculacion de certificados impresos en la fecha seleccionada
    public function getData(CI_DB_mysqli_driver $conexion, $condiciones, $filial)
    {
        $conexion->select("general.filiales.nombre as filial, alumnos.codigo as codigo_alumno, alumnos.apellido as apellido_alumno, alumnos.nombre as nombre_alumno, matriculas_periodos.cod_matricula as codigo_matricula, certificados_estado_historico.cod_matricula_periodo as codigo_mat_periodo, matriculas_periodos.fecha_emision as fecha_matriculacion");
        $conexion->from('certificados_estado_historico, general.filiales');
        $conexion->join('matriculas_periodos','matriculas_periodos.codigo = certificados_estado_historico.cod_matricula_periodo');
        $conexion->join('matriculas', 'matriculas.codigo = matriculas_periodos.cod_matricula');
        $conexion->join('alumnos', 'alumnos.codigo = matriculas.cod_alumno');
        $conexion->where('general.filiales.codigo = '.$filial);
        $conexion->where($condiciones);

        $result = $conexion->get();
        $arrData = $result->result();

        foreach ($arrData as $key => $data) {
            $data->fecha_matriculacion = formatearFecha_pais($data->fecha_matriculacion);
        }

        return $arrData;

    }

}
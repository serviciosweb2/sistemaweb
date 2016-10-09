<?php

class Alumnos_activos_por_pais extends CI_Controller
{
    public function __construct() {
        parent::__construct();
        $this->load->model("Model_filiales", "", false,'');
    }

    public function index()
    {
        $paises = array(
            1 => 'Argentina',
            2 => 'Brasil',
            3 => 'Uruguay',
            4 => 'Paraguay',
            5 => 'Venezuela',
            6 => 'Bolivia',
            7 => 'Chile',
            8 => 'Colombia',
            9 => 'Panamá',
            10 => 'USA',
            11 => 'España',
            12 => 'Ecuador',
            13 => 'Mexico',
            14 => 'República Dominicana',
            15 => 'Haití'
        );

        echo '<meta charset="utf-8" />';
        echo '<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">';
        echo '<div class="container"><h1>Alumnos activos con matrículas activas de 1° y 2° periodo<small></small></h1><hr>';

        foreach ($paises as $key => $pais){

            $totalPais1 = 0;
            $totalPais2 = 0;

            $filiales = $this->Model_filiales->getFiliales($key,2);

            if(!empty($filiales)) {
                echo '<h3>'. $pais .'</h3>';
                echo '<table class="table table-responsive table-condensed table-striped">
                        <thead>
                            <th style="width:50%">Filial</th>
                            <th style="width:25%">Cant. de alumnos 1° Año</th>
                            <th style="width:25%">Cant. de alumnos 2° Año</th>
                        </thead><tbody>';
                foreach ($filiales as $filial) {

                    $cantidadFilial1 = 0;
                    $cantidadFilial2 = 0;

                    $conexion = $this->load->database($filial['codigo'], true);

                    $conexion->select('alumnos.codigo, alumnos.nombre, alumnos.apellido, matriculas_periodos.cod_tipo_periodo as periodo');
                    $conexion->from('alumnos');
                    $conexion->join('matriculas', 'matriculas.cod_alumno = alumnos.codigo AND matriculas.estado = "habilitada" AND (matriculas.cod_plan_academico = 1 OR matriculas.cod_plan_academico = 2 OR matriculas.cod_plan_academico = 22 OR matriculas.cod_plan_academico = 30 OR matriculas.cod_plan_academico = 31 OR matriculas.cod_plan_academico = 33 OR matriculas.cod_plan_academico = 57 OR matriculas.cod_plan_academico = 63 OR matriculas.cod_plan_academico = 95)');
                    $conexion->join('matriculas_periodos', 'matriculas_periodos.cod_matricula = matriculas.codigo AND matriculas_periodos.estado = "habilitada"');
                    $conexion->where('matriculas_periodos.cod_tipo_periodo = 1 AND alumnos.baja = "habilitada" OR matriculas_periodos.cod_tipo_periodo = 2 AND alumnos.baja = "habilitada"');
                    $conexion->group_by('cod_alumno');
                    $result = $conexion->get();
                    $data = $result->result();

                    foreach ($data as $alumno) {
                        if ($alumno->periodo == 1){
                            $cantidadFilial1++;
                        } elseif ($alumno->periodo == 2){
                            $cantidadFilial2++;
                        }
                    }
                    if ($cantidadFilial1 != 0 && $cantidadFilial2 != 0) {
                        echo '<tr>';
                        echo '<td>' . $filial['nombre'] . '</td>';
                        echo '<td style="text-align:center;">' . $cantidadFilial1 . '</td>';
                        echo '<td style="text-align:center;">' . $cantidadFilial2 . '</td>';
                        echo '</tr>';

                        $totalPais1 = $totalPais1 + $cantidadFilial1;
                        $totalPais2 = $totalPais2 + $cantidadFilial2;
                    }


                }
                echo '</tbody>';
                echo '<tfoot><tr><td>TOTAL PAÍS</td><td style="text-align:center;">'.$totalPais1.'</td><td style="text-align:center;">'.$totalPais2.'</td></tr></tfoot>';
                echo '</table><hr>';
            }

        }

        echo "</div>";

    }

}
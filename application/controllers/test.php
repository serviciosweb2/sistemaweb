<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class test extends CI_Controller {

    private $seccion;

    public function __construct() {
        parent::__construct();
        session_method();
        $this->lang->load(get_idioma(), get_idioma());
        $this->seccion = session_method();
        $filial = $this->session->userdata('filial');
        $config = array("codigo_filial" => $filial["codigo"]);
        $this->load->model("Model_cursos", "", false, $config);
    }



    public function reporte_certificados(){


            $conexion = $this->load->database("default", true);
            $arrFiliales = Vfiliales::listarFiliales($conexion, array("version_sistema" => 2, "baja" => "0", "pais" => "1"));
        //print_r($arrFiliales);
           foreach ($arrFiliales as $filial)
            {
                $codFilial = $filial['codigo'];
                $nombre = $filial['nombre'];

                echo $codFilial." - ".$nombre."</br>";
               // $conexion = $this->load->database($codFilial, true);
               // $conexion->query("ALTER TABLE `matriculas` MODIFY COLUMN `observaciones`  varchar(511) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL AFTER `fecha_emision`");
            }

    }




    
    /* ################ PROXIMO PASAJE A PRODUCCION #########################*/
    /* ################ EJECUTAR INICIO #########################*/
	
    public function subMenuReporteMenuPrincipal()
    {
        $connection = $this->load->database('default', true);
        $connection->query("ALTER TABLE general.secciones ADD COLUMN grupo enum('reportes-interesados','reportes-administrativos','reportes-academicos', 'reportes-franquiciados') NULL AFTER id_atajo");
        
        $connection->query("UPDATE general.secciones set general.secciones.grupo = 'reportes-academicos' 
                            WHERE general.secciones.slug 
                            IN ('reporte_alumnos', 
                                    'reporte_inscripciones_y_bajas', 
                                    'reporte_inscripciones',
                                    'inscriptos_por_comisiones',
                                    'reporte_alumnos_por_materias',
                                    'reporte_inscriptos_por_materia',
                                    'reporte_inscripciones_comisiones',
                                    'reporte_bajas',
                                    'estado_alumnos_certificados')");
        
        $connection->query("UPDATE general.secciones set general.secciones.grupo = 'reportes-interesados' 
                            WHERE general.secciones.slug 
                            IN ('reporte_consultas_web',
                                'reporte_presupuestos')");
        
        $connection->query("UPDATE general.secciones set general.secciones.grupo = 'reportes-administrativos' 
                            WHERE general.secciones.slug 
                            IN ('rentabilidad',
                                    'deudas_por_alumno',
                                    'reporte_ctacte_pendientes',
                                    'reporte_cobros',
                                    'reporte_facturas',
                                    'facturacion_y_cobro',
                                    'reporte_movimientos_cajas',
                                    'reporte_cajas',
                                    'reporte_comprobantes_compras')");
        
        $connection->query("DELETE FROM general.secciones WHERE general.secciones.slug IN ('estado_alumnos_certificados', 'rentabilidad')", false);
        
        //reporte de alumnos estados certificados
        //quitado de prod para ocultarlo - no agregar hasta nuevo aviso
        //164	estado_alumnos_certificados	0	menu_principal	estado_alumnos_certificados	reportes	reportes/estado_alumnos_certificados	47		reportes-academicos

        //reporte de rentabilidad
        //quitado de prod para ocultarlo - no agregar hasta nuevo aviso
        //162	rentabilidad	0	menu_principal	rentabilidad	reportes	reportes/rentabilidad	20		reportes-franquiciados

    }

	
	
	/* ################ EJECUTAR FIN #########################*/
	
	
	
	/*
     * Agrega la tabla mails_inbox_externa a la DB de cada filial
     */
    public function agregarTablaMailsInboxExterna() {
        $connection = $this->load->database('default', true);
        $filiales = Vfiliales::listarFiliales($connection);
        
        echo "<pre>\n\nCreando tabla mails_inbox_externa en las DBs de filiales:\n";
        
        foreach ($filiales as $current_filial) {
            $connection = $this->load->database(''.$current_filial['codigo'], true);
            
            echo "\nFilial ".$current_filial['codigo'].": ";
            
            $query_result = true;
            
            $query_result = $connection->query(
                "CREATE TABLE `mails_inbox_externa` (
                  `email_uid` int(10) unsigned NOT NULL DEFAULT '0',
                  `email_body` mediumtext NOT NULL,
                  `from_name` varchar(100) NOT NULL,
                  `from_account` varchar(100) NOT NULL,
                  `subject` varchar(500) NOT NULL,
                  `date_time` varchar(19) NOT NULL,
                  `readed` tinyint(1) NOT NULL DEFAULT '0',
                  `hidden` tinyint(1) NOT NULL DEFAULT '0',
                  PRIMARY KEY (`email_uid`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8;"
            );
            
            if ($query_result !== false) {
                echo "Tabla creada! :)";
            }
            else
            {
                echo "Error al crear tabla!!!!!! :(";
            }
        }
        
        echo "</pre>";
    }
    




    public function agregarTablasTarjetaDebito() {
        $connection = $this->load->database('default', true);
        $filiales = Vfiliales::listarFiliales($connection);
        
        echo "<pre>\n\nCreando tablas para medio_debito en las DBs de filiales:\n";
        foreach ($filiales as $current_filial) {

            $connection = $this->load->database(''.$current_filial['codigo'], true);
            
            echo "\nFilial ".$current_filial['codigo'].": ";
            
            $query_result = true;
            
            $query_result = $connection->query(
                "CREATE TABLE `medio_debito` (
                `codigo` int(4) NOT NULL AUTO_INCREMENT,
                `cod_tipo` int(4) NOT NULL,
                `cod_bco_emisor` int(4) DEFAULT NULL,
                `cupon` varchar(12) NOT NULL,
                `cod_cobro` int(11) DEFAULT NULL,
                `cod_terminal` int(11) NOT NULL,
                `cod_autorizacion` varchar(12) NOT NULL,
                `cuotas` int(11) DEFAULT NULL,
                PRIMARY KEY (`codigo`),
                KEY `cod_bco_emisor` (`cod_bco_emisor`),
                KEY `cod_cobro` (`cod_cobro`),
                KEY `cod_terminal` (`cod_terminal`),
                KEY `medio_debito_ibfk_3` (`cod_tipo`),
                CONSTRAINT `medio_debito_ibfk_1` FOREIGN KEY (`cod_bco_emisor`) REFERENCES `bancos`.`bancos` (`codigo`),
                CONSTRAINT `medio_debito_ibfk_2` FOREIGN KEY (`cod_cobro`) REFERENCES `cobros` (`codigo`),
                CONSTRAINT `medio_debito_ibfk_3` FOREIGN KEY (`cod_tipo`) REFERENCES `tarjetas`.`tipos_debito` (`codigo`),
                CONSTRAINT `medio_debito_ibfk_4` FOREIGN KEY (`cod_terminal`) REFERENCES `pos_terminales` (`codigo`)
              ) ENGINE=InnoDB AUTO_INCREMENT=587 DEFAULT CHARSET=utf8;"
            );
            
            if ($query_result !== false) {
                echo "Tabla creada! :)";
            }
            else
            {
                echo "Error al crear tabla!!!!!! :(";
            }
        }

        foreach ($filiales as $current_filial) {


            $connection = $this->load->database(''.$current_filial['codigo'], true);
            
            echo "\nFilial ".$current_filial['codigo'].": ";
            
            $query_result = true;
            
            $query_result = $connection->query(
                "CREATE TABLE `pos_terminales_debito` (
                 `cod_terminal` int(11) NOT NULL,
                 `cod_tipo` int(11) NOT NULL,
                  PRIMARY KEY (`cod_terminal`,`cod_tipo`)
                ) ENGINE=InnoDB DEFAULT CHARSET=latin1;"
            );
            
            if ($query_result !== false) {
                echo "Tabla creada! :)";
            }
            else
            {
                echo "Error al crear tabla!!!!!! :(";
            }
        }
        
        echo "</pre>";
    }








    public function create_rubros()
    {
        echo "<pre>";
        $conexion = $this->load->database("default", true);
        $arrFiliales = Vfiliales::listarFiliales($conexion, array("version_sistema" => 2, "baja" => "0"));
        foreach ($arrFiliales as $filial)
        {
            $codFilial = $filial['codigo'];
            $conexion = $this->load->database($codFilial, true);
            echo "filial: ". $codFilial ."<br>";
            echo "preparando query para la creacion de la tabla <br>";
            
            $conexion->query("DROP TABLE rubros_caja");
            $conexion->query("CREATE TABLE rubros_caja (codigo int(11) UNSIGNED NOT NULL AUTO_INCREMENT ,rubro enum('GASTOS_DE_ADMINISTRACION','GASTOS_DE_SERVICIO','GASTOS_OPERATIVOS','GASTOS_FINANCIEROS', 'IMPUESTOS', 'SERVICIOS', 'INSUMOS') NOT NULL , subrubro enum('PERDIDAENDIFERENCIADECAMBIO','PERDIDAENVENTASDEACTIVOSFIJOS','GASTOSBACARIOS','IMPUESTOSBANCARIOS','INTERESES','COMISIONESBANCARIAS','MULTASEINTERESESDELFISCO','INDUMENTARIA','ALIMENTOSYREFRIGERIOS','PREMIOSAEMPLEADOS','HONORARIOSPROFESIONALES','GASTOSPORCONTRIBUCIONES','HORASEXTRAS','VIATICOSYMOVILIDAD','PUBLICIDAD','ALQUILERES','SEGUROS','GASTOSDEDEPRECIACIONES','GASTOSDEMANTENIMIENTOYREPARACIONES','GASTOSPORASESORAMIENTOYCAPACITACION','MATERIALESYARTICULOSDELIMPIEZA','MATERIALESYUTILESDEOFICINA','INTERNET','TELEFONO','AGUA','LUZ','OTROSGASTOSDEPERSONAL','SUELDOSYJORNALES','TGI','DREI','API', 'ANT_GANANCIAS','ALARMA', 'CERTIFICADOS','MATESTUDIO','UNIFORMES', 'ROYALTY') NOT NULL , PRIMARY KEY (codigo))",false);
            echo "query ejecutada<br>";
            
            $administracion = array("SUELDOSYJORNALES", "OTROSGASTOSDEPERSONAL");
            $gastos_servicios = array('GASTOSDEDEPRECIACIONES','GASTOSDEMANTENIMIENTOYREPARACIONES','GASTOSPORASESORAMIENTOYCAPACITACION','MATERIALESYARTICULOSDELIMPIEZA','MATERIALESYUTILESDEOFICINA','INTERNET','TELEFONO','AGUA','LUZ');
            $operativos = array('MULTASEINTERESESDELFISCO','INDUMENTARIA','ALIMENTOSYREFRIGERIOS','PREMIOSAEMPLEADOS','HONORARIOSPROFESIONALES','GASTOSPORCONTRIBUCIONES','HORASEXTRAS','VIATICOSYMOVILIDAD','PUBLICIDAD','ALQUILERES','SEGUROS', 'ROYALTY');
            $financieros = array('PERDIDAENDIFERENCIADECAMBIO','PERDIDAENVENTASDEACTIVOSFIJOS','GASTOSBACARIOS','IMPUESTOSBANCARIOS','INTERESES','COMISIONESBANCARIAS');
            $impuestos = array('TGI','DREI','API', 'ANT_GANANCIAS');
            $servicios = array('ALARMA');
            $insumos = array('CERTIFICADOS','MATESTUDIO','UNIFORMES');
            
            echo "tabla creada ok------<br>";
            echo "insertando registros<br>";
            
            foreach ($administracion as $adminValue)
            {
                $conexion->query("INSERT INTO rubros_caja VALUES(null,'GASTOS_DE_ADMINISTRACION', '".$adminValue."')",false);
            }
            foreach ($gastos_servicios as $serviValue)
            {
                $conexion->query("INSERT INTO rubros_caja VALUES(null,'GASTOS_DE_SERVICIO', '".$serviValue."')",false);
            }
            foreach ($operativos as $opValue)
            {
                $conexion->query("INSERT INTO rubros_caja VALUES(null,'GASTOS_OPERATIVOS', '".$opValue."')",false);
            }
            foreach ($financieros as $finanValue)
            {
                $conexion->query("INSERT INTO rubros_caja VALUES(null,'GASTOS_FINANCIEROS', '".$finanValue."')",false);
            }
            foreach ($impuestos as $impuesto)
            {
                $conexion->query("INSERT INTO rubros_caja VALUES(null,'IMPUESTOS', '".$impuesto."')",false);
            }
            foreach ($servicios as $servicio)
            {
                $conexion->query("INSERT INTO rubros_caja VALUES(null,'SERVICIOS', '".$servicio."')",false);
            }
            foreach ($insumos as $insumo)
            {
                $conexion->query("INSERT INTO rubros_caja VALUES(null,'INSUMOS', '".$insumo."')",false);
            }
            
            echo "exito<br><br>";
        }
        echo "<pre>";
    }

    public function agregarDebitoBancario()
    {
        $conexion = $this->load->database("default", true);
        
        $conexion->query("INSERT IGNORE INTO general.medios_pago VALUES (9, 'DEBITO_BANCARIO')");
        
        $conexion->query("INSERT IGNORE INTO general.medios_pago_paises values (9,1)");
        $conexion->query("INSERT IGNORE INTO general.medios_pago_paises values (9,2)");
        $conexion->query("INSERT IGNORE INTO general.medios_pago_paises values (9,3)");
        $conexion->query("INSERT IGNORE INTO general.medios_pago_paises values (9,4)");
        $conexion->query("INSERT IGNORE INTO general.medios_pago_paises values (9,6)");
        $conexion->query("INSERT IGNORE INTO general.medios_pago_paises values (9,9)");
        
    }
    
    public function recortarCampoObservaciones()
    {
        $conexion = $this->load->database("default", true);
        $arrFiliales = Vfiliales::listarFiliales($conexion, array("version_sistema" => 2, "baja" => "0"));
        foreach ($arrFiliales as $filial)
        {
            $codFilial = $filial['codigo'];
            echo $codFilial;
            $conexion = $this->load->database($codFilial, true);
            $conexion->query("ALTER TABLE `matriculas` MODIFY COLUMN `observaciones`  varchar(511) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL AFTER `fecha_emision`");
        }
    }


    public function addcolumna_pais()
    {
        $conexion = $this->load->database("default", true);
        $arrFiliales = Vfiliales::listarFiliales($conexion, array("version_sistema" => 2, "baja" => "0"));
        foreach ($arrFiliales as $filial)
        {
            $codFilial = $filial['codigo'];
            if ($codFilial < 14 ) {
                continue(1);
            }
            if ($codFilial == 36) {
                continue(1);
            }
            $conexion = $this->load->database($codFilial, true);
            $conexion->query("ALTER TABLE `telefonos` ADD COLUMN `pais`  varchar(2) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL AFTER `cod_area_old`");
        }
    }


    //SELECT * FROM general.medios_pago
    //agregar 9	DEBITO_BANCARIO
    //SELECT * FROM general.medios_pago_paises
    //agregar relacion medio pago pais
    

    //agregar a general/secciones
    /*deudas_por_alumno	0	menu_principal	deudas_por_alumno	reportes	reportes/deudas_por_alumno	*/
    //bajas             0	menu_principal	bajas               reportes	reportes/bajas              	
    //rentabilidad      0	menu_principal	rentabilidad        reportes	reportes/rentabilidad       

    //agregar permisos para usuarios
    /*insert into usuarios_permisos (id_usuario, id_seccion) select codigo, 157 from usuarios_sistema where baja = 0;*/
    /*insert into usuarios_permisos (id_usuario, id_seccion) select codigo, 161 from usuarios_sistema where baja = 0;*/
    /*insert into usuarios_permisos (id_usuario, id_seccion) select codigo, 162 from usuarios_sistema where baja = 0;*/
    
    
    public function corregir_horariosduplicados () {
        //ini_set("max_execution_time", 0);
        $conexion = $this->load->database("default", true);
        $arrFiliales = Vfiliales::listarFiliales($conexion, array("version_sistema" => 2, "baja" => "0"));
        
        $ini = ($this->input->get("cod") != '') ? $this->input->get("cod") : 0 ;
        $fi = $this->input->get("filial");
        
        foreach ($arrFiliales as $filial)
        {
            if($filial['codigo'] == $fi){
                
            }
        }
    }
    public function checkEstadosCertificados()
    {
        $fi = '';
        if($this->input->get('codigo_filial') != '')
        {
            $fi = $this->input->get('codigo_filial');
            $arrFiliales = array(array("codigo"=>$fi));
        }
        else
        {
            $conexion = $this->load->database("default", true);
            $arrFiliales = Vfiliales::listarFiliales($conexion, array("version_sistema" => 2, "baja" => "0"));
        }
        
        $ini = ($this->input->get("cod") != '') ? $this->input->get("cod") : 0 ;
        $w = 0;
        
        foreach ($arrFiliales as $filial)
        {
            $codFilial = $filial['codigo'];
            echo "Filial: ".$codFilial . "<br>";
            $conexion = $this->load->database($codFilial, true);

            $conexion->select("*");
            $conexion->from("matriculas_periodos");

            $query = $conexion->get();
            $matriculasPeriodos = $query->result_array();
            echo count($matriculasPeriodos)." matriculas periodo a revisar";
            
            
            foreach ($matriculasPeriodos as $i => $matriculaPeriodo)
            {
                if($i>=$ini && $i<$ini+500)
                {
                    $objcertificado = new Vcertificados($conexion, $matriculaPeriodo["codigo"], 1);
                    $objcertificado->cambiarEstadoCertificadoIGA();
                    $w = $i;
                }
                
            }
            
        }
        echo " => Fin de esta vuelta </br>";
        if($w == $i)
        {
            echo "Todo listo!";
        }
        else
        {
            echo "Actualizó hasta matricula: ".$w."  => ";
            $link = 'checkEstadosCertificados?codigo_filial='.$fi.'&cod='.($w+1);
            echo '<a href="'.$link.'" id="continuar">Continuar</a>';
            //Martin hago esto para q dani no se vuele los pelos
            ?> 

            <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
            <script>
                $(document).ready(function (){
                    var delay = 1000; //Your delay in milliseconds
                    setTimeout(function(){ window.location = "<?php echo $link ?>" }, delay);
                });
            </script>
            <?php
            
        }
    }
    public function agregarOpcionMenuCuposDisponibles()
    {
        $conexion = $this->load->database("general", true);
        $conexion->query("INSERT INTO `secciones` (`codigo`, `slug`, `id_seccion_padre`, `menu_tipo`, `method`, `categoria`, `control`, `prioridad`, `id_atajo`, `grupo`) VALUES (168, 'cupos_abiertos', 0, 'menu_principal', 'cupos_abiertos', 'reportes', 'reportes/cupos_abiertos_curso', 78, NULL, 'reportes-franquiciados');");
    }
    
    


    /**
     *  Actualiza el campo asistecia en la tabla horarios
     *  el campo asistencia se usa en el calendario para pintar los dias verdes o rojos según su estado
     */
    public function actualizaAsistenciasTomadas($codFilial)
    {
        echo $codFilial;

            $conexion = $this->load->database($codFilial, true);

            //TODAS LAS COMISIONES Y HORARIOS
            $conexion->select('comisiones.*, horarios.codigo as horario_codigo, horarios.asistencia as horario_asistencia, horarios.dia as horario_dia, horarios.cod_materia as horario_materia');
            $conexion->from('horarios');
            $conexion->join('comisiones', 'horarios.cod_comision = comisiones.codigo');
            $conexion->where('horarios.baja', 0);
            $conexion->where('horarios.dia >=', '2015-01-01');
            $conexion->where('horarios.dia <', '2016-01-01');
            $conexion->order_by('horarios.codigo', 'desc');

            $query = $conexion->get();
            $horarios = $query->result_array();
            /*
            print '<pre>';
            print_r($conexion->last_query());
            print '</pre>';
            print '<pre>';
            print_r($horarios);
            print '</pre>';
            die;
            */

            foreach($horarios as $horario)
            {
                //ULTIMO ESTADO CARGADO EN ESE HORARIO tabla matriculas_horario
                $conexion->select('horarios.dia, matriculas_horarios.*');
                $conexion->from('matriculas_horarios');
                $conexion->join('horarios', 'horarios.codigo = matriculas_horarios.cod_horario');
                $conexion->where('matriculas_horarios.cod_horario', $horario['horario_codigo']);
                $conexion->where('matriculas_horarios.cod_horario', $horario['horario_codigo']);
                $conexion->order_by('matriculas_horarios.fecha_hora', 'desc');
                $conexion->limit(3);

                $query = $conexion->get();
                $ultimo_registro = $query->result_array();

                /*
                print '<pre>';
                print_r($conexion->last_query());
                print '</pre>';
                die;
                */

                print '<pre>';
                print_r($horario);
                print '</pre>';

                print '<pre>';
                print 'ultimo cargado en '.$horario["nombre"].'  <br>';
                print_r($ultimo_registro);
                print '</pre>';
                echo $horario['horario_asistencia']. ' <br>';

                /* SI EL REGISTRO TIENE ASIGNADO UN ESTADO (asistencia)
                 * ACTUALIZA EL CAMPO asistencia EN tabla horarios
                 */
                if(($ultimo_registro[0]['estado'] != null && $ultimo_registro[0]['estado'] != '') || ($ultimo_registro[1]['estado'] != null && $ultimo_registro[1]['estado'] != '') || ($ultimo_registro[2]['estado'] != null && $ultimo_registro[2]['estado'] != '')  && $horario['horario_asistencia'] == 0)
                {
                    $conexion->update('horarios', array('asistencia' => 1), 'codigo = '.$horario['horario_codigo']);

                    print '<pre>';
                    print_r($conexion->last_query());
                    print '</pre>';
                    //die;

                }
                elseif ($ultimo_registro[0]['estado'] == '' && $ultimo_registro[1]['estado'] == '' && $ultimo_registro[2]['estado'] == '' && $horario['horario_asistencia'] == 1)
                {
                    $conexion->update('horarios', array('asistencia' => 0), 'codigo = '.$horario['horario_codigo']);

                    echo 'no cargada <br>'. $ultimo_registro[0]['estado'];

                    print '<pre>';
                    print_r($conexion->last_query());
                    print '</pre>';
                    //die;
                }
            }

        /*
        print '<pre>';
        print_r($horarios);
        print '</pre>';
        die;
        */
        echo "fin";
    }

    /**
     *  Actualiza la tabla planes_financiaciones_descuentos
     *  Agrega los campos 'limite_vigencia' y 'fecha_vigencia'
     *  se utilizan para poner fecha de cierre a los planes
     *  linea por linea
     */
    public function actualizaTablaPlanesFinanciacionesDescuentos()
    {
        $conexion = $this->load->database("default", true);
        $arrFiliales = Vfiliales::listarFiliales($conexion, array("version_sistema" => 2, "baja" => "0"));
        foreach ($arrFiliales as $filial)
        {
            $codFilial = $filial['codigo'];

            echo "filial: ". $codFilial ." ". $filial['nombre'] ."<br>";

                echo "ejecutar alter table planes_financiaciones_descuentos <br>";

                $query_existe_tabla = $conexion->query("SELECT * FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = $codFilial AND TABLE_NAME = 'planes_financiaciones_descuentos'");

                if($query_existe_tabla->num_rows() > 0)
                {
                  $query_checkColumn_limiteVigencia = $conexion->query("SELECT * FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = $codFilial AND TABLE_NAME = 'planes_financiaciones_descuentos' AND COLUMN_NAME = 'limite_vigencia'");
                  $query_checkColumn_fechaVigencia = $conexion->query("SELECT * FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = $codFilial AND TABLE_NAME = 'planes_financiaciones_descuentos' AND COLUMN_NAME = 'fecha_vigencia'");

                    if($query_checkColumn_limiteVigencia->num_rows() == 0)
                    {
                        $query_agrega_limiteVigencia = $conexion->query("ALTER TABLE `$codFilial`.`planes_financiaciones_descuentos` ADD COLUMN `limite_vigencia` ENUM('sin_fecha_limite','con_fecha_limite','al_momento') NOT NULL DEFAULT 'sin_fecha_limite' AFTER `fecha_limite`");
                        if ($query_agrega_limiteVigencia !== false) {
                            echo "Ok! :) Query ejecutada. Se agregó columna limite_vigencia <br>";
                        }
                        else
                        {
                            echo "Error alter table! columna limite_vigencia <br>";
                        }
                    }
                    else
                    {
                        echo "Ya existe la columna limite_vigencia <br>";
                    }

                    if($query_checkColumn_fechaVigencia->num_rows() == 0)
                    {
                        $query_agrega_fechaVigencia = $conexion->query("ALTER TABLE `$codFilial`.`planes_financiaciones_descuentos` ADD COLUMN `fecha_vigencia` DATE NULL DEFAULT NULL AFTER `limite_vigencia`");
                        if ($query_agrega_fechaVigencia !== false) {
                            echo "Ok! :) Query ejecutada. Se agregó columna fecha_vigencia <br>";
                        }
                        else
                        {
                            echo "Error alter table!  columna fecha_vigencia <br>";
                        }
                    }
                    else
                    {
                        echo "Ya existe la columna fecha_vigencia <br>";
                    }

                    //die;
                }



            echo "/---------------/ <br> <br> ";
        }
        echo "FIN";
    }


    public function actualizaEstadoAcademico()
    {
        $conexion = $this->load->database("default", true);
        $arrFiliales = Vfiliales::listarFiliales($conexion, array("version_sistema" => 2, "baja" => "0"));

        foreach ($arrFiliales as $filial)
        {
            $codFilial = $filial['codigo'];

            //SOLO BELO HORIZONTE
            if($codFilial == 46)
            {
                echo "filial: ". $codFilial ." ". $filial['nombre'] ."<br>";

                $estados = array();
                $estados[] = array('matricula' => 2713, 'estado_academico' => 28377);
                $estados[] = array('matricula' => 2677, 'estado_academico' => 27981);
                $estados[] = array('matricula' => 2848, 'estado_academico' => 30069);
                $estados[] = array('matricula' => 2826, 'estado_academico' => 29806);
                $estados[] = array('matricula' => 2853, 'estado_academico' => 30130);
                $estados[] = array('matricula' => 2804, 'estado_academico' => 29543);
                $estados[] = array('matricula' => 2742, 'estado_academico' => 28747);
                $estados[] = array('matricula' => 2715, 'estado_academico' => 28405);
                $estados[] = array('matricula' => 2858, 'estado_academico' => 30174);

                foreach($estados as $estado)
                {
                    echo "Modifica estado academico a reguluar de matricula: ". $estado['matricula'] ." y estado academico: ". $estado['estado_academico'] ."<br>";

                    $queryActualiza = $conexion->query("UPDATE `$codFilial`.`estadoacademico` SET estado = 'regular' WHERE codigo = ".$estado['estado_academico']);

                    if($queryActualiza)
                    {
                        echo "Ok! Se actualizo =) <br>";
                    }
                    else
                    {
                        echo "ERROR al intentar actualizar <br>";
                    }

                    echo "<br> /-----------------/ <br>";
                    //die;
                }//end foreach
            }
        }//end foreach
    }

    public function actualizaLongitudCampoNombreComisiones()
    {
        $conexion = $this->load->database("default", true);
        $arrFiliales = Vfiliales::listarFiliales($conexion, array("version_sistema" => 2, "baja" => "0"));

        foreach ($arrFiliales as $filial)
        {
            $codFilial = $filial['codigo'];
            //$codFilial = '00';

            echo "filial: ". $codFilial ." ". $filial['nombre'] ."<br>";
            echo "Actualiza Longitud Campo Nombre Comisiones <br>";


            try {
                $query_str = 'ALTER TABLE `'.$codFilial.'`.`comisiones` CHANGE COLUMN `nombre` `nombre` VARCHAR(100) NOT NULL';
                $result = $this->db->query($query_str);
                //$estadotran = $conexion->trans_status();

                if (!$result)
                {
                    throw new Exception('error en query');
                    return false;
                }
                echo "OK! :) <br>";
                echo "-------------------- <br> <br>";
            } catch (Exception $e) {
                //print_r($e);
                echo $e->getMessage();
                echo "-------------------- <br> <br>";
                continue;
            }

        }//end foreach
    }

    /**
     *
     */
    public function recalcular_porcentajes_asistencias($codFilial) {
        echo "<pre>";
        echo "function\n";

        try {
            echo "-> try\n";
            $conexion = $this->load->database($codFilial, true);
                //echo "--> Filial: ".$filial['codigo']."\n";
                echo "\nFilial: ".$codFilial;
                //error_log("+ Recalculando asistencias de filial \"".$codFilial['codigo']."\".");
                error_log("+ Recalculando asistencias de filial \"".$codFilial."\".");

                //$conexion = $this->load->database($codFilial['codigo'], true);
                $conexion = $this->load->database("".$codFilial, true);

                //SELECT estadoacademico.codigo FROM `estadoacademico` WHERE `porcasistencia` IS NOT NULL;
                $conexion->select("estadoacademico.codigo");
                $conexion->from('estadoacademico');
                $conexion->where('porcasistencia IS NOT NULL');
                $query = $conexion->get();

                //echo "<pre>";
                //echo "\nActualizando porcentajes de asistencia...\n\n";

                $query_result = $query->result_array();

                $cantidad = 0;
                $time_ini = time();
                foreach ($query_result as $obj_estado_academico) {
                    //echo "---> foreach, codigo de estado acad: ".$obj_estado_academico['codigo']."\n";
                    //echo "---> foreach, codigo de estado acad: ".$obj_estado_academico['codigo']."\n";
                    $model_vestado_academico = new Vestadoacademico($conexion, $obj_estado_academico['codigo']);

                    $resultado_calculo = $model_vestado_academico->calcular_porcentaje_asistencia();

                    /*
                    echo "\n";
                    print_r($resultado_calculo);
                    echo "\n";
                     */

                    $cantidad++;
                }

                echo "\nTotal procesados: ".$cantidad."\nTiempo de calculo: ".(time() - $time_ini)."\n";
                //echo "</pre>";

                /*
                echo "<pre>";
                print_r($query_result);
                echo "</pre>";
                 */

        } catch (Exception $exc) {
            echo $exc->getTraceAsString();
        }

        echo "</pre>";
    }
    
    function convertir_mails_consultas_en_aspirantes($desdeFilial = null){
        $conexion = $this->load->database("mails_consultas", true);
        $conexion->select("*");
        $conexion->from("mails_consultas.mails_consultas");
        $conexion->where("DATE(fechahora) >=", "2015-08-01");
        $conexion->where("cod_filial <>", "0");
        if ($desdeFilial != null){
            $conexion->where("cod_filial >=", $desdeFilial); // ir variando esta columna
        }
        $conexion->order_by("cod_filial", "ASC");
        $conexion->order_by("fechahora", "ASC");
        $query = $conexion->get();
        $arrConsultas = $query->result_array(); 
        echo "<pre>"; print_r($arrConsultas[0]); echo "</pre>";
        $codFilialBase = 0;
        $cantidad_insertada = 0;
        foreach ($arrConsultas as $consulta){
            $codFilial = $consulta['cod_filial'];
            if ($codFilial <> $codFilialBase){
                if ($codFilialBase <> 0){
                    echo "cantidad: $cantidad_insertada<br>";
                }
                $codFilialBase = $codFilial;
                echo "filial: $codFilial<br>";
                $conexion = $this->load->database($codFilial, true);
                $myFilial = new Vfiliales($conexion, $codFilial);
                $tipoDocumento = Vpaises::getDocumentoDefaultPais($myFilial->pais);                
                $cantidad_insertada = 0;
            }
            $email = trim($consulta['mail']);
            $nombre = $consulta['nombre'];
            $temp = trim($nombre);
            $condiciones = array("email" => $email);
            $cantidad = Vaspirantes::listarAspirantes($conexion, $condiciones, null, null, null, true);
            if ($temp <> '' && $cantidad == 0){
                echo "********************<br>";
                echo "filial: $codFilial<br>";
                echo "email $email<br>";
                echo "********************<br>";
                $temp = explode(" ", $nombre);
                if (count($temp) == 1){
                    $temp = explode(",", $nombre);
                }
                if (count($temp) > 1){
                    $nombre = $temp[0];
                    $apellido = $temp[1];
                } else {
                    $apellido = " ";
                }
                $telefono = trim($consulta['telefono']);
                $tipoAsunto = $consulta['tipo_asunto'];
                $codAsunto = $consulta['cod_curso_asunto'];
                $fecha = $consulta['fechahora'];
                $conexion->trans_begin();
                $myAspirante = new Vaspirantes($conexion);
                $myAspirante->apellido = $apellido;
                $myAspirante->calle = '';
                $myAspirante->calle_numero = 0;
                $myAspirante->comonosconocio = 73;
                $myAspirante->documento = ' ';
                $myAspirante->email = strtolower($email);
                $myAspirante->email_enviado = 1;
                $myAspirante->fechaalta = $fecha;
                $myAspirante->nombre = $nombre;
                $myAspirante->tipo = $tipoDocumento;
                $myAspirante->tipo_contacto = 'EMAIL';
                $myAspirante->usuario_creador = 868;
                $myAspirante->guardarAspirantes();
                if ($telefono <> ''){
                    $myTelefono = new Vtelefonos($conexion);
                    $myTelefono->baja = 0;
                    $myTelefono->cod_area = 0;
                    $myTelefono->numero = $telefono;
                    $myTelefono->tipo_telefono = 'fijo';
                    $myTelefono->guardarTelefonos();
                    $myAspirante->setTelefonosAspirante($myTelefono->getCodigo(), 1);
                }
                if ($tipoAsunto == 'curso' && $codAsunto <> '' && $codAsunto <> 0){
                    $myAspirante->setCursosDeInteres(array($codAsunto), array(4), array(0), array('normal'));
                }
                if ($conexion->trans_status()){
                    $conexion->trans_commit();
                    $cantidad_insertada++;
                } else {
                    echo "[".$conexion->_error_number()."] ".$conexion->_error_message()."<br>";
                    $conexion->trans_rollback();
                    die("fin del script por error");
                }
            }
        }
        
        
        echo "cantidad de consultas ".count($arrConsultas)."<br>";
        
        /*
         * select * from mails_consultas where date(fechahora) >= '2015-08-01' and cod_filial <> 0 
         * order by cod_filial asc, fechahora asc;
         */

        
        
    }

    function buscar_horario_streaming(){
        $conexion = $this->load->database("default", true);
        $query = "SELECT 'Rafaela' AS filial, general.materias.codigo, general.materias.nombre_es, `1`.horarios.dia, `1`.horarios.horadesde, materias.cod_tipo_materia FROM `1`.horarios INNER JOIN general.materias ON general.materias.codigo = `1`.horarios.cod_materia AND general.materias.cod_tipo_materia IN ('teorico') WHERE dia >= '2016-01-01' AND dia <= '2016-12-31' AND baja = 0 ".
"UNION SELECT 'San Francisco' AS filial, general.materias.codigo, general.materias.nombre_es, `2`.horarios.dia, `2`.horarios.horadesde, materias.cod_tipo_materia FROM `2`.horarios INNER JOIN general.materias ON general.materias.codigo = `2`.horarios.cod_materia AND general.materias.cod_tipo_materia IN ('teorico') WHERE dia >= '2016-01-01' AND dia <= '2016-12-31' AND baja = 0 ".
"UNION SELECT 'Gualeguaychú' AS filial, general.materias.codigo, general.materias.nombre_es, `3`.horarios.dia, `3`.horarios.horadesde, materias.cod_tipo_materia FROM `3`.horarios INNER JOIN general.materias ON general.materias.codigo = `3`.horarios.cod_materia AND general.materias.cod_tipo_materia IN ('teorico') WHERE dia >= '2016-01-01' AND dia <= '2016-12-31' AND baja = 0 ".
"UNION SELECT 'Concordia' AS filial, general.materias.codigo, general.materias.nombre_es, `4`.horarios.dia, `4`.horarios.horadesde, materias.cod_tipo_materia FROM `4`.horarios INNER JOIN general.materias ON general.materias.codigo = `4`.horarios.cod_materia AND general.materias.cod_tipo_materia IN ('teorico') WHERE dia >= '2016-01-01' AND dia <= '2016-12-31' AND baja = 0 ".
"UNION SELECT 'Santa Fe' AS filial, general.materias.codigo, general.materias.nombre_es, `5`.horarios.dia, `5`.horarios.horadesde, materias.cod_tipo_materia FROM `5`.horarios INNER JOIN general.materias ON general.materias.codigo = `5`.horarios.cod_materia AND general.materias.cod_tipo_materia IN ('teorico') WHERE dia >= '2016-01-01' AND dia <= '2016-12-31' AND baja = 0 ".
"UNION SELECT 'Paraná' AS filial, general.materias.codigo, general.materias.nombre_es, `6`.horarios.dia, `6`.horarios.horadesde, materias.cod_tipo_materia FROM `6`.horarios INNER JOIN general.materias ON general.materias.codigo = `6`.horarios.cod_materia AND general.materias.cod_tipo_materia IN ('teorico') WHERE dia >= '2016-01-01' AND dia <= '2016-12-31' AND baja = 0 ".
"UNION SELECT 'Zarate' AS filial, general.materias.codigo, general.materias.nombre_es, `7`.horarios.dia, `7`.horarios.horadesde, materias.cod_tipo_materia FROM `7`.horarios INNER JOIN general.materias ON general.materias.codigo = `7`.horarios.cod_materia AND general.materias.cod_tipo_materia IN ('teorico') WHERE dia >= '2016-01-01' AND dia <= '2016-12-31' AND baja = 0 ".
"UNION SELECT 'Córdoba' AS filial, general.materias.codigo, general.materias.nombre_es, `8`.horarios.dia, `8`.horarios.horadesde, materias.cod_tipo_materia FROM `8`.horarios INNER JOIN general.materias ON general.materias.codigo = `8`.horarios.cod_materia AND general.materias.cod_tipo_materia IN ('teorico') WHERE dia >= '2016-01-01' AND dia <= '2016-12-31' AND baja = 0 ".
"UNION SELECT 'San Nicolás' AS filial, general.materias.codigo, general.materias.nombre_es, `10`.horarios.dia, `10`.horarios.horadesde, materias.cod_tipo_materia FROM `10`.horarios INNER JOIN general.materias ON general.materias.codigo = `10`.horarios.cod_materia AND general.materias.cod_tipo_materia IN ('teorico') WHERE dia >= '2016-01-01' AND dia <= '2016-12-31' AND baja = 0 ".
"UNION SELECT 'Resistencia' AS filial, general.materias.codigo, general.materias.nombre_es, `11`.horarios.dia, `11`.horarios.horadesde, materias.cod_tipo_materia FROM `11`.horarios INNER JOIN general.materias ON general.materias.codigo = `11`.horarios.cod_materia AND general.materias.cod_tipo_materia IN ('teorico') WHERE dia >= '2016-01-01' AND dia <= '2016-12-31' AND baja = 0 ".
"UNION SELECT 'Corrientes' AS filial, general.materias.codigo, general.materias.nombre_es, `12`.horarios.dia, `12`.horarios.horadesde, materias.cod_tipo_materia FROM `12`.horarios INNER JOIN general.materias ON general.materias.codigo = `12`.horarios.cod_materia AND general.materias.cod_tipo_materia IN ('teorico') WHERE dia >= '2016-01-01' AND dia <= '2016-12-31' AND baja = 0 ".
"UNION SELECT 'Concepción Del Uruguay' AS filial, general.materias.codigo, general.materias.nombre_es, `13`.horarios.dia, `13`.horarios.horadesde, materias.cod_tipo_materia FROM `13`.horarios INNER JOIN general.materias ON general.materias.codigo = `13`.horarios.cod_materia AND general.materias.cod_tipo_materia IN ('teorico') WHERE dia >= '2016-01-01' AND dia <= '2016-12-31' AND baja = 0 ".
"UNION SELECT 'Reconquista' AS filial, general.materias.codigo, general.materias.nombre_es, `14`.horarios.dia, `14`.horarios.horadesde, materias.cod_tipo_materia FROM `14`.horarios INNER JOIN general.materias ON general.materias.codigo = `14`.horarios.cod_materia AND general.materias.cod_tipo_materia IN ('teorico') WHERE dia >= '2016-01-01' AND dia <= '2016-12-31' AND baja = 0 ".
"UNION SELECT 'San Luis' AS filial, general.materias.codigo, general.materias.nombre_es, `15`.horarios.dia, `15`.horarios.horadesde, materias.cod_tipo_materia FROM `15`.horarios INNER JOIN general.materias ON general.materias.codigo = `15`.horarios.cod_materia AND general.materias.cod_tipo_materia IN ('teorico') WHERE dia >= '2016-01-01' AND dia <= '2016-12-31' AND baja = 0 ".
"UNION SELECT 'Pergamino' AS filial, general.materias.codigo, general.materias.nombre_es, `16`.horarios.dia, `16`.horarios.horadesde, materias.cod_tipo_materia FROM `16`.horarios INNER JOIN general.materias ON general.materias.codigo = `16`.horarios.cod_materia AND general.materias.cod_tipo_materia IN ('teorico') WHERE dia >= '2016-01-01' AND dia <= '2016-12-31' AND baja = 0 ".
"UNION SELECT 'Santiago Del Estero' AS filial, general.materias.codigo, general.materias.nombre_es, `17`.horarios.dia, `17`.horarios.horadesde, materias.cod_tipo_materia FROM `17`.horarios INNER JOIN general.materias ON general.materias.codigo = `17`.horarios.cod_materia AND general.materias.cod_tipo_materia IN ('teorico') WHERE dia >= '2016-01-01' AND dia <= '2016-12-31' AND baja = 0 ".
"UNION SELECT 'Lomas De Zamora' AS filial, general.materias.codigo, general.materias.nombre_es, `18`.horarios.dia, `18`.horarios.horadesde, materias.cod_tipo_materia FROM `18`.horarios INNER JOIN general.materias ON general.materias.codigo = `18`.horarios.cod_materia AND general.materias.cod_tipo_materia IN ('teorico') WHERE dia >= '2016-01-01' AND dia <= '2016-12-31' AND baja = 0 ".
"UNION SELECT 'Jujuy' AS filial, general.materias.codigo, general.materias.nombre_es, `19`.horarios.dia, `19`.horarios.horadesde, materias.cod_tipo_materia FROM `19`.horarios INNER JOIN general.materias ON general.materias.codigo = `19`.horarios.cod_materia AND general.materias.cod_tipo_materia IN ('teorico') WHERE dia >= '2016-01-01' AND dia <= '2016-12-31' AND baja = 0 ".
"UNION SELECT 'Rosario' AS filial, general.materias.codigo, general.materias.nombre_es, `20`.horarios.dia, `20`.horarios.horadesde, materias.cod_tipo_materia FROM `20`.horarios INNER JOIN general.materias ON general.materias.codigo = `20`.horarios.cod_materia AND general.materias.cod_tipo_materia IN ('teorico') WHERE dia >= '2016-01-01' AND dia <= '2016-12-31' AND baja = 0 ".
"UNION SELECT 'Bahía Blanca' AS filial, general.materias.codigo, general.materias.nombre_es, `21`.horarios.dia, `21`.horarios.horadesde, materias.cod_tipo_materia FROM `21`.horarios INNER JOIN general.materias ON general.materias.codigo = `21`.horarios.cod_materia AND general.materias.cod_tipo_materia IN ('teorico') WHERE dia >= '2016-01-01' AND dia <= '2016-12-31' AND baja = 0 ".
"UNION SELECT 'Formosa' AS filial, general.materias.codigo, general.materias.nombre_es, `22`.horarios.dia, `22`.horarios.horadesde, materias.cod_tipo_materia FROM `22`.horarios INNER JOIN general.materias ON general.materias.codigo = `22`.horarios.cod_materia AND general.materias.cod_tipo_materia IN ('teorico') WHERE dia >= '2016-01-01' AND dia <= '2016-12-31' AND baja = 0 ".
"UNION SELECT 'Salta' AS filial, general.materias.codigo, general.materias.nombre_es, `23`.horarios.dia, `23`.horarios.horadesde, materias.cod_tipo_materia FROM `23`.horarios INNER JOIN general.materias ON general.materias.codigo = `23`.horarios.cod_materia AND general.materias.cod_tipo_materia IN ('teorico') WHERE dia >= '2016-01-01' AND dia <= '2016-12-31' AND baja = 0 ".
"UNION SELECT 'Posadas' AS filial, general.materias.codigo, general.materias.nombre_es, `24`.horarios.dia, `24`.horarios.horadesde, materias.cod_tipo_materia FROM `24`.horarios INNER JOIN general.materias ON general.materias.codigo = `24`.horarios.cod_materia AND general.materias.cod_tipo_materia IN ('teorico') WHERE dia >= '2016-01-01' AND dia <= '2016-12-31' AND baja = 0 ".
"UNION SELECT 'Florencio Varela' AS filial, general.materias.codigo, general.materias.nombre_es, `25`.horarios.dia, `25`.horarios.horadesde, materias.cod_tipo_materia FROM `25`.horarios INNER JOIN general.materias ON general.materias.codigo = `25`.horarios.cod_materia AND general.materias.cod_tipo_materia IN ('teorico') WHERE dia >= '2016-01-01' AND dia <= '2016-12-31' AND baja = 0 ".
"UNION SELECT 'San Juan' AS filial, general.materias.codigo, general.materias.nombre_es, `26`.horarios.dia, `26`.horarios.horadesde, materias.cod_tipo_materia FROM `26`.horarios INNER JOIN general.materias ON general.materias.codigo = `26`.horarios.cod_materia AND general.materias.cod_tipo_materia IN ('teorico') WHERE dia >= '2016-01-01' AND dia <= '2016-12-31' AND baja = 0 ".
"UNION SELECT 'Comodoro Rivadavia' AS filial, general.materias.codigo, general.materias.nombre_es, `27`.horarios.dia, `27`.horarios.horadesde, materias.cod_tipo_materia FROM `27`.horarios INNER JOIN general.materias ON general.materias.codigo = `27`.horarios.cod_materia AND general.materias.cod_tipo_materia IN ('teorico') WHERE dia >= '2016-01-01' AND dia <= '2016-12-31' AND baja = 0 ".
"UNION SELECT 'Junín' AS filial, general.materias.codigo, general.materias.nombre_es, `28`.horarios.dia, `28`.horarios.horadesde, materias.cod_tipo_materia FROM `28`.horarios INNER JOIN general.materias ON general.materias.codigo = `28`.horarios.cod_materia AND general.materias.cod_tipo_materia IN ('teorico') WHERE dia >= '2016-01-01' AND dia <= '2016-12-31' AND baja = 0 ".
"UNION SELECT 'Rio Cuarto' AS filial, general.materias.codigo, general.materias.nombre_es, `29`.horarios.dia, `29`.horarios.horadesde, materias.cod_tipo_materia FROM `29`.horarios INNER JOIN general.materias ON general.materias.codigo = `29`.horarios.cod_materia AND general.materias.cod_tipo_materia IN ('teorico') WHERE dia >= '2016-01-01' AND dia <= '2016-12-31' AND baja = 0 ".
"UNION SELECT 'San Rafael' AS filial, general.materias.codigo, general.materias.nombre_es, `30`.horarios.dia, `30`.horarios.horadesde, materias.cod_tipo_materia FROM `30`.horarios INNER JOIN general.materias ON general.materias.codigo = `30`.horarios.cod_materia AND general.materias.cod_tipo_materia IN ('teorico') WHERE dia >= '2016-01-01' AND dia <= '2016-12-31' AND baja = 0 ".
"UNION SELECT 'Catamarca' AS filial, general.materias.codigo, general.materias.nombre_es, `31`.horarios.dia, `31`.horarios.horadesde, materias.cod_tipo_materia FROM `31`.horarios INNER JOIN general.materias ON general.materias.codigo = `31`.horarios.cod_materia AND general.materias.cod_tipo_materia IN ('teorico') WHERE dia >= '2016-01-01' AND dia <= '2016-12-31' AND baja = 0 ".
"UNION SELECT 'La Plata' AS filial, general.materias.codigo, general.materias.nombre_es, `34`.horarios.dia, `34`.horarios.horadesde, materias.cod_tipo_materia FROM `34`.horarios INNER JOIN general.materias ON general.materias.codigo = `34`.horarios.cod_materia AND general.materias.cod_tipo_materia IN ('teorico') WHERE dia >= '2016-01-01' AND dia <= '2016-12-31' AND baja = 0 ".
"UNION SELECT 'Mendoza' AS filial, general.materias.codigo, general.materias.nombre_es, `35`.horarios.dia, `35`.horarios.horadesde, materias.cod_tipo_materia FROM `35`.horarios INNER JOIN general.materias ON general.materias.codigo = `35`.horarios.cod_materia AND general.materias.cod_tipo_materia IN ('teorico') WHERE dia >= '2016-01-01' AND dia <= '2016-12-31' AND baja = 0 ".
"UNION SELECT 'San Miguel' AS filial, general.materias.codigo, general.materias.nombre_es, `37`.horarios.dia, `37`.horarios.horadesde, materias.cod_tipo_materia FROM `37`.horarios INNER JOIN general.materias ON general.materias.codigo = `37`.horarios.cod_materia AND general.materias.cod_tipo_materia IN ('teorico') WHERE dia >= '2016-01-01' AND dia <= '2016-12-31' AND baja = 0 ".
"UNION SELECT 'Mar Del Plata' AS filial, general.materias.codigo, general.materias.nombre_es, `38`.horarios.dia, `38`.horarios.horadesde, materias.cod_tipo_materia FROM `38`.horarios INNER JOIN general.materias ON general.materias.codigo = `38`.horarios.cod_materia AND general.materias.cod_tipo_materia IN ('teorico') WHERE dia >= '2016-01-01' AND dia <= '2016-12-31' AND baja = 0 ".
"UNION SELECT 'Goya' AS filial, general.materias.codigo, general.materias.nombre_es, `39`.horarios.dia, `39`.horarios.horadesde, materias.cod_tipo_materia FROM `39`.horarios INNER JOIN general.materias ON general.materias.codigo = `39`.horarios.cod_materia AND general.materias.cod_tipo_materia IN ('teorico') WHERE dia >= '2016-01-01' AND dia <= '2016-12-31' AND baja = 0 ".
"UNION SELECT 'San Justo' AS filial, general.materias.codigo, general.materias.nombre_es, `40`.horarios.dia, `40`.horarios.horadesde, materias.cod_tipo_materia FROM `40`.horarios INNER JOIN general.materias ON general.materias.codigo = `40`.horarios.cod_materia AND general.materias.cod_tipo_materia IN ('teorico') WHERE dia >= '2016-01-01' AND dia <= '2016-12-31' AND baja = 0 ".
"UNION SELECT 'Tucumán' AS filial, general.materias.codigo, general.materias.nombre_es, `48`.horarios.dia, `48`.horarios.horadesde, materias.cod_tipo_materia FROM `48`.horarios INNER JOIN general.materias ON general.materias.codigo = `48`.horarios.cod_materia AND general.materias.cod_tipo_materia IN ('teorico') WHERE dia >= '2016-01-01' AND dia <= '2016-12-31' AND baja = 0 ".
"UNION SELECT 'Escobar' AS filial, general.materias.codigo, general.materias.nombre_es, `50`.horarios.dia, `50`.horarios.horadesde, materias.cod_tipo_materia FROM `50`.horarios INNER JOIN general.materias ON general.materias.codigo = `50`.horarios.cod_materia AND general.materias.cod_tipo_materia IN ('teorico') WHERE dia >= '2016-01-01' AND dia <= '2016-12-31' AND baja = 0 ".
"UNION SELECT 'Lanús' AS filial, general.materias.codigo, general.materias.nombre_es, `51`.horarios.dia, `51`.horarios.horadesde, materias.cod_tipo_materia FROM `51`.horarios INNER JOIN general.materias ON general.materias.codigo = `51`.horarios.cod_materia AND general.materias.cod_tipo_materia IN ('teorico') WHERE dia >= '2016-01-01' AND dia <= '2016-12-31' AND baja = 0 ".
"UNION SELECT 'Morón' AS filial, general.materias.codigo, general.materias.nombre_es, `52`.horarios.dia, `52`.horarios.horadesde, materias.cod_tipo_materia FROM `52`.horarios INNER JOIN general.materias ON general.materias.codigo = `52`.horarios.cod_materia AND general.materias.cod_tipo_materia IN ('teorico') WHERE dia >= '2016-01-01' AND dia <= '2016-12-31' AND baja = 0 ".
"UNION SELECT 'Tandil' AS filial, general.materias.codigo, general.materias.nombre_es, `53`.horarios.dia, `53`.horarios.horadesde, materias.cod_tipo_materia FROM `53`.horarios INNER JOIN general.materias ON general.materias.codigo = `53`.horarios.cod_materia AND general.materias.cod_tipo_materia IN ('teorico') WHERE dia >= '2016-01-01' AND dia <= '2016-12-31' AND baja = 0 ".
"UNION SELECT 'Venado Tuerto' AS filial, general.materias.codigo, general.materias.nombre_es, `54`.horarios.dia, `54`.horarios.horadesde, materias.cod_tipo_materia FROM `54`.horarios INNER JOIN general.materias ON general.materias.codigo = `54`.horarios.cod_materia AND general.materias.cod_tipo_materia IN ('teorico') WHERE dia >= '2016-01-01' AND dia <= '2016-12-31' AND baja = 0 ".
"UNION SELECT 'Capital Federal Paternal' AS filial, general.materias.codigo, general.materias.nombre_es, `55`.horarios.dia, `55`.horarios.horadesde, materias.cod_tipo_materia FROM `55`.horarios INNER JOIN general.materias ON general.materias.codigo = `55`.horarios.cod_materia AND general.materias.cod_tipo_materia IN ('teorico') WHERE dia >= '2016-01-01' AND dia <= '2016-12-31' AND baja = 0 ".
"UNION SELECT 'Neuquén' AS filial, general.materias.codigo, general.materias.nombre_es, `56`.horarios.dia, `56`.horarios.horadesde, materias.cod_tipo_materia FROM `56`.horarios INNER JOIN general.materias ON general.materias.codigo = `56`.horarios.cod_materia AND general.materias.cod_tipo_materia IN ('teorico') WHERE dia >= '2016-01-01' AND dia <= '2016-12-31' AND baja = 0 ".
"UNION SELECT 'Tres De Febrero' AS filial, general.materias.codigo, general.materias.nombre_es, `58`.horarios.dia, `58`.horarios.horadesde, materias.cod_tipo_materia FROM `58`.horarios INNER JOIN general.materias ON general.materias.codigo = `58`.horarios.cod_materia AND general.materias.cod_tipo_materia IN ('teorico') WHERE dia >= '2016-01-01' AND dia <= '2016-12-31' AND baja = 0 ".
"UNION SELECT 'Quilmes' AS filial, general.materias.codigo, general.materias.nombre_es, `60`.horarios.dia, `60`.horarios.horadesde, materias.cod_tipo_materia FROM `60`.horarios INNER JOIN general.materias ON general.materias.codigo = `60`.horarios.cod_materia AND general.materias.cod_tipo_materia IN ('teorico') WHERE dia >= '2016-01-01' AND dia <= '2016-12-31' AND baja = 0 ".
"UNION SELECT 'Eldorado' AS filial, general.materias.codigo, general.materias.nombre_es, `61`.horarios.dia, `61`.horarios.horadesde, materias.cod_tipo_materia FROM `61`.horarios INNER JOIN general.materias ON general.materias.codigo = `61`.horarios.cod_materia AND general.materias.cod_tipo_materia IN ('teorico') WHERE dia >= '2016-01-01' AND dia <= '2016-12-31' AND baja = 0 ".
"UNION SELECT 'Moreno' AS filial, general.materias.codigo, general.materias.nombre_es, `63`.horarios.dia, `63`.horarios.horadesde, materias.cod_tipo_materia FROM `63`.horarios INNER JOIN general.materias ON general.materias.codigo = `63`.horarios.cod_materia AND general.materias.cod_tipo_materia IN ('teorico') WHERE dia >= '2016-01-01' AND dia <= '2016-12-31' AND baja = 0 ".
"UNION SELECT 'Monte Grande' AS filial, general.materias.codigo, general.materias.nombre_es, `64`.horarios.dia, `64`.horarios.horadesde, materias.cod_tipo_materia FROM `64`.horarios INNER JOIN general.materias ON general.materias.codigo = `64`.horarios.cod_materia AND general.materias.cod_tipo_materia IN ('teorico') WHERE dia >= '2016-01-01' AND dia <= '2016-12-31' AND baja = 0 ".
"UNION SELECT 'Avellaneda' AS filial, general.materias.codigo, general.materias.nombre_es, `66`.horarios.dia, `66`.horarios.horadesde, materias.cod_tipo_materia FROM `66`.horarios INNER JOIN general.materias ON general.materias.codigo = `66`.horarios.cod_materia AND general.materias.cod_tipo_materia IN ('teorico') WHERE dia >= '2016-01-01' AND dia <= '2016-12-31' AND baja = 0 ".
"UNION SELECT 'Merlo' AS filial, general.materias.codigo, general.materias.nombre_es, `67`.horarios.dia, `67`.horarios.horadesde, materias.cod_tipo_materia FROM `67`.horarios INNER JOIN general.materias ON general.materias.codigo = `67`.horarios.cod_materia AND general.materias.cod_tipo_materia IN ('teorico') WHERE dia >= '2016-01-01' AND dia <= '2016-12-31' AND baja = 0 ".
"UNION SELECT 'Pilar' AS filial, general.materias.codigo, general.materias.nombre_es, `69`.horarios.dia, `69`.horarios.horadesde, materias.cod_tipo_materia FROM `69`.horarios INNER JOIN general.materias ON general.materias.codigo = `69`.horarios.cod_materia AND general.materias.cod_tipo_materia IN ('teorico') WHERE dia >= '2016-01-01' AND dia <= '2016-12-31' AND baja = 0 ".
"UNION SELECT 'San Isidro' AS filial, general.materias.codigo, general.materias.nombre_es, `70`.horarios.dia, `70`.horarios.horadesde, materias.cod_tipo_materia FROM `70`.horarios INNER JOIN general.materias ON general.materias.codigo = `70`.horarios.cod_materia AND general.materias.cod_tipo_materia IN ('teorico') WHERE dia >= '2016-01-01' AND dia <= '2016-12-31' AND baja = 0 ".
"UNION SELECT 'Almirante Brown' AS filial, general.materias.codigo, general.materias.nombre_es, `72`.horarios.dia, `72`.horarios.horadesde, materias.cod_tipo_materia FROM `72`.horarios INNER JOIN general.materias ON general.materias.codigo = `72`.horarios.cod_materia AND general.materias.cod_tipo_materia IN ('teorico') WHERE dia >= '2016-01-01' AND dia <= '2016-12-31' AND baja = 0 ".
"UNION SELECT 'Tigre' AS filial, general.materias.codigo, general.materias.nombre_es, `73`.horarios.dia, `73`.horarios.horadesde, materias.cod_tipo_materia FROM `73`.horarios INNER JOIN general.materias ON general.materias.codigo = `73`.horarios.cod_materia AND general.materias.cod_tipo_materia IN ('teorico') WHERE dia >= '2016-01-01' AND dia <= '2016-12-31' AND baja = 0 ".
"UNION SELECT 'Santo Tomé' AS filial, general.materias.codigo, general.materias.nombre_es, `93`.horarios.dia, `93`.horarios.horadesde, materias.cod_tipo_materia FROM `93`.horarios INNER JOIN general.materias ON general.materias.codigo = `93`.horarios.cod_materia AND general.materias.cod_tipo_materia IN ('teorico') WHERE dia >= '2016-01-01' AND dia <= '2016-12-31' AND baja = 0 ".
"UNION SELECT 'Villa Mercedes' AS filial, general.materias.codigo, general.materias.nombre_es, `1000`.horarios.dia, `1000`.horarios.horadesde, materias.cod_tipo_materia FROM `1000`.horarios INNER JOIN general.materias ON general.materias.codigo = `1000`.horarios.cod_materia AND general.materias.cod_tipo_materia IN ('teorico') WHERE dia >= '2016-01-01' AND dia <= '2016-12-31' AND baja = 0 ".
"ORDER BY codigo ASC, dia ASC, horadesde ASC";
        $query = $conexion->query($query);
        $arrMaterias = $query->result_array();
        echo "cantidad registros ".count($arrMaterias)."<br>";
        $arrResp = array();
        foreach ($arrMaterias as $materia){
            $codMateria = $materia['codigo'];
            $dia = $materia['dia'];
            $hora = $materia['horadesde'];
            $filial = utf8_decode($materia['filial']);
            $nombreMateria = utf8_decode($materia['nombre_es']);
            $arrResp[$nombreMateria][$dia][$hora][] = $filial;
        }
//        echo "<pre>"; print_r($arrResp); echo "</pre>"; die();
        foreach ($arrResp as $materia => $dia){ // $dia            
            foreach ($dia as $diaNombre => $hora){ // $diaNombre
                foreach ($hora as $horaNombre => $filiales){ // $horaNombre
                    if (count($filiales) > 1){
//                        echo "<pre>"; print_r($filiales); echo "</pre>"; die();
                        foreach($filiales as $filial){
                            echo "$materia;$diaNombre;$horaNombre;$filial<br>";
                        }                   

                    }
                }
            }
        }
    }

    //ALUMNOS NUEVO CAMPUS
    function  alumnos_nuevo_campus()
    {
        $conexion = $this->load->database("default", true);
        $arrFiliales = Vfiliales::listarFiliales($conexion, array("version_sistema" => 2, "baja" => "0"));

        $conexion_alumnos = $this->load->database("alumnos", true);

        $conexion_campus = $this->load->database("campus", true);

        $i = 0;

        foreach ($arrFiliales as $filial)
        {
            if($i < 3)
            {
                $cod_filial = $filial['codigo'];
                $conexion = $this->load->database($filial['codigo'], true);

                $sql = "SELECT AL.codigo AS codigo_alumno, AL.nombre, AL.apellido, AL.email AS email, AL.sexo, CL.pass, C.cod_filial
                            FROM `$cod_filial`.alumnos  AL
                            JOIN login_filial C ON C.cod_alumno = AL.codigo AND C.cod_filial = '$cod_filial'
                            JOIN login CL ON CL.codigo = C.cod_login
                            WHERE AL.baja = 'habilitada'
                            AND AL.email != 'NULL'
                            GROUP BY codigo_alumno";
                $query = $conexion_alumnos->query($sql);
                $result = $query->result_array();

                echo $sql.'<br><br>';

                echo count($result).' registros <br>';

                print '<pre>';
                print_r($result);
                print '</pre>';
            }

            $i++;
        }
    }

    //ALTA ALUMNOS NUEVO CAMPUS
    function alta_alumnos_nuevo_campus()
    {

    }
   
    //Borrar alertas alumnos invalidas.
    function borrar_alertas_invalidas(){

        $conexion = $this->load->database("default", true);
        $arrFiliales = Vfiliales::listarFiliales($conexion, array("version_sistema" => 2, "baja" => "0"));
        foreach ($arrFiliales as $filial) {
            $conexion = $this->load->database($filial['codigo'], true);
            $conexion->query("delete from .alertas_alumnos
                         WHERE 
                         (select estado from .alertas as ale where ale.codigo = alertas_alumnos.cod_alerta) <> 'enviado'
                         and alertas_alumnos.estado <> 'enviado'
                         and alertas_alumnos.cod_alumno not in(
                             select alumnos.codigo from ctacte
                             join alumnos on alumnos.codigo = ctacte.cod_alumno
                             join matriculas on matriculas.estado = 'habilitada' 
                             AND matriculas.codigo = ctacte.concepto
                             join matriculas_periodos on matriculas_periodos.cod_matricula = matriculas.codigo 
                             and (matriculas_periodos.estado = 'habilitada' OR matriculas_periodos.estado = 'finalizada')
                             where ctacte.importe > ctacte.pagado
                             AND ctacte.fechavenc < curdate()
                             AND ctacte.habilitado IN(1,2)
                             group by alumnos.codigo
                         )
                         and alertas_alumnos.cod_alumno in (
                             select alumnos.codigo from ctacte
                             join alumnos on alumnos.codigo = ctacte.cod_alumno
                             join matriculas on matriculas.estado = 'habilitada' 
                             AND matriculas.codigo = ctacte.concepto
                             join matriculas_periodos on matriculas_periodos.cod_matricula = matriculas.codigo 
                             and matriculas_periodos.estado <> 'inhabilitada'
                             where ctacte.importe > ctacte.pagado
                             AND ctacte.fechavenc < curdate()
                             AND ctacte.habilitado IN(1,2)
                             group by alumnos.codigo
                         );");
        }

    }

    function reporteAspirantesDani() {
        
        $conexiongral = $this->load->database("default", true);
        $arrFiliales = Vfiliales::listarFiliales($conexiongral, array("version_sistema" => 2, "baja" => "0"));
        $respuesta = array();
        $totales = array();
        $totalesPaises = array();
            echo "PAIS, LOCALIDAD, COD_COMONOSCONOCIO, COMONOSCONOCIO, CANTIDAD ASPIRANTES" . PHP_EOL;
        foreach ($arrFiliales as $filial) {
            $conexion = $this->load->database($filial['codigo'], true);
            $resp = $conexion->query("SELECT count(*) as cantidad, 
            como_nos_conocio.descripcion_es as forma, 
            comonosconocio as codigo 
            from aspirantes
            left join general.como_nos_conocio on aspirantes.comonosconocio = general.como_nos_conocio.codigo
            where fechaalta >= '2014-10-01'
            group by comonosconocio")->result_array();
            $localidad = $filial['ciudad'];
            $pais = new Vpaises($conexiongral, $filial['pais']);
            $nombrePais = $pais->pais;
            foreach($resp as $aspirantes){
                $codigo = $aspirantes['codigo'];
                $forma = $aspirantes['forma'];
                $cantidad = $aspirantes['cantidad'];
                echo "$nombrePais, $localidad, $codigo, $forma, $cantidad" . PHP_EOL;
            }
        }
    }


    function crear_tabla_rubros()
    {

        $conexion = $this->load->database("default", true);
        $arrFiliales = Vfiliales::listarFiliales($conexion, array("version_sistema" => 2, "baja" => "0"));


        foreach ($arrFiliales as $filial) {
            var_dump($filial['codigo']);

            $conexion->query("DROP TABLE `" . $filial['codigo'] . "`.rubros_caja");

                $conexion->query("CREATE TABLE `" . $filial['codigo'] . "`.`rubros_caja` (
                  `codigo` int(11) unsigned NOT NULL AUTO_INCREMENT,
                  `rubro` enum('EGRESOS','DEVOLUCION_PRESTAMO','RETIRO_UTILIDADES') NOT NULL,
                  `subrubro` enum('ALQUILER','ALUMNOS','EQUIPAMIENTO','GASTOS_BANCARIOS','IMPUESTOS','INFRAESTRUCTURA','LIMPIEZA','MATERIAS_PRIMAS','INSUMOS','PRODUCTOS','PUBLICIDAD','ROYALTY','SALARIOS','SEGUROS','SERVICIOS','VIATICOS','REDEFINIR','RETIRO','PRESTAMOS','MANTENIMIENTO_EDILICIO','MANTENIMIENTO_GENERAL','HONORARIOS','INSUMOS_FERRETERIA','INSUMOS_LIMPIEZA','INSUMOS_LIBRERIA','INSUMOS_INFORMATICOS','CORREO_Y_CADETERIA', 'INSUMOS_FARMACIA') NOT NULL,
                  `baja` tinyint(1) NOT NULL,
                   PRIMARY KEY (`codigo`)
                  ) ENGINE=InnoDB AUTO_INCREMENT=28 DEFAULT CHARSET=utf8");

                $conexion->query("INSERT INTO `" . $filial['codigo'] . "`.`rubros_caja` (codigo,rubro,subrubro,baja) VALUES
                                (1,'EGRESOS','ALQUILER',0)
                                ,(2,'EGRESOS','ALUMNOS',0)
                                ,(3,'EGRESOS','EQUIPAMIENTO',0)
                                ,(4,'EGRESOS','GASTOS_BANCARIOS',0)
                                ,(5,'EGRESOS','IMPUESTOS',0)
                                ,(6,'EGRESOS','INFRAESTRUCTURA',1)
                                ,(7,'EGRESOS','LIMPIEZA',1)
                                ,(8,'EGRESOS','MATERIAS_PRIMAS',0)
                                ,(9,'EGRESOS','INSUMOS',1)
                                ,(10,'EGRESOS','PRODUCTOS',0)
                                ;");
                $conexion->query("INSERT INTO `" . $filial['codigo'] . "`.`rubros_caja` (codigo,rubro,subrubro,baja) VALUES 
                                (11,'EGRESOS','PUBLICIDAD',0)
                                ,(12,'EGRESOS','ROYALTY',0)
                                ,(13,'EGRESOS','SALARIOS',0)
                                ,(14,'EGRESOS','SEGUROS',0)
                                ,(15,'EGRESOS','SERVICIOS',0)
                                ,(16,'EGRESOS','VIATICOS',0)
                                ,(17,'EGRESOS','REDEFINIR',1)
                                ,(19,'RETIRO_UTILIDADES','RETIRO',0)
                                ,(20,'EGRESOS','HONORARIOS',0)
                                ,(21,'EGRESOS','INSUMOS_FERRETERIA',0)
                                ;");

                $conexion->query("INSERT INTO `" . $filial['codigo'] . "`.`rubros_caja` (codigo,rubro,subrubro,baja) VALUES 
                                (22,'EGRESOS','INSUMOS_LIMPIEZA',0)
                                ,(23,'EGRESOS','INSUMOS_LIBRERIA',0)
                                ,(24,'EGRESOS','INSUMOS_INFORMATICOS',0)
                                ,(25,'EGRESOS','MANTENIMIENTO_EDILICIO',0)
                                ,(26,'EGRESOS','MANTENIMIENTO_GENERAL',0)
                                ,(27,'EGRESOS','CORREO_Y_CADETERIA',0)
                                ,(28,'EGRESOS','INSUMOS_FARMACIA', 0)
                                ;");

        }

    }

    function mudar_subrubros()
    {

        $conexion = $this->load->database("default", true);
        $arrFiliales = Vfiliales::listarFiliales($conexion, array("version_sistema" => 2, "baja" => "0"));


        foreach ($arrFiliales as $filial) {
           var_dump($filial['codigo']);
            if ($filial['codigo'] != 20) {
                $conexion->query("UPDATE `" . $filial['codigo'] . "`.`movimientos_caja` SET concepto = CASE
    WHEN concepto = 1 THEN 13
    WHEN concepto = 2 THEN 13
    WHEN concepto = 3 THEN 17
    WHEN concepto = 4 THEN 26
    WHEN concepto = 5 THEN 20
    WHEN concepto = 6 THEN 22
    WHEN concepto = 7 THEN 23
    WHEN concepto = 8 THEN 15
    WHEN concepto = 9 THEN 15
    WHEN concepto = 10 THEN 15
    WHEN concepto = 11 THEN 15
    WHEN concepto = 12 THEN 5
    WHEN concepto = 13 THEN 10
    WHEN concepto = 14 THEN 8
    WHEN concepto = 15 THEN 13
    WHEN concepto = 16 THEN 20
    WHEN concepto = 17 THEN 17
    WHEN concepto = 18 THEN 13
    WHEN concepto = 19 THEN 16
    WHEN concepto = 20 THEN 11
    WHEN concepto = 21 THEN 1
    WHEN concepto = 22 THEN 14
    WHEN concepto = 23 THEN 17
    WHEN concepto = 24 THEN 17
    WHEN concepto = 25 THEN 17
    WHEN concepto = 26 THEN 4
    WHEN concepto = 27 THEN 4
    WHEN concepto = 28 THEN 17
    WHEN concepto = 29 THEN 4  
    END
 WHERE concepto IN (1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29)");
            }


        }
    }

    function fixAlumnosEscobar(){
        $conexion = $this->load->database("50", true);
        $arrayMaterias= array(7,9,10);

        foreach($arrayMaterias as $materia){
            $query = $conexion->query("SELECT
                                codigo
                          FROM
	                            horarios
	                      WHERE 
                                cod_comision = 615
                                AND cod_materia = ".$materia."
                                and dia > '2016-06-01';");

            $arrCodHorarios = $query->result_array();

            foreach($arrCodHorarios as $cod_horario){
                $conexion->query("UPDATE
                                  matriculas_horarios 
                              SET 
                                  baja = 0 
                              WHERE 
                                  cod_horario = ".(int)$cod_horario['codigo']." 
                              AND 
                                  baja = 1 
                              AND 
                                  motivo_baja IS NULL;");
            }
        }
    }
    
    function addSubrubroDescartable(){
        $conexion = $this->load->database("default", true);
        $arrFiliales = Vfiliales::listarFiliales($conexion, array("version_sistema" => 2, "baja" => "0"));

        foreach ($arrFiliales as $filial) {
            $conexion->query("ALTER TABLE `" . $filial['codigo'] . "`.rubros_caja MODIFY COLUMN subrubro enum('ALQUILER','ALUMNOS','EQUIPAMIENTO','GASTOS_BANCARIOS','IMPUESTOS','INFRAESTRUCTURA','LIMPIEZA','MATERIAS_PRIMAS','INSUMOS','PRODUCTOS','PUBLICIDAD','ROYALTY','SALARIOS','SEGUROS','SERVICIOS','VIATICOS','REDEFINIR','RETIRO','PRESTAMOS','MANTENIMIENTO_EDILICIO','MANTENIMIENTO_GENERAL','HONORARIOS','INSUMOS_FERRETERIA','INSUMOS_LIMPIEZA','INSUMOS_LIBRERIA','INSUMOS_INFORMATICOS','CORREO_Y_CADETERIA','INSUMOS_FARMACIA','INSUMOS_DESCARTABLES') NOT NULL; ");

            $conexion->query("INSERT INTO `" . $filial['codigo'] . "`.`rubros_caja` (codigo,rubro,subrubro,baja) VALUES
                                (29,'EGRESOS','INSUMOS_DESCARTABLES',0);");
        }
    }

    function cargaComoNosConocioFiliales(){
        $conexion = $this->load->database("default", true);
        $conexion->db_debug = false;
        $arrFiliales = Vfiliales::listarFiliales($conexion, array("version_sistema" => 2, "baja" => "0", "estado" => "activa"));
        $como_nos_conocio = $this->input->get('cnc');

        if(!empty($como_nos_conocio)) {
            foreach ($arrFiliales as $filial) {
                try {
                    $result = $conexion->query("INSERT INTO general.como_nos_conocio_filiales (id_conocio,id_filial) VALUES
                                ($como_nos_conocio,{$filial['codigo']});");
                    if(!$result) {
                        throw new Exception('Error al crear');
                    }
                    echo 'Creado en la filial '.$filial['codigo'].'<br>';
                }
                catch (Exception $ex) {
                    echo $ex->getMessage().' En la filial '.$filial['codigo'].'<br>';
                }
            }
        }
    }

    //busca alumnos que por lo menos 1 materia no esta aprovada ou homologada en primero periodo y las materias de segundo periodo no tienen comision
    public function buscaAlumnosCursandoDosPeriodoAuto() {
        $ano = $this->input->get('ano')?$this->input->get('ano'):'2016';
        $conexion = $this->load->database('default', true);
        $arrFiliales = Vfiliales::listarFiliales($conexion, array("version_sistema" => 2, "baja" => "0", "estado" => "activa"));

        foreach ($arrFiliales as $filial) {
            $conexionFilial = $this->load->database($filial['codigo'], true);
            $alumnos = Valumnos::buscaAlumnosCursandoDosPeriodoSinComision($conexionFilial, $ano);
            if(!empty($alumnos)) {
                echo 'Filial '.$filial['codigo'].'<br>';
            }
            foreach ($alumnos as $alumno) {
                echo 'Codigo matricula: '.$alumno['mat_codigo'].', '.$alumno['nombre'].' '.$alumno['apellido'].'<br>';
                //cambiar segundo periodo para 'no cursa'
                $estadosacademicos = Vestadoacademico::getEstadoacademicoPeriodo($conexionFilial, $alumno['mat_codigo'], '2', 'cursando');
                foreach ($estadosacademicos as $estadoacademico) {
                    $estadoacademico_obj = new Testadoacademico($conexionFilial, $estadoacademico['codigo']);
                    $estadoacademico_obj->estado = 'no_curso';
                    $estadoacademico_obj->guardarEstadoacademico();
                }
            }
        }
    }

    //busca alumnos que tengan materias en primero y segundo periodo y que tenga comisiones en los dos
    public function buscaAlumnosCursandoDosPeriodoManual() {
        $ano = $this->input->get('ano')?$this->input->get('ano'):'2016';
        $conexion = $this->load->database('default', true);
        $arrFiliales = Vfiliales::listarFiliales($conexion, array("version_sistema" => 2, "baja" => "0", "estado" => "activa"));

        foreach ($arrFiliales as $filial) {
            $conexionFilial = $this->load->database($filial['codigo'], true);
            $alumnos = Valumnos::buscaAlumnosCursandoDosPeriodoConComision($conexionFilial, $ano);
            if(!empty($alumnos)) {
                echo 'Filial '.$filial['codigo'].'<br>';
            }
            foreach ($alumnos as $alumno) {
                echo 'Codigo matricula: '.$alumno['mat_codigo'].', '.$alumno['nombre'].' '.$alumno['apellido'].'<br>';
            }
        }
    }

    /**
     * Normalización del como nos conocio
     */
    public function creaTablaComoNosConocioNueva() {
        $conexion = $this->load->database("general", true);
        $conexion->db_debug = false;

        try {
            $result = $conexion->query('rename table `como_nos_conocio` to `como_nos_conocio_antigua`;');
            if(!$result) {
                throw new Exception('Falla al cambiar el nombre la tabla como_nos_conocio');
            }
            $result = $conexion->query('CREATE TABLE IF NOT EXISTS `general`.`como_nos_conocio` (
                                          `codigo` INT UNSIGNED ZEROFILL NOT NULL AUTO_INCREMENT COMMENT \'\',
                                          `descripcion_es` VARCHAR(255) NOT NULL COMMENT \'\',
                                          `descripcion_pt` VARCHAR(255) NOT NULL COMMENT \'\',
                                          `descripcion_en` VARCHAR(255) NOT NULL COMMENT \'\',
                                          `activo` TINYINT(1) NOT NULL COMMENT \'\',
                                          `lft` INT(7) UNSIGNED NOT NULL COMMENT \'\',
                                          `rgt` INT(7) UNSIGNED NOT NULL COMMENT \'\',
                                          PRIMARY KEY (`codigo`)  COMMENT \'\')
                                        ENGINE = InnoDB
                                        COMMENT = \'lft = left\nrgt = right\nson palabras reservadas en mysql\'');
            if(!$result) {
                throw new Exception('Falla al crea la tabla como_nos_conocio');
            }
            echo 'OK!';
        }
        catch (Exception $ex) {
            echo $ex->getMessage().'<br>';
        }
    }

    public function creaTablaComoNosConocioNuevaFilial() {
        $conexion = $this->load->database("general", true);
        $conexion->db_debug = false;

        try {
            $result = $conexion->query('rename table `como_nos_conocio_filiales` to `como_nos_conocio_filiales_antigua`');
            if(!$result) {
                throw new Exception('Falla al cambiar el nombre la tabla como_nos_conocio_filial');
            }
            $result = $conexion->query('CREATE TABLE IF NOT EXISTS `general`.`como_nos_conocio_filiales` (
                                          `id_filial` INT(11) NOT NULL COMMENT \'\',
                                          `id_conocio` INT UNSIGNED ZEROFILL NOT NULL COMMENT \'\',
                                          `activo` TINYINT(1) NOT NULL COMMENT \'\',
                                          PRIMARY KEY (`id_filial`, `id_conocio`)  COMMENT \'\',
                                          INDEX `fk_filiales_has_como_nos_conocio_nueva_como_nos_conocio_nue_idx` (`id_conocio` ASC)  COMMENT \'\',
                                          INDEX `fk_filiales_has_como_nos_conocio_nueva_filiales1_idx` (`id_filial` ASC)  COMMENT \'\',
                                          CONSTRAINT `fk_filiales_has_como_nos_conocio_nueva_filiales1`
                                            FOREIGN KEY (`id_filial`)
                                            REFERENCES `general`.`filiales` (`codigo`)
                                            ON DELETE NO ACTION
                                            ON UPDATE NO ACTION,
                                          CONSTRAINT `fk_filiales_has_como_nos_conocio_nueva_como_nos_conocio_nueva1`
                                            FOREIGN KEY (`id_conocio`)
                                            REFERENCES `general`.`como_nos_conocio` (`codigo`)
                                            ON DELETE NO ACTION
                                            ON UPDATE NO ACTION)
                                        ENGINE = InnoDB
                                        DEFAULT CHARACTER SET = utf8');
            if(!$result) {
                throw new Exception('Falla al crea la tabla como_nos_conocio_filial');
            }
            echo 'OK!';
        }
        catch (Exception $ex) {
            echo $ex->getMessage().'<br>';
        }
    }

    public function insertComoNosConocioDatos() {
        $conexion = $this->load->database("general", true);
        $conexion->db_debug = false;

        $conexion->trans_begin();
        $conexion->query("INSERT INTO `como_nos_conocio` (`codigo`,`descripcion_es`,`descripcion_pt`,`descripcion_en`,`activo`,`lft`,`rgt`) VALUES (0000000001,'AudioVisuales','AudioVisuales','AudioVisuales',1,1,26);");
        $conexion->query("INSERT INTO `como_nos_conocio` (`codigo`,`descripcion_es`,`descripcion_pt`,`descripcion_en`,`activo`,`lft`,`rgt`) VALUES (0000000002,'Impresos','Impresos','Impresos',1,27,52);");
        $conexion->query("INSERT INTO `como_nos_conocio` (`codigo`,`descripcion_es`,`descripcion_pt`,`descripcion_en`,`activo`,`lft`,`rgt`) VALUES (0000000003,'Digitales','Digitales','Digitales',1,53,132);");
        $conexion->query("INSERT INTO `como_nos_conocio` (`codigo`,`descripcion_es`,`descripcion_pt`,`descripcion_en`,`activo`,`lft`,`rgt`) VALUES (0000000004,'Personales','Personales','Personales',1,133,146);");
        $conexion->query("INSERT INTO `como_nos_conocio` (`codigo`,`descripcion_es`,`descripcion_pt`,`descripcion_en`,`activo`,`lft`,`rgt`) VALUES (0000000005,'TV','TV','TV',1,2,9);");
        $conexion->query("INSERT INTO `como_nos_conocio` (`codigo`,`descripcion_es`,`descripcion_pt`,`descripcion_en`,`activo`,`lft`,`rgt`) VALUES (0000000006,'Radio','Radio','Radio',1,10,13);");
        $conexion->query("INSERT INTO `como_nos_conocio` (`codigo`,`descripcion_es`,`descripcion_pt`,`descripcion_en`,`activo`,`lft`,`rgt`) VALUES (0000000007,'Cine','Cine','Cine',1,14,17);");
        $conexion->query("INSERT INTO `como_nos_conocio` (`codigo`,`descripcion_es`,`descripcion_pt`,`descripcion_en`,`activo`,`lft`,`rgt`) VALUES (0000000008,'Teatro','Teatro','CineTeatro',1,18,21);");
        $conexion->query("INSERT INTO `como_nos_conocio` (`codigo`,`descripcion_es`,`descripcion_pt`,`descripcion_en`,`activo`,`lft`,`rgt`) VALUES (0000000009,'Recital','Recital','Recital',1,22,25);");
        $conexion->query("INSERT INTO `como_nos_conocio` (`codigo`,`descripcion_es`,`descripcion_pt`,`descripcion_en`,`activo`,`lft`,`rgt`) VALUES (0000000010,'America TV - espacio publicitario','America TV - espacio publicitario','America TV - espacio publicitario',1,3,4);");
        $conexion->query("INSERT INTO `como_nos_conocio` (`codigo`,`descripcion_es`,`descripcion_pt`,`descripcion_en`,`activo`,`lft`,`rgt`) VALUES (0000000011,'Intratables PNT','Intratables PNT','Intratables PNT',1,5,6);");
        $conexion->query("INSERT INTO `como_nos_conocio` (`codigo`,`descripcion_es`,`descripcion_pt`,`descripcion_en`,`activo`,`lft`,`rgt`) VALUES (0000000012,'Intrusos PNT','Intrusos PNT','Intrusos PNT',1,7,8);");
        $conexion->query("INSERT INTO `como_nos_conocio` (`codigo`,`descripcion_es`,`descripcion_pt`,`descripcion_en`,`activo`,`lft`,`rgt`) VALUES (0000000013,'Volantes','Volantes','Volantes',1,28,33);");
        $conexion->query("INSERT INTO `como_nos_conocio` (`codigo`,`descripcion_es`,`descripcion_pt`,`descripcion_en`,`activo`,`lft`,`rgt`) VALUES (0000000014,'Via publica','Via publica','Via publica',1,34,39);");
        $conexion->query("INSERT INTO `como_nos_conocio` (`codigo`,`descripcion_es`,`descripcion_pt`,`descripcion_en`,`activo`,`lft`,`rgt`) VALUES (0000000015,'Omnibus','Omnibus','Omnibus',1,40,43);");
        $conexion->query("INSERT INTO `como_nos_conocio` (`codigo`,`descripcion_es`,`descripcion_pt`,`descripcion_en`,`activo`,`lft`,`rgt`) VALUES (0000000016,'Diario','Diario','Diario',1,44,47);");
        $conexion->query("INSERT INTO `como_nos_conocio` (`codigo`,`descripcion_es`,`descripcion_pt`,`descripcion_en`,`activo`,`lft`,`rgt`) VALUES (0000000017,'Revista','Revista','Revista',1,48,51);");
        $conexion->query("INSERT INTO `como_nos_conocio` (`codigo`,`descripcion_es`,`descripcion_pt`,`descripcion_en`,`activo`,`lft`,`rgt`) VALUES (0000000018,'En mano','En mano','En mano',1,29,30);");
        $conexion->query("INSERT INTO `como_nos_conocio` (`codigo`,`descripcion_es`,`descripcion_pt`,`descripcion_en`,`activo`,`lft`,`rgt`) VALUES (0000000019,'Domicilio','Domicilio','Domicilio',1,31,32);");
        $conexion->query("INSERT INTO `como_nos_conocio` (`codigo`,`descripcion_es`,`descripcion_pt`,`descripcion_en`,`activo`,`lft`,`rgt`) VALUES (0000000020,'Cartel Luminoso','Cartel Luminoso','Cartel Luminoso',1,35,36);");
        $conexion->query("INSERT INTO `como_nos_conocio` (`codigo`,`descripcion_es`,`descripcion_pt`,`descripcion_en`,`activo`,`lft`,`rgt`) VALUES (0000000021,'Afiche','Afiche','Cartel Luminoso',1,37,38);");
        $conexion->query("INSERT INTO `como_nos_conocio` (`codigo`,`descripcion_es`,`descripcion_pt`,`descripcion_en`,`activo`,`lft`,`rgt`) VALUES (0000000022,'Redes Sociales','Redes Sociales','Redes Sociales',1,54,93);");
        $conexion->query("INSERT INTO `como_nos_conocio` (`codigo`,`descripcion_es`,`descripcion_pt`,`descripcion_en`,`activo`,`lft`,`rgt`) VALUES (0000000023,'Buscadores','Buscadores','Buscadores',1,94,111);");
        $conexion->query("INSERT INTO `como_nos_conocio` (`codigo`,`descripcion_es`,`descripcion_pt`,`descripcion_en`,`activo`,`lft`,`rgt`) VALUES (0000000024,'Otros Sitios','Otros Sitios','Otros Sitios',1,112,115);");
        $conexion->query("INSERT INTO `como_nos_conocio` (`codigo`,`descripcion_es`,`descripcion_pt`,`descripcion_en`,`activo`,`lft`,`rgt`) VALUES (0000000025,'Blogs','Blogs','Blogs',1,116,119);");
        $conexion->query("INSERT INTO `como_nos_conocio` (`codigo`,`descripcion_es`,`descripcion_pt`,`descripcion_en`,`activo`,`lft`,`rgt`) VALUES (0000000026,'Mailing','Mailing','Mailing',1,55,56);");
        $conexion->query("INSERT INTO `como_nos_conocio` (`codigo`,`descripcion_es`,`descripcion_pt`,`descripcion_en`,`activo`,`lft`,`rgt`) VALUES (0000000027,'Facebook Soporte','Facebook Soporte','Facebook Soporte',1,57,58);");
        $conexion->query("INSERT INTO `como_nos_conocio` (`codigo`,`descripcion_es`,`descripcion_pt`,`descripcion_en`,`activo`,`lft`,`rgt`) VALUES (0000000028,'YouTube','YouTube','YouTube',1,59,60);");
        $conexion->query("INSERT INTO `como_nos_conocio` (`codigo`,`descripcion_es`,`descripcion_pt`,`descripcion_en`,`activo`,`lft`,`rgt`) VALUES (0000000029,'WhatsApp','WhatsApp','WhatsApp',1,61,62);");
        $conexion->query("INSERT INTO `como_nos_conocio` (`codigo`,`descripcion_es`,`descripcion_pt`,`descripcion_en`,`activo`,`lft`,`rgt`) VALUES (0000000030,'QQ','QQ','QQ',1,63,64);");
        $conexion->query("INSERT INTO `como_nos_conocio` (`codigo`,`descripcion_es`,`descripcion_pt`,`descripcion_en`,`activo`,`lft`,`rgt`) VALUES (0000000031,'WeChat','WeChat','WeChat',1,65,66);");
        $conexion->query("INSERT INTO `como_nos_conocio` (`codigo`,`descripcion_es`,`descripcion_pt`,`descripcion_en`,`activo`,`lft`,`rgt`) VALUES (0000000032,'Qzone','Qzone','Qzone',1,67,68);");
        $conexion->query("INSERT INTO `como_nos_conocio` (`codigo`,`descripcion_es`,`descripcion_pt`,`descripcion_en`,`activo`,`lft`,`rgt`) VALUES (0000000033,'LinkedIn','LinkedIn','LinkedIn',1,69,70);");
        $conexion->query("INSERT INTO `como_nos_conocio` (`codigo`,`descripcion_es`,`descripcion_pt`,`descripcion_en`,`activo`,`lft`,`rgt`) VALUES (0000000034,'Weibo','Weibo','Weibo',1,71,72);");
        $conexion->query("INSERT INTO `como_nos_conocio` (`codigo`,`descripcion_es`,`descripcion_pt`,`descripcion_en`,`activo`,`lft`,`rgt`) VALUES (0000000035,'Instagram','Instagram','Instagram',1,73,74);");
        $conexion->query("INSERT INTO `como_nos_conocio` (`codigo`,`descripcion_es`,`descripcion_pt`,`descripcion_en`,`activo`,`lft`,`rgt`) VALUES (0000000036,'Google+','Google+','Google+',1,75,76);");
        $conexion->query("INSERT INTO `como_nos_conocio` (`codigo`,`descripcion_es`,`descripcion_pt`,`descripcion_en`,`activo`,`lft`,`rgt`) VALUES (0000000037,'Twitter','Twitter','Twitter',1,77,78);");
        $conexion->query("INSERT INTO `como_nos_conocio` (`codigo`,`descripcion_es`,`descripcion_pt`,`descripcion_en`,`activo`,`lft`,`rgt`) VALUES (0000000038,'Line','Line','Line',1,79,80);");
        $conexion->query("INSERT INTO `como_nos_conocio` (`codigo`,`descripcion_es`,`descripcion_pt`,`descripcion_en`,`activo`,`lft`,`rgt`) VALUES (0000000039,'Tagged','Tagged','Tagged',1,81,82);");
        $conexion->query("INSERT INTO `como_nos_conocio` (`codigo`,`descripcion_es`,`descripcion_pt`,`descripcion_en`,`activo`,`lft`,`rgt`) VALUES (0000000040,'Habbo','Habbo','Habbo',1,83,84);");
        $conexion->query("INSERT INTO `como_nos_conocio` (`codigo`,`descripcion_es`,`descripcion_pt`,`descripcion_en`,`activo`,`lft`,`rgt`) VALUES (0000000041,'Hi5','Hi5','Hi5',1,85,86);");
        $conexion->query("INSERT INTO `como_nos_conocio` (`codigo`,`descripcion_es`,`descripcion_pt`,`descripcion_en`,`activo`,`lft`,`rgt`) VALUES (0000000042,'Tumblr','Tumblr','Tumblr',1,87,88);");
        $conexion->query("INSERT INTO `como_nos_conocio` (`codigo`,`descripcion_es`,`descripcion_pt`,`descripcion_en`,`activo`,`lft`,`rgt`) VALUES (0000000043,'SoundCloud','SoundCloud','SoundCloud',1,89,90);");
        $conexion->query("INSERT INTO `como_nos_conocio` (`codigo`,`descripcion_es`,`descripcion_pt`,`descripcion_en`,`activo`,`lft`,`rgt`) VALUES (0000000044,'Badoo','Badoo','Badoo',1,91,92);");
        $conexion->query("INSERT INTO `como_nos_conocio` (`codigo`,`descripcion_es`,`descripcion_pt`,`descripcion_en`,`activo`,`lft`,`rgt`) VALUES (0000000045,'Google','Google','Google',1,95,96);");
        $conexion->query("INSERT INTO `como_nos_conocio` (`codigo`,`descripcion_es`,`descripcion_pt`,`descripcion_en`,`activo`,`lft`,`rgt`) VALUES (0000000046,'MSN','MSN','MSN',1,97,98);");
        $conexion->query("INSERT INTO `como_nos_conocio` (`codigo`,`descripcion_es`,`descripcion_pt`,`descripcion_en`,`activo`,`lft`,`rgt`) VALUES (0000000047,'Search','Search','Search',1,99,100);");
        $conexion->query("INSERT INTO `como_nos_conocio` (`codigo`,`descripcion_es`,`descripcion_pt`,`descripcion_en`,`activo`,`lft`,`rgt`) VALUES (0000000048,'Live','Live','Live',1,101,102);");
        $conexion->query("INSERT INTO `como_nos_conocio` (`codigo`,`descripcion_es`,`descripcion_pt`,`descripcion_en`,`activo`,`lft`,`rgt`) VALUES (0000000049,'Terra','Terra','Terra',1,103,104);");
        $conexion->query("INSERT INTO `como_nos_conocio` (`codigo`,`descripcion_es`,`descripcion_pt`,`descripcion_en`,`activo`,`lft`,`rgt`) VALUES (0000000050,'AOL','AOL','AOL',1,105,106);");
        $conexion->query("INSERT INTO `como_nos_conocio` (`codigo`,`descripcion_es`,`descripcion_pt`,`descripcion_en`,`activo`,`lft`,`rgt`) VALUES (0000000051,'Altavista','Altavista','Altavista',1,107,108);");
        $conexion->query("INSERT INTO `como_nos_conocio` (`codigo`,`descripcion_es`,`descripcion_pt`,`descripcion_en`,`activo`,`lft`,`rgt`) VALUES (0000000052,'Ask','Ask','Ask',1,109,110);");
        $conexion->query("INSERT INTO `como_nos_conocio` (`codigo`,`descripcion_es`,`descripcion_pt`,`descripcion_en`,`activo`,`lft`,`rgt`) VALUES (0000000053,'Recomendado','Recomendado','Recomendado',1,134,137);");
        $conexion->query("INSERT INTO `como_nos_conocio` (`codigo`,`descripcion_es`,`descripcion_pt`,`descripcion_en`,`activo`,`lft`,`rgt`) VALUES (0000000054,'Fachada','Fachada','Fachada',1,138,141);");
        $conexion->query("INSERT INTO `como_nos_conocio` (`codigo`,`descripcion_es`,`descripcion_pt`,`descripcion_en`,`activo`,`lft`,`rgt`) VALUES (0000000055,'Expos','Expos','Expos',1,142,145);");
        $conexion->query("INSERT INTO `como_nos_conocio` (`codigo`,`descripcion_es`,`descripcion_pt`,`descripcion_en`,`activo`,`lft`,`rgt`) VALUES (0000000056,'Radio','Radio','Radio',1,11,12);");
        $conexion->query("INSERT INTO `como_nos_conocio` (`codigo`,`descripcion_es`,`descripcion_pt`,`descripcion_en`,`activo`,`lft`,`rgt`) VALUES (0000000057,'Cine','Cine','Cine',1,15,16);");
        $conexion->query("INSERT INTO `como_nos_conocio` (`codigo`,`descripcion_es`,`descripcion_pt`,`descripcion_en`,`activo`,`lft`,`rgt`) VALUES (0000000058,'Teatro','Teatro','Teatro',1,19,20);");
        $conexion->query("INSERT INTO `como_nos_conocio` (`codigo`,`descripcion_es`,`descripcion_pt`,`descripcion_en`,`activo`,`lft`,`rgt`) VALUES (0000000059,'Recital','Recital','Recital',1,23,24);");
        $conexion->query("INSERT INTO `como_nos_conocio` (`codigo`,`descripcion_es`,`descripcion_pt`,`descripcion_en`,`activo`,`lft`,`rgt`) VALUES (0000000060,'Omnibus','Omnibus','Omnibus',1,41,42);");
        $conexion->query("INSERT INTO `como_nos_conocio` (`codigo`,`descripcion_es`,`descripcion_pt`,`descripcion_en`,`activo`,`lft`,`rgt`) VALUES (0000000061,'Diario','Diario','Diario',1,45,46);");
        $conexion->query("INSERT INTO `como_nos_conocio` (`codigo`,`descripcion_es`,`descripcion_pt`,`descripcion_en`,`activo`,`lft`,`rgt`) VALUES (0000000062,'Revista','Revista','Revista',1,49,50);");
        $conexion->query("INSERT INTO `como_nos_conocio` (`codigo`,`descripcion_es`,`descripcion_pt`,`descripcion_en`,`activo`,`lft`,`rgt`) VALUES (0000000063,'Otros Sitios','Otros Sitios','Otros Sitios',1,113,114);");
        $conexion->query("INSERT INTO `como_nos_conocio` (`codigo`,`descripcion_es`,`descripcion_pt`,`descripcion_en`,`activo`,`lft`,`rgt`) VALUES (0000000064,'Blogs','Blogs','Blogs',1,117,118);");
        $conexion->query("INSERT INTO `como_nos_conocio` (`codigo`,`descripcion_es`,`descripcion_pt`,`descripcion_en`,`activo`,`lft`,`rgt`) VALUES (0000000065,'Recomendado','Recomendado','Recomendado',1,135,136);");
        $conexion->query("INSERT INTO `como_nos_conocio` (`codigo`,`descripcion_es`,`descripcion_pt`,`descripcion_en`,`activo`,`lft`,`rgt`) VALUES (0000000066,'Fachada','Fachada','Fachada',1,139,140);");
        $conexion->query("INSERT INTO `como_nos_conocio` (`codigo`,`descripcion_es`,`descripcion_pt`,`descripcion_en`,`activo`,`lft`,`rgt`) VALUES (0000000067,'Expos','Expos','Expos',1,143,144);");
        $conexion->query("INSERT INTO `como_nos_conocio` (`codigo`,`descripcion_es`,`descripcion_pt`,`descripcion_en`,`activo`,`lft`,`rgt`) VALUES (0000000068,'Publicidad','Publicidad','Publicidad',1,120,127);");
        $conexion->query("INSERT INTO `como_nos_conocio` (`codigo`,`descripcion_es`,`descripcion_pt`,`descripcion_en`,`activo`,`lft`,`rgt`) VALUES (0000000069,'Facebook','Facebook','Facebook',1,121,122);");
        $conexion->query("INSERT INTO `como_nos_conocio` (`codigo`,`descripcion_es`,`descripcion_pt`,`descripcion_en`,`activo`,`lft`,`rgt`) VALUES (0000000070,'Publicidad Web','Publicidad Web','Publicidad Web',1,123,124);");
        $conexion->query("INSERT INTO `como_nos_conocio` (`codigo`,`descripcion_es`,`descripcion_pt`,`descripcion_en`,`activo`,`lft`,`rgt`) VALUES (0000000071,'Google AdWords','Google AdWords','Google AdWords',1,125,126);");
        $conexion->query("INSERT INTO `como_nos_conocio` (`codigo`,`descripcion_es`,`descripcion_pt`,`descripcion_en`,`activo`,`lft`,`rgt`) VALUES (0000000072,'Web','Web','Web',1,128,131);");
        $conexion->query("INSERT INTO `como_nos_conocio` (`codigo`,`descripcion_es`,`descripcion_pt`,`descripcion_en`,`activo`,`lft`,`rgt`) VALUES (0000000073,'Web','Web','Web',1,129,130);");
        $conexion->query("INSERT INTO `como_nos_conocio` (`codigo`,`descripcion_es`,`descripcion_pt`,`descripcion_en`,`activo`,`lft`,`rgt`) VALUES (0000000074,'Otro','Otro','Otro',1,147,152);");
        $conexion->query("INSERT INTO `como_nos_conocio` (`codigo`,`descripcion_es`,`descripcion_pt`,`descripcion_en`,`activo`,`lft`,`rgt`) VALUES (0000000075,'Otro','Otro','Otro',1,148,151);");
        $conexion->query("INSERT INTO `como_nos_conocio` (`codigo`,`descripcion_es`,`descripcion_pt`,`descripcion_en`,`activo`,`lft`,`rgt`) VALUES (0000000076,'Otro','Otro','Otro',1,149,150);");
        $conexion->trans_commit();
    }

    public function creaSeccionIgaCloud() {
        $conexion = $this->load->database("general", true);
        $conexion->db_debug = false;

        try {
            $result = $conexion->query('insert into general.secciones
                                          (slug, id_seccion_padre, menu_tipo, method, categoria, control, prioridad, id_atajo, grupo)
                                          values(\'config_igacloud\', \'51\', \'tab\', \'config_igacloud\', \'ajustes\', \'configuracion\', \'0\', null, null)');
            if(!$result) {
                throw new Exception('Falla al crear la seccion config_igacloud');
            }
            echo 'OK!';
        }
        catch (Exception $ex) {
            echo $ex->getMessage().'<br>';
        }
    }

    public function insertComoNosConocio() {
        $conexion = $this->load->database("general", true);
        $conexion->db_debug = true;
        $como_nos_conocio = new Vcomo_nos_conocio($conexion);
        $codigo_padre = $this->input->get('padre');
        $como_nos_conocio->descripcion_es = $this->input->get('es');
        $como_nos_conocio->descripcion_pt = $this->input->get('es');
        $como_nos_conocio->descripcion_en = $this->input->get('es');
        $como_nos_conocio->activo = true;

        try {
            $result = $como_nos_conocio->insert($codigo_padre);
            if(!$result) {
                throw new Exception('Falla al crear iten en como_nos_conocio');
            }
            echo 'OK!';
        }
        catch (Exception $ex) {
            echo $ex->getMessage().'<br>';
        }
    }

    public function deleteComoNosConocio() {
        $conexion = $this->load->database("general", true);
        $conexion->db_debug = true;
        $como_nos_conocio = new Vcomo_nos_conocio($conexion);
        $codigo = $this->input->get('id');

        try {
            $result = $como_nos_conocio->delete($codigo);
            if(!$result) {
                throw new Exception('Falla al borrar iten en como_nos_conocio');
            }
            echo 'OK!';
        }
        catch (Exception $ex) {
            echo $ex->getMessage().'<br>';
        }
    }

    public function creaArrayNormalizacionComoNosConocio() {
        return array(
            array(
                'viejo' => array(2),
                'nuevo' => 10
            ),
            array(
                'viejo' => array(1),
                'nuevo' => 56
            ),
            array(
                'viejo' => array(3,11,21),
                'nuevo' => 18
            ),
            array(
                'viejo' => array(12,13),
                'nuevo' => 19
            ),
            array(
                'viejo' => array(6),
                'nuevo' => 21
            ),
            array(
                'viejo' => array(28,31),
                'nuevo' => 60
            ),
            array(
                'viejo' => array(16,17,18),
                'nuevo' => 61
            ),
            array(
                'viejo' => array(15,30),
                'nuevo' => 62
            ),
            array(
                'viejo' => array(4),
                'nuevo' => 26
            ),
            array(
                'viejo' => array(8,9),
                'nuevo' => 27
            ),
            array(
                'viejo' => array(19),
                'nuevo' => 37
            ),
            array(
                'viejo' => array(14),
                'nuevo' => 45
            ),
            array(
                'viejo' => array(22),
                'nuevo' => 64
            ),
            array(
                'viejo' => array(33),
                'nuevo' => 69
            ),
            array(
                'viejo' => array(32),
                'nuevo' => 70
            ),
            array(
                'viejo' => array(34),
                'nuevo' => 71
            ),
            array(
                'viejo' => array(5),
                'nuevo' => 73
            ),
            array(
                'viejo' => array(10,20,26),
                'nuevo' => 65
            ),
            array(
                'viejo' => array(27),
                'nuevo' => 66
            ),
            array(
                'viejo' => array(25),
                'nuevo' => 67
            ),
            array(
                'viejo' => array(7,23,24,29),
                'nuevo' => 76
            ),
        );
    }

    public function normalizaComoNosConocioAlumnos() {
        $arrayNormal = $this->creaArrayNormalizacionComoNosConocio();
        $conexion = $this->load->database("default", true);
        $conexion->db_debug = false;
        $arrFiliales = Vfiliales::listarFiliales($conexion, array("version_sistema" => 2, "baja" => "0", "estado" => "activa"));

        foreach ($arrFiliales as $filial) {
            $conexionFilial = $this->load->database($filial['codigo'], true);
            $conexionFilial->db_debug = false;
            echo 'Filial '.$filial['codigo'].'<br>';
            foreach ($arrayNormal as $normal) {
                $viejo = implode(',', $normal['viejo']);
                $query = "update alumnos set comonosconocio = {$normal['nuevo']} where comonosconocio in ({$viejo})";
                echo $query.'<br>';
                try {
                    $result = $conexionFilial->query($query);

                    if(!$result) {
                        throw new Exception('Falla al normalizar tabla de alumnos');
                    }
                    echo 'ok<br>';
                }
                catch (Exception $ex) {
                    echo $ex->getMessage().'<br>';
                }
            }
        }
    }

    public function normalizaComoNosConocioAspirantes() {
        $arrayNormal = $this->creaArrayNormalizacionComoNosConocio();
        $conexion = $this->load->database("default", true);
        $conexion->db_debug = false;
        $arrFiliales = Vfiliales::listarFiliales($conexion, array("version_sistema" => 2, "baja" => "0", "estado" => "activa"));

        foreach ($arrFiliales as $filial) {
            $conexionFilial = $this->load->database($filial['codigo'], true);
            $conexionFilial->db_debug = false;
            echo 'Filial '.$filial['codigo'].'<br>';
            foreach ($arrayNormal as $normal) {
                $viejo = implode(',', $normal['viejo']);
                $query = "update aspirantes set comonosconocio = {$normal['nuevo']} where comonosconocio in ({$viejo})";
                echo $query.'<br>';
                try {
                    $result = $conexionFilial->query($query);

                    if(!$result) {
                        throw new Exception('Falla al normalizar tabla de aspirantes');
                    }
                    echo 'ok<br>';
                }
                catch (Exception $ex) {
                    echo $ex->getMessage().'<br>';
                }
            }
        }
    }

    public function normalizaComoNosConocioConsultasWeb() {
        $arrayNormal = $this->creaArrayNormalizacionComoNosConocio();
        $conexion = $this->load->database("mails_consultas", true);
        $conexion->db_debug = false;

        foreach ($arrayNormal as $normal) {
            $viejo = implode(',', $normal['viejo']);
            $query = "update mails_consultas set como_nos_conocio_codigo = {$normal['nuevo']} where como_nos_conocio_codigo in ({$viejo})";
            echo $query.'<br>';
            try {
                $result = $conexion->query($query);

                if(!$result) {
                    throw new Exception('Falla al normalizar tabla de mails_consultas');
                }
                echo 'ok<br>';
            }
            catch (Exception $ex) {
                echo $ex->getMessage().'<br>';
            }
        }
    }

    public function activarComoNosConocioFiliales() {
        $cnc = array(10,11,12,56,18,19,20,21,60,61,62,26,27,28,35,45,46,64,69,70,71,65,66,67);
        $conexion = $this->load->database("default", true);
        $conexion->db_debug = false;
        $arrFiliales = Vfiliales::listarFiliales($conexion, array("version_sistema" => 2, "baja" => "0", "estado" => "activa"));

        foreach ($arrFiliales as $filial) {
            echo 'Filial ' . $filial['codigo'] . '<br>';
            foreach($cnc as $como_nos_conocio) {
                $query = "insert into como_nos_conocio_filiales values({$filial['codigo']}, $como_nos_conocio, 1);";
                try {
                    $result = $conexion->query($query);

                    if(!$result) {
                        throw new Exception("Falla al crear como_nos_conocio $como_nos_conocio en la filial {$filial['codigo']}");
                    }
                    echo 'ok<br>';
                }
                catch (Exception $ex) {
                    echo $ex->getMessage().'<br>';
                }
            }
        }
    }
    
    public function agregarCodSub(){
        $conexion = $this->load->database("default", true);
        $conexion->db_debug = false;
        $arrFiliales = Vfiliales::listarFiliales($conexion, array("version_sistema" => 2, "baja" => "0", "estado" => "activa"));
        foreach ($arrFiliales as $filial) {
          $query = "ALTER TABLE `" . $filial['codigo']."`.articulos_categorias ADD cod_rubros_caja int(11) DEFAULT 17 NOT NULL;";
            try {
                $result = $conexion->query($query);

                if(!$result) {
                    throw new Exception("Falla al crear columna en la filial {$filial['codigo']}");
                }
                echo 'ok<br>';
            }
            catch (Exception $ex) {
                echo $ex->getMessage().'<br>';
            }
        }


    }
}

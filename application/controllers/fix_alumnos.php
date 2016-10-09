<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class Fix_alumnos extends CI_Controller {
    
    public function __construct() {
        parent::__construct();
        $this->lang->load(get_idioma(), get_idioma());
        $this->seccion = session_method();
        $conexion = $this->load->database('campus', true);
        $filial = $this->session->userdata('filial');
        $config = array("codigo_filial" => $filial["codigo"]);
        $this->load->model("Model_alumnos", "alumnos", false, $config);
    }
    
    public function index(){ 
    	echo "<meta charset='utf-8' />";
    	$filial = $this->session->userdata('filial');
    	$cod_filial = $filial['codigo'];

    	//Get usuarios con matriculas activas de la filial
    	$conexion = $this->load->database($cod_filial, true);
    	$conexion->select('alumnos.codigo as codigo, matriculas_periodos.estado as estado');    	
    	$conexion->where('baja', 'habilitada');
    	$conexion->where('matriculas.estado', 'habilitada');
    	$conexion->where('matriculas_periodos.estado', 'habilitada');
    	$conexion->join('matriculas','matriculas.cod_alumno = alumnos.codigo');
    	$conexion->join('matriculas_periodos','matriculas.codigo = matriculas_periodos.cod_matricula');
    	$conexion->order_by('alumnos.codigo', 'DESC');
    	$conexion->group_by('alumnos.codigo');
    	$arrayUSers = $conexion->get('alumnos')->result(); //,200,0 Qty,Offset

    	//echo "<p>Mostrando ".sizeof($arrayUSers) ." alumnos con errores en el campus</p>";
    	//die(var_dump(sizeof($arrayUSers)));
    	//Por cada alumno activo chequea
    	foreach ($arrayUSers as $alumno) {
    		$a = $this->check_alumno($alumno->codigo);
    	}

	}

	public function check_alumno($id_alumno){

		$id_alumno = intval($id_alumno);
		$filialDatos = $this->session->userdata('filial');
		$filial = intval($filialDatos['codigo']);

		//Get usuarios del Campus relacionados al alumno
		$query_usuarios = $this->db->select('*');
		$query_usuarios = $this->db->from('campus.usuarios_tipos_filiales');
		$query_usuarios = $this->db->where('id_interno',$id_alumno);
		$query_usuarios = $this->db->where('id_filial',$filial);
		$query_usuarios = $this->db->where('estado','habilitado');
		$query_usuarios = $this->db->get();	
		$usuarios = $query_usuarios->result();
		
		//
		$cantidad = sizeof($usuarios);
		$mostrar = 1;

		$out = "<hr style='height:20px;background-color:#777;'>";	
		$alumno = $this->alumnos->getAlumno($id_alumno);	
		$out .= "<p>Alumno: " .$alumno->nombre.' '.$alumno->apellido.' Email: <strong>'.$alumno->email.'</strong><br>Fecha de alta: '.formatearFecha_pais(substr($alumno->fechaalta, 0, 10)).'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;ID Alumno: '.$alumno->getCodigo().'</p>';

		if ($cantidad < 1) {	

			$out .= "<div style='color:#700;'><hr>&#10008; No existe el usuario en el campus</div>";

			$form_alta = "<form action='fix_alumnos/alta_campus' method='POST'>
				    	<label for='cod_alumno' />ID Alumno</label>
				    	<input type='text' value='".$id_alumno."' name='cod_alumno' /><br>
				    	<label for='nombre' />Nombre</label>
				    	<input type='text' value='".$alumno->nombre."' name='nombre' /><br>
				    	<label for='apellido' />Apellido</label>
				    	<input type='text' value='".$alumno->apellido."' name='apellido' /><br>
				    	<label for='email' />Email</label>
				    	<input type='text' value='".$alumno->email."' name='email' /><br>

				    	<input type='hidden' value='".strtolower($alumno->sexo)."' name='sexo' />
				    	<input type='hidden' value='".$this->session->userdata('idioma')."' name='idioma' />
				    	<input type='hidden' value='".$filialDatos['codigo']."' name='cod_filial' />

				    	<p><input type='submit' value='Crear usuario de Campus a partir del alumno'/></p>
				    	</form>";
			
			$out .= $form_alta;	

			$mostrar = 1;

		} else {
			
			$out .= "<div style='color:#070;'><hr>&#10004; <small>Existe el usuario en el Campus</small></div>";

			$mostrar = 0;
			
			if ($cantidad > 1) {
				$out .= "<div style='color:#700;'><hr>&#10008; Alumno repetido (Cantidad: ".$cantidad.")<hr></div>";

				foreach ($usuarios as $usuario => $valor) {

					$query_usuario = $this->db->select('*');
					$query_usuario = $this->db->from('campus.usuarios');
					$query_usuario = $this->db->where('id',$valor->id_usuario);
					$query_usuario = $this->db->get();	
					$usuario = $query_usuario->result();
					
					$formBajaDuplicado = '<form action="dehabilitar_user_duplicado" method="POST" style="float:left;width:50%;">
					ID Campus: '.$usuario[0]->id.'<br>
					Nombre: '.$usuario[0]->nombre.' '.$usuario[0]->apellido.'<br>
					Email campus: <strong>'.$usuario[0]->email .'</strong>
					<input type="hidden" name="id_usuario" value="'.$usuario[0]->id.'" /><br>
					<input type="submit" value="Deshabilitar usuario duplicado" title="Eliminar usuario del campus" />
					</form>';

					$out .= $formBajaDuplicado;
				}

				$mostrar = 1;

			} else {

				$out .= "<div style='color:#070;'><hr>&#10004; <small>No esta repetido en el Campus</small></div>";
				//SI existe y es unico en el campus traigo los datos del usuario y del alumno
				$query_usuario = $this->db->select('*');
				$query_usuario = $this->db->from('campus.usuarios');
				$query_usuario = $this->db->where('id',$usuarios[0]->id_usuario);
				$query_usuario = $this->db->get();	
				$usuario = $query_usuario->result();

				if ($alumno->email != '') {

					if ($alumno->email != $usuario[0]->email) {
						$out .= "<div style='color:#700;'><hr>&#10008; Las direcciones de email no coinciden</div>";

						$formFixEmail = "<form action='fix_alumnos/fix_email_campus' method='POST'>
				    	<input type='hidden' value='".$alumno->email."' name='email' />
				    	<input type='hidden' value='".$usuario[0]->id."' name='codigo' />
				    	<input type='submit' value='Reemplazar direccion de filial a campus'/>
				    	</form>";

						$out .= "<br>Alumno filial: ";
						$out .= $alumno->email;
						$out .= " -> Usuario campus: ";
						$out .= $usuario[0]->email;
						$out .= $formFixEmail;

						$mostrar = 1;

					} else {
						$out .= "<div style='color:#070;'><hr>&#10004; <small>Tiene el mismo email en el Campus que en la Filial</small></div>";

						$out .= "<br>Alumno filial: ";
						$out .= $alumno->email;
						$out .= "<br>Usuario campus: ";
						$out .= $usuario[0]->email;
						$out .= "<p><small>Si el email de la filial no es el correcto, corregir editando los datos del alumno.</small></p>";

						$mostrar = 0;

					}

				} else {
					
					$out .= "<div style='color:#700;'><hr>&#10008; No tiene una direccion de email cargada en la Filial</div>";

					$formSetEmailFilial = "<form action='fix_alumnos/set_email_filial' method='POST'>
			    	<input type='hidden' value='".$alumno->getCodigo()."' name='codigo' />
			    	<input type='email' value='' name='email' placeholder='Igrese email' required />
			    	<input type='submit' value='Arreglar'/>
			    	</form>";

					$out .= "<br>Alumno filial: ";
					$out .= $alumno->email;
			    	$out .= "<br>Usuario campus: ";
					$out .= $usuario[0]->email;
					$out .= $formSetEmailFilial;

					$mostrar = 1;
				}
			}
		}

		if($mostrar == 1) { echo $out; }

	}

	public function fix_email_campus(){

		$codigo = $this->input->post('codigo');
		$email = $this->input->post('email');

		$data = array('email' => $email);
		$this->db->where('id', $codigo);
		
		$this->db->update('campus.usuarios', $data);

		header('Location: fix_alumnos');

	}

	public function set_email_filial(){

		$filial = $this->session->userdata('filial');
		$conexion = $this->load->database($filial['codigo'], true);

		$codigo = $this->input->post('codigo');
		$email = $this->input->post('email');

		$data = array('email' => $email);
		$query = $conexion->where('codigo', $codigo);

		$conexion->update('alumnos', $data);

		header('Location: fix_alumnos');

	}

	public function dehabilitar_user_duplicado(){

		$id_usuario = $this->input->post('id_usuario');
		
		$data = array('estado' => 'inhabilitado');
		$this->db->where('id_usuario', $id_usuario);
		
		$this->db->update('campus.usuarios_tipos_filiales', $data);

		header('Location: fix_alumnos');

	}

	public function alta_campus(){
				
		$nombre = $this->input->post('nombre');
		$apellido = $this->input->post('apellido');
		$email = $this->input->post('email');
		$sexo = $this->input->post('sexo');
		$idioma = $this->input->post('idioma');		
		$filial = $this->input->post('filial');
		$cod_filial = $this->input->post('cod_filial');
		$cod_alumno = $this->input->post('cod_alumno');

		$this->alumnos->alta_campus_nuevo($nombre, $apellido, $email, $sexo, $idioma, $cod_filial, $cod_alumno);

		header('Location: fix_alumnos');
	}

}
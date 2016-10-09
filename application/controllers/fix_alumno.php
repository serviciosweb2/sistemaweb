<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class Fix_alumno extends CI_Controller {
    
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

    	$this->print_form_get_alumno();

	}

	public function print_form_get_alumno(){

		$formGetAlumno = "<form action='fix_alumno/get_alumno_campus' method='POST'>
    	<label for='alumno'>ID ALUMNO</label>
    	<input type='text' name='alumno' placeholder='Ingrese Codigo de alumno' autofocus required />
    	<input type='submit' value='Buscar'/>
    	</form>";

    	echo $formGetAlumno;

	}

	public function get_alumno_campus(){
		echo "<meta charset='utf-8' />";
		$filialDatos = $this->session->userdata('filial');
		$filial = intval($filialDatos['codigo']);

		$id_alumno = intval($this->input->post('alumno'));

		$query_usuarios = $this->db->select('*');
		$query_usuarios = $this->db->from('campus.usuarios_tipos_filiales as UTF');
		$query_usuarios = $this->db->where('id_interno',$id_alumno);
		$query_usuarios = $this->db->where('id_filial',$filial);
		$query_usuarios = $this->db->where('estado','habilitado');
		$query_usuarios = $this->db->get();	
		$usuarios = $query_usuarios->result();


		//
		$cantidad = sizeof($usuarios);
		$existe_en_campus = 0;
		$existe_en_filial = 0;
		//

		echo "<hr style='height:20px;background-color:#888;'>";		

		$alumno = $this->alumnos->getAlumno($id_alumno);

		if ($cantidad < 1) {
			echo "<div style='color:#700;'><hr>&#10008; No existe el usuario en el campus</div>";

			$form_alta = "<form action='fix_alumno/alta_campus' method='POST'>
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
			
			echo $form_alta;
		} else {
			
			echo "<p>Alumno: " .$alumno->nombre.' '.$alumno->apellido.'<br>Email: <strong>'.$alumno->email.'</strong> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;ID Alumno: '.$alumno->getCodigo().'</p>';

			echo "<hr>&#10004; <small>Existe el usuario en el Campus</small><br>";
			if ($cantidad > 1) {
				echo "<hr>&#10008; Alumno repetido (Cantidad: ".$cantidad.")<hr>";

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

					echo $formBajaDuplicado;
				}

			} else {
				echo "<hr>&#10004; <small>No esta repetido en el Campus</small><br>";
				//SI existe y es unico en el campus traigo los datos del usuario y del alumno
				$query_usuario = $this->db->select('*');
				$query_usuario = $this->db->from('campus.usuarios');
				$query_usuario = $this->db->where('id',$usuarios[0]->id_usuario);
				$query_usuario = $this->db->get();	
				$usuario = $query_usuario->result();

				if ($alumno->email != '') {

					if ($alumno->email != $usuario[0]->email) {
						echo "<hr>&#10008; Las direcciones de email no coinciden<br>";

						$formFixEmail = "<form action='fix_alumno/fix_email_campus' method='POST'>
				    	<input type='hidden' value='".$alumno->email."' name='email' />
				    	<input type='hidden' value='".$usuario[0]->id."' name='codigo' />
				    	<input type='submit' value='Reemplazar direccion de filial a campus'/>
				    	</form>";

						echo "<br>Alumno filial: ";
						echo $alumno->email;
						echo "<br>Usuario campus: ";
						echo $usuario[0]->email;
						echo $formFixEmail;
					} else {
						echo "<hr>&#10004; Tiene el mismo email en el Campus que en la Filial<br>";

						echo "<br>Alumno filial: ";
						echo $alumno->email;
						echo "<br>Usuario campus: ";
						echo $usuario[0]->email;
						echo "<p><small>Si el email de la filial no es el correcto, corregir editando los datos del alumno.</small></p>";

						echo "<p><hr>Revisar otro alumno:</p>";
						$this->print_form_get_alumno();
					}

				} else {
					echo "<hr>&#10008; No tiene una direccion de email cargada en la Filial<br>";

					$formSetEmailFilial = "<form action='fix_alumno/set_email_filial' method='POST'>
			    	<input type='hidden' value='".$alumno->getCodigo()."' name='codigo' />
			    	<input type='email' value='' name='email' placeholder='Igrese email' required />
			    	<input type='submit' value='Arreglar'/>
			    	</form>";

					echo "<br>Alumno filial: ";
					echo $alumno->email;
			    	echo "<br>Usuario campus: ";
					echo $usuario[0]->email;
					echo $formSetEmailFilial;
				}
			}
		}

	}

	public function fix_email_campus(){

		$codigo = $this->input->post('codigo');
		$email = $this->input->post('email');

		$data = array('email' => $email);
		$this->db->where('id', $codigo);
		
		$this->db->update('campus.usuarios', $data);

		header('Location: fix_alumno');

	}

	public function set_email_filial(){

		$filial = $this->session->userdata('filial');
		$conexion = $this->load->database($filial['codigo'], true);

		$codigo = $this->input->post('codigo');
		$email = $this->input->post('email');

		$data = array('email' => $email);
		$query = $conexion->where('codigo', $codigo);

		$conexion->update('alumnos', $data);

		header('Location: fix_alumno');

	}

	public function dehabilitar_user_duplicado(){

		$id_usuario = $this->input->post('id_usuario');
		
		$data = array('estado' => 'inhabilitado');
		$this->db->where('id_usuario', $id_usuario);
		
		$this->db->update('campus.usuarios_tipos_filiales', $data);

		header('Location: fix_alumno');

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

		$resultado = $this->alumnos->alta_campus_nuevo($nombre, $apellido, $email, $sexo, $idioma, $cod_filial, $cod_alumno);

		//var_dump($resultado);

		echo "<p><hr>Revisar otro alumno:</p>";
		$this->print_form_get_alumno();

		
	}

}
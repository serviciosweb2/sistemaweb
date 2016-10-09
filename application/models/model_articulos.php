<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class Model_articulos extends CI_Model {

    var $codigo_filial = 0;

    public function __construct($arg) {
        parent::__construct();
        
        $this->codigo_filial = $arg["codigo_filial"];
    }

    public function listarArticulosDataTable($arrFiltros) {
        $conexion = $this->load->database($this->codigo_filial, true);
        $arrCondiciones = array();
        if ($arrFiltros["sSearch"] != "") {
            $arrCondiciones = array(
                "articulos.codigo"=>$arrFiltros['sSearch'],
                "articulos.nombre" => $arrFiltros["sSearch"],
                "articulos_categorias.nombre"=>$arrFiltros['sSearch'],
                "general.tipos_unidades_medida.unidad"=>$arrFiltros['sSearch'],
                "articulos.costo"=>$arrFiltros['sSearch']
            );
        }

        $arrLimit = array();
        if ($arrFiltros["iDisplayStart"] != "" and $arrFiltros["iDisplayLength"] != "") {

            $arrLimit = array(
                "0" => $arrFiltros["iDisplayStart"],
                "1" => $arrFiltros["iDisplayLength"]
            );
        }

        $arrSort = array();

        if ($arrFiltros["SortCol"] != "" and $arrFiltros["sSortDir"] != "") {

            $arrSort = array(
                "0" => $arrFiltros["SortCol"],
                "1" => $arrFiltros["sSortDir"]
            );
        }

        $datos = Varticulos::listarArticulosDataTable($conexion, $arrCondiciones, $arrLimit, $arrSort);
        $contar = Varticulos::listarArticulosDataTable($conexion, $arrCondiciones, "", "", true);

        $retorno = array(
            "sEcho" => $arrFiltros["sEcho"],
            "iTotalRecords" => $contar,
            "iTotalDisplayRecords" => $contar,
            "aaData" => array()
        );
        $rows = array();
        foreach ($datos as $row) {
            $rows[] = array(
                $row["codigo"],
                $row["nombre"],
                $row['costo'],
                $row['unidad_medida'],
                $row['categoria'],
                $row['stock'],
                ' ',
                $row['estado']
            );
        }
        $retorno['aaData'] = $rows;
        return $retorno;
    }

    public function getArticulos() {
        $conexion = $this->load->database($this->codigo_filial, true);
        $articulos = Varticulos::listarArticulos($conexion);
        return $articulos;
    }

    public function getObjArticulo($cod_articulo) {
        $conexion = $this->load->database($this->codigo_filial, true);
        $objArticulo = new Varticulos($conexion, $cod_articulo);
       
        return $objArticulo;
    }

    public function guardar($datos) {
        $conexion = $this->load->database($this->codigo_filial, true);
        $conexion->trans_begin();

        //GUARDO ARTICULO
        $articulo = new Varticulos($conexion, $datos['articulo']['codigo']);
        $articulo->setArticulos($datos['articulo']);
        $articulo->guardarArticulos();

        $articulo->unSetImpuestos();
        //RECORRE IMPUESTOS
        foreach ($datos['impuestos'] as $impuesto) {
            
            $articulo->setImpuestos($impuesto);
         }

        $estadotran = $conexion->trans_status();
        if ($estadotran === FALSE) {
            $conexion->trans_rollback();
        } else {

            $conexion->trans_commit();
        }

        $respuesta = $articulo->getCodigo();

        return class_general::_generarRespuestaModelo($conexion, $estadotran, $respuesta);
    }

    public function cambiarEstado($cod_articulo) {
        $conexion = $this->load->database($this->codigo_filial, true);
        $conexion->trans_begin();

        $articulo = new Varticulos($conexion, $cod_articulo);
        $estado = $articulo->estado == 'inhabilitado' ? 'habilitado' : 'inhabilitado';
        $articulo->estado = $estado;
        $articulo->guardarArticulos();

        $estadotran = $conexion->trans_status();
        if ($estadotran === FALSE) {
            $conexion->trans_rollback();
        } else {

            $conexion->trans_commit();
        }

        return class_general::_generarRespuestaModelo($conexion, $estadotran);
    }

    public function getImpuestosAsignados($codarticulo) {
        $conexion = $this->load->database($this->codigo_filial, true);
        $objArticulo = new Varticulos($conexion, $codarticulo);
        $impuestosArticulos = $objArticulo->getImpuestos(true);
        return $impuestosArticulos;
    }
    
    public function getArbolCategorias(){
        $conexion = $this->load->database($this->codigo_filial,true);
        $this->load->helper('alumnos');
        $condiciones = array(
            "baja"=>0
        );
        $listadoCategorias = Varticulos_categorias::listarArticulos_categorias($conexion,$condiciones);
        $arrPadres = array();
        $retorno = array();
        foreach($listadoCategorias as $row){
            if($row['cod_padre'] == ''){
                $arrPadres[] = $row;
            }
        }
        
        function getHijos($arrayPadres, $listCategorias){
            
            $valor = array();
            $a=0;
            $hijos = array();
            
            foreach($listCategorias as $categoria){
                if($arrayPadres['codigo'] == $categoria['cod_padre']){
                    $hijos = getHijos($categoria, $listCategorias);
                    $valor[] = array(
                        'title' =>  inicialesMayusculas($categoria['nombre']),
                        'key' => $categoria['codigo'],
                        'select' =>'',
                        'hideCheckbox' => true,
                        'children' => $hijos
                    );
                }
                 $a++;
            }
            return $valor;
        }
        
        for ($i = 0; $i < count($arrPadres); $i++) {
            $retorno[] = array(
                'title' => inicialesMayusculas($arrPadres[$i]['nombre']),
                'key' => $arrPadres[$i]['codigo'],
                'select' => '',
                'hideCheckbox' => true,
                'expand' =>FALSE,
                'children' => getHijos($arrPadres[$i],$listadoCategorias)
            );
        }
      $arrayRetorno = array(
          'title' => lang('categorias_articulos'),
            'key' => '',
            'select' =>'',
            'hideCheckbox' => true,
             'expand' => TRUE, 
            'children' => $retorno
            
      );
        return $arrayRetorno;
    }
    
    public function agregarCategoriaSubcategoria($arrGuardarCat){
        $conexion = $this->load->database($this->codigo_filial,true);
         $conexion->trans_begin();
        $myArticuloCategoria = new Varticulos_categorias($conexion);
        $arrGuardarCategoria = array(
            "nombre"=>$arrGuardarCat['nombre_categoria'],
            "cod_padre"=>$arrGuardarCat['cod_padre'],
            "baja"=>0
        );
        $myArticuloCategoria->setArticulos_categorias($arrGuardarCategoria);
        $myArticuloCategoria->guardarArticulos_categorias();
         $estadotran = $conexion->trans_status();
        if ($estadotran === FALSE) {
            $conexion->trans_rollback();
        } else {
            $conexion->trans_commit();
        }
        return class_general::_generarRespuestaModelo($conexion, $estadotran);
    }
    
    public function modificarCategoria($data_post){
        $conexion = $this->load->database($this->codigo_filial,true);
        $myArtCat = new Varticulos_categorias($conexion, $data_post['cod_categoria']);
        $myArtCat->nombre = $data_post['nombre'];
        $estado = $myArtCat->guardarArticulos_categorias();
        return class_general::_generarRespuestaModelo($conexion, $estado);
    }

    public function getObjArtCat($cod_categoria){
        $conexion = $this->load->database($this->codigo_filial,true);
        $myArtCat = new Varticulos_categorias($conexion, $cod_categoria);
        return $myArtCat;
    }
    
    public function bajaCategoria($cod_categoria,$baja){
        $conexion = $this->load->database($this->codigo_filial,true);
        $myArtCat = new Varticulos_categorias($conexion, $cod_categoria);
        $myArtCat->baja = $baja;
        $estado = $myArtCat->guardarArticulos_categorias();
        return class_general::_generarRespuestaModelo($conexion, $estado);
    }
    
    public function getHijosCategoria($cod_categoria){
        $conexion = $this->load->database($this->codigo_filial,true);
        $condiciones = array(
            "cod_padre"=>$cod_categoria,
            "baja"=>0
        );
        $hijosCategoria = Varticulos_categorias::listarArticulos_categorias($conexion, $condiciones);
        $arrCondiciones = array(
            "cod_categoria"=>$cod_categoria
        );
        $articulosCategoria = Varticulos::listarArticulos($conexion, $arrCondiciones);
        $retorno['hijosCategoria'] = $hijosCategoria;
        $retorno['articulosCategoria'] = $articulosCategoria;
        return $retorno;
    }
    
    public function validarBajaArticulo($cod_articulo){
        $conexion = $this->load->database($this->codigo_filial,true);
        $condiciones = array(
            "baja"=>0,
            "cod_articulo"=>$cod_articulo
        );
        $arrComprasArticulo = Vcompras_renglones::listarCompras_renglones($conexion, $condiciones);
        if(count($arrComprasArticulo) > 0){
            return false;
        }else{
            return true;
        }
    }
}


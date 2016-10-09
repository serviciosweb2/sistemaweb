<?php 

class filtros_reportes{

    public $id;
    public $display;
    public $condicion;
    public $method; // where or having
    public $hint;

    function __construct($id, $display, $condicion, $method, $hint = null) {
        $this->id = $id;
        $this->display = $display;
        $this->condicion = $condicion;
        $this->method = $method;
        $this->hint = $hint;        
    }

}

?>
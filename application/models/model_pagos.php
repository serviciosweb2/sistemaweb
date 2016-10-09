<?php

/**
 * Model_cobros
 * 
 * Description...
 * 
 * @package model_cobros
 * @author vane
 * @version 1.0.0
 */
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Model_pagos extends CI_Model {

    var $codigo_filial = 0;

    public function __construct($arg) {
        parent::__construct();

        $this->codigo_filial = $arg["codigo_filial"];
    }

}

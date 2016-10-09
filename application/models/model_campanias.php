<?php

/**
 * Model_alumnos
 *
 * Description...
 *
 * @package model_alumnos
 * @author ivan <ivan.sys@gmail.com>
 * @version 1.0.0
 */
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Model_email_mkt extends CI_Model
{

    var $codigofilial = 0;

    public function __construct($arg)
    {
        parent::__construct();
        $this->codigofilial = $arg["codigo_filial"];
    }

    
}
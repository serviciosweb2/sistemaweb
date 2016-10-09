<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of MY_loader
 *
 * @author ivan
 */
class MY_Loader extends CI_Loader {

    public function get_cached_vars()
    {
        return $this->_ci_cached_vars;
    }

} 
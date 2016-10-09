<?php
    $ci = &get_instance();
    $ci->output->set_header("Expires: Tue, 03 Jul 2001 06:00:00 GMT");
    $ci->output->set_header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
    $ci->output->set_header("Cache-Control: no-store, no-cache, must-revalidate");
    $ci->output->set_header("Cache-Control: post-check=0, pre-check=0", false);
    $ci->output->set_header("Pragma: no-cache");

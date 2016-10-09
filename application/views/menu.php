<?php $ci=&get_instance();
$session=$ci->session->all_userdata();


?>

<li >
    <a href="<?php echo base_url('dashboard');?>">
        <i class="icon-dashboard"></i>
        <span class="menu-text"> HOME </span>
    </a>
</li>


<li <?php echo $seccion["categoria"] ===  "interesados" ? "class='active open'": ""?>>
    <a  href="#" class="dropdown-toggle">
        <i class="icon-interesados"></i>
        <span class="menu-text">  <?php echo lang('interesados')?> </span>

        <b class="arrow icon-angle-down"></b>
    </a>


    <ul class="submenu">

        <?php echo  session_menu($session, 'menu_principal', '', 'interesados',$ci->router->class,'',true)?>

    </ul>




</li>


<li  <?php echo $seccion["categoria"] ===  "academicos" ? "class='active open'": ""?>>
    <a  class="dropdown-toggle">
        <i class="icon-academico"></i>



        <span class="menu-text"> <?php echo lang('academicos')?></span>
        <b class="arrow icon-angle-down"></b>


    </a>


    <ul class="submenu">
        <?php echo session_menu($session, 'menu_principal', '', 'academicos',$ci->router->class,'',true)?>
    </ul>



</li>


<li <?php echo $seccion["categoria"] ===  "administracion" ? "class='active'": ""?> >
    <a class="dropdown-toggle" >
        <i class="icon-administrativo"></i>



        <span class="menu-text"> <?php echo lang('administracion')?></span>
        <b class="arrow icon-angle-down"></b>


    </a>

    <ul class="submenu">
        <?php  echo session_menu($session, 'menu_principal', '', 'administracion',$ci->router->class,'',true)?>
    </ul>



</li>


<li <?php echo $seccion["categoria"] ===  "reportes" ? "class='active'": '' ?>>
    <a class="dropdown-toggle" >
        <i class="icon-reportes"></i>



        <span class="menu-text">  <?php echo lang('reportes')?></span>
        <b class="arrow icon-angle-down"></b>

    </a>

    <ul class="nav nav-list" style="display: none;">

        <li>
            <a class="dropdown-toggle" >
                <i class="icon-caret-right"></i>
                <span class="menu-text">  <?php echo lang('interesados')?></span>
                <b class="arrow icon-angle-down"></b>
            </a>
            <ul class="submenu" style="display: none;">

                <?php
                $conexion = $this->load->database('default', true);
                $grupoTmp = Vsecciones::getGrupo($conexion,'reportes-interesados');
                $grupo = array();
                foreach ($grupoTmp as $grup)
                {
                    $grupo[] = $grup['slug'];
                }
                echo getMenuReporte($session, $grupo, 'reportes',$ci->router->class,'',true);
                ?>

            </ul>
        </li>


        <li>
            <a class="dropdown-toggle" >
                <i class="icon-caret-right"></i>
                <span class="menu-text">  <?php echo lang('configuracion_academicos')?></span>
                <b class="arrow icon-angle-down"></b>
            </a>
            <ul class="submenu" style="display: none;">

                <?php


                $grupoTmp = Vsecciones::getGrupo($conexion,'reportes-academicos');
                $grupo = array();
                foreach ($grupoTmp as $grup)
                {
                    $grupo[] = $grup['slug'];
                }

                echo getMenuReporte($session, $grupo, 'reportes',$ci->router->class,'',true);
                ?>

            </ul>
        </li>


        <li>
            <a class="dropdown-toggle" >
                <i class="icon-caret-right"></i>
                <span class="menu-text">  <?php echo lang('administracion')?></span>
                <b class="arrow icon-angle-down"></b>
            </a>
            <ul class="submenu" style="display: none;">

                <?php
                $grupoTmp = Vsecciones::getGrupo($conexion,'reportes-administrativos');
                $grupo = array();
                foreach ($grupoTmp as $grup)
                {
                    $grupo[] = $grup['slug'];
                }
                echo getMenuReporte($session, $grupo, 'reportes',$ci->router->class,'',true);
                ?>

            </ul>
        </li>

        <?php
        $grupoTmp = Vsecciones::getGrupo($conexion,'reportes-franquiciados');
        if($grupoTmp)
        {
            ?>
            <li>
                <a class="dropdown-toggle" >
                    <i class="icon-caret-right"></i>
                    <span class="menu-text">  <?php echo lang('sistema_gestion_comercial')?></span>
                    <b class="arrow icon-angle-down"></b>
                </a>
                <ul class="submenu" style="display: none;">

                    <?php
                    $grupoTmp = Vsecciones::getGrupo($conexion,'reportes-franquiciados');
                    //die(var_dump($session['secciones']));
                    $grupo = array();
                    foreach ($grupoTmp as $grup)
                    {
                        $grupo[] = $grup['slug'];
                    }
                    echo getMenuReporte($session, $grupo, 'reportes',$ci->router->class,'',true);
                    ?>

                </ul>
            </li>
        <?php } ?>
    </ul>
</li>


<!-- Inicio Reportes Intranet -->
<li <?php echo $seccion["categoria"] ===  "reportes-franquiciados" ? "class='active'": '' ?>>
    <a class="dropdown-toggle" >
        <i class=" menu-icon icon-bar-chart"></i>


        <span class="menu-text">  <?php echo lang('Reportes')?></span>
        <b class="arrow icon-angle-down"></b>

    </a>
    <ul class="submenu" style="display: none;">

        <?php
        $conexion = $this->load->database('default', true);
        $grupoTmp = Vsecciones::getGrupo($conexion,'reportes-franquiciados');
        $grupo = array();
        foreach ($grupoTmp as $grup)
        {
            $grupo[] = $grup['slug'];
        }
        echo getMenuReporte($session, $grupo, 'reportes-franquiciados',$ci->router->class,'',true);
        ?>

    </ul>

</li>
<!-- Inicio Reportes Intranet -->


<?php echo session_menu($session, 'menu_principal','', 'ajustes',$ci->router->class,'icon-cog','',false)?>



                                          
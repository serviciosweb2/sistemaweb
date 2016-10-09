<script>

    function ver_imagen(url){
        window.open(url, url);
    }

</script>
<div class="modal-body">
    <div class="row">
        <div class="col-md-12 col-xs-12">
            <h3 class="smaller light-blue">
                <?php echo ucwords($myComunicado->titulo) ?>
            </h3>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6 col-xs-6">
            <?php echo ucwords($myComunicado->usuario); ?>
        </div>
        <div class="col-md-6 col-xs-6">
            <small class="green" style="float: right">
                <?php echo formatearFecha_pais($myComunicado->fecha_creacion, true); ?>
            </small>
        </div>
    </div>
    <hr>
    <div class="row">
        <div class="col-md-12 col-xs-12">
            <blockquote>
                <p><?php echo str_replace("\n", "<br>", $myComunicado->mensaje); ?></p>
            </blockquote>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12 col-xs-12">
            <?php if (isset($imagenes) && is_array($imagenes)){
                foreach ($imagenes as $imagen){ ?>
            <table style="float: left;">
                <tr>
                    <td style="padding: 4px 4px;">
                        <img src="<?php echo $imagen['url'] ?>" height="80px" style="cursor: pointer;" onclick="ver_imagen('<?php echo $imagen['url'] ?>');">
                    </td>
                </tr>
            </table>    
                <?php }
            } ?>
        </div>
    </div>
</div>

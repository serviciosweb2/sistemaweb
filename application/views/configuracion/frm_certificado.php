
<div class="modal-content">
    <form id="nuevoImpuesto">
        <div class="modal-header">
            <h4 class="blue bigger"><?php echo 'Información del Certificado'; ?></h4>
        </div>

        <div class="modal-body overflow-visible">
            <label><?php echo '<b>Nombre: </b>'.$subject['CN']; ?></label></br>
            <label><?php echo '<b>Identificación: </b>'.$subject['serialNumber']; ?></label></br>
            <label><?php echo '<b>País: </b>'.$subject['C']; ?></label></br>
            <label><?php echo '<b>Organización: </b>'.$subject['O']; ?></label></br>
            <hr>
            <h4><?php echo 'Emisor'; ?></h4>
            <label><?php echo '<b>Nombre: </b>'.$issuer['CN']; ?></label></br>
            <label><?php echo '<b>País: </b>'.$issuer['C']; ?></label></br>
            <label><?php echo '<b>Organización: </b>'.$issuer['O']; ?></label></br>
            <hr>
            <label><?php echo '<b>Versión: </b>'.$version; ?></label></br>
            <label><?php echo '<b>Nº Serie: </b>'.$serialNumber; ?></label></br>
            <label><?php echo '<b>Fecha desde: </b>'.date('d/m/Y', $validFrom_time_t); ?></label></br>
            <label><?php  if($validTo_time_t < time()){echo '<font color=red>';};echo '<b>Fecha hasta: </b>'.date('d/m/Y', $validTo_time_t); ?></label></br>
        </div>

    </form>
</div>
<script src="<?php echo base_url('assents/js/configuracion/frm_offline.js')?>"></script>
<div class="modal-content">
    <form id="frmOffline">
    <div class="modal-header">
            <h4 class="blue bigger">configuracion offline</h4>
    </div>

    <div class="modal-body overflow-visible">
            <div class="row">
                    
                <div class="col-md-12 form-group">
                    <label>Indique un nombre para este equipo</label>
                    <input name="nombreEquipo" class="form-control">
                </div>
                
                <div class="col-md-12 form-group">
                    <label>Indique un pin para este equipo</label>
                    <input name="pin" type="password" class="form-control">
                </div>
                
                <div class="col-md-12 form-group">
                    <label>repita el pin</label>
                    <input name="re-pin" type="password" class="form-control">
                </div>
                    
            </div>
    </div>

    <div class="modal-footer">
<!--            <button class="btn btn-sm" data-dismiss="modal">
                    <i class="icon-remove"></i>
                    Cancel
            </button>-->

            <button class="btn btn-sm btn-primary" type="submit">
                    <i class="icon-ok"></i>
                    Guardar
            </button>
    </div>
    </form>
</div>


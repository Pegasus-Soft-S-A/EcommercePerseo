<script>
    function confirm_modal(delete_url)
    {
        jQuery('#confirm-delete').modal('show', {backdrop: 'static'});
        document.getElementById('delete_link').setAttribute('href' , delete_url);
    }
</script>

<div class="modal fade" id="confirm-delete" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-header">
             
                <h4 class="modal-title" id="myModalLabel">Confirmar</h4>
            </div>

            <div class="modal-body">
                <p>Eliminar mensaje de confirmación</p>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                <a id="delete_link" class="btn btn-danger btn-ok">Elimianr</a>
            </div>
        </div>
    </div>
</div>

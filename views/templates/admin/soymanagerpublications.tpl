<!-- modal editar url -->
<div class="modal" tabindex="-1" role="dialog" id="myModal">
    <div class="modal-dialog soymanagerpublication_modal" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">ACTUALIZAR URL</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="panel" >
                    <div class="form-wrapper">
                        <div class="form-group">
                            <label class="control-label col-lg-3 required">
                                <span class="label-tooltip" data-toggle="tooltip" data-html="true" title="" data-original-title="">
                                    {l s='ID Product' mod='soymanagerpublications'}
                                </span>
                            </label>
                            <div class="col-lg-9">
                                <input type="number" name="soy_idProduct_modal" id="soy_id_product_modal" value="" class="fixed-width-xl" required="required" readonly>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label col-lg-3 required">
                                {l s='Name' mod='soymanagerpublications'}
                            </label>
                            <div class="col-lg-9">
                                <input type="text" name="soymanagerpublications_name_modal" id="soymanagerpublications_name_modal" value="" class="" readonly>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label col-lg-3 required">
                                {l s='Medio' mod='soymanagerpublications'}
                            </label>
                            <div class="col-lg-9">
                                <input type="text" name="soymanagerpublications_medio_modal" id="soymanagerpublications_medio_modal" value="" class="" readonly>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label col-lg-3 required">
                                <span class="label-tooltip" data-toggle="tooltip" data-html="true" title="" data-original-title="">
                                    {l s='Url' mod='soymanagerpublications'}
                                </span>
                            </label>
                            <div class="col-lg-9">
                                <input type="text" name="soymanagerpublications_url_modal" id="soymanagerpublications_url_modal" value="" class="" required="required">
                            </div>
                        </div>
                    </div><!-- /.form-wrapper -->
                    <input type="hidden" name="soymanagerpublications_id_modal" id="soymanagerpublications_id_modal" value="" class="">

                    <div class="panel-footer">
                        <button type="submit" value="1" id="configuration_form_submit_btn_1" name="soymanagerpublications_guardarConfi_Update_url_modal" class="btn btn-default pull-right">
                            <i class="process-icon-save"></i> {l s='Update'}
                        </button>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary soystockpordefecto_close_modal" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<div>
    <div>
        <ul class="nav nav-tabs">
            <li id="lang-1" class="lang-flag active">
                <a href="#items-1"  data-toggle="tab">Crear publicaciones</a>
            </li>
            <li id="lang-2" class="lang-flag">
                <a href="#items-2"  data-toggle="tab">Ver publicaciones</a>
            </li>
        </ul>
    </div>
    <div class="soymanagerpublications-items tab-content">

        <div id="items-1" class="lang-content tab-pane active">
            {*
                IMPRIMIMOS EL HELPERFORM CREAR PUBLICACIONES
            *}
            {$helperFormCrearPublicaciones}
            <input type="hidden" name="soymanagerpublicaciones_hidden_medio" id="soymanagerpublicaciones_hidden_medio" value="">
        </div>
        <div id="items-2" class="lang-content tab-pane">
            {if isset($soymanagerpublications_publicaciones)}
                {include file="./soymanagerpublication_tabla.tpl"}
            {/if}
        </div>
    </div>
</div>

{*
    Creear textos traducibles prestashop desde JS
*}
{literal}
<script>
    var soymanager_confirmacion_borrar_publicacion = '{/literal}{l s='You want to delete this publication?' mod='soymanagerpublications'}{literal}'
</script>
{/literal}

<div class="fila-resultado">
    <th class="text-center">{l s='SEARCH RESULTS' mod='soymanagerpublications'}</th>
</div>
{if isset($productos) && $productos}
    <table class="table table-striped text-center" id="tabla_busqueda">
        <tr class="fila-cabecera text-center">
            <th scope="col" class="text-center"></th>
            <th scope="col" class="text-center"><p>{l s='Reference' mod='soymanagerpublications'}</p></th>
            <th scope="col" class="text-center"><p>{l s='ID Product' mod='soymanagerpublications'}</p></th>
            <th scope="col" class="text-center"><p>{l s='Name' mod='soymanagerpublications'}</p></th>
            <th scope="col" class="text-center"></th>
        </tr>
        {foreach from = $productos item=producto name=productos }
            <tr class="fila">
                <td class="columna"><img class="img-thumbnail" style="width:70px;" src="{$producto.imagePath}" alt=""></td>
                <td class="columna"><p>{$producto.reference}</p></td>
                <td class="columna soymanagerpublications_id"><p>{$producto.id_product}</p></td>
                <td class="columna soymanagerpublications_nombre"><p>{$producto.name}</p></td>
                <td class="columna">
                    <input type="hidden" name="id_product" value="{$producto.id_product}">
                    <button class="anyadir_producto btn btn-success" >{l s='Add product' mod='soymanagerpublications'}</button>
                </td>
            </tr>
        {/foreach}
    </table>
{else}
    <div class="mensaje-error-busqueda">
        <span class="alert alert-warning">{l s='No product found' mod='soymanagerpublications'}</span>
    </div>
{/if}

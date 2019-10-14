<div id="soymanagerpublications_container_views">

    <table class="table product">
        <tr class="nodrag nodrop">
            <th class="text-center" id="soymanagerpublication-idproduct-order">{l s='id' mod='soymanagerpublications'}<i class="icon-sort-down"></i></th>
            <th class="text-center" id="soymanagerpublication-idname-order">{l s='name'}<i class="icon-sort-down"></i></th>
            <th class="text-center" id="soymanagerpublication-idmedio-order">{l s='medio'}<i class="icon-sort-down"></i></th>
            <th class="text-center">{l s='logo' mod='soymanagerpublications'}</th>
            <th class="text-center">{l s='url' mod='soymanagerpublications'}</th>
            <th class="text-center">{l s='preview' mod='soymanagerpublications'}</th>
            <th class="text-center">{l s='modify' mod='soymanagerpublications'}</th>
            <th class="text-center">{l s='delete' mod='soymanagerpublications'}</th>
            <th class="text-center">{l s='active' mod='soymanagerpublications'}</th>
        </tr>

        {if isset($soymanagerpublications_publicaciones)  and !empty($soymanagerpublications_publicaciones) }
            {foreach from=$soymanagerpublications_publicaciones key=key item=item}

                <tr id="soymanagerpublicationfila-{$key}" class="nodrag nodrop">
                    <td class="text-center">
                        {$item['id_product']}
                    </td>
                    <td class="text-center">
                        {$item['name']}
                    </td>
                    <td class="text-center">
                        {$item['medio']}
                    </td>
                    <td class="text-center soy-box-img">
                        <div id="soymanagerpublications_logo_table">
                            <a href="{$item['url']}">
                                <img src="{$item['image_url']}">
                            </a>
                        </div>
                    </td>
                    <td class="text-center ">
                        <a href="{$item['url']}">{$item['url']}</a>
                    </td>
                    <td class="text-center ">
                        <a href="{$arrayUrls[$key]}" class="manager-preview" target=»_blank»>
                            <i class="process-icon-preview previewUrl"></i>
                        </a>
                    </td>

                    <td class="text-center soy-box-modificar">
                        <a href="#" class="pull-center btn btn-default" data-toggle='modal' data-target='#myModal'
                           data-mod='{
                                            "id":"{$item['id']}",
                                            "id_product":"{$item['id_product']}",
                                            "name":"{$item['name']}",
                                            "medio":"{$item['medio']}",
                                            "url":"{$item['url']}"
                                            }'
                        >
                            <i class="icon-pencil"></i> {l s='modify' mod='soymanagerpublications'}
                        </a>
                    </td>

                    <td class="text-center soy-box-delete" >
                        <a href="#" class="pull-center btn btn-default"
                           data-delete='{
                                            "id":"{$item['id']}",
                                            "id_product":"{$item['id_product']}",
                                            "medio":"{$item['medio']}",
                                            "url":"{$item['url']}"
                                            }'
                        >
                            <i class="icon-trash"></i> {l s='delete' mod='soymanagerpublications'}
                        </a>
                    </td>

                    <td class="text-center">
                        <input type="checkbox" class="soymanagerpublications_active" name="soymanagepublicacion_activate[]" {if $item['active'] eq 1}checked{/if}
                               data-active='{
                                            "id":"{$item['id']}",
                                            "id_product":"{$item['id_product']}",
                                            "name":"{$item['name']}",
                                            "medio":"{$item['medio']}",
                                            "url":"{$item['url']}"
                                            }'
                        >
                    </td>
                </tr>
            {/foreach}
        {else}

            <tr class="nodrag nodrop soymanagerpublications_sinpublicaciones">
                <td class="text-left" colspan="9">
                    <span class="alert alert-warning">{l s='No posts created' mod='soymanagerpublications'}</span>
                </td>
            </tr>
        {/if}
    </table>
</div>
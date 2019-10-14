/*
* Copyright (c) 2019, Nicripsia Internet SL www.soy.es All rights reserved.
*
* THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES,
* INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
* DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
* SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR
* SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY,
* WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE
* USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
*
* @author    Soy.es <info@soy.es>
* @copyright 2019 Soy.es
* @license   Soy.es

*/

$(document).ready(function () {
    
    $(document).on('click',".soy-box-modificar a",soymodificarPublicacion);
    $(document).on('click',".soy-box-delete a",soyBorrarPublicacion);
    $("#soymanagerpublicaciones_hidden_medio").val("");
    $(document).on('click',".soymanagerpublications_active",soyEstadoPublicacion);

    /*==========================================================================
    Añadimos eventos a las tabs para mostrar sus info segun la tab seleccionada
    ============================================================================*/
    $(document).on('click',"#lang-1",function () {
       $(".soy-info-1").show();
       $(".soy-info-2").hide();
    });
    $(document).on('click',"#lang-2",function () {
        $(".soy-info-1").hide();
        $(".soy-info-2").show();
    });
    /*=========================================
    Input que recoge la busqueda del cliente
    ==========================================*/
    $(document).on('keyup','#soymanagerpublication_buscador_ajax',function(event){
        ajaxGetProduct(event);
    });
    $(document).on('click','#soymanagerpublication_buscador_ajax',function(event){
        ajaxGetProduct(event);
    });
    $('#soymanagerpublication_producto_seleccionado').attr("readonly","readonly");
    $(document).on('click','.anyadir_producto',function(){
        event.preventDefault();
        var nombre = $(this).parents(".fila").find(".soymanagerpublications_nombre p").text();
        var id = $(this).parents(".fila").find(".soymanagerpublications_id p").text();
        $("#soymanagerpublications_idProduct").val(id);

        $('#soymanagerpublication_producto_seleccionado').empty().val(nombre);
    });
    //cuando se hace click fuera del buscador de productos.
    $(document).on('click', function (e) {
        if ($(e.target).closest("#soymanagerpublication_buscador_ajax").length === 0) {
            $("#soymanagerpublication_busqueda").slideUp();
        }
    });


    /*=========================================================================================================
    Llamadas ajax para ordenar la tabla al seleccionar los TH product-name-medio de la tabla VER PUBLICACIONES
    ===========================================================================================================*/

    $(document).on('click','#soymanagerpublication-idproduct-order',function () {
        ajaxGetProductOrder($(this).text());

    });

    $(document).on('click','#soymanagerpublication-idname-order',function () {
        ajaxGetProductOrder($(this).text());
    });

    $(document).on('click','#soymanagerpublication-idmedio-order',function () {
        ajaxGetProductOrder($(this).text());
    });

});

function soyEstadoPublicacion(){

    var id_product = $(this).data("active").id_product;
    var medio = $(this).data("active").medio;

    let parametros = {};
    parametros.id_product = id_product;
    parametros.medio = medio;
    parametros.ajax = true;
    if($(this).attr("checked")){
        parametros.action = "activar";
        parametros.estado = 1;
    }else{
        parametros.action = "desactivar";
        parametros.estado = 0;
    }
    console.info(parametros);
    llamadaAjaxPublicaciones(parametros);
}

function soyBorrarPublicacion(){

   if(confirm(soymanager_confirmacion_borrar_publicacion)) {
       var id_product = $(this).data("delete").id_product;
       var medio = $(this).data("delete").medio;
       var id_fila = $(this).parents('tr').attr('id');
       id_fila = id_fila.split("-");

       let parametros = {};
       parametros.id_product = id_product;
       parametros.medio = medio;
       parametros.fila = id_fila[1];//pasamos la fila que queremos borrar para ocultarla cuando se complete el ajax
       parametros.ajax = true;
       parametros.action = "borrarPublicacion";
       parametros.id = $(this).data("delete").id;
       llamadaAjaxPublicaciones(parametros);
   }
}

function soymodificarPublicacion(){

    var id_product = $(this).data("mod").id_product;
    var medio = $(this).data("mod").medio;
    var url = $(this).data("mod").url;
    var name = $(this).data("mod").name;
    var id = $(this).data("mod").id;

    $("#soy_id_product_modal").val(id_product);
    $("#soymanagerpublications_medio_modal").val(medio);
    $("#soymanagerpublications_url_modal").val(url);
    $("#soymanagerpublications_name_modal").val(name);
    $("#soymanagerpublications_id_modal").val(id);
}

function ajaxGetProductOrder(busqueda){
    //Ajax Controller/AdminListadoVentasController.
    //Obtiene todos los productos de la tienda. filtrados por un parámetro, nombre  del producto.
    //Devuelve el template buscarproductos.tpl

    let data = {
        ajax:true,
        action:'getProductosOrder',
        busqueda:busqueda,
    };
    if(busqueda != ''){
        $.ajax({
            type:"GET",
            url: soymanagerpublications_ajax_link,
            cache:false,
            dataType: 'html',
            data:data,
            success: function(response) {
            $("#items-2").empty().append(response);

            if(busqueda == "id"){
                $("#soymanagerpublication-idproduct-order").find(".icon-sort-down").css({
                    'transform':'rotate(-180deg)',
                    'color':'#4ac7e0'
                });
            }else if(busqueda == "name"){
                $("#soymanagerpublication-idname-order").find(".icon-sort-down").css({
                    'transform':'rotate(-180deg)',
                    'transition': '1s',
                    'color':'#4ac7e0'
                });
            }else if(busqueda == "medio"){
                $("#soymanagerpublication-idmedio-order").find(".icon-sort-down").css({
                    'transform':'rotate(-180deg)',
                    'transition': '1s',
                    'color':'#4ac7e0'
                });
            }

            },
            error: function(xhr) {
                alert("An error occured: " + xhr.status + " " + xhr.statusText);
            }
        });
    }
}

function llamadaAjaxPublicaciones(parametros) {
    $.ajax({
        type: 'POST',
        data: parametros,
        dataType: 'json',
        url: soymanagerpublications_ajax_link,
        success: function (data) {
            var accion = data['accion'];
            switch(accion) {
                case 'InfoMedio':
                    console.info((data['datos']));

                    // cambiamos de pestaña y rellenamos campos para modificar la publicación
                    $("#lang-2").removeClass("active");
                    $("#items-2").removeClass("active");
                    //mostramos la informacion de VER PUBLICACIONES
                    $(".soy-info-publicacion-activada").removeClass(".soy-info-publicacion-activada").addClass(".soy-info-publicacion-desactivada");

                    $("#items-1").addClass("active");
                    $("#lang-1").addClass("active");
                    $(".soy-info-publicacion-desactivada").removeClass(".soy-info-publicacion-desactivada").addClass(".soy-info-publicacion-activada");

                    $("input#soy_id_product").val(data['datos'].id_product);
                    $("input#soy_id_product").attr("readonly",true)
                    $("input#soymanagerpublications_url").val(data['datos'].url);
                    $("#soymanagerpublications_select_medios option[value="+data['datos'].medio+"]").attr("selected","selected");
                    $("#soymanagerpublications_select_medios").attr("disabled",true);
                    $("#soymanagerpublicaciones_hidden_medio").val(data['datos'].medio);
                    break;
                case 'borrarPublicacion':
                    if(data['datos'] == true){
                        $("#soymanagerpublicationfila-"+data['fila']).slideUp();
                    }
                    break;
                case 'activado':
                    $(".soy-publicacion-desactivada").slideUp();
                    $(".soy-publicacion-activa").slideDown();
                    break;

                case 'desactivado':
                    $(".soy-publicacion-activa").slideUp();
                    $(".soy-publicacion-desactivada").slideDown();
                    break;
            }
        }
    });
}

function ajaxGetProduct(event){
    //Ajax Controller/AdminListadoVentasController.
    //Obtiene todos los productos de la tienda. filtrados por un parámetro, nombre  del producto.
    //Devuelve el template buscarproductos.tpl
    let name_product = $('#soymanagerpublication_buscador_ajax').val();
    let data = {
        ajax:true,
        action:'getProductos',
        name_product:name_product,
    };
    if(name_product != ''){
        $.ajax({
            type:"GET",
            url: soymanagerpublications_ajax_link,
            cache:false,
            dataType: 'html',
            data:data,
            success: function(response) {
                console.info(response);
                if(!$("#soymanagerpublication_busqueda").length && !$('soyproductos_seleccionados').length){
                    $('#soymanagerpublication_buscador_ajax')
                        .parent()
                        .append('<div class="productos_seleccionados" id="soyproductos_seleccionados"></div>')
                        .append('<div class="buscar-productos" id="soymanagerpublication_busqueda" style="display: none;"></div>');
                }

                $("#soymanagerpublication_busqueda").empty().append(response);

                if(!$("#soymanagerpublication_busqueda").is(":visible")){
                    $("#soymanagerpublication_busqueda").slideDown();
                }

            },
            error: function(xhr) {
                alert("An error occured: " + xhr.status + " " + xhr.statusText);
            }
        });
    }else{
        $("#soymanagerpublication_busqueda")
            .slideDown()
            .empty();
    }
}


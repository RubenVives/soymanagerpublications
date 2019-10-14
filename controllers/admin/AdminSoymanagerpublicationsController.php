<?php

class AdminSoymanagerpublicationsController extends ModuleAdminController
{
    protected $response = array();
    function ajaxProcessgetInfoMedio()
    {
        $response['datos'] = self::getInfoMedio();
        $response['accion'] = "InfoMedio";
        die(Tools::jsonEncode($response));
    }

    function ajaxProcessborrarPublicacion()
    {
        $module = new SoyManagerPublications;
        $response['datos'] = $module->borrarPublicacion();
        $response['accion'] = "borrarPublicacion";
        $response['fila'] = Tools::getValue('fila');
        die(Tools::jsonEncode($response));
    }

    function ajaxProcessActivar()
    {
        $module = new SoyManagerPublications;
        $module->activarPublicacion();
        $response['accion'] = "activado";
        die(Tools::jsonEncode($response));
    }

    function ajaxProcessDesactivar()
    {
        $module = new SoyManagerPublications;
        $module->desactivarPublicacion();
        $response['accion'] = "desactivado";
        die(Tools::jsonEncode($response));
    }

    private function getInfoMedio(){
        $module = new SoyManagerPublications;
        $datos = $module->datosPublicacion(Tools::getValue('id_product'),Tools::getValue('medio'));
        return $datos;
    }

    public function ajaxProcessgetProductos(){

        $cadena = Tools::getValue('name_product');

        $sql = "SELECT p.id_product,
        p.reference,
        ps.id_shop,
        pl.name,
        pl.description_short,
        pl.link_rewrite,
        ish.id_image
		FROM `"._DB_PREFIX_."product` p
		INNER JOIN `"._DB_PREFIX_."product_shop` ps ON p.id_product = ps.id_product 
		INNER JOIN `"._DB_PREFIX_."product_lang` pl ON ps.id_product = pl.id_product 
		INNER JOIN  `"._DB_PREFIX_."image_shop` ish ON p.id_product = ish.id_product AND ps.id_shop = ish.id_shop
		WHERE p.id_product LIKE '%".$cadena."%' OR 
		p.reference LIKE '%".$cadena."%' OR
		pl.name LIKE '%".$cadena."%' 
		GROUP BY p.id_product";

        $productos = Db::getInstance()->executeS($sql);
        if(count($productos)){
            foreach($productos as $key => $producto){
                $productos[$key]['imagePath'] =  Context::getContext()->link->getImageLink($producto['link_rewrite'], $producto['id_image'], 'home_default');
            }
            $this->context->smarty->assign('productos',$productos);
        }
        die($this->context->smarty->fetch(_PS_MODULE_DIR_.'soymanagerpublications/views/templates/admin/buscarproductos.tpl'));
    }

    public function ajaxProcessgetProductosOrder(){

        $busqueda = (Tools::getValue('busqueda') == "id") ? "id_product" : Tools::getValue('busqueda');

        $sql = 'SELECT id,id_product,name,medio,url,image_url,active FROM ' . _DB_PREFIX_ . 'soymanagerpublications 
                ORDER BY '.$busqueda.' ASC';
        $result = Db::getInstance()->executeS($sql);
        $this->context->smarty->assign(array(
            "busqueda"=>$busqueda,
            "soymanagerpublications_publicaciones" => $result
        ));
        die($this->context->smarty->fetch(_PS_MODULE_DIR_.'soymanagerpublications/views/templates/admin/soymanagerpublication_tabla.tpl'));
    }
}
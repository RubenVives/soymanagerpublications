<?php

/**
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
 * /*
 *
 * == README ==
 *
 * PRESTASHOP 1.7
 *
 * Para visualizar el módulo enn la ficha del producto agregamos en el tpl product.tpl
 * a continuación de
 * <div class="product-actions">
 *  .....
 * </div> insertamos *
 *  {* MOD soy*}
 *      {block name='soymanagerpublications'}
            {hook h='soyManagerPublications' soyproduct=$product mod='soymanagerpublications'}
        {/block}
 *   {* /MOD *}
 *
 * PRESTASHOP 1.6
 *
 * Para visualizar el módulo en la ficha del producto agregamos en el tpl product.tpl
 * a continuación de
 * <div id="short_description_block">
    {if $product->description_short}
        <div id="short_description_content" class="rte align_justify" itemprop="description">{$product->description_short}</div>
    {/if}

    {* MOD soy*}
        {block name='soymanagerpublications'}
            {hook h='soyManagerPublications' soyproduct=$product mod='soymanagerpublications'}
        {/block}
 * {* /MOD *}
 *
 *
 * == CHANGELOG ==
 *
 * === Version 1.0.0 (1/10/2019) ===
 * Módulo que permite agregar al cliente medio y crear publicaciones asociandolas a los productos de la tienda
 *
 *
 */

if (!defined('_PS_VERSION_'))
    exit;

define("MAX_WIDTH",90);
class SoyManagerPublications extends Module
{

    protected $_html;

    public function __construct()
    {
        $this->name = 'soymanagerpublications';
        $this->tab = 'front_office_features';
        $this->author = 'Soy.es';
        $this->ps_versions_compliancy = array('min' => '1.6.0', 'max' => _PS_VERSION_);
        $this->version = '1.0.0';
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Manager publications');
        $this->description = $this->l('Allows the customer configure publications.');
    }

    public function install()
    {
        /**
         * Registramos nuestro controlador admin en una tab para poder obtener la ruta del modulo
         * con $link->getAdminLink("controlador")
         */
        $tab = new Tab();
        $tab->active = 1;
        $tab->class_name = 'AdminSoymanagerpublications'; //nombre del controlador sin Controller
        $tab->name = array();
        foreach (Language::getLanguages(true) as $lang)
            $tab->name[$lang['id_lang']] = 'SoyManagerPublications'; //nombre de las clase
        $tab->id_parent = -1;
        $tab->module = $this->name;
        $tab->add();

        //Vamos a comprobar en que version de presta estamos para registrar un hook u otro
        //Ya que para que los js funcionen bien en 1.7.6.0 hay que usar el actionAdminControllerSetMedia
        $registerHook = (version_compare(_PS_VERSION_, '1.7', '>=')) ? "actionAdminControllerSetMedia" : "displayBackOfficeHeader";

        return parent::install() &&
            $this->registerHook($registerHook) &&
            $this->registerHook('header') &&
            $this->registerHook('soyManagerPublications') &&
            $this->createTables();
    }

    /**
     * @return bool
     */
    public function uninstall()
    {
        $this->_clearCache('*');
        return parent::uninstall();
    }

    public function createTables()
    {
        $sql = 'CREATE TABLE  if not exists `' . _DB_PREFIX_ . 'soymanagerpublications` (
                    `id` int(11) AUTO_INCREMENT,
                    `id_product` int(10),
                    `name` varchar(128),
                    `id_shop` int(11),
                    `id_lang` int(10),                    
                    `medio` varchar(128),
                    `url` varchar(128),
                    `image_url` varchar(128),
                    `active` int(11),
                    PRIMARY KEY (`id`)
                  ) ENGINE=InnoDB DEFAULT CHARSET=utf8';
        Db::getInstance()->execute($sql);

        $sql = 'CREATE TABLE  if not exists `' . _DB_PREFIX_ . 'soymanagermedios` (
                    `id` int(11) AUTO_INCREMENT,
                    `medio` varchar(128) UNIQUE,
                    `image_url` varchar(512),
                    PRIMARY KEY (`id`)
                  ) ENGINE=InnoDB DEFAULT CHARSET=utf8';
        Db::getInstance()->execute($sql);

        return true;
    }

    public function getContent()
    {
        //Logo soy
        $this->context->smarty->assign(
            "soymanagerpublications_iconoSoy", _MODULE_DIR_ . "soymanagerpublications/logo.png"
        );
        $this->postData();
        //recogemos el herlperForm de la creacion de medios
        $fields_form = $this->renderFormMedios();

        //SQL para comprobar si existen medios, si existen mostramos la seccion de publicaciones
        $sql = 'SELECT medio FROM ' . _DB_PREFIX_ . 'soymanagermedios';
        $result = Db::getInstance()->executeS($sql);
        $fields_publications = "";
        if ($result) {
            //Cargamos el segundo bloque de publicaciones si existe algun medio que mostrar
            $fields_publications = $this->renderFormPublications();
            $fields_modificar_medios = $this->renderFormModificarMedio();
        }

        return $this->context->smarty->fetch($this->local_path . 'views/templates/admin/soymodulo_info.tpl') . $fields_publications . $fields_form . $fields_modificar_medios;
    }

    public function hookDisplayBackOfficeHeader()
    {
        if (Tools::getValue('controller') == "AdminModules" && Tools::getValue('configure') == "soymanagerpublications") {
            $this->context->controller->addCSS($this->_path . 'views/css/soymodulo_info.css');
            $this->context->controller->addCSS($this->_path . 'views/css/soymanagerpublications.css');
            $this->context->controller->addJS($this->_path . 'views/js/soymanagerpublications.js');

            //Creamos el enlace al controlador y lo añadimos al fichero js
            $link = new Link;
            $ajax_link = $link->getAdminLink("AdminSoymanagerpublications");
            Media::addJsDef(array(
                "soymanagerpublications_ajax_link" => $ajax_link
            ));
        }
    }

    /**
     *hookAction para meter JS y CSS para prestashop 1.7.6
     */
    public function hookActionAdminControllerSetMedia()
    {
        if (Tools::getValue('controller') == "AdminModules" && Tools::getValue('configure') == "soymanagerpublications") {
            $this->context->controller->addCSS($this->_path . 'views/css/soymodulo_info.css');
            $this->context->controller->addCSS($this->_path . 'views/css/soymanagerpublications.css');
            $this->context->controller->addJS($this->_path . 'views/js/soymanagerpublications.js');

            //Creamos el enlace al controlador y lo añadimos al fichero js
            $link = new Link;
            $ajax_link = $link->getAdminLink("AdminSoymanagerpublications");
            Media::addJsDef(array(
                "soymanagerpublications_ajax_link" => $ajax_link
            ));
        }
    }

    public function hookHeader()
    {
        if ($this->context->controller->php_self == 'product') {
            $this->context->controller->addCSS($this->_path . 'views/css/soymanagerpublications_front.css');
        }
    }

    public function renderFormMedios()
    {
        $fields_form = array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('New medio'),
                    'image' => _MODULE_DIR_ . "soymanagerpublications/logo.gif"
                ),
                'input' => array(
                    array(
                        'type' => 'html',
                        'name' => 'soymanagerpublications_infomedio',
                        'required' => true,
                        'html_content' => $this->context->smarty->fetch($this->local_path . 'views/templates/admin/soymanagerpublications_infocrearmedio.tpl')
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Media'),
                        'name' => 'soymanagerpublications_medio',
                        'required' => true,
                        'hint' => $this->l('Media')
                    ),
                    array(
                        'label' => $this->l('Select logo'),
                        'type' => 'file',
                        'value' => 'manager',
                        'name' => 'filename',
                        'required' => true,
                        'hint' => $this->l('Select logo'),
                        'desc' => $this->l('Choose the logo')
                    )
                ),
                'submit' => array(
                    'name' => 'soymanagerpublications_guardarConfi',
                    'type' => 'submit',
                    'title' => $this->l('Add media'),
                    'class' => 'btn btn-default pull-right'
                )
            ),
        );
        $helper = new HelperForm();
        // Module, token and currentIndex
        $helper->show_toolbar = false;
        $helper->module = $this;
        $helper->name_controller = $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = AdminController::$currentIndex . '&configure=' . $this->name;

        return $helper->generateForm(array($fields_form));
    }

    public function renderFormPublications()
    {
        //cargamos el tab de "CREAR PUBLICACIONES"
        $this->renderFormCreatePublications();

        //metemos al tpl la informacion de las publicaciones para el tab "VER PUBLICACIONES"
        $this->getConfigurationViewsPublications();
        $fields_form = array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('Publications'),
                    'image' => _MODULE_DIR_ . "soymanagerpublications/logo.gif"
                ),
                'input' => array(
                    array(
                        'type' => 'html',
                        'name' => 'soymanagerpublications_infopublications',
                        'required' => true,
                        'html_content' => $this->context->smarty->fetch($this->local_path . 'views/templates/admin/soymanagerpublications_infopublications.tpl'),
                    ),
                    array(
                        'type' => 'html',
                        'name' => 'soylista_publicaciones',
                        'required' => true,
                        'html_content' => $this->context->smarty->fetch($this->local_path . 'views/templates/admin/soymanagerpublications.tpl'),
                    )
                )
            ),
        );
        $helper = new HelperForm();
        // Module, token and currentIndex
        $helper->show_toolbar = false;
        $helper->module = $this;
        $helper->name_controller = $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = AdminController::$currentIndex . '&configure=' . $this->name;
        return $helper->generateForm(array($fields_form));
    }

    public function renderFormCreatePublications()
    {

        $fields_form2 = array(
            'form' => array(

                'input' => array(
                    array(
                        'type' => 'text',
                        'label' => $this->l('Search'),
                        'required' => true,
                        'hint' => $this->l('Search by reference, id or name'),
                        'id'=>'soymanagerpublication_buscador_ajax',
                        'desc' => $this->l('Search by reference, id or name')
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Selected product'),
                        'required' => true,
                        'hint' => $this->l('Selected product'),
                        'class' => '',
                        'id'=>'soymanagerpublication_producto_seleccionado'

                    ),
                    array(
                        'type' => 'hidden',
                        'required' => true,
                        'name' => 'soymanagerpublications_idProduct',
                        'id'=>'soymanagerpublications_idProduct'
                    ),
                    array(
                        'type' => 'select',
                        'label' => $this->l('Medios'),
                        'name' => 'soymanagerpublications_select_medios',
                        'required' => true,
                        'class' => 'form-control',
                        'options' => array(
                            'query' => $options =
                                self::listaMedios()
                        ,
                            'id' => 'id_option',
                            'name' => 'name'
                        )
                    ), array(
                        'type' => 'text',
                        'label' => $this->l('Url'),
                        'name' => 'soymanagerpublications_url',
                        'required' => true,
                        'hint' => $this->l('Url')
                    )
                ),
                'submit' => array(
                    'name' => 'soymanagerpublications_guardarConfi_Create',
                    'type' => 'submit',
                    'title' => $this->l('Add publication'),
                    'class' => 'btn btn-default pull-right'
                )
            ),
        );

        //ddd($fields_form2);
        $helper = new HelperForm();
        // Module, token and currentIndex
        $helper->show_toolbar = false;
        $helper->module = $this;
        $helper->name_controller = $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = AdminController::$currentIndex . '&configure=' . $this->name;

        $this->context->smarty->assign(array(
            "helperFormCrearPublicaciones" => $helper->generateForm(array($fields_form2))
        ));
    }

    public function renderFormModificarMedio()
    {
        $fields_form = array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('Modify'),
                    'image' => _MODULE_DIR_ . "soymanagerpublications/logo.gif"
                ),
                'input' => array(
                    array(
                        'type' => 'html',
                        'name' => 'soymanagerpublications_infomodmedio',
                        'required' => true,
                        'html_content' => $this->context->smarty->fetch($this->local_path . 'views/templates/admin/soymanagerpublications_infomodmedio.tpl')
                    ),
                    array(
                        'type' => 'select',
                        'label' => $this->l('Medios'),
                        'name' => 'soymanagerpublications_select_medios_mod',
                        'class' => 'form-control',
                        'options' => array(
                            'query' => $options =
                                self::listaMedios()
                        ,
                            'id' => 'id_option',
                            'name' => 'name'
                        )
                    ), array(
                        'type' => 'text',
                        'label' => $this->l('New name'),
                        'name' => 'soymanagerpublications_medio_actualizado',
                        'hint' => $this->l('Insert the new name to the medio')
                    ), array(
                        'label' => $this->l('Select logo'),
                        'type' => 'file',
                        'value' => 'manager',
                        'name' => 'filename_mod',
                        'hint' => $this->l('Select logo'),
                        'desc' => $this->l('Choose the logo')
                    )
                ),
                'submit' => array(
                    'name' => 'soymanagerpublications_actualizarMedio',
                    'type' => 'submit',
                    'title' => $this->l('Update'),
                    'class' => 'btn btn-default pull-right',
                    'icon' => 'process-icon-update'
                )
            ),
        );
        $helper = new HelperForm();
        // Module, token and currentIndex
        $helper->show_toolbar = false;
        $helper->module = $this;
        $helper->name_controller = $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = AdminController::$currentIndex . '&configure=' . $this->name;

        return $helper->generateForm(array($fields_form));
    }

    public function postData()
    {
        if (Tools::getValue('controller') == "AdminModules" && Tools::getValue('configure') == "soymanagerpublications") {

            //CREAMOS MEDIO
            if (Tools::getValue("soymanagerpublications_guardarConfi")) {
                //comprobamos campos
                if (Tools::getValue('soymanagerpublications_medio') != "" && Tools::getValue('filename') != "") {
                    //comprobamos los datos recibidos del formulario que crea un nuevo medio( el texto y el logo )
                    $this->comprobarNuevoMedio();
                } else {
                    $error = $this->displayError($this->l('Fill all fields'));

                    $this->context->smarty->assign("soymanagererror", $error);
                }

            //CREAMOS PUBLICACION
            } else if (Tools::getValue('soymanagerpublications_guardarConfi_Create')) {
                $this->nuevaPublicacion();

            //ACTUALIZAMOS URL DE UNA PUBLICACION
            } else if (Tools::getValue("soymanagerpublications_guardarConfi_Update_url_modal")) {
                $this->actualizarPublicacion();
            //MODIFICAMOS UN MEDIO
            } else if (Tools::getValue('soymanagerpublications_actualizarMedio')) {
                $this->actualizarMedio();
            }
        }
    }

    /**
     * Función que comprueba los datos (imagen y medio) recibidos del formulario que crea un nuevo medio
     *
     * Comprueba que el logo sea correcto y comprabamos si el medio esta creado ya o no.
     */
    public function comprobarNuevoMedio()
    {
        $type_soy = explode("/", $_FILES['filename']['type']);
        list($tmp_width, $tmp_height) = getimagesize($_FILES['filename']['tmp_name']);

        $medio = self::sanitizarTexto(Tools::getValue('soymanagerpublications_medio'));
        if ($this->comprobarLogo($_FILES['filename'])) {
            if (!self::comprobarExisteMedio($medio)) {

                //comprobamos que la imagen no sea cuadrada
                if ($tmp_width != $tmp_height) {
                    $new_height = ($tmp_height * MAX_WIDTH) / $tmp_width;
                    $new_width = MAX_WIDTH;
                } else {
                    $new_height = MAX_WIDTH;
                    $new_width = MAX_WIDTH;
                }
                //redimensionamos la imagen y la llevamos a la carpeta del modulo views/images

                //para diferenciar los logos agregamos un numero delante del nombre del logo, en este sacamos el ultimo
                //valor del id autoincrementable de la tabla soymanagermedios y le sumamos 1, en caso ser la primera vez
                //agregamos un 0 -> 0-logo.png || 12-logo.png

                $sql_id = 'SELECT max(id) FROM '._DB_PREFIX_.'soymanagermedios';
                $result = Db::getInstance()->getValue($sql_id);
                $id_image = ($result)?($result+1)."-":"0-";
                

                $resize = ImageManager::resize($_FILES['filename']['tmp_name'], _PS_MODULE_DIR_ . "soymanagerpublications/views/images/" .$id_image. Tools::getValue("filename"), $new_width, $new_height, $type_soy[1]);
                $path_image = $this->context->link->getBaseLink() . "modules/soymanagerpublications/views/images/" .$id_image. Tools::getValue("filename");

                self::agregarMedio($medio, $path_image);
                $correcto = $this->displayConfirmation($this->l('Updated data'));
                $this->context->smarty->assign("correcto", $correcto);
            } else {
                $error = $this->displayError($this->l('This data already exists '));
                $this->context->smarty->assign("soymanagererror", $error);
            }
        }
    }

    /**
     * Función que evalua la imagen que cliente añade, usando la clase ImagManager que controla el formato de imagen y el
     * tamaño maximo de la imagen que le pasamos por parametro
     *
     * Imagenes soportadas para redimensionar hasta 5megas
     * En caso de error validando la imagen con validateUpload() nos devolvera el tipo de error y lo mostramos en pantalla
     *
     * @param $filename
     * @return bool
     */
    public function comprobarLogo($filename)
    {
        $memory_limit = Tools::getMemoryLimit();
        $infos = @getimagesize($filename['tmp_name']);
        $bits = $infos['bits'] / 8;
        $channel = isset($infos['channels']) ? $infos['channels'] : 1;
        $current_memory = memory_get_usage();
        //calculamos que el tamaño de la imagen no es superior al parmitido para redimensionar la imagen
        if (($infos[0] * $infos[1] * $bits * $channel + 65536) * 1.8 + $current_memory < $memory_limit - 1048576) {
            $validate_img = ImageManager::validateUpload($filename,$memory_limit);
            if ($validate_img) { //error imagen
                $error = $this->displayError($validate_img);
                $this->context->smarty->assign("soymanagererror", $error);
                return false;
            } else {
                return true;
            }
        }else{
            $error = $this->displayError($this->l('Image too big'));
            $this->context->smarty->assign("soymanagererror", $error);
        }
    }

    /**
     * Función que crea nuevas publicaciones partiendo de los medios ya creados
     * Añadimos al medio el ID product y la URL que recibimos de formulario
     */
    public function nuevaPublicacion()
    {
        if (
            is_numeric(Tools::getValue('soymanagerpublications_idProduct')) &&
            Tools::getValue('soymanagerpublications_url') != "" &&
            Tools::getValue('soymanagerpublications_select_medios')
        ) {
            $idProduct = Tools::getValue('soymanagerpublications_idProduct');
            //Comprobar si existe el ID del producto
            $sql = 'SELECT id_product FROM ' . _DB_PREFIX_ . 'product  
                            WHERE id_product=' . (int)$idProduct;
            $result = Db::getInstance()->getValue($sql);
            if ($result) {
                $medio = self::sanitizarTexto(Tools::getValue('soymanagerpublications_select_medios'));
                $url = self::sanitizarTexto(Tools::getValue('soymanagerpublications_url'));

                //comprobamos que el medio existe por si lo recibimos modificado
                if (self::comprobarExisteMedio($medio)) {
                    $sql = 'SELECT image_url FROM ' . _DB_PREFIX_ . 'soymanagermedios
                            WHERE medio="' . $medio . '"';
                    $result = Db::getInstance()->getValue($sql);

                    $product = new Product($idProduct, false, Context::getContext()->language->id, Context::getContext()->shop->id);

                    $sql = 'INSERT INTO ' . _DB_PREFIX_ . 'soymanagerpublications 
                            (id_product,name,id_shop,id_lang,medio,url,image_url,active)
                            VALUES 
                            (' . (int)$idProduct . ',"' . $product->name . '",' . (int)Context::getContext()->shop->id . ',' . (int)Context::getContext()->language->id . ',"' . $medio . '","' . $url . '","' . $result . '",1)';

                    if (!Db::getInstance()->execute($sql)) {
                        //avisamos al cliente que la publicacion ya existe y que puede modificarla
                        $error = $this->displayError($this->l('This publication already exists.'));
                        $this->context->smarty->assign("soymanagererror", $error);
                    } else {
                        //avisamos al cliente que la publicacion nueva a sido creada correctamente
                        $correcto= $this->displayConfirmation($this->l('The configuration has been updated successfully.'));
                        $this->context->smarty->assign("correcto", $correcto);
                    }
                }
            } else {
                $error = $this->displayError($this->l('This id product not exist.'));
                $this->context->smarty->assign("soymanagererror", $error);
            }
        } else {
            $error = $this->displayError($this->l('Fill all fields'));
            $this->context->smarty->assign("soymanagererror", $error);
        }
    }

    /**
     * Función que actualiza el nombre del medio o el logo del medio
     *
     */
    public function actualizarMedio()
    {
        $error = false;

        $filename = (version_compare(_PS_VERSION_, '1.7', '>=')) ? "filename_mod" : "filename";

        if(
            Tools::getValue('soymanagerpublications_medio_actualizado') == "" &&
            Tools::getValue($filename) == "") {

            $error = $this->displayError($this->l('All fields are empty, fill the new name to the media or choose a new logo'));
            $this->context->smarty->assign("soymanagererror", $error);

        }else{
            if (
                Tools::getValue('soymanagerpublications_medio_actualizado') != "" ||
                Tools::getValue($filename) != ""

            ) {
                //modificamos el nombre del medio si recibimos uno nuevo
                if (Tools::getValue('soymanagerpublications_medio_actualizado') && Tools::getValue('soymanagerpublications_medio_actualizado') != "") {

                    if (!self::comprobarExisteMedio(self::sanitizarTexto(Tools::getValue('soymanagerpublications_medio_actualizado')))) {
                        $medio_sanitizado = self::sanitizarTexto(Tools::getValue('soymanagerpublications_medio_actualizado'));

                        //preparamos los valores del options para ser agregadas a la sql update en caso de existir modificacion de medio
                        $options['medio'] = 'medio="' . $medio_sanitizado . '"';
                    } else {
                        $error = $this->displayError($this->l('This data already exists '));
                        $this->context->smarty->assign("soymanagererror", $error);
                        $error = true;
                    }
                }

                //modificamos el logo del medio si recibimos uno nuevo
                if (Tools::getValue($filename) && Tools::getValue($filename) != "") {

                    $type_soy = explode("/", $_FILES['filename_mod']['type']);
                    list($tmp_width, $tmp_height) = getimagesize($_FILES['filename_mod']['tmp_name']);
                    if ($this->comprobarLogo($_FILES['filename_mod'])) {
                        //comprobamos que la imagen no sea cuadrada
                        if ($tmp_width != $tmp_height) {
                            $new_height = ($tmp_height * MAX_WIDTH) / $tmp_width;
                            $new_width = MAX_WIDTH;
                        } else {
                            $new_height = MAX_WIDTH;
                            $new_width = MAX_WIDTH;
                        }
                        //redimensionamos la imagen y la llevamos a la carpeta del modulo views/images

                        //borramos la imagen antigua y guardamos la nueva conservando el prefijo con el id que tenia el logo
                        $sql_id_logo = 'SELECT id,image_url FROM '._DB_PREFIX_.'soymanagermedios
                                        WHERE medio="'.Tools::getValue('soymanagerpublications_select_medios_mod').'"';

                        $result = Db::getInstance()->getRow($sql_id_logo);
        
                        $pos = strpos($result['image_url'],"soymanagerpublications",0);
                        $path= substr($result['image_url'],$pos);

                        if($result){
                            //borramos la imagen
                            unlink(_PS_MODULE_DIR_ .$path);
                        }

                        //agregamos la imagen nueva
                        $resize = ImageManager::resize($_FILES['filename_mod']['tmp_name'], _PS_MODULE_DIR_ . "soymanagerpublications/views/images/" .$result['id']."-". Tools::getValue($filename), $new_width, $new_height, $type_soy[1]);
                        $path_image = $this->context->link->getBaseLink() . "modules/soymanagerpublications/views/images/" .$result['id']."-". Tools::getValue($filename);

                        //preparamos los valores del options para ser agregadas a la sql update en caso de existir modificacion del logo
                        $options['image_url'] = 'image_url="' . $path_image . '"';
                    } else {
                        $error = true;
                    }
                }

                //actualizamos sino hay errores
                if(!$error) {
                    //ACTUALIZAMOS TABLA MEDIOS soymanagermedios
                    $sql = 'UPDATE ' . _DB_PREFIX_ . 'soymanagermedios SET';
                    $tmp = 1;

                    //En options tenemos las 2 posibles actualizaciones el name del medio o el logo,o ambas.
                    //agregamos al SQL las posibles actualizaciones
                    foreach ($options as $item => $valor) {
                        $sql .= ' ' . $valor;
                        if ($tmp < count($options)) {
                            $sql .= ",";
                        }
                        $tmp++;
                    }

                    $old_name_medio = self::sanitizarTexto(Tools::getValue('soymanagerpublications_select_medios_mod'));
                    $sql .= ' WHERE medio="' . $old_name_medio . '"';
                    Db::getInstance()->execute($sql);

                    //ACTUALIZAMOS TABLA PUBLICACIONES soymanagerpublications
                    $sql = 'UPDATE ' . _DB_PREFIX_ . 'soymanagerpublications SET';
                    $tmp = 1;

                    foreach ($options as $item => $valor) {
                        $sql .= ' ' . $valor;
                        if ($tmp < count($options)) {
                            $sql .= ",";
                        }
                        $tmp++;
                    }
                    $old_name_medio = self::sanitizarTexto(Tools::getValue('soymanagerpublications_select_medios_mod'));
                    $sql .= ' WHERE medio="' . $old_name_medio . '"';
                    Db::getInstance()->execute($sql);

                    $correcto= $this->displayConfirmation($this->l('The configuration has been updated successfully.'));
                    $this->context->smarty->assign("correcto", $correcto);
                }
            }
        }
    }

    public function actualizarPublicacion()
    {

        if (is_numeric(Tools::getValue('soy_idProduct_modal')) && Tools::getValue('soymanagerpublications_url_modal') != "") {

            $url = self::sanitizarTexto(Tools::getValue('soymanagerpublications_url_modal'));
            $medio = self::sanitizarTexto(Tools::getValue('soymanagerpublications_medio_modal'));
            $idProduct = Tools::getValue('soy_idProduct_modal');
            $id_publicacion = Tools::getValue('soymanagerpublications_id_modal');
            //comprobamos que el medio existe por si lo recibimos modificado
            if (self::comprobarExisteMedio($medio)) {
                $sql = 'UPDATE ' . _DB_PREFIX_ . 'soymanagerpublications
                SET url="' . $url . '" 
                WHERE id_product=' . (int)$idProduct . ' 
                AND medio="' . $medio . '" 
                AND id='.(int)$id_publicacion;

                if (Db::getInstance()->executeS($sql)) {
                    $correcto= $this->displayConfirmation($this->l('The configuration has been updated successfully.'));
                    $this->context->smarty->assign("correcto", $correcto);
                } else {
                    $error= $this->displayError($this->l('This media not exist.'));
                    $this->context->smarty->assign("soymanagererror", $error);

                }
            }
        } else {
            $error= $this->displayError($this->l('The field URL is empty.'));
            $this->context->smarty->assign("soymanagererror", $error);
        }



    }

    public function getConfigurationViewsPublications()
    {

        $sql = 'SELECT id,id_product,name,medio,url,image_url,active FROM ' . _DB_PREFIX_ . 'soymanagerpublications ORDER BY id_product';
        $result = Db::getInstance()->executeS($sql);

        $link = new Link();
        $arrayLinks = array();
        foreach ($result as $medio) {
            $arrayLinks[] = $link->getProductLink($medio['id_product']);
        }
        $this->context->smarty->assign(array(
            "soymanagerpublications_publicaciones" => $result,
            "arrayUrls" => $arrayLinks
        ));

        //asignamos una variable JS con la informacion de las publicaciones para acceder a los datos desde el JS y
        //poder modificar los datos en caso de ser necesario
        $arrayJS = [];
        foreach ($result as $medio) {
            $arrayJS[$medio['id_product']] = array(
                "id_product" => $medio['id_product'],
                "medio" => $medio['medio'],
                "url" => $medio['url']
            );
        }
        Media::addJsDef(array(
            "soymanagerpublications_arrayJS" => $arrayJS
        ));

    }

    private static function listaMedios()
    {
        $sql = 'SELECT medio FROM ' . _DB_PREFIX_ . 'soymanagermedios ORDER BY medio DESC';
        $lista = Db::getInstance()->executeS($sql);

        $medios = [];
        foreach ($lista as $key => $medio) {
            $medios[$key]['id_option'] = $medio['medio'];
            $medios[$key]['name'] = $medio['medio'];
        }
        return $medios;
    }

    public function activarPublicacion()
    {
        $id_product = Tools::getValue('id_product');
        $medio = self::sanitizarTexto(Tools::getValue('medio'));
        $sql = 'UPDATE ' . _DB_PREFIX_ . 'soymanagerpublications 
                SET active=1 WHERE id_product=' . (int)$id_product . ' AND medio="' . $medio . '"';
        Db::getInstance()->execute($sql);
    }

    public function desactivarPublicacion()
    {
        $id_product = Tools::getValue('id_product');
        $medio = self::sanitizarTexto(Tools::getValue('medio'));
        $sql = 'UPDATE ' . _DB_PREFIX_ . 'soymanagerpublications 
                SET active=0 WHERE id_product=' . (int)$id_product . ' AND medio="' . $medio . '"';
        Db::getInstance()->execute($sql);
        $this->context->smarty->assign("soy_datos_publicacion_desactivada", true);
    }

    private static function comprobarExisteMedio($medio)
    {

        $sql = 'SELECT medio FROM ' . _DB_PREFIX_ . 'soymanagermedios 
                WHERE medio="' . $medio . '"';
        //ddd($sql);
        $result = Db::getInstance()->getValue($sql);
        if ($result) {
            return true;
        } else {
            return false;
        }
    }

    private static function agregarMedio($medio, $path_image)
    {
        $sql = 'INSERT INTO ' . _DB_PREFIX_ . 'soymanagermedios (medio,image_url) VALUES ("' . $medio . '","' . $path_image . '")';
        Db::getInstance()->execute($sql);
    }

    public function borrarPublicacion()
    {
        $id_product = Tools::getValue('id_product');
        $medio = Tools::getValue('medio');
        $id_publicacion = Tools::getValue('id');
        $medio_sanitizado = SoyManagerPublications::sanitizarTexto($medio);

        $sql = 'DELETE FROM ' . _DB_PREFIX_ . 'soymanagerpublications
                WHERE id_product=' . (int)$id_product . ' 
                AND medio="' . $medio_sanitizado . '" 
                AND id='.(int)$id_publicacion;

        if (Db::getInstance()->execute($sql)) {
            return true;
        } else {
            return false;
        }
    }

    public function datosPublicacion($id, $medio)
    {
        $sql = 'SELECT id_product,name,medio,url FROM ' . _DB_PREFIX_ . 'soymanagerpublications
                WHERE id_product=' . (int)$id . '
                AND medio="' . $medio . '"';
        $result = Db::getInstance()->getRow($sql);
        if ($result) {
            return $result;
        } else {
            return false;
        }
    }

    public function hookSoyManagerPublications($params)
    {
        $sql = 'SELECT url,image_url FROM ' . _DB_PREFIX_ . 'soymanagerpublications
                WHERE id_product=' . (int)$params['soyproduct']->id . ' AND active=1';
        $result = Db::getInstance()->executeS($sql);
        if ($result) {
            $this->context->smarty->assign(array(
                "soymanagerpublications_publicaciones" => $result
            ));
        }
        return $this->context->smarty->fetch(_PS_MODULE_DIR_ . '/soymanagerpublications/views/templates/hook/soymanagerpublications_front.tpl');
    }

    /**
     * Borramos las posibles inyecciones sql de los textos
     *
     * @param $texto
     * @return mixed
     */
    public static function sanitizarTexto($texto)
    {
        $texto_sanitizado = filter_var($texto, FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES);
        $texto_sanitizado = str_replace('"', "", $texto_sanitizado);
        $texto_sanitizado = str_replace("'", "", $texto_sanitizado);
        $texto_sanitizado = str_replace(";", "", $texto_sanitizado);
        return $texto_sanitizado;
    }

}
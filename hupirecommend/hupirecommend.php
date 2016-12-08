<?php
/**
 *   2009-2016 ohmyweb!
 *
 *   @author    ohmyweb <contact@ohmyweb.fr>
 *   @copyright 2009-2016 ohmyweb!
 *   @license   Proprietary - no redistribution without authorization
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

class HupiRecommend extends Module
{
    public function __construct()
    {
        $this->name = 'hupirecommend';
        $this->tab = 'analytics_stats';
        $this->version = '1.0.1';
        $this->author = 'Hupi';
        $this->need_instance = 0;

        /**
         * Set $this->bootstrap to true if your module is compliant with bootstrap (PrestaShop 1.6)
         */
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Hupi Recommendation');
        $this->description = $this->l('This recommend products thanks to Hupi');

        $this->confirmUninstall = $this->l('Are you sure you want to uninstall my module?');
    }

    /**
     * Don't forget to create update methods if needed:
     * http://doc.prestashop.com/display/PS16/Enabling+the+Auto-Update
     */
    public function install()
    {
        return parent::install() &&
            $this->isParentModuleInstalled() &&
            $this->registerHook('displayFooter') &&
			$this->registerHook('displayHomeTab') &&
			$this->registerHook('displayHomeTabContent') &&
            $this->registerHook('productTab') &&
            $this->registerHook('productTabContent') &&
            $this->registerHook('displayShoppingCart') &&
            $this->registerHook('displayHupiRecommendations') &&
            $this->registerHook('actionCartSave') &&
            $this->updateConfigFile('install');
    }

    public function uninstall()
    {
        return parent::uninstall() &&
            $this->updateConfigFile('uninstall');
    }
    
    public function isParentModuleInstalled() {
        if(Module::isInstalled('hupilytics') && Module::isEnabled('hupilytics')) {
            return true;
        } else {
            $this->_errors[] = Tools::displayError('hupilytics must be installed on your shop.');
            return false;
        }
    }
    
    private function codeModificationFile()
    {
        $modif = '<li{if isset($id) && $id == "hupirecommend"} data-product="{$product.id_product}"{/if} ';
        return array('../themes/'._THEME_NAME_.'/product-list.tpl' =>
                            array(
                                'before'	=> '<li ',
                                'after'		=> $modif,
                                'search'	=> 'li_combi_'
                            ));
    }
    
    private function updateConfigFile($action = 'install')
    {
        foreach ($this->codeModificationFile() as $file => $modification)
        {
            if (!file_exists($file)) {
                return false;
            }
         
            list ($before, $after) = ($action == 'uninstall')? array($modification['after'], $modification['before']) : array($modification['before'], $modification['after']);
            $chmod = fileperms($file);
            $content = Tools::file_get_contents($file);
        
            if (strstr($content, $modification['search']) && $action == 'install' || !strstr($content, $modification['search']) && $action != 'install') {
                return true;
            }
            
            $content = str_replace($before, $after, $content);
            chmod($file, 0777);
            if (!$fp = fopen ($file, 'w')) {
                chmod($file, $chmod);
                fclose ($fp);
                return false;
            }
           
            fwrite ($fp, $content);
            fclose ($fp);
            chmod($file, $chmod);
        }
        return true;
    }
    private function testModificationsFiles()
    {
        $html = '';
        foreach ($this->codeModificationFile() as $file => $modification)
        {
            if (!file_exists($file)) {
                $html .= $this->displayError($this->l('Impossible to find the file').' : '.$file);
            } else {
                if (!$this->fileModified($file, $modification['after'])) {
                    $html .= $this->displayError(
                        $this->l('To be able to track the clicks of recommendations thank you to replace').' : <br/><code>'.Tools::htmlentitiesUTF8($modification['before']).'</code>'.$this->l(' by ').'<code>'.Tools::htmlentitiesUTF8($modification['after']).'</code><br/>'.$this->l('in the file').' <strong>'.$file.'</strong>');
                }
            }
        }
        return $html;
    }
    
    private function fileModified($file, $search = false)
    {
        $search = (!$search)? MD5($this->name) : $search;
        if ($page_res = Tools::file_get_contents($file)) {
            return mb_strpos($page_res, $search);
        }
        return false;
    }
    
    public function getContent()
    {
        if(Tools::getValue('ajax')) {
            $this->dispatchAjax();
        }
        else {
            if($messageErrorFile = $this->testModificationsFiles()) {
                $this->context->smarty->assign('errorFile', $messageErrorFile);
            }

            $token = Configuration::get('HUPIRECO_TOKEN');
            
            $product_page = array(
                'active' => Configuration::get('HUPIRECO_PROD_ACTIVE'),
                'end_point' => Configuration::get('HUPIRECO_PROD_ENDPOINT'),
                'nb_products' => Configuration::get('HUPIRECO_PROD_NB'),
            );
            $shopping_cart = array(
                'active' => Configuration::get('HUPIRECO_CART_ACTIVE'),
                'end_point' => Configuration::get('HUPIRECO_CART_ENDPOINT'),
                'nb_products' => Configuration::get('HUPIRECO_CART_NB'),
            );
            $homepage = array(
                'active' => Configuration::get('HUPIRECO_HP_ACTIVE'),
                'end_point' => Configuration::get('HUPIRECO_HP_ENDPOINT'),
                'nb_products' => Configuration::get('HUPIRECO_HP_NB'),
            );
            if (Tools::isSubmit('updateConfig')) {
                Configuration::updateValue('HUPIRECO_TOKEN', Tools::getValue('hupireco_token'));
                
                if($product_page = Tools::getValue('product_page')) {
                    Configuration::updateValue('HUPIRECO_PROD_ACTIVE', $product_page['active']);
                    Configuration::updateValue('HUPIRECO_PROD_ENDPOINT', $product_page['end_point']);
                    Configuration::updateValue('HUPIRECO_PROD_NB', $product_page['nb_products']);
                }
                if($shopping_cart = Tools::getValue('shopping_cart')) {
                    Configuration::updateValue('HUPIRECO_CART_ACTIVE', $shopping_cart['active']);
                    Configuration::updateValue('HUPIRECO_CART_ENDPOINT', $shopping_cart['end_point']);
                    Configuration::updateValue('HUPIRECO_CART_NB', $shopping_cart['nb_products']);
                }
                if($homepage = Tools::getValue('homepage')) {
                    Configuration::updateValue('HUPIRECO_HP_ACTIVE', $homepage['active']);
                    Configuration::updateValue('HUPIRECO_HP_ENDPOINT', $homepage['end_point']);
                    Configuration::updateValue('HUPIRECO_HP_NB', $homepage['nb_products']);
                }
            }
            
            $this->context->controller->addCSS($this->_path.'views/css/hupirecommend.css');
            $this->context->smarty->assign(array(
                'module_dir' => $this->_path,
                'hupireco_token' => $token,
                'product_page' => $product_page,
                'shopping_cart' => $shopping_cart,
                'homepage' => $homepage,
            ));
            return $this->context->smarty->fetch($this->local_path.'views/templates/admin/configure.tpl');
        }
    }
    
    private function displayConfigField($jsonConfig = null)
    {
        $config = json_decode($jsonConfig, true);
        $default_lang = (int)Configuration::get('PS_LANG_DEFAULT');
        $languages = Language::getLanguages(false);
    
        $txt  = "<div id=\"cef_config\" class=\"col-sm-12 col-lg-6\">";
        $txt .= "  <input type=\"hidden\" name=\"config\" value=\"".htmlentities($jsonConfig)."\" />";
        $txt .= "  <div class=\"banner_up\">";
        $txt .= "      <div class=\"col-xs-offset-1 col-xs-4\"><span>Value</span></div> ";
        $txt .= "      <div class=\"col-xs-7\"><span> Option text</span></div>";
        $txt .= "    </div>";
        $txt .= "    <ul class=\"admin_selector clearfix\" id=\"selector_list\">";
    
        if($config['values']) {
            $i = 0;
            foreach ($config['values'] as $val) {
                $txt .= "      <li id=\"selector_".$i."\" class=\"selector_item\">";
                $txt .= "         <div class=\"row\">";
                $txt .= "           <div class=\"drp-drag col-xs-1\"><i class=\"icon-bars\" aria-hidden=\"true\"></i></div>";
                $txt .= "           <div class=\"drp-val col-xs-4\"><input value=\"".$val['value']."\" placeholder=\"Value\" type=\"text\"></div>";
                $txt .= "           <div class=\"drp-opt col-xs-6\">";
                foreach ($languages as $lang) {
                    if (count($languages) > 1) {
                        $txt .= "       <div class=\"translatable-field lang-".$lang['id_lang']."\"".($lang['id_lang'] != $default_lang?" style=\"display:none\"":"").">";
                        $txt .= "           <div class=\"col-xs-9\">";
                    }
                    $txt .= "                       <input value=\"".(isset($val['label'][$lang['id_lang']])?$val['label'][$lang['id_lang']]:'')."\" placeholder=\"Label\" type=\"text\">";
                    if (count($languages) > 1) {
                        $txt .= "           </div>";
                        $txt .= "           <div class=\"col-xs-2\">";
                        $txt .= "               <button type=\"button\" class=\"btn btn-default dropdown-toggle\" tabindex=\"-1\" data-toggle=\"dropdown\">".$lang['iso_code']."<i class=\"icon-caret-down\"></i></button>";
                        $txt .= "               <ul class=\"dropdown-menu\">";
                        foreach ($languages as $lng) {
                            $txt .= "               <li><a href=\"javascript:hideOtherLanguage(".$lng['id_lang'].");\" tabindex=\"-1\">".$lng['name']."</a></li>";
                        }
                        $txt .= "               </ul>";
                        $txt .= "           </div>";
                        $txt .= "       </div>";
                    }
                }
    
                $txt .= "           </div>";
                $txt .= "           <div class=\"drp-rmv col-xs-1\"><button type=\"button\" class=\"btn btn-small\" title=\"Remove\"><i class=\"icon-trash\" aria-hidden=\"true\"></i></button></div>";
                $txt .= "         </div>";
                $txt .= "      </li>";
                $i++;
            }
        }
        else {
            $txt .= "      <li id=\"selector_0\" class=\"selector_item\">";
            $txt .= "         <div class=\"row\">";
            $txt .= "           <div class=\"drp-drag col-xs-1\"><i class=\"icon-bars\" aria-hidden=\"true\"></i></div>";
            $txt .= "           <div class=\"drp-val col-xs-4\"><input value=\"\" placeholder=\"Value\" type=\"text\"></div>";
            $txt .= "           <div class=\"drp-opt col-xs-6\">";
            foreach ($languages as $lang) {
                if (count($languages) > 1) {
                    $txt .= "       <div class=\"translatable-field lang-".$lang['id_lang']."\"".($lang['id_lang'] != $default_lang?" style=\"display:none\"":"").">";
                    $txt .= "           <div class=\"col-xs-9\">";
                }
                $txt .= "                       <input value=\"\" placeholder=\"Label\" type=\"text\">";
                if (count($languages) > 1) {
                    $txt .= "           </div>";
                    $txt .= "           <div class=\"col-xs-2\">";
                    $txt .= "               <button type=\"button\" class=\"btn btn-default dropdown-toggle\" tabindex=\"-1\" data-toggle=\"dropdown\">".$lang['iso_code']."<i class=\"icon-caret-down\"></i></button>";
                    $txt .= "               <ul class=\"dropdown-menu\">";
                    foreach ($languages as $lng) {
                        $txt .= "               <li><a href=\"javascript:hideOtherLanguage(".$lng['id_lang'].");\" tabindex=\"-1\">".$lng['name']."</a></li>";
                    }
                    $txt .= "               </ul>";
                    $txt .= "           </div>";
                    $txt .= "       </div>";
                }
            }
    
            $txt .= "           </div>";
            $txt .= "           <div class=\"drp-rmv col-xs-1\"><button type=\"button\" class=\"btn btn-small\" title=\"Remove\"><i class=\"icon-trash\" aria-hidden=\"true\"></i></button></div>";
            $txt .= "         </div>";
            $txt .= "      </li>";
        }
        $txt .= "    </ul>";
        $txt .= "    <div class=\"banner_down\">";
        $txt .= "       <div class=\"col-xs-6\"><label for=\"selector_multi\"><span>Multiple:</span> <input id=\"selector_multi\" type=\"checkbox\"".((isset($config['multiple']) && $config['multiple'])?" checked=\"checked\"":"")." /></label></div>";
        $txt .= "       <div class=\"col-xs-6\"><button type=\"button\" id=\"selector_ad-optn\" class=\"btn btn-small\">Add option <i class=\"icon-plus\" aria-hidden=\"true\"></i></button></div>";
        $txt .= "    </div>";
        $txt .= "</div>";
    
        return $txt;
    }

    public function hookDisplayFooter()
    {
		return '<script type="text/javascript">Hupi.addProductRecommendationClick();</script>';
    }    
    
    protected function getProducts($endpoint, $nbProducts = 4, $product = null) {
        $id_lang = $this->context->language->id;
        
        if($this->context->cookie->__get('pk_id') && Configuration::get('HUPIRECO_TOKEN')) {
            $arrayPkId = explode('.', $this->context->cookie->__get('pk_id'));
            $visitor_id = array_shift($arrayPkId);
            $filters = array("visitor_id" => $visitor_id);
            if($product) {
                $filters['id_demande'] = $product;
            }
            if($products = $this->context->cart->getProducts(true)) {
                $productList = array();
                foreach ($products as $product) {
                    $productList[] = (int)$product['id_product'];
                }
                if($productList) {
                    $filters['basket'] = json_encode($productList);
                }
            }
                
            $data = array("client" => Configuration::get('HUPI_ACCOUNT_ID'), "render_type" => "cursor", "filters" => json_encode($filters));
            $data_string = json_encode($data);
            
            $ch = curl_init("https://api.dataretriever.hupi.io/private/".Configuration::get('HUPI_ACCOUNT_ID')."/".$endpoint);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                'Accept-Version: v1',
                'X-API-Token: '.Configuration::get('HUPIRECO_TOKEN'),
                'Content-Length: ' . strlen($data_string)
            ));
             
            $result = curl_exec($ch);
            curl_close($ch);
            
            $jsonArray = json_decode($result, true);
            if($jsonArray['data'] && count($jsonArray['data'])) {
                $idProducts = array_values(array_map('current',$jsonArray['data']));
            
                $sql = 'SELECT p.*, product_shop.*, stock.`out_of_stock` out_of_stock, pl.`description`, pl.`description_short`,
    						pl.`link_rewrite`, pl.`meta_description`, pl.`meta_keywords`, pl.`meta_title`, pl.`name`,
    						p.`ean13`, p.`upc`, MAX(image_shop.`id_image`) id_image, il.`legend`
    					FROM `'._DB_PREFIX_.'product` p
    					LEFT JOIN `'._DB_PREFIX_.'product_lang` pl ON (
    						p.`id_product` = pl.`id_product`
    						AND pl.`id_lang` = '.(int)$id_lang.Shop::addSqlRestrictionOnLang('pl').'
    					)
    					'.Shop::addSqlAssociation('product', 'p').'
    					LEFT JOIN `'._DB_PREFIX_.'image` i ON (i.`id_product` = p.`id_product`)'.
            					Shop::addSqlAssociation('image', 'i', false, 'image_shop.cover=1').'
    					LEFT JOIN `'._DB_PREFIX_.'image_lang` il ON (i.`id_image` = il.`id_image` AND il.`id_lang` = '.(int)$id_lang.')
    					'.Product::sqlStock('p', 0).'
    					WHERE 1
    						AND p.`id_product` IN ('.implode(',', $idProducts).')
    						AND product_shop.`visibility` IN ("both", "catalog")
    						AND product_shop.`active` = 1
    					GROUP BY product_shop.id_product
    					ORDER BY FIELD(p.`id_product`, '.implode(',', $idProducts).')
					    LIMIT 0, '. (int)$nbProducts;
                
                $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
				return Product::getProductsProperties($id_lang, $result);
            }
        }
        
        return array();
    }
    
    public function hookProductTab($params)
    {
        if(Configuration::get('HUPIRECO_PROD_ACTIVE') != '1' || !Configuration::get('HUPIRECO_PROD_ENDPOINT')) {
            return;
        }
        
        $nbProd = (int)Configuration::get('HUPIRECO_PROD_NB');
        if($nbProd == 0) {
            $nbProd = null;
        }
        
        $id_product = Tools::getValue('id_product');
        if($products = $this->getProducts(Configuration::get('HUPIRECO_PROD_ENDPOINT'), $nbProd, $id_product)) {
            $this->smarty->assign('products', $products);
            
            if (version_compare(_PS_VERSION_, '1.6', '>=') && !(bool)Configuration::get('HUPIRECO_PROD_HASTAB')) {
                return $this->display(__FILE__, 'tab-name.tpl');
            } else {
                return $this->display(__FILE__, 'tab-name-1.5.tpl');
            }
        }
    }
    
    public function hookProductTabContent($params)
    {
        if(Configuration::get('HUPIRECO_PROD_ACTIVE') != '1' || !Configuration::get('HUPIRECO_PROD_ENDPOINT')) {
            return;
        }
        $nbProd = (int)Configuration::get('HUPIRECO_PROD_NB');
        if($nbProd == 0) {
            $nbProd = null;
        }
        
        $id_product = Tools::getValue('id_product');
        if($products = $this->getProducts(Configuration::get('HUPIRECO_PROD_ENDPOINT'), $nbProd, $id_product)) {
            $this->smarty->assign('products', $products);
            
            if (version_compare(_PS_VERSION_, '1.6', '>=') && !(bool)Configuration::get('HUPIRECO_PROD_HASTAB')) {
                return $this->display(__FILE__, 'tab-content.tpl');
            } else {
                return $this->display(__FILE__, 'tab-content-1.5.tpl');
            }
        }
    }
    
    public function hookDisplayHomeTab($params)
    {
        if(Configuration::get('HUPIRECO_HP_ACTIVE') != '1' || !Configuration::get('HUPIRECO_HP_ENDPOINT')) {
            return;
        }
        
        $nbProd = (int)Configuration::get('HUPIRECO_HP_NB');
        if($nbProd == 0) {
            $nbProd = null;
        }
        
        if($products = $this->getProducts(Configuration::get('HUPIRECO_HP_ENDPOINT'), $nbProd)) {
            $this->smarty->assign('products', $products);
            
            return $this->display(__FILE__, 'home-tab-name.tpl');
        }
    }
    
    public function hookDisplayHome($params)
    {
        if(Configuration::get('HUPIRECO_HP_ACTIVE') != '1' || !Configuration::get('HUPIRECO_HP_ENDPOINT')) {
            return;
        }
        $nbProd = (int)Configuration::get('HUPIRECO_HP_NB');
        if($nbProd == 0) {
            $nbProd = null;
        }
        
        if($products = $this->getProducts(Configuration::get('HUPIRECO_HP_ENDPOINT'), $nbProd)) {
            $this->smarty->assign('products', $products);
            
            return $this->display(__FILE__, 'home-tab-content.tpl');
        }
    }
    
    public function hookDisplayHomeTabContent($params)
    {
        return $this->hookDisplayHome($params);
    }
    
    public function hookdisplayHupiRecommendations($params)
    {
        if(isset($params['productId']) && $params['productId']) {
            $id_product = $params['productId'];
        } else { 
            return ;
        }
        
        if(isset($params['nbItems']) && $params['nbItems']) {
            $nbProd = (int)$params['nbItems'];
        } else {
            $nbProd = (int)Configuration::get('HUPIRECO_PROD_NB');
        }
        if($nbProd == 0) {
            $nbProd = null;
        }
        
        if($products = $this->getProducts(Configuration::get('HUPIRECO_PROD_ENDPOINT'), $nbProd, $id_product)) {
            $this->smarty->assign('products', $products);
        
            return $this->display(__FILE__, 'display-recommendation.tpl');
        }
        return ;
    }
    
    public function hookDisplayShoppingCart($params)
    {
        if(Configuration::get('HUPIRECO_CART_ACTIVE') != '1' || !Configuration::get('HUPIRECO_CART_ENDPOINT')) {
            return; 
        }
        
        $nbProd = (int)Configuration::get('HUPIRECO_CART_NB');
        if($nbProd == 0) {
            $nbProd = null;
        }
        
        
        if($products = $this->getProducts(Configuration::get('HUPIRECO_CART_ENDPOINT'), $nbProd)) {
            $this->context->controller->addCss(_THEME_CSS_DIR_.'product_list.css', 'all');
            $this->smarty->assign('products', $products);
    
            return $this->display(__FILE__, 'shopping-cart.tpl');
        }
        
    }
}

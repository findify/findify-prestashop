<?php
/**
 * AM FINDIFY
 *
 * @author    LATOUTFRANCAIS | Arnaud Merigeau <contact@arnaud-merigeau.fr> - https://www.arnaud-merigeau.fr
 * @copyright Arnaud Merigeau 2020 - https://www.arnaud-merigeau.fr
 * @license   Commercial
 *
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

class Findify extends Module
{   
    public function __construct()
    {
        $this->name = 'findify';
        $this->tab = 'front_office_features';
        $this->version = '1.0.6';
        $this->author = 'LATOUTFRANCAIS';
        $this->need_instance = 0;
        $this->context = Context::getContext();
        $this->bootstrap = true;
        parent::__construct();

        $this->displayName = $this->l('Findify | Search & Personalization');
        $this->description = $this->l('Accelerate your sales by putting the right products in front of the right customers at the right time.');
        $this->confirmUninstall = $this->l('Are you sure you want to uninstall this module ?');
    }

    public function install()
    {
        if (Shop::isFeatureActive()) {
            Shop::setContext(Shop::CONTEXT_ALL);
        }

        return parent::install()
            && $this->registerHook('header')
            && $this->registerHook('displayFooterProduct')
            && $this->registerHook('displayShoppingCart')
            && Configuration::updateValue('amf_fname', null)
            && Configuration::updateValue('amf_lname', null)
            && Configuration::updateValue('amf_email', Configuration::get('PS_SHOP_EMAIL'))
            && Configuration::updateValue('amf_sendemail', '1')
            && Configuration::updateValue('amf_ag', null)
            && Configuration::updateValue('amf_fg', null);
    }

    public function uninstall()
    {
        return parent::uninstall()
            && Configuration::deleteByName('amf_fname')
            && Configuration::deleteByName('amf_lname')
            && Configuration::deleteByName('amf_email')
            && Configuration::deleteByName('amf_sendemail')
            && Configuration::deleteByName('amf_ag')
            && Configuration::deleteByName('amf_fg');
    }

    public function uninstallFiles()
    {
        define('MOD_DIR', dirname(__FILE__));
        define('THEME_DIR', _THEME_DIR_);
        $theme_path = realpath(MOD_DIR . '/../../../' . THEME_DIR);

        // uninstall cart-detailed.tpl
        $file1 = 'cart-detailed.tpl';
        unlink($theme_path . '/templates/checkout/_partials/' . $file1);
        rename(MOD_DIR . '/views/templates/backup/' . $file1, $theme_path . '/templates/checkout/_partials/' . $file1);
        
        // uninstall order-confirmation-table.tpl
        $file2 = 'order-confirmation-table.tpl';
        unlink($theme_path . '/templates/checkout/_partials/' . $file2);
        rename(MOD_DIR . '/views/templates/backup/' . $file2, $theme_path . '/templates/checkout/_partials/' . $file2);
    }

    public static function convertIsoArrayToIdArray($array)
    {
        if (!is_array($array) || empty($array)) {
            return array();
        }
        $converted = array();
        foreach (Language::getLanguages(false) as $language) {
            $key = key_exists($language['iso_code'], $array) ? $language['iso_code'] : 'en';
            $converted[$language['id_lang']] = $array[$key];
        }
        return $converted;
    }

    public function getContent()
    {
        $this->context->controller->addCSS(($this->_path).'views/css/select2.min.css', 'all');
        $this->context->controller->addCSS(($this->_path).'views/css/admin.findify.css', 'all');
        $this->context->controller->addJS($this->_path . 'views/js/select2.full.min.js');
        $this->context->controller->addJS($this->_path . 'views/js/admin.findify.js');

        define('MOD_DIR', dirname(__FILE__));
        define('THEME_DIR', _THEME_DIR_);
        define('THEME_PATH', realpath(MOD_DIR . '/../../../' . THEME_DIR));
        define('BKP_DIR', '/views/templates/backup/');
        define('INSTALL_DIR', '/views/templates/install/');

        define('FILE1', 'cart-detailed.tpl');
        define('FILE2', 'order-confirmation-table.tpl');
        define('FILE3', 'product-list.tpl');
        define('FILE4', 'category-product-list.tpl');
        define('FILE5', 'category.tpl');
        define('FILE6', 'layout-both-columns.tpl');
        define('FILE7', 'layout-left-column.tpl');

        $output = null;

        if (Tools::isSubmit('submit' . $this->name)) {
            $amf_fname = Tools::getValue('amf_fname');
            $amf_lname = Tools::getValue('amf_lname');
            $amf_email = Tools::getValue('amf_email');
            $amf_sendemail = Tools::getValue('amf_sendemail');
            $amf_ag = Tools::getValue('amf_ag');
            $amf_fg = Tools::getValue('amf_fg');
            if (!Validate::isCustomerName($amf_fname))  {
                $output .= $this->displayError($this->l('First name is invalid.'));
            } elseif (!Validate::isCustomerName($amf_lname))  {
                $output .= $this->displayError($this->l('Last name is invalid.'));
            } elseif (!Validate::isEmail($amf_email))  {
                $output .= $this->displayError($this->l('Email address is invalid.'));
            } elseif ($amf_fname == '')  {
                $output .= $this->displayError($this->l('First name is required.'));
            } elseif ($amf_lname == '')  {
                $output .= $this->displayError($this->l('Last name is required.'));
            } elseif ($amf_email == '')  {
                $output .= $this->displayError($this->l('Email address is required.'));
            } else {
                Configuration::updateValue('amf_fname', $amf_fname);
                Configuration::updateValue('amf_lname', $amf_lname);
                Configuration::updateValue('amf_email', $amf_email);
                Configuration::updateValue('amf_sendemail', $amf_sendemail);
                if (count($amf_ag) > 1) {
                    $amf_ag = implode(',', $amf_ag);
                } else {
                    $amf_ag = $amf_ag[0];
                }
                Configuration::updateValue('amf_ag', $amf_ag);
                if (count($amf_fg) > 1) {
                    $amf_fg = implode(',', $amf_fg);
                } else {
                    $amf_fg = $amf_fg[0];
                }
                Configuration::updateValue('amf_fg', $amf_fg);
                $languages = Language::getLanguages(false);
                foreach ($languages as $lang) {
                    $amf_script = Tools::getValue('amf_script_'.$lang['id_lang']);
                    Configuration::updateValue('amf_script_'.$lang['id_lang'], $amf_script);
                }
                $output .= $this->displayConfirmation($this->l('Update successful !'));
            }
        } else if (Tools::isSubmit('submit_type')) {
            $type = Tools::getValue('type');
            $file = Tools::getValue('file');
            $this->installFiles($type, $file);
        }

        return $output . $this->displayBranding() . $this->displayForm() . $this->displayCron() /*. $this->stayInTouch()*/;
    }

    public function installFiles($type, $file)
    {
        if($type && $file){
            if($type == 1 || $type == 2){
                rename(THEME_PATH . '/templates/checkout/_partials/' . $file, MOD_DIR . BKP_DIR . $file);
                copy(MOD_DIR . INSTALL_DIR . $file, THEME_PATH . '/templates/checkout/_partials/' . $file);
            }else if($type == 3 || $type == 4 || $type == 5){
                if($type == 3 || $type == 5)
                    rename(THEME_PATH . '/templates/catalog/listing/' . $file, MOD_DIR . BKP_DIR . $file);
                copy(MOD_DIR . INSTALL_DIR . $file, THEME_PATH . '/templates/catalog/listing/' . $file);
            }else if($type == 6 || $type == 7){
                rename(THEME_PATH . '/templates/layouts/' . $file, MOD_DIR . BKP_DIR . $file);
                copy(MOD_DIR . INSTALL_DIR . $file, THEME_PATH . '/templates/layouts/' . $file);
            }
        }      
    }

    public function displayForm()
    {
        $id_lang = $this->context->language->id;
        $output = null;

        // Check options selected
        $output .= '
            <style>
                select[name*=amf_ag]{min-width:280px!important;}
                select[name*=amf_fg]{min-width:280px!important;}
            </style>
            <script>
                var values="'.Configuration::get('amf_ag').'";
                $.each(values.split(","), function(i,e){
                    $("select[name*=amf_ag] option[value=\'" + e + "\']").prop("selected", true);
                });
                var values="'.Configuration::get('amf_fg').'";
                $.each(values.split(","), function(i,e){
                    $("select[name*=amf_fg] option[value=\'" + e + "\']").prop("selected", true);
                });
            </script>
        ';

        // Get attribute groups
        $ag_list = array();
        if (Configuration::get('PS_COMBINATION_FEATURE_ACTIVE')) {
            $ag = new AttributeGroup();
            $ag_list = array();
            foreach ($ag->getAttributesGroups($id_lang) as $group) {
                $ag_list[] = array(
                    'id' => (int)$group['id_attribute_group'],
                    'name' => $group['name']
                );
            }
        }

        // Get feature groups
        $fg_list = array();
        if (Configuration::get('PS_FEATURE_FEATURE_ACTIVE')) {
            $fg = new Feature();
            foreach ($fg->getFeatures($id_lang) as $group) {
                $fg_list[] = array(
                    'id' => (int)$group['id_feature'],
                    'name' => $group['name']
                );
            }
        }

        // Form
        $fields_form = array();
        $default_lang = (int)Configuration::get('PS_LANG_DEFAULT');
        $fields_form[0]['form'] = array(
            'legend' => array(
                'title' => $this->l('Product feed options')
            ),
            'input' => array(
                array(
                    'type' => 'text',
                    'label' => $this->l('First Name').'*',
                    'name' => 'amf_fname',
                    'desc' => $this->l('Required.')
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Last Name').'*',
                    'name' => 'amf_lname',
                    'desc' => $this->l('Required.')
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Email').'*',
                    'name' => 'amf_email',
                    'desc' => $this->l('Required.').' '.$this->l('Email address sent to Findify with your product feed.')
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Send email'),
                    'name' => 'amf_sendemail',
                    'is_bool' => true,
                    'desc' => $this->l('Send product feed by mail to yourfriends@findify.io when generated.'),
                    'values' => array(
                        array(
                            'id' => 'active_on',
                            'value' => 1,
                            'label' => $this->l('Enabled')
                        ),
                        array(
                            'id' => 'active_off',
                            'value' => 0,
                            'label' => $this->l('Disabled')
                        )
                    )
                ),
                array(
                    'type' => 'select',
                    'label' => $this->l('Attribute groups to export'),
                    'name' => 'amf_ag[]',
                    'desc' => $this->l('Type or click in the field to choose attribute groups to export.'),
                    'multiple' => true,
                    'class' => 'fixed-width-xxl select2',
                    'options' => array(
                        'query' => $ag_list,
                        'id' => 'id',
                        'name' => 'name'
                    )
                ),
                array(
                    'type' => 'select',
                    'label' => $this->l('Feature groups to export'),
                    'name' => 'amf_fg[]',
                    'desc' => $this->l('Type or click in the field to choose feature groups to export.'),
                    'multiple' => true,
                    'class' => 'fixed-width-xxl select2',
                    'options' => array(
                        'query' => $fg_list,
                        'id' => 'id',
                        'name' => 'name'
                    )
                ),
                array(
                    'type' => 'text',
                    'lang' => true,
                    'label' => $this->l('Script url').'*',
                    'name' => 'amf_script',
                    'desc' => $this->l('Required.').' '.$this->l('Script url given by Findify.'),
                ),
            ),
            'submit' => array(
                'title' => $this->l('Save'),
                'class' => 'btn btn-default'
            )
        );

        $helper = new HelperForm();
        $helper->module = $this;
        $helper->name_controller = $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = AdminController::$currentIndex . '&configure=' . $this->name;
        $helper->default_form_language = $default_lang;
        $helper->allow_employee_form_lang = $default_lang;
        $helper->title = $this->displayName;
        $helper->show_toolbar = true;
        $helper->toolbar_scroll = true;
        $helper->submit_action = 'submit' . $this->name;
        $helper->toolbar_btn = array(
            'save' =>
                array(
                    'desc' => $this->l('Save'),
                    'href' => AdminController::$currentIndex . '&configure=' . $this->name . '&save' . $this->name .
                        '&token=' . Tools::getAdminTokenLite('AdminModules'),
                ),
            'back' => array(
                'href' => AdminController::$currentIndex . '&token=' . Tools::getAdminTokenLite('AdminModules'),
                'desc' => $this->l('Back to list')
            )
        );

        $helper->tpl_vars = array(
            'fields_value' => $this->getConfigFieldsValues(),
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id
        );

        return $helper->generateForm($fields_form) . $output;
    }

    public function getConfigFieldsValues()
    {
        $languages = Language::getLanguages(false);
        $fields = array();

        $fields['amf_fname'] = Tools::getValue('amf_fname', Configuration::get('amf_fname'));
        $fields['amf_lname'] = Tools::getValue('amf_lname', Configuration::get('amf_lname'));
        $fields['amf_email'] = Tools::getValue('amf_email', Configuration::get('amf_email'));
        $fields['amf_sendemail'] = Tools::getValue('amf_sendemail', Configuration::get('amf_sendemail'));
        $fields['amf_ag[]'] = Tools::getValue('amf_ag[]', Configuration::get('amf_ag[]'));
        $fields['amf_fg[]'] = Tools::getValue('amf_fg[]', Configuration::get('amf_fg[]'));

        foreach ($languages as $lang) {
            $fields['amf_script'][$lang['id_lang']] = Tools::getValue('amf_script_'.$lang['id_lang'], Configuration::get('amf_script_'.$lang['id_lang']));
        }

        return $fields;
    }

    public function displayCron()
    {
        // prestashop version
        if (version_compare(_PS_VERSION_, '1.7.0.0', '>=')) {
            $store_url = $this->context->link->getBaseLink();
        }else{
            $store_url = Tools::getHttpHost(true).__PS_BASE_URI__;
        }        

        // base url
        $useSSL = (Configuration::get('PS_SSL_ENABLED') || Tools::usingSecureMode()) ? true : false;

        // display logs
        $logsURLs = array();
        define('DIR_LOG', $_SERVER['DOCUMENT_ROOT']._MODULE_DIR_.'findify/log/');
        $logs = scandir(DIR_LOG, 1);
        if($logs){
            foreach ($logs as $k => $log) {
                if(strpos($log, 'amf-log') !== false){
                    $logsURLs[$k]['url'] = $store_url . 'modules/findify/log/' . $log;
                    $logsURLs[$k]['filename'] = $log;
                }
            }
        }

        // display feed
        $amf_feed_url = array();
        define('URL_EXPORT', $store_url.'modules/findify/export/');
        define('DIR_EXPORT', $_SERVER['DOCUMENT_ROOT']._MODULE_DIR_.'findify/export/');
        $feeds = scandir(DIR_EXPORT, 1);
        if($feeds){
            foreach ($feeds as $k => $feed) {
                if(strpos($feed, 'findify-product-feed') !== false){
                    $amf_feed_url[] = URL_EXPORT.$feed;
                }
            }
        }

        // test files backup
        if (file_exists(MOD_DIR . BKP_DIR . FILE1)) {   
            $type1 = 1;
        } else {
            $type1 = null;
        }
        if (file_exists(MOD_DIR . BKP_DIR . FILE2)) {   
            $type2 = 1;
        } else {
            $type2 = null;
        }
        if (file_exists(MOD_DIR . BKP_DIR . FILE3)) {   
            $type3 = 1;
        } else {
            $type3 = null;
        }
        if (file_exists(THEME_PATH . '/templates/catalog/listing/' . FILE4)) {   
            $type4 = 1;
        } else {
            $type4 = null;
        }
        if (file_exists(MOD_DIR . BKP_DIR . FILE5)) {   
            $type5 = 1;
        } else {
            $type5 = null;
        }
        if (file_exists(MOD_DIR . BKP_DIR . FILE6)) {   
            $type6 = 1;
        } else {
            $type6 = null;
        }
        if (file_exists(MOD_DIR . BKP_DIR . FILE7)) {   
            $type7 = 1;
        } else {
            $type7 = null;
        }

        $this->context->smarty->assign(array(
            'amf_feed_url' => $amf_feed_url,
            'amf_cron' => $store_url . 'modules/findify/cron.php?token=' . Tools::substr(Tools::encrypt('findify/cron'), 0, 10) . '&id_shop=' . $this->context->shop->id,
            'prestashop_ssl' => Configuration::get('PS_SSL_ENABLED'),
            'shop' => $this->context->shop,
            'logs' => $logsURLs,
            'action' => AdminController::$currentIndex.'&configure='.$this->name.'&integrate&token='.Tools::getAdminTokenLite('AdminModules'),
            'type1' => $type1,
            'type2' => $type2,
            'type3' => $type3,
            'type4' => $type4,
            'type5' => $type5,
            'type6' => $type6,
            'type7' => $type7
        ));

        return $this->display(__FILE__, 'views/templates/admin/configuration.tpl');   
    }

    /**
     * Delete old log files
     * @param dir/max_age
     * @return list
     */
    public function deleteOlderThan($dir, $max_age)
    {
        $list = array();
        $limit = time() - $max_age;
        $dir = realpath($dir);
        if (!is_dir($dir)) {
            return;
        }
        $dh = opendir($dir);
        if ($dh === false) {
            return;
        }
        while (($file = readdir($dh)) !== false) {
            $file = $dir . '/' . $file;
            if (!is_file($file)) {
            continue;
            }
             
            if (filemtime($file) < $limit) {
            $list[] = $file;
            unlink($file);
            }
            
        }
        closedir($dh);
        return $list;
    }

    /**
     * Debug function
     * @param var/array
     * @return -
     */
    public function debug($var)
    {
        echo '<pre>';
        print_r($var);
        echo '</pre>';
    }

    /**
     * Debug and die function
     * @param var/array
     * @return -
     */
    public function debugdie($var)
    {
        echo '<pre>';
        print_r($var);
        echo '</pre>';
        die;
    }

    /**
     * Get attribute group name for a given language.
     * @param int $idLang Language id
     * @return array Attribute group name
     */
    public static function getAttributeGroupName($id_attribute_group, $id_lang)
    {
        if (!Combination::isFeatureActive()) {
            return array();
        }

        return Db::getInstance()->executeS('
            SELECT name
            FROM ' . _DB_PREFIX_ . 'attribute_group_lang
            WHERE id_attribute_group = ' . $id_attribute_group . ' AND id_lang = ' . $id_lang . '
        ');
    }

    /**
     * Get feature group name for a given language.
     * @param int $idLang Language id
     * @return array Feature group name
     */
    public static function getFeatureGroupName($id_feature, $id_lang)
    {
        if (!Feature::isFeatureActive()) {
            return array();
        }

        return Db::getInstance()->executeS('
            SELECT name
            FROM ' . _DB_PREFIX_ . 'feature_lang
            WHERE id_feature = ' . $id_feature . ' AND id_lang = ' . $id_lang . '
        ');
    }

    /**
     * Clean string.
     * @param $str
     * @return $str
     */
    public function cleanStr($str)
    {
        // Strip HTML Tags
        $str = strip_tags($str);
        // Clean up things like &amp;
        $str = html_entity_decode($str);
        // Strip out any url-encoded stuff
        $str = urldecode($str);
        // Replace non-AlNum characters with space
        $str = preg_replace('/[^A-Za-z0-9]/', ' ', $str);
        // Replace Multiple spaces with single space
        $str = preg_replace('/ +/', ' ', $str);
        // Trim the string of leading/trailing space
        $str = trim($str);

        return $str;
    }

    /**
     * Remove \n.
     * @param $str
     * @return $str
     */
    public function removeNl($str)
    {
        // Replace new line with space
        $str = trim(preg_replace('/\s\s+/', ' ', $str));

        return $str;
    }

    /**
     * Export product feed
     * @param -
     * @return -
     */
    public function exportFeed($language)
    {
        // Vars
        define('DEV', 1);
        if (Configuration::get('PS_SSL_ENABLED')){
            $protocol = 'https://';
        } else {
            $protocol = 'http://';
        }
        $sep = ',';
        $log = fopen(LOG_FILE, 'a+') or die($this->l('Permission error on log dir'));
        // delete old export file
        unlink(DIR_EXPORT.'findify-product-feed-'.$language['iso_code'].'.csv');
        // create new export file
        $export = DIR_EXPORT.'findify-product-feed-'.$language['iso_code'].'.csv';
        $shop_name = Configuration::get('PS_SHOP_NAME');
        if (Configuration::get('amf_email') != ''){
            $from = Configuration::get('amf_email');
            $to = Configuration::get('amf_email');
        }else{
            $from = Configuration::get('PS_SHOP_EMAIL');
            $to = Configuration::get('PS_SHOP_EMAIL');
        }
        if (version_compare(_PS_VERSION_, '1.7.0.0', '>=')) {
            $store_url = $this->context->link->getBaseLink();
        }else{
            $store_url = Tools::getHttpHost(true).__PS_BASE_URI__;
        }   
        $context = Context::getContext();
        $id_lang = $language['id_lang'];
        $iso = $language['iso_code'];

        try {

            if (file_exists($export))
                unlink($export);

            echo '-----------------------------------------------------<br>'.date("Y-m-d H:i:s").' '.$this->l('Product feed export starts : ').$iso.'<br>';
            fputs($log, '-----------------------------------------------------'.PHP_EOL.date("Y-m-d H:i:s").' Product feed export starts : '.$iso.PHP_EOL);

            // Get data
            $db = Db::getInstance();
            $products = $db->connect()->query('
                SELECT *
                FROM '._DB_PREFIX_.'product p
                LEFT JOIN '._DB_PREFIX_.'product_lang AS pl ON p.id_product = pl.id_product
                WHERE p.active = 1 AND pl.id_lang = '.$id_lang.'
                GROUP BY p.id_product
            ');

            if ( $fp = fopen($export, 'w+') ) { 

                // Utf-8
                // fputs($fp, $bom =( chr(0xEF) . chr(0xBB) . chr(0xBF) ));

                // Tip ! fields doc here : https://developers.findify.io/docs/feed-generation-manual-csv
                $header = array(
                    'id',
                    'item_group_id',
                    'title',
                    'description',
                    'price',
                    'image_url',
                    'product_url',
                    'category',
                    'thumbnail_url',
                    'availability',
                    'created_at',
                    'sku',
                    'brand',
                    'seller',
                    'quantity',
                    'on_sale',
                    'new',
                    'name_ar',
                    'sticker_new_edition'
                );

                $h = count($header);

                // Add currencies labels to export prices for each currency
                $currencies = new Currency();
                foreach ($currencies->getCurrencies() as $currency) {
                    $h++;
                    $currency_label = 'price_'.Tools::strtolower($currency['iso_code']);
                    array_push($header, $currency_label);
                }

                // Add currencies labels to export sale prices for each currency
                $currencies = new Currency();
                foreach ($currencies->getCurrencies() as $currency) {
                    $h++;
                    $currency_label = 'sale_price_'.Tools::strtolower($currency['iso_code']);
                    array_push($header, $currency_label);
                }

                // Add attribute groups labels if selected
                $labels_ag = explode(',', Configuration::get('amf_ag'));
                foreach ($labels_ag as $label_ag) {
                    $h++;
                    $attribute_group_label = $this->getAttributeGroupName($label_ag, 1); // labels in english only
                    $attribute_group_label = Tools::strtolower('attribute_'.$attribute_group_label[0]['name']);
                    $attribute_group_label = str_replace(' ', '_', $attribute_group_label);
                    array_push($header, $attribute_group_label);
                }

                // Add feature groups labels if selected
                $labels_fg = explode(',', Configuration::get('amf_fg'));
                foreach ($labels_fg as $label_fg) {
                    $h++;
                    $feature_label = $this->getFeatureGroupName($label_fg, 1); // labels in english only
                    $feature_label = Tools::strtolower('feature_'.$feature_label[0]['name']);
                    $feature_label = str_replace(' ', '_', $feature_label);
                    array_push($header, $feature_label);
                }

                if(DEV){
                    echo "<pre>HEADER<br>";
                    print_r($header);
                    echo "</pre><br><br>";
                }

                fputcsv($fp, $header, $sep);
                fseek($fp, -1, SEEK_CUR); 
                // fwrite($fp, "\r\n");

            }

            if($products->rowCount() > 0){
                while ($p = $products->fetchObject()) {

                    if ( $fp = fopen($export, 'a+') ) {

                        // UTF8
                        // fputs($fp, $bom =( chr(0xEF) . chr(0xBB) . chr(0xBF) ));
                        
                        // Declare product class
                        $product = new Product((int)$p->id_product, false, $id_lang);

                        // --------------------------------------------------------------------
                        // If product get variants
                        // --------------------------------------------------------------------
                        if ($variants = $product->getProductAttributesIds($p->id_product)) {
                            
                            foreach ($variants as $variant) {
                                
                                // Declare produit if attribute
                                $v_id = $variant['id_product_attribute'];

                                // Get description
                                $p->description_short = $this->cleanStr($p->description_short);

                                // Get price tax incl
                                $v_price_tax_incl = $product->getPriceStatic($p->id_product, true, $v_id, 6, null, false, false);

                                // Get url
                                $link = new Link();
                                $p->product_url = $link->getProductLink($p->id_product, null, null, null, $id_lang);

                                // Get image url & thumbnail_url
                                if ($v_has_img = $product->getCombinationImageById($v_id, $id_lang)){
                                    $v_image_url = $protocol . $link->getImageLink(isset($p->link_rewrite) ? $p->link_rewrite : $p->name, (int)$v_has_img['id_image'], 'findify-image');
                                    $v_thumbnail_url = $protocol . $link->getImageLink(isset($p->link_rewrite) ? $p->link_rewrite : $p->name, (int)$v_has_img['id_image'], 'findify-thumbnail');
                                }else{
                                    $product_img = $product->getCover($p->id_product);
                                    $v_image_url = $protocol . $link->getImageLink(isset($p->link_rewrite) ? $p->link_rewrite : $p->name, (int)$product_img['id_image'], 'findify-image');
                                    $v_thumbnail_url = $protocol . $link->getImageLink(isset($p->link_rewrite) ? $p->link_rewrite : $p->name, (int)$product_img['id_image'], 'findify-thumbnail');
                                }                                

                                // Get categories
                                $p->category = '';
                                $product_categories = $product->getProductCategoriesFull($p->id_product, $id_lang);
                                $c=0;
                                foreach ($product_categories as $product_category) {
                                    if ($c > 0)
                                        $p->category .= ' > ';
                                    $p->category .= $product_category['name'];
                                    $c++;
                                }

                                // Get availability
                                $p->available_quantity = $product->getQuantity($p->id_product, $v_id);
                                $p->availability = ($p->available_quantity < 1) ? $this->l('out of stock') : $this->l('in stock');

                                // Get manufacturer name
                                $manufacturer = new Manufacturer();
                                $p->brand = $manufacturer->getNameById($p->id_manufacturer);

                                // Get sale price
                                $p->sale_price = $product->getPriceStatic($p->id_product, true, $v_id, 6, null, false, true);

                                // Get date
                                $p->created_at = date(DATE_ISO8601, strtotime($p->date_add));

                                // On sale
                                $p->on_sale = $p->on_sale;

                                // New
                                $p->new = $product->isNew();
                                if(!$p->new)
                                    $p->new = 0;

                                // Extra fields
                                $p->name_ar = $p->name_ar;

                                // Sticker new edition
                                // id_st_sticker_map = 7
                                $ids_concerned = Db::getInstance()->executeS('
                                    SELECT id_products
                                    FROM ' . _DB_PREFIX_ . 'st_sticker_map
                                    WHERE id_st_sticker = 7
                                ');
                                $ids_concerned = explode(',', $ids_concerned[0]['id_products']);
                                if(in_array($p->id_product, $ids_concerned)){
                                    $p->sticker_new_edition = 1;
                                }else{
                                    $p->sticker_new_edition = 0;
                                }

                                // Export data
                                $line = array(
                                    $v_id,
                                    $p->id_product,
                                    $p->name,
                                    $p->description_short,
                                    $v_price_tax_incl,
                                    $v_image_url,
                                    $p->product_url,
                                    $p->category,
                                    $v_thumbnail_url,
                                    $p->availability,
                                    $p->created_at,
                                    $p->reference,
                                    $p->brand,
                                    $p->brand,
                                    $p->available_quantity,
                                    $p->on_sale,
                                    $p->new,
                                    $p->name_ar,
                                    $p->sticker_new_edition
                                );

                                $i = count($line);

                                // Export prices for each currency
                                $currencies = new Currency();
                                foreach ($currencies->getCurrencies() as $currency) {
                                    $line[$i] = $v_price_tax_incl * $currency['conversion_rate'];
                                    $i++;
                                }

                                // Export sale prices for each currency
                                $currencies = new Currency();
                                foreach ($currencies->getCurrencies() as $currency) {
                                    $line[$i] = $p->sale_price * $currency['conversion_rate'];
                                    $i++;
                                }

                                // Export attribute groups values
                                $amf_attr_groups = explode(',', Configuration::get('amf_ag'));
                                if($amf_attr_groups && !empty($amf_attr_groups)){
                                    $attr_value = null;
                                    foreach ($amf_attr_groups as $amf_attr_group) {
                                        $product_attribute_groups = $product->getAttributesGroups($id_lang);
                                        foreach ($product_attribute_groups as $pag) {
                                            if ($pag['id_attribute_group'] == $amf_attr_group){
                                                $attr_value = $pag['attribute_name'];
                                            }
                                        }
                                        $line[$i] = $attr_value;
                                        unset($attr_value); 
                                        $i++;
                                    }
                                }

                                // Export feature values
                                $amf_features = explode(',', Configuration::get('amf_fg'));
                                if($amf_features && !empty($amf_features)){
                                    $feat_value = null;
                                    foreach ($amf_features as $amf_feature) {
                                        $product_features_groups = $product->getFeaturesStatic((int)$p->id_product);
                                        foreach ($product_features_groups as $pf) {
                                            if ($pf['id_feature'] == $amf_feature && $feature_values = FeatureValue::getFeatureValueLang($pf['id_feature_value'])){
                                                foreach ($feature_values as $feature_value) {
                                                    if($feature_value['id_lang'] == $id_lang){
                                                        $feat_value = $this->removeNl($feature_value['value']);
                                                    }
                                                }
                                            }
                                        }
                                        $line[$i] = $feat_value;
                                        unset($feat_value);                                        
                                        $i++;                                        
                                    }
                                }

                                if(DEV){
                                    echo "<pre>P ID : ".$p->id_product." - PA ID : ".$v_id."<br>";
                                    print_r($line);
                                    echo "</pre><br><br>";
                                }

                                fputcsv($fp, $line, $sep);
                                fseek($fp, -1, SEEK_CUR); 
                                // fwrite($fp, "\r\n");

                                echo date("Y-m-d H:i:s").' '.$this->l('Export product ID').' '.$p->id_product.' - '.$this->l('Attribute ID').' '.$v_id.'<br>';
                                fputs($log, date("Y-m-d H:i:s").' '.$this->l('Export product ID').' '.$p->id_product.' - '.$this->l('Attribute ID').' '.$v_id.PHP_EOL);
                            }

                        // --------------------------------------------------------------------
                        // If simple product
                        // --------------------------------------------------------------------
                        } else {

                            // Get description
                            $p->description_short = $this->cleanStr($p->description_short);

                            // Get price tax incl
                            $p->price_tax_incl = $product->getPriceStatic($p->id_product, true, null, 6, null, false, false);

                            // Get url
                            $link = new Link();
                            $p->product_url = $link->getProductLink($p->id_product, null, null, null, $id_lang);

                            // Get image url & thumbnail_url
                            $product_img = $product->getCover($p->id_product);
                            $p->image_url = $protocol . $link->getImageLink(isset($p->link_rewrite) ? $p->link_rewrite : $p->name, (int)$product_img['id_image'], 'findify-image');
                            $p->thumbnail_url = $protocol . $link->getImageLink(isset($p->link_rewrite) ? $p->link_rewrite : $p->name, (int)$product_img['id_image'], 'findify-thumbnail');

                            // Get categories
                            $p->category = '';
                            $product_categories = $product->getProductCategoriesFull($p->id_product, $id_lang);
                            $c=0;
                            foreach ($product_categories as $product_category) {
                                if ($c > 0)
                                    $p->category .= ' > ';
                                $p->category .= $product_category['name'];
                                $c++;
                            }

                            // Get availability
                            $p->available_quantity = $product->getQuantity($p->id_product);
                            $p->availability = ($p->available_quantity < 1) ? $this->l('out of stock') : $this->l('in stock');

                            // Get manufacturer name
                            $manufacturer = new Manufacturer();
                            $p->brand = $manufacturer->getNameById($p->id_manufacturer);

                            // Get sale price
                            $p->sale_price = $product->getPriceStatic($p->id_product, true, null, 6, null, false, true);

                            // Get date
                            $p->created_at = date(DATE_ISO8601, strtotime($p->date_add));

                            // On sale
                            $p->on_sale = $p->on_sale;

                            // New
                            $p->new = $product->isNew();
                            if(!$p->new)
                                $p->new = 0;

                            // Extra fields
                            $p->name_ar = $p->name_ar;

                            // Sticker new edition
                            // id_st_sticker_map = 7
                            $ids_concerned = Db::getInstance()->executeS('
                                SELECT id_products
                                FROM ' . _DB_PREFIX_ . 'st_sticker_map
                                WHERE id_st_sticker = 7
                            ');
                            $ids_concerned = explode(',', $ids_concerned[0]['id_products']);
                            if(in_array($p->id_product, $ids_concerned)){
                                $p->sticker_new_edition = 1;
                            }else{
                                $p->sticker_new_edition = 0;
                            }
                            
                            // Export data
                            $line = array(
                                $p->id_product,
                                $p->id_product,
                                $p->name,
                                $p->description_short,
                                $p->price_tax_incl,
                                $p->image_url,
                                $p->product_url,
                                $p->category,
                                $p->thumbnail_url,
                                $p->availability,
                                $p->created_at,
                                $p->reference,
                                $p->brand,
                                $p->brand,
                                $p->available_quantity,
                                $p->on_sale,
                                $p->new,
                                $p->name_ar,
                                $p->sticker_new_edition
                            );

                            $i = count($line);

                            // Export prices for each currency
                            $currencies = new Currency();
                            foreach ($currencies->getCurrencies() as $currency) {
                                $line[$i] = $p->price_tax_incl * $currency['conversion_rate'];
                                $i++;
                            }

                            // Export sale prices for each currency
                            $currencies = new Currency();
                            foreach ($currencies->getCurrencies() as $currency) {
                                $line[$i] = $p->sale_price * $currency['conversion_rate'];
                                $i++;
                            }

                            // Export attribute groups values
                            $amf_attr_groups = explode(',', Configuration::get('amf_ag'));
                            if($amf_attr_groups && !empty($amf_attr_groups)){
                                foreach ($amf_attr_groups as $amf_attr_group) {
                                    $attr_value = '';
                                    $line[$i] = $attr_value;
                                    $i++;
                                }
                            }

                            // Export feature values
                            $amf_features = explode(',', Configuration::get('amf_fg'));
                            if($amf_features && !empty($amf_features)){
                                $feat_value = null;
                                foreach ($amf_features as $amf_feature) {
                                    $product_features_groups = $product->getFeaturesStatic((int)$p->id_product);
                                    foreach ($product_features_groups as $pf) {
                                        if ($pf['id_feature'] == $amf_feature && $feature_values = FeatureValue::getFeatureValueLang($pf['id_feature_value'])){
                                            foreach ($feature_values as $feature_value) {
                                                if($feature_value['id_lang'] == $id_lang){
                                                    $feat_value = $this->removeNl($feature_value['value']);
                                                }
                                            }
                                        }
                                    }
                                    $line[$i] = $feat_value;
                                    unset($feat_value);                                        
                                    $i++;                                        
                                }
                            }

                            if(DEV){
                                echo "<pre>P ID : ".$p->id_product."<br>";
                                print_r($line);
                                echo "</pre><br><br>";
                            }

                            fputcsv($fp, $line, $sep);
                            fseek($fp, -1, SEEK_CUR); 
                            // fwrite($fp, "\r\n");

                            echo date("Y-m-d H:i:s").' '.$this->l('Export product ID').' '.$p->id_product.'<br>';
                            fputs($log, date("Y-m-d H:i:s").' '.$this->l('Export product ID').' '.$p->id_product.PHP_EOL);
                        }
                    }
                }
            }else{
                // If no product to export
                echo date("Y-m-d H:i:s").' '.$this->l('No product to export').'<br>';
                fputs($log, date("Y-m-d H:i:s").' '.$this->l('No product to export').PHP_EOL);
            } 

            fclose($fp);   

            // Delete old log files
            $dir = DIR_LOG;
            $deleted = $this->deleteOlderThan($dir, 3600*24*30);  

        } catch (PrestaShopException $e) {
            fputs($log, date("Y-m-d H:i:s").' ERROR : '.$e->getMessage().PHP_EOL.$e->getTraceAsString().PHP_EOL);
        }

        // Send mail to Findify
        if (Configuration::get('amf_sendemail')){

            $dir_mail = false;
            if (file_exists(dirname(__FILE__).'/mails/'.$iso.'/sendfeed.txt') &&
                file_exists(dirname(__FILE__).'/mails/'.$iso.'/sendfeed.html')) {
                $dir_mail = dirname(__FILE__).'/mails/';
            }
            if (file_exists(_PS_MAIL_DIR_.$iso.'/sendfeed.txt') &&
                file_exists(_PS_MAIL_DIR_.$iso.'/sendfeed.html')) {
                $dir_mail = _PS_MAIL_DIR_.'/'.$iso.'/';
            }

            $url_export = $store_url.'modules/findify/export/';
            $amf_feed_url = $url_export.'findify-product-feed-'.$iso.'.csv';

            $subject = $this->l('Findify product feed');

            $mail_vars = array(
                '{subject}' => $this->l('Findify product feed').' ['.$iso.']',
                '{firstname}' => Configuration::get('amf_fname'),
                '{lastname}' => Configuration::get('amf_lname'),
                '{from_email}' => $from,
                '{from_website}' => $shop_name,
                '{feed_url}' => $amf_feed_url,
            );

            if(Mail::Send(
                (int)$context->language->id,
                'sendfeed',
                $subject,
                $mail_vars,
                $to,
                null,
                null,
                null,
                null,
                null,
                $dir_mail
            )){
                echo date("Y-m-d H:i:s").' '.$this->l('Product feed sent to ').$to.'<br>';
                fputs($log, date("Y-m-d H:i:s").' '.$this->l('Product feed sent to ').$to.PHP_EOL);
            }else{
                echo date("Y-m-d H:i:s").' '.$this->l('Can\'t send product feed').'<br>';
                fputs($log, date("Y-m-d H:i:s").' '.$this->l('Can\'t send product feed').PHP_EOL);
            }

        }

        fputs($log, date("Y-m-d H:i:s").' '.$this->l('Export complete').' : '.$iso.PHP_EOL);
        fclose($log);
        echo date("Y-m-d H:i:s").' '.$this->l('Export complete').' : '.$iso.'<br>';
    }

    public function hookHeader()
    {
        $this->context->controller->addCSS(($this->_path).'views/css/findify.css', 'all');
        $id_lang = $this->context->language->id;
        $this->smarty->assign('amf_script', Configuration::get('amf_script_'.$id_lang));
        return $this->display(__FILE__, 'views/templates/hook/header.tpl');
    }

    public function hookdisplayFooterProduct($params)
    {
        $amf_product = array(
            'id_product' => $params['product']->id_product,
            'id_variant' => $params['product']->id_product_attribute,
        );
        $this->smarty->assign('amf_product',$amf_product);
        return $this->display(__FILE__, 'views/templates/hook/displayfooterproduct.tpl');
    }

    public function hookdisplayShoppingCart()
    {
        return $this->display(__FILE__, 'views/templates/hook/displayShoppingCart.tpl');
    }

    public function displayBranding()
    {
        $html = '<div class="panel">
            <legend>'.$this->l('Findify').'</legend>
            <p><img src="'._PS_BASE_URL_.__PS_BASE_URI__.'modules/findify/views/img/findify.png" alt="'.$this->l('Findify').'" width="" height="" /></p>
            <p>'.$this->l('For more information, please visit').' : <a target="_blank" href="https://www.findify.io">www.findify.io</a></p>
            <p>Created by <a target="_blank" href="https://www.arnaud-merigeau.fr/">Latoutfrancais</a></p>
        </div>';
        return $html;
    }

    public function stayInTouch()
    {
        if (version_compare(_PS_VERSION_, '1.6.0.0', '<')) {

            $html = '';
            $html .=
            '<br />
            <fieldset>
                <legend>'.$this->l('Plus de tutos PrestaShop ?').'</legend>
                <p>'.$this->l('Vous pouvez lire des articles et des tutos sur mon blog').' : <a target="_blank" href="https://www.arnaud-merigeau.fr/blog?utm_source=module&utm_medium=liens">www.arnaud-merigeau.fr/blog</a></p>
                <p>'.$this->l('Si ce module vous a été utile, laissez-moi un avis en cliquant sur l\'image :').'</p>
                <p><a target="_blank" href="https://www.google.fr/search?q=Freelance+PrestaShop+%26+WordPress,+20+Rue+de+Betnoms,+B%C3%A2timent+Digit+Halles,+33185+Le+Haillan&ludocid=11545508322336072769#lrd=0xd5528a606097133:0xa039e2a600286441,3"><img alt="avis google" src="https://www.arnaud-merigeau.fr/wp-content/uploads/2018/06/bloc_avis_google_freelance_prestashop.jpg" /></a></p>
            </fieldset>
            <br />
            <fieldset>
                <legend>'.$this->l('Restons en contact').'</legend>
                <table class="social" style="width:100%">
                <tr>
                    <td style="width:33.33%; text-align:center;">
                        <div id="fb-root"></div>
                        <script>(function(d, s, id) {
                          var js, fjs = d.getElementsByTagName(s)[0];
                          if (d.getElementById(id)) return;
                          js = d.createElement(s); js.id = id;
                          js.src = "https://connect.facebook.net/fr_FR/sdk.js#xfbml=1&version=v3.2&appId=118397404931307&autoLogAppEvents=1";
                          fjs.parentNode.insertBefore(js, fjs);
                        }(document, "script", "facebook-jssdk"));</script>
                        <div class="fb-page" data-href="https://www.facebook.com/freelance.prestashop.wordpress/" data-small-header="false" data-adapt-container-width="true" data-hide-cover="false" data-show-facepile="true"><blockquote cite="https://www.facebook.com/freelance.prestashop.wordpress/" class="fb-xfbml-parse-ignore"><a href="https://www.facebook.com/freelance.prestashop.wordpress/">Freelance PrestaShop &amp; WordPress Arnaud Merigeau</a></blockquote></div>
                    </td>
                    <td style="width:33.33%; text-align:center;">
                        <a href="https://twitter.com/arnaudmerigeau?ref_src=twsrc%5Etfw" class="twitter-follow-button" data-show-count="false">Suivre @arnaudmerigeau</a><script async src="https://platform.twitter.com/widgets.js" charset="utf-8"></script>
                    </td>
                    <td style="width:33.33%; text-align:center;">
                        <script src="https://apis.google.com/js/platform.js"></script>
                        <div class="g-ytsubscribe" data-channelid="UCKfHcvvjMdStwZhuIhyvtEA" data-layout="full" data-count="default"></div>
                    </td>
                </tr>
                </table>
            </fieldset>
            <br />
            <style>
                @media screen and (max-width: 991px){
                    table.social,table.social tr, table.social td{display:block;width:100%!important;padding:10px 0;}
                }
            </style>
            <iframe src="https://feed.mikle.com/widget/v2/72825/" height="337px" width="100%" class="fw-iframe" scrolling="no" frameborder="0"></iframe>';

            return $html;

        }else{

            $this->_html = '';
            $this->_html .=
            '<div class="panel">
                <legend>'.$this->l('Plus de tutos PrestaShop ?').'</legend>
                <p>'.$this->l('Vous pouvez lire des articles et des tutos sur mon blog').' : <a target="_blank" href="https://www.arnaud-merigeau.fr/blog?utm_source=module&utm_medium=liens">www.arnaud-merigeau.fr/blog</a></p>
                <p>'.$this->l('Si ce module vous a été utile, laissez-moi un avis en cliquant sur l\'image :').'</p>
                <p><a target="_blank" href="https://www.google.fr/search?q=Freelance+PrestaShop+%26+WordPress,+20+Rue+de+Betnoms,+B%C3%A2timent+Digit+Halles,+33185+Le+Haillan&ludocid=11545508322336072769#lrd=0xd5528a606097133:0xa039e2a600286441,3"><img alt="avis google" src="https://www.arnaud-merigeau.fr/wp-content/uploads/2018/06/bloc_avis_google_freelance_prestashop.jpg" /></a></p>
            </div>
            <div class="panel">
                <legend>'.$this->l('Restons en contact').'</legend>
                <table class="social" style="width:100%">
                <tr>
                    <td style="width:33.33%; text-align:center;">
                        <div id="fb-root"></div>
                        <script>(function(d, s, id) {
                          var js, fjs = d.getElementsByTagName(s)[0];
                          if (d.getElementById(id)) return;
                          js = d.createElement(s); js.id = id;
                          js.src = "https://connect.facebook.net/fr_FR/sdk.js#xfbml=1&version=v3.2&appId=118397404931307&autoLogAppEvents=1";
                          fjs.parentNode.insertBefore(js, fjs);
                        }(document, "script", "facebook-jssdk"));</script>
                        <div class="fb-page" data-href="https://www.facebook.com/freelance.prestashop.wordpress/" data-small-header="false" data-adapt-container-width="true" data-hide-cover="false" data-show-facepile="true"><blockquote cite="https://www.facebook.com/freelance.prestashop.wordpress/" class="fb-xfbml-parse-ignore"><a href="https://www.facebook.com/freelance.prestashop.wordpress/">Freelance PrestaShop &amp; WordPress Arnaud Merigeau</a></blockquote></div>
                    </td>
                    <td style="width:33.33%; text-align:center;">
                        <a href="https://twitter.com/arnaudmerigeau?ref_src=twsrc%5Etfw" class="twitter-follow-button" data-show-count="false">Suivre @arnaudmerigeau</a><script async src="https://platform.twitter.com/widgets.js" charset="utf-8"></script>
                    </td>
                    <td style="width:33.33%; text-align:center;">
                        <script src="https://apis.google.com/js/platform.js"></script>
                        <div class="g-ytsubscribe" data-channelid="UCKfHcvvjMdStwZhuIhyvtEA" data-layout="full" data-count="default"></div>
                    </td>
                </tr>
                </table>
            </div>
            <style>
                @media screen and (max-width: 991px){
                    table.social,table.social tr, table.social td{display:block;width:100%!important;padding:10px 0;}
                }
            </style>
            <iframe src="https://feed.mikle.com/widget/v2/72825/" height="337px" width="100%" class="fw-iframe" scrolling="no" frameborder="0"></iframe>';

            return $this->_html;

        }
    }
}
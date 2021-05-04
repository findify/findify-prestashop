<?php
/**
 * AM FINDIFY
 *
 * @author    LATOUTFRANCAIS | Arnaud Merigeau <contact@arnaud-merigeau.fr> - https://www.arnaud-merigeau.fr
 * @copyright Arnaud Merigeau 2020 - https://www.arnaud-merigeau.fr
 * @license   Commercial
 *
 */

/* This file can be called using a cron to generate product feed */
include(dirname(__FILE__) . '/../../config/config.inc.php');
include(dirname(__FILE__) . '/../../init.php');

/* Check security token */
if (!Tools::isPHPCLI()) {
    if (Tools::substr(Tools::encrypt('findify/cron'), 0, 10) != Tools::getValue('token') || !Module::isInstalled('findify')) {
        die('Bad token');
    }
}

/* Check if the module is enabled */
define('DIR_LOG', $_SERVER['DOCUMENT_ROOT']._MODULE_DIR_.'findify/log/');
define('DIR_EXPORT', $_SERVER['DOCUMENT_ROOT']._MODULE_DIR_.'findify/export/');
define('LOG_FILE', DIR_LOG.'amf-log-'.date('Ymd').'.txt');
$amf = Module::getInstanceByName('findify');
if ($amf->active) {
	foreach (Language::getLanguages(true) as $language) {
        $amf->exportFeed($language);
    }	
}

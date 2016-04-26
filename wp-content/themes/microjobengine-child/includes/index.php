<?php
echo 1;
require_once dirname(__FILE__) . '/aecore/index.php';
if(!class_exists('AE_Base')) return;
echo 2;
require_once dirname(__FILE__) . '/admin.php';
require_once dirname(__FILE__) . '/modules/index.php';
require_once dirname(__FILE__) . '/libs/index.php';
/**
 * Check plugin is active or not
 */
function et_is_plugin_active($plugin) {
    include_once (ABSPATH . 'wp-admin/includes/plugin.php');
    return is_plugin_active($plugin);
}
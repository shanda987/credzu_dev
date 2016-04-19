<?php
require_once dirname(__FILE__) . '/aecore/index.php';
if(!class_exists('AE_Base')) return;
require_once dirname(__FILE__) . '/admin.php';
require_once dirname(__FILE__) . '/alias-function.php';
require_once dirname(__FILE__) . '/theme.php';
require_once dirname(__FILE__) . '/modules/mJobMailing/index.php';
require_once dirname(__FILE__) . '/class-mJobPost.php';
require_once dirname(__FILE__) . '/class-mJobPostAction.php';
require_once dirname(__FILE__) . '/class-mJobSearchPost.php';
require_once dirname(__FILE__) . '/class-mJobRevenueAction.php';
require_once dirname(__FILE__) . '/modules/index.php';
require_once dirname(__FILE__) . '/widgets.php';
require_once dirname(__FILE__) . '/libs/index.php';
/**
 * Check plugin is active or not
 */
function et_is_plugin_active($plugin) {
    include_once (ABSPATH . 'wp-admin/includes/plugin.php');
    return is_plugin_active($plugin);
}
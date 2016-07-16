<?php
define("ET_UPDATE_PATH", "");
define("ET_VERSION", '1.0.3');
if (!defined('ET_URL')) define('ET_URL', 'http://www.enginethemes.com/');
if (!defined('ET_CONTENT_DIR')) define('ET_CONTENT_DIR', WP_CONTENT_DIR . '/et-content/');
define('TEMPLATEURL', get_template_directory_uri() );
$theme_name = 'microjobengine';
define('THEME_NAME', $theme_name);
define('ET_DOMAIN', 'enginetheme');
define('MOBILE_PATH', TEMPLATEPATH . '/mobile/');

/** User Role Types */
define('ADMIN', 'administrator');
define('INDIVIDUAL', 'individual');
define('COMPANY', 'company');
define('STAFF', 'staff');

/** Company Status' */
define('COMPANY_STATUS_REGISTERED', 'registered');
define('COMPANY_STATUS_UNDER_REVIEW', 'under_review');
define('COMPANY_STATUS_NEEDS_CHANGES', 'needs_changes');
define('COMPANY_STATUS_SUSPENDED', 'suspended');
define('COMPANY_STATUS_APPROVED', 'approved');
define('COMPANY_STATUS_DECLINED', 'declined');

/** Company Billing Status */
define('COMPANY_PAYEE_NAME_OVERRIDE_STATUS_APPROVED', 'approved');
define('COMPANY_PAYEE_NAME_OVERRIDE_STATUS_UNDER_REVIEW', 'under_review');
define('COMPANY_PAYEE_NAME_OVERRIDE_STATUS_UNDER_REVIEW_EXISTS', 'under_review_exists');
define('COMPANY_PAYEE_NAME_OVERRIDE_STATUS_DECLINED', 'declined');

// define( 'ALLOW_UNFILTERED_UPLOADS', true );

if (!defined('THEME_CONTENT_DIR ')) define('THEME_CONTENT_DIR', WP_CONTENT_DIR . '/et-content' . '/' . $theme_name);
if (!defined('THEME_CONTENT_URL')) define('THEME_CONTENT_URL', content_url() . '/et-content' . '/' . $theme_name);

// theme language path
if (!defined('THEME_LANGUAGE_PATH')) define('THEME_LANGUAGE_PATH', THEME_CONTENT_DIR . '/lang/');

if (!defined('ET_LANGUAGE_PATH')) define('ET_LANGUAGE_PATH', THEME_CONTENT_DIR . '/lang');

if (!defined('ET_CSS_PATH')) define('ET_CSS_PATH', THEME_CONTENT_DIR . '/css');

if (!defined('USE_SOCIAL')) define('USE_SOCIAL', 1);

// define posttype
if(!defined('MJOB')) {
    define('MJOB', 'mjob_post');
}

require_once dirname(__FILE__) . '/includes/index.php';
global $ae_tax_factory;
$meta  = array(
    'featured-tax',
    'mjob_category_image',
    'cat_bottom_title',
    'cat_bottom_block1_title',
    'cat_bottom_block2_title',
    'cat_bottom_block3_title',
    'cat_bottom_block1_content',
    'cat_bottom_block2_content',
    'cat_bottom_block3_content',
    'pricing_plan',
    'mjob_category_page_content',
    'mjob_category_banner_image'
);
$ae_tax_factory->set('mjob_category', new AE_Taxonomy_Meta('mjob_category', $meta) );
$meta1 = array(
    'click_type',
    'requirement_short_name',
    'requirement_checkbox_name',
    'requirement_modal_name'
    );
$ae_tax_factory->set('mjob_requirement', new AE_Taxonomy_Meta('mjob_requirement', $meta1));
if (!class_exists('AE_Base')) return;

function resetOption(){
    ae_update_option('client_payment_check_number', 0);
    ae_update_option('payment_check_number', 0);
}
///resetOption();
add_filter( 'comment_notification_recipients', '__return_empty_array', PHP_INT_MAX );
add_filter( 'comment_moderation_recipients',
    function( $emails )
    {
        // only send notification to the admin:
        return array( get_option( 'admin_email' ) );
    }
    , PHP_INT_MAX );
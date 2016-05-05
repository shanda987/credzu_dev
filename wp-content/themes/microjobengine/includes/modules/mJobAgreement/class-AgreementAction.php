<?php
class agreementAction extends mJobPostAction{
    public static $instance;
    /**
     * getInstance method
     *
     */
    public static function getInstance()
    {
        if (!self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    /**
     * the constructor of this class
     *
     */
    public  function __construct($post_type = 'mjob_post'){
        parent::__construct($post_type);
        $this->add_action('wp_enqueue_scripts', 'agreement_add_scripts', 9);
    }
    /**
     * add script
     *
     * @param void
     * @return void
     * @since 1.0
     * @package MicrojobEngine
     * @category void
     * @author JACK BUI
     */
    public function agreement_add_scripts(){
        $this->add_style('css-sign-js', get_template_directory_uri() . '/includes/modules/mJobAgreement/css/signature-pad.css', array(
            'jquery',
            'underscore',
            'backbone',
            'appengine'), ET_VERSION, true);
        $this->add_script('sign-js', get_template_directory_uri() . '/includes/modules/mJobAgreement/js/signature_pad.js', array(
            'jquery',
            'underscore',
            'backbone',
            'appengine'), ET_VERSION, true);
        $this->add_script('app-js', get_template_directory_uri() . '/includes/modules/mJobAgreement/js/app.js', array(
            'jquery',
            'underscore',
            'backbone',
            'appengine'), ET_VERSION, true);
    }
}
new agreementAction();
add_shortcode('client-first-name', 'addFirstName');
/**
 * add first name to shortcode
 *
 * @param array $atts
 * @return void
 * @since 1.0
 * @package MicrojobEngine
 * @category void
 * @author JACK BUI
 */
 function addFirstName(){
    global $user_ID;
    $profile = mJobProfileAction()->getProfile($user_ID);
    $name = '[client-first-name]';
    if( !empty($profile) ){
        $name =  $profile->first_name;
    }
    return $name;
}
add_shortcode('client-last-name', 'addLastName');
/**
 * add first name to shortcode
 *
 * @param array $atts
 * @return void
 * @since 1.0
 * @package MicrojobEngine
 * @category void
 * @author JACK BUI
 */
function addLastName(){
    global $user_ID;
    $profile = mJobProfileAction()->getProfile($user_ID);
    $name = '[client-last-name]';
    if( !empty($profile) ){
        $name =  $profile->last_name;
    }
    return $name;
}
add_shortcode('client-address', 'addAddress');
/**
 * add first name to shortcode
 *
 * @param array $atts
 * @return void
 * @since 1.0
 * @package MicrojobEngine
 * @category void
 * @author JACK BUI
 */
function addAddress(){
    global $user_ID;
    $profile = mJobProfileAction()->getProfile($user_ID);
    $name = '[client-address]';
    if( !empty($profile) ){
        $name =  $profile->billing_full_address;
    }
    return $name;
}
add_shortcode('signature', 'addSignature');
/**
 * add first name to shortcode
 *
 * @param array $atts
 * @return void
 * @since 1.0
 * @package MicrojobEngine
 * @category void
 * @author JACK BUI
 */
function addSignature(){
    global $user_ID;
    $profile = mJobProfileAction()->getProfile($user_ID);
    $name = '[signature]';
    if( !empty($profile) ){
        $name =  $profile->signature;
    }
    return $name;
}
add_shortcode('client-email', 'addClientEmail');
/**
 * add client email to shortcode
 *
 * @param void
 * @return void
 * @since 1.0
 * @package MicrojobEngine
 * @category void
 * @author JACK BUI
 */
function addClientEmail(){
    global $user_ID;
    $profile = mJobProfileAction()->getProfile($user_ID);
    $name = '[client-email]';
    if( !empty($profile) ){
        $name =  $profile->business_email;
    }
    return $name;
}
add_shortcode('client-phone', 'addClientPhone');
/**
 * add client email to shortcode
 *
 * @param void
 * @return void
 * @since 1.0
 * @package MicrojobEngine
 * @category void
 * @author JACK BUI
 */
function addClientPhone(){
    global $user_ID;
    $profile = mJobProfileAction()->getProfile($user_ID);
    $name = '[client-phone]';
    if( !empty($profile) ){
        $name =  $profile->phone;
    }
    return $name;
}
add_shortcode('company-name', 'addCompanyName');
/**
 * add first name to shortcode
 *
 * @param array $atts
 * @return void
 * @since 1.0
 * @package MicrojobEngine
 * @category void
 * @author JACK BUI
 */
function addCompanyName(){
    $name = '[company-name]';
    if( isset($_GET['jid']) ){
        $mjob = mJobAction()->get_mjob($_GET['jid']);
        if( !empty($mjob) ) {
            $profile = mJobProfileAction()->getProfile($mjob->post_author);
            if (!empty($profile)) {
                $name = $profile->first_name;
                return $name;
            }
        }
    }
    return $name;
}
add_shortcode('company-address', 'addAddressName');

/**
 * add first name to shortcode
 *
 * @param array $atts
 * @return void
 * @since 1.0
 * @package MicrojobEngine
 * @category void
 * @author JACK BUI
 */
function addAddressName(){
    $name = '[company-address]';
    if( isset($_GET['jid']) ){
        $mjob = mJobAction()->get_mjob($_GET['jid']);
        if( !empty($mjob) ) {
            $profile = mJobProfileAction()->getProfile($mjob->post_author);
            if (!empty($profile)) {
                $name = $profile->billing_full_address;
                return $name;
            }
        }
    }
    return $name;
}
add_shortcode('company-first-name', 'addCompanyFirstName');
/**
 * add first name to shortcode
 *
 * @param array $atts
 * @return void
 * @since 1.0
 * @package MicrojobEngine
 * @category void
 * @author JACK BUI
 */
function addCompanyFirstName(){
    $name = '[company-first-name]';
    if( isset($_GET['jid']) ){
        $mjob = mJobAction()->get_mjob($_GET['jid']);
        if( !empty($mjob) ) {
            $profile = mJobProfileAction()->getProfile($mjob->post_author);
            if (!empty($profile)) {
                $name = $profile->company_first_name;
                return $name;
            }
        }
    }
    return $name;
}
add_shortcode('service-description', 'addMjobDescription');
/**
 * add first name to shortcode
 *
 * @param array $atts
 * @return void
 * @since 1.0
 * @package MicrojobEngine
 * @category void
 * @author JACK BUI
 */
function addMjobDescription(){
    if( isset($_GET['jid']) ){
        $mjob = mJobAction()->get_mjob($_GET['jid']);
        if( !empty($mjob) ) {
            return $mjob->post_content;
        }
    }
    return '[service-description]';
}
add_shortcode('service-price', 'addMjobPrice');
/**
 * add first name to shortcode
 *
 * @param array $atts
 * @return void
 * @since 1.0
 * @package MicrojobEngine
 * @category void
 * @author JACK BUI
 */
function addMjobPrice(){
    if( isset($_GET['jid']) ){
        $mjob = mJobAction()->get_mjob($_GET['jid']);
        if( !empty($mjob) ) {
            return $mjob->et_budget_text;
        }
    }
    return '[service-price]';
}
add_shortcode('service-duration', 'addMjobDuration');
/**
 * add service duration to shortcode
 *
 * @param array $atts
 * @return void
 * @since 1.0
 * @package MicrojobEngine
 * @category void
 * @author JACK BUI
 */
function addMjobDuration(){
    if( isset($_GET['jid']) ){
        $mjob = mJobAction()->get_mjob($_GET['jid']);
        if( !empty($mjob) ) {
            return $mjob->time_delivery;
        }
    }
    return '[service-duration]';
}
add_shortcode('timestamp', 'addTimeStamp');
/**
 * add first name to shortcode
 *
 * @param array $atts
 * @return void
 * @since 1.0
 * @package MicrojobEngine
 * @category void
 * @author JACK BUI
 */
function addTimeStamp(){
    $time_option = get_option('date_format');
    return date($time_option, time());
}
add_shortcode('ip-address', 'addIpAddress');
/**
 * add first name to shortcode
 *
 * @param array $atts
 * @return void
 * @since 1.0
 * @package MicrojobEngine
 * @category void
 * @author JACK BUI
 */
function addIpAddress(){
    $remoteIp = new RemoteAddress();
    $remoteIp = $remoteIp->getIpAddress();
    return $remoteIp;
}
class RemoteAddress
{
    /**
     * Whether to use proxy addresses or not.
     *
     * As default this setting is disabled - IP address is mostly needed to increase
     * security. HTTP_* are not reliable since can easily be spoofed. It can be enabled
     * just for more flexibility, but if user uses proxy to connect to trusted services
     * it's his/her own risk, only reliable field for IP address is $_SERVER['REMOTE_ADDR'].
     *
     * @var bool
     */
    protected $useProxy = false;

    /**
     * List of trusted proxy IP addresses
     *
     * @var array
     */
    protected $trustedProxies = array();

    /**
     * HTTP header to introspect for proxies
     *
     * @var string
     */
    protected $proxyHeader = 'HTTP_X_FORWARDED_FOR';

    // [...]

    /**
     * Returns client IP address.
     *
     * @return string IP address.
     */
    public function getIpAddress()
    {
        $ip = $this->getIpAddressFromProxy();
        if ($ip) {
            return $ip;
        }

        // direct IP address
        if (isset($_SERVER['REMOTE_ADDR'])) {
            return $_SERVER['REMOTE_ADDR'];
        }

        return '';
    }

    /**
     * Attempt to get the IP address for a proxied client
     *
     * @see http://tools.ietf.org/html/draft-ietf-appsawg-http-forwarded-10#section-5.2
     * @return false|string
     */
    protected function getIpAddressFromProxy()
    {
        if (!$this->useProxy
            || (isset($_SERVER['REMOTE_ADDR']) && !in_array($_SERVER['REMOTE_ADDR'], $this->trustedProxies))
        ) {
            return false;
        }

        $header = $this->proxyHeader;
        if (!isset($_SERVER[$header]) || empty($_SERVER[$header])) {
            return false;
        }

        // Extract IPs
        $ips = explode(',', $_SERVER[$header]);
        // trim, so we can compare against trusted proxies properly
        $ips = array_map('trim', $ips);
        // remove trusted proxy IPs
        $ips = array_diff($ips, $this->trustedProxies);

        // Any left?
        if (empty($ips)) {
            return false;
        }

        // Since we've removed any known, trusted proxy servers, the right-most
        // address represents the first IP we do not know about -- i.e., we do
        // not know if it is a proxy server, or a client. As such, we treat it
        // as the originating IP.
        // @see http://en.wikipedia.org/wiki/X-Forwarded-For
        $ip = array_pop($ips);
        return $ip;
    }

    // [...]
}
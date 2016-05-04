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
add_shortcode('client-signature', 'addSignature');
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
    $name = '[client-signature]';
    if( !empty($profile) ){
        $name =  $profile->client_signature;
    }
    return $name;
}
add_shortcode('company-signature', 'addCompanySignature');
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
function addCompanySignature(){
    $name = '[company-signature]';
    if( isset($_GET['jid']) ){
        $mjob = mJobAction()->get_mjob($_GET['jid']);
        if( !empty($mjob) ) {
            $profile = mJobProfileAction()->getProfile($mjob->post_author);
            if (!empty($profile)) {
                $name = $profile->company_signature;
                return $name;
            }
        }
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
add_shortcode('mjob-description', 'addMjobDescription');
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
    return '';
}
add_shortcode('mjob-price', 'addMjobPrice');
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
    return '[mjob-price]';
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
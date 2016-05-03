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
    $name = '';
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
    $name = '';
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
    $name = '';
    if( !empty($profile) ){
        $name =  $profile->billing_full_address;
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
    $name = '';
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
    $name = '';
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
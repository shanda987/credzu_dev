<?php
class mJobConnectSocial extends AE_Base
{
    public static $instance;
    /**
     * Get instance method
     */
    public static function getInstance() {
        if(!self::$instance) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Constructor of class
     */
    public function __construct() {
        $this->add_action('ae_facebook_connect_social', 'mJobConnectFacebook');
        $this->add_action('ae_google_connect_social', 'mJobConnectGoogle');
        $this->add_action('ae_twitter_connect_social', 'mJobConnectTwitter');

        $this->add_filter('ae_social_redirect_link', 'mJobConnectSocialRedirectLink');
    }

    /**
     * Connect to Facebook
     * @param string $social_id
     * @return void
     * @since 1.0
     * @package MicrojobEngine
     * @category Connect Social
     * @author Tat Thien
     */
    public function mJobConnectFacebook($social_id) {
        $this->mJobUpdateUserSocialID('et_facebook_id', $social_id);
    }

    /**
     * Connect to Google
     * @param string $social_id
     * @return void
     * @since 1.0
     * @package MicrojobEngine
     * @category Connect Social
     * @author Tat Thien
     */
    public function mJobConnectGoogle($social_id) {
        $this->mJobUpdateUserSocialID('et_google_id', $social_id);
    }

    /**
     * Connect to Twitter
     * @param string $social_id
     * @return void
     * @since 1.0
     * @package MicrojobEngine
     * @category Connect Social
     * @author Tat Thien
     */
    public function mJobConnectTwitter($social_id) {
        $this->mJobUpdateUserSocialID('et_twitter_id', $social_id);
    }

    /**
     * Save social id to user meta
     * @param string $meta_key
     * @param string $social_id
     * @return void
     * @since 1.0
     * @package MicrojobEngine
     * @category Connect Social
     * @author Tat Thien
     */
    public function mJobUpdateUserSocialID($meta_key, $social_id) {
        global $current_user;
        if(!empty($social_id) && !empty($current_user->ID) && mJobUserActivate($current_user->ID)) {
            update_user_meta($current_user->ID, $meta_key, $social_id);
            if($meta_key == 'et_facebook_id') {
                $resp = array(
                    'success'   => true,
                    'data'      => array(
                        'redirect_url' => et_get_page_link('profile')
                    )
                );
                wp_send_json($resp);
            } else {
                wp_redirect(et_get_page_link('profile'));
                exit;
            }
        }
    }

    /**
     * Return to dashboard when user connect social successfully
     * @param string $url
     * @return string $url
     * @since 1.0
     * @package MicrojobEngine
     * @category void
     * @author Tat Thien
     */
    public function mJobConnectSocialRedirectLink($url) {
        $url = et_get_page_link('dashboard');
        return $url;
    }
}

$new_instance = mJobConnectSocial::getInstance();
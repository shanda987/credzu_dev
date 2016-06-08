<?php

/**
 * Twitter login Authentication class
 * @since 1.2
 * @author Dakachi update
 */
class ET_TwitterAuth extends ET_SocialAuth
{
    
    const OPT_CONSUMER_KEY = 'et_twitter_key';
    const OPT_CONSUMER_SECRET = 'et_twitter_secret';
    protected $consumer_key;
    protected $consumer_secret;
    protected $oath_callback;
    public function __construct() {
        parent::__construct('twitter', 'et_twitter_id', array('title' => __("SIGN IN WITH TWITTER", ET_DOMAIN), 'content' => __("This seems to be your first time signing in using your Twitter account.If you already have an account  , please log in using the form below to link it to your Twitter account. Otherwise, please enter an email address and a password on the form, and a username on the next page to create an account.You will only do this step ONCE. Next time, you'll get logged in right away.", ET_DOMAIN), 'content_confirm' => __("Please provide a username to continue", ET_DOMAIN)));
        $this->consumer_key = ae_get_option(self::OPT_CONSUMER_KEY, '');
        
        // 'H7ggzgE4rNubSq09SKQJGw';
        $this->consumer_secret = ae_get_option(self::OPT_CONSUMER_SECRET, '');
        
        //'zUrMVznhHvrMEKBE5LhipfvRODLlPsvEJLvYiaf4yqE';
        $this->oath_callback = add_query_arg('action', 'twitterauth_callback', home_url());
        
        // only run if options are given
        if (!empty($this->consumer_key) && !empty($this->consumer_secret) /*&& !is_user_logged_in()*/) {
            
            //$this->add_action('init', 'redirect');
            $this->redirect();
        }
    }
    
    /**
     * Return if twitter auth are ready to run
     */
    public static function is_active() {
        $consumer_key = ae_get_option(self::OPT_CONSUMER_KEY, '');
        $consumer_secret = ae_get_option(self::OPT_CONSUMER_SECRET, '');
        
        return (!empty($consumer_key) && !empty($consumer_secret));
    }
    
    protected function send_created_mail($user_id) {
        do_action('et_after_register', $user_id);
    }
    
    /**
     * Redirect and auth twitter account
     */
    public function redirect() {
        if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'twitterauth') {

            // request token
            if (!isset($_SESSION)) {
                ob_start();
                @session_start();
            }
            require_once dirname(__FILE__) . '/twitteroauth/twitteroauth.php';
            
            // create connection
            $connection = new TwitterOAuth($this->consumer_key, $this->consumer_secret);

            // request token
            $request_token = $connection->getRequestToken($this->oath_callback);
            //
            if ($request_token) {
                
                // var_dump($request_token);
                // exit;
                if (isset($request_token['oauth_token']) && $request_token['oauth_token_secret']) {
                    $token = $request_token['oauth_token'];
                    $_SESSION['oauth_token'] = $token;
                    $_SESSION['oauth_token_secret'] = $request_token['oauth_token_secret'];
                    update_option($token, $request_token['oauth_token_secret']);
                    update_option('testsss', $token);
                    update_option('test2', $request_token['oauth_token_secret']);
                    // try et session
                    et_write_session('oauth_token', $token);
                    et_write_session('oauth_token_secret', $request_token['oauth_token_secret']);
                }
                
                // redirect to twitter
                switch ($connection->http_code) {
                    case 200:
                        $url = $connection->getAuthorizeURL($request_token);

                        //redirect to Twitter .
                        header('Location: ' . $url);
                        exit;
                        break;

                    default:
                        _e("Conection with twitter Failed", ET_DOMAIN);
                        exit;
                        break;
                }
            } 
            else {
                echo __("Error Receiving Request Token", ET_DOMAIN);
                exit;
            }
        }
        
        // twitter auth callback
        else if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'twitterauth_callback' && $_GET['oauth_token']) {

            // request access token and
            // create account here
            if (!isset($_SESSION)) {
                ob_start();
                @session_start();
            }
            require_once dirname(__FILE__) . '/twitteroauth/twitteroauth.php';
            
            $et_session = et_read_session();
            if(isset($et_session['oauth_token'])) {
                $oauth_token = $et_session['oauth_token'];
                $oauth_token_secret =  $et_session['oauth_token_secret'];
            }elseif( isset( $_SESSION['oauth_token'])) {
                $oauth_token = $_SESSION['oauth_token'];
                $oauth_token_secret =  $_SESSION['oauth_token_secret'];
            }
            else{
                $oauth_token = $_GET['oauth_token'];
                $oauth_token_secret = get_option($oauth_token);
                delete_option($_GET['oauth_token']);
            }
            var_dump($oauth_token);
            var_dump(get_option('testsss'));
            var_dump($oauth_token_secret);
            // create connection
            $connection = new TwitterOAuth($this->consumer_key, $this->consumer_secret, $oauth_token, $oauth_token_secret);
            // request access token
            $access_token = $connection->getAccessToken($_REQUEST['oauth_verifier']);
            var_dump($access_token);
            //
            if ($access_token && isset($access_token['oauth_token'])) {
                
                // recreate connection
                $connection = new TwitterOAuth($this->consumer_key, $this->consumer_secret, $access_token['oauth_token'], $access_token['oauth_token_secret']);
                $account = $connection->get('account/verify_credentials');
                
                // create account
                if ($account && isset($account->screen_name) && isset($account->name)) {
                    
                    // find
//                    $users = get_users(array('meta_key' => 'et_twitter_id', 'meta_value' => $account->id));
                    $users = $this->get_user( $account->id );
                    if (!empty($users) ) {
                        //$ae_user = $users[0];
                        $result = $this->logged_user_in( $account->id );
                        //wp_redirect(apply_filters('ae_social_redirect_link', home_url()));
                        wp_redirect( et_get_page_link('dashboard') );
                        exit('111');
                    }
                    /**
                     * Hook action connect social
                     * @since MicrojobEngine 1.0
                     */
                    do_action('ae_twitter_connect_social', $account->id);
                    
                    $avatars = array();
                    $sizes = get_intermediate_image_sizes();
                    foreach ($sizes as $size) {
                        $avatars[$size] = array($account->profile_image_url);
                    }
                    
                    $params = array(
                        'user_login' => $account->screen_name,
                        'display_name' => $account->name,
                        'user_location' => $account->location,
                        'description' => $account->description,
                        'et_avatar' => $avatars,
                        'et_social_id'=>$account->id
                        );
                    $params = serialize($params);
                    // save user info for saving later
                    $_SESSION['user_login'] = $account->screen_name;
                    $_SESSION['display_name'] = $account->name;
                    $_SESSION['et_twitter_id'] = $account->id;
                    $_SESSION['user_location'] = $account->location;
                    $_SESSION['description'] = $account->description;
                    $_SESSION['profile_image_url'] = $account->profile_image_url;
                    $_SESSION['et_auth'] = serialize(array('user_login' => $account->screen_name, 'display_name' => $account->name, 'user_location' => $account->location, 'description' => $account->description, 'et_avatar' => $avatars,));
                    $_SESSION['et_social_id'] = $account->id;
                    $_SESSION['et_auth_type'] = 'twitter';
                    
                    // try to user et session
                    et_write_session('et_auth', serialize($params));
                    et_write_session('et_social_id', $account->id);
                    et_write_session('et_auth_type', 'twitter');
                    $params = base64_encode($params);
                    wp_redirect($this->auth_url.'&param='.$params.'&c='.time());
                    exit();
                }
              //  wp_redirect(home_url());
            }
            //wp_redirect(home_url());
            exit();
        }
        
        // create user
        else if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'twitterauth_login') {
            if (!isset($_SESSION)) {
                ob_start();
                @session_start();
            }
            if (!empty($_POST['user_email'])) {
                $password = wp_generate_password();
                $new_account = array('user_login' => $_SESSION['user_login'], 'display_name' => $_SESSION['display_name'], 'et_twitter_id' => $_SESSION['et_twitter_id'], 'user_location' => $_SESSION['user_location'], 'description' => $_SESSION['description'], 'user_email' => $_POST['user_email'], 'user_pass' => $password, 'et_avatar' => array('thumbnail' => array($_SESSION['profile_image_url'])));
                $ae_user = get_user_by('login', $new_account['user_login']);
                if ($ae_user != false) {
                    $new_account['user_login'] = str_replace('@', '', $_POST['user_email']);
                }
                $ae_user = AE_Users::get_instance();
                $result = $ae_user->insert($new_account);
                if (!is_wp_error($result)) {
                    
                    // send email here
                    //
                    do_action('et_after_register', $result);
                    
                    // wp_mail( $_POST['user_email'],
                    //  __("You have been logged in via Twitter", ET_DOMAIN),
                    //  "Hi, <br/> your pasword on our site is {$password}");
                    // login
                    $ae_user = wp_signon(array('user_login' => $new_account['user_login'], 'user_password' => $new_account['user_pass']));
                    if (is_wp_error($ae_user)) {
                        global $et_error;
                        $et_error = $ae_user->get_error_message();
                        
                        //echo $user->get_error_message();
                        
                    } 
                    else {
                        wp_redirect(home_url());
                        exit;
                    }
                } 
                else {
                    global $et_error;
                    $et_error = $result->get_error_message();
                }
            }
            
            // ask people for password
            include TEMPLATEPATH . '/page-twitter-auth.php';
            exit;
        }
    }
}

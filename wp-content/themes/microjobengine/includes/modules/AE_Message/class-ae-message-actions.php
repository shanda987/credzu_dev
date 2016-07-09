<?php
/**
 * Private message action class
 */
class AE_Private_Message_Actions extends mJobPostAction
{
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
     * The constructor
     *
     * @param void
     * @return void
     * @since 1.0
     * @author Tambh
     */
    public function __construct($post_type = 'ae_message') {
     $this->post_type = 'ae_message';
        parent::__construct($post_type);
        $this->ruler = array(
            'post_content'
        );
    }
    /**
      * Init for class AE_Private_Message_Actions
      * @param void
      * @return void
      * @since 1.0
      * @package FREELANCEENGINE
      * @category PRIVATE MESSAGE
      * @author Tambh
      */
    public function init()
    {
        $this->add_ajax('ae-fetch-ae_message', 'fetch_post');
        $this->add_ajax('ae-ae_message-sync', 'syncMessage');
        $this->add_action( 'wp_footer', 'ae_message_add_template' );
        $this->add_action('wp_enqueue_scripts', 'aeMessageScript');
        $this->add_filter('ae_convert_ae_message', 'convertPost');
        
    }
    /**
     * enqueue script for ae message
     *
     * @param void
     * @return void
     * @since 1.0
     * @package MicrojobEngine
     * @category void
     * @author JACK BUI
     */
    public function aeMessageScript(){
        wp_enqueue_script('ae-message-js', get_template_directory_uri() . '/includes/modules/AE_Message/js/ae_message.js', array(
            'jquery',
            'underscore',
            'backbone',
            'appengine',
            'front'
        ), 1.0, true);
    }
    /**
      * Add private message modal
      * @param void
      * @return void
      * @since 1.0
      * @package FREELANCEENGINE
      * @category PRIVATE MESSAGE
      * @author Tambh
      */
    public  function ae_message_add_template(){

    }
    /**
      * Sync private message data
      * @param void
      * @return void
      * @since 1.0
      * @package FREELANCEENGINE
      * @category PRIVATE MESSAGE
      * @author Tambh
      */
    public function syncMessage(){
        global $user_ID;
        $request = $_REQUEST;
        if(isset($request['conversation_content'])) {
            $request['post_content'] = $request['conversation_content'];
        };

        do_action('ae_message_validate_before_sync', $request);
        $response = $this->validatePost($request);
        if( !$response['success'] ){
            wp_send_json($response);
            exit;
        }
        $request['post_status'] = 'publish';

        if(!isset($request['post_title']) || empty($request['post_title'])) {
            $request['post_title'] = __('Message for: ', ET_DOMAIN);
            if( isset( $request['post_parent']) ){
                $parent = get_post($request['post_parent']);
                if( $parent ){
                    $request['post_title'] .= $parent->post_title;
                }

            }
        }

        $request['from_user'] = $user_ID;
        $response = $this->sync_post($request);
        if( $response && !is_wp_error($response) ){
            if( $request['method'] == 'create'){
                update_post_meta($response['data']->ID, $request['to_user'].'_conversation_status', 'unread' );
            }
        }
        do_action('ae_after_message', $response, $request);
        $response = apply_filters('ae_message_response', $response, $request);
        wp_send_json($response);

    }
    /**
     * validate data
     *
     * @param array $data
     * @return array $result
     * @since 1.0
     * @package MicrojobEngine
     * @category void
     * @author JACK BUI
     */
    public function validatePost($data){
        $result = array(
            'success'=> true,
            'msg'=> __('Successful!', ET_DOMAIN)
        );
        return $result;
    }
    /**
      * convert
      *
      * @param void
      * @return void
      * @since 1.0
      * @package FREELANCEENGINE
      * @category FRE CREDIT
      * @author Jack Bui
      */
    public function convertPost($result){
        global $user_ID;
        $result->author_avatar = mJobAvatar($result->post_author);
        if (current_user_can('manage_options') || $result->post_author == $user_ID || $result->to_user == $user_ID || ae_user_role($result->post_author) == 'administrator') {
            $children = get_children(array(
                'numberposts' => 15,
                'order' => 'ASC',
                'post_parent' => $result->ID,
                'post_type' => 'attachment'
            ));
            $result->et_carousels = array();

            foreach ($children as $key => $value) {
                $result->et_carousels[] = $value;
            }
            $result->et_files = array();
            foreach ($children as $key => $value) {
                $result->et_files[] = $value;
            }
        }
        return $result;
    }

    /**
      * Validate data
      * @param array $data
      * @return array $response
      * @since 1.0
      * @package FREELANCEENGINE
      * @category PRIVATE MESSAGE
      * @author Tambh
      */
    public function ae_private_message_validate( $data ){
        global $user_ID;
        if( !empty($data)){
            if( isset($data['method']) && $data['method'] == 'create' ) {
                $response = ae_private_message_created_a_conversation($data);
                if (!$response['success']) {
                    return $response;
                }
                //check sender can send a message
                if (isset($data['from_user']) && $data['project_id']) {
                    $response = $this->ae_private_message_authorize_sender($data['from_user'], $data['project_id'], $data);
                } else {
                    $response = array(
                        'success' => false,
                        'msg' => __("Your account can't send this message!", ET_DOMAIN)
                    );
                    return $response;
                }
                // check receiver can receive a message
                if (isset($data['to_user']) && $data['bid_id']) {
                    $response = $this->ae_private_message_authorize_receiver($data['to_user'], $data['bid_id'], $data);
                } else {
                    $response = array(
                        'success' => false,
                        'msg' => __("Your account can't send this message!", ET_DOMAIN)
                    );
                    return $response;
                }
                //check message content
                $response = $this->ae_private_message_authorize_message($data);
            }
        }
        else{
            $response = array(
                'success' => false,
                'msg' => __('Data is empty!', ET_DOMAIN)
            );
        }
        return $response;
    }
    /**
      * authorize sender
      * @param integer $user_id
      * @param integer $project_id
      * @param array $data
      * @return array $response
      * @since 1.0
      * @package FREELANCEENGINE
      * @category PRIVATE MESSAGE
      * @author Tambh
      */
    public function ae_private_message_authorize_sender( $user_id, $project_id, $data ){
        global $user_ID;
        if( $user_id == $user_ID ) {
            if (is_project_owner($user_ID, $project_id)) {
                $response = array(
                    'success' => true,
                    'msg' => __("Authorize successful", ET_DOMAIN),
                    'data'=> $data
                );
                return $response;
            }
        }
        $response = array(
            'success' => false,
            'msg' => __("Your account can't send this message!", ET_DOMAIN)
        );
        return $response;
    }
    /**
     * authorize receiver
     * @param integer $user_id
     * @param integer $bid_id
     * @param array $data
     * @return array $response
     * @since 1.0
     * @package FREELANCEENGINE
     * @category PRIVATE MESSAGE
     * @author Tambh
     */
    public function ae_private_message_authorize_receiver( $user_id, $bid_id, $data ){
        global $user_ID;
        if( $user_id == $user_ID ) {
            if (is_bid_owner($user_ID, $bid_id)) {
                $response = array(
                    'success' => true,
                    'msg' => __("Authorize successful", ET_DOMAIN),
                    'data'=> $data
                );
                return $response;
            }
        }
        $response = array(
            'success' => false,
            'msg' => __("Your account can't send this message!", ET_DOMAIN)
        );
        return $response;
    }
    /**
     * authorize message content
     * @param array $data
     * @return array $response
     * @since 1.0
     * @package FREELANCEENGINE
     * @category PRIVATE MESSAGE
     * @author Tambh
     */
    public function ae_private_message_authorize_message( $data ){
        if( isset($data['post_title']) &&  $data['post_title'] !== '' ){
            $response = array(
                'success' => true,
                'msg' => __("Valid title!", ET_DOMAIN),
                'data'=> $data
            );
        }
        else{
            $response = array(
                'success' => false,
                'msg' => __("Please enter your message's subject!", ET_DOMAIN)
            );
            return $response;
        }
        if( isset($data['post_content']) &&  $data['post_content'] != '' ){
            $response = array(
                'success' => true,
                'msg' => __("This content is valid!", ET_DOMAIN),
                'data'=> $data
            );
        }
        else{
            $response = array(
                'success' => false,
                'msg' => __("Please enter your message's content!", ET_DOMAIN)
            );
            return $response;
        }
        return $response;
    }

    /**
      * Convert private message
      * @param object $result
      * @return object $result
      * @since 1.0
      * @package FREELANCEENGINE
      * @category PRIVATE MESSAGE
      * @author Tambh
      */
    public function ae_message_convert($result){

        return $result;
    }
    /**
      * Filter args when fetch data
      * @param array $query_args
      * @return array $query_args
      * @since 1.0
      * @package FREELANCEENGINE
      * @category PRIVATE MESSAGE
      * @author Tambh
      */
    public function filter_query_args($query_args){
		global $user_ID;
        $query = $_REQUEST['query'];
        $query_args['meta_query'] = $query['meta_query'];
        return $query_args;
    }
}
$instance = AE_Private_Message_Actions::getInstance();
$instance->init();
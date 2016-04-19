<?php
class mJobPostAction extends AE_PostAction{
    public static $instance;
    public $ruler;
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
    public  function __construct($post_type = 'post'){
        parent::__construct($post_type);
    }
    /**
     * sync post
     *
     * @param array $request
     * @return array $result
     * @since 1.0
     * @package MicrojobEngine
     * @category void
     * @author JACK BUI
     */
    public function sync_post($request) {
        /*
         * Defaul result
         */
        global $user_ID;
        $result = array(
            'success' => FALSE,
            'msg'     => __( 'Failed!', ET_DOMAIN )
        );
        $ruler = array(
            'post_type' => 'required',
        );
        $resp  = $this->checkPendingAccount($request);
        if( !$resp['success'] ){
            return $resp;
        }
        if ( (!de_check_ajax_referer('ae-mjob_post-sync', false, false) && !check_ajax_referer('ae-mjob_post-sync', false, false) ) ){
            $result = array(
                'success' => false,
                'msg' => __("Don't try to hack!", ET_DOMAIN)
            );
            return $result;
        }
        $this->ruler = wp_parse_args($this->ruler, $ruler);
        $error_message = array(
            'post_type.required' =>__('Don\'t try to hack 1!', ET_DOMAIN),
            'nonce.required' =>__('Don\'t try to hack 2!', ET_DOMAIN),
            'nonce.nonce' =>__('Don\'t try to hack 3!', ET_DOMAIN),
        );
        $validator = new AE_Validator( $request, $this->ruler, array(), $error_message);
        if ( $validator->fails() ) {
            $result[ 'msg' ]  = __( 'Invalid input. Please try again.', ET_DOMAIN );
            $result[ 'data' ] = $validator->getMessages();
        } else {
            //Request pass the rules, call post object to sync it
            global $ae_post_factory;
            if(!isset($request[ 'post_type' ]))
            {
                $result[ 'success' ] = FALSE;
                $result[ 'msg' ]     = __( 'Don\'t try to hack my groom baby!', ET_DOMAIN );
                return $result;
            }
            $post_object = $ae_post_factory->get( $request[ 'post_type' ] );
            if ( NULL == $post_object ) {
                $result[ 'success' ] = FALSE;
                $result[ 'msg' ]     = __( 'Don\'t try to hack my groom baby!', ET_DOMAIN );
                return $result;
            }
            // unset package data when edit place if user can edit others post
            if (isset($request['archive'])) {
                $request['post_status'] = 'archive';
            }
            if (isset($request['publish'])) {
                $request['post_status'] = 'publish';
            }
            if (isset($request['delete'])) {
                $request['post_status'] = 'trash';
            }
            if (isset($request['disputed'])) {
                $request['post_status'] = 'disputed';
            }
            if (isset($request['pause'])) {
                $request['post_status'] = 'pause';
                unset($request['pause']);
            }
            if (isset($request['unpause'])) {
                $request['post_status'] = 'unpause';
                unset($request['unpause']);
            }
            if (isset($request['finished'])) {
                $request['post_status'] = 'finished';
                unset($request['finished']);
            }
            // Call instance sync
            $post = $post_object->sync( $request );
            if ( is_wp_error( $post ) ) {
                //Not inserted
                $result[ 'success' ] = FALSE;
                $result[ 'msg' ]     = $post->get_error_messages();
                $result[ 'data' ]    = $post->get_error_data();
            } else {
                if ( isset($request['et_carousels']) ) {

                    // loop request carousel id
                    foreach ($request['et_carousels'] as $key => $value) {
                        $att = get_post($value);
                        // just admin and the owner can add carousel
                        global $user_ID;
                        if( isset($att->post_author) ) {
                            if (current_user_can('manage_options') || $att->post_author == $user_ID) {
                                wp_update_post(array(
                                    'ID' => $value,
                                    'post_parent' => $post->ID
                                ));
                            }
                        }
                    }

                    if (current_user_can('manage_options') || $att->post_author == $user_ID) {

                        /**
                         * featured image not null and should be in carousels array data
                         */
                        if (!isset($request['featured_image'])) {
                            set_post_thumbnail($post->ID, $value);
                        }
                    }
                }
                $result[ 'success' ] = TRUE;
                $result[ 'data' ]    = $post_object->convert($post);
                if( 'remove' === $request['method'] )
                {
                    $result[ 'msg' ]     = __( 'Delete successfully!', ET_DOMAIN );
                }
                else
                {
                    $result[ 'msg' ]     = __( 'Successful!', ET_DOMAIN );
                }
            }

        }
        return $result;
    }
    /**
     * check pending account
     *
     * @param void
     * @return void
     * @since 1.0
     * @package MicrojobEngine
     * @category void
     * @author JACK BUI
     */
    public function checkPendingAccount($request){
        global $user_ID;
        $result = array(
            'success'=> true,
            'msg'=> __('success', ET_DOMAIN)
        );
        if (!AE_Users::is_activate($user_ID)) {
            $result = array(
                'success' => false,
                'msg' => __("Your account is pending. You have to activate your account to continue this step.", ET_DOMAIN)
            );
        }
        return apply_filters('mjob_check_pending_account', $result, $request);
    }

}
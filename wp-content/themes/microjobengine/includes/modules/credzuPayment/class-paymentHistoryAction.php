<?php
class credzuPaymentHistoryAction extends mJobPostAction{
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
    public  function __construct($post_type = 'payment_history'){
        $this->post_type = 'payment_history';
        $this->add_ajax('ae-payment_history-sync', 'syncPost');
        $this->add_action('create_payment_history', 'create_payment_history', 10, 4);
        $this->add_action('create_payment_history', 'create_client_payment_history', 10, 4);
    }
    /**
     * sync Post function
     *
     * @param void
     * @return void
     * @since 1.0
     * @package MicrojobEngine
     * @category void
     * @author JACK BUI
     */
    public function syncPost(){
        $request = $_POST;
        $result = $this->validatePost($request);
        if( $result['success'] ){
            $result = $this->sync_post($request);
        }
        wp_send_json($result);
    }
    /**
     * convert post
     *
     * @param object $result
     * @return object $result after convert
     * @since 1.0
     * @package MicrojobEngine
     * @category void
     * @author JACK BUI
     */
    public function convertPost($result){
        $result->et_budget_text = mJobPriceFormat($result->et_budget);
        return $result;
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
            'msg'=> __('Success!', ET_DOMAIN)
        );
        return $result;
    }
    /**
     * get extra of a Microjob
     *
     * @param integer $extra_id
     * @param integer $mjob_id;
     * @return object $extra or false
     * @since 1.0
     * @package MicrojobEngine
     * @category void
     * @author JACK BUI
     */
    public function get_extra_of_mjob($extra_id, $mjob_id){
        global $ae_post_factory;
        $post_obj = $ae_post_factory->get('mjob_extra');
        $post = get_post($extra_id);
        if( !$post || $post->post_parent != $mjob_id ){
            return false;
        }
        $extra = $post_obj->convert($post);
        return $extra;
    }
    /**
     * filter query_args
     *
     * @param void
     * @return void
     * @since 1.0
     * @package MicrojobEngine
     * @category void
     * @author JACK BUI
     */
    public function filter_query_args($query_args){
        $query = $_REQUEST['query'];
        $args = array();
        if( isset($query['post_parent']) ) {
            $args = array(
                'post_type' => 'payment_history',
                'post_status' => 'publish'
            );
        }
        $query_args = wp_parse_args($args, $query_args);
        return $query_args;
    }
    /**
      * Description
      *
      * @param void
      * @return void
      * @since 1.4
      * @package MicrojobEngine
      * @category CREDZU
      * @author JACK BUI
      */
    public function create_payment_history($data, $profile, $path, $payment_check){
        $args = array(
            'post_title'=> sprintf(__('Payment for post: "%s"', ET_DOMAIN), $data['post_title']),
            'post_type'=>'payment_history',
            'post_status'=> 'pending'
        );
        $result = wp_insert_post($args);
        if( $result ){
            update_post_meta($result, 'mjob', $data);
            update_post_meta($result, 'pdf_path', $path);
            update_post_meta($result, 'type', 'company_to_credzu');
            update_post_meta($result, 'amount', $data['latest_amount']);
            update_post_meta($result, 'payment_check_number', formatCheckNumber($payment_check));
            update_option('payment_check_number', $payment_check);
            global $ae_post_factory;
            $p = get_post($result);
            $obj = $ae_post_factory->get('payment_history');
            $p = $obj->convert($p);
           do_action('payment_check_email', $profile->company_email, $p, array($path));
           $my_post = array(
              'ID'           => $p->mjob['ID'],
             'post_status'=> 'pending'
           );
            wp_update_post( $my_post );
            wp_send_json(array(
                'success'=> true,
                'msg'=> __('Success!', ET_DOMAIN),
                'data'=> $p
            ));
        }
        wp_send_json(array(
            'sucess'=> false,
            'msg'=> __('Failed!', ET_DOMAIN),
        ));
        exit;
    }

    /**
      * Description
      *
      * @param void
      * @return void
      * @since 1.4
      * @package MicrojobEngine
      * @category CREDZU
      * @author JACK BUI
      */
    public function create_client_payment_history($data, $profile, $path, $payment_check){
        $args = array(
            'post_title'=> sprintf(__('Payment for post: "%s"', ET_DOMAIN), $data->post_title),
            'post_type'=>'payment_history',
            'post_status'=> 'pending'
        );
        $result = wp_insert_post($args);
        var_dump($data);
        exit;
        if( $result ){
            update_post_meta($result, 'mjob', $data);
            update_post_meta($result, 'pdf_path', $path);
            update_post_meta($result, 'type', 'client_to_company');
            update_post_meta($result, 'amount', $data->amount);
            update_post_meta($result, 'client_payment_check_number', formatCheckNumber($payment_check));
            update_option('client_payment_check_number', $payment_check);
            global $ae_post_factory;
            $p = get_post($result);
            $obj = $ae_post_factory->get('payment_history');
            $p = $obj->convert($p);
           do_action('client_payment_check_email', $profile->business_email, $p, array($path));
           $my_post = array(
              'ID'           => $p->mjob->ID,
             'post_status'=> 'pending'
           );
            wp_update_post( $my_post );
            wp_send_json(array(
                'success'=> true,
                'msg'=> __('Success!', ET_DOMAIN),
                'data'=> $p
            ));
        }
        wp_send_json(array(
            'sucess'=> false,
            'msg'=> __('Failed!', ET_DOMAIN),
        ));
        exit;
    }
}
new credzuPaymentHistoryAction();
if( !function_exists('credzuPaymentHistoryAction') ){
    /**
      * Description
      *
      * @param void
      * @return void
      * @since 1.4
      * @package MicrojobEngine
      * @category CREDZU
      * @author JACK BUI
      */
    function credzuPaymentHistoryAction(){
        return credzuPaymentHistoryAction::getInstance();
    }
}
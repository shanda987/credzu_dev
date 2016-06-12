<?php
class mJobOrderDeliveryAction extends mJobPostAction{
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
    public  function __construct($post_type = 'order_delivery'){
        parent::__construct($post_type);
        $this->add_ajax('ae-fetch-order_delivery', 'fetch_post');
        $this->add_ajax('ae-order_delivery-sync', 'syncPost');
        $this->add_filter('ae_convert_order_delivery', 'convertPost');
        $this->ruler = array(
            'post_content',
            'post_parent'
        );

        $this->mail = mJobMailing::getInstance();
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
        global $user_ID, $ae_post_factory;
        $request = $_POST;
        $response = $this->validatePost($request);
        if( !$response['success'] ){
            wp_send_json($response);
            exit;
        }
        $request['post_title'] = sprintf(__('Delivery for: %s', ET_DOMAIN), $response['order_title']);
        $order = mJobAction()->get_mjob($request['post_parent']);
        $request['post_status'] = 'publish';
        $response = $this->sync_post($request);
        if( $response['success'] ){
            $my_post = array(
                'ID'           => $response['data']->post_parent,
                'post_status'=> 'finished',
            );
            wp_update_post( $my_post );
            $post_date = get_the_time('Y-m-d H:i:s', $response['data']->ID);
            update_post_meta($response['data']->post_parent, 'order_delivery_day', $post_date);
            // Send email order delivery
            if($response['data']->post_status == 'publish') {
                $this->mail->mJobDeliveryOrder($response['data']);
            }
            $msg = $response['data']->post_content;
            $msg_id = mJobAddOrderMessage($response['data']->post_parent, $user_ID, $response['data']->order_author, 'delivery_message', $msg );
            if( !empty($response['data']->et_carousels)){
                foreach($response['data']->et_carousels as $att){
                    $filename = get_attached_file( $att->ID );
                    $attachment = array(
                        'guid'           => $att->guid,
                        'post_mime_type' => $att->post_mime_type,
                        'post_title'     => $att->post_title,
                        'post_content'   => '',
                        'post_status'    => 'inherit'
                    );
                    $attach_id = wp_insert_attachment( $attachment, $filename, $msg_id );
                }
            }
            mJobAddOrderChangeLog($response['data']->post_parent, $user_ID, 'delivery_new', 'delivery' );
        }
        wp_send_json($response);
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
        global $user_ID;
        $result->author_name = get_the_author_meta('display_name', $result->post_author);
        $order = get_post($result->post_parent);
        $result->order_author = '';
        if( $order ){
            $result->order_author = $order->post_author;
        }
        if (current_user_can('manage_options') || $result->post_author == $user_ID || $result->order_author == $user_ID) {
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
        }
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
            'success'=> false,
            'msg'=> __('Failed!', ET_DOMAIN)
        );
        if( isset($data['post_parent']) ){
            $order = get_post($data['post_parent']);
            if( $order ){
                $args = array(
                    'post_type'=> 'order_delivery',
                    'post_parent'=> $data['post_parent']
                );
                $q = new WP_Query($args);
                if( $q->found_posts == 0 ){
                    return array(
                        'success'=> true,
                        'msg'=> __('Success!', ET_DOMAIN),
                        'order_title'=> $order->post_title
                    );
                }
            }
        }
        return $result;
    }
    /**
     * override filter query args
     *
     * @param void
     * @return void
     * @since 1.0
     * @package MicrojobEngine
     * @category void
     * @author JACK BUI
     */
    public function filter_query_args($query_args)
    {
        global $user_ID;
        return $query_args;
    }
}
new mJobOrderDeliveryAction();
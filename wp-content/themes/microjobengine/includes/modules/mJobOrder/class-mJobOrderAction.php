<?php
class mJobOrderAction extends mJobPostAction{
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
    public  function __construct($post_type = 'mjob_order'){
        parent::__construct($post_type);
        $this->add_ajax('ae-fetch-mjob_order', 'fetch_post');
        $this->add_ajax('ae-mjob_order-sync', 'syncPost');
        $this->add_filter('ae_convert_mjob_order', 'convertPost');
        $this->add_action('mjob_process_payment_action', 'mjobProcessPaymentAction', 10, 2);
        $this->add_action(  'transition_post_status',  'mjob_updated_order', 10, 3 );
        $this->add_action('ae_after_message', 'updateStatus');
        $this->add_filter('mjob_check_pending_account', 'checkPendingAccountOrder', 10, 2);
        $this->add_ajax('mjob-work-complete-confirm', 'mjobWorkCompleteConfirm');
        $this->add_ajax('mjob-reorder', 'mjobReorder');
        $this->add_ajax('mjob-get-pdf-data', 'mJobGetPdfData');
        $this->ruler = array(
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
        global $user_ID, $ae_post_factory, $wpdb;
        $order_obj = $ae_post_factory->get('mjob_order');;
        $request = $_POST;
        $response = $this->validatePost($request);
        if( !$response['success'] ){
            wp_send_json($response);
            exit;
        }
        if( !isset($request['post_author']) && !$user_ID ){
            $request['post_author'] = mjob_get_temp_user_id();
        }
        if( $user_ID ){
            $request['post_author'] = $user_ID;
        }
        if( isset($request['method']) && $request['method'] == 'create'){
            $request['post_status'] = 'publish';
        }
        if( !isset($request['updateAuthor']) || $request['updateAuthor'] != 1) {
            $temp = array();
            $temp_order = get_post($request['ID']);
            $temp_order = $order_obj->convert($temp_order);
            $requirement_files = $temp_order->requirement_files;
            if( isset($request['requirement_id']) && isset( $request['requirement_files'])){
                $a = $request['requirement_id'];
                $temp[$a] = $request['requirement_files'];
                $term = get_term_by('slug', $a, 'mjob_requirement');
                if( $term && !is_wp_error($term)){
                    $msg = $term->name;
                }
                else {
                    $msg = __('new file', ET_DOAMAIN);
                }
                $requirement_files = wp_parse_args($temp, $requirement_files);
                $request['requirement_files'] = $requirement_files;

                $msg_id = mJobAddOrderChangeLog($request['ID'], $user_ID, 'upload_document', $msg);
            }
            if(isset($request['need_upload_remove']) && isset($request['need_uploads'])){
                $se = array_search($request['need_upload_remove'], $request['need_uploads']);
                if( $se !== false && NULL != $se){
                    unset($request['need_uploads'][$se]);
                }
                $request['uploaded'] = wp_parse_args(array($request['need_upload_remove']), array($request['uploaded']));
            }
            if( isset($request['need_upload_add']) && isset($request['need_uploads'])){
                if( $request['mjob_author'] == $user_ID && ae_user_role($user_ID) == COMPANY ) {
                    $request['need_uploads'] = wp_parse_args(array($request['need_upload_add']), $request['need_uploads']);
                    $sr = array_search($request['need_upload_add'], (array)$request['uploaded']);
                    if ($sr !== false) {
                        unset($request['uploaded'][$sr]);
                    }
                    $request['need_uploads'] = array_values(array_unique($request['need_uploads']));
                    $request['uploaded'] = array_values(array_unique($request['uploaded']));
                    $request['need_uploads'] = serialize($request['need_uploads']);
                    $request['uploaded'] = serialize($request['uploaded']);
                    $re1 = $wpdb->query($wpdb->prepare("UPDATE $wpdb->postmeta  as mt SET mt.meta_value = %s WHERE mt.post_id = %s AND mt.meta_key=%s", $request['need_uploads'], $request['ID'], 'need_uploads'));
                    $re2 = $wpdb->query($wpdb->prepare("UPDATE $wpdb->postmeta  as mt SET mt.meta_value = %s WHERE mt.post_id = %s AND mt.meta_key=%s", $request['uploaded'], $request['ID'], 'uploaded'));
                    if (!is_wp_error($re1) && !is_wp_error($re2)) {
                        $ood = get_post($request['ID']);
                        $ood = $order_obj->convert($ood);
                        $response = array(
                            'success' => true,
                            'msg' => __('Successful!', ET_DOMAIN),
                            'data' => $ood
                        );
                    } else {
                        $response = array(
                            'success' => false,
                            'msg' => __('Failed!', ET_DOMAIN),
                        );
                    }
                    wp_send_json($response);
                }
            }
            if (isset($request['late']) && $request['late'] == '1' ) {
                if( $temp_order ) {
                    if( $temp_order->mjob_author == $user_ID ) {
                        $update_result = $wpdb->query($wpdb->prepare("UPDATE $wpdb->posts as P SET P.post_status = %s WHERE P.ID = %d", 'late', $request['ID']));
                        $temp_order->post_status = 'late';
                        $temp_order->status_text = __('LATE', ET_DOMAIN);
                        $temp_order->status_class = 'late-color';
                        $temp_order->status_text_color = 'late-text';
                        wp_send_json(array(
                            'success' => true,
                            'msg' => __("Successful update!", ET_DOMAIN),
                            'data'=> $temp_order
                        ) );

                    }
                }
            } else {
                if(isset($request['finished']) && $request['finished'] == '1') {
                    AE_WalletAction()->transferWorkingToAvailable($request['seller_id'], $request['ID'], $request['real_amount']);
                }
                $response = $this->sync_post($request);
                if ($response['success']) {
                    $result = $response['data'];
                    $mjob = mJobAction()->get_mjob($result->post_parent);
                    if (!$mjob) {
                        $response = array(
                            'success' => false,
                            'msg' => __("No mJob found for this order!", ET_DOMAIN)
                        );
                        wp_send_json($response);
                    }
                    $total = $mjob->et_budget;
//                    if (!empty($result->extra_ids)) {
//                        foreach ($result->extra_ids as $key => $value) {
//                            $extra = mJobExtraAction()->get_extra_of_mjob($value, $result->post_parent);
//                            if ($extra) {
//                                $total += $extra->et_budget;
//                            }
//                        }
//                    }
                    $currency = mjob_get_currency();
                    update_post_meta($result->ID, 'amount', $total);
                    update_post_meta($result->ID, 'real_amount', mJobRealPrice($total));
                    update_post_meta($result->ID, 'currency', $currency);
                    update_post_meta($result->ID, 'seller_id', $mjob->post_author);
                    update_post_meta($result->ID, 'buyer_id', $result->post_author);
                    $response['data'] = $order_obj->convert($result);
                    $response['data']->updateAuthor = false;
//                    if (!isset($request['noSetupPayment'])) {
//                        $response = $this->setupPayment($response['data']);
//                        $response['data']['updateAuthor'] = false;
//                    }
                }
            }
        }
        else{
            $session	=	et_read_session();
            $data['updateAuthor'] = false;
            $data['ACK'] = false;
            $response = array(
                'success' => false,
                'msg' => __('Failed!', ET_DOMAIN),
                'data' => $data
            );
            if( isset($session['order_id']) && !empty($session['order_id']) && $user_ID ){
                $result = $wpdb->query($wpdb->prepare("UPDATE $wpdb->posts as P SET P.post_author = %d WHERE P.ID = %d", $user_ID, $session['order_id']));
                if( false !== $result ) {
                    $post = get_post($session['order_id']);
                    if ($post) {
                        $data = $order_obj->convert($post);
                        $data->updateAuthor = true;
                        $data->ACK = true;
                        $response = array(
                            'success' => true,
                            'msg' => __('Successful!', ET_DOMAIN),
                            'data' => $data
                        );

                        do_action('mjob_update_order_author', $data);
                    }
                }
            }
            et_destroy_session();
        }
        wp_send_json($response);
    }
    /*
     * override checkPendingAccount
     *
     */
    public function checkPendingAccountOrder($result, $request){
        if( isset($request['post_type']) && ($request['post_type'] == 'mjob_order' || $request['post_type'] == 'ae_message') ) {
            if( $request['post_type'] != 'ae_message'){
                return array(
                    'success' => true,
                    'msg' => __('Success', ET_DOMAIN)
                );
            }
            if( $request['post_type'] == 'ae_message' && (isset($request['type']) && $request['type'] == 'dispute') ) {
                return array(
                    'success' => true,
                    'msg' => __('Success', ET_DOMAIN)
                );
            }
        }
        return $result;
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
        global $ae_post_factory, $user_ID;
        $ae_user = AE_Users::get_instance();
        $auth = get_userdata($result->post_author);
        $auth = $ae_user->convert($auth);
        $result->author_name = $auth->initial_display_name;
        $result->mjob_order_author_url = get_author_posts_url($result->post_author);
        $mjob_obj = $ae_post_factory->get('mjob_post');
        $mjob = get_post($result->post_parent);
        $mjob = $mjob_obj->convert($mjob);
        $author = get_userdata($mjob->post_author);
        $result->mjob_author = $mjob->post_author;
        $result->mjob_category = '';
        if( isset($mjob->tax_input['mjob_category']['0']->name)){
            $result->mjob_category = $mjob->tax_input['mjob_category']['0']->name;
        }
        $result->mjob = $mjob;
        $result->mjob_author_name = $author->initial_display_name;
        $result->mjob_author_url = get_author_posts_url($mjob->post_author);
        $result->mjob_content = $mjob->post_content;
        $result->mjob_price_text = mJobPriceFormat($mjob->et_budget);
        $result->mjob_id = $mjob->ID;
        $result->mjob_price = $mjob->et_budget;
        $result->mjob_time_delivery = $mjob->time_delivery;
        $result->order_date = sprintf( _x( '%s ago', '%s = human-readable time difference', ET_DOMAIN ), human_time_diff( strtotime($result->post_date), time() ));
        $result->amount_text = mJobPriceFormat($result->amount);
        $date_format = get_option('date_format');
        $result->modified_date = the_modified_date( $date_format, '', '', false );
        if (current_user_can('manage_options') || $result->post_author == $user_ID || $result->mjob_author == $user_ID) {
            $children = get_children(array(
                'numberposts' => 15,
                'order' => 'ASC',
                'post_parent' => $result->ID,
                'post_type' => 'order_delivery'
            ));

            $order_delivery = $ae_post_factory->get('order_delivery');
            $result->order_delivery = array();
            foreach ($children as $key => $value) {
                $value = $order_delivery->convert($value);
                $result->order_delivery[] = $value;
            }
            $children = get_children(array(
                'numberposts' => 15,
                'order' => 'ASC',
                'post_parent' => $result->ID,
                'post_type' => 'ae_message'
            ));

            $ae_message = $ae_post_factory->get('ae_message');
            $result->ae_message = array();
            foreach ($children as $key => $value) {
                $value = $ae_message->convert($value);
                $result->ae_message[] = $value;
            }
        }
        switch($result->post_status){
            case 'publish':
                $result->status_text = __('PENDING', ET_DOMAIN);
                $result->status_class = 'pending-color';
                $result->status_text_color = 'active-text';
                break;
            case 'pending':
                $result->status_text = __('PENDING', ET_DOMAIN);
                $result->status_class = 'pending-color';
                $result->status_text_color = 'pending-text';
                break;
            case 'processing':
                $result->status_text = __('PROCESSING', ET_DOMAIN);
                $result->status_class = 'active-color';
                $result->status_text_color = 'active-text';
                break;
            case 'verification':
                $result->status_text = __('VERIFICATION', ET_DOMAIN);
                $result->status_class = 'finished-color';
                $result->status_text_color = 'finished-text';
                break;
            case 'late':
                $result->status_text = __('LATE', ET_DOMAIN);
                $result->status_class = 'late-color';
                $result->status_text_color = 'late-text';
                break;
            case 'delivery':
                $result->status_text = __('FINISHED', ET_DOMAIN);
                $result->status_class = 'disputing-color';
                $result->status_text_color = 'delivered-text';
                break;
            case 'disputed':
                $result->status_text = __('RESOLVED', ET_DOMAIN);
                $result->status_class = 'disputed-color';
                $result->status_text_color = 'disputed-text';
                break;
            case 'disputing':
                $result->status_text = __('DISPUTING', ET_DOMAIN);
                $result->status_class = 'disputing-color';
                $result->status_text_color = 'disputing-text';
                break;
            case 'finished':
                $result->status_text = __('FINISHED', ET_DOMAIN);
                $result->status_class = 'disputing-color';
                $result->status_text_color = 'finished-text';
                break;
            case 'draft':
                $result->status_text = __('DRAFT', ET_DOMAIN);
                $result->status_class = 'draft-color';
                $result->status_text_color = 'draft-text';
                break;
            default:
//                $result->status_text = __('ACTIVE', ET_DOMAIN);
//                $result->status_class = 'active-color';
//                $result->status_text_color = 'active-text';
                break;
        }

        if(!isset($result->real_amount) || empty($result->real_amount)) {
            $result->real_amount = mJobRealPrice($result->amount);
        }
        $result->doc_html =  $this->mjob_get_requirement_template($result->requirement_files);
        if( empty($result->need_uploads)){
            add_post_meta($result->ID, 'need_uploads', ' ');
        }
        if(empty($result->uploaded) ){
            add_post_meta($result->ID, 'uploaded', ' ');
        }
//        echo '<pre>';
//        var_dump($result->need_uploads);
//        var_dump($result->uploaded);
//        exit;
//        update_post_meta($result->ID, 'need_uploads', array('billing-information', 'contact-information'));
        $type = 'mjob_review';
        global $current_user;
        $comment = get_comments(array(
            'status' => 'approve',
            'type' => $type,
            'post_id' => $result->mjob->ID,
            'author_email' => $current_user->user_email,
            'meta_key' => 'order_id',
            'meta_value' => $result->ID
        ));
        $result->can_review = true;
        if( !empty($comment)){
            $result->can_review = false;
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
        global $user_ID;
        $result = array(
            'success'=> true,
            'msg'=> __('Successful!', ET_DOMAIN)
        );
        if( isset($data['late']) && $data['late'] == 1 ){
            if( !isset($data['post_status']) || $data['post_status'] != 'publish' ){
                $result = array(
                    'success'=> false,
                    'msg'=> __('Failed!', ET_DOMAIN)
                );
                return $result;
            }
        }
            if( $data['method'] == 'create') {
                $is_c = $this->user_can_create_order($data['post_parent'], $user_ID);
                if (!$is_c['success']) {
                    $result = array(
                        'success' => false,
                        'msg' => __('You already created this order!', ET_DOMAIN)
                    );
                    return $result;
                }
            }
        return $result;
    }
    /**
     * setup payment after save draft order
     *
     * @param object order
     * @return array $response
     * @since 1.0
     * @package MicrojobEngine
     * @category void
     * @author JACK BUI
     */
    public function setupPayment($order){
        $response = array(
            'success'=> false,
            'msg'=> __('Payment failed!', ET_DOMAIN)
        );
        // write session
        et_write_session('order_id', $order->ID);
        et_write_session('processType', 'buy');
        $arg = apply_filters('ae_payment_links', array(
            'return' => et_get_page_link('process-payment') ,
            'cancel' => et_get_page_link('process-payment')
        ));
        /**
         * process payment
         */
        $paymentType_raw = $order->payment_type;
        $paymentType = strtoupper($order->payment_type);
        /**
         * factory create payment visitor
         */
        $order_data = array(
            'payer' => $order->post_author,
            'total' => '',
            'status' => 'draft',
            'payment' => $paymentType,
            'paid_date' => '',
            'post_parent' => $order->post_parent,
            'ID'=> $order->ID,
            'amount'=> $order->amount
        );
        $order_temp = new mJobOrder($order_data);
        $order_temp->add_product($order);
        $order = $order_temp;
        $visitor = AE_Payment_Factory::createPaymentVisitor($paymentType, $order, $paymentType_raw);
        // setup visitor setting
        $visitor->set_settings($arg);
        // accept visitor process payment
        $nvp = $order->accept($visitor);
        if ($nvp['ACK']) {
            $response = array(
                'success' => $nvp['ACK'],
                'data' => $nvp,
                'paymentType' => $paymentType
            );
        } else {
            $response = array(
                'success' => false,
                'paymentType' => $paymentType,
                'msg' => __("Invalid payment gateway!", ET_DOMAIN)
            );
        }
        /**
         * filter $response send to client after process payment
         *
         * @param Array $response
         * @param String $paymentType  The payment gateway user select
         * @param Array $order The order data
         *
         * @package  AE Payment
         * @category payment
         *
         * @since  1.0
         * @author  Dakachi
         */
        $response = apply_filters('mjob_setup_payment', $response, $paymentType, $order);
        return $response;
    }
    /**
     * ae_process_payment function process payment return to check payment amount, update order
     * @use AE_Order , ET_NOPAYOrder, AE_Payment_Factory
     * @param string $payment_type the string of payment type such as paypal, 2checkout , stripe
     * @param Array $data
     *  -args $order_id : current order_id on process
     *  -args $ad_id : current ad id user submit
     * @return Array $payment_return
     *
     * @package AE Payment
     * @category payment
     *
     * @since 1.0
     * @author  Dakachi
     *
     */
    public function process_payment($payment_type, $data) {
        $payment_return = array(
            'ACK' => false
        );
        if ($payment_type) {

            // check order id
            if (isset($data['order_id'])) $order = new mJobOrder($data['order_id']);
            else $order = new ET_NOPAYOrder();

            // call a visitor process order base on payment type
            $visitor = AE_Payment_Factory::createPaymentVisitor(strtoupper($payment_type), $order, $payment_type);
            $payment_return = $order->accept($visitor);

            $data['order'] = $order;
            $data['payment_type'] = $payment_type;

            /**
             * filter payment return
             * @param Array $payment_return
             * @param Array $data -order : Order data, payment_type ...
             * @since 1.0
             */
            $payment_return = apply_filters('ae_process_payment', $payment_return, $data);
            $payment_return['order'] = $data['order'];

            /**
             * do an action after payment
             * @param Array $payment_return
             * @param Array $data -order : Order data, payment_type ...
             * @since 1.0
             */
            do_action('mjob_process_payment_action', $payment_return, $data);
        }
        return $payment_return;
    }
    /**
     * process payment action
     *
     * @param array $payment_return
     * @param $_SESTION $data
     * @return void
     * @since 1.0
     * @package MicrojobEngine
     * @category void
     * @author JACK BUI
     */
    public function mjobProcessPaymentAction($payment_return, $data){
        if( $payment_return['ACK'] ){
            $args = array(
                'post_type'=> 'mjob_order',
                'ID'=> $data['order_id']
            );
            if( strtoupper($data['payment_type']) == 'CASH'){
                $args['post_status'] = 'pending';
            }
            else{
                $args['post_status'] = 'publish';
                $post_parent = wp_get_post_parent_id( $data['order_id'] );
                global $ae_post_factory;
                $order_obj = $ae_post_factory->get('mjob_order');
                $post = get_post($post_parent);
                $order = get_post($data['order_id']);
                if(  $order ){
                    $order = $order_obj->convert($order);
                    if( !isset($order->paid) || !$order->paid ) {
                        $user_wallet = AE_WalletAction()->getUserWallet($post->post_author, "working");
                        $user_wallet->balance += $order->real_amount;
                        AE_WalletAction()->setUserWallet($post->post_author, $user_wallet, "working");
                        update_post_meta($data['order_id'], 'paid', true);
                    }
                }
            }
            wp_update_post($args);
        }
        return $payment_return;
    }
    /**
     * update order
     *
     * @param integer $post_id
     * @return void
     * @since 1.0
     * @package MicrojobEngine
     * @category void
     * @author JACK BUI
     */
    public function mjob_updated_order($new_status, $old_status, $post ){
        if( $post->post_type == 'mjob_order' ){
            if( $new_status == 'publish' && $old_status == 'pending'){
                global $ae_post_factory;
                $order_obj = $ae_post_factory->get('mjob_order');
                $mjob = get_post($post->post_parent);
                if( $post ){
                    $post = $order_obj->convert($post);
                    if( !$post->paid ) {
                        $user_wallet = AE_WalletAction()->getUserWallet($mjob->post_author, "working");
                        $user_wallet->balance += $post->real_amount;
                        AE_WalletAction()->setUserWallet($mjob->post_author, $user_wallet, "working");
                        update_post_meta($post->ID, 'paid', true);
                    }
                }
                $mail = mJobMailing::getInstance();
                $mail->mJobNewOrder($post);
            }
        }
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
        $query = $_REQUEST['query'];

        if (isset($query['meta_key'])) {
            $query_args['meta_key'] = $query['meta_key'];
            if (isset($query['meta_value'])) {
                $query_args['meta_value'] = $query['meta_value'];
            }

            if (isset($query['meta_compare'])) {
                $query_args['meta_compare'] = $query['meta_compare'];
            }
        }

        $query_args['post_type'] = 'mjob_order';

        if(!isset($query['is_task']) || $query['is_task'] == false) {
            $query_args['author'] = $user_ID;
        }

        $query_args['post_status'] = 'any';
        if( isset($query['post_status']) ){
            if( $query['post_status'] == 'finished' ){
                $query['post_status'] = array('delivery', 'finished');
            }
            $query_args['post_status'] = $query['post_status'];
        }

        // If post_status is all
        if(isset($query['is_task']) && $query['is_task'] == true && empty($query['post_status'])) {
            $query_args['post_status'] = array(
                'publish',
                'late',
                'delivery',
                'disputing',
                'disputed',
                'finished'
            );
        }
        return $query_args;
    }
    /**
     * update status
     *
     * @param array $response
     * @return void
     * @since 1.0
     * @package MicrojobEngine
     * @category void
     * @author JACK BUI
     */
    public function updateStatus($response){
        if( $response['success'] ){
            if( $response['data']->type == 'dispute'){
                $my_post = array(
                    'ID'           => $response['data']->post_parent,
                    'post_status'=> 'disputing'
                );
                wp_update_post( $my_post );
            }
        }
    }
    /**
      * get document template
      *
      * @param void
      * @return void
      * @since 1.4
      * @package MicrojobEngine
      * @category CREDZU
      * @author JACK BUI
      */
    public function mjob_get_requirement_template($requirement_files){
        $html = '';
        if( isset($requirement_files) && !empty($requirement_files) ){
            foreach( $requirement_files as $key=> $files) {
                $term = get_term_by('slug', $key, 'mjob_requirement');
                if (!empty($files)):
                    $i = 0;
                    $tx = '';
                    foreach ($files as $file):
                        $f = get_post($file);
                        if ($i > 0):
                            $tx = '_' . $i;
                        endif;
                        $html .= '<li class="col-lg-6 col-md-6 col-xs-12 item-requirement">';
                        $html .= '<a  href="'.et_get_page_link('simple-download').'?id='.$f->ID.'" data-name="'.$term->name.$tx.' : '.date('d/m/Y', strtotime($f->post_date)).'" class="show-requirement-docs">';
                        $html .= '<div class="doc-icon"> <i class="fa fa-file-pdf-o" aria-hidden="true"></i></div>';
                        $html .= '<div class="doc-name">'.$term->name.$tx.'</div>';
                        $html .= '<div class="doc-time">'.date('d/m/Y', strtotime($f->post_date)).'</div>';
                        $html .= '</li>';
                        $i++;
                    endforeach;
                endif;
            }
        }
        return $html;
    }
    /**
      * update order status
      *
      * @param object $order
      * @return void
      * @since 1.4
      * @package MicrojobEngine
      * @category CREDZU
      * @author JACK BUI
      */
    public function updateOrderStatus($order_id, $new_status = ''){
        global $wpdb, $ae_post_factory;
        $order_obj = $ae_post_factory->get('mjob_order');
        $order = get_post($order_id);
        $order = $order_obj->convert($order);
        $profile = mJobProfileAction()->getProfile($order->post_author);
        $profile1 = mJobProfileAction()->getProfile($order->mjob_author);
        if( !empty($order)){
            if( empty($new_status) ){
                $new_status = $order->post_status;
            }
            $old_status = $order->status_text;
            $update_result = $wpdb->query($wpdb->prepare("UPDATE $wpdb->posts as P SET P.post_status = %s WHERE P.ID = %d", $new_status, $order->ID));
            if( $new_status == 'verification' || $new_status == 'finished' || $new_status == 'processing'){
                if ($new_status == 'verification') {
                    $new_status = 'virification';
                }
                if ($new_status == 'delivery') {
                    $new_status = 'FINISHED';
                }
                if ($new_status == 'processing') {
                    $first = get_post_meta($order->post_parent, 'first_order', true);
                    if( !empty($first) ){
                        if( !in_array($order->ID, (array)$first)) {
                            $my_posts = array(
                                'ID' => $order->post_parent,
                                'post_status' => 'inactive'
                            );
                            wp_update_post($my_posts);
                            array_push($first, $order->ID);
                            $first = array_unique($first);
                            update_post_meta($order->post_parent, 'first_order', $first);
                        }
                    }
                    else{
                        array_push($first, $order->ID);
                        $first = array_unique($first);
                        update_post_meta($order->post_parent, 'first_order', $first);
                        $my_posts = array(
                            'ID' => $order->post_parent,
                            'post_status' => 'inactive'
                        );
                        wp_update_post($my_posts);
                    }
                    $new_status = 'processing';
                }
            }
            else {
                $new_status = 'pending';
            }
            $new_status = strtoupper($new_status);
            //if( $old_status != $new_status ) {
                do_action('changing_order_status_email', $profile1->company_email, $profile->business_email, $old_status, $new_status);
            //}
            return $update_result;
        }
        return false;
    }
    /**
      * confirm work complete
      *
      * @param void
      * @return void
      * @since 1.4
      * @package MicrojobEngine
      * @category CREDZU
      * @author JACK BUI
      */
    public function mjobWorkCompleteConfirm(){
        global $ae_post_factory;
        $order_object = $ae_post_factory->get('mjob_order');
        $request = $_REQUEST;
        if( isset($request['order_id']) && !empty($request['order_id'])){
            $result = $this->updateOrderStatus($request['order_id'], 'verification');
            if( $result && !is_wp_error($result)){
                $order = get_post($request['order_id']);
                $order = $order_object->convert($order);
                if( $order->post_status == 'processing' ){
                    do_action('client_do_checkout', $order);
                }
                wp_send_json(array(
                    'success'=> true,
                    'msg'=> __('Confirm success!', ET_DOMAIN)
                ));
            }
            wp_send_json(array(
                'success'=> false,
                'msg'=> __('Failed!', ET_DOMAIN)
            ));
        }
        wp_send_json(array(
            'success'=> false,
            'msg'=> __('Failed!', ET_DOMAIN)
        ));
    }
    /**
      * reorder
      *
      * @param void
      * @return void
      * @since 1.4
      * @package MicrojobEngine
      * @category CREDZU
      * @author JACK BUI
      */
    public function mjobReorder(){
        $request = $_REQUEST;
        if( isset($request['order_id']) && !empty($request['order_id'])){
            $result = $this->updateOrderStatus($request['order_id'], 'processing');
            if( $result && !is_wp_error($result)){
                wp_send_json(array(
                    'success'=> true,
                    'msg'=> __('Confirm success!', ET_DOMAIN)
                ));
            }
            wp_send_json(array(
                'success'=> false,
                'msg'=> __('Failed!', ET_DOMAIN)
            ));
        }
        wp_send_json(array(
            'success'=> false,
            'msg'=> __('Failed!', ET_DOMAIN)
        ));
    }
    /**
      * check if user can create a order
      *
      * @param void
      * @return void
      * @since 1.4
      * @package MicrojobEngine
      * @category CREDZU
      * @author JACK BUI
      */
    public function user_can_create_order($mjob_id, $user_id){
        global $ae_post_factory;
        $order = $ae_post_factory->get('mjob_order');
        $args = array(
            'post_type'=>'mjob_order',
            'post_parent'=> $mjob_id,
            'author'=> $user_id,
            'posts_per_page'=> 3
        );
        $posts = get_posts($args);
        if( $posts && !empty($posts) ) {
            return array('success'=>false, 'data'=>$order->convert($posts['0']));
        }
        return array('success'=> true);

    }
    /**
      * get pdf content
      *
      * @param void
      * @return void
      * @since 1.4
      * @package MicrojobEngine
      * @category CREDZU
      * @author JACK BUI
      */
    public function mJobGetPdfData(){
        if( isset( $_REQUEST['id']) ){
            $id = $_REQUEST['id'];
            $file = get_attached_file($id);
//            $file_name = basename( $file );
//            $fp = fopen($file, 'rb');
//            header("Content-Type: application/octet-stream");
//            header("Content-Disposition: attachment; filename=$file_name");
//            header("Content-Length: " . filesize($file));
            $file = $this->pdf2text($file);
//            fclose($fp);
            wp_send_json(array(
                'success'=> true,
                'data'=>$file));
        }
    }
    public function pdf2text($filename) {

        // Read the data from pdf file
        $infile = @file_get_contents($filename, FILE_BINARY);
        if (empty($infile))
            return "";

        // Get all text data.
        $transformations = array();
        $texts = array();

        // Get the list of all objects.
        preg_match_all("#obj(.*)endobj#ismU", $infile, $objects);
        $objects = @$objects[1];
        // Select objects with streams.
        for ($i = 0; $i < count($objects); $i++) {
            $currentObject = $objects[$i];

            // Check if an object includes data stream.
            if (preg_match("#stream(.*)endstream#ismU", $currentObject, $stream)) {
                $stream = ltrim($stream[1]);
                // Check object parameters and look for text data.
                $options = $this->getObjectOptions($currentObject);
                if (!(empty($options["Length1"]) && empty($options["Type"]) && empty($options["Subtype"])))
                    continue;

                // So, we have text data. Decode it.
                $data = $this->getDecodedStream($stream, $options);
                if (strlen($data)) {
                    if (preg_match_all("#BT(.*)ET#ismU", $data, $textContainers)) {
                        $textContainers = @$textContainers[1];
                        $this->getDirtyTexts($texts, $textContainers);
                    } else
                        $this->getCharTransformations($transformations, $data);
                }
            }

        }

        // Analyze text blocks taking into account character transformations and return results.
        return $this->getTextUsingTransformations($texts, $transformations);
    }
    function decodeAsciiHex($input) {
        $output = "";

        $isOdd = true;
        $isComment = false;

        for($i = 0, $codeHigh = -1; $i < strlen($input) && $input[$i] != '>'; $i++) {
            $c = $input[$i];

            if($isComment) {
                if ($c == '\r' || $c == '\n')
                    $isComment = false;
                continue;
            }

            switch($c) {
                case '\0': case '\t': case '\r': case '\f': case '\n': case ' ': break;
                case '%':
                    $isComment = true;
                    break;

                default:
                    $code = hexdec($c);
                    if($code === 0 && $c != '0')
                        return "";

                    if($isOdd)
                        $codeHigh = $code;
                    else
                        $output .= chr($codeHigh * 16 + $code);

                    $isOdd = !$isOdd;
                    break;
            }
        }

        if($input[$i] != '>')
            return "";

        if($isOdd)
            $output .= chr($codeHigh * 16);

        return $output;
    }
    function decodeAscii85($input) {
        $output = "";

        $isComment = false;
        $ords = array();

        for($i = 0, $state = 0; $i < strlen($input) && $input[$i] != '~'; $i++) {
            $c = $input[$i];

            if($isComment) {
                if ($c == '\r' || $c == '\n')
                    $isComment = false;
                continue;
            }

            if ($c == '\0' || $c == '\t' || $c == '\r' || $c == '\f' || $c == '\n' || $c == ' ')
                continue;
            if ($c == '%') {
                $isComment = true;
                continue;
            }
            if ($c == 'z' && $state === 0) {
                $output .= str_repeat(chr(0), 4);
                continue;
            }
            if ($c < '!' || $c > 'u')
                return "";

            $code = ord($input[$i]) & 0xff;
            $ords[$state++] = $code - ord('!');

            if ($state == 5) {
                $state = 0;
                for ($sum = 0, $j = 0; $j < 5; $j++)
                    $sum = $sum * 85 + $ords[$j];
                for ($j = 3; $j >= 0; $j--)
                    $output .= chr($sum >> ($j * 8));
            }
        }
        if ($state === 1)
            return "";
        elseif ($state > 1) {
            for ($i = 0, $sum = 0; $i < $state; $i++)
                $sum += ($ords[$i] + ($i == $state - 1)) * pow(85, 4 - $i);
            for ($i = 0; $i < $state - 1; $i++)
                $ouput .= chr($sum >> ((3 - $i) * 8));
        }

        return $output;
    }
    function decodeFlate($input) {
        return @gzuncompress($input);
    }

    function getObjectOptions($object) {
        $options = array();
        if (preg_match("#<<(.*)>>#ismU", $object, $options)) {
            $options = explode("/", $options[1]);
            @array_shift($options);

            $o = array();
            for ($j = 0; $j < @count($options); $j++) {
                $options[$j] = preg_replace("#\s+#", " ", trim($options[$j]));
                if (strpos($options[$j], " ") !== false) {
                    $parts = explode(" ", $options[$j]);
                    $o[$parts[0]] = $parts[1];
                } else
                    $o[$options[$j]] = true;
            }
            $options = $o;
            unset($o);
        }

        return $options;
    }
    function getDecodedStream($stream, $options) {
        $data = "";
        if (empty($options["Filter"]))
            $data = $stream;
        else {
            $length = !empty($options["Length"]) ? $options["Length"] : strlen($stream);
            $_stream = substr($stream, 0, $length);
            foreach ($options as $key => $value) {
                if ($key == "ASCIIHexDecode")
                    $_stream = $this->decodeAsciiHex($_stream);
                if ($key == "ASCII85Decode")
                    $_stream = $this->decodeAscii85($_stream);
                if ($key == "FlateDecode")
                    $_stream = $this->decodeFlate($_stream);
            }
            $data = $_stream;
        }
        return $data;
    }
    function getDirtyTexts(&$texts, $textContainers) {
        for ($j = 0; $j < count($textContainers); $j++) {
            if (preg_match_all("#\[(.*)\]\s*TJ#ismU", $textContainers[$j], $parts))
                $texts = array_merge($texts, @$parts[1]);
            elseif(preg_match_all("#Td\s*(\(.*\))\s*Tj#ismU", $textContainers[$j], $parts))
                $texts = array_merge($texts, @$parts[1]);
        }
    }
    function getCharTransformations(&$transformations, $stream) {
        preg_match_all("#([0-9]+)\s+beginbfchar(.*)endbfchar#ismU", $stream, $chars, PREG_SET_ORDER);
        preg_match_all("#([0-9]+)\s+beginbfrange(.*)endbfrange#ismU", $stream, $ranges, PREG_SET_ORDER);

        for ($j = 0; $j < count($chars); $j++) {
            $count = $chars[$j][1];
            $current = explode("\n", trim($chars[$j][2]));
            for ($k = 0; $k < $count && $k < count($current); $k++) {
                if (preg_match("#<([0-9a-f]{2,4})>\s+<([0-9a-f]{4,512})>#is", trim($current[$k]), $map))
                    $transformations[str_pad($map[1], 4, "0")] = $map[2];
            }
        }
        for ($j = 0; $j < count($ranges); $j++) {
            $count = $ranges[$j][1];
            $current = explode("\n", trim($ranges[$j][2]));
            for ($k = 0; $k < $count && $k < count($current); $k++) {
                if (preg_match("#<([0-9a-f]{4})>\s+<([0-9a-f]{4})>\s+<([0-9a-f]{4})>#is", trim($current[$k]), $map)) {
                    $from = hexdec($map[1]);
                    $to = hexdec($map[2]);
                    $_from = hexdec($map[3]);

                    for ($m = $from, $n = 0; $m <= $to; $m++, $n++)
                        $transformations[sprintf("%04X", $m)] = sprintf("%04X", $_from + $n);
                } elseif (preg_match("#<([0-9a-f]{4})>\s+<([0-9a-f]{4})>\s+\[(.*)\]#ismU", trim($current[$k]), $map)) {
                    $from = hexdec($map[1]);
                    $to = hexdec($map[2]);
                    $parts = preg_split("#\s+#", trim($map[3]));

                    for ($m = $from, $n = 0; $m <= $to && $n < count($parts); $m++, $n++)
                        $transformations[sprintf("%04X", $m)] = sprintf("%04X", hexdec($parts[$n]));
                }
            }
        }
    }
    function getTextUsingTransformations($texts, $transformations) {
        $document = "";
        for ($i = 0; $i < count($texts); $i++) {
            $isHex = false;
            $isPlain = false;

            $hex = "";
            $plain = "";
            for ($j = 0; $j < strlen($texts[$i]); $j++) {
                $c = $texts[$i][$j];
                switch($c) {
                    case "<":
                        $hex = "";
                        $isHex = true;
                        break;
                    case ">":
                        $hexs = str_split($hex, 4);
                        for ($k = 0; $k < count($hexs); $k++) {
                            $chex = str_pad($hexs[$k], 4, "0");
                            if (isset($transformations[$chex]))
                                $chex = $transformations[$chex];
                            $document .= html_entity_decode("&#x".$chex.";");
                        }
                        $isHex = false;
                        break;
                    case "(":
                        $plain = "";
                        $isPlain = true;
                        break;
                    case ")":
                        $document .= $plain;
                        $isPlain = false;
                        break;
                    case "\\":
                        $c2 = $texts[$i][$j + 1];
                        if (in_array($c2, array("\\", "(", ")"))) $plain .= $c2;
                        elseif ($c2 == "n") $plain .= '\n';
                        elseif ($c2 == "r") $plain .= '\r';
                        elseif ($c2 == "t") $plain .= '\t';
                        elseif ($c2 == "b") $plain .= '\b';
                        elseif ($c2 == "f") $plain .= '\f';
                        elseif ($c2 >= '0' && $c2 <= '9') {
                            $oct = preg_replace("#[^0-9]#", "", substr($texts[$i], $j + 1, 3));
                            $j += strlen($oct) - 1;
                            $plain .= html_entity_decode("&#".octdec($oct).";");
                        }
                        $j++;
                        break;

                    default:
                        if ($isHex)
                            $hex .= $c;
                        if ($isPlain)
                            $plain .= $c;
                        break;
                }
            }
            $document .= "\n";
        }

        return $document;
    }

}
new mJobOrderAction();
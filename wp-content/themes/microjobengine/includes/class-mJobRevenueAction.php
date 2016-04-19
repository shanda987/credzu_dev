<?php
class mJobRevenueAction extends AE_Base
{
    public static $instance;

    static function getInstance()
    {
        if (self::$instance == null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    function __construct() {
        define('MIN_WITHDRAW', ae_get_option('minimum_withdraw', 15));

        $this->mail = mJobMailing::getInstance();
        $this->secure_code_request_time = 2; // mins
        $this->add_ajax('mjob_revenue_sync', 'mJobRevenueSync');
        $this->add_ajax('mjob_withdraw_sync', 'mJobWithdrawSync');
    }

    /**
     * All revenue actions
     * @param void
     * @return void
     * @since 1.0
     * @package MicrojobEngine
     * @category Revenues
     * @author Tat Thien
     */
    public function mJobRevenueSync() {
        global $user_ID;
        $request = $_REQUEST;

        if(empty($user_ID)) {
            wp_send_json(array(
                'success' => false,
                'msg' => __('Invalid user.', ET_DOMAIN),
            ));
        }

        if(!wp_verify_nonce($request['_wpnonce'], 'withdraw_action')) {
            wp_send_json(array(
                'success' => false,
                'msg' => __('Don\'t try to hack.', ET_DOMAIN),
            ));
        }

        if(isset($request['do_action'])) {
            switch($request['do_action']) {
                case 'request_secure_code':
                    $resp = $this->mJobRequestSecureCode($user_ID);
                    break;
            }

            wp_send_json($resp);
        }
    }

    /**
     * Do withdraw
     * @param void
     * @return void
     * @since 1.0
     * @package MicrojobEngine
     * @category Revenues
     * @author Tat Thien
     */
    public function mJobWithdrawSync() {
        global $user_ID, $current_user;
        $request = $_REQUEST;
        $default = array(
            'account_type'=> '',
            'amount'=> '',
            'secure_code'=> '',
            '_wpnonce' => ''
        );
        $request = wp_parse_args($request, $default);

        // Check user
        if(empty($user_ID)) {
            wp_send_json(array(
                'success' => false,
                'msg' => __('Invalid user.', ET_DOMAIN),
            ));
        }

        // Check user active
        if(!mJobUserActivate($user_ID)) {
            wp_send_json(array(
                'success' => false,
                'msg' => __('Your account is pending. You have to activate your account to continue this step!', ET_DOMAIN)
            ));
        }

        // Check nonce
        if(!wp_verify_nonce($request['_wpnonce'], 'withdraw_action')) {
            wp_send_json(array(
                'success' => false,
                'msg' => __('Don\'t try to hack.', ET_DOMAIN),
            ));
        }

        // Check balance
        $wallet = ae_credit_convert_wallet($request['amount']);
        $result = AE_WalletAction()->checkBalance($user_ID, $wallet );
        if( $result < 0 ){
            wp_send_json(array(
                'success'=> false,
                'msg'=> __("You don't have enough money in your wallet!", ET_DOMAIN)
            ));
        }

        // Check valid secure code
        $secure_code = AE_WalletAction()->getSecureCode($user_ID);
        if(empty($secure_code)) {
            wp_send_json(array(
                'success'=> false,
                'msg'=> __('You don\'t have a secure code. Please request one!', ET_DOMAIN)
            ));
        }

        // Check user payment
        $payment_info = mJobUserAction()->mJobCheckPaymentInfo($user_ID, $request['account_type']);
        if($payment_info == false) {
            $resp = array(
                'success'=> false,
            );
            // Check setup PayPal account
            if($request['account_type'] == 'paypal') {
                $resp['msg'] = sprintf(__('Please set up your PayPal account <a href="%s" style="text-decoration: underline;">here</a>!', ET_DOMAIN), et_get_page_link('payment-method'));
                wp_send_json($resp);
            } else { // Check setup bank account
                $resp['msg'] = sprintf(__('Please set up your bank account <a href="%s" style="text-decoration: underline;">here</a>!', ET_DOMAIN), et_get_page_link('payment-method'));
                wp_send_json($resp);
            }
        }

        $result = AE_WalletAction()->checkSecureCode($user_ID, $request['secure_code']);
        if(!$result){
            wp_send_json(array(
                'success'=> false,
                'msg'=> __('Please enter a valid secure code!', ET_DOMAIN)
            ));
        }

        // Check empty amount
        if(empty($request['amount'])){
            wp_send_json(array(
                'success'=> false,
                'msg'=> __('Please enter a valid number!', ET_DOMAIN)
            ));
        }

        // Check minimum amount
        if( (float)$request['amount'] < (float)MIN_WITHDRAW ){
            wp_send_json(array(
                'success'=> false,
                'msg'=> __('Please enter a number greater than minimum withdrawal!', ET_DOMAIN)
            ));
        }

        $user_wallet = AE_WalletAction()->getUserWallet($user_ID);
        $charge_obj = array(
            'amount' => (float)$request['amount'],
            'currency' => $user_wallet->currency,
            'customer' => $user_ID,
            'status'=> 'pending',
            'post_title'=> __('withdrew',ET_DOMAIN),
            'history_type'=> 'withdraw',
            'payment_method' => $request['account_type']
        );
        $charge = AE_WalletAction()->charge($charge_obj);
        if( !$charge['success'] ){
            wp_send_json($charge);
        }

        // Insert withdraw
        $post_title = sprintf(__('%s sent a request to withdraw %s ', ET_DOMAIN), $current_user->data->display_name, ae_price_format($request['amount']));

        $payment_info = mJobUserAction()->mJobGetPaymentInfo($user_ID);

        if($request['account_type'] == 'paypal') { // Account is PayPal
            $content = __('<h2>PayPal Infomation: </h2>');
            $content .= sprintf(__('User name: <a href="%s">%s</a> <br>', ET_DOMAIN), get_author_posts_url($user_ID), $current_user->data->display_name);
            $content .= sprintf(__('Email address: %s', ET_DOMAIN), $payment_info['paypal']);
        } else { // Account is bank
            $content = __('<h2>Bank Infomation: </h2>');
            $content .= sprintf(__('User name: <a href="%s">%s</a> <br>', ET_DOMAIN), get_author_posts_url($user_ID), $current_user->data->display_name);
            $content .= sprintf(__('First name: %s <br>', ET_DOMAIN), $payment_info['bank']['first_name']);
            $content .= sprintf(__('Middle name: %s <br>', ET_DOMAIN), $payment_info['bank']['middle_name']);
            $content .= sprintf(__('Last name: %s <br>', ET_DOMAIN), $payment_info['bank']['last_name']);
            $content .= sprintf(__('SWIFT code: %s <br>', ET_DOMAIN), $payment_info['bank']['swift_code']);
            $content .= sprintf(__('Account number: %s <br>', ET_DOMAIN), $payment_info['bank']['account_no']);
        }


        $withdraw = array(
            'post_title'=> $post_title,
            'post_type'=> 'ae_credit_withdraw',
            'post_status'=> 'pending',
            'post_content'=> $content,
            'post_author'=> $user_ID
        );

        $post = wp_insert_post($withdraw);
        if($post){
            update_post_meta($post, 'amount', $request['amount']);
            update_post_meta($post, 'currency', $user_wallet->currency);
            update_post_meta($post, 'charge_id', $charge['id']);

            // Send email to admin
            $this->mail->mJobRequestWithdraw($post);

            wp_send_json(array(
                'success'=> true,
                'msg'=> __('Request sent!', ET_DOMAIN),
                'data'=> ae_credit_balance_info($user_ID)
            ));
        }
        else{
            wp_send_json(array(
                'success'=> false,
                'msg'=> __('Failed request!', ET_DOMAIN)
            ));
        }
    }

    /**
     * Request a secure code
     * @param int $user_id
     * @return array $resp
     * @since 1.0
     * @package MicrojobEngine
     * @category void
     * @author Tat Thien
     */
    public function mJobRequestSecureCode($user_id) {
        // Check request time
        $request_time = get_user_meta($user_id, 'secure_code_request_time', true);
        if(!empty($request_time)) {
            $duration = (time() - $request_time) / 60;
            if((int)$duration < $this->secure_code_request_time) {
                return array(
                    'success' => false,
                    'msg' => sprintf(__('You need to wait for %s minutes to request a new secure code!', ET_DOMAIN), $this->secure_code_request_time)
                );
            }
        }

        // Generate secure code
        $secure_code = AE_WalletAction()->generateSecureCode();
        // Save secure code to user
        AE_WalletAction()->setSecureCode($user_id, $secure_code);
        // Update request time
        update_user_meta($user_id, 'secure_code_request_time', time());
        // Send email
        $this->mail->mJobSendSecureCode($user_id, $secure_code);

        $resp = array(
            'success' => true,
            'msg' => __('Secure code has been sent to your email.', ET_DOMAIN)
        );

        return $resp;
    }
}

$new_intance = mJobRevenueAction::getInstance();
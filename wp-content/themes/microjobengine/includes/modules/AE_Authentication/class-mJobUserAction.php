<?php
class mJobUserAction extends AE_UserAction
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
        $user = new mJobUser();
        parent::__construct($user);
        $this->mail = AE_Mailing::get_instance();

        // Add ajax
        $this->add_ajax('mjob_sync_user', 'mJobUserSync');

        // Filter result when register new user
        $this->add_filter('ae_after_insert_user', 'mJobFilterRegisterUser');
        $this->add_filter('ae_after_login_user', 'mJobFilterSignInUser');
        $this->add_filter('ae_reset_pass_response', 'mJobFilterResetPassword');
        $this->add_filter('ae_convert_user', 'mJobFilterUser');
        $this->add_filter('ae_confirm_user_time_out', 'mJobConfirmUser');
        /**
         * check role for user when register
         */
        $this->add_filter('ae_pre_insert_user', 'ae_check_role_user');
        // User action
        $this->add_action('ae_insert_user', 'mJobAfterRegisterUser', 10, 2);
        $this->add_action('ae_user_forgot', 'mJobAfterForgotPassword', 10, 2);
        $this->add_action('ae_user_reset_pass', 'mJobAfterResetPassword', 10, 1);

        // Add scripts
        $this->add_action('wp_enqueue_scripts', 'mJobAuthScripts');

        // Add template
        $this->add_action('wp_footer', 'mJobAuthenticationTemplate');
        $this->add_filter('ae_register_email_template_select', 'mJobFilterEmailRegister', 10, 2);
        $this->add_filter('ae_pre_insert_user', 'filterUserInfo');
        $this->add_filter('mjobChangePassMessage', 'password_change_email');
    }

    /**
     * User sync
     * @param void
     * @return void
     * @since 1.0
     * @package MicrojobEngine
     * @category Authentication
     * @author Tat Thien
     */
    public function mJobUserSync() {
        global $current_user;

        // Check active user
        if(!mJobUserActivate($current_user->ID)) {
            wp_send_json(array(
                'success' => false,
                'msg' => __('Your account is pending. You have to activate your account to continue this step.', ET_DOMAIN)
            ));
        }

        $request = $_REQUEST;
        $result = array();
        if(isset($request['do_action']) && !empty($request['do_action'])) {
            switch ($request['do_action']) {
                case 'check_email':
                    $result = $this->validateEmail($request['check_user_email']);
                    wp_send_json($result);
                    break;
                case 'update_payment_method':
                    $payment_info = get_user_meta($current_user->ID, 'payment_info', true);
                    if(empty($payment_info)) {
                        $payment_info = array();
                    }
                    if(isset($request['paypal_email'])) {
                        $payment_info['paypal'] = $request['paypal_email'];
                        update_user_meta($current_user->ID, 'payment_info', $payment_info);
                    } else if(isset($request['bank_account_no'])) {
                        $payment_info['bank'] = array (
                            'first_name' => $request['bank_first_name'],
                            'middle_name' => $request['bank_middle_name'],
                            'last_name' => $request['bank_last_name'],
                            'name' => $request['bank_name'],
                            'swift_code' => $request['bank_swift_code'],
                            'account_no' => $request['bank_account_no'],
                            'routing_no' => $request['bank_routing_no'],
                            'payee_name_override' => $request['payee_name_override'],
                            'payee_name_override_status' => $request['payee_name_override_status'],
                        );
                        update_user_meta($current_user->ID, 'payment_info', $payment_info);
                    }
                    break;
            }
        }

        parent::sync();
    }

    /**
     * Validate email: empty, format, exist
     * @param string $email
     * @return array
     * @since 1.0
     * @package MicrojobEngine
     * @category void
     * @author Tat Thien
     */
    public function validateEmail($email) {
        // Check email empty
        if(empty($email)) {
            return array(
                'success' => false,
                'msg' => __('Email field is empty.', ET_DOMAIN),
                'show'=> 0
            );
        }
        // Check email valid
        if(!is_email($email)) {
            return array(
                'success' => false,
                'msg' => __('Email field is invalid.', ET_DOMAIN),
                'show'=> 0
            );
        }
        // Check email exist
        if(email_exists($email)) {
            return array(
                'success' => false,
                'msg' => __('This email is already used on this site. Please enter a new email.', ET_DOMAIN),
                'user_email'=>$email,
                'show'=> 1
            );
        }
        return array(
            'success' => true,
            'user_email'=>$email,
            'show'=> 1
        );
    }

    /**
     * Filter response value when register new user
     * @param object $result
     * @return object $result
     * @since 1.0
     * @package MicrojobEngine
     * @category Authentication
     * @author Tat Thien
     */
    public function mJobFilterRegisterUser($result) {
        return $result;
    }

    public function mJobFilterSignInUser($result) {
        return $result;
    }

    /**
     * Filter response value after reset password
     * @param array $result
     * @return array $result
     * @since 1.0
     * @package MicrojobEngine
     * @category Authentication
     * @author Tat Thien
     */
    public function mJobFilterResetPassword($result) {
        $result['redirect_url'] = et_get_page_link('sign-in');
        return $result;
    }

    public function mJobFilterUser($result) {
        $result->avatar = mJobAvatar($result->ID, 35);
        $result->payment_info = get_user_meta($result->ID, 'payment_info', true);
        $result->user_status = get_user_meta($result->ID, 'user_status', true);
        $result->initial_display_name = explode(' ', $result->display_name);
        $l = count($result->initial_display_name);
        if( $l > 1 ){
            $l = $l-1;
            $result->initial_display_name = $result->initial_display_name['0'].' '.strtoupper(substr($result->initial_display_name[$l], 0, 1));
        }
        else{
            $result->initial_display_name = $result->display_name;
        }
        update_user_meta($result->ID, 'initial_display_name', $result->initial_display_name);
        //echo '<pre>';
        //var_dump($result);
        return $result;
    }

    /**
     * Update user key confirm and send register email
     * @param void
     * @return void
     * @since 1.0
     * @package MicrojobEngine
     * @category Authentication
     * @author Tat Thien
     */
    public function mJobAfterRegisterUser($result, $user_data) {
        // add key confirm for user
        if(ae_get_option('user_confirm')) {
            update_user_meta($result, 'register_status', 'unconfirm');
            update_user_meta($result, 'key_confirm', md5($user_data['user_email']));
        }
        if( isset($user_data['role']) && $user_data['role'] == COMPANY ){
            update_user_meta($result, 'user_status', COMPANY_STATUS_REGISTERED);
        }
        // send email registration to user
        $this->mail->register_mail($result);
    }

    /**
     * Send link reset password
     * @param void
     * @return void
     * @since 1.0
     * @package MicrojobEngine
     * @category Authentication
     * @author Tat Thien
     */
    public function mJobAfterForgotPassword($result, $key) {
        $this->mail->forgot_mail($result, $key);
    }

    /**
     * Send email after reset password successfully
     * @param int $user_id
     * @return void
     * @since 1.0
     * @package MicrojobEngine
     * @category Authentication
     * @author Tat Thien
     */
    public function mJobAfterResetPassword($user_id) {
        $this->mail->resetpass_mail($user_id);
    }

    /**
     * Add scripts for authentication
     * @param void
     * @return void
     * @since 1.0
     * @package MicrojobEngine
     * @category Authentication
     * @author Tat Thien
     */
    public function mJobAuthScripts() {
        wp_enqueue_script('mjob-auth', get_template_directory_uri() . '/includes/modules/AE_Authentication/js/mjob-auth.js', array(
            'jquery',
            'underscore',
            'backbone',
            'appengine',
            'front'
        ), 1.0, true);
    }

    /**
     * Add modal sign in into footer
     * @param void
     * @return void
     * @since 1.0
     * @package MicrojobEngine
     * @category Authentication
     * @author Tat Thien
     */
    public function mJobAddModalSignIn() {
        mJobModalSignIn();
    }

    /**
     * Add modal sign up step one into footer
     * @param void
     * @return void
     * @since 1.0
     * @package MicrojobEngine
     * @category Authentication
     * @author Tat Thien
     */
    public function mJobAddModalSignUpStepOne() {
        $default_intro = "<p><strong>Welcome to MicrojobEngine!</strong></p><p>If you have amazing skills, we have amazing mJobs. MicrojobEngine has opportunities for all types of fun. Let's turn your little hobby into Big Bucks.</p>";

        $intro = ae_get_option("sign_up_intro_text", $default_intro);

        mJobModalSignUpStepOne($intro);
    }
    /**
     * Add modal sign up before step one into footer
     * @param void
     * @return void
     * @since 1.0
     * @package MicrojobEngine
     * @category Authentication
     * @author Tat Thien
     */
    public function mJobAddModalSignUpBeforeStepOne() {
        $default_intro = __('<p><strong>Choose the appropriate account</strong></p>', ET_DOMAIN);
        $default_intro .= __('<p>If you are an Individual looking to hire other select "Individual". If you are company looking to provide service select "Company." </p>', ET_DOMAIN);
        $intro = ae_get_option("sign_up_before_intro_text", $default_intro);
        mJobModalSignUpBeforeStepOne($intro);
    }
    /**
     * Add modal sign up into footer
     * @param void
     * @return void
     * @since 1.0
     * @package MicrojobEngine
     * @category Authentication
     * @author Tat Thien
     */
    public function mJobAddModalSignUp() {
        mJobModalSignUp();
    }

    /**
     * Add modal forgot password into footer
     * @param void
     * @return void
     * @since 1.0
     * @package MicrojobEngine
     * @category Authentication
     * @author Tat Thien
     */
    public function mJobAddModalForgotPassword() {
        mJobModalForgotPassword();
    }

    /**
     * Set time out for confirm user
     * @param int $time     mili second
     * @return int $time
     * @since 1.0
     * @package MicrojobEngine
     * @category Authentication
     * @author Tat Thien
     */
    public function mJobConfirmUser($time) {
        $time = 4000;
        return $time;
    }

    /**
     * Get user payment information
     * @param int $user_id
     * @return array $payment_info
     * @since 1.0
     * @package MicrojobEngine
     * @category void
     * @author Tat Thien
     */
    public function mJobGetPaymentInfo($user_id) {
        $payment_info = get_user_meta($user_id, 'payment_info', true);
        return $payment_info;
    }

    /**
     * Check user payment info
     * @param int $user_id
     * @return boolean
     * @since 1.0
     * @package MicrojobEngine
     * @category void
     * @author Tat Thien
     */
    public function mJobCheckPaymentInfo($user_id, $account_type) {
        $payment_info = $this->mJobGetPaymentInfo($user_id);
        if(empty($payment_info)) {
            return false;
        } else if($account_type == 'paypal' && (!isset($payment_info['paypal']) || empty($payment_info['paypal']))){
            return false;
        } else if($account_type == 'bank' && (!isset($payment_info['bank']) || empty($payment_info['bank']))) {
            return false;
        } else {
            return true;
        }
    }
    /**
     * include all authentication template
     *
     * @param void
     * @return void
     * @since 1.0
     * @package MicrojobEngine
     * @category void
     * @author JACK BUI
     */
    public function mJobAuthenticationTemplate(){
        $this->mJobAddModalSignIn();
        $this->mJobAddModalSignUpStepOne();
        $this->mJobAddModalSignUpBeforeStepOne();
        $this->mJobAddModalSignUp();
        $this->mJobAddModalForgotPassword();
    }
    /**
     * check user role when signup
     *
     * @param void
     * @return void
     * @since 1.0
     * @package MicrojobEngine
     * @category void
     * @author JACK BUI
     */
    public function ae_check_role_user($user_data) {
        if ( isset($user_data['role'] ) && ($user_data['role'] != INDIVIDUAL && $user_data['role'] != COMPANY)) {
            unset($user_data['role']);
        }
        return $user_data;
    }
    /**
     * check if user is company role
     *
     * @param integer $user_id
     * @return Boolean true if is company role and false if it isn't
     * @since 1.0
     * @package MicrojobEngine
     * @category void
     * @author JACK BUI
     */
    public function is_company( $user_id ){
        if( !empty($user_id) ){
            $user_role =  ae_user_role($user_id);
            if( $user_role == COMPANY ){
                return true;
            }
        }
        return false;
    }
    /**
     * check if user is individual role
     *
     * @param integer $user_id
     * @return Boolean true if is company role and false if it isn't
     * @since 1.0
     * @package MicrojobEngine
     * @category void
     * @author JACK BUI
     */
    public function is_individual( $user_id ){
        if( !empty($user_id) ){
            $user_role =  ae_user_role($user_id);
            if( $user_role == INDIVIDUAL ){
                return true;
            }
        }
        return false;
    }

    /**
     * Get the user role
     * @param integer $user_id
     * @return string
     * @since 1.0
     * @package MicrojobEngine
     * @category void
     * @author Jesse Boyer
     */
    public function get_role( $user_id ){
        if(! empty($user_id) ){
            return ae_user_role($user_id);
        }
    }
    /**
     * Get the company status
     * @param integer $user_id
     * @return string
     * @since 1.0
     * @package MicrojobEngine
     * @category void
     * @author Jesse Boyer
     */
    public function get_company_status( $user_id ) {
        return get_user_meta($user_id, 'company_status', true);
    }
    /**
     * Filter Email when user register
     *
     * @param string $message
     * @param object $user
     * @return string $message
     * @since 1.0
     * @package MicrojobEngine
     * @category void
     * @author JACK BUI
     */
    public function mJobFilterEmailRegister($message, $user){
        if( isset( $user->ID) ) {
            $is_individual = $this->is_individual($user->ID);
            if ( $is_individual ){
                $message = ae_get_option('register_mail_template_individual');
            }
        }
        return $message;
    }
    /**
     * filter user data
     *
     * @param void
     * @return void
     * @since 1.0
     * @package MicrojobEngine
     * @category void
     * @author JACK BUI
     */
    public function filterUserInfo($user_data){
        $user_data['role'] = INDIVIDUAL;
        return $user_data;
    }
    public function password_change_email($message){
        global $user_ID;
        $profile= mJobProfileAction()->getProfile($user_ID);
        $message = str_replace( '###USERNAME###', $profile->initial_display_name, $message );
        return $message;
    }
}

$new_instance = mJobUserAction::getInstance();

if(!function_exists('mJobUserAction')) {
    function mJobUserAction() {
        return mJobUserAction::getInstance();
    }
}
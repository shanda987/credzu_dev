<?php
class mJobProfileAction extends mJobPostAction
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
     * the constructor of this class
     *
     */
    public function __construct($post_type = 'mjob_profile') {
        parent::__construct($post_type);
        $this->add_ajax('mjob_sync_profile', 'syncPost');
        $this->add_ajax('mjob_crop_avatar', 'mJobCropAvatar');
        $this->add_action('ae_insert_user', 'mJobInsertProfile', 10, 2);
        $this->add_action('ae_login_user', 'mJobInsertProfileAfterLogin', 10, 1);
        $this->add_action('wp_footer', 'mJobAddProfileModal');
        $this->add_ajax('mjob-check-smarty-address', 'mJobCheckSmartyAddress');
        $this->add_action('ae_convert_mjob_profile', 'mJobConvertProfile');
        $this->add_ajax('mjob-check-user-active',  'mJobCheckActiveAccount');
    }

    /**
     * Insert profile after user sign up
     * @param int $result
     * @param object $user_data
     * @since 1.0
     * @package MicrojobEngine
     * @category Profile
     * @author Tat Thien
     */
    public function mJobInsertProfile($result, $user_data) {
        $user = get_userdata($result);
        $profile = wp_insert_post(array(
            'post_type' => 'mjob_profile',
            'post_status' => 'publish',
            'post_title' => $user->display_name,
            'post_author' => $result
        ));

        if(!is_wp_error($profile)) {
            update_user_meta($result, 'user_profile_id', $profile);
            /*
             * @TODO  @Jesse Why should we check $user->display_name == COMPANY ?
             */
            if ($user->display_name == COMPANY) {
                update_user_meta($result, 'company_status', COMPANY_STATUS_REGISTERED);
            }
        }
    }

    /**
     * If is assign user from multi site then create a profile
     * @param object $result
     * @return void
     * @since 1.0
     * @package MicrojobEngine
     * @category void
     * @author Tat Thien
     */
    public function mJobInsertProfileAfterLogin($result) {
        $user_profile_id = get_user_meta($result->ID, 'user_profile_id', true);
        $profile = get_post($user_profile_id);
        if(empty($user_profile_id) || empty($profile)) {
            $profile = wp_insert_post(array(
                'post_type' => 'mjob_profile',
                'post_status' => 'publish',
                'post_title' => $result->display_name,
                'post_author' => $result->ID
            ));

            if(!is_wp_error($profile)) {
                update_user_meta($result->ID, 'user_profile_id', $profile);
            }
        }
    }

    /**
     * Sync profile
     * @param void
     * @return void
     * @since 1.0
     * @package MicrojobEngine
     * @category Profile
     * @author Tat Thien
     */
    public function syncPost() {
        global $current_user;
        $request = $_REQUEST;

        // Check valid user
        if($request['post_author'] != $current_user->ID) {
            wp_send_json(array(
                'success' => false,
                'msg' => __('Invalid user.', ET_DOMAIN)
            ));
        }

        // Check active user
        if(!mJobUserActivate($current_user->ID)) {
            wp_send_json(array(
                'success' => false,
                'msg' => __('Your account is pending. You have to activate your account to continue this step.', ET_DOMAIN)
            ));
        }
        if( isset($request['routing_number']) ) {
            $response = $this->getBankName($request['routing_number']);
            if( !$response['success'] ){
                wp_send_json($response);
            }
            else{
                $request['bank_name'] = $response['data'];
            }
            $res = $this->verifyBankInfo( $request['account_number'], $request['routing_number']);
            if( !$res['success'] ){
                $res['msg'] = __('Error with billing information. Please try again. Call 888-831-4742 if the problem continues', ET_DOMAIN);
                wp_send_json($res);
            }
        }
        $result = $this->sync_post($request);
        if($result['success'] != false && !is_wp_error($result)) {
            if($request['method'] == 'create') {
                update_user_meta($current_user->ID, 'user_profile_id', $result['data']->ID);
            }

            wp_send_json(array(
                'success' => true,
                'data' => $result['data'],
                'msg' => __('Successful update.', ET_DOMAIN)
            ));
        } else {
            wp_send_json(array(
                'success' => false,
                'msg' => $result['msg']
            ));
        }
    }

    /**
     * Crop user avatar
     * @param void
     * @return void
     * @since 1.0
     * @package MicrojobEngine
     * @category Profile
     * @author Tat Thien
     */
    public function mJobCropAvatar() {
        global $current_user;
        $request = $_REQUEST;

        // Check valid image
        if(!isset($request['attach_id']) || empty($request['attach_id'])) {
            wp_send_json(array(
                'success' => false,
                'msg' => __('Invalid image!', ET_DOMAIN)
            ));
        }

        // Check valid user
        if($request['user_id'] != $current_user->ID) {
            wp_send_json(array(
                'success' => false,
                'msg' => __('Invalid user!', ET_DOMAIN)
            ));
        }

        $des_file = wp_crop_image(
            $request['attach_id'],
            $request['crop_x'],
            $request['crop_y'],
            $request['crop_width'],
            $request['crop_height'],
            $request['crop_width'],
            $request['crop_height']
        );

        // Check the type of file. We'll use this as the 'post_mime_type'.
        $filetype = wp_check_filetype(basename( $des_file ), null);

        // Get the path to the upload directory.
        $wp_upload_dir = wp_upload_dir();

        // Prepare an array of post data for the attachment.
        $attachment = array(
            'guid'           => $wp_upload_dir['url'] . '/' . basename($des_file),
            'post_mime_type' => $filetype['type'],
            'post_title'     => preg_replace( '/\.[^.]+$/', '', basename($des_file)),
            'post_content'   => '',
            'post_status'    => 'inherit'
        );

        // Insert the attachment.
        $attach_id = wp_insert_attachment($attachment, $des_file);

        // Make sure that this file is included, as wp_generate_attachment_metadata() depends on it.
        require_once( ABSPATH . 'wp-admin/includes/image.php' );

        // Generate the metadata for the attachment, and update the database record.
        $attach_data = wp_generate_attachment_metadata($attach_id, $des_file);
        wp_update_attachment_metadata($attach_id, $attach_data);

        $attach_data = et_get_attachment_data($attach_id);

        if (!isset($request['user_id'])) return;

        $ae_users = AE_Users::get_instance();

        //update user avatar
        $user = $ae_users->update(array(
            'ID' => $request['user_id'],
            'et_avatar' => $attach_data['attach_id'],
            'et_avatar_url' => $attach_data['thumbnail'][0]
        ));

        wp_send_json(array(
            'success' => true,
            'msg' => __('Your profile picture has been uploaded successfully.', ET_DOMAIN) ,
            'data' => $attach_data
        ));
    }

    /**
     * Verifies Routing Numbers to a Bank
     * @param $account_no
     * @return string json
     * @since 1.0
     * @package MicrojobEngine
     * @category Profile
     * @author Jesse Boyer
     * @author Jack Bui
     *
     */
    public function verifyBankInfo($account_no, $routing_no) {
        $username = ae_get_option('giact_api_username', 'XHBKT-C50M-T7F7-UFKL-TU9CK');
        $password = ae_get_option('giact_api_password', 'fmieTL-QNE_3PYo');
        AE_GVerify()->init($username, $password);
        $result = AE_GVerify()->verifyPayment($routing_no, $account_no, array('TestMode'=>true));
        return $result;
    }
    /**
      * get bank name
      *
      * @param string $routing_number
      * @return array $response
      * @since 1.4
      * @package MicrojobEngine
      * @category CREDZU
      * @author JACK BUI
      */
    public function getBankName($routing_number = ''){
        $uri = 'http://www.routingnumbers.info/api/name.json?rn='. $routing_number;
        $data = wp_remote_post($uri);
        $response = array(
            'success'=> false,
            'msg'=> __('Failed!', ET_DOMAIN)
        );
        if( isset($data['body']) ){
            $data = $data['body'];
            $data = json_decode($data);
            if( $data->code == 200 ){
                $response = array(
                    'success'=> true,
                    'msg'=> __('Success!', ET_DOMAIN),
                    'data'=> $data->name
                );
            }
            else{
                $response = array(
                    'success'=> false,
                    'msg'=> $data->message
                );
            }
        }
        return $response;
    }


    public function mJobConvertProfile($result) {
        $user = get_userdata($result->post_author);
        $result->post_content= !empty($result->post_content) ? $result->post_content : __('There is no content', ET_DOMAIN);
        $result->payment_info = !empty($result->payment_info) ? $result->payment_info : __('There is no content', ET_DOMAIN);
        $result->billing_full_name = !empty($result->billing_full_name) ? $result->billing_full_name : __('There is no content', ET_DOMAIN);
        $result->billing_full_address = !empty($result->billing_full_address) ? $result->billing_full_address : __('Physical address', ET_DOMAIN);
        $result->billing_country = !empty($result->billing_country) ? $result->billing_country : __('There is no content', ET_DOMAIN);
        $result->billing_vat = !empty($result->billing_vat) ? $result->billing_vat : __('There is no content', ET_DOMAIN);
        $result->first_name = !empty($result->first_name) ? $result->first_name : __('First Name', ET_DOMAIN);
        $result->last_name = !empty($result->last_name) ? $result->last_name : __('Last Name', ET_DOMAIN);
        $result->phone = !empty($result->phone) ? $result->phone : __('Phone', ET_DOMAIN);
        $result->business_email = !empty($result->business_email) ? $result->business_email : $user->user_email;
        $result->credit_goal = !empty($result->credit_goal) ? $result->credit_goal : __('Credit_goals', ET_DOMAIN);
        $result->company_status = get_user_meta($user->ID,'user_status', true);
        return $result;
    }

    public function mJobAddProfileModal() {
        ?>
        <div class="modal fade" id="uploadAvatar" tabindex="-1" role="dialog" aria-labelledby="myModalLabel1">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true"><img
                                    src="<?php echo get_template_directory_uri() ?>/assets/img/icon-close.png" alt=""></span></button>
                        <h4 class="modal-title" id="myModalLabel1"><?php _e('Upload Avatar', ET_DOMAIN); ?></h4>
                    </div>
                    <div class="modal-body">
                        <div class="inner-form">
                            <div id="upload_avatar_container" class="image-upload" style="margin-bottom: 30px;">
                                <div id="upload_avatar_browse_button" class="browse_button">
                                    <div class="drag-image">
                                        <i class="fa fa-cloud-upload"></i>
                                        <span class="drag-image-title"><?php _e('Drag profile image here') ?></span>
                                        <span class="drag-image-text"><?php _e('or', ET_DOMAIN); ?></span>
                                        <a class="drag-image-select-button"><?php _e('upload from local storage', ET_DOMAIN); ?></a>
                                    </div>
                                </div>
                            </div>
                            <input type="hidden" class="et_ajax_nonce" value="<?php echo de_create_nonce( 'upload_avatar_et_uploader' ); ?>">
                            <div class="form-group float-right">
                                <button class="btn-submit btn-save" disabled="true"><?php _e('SAVE', ET_DOMAIN); ?></button>
                                <a href="#" class="btn-remove"><?php _e('REMOVE IMAGE', ET_DOMAIN); ?></a>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
        <?php
    }
    /**
     * get user profile
     *
     * @param integer $user_id
     * @return void
     * @since 1.0
     * @package MicrojobEngine
     * @category void
     * @author JACK BUI
     */
    public function getProfile($user_id){
        global  $ae_post_factory, $post;
        $profile_obj = $ae_post_factory->get('mjob_profile');
        $profile_id = get_user_meta($user_id, 'user_profile_id', true);
        $profile = '';
        $id = $json_to_page;
        if($profile_id) {
            $p = get_post($profile_id);
            if($p && !is_wp_error($p)) {
                $profile = $profile_obj->convert($p);
            }
        }
        return $profile;
    }

    /**
     * Output JSON for user Profile
     *
     * @param object $profile (The getProfile must be called first)
     * @return void
     * @since 1.0
     * @package MicrojobEngine
     * @category void
     * @author JESSE BOYER
     */
    public function getProfileJson($profile) {

      if ($profile) {
        return "<script type='text/json' id='mjob_profile_data'>".json_encode($profile).'</script>';
      }

      return false;
    }

    /**
     * Displays the header messages for a profile
     *
     * @param integer $user_id
     * @param string $company_status
     * @return void
     * @since 1.0
     * @package MicrojobEngine
     * @category void
     * @author Jesse Boyer
     */
    public function display_company_status($user_role, $company_status){
        $output = '';
        if ($user_role == COMPANY && $company_status != COMPANY_STATUS_APPROVED) {

            $output .= '<div class="top-message"><span>';

            if ($company_status == COMPANY_STATUS_REGISTERED || !$company_status) {
                $output .= __("ACCOUNT PENDING: Please complete your Personal Profile, Company Profile and Billing information so we can review and approve your account", ET_DOMAIN);
            }
            elseif ($company_status == COMPANY_STATUS_UNDER_REVIEW) {
                $output .= __('Your account is under review.', ET_DOMAIN);
            }
            elseif ($company_status == COMPANY_STATUS_NEEDS_CHANGES) {
                $output .= __("Your account needs changes. You must update your profile and then click", ET_DOMAIN);
                $output .= __("<a href='#' class='btn-basic'>Activate Account</a> in order to post listings.", ET_DOMAIN);
            }
            elseif ($company_status == COMPANY_STATUS_DECLINED) {
                $output .= __("Sorry. Your account was declined by our staff.", ET_DOMAIN);
            }
            elseif ($company_status == COMPANY_STATUS_SUSPENDED) {
                $output .= __("This account has been suspended.", ET_DOMAIN);
            }
            $output .= '</span></div>';
        }
        return $output;
    }
    /**
      * Check company profile
      *
      * @param object $profile
      * @param array $fields_to_check is a array meta_field to check
      * @return bolean true if there is a empty field that user need to fill and false if that it's enough
      * @since 1.4
      * @package MicrojobEngine
      * @category CREDZU
      * @author JACK BUI
      */
    public function empty_company_profile($profile, $fields_to_check = array()){
        if( !empty($profile) ){
            $profile_arr = (array)$profile;
            if( !empty($fields_to_check) ) {
                if( is_array($fields_to_check) ) {
                    foreach ($profile_arr as $key => $value) {
                        if (in_array($key, $fields_to_check) && empty($value)) {
                            return true;
                        }
                    }
                    return false;
                }
                else{
                    return ae_is_empty_array_value($profile_arr);
                }
            }
            else{
                return false;
            }
        }

    }
    /**
      * check company can active or not
      *
      * @param void
      * @return void
      * @since 1.4
      * @package MicrojobEngine
      * @category CREDZU
      * @author JACK BUI
      */
    public function mJobCheckActiveAccount(){
        global $user_ID;
        $profile = $this->getProfile($user_ID);
        $arr_to_check = array(
            'company_name',
            'company_address',
            'company_phone',
            'company_email',
            'company_website',
            'company_year_established',
            'company_amount_of_employees',
            'company_description',
            'account_number',
            'routing_number',
            'billing_other_address',
            'account_holder',
            'use_holder_account',
            'use_billing_address',
            'first_name',
            'last_name',
            'phone',
            'business_email'
        );
        $check = $this->empty_company_profile($profile, $arr_to_check);
        if( !$check ){
            update_user_meta($user_ID, 'user_status', COMPANY_STATUS_UNDER_REVIEW);
            wp_send_json(array(
                'success'=> true,
                'msg'=> __('Success!', ET_DOMAIN)
            ));
        }
        else{
            wp_send_json( array(
                    'success'=> false,
                    'msg'=> __('Please complete your profile before click active account button!', ET_DOMAIN)
                )

            );
        }
    }
}
$new_instance = mJobProfileAction::getInstance();
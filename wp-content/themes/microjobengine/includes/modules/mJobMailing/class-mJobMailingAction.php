<?php
class mJobMailingAction extends AE_Base
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
        $this->mail = mJobMailing::getInstance();
        $this->add_action('ae_reject_post', 'mJobMailRejectPost', 9, 1);
        $this->add_action('mjob_process_payment_action', 'mJobMailNewOrder', 10, 2);
        $this->add_action('mjob_update_order_author', 'mJobMailUpdateNewOrder', 10, 1);
        $this->add_action('ae_approve_withdraw', 'mJobMailApproveWithdraw');
        $this->add_action('ae_decline_withdraw', 'mJobMailDeclineWithdraw');
        $this->add_action('mjob_decline_order', 'mJobMailDeclineMjobOrder');
        $this->add_filter('ae_filter_receipt_mail_template', 'mJobMailFilterReceiptContent', 10, 3);
        $this->add_action('mjob_consumer_rights_email', 'mJobMailConsumerRights', 10, 2);
        $this->add_action('mjob_agreement_email', 'mJobMailAgreement', 10, 5);
        $this->add_action('mjob_company_created_email', 'mJobMailCompanyCreated', 10, 2);
        $this->add_action('payment_check_email', 'mJobMailCheckPayment', 10, 3);
        $this->add_action('client_payment_check_email', 'mJobMailClientCheckPayment', 10, 3);
        $this->add_action('credzu_company_agreement_email', 'mJobMailCompanyCredzu', 10, 2);
        $this->add_action('changing_order_status_email', 'mJobChangingOrderStatus', 10, 4);
        $this->add_action('change_user_role_email', 'mJobChangingUserRole');
        $this->add_action('send_request_new_document', 'mJobRequestNewDocument', 10, 2);
        $this->add_action('email_to_company_comment_approved', 'mJobCommentApprovedEmail');
        $this->add_action('email_mjob_review', 'mJobReviewEmail');
        $this->add_action('email_mjob_rehire', 'mJobRehireEmail');
    }

    public function mJobMailRejectPost($args) {
        if($args['post_type'] == 'mjob_post') {
            $this->mail->mJobRejectPost($args);
        }

        global $et_appengine;
        remove_action('ae_reject_post', array($et_appengine, 'reject_post'));
    }

    public function mJobMailNewOrder($payment_return, $data) {
        global $ae_post_factory, $user_ID;
        $order_obj = $ae_post_factory->get('mjob_order');
        $post = get_post($data['order_id']);
        $order = $order_obj->convert($post);
        if($order->post_status == 'publish' && $user_ID) {
            $this->mail->mJobNewOrder($order);
        }
    }

    public function mJobMailUpdateNewOrder($data) {
        if($data->post_status == 'publish') {
            $this->mail->mJobNewOrder($data);
        }
    }

    public function mJobMailFilterReceiptContent($content, $order, $data) {
        if($order['payment'] == 'cash') {
            $content = ae_get_option('pay_package_by_cash');
        }

        $post = get_post($data['ad_id']);
        if(!empty($post)) {
            $link = '<a href="'. get_permalink($post->ID) .'">'. get_permalink($post->ID) .'</a>';
            $detail = "";
            switch($post->post_type) {
                case 'mjob_post':
                    $detail = sprintf(__('Submit a job, visit here: %s', ET_DOMAIN), $link);
                    break;

                case 'mjob_order':
                    $detail = sprintf(__('Buy a job. visit here: %s', ET_DOMAIN), $link);
                    break;

                default:
                    $detail = __('Submit post', ET_DOMAIN);

            }

            $content = str_ireplace('[detail]', $detail, $content);
        }
        return $content;
    }

    public function mJobMailApproveWithdraw($withdraw) {
        $this->mail->mJobApproveWithdraw($withdraw->post_author);
    }

    public function mJobMailDeclineWithdraw($withdraw) {
        $this->mail->mJobDeclineWithdraw($withdraw);
    }
    public function mJobMailDeclineMjobOrder($mjob_order) {
        $this->mail->mJobDeclineMjobOrder($mjob_order);
    }
    public function mJobMailConsumerRights($emails, $file_path){
        $this->mail->email_consumer_rights($emails, $file_path);
    }
    public function mJobMailAgreement($client_email, $company_email, $file_path, $company_name, $client_name){
        $this->mail->email_agreement($client_email, $company_email, $file_path, $company_name, $client_name);
    }
    /**
      * Send an email to admin everytime a compnay is created
      *
      * @param integer/string $profile_id
      * @return void
      * @since 1.4
      * @package MicrojobEngine
      * @category CREDZU
      * @author JACK BUI
      */
    public function mJobMailCompanyCreated($profile_id, $is_edit){
        $this->mail->email_company_created($profile_id, $is_edit);
    }
    /**
     * sent email checkout
     *
     * @param void
     * @return void
     * @since 1.0
     * @package MicrojobEngine
     * @category void
     * @author JACK BUI
     */
    public function  mJobMailCheckPayment($email, $data, $path){
        $this->mail->email_payment_check($email, $data, $path);
    }
    /**
     * sent email checkout
     *
     * @param void
     * @return void
     * @since 1.0
     * @package MicrojobEngine
     * @category void
     * @author JACK BUI
     */
    public function  mJobMailClientCheckPayment($email, $data, $path){
        $this->mail->email_client_payment_check($email, $data, $path);
    }
    /**
      * Send email when company sign  agreement
      *
      * @param void
      * @return void
      * @since 1.4
      * @package MicrojobEngine
      * @category CREDZU
      * @author JACK BUI
      */
    public function mJobMailCompanyCredzu($email, $path){
        $this->mail->email_company_credzu_agreement($email, $path);
    }
    /**
      * send email when order status is changed
      *
      * @param string $company_email
     * @param string $client_email
     * @param string $old_status
     * @param string $new_status
      * @return void
      * @since 1.4
      * @package MicrojobEngine
      * @category CREDZU
      * @author JACK BUI
      */
    public function mJobChangingOrderStatus($company_email, $client_email, $old_status, $new_status){
        $this->mail->email_changing_order_status($company_email, $client_email, $old_status, $new_status);
    }
    /**
      * sent an email to infor@credzu.com when a user covert their account to company
      *
      * @param void
      * @return void
      * @since 1.4
      * @package MicrojobEngine
      * @category CREDZU
      * @author JACK BUI
      */
    public function mJobChangingUserRole($user_ID){
        $this->mail->email_changing_user_role($user_ID);
    }
    /**
      * send an email to client when company request a new document
      *
      * @param void
      * @return void
      * @since 1.4
      * @package MicrojobEngine
      * @category CREDZU
      * @author JACK BUI
      */
    public function mJobRequestNewDocument($ood, $name){
        $this->mail->email_request_new_document($ood, $name);
    }
    /**
      * Send to company a email when admin approved their comment
      *
      * @param void
      * @return void
      * @since 1.4
      * @package MicrojobEngine
      * @category CREDZU
      * @author JACK BUI
      */
    public function mJobCommentApprovedEmail($comment){
        $this->mail->email_comment_approved_by_admin($comment);
    }
    /**
     * Send to company a email when admin approved their comment
     *
     * @param void
     * @return void
     * @since 1.4
     * @package MicrojobEngine
     * @category CREDZU
     * @author JACK BUI
     */
    public function mJobReviewEmail($comment){
        $this->mail->email_mjob_review($comment);
    }
    /**
     * Send to company a email when admin approved their comment
     *
     * @param void
     * @return void
     * @since 1.4
     * @package MicrojobEngine
     * @category CREDZU
     * @author JACK BUI
     */
    public function mJobRehireEmail($o){
        $this->mail->email_mjob_rehire($o);
    }
}
$new_instance = mJobMailingAction::getInstance();
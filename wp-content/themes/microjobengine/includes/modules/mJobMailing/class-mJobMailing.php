<?php
class mJobMailing extends AE_Mailing
{
    public static $instance;

    static function getInstance() {
        if (self::$instance == null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    function __construct() {
    }

    /**
     * Send email to admin when have a new post
     * @param int $postID
     * @return void
     * @since 1.0
     * @package MicrojobEngine
     * @category mJob Mailing
     * @author Tat Thien
     */
    public function mJobNewPost($postID) {
        $subject = sprintf(__('A new mJob submitted on your site', ET_DOMAIN));
        $message = ae_get_option('new_mjob_mail_template');
        $message = $this->filter_post_placeholder($message, $postID);
        $post_link = '<a href="' . get_permalink($postID) . '" >'. __('here', ET_DOMAIN) .'</a>';
        $message = str_ireplace('[here]', $post_link, $message);
        // Mail to admin
        $this->wp_mail(get_option('admin_email'), $subject, $message, array(
            'post' => $postID
        ));
    }

    /**
     * Email notification when mJob has changed status
     * @param string $newStatus
     * @param string $oldStatus
     * @param object $post
     * @return string $newStatus
     * @since 1.0
     * @package MicrojobEngine
     * @category mJob Mailing
     * @author Tat Thien
     */
    public function mJobChangeStatus($newStatus, $oldStatus, $post, $rejectMessage) {
        if ($newStatus != $oldStatus) {
            $authorID = $post->post_author;
            $user = get_userdata($authorID);
            $userEmail = $user->user_email;

            switch ($newStatus) {
                case 'publish':
                    // publish post mail
                    $subject = sprintf(__("Your post '%s' has been approved.", ET_DOMAIN) , get_the_title($post->ID));
                    $message = ae_get_option('approve_mjob_mail_template');
                    //send mail
                    $this->wp_mail($userEmail, $subject, $message, array(
                        'user_id' => $authorID,
                        'post' => $post->ID
                    ) , '');
                    break;

                case 'archive':
                    // archive post mail
                    $subject = sprintf(__('Your post "%s" has been archived', ET_DOMAIN) , get_the_title($post->ID));
                    $message = ae_get_option('archived_mjob_mail_template');
                    $message = str_ireplace('[reject_message]', $rejectMessage, $message);
                    $dashboardLink = '<a href="'. et_get_page_link('dashboard') .'">'. __('dashboard') .'</a>';
                    $message = str_ireplace('[dashboard]', $dashboardLink, $message);
                    // send mail
                    $this->wp_mail($userEmail, $subject, $message, array(
                        'user_id' => $authorID,
                        'post' => $post->ID
                    ) , '');
                    break;
                default:

                    //code
                    break;
            }
        }
        return $newStatus;
    }

    /**
     * Send email to admin when reject a post
     * @param object $data
     * @return void
     * @since 1.0
     * @package MicrojobEngine
     * @category mJob Mailing
     * @author Tat Thien
     */
    public function mJobRejectPost($data) {
        // get post author
        $user = get_user_by('id', $data['post_author']);
        $user_email = $user->user_email;

        // mail title
        $subject = sprintf(__("Your post '%s' has been rejected.", ET_DOMAIN) , get_the_title($data['ID']));

        // get reject mail template
        $message = ae_get_option('reject_mail_template');

        // filter reject message
        $message = str_replace('[reject_message]', $data['reject_message'], $message);

        // filter dashboard link
        $dashboardLink = '<a href="'. et_get_page_link('dashboard') .'">'. __('dashboard') .'</a>';
        $message = str_ireplace('[dashboard]', $dashboardLink, $message);

        // send reject mail
        $this->wp_mail($user_email, $subject, $message, array(
            'user_id' => $data['post_author'],
            'post' => $data['ID']
        ) , '');
    }

    /**
     * Send email to author when someone order
     * @param object $order
     * @return void
     * @since 1.0
     * @package MicrojobEngine
     * @category mJob Mailing
     * @author Tat Thien
     */
    public function mJobNewOrder($order) {
        $message = ae_get_option('new_order');
        $post = get_post($order->post_parent);
        $author = get_userdata($post->post_author);

        $subject = sprintf(__('Your post "%s" has a new order', ET_DOMAIN), $post->post_title);

        //Filter order placeholder
        $message = $this->mJobFilterOrderPlaceholder($message, $order);

        $this->wp_mail($author->user_email, $subject, $message, array(
            'user_id' => $post->post_author,
            'post' => $post->ID
        ));
    }

    /**
     * Send secure code to user
     * @param object $order
     * @return void
     * @since 1.0
     * @package MicrojobEngine
     * @category mJob Mailing
     * @author Tat Thien
     */
    public function mJobSendSecureCode($user_id, $secure_code) {
        $user = get_userdata($user_id);
        $user_email = $user->user_email;
        $subject = sprintf(__('%s has sent you a secure code', ET_DOMAIN), get_option('blogname'));
        $message = ae_get_option('secure_code_mail');
        $message = str_ireplace('[secure_code]', $secure_code, $message);
        $this->wp_mail($user_email, $subject, $message, array(
            'user_id' => $user_id
        ));
    }

    /**
     * Send email to buyer when his order is delivered
     * @param object $order_delivery
     * @return void
     * @since 1.0
     * @package MicrojobEngine
     * @category mJob Mailing
     * @author Tat Thien
     */
    public function mJobDeliveryOrder($order_delivery) {
        $message = ae_get_option('delivery_order');
        $mjob_order = get_post($order_delivery->post_parent);
        $mjob = get_post($mjob_order->post_parent);
        $user = get_userdata($mjob_order->post_author);
        $message = $this->mJobFilterOrderPlaceholder($message, $mjob_order);
        $message = str_ireplace('[note]', $order_delivery->post_content, $message);

        $subject = __('Your order has been delivered', ET_DOMAIN);

        $this->wp_mail($user->user_email, $subject, $message, array(
            'post' => $mjob,
            'user_id' => $mjob_order->post_author
        ));
    }

    /**
     * Send mail when order dispute
     * @param void
     * @return void
     * @since 1.0
     * @package MicrojobEngine
     * @category mJob Mailing
     * @author Tat Thien
     */
    public function mJobDisputeOrder($order) {
        global $user_ID;
        // Get user data
        $seller_id = $order->seller_id;
        $seller = get_userdata($seller_id);
        $buyer_id = $order->post_author;
        $buyer = get_userdata($order->post_author);

        // Get admin email
        $admin_email = get_option('admin_email');
        // Get message content
        $admin_message = ae_get_option('dispute_order');
        $user_message = ae_get_option('dispute_order_user');
        // Email subject
        $subject = __('One of your order has been reported.', ET_DOMAIN);

        if($user_ID == $seller_id) {
            // Send to admin
            $this->wp_mail($admin_email, __('There was an order has been reported.', ET_DOMAIN), $admin_message, array(
                'post' => $order
            ));
            // Send to buyer
            $this->wp_mail($buyer->user_email, $subject, $user_message, array(
                'post' => $order,
                'user_id' => $buyer_id
            ));
        } elseif($user_ID == $buyer_id) {
            // Send to admin
            $this->wp_mail($admin_email, __('There was an order has been reported.', ET_DOMAIN), $admin_message, array(
                'post' => $order
            ));
            // Send to seller
            $this->wp_mail($seller->user_email, $subject, $user_message, array(
                'post' => $order,
                'user_id' => $seller_id
            ));
        } else {
            // Send to buyer
            $this->wp_mail($buyer->user_email, $subject, $user_message, array(
                'post' => $order,
                'user_id' => $buyer_id
            ));
            // Send to seller
            $this->wp_mail($seller->user_email, $subject, $user_message, array(
                'post' => $order,
                'user_id' => $seller_id
            ));
        }
    }

    /**
     * Dispute decision mail
     * @param $order
     * @param $winner
     * @return void
     * @since 1.0
     * @package MicrojobEngine
     * @category mJob Mailing
     * @author Tat Thien
     */
    public function mJobDisputeDecision($order, $winner) {
        // Get user data
        $seller_id = $order->seller_id;
        $seller = get_userdata($seller_id);
        $buyer_id = $order->post_author;
        $buyer = get_userdata($order->post_author);

        $message = ae_get_option('dispute_seller_win');
        $subject = __('Your disputed order has been processed by admin', ET_DOMAIN);

        // If winner is buyer
        if($winner == $order->post_author) {
            $message = ae_get_option('dispute_buyer_win');
        }

        $this->wp_mail($buyer->user_email, $subject, $message, array(
            'post' => $order,
            'user_id' => $buyer_id
        ));

        $this->wp_mail($seller->user_email, $subject, $message, array(
            'post' => $order,
            'user_id' => $seller_id
        ));

    }

    /**
     * Send email to admin when someone request withdraw
     * @param int $withdraw_id
     * @return void
     * @since 1.0
     * @package MicrojobEngine
     * @category mJob Mailing
     * @author Tat Thien
     */
    public function mJobRequestWithdraw($withdraw_id) {
        $withdraw = get_post($withdraw_id);
        $message = ae_get_option('new_withdraw');

        $admin_email = get_option('admin_email');
        $amount = get_post_meta($withdraw->ID, 'amount', true);
        $user = get_userdata($withdraw->post_author);
        $subject = __('You\'ve got a new withdrawal request.', ET_DOMAIN);

        $message = str_ireplace('[user_name]', $user->display_name, $message);
        $message = str_ireplace('[total]', mJobPriceFormat($amount), $message);
        $message = str_ireplace('[withdraw_info]', $withdraw->post_content, $message);

        $user_link = '<a href="'. get_author_posts_url($withdraw->post_author) .'">'. get_author_posts_url($withdraw->post_author) .'</a>';
        $message = str_ireplace('[user_link]', $user_link, $message);

        $this->wp_mail($admin_email, $subject, $message, array());
    }

    /**
     * Send email to user when admin approve his withdraw request
     * @param int $user_id
     * @return void
     * @since 1.0
     * @package MicrojobEngine
     * @category mJob Mailing
     * @author Tat Thien
     */
    public function mJobApproveWithdraw($user_id) {
        $user = get_userdata($user_id);
        $user_email = $user->user_email;
        $message = ae_get_option('approve_withdraw');
        $subject = __('Your withdrawal request has been approved', ET_DOMAIN);

        $available = AE_WalletAction()->getUserWallet($user_id)->balance;
        $working = AE_WalletAction()->getUserWallet($user_id, "working")->balance;
        $pending = AE_WalletAction()->getUserWallet($user_id, "freezable")->balance;
        $balance = $available + $working + $pending;
        $message = str_ireplace('[balance]', mJobPriceFormat($balance), $message);

        $this->wp_mail($user_email, $subject, $message, array(
           'user_id' => $user_id
        ));
    }

    /**
     * Send email to user when admin decline his withdraw request
     * @param object $withdraw
     * @return void
     * @since 1.0
     * @package MicrojobEngine
     * @category mJob Mailing
     * @author Tat Thien
     */
    public function mJobDeclineWithdraw($withdraw) {
        $user = get_userdata($withdraw->post_author);
        $user_id = $user->ID;
        $user_email = $user->user_email;
        $message = ae_get_option('decline_withdraw');
        $subject = __('Your withdrawal request has been declined', ET_DOMAIN);

        $available = AE_WalletAction()->getUserWallet($user_id)->balance;
        $working = AE_WalletAction()->getUserWallet($user_id, "working")->balance;
        $pending = AE_WalletAction()->getUserWallet($user_id, "freezable")->balance;
        $balance = $available + $working + $pending;
        $message = str_ireplace('[balance]', mJobPriceFormat($balance), $message);
        $message = str_ireplace('[note]', $withdraw->reject_message, $message);

        $this->wp_mail($user_email, $subject, $message, array(
            'user_id' => $user->ID
        ));
    }
    /**
     * Send email to user when admin decline his withdraw request
     * @param object $mjob_order
     * @return void
     * @since 1.0
     * @package MicrojobEngine
     * @category mJob Mailing
     * @author Tat Thien
     */
    public function mJobDeclineMjobOrder($mjob_order) {
        $user = get_userdata($mjob_order->post_author);
        $user_email = $user->user_email;
        $message = ae_get_option('decline_mjob_order');
        $subject = __('Your microjob order request has been declined', ET_DOMAIN);

        $balance = AE_WalletAction()->getUserWallet($user->ID)->balance;
        //$message = str_ireplace('[balance]', mJobPriceFormat($balance), $message);
        $message = str_ireplace('[note]', $mjob_order->reject_message, $message);
        $this->wp_mail($user_email, $subject, $message, array(
            'user_id' => $user->ID
        ));
    }
    /**
     * Send email when user have a new message
     * @param $object $message
     * @return void
     * @since 1.0
     * @package MicrojobEngine
     * @category mJob Mailing
     * @author Tat Thien
     */
    public function mJobNewMessageAlert($message) {
        $from_user = get_userdata($message->from_user);
        $to_user = $message->to_user;

        $email_msg = ae_get_option('new_message_alert');
        $email_msg = str_ireplace('[content]', $message->post_content, $email_msg);
        $subject = sprintf(__('% has sent you a new message on %s', ET_DOMAIN), $from_user->display_name, get_option('blogname'));

        $this->wp_mail($to_user, $subject, $email_msg, array(
            'user_id' => $to_user
        ));
    }

    /**
     * @override get_mail_header of class AE_Mailing
     * return mail header template
     */
    public function get_mail_header() {
        ob_start();
        get_template_part('template/email', 'header');
        $mail_header = ob_get_clean();
        return $mail_header;
    }

    /**
     * @override get_mail_footer of class AE_Mailing
     * return mail footer html template
     */
    function get_mail_footer() {
        ob_start();
        get_template_part('template/email', 'footer');
        $mail_footer = ob_get_clean();
        return $mail_footer;
    }

    /**
     * Filter order data
     * @param string $message
     * @return object $order
     * @since 1.0
     * @package MicrojobEngine
     * @category mJob Mailing
     * @author Tat Thien
     */
    public function mJobFilterOrderPlaceholder($message, $order) {
        $buyer = get_userdata($order->post_author);

        // Buyer name
        $message = str_ireplace('[buyer_name]', $buyer->display_name, $message);

        // Total order
        $total = mJobPriceFormat($order->amount, 'span');
        $message = str_ireplace('[total]', $total, $message);

        // Order link
        $order_permalink = get_the_permalink($order->ID);
        $order_link = '<a href="'. $order_permalink .'">'. $order_permalink .'</a>';
        $message = str_ireplace('[order_link]', $order_link, $message);

        return $message;
    }
    /**
     * Send consumer rights statement
     *
     * @param void
     * @return void
     * @since 1.0
     * @package MicrojobEngine
     * @category void
     * @author JACK BUI
     */
    public function email_consumer_rights( $emails, $file_path ){
        global $user_ID;
        $subject = __('Consumer rights statement email', ET_DOMAIN);
        $email_msg = ae_get_option('consumer_agreement_mail_template', '');
        $attachment = $file_path;
        $result = $this->wp_mail($emails, $subject, $email_msg, array('user_id' => $user_ID),'', $attachment);
    }
    /**
     * Send consumer rights statement
     *
     * @param void
     * @return void
     * @since 1.0
     * @package MicrojobEngine
     * @category void
     * @author JACK BUI
     */
    public function email_agreement( $emails, $file_path ){
        global $user_ID;
        $subject = __('Consumer rights statement email', ET_DOMAIN);
        $email_msg = ae_get_option('agreement_mail_template', '');
        $attachment = $file_path;
        $result = $this->wp_mail($emails, $subject, $email_msg, array('user_id' => $user_ID),'', $attachment);
    }
    /**
      * Email to admin every times a company profile is created
      *
      * @param integer/string $profile_id
      * @return void
      * @since 1.4
      * @package MicrojobEngine
      * @category CREDZU
      * @author JACK BUI
      */
    public function email_company_created($profile_id, $is_edit){
        global $user_ID;
        $subject = __('A new company profile is created', ET_DOMAIN);
        if( $is_edit ){
            $subject = __('A company profile is updated', ET_DOMAIN);
        }
        $link = sprintf('<a href="http://localhost/credzu/wp-admin/post.php?post=%s&action=edit" target="_blank">Here</a>', $profile_id);
        $email_msg = ae_get_option('company_profile_mail', '');
        $email_msg = str_ireplace('[approve_link]', $link , $email_msg);
        $emails = ae_get_option('admin_emails', 'info@credzu.com');
        $result = $this->wp_mail($emails, $subject, $email_msg, array('user_id' => $user_ID));
    }

}
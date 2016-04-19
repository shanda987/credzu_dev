<?php
class mJobConversationAction extends mJobPostAction
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
    public function __construct($post_type = 'ae_message')
    {
        parent::__construct($post_type);
        $this->add_ajax('mjob_conversation_sync', 'mJobConversationSync');
        $this->add_action('ae_after_message', 'mJobAfterSendMessage', 10, 2);
        $this->add_action('wp_enqueue_scripts', 'mJobConversationScripts');
        $this->add_action('ae_message_validate_before_sync', 'mJobConversationValidateBeforeSync');
        $this->add_filter('ae_convert_ae_message', 'mJobConversationFilter');
        $this->add_filter('ae_message_response', 'mJobMessageResponse', 10, 2);
        $this->mail = mJobMailing::getInstance();
    }

    /**
     * Conversation Sync
     * @param void
     * @return void
     * @since 1.0
     * @package MicrojobEngine
     * @category Conversation
     * @author Tat Thien
     */
    public function mJobConversationSync() {
        $request = $_REQUEST;
        switch($request['do_action']) {
            case 'mark_as_read':
                $this->mJobConversationMarkUnread();
                break;
        }
    }

    public function mJobConversationMarkUnread() {
        global $user_ID, $ae_post_factory;
        $post_object = $ae_post_factory->get('ae_message');

        if(!$user_ID) {
            wp_send_json(array(
                'success' => false,
                'msg' => __('Invalid user!', ET_DOMAIN)
            ));
        }

        // Update unread meta
        $unread_conversation = mJobGetUnreadConversation();

        if(!empty($unread_conversation)) {
            foreach($unread_conversation as $unread) {
                update_post_meta($unread->ID, $user_ID . '_conversation_status', 'read');

                // Update read for message
                $unread_messages = mJobGetUnreadMessage($post_object->convert($unread));
                if(!empty($unread_messages)) {
                    foreach($unread_messages as $message) {
                        update_post_meta($message->ID, 'receiver_unread', "");
                    }
                }
            }

            wp_send_json(array(
                'success' => true,
                'msg' => __('Successful', ET_DOMAIN)
            ));
        } else {
            wp_send_json(array(
                'success' => true,
                'msg' => __('No conversations found. Start a new one!', ET_DOMAIN)
            ));
        }
    }

    /**
     * Action after create a message
     * @param object $message;
     * @param array $request
     * @return void
     * @since 1.0
     * @package MicrojobEngine
     * @category Conversation
     * @author Tat Thien
     */
    public function mJobAfterSendMessage($message, $request) {
        global $user_ID;
        if(isset($message['data']) && !empty($message['data'])) {
            $message_data = $message['data'];
            // Update latest reply
            if(isset($message_data->is_conversation) && $message_data->is_conversation == '1') {
                update_post_meta($message_data->ID, 'latest_reply', $message_data->ID);
                update_post_meta($message_data->ID, 'latest_reply_timestamp', time());
                update_post_meta($message_data->ID, 'parent_conversation_id', $message_data->ID);
            } else {
                update_post_meta($message_data->post_parent, 'latest_reply', $message_data->ID);
                update_post_meta($message_data->post_parent, 'latest_reply_timestamp', time());
                update_post_meta($message_data->ID, 'parent_conversation_id', $message_data->post_parent);
            }
            // Update user unread
            if($message_data->from_user == $user_ID) {
                update_post_meta($message_data->ID, 'receiver_unread', true);
                update_post_meta($message_data->ID, 'sender_unread', false);

                // Update unread for conversation
                if(isset($message_data->post_parent)) {
                    update_post_meta($message_data->post_parent, $message_data->to_user.'_conversation_status', 'unread');
                } else {
                    update_post_meta($message_data->ID, $message_data->to_user.'_conversation_status', 'unread');
                }
            }
            // Send email to user
            if($message_data->type == 'message' || $message_data->type == 'conversation') {
                $to_user = get_userdata($message_data->to_user);
                $this->mail->inbox_mail($to_user, $message_data->post_content);
            }

            // Send email dispute to seller and admin
            if($message_data->type == 'dispute') {
                global $ae_post_factory;
                $post_obj = $ae_post_factory->get('mjob_order');
                $post = get_post($message_data->post_parent);
                $order = $post_obj->convert($post);

                update_post_meta($order->ID, 'is_dispute', true);

                if(isset($request['winner']) && !empty($request['winner'])) {
                    $this->mail->mJobDisputeDecision($order, $request['winner']);
                } else {
                    $this->mail->mJobDisputeOrder($order);
                }
            }

            if( isset($request['winner']) && !empty($request['winner']) ){
                $upresult = $this->updateWinnerBalance($request['winner'], $message_data);
                if( $upresult ) {
                    update_post_meta($message_data->ID, 'winner', $request['winner']);
                    $userdata = get_userdata($request['winner']);
                    $user = '';
                    if ($userdata) {
                        $user = $userdata->display_name;
                    }
                    update_post_meta($message_data->ID, 'winner_name', $user);
                    $post = array(
                        'ID' => $message_data->post_parent,
                        'post_status' => 'disputed'
                    );
                    wp_update_post($post);
                }
            }
        }
    }

    /**
     * Conversation scripts
     * @param void
     * @return void
     * @since 1.0
     * @package MicrojobEngine
     * @category Conversation
     * @author Tat Thien
     */
    public function mJobConversationScripts() {
        global $current_user;
        wp_enqueue_script('conversation', get_template_directory_uri() . '/assets/js/conversation.js', array(
            'jquery',
            'underscore',
            'backbone',
            'appengine',
            'front',
            'mjob-auth',
            'ae-message-js'), ET_VERSION, true);

        wp_localize_script('conversation', 'conversation_global', array(
            'file_max_size' => '',
            'file_types' => '',
            'conversation_title' => __('Conversation by ' . $current_user->display_name, ET_DOMAIN),
            'message_title' => __('Message from ' . $current_user->display_name)
        ));
    }

    /**
     * Validate conversation before sync
     * @param array $request
     * @return void
     * @since 1.0
     * @package MicrojobEngine
     * @category void
     * @author Tat Thien
     */
    public function mJobConversationValidateBeforeSync($request) {
        // Check conversation exist between two users
        $flag = true;
        $msg = "";
        if($request['type'] == 'conversation') {
            if(isset($request['from_user']) && isset($request['to_user'])) {
                if(mJobIsHasConversation($request['from_user'], $request['to_user'])) {
                    $flag = false;
                    $msg = __('You have created a conversation with this user. Please go to conversation detail to add reply.', ET_DOMAIN);
                }
            } else {
                $flag = false;
                $msg = __('Don\'t try to hack.', ET_DOMAIN);
            }
        }

        if(!$flag) {
            wp_send_json(array(
                'success' => false,
                'msg' => $msg
            ));
        }
    }

    /**
     * Filter the converted message
     * @param object $result
     * @return object $result
     * @since 1.0
     * @package MicrojobEngine
     * @category Conversation
     * @author Tat Thien
     */
    public function mJobConversationFilter($result) {
        global $user_ID;

        if($result->is_conversation == "1") {
            // Latest reply
            if(isset($result->latest_reply) && !empty($result->latest_reply)) {
                $message = get_post($result->latest_reply);
                if($message->post_author == $user_ID) {
                    $result->latest_reply_text = __('You: ', ET_DOMAIN) . mJobFilterMessageContent($message->post_content);
                } else {
                    $result->latest_reply_text = mJobFilterMessageContent($message->post_content);
                }
                $result->latest_reply_time = et_the_time(get_the_time('U', $message->ID));
            }

            $from_user = $result->from_user;
            $to_user = $result->to_user;

            if($user_ID == $from_user) {
                $user_id = $to_user;
            } else if($user_ID == $to_user) {
                $user_id = $from_user;
            }

            $user_data = get_userdata($user_id);
            if(!empty($user_data)) {
                $result->author_name = $user_data->display_name;
            }
            $result->author_avatar = mJobAvatar($user_id, 80);

            // Message parent
            //$count_unread_msg = mJobGetUnreadMessageCount($result);
            $conversation_status = get_post_meta($result->ID, $user_ID . '_conversation_status', true);
            if($conversation_status == "unread") {
                // If unread
                $result->unread_class = "unread";
            } else {
                $result->unread_class = "";
            }
        } else {
            // Message child
            if($user_ID != $result->post_author) {
                $result->author_avatar = mJobAvatar($result->post_author, 50);
            } else {
                $result->author_avatar = mJobAvatar($user_ID, 50);
            }
        }

        $result->post_content_filtered = mJobFilterMessageContent($result->post_content);
        $result->post_date =  et_the_time(get_the_time('U', $result->ID));

        // Get message attachment
        $output = '<ul>';
        if( !empty($result->et_files) ):
            foreach($result->et_files as $key=> $value){
                $output .= '<li class="image-item" id="'. $value->ID .'">';
                $output .= '<a class="ellipsis" title="'. $value->post_title .'" href="'. $value->guid . '"><i class="fa fa-paperclip"></i>' .$value->post_title. '</a>';
                $output .= '</li>';
            }
        endif;
        $output .= '</ul>';
        $result->message_attachment = $output;
        $result->message_class = mJobGetMessageClass($result->post_author);

        return $result;
    }

    /**
     * update balance after admin decide the winner
     *
     * @param integer $winner
     * @param object $message_data
     * @return void
     * @since 1.0
     * @package MicrojobEngine
     * @category void
     * @author JACK BUI
     */
    public function updateWinnerBalance($winner, $message_data){
        global $ae_post_factory;
        $order_obj = $ae_post_factory->get('mjob_order');
        $order = get_post($message_data->post_parent);
        if( $order ){
            $order = $order_obj->convert($order);
            if( $winner != $order->post_author && $winner != $order->mjob_author ){
                return false;
            }
            else{
                // Check if order is transferred or not
                $is_transferred = get_post_meta($order->ID, "is_transferred", true);
                if(!$is_transferred) {
                    if( $winner == $order->seller_id ) { // Seller win
                        // Transfer working fund to available fund of seller
                        AE_WalletAction()->transferWorkingToAvailable($winner, $order->ID, $order->real_amount);
                    } elseif($winner == $order->post_author) { // Buyer win
                        // Get buyer and seller wallet
                        $winner_wallet = AE_WalletAction()->getUserWallet($winner);
                        $loser_wallet_wf = AE_WalletAction()->getUserWallet($order->mjob_author, "working");
                        $loser_wallet_af = AE_WalletAction()->getUserWallet($order->mjob_author);

                        // Get order amount with commision
                        $price = $order->real_amount;
                        $winner_wallet->balance += $price;

                        // If working fund of seller greater than price
                        if($loser_wallet_wf->balance >= $price) {
                            $loser_wallet_wf->balance -= $price;
                        } else {
                            $loser_wallet_af->balance -= ($price - $loser_wallet_wf->balance);
                            $loser_wallet_wf->balance = 0;
                        }

                        // Update available fund of buyer
                        AE_WalletAction()->setUserWallet($winner, $winner_wallet);

                        // Update working fund of seller
                        AE_WalletAction()->setUserWallet($order->mjob_author, $loser_wallet_wf, "working");
                        AE_WalletAction()->setUserWallet($order->mjob_author, $loser_wallet_af);

                        // Update order transferred
                        update_post_meta($order->ID, 'is_transferred', true);
                    }
                }
                return true;
            }

            update_post_meta($order->ID, "is_transferred", true);
        }

    }
   /**
    * filter response
    *
    * @param void
    * @return void
    * @since 1.0
    * @package MicrojobEngine
    * @category void
    * @author JACK BUI
    */
    public function mJobMessageResponse($response, $request){
        if( isset($request['type']) && $request['type'] == 'dispute'){
            $response['msg'] = __('Your report has been sent.', ET_DOMAIN);
        }
        return $response;
    }
}

$new_instance = mJobConversationAction::getInstance();
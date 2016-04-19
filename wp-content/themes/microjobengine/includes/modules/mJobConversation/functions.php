<?php
/**
 * Update receiver id
 * @param int $user_id
 * @param int $receiver_id
 * @return void
 * @since 1.0
 * @package MicrojobEngine
 * @category Conversation
 * @author Tat Thien
 */
if(!function_exists('mJobUpdateReceiverID')) {
    function mJobUpdateReceiverID($user_id, $receiver_id) {
        // Get array of receiver id
        $receiver_id_arr = mJobGetReceiverID($user_id);
        array_push($receiver_id_arr, $receiver_id);
        update_user_meta($user_id, 'receiver_id', $receiver_id_arr);
    }
}

/**
 * Get array of receivers id of specific user
 * @param int $user_id
 * @return array $receiver_id
 * @since 1.0
 * @package MicrojobEngine
 * @category Conversation
 * @author Tat Thien
 */
if(!function_exists('mJobGetReceiverID')) {
    function mJobGetReceiverID($user_id) {
        $receiver_id = get_user_meta($user_id, 'receiver_id', true);
        if(empty($receiver_id)) {
            return array();
        } else {
            return $receiver_id;
        }
    }
}

/**
 * Check if  two users have conversation
 * @param int $user_id
 * @param int $receiver_id
 * @return boolean
 * @since 1.0
 * @package MicrojobEngine
 * @category Conversation
 * @author Tat Thien
 */
if(!function_exists('mJobIsHasConversation')){
    function mJobIsHasConversation($user_id, $receiver_id) {
        $conversation = mJobGetConversation($user_id, $receiver_id);
        if(!empty($conversation)) {
            return true;
        } else {
            return false;
        }
    }
}

/**
 * Get conversation of two user
 * @param int $first_user    ID of user
 * @param int $second_user   ID of user
 * @return object $result
 * @since 1.0
 * @package MicrojobEngine
 * @category Conversation
 * @author Tat Thien
 */
if(!function_exists('mJobGetConversation')) {
    function mJobGetConversation($first_user, $second_user) {
        $args = array(
            'post_type' => 'ae_message',
            'post_status' => 'publish',
            'meta_query' => array(
                'relation' => 'AND',
                array(
                    'relation' => 'OR',
                    array(
                        'relation' => 'AND',
                        array(
                            'key' => 'to_user',
                            'value' => $first_user,
                        ),
                        array(
                            'key' => 'from_user',
                            'value' => $second_user,
                        )
                    ),
                    array(
                        'relation' => 'AND',
                        array(
                            'key' => 'to_user',
                            'value' => $second_user,
                        ),
                        array(
                            'key' => 'from_user',
                            'value' => $first_user,
                        )
                    )
                ),
                array(
                    'key' => 'is_conversation',
                    'value' => '1',
                )
            )
        );

        $result = get_posts($args);
        return $result;
    }
}

/**
 * Get conversation of an user
 * @param int $user_id
 * @return object $result
 * @since 1.0
 * @package MicrojobEngine
 * @category Conversation
 * @author Tat Thien
 */
if(!function_exists('mJobGetConversationByUser')) {
    function mJobGetConversationByUser($user_id) {
        $args = array(
            'post_type' => 'ae_message',
            'post_status' => 'publish',
            'meta_query' => array(
                'relation' => 'AND',
                array(
                    'relation' => 'OR',
                    array(
                        'key' => 'to_user',
                        'value' => $user_id,
                    ),
                    array(
                        'key' => 'from_user',
                        'value' => $user_id,
                    )
                ),
                array(
                    'key' => 'is_conversation',
                    'value' => '1',
                )
            )
        );

        $result = get_posts($args);
        return $result;
    }
}

/**
 * Get conversation page link
 * @param int $first_user    ID of user
 * @param int $second_user   ID of user
 * @return string $link
 * @since 1.0
 * @package MicrojobEngine
 * @category Conversation
 * @author Tat Thien
 */
if(!function_exists('mJobGetConversationLink')) {
    function mJobGetConversationLink($first_user, $second_user) {
        $posts = mJobGetConversation($first_user, $second_user);
        $link = get_permalink($posts[0]->ID);
        return $link;
    }
}

/**
 * Render class for message item
 * @param int $post_author
 * @return string
 * @since 1.0
 * @package MicrojobEngine
 * @category Conversation
 * @author Tat Thien
 */
if(!function_exists('mJobGetMessageClass')) {
    function mJobGetMessageClass($post_author) {
        global $user_ID;
        if($user_ID == $post_author) {
            return "private-message";
        } else {
            return "guest-message";
        }
    }
}

/**
 * Filter message content
 * @param string $content
 * @return string $content
 * @since 1.0
 * @package MicrojobEngine
 * @category Conversation
 * @author Tat Thien
 */
if(!function_exists('mJobFilterMessageContent')) {
    function mJobFilterMessageContent($content) {
        // Get bad words
        $bad_words = trim(ae_get_option('filter_bad_words'));
        $bad_words = preg_replace('/\s+/', '', $bad_words);

        $content = apply_filters('mjob_before_filter_message_content', $content);
        if(!empty($bad_words)) {
            // Get bad words replace
            $bad_words_replace = ae_get_option('bad_word_replace');
            if(empty($bad_words_replace)) {
                $bad_words_replace = "[bad word]";
            }
            $bad_words_arr = explode(",", $bad_words);
            foreach($bad_words_arr as $bad_word) {
                if(!empty($bad_word)) {
                    $content = str_ireplace($bad_word, $bad_words_replace, $content);
                }
            }
        }

        $content = apply_filters('mjob_after_filter_message_content', $content);

        return $content;
    }
}

/**
 * Return default arguments to get conversations of a user;
 * @param void
 * @return void
 * @since 1.0
 * @package MicrojobEngine
 * @category Conversation
 * @author Tat Thien
 */
if(!function_exists('mJobQueryConversationDefaultArgs')) {
    function mJobQueryConversationDefaultArgs() {
        global $user_ID;
        $default = array(
            'post_type' => 'ae_message',
            'post_status' => 'publish',
            'meta_query' => array(
                'relation' => 'AND',
                array(
                    'relation' => 'OR',
                    array(
                        'key' => 'to_user',
                        'value' => $user_ID,
                    ),
                    array(
                        'key' => 'from_user',
                        'value' => $user_ID,
                    )
                ),
                array(
                    'key' => 'is_conversation',
                    'value' => '1',
                )
            )
        );
        return $default;
    }
}


/**
 * Get unread messages of a conversation
 * @param object $conversation
 * @return int $count
 * @since 1.0
 * @package MicrojobEngine
 * @category Conversation
 * @author Tat Thien
 */
if(!function_exists('mJobGetUnreadMessage')) {
    function mJobGetUnreadMessage($conversation) {
        global $user_ID;
        $count = 0;
        if($user_ID == $conversation->to_user && $conversation->receiver_unread == true) {
            $count = 1;
        }

        $messages = get_posts(array(
            'post_type' => 'ae_message',
            'post_status' => 'publish',
            'posts_per_page' => -1,
            'post_parent' => $conversation->ID,
            'meta_query' => array(
                'relation' => 'AND',
                array(
                    'key' => 'to_user',
                    'value' => $user_ID,
                ),
                array(
                    'key' => 'receiver_unread',
                    'value' => '1'
                )
            )
        ));

       return $messages;
    }
}

/**
 * Get amount of unread messages
 * @param object $conversation
 * @return int $count
 * @since 1.0
 * @package MicrojobEngine
 * @category Conversation
 * @author Tat Thien
 */
if(!function_exists('mJobGetUnreadMessageCount')) {
    function mJobGetUnreadMessageCount($conversation) {
        $messages = mJobGetUnreadMessage($conversation);
        $count = count($messages);
        return $count;
    }
}

/**
 * Get unread conversation
 * @param void
 * @return void
 * @since 1.0
 * @package MicrojobEngine
 * @category void
 * @author Tat Thien
 */
if(!function_exists('mJobGetUnreadConversation')) {
    function mJobGetUnreadConversation() {
        global $user_ID;
        $default = mJobQueryConversationDefaultArgs();
        $args = wp_parse_args(array(
            'posts_per_page' => -1,
            'meta_query' => array(
                'relation' => 'AND',
                array(
                    'key' => $user_ID . '_conversation_status',
                    'value' => 'unread',
                ),
                array(
                    'key' => 'is_conversation',
                    'value' => '1'
                )
            )
        ), $default);
        $conversation = get_posts($args);
        return $conversation;
    }
}

/**
 * Get amount of unread conversation
 * @param void
 * @return int $count
 * @since 1.0
 * @package MicrojobEngine
 * @category Conversation
 * @author Tat Thien
 */
if(!function_exists('mJobGetUnreadConversationCount')) {
    function mJobGetUnreadConversationCount() {
        $conversation = mJobGetUnreadConversation();
        $count = count($conversation);
        return $count;
    }
}
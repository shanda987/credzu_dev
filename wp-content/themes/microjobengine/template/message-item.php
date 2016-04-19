<?php
global $wp_query, $ae_post_factory, $post, $user_ID;
$post_object    = $ae_post_factory->get('ae_message');
$current        = $post_object->current_post;
if($user_ID == $current->to_user) {
    update_post_meta($current->ID, "receiver_unread", "");
}
?>
<li class="clearfix message-item">
    <div class="<?php echo $current->message_class; ?>">
        <div class="img-avatar">
            <?php echo $current->author_avatar; ?>
        </div>
        <div class="conversation-text">
            <?php echo $current->post_content_filtered; ?>
            <?php echo $current->message_attachment; ?>
        </div>

        <span class="message-time">
            <?php echo $current->post_date; ?>
        </span>
    </div>
</li>
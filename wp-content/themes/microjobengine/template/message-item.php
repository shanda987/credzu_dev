<?php
global $wp_query, $ae_post_factory, $post, $user_ID;
$post_object = $ae_post_factory->get('ae_message');
$current = $post_object->convert($post);
if( $current->post_author == $user_ID ){
    $cls = 'company-side';
}
else{
    $cls = 'client-side';
}
if($user_ID == $current->to_user) {
    update_post_meta($current->ID, "receiver_unread", "");
}
?>
<?php if($current->type == 'changelog'): ?>
    <li class="clearfix message-item block-changelog">
        <div class="changelog-item">
            <div class="changelog-text">
                <?php
                echo $current->changelog;
                ?>
            </div>

            <div class="message-time">
                <?php echo $current->post_date; ?>
            </div>
        </div>
    </li>
<?php else: ?>
    <li class="clearfix message-item <?php echo $cls; ?>">
        <div class="<?php echo $current->message_class; ?>">
            <div class="img-avatar">
                <?php echo $current->author_avatar; ?>
            </div>
            <div class="conversation-text">
                <?php echo $current->post_content_filtered; ?>
                <?php echo $current->message_attachment; ?>
            </div>
            <div class="message-time">
                <?php
                if($current->admin_message == true) {
                    echo '<strong>' . __("by Admin", ET_DOMAIN) . '</strong> - ' . $current->post_date;
                } else {
                    echo $current->post_date;
                }
                ?>
            </div>
        </div>
    </li>
<?php endif; ?>

<?php
    global $post, $ae_post_factory, $user_ID;
    $post_object = $ae_post_factory->get('ae_message');
    $current = $post_object->convert($post);
?>
<li class="clearfix conversation-item">
    <div class="inner <?php echo $current->unread_class; ?>">
        <div class="img-avatar">
            <?php echo $current->author_avatar ?>
        </div>
        <div class="conversation-text">
            <p class="name-author"><a href="<?php echo $current->permalink; ?>"><?php echo $current->author_name; ?></a></p>
        <span class="latest-reply">
            <?php echo $current->latest_reply_text; ?>
        </span>
        <p class="latest-reply-time"><?php echo $current->latest_reply_time; ?></p>
        </div>
    </div>
</li>
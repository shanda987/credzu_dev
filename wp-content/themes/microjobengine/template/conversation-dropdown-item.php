<?php
global $post, $ae_post_factory, $user_ID;
$post_object = $ae_post_factory->get('ae_message');
$current = $post_object->convert($post);
?>

<li class="clearfix conversation-item">
    <a href="<?php echo $current->permalink; ?>">
        <div class="inner <?php echo $current->unread_class; ?>">
            <div class="img-avatar">
                <?php echo $current->author_avatar ?>
            </div>
            <div class="conversation-text">
                <span class="latest-reply"> <?php echo $current->post_content; ?></span>
                <span class="latest-reply-time"><?php echo $current->post_date; ?></span>
            </div>
        </div>
    </a>
</li>

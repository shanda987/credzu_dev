<?php
/**
 * Used by Dashboard
 */
global $wp_query, $ae_post_factory, $post;
$post_object    = $ae_post_factory->get('mjob_order');
$current        = $post_object->current_post;
?>
<li class="task-item">
    <h2><a href="<?php echo $current->permalink; ?>"><?php echo $current->post_title; ?></a></h2>
    <div class="label-status <?php echo $current->status_class; ?>">
        <span><?php echo $current->status_text; ?></span>
    </div>
    <p><?php _e('Order by ', ET_DOMAIN);?><span class="author-name"> <?php echo $current->author_name;?></span></p>
    <span class="date-post">
        <?php echo et_the_time(get_the_time('U')); ?>
    </span>
</li>

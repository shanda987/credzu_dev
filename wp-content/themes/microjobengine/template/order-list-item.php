<?php
/**
 * Used by Dashboard
 */
global $wp_query, $ae_post_factory, $post;
$post_object    = $ae_post_factory->get('mjob_order');
$current        = $post_object->current_post;

$mjob = get_post($current->post_parent);
$author = get_userdata($mjob->post_author);
$author_name = $author->initial_display_name;
?>
<li class="order-item">
    <h2><a href="<?php echo $current->permalink; ?>"><?php echo $current->post_title; ?></a></h2>
    <div class="label-status <?php echo $current->status_class; ?>">
        <span><?php echo $current->status_text; ?></span>
    </div>
    <p class="author"><span><?php _e('Author ', ET_DOMAIN);?></span> <span class="author-name"><?php echo $author_name; ?></span></p>
    <span class="date-post">
        <?php echo et_the_time(get_the_time('U')); ?>
    </span>
</li>


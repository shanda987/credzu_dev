<?php
/**
 * The template for displaying post details in a loop
 * @since 1.0
 * @package MicrojobEngine
 * @category Template
 */
global $wp_query, $ae_post_factory, $post;
$post_object    = $ae_post_factory->get('post');
$current        = $post_object->current_post;
?>
<li class="post-item clearfix">
    <div class="image-avatar col-lg-4 col-md-4 col-sm-5 col-xs-12 image-post">
        <a href="<?php echo $current->permalink; ?>">
            <img src="<?php echo $current->the_post_thumbnail; ?>" alt="" class="img-responsive">
        </a>
    </div>
    <div class="info-items col-lg-8 col-md-8 col-sm-7 col-xs-12 article-post">
        <p class="author-post"><?php echo sprintf(__('Written by %s', ET_DOMAIN), $current->author_name); ?></p>
        <p class="date-post"><?php echo $current->post_date ;?></p>
        <h2><a href="<?php echo $current->permalink ?>"><?php echo $current->post_title; ?></a></h2>
        <div class="group-function">
            <?php echo $current->post_excerpt; ?>
            <a href="<?php echo $current->permalink; ?>" class="more"><?php _e('Read more', ET_DOMAIN); ?></a>
            <p class="total-comments"><?php echo $current->comment_number; ?></p>
        </div>
    </div>
</li>
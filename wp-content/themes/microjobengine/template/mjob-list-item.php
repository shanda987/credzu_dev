<?php
/**
 * Used by Dashboard
 */
global $wp_query, $ae_post_factory, $post;
$post_object    = $ae_post_factory->get('mjob_post');
$current        = $post_object->current_post;
?>

<li class="clearfix">
    <div class="image-avatar">
        <a href="<?php echo $current->permalink; ?>">
            <img src="<?php echo $current->the_post_thumbnail; ?>" alt="">
        </a>
    </div>
    <div class="info-items">
        <h2><a href="<?php echo $current->permalink ?>"><?php echo $current->post_title; ?></a></h2>
        <?php if(!is_author()) : ?>
            <div class="label-status <?php echo $current->status_class; ?>">
                <span><?php echo $current->status_text; ?></span>
            </div>
        <?php endif; ?>
        <div class="group-function">
            <div class="vote">
                <div class="rate-it star" data-score="<?php echo $current->rating_score; ?>"></div>
                <span class="total-review"><?php printf('(%s)', $current->mjob_total_reviews); ?></span>
            </div>
            <span class="price"><?php echo $current->et_budget_text ?></span>
        </div>
    </div>
</li>

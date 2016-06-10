<?php
global $wp_query, $ae_post_factory, $post;
$post_object    = $ae_post_factory->get( 'mjob_post' );
$current        = $post_object->current_post;
?>
<li class="col-lg-4 col-md-4 col-sm-4 col-xs-6 mjob-item animation-element animated" nameAnimation="zoomIn">
    <div class="inner clearfix">
        <div class="ribbon-featured"><span><?php _e('Featured', ET_DOMAIN); ?></span></div>
        <div class="vote">
            <div class="rate-it star" data-score="<?php echo $current->rating_score; ?>"></div>
            <span class="total-review"><?php printf('(%s)', $current->mjob_total_reviews); ?></span>
        </div>

        <?php if(!is_search()) : ?>
        <div class="bookmark">
            <p class="marks <?php echo $current->status_class; ?>"><?php echo $current->status_text; ?></p>
        </div>
        <?php else: ?>
            <?php if( isset($current->et_featured) && $current->et_featured == 1): ?>
                <div class="bookmark">
                    <p class="marks featured-color"><?php _e('Featured', ET_DOMAIN) ?></p>
                </div>
            <?php endif; ?>
        <?php endif; ?>

        <div class="set-status">
            <a href="<?php echo $current->permalink; ?>"><img src="<?php echo $current->the_post_thumbnail; ?>" alt=""></a>
            <?php
                get_template_part('template/manage', 'action');
            ?>
        </div>
        <h2 class="name-job"><a href="<?php echo $current->permalink; ?>"><?php echo $current->post_title; ?></a></h2>
        <div class="author">
            <p title="<?php echo $current->author_name; ?>"><span class="by-author"><?php _e('by ', ET_DOMAIN); ?></span> <?php echo $current->author_name;?></p>
        </div>
        <div class="price">
            <span><?php echo $current->et_budget_text ?></span>
        </div>
    </div>
</li>
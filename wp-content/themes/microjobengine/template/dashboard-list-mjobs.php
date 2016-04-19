<?php
    global $user_ID, $ae_post_factory;
    $post_obj = $ae_post_factory->get('mjob_post');

    $defaults = array();
    wp_reset_query();
    if(is_page_template('page-dashboard.php')) {
        $defaults = array(
            'posts_per_page' => 5,
            'orderby' => 'date',
            'post_status'=> array(
                'pending',
                'publish',
                'reject',
                'archive',
                'pause',
                'unpause',
                'draft'
            ),
        );
    }

    if(is_author()) {
        $user_ID = get_query_var('author');
        $defaults = array(
            'post_status' => array('publish', 'pause', 'unpause')
        );
    }
?>

<div class="list-job">
    <?php
    $args = array(
        'post_type' => 'mjob_post',
        'author' => $user_ID
    );

    $args = wp_parse_args($args, $defaults);
    $mjob_posts = new WP_Query($args);
    $postdata = array();
    if($mjob_posts->have_posts()) {
        ?>
        <ul>
            <?php
            while($mjob_posts->have_posts()) {
                $mjob_posts->the_post();
                $convert = $post_obj->convert($post);
                $postdata[] = $convert;
                get_template_part('template/mjob-list', 'item');
            }

            wp_reset_postdata();
            ?>
        </ul>

        <?php if(is_page_template('page-dashboard.php')) : ?>
            <div class="view-all float-center"><a href="<?php echo et_get_page_link('my-listing-jobs'); ?>"><?php _e('View all', ET_DOMAIN); ?></a></div>
        <?php endif; ?>
    <?php } else { ?>
        <p class="no-items"><?php _e('There are no mJobs found!', ET_DOMAIN); ?></p>
    <?php } ?>

    <?php
    if( !is_page_template('page-dashboard.php') ):
        echo '<div class="paginations-wrapper">';
        ae_pagination($mjob_posts, get_query_var('paged'), 'load');
        echo '</div>';
        /**
         * render post data for js
         */
        echo '<script type="data/json" class="mjob_postdata" >' . json_encode($postdata) . '</script>';
    endif;
    ?>
</div>
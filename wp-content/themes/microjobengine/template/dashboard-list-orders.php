<?php
global $ae_post_factory, $user_ID, $wp_query;
$post_obj = $ae_post_factory->get('mjob_order');
$default = array();
if( is_page_template('page-dashboard.php') ){
    $default = array('posts_per_page'=> 5);
}
?>
<div class="list-order">
    <?php
    $args = array(
        'post_type' => 'mjob_order',
        'post_status' => array(
            'publish',
            'late',
            'pending',
            'delivery',
            'disputing',
            'disputed',
            'finished',
            'processing',
            'verification'
            ),
        'author' => $user_ID,
        'orderby'=> 'date',
        'order'=> 'DECS'
    );
    $args = wp_parse_args($args, $default);
    $cus_query = new WP_Query($args);
    $postdata = array();
    if($cus_query->have_posts()) { ?>
        <ul class="list-orders">
            <?php
            while($cus_query->have_posts()) {
                $cus_query->the_post();
                $convert = $post_obj->convert($post);
                $postdata[] = $convert;
                get_template_part('template/order-list', 'item');
            }
            wp_reset_postdata();
            ?>
        </ul>
        <?php if(is_page_template('page-dashboard.php')) : ?>
            <div class="view-all float-center"><a href="<?php echo et_get_page_link('my-list-order'); ?>"><?php _e('View all', ET_DOMAIN); ?></a></div>
        <?php endif; ?>

    <?php } else { ?>
        <div class="dashboard-notification">
            <p class="cl-items"><?php _e('The bad news?', ET_DOMAIN); ?></p>
            <p class="cl-items"><?php _e('... you not have hired a company?', ET_DOMAIN); ?></p>
            <p class="cl-items"><?php _e('The good news?', ET_DOMAIN); ?></p>
            <?php $archive_link =  get_post_type_archive_link('mjob_post'); ?>
            <p class="cl-items"><?php echo sprintf(__('... you can <a href="%s">click here</a> to view companies and service', ET_DOMAIN),  $archive_link); ?></p>
        </div>
    <?php } ?>

    <?php
    if( !is_page_template('page-dashboard.php') ):
        echo '<div class="paginations-wrapper">';
        ae_pagination($cus_query, get_query_var('paged'), 'load');
        echo '</div>';
        /**
         * render post data for js
         */
        echo '<script type="data/json" class="order_postdata" >' . json_encode($postdata) . '</script>';
    endif;
    ?>
</div>

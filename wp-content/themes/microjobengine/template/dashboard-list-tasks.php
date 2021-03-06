<?php
    global $ae_post_factory, $user_ID;
    $post_obj = $ae_post_factory->get('mjob_order');
    $default = array();
    if( is_page_template('page-dashboard.php') ){
        $default = array('posts_per_page'=> 5);
    }
    $profile = mJobProfileAction()->getProfile($user_ID);
?>
<div class="list-order list-task-wrapper">
    <?php
    $args = array(
        'post_type' => 'mjob_order',
        'post_status' => array(
            'publish',
            'delivery',
            'disputed',
            'disputing',
            'late',
            'finished',
            'processing',
            'verification'
        ),
        'meta_key' => 'seller_id',
        'meta_value' => array($user_ID),
        'meta_compare' => 'IN'
    );
    $args = wp_parse_args($args, $default);
    $postdata = array();
    $task_query = new WP_Query($args);
    if($task_query->have_posts()) {
        ?>
        <ul class="list-tasks">
            <?php
            while($task_query->have_posts()) {
                $task_query->the_post();
                $convert = $post_obj->convert($post);
                $postdata[] = $convert;
                get_template_part('template/task-list', 'item');
            }

            wp_reset_postdata();
            ?>
        </ul>

<!--        --><?php //if(is_page_template('page-dashboard.php')) : ?>
<!--            <div class="view-all float-center"><a href="--><?php //echo et_get_page_link('my-list-order'); ?><!--">--><?php //_e('View all', ET_DOMAIN); ?><!--</a></div>-->
<!--        --><?php //endif; ?>
    <?php } else { ?>
        <p class="no-items">
        <?php
            $absolute_url = full_url( $_SERVER );
            $post_link = et_get_page_link('post-service') . '?return_url=' . $absolute_url;
            //$post_link = "creating a listing once approved.";
            if ($profile->company_status == COMPANY_STATUS_APPROVED) {
                $post_link = "<a href='$post_link'>creating a listing</a>";
            }

            _e("Currently, you do not have any clients<br>", ET_DOMAIN);
            if( empty($profile->personal_profile_completed) && empty($profile->company_profile_completed) && empty($profile->billing_completed) && empty($profile->company_agreement_link) && empty($profile->create_listing_completed) ):
               _e("Complete the following requirements:<br>", ET_DOMAIN);
            endif;
               if( empty($profile->personal_profile_completed) ):
                _e('Personal profile<br/>', ET_DOMAIN);
               endif;
               if( empty($profile->company_profile_completed)):
                _e('Company Profile <br/>', ET_DOMAIN);
               endif;
               if( empty($profile->billing_completed)):
                _e('Billing information<br/>', ET_DOMAIN);
                endif;
               if( empty($profile->company_agreement_link)):
               _e('Agreement<br/>', ET_DOMAIN);
               endif;
               // if( empty($profile->create_listing_completed)):
               // _e("Create Listing<br/>", ET_DOMAIN);
               // endif; 
			   ?>
        </p>
    <?php } ?>
</div>
<?php
//if( is_page_template('page-dashboard.php') ):
    echo '<div class="paginations-wrapper">';
    $task_query->query = array_merge($task_query->query, array('is_task' => true));
    ae_pagination($task_query, get_query_var('paged'), 'load');
    echo '</div>';
    /**
     * render post data for js
     */
    echo '<script type="data/json" class="task_postdata" >' . json_encode($postdata) . '</script>';
//endif;

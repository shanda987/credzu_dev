<?php
    global $ae_post_factory, $user_ID;
    $default = array();
    if( is_page_template('page-dashboard.php') ){
        $default = array('posts_per_page'=> 5);
    }
?>
<div class="list-order list-task-wrapper">
    <?php
    $args = array(
        'role' => COMPANY
    );
    $companies = get_users($args);

    if($companies) {
        ?>
        <ul class="list-tasks">
            <?php
            foreach ($companies as $key => $value) {
                $profile = mJobProfileAction()->getProfile($value->ID);
                // @TODO: Below will be a Notification for everything
                ?>
                <li class="task-item">
                    <div>
                    <h2><?php echo $profile->post_title; ?></h2>
                    <p><?php _e('Status: ', ET_DOMAIN);?>
                        <?php echo ($profile->company_status != '') ? $profile->company_status : COMPANY_STATUS_REGISTERED; ?>
                        <?php echo $profile->author_name;?>
                    </p>
                        <span class="date-post">
                            <?php echo et_the_time(get_the_time('U')); ?>
                        </span>
                    </div>
                    <div class="pull-right">
                        <a class="btn-basic" href="?user_id=<?=$value->ID?>&company_status=<?=COMPANY_STATUS_APPROVED?>">Approve</a>
                        <a class="btn-basic" href="?user_id=<?=$value->ID?>&company_status=<?=COMPANY_STATUS_DECLINED?>">Decline</a>
                        <a class="btn-basic" href="?user_id=<?=$value->ID?>&company_status=<?=COMPANY_STATUS_SUSPENDED?>">Suspend</a>
                        <a class="btn-basic" href="?user_id=<?=$value->ID?>&company_status=<?=COMPANY_STATUS_NEEDS_CHANGES?>">Needs Changes</a>
                    </div>
                    <div class="clearfix"></div>
                </li>
                <?php
            }
            // while($task_query->have_posts()) {
            //     $task_query->the_post();
            //     $convert = $post_obj->convert($post);
            //     $postdata[] = $convert;
            //     get_template_part('template/task-list', 'item');
            // }

            // wp_reset_postdata();
            ?>
        </ul>

        <?php if(is_page_template('page-dashboard.php')) : ?>
            <div class="view-all float-center"><a href="<?php echo et_get_page_link('my-list-order'); ?>"><?php _e('View all', ET_DOMAIN); ?></a></div>
        <?php endif; ?>
    <?php } ?>
</div>
<?php
if( !is_page_template('page-dashboard.php') ):
    echo '<div class="paginations-wrapper">';
    $task_query->query = array_merge($task_query->query, array('is_task' => true));
    ae_pagination($task_query, get_query_var('paged'), 'load');
    echo '</div>';
    /**
     * render post data for js
     */
    echo '<script type="data/json" class="task_postdata" >' . json_encode($postdata) . '</script>';
endif;

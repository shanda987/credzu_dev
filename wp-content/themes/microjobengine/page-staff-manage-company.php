<?php
/**
 * Template Name: Page Profile
 */
global $wp_query, $ae_post_factory, $post, $current_user, $user_ID, $is_individual;

get_header();
?>
    <div class="container mjob-profile-page">
        <div class="title-top-pages">
            <p class="block-title"><?php _e('MANAGE COMPANIES', ET_DOMAIN); ?></p>
            <p><a href="<?php echo et_get_page_link('dashboard'); ?>" class="btn-back"><i class="fa fa-angle-left"></i><?php _e('Back to dashboard', ET_DOMAIN); ?></a></p>
        </div>
        <div class="row profile">
            <div class="col-lg-4 col-md-4 col-sm-12 col-sx-12 block-items-detail profile">
                <?php get_sidebar('my-profile'); ?>
            </div>

            <div class="col-lg-8 col-md-8 col-sm-12 col-sx-12">
                <div class="block-profile">
                    <div class="list-order list-task-wrapper">
                        <?php
                        $args = array(
                            'role'         => COMPANY
                        );
                        $companies = get_users($args);

                        if($companies) {
                            ?>
                            <ul class="list-tasks">
                                <?php
                                foreach ($companies as $key => $value) {
                                    $profile = mJobProfileAction()->getProfile($value->ID);
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
                </div>
            </div>

        </div>
    </div>
<?php
get_footer();
?>
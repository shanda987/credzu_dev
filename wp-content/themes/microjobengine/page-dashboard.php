<?php
/**
 * Template Name: Dashboard
 *
 * @param void
 * @return void
 * @since 1.0
 * @package MicrojobEngine
 * @category void
 * @author JACK BUI
 */
global $current_user, $ae_post_factory, $user_ID;
// Get user info
$user = mJobUser::getInstance();
$user_data = $user->convert($current_user->data);
$user_role = ae_user_role($current_user->ID);

/*
 * @TODO Jesse You should use hook template_redirect to redirect a page Don't use wp_redirect direct on a page template.
 *
 * @TODO Admin and Individual user role also can view dashbaord too.
 *
 */
// Protect the Page
//if ($user_role !== COMPANY && !is_super_admin()) {
//    wp_redirect(home_url()); exit;
//}
$profile = mJobProfileAction()->getProfile($user_ID);
echo mJobProfileAction()->getProfileJson($profile);

get_header();

// If Company, this outputs the Company Status bar (Doesn't show when approved)
echo mJobProfileAction()->display_company_status($user_role, $profile->company_status);
?>

    <div id="content">
        <div class="block-page">
            <div class="container dashboard withdraw">
                <div class="row title-top-pages">
                    <p class="block-title"><?php _e('DASHBOARD', ET_DOMAIN); ?></p>
                </div>
                <div class="row profile">
                    <div class="col-lg-3 col-md-3 col-sm-12 col-sx-12 block-items-detail profile">
                        <?php get_sidebar('my-profile'); ?>
                    </div>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-sx-12 outer-revenues">
                        <div class="information-items-detail box-shadow">
                            <div class="tabs-information">

                                <div class="order-ct">
                                    <?php if ($user_role == INDIVIDUAL):?>
                                    <div role="tabpanel" class="tab-pane active order-container-control" id="order">
                                        <?php get_template_part('template/dashboard-list', 'orders'); ?>
                                    </div>
                                    <?php elseif ($user_role == COMPANY): ?>
                                    <div role="tabpanel" class="tab-pane task-container-control" id="task">
                                        <?php get_template_part('template/dashboard-list', 'tasks'); ?>
                                    </div>
                                    <?php elseif ($user_role == STAFF): ?>
                                    <div role="tabpanel" class="tab-pane task-container-control" id="task"> <!-- Change ID? -->
                                        <?php get_template_part('template/dashboard-list', 'staff'); ?>
                                    </div>
                                    <?php elseif ($user_role == ADMIN): ?>
                                    <div role="tabpanel" class="tab-pane task-container-control" id="task">
                                        <div class="list-order list-task-wrapper">
                                            <p class="no-items">The Admin Dashboard</p>
                                        </div>
                                    </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <!--                    <div class="load-more">-->
                        <!--                        <a href="" class="link-more">Load more<i class="fa fa-angle-right"></i></a>-->
                        <!--                    </div>-->
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php
get_footer();

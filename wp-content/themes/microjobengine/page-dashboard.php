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

// Protect the Page
if ($user_role !== COMPANY) {
    wp_redirect(home_url()); exit;
}



$profile_obj = $ae_post_factory->get('mjob_profile');
$profile_id = get_user_meta($user_ID, 'user_profile_id', true);

if($profile_id) {
    $post = get_post($profile_id);
    if($post && !is_wp_error($post)) {
        $profile = $profile_obj->convert($post);
    }
    echo '<script type="text/json" id="mjob_profile_data" >'.json_encode($profile).'</script>';
}

get_header();
?>

    <?php if ($user_role == COMPANY):?>
    <div>
    <!--
    @TODO
    Check if meta of user profile "company_approved" is set and it == 1
    add CSS styles to put at top
    -->
    Your account is pending. You must complete your profile and then click <a href="plus-circle">Activate Account</a> in order to post listings.
    </div>
    <?php endif; ?>


    <div id="content">
        <div class="block-page">
            <div class="container dashboard withdraw">
                <div class="row title-top-pages">
                    <p class="block-title"><?php _e('DASHBOARD', ET_DOMAIN); ?></p>
                    <p><?php _e('Here are your current orders', ET_DOMAIN); ?></p>
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

<?php
global $wp_query, $ae_post_factory, $post, $current_user, $user_ID;
$user = mJobUser::getInstance();
$user_data = $user->convert($current_user->data);
$user_role = ae_user_role($current_user->ID);

// Convert profile
$profile_obj = $ae_post_factory->get('mjob_profile');
$profile_id = get_user_meta($user_ID, 'user_profile_id', true);
if($profile_id) {
    $post = get_post($profile_id);
    if($post && !is_wp_error($post)) {
        $profile = $profile_obj->convert($post);
    }
}

$user_role = ae_user_role($current_user->ID);

$country_id = isset($profile->tax_input['country'][0]) ? $profile->tax_input['country'][0]->term_id : '';
$languages = isset($profile->tax_input['language']) ? $profile->tax_input['language'] : '';
$display_name = isset($user_data->display_name) ? $user_data->display_name : '';
?>
<div class="block-statistic">
    <div class="dropdown">
        <button class="button-dropdown-menu" id="dLabel" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            Categories
            <span class="caret"></span>
        </button>
        <ul class="dropdown-menu">
            <li class="hvr-wobble-horizontal"><a href="<?php echo et_get_page_link('dashboard'); ?>"><?php _e('Dashboard', ET_DOMAIN); ?></a></li>
            <?php if ($user_role == INDIVIDUAL):?>
                <li class="hvr-wobble-horizontal"><a href="<?php echo et_get_page_link('profile'); ?>"><?php _e('My Profile', ET_DOMAIN); ?></a></li>
                <li class="hvr-wobble-horizontal"><a href="<?php echo et_get_page_link('revenues'); ?>"><?php _e('Billing information', ET_DOMAIN); ?></a></li>
                <li class="hvr-wobble-horizontal"><a href="#"><?php _e('Earn money', ET_DOMAIN); ?></a></li>
                <li class="line-distance"></li>
                <li class="hvr-wobble-horizontal"><a href="<?php echo et_get_page_link('payment-method') ?>"><?php _e('Payment method', ET_DOMAIN); ?></a></li>
                <li class="hvr-wobble-horizontal"><a href="<?php echo et_get_page_link('change-password') ?>"><?php _e('Change password', ET_DOMAIN); ?></a></li>
            <?php elseif ($user_role == COMPANY): ?>
                <li class="hvr-wobble-horizontal"><a href="#"><?php _e('My Listings', ET_DOMAIN); ?></a></li>
                <li class="hvr-wobble-horizontal"><a href="<?php echo et_get_page_link('profile'); ?>"><?php _e('Personal Profile', ET_DOMAIN); ?></a></li>
                <li class="hvr-wobble-horizontal"><a href="<?php echo et_get_page_link('profile-company'); ?>"><?php _e('Company Profile', ET_DOMAIN); ?></a></li>
                <li class="hvr-wobble-horizontal"><a href="<?php echo et_get_page_link('billing-info-company'); ?>"><?php _e('Billing Information', ET_DOMAIN); ?></a></li>
                <li class="line-distance"></li>
                <li class="hvr-wobble-horizontal"><a href="<?php echo et_get_page_link('change-password') ?>"><?php _e('Change password', ET_DOMAIN); ?></a></li>
            <?php elseif ($user_role == ADMIN):?>
                <!-- Admin Options -->
            <?php elseif ($user_role == STAFF):?>
                <li>Staff</li>
            <?php else:?>
                <li>Error: Unknown Role</li>
                <!-- For any user role not checked -->
            <?php endif; ?>
        </ul>
    </div>
</div>
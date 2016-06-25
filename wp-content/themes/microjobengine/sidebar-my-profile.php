<?php
global $wp_query, $ae_post_factory, $post, $current_user, $user_ID;
$user = mJobUser::getInstance();
$user_data = $user->convert($current_user->data);
$user_role = ae_user_role($current_user->ID);

$profile = mJobProfileAction()->getProfile($user_ID);

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
            <li class="hvr-wobble-horizontal"><a <?=(is_page('dashboard')) ? 'class="active"' : '' ?>" href="<?php echo et_get_page_link('dashboard'); ?>"><?php _e('Dashboard', ET_DOMAIN); ?></a></li>
            <?php if ($user_role == INDIVIDUAL ):?>
                <li class="hvr-wobble-horizontal"><a <?=(is_page('profile')) ? 'class="active"' : '' ?>" href="<?php echo et_get_page_link('profile'); ?>"><?php _e('My Profile', ET_DOMAIN); ?></a></li>
                <li class="hvr-wobble-horizontal"><a <?=(is_page('earn-money')) ? 'class="active"' : '' ?>" href="#"><?php _e('Earn money', ET_DOMAIN); ?></a></li>
                <li class="line-distance"></li>
                <li class="hvr-wobble-horizontal"><a <?=(is_page('billing-info')) ? 'class="active"' : '' ?>" href="<?php echo et_get_page_link('billing-info'); ?>"><?php _e('Billing information', ET_DOMAIN); ?></a></li>
            <?php elseif ($user_role == COMPANY ): ?>
                <li class="hvr-wobble-horizontal"><a <?=(is_page('my-listing-jobs')) ? 'class="active"' : '' ?>" href="<?php echo et_get_page_link('my-listing-jobs'); ?>"><?php _e('My Listings', ET_DOMAIN); ?></a></li>
                <li class="hvr-wobble-horizontal"><a <?=(is_page('profile')) ? 'class="active"' : '' ?>" href="<?php echo et_get_page_link('profile'); ?>"><?php _e('Personal Profile', ET_DOMAIN); ?></a></li>
                <li class="hvr-wobble-horizontal"><a <?=(is_page('profile-company')) ? 'class="active"' : '' ?>" href="<?php echo et_get_page_link('profile-company'); ?>"><?php _e('Company Profile', ET_DOMAIN); ?></a></li>
                <li class="hvr-wobble-horizontal"><a <?=(is_page('billing-info-company')) ? 'class="active"' : '' ?>" href="<?php echo et_get_page_link('billing-info-company'); ?>"><?php _e('Billing Information', ET_DOMAIN); ?></a></li>
                <li class="hvr-wobble-horizontal"><a <?=(is_page('signature-company')) ? 'class="active"' : '' ?>" href="<?php echo et_get_page_link('signature-company'); ?>"><?php _e('Agreement', ET_DOMAIN); ?></a></li>
                <li class="line-distance"></li>
            <?php elseif ($user_role == ADMIN):?>
                <!-- Admin Options -->
                <li class="hvr-wobble-horizontal"><a <?=(is_page('profile')) ? 'class="active"' : '' ?>" href="<?php echo et_get_page_link('profile'); ?>"><?php _e('Personal Profile', ET_DOMAIN); ?></a></li>
                <li class="line-distance"></li>
            <?php elseif ($user_role == STAFF):?>
                <li class="hvr-wobble-horizontal"><a <?=(is_page('profile')) ? 'class="active"' : '' ?>" href="<?php echo et_get_page_link('profile'); ?>"><?php _e('Personal Profile', ET_DOMAIN); ?></a></li>
                <li class="hvr-wobble-horizontal"><a <?=(is_page('staff-manage-company')) ? 'class="active"' : '' ?>" href="<?php echo et_get_page_link('staff-manage-company') ?>"><?php _e('Manage Companies', ET_DOMAIN); ?></a></li>
                <li class="hvr-wobble-horizontal"><a <?=(is_page('staff-manage-listing')) ? 'class="active"' : '' ?>" href="<?php echo et_get_page_link('staff-manage-listing') ?>"><?php _e('Manage Listings', ET_DOMAIN); ?></a></li>
                <li class="hvr-wobble-horizontal"><a <?=(is_page('staff-manage-billing')) ? 'class="active"' : '' ?>" href="<?php echo et_get_page_link('staff-manage-billing') ?>"><?php _e('Manage Billing', ET_DOMAIN); ?></a></li>
                <li class="hvr-wobble-horizontal"><a <?=(is_page('staff-manage-dispute')) ? 'class="active"' : '' ?>" href="<?php echo et_get_page_link('staff-manage-dispute') ?>"><?php _e('Manage Disputes', ET_DOMAIN); ?></a></li>
                <li class="line-distance"></li>
            <?php else:?>
                <li>Error: Unknown Role</li>
                <!-- For any user role not checked -->
            <?php endif; ?>
                <li class="hvr-wobble-horizontal"><a <?=(is_page('change-password')) ? 'class="active"' : '' ?>" href="<?php echo et_get_page_link('change-password') ?>"><?php _e('Change password', ET_DOMAIN); ?></a></li>
        </ul>
    </div>
</div>
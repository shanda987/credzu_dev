<?php
/**
 * Template Name: Page Profile
 */
global $wp_query, $ae_post_factory, $post, $current_user, $user_ID, $is_individual;
$user = mJobUser::getInstance();
$user_data = $user->convert($current_user->data);
$user_role = ae_user_role($current_user->ID);

$profile = mJobProfileAction()->getProfile($user_ID);
echo mJobProfileAction()->getProfileJson($profile);

$description = !empty($profile->profile_description) ? $profile->profile_description : __('There is no content', ET_DOMAIN);
$payment_info = !empty($profile->payment_info) ? $profile->payment_info : __('There is no content', ET_DOMAIN);
$billing_full_name = !empty($profile->billing_full_name) ? $profile->billing_full_name : '';
$billing_full_address = !empty($profile->billing_full_address) ? $profile->billing_full_address : '';
$billing_country = !empty($profile->billing_country) ? $profile->billing_country : '';
$billing_vat = !empty($profile->billing_vat) ? $profile->billing_vat : __('There is no content', ET_DOMAIN);
$first_name = !empty($profile->first_name) ? $profile->first_name : '';
$last_name = !empty($profile->last_name) ? $profile->last_name : '';
$phone = !empty($profile->phone) ? $profile->phone : '';
$business_email = !empty($profile->business_email) ? $profile->business_email : $user_data->user_email;

if ( $is_individual ) {
    $credit_goal = !empty($profile->credit_goal) ? $profile->credit_goal : '';
}

get_header();

// If Company, this outputs the Company Status bar (Doesn't show when approved)
echo mJobProfileAction()->display_company_status($user_role, $profile->company_status);
?>
    <div class="container mjob-profile-page">
        <div class="title-top-pages">
            <p class="block-title"><?php _e('MY PROFILE', ET_DOMAIN); ?></p>
            <p><a href="<?php echo et_get_page_link('dashboard'); ?>" class="btn-back"><i class="fa fa-angle-left"></i><?php _e('Back to dashboard', ET_DOMAIN); ?></a></p>
        </div>
        <div class="row profile">
            <div class="col-lg-4 col-md-4 col-sm-12 col-sx-12 block-items-detail profile">
                <?php get_sidebar('my-profile'); ?>
            </div>

            <div class="col-lg-8 col-md-8 col-sm-12 col-sx-12">
                <div class="block-profile">
                    <div class="status-customer float-right" style="display: none">
                        <select name="user_status" id="user_status" data-edit="user" class="user-status">
                            <?php if($user_data->user_status == 'online') { ?>
                                <option value="online" selected><?php _e('Online', ET_DOMAIN); ?></option>
                                <option value="offline"><?php _e('Offline', ET_DOMAIN); ?></option>
                            <?php } else { ?>
                                <option value="online"><?php _e('Online', ET_DOMAIN); ?></option>
                                <option value="offline" selected><?php _e('Offline', ET_DOMAIN); ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="float-center profile-avatar">
                        <div class="upload-profile-avatar">
                            <div class="back-top-hover"><i class="fa fa-upload"></i></div>
                            <a href="#" class="">
                                <?php
                                echo mJobAvatar($user_ID, 75);
                                ?>
                            </a>
                        </div>
                    </div>
                    <div class="block-billing mjob-profile-form">
                        <form class="et-form">
                        <div class="form-group clearfix">
                            <div class="input-group">
                                <div class="input-group-addon no-addon"><?php _e('First Name:', ET_DOMAIN); ?></div>
                                <input type="text" name="first_name" id="first_name" placeholder="<?php _e('First Name', ET_DOMAIN); ?>" value="<?php echo $first_name; ?>">
                            </div>
                        </div>
                        <div class="form-group clearfix">
                            <div class="input-group">
                                <div class="input-group-addon no-addon"><?php _e('Last Name:', ET_DOMAIN); ?></div>
                                <input type="text" name="last_name" id="last_name" placeholder="<?php _e('Last Name', ET_DOMAIN); ?>" value="<?php echo $last_name; ?>">
                            </div>
                        </div>
                        <div class="form-group clearfix">
                            <div class="input-group">
                                <div class="input-group-addon no-addon"><?php _e('Phone:', ET_DOMAIN); ?></div>
                                <input type="text" name="phone" id="phone" placeholder="<?php _e('Phone', ET_DOMAIN); ?>" value="<?php echo $phone; ?>">
                            </div>
                        </div>
                        <div class="form-group clearfix">
                            <div class="input-group">
                                <div class="input-group-addon no-addon"><?php _e('Email:', ET_DOMAIN); ?></div>
                                <input type="email" name="business_email" id="business_email" placeholder="<?php _e('Email', ET_DOMAIN); ?>" value="<?php echo $business_email; ?>" >
                            </div>
                        </div>
                        <div class="form-group clearfix">
                            <div class="input-group">
                                <div class="input-group-addon no-addon"><?php _e('Address:', ET_DOMAIN); ?></div>
                                <input type="text" name="billing_full_address" id="billing_full_address" placeholder="<?php _e('Physical address', ET_DOMAIN); ?>" value="<?php echo $billing_full_address; ?>">
                            </div>
                        </div>
                        <?php if ( $is_individual ): ?>
                        <div class="form-group clearfix">
                                <div class="input-group">
                                    <div class="input-group-addon no-addon"><?php _e('Credit goals:', ET_DOMAIN); ?></div>
                                    <input type="text" name="credit_goal" id="credit_goal" placeholder="<?php _e('Credit goals', ET_DOMAIN); ?>" value="<?php echo $credit_goal; ?>">
                                </div>
                            </div>
                        <?php endif; ?>
                            <div class="form-group clearfix float-right change-pass-button-method">
                                <button class="btn-submit"><?php _e('Update', ET_DOMAIN); ?></button>
                            </div>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>
    <input type="hidden" class="input-item" name="_wpnonce" id="profile_wpnonce" value="<?php echo de_create_nonce('ae-mjob_post-sync');?>" />
<?php
get_footer();
?>
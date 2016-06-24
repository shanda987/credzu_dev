<?php
/**
 * Template Name: Page Profile
 */
global $wp_query, $ae_post_factory, $post, $current_user, $user_ID;
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
$credit_goal = !empty($profile->credit_goal) ? $profile->credit_goal : '';
if( $user_role == COMPANY){
    $check1 = '';
    $check2 = 'checked';
}
else{
    $check1 = 'checked';
    $check2 = '';
}
get_header();

// If Company, this outputs the Company Status bar (Doesn't show when approved)
echo mJobProfileAction()->display_company_status($user_role, $profile->company_status);
?>
    <div class="container mjob-profile-page withdraw">
        <div class="title-top-pages">
            <?php if( $user_role == COMPANY ): ?>
            <p class="block-title"><?php _e('MY PERSONAL PROFILE', ET_DOMAIN); ?></p>
            <p class="btn-back"><?php _e('Input your personal information. Your company information is separate from this individual information.', ET_DOMAIN); ?></p>
            <? else: ?>
            <p class="block-title"><?php _e('MY PROFILE', ET_DOMAIN); ?></p>
                <p class="btn-back"><?php _e('Complete your profile page', ET_DOMAIN); ?></p>
            <?php endif; ?>
        </div>
        <div class="row profile">
            <div class="col-lg-3 col-md-3 col-sm-12 col-sx-12 block-items-detail profile">
                <?php get_sidebar('my-profile'); ?>
            </div>

            <div class="col-lg-9 col-md-9 col-sm-12 col-sx-12">
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
                                <div class="input-group-addon no-addon"><?php _e('Address Line 1:', ET_DOMAIN); ?></div>
                                <input type="text" name="billing_full_address" id="billing_full_address" placeholder="<?php _e('Address Line 1', ET_DOMAIN); ?>" value="<?php echo $billing_full_address; ?>">
                            </div>
                        </div>
                        <div class="form-group clearfix">
                            <div class="input-group">
                                <div class="input-group-addon no-addon"><?php _e('Address line 2:', ET_DOMAIN); ?></div>
                                <input type="text" name="address_line2" id="address_line2" placeholder="<?php _e('Address Line 2', ET_DOMAIN); ?>" value="<?php echo $profile->address_line2; ?>">
                            </div>
                        </div>
                        <div class="form-group clearfix">
                            <div class="input-group">
                                <div class="input-group-addon no-addon"><?php _e('City:', ET_DOMAIN); ?></div>
                                <input type="text" name="city" id="city" placeholder="<?php _e('City', ET_DOMAIN); ?>" value="<?php echo $profile->city; ?>">
                            </div>
                        </div>
                        <div class="form-group clearfix">
                            <div class="input-group">
                                <div class="input-group-addon no-addon"><?php _e('State:', ET_DOMAIN); ?></div>

                                 <?php mJobProfileAction()->profileStates($profile); ?>
<!--                                <input type="text" name="state" id="state" placeholder="--><?php //_e('State', ET_DOMAIN); ?><!--" value="--><?php //echo $profile->state; ?><!--">-->
                            </div>
                        </div>
                        <div class="form-group clearfix">
                            <div class="input-group">
                                <div class="input-group-addon no-addon"><?php _e('Zip code:', ET_DOMAIN); ?></div>
                                <input type="text" name="zip_code" id="zip_code" placeholder="<?php _e('Zip code', ET_DOMAIN); ?>" value="<?php echo $profile->zip_code; ?>">
                            </div>
                        </div>
                            <?php if( $user_role == INDIVIDUAL): ?>
                            <div class="form-group profile-type-css clearfix">
                                <p class="title"><?php _e('PROFILE TYPE', ET_DOMAIN); ?></p>
                            </div>
                            <div class="form-group check-payment profile-page-role clearfix">
                                <div class="checkbox">
                                    <label for="role_client">
                                        <input type="radio" name="role" id="role_client" value="<?php echo INDIVIDUAL?>" <?php echo $check1; ?>>
                                        <span><?php _e(' Client(Buyer)', ET_DOMAIN); ?></span>
                                    </label>
                                </div>
                                <div class="checkbox">
                                    <label for="role_company">
                                        <input type="radio" name="role" id="role_company" value="<?php echo COMPANY; ?>" <?php echo $check2; ?>>
                                        <span><?php _e(' Company(Provider)', ET_DOMAIN); ?></span>
                                    </label>
                                </div>
                            </div>
                            <div class="form-group clearfix">
                                <div class="input-group">
                                    <div class="input-group-addon no-addon"><?php _e('Credit goals:', ET_DOMAIN); ?></div>
                                    <input type="text" name="credit_goal" id="credit_goal" placeholder="<?php _e('Credit goals', ET_DOMAIN); ?>" value="<?php echo $credit_goal; ?>">
                                </div>
                            </div>
                            <?php endif; ?>
                            <div class="block-connect-social block-connect-social-css form-group clearfix">
                                <p class="title title-connect-social"><?php _e('CONNECT TO SOCIALS', ET_DOMAIN); ?>
                                </p>
                                <br/><span class="social-noti"><?php _e('This is not required, but to avoid possible confusion, you can link all of your accounts.', ET_DOMAIN) ?></span>
                                <?php
                                ae_render_connect_social_button();
                                ?>
                            </div>
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
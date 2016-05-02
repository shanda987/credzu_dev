<?php
/**
 * Template Name: Page Profile
 */
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
    echo '<script type="text/json" id="mjob_profile_data" >'.json_encode($profile).'</script>';
}

$description = !empty($profile->profile_description) ? $profile->profile_description : __('There is no content', ET_DOMAIN);
$payment_info = !empty($profile->payment_info) ? $profile->payment_info : __('There is no content', ET_DOMAIN);
$billing_full_name = !empty($profile->billing_full_name) ? $profile->billing_full_name : __('First Name', ET_DOMAIN);
$billing_full_address = !empty($profile->billing_full_address) ? $profile->billing_full_address : __('Physical address', ET_DOMAIN);
$billing_country = !empty($profile->billing_country) ? $profile->billing_country : '';
$billing_vat = !empty($profile->billing_vat) ? $profile->billing_vat : __('There is no content', ET_DOMAIN);
$first_name = !empty($profile->first_name) ? $profile->first_name : __('First name', ET_DOMAIN);
$last_name = !empty($profile->last_name) ? $profile->last_name : __('Last name', ET_DOMAIN);
$phone = !empty($profile->phone) ? $profile->phone : __('Phone', ET_DOMAIN);
$business_email = !empty($profile->business_email) ? $profile->business_email : __('Email', ET_DOMAIN);
$credit_goal = !empty($profile->credit_goal) ? $profile->credit_goal : __('Credit Goals', ET_DOMAIN);
get_header();
?>
    <div class="container mjob-profile-page">
        <div class="title-top-pages">
            <p class="block-title"><?php _e('MY PROFILE', ET_DOMAIN); ?></p>
            <p><?php _e('Here is your profile information', ET_DOMAIN); ?></p>
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
                        <form>
                        <ul>
                            <li>
                                <div id="first_name" class="info-content">
                                    <div class="" data-type="input" data-name="first_name" data-id="#first_name">
                                        <label for="first_name"><?php _e('First Name', ET_DOMAIN )?></label>
                                        <input type="text" name="first_name" placeholder="<?php echo $first_name; ?>" value="<?php echo $first_name; ?>" />
                                    </div>
                                </div>
                            </li>
                            <li>
                                <div id="last_name" class="info-content">
                                    <div class="" data-type="input" data-name="last_name" data-id="#last_name">
                                        <label for="last_name"><?php _e('Last Name', ET_DOMAIN )?></label>
                                        <input type="text"  name="last_name" placeholder="<?php echo $last_name; ?>" value="<?php echo $last_name; ?>" />
                                    </div>
                                </div>
                            </li>
                            <li>
                                <div id="phone" class="info-content">
                                    <div class="" data-type="input" data-name="phone" data-id="#phone">
                                        <label for="phone"><?php _e('Phone', ET_DOMAIN )?></label>
                                        <input type="text"  name="phone" placeholder="<?php echo $phone; ?>" value="<?php echo $phone; ?>" />
                                    </div>
                                </div>
                            </li>
                            <li>
                                <div id="business_email" class="info-content">
                                    <div class="" data-type="input" data-name="business_email" data-id="#business_email">
                                        <label for="business_email"><?php _e('Email', ET_DOMAIN )?></label>
                                        <input type="email"  name="business_email" placeholder="<?php echo $business_email; ?>" value="<?php echo $business_email; ?>" />
                                    </div>
                                </div>
                            </li>
                            <li>
                                <div id="billing_full_address" class="info-content  text-address">
                                    <label for="billing_full_address"><?php _e('Physical Address', ET_DOMAIN )?></label>
                                    <textarea class="" name="billing_full_address"><?php echo $billing_full_address; ?></textarea>
                                </div>
                            </li>
                            <li>
                                <div id="credit_goal" class="info-content  text-address">
                                    <label for="credit_goal"><?php _e('Credit goals', ET_DOMAIN )?></label>
                                    <textarea class="" name="credit_goal"><?php echo $credit_goal; ?></textarea>
                                </div>
                            </li>
                            <li class="button-sb">
                                <button class="btn-submit btn-save waves-effect waves-light" type="submit"><?php _e('UPDATE', ET_DOMAIN); ?></button>
                            </li>
                        </ul>
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
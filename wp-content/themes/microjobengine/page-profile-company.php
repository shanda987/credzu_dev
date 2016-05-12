<?php
/**
 * Template Name: Page Profile Company
 * @TODO -- Not Totally sure yet what "description" does.
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

// $description = !empty($profile->profile_description) ? $profile->profile_description : __('There is no content', ET_DOMAIN);
// $payment_info = !empty($profile->payment_info) ? $profile->payment_info : __('There is no content', ET_DOMAIN);
// $billing_full_name = !empty($profile->billing_full_name) ? $profile->billing_full_name : '';
// $billing_full_address = !empty($profile->billing_full_address) ? $profile->billing_full_address : '';
// $billing_country = !empty($profile->billing_country) ? $profile->billing_country : '';
// $billing_vat = !empty($profile->billing_vat) ? $profile->billing_vat : __('There is no content', ET_DOMAIN);

$company_name = !empty($profile->company_name) ? $profile->company_name : '';
$company_address = !empty($profile->company_address) ? $profile->company_address : '';
$company_phone = !empty($profile->company_phone) ? $profile->company_phone : '';
$company_email = !empty($profile->company_email) ? $profile->company_email : '';
$company_website = !empty($profile->company_website) ? $profile->company_website : '';
$company_year_established = !empty($profile->company_year_established) ? $profile->company_year_established : '';
$company_amount_of_employees = !empty($profile->company_amount_of_employees) ? $profile->company_amount_of_employees : '';
$company_description = !empty($profile->company_description) ? $profile->company_description : '';

// $first_name = !empty($profile->first_name) ? $profile->first_name : '';
// $last_name = !empty($profile->last_name) ? $profile->last_name : '';
// $phone = !empty($profile->phone) ? $profile->phone : '';
// $business_email = !empty($profile->business_email) ? $profile->business_email : $user_data->user_email;
// $credit_goal = !empty($profile->credit_goal) ? $profile->credit_goal : '';
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

                    <div class="block-billing mjob-profile-form">
                        <form class="et-form">
                        <div class="form-group clearfix">
                            <div class="input-group">
                                <div class="input-group-addon no-addon"><?php _e('Company Name:', ET_DOMAIN); ?></div>
                                <input type="text" name="company_name" id="company_name" placeholder="<?php _e('Company Name', ET_DOMAIN); ?>" value="<?php echo $company_name; ?>">
                            </div>
                        </div>
                        <div class="form-group clearfix">
                            <div class="input-group">
                                <div class="input-group-addon no-addon"><?php _e('Company Address:', ET_DOMAIN); ?></div>
                                <input type="text" name="company_address" id="company_address" placeholder="<?php _e('Company Address', ET_DOMAIN); ?>" value="<?php echo $company_address; ?>">
                            </div>
                        </div>
                        <div class="form-group clearfix">
                            <div class="input-group">
                                <div class="input-group-addon no-addon"><?php _e('Company Phone:', ET_DOMAIN); ?></div>
                                <input type="text" name="company_phone" id="company_phone" placeholder="<?php _e('Company Phone', ET_DOMAIN); ?>" value="<?php echo $company_phone; ?>">
                            </div>
                        </div>
                        <div class="form-group clearfix">
                            <div class="input-group">
                                <div class="input-group-addon no-addon"><?php _e('Company Email:', ET_DOMAIN); ?></div>
                                <input type="email" name="company_email" id="company_email" placeholder="<?php _e('Company Email', ET_DOMAIN); ?>" value="<?php echo $company_email; ?>" >
                            </div>
                        </div>
                        <div class="form-group clearfix">
                            <div class="input-group">
                                <div class="input-group-addon no-addon"><?php _e('Company Website:', ET_DOMAIN); ?></div>
                                <input type="text" name="company_website" id="company_website" placeholder="<?php _e('Company Website', ET_DOMAIN); ?>" value="<?php echo $company_website; ?>">
                            </div>
                        </div>
                        <div class="form-group clearfix">
                            <div class="input-group">
                                <div class="input-group-addon no-addon"><?php _e('Year Established:', ET_DOMAIN); ?></div>
                                <input type="text" name="company_year_established" id="company_website" placeholder="<?php _e('Year Established', ET_DOMAIN); ?>" value="<?php echo $company_year_established; ?>">
                            </div>
                        </div>
                        <div class="form-group clearfix">
                            <div class="input-group">
                                <div class="input-group-addon no-addon"><?php _e('Amount of Employees:', ET_DOMAIN); ?></div>
                                <input type="text" name="company_amount_of_employees" id="company_amount_of_employees" placeholder="<?php _e('Amount of Employees', ET_DOMAIN); ?>" value="<?php echo $company_amount_of_employees; ?>">
                            </div>
                        </div>
                        <div class="form-group clearfix">
                                <div class="input-group">
                                    <div class="input-group-addon no-addon"><?php _e('Company Description:', ET_DOMAIN); ?></div>
                                    <textarea name="company_description" id="company_description" placeholder="<?php _e('Company Description', ET_DOMAIN); ?>"><?php echo $company_description; ?></textarea>
                                </div>
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
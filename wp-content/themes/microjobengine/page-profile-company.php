<?php
/**
 * Template Name: Page Profile Company
 * @TODO -- Not Totally sure yet what "description" does.
 */
global $wp_query, $ae_post_factory, $post, $current_user, $user_ID;
$user = mJobUser::getInstance();
$user_data = $user->convert($current_user->data);
$user_role = ae_user_role($current_user->ID);

// Protect the Page
if ($user_role !== COMPANY) {
    wp_redirect(home_url()); exit;
}

$profile = mJobProfileAction()->getProfile($user_ID);
echo mJobProfileAction()->getProfileJson($profile);

$company_name = !empty($profile->company_name) ? $profile->company_name : '';
$company_address = !empty($profile->company_address) ? $profile->company_address : '';
$company_phone = !empty($profile->company_phone) ? $profile->company_phone : '';
$company_email = !empty($profile->company_email) ? $profile->company_email : '';
$company_website = !empty($profile->company_website) ? $profile->company_website : '';
$company_year_established = !empty($profile->company_year_established) ? $profile->company_year_established : '';
$company_amount_of_employees = !empty($profile->company_amount_of_employees) ? $profile->company_amount_of_employees : '';
$company_description = !empty($profile->company_description) ? $profile->company_description : '';
$company_welcome_message = !empty($profile->company_welcome_message) ? $profile->company_welcome_message : '';
$company_status_message = !empty($profile->company_status_message) ? $profile->company_status_message : '';
get_header();

// If Company, this outputs the Company Status bar (Doesn't show when approved)
echo mJobProfileAction()->display_company_status($user_role, $profile->company_status);
?>
    <div class="container mjob-profile-page withdraw">
        <div class="title-top-pages">
            <p class="block-title"><?php _e('Company PROFILE', ET_DOMAIN); ?></p>
            <p><a href="<?php echo et_get_page_link('dashboard'); ?>" class="btn-back"><i class="fa fa-angle-left"></i><?php _e('Back to dashboard', ET_DOMAIN); ?></a></p>
        </div>
        <div class="row profile">
            <div class="col-lg-3 col-md-3 col-sm-12 col-sx-12 block-items-detail profile">
                <?php get_sidebar('my-profile'); ?>
            </div>

            <div class="col-lg-9 col-md-9 col-sm-12 col-sx-12">
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
                                <div class="input-group-addon no-addon"><?php _e('Address line 2:', ET_DOMAIN); ?></div>
                                <input type="text" name="company_address_line2" id="company_address_line2" placeholder="<?php _e('Address Line 2', ET_DOMAIN); ?>" value="<?php echo $profile->address_line2; ?>">
                            </div>
                        </div>
                        <div class="form-group clearfix">
                            <div class="input-group">
                                <div class="input-group-addon no-addon"><?php _e('City:', ET_DOMAIN); ?></div>
                                <input type="text" name="company_city" id="company_city" placeholder="<?php _e('City', ET_DOMAIN); ?>" value="<?php echo $profile->city; ?>">
                            </div>
                        </div>
                        <div class="form-group clearfix">
                            <div class="input-group">
                                <div class="input-group-addon no-addon"><?php _e('State:', ET_DOMAIN); ?></div>
                                <input type="text" name="company_state" id="company_state" placeholder="<?php _e('State', ET_DOMAIN); ?>" value="<?php echo $profile->state; ?>">
                            </div>
                        </div>
                        <div class="form-group clearfix">
                            <div class="input-group">
                                <div class="input-group-addon no-addon"><?php _e('Zip code:', ET_DOMAIN); ?></div>
                                <input type="text" name="company_zip_code" id="company_zip_code" placeholder="<?php _e('Zip code', ET_DOMAIN); ?>" value="<?php echo $profile->zip_code; ?>">
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
                                    <textarea name="company_description" id="company_description" maxlength="500" placeholder="<?php _e('Company Description', ET_DOMAIN); ?>"><?php echo $company_description; ?></textarea>
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
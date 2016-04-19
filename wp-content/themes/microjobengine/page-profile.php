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
$billing_full_name = !empty($profile->billing_full_name) ? $profile->billing_full_name : __('There is no content', ET_DOMAIN);
$billing_full_address = !empty($profile->billing_full_address) ? $profile->billing_full_address : __('There is no content', ET_DOMAIN);
$billing_country = !empty($profile->billing_country) ? $profile->billing_country : '';
$billing_vat = !empty($profile->billing_vat) ? $profile->billing_vat : __('There is no content', ET_DOMAIN);
get_header();
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
                    <div class="block-intro">
                        <p class="title"><?php _e('DESCRIPTION', ET_DOMAIN); ?></p>
                        <div class="vote">
                            <div class="rate-it star" data-score="<?php echo mJobUserCountReview($user_ID); ?>"></div>
                        </div>
                        <div id="post_content" class="text-content-wrapper text-content">
                            <div><textarea class="editable" name="profile_description">
                                <?php echo strip_tags($description); ?>
                            </textarea></div>   
                        </div>
                    </div>
                    <div class="block-payment">
                        <p class="title"><?php _e('PAYMENT INFO', ET_DOMAIN); ?></p>
                        <div id="payment_info" class="text-content-wrapper text-content">
                            <div><textarea class="editable" name="payment_info">
                                <?php echo $payment_info; ?>
                            </textarea></div>
                        </div>
                    </div>
                    <div class="block-billing">
                        <p class="title"><?php _e('BILLING INFO', ET_DOMAIN); ?></p>
                        <ul>
                            <li>
                                <div class="cate-title"><?php _e('Business full name', ET_DOMAIN); ?></div>
                                <div id="billing_full_name" class="info-content">
                                    <div class="text-content" data-type="input" data-name="billing_full_name" data-id="#billing_full_name"><p><?php echo $billing_full_name; ?></p></div>
                                </div>
                            </li>
                            <li>
                                <div class="cate-title full-address"><?php _e('Full Address', ET_DOMAIN); ?></div>
                                <div id="billing_full_address" class="info-content text-content text-address">
                                    <textarea class="editable" name="billing_full_address"><?php echo $billing_full_address; ?></textarea>
                                </div>
                            </li>
                            <li>
                                <div class="cate-title"><?php _e('Country', ET_DOMAIN); ?></div>
                                <div id="billing_country" class="info-content">
                                    <?php
                                    ae_tax_dropdown('country', array(
                                        'id' => 'billing_country',
                                        'name' => 'billing_country',
                                        'class' => 'chosen-single is-chosen',
                                        'hide_empty' => false,
                                        'show_option_all' => __('Select your country', ET_DOMAIN),
                                        'selected' => (int) $billing_country,
                                    ));
                                    ?>
                                </div>
                            </li>
                            <li>
                                <div class="cate-title"><?php _e('VAT or Tax Number', ET_DOMAIN); ?></div>
                                <div id="billing_vat" class="info-content">
                                    <div class="text-content" data-type="input" data-name="billing_vat" data-id="#billing_vat"><p><?php echo $billing_vat; ?></p></div>
                                </div>
                            </li>
                        </ul>
                    </div>

                    <div class="block-connect-social">
                    <p class="title"><?php _e('CONNECT TO SOCIALS', ET_DOMAIN); ?></p>
                    <?php
                        ae_render_connect_social_button();
                    ?>
                </div>
                </div>
                
            </div>
        </div>
    </div>
    <input type="hidden" class="input-item" name="_wpnonce" id="profile_wpnonce" value="<?php echo de_create_nonce('ae-mjob_post-sync');?>" />
<?php
get_footer();
?>
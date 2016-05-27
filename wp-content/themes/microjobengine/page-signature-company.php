<?php
/**
 * Template Name: Agreement company
 *
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

$profile = mJobProfileAction()->getProfile($user_ID);
echo mJobProfileAction()->getProfileJson($profile);
$agrs = array(
    'post_type'=>'mjob_agreement',
    'post_status'=>'publish',
    'meta_query' => array(
        array(
            'key' => 'agreement_company_to_credzu',
            'value' => 'yes',
        )
    )
);
$agreement = get_posts($agrs);
$content = '';
if( !empty($agreement)) {
    global $ae_post_factory;
    $agr_obj = $ae_post_factory->get('mjob_agreement');
    $agreement = $agr_obj->convert($agreement['0']);
    $content = $agreement->post_content;
    $file_path = decodeImage($profile->company_signature);
    $img =  '<img style="width:170px" class="signature-img" src="'.$file_path.'" />';
    $content = str_ireplace('[company_signature]', $img, $content );
}
get_header();
?>
    <div class="container mjob-profile-page withdraw">
        <div class="title-top-pages">
            <p class="block-title"><?php _e('Agreement signing', ET_DOMAIN); ?></p>
            <p><?php _e('Your agreement siging', ET_DOMAIN ); ?></p>
        </div>
        <div class="row profile">
            <div class="col-lg-3 col-md-3 col-sm-12 col-sx-12 block-items-detail profile">
                <?php get_sidebar('my-profile'); ?>
            </div>

            <div class="col-lg-9 col-md-9 col-sm-12 col-sx-12">
                <div class="block-profile">

                    <div class="block-billing mjob-profile-form">
                        <form class="et-form" id="signature-form">
                            <div class="agreement-content"><?php echo $content; ?></div>
                            <div class="form-group clearfix float-left check-terms">
                                <div id="signature-pad" class="m-signature-pad">
                                    <div class="m-signature-pad--body m-signature-pad--body1">
                                        <canvas></canvas>
                                    </div>
                                    <div class="m-signature-pad--footer">
                                        <div class="description"><?php _e('Draw your signature above', ET_DOMAIN); ?></div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group clearfix float-right change-pass-button-method">
                                <a  class="button clear" data-action="clear"><?php _e('CLEAR SIGNATURE', ET_DOMAIN); ?></a>
                                <button type="button" class="button save btn-submit" data-action="save"><?php _e('SIGN  ', ET_DOMAIN) ?><i class="fa fa-arrow-right"></i></button>
                            </div>
                            <input type="hidden" class="input-item" name="_wpnonce" id="profile_wpnonce" value="<?php echo de_create_nonce('ae-mjob_post-sync');?>" />
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
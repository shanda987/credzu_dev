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
get_header();
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
                        <form class="et-form post-job" id="signature-form">
                            <input type="hidden" name="mjob_id" id="mjob_id" value="<?php echo $mjob->ID ?>" />
                            <div class="form-group clearfix float-left check-terms">
                                <div id="signature-pad" class="m-signature-pad">
                                    <div class="m-signature-pad--body">
                                        <canvas></canvas>
                                    </div>
                                    <div class="m-signature-pad--footer">
                                        <div class="description"><?php _e('Draw your signature above', ET_DOMAIN); ?></div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group clearfix float-right change-pass-button-method">
                                <a  class="button  mjob-process-hiring-back mjob-process-hiring-back-step2" ><i class="fa fa-arrow-left"></i> <?php _e('BACK', ET_DOMAIN); ?></a>
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
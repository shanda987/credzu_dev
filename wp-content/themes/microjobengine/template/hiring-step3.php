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
?>
<div class="form-sign-agreement">
    <form class="et-form post-job" id="signature-form">
        <div class="form-group clearfix float-left check-terms">
            <div class="checkbox">
                <label>
                    <input type="checkbox" name="term_privacy" id="term_privacy"><span class="text-choosen"><?php _e('I accept with the', ET_DOMAIN); ?>
                        <a href="<?php echo et_get_page_link('tos'); ?>" target="_blank"><?php _e('terms and conditions', ET_DOMAIN); ?></a></span>
                </label>
            </div>
        </div>
        <div class="form-group clearfix float-left check-terms">
            <div class="checkbox">
                <label>
                    <input type="checkbox" name="term_privacy" id="term_privacy"><span class="text-choosen"><?php _e('I accept with the', ET_DOMAIN); ?>
                        <a href="<?php echo et_get_page_link('tos'); ?>" target="_blank"><?php _e('terms and conditions', ET_DOMAIN); ?></a></span>
                </label>
            </div>
        </div>
        <div class="form-group clearfix float-left check-terms">
            <div class="checkbox">
                <label>
                    <input type="checkbox" name="term_privacy" id="term_privacy"><span class="text-choosen"><?php _e('I accept with the', ET_DOMAIN); ?>
                        <a href="<?php echo et_get_page_link('tos'); ?>" target="_blank"><?php _e('terms and conditions', ET_DOMAIN); ?></a></span>
                </label>
            </div>
        </div>
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
            <a  class="button clear" data-action="clear"><?php _e('CLEAR SIGNATURE', ET_DOMAIN); ?></a>
            <button type="button" class="button save btn-submit" data-action="save"><?php _e('SIGN', ET_DOMAIN) ?></button>
        </div>
        <input type="hidden" class="input-item" name="_wpnonce" id="profile_wpnonce" value="<?php echo de_create_nonce('ae-mjob_post-sync');?>" />
    </form>
</div>
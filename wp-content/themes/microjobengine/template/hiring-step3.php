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
global $current_mjob;
$current_mjob = '';
$agreements = array();
$mjob = array();
if( isset($_GET['jid']) && !empty($_GET['jid'] ) ){
    $current_mjob = $_GET['jid'];
    $mjob = mJobAction()->get_mjob($_GET['jid']);
    if( isset($mjob->mjob_category['0']) ){
        $agreements = agreementAction()->get_agreement_by_cats($mjob->mjob_category['0']);
    }
}
?>
<div class="form-sign-agreement">
    <form class="et-form post-job" id="signature-form">
        <input type="hidden" name="mjob_id" id="mjob_id" value="<?php echo $mjob->ID ?>" />
        <?php if( !empty($agreements) ):
            foreach( $agreements as $key=>$value):
                echo '<script type="text/json" id="agreement_data_'.$value->ID.'" >'.json_encode($value).'</script>';
            ?>
                <div class="form-group clearfix float-left check-terms">
                    <div class="checkbox">
                        <label>
                            <input type="checkbox" data-id="<?php echo $value->ID ?>" name="read_and_understand_<?php echo $value->ID; ?>" id="read_and_understand_<?php echo $value->ID; ?>"><span class="text-choosen"><?php _e('I read and understand the', ET_DOMAIN); ?>
                                <a href="#" data-id="<?php echo $value->ID ?>" class="agreement-title-link"><?php echo $value->post_title; ?></a></span>
                        </label>
                    </div>
                </div>
        <?php
            endforeach;
        endif; ?>
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
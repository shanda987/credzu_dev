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
        echo '<script type="text/json" id="mjob_profile_data" >'.json_encode($profile).'</script>';
    }
}
?>
<div class="form-confirm-billing-profile">
    <form class="et-form">
        <div class="form-group clearfix">
            <div class="input-group">
                <div class="input-group-addon no-addon"><?php _e('Routing number:', ET_DOMAIN); ?></div>
                <input type="text" name="routing_number" id="routing_number" placeholder="<?php _e('Routing number', ET_DOMAIN); ?>" value="<?php echo $profile->routing_number; ?>">
            </div>
        </div>
        <div class="form-group clearfix">
            <div class="input-group">
                <div class="input-group-addon no-addon"><?php _e('Account Number:', ET_DOMAIN); ?></div>
                <input type="text" name="account_number" id="account_number" placeholder="<?php _e('Account Number', ET_DOMAIN); ?>" value="<?php echo $profile->account_number; ?>">
            </div>
        </div>
        <div class="form-group clearfix">
            <div class="input-group">
                <div class="input-group-addon no-addon"><?php _e('Billing address ( same as your address):', ET_DOMAIN); ?></div>
                <select class="hiring-process-select selectpicker" name="use_billing_address">
                    <option value="yes"><?php _e('Yes', ET_DOMAIN);?></option>
                    <option value="no"><?php _e('No', ET_DOMAIN);?></option>
                </select>
            </div>
        </div>
        <div class="form-group clearfix billing-order-address">
            <div class="input-group">
                <div class="input-group-addon no-addon"><?php _e('Address', ET_DOMAIN); ?></div>
                <input type="text" name="billing_other_address" id="billing_other_address" placeholder="<?php _e('Address', ET_DOMAIN); ?>" value="<?php echo $profile->billing_other_address; ?>">
            </div>
        </div>
        <div class="form-group clearfix">
            <div class="input-group">
                <div class="input-group-addon no-addon"><?php _e('Account holder ( Is this your account?):', ET_DOMAIN); ?></div>
                <select class="hiring-process-select selectpicker" name="use_holder_account">
                    <option value="yes"><?php _e('Yes', ET_DOMAIN);?></option>
                    <option value="no"><?php _e('No', ET_DOMAIN);?></option>
                </select>
            </div>
        </div>
        <div class="form-group clearfix account-holder">
            <div class="input-group">
                <div class="input-group-addon no-addon"><?php _e('Account holder:', ET_DOMAIN); ?></div>
                <input type="account_holder" name="account_holder" id="account_holder" placeholder="<?php _e('Account holder', ET_DOMAIN); ?>" value="<?php echo $profile->account_holder; ?>" >
            </div>
        </div>
        <div class="form-group clearfix float-right change-pass-button-method">
            <button class="btn-submit"><?php _e('Save', ET_DOMAIN); ?></button>
        </div>
        <input type="hidden" class="input-item" name="_wpnonce" id="profile_wpnonce" value="<?php echo de_create_nonce('ae-mjob_post-sync');?>" />
    </form>
</div>
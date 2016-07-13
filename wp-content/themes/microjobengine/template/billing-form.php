<?php
global $wp_query, $ae_post_factory, $post, $current_user, $user_ID;
$user = mJobUser::getInstance();
$user_data = $user->convert($current_user->data);
$user_role = ae_user_role($current_user->ID);

$profile = mJobProfileAction()->getProfile($user_ID);
echo mJobProfileAction()->getProfileJson($profile);

?>
<div class="form-confirm-billing-profile">
    <form class="et-form">
        <div class="form-group clearfix">
            <div class="input-group">
                <div class="input-group-addon no-addon"><?php _e('Routing number:', ET_DOMAIN); ?></div>
                <input type="text" name="routing_number" id="routing_number" minlength="9" maxlength="9" placeholder="<?php _e('*********', ET_DOMAIN); ?>" value="<?php echo $profile->routing_number; ?>">
            </div>
        </div>
        <div class="form-group clearfix">
            <div class="input-group">
                <div class="input-group-addon no-addon"><?php _e('Account Number:', ET_DOMAIN); ?></div>
                <input type="text" name="account_number" id="account_number" placeholder="<?php _e('************', ET_DOMAIN); ?>" value="<?php echo $profile->account_number; ?>">
            </div>
        </div>
        <div class="form-group clearfix">
            <div class="input-group">
                <div class="input-group-addon no-addon"><?php _e('Billing address:', ET_DOMAIN); ?></div>
                <select class="hiring-process-select selectpicker required" name="use_billing_address">
                    <option value=""><?php _e('Select Address', ET_DOMAIN);?></option>
                    <option value="personal"><?php _e('Personal Address', ET_DOMAIN);?></option>
                    <option value="comapany"><?php _e('Company Address', ET_DOMAIN);?></option>
                    <option value="no"><?php _e('Other', ET_DOMAIN);?></option>
                </select>
            </div>
        </div>
        <div class="form-group clearfix billing-order-address">
            <div class="input-group">
                <div class="input-group-addon no-addon"><?php _e('Address line 1', ET_DOMAIN); ?></div>
                <input type="text" name="billing_other_address" id="billing_other_address" placeholder="<?php _e('e.g., 3837 Adam Street', ET_DOMAIN); ?>" value="<?php echo $profile->billing_other_address; ?>">
            </div>
        </div>
        <div class="form-group clearfix billing-order-address">
            <div class="input-group">
                <div class="input-group-addon no-addon"><?php _e('Address Line 2', ET_DOMAIN); ?></div>
                <input type="text" name="billing_address_line2" id="address_line2" placeholder="<?php _e('e.g., Unit 335', ET_DOMAIN); ?>" value="<?php echo $profile->billing_address_line2; ?>">
            </div>
        </div>
        <div class="form-group clearfix billing-order-address">
            <div class="input-group">
                <div class="input-group-addon no-addon"><?php _e('City', ET_DOMAIN); ?></div>
                <input type="text" name="billing_city" id="city" placeholder="<?php _e('e.g., Cape Canaveral', ET_DOMAIN); ?>" value="<?php echo $profile->billing_city; ?>">
            </div>
        </div>
        <div class="form-group clearfix billing-order-address">
            <div class="input-group">
                <div class="input-group-addon no-addon"><?php _e('State', ET_DOMAIN); ?></div>
                <?php mJobProfileAction()->profileStates($profile, 'billing_state'); ?>
<!--                <input type="text" name="billing_state" id="state" placeholder="--><?php //_e('e.g., Florida', ET_DOMAIN); ?><!--" value="--><?php //echo $profile->billing_state; ?><!--">-->
            </div>
        </div>
        <div class="form-group clearfix billing-order-address">
            <div class="input-group">
                <div class="input-group-addon no-addon"><?php _e('Zip code', ET_DOMAIN); ?></div>
                <input type="text" name="billing_zip_code" id="zip_code" placeholder="<?php _e('e.g., 12345', ET_DOMAIN); ?>" value="<?php echo $profile->billing_zip_code; ?>">
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
                <input type="account_holder" name="account_holder" id="account_holder" placeholder="<?php _e('e.g., John Smith', ET_DOMAIN); ?>" value="<?php echo $profile->account_holder; ?>" >
            </div>
        </div>
        <div class="form-group clearfix float-right change-pass-button-method">
            <button class="btn-submit"><?php _e('Save', ET_DOMAIN); ?></button>
        </div>
        <input type="hidden" name="is_billing" value="1"/>
        <input type="hidden" name="billing_completed" value="1" />
        <input type="hidden" class="input-item" name="_wpnonce" id="profile_wpnonce" value="<?php echo de_create_nonce('ae-mjob_post-sync');?>" />
    </form>
</div>
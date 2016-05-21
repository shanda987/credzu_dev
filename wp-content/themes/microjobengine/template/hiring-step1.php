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
    echo '<script type="text/json" id="mjob_profile_data" >'.json_encode($profile).'</script>';
}

$description = !empty($profile->profile_description) ? $profile->profile_description : __('There is no content', ET_DOMAIN);
$payment_info = !empty($profile->payment_info) ? $profile->payment_info : __('There is no content', ET_DOMAIN);
$billing_full_name = !empty($profile->billing_full_name) ? $profile->billing_full_name : '';
$billing_full_address = !empty($profile->billing_full_address) ? $profile->billing_full_address : '';
$billing_country = !empty($profile->billing_country) ? $profile->billing_country : '';
$billing_vat = !empty($profile->billing_vat) ? $profile->billing_vat : __('There is no content', ET_DOMAIN);
$first_name = !empty($profile->first_name) ? $profile->first_name : '';
$last_name = !empty($profile->last_name) ? $profile->last_name : '';
$phone = !empty($profile->phone) ? $profile->phone : '';
 $business_email = !empty($profile->business_email) ? $profile->business_email : $user_data->user_email;
$credit_goal = !empty($profile->credit_goal) ? $profile->credit_goal : '';
 $mjob = '';
if( isset($_GET['jid']) && !empty($_GET['jid'] ) ){
 $current_mjob = $_GET['jid'];
 $mjob = mJobAction()->get_mjob($_GET['jid']);
}
 $link = '';
 if( isset($mjob->permalink)){
     $link = $mjob->permalink;
 }
?>
<div class="form-confirm-info">
    <form class="et-form post-job">
        <div class="form-group clearfix">
            <div class="input-group">
                <div class="input-group-addon no-addon"></div>
                <input type="text" name="first_name" id="first_name" placeholder="<?php _e('First Name', ET_DOMAIN); ?>" value="<?php echo $first_name; ?>">
            </div>
        </div>
        <div class="form-group clearfix">
            <div class="input-group">
                <div class="input-group-addon no-addon"></div>
                <input type="text" name="last_name" id="last_name" placeholder="<?php _e('Last Name', ET_DOMAIN); ?>" value="<?php echo $last_name; ?>">
            </div>
        </div>
        <div class="form-group clearfix">
            <div class="input-group">
                <div class="input-group-addon no-addon"></div>
                <input type="text" name="phone" id="phone" placeholder="<?php _e('Phone', ET_DOMAIN); ?>" value="<?php echo $phone; ?>">
            </div>
        </div>
        <div class="form-group clearfix">
            <div class="input-group">
                <div class="input-group-addon no-addon"></div>
                <input type="email" name="business_email" id="business_email" placeholder="<?php _e('Email', ET_DOMAIN); ?>" value="<?php echo $business_email; ?>" >
            </div>
        </div>
        <div class="form-group clearfix">
            <div class="input-group">
                <div class="input-group-addon no-addon"></div>
                <input type="text" name="billing_full_address" id="billing_full_address" placeholder="<?php _e('Address Line 1', ET_DOMAIN); ?>" value="<?php echo $billing_full_address; ?>">
            </div>
        </div>
        <div class="form-group clearfix">
            <div class="input-group">
                <div class="input-group-addon no-addon"></div>
                <input type="text" name="address_line2" id="address_line2" placeholder="<?php _e('Address Line 2', ET_DOMAIN); ?>" value="<?php echo $profile->address_line2; ?>">
            </div>
        </div>
        <div class="form-group clearfix">
            <div class="input-group">
                <div class="input-group-addon no-addon"></div>
                <input type="text" name="city" id="city" placeholder="<?php _e('City', ET_DOMAIN); ?>" value="<?php echo $profile->city; ?>">
            </div>
        </div>
        <div class="form-group clearfix">
            <div class="input-group">
                <div class="input-group-addon no-addon"></div>
                <input type="text" name="state" id="state" placeholder="<?php _e('State', ET_DOMAIN); ?>" value="<?php echo $profile->state; ?>">
            </div>
        </div>
        <div class="form-group clearfix">
            <div class="input-group">
                <div class="input-group-addon no-addon"></div>
                <input type="text" name="zip_code" id="zip_code" placeholder="<?php _e('Zip code', ET_DOMAIN); ?>" value="<?php echo $profile->zip_code; ?>">
            </div>
        </div>

<!--        <div class="form-group clearfix">-->
<!--            <div class="input-group">-->
<!--                <div class="input-group-addon no-addon"></div>-->
<!--                <input type="text" name="credit_goal" id="credit_goal" placeholder="--><?php //_e('Credit goals', ET_DOMAIN); ?><!--" value="--><?php //echo $credit_goal; ?><!--">-->
<!--            </div>-->
<!--        </div>-->
        <div class="form-group clearfix float-right change-pass-button-method">
            <a href="<?php echo $link ?>"  class="button  mjob-process-hiring-back" ><i class="fa fa-arrow-left"></i> <?php _e('BACK', ET_DOMAIN); ?></a>
            <button class="btn-submit"><?php _e('Save', ET_DOMAIN); ?></button>
        </div>
        <input type="hidden" class="input-item" name="_wpnonce" id="profile_wpnonce" value="<?php echo de_create_nonce('ae-mjob_post-sync');?>" />
    </form>
</div>
<?php
global $wp_query, $ae_post_factory, $post, $current_user, $user_ID;
$user = mJobUser::getInstance();
$user_data = $user->convert($current_user->data);
$user_role = ae_user_role($current_user->ID);

$profile = mJobProfileAction()->getProfile($user_ID);
echo mJobProfileAction()->getProfileJson($profile);

?>

<p>
Current Payee Name: <?=$profile->company_name?>
</p>

<div class="form-confirm-billing-profile">
    <form class="et-form">
        <div class="form-group clearfix">
            <div class="input-group">
                <div class="input-group-addon"><i class="fa fa-envelope"></i></div>
                <input type="text" name="company_payee_name_override" id="company_payee_name_override" placeholder="<?php _e('Payee Name', ET_DOMAIN); ?>" value="<?=($bank_payee_name_override_status == 'approved') ? $bank_payee_name_override : $company_name; ?>">
            </div>
        </div>
        <div class="form-group clearfix">
            <div class="input-group">
            <?php
                $status = $bank_payee_name_override_status;
                if ($status == COMPANY_PAYEE_NAME_OVERRIDE_STATUS_APPROVED):?>
                <i class="fa fa-circle text-success"></i> <span class="text-success">Approved</span>
                <?php elseif ($status == COMPANY_PAYEE_NAME_OVERRIDE_STATUS_UNDER_REVIEW):?>
                <i class="fa fa-circle text-warning"></i> <span class="text-warning">Under Review</span>
                <?php elseif ($status == COMPANY_PAYEE_NAME_OVERRIDE_STATUS_DECLINED):?>
                <i class="fa fa-circle text-error"></i> <span class="text-error">Declined</span>
            <?php else: ?>
                <i class="fa fa-circle "></i> Nothing has been set.
            <?php endif;?>
            </div>
        </div>
        <div class="form-group clearfix float-right change-pass-button-method">
            <button class="btn-submit"><?php _e('Save', ET_DOMAIN); ?></button>
        </div>
        <input type="hidden" class="input-item" name="_wpnonce" id="profile_wpnonce" value="<?php echo de_create_nonce('ae-mjob_post-sync');?>" />

    </form>
</div>
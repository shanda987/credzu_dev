<?php
global $wp_query, $ae_post_factory, $post, $current_user, $user_ID;
$user = mJobUser::getInstance();
$user_data = $user->convert($current_user->data);
$user_role = ae_user_role($current_user->ID);

$profile = mJobProfileAction()->getProfile($user_ID);
echo mJobProfileAction()->getProfileJson($profile);

// Override Status
$status = $bank_payee_name_override_status;
?>

<p>
This is the "payee" to whom payments will be made when a client hires you/your company. If this is different than your company name it will not change until it is approved.
</p>

<p>
Current Payee Name: <?=$profile->company_name?>
</p>

<div class="form-confirm-billing-profile">
    <form class="et-form">
        <div class="form-group clearfix">
            <div class="input-group">
                <div class="input-group-addon"><i class="fa fa-envelope"></i></div>
                <input type="text" name="company_payee_name_override" id="company_payee_name_override" placeholder="<?php _e('Payee Name', ET_DOMAIN); ?>" value="<?=($status == COMPANY_PAYEE_NAME_OVERRIDE_STATUS_APPROVED) ? $bank_payee_name_override : $company_name; ?>">
            </div>
        </div>
        <div>

        <?php

        if ($status == COMPANY_PAYEE_NAME_OVERRIDE_STATUS_APPROVED):?>
            <div class="alert alert-success">
            <i class="fa fa-check"></i> <span class="text-success">Payee Name Approved</span> - If you choose assign a new name it will go back into Review status and use your previous name until then.
            </div>
        <?php elseif ($status == COMPANY_PAYEE_NAME_OVERRIDE_STATUS_UNDER_REVIEW):?>
            <div class="alert alert-warning">
            <i class="fa fa-warning"></i> <span class="text-warning">Payee Name Under Review</span>
            </div>
        <?php elseif ($status == COMPANY_PAYEE_NAME_OVERRIDE_STATUS_UNDER_REVIEW_EXISTS):?>
            <div class="alert alert-warning">
            <i class="fa fa-warning"></i> <span class="text-warning">New Payee Name Under Review</span>
            </div>
        <?php elseif ($status == COMPANY_PAYEE_NAME_OVERRIDE_STATUS_DECLINED):?>
            <div class="alert alert-danger">
            <i class="fa fa-ban text-error"></i> <span class="text-error">Payee Name Declined</span>
            </div>
        <?php else: ?>
            <div class="alert alert-info">
            <i class="fa fa-chevron-circle-right "></i> No changes have been made, if you make changes it will require a Review period.
            </div>
        <?php endif;?>

        </div>
        <div class="form-group clearfix float-right change-pass-button-method">
            <button class="btn-submit"><?php _e('Save', ET_DOMAIN); ?></button>
        </div>
        <input type="hidden" name="" value="">
        <input type="hidden" class="input-item" name="_wpnonce" id="profile_wpnonce" value="<?php echo de_create_nonce('ae-mjob_post-sync');?>" />

    </form>

</div>
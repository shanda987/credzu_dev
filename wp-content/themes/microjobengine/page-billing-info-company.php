<?php
/**
 * Template Name: Billing Info
 * @ TODO Change all this stuff
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

$profile_obj = $ae_post_factory->get('mjob_profile');
$profile_id = get_user_meta($user_ID, 'user_profile_id', true);
if($profile_id) {
    $post = get_post($profile_id);
    if($post && !is_wp_error($post)) {
        $profile = $profile_obj->convert($post);
    }
    echo '<script type="text/json" id="mjob_profile_data" >'.json_encode($profile).'</script>';
}
// var_dump($company_name);

// Bank account data
$company_name = !empty($profile->company_name) ? $profile->company_name : '';
$bank_name = isset($user_data->payment_info['bank']) ? $user_data->payment_info['bank']['name'] : '';
$bank_routing_no = isset($user_data->payment_info['bank']) ? $user_data->payment_info['bank']['routing_no'] : '';
$bank_account_no = isset($user_data->payment_info['bank']) ? $user_data->payment_info['bank']['account_no'] : '';

// Second Tab
$bank_payee_name_override = isset($user_data->payment_info['bank']) ? $user_data->payment_info['bank']['payee_name_override'] : '';
$bank_payee_name_override_status = isset($user_data->payment_info['bank']) ? $user_data->payment_info['bank']['payee_name_override_status'] : '';

get_header();
?>
    <div class="container mjob-payment-method-page dashboard withdraw">
        <div class="row title-top-pages">
            <p class="block-title"><?php _e('Payment method', ET_DOMAIN); ?></p>
            <p><?php _e('Here is your payment method page', ET_DOMAIN); ?></p>
        </div>
        <div class="row profile">
            <div class="col-lg-3 col-md-3 col-sm-12 col-sx-12 block-items-detail profile">
                <?php get_sidebar('my-profile'); ?>
            </div>

            <div class="col-lg-9 col-md-9 col-sm-12 col-sx-12 payment-method box-shadow">
                <div class="tabs-information">
                    <ul class="nav nav-tabs" role="tablist">
                        <li role="presentation" class="active"><a href="#outgoing-payments" aria-controls="outgoing-payments" role="tab" data-toggle="tab"><?php _e('Outgoing Payments', ET_DOMAIN); ?></a></li>
                        <li role="presentation"><a href="#incoming-payments" aria-controls="incoming-payments" role="tab" data-toggle="tab"><?php _e('Incoming Payments', ET_DOMAIN); ?></a></li>
                    </ul>
                    <div class="tab-content">
                        <div role="tabpanel" class="tab-pane active" id="outgoing-payments">
                            <p>
                            The billing information below will be used to generate payments from you/your company to Credzu, LLC for the purpose of paying for the listings you create on Credzu.com
                            </p>
                            <div id="bankAccountForm">
                                <form class="et-form">

                                    <div class="form-group clearfix">
                                        <div class="input-group">
                                            <div class="input-group-addon"><i class="fa fa-university"></i></div>
                                            <input type="text" name="bank_name" id="bank_name" placeholder="<?php _e('Bank name', ET_DOMAIN); ?>"  value="<?php echo $bank_name; ?>">
                                        </div>
                                    </div>

                                    <div class="form-group clearfix">
                                        <div class="input-group">
                                            <div class="input-group-addon"><i class="fa fa-university"></i></div>
                                            <input type="text" name="billing_address" id="billing_address" placeholder="<?php _e('Billing address', ET_DOMAIN); ?>"  value="<?php echo $billing_address; ?>">
                                        </div>
                                    </div>

                                    <div class="form-group clearfix">
                                        <div class="input-group">
                                            <div class="input-group-addon"><i class="fa fa-th-large"></i></div>
                                            <div class="bank-group">
                                                <div class="bank">
                                                    <input type="text" name="bank_routing_no" id="bank_routing_no" placeholder="<?php _e('Routing number', ET_DOMAIN); ?>" value="<?php echo $bank_routing_no; ?>">
                                                </div>
                                                <div class="bank">
                                                    <input type="text" name="bank_account_no" id="bank_account_no" placeholder="<?php _e('Account number', ET_DOMAIN); ?>"  value="<?php echo $bank_account_no; ?>">
                                                </div>
                                                <div class="bank">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group float-right send-button-method clearfix">
                                        <button class="btn-submit btn-send"><?php _e('Save', ET_DOMAIN); ?></button>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <div role="tabpanel" class="tab-pane" id="incoming-payments">
                            <div id="paypalAccountForm">
                                <p>
                                This is the "payee" to whom payments will be made when a client hires you/your company.
                                If this is different than your company name it will not change until it is approved.
                                </p>
                                <p>
                                Current Payee Name:
                                </p>
                                <form class="et-form">
                                    <div class="form-group clearfix">
                                        <div class="input-group">
                                            <div class="input-group-addon"><i class="fa fa-envelope"></i></div>
                                            <input type="text" name="company_payee_name_override" id="company_payee_name_override" placeholder="<?php _e('Payee Name', ET_DOMAIN); ?>" value="<?=($bank_payee_name_override_status == 'approved') ? $bank_payee_name_override : $company_name; ?>">
                                            <i class="fa fa-circle text-warning"></i> <span class="text-warning">Pending</span>
                                            <i class="fa fa-circle text-success"></i> <span class="text-success">Approved</span>
                                            <i class="fa fa-circle text-error"></i> <span class="text-error">Declined</span>
                                        </div>

                                    </div>
                                    <div class="form-group clearfix float-right save-button-paypal">
                                        <button class="btn-submit btn-save"><?php _e('Save', ET_DOMAIN); ?></button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php
get_footer();
?>
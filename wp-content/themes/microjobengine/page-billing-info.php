<?php
/**
 * Template Name: Billing Info
 * @ TODO Change all this stuff
 */
global $current_user, $ae_post_factory;
// Get user info
$user = mJobUser::getInstance();
$user_data = $user->convert($current_user->data);

// Bank account data
$bank_first_name = isset($user_data->payment_info['bank']) ? $user_data->payment_info['bank']['first_name'] : '';
$bank_middle_name = isset($user_data->payment_info['bank']) ? $user_data->payment_info['bank']['middle_name'] : '';
$bank_last_name = isset($user_data->payment_info['bank']) ? $user_data->payment_info['bank']['last_name'] : '';
$bank_name = isset($user_data->payment_info['bank']) ? $user_data->payment_info['bank']['name'] : '';
$bank_swift_code = isset($user_data->payment_info['bank']) ? $user_data->payment_info['bank']['swift_code'] : '';
$bank_account_no = isset($user_data->payment_info['bank']) ? $user_data->payment_info['bank']['account_no'] : '';

// Paypal account data
$paypal_email = isset($user_data->payment_info['paypal']) ? $user_data->payment_info['paypal'] : '';

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
                        <li role="presentation" class="active"><a href="#bank-account" aria-controls="bank-account" role="tab" data-toggle="tab"><?php _e('Add a bank account', ET_DOMAIN); ?></a></li>
                        <li role="presentation"><a href="#paypal-account" aria-controls="paypal-account" role="tab" data-toggle="tab"><?php _e('Paypal', ET_DOMAIN); ?></a></li>
                    </ul>
                    <div class="tab-content">
                        <div role="tabpanel" class="tab-pane active" id="bank-account">
                            <div id="bankAccountForm">
                                <form class="et-form">
                                    <div class="form-group clearfix">
                                        <div class="input-group">
                                            <div class="input-group-addon"><i class="fa fa-user"></i></div>
                                            <div class="bank-group">
                                                <div class="bank">
                                                    <input type="text" name="bank_first_name" id="bank_first_name" placeholder="<?php _e('First name', ET_DOMAIN); ?>" value="<?php echo $bank_first_name; ?>">
                                                </div>
                                                <div class="bank">
                                                    <input type="text" name="bank_middle_name" id="bank_middle_name" placeholder="<?php _e('Middle name', ET_DOMAIN); ?>"  value="<?php echo $bank_middle_name; ?>">
                                                </div>
                                                <div class="bank">
                                                    <input type="text" name="bank_last_name" id="bank_last_name" placeholder="<?php _e('Last name', ET_DOMAIN); ?>"  value="<?php echo $bank_last_name; ?>">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group clearfix">
                                        <div class="input-group">
                                            <div class="input-group-addon"><i class="fa fa-university"></i></div>
                                            <input type="text" name="bank_name" id="bank_name" placeholder="<?php _e('Bank name', ET_DOMAIN); ?>"  value="<?php echo $bank_name; ?>">
                                        </div>
                                    </div>

                                    <div class="form-group clearfix">
                                        <div class="input-group">
                                            <div class="input-group-addon"><i class="fa fa-th-large"></i></div>
                                            <input type="text" name="bank_swift_code" id="bank_swift_code" placeholder="<?php _e('SWIFT code', ET_DOMAIN); ?>"  value="<?php echo $bank_swift_code; ?>">
                                        </div>
                                    </div>

                                    <div class="form-group clearfix">
                                        <div class="input-group">
                                            <div class="input-group-addon"><i class="fa fa-user"></i></div>
                                            <input type="text" name="bank_account_no" id="bank_account_no" placeholder="<?php _e('Account number', ET_DOMAIN); ?>"  value="<?php echo $bank_account_no; ?>">
                                        </div>
                                    </div>

                                    <div class="form-group float-right send-button-method clearfix">
                                        <button class="btn-submit btn-send"><?php _e('Save', ET_DOMAIN); ?></button>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <div role="tabpanel" class="tab-pane" id="paypal-account">
                            <div id="paypalAccountForm">
                                <form class="et-form">
                                    <div class="form-group clearfix">
                                        <div class="input-group">
                                            <div class="input-group-addon"><i class="fa fa-envelope"></i></div>
                                            <input type="text" name="paypal_email" id="paypal_email" placeholder="<?php _e('Email address', ET_DOMAIN); ?>" value="<?php echo $paypal_email; ?>">
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
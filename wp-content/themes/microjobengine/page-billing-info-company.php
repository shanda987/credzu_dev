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

$profile = mJobProfileAction()->getProfile($user_ID);
echo mJobProfileAction()->getProfileJson($profile);

// Bank account data
$company_name = !empty($profile->company_name) ? $profile->company_name : '';
$bank_name = isset($user_data->payment_info['bank']) ? $user_data->payment_info['bank']['name'] : '';
$bank_routing_no = isset($user_data->payment_info['bank']) ? $user_data->payment_info['bank']['routing_no'] : '';
$bank_account_no = isset($user_data->payment_info['bank']) ? $user_data->payment_info['bank']['account_no'] : '';

// Second Tab
$bank_payee_name_override = isset($user_data->payment_info['bank']) ? $user_data->payment_info['bank']['payee_name_override'] : '';
$bank_payee_name_override_status = isset($user_data->payment_info['bank']) ? $user_data->payment_info['bank']['payee_name_override_status'] : '';

get_header();

// If Company, this outputs the Company Status bar (Doesn't show when approved)
echo mJobProfileAction()->display_company_status($user_role, $profile->company_status);
?>
    <div class="container mjob-payment-method-page dashboard withdraw section-billing-info">
        <div class="row title-top-pages">
            <p class="block-title"><?php _e('Billing Info', ET_DOMAIN); ?></p>
            <p class="btn-back"><?php _e('"Outgoing Payments" requests billing information which we will use to generate payments from you to Credzu, LLC for listings you create. "Incoming Payments" requests information which we will use to generate payments from clients to you for services you provide them.', ET_DOMAIN); ?></p>
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
                            <div id="bankAccountForm">
                                <?php get_template_part('template/billing', 'form'); ?>
                            </div>
                            <input type="hidden" class="input-item" name="_wpnonce" id="profile_wpnonce" value="<?php echo de_create_nonce('ae-mjob_post-sync');?>" />
                        </div>
                        <div role="tabpanel" class="tab-pane" id="incoming-payments">
                            <div id="incomingPaymentsForm">
                            <?php get_template_part('template/billing-form', 'payee'); ?>
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
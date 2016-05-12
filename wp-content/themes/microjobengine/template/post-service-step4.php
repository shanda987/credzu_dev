<?php
global $user_ID;
$step = 4;

$disable_plan = ae_get_option('disable_plan', false);
if($disable_plan) $step--;
if($user_ID) $step--;

?>
<div class="post-job step-payment step-wrapper" id="step4">
    <p class="select-gateway"><?php _e('Please select your payment method.', ET_DOMAIN); ?></p>

    <form method="post" action="" id="checkout_form">
        <div class="payment_info"></div>
        <div style="position:absolute; left : -7777px; " >
            <input type="submit" id="payment_submit" />
        </div>
    </form>
    <ul class="list-price">
        <div class="row">
            How do we make this published with a submit button?
            They are Saved as Drafts. I want to use the mjob Backbbone to undraft it.
        </div>
    </ul>
    <?php do_action( 'after_payment_list_wrapper' ); ?>
</div>

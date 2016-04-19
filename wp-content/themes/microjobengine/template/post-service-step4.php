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
            <?php
            $paypal = ae_get_option('paypal');
            if($paypal['enable']) {
                ?>
                <li class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
                    <div class="outer-payment-items">
                        <a href="#" class="btn-submit-price-plan select-payment" data-type="paypal"><img src="<?php echo get_stylesheet_directory_uri() ?>/assets/img/icon-paypal.png" alt="" class="img-logo">
                        <p class="text-bank"><?php _e("PAYPAL", ET_DOMAIN); ?></p>
                        </a>
                    </div>
                </li>
            <?php }
            $co = ae_get_option('2checkout');
            if($co['enable']) {
                ?>
                <li class="col-lg-4 col-md-4 col-sm-6 col-xs-12"">
                    <div class="outer-payment-items">
                        <a href="#" class="btn-submit-price-plan select-payment" data-type="2checkout"><img src="<?php echo get_stylesheet_directory_uri() ?>/assets/img/icon-2checkout.png" alt="">
                        <p class="text-checkout"><?php _e("2CHECKOUT", ET_DOMAIN); ?></p>
                        </a>
                    </div>
                </li>
                <?php
            }
            $cash = ae_get_option('cash');
            if($cash['enable']) {
                ?>
                <li class="col-lg-4 col-md-4 col-sm-6 col-xs-12"">
                    <div class="outer-payment-items">
                        <a href="#" class="btn-submit-price-plan select-payment" data-type="cash"><img src="<?php echo get_stylesheet_directory_uri() ?>/assets/img/icon-cash.png" alt="">
                        <p class="text-cash"><?php _e("CASH", ET_DOMAIN); ?></p></a>
                    </div>
                </li>
            </ul>
            <?php }
            do_action( 'after_payment_list' );
            ?>
        </div>
    </ul>
    <?php do_action( 'after_payment_list_wrapper' ); ?>
</div>

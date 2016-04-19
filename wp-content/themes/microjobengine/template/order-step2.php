<?php
global $user_ID;
?>
<div class="post-job step-payment" id="checkout-step2">
    <p class="float-center note"><?php _e('Please select the most appropriate payment gateway for you.', ET_DOMAIN); ?></p>
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
                <li class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                    <div class="outer-payment-items">
                        <a href="#" class="btn btn-submit-price-plan select-payment" data-type="paypal"><img src="<?php echo get_stylesheet_directory_uri() ?>/assets/img/icon-paypal.png" alt="">
                        <p><?php _e("PAYPAL", ET_DOMAIN); ?></p>
                        </a>
                    </div>
                </li>
            <?php }
            $co = ae_get_option('2checkout');
            if($co['enable']) {
                ?>
                <li class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                    <div class="outer-payment-items">
                        <a href="#" class="btn btn-submit-price-plan select-payment" data-type="2checkout"><img src="<?php echo get_stylesheet_directory_uri() ?>/assets/img/icon-2checkout.png" alt="">
                        <p><?php _e("2CHECKOUT", ET_DOMAIN); ?></p>
                        </a>
                    </div>
                </li>
                <?php
            }
            $cash = ae_get_option('cash');
            if($cash['enable']) {
            ?>
            <li class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
            <div class="outer-payment-items">
                <a href="#" class="btn btn-submit-price-plan select-payment" data-type="cash"><img src="<?php echo get_stylesheet_directory_uri() ?>/assets/img/icon-cash.png" alt="">
                    <p><?php _e("CASH", ET_DOMAIN); ?></p>
                </a>
            </div>
            </li>
        </div>    
    <?php }
    do_action( 'after_payment_list' );
    ?>
</div>
    </ul>
    <?php do_action( 'after_payment_list_wrapper' ); ?>
</div>

<?php
get_header();
global $payment_return, $order_id;
extract( $payment_return );
$payment_type = get_query_var( 'paymentType' );
$permalink = home_url();
?>
<div id="content" class="mjob-order-page mjob-single-page payment-fail">
    <div class="block-page">
        <div class="container dashboard withdraw float-center">
            <p class="block-title"><?php _e('Payment Failed', ET_DOMAIN); ?></p>
            <div class="checkout-payment">
                <div class="content">
                    <?php _e("You are now redirected to home page",ET_DOMAIN);?> <br/>
                    <p class="time-leave"><?php printf(__('Time left: %s', ET_DOMAIN ), '<span class="count_down">10</span>')  ?><p>
                </div>
                <?php echo '<a href="'.$permalink.'" class="btn-submit">'.__("Home page", ET_DOMAIN).'</a>'; ?>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    jQuery(document).ready (function () {
        var $count_down	=	jQuery('.count_down');
        setTimeout (function () {
            window.location = '<?php echo $permalink ?>';
        }, 10000 );
        setInterval (function () {
            if($count_down.length >  0) {
                var i	=	 $count_down.html();
                if( parseInt(i) >= 1) {
                    $count_down.html(parseInt(i) - 1);
                }
            }
        }, 1000 );
    });
</script>
<?php
get_footer();
?>

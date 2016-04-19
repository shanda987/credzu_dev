<?php
get_header();
global $payment_return, $order_id;
extract( $payment_return );
$payment_type = get_query_var( 'paymentType' );
if($order_id){
    $permalink	=	get_permalink( $order_id );
}
else{
    $permalink	=	home_url();
}
?>
<div id="content" class="mjob-order-page mjob-single-page">
    <div class="block-page">
        <div class="container float-center">
            <p class="block-title no-margin"><?php _e('PAYMENT SUCCESS', ET_DOMAIN); ?></p>
            <div class="checkout-payment">
                <?php
                global $user_ID;
                if($user_ID):
                    if($order_id):
                        $permalink	=	get_permalink( $order_id );
                        if($payment_type == 'cash'){
                            printf(__("<p>Your listing has been submitted to our website.</p> %s ", ET_DOMAIN) , $response['L_MESSAAGE']);
                        } ?>
                    <p><?php _e("You are now redirected to your order detail page",ET_DOMAIN);?></p>
                       <p class="time-leave"><?php printf(__('Time left: %s', ET_DOMAIN ), '<span class="count_down">10</span>');  ?></p>
                    <?php echo '<a href="'.$permalink.'" class="btn-submit" >'.__("View your order", ET_DOMAIN).'</a>';
                    else:
                        _e('You buy a service success!', ET_DOMAIN);
                    endif;
                    et_destroy_session();
                else:
                    $signin_text = __('Please login to view your order detail', ET_DOMAIN);
                    $signup_text = __('Register an account to handle your order', ET_DOMAIN);
                    mJobAuthFormOnPage(false, $signin_text, $signup_text);
                    global $ae_post_factory;
                    $order_obj = $ae_post_factory->get('mjob_order');
                    $order = get_post($order_id);
                    $order = $order_obj->convert($order);
                    echo '<script type="text/template" id="mjob-order-data">'.json_encode($order).'</script>';
                endif;
                ?>
            </div>
        </div>
    </div>
</div>
<?php if($user_ID): ?>
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
endif;
get_footer();
?>

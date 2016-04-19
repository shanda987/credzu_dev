<?php
/**
 * this template for payment success, you can overide this template by child theme
*/
global $ad, $payment_return;
extract( $payment_return );
$payment_type			= get_query_var( 'paymentType' );
?>
<div id="content" class="redirect-content" style="overflow:hidden" >
	<div class="main-center main-content float-center">
		<h3 class="block-title"><?php _e("SUCCESS, FRIEND",ET_DOMAIN);?></h3>
		<?php
		if($ad):
			$permalink	=	get_permalink( $ad->ID );
		?>
			<div class="content">
			<?php
				if($payment_type == 'cash'){
					printf(__("<p>Your order has been placed successfully.</p> %s ", ET_DOMAIN) , $response['L_MESSAAGE']);
				}

				if($payment_status == 'Pending')
					printf(__("Your payment has been sent successfully but is currently set as 'pending' by %s. <br/>You will be notified when your listing is approved.", ET_DOMAIN), $payment_type);
				?>
				<?php if( isset($bid_msg) ){
					echo $bid_msg;
					} ?>
				<p><?php _e("Your service has been submitted to our website.<br/>You are now redirected to your service details page ...",ET_DOMAIN);?></p>
				<p class="time-leave"><?php printf(__('Time left: %s', ET_DOMAIN ), '<span class="count_down">10</span>');  ?></p>
			</div>
			<?php echo '<a href="'.$permalink.'" class="btn-submit">'.__("View your job", ET_DOMAIN).'</a>'; ?>
		<?php
		else:
			$order = $payment_return['order'];
			$order_pay = $order->get_order_data();

			if($payment_type == 'cash'){
				printf(__("<p>You have purchased successful package: %s.</p> %s ", ET_DOMAIN), $order_pay['payment_package'] , $response['L_MESSAAGE']);
			}
			if($payment_status == 'Pending') {
				printf(__("Your payment has been sent successfully but is currently set as 'pending' by %s. <br/>You will be notified when your listing is approved.", ET_DOMAIN), $payment_type);
			}
			?>
			<?php if( isset($bid_msg) ){
					echo $bid_msg;
					} ?>
			<p><?php _e("You are now redirected to home page ... ",ET_DOMAIN);?></p>
			<p class="time-leave"><?php printf(__('Time left: %s', ET_DOMAIN ), '<span class="count_down">10</span>');
		endif;
		?></p>
	</div>
</div>	

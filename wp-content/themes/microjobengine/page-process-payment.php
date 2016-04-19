<?php 
/**
 *	Template Name: Process Payment
 */
$payment_type			= get_query_var( 'paymentType' );
$session	=	et_read_session ();
global $ad , $payment_return, $order_id;
get_header();
//processs payment
if( isset($session['processType']) && $session['processType'] == 'buy' ){
	$payment_return = mJobOrderAction()->process_payment($payment_type, $session);
	$payment_return	=	wp_parse_args( $payment_return, array('ACK' => false, 'payment_status' => '' ));
	$order_id = $session['order_id'];
	if( $payment_return['ACK'] ){
		get_template_part('template/buy', 'success');
	}
	else{
		get_template_part('template/buy', 'fail');
	}
	exit;
}
else {

	$payment_return = ae_process_payment($payment_type, $session);
}
$ad_id		=	$session['ad_id'];
$payment_return	=	wp_parse_args( $payment_return, array('ACK' => false, 'payment_status' => '' ));
extract( $payment_return );
if($session['ad_id'])
	$ad	=	get_post( $session['ad_id'] );
else 
	$ad	=	false;

?>
<!-- Page Blog -->
<section id="blog-page">
    <div class="container page-container">
		<!-- block control  -->
		<div class="row  block-page">
			<div class="blog-content">


				<?php
				if( ( isset($ACK) && $ACK )  ) {
					if($ad) :
						$permalink	=	get_permalink( $ad->ID );
					else:
						$permalink = home_url();
					endif;
					/**
					 * template payment success
					 */
					get_template_part( 'template/payment' , 'success' );

				} else {

					if($ad):
						$permalink	=	et_get_page_link('post-service', array( 'id' => $ad->ID ));
					else :
						$permalink	=	home_url();
					endif;
					/**
					 * template payment fail
					 */
					get_template_part( 'template/payment' , 'fail' );

				}
				// clear session
				et_destroy_session();

				?>


			</div>
		</div>
    </div>
</section>
<!-- Page Blog / End -->   
<script type="text/javascript">
  	jQuery(document).ready (function () {
  		var $count_down	=	jQuery('.count_down');
		setTimeout (function () {
			window.location = '<?php echo $permalink ?>';
		}, 10000 );
		setInterval (function () { 
			if($count_down.length >  0) {
				var i	=	 $count_down.html();
				$count_down.html(parseInt(i) -1 );
			}					
		}, 1000 );
  	});
</script>

<?php
get_footer();
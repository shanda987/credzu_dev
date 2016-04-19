<?php
global $post, $ae_post_factory;
$mjob_order_obj = $ae_post_factory->get('mjob_order');
$mjob_order = $mjob_order_obj->convert($post);
?>
<li class="mjob-order-item">
	<div class="method">
		<?php
			if($post->post_status == 'pending') : ?> 
				<a title="<?php _e("Approve", ET_DOMAIN); ?>" data-action="approve" class="color-green action publish" data-id="<?php echo $post->ID; ?>" href="#">
					<span class="icon" data-icon="3"></span>
				</a>
				<a title="<?php _e("Decline", ET_DOMAIN); ?>" data-action="decline-mjob-order" class="color-red action decline" data-id="<?php echo $post->ID; ?>" href="#">
					<span class="icon" data-icon="*"></span>
				</a>
		<?php 
			endif; 
		?>
	</div>
	<div class="content">
		<?php 
		if( $post ) {
			switch ($post->post_status) {
			case 'pending':
				echo '<a title="' . __("Pending", ET_DOMAIN) . '" class="color-red error" href="#"><span class="icon" data-icon="!"></span></a>';
				break;
			case 'draft':
				echo '<a title="' .__("Failed", ET_DOMAIN) .'" class="color" style="color :grey;" href="#"><span class="icon" data-icon="*"></span></a>';
				break;
			default:
				echo '<a title="'. __("Confirmed", ET_DOMAIN) . '" class="color-green" href="#"><span class="icon" data-icon="2"></span></a>';
				break;

			} ?>
			<span class="price font-quicksand">
				<?php //echo ae_currency_sign(false) . $order_data['total']; ?>
			</span>
		<?php if($mjob_order): ?>
				<a target="_blank" href="<?php echo get_edit_post_link( $mjob_order->ID ) ?>" class="ad ad-name">
					<?php echo $mjob_order->post_title; ?>
				</a>
			<?php endif;
			 	_e(' by ', ET_DOMAIN); ?>
			<a target="_blank" href="<?php echo get_author_posts_url($post->post_author, $author_nicename = '') ?>" class="company">
				<?php echo get_the_author_meta('display_name',$post->post_author) ?>
			</a>
		<?php 
		} else { 
			$author	=	'<a target="_blank" href="'.get_author_posts_url($post->post_author).'" class="company">' . 
							get_the_author_meta('display_name',$post->post_author) .
						'</a>'; 
		?>
			<span>
				<?php printf (__("This post has been deleted by %s", ET_DOMAIN) , $author ); ?>
			</span>
		<?php 
			} 
		?>
			
	</div>
</li>
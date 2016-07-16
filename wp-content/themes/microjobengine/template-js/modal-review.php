<?php
global $post, $user_ID;
$mjob_post = get_post($post->post_parent);
?>
<!-- MODAL FINISH PROJECT-->
<div class="modal fade" id="modal_review" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">
					<span aria-hidden="true"><img
							src="<?php echo get_template_directory_uri() ?>/assets/img/icon-close.png" alt=""></span>
				</button>
				<h4 class="modal-title"><?php _e("Submit a rating and review.", ET_DOMAIN) ?></h4>
			</div>
			<div class="modal-body">
			<form role="form" id="review_form" class="review-form">
			 <?php
			 	$employer_name = get_the_author_meta( 'display_name', $mjob_post->post_author );
			  ?>
                <div class="form-group">
                    <span class="post_content"><?php printf(__('Rating',ET_DOMAIN),$mjob_post->post_title); ?> </span>
                    <div class="rating-it" style="cursor: pointer;"></div>
				</div>
				<div class="form-group">
					<textarea name="comment_content" cols="8" rows="10" placeholder="<?php _e('Your review here', ET_DOMAIN); ?>"></textarea>
				</div>
                <div class="form-group">
                    <button type="submit" class="btn-submit btn-ok">
						<?php _e('Send', ET_DOMAIN) ?>
                    </button>
					<button type="button" class="btn-skip btn-discard">
						<?php _e('Skip', ET_DOMAIN) ?>
					</button>
				</div>
				</form>
			</div>
		</div><!-- /.modal-content -->
	</div><!-- /.modal-dialog login -->
</div><!-- /.modal -->
<!-- MODAL FINISH PROJECT-->
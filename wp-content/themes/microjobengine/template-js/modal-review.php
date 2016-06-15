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
				<h4 class="modal-title"><?php _e("Write review", ET_DOMAIN) ?></h4>
			</div>
			<div class="modal-body">
			
			</div>
		</div><!-- /.modal-content -->
	</div><!-- /.modal-dialog login -->
</div><!-- /.modal -->
<!-- MODAL FINISH PROJECT-->
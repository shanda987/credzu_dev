<div class="modal fade" id="reject_post">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true"><img
							src="<?php echo get_template_directory_uri() ?>/assets/img/icon-close.png" alt=""></span></button>
				<h4 class="modal-title modal-title-sign-in" id="myModalLabel">
                    <?php printf(__("Reject <span>%s</span>", ET_DOMAIN), 'post' ) ; ?>
                </h4>
			</div>
			<div class="modal-body">
            	<form class="reject-ad reject-project form_modal_style">
                    		
                    <div class="form-group">
                        <!--<label><?php /*_e("MESSAGE", ET_DOMAIN) */?><span class="alert-icon">*</span></label>-->
                        <textarea name="reject_message" rows="10" placeholder="Inactive text field"></textarea>
                    </div>  
                    <div class="clearfix"></div>                 
                    <div class="form-group">
                        <button type="submit" class="btn-submit btn-sumary btn-sub-create mjob-button-reject">
						<?php _e('Reject', ET_DOMAIN) ?>
					</button>
                    </div>              
                    
                </form>  
			</div>
			
		</div><!-- /.modal-content -->
	</div><!-- /.modal-dialog -->
</div><!-- /.modal -->
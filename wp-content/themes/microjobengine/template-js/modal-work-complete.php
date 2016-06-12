<div class="modal fade" id="work_complete_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true"><img
                            src="<?php echo get_template_directory_uri() ?>/assets/img/icon-close.png" alt=""></span></button>
                <h4 class="modal-title" id="myModalLabelForgot"><?php _e('Word is complete?;', ET_DOMAIN); ?></h4>
            </div>
            <div class="modal-body delivery-order">
                <div class="unlock-requirement-modal">
                    <div class="form-group clearfix">
                        <div class="input-group">
                            <p class="notice-message">
                                <span class="note-action"><?php _e('Confirm your job is done.', ET_DOMAIN); ?></span><br/>
                                <?php _e('By confirming that you have completed the work ou agreed to perform for your client. a payment from your client will be generated and they will informed. Please communicate with your client so they understand the difference between performing work and obtaining results.', ET_DOMAIN ) ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group clearfix float-right change-pass-button-method">
                        <button class="btn-submit btn-work-complete-submit"><?php _e('CONFIRM', ET_DOMAIN); ?></button>
                        <input type="hidden" class="input-item" name="_wpnonce" id="profile_wpnonce" value="<?php echo de_create_nonce('ae-mjob_post-sync');?>" />
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
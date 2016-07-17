<div class="modal fade" id="work_complete_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true"><img
                            src="<?php echo get_template_directory_uri() ?>/assets/img/icon-close.png" alt=""></span></button>
                <h4 class="modal-title" id="myModalLabelForgot"><?php _e('Work is complete?', ET_DOMAIN); ?></h4>
            </div>
            <div class="modal-body delivery-order">
                <div class="unlock-requirement-modal">
                    <div class="form-group clearfix">
                        <div class="input-group">
                            <p class="notice-message">
                                <span class="note-action"><?php _e('Confirm your job is done.', ET_DOMAIN); ?></span><br/>
                                <span class="note-body"><?php _e('By confirming that you have completed the work you agreed to perform for your client, a payment from your client will be generated and they will informed. Please communicate with your client so they understand the difference between performing work and obtaining results.', ET_DOMAIN ) ?></span>
                            </p>
                            <p>
                                <div class="input-group col-md-3 col-xs-12"><label for="work_complete_date"> DATE </label> </div>
                                <div class="col-md-9 col-xs-12"> <input type="text"  name="work_complete_date" id="work_complete_date" placeholder="<?php _e('DATE', ET_DOMAIN); ?>" value=""></div>
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
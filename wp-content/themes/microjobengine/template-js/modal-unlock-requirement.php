<div class="modal fade" id="unlock_requirement_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true"><img
                            src="<?php echo get_template_directory_uri() ?>/assets/img/icon-close.png" alt=""></span></button>
                <h4 class="modal-title" id="myModalLabelForgot"><?php _e('ASK MORE INFORMATION', ET_DOMAIN); ?></h4>
            </div>
            <div class="modal-body delivery-order">
                <div class="unlock-requirement-modal">
                    <div class="form-group clearfix">
                        <div class="input-group">
                            <div class="input-group-addon no-addon"><?php _e('Do you want to ask your client to get more information about: <span class="unlock-more"></span>', ET_DOMAIN ) ?></div>
                        </div>
                    </div>
                    <div class="form-group clearfix float-right change-pass-button-method">
                        <button class="btn-submit"><?php _e('Save', ET_DOMAIN); ?></button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
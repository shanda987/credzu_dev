<div class="modal fade" id="change_user_role_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true"><img
                            src="<?php echo get_template_directory_uri() ?>/assets/img/icon-close.png" alt=""></span></button>
                <h4 class="modal-title" id="myModalLabelForgot"><?php _e('Change from client to company?', ET_DOMAIN); ?></h4>
            </div>
            <div class="modal-body delivery-order">
                <div class="unlock-requirement-modal">
                    <div class="form-group clearfix">
                        <div class="input-group">
                            <p class="notice-message">
                                <span class="note-action"><?php _e('NOTE: This action is irreversible!', ET_DOMAIN); ?></span><br/>
                                <?php _e('Changing your account from a "Client (buyer)" profile to a "Company (provider)” is intended for companies only. Unless you are a company intending to provide credit-related services to consumers, do not continue.', ET_DOMAIN ) ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group clearfix float-right change-pass-button-method">
                        <a href="#" class="button role-back-return-css role-back-return" ><?php _e('CANCEL AND RETURN', ET_DOMAIN); ?></a>
                        <button class="btn-submit btn-change-user-role"><?php _e('CONFIRM', ET_DOMAIN); ?></button>
                        <input type="hidden" class="input-item" name="_wpnonce" id="profile_wpnonce" value="<?php echo de_create_nonce('ae-mjob_post-sync');?>" />
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
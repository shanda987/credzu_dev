<div class="modal fade" id="confirmInfo" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog confirmInforContent" role="document" >
        <div class="modal-content">
            <div class="modal-header confirm-header-modal">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true"><img
                            src="<?php echo get_template_directory_uri() ?>/assets/img/icon-close.png" alt=""></span></button>
                <div class="progress-bar">
                    <div class="mjob-progress-bar-item">
                        <?php mJobProgressBar(3, true); ?>
                    </div>
                </div>
                <h4 class="modal-title" id="myModalLabelForgot"><?php _e('CONFIRM CONTACT INFORMATION', ET_DOMAIN) ?></h4>
            </div>
            <div class="modal-body delivery-order">
                <div class="form-delivery-order form-confirm-info">
                    <form class="et-form" id="confirm-form">
                        <div class="form-group clearfix">
                            <div class="input-group">
                                <div class="input-group-addon no-addon"></div>
                                <input type="text" name="first_name" id="first_name" placeholder="<?php _e('First Name', ET_DOMAIN); ?>">
                            </div>
                        </div>
                        <div class="form-group clearfix">
                            <div class="input-group">
                                <div class="input-group-addon no-addon"></div>
                                <input type="text" name="last_name" id="last_name" placeholder="<?php _e('Last Name', ET_DOMAIN); ?>">
                            </div>
                        </div>
                        <div class="form-group clearfix">
                            <div class="input-group">
                                <div class="input-group-addon no-addon"></div>
                                <input type="text" name="phone" id="phone" placeholder="<?php _e('Phone', ET_DOMAIN); ?>">
                            </div>
                        </div>
                        <div class="form-group clearfix">
                            <div class="input-group">
                                <div class="input-group-addon no-addon"></div>
                                <input type="email" name="business_email" id="business_email" placeholder="<?php _e('Email', ET_DOMAIN); ?>">
                            </div>
                        </div>
                        <div class="form-group clearfix">
                            <div class="input-group">
                                <div class="input-group-addon no-addon"></div>
                                <input type="text" name="billing_full_address" id="billing_full_address" placeholder="<?php _e('Physical address', ET_DOMAIN); ?>">
                            </div>
                        </div>
<!--                        <div class="form-group clearfix">-->
<!--                            <div class="input-group">-->
<!--                                <div class="input-group-addon no-addon"></div>-->
<!--                                <input type="text" name="credit_goal" id="credit_goal" placeholder="--><?php //_e('Credit goals', ET_DOMAIN); ?><!--">-->
<!--                            </div>-->
<!--                        </div>-->
                        <div class="form-group clearfix float-right change-pass-button-method">
                            <button class="btn-submit"><?php _e('Update', ET_DOMAIN); ?></button>
                        </div>
                        <input type="hidden" class="input-item" name="_wpnonce" id="profile_wpnonce" value="<?php echo de_create_nonce('ae-mjob_post-sync');?>" />
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
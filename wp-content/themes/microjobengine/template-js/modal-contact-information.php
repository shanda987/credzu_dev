<div class="modal fade" id="contact_info_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true"><img
                            src="<?php echo get_template_directory_uri() ?>/assets/img/icon-close.png" alt=""></span></button>
                <h4 class="modal-title" id="myModalLabelForgot"><?php _e('CONTACT INFORMATION', ET_DOMAIN); ?></h4>
            </div>
            <div class="modal-body delivery-order">
                <div class=" form-confirm-info-modal">
                    <form class="et-form contact-info-form">
                        <div class="form-group clearfix">
                            <div class="input-group">
                                <div class="input-group-addon no-addon"></div>
                                <input type="text" name="first_name" id="first_name" placeholder="<?php _e('First Name', ET_DOMAIN); ?>" value="">
                            </div>
                        </div>
                        <div class="form-group clearfix">
                            <div class="input-group">
                                <div class="input-group-addon no-addon"></div>
                                <input type="text" name="last_name" id="last_name" placeholder="<?php _e('Last Name', ET_DOMAIN); ?>" value="">
                            </div>
                        </div>
                        <div class="form-group clearfix">
                            <div class="input-group">
                                <div class="input-group-addon no-addon"></div>
                                <input type="text" name="phone" id="phone" placeholder="<?php _e('Phone', ET_DOMAIN); ?>" value="">
                            </div>
                        </div>
                        <div class="form-group clearfix">
                            <div class="input-group">
                                <div class="input-group-addon no-addon"></div>
                                <input   class="input-item" type="email" name="business_email" id="business_email" placeholder="<?php _e('Email', ET_DOMAIN); ?>" value="" >
                            </div>
                        </div>
                        <div class="form-group clearfix">
                            <div class="input-group">
                                <div class="input-group-addon no-addon"></div>
                                <input type="text" name="billing_full_address" id="billing_full_address" placeholder="<?php _e('Address Line 1', ET_DOMAIN); ?>" value="">
                            </div>
                        </div>
                        <div class="form-group clearfix">
                            <div class="input-group">
                                <div class="input-group-addon no-addon"></div>
                                <input type="text" name="address_line2" id="address_line2" placeholder="<?php _e('Address Line 2', ET_DOMAIN); ?>" value="">
                            </div>
                        </div>
                        <div class="form-group clearfix">
                            <div class="input-group">
                                <div class="input-group-addon no-addon"></div>
                                <input type="text" name="city" id="city" placeholder="<?php _e('City', ET_DOMAIN); ?>" value="">
                            </div>
                        </div>
                        <div class="form-group clearfix">
                            <div class="input-group">
                                <div class="input-group-addon no-addon"></div>
                                <input type="text" name="state" id="state" placeholder="<?php _e('State', ET_DOMAIN); ?>" value="">
                            </div>
                        </div>
                        <div class="form-group clearfix">
                            <div class="input-group">
                                <div class="input-group-addon no-addon"></div>
                                <input type="text" name="zip_code" id="zip_code" placeholder="<?php _e('Zip code', ET_DOMAIN); ?>" value="">
                            </div>
                        </div>
                        <div class="form-group clearfix">
                            <div class="input-group">
                                <div class="input-group-addon no-addon"></div>
                                <input type="text" name="credit_goal" id="credit_goal" placeholder="<?php _e('Credit goals', ET_DOMAIN); ?>" value="">
                            </div>
                        </div>
                        <div class="form-group clearfix float-right change-pass-button-method">
                            <button class="btn-submit"><?php _e('Save', ET_DOMAIN); ?></button>
                        </div>
                        <input type="hidden" class="input-item" name="_wpnonce" id="profile_wpnonce" value="<?php echo de_create_nonce('ae-mjob_post-sync');?>" />
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
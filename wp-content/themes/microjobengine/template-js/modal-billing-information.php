<div class="modal fade" id="billing_info_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true"><img
                            src="<?php echo get_template_directory_uri() ?>/assets/img/icon-close.png" alt=""></span></button>
                <h4 class="modal-title" id="myModalLabelForgot"><?php _e('BILLING INFORMATION', ET_DOMAIN); ?></h4>
            </div>
            <div class="modal-body delivery-order">
                <div class="form-confirm-billing-modal">
                    <form class="et-form">
                        <div class="form-group clearfix">
                            <div class="input-group">
                                <div class="input-group-addon no-addon"></div>
                                <label for="routing_number"><?php _e('Routing number', ET_DOMAIN); ?></label>
                                <input type="text" name="routing_number" id="routing_number" placeholder="<?php _e('Routing number', ET_DOMAIN); ?>" value="">
                            </div>
                        </div>
                        <div class="form-group clearfix">
                            <div class="input-group">
                                <div class="input-group-addon no-addon"></div>
                                <label for="account_number"><?php _e('Account Number', ET_DOMAIN); ?></label>
                                <input type="text" name="account_number" id="account_number" placeholder="<?php _e('Account Number', ET_DOMAIN); ?>" value="">
                            </div>
                        </div>
                        <div class="form-group clearfix">
                            <div class="input-group">
                                <div class="input-group-addon no-addon"></div>
                                <label for="use_billing_address"><?php _e('Billing address ( same as your address)', ET_DOMAIN); ?></label></br>
                                <select class="hiring-process-select" name="use_billing_address" id="use_billing_address">
                                    <option value="yes"><?php _e('Yes', ET_DOMAIN);?></option>
                                    <option value="no"><?php _e('No', ET_DOMAIN);?></option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group clearfix billing-order-address">
                            <div class="input-group">
                                <div class="input-group-addon no-addon"></div>
                                <input type="text" name="billing_other_address" id="billing_other_address" placeholder="<?php _e('Address', ET_DOMAIN); ?>" value="">
                            </div>
                        </div>
                        <div class="form-group clearfix billing-order-address">
                            <div class="input-group">
                                <div class="input-group-addon no-addon"></div>
                                <input type="text" name="billing_address_line2" id="address_line2" placeholder="<?php _e('Address Line 2', ET_DOMAIN); ?>" value="">
                            </div>
                        </div>
                        <div class="form-group clearfix billing-order-address">
                            <div class="input-group">
                                <div class="input-group-addon no-addon"></div>
                                <input type="text" name="billing_city" id="city" placeholder="<?php _e('City', ET_DOMAIN); ?>" value="">
                            </div>
                        </div>
                        <div class="form-group clearfix billing-order-address">
                            <div class="input-group">
                                <div class="input-group-addon no-addon"></div>
                                <input type="text" name="billing_state" id="state" placeholder="<?php _e('State', ET_DOMAIN); ?>" value="">
                            </div>
                        </div>
                        <div class="form-group clearfix billing-order-address">
                            <div class="input-group">
                                <div class="input-group-addon no-addon"></div>
                                <input type="text" name="billing_zip_code" id="zip_code" placeholder="<?php _e('Zip code', ET_DOMAIN); ?>" value="">
                            </div>
                        </div>
                        <div class="form-group clearfix">
                            <div class="input-group">
                                <div class="input-group-addon no-addon"></div>
                                <label for="use_holder_account"><?php _e('Account holder ( Is this your account?)', ET_DOMAIN); ?></label><br/>
                                <select class="hiring-process-select" name="use_holder_account">
                                    <option value="yes"><?php _e('Yes', ET_DOMAIN);?></option>
                                    <option value="no"><?php _e('No', ET_DOMAIN);?></option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group clearfix account-holder">
                            <div class="input-group">
                                <div class="input-group-addon no-addon"></div>
                                <input type="account_holder" name="account_holder" id="account_holder" placeholder="<?php _e('Account holder', ET_DOMAIN); ?>" value="" >
                            </div>
                        </div>
                        <div class="form-group clearfix float-right change-pass-button-method">
                            <button class="btn-submit"><?php _e('Save', ET_DOMAIN); ?></button>
                        </div>
                        <input type="hidden" name="is_billing" value="1"/>
                        <input type="hidden" class="input-item" name="_wpnonce" id="profile_wpnonce" value="<?php echo de_create_nonce('ae-mjob_post-sync');?>" />
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
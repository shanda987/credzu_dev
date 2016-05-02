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
                    <form id="confirm-form">
                        <ul>
                                <li>
                                    <div id="first_name" class="info-content">
                                        <div class="" data-type="input" data-name="first_name" data-id="#first_name">
                                            <label for="first_name"><?php _e('First Name', ET_DOMAIN )?></label>
                                            <input type="text" name="first_name" placeholder="" value="" />
                                        </div>
                                    </div>
                                </li>
                                <li>
                                    <div id="last_name" class="info-content">
                                        <div class="" data-type="input" data-name="last_name" data-id="#last_name">
                                            <label for="last_name"><?php _e('Last Name', ET_DOMAIN )?></label>
                                            <input type="text" name="last_name" placeholder="" value="" />
                                        </div>
                                    </div>
                                </li>
                                <li>
                                    <div id="phone" class="info-content">
                                        <div class="" data-type="input" data-name="phone" data-id="#phone">
                                            <label for="phone"><?php _e('Phone', ET_DOMAIN )?></label>
                                            <input type="text" name="phone" placeholder="" value="" />
                                        </div>
                                    </div>
                                </li>
                                <li>
                                    <div id="business_email" class="info-content">
                                        <div class="" data-type="input" data-name="business_email" data-id="#business_email">
                                            <label for="business_email"><?php _e('Email', ET_DOMAIN )?></label>
                                            <input type="email" name="business_email" placeholder="" value="" />
                                        </div>
                                    </div>
                                </li>
                                <li>
                                    <div id="billing_full_address" class="info-content  text-address">
                                        <label for="billing_full_address"><?php _e('Physical Address', ET_DOMAIN )?></label>
                                        <textarea class="" name="billing_full_address"></textarea>
                                    </div>
                                </li>
                            <li class="button-sb">
                                <input type="hidden" class="input-item" name="_wpnonce" id="profile_wpnonce" value="<?php echo de_create_nonce('ae-mjob_post-sync');?>" />
                                <button class="btn-submit btn-save waves-effect waves-light" type="submit"><?php _e('SAVE', ET_DOMAIN); ?></button>
                            </li>
                        </ul>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="hire_signup_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true"><img
                            src="<?php echo get_template_directory_uri() ?>/assets/img/icon-close.png" alt=""></span></button>
                <h4 class="modal-title" id="myModalLabelForgot"><?php _e('Login to continue', ET_DOMAIN); ?></h4>
            </div>
            <div class="modal-body delivery-order">
                <div class="unlock-requirement-modal">
                    <div class="form-group clearfix">
                        <div class="input-group">
                            <p class="notice-message">
                                <span class="note-action"><?php _e("Sign up is required, but it's easy.", ET_DOMAIN); ?></span><br/>
                                <?php _e('You can login through Facebook, Twitter, Google or user your email address. Once you login, you can coninute to view and hire companies', ET_DOMAIN ) ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group clearfix float-right change-pass-button-method">
                        <ul class="login-list-icon">
                            <li><a href="#" class="login-facebook facebook_auth_btn"><i class="fa fa-facebook" aria-hidden="true"></i></a></li>
                            <li><a href="#" class="login-google gplus_login_btn gplus"><i class="fa fa-google-plus" aria-hidden="true"></i></i></a></li>
                            <li><a href="<?php echo add_query_arg('action', 'twitterauth', home_url()) ?>" class="login-twitter"><i class="fa fa-twitter" aria-hidden="true"></i></a></li>
                            <li><a class="signup-by-email" href="<?php echo et_get_page_link('user-authentication') ?>" class=""><i class="fa fa-envelope"></i></a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
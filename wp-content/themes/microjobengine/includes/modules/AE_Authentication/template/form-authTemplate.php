<?php
if(!function_exists('mJobSignUpFormStepOne')) {
    /**
     * Render sign up form step 1
     * @param string $intro      Form intro
     * @return void
     * @since 1.0
     * @package MicrojobEngine
     * @category Authentication Template
     * @author Tat Thien
     */
    function mJobSignUpFormStepOne($intro) {
        // Add filter to change the content of note
        //$intro = apply_filters('mjob_signup_form_note', $intro);
        ?>
        <form id="signUpFormStep1" class="form-authentication et-form">
            <div class="inner-form">
                <div class="note-paragraph"><?php echo $intro ?></div>
                <div class="form-group clearfix insert-email">
                    <div class="input-group">
                        <div class="input-group-addon"><i class="fa fa-envelope"></i></div>
                        <button class="btn-submit btn-continue waves-effect waves-light"><?php _e('CONTINUE', ET_DOMAIN); ?><i class="fa fa-angle-right"></i></button>
                        <input type="text" name="user_email" id="user_email" class="form-control" placeholder="<?php _e('Enter your email here', ET_DOMAIN); ?>">
                    </div>
                </div>
                <div class="form-group no-margin social">
                    <?php
                    if( function_exists('ae_render_social_button')){
                        $before_string = __("Or join us with:", ET_DOMAIN);
                        ae_render_social_button(array(), array(), $before_string);
                    }
                    ?>
                </div>
            </div>
        </form>
        <?php
    }
}
if(!function_exists('mJobSignUpFormBeforeStepOne')) {
    /**
     * Render sign up form step before 1
     * @param string $intro      Form intro
     * @return void
     * @since 1.0
     * @package MicrojobEngine
     * @category Authentication Template
     * @author Tat Thien
     */
    function mJobSignUpFormBeforeStepOne($intro) {
        // Add filter to change the content of note
        //$intro = apply_filters('mjob_signup_form_note', $intro);
        ?>
        <form id="signUpFormStep1" class="form-authentication et-form">
            <div class="inner-form">
                <div class="note-paragraph"><?php echo $intro ?></div>
                <ul class="list-price beforeSignup">
                    <div class="row">
                        <li class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                            <div class="outer-payment-items">
                                <a href="#" class="btn-submit-price-plan btn-select-user-role" data-value="individual"><i class="fa fa-user fa-4x"></i>
                                    <p class="text-bank"><?php _e('Individual', ET_DOMAIN); ?></p>
                                </a>
                            </div>
                        </li>
                        <li class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                        <div class="outer-payment-items">
                            <a href="#" class="btn-submit-price-plan btn-select-user-role" data-value="company"><i class="fa fa-building fa-4x"></i>
                                <p class="text-bank"><?php _e('Service Provide', ET_DOMAIN); ?></p>
                            </a>
                        </div>
                        </li>

                    </div></ul>
            </div>
        </form>
        <?php
    }
}
if(!function_exists('mJobSignUpForm')) {
    /**
     * Render sign up form
     * @param boolean $email    If $email = true then the field email will be hidden
     * @return void
     * @since 1.0
     * @package MicrojobEngine
     * @category Authentication Template
     * @author Tat Thien
     */
    function mJobSignUpForm($email = '', $is_page = false, $redirect_url = 'dashboard', $header_text = "") {
        ?>
        <div id="signUpForm">
            <?php
            if(!empty($header_text)) {
                echo '<p class="form-header-text">'. $header_text .'</p>';
            }
            ?>
            <form class="form-authentication et-form float-left">
                <?php
                if($redirect_url == 'dashboard') {
                    $redirect_url = et_get_page_link('dashboard');
                    echo '<input type="hidden" name="redirect_url" class="redirect_url" value="'. $redirect_url .'" />';
                } else if($redirect_url != false) {
                    echo '<input type="hidden" name="redirect_url" class="redirect_url" value="'. $redirect_url .'" />';
                }
                ?>
                <div class="btn-back-sign">
                    <?php
                    if($is_page == true) {
                        ?>
                        <a href="#" class="focus-signin-form"><i class="fa fa-angle-left"></i><?php _e('Back to sign in', ET_DOMAIN); ?></a>
                        <?php
                    }
                    ?>
                </div>
                <div class="inner-form">
                    <?php
                        if(empty($email)) {
                            ?>
                            <div class="form-group clearfix">
                                <div class="input-group">
                                    <div class="input-group-addon"><i class="fa fa-envelope"></i></div>
                                    <input type="text" name="user_email" id="user_email" class="form-control" placeholder="Email">
                                </div>
                            </div>
                            <?php
                        }
                    ?>
                    <div class="form-group clearfix">
                        <div class="input-group">
                            <div class="input-group-addon"><i class="fa fa-user"></i></div>
                            <input type="text" name="user_login" id="user_login" class="form-control" placeholder="User name">
                        </div>
                    </div>
                    <div class="form-group clearfix">
                        <div class="input-group">
                            <div class="input-group-addon"><i class="fa fa-lock"></i></div>
                            <input type="password" name="user_pass" id="user_pass" class="form-control" placeholder="Enter your password">
                        </div>
                    </div>
                    <div class="form-group margin-bot-15 clearfix">
                        <div class="input-group">
                            <div class="input-group-addon"></div>
                            <input type="password" name="repeat_pass" id="repeat_pass" class="form-control repeat_pass" placeholder="Confirm your password">
                        </div>
                    </div>
                    <div class="form-group clearfix float-left check-terms">
                        <div class="checkbox">
                            <label>
                                <input type="checkbox" name="term_privacy" id="term_privacy"><span class="text-choosen"><?php _e('I accept with the', ET_DOMAIN); ?>
                                    <a href="<?php echo et_get_page_link('tos'); ?>" target="_blank"><?php _e('terms and conditions', ET_DOMAIN); ?></a></span>
                            </label>
                        </div>
                    </div>
                    <div class="form-group float-right">
                        <button class="btn-submit waves-effect waves-light"><?php _e('JOIN', ET_DOMAIN); ?></button>
                    </div>

                    <?php if($is_page == true && !is_page_template('page-process-payment.php')) : ?>
                    <div class="clearfix float-right social">
                        <?php
                        if( function_exists('ae_render_social_button')){
                            $before_string = __("Or sign up with:", ET_DOMAIN);
                            ae_render_social_button( array(), array(), $before_string );
                        }
                        ?>
                    </div>
                    <?php endif; ?>
                </div>
            </form>
        </div>
        <?php
    }
}

if(!function_exists('mJobSignInForm')) {
    /**
     * Render sign sign in form
     * @param void
     * @return void
     * @since 1.0
     * @package MicrojobEngine
     * @category Authentication Template
     * @author Tat Thien
     */
    function mJobSignInForm($is_page = false, $redirect_url = 'dashboard', $header_text="") {
        ?>
        <div id="signInForm">
            <?php
                if(!empty($header_text)) {
                    echo '<p class="form-header-text">'. $header_text .'</p>';
                }
            ?>
            <form class="form-authentication et-form">
                <?php
                    if($redirect_url == 'dashboard') {
                        $redirect_url = et_get_page_link('dashboard');
                        echo '<input type="hidden" name="redirect_url" class="redirect_url" value="'. $redirect_url .'" />';
                    } else if($redirect_url != false) {
                        echo '<input type="hidden" name="redirect_url" class="redirect_url" value="'. $redirect_url .'" />';
                    }
                ?>
                <div class="inner-form signin-form">
                    <div class="form-group clearfix">
                        <div class="input-group">
                            <div class="input-group-addon"><i class="fa fa-user"></i></div>
                            <input type="text" name="user_login" id="user_login" class="form-control" placeholder="Username or Email">
                        </div>
                    </div>
                    <div class="form-group clearfix">
                        <div class="input-group">
                            <div class="input-group-addon"><i class="fa fa-lock"></i></div>
                            <input type="password" name="user_pass" id="user_pass" class="form-control" placeholder="Password">
                        </div>
                    </div>
                    <div class="row clearfix">
                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 float-left">
                            <a href="javascript:void(0)" class="open-forgot-modal"><?php _e('Forgot your password?', ET_DOMAIN); ?></a>
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 float-right sign-in-button">
                            <button class="btn-submit waves-effect waves-light"><?php _e('SIGN IN', ET_DOMAIN); ?></button>
                        </div>
                    </div>
                    <div class="clearfix float-right social">
                        <?php
                        if(!is_page_template('page-process-payment.php')) {
                            if( function_exists('ae_render_social_button')){
                                $before_string = __("Or sign in with:", ET_DOMAIN);
                                ae_render_social_button( array(), array(), $before_string );
                            }
                        }
                        ?>
                    </div>
                </div>
                <div class="form-group float-center not-member">
                    <?php
                    if($is_page == true) {
                        ?>
                        <span><?php _e('Not a member yet?', ET_DOMAIN); ?></span> <a href="#" class="focus-signup-form"><?php _e('Join us!', ET_DOMAIN); ?></a>
                        <?php
                    } else  {
                        ?>
                        <span><?php _e('Not a member yet?', ET_DOMAIN); ?></span> <a href="#" class="open-signup-modal"><?php _e('Join us!', ET_DOMAIN); ?></a>
                        <?php
                    }
                    ?>
                </div>
            </form>
        </div>
        <?php
    }
}

if(!function_exists('mJobForgotPasswordForm')) {
    /**
     * Render forgot password form
     * @param void
     * @return void
     * @since 1.0
     * @package MicrojobEngine
     * @category Authentication Template
     * @author Tat Thien
     */
    function mJobForgotPasswordForm() {
        ?>
        <div id="forgotPasswordForm">
            <form class="form-authentication et-form">
                <div class="inner-form">
                    <div class="form-group">
                        <div class="input-group">
                            <div class="input-group-addon"><i class="fa fa-envelope"></i></div>
                            <input type="text" name="user_login" id="user_login" class="form-control" placeholder="Enter your email here">
                        </div>
                    </div>
                    <div class="form-group float-right reset-pass">
                        <button class="btn-submit"><?php _e('SUBMIT', ET_DOMAIN); ?></button>
                    </div>
                </div>
            </form>
        </div>
        <?php
    }
}

if(!function_exists('mJobResetPasswordForm')) {
    /**
     * Render reset password form
     * @param void
     * @return void
     * @since 1.0
     * @package MicrojobEngine
     * @category Authentication Template
     * @author Tat Thien
     */
    function mJobResetPasswordForm() {
        ?>
        <div id="resetPassForm">
            <form class="form-reset et-form">
                <input type="hidden" name="user_login" id="user_login" value="<?php if(isset($_GET['user_login'])) echo $_GET['user_login'] ?>">
                <input type="hidden" name="user_key" id="user_key" value="<?php if(isset($_GET['key'])) echo $_GET['key'] ?>">
                <div class="inner-form">
                    <div class="form-group clearfix">
                        <div class="input-group">
                            <div class="input-group-addon"><i class="fa fa-lock"></i></div>
                            <input type="password" name="new_password" id="new_password" placeholder="<?php _e('New password', ET_DOMAIN); ?>">
                        </div>
                    </div>
                    <div class="form-group clearfix">
                        <div class="input-group">
                            <div class="input-group-addon"><i class="fa fa-asterisk"></i></div>
                            <input type="password" name="repeat_pass" id="repeat_pass" placeholder="<?php _e('Retype password', ET_DOMAIN); ?>">
                        </div>
                    </div>
                    <div class="form-group float-right">
                        <button class="btn-submit"><?php _e('SUBMIT', ET_DOMAIN); ?></button>
                    </div>
                </div>
            </form>
        </div>
        <?php
    }
}

if(!function_exists('mJobChangePasswordForm')) {
    /**
     * Render change password form
     * @param void
     * @return void
     * @since 1.0
     * @package MicrojobEngine
     * @category Authentication Template
     * @author Tat Thien
     */
    function mJobChangePasswordForm() {
        ?>
        <div id="changePassForm">
            <form class="change-password et-form">
                <div class="form-group clearfix">
                    <div class="input-group">
                        <div class="input-group-addon"><i class="fa fa-unlock-alt"></i></div>
                        <input type="password" name="old_password" id="old_password" placeholder="<?php _e('Current password', ET_DOMAIN); ?>">
                    </div>
                </div>
                <div class="form-group clearfix">
                    <div class="input-group">
                        <div class="input-group-addon no-addon"></div>
                        <input type="password" name="new_password" id="new_password" placeholder="<?php _e('New password', ET_DOMAIN); ?>">
                    </div>
                </div>
                <div class="form-group clearfix">
                    <div class="input-group">
                        <div class="input-group-addon no-addon"></div>
                        <input type="password" name="renew_password" id="renew_password" placeholder="<?php _e('Confirm new password', ET_DOMAIN); ?>">
                    </div>
                </div>
                <div class="form-group clearfix float-right change-pass-button-method">
                    <button class="btn-submit"><?php _e('Change', ET_DOMAIN); ?></button>
                </div>
            </form>
        </div>
        <?php
    }
}

if(!function_exists('mJobAuthFormOnPage')) {
    function mJobAuthFormOnPage($redirect_url = false, $signin_text = "", $signup_text = "") {
        echo '<div id="authentication-page">';
        mJobSignInForm(true, $redirect_url, $signin_text);
        mJobSignUpForm('', true, $redirect_url, $signup_text);
        echo '</div>';
    }
}
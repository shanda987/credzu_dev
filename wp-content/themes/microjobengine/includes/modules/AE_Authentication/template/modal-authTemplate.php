<?php
if(!function_exists('mJobModalSignUpStepOne')) {
    /**
     * Modal sign up step 1 - Enter email
     * @param void
     * @return void
     * @since 1.0
     * @package MicrojobEngine
     * @category Authentication Template
     * @author Tat Thien
     */
    function mJobModalSignUpStepOne($intro) {
        ?>
        <div class="modal fade" id="signUpStep1" tabindex="-1" role="dialog" aria-labelledby="myModalLabel1">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true"><img
                                    src="<?php echo get_template_directory_uri() ?>/assets/img/icon-close.png" alt=""></span></button>
                        <h4 class="modal-title" id="myModalLabel1"><?php _e('Join us', ET_DOMAIN); ?></h4>
                    </div>
                    <div class="modal-body">
                        <?php
                        // Show form
                        mJobSignUpFormStepOne($intro);
                        ?>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }
}
if(!function_exists('mJobModalSignUpBeforeStepOne')) {
    /**
     * Modal sign up step 1 - Enter email
     * @param void
     * @return void
     * @since 1.0
     * @package MicrojobEngine
     * @category Authentication Template
     * @author Tat Thien
     */
    function mJobModalSignUpBeforeStepOne($intro) {
        ?>
        <div class="modal fade" id="selectUserRole" tabindex="-1" role="dialog" aria-labelledby="myModalLabel1">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true"><img
                                    src="<?php echo get_template_directory_uri() ?>/assets/img/icon-close.png" alt=""></span></button>
                        <h4 class="modal-title" id="myModalLabel1"><?php _e('Get your free account!', ET_DOMAIN); ?></h4>
                    </div>
                    <div class="modal-body">
                        <?php
                        // Show form
                        mJobSignUpFormBeforeStepOne($intro);
                        ?>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }
}

if(!function_exists('mJobModalSignUp')) {
    /**
     * Modal sign up
     * @param void
     * @return void
     * @since 1.0
     * @package MicrojobEngine
     * @category void
     * @author Tat Thien
     */
    function mJobModalSignUp() {
        ?>
        <div class="modal fade" id="signUpStep2" tabindex="-1" role="dialog" aria-labelledby="myModalLabel2">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                    test
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true"><img
                                    src="<?php echo get_template_directory_uri() ?>/assets/img/icon-close.png" alt=""></span></button>
                        <h4 class="modal-title" id="myModalLabel2"><?php _e('Join us', ET_DOMAIN); ?></h4>
                    </div>
                    <div class="modal-body">
                        <?php
                            //Show form
                            mJobSignUpForm(true);
                        ?>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }
}

if(!function_exists('mJobModalSignIn')) {
    /**
     * Modal sign in
     * @param void
     * @return void
     * @since 1.0
     * @package MicrojobEngine
     * @category Authentication Template
     * @author Tat Thien
     */
    function mJobModalSignIn() {
        ?>
        <div class="modal fade" id="signIn" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true"><img
                                    src="<?php echo get_template_directory_uri() ?>/assets/img/icon-close.png" alt=""></span></button>
                        <h4 class="modal-title" id="myModalLabel"><?php _e('Sign in', ET_DOMAIN); ?></h4>
                    </div>
                    <div class="modal-body">
                        <?php
                            // Show form
                            mJobSignInForm();
                        ?>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }
}

if(!function_exists('mJobModalForgotPassword')) {
    /**
     * Modal forgot password
     * @param void
     * @return void
     * @since 1.0
     * @package MicrojobEngine
     * @category Authentication Template
     * @author Tat Thien
     */
    function mJobModalForgotPassword() {
        ?>
        <div class="modal fade" id="forgotPassword" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true"><img
                                    src="<?php echo get_template_directory_uri() ?>/assets/img/icon-close.png" alt=""></span></button>
                        <h4 class="modal-title" id="myModalLabelForgot"><?php _e('Forgot password', ET_DOMAIN); ?></h4>
                    </div>
                    <div class="modal-body">
                        <?php
                            // Show form
                            mJobForgotPasswordForm();
                        ?>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }
}
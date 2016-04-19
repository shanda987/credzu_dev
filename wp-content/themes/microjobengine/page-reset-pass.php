<?php
global $current_user;
// Redirect if user logged in
if(!empty($current_user->ID)) {
    // @todo ??i l?i link user dashboard
    ob_start();
    wp_redirect(et_get_page_link('dashboard'));
}

get_header();
/**
 * Template Name: Reset Password
 * @since 1.0
 * @package MicrojobEngine
 * @category Authentication
 * @author Tat Thien
 */
?>
    <div id="content">
        <div class="container reset-pass reset-pass-active float-center">
            <p class="reset-title"><?php _e('Reset your password', ET_DOMAIN); ?></p>
            <?php
                mJobResetPasswordForm();
            ?>
        </div>
    </div>
<?php
get_footer();
<?php
/**
 * Template Name: Change Password
 */
global $current_user;
get_header();
?>
    <div class="container dashboard withdraw">
        <div class="row title-top-pages">
            <p class="block-title"><?php _e('Change password', ET_DOMAIN); ?></p>
            <p><?php _e('Here is your change password page', ET_DOMAIN); ?></p>
        </div>
        <div class="row profile">
            <div class="col-lg-3 col-md-3 col-sm-12 col-sx-12 block-items-detail profile">
                <?php get_sidebar('my-profile'); ?>
            </div>

            <div class="col-lg-9 col-md-9 col-sm-12 col-sx-12 box-shadow change-password-dashboard">
                <?php
                    mJobChangePasswordForm();
                ?>
            </div>
        </div>
    </div>
<?php
get_footer();
?>
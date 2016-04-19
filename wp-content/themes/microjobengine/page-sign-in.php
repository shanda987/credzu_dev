<?php
    /**
     * Template Name: Authentication
     */

    global $current_user;
    // Redirect if user logged in
    if(!empty($current_user->ID)) {
        // @todo link to user dashboard
        ob_start();
        wp_redirect(et_get_page_link('dashboard'));
    }

	global $post;
	get_header();
	the_post();
?>
    <div class="container">
        <div class="block-pages post-job page-sign-in">
            <p class="title-pages float-center"><?php _e('AUTHENTICATION', ET_DOMAIN); ?></p>
            <?php
                mJobAuthFormOnPage('dashboard');
            ?>
        </div>
    </div>
<?php
	get_footer();
?>
<?php
/**
 * Template Name: Authentication template
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
$r_url = 'dashboard';
if( isset($_GET['r_url']) && !empty($_GET['r_url']) ):
    $r_url = urlencode($_GET['r_url']);
endif;
?>
    <div class="container">
        <div class="block-pages post-job page-sign-in page-user-authentication">
            <p class="title-pages float-center"></p>
            <?php
//            mJobAuthFormOnPage('dashboard');
            mJobSignUpFormStepOne('');
            mJobSignInForm(true, $r_url);
            mJobSignUpForm('', true, $r_url);
            ?>
        </div>
    </div>
<?php
get_footer();
?>
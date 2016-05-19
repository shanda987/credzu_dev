<?php
global $user_ID;
$user_role = mJobUserAction()->get_role($user_ID);
//if (!is_super_admin() || $user_role !== COMPANY) {
//    wp_redirect(home_url()); exit;
//}

get_header();

/**
 * Template Name: Post a service
 *
 * @param void
 * @return void
 * @since 1.0
 * @package MicrojobEngine
 * @category void
 * @author JACK BUI
 */
$disable_plan = ae_get_option('disable_plan', false);

?>
<div id="content" class="mjob-post-service">
    <div class="container float-center">
        @TODO: Will need to make sure the company has been approved

        <p class="block-title"><?php _e('POST A LISTING', ET_DOMAIN); ?></p>
        <?php if(!$disable_plan) : ?>
        <div class="progress-bar">
            <div class="mjob-progress-bar-item">
            <?php if(!$user_ID):
                mJobProgressBar(4, true);
                else:
                    mJobProgressBar(3, true);
                endif; ?>
            </div>
        </div>
        <?php
        endif;
        // @TODO -- DISABLE THIS FOR NOW, It will be determined on if the company is Approved.
        // check disable payment plan or not
        // if(!$disable_plan) {
        //     get_template_part( 'template/post-service', 'step1' );
        // }

        // Shows login page if not logged in, dont need it i dont think
        // if(!$user_ID) {
        //     get_template_part( 'template/post-service', 'step2' );
        // }

        get_template_part( 'template/post-service', 'step3' );

        if(!$disable_plan) {
            get_template_part( 'template/post-service', 'step4' );
        } ?>
    </div>

</div>
<?php
get_footer();
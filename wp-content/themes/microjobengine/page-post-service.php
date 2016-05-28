<?php
global $user_ID;
//$user_role = mJobUserAction()->get_role($user_ID);
//if (!is_super_admin() || $user_role !== COMPANY) {
//    wp_redirect(home_url()); exit;
//}
$show = true;
if( isset($_REQUEST['rod']) && $_REQUEST['rod'] == 1){
    $show = false;
    echo '<script type="data/json" class="is_rod" >'.json_encode(1).'</script>';
}
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

        <p class="block-title"><?php _e('POST A LISTING', ET_DOMAIN); ?></p>
        <?php if(!$disable_plan) : ?>
        <div class="progress-bar">
            <div class="mjob-progress-bar-item">
            <?php if(!$user_ID):
                mJobProgressBar(4, true);
                else:
                    mJobProgressBar(2, true);
                endif; ?>
            </div>
        </div>
        <?php
        endif;
        // check disable payment plan or not
        // if(!$disable_plan) {
        //     get_template_part( 'template/post-service', 'step1' );
        // }

        // Shows login page if not logged in, dont need it i dont think
        if(!$user_ID):
            get_template_part( 'template/post-service', 'step2' );
        endif;
        if( $show ):
            get_template_part( 'template/post-service', 'step3' );
        endif;
        if(!$disable_plan):
            get_template_part( 'template/post-service', 'step4' );
        edif; ?>
    </div>

</div>
<?php
get_footer();
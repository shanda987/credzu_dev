<?php
global $user_ID;
//$user_role = mJobUserAction()->get_role($user_ID);
//if (!is_super_admin() || $user_role !== COMPANY) {
//    wp_redirect(home_url()); exit;
//}
$show_next = true;
if( isset($_GET['rod']) && $_GET['rod'] == 1){
    $show_next = false;
    echo '<script type="data/json" class="is_rod" >'.json_encode(1).'</script>';
}
if( isset($_GET['id']) ){
    $show = false;
    global $ae_post_factory;
    $obj = $ae_post_factory->get('mjob_post');
    $mjob = get_post($_GET['id']);
    if( !empty($mjob) ){
        $mjob = $obj->convert($mjob);
        echo '<script type="data/json" id="mjob_datas" >'.json_encode($mjob).'</script>';
    }
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

        <p class="block-title post-service-title"><?php _e('POST A LISTING', ET_DOMAIN); ?></p>
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
        if( $show_next ):
            get_template_part( 'template/post-service', 'step3' );
        endif;
        if(!$disable_plan):
            get_template_part( 'template/post-service', 'step4' );
        endif; ?>
    </div>

</div>
<?php
get_footer();
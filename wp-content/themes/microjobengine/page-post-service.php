<?php
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
        <p class="block-title"><?php _e('POST A MJOB', ET_DOMAIN); ?></p>
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
        // check disable payment plan or not
        if(!$disable_plan) {
            get_template_part( 'template/post-service', 'step1' );
        }
        if(!$user_ID) {
            get_template_part( 'template/post-service', 'step2' );
        }
        get_template_part( 'template/post-service', 'step3' );
        if(!$disable_plan) {
            get_template_part( 'template/post-service', 'step4' );
        } ?>
    </div>
</div>
<?php
get_footer();
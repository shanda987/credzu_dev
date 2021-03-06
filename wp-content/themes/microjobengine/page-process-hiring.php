<?php
get_header();
/**
 * Template Name: Process hiring
 *
 * @param void
 * @return void
 * @since 1.0
 * @package MicrojobEngine
 * @category void
 * @author JACK BUI
 */
?>
<div id="content" class="mjob-post-service">
    <div class="container float-center">
        <div class="progress-bar">
            <div class="mjob-progress-bar-item">
                <?php mJobProgressBar(3, true); ?>
            </div>
        </div>
        <p class="block-title"><?php _e('CONFIRM CONTACT INFORMATION', ET_DOMAIN); ?></p>
        <?php

            get_template_part( 'template/hiring', 'step1' );
            get_template_part( 'template/hiring', 'step2' );
            get_template_part( 'template/hiring', 'step3' );
             ?>
    </div>
</div>
<?php
get_footer();
<?php init_user_roles()?>
<footer id="footer">
    <?php
    if( is_active_sidebar( 'mjob-footer-1' )    || is_active_sidebar( 'mjob-footer-2' )
    || is_active_sidebar( 'mjob-footer-3' ) || is_active_sidebar( 'mjob-footer-4' )
    ) {
        $flag = true; ?>
        <div class="et-pull-top">
            <div class="container">
                <div class="row">
                    <div class="col-md-3 col-sm-4 col-xs-12">
                        <?php if (is_active_sidebar('mjob-footer-1')) dynamic_sidebar('mjob-footer-1'); ?>
                    </div>
                    <div class="col-md-3 col-sm-4 col-xs-12">
                        <?php if (is_active_sidebar('mjob-footer-2')) dynamic_sidebar('mjob-footer-2'); ?>
                    </div>
                    <div class="col-md-3 col-sm-4 col-xs-12">
                        <?php if (is_active_sidebar('mjob-footer-3')) dynamic_sidebar('mjob-footer-3'); ?>
                    </div>
                    <div class="col-md-3 col-sm-4 col-xs-12">
                        <?php if (is_active_sidebar('mjob-footer-4')) dynamic_sidebar('mjob-footer-4'); ?>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }

    $copyright = ae_get_option('copyright');
   //$copyright = apply_filters('ae_attribution_footer', $copyright . '<span class="enginethemes">Powered by <a href="https://www.enginethemes.com/themes/microjobengine">MicrojobEngine Theme</a></span>');
    ?>
    <div class="et-pull-bottom">
        <div class="container">
            <div class="row">
                <div class="col-lg-6 col-md-6 col-sm-9 col-xs-12 no-padding">
                    <span class="enginethemes"><?php echo $copyright; ?> </span>
                </div>
                <div class="col-lg-6 col-md-6 col-sm-3 col-xs-12 float-right">
                    <div class="social-link">
                        <ul>
                            <?php $site_facebook = ae_get_option('site_facebook', '');
                            if( !empty($site_facebook) ): ?>
                                <li><a href="<?php echo ae_get_option('site_facebook'); ?>" target="_blank" class="face"><i class="fa fa-facebook"></i></a></li>
                            <?php endif; ?>

                            <?php $site_twitter = ae_get_option('site_twitter', '');
                            if(!empty($site_twitter)): ?>
                                <li><a href="<?php echo ae_get_option('site_twitter'); ?>" target="_blank" class="twitter"><i class="fa fa-twitter"></i></a></li>
                            <?php endif; ?>

                            <?php
                            $site_google = ae_get_option('site_google', '');
                            if(!empty($site_google)): ?>
                                <li><a href="<?php echo ae_get_option('site_google'); ?>" target="_blank" class="google"><i class="fa fa-google-plus"></i></a></li>
                            <?php endif; ?>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</footer><!--End Footer-->
<?php

	wp_footer();
?>
</body>
</html>

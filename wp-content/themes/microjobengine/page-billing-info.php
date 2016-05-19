<?php
/**
 * Template Name: Billing Info
 * @ TODO Change all this stuff
 */
get_header();
?>
    <div class="container mjob-profile-page withdraw section-billing-info">
        <div class="title-top-pages">
            <p class="block-title"><?php _e('MY BILLING INFORMATION', ET_DOMAIN); ?></p>
            <p class="btn-back"><?php _e('Complete your billing information', ET_DOMAIN); ?></p>
        </div>
        <div class="row profile">
            <div class="col-lg-3 col-md-3 col-sm-12 col-sx-12 block-items-detail profile">
                <?php get_sidebar('my-profile'); ?>
            </div>

            <div class="col-lg-9 col-md-9 col-sm-12 col-sx-12">
                <div class="block-profile">
                    <?php get_template_part('template/billing', 'form'); ?>
                </div>

            </div>
        </div>
    </div>
    <input type="hidden" class="input-item" name="_wpnonce" id="profile_wpnonce" value="<?php echo de_create_nonce('ae-mjob_post-sync');?>" />
<?php
get_footer();
?>
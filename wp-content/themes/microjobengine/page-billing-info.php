<?php
/**
 * Template Name: Billing Info
 * @ TODO Change all this stuff
 */
get_header();
?>
    <div class="container mjob-profile-page">
        <div class="title-top-pages">
            <p class="block-title"><?php _e('MY BILLING INFORMATION', ET_DOMAIN); ?></p>
            <p><a href="<?php echo et_get_page_link('dashboard'); ?>" class="btn-back"><i class="fa fa-angle-left"></i><?php _e('Back to dashboard', ET_DOMAIN); ?></a></p>
        </div>
        <div class="row profile">
            <div class="col-lg-4 col-md-4 col-sm-12 col-sx-12 block-items-detail profile">
                <?php get_sidebar('my-profile'); ?>
            </div>

            <div class="col-lg-8 col-md-8 col-sm-12 col-sx-12">
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
<?php
/**
 * Template Name: My jobs listing
 *
 * @param void
 * @return void
 * @since 1.0
 * @package MicrojobEngine
 * @category void
 * @author JACK BUI
 */
get_header();
?>
<div id="content">
    <div class="block-page">
        <div class="container mjob-container-control my-list-mjobs dashboard withdraw">
            <div class="row title-top-pages">
                <!--<div class="col-lg-6 col-md-6 col-sm-12 col-sx-12">
                    <p class="block-title"><?php /*_e('MY JOBS', ET_DOMAIN); */?></p>
                </div>
                <div class="col-lg-6 col-md-6 col-sm-12 col-sx-12 float-right">
                    <?php /*get_template_part('template/filter', 'template'); */?>
                </div>
                <div class="col-lg-12 col-md-12 col-sm-12 col-sx-12">
                    <a href="<?php /*echo et_get_page_link('dashboard'); */?>"><?php /*_e('Back to dashboard', ET_DOMAIN); */?></a>
                </div>-->
                <p class="block-title"><?php _e('MY LISTINGS', ET_DOMAIN); ?></p>
                <div class="filter"><?php get_template_part('template/filter', 'template');?></div>
                <p><a href="<?php echo et_get_page_link('dashboard'); ?>" class="btn-back"><i class="fa fa-angle-left"></i><?php _e('Back to dashboard', ET_DOMAIN); ?></a></p>
            </div>
            <div class="row profile">
                <div class="col-lg-3 col-md-3 col-sm-12 col-sx-12 block-items-detail profile">
                    <?php get_sidebar('my-profile'); ?>
                </div>
                <div class="col-lg-9 col-md-9 col-sm-12 col-sx-12">
                    <div class="block-items no-margin">
                        <?php
                        global $user_ID, $wp_query;
                        $args = array(
                            'post_type'=> 'mjob_post',
                            'author'=> $user_ID,
                            'post_status'=> array(
                                    'pending',
                                    'publish',
                                    'reject',
                                    'archive',
                                    'pause',
                                    'unpause',
                                    'draft',
                                    'inactive'
                                ),
                            );
                        query_posts($args);
                        get_template_part('template/list', 'mjobs');
                        $wp_query->query = array_merge(  $wp_query->query ,array('is_author' => true) ) ;
                        echo '<div class="paginations-wrapper">';
                        ae_pagination($wp_query, get_query_var('paged'), 'load');
                        echo '</div>';
                        wp_reset_query();
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php get_footer() ; ?>

<?php
global $wp_query, $ae_post_factory, $post;
$post_object = $ae_post_factory->get('mjob_post');
get_header();
?>
<div id="content">
    <?php get_template_part('template/content', 'page');?>
    <div class="block-page mjob-container-control">
        <div class="container">
            <div class="row functions-items">
                <div class="col-lg-6 col-md-6 col-sm-6 col-sx-12 no-padding">
                    <h2><?php _e('All Services', ET_DOMAIN); ?></h2>
                </div>
                <div class="col-lg-6 col-md-6 col-sm-16 col-sx-12 no-padding float-right">
                    <?php get_template_part('template/sort', 'template'); ?>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-3 col-md-3 col-sm-12 col-sx-12">
                    <div class="menu-left">
                        <p class="title-menu"><?php _e('Categories', ET_DOMAIN); ?></p>
                        <?php
                        mJobShowFilterCategories('mjob_category', array('parent' => 0));
                        ?>
                    </div>
                    <div class="filter-tags">
                        <p  class="title-menu"><?php _e('Tags', ET_DOMAIN); ?></p>
                        <?php
                        mJobShowFilterTags(array('skill'), array('hide_empty' => false));
                        ?>
                    </div>
                </div>
                <div class="col-lg-9 col-md-9 col-sm-12 col-sx-12">
                    <div class="block-items no-margin mjob-list-container">
                        <?php
                        get_template_part('template/list', 'mjobs');
                        $wp_query->query = array_merge(  $wp_query->query ,array('is_archive_mjob_post' => is_post_type_archive('mjob_post') ) ) ;
                        echo '<div class="paginations-wrapper">';
                        ae_pagination($wp_query, get_query_var('paged'));
                        echo '</div>';
                        wp_reset_query();
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
get_footer();
?>
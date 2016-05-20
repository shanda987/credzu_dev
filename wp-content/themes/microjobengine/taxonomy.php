<?php
global $wp_query, $ae_post_factory, $post;
$post_object = $ae_post_factory->get('mjob_post');
$term = get_queried_object();
get_header();
?>
    <div id="content">
        <?php get_template_part('template/content', 'page');?>
        <div class="block-page mjob-container-control">
            <div class="container">
                <div class="row functions-items">
                    <div class="col-lg-8 col-md-8 col-sm-8 col-sx-12 no-padding">
                        <h2 class="block-title">
                            <span class="block-title-text" data-prefix="<?php _e('in', ET_DOMAIN); ?>">
                                <?php
                                // Get term name
                                $term_name = (isset($term->name) && is_tax('mjob_category')) ? sprintf(__('<span class="term-name">in %s</span>'), $term->name) : '<span class="term-name"></span>';
                                // Get search result
                                $search_result = $wp_query->found_posts;

                                if($search_result == 1) {
                                    printf(__('<span class="search-result-count">%s</span> Search result %s', ET_DOMAIN), $search_result, $term_name);
                                } else {
                                    printf(__('<span class="search-result-count">%s</span> Search results %s', ET_DOMAIN), $search_result, $term_name);
                                }
                                ?>
                            </span>
                        </h2>
                    </div>
                    <div class="col-lg-4 col-md-4 col-sm-4 col-sx-12 no-padding float-right">
                        <?php get_template_part('template/sort', 'template'); ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-3 col-md-3 col-sm-12 col-sx-12">
                        <div class="menu-left">
                            <p class="title-menu"><?php _e('Categories', ET_DOMAIN); ?></p>
                            <?php
                            mJobShowFilterCategories('mjob_category', array('parent' => 0), $term->term_id);
                            ?>
                        </div>
                        <div class="filter-tags">
                            <p  class="title-menu"><?php _e('Tags', ET_DOMAIN); ?></p>
                            <?php
                            mJobShowFilterTags(array('skill'), array('hide_empty' => false), $term->slug);
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
        <?php get_template_part('template/cat', 'block'); ?>
    </div>
<?php
get_footer();
?>
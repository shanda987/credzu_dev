<?php
/**
 * Template name: Blogs
 */
get_header();
?>

    <div id="content">
        <div class="container dashboard withdraw">
            <!-- block control  -->
            <div class="row title-top-pages">
                <p class="block-title"><?php single_cat_title( '', true ); ?></p>
            </div>
            <div class="row block-posts" id="post-control">
                <div class="col-lg-9 col-md-9 col-sm-12 col-xs-12 posts-container" id="posts_control">
                    <?php
                    $args = array(
                        'post_type'=>'post',
                        'post_status'=>array('publish'),
                    );
                    query_posts($args);
                    if(have_posts()){
                        get_template_part( 'template/list', 'posts' );
                    } else {
                        echo '<h5>'.__( 'There is no posts yet', ET_DOMAIN ).'</h5>';
                    }
                    wp_reset_query();
                    ?>
                </div><!-- RIGHT CONTENT -->
                <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12 col-sm-12 col-xs-12">
                    <div class="menu-left">
                        <p class="title-menu"><?php _e('Categories', ET_DOMAIN); ?></p>
                        <?php mJobShowFilterCategories('category', array('parent' => 0)); ?>
                    </div>
                </div>
            </div>
            <!--// block control  -->
        </div>
    </div>
<?php
get_footer();
?>
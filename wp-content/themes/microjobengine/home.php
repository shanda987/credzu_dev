<?php
/**
 * The main template file
 *
 * This is the most generic template file in a WordPress theme and one
 * of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query,
 * e.g., it puts together the home page when no home.php file exists.
 *
 * @link http://codex.wordpress.org/Template_Hierarchy
 *
 * @package WordPress
 * @subpackage MicrojobEngine
 * @since MicrojobEngine 1.0
 */
/**
 * Template Name: Home page
 */
get_header();
$search_text = ae_get_option('site_demonstration', array(
    'search_heading_text'=>__('Get your stuffs done from $5', ET_DOMAIN),
    'search_normal_text'=> __('Browse through millions of micro jobs. Choose one you trust. Pay as you go.', ET_DOMAIN)
));
$img_url = ae_get_option('search_video_fallback', get_stylesheet_directory_uri() ."/assets/img/bg-slider.jpg");
?>
    <div class="slider">
        <div class="search-form">
            <h1 class="wow fadeInDown"><?php echo $search_text['search_heading_text']; ?></h1>
            <h4 class="wow fadeInDown"><?php echo $search_text['search_normal_text']; ?></h4>
            <form action="<?php echo get_site_url(); ?>" class="form-search">
                <div class="outer-form">
                    <span class="text"><?php _e('I am looking for', ET_DOMAIN); ?></span>
                    <input type="text" name="s" class="text-search-home" placeholder="<?php _e($search_text['search_input_textform'], ET_DOMAIN); ?>">
                    <button class="btn-search hvr-buzz-out waves-effect waves-light"><div class="search-title"><span class="text-search"><?php _e('SEARCH NOW', ET_DOMAIN) ;?></span></div></button>
                </div>
            </form>
        </div>
        <div class="background-image">
            <div class="backgound-under"></div>
            <img src="<?php echo $img_url; ?>" alt="" class="wow fadeIn">
        </div>
        <div class="statistic-job-number">
            <p class="link-last-job"><?php echo sprintf(__('There are %s microjobs more', ET_DOMAIN), mJobCountPost()); ?> <div class="bounce"><i class="fa fa-angle-down"></i></div></p>
        </div>
    </div>
    <div id="content">
        <div class="block-hot-items">
            <div class="container inner-hot-items wow fadeInUpBig">
                <?php
                $cat_title = ae_get_option('featured_categories_block_title', __('FIND WHAT YOU NEED', ET_DOMAIN));
                ?>
                <p class="block-title"><?php echo $cat_title;  ?></p>
                <?php
                $terms = get_terms( 'mjob_category', 'orderby=count&hide_empty=0' ); ?>
                <ul class="row">
                    <?php
                    if( !empty($terms) ):
                        $i = 0;
                        $img_url = get_stylesheet_directory_uri().'/assets/img/icon-1.png';
                        foreach( $terms as $key=>$term){
                            $link = get_term_link($term->term_id, 'mjob_category');
                            $featured = get_term_meta($term->term_id, 'featured-tax', true);
                            $img = get_term_meta($term->term_id, 'mjob_category_image', true);
                            if( !empty($img) ){
                                $img_url = esc_url( wp_get_attachment_image_url( $img, 'full' ) );
                            }
                            if( $featured && $i < 8 ):
                                $i++;
                    ?>
                    <li class="col-lg-3 col-md-3 col-sm-6 col-xs-6">
                        <div class="hvr-float-shadow">
                            <div class="avatar">
                                <a href="<?php echo $link; ?>"><img src="<?php echo $img_url;?>" alt=""></a>
                                <div class="line"><span class="line-distance"></span></div>
                            </div>
                            <h2 class="name-items">
                                <a href="<?php echo $link; ?>"><?php echo $term->name ?></a>
                            </h2>
                        </div>
                    </li>
                    <?php
                                endif;

                            }
                            endif; ?>
                </ul>
<!--                <div class="see-all">-->
<!--                    <a href="">SEE ALL CATEGORIES <i class="fa fa-angle-right"></i></a>-->
<!--                </div>-->
            </div>
        </div>
        <div class="block-items">
            <div class="container">
                <?php
                $mjob_title = ae_get_option('featured_mjob_block_title', __('LATEST MICROJOBS', ET_DOMAIN));
                ?>
                <p class="block-title float-center"><?php echo $mjob_title ;?></p>
                <?php global $user_ID;
                $args = array(
                    'post_type'=> 'mjob_post',
                    'post_status'=> array(
                        'publish',
                        'unpause'
                        ),
                    'showposts'=> 8,
                    'orderby'=>'date',
                    'order'=> 'DESC'
                    );
                query_posts($args);
                get_template_part('template/featured', 'mjob');
                wp_reset_query();
                ?>
            </div>
        </div>
        <?php get_template_part('template/about', 'block'); ?>
    </div>
<?php
get_footer();
?>
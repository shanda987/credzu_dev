<?php
/**
 * The main Single template file
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
global $post;
get_header();
the_post();
$cats = get_the_category($post->ID);
$parent = $cats['0']->parent;
$breadcrum = '<h2><span>'.$cats["0"]->name.'</span></h2>';
if( $parent != 0 ){
	$parent = get_term_by('ID', $parent, 'category');
	$breadcrum = '<h2><span>'.$parent->name .'</span><i class="fa fa-angle-right"></i><span class="sub-caterogies">'.$cats["0"]->name.'</span></h2>';
}
?>
<div class="container dashboard withdraw">
	<!-- block control  -->
	<div class="row block-posts post-detail" id="post-control">
		<div class="row title-top-pages">
			<p class="block-title"><?php _e('BLOGS', ET_DOMAIN); ?></p>
			<div class="breadscrums">
				<?php echo $breadcrum; ?>
			</div>
		</div>
		<div class="col-lg-9 col-md-9 col-sm-12 col-xs-12 posts-container">
			<div class="blog-wrapper">
	            <div class="row">
					<div class="blog-content">
						<p class="author-post">Written by <?php the_author();?></p>
						<p class="date-post"><?php the_time('M j');  ?> <sup><?php the_time('S');?></sup>, <?php the_time('Y');?></p>

						<h2 class="title-blog">
							<a href="<?php the_permalink(); ?>"><?php the_title() ?></a>
						</h2><!-- end title -->
						<div class="post-content">
							<?php
							the_content();
							wp_link_pages( array(
								'before'      => '<div class="page-links"><span class="page-links-title">' . __( 'Pages:', ET_DOMAIN ) . '</span>',
								'after'       => '</div>',
								'link_before' => '<span>',
								'link_after'  => '</span>',
							) );
							?>
						</div>
						<div class="cmt">
							<p><span class="text-comment">comments</span>(<?php comments_number(); ?>)</p>
	                    </div><!-- end cmt count -->
					</div>
	            </div>
	        </div>
	        <div class="clearfix"></div>
	        <?php comments_template(); ?>
		</div><!-- RIGHT CONTENT -->
		<div class="col-lg-3 col-md-3 col-sm-12 col-xs-12 blog-sidebar" id="right_content">
			<div class="menu-left">
				<p class="title-menu"><?php _e('Categories', ET_DOMAIN); ?></p>
				<?php mJobShowFilterCategories('category', array('parent' => 0)); ?>
			</div>
		</div><!-- RIGHT CONTENT -->
	</div>
	<!--// block control  -->
</div>
<?php
	get_footer();
?>
<?php
/**
 * The category template file
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
	get_header();
?>

<div id="content">
	<div class="container dashboard withdraw">
		<!-- block control  -->
		<div class="row title-top-pages">
			<p class="block-title"><?php single_cat_title( '', true ); ?></p>
		</div>
		<div class="row block-posts" id="post-control">
			<div class="col-lg-3 col-md-3 col-sm-12 col-xs-12 col-sm-12 col-xs-12">
				<div class="menu-left">
					<p class="title-menu"><?php _e('Categories', ET_DOMAIN); ?></p>
					<?php mJobShowFilterCategories('category', array('parent' => 0)); ?>
				</div>
			</div>
			<div class="col-lg-9 col-md-9 col-sm-12 col-xs-12 posts-container" id="posts_control">
			<?php
				if(have_posts()){
					get_template_part( 'template/list', 'posts' );
				} else {
					echo '<h5>'.__( 'There is no posts yet', ET_DOMAIN ).'</h5>';
				}
			?>
			</div><!-- RIGHT CONTENT -->
		</div>
		<!--// block control  -->
	</div>
</div>
<?php
	get_footer();
?>
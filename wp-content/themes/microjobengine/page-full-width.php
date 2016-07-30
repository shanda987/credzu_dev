<?php
/**
 * Template name: Full Width
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
?>
<div id="content" class="container dashboard withdraw full-page">
	<!-- block control  -->
	<div class="row block-posts post-detail" id="post-control">
		<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 posts-container">
			<div class="blog-wrapper">
	            <div class="rows">
					<div class="blog-contents">
						<div class="post-contents">
							<?php
							the_content();
							?>
						</div>
					</div>
	            </div>
	        </div>
	        <div class="clearfix"></div>
		</div><!-- RIGHT CONTENT -->
	</div>
	<!--// block control  -->
</div>
<?php
	get_footer();
?>
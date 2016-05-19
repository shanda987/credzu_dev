<?php
/**
 * The template for displaying 404 pages (Not Found)
 *
 * @package WordPress
 * @subpackage Freelance Engine
 * @since Freelance Engine 1.0
 */
get_header();
?>

<div id="content" class="blog-header-container">
	<div class="container">
		<!-- blog header -->
		<div class="float-center page-404">
			<p class="note-wrong">Something went wrong!</p>
			<img src="<?php echo get_stylesheet_directory_uri() ?>/assets/img/404.png" alt="">
			<p class="content-404">The link you are looking for seems to be broken or missing.</p>
			<p>You can go back to the previous page or our <a href="<?php echo get_site_url() ?>">homepage</a>
			</p>
			<div class="link-back">
				<a href="<?php echo get_site_url() ?>" class="btn-submit"><i class="fa fa-angle-left"></i>go back</a>
			</div>
		</div>
	</div>
</div>


<?php

get_footer();

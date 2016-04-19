<?php
global $wp_query, $ae_post_factory, $post;
$post_object = $ae_post_factory->get('mjob_post');
get_header();
?>
	<div id="content">
		<?php get_template_part('template/content', 'page');?>
		<div class="block-page mjob-container-control search-result dashboard">
			<div class="container">
				<h2 class="block-title">
					<span class="block-title-text" data-prefix="<?php _e('in', ET_DOMAIN); ?>">
						<?php
						$term_id = (isset($_GET['mjob_category']) && !empty($_GET['mjob_category'])) ? $_GET['mjob_category'] : '';
						$term = get_term($term_id);
						// Get term name
						$term_name = isset($term->name) ? sprintf(__('<span class="term-name">in %s</span>'), $term->name) : '<span class="term-name"></span>';
						// Get search result
						$search_result = $wp_query->found_posts;

						if($search_result == 1) {
							printf(__('<span class="search-result-count">%s</span> Search result for "%s" %s', ET_DOMAIN), $search_result, get_query_var('s'), $term_name);
						} else {
							printf(__('<span class="search-result-count">%s</span> Search results for "%s" %s', ET_DOMAIN), $search_result, get_query_var('s'), $term_name);
						}

						?>
					</span>
					<?php get_template_part('template/sort', 'template'); ?>
				</h2>
				<div class="row search-content">
					<div class="col-lg-3 col-md-3 col-sm-12 col-sx-12">
						<div class="menu-left">
							<p class="title-menu"><?php _e('Categories', ET_DOMAIN); ?></p>
							<?php
								mJobShowFilterCategories( 'mjob_category', array('parent' => 0), $term_id);
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
							get_template_part('template/list', 'mjobs-search');
							echo '<div class="paginations-wrapper">';
							$wp_query->query = array_merge($wp_query->query, array('is_search' => is_search()));
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
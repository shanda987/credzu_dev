<?php
/**
 * Template Name: Page Profile
 */
global $wp_query, $ae_post_factory, $post, $current_user, $user_ID;

$author_id 	= get_query_var('author');
$author = mJobUser::getInstance();
$author_data = $author->get($author_id);

// Convert profile
$profile_obj = $ae_post_factory->get('mjob_profile');
$profile_id = get_user_meta($author_id, 'user_profile_id', true);
if($profile_id) {
    $post = get_post($profile_id);
    if($post && !is_wp_error($post)) {
        $profile = $profile_obj->convert($post);
    }
}

get_header();
?>
    <div class="container mjob-profile-page mjob-author-page">
        <div class="title-top-pages">
            <p class="block-title"><?php printf(__('%s\'s profile', ET_DOMAIN), $author_data->display_name); ?></p>
        </div>
        <div class="row profile user-public-profile">
            <div class="col-lg-4 col-md-4 col-sm-12 col-sx-12 block-items-detail profile">
                <?php get_sidebar('public-profile'); ?>
            </div>

            <div class="col-lg-8 col-md-8 col-sm-12 col-sx-12">
                <?php
                global $mjob_is_author;
                $mjob_is_author = true;
                get_template_part('template/dashboard', 'list-mjobs');
                ?>
            </div>
        </div>
    </div>
<?php
get_footer();
?>
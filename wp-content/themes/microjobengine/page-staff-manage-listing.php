<?php
/**
 * Template Name: Page Profile
 */
global $wp_query, $ae_post_factory, $post, $current_user, $user_ID, $is_individual;
$user = mJobUser::getInstance();
$user_data = $user->convert($current_user->data);
$user_role = ae_user_role($current_user->ID);

$profile = mJobProfileAction()->getProfile($user_ID);
echo mJobProfileAction()->getProfileJson($profile);

get_header();
?>
    <div class="container mjob-profile-page">
        <div class="title-top-pages">
            <p class="block-title"><?php _e('MANAGE LISTINGS', ET_DOMAIN); ?></p>
            <p><a href="<?php echo et_get_page_link('dashboard'); ?>" class="btn-back"><i class="fa fa-angle-left"></i><?php _e('Back to dashboard', ET_DOMAIN); ?></a></p>
        </div>
        <div class="row profile">
            <div class="col-lg-4 col-md-4 col-sm-12 col-sx-12 block-items-detail profile">
                <?php get_sidebar('my-profile'); ?>
            </div>

            <div class="col-lg-8 col-md-8 col-sm-12 col-sx-12">
                <div class="block-profile">
                    <?php
                        global $user_ID;
                        $args = array(
                            'post_type'=> 'mjob_post',
                            'author'=> $user_ID,
                            'post_status'=> array(
                                    'pending',
                                    'publish',
                                    'reject',
                                    'archive',
                                    'pause',
                                    'unpause',
                                    'draft'),
                            );
                        query_posts($args);
                        get_template_part('template/list', 'mjobs');
                        $wp_query->query = array_merge(  $wp_query->query ,array('is_author' => true) ) ;
                        echo '<div class="paginations-wrapper">';
                        ae_pagination($wp_query, get_query_var('paged'), 'load');
                        echo '</div>';
                        wp_reset_query();
                        ?>
                </div>
            </div>

        </div>
    </div>

    <input type="hidden" class="input-item" name="_wpnonce" id="profile_wpnonce" value="<?php echo de_create_nonce('ae-mjob_post-sync');?>" />
<?php
get_footer();
?>
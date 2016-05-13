<?php
/**
 * Template Name: Page Profile
 */
global $wp_query, $ae_post_factory, $post, $current_user, $user_ID, $is_individual;
$user = mJobUser::getInstance();
$user_data = $user->convert($current_user->data);
$user_role = ae_user_role($current_user->ID);

// Convert profile
$profile_obj = $ae_post_factory->get('mjob_profile');
$profile_id = get_user_meta($user_ID, 'user_profile_id', true);
if($profile_id) {
    $post = get_post($profile_id);
    if($post && !is_wp_error($post)) {
        $profile = $profile_obj->convert($post);
    }
    echo '<script type="text/json" id="mjob_profile_data" >'.json_encode($profile).'</script>';
}

get_header();
?>
    <div class="container mjob-profile-page">
        <div class="title-top-pages">
            <p class="block-title"><?php _e('MANAGE COMPANIES', ET_DOMAIN); ?></p>
        </div>
        <div class="row profile">
            <div class="col-lg-4 col-md-4 col-sm-12 col-sx-12 block-items-detail profile">
                <?php get_sidebar('my-profile'); ?>
            </div>

            <div class="col-lg-8 col-md-8 col-sm-12 col-sx-12">
                <div class="block-profile">
                        <div class="form-group clearfix">
                                <div class="input-group">
                                    Items in here.
                                </div>
                            </div>
                            <div class="form-group clearfix float-right change-pass-button-method">
                                <button class="btn-submit"><?php _e('Update', ET_DOMAIN); ?></button>
                            </div>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>
    <input type="hidden" class="input-item" name="_wpnonce" id="profile_wpnonce" value="<?php echo de_create_nonce('ae-mjob_post-sync');?>" />
<?php
get_footer();
?>
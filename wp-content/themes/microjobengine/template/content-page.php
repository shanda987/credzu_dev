<?php
global $user_ID;
$absolute_url = full_url( $_SERVER );
$term = get_queried_object(); //04-22-2016
if( is_page_template('page-post-service.php') ){
    $post_link = '#';
}
else {
    $post_link = et_get_page_link('post-service') . '?return_url=' . $absolute_url;
}
$is_com = mJobUserAction()->is_company($user_ID);
?>
<div class="banner">
    <div class="container">
        <div class="search-slider float-center job-items-title">
            <h2 class="banner-title"><?php echo ae_get_option('mjob_post_block_title', __('Get your stuffs done from $5', ET_DOMAIN)); ?></h2>
            <?php if( is_super_admin() || $is_com ): ?>
            <a href="<?php echo $post_link; ?>" class="btn-post hvr-sweep-to-left waves-effect waves-light"><?php _e('Post a mJob', ET_DOMAIN); ?> <span class="cirlce-plus"><i class="fa fa-plus"></i></span></a>
            <?php endif; ?>
        </div>
    </div>
    <div class="header-images">
        <img src="<?php echo ae_get_option('mjob_post_block_image', get_stylesheet_directory_uri() . '/assets/img/banner.png') ?>" alt="">
    </div>
</div>
<?php
$absolute_url = full_url( $_SERVER );
if( is_page_template('page-post-service.php') ){
    $post_link = '#';
}
else {
    $post_link = et_get_page_link('post-service') . '?return_url=' . $absolute_url;
}
?>
<div class="banner">
    <div class="container">
        <div class="search-slider float-center job-items-title">
            <h2 class="banner-title"><?php echo ae_get_option('mjob_post_block_title', __('Get your stuffs done from $5', ET_DOMAIN)); ?></h2>
            <a href="<?php echo $post_link; ?>" class="btn-post hvr-sweep-to-left waves-effect waves-light"><?php _e('Post a mJob', ET_DOMAIN); ?> <span class="cirlce-plus"><i class="fa fa-plus"></i></span></a>
        </div>
    </div>
    <div class="header-images">
        <img src="<?php echo ae_get_option('mjob_post_block_image', get_stylesheet_directory_uri() . '/assets/img/banner.png') ?>" alt="">
    </div>
</div>
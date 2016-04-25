<?php
$absolute_url = full_url( $_SERVER );

$term = get_queried_object(); //04-22-2016

if( is_page_template('page-post-service.php') ){
    $post_link = '#';
}
else {
    $post_link = et_get_page_link('post-service') . '?return_url=' . $absolute_url;
}
?>
<div class="banner">
    <div class="container">
	
		<div class="search-form">
            <h1 class="wow fadeInDown"><?php _e($term->name, ET_DOMAIN); ?></h1>
            <h4 class="wow fadeInDown"><?php _e($term->description, ET_DOMAIN); ?></h4>
        </div>
	
    </div>
    <div class="header-images">
        <img src="<?php echo ae_get_option('mjob_post_block_image', get_stylesheet_directory_uri() . '/assets/img/banner.png') ?>" alt="">
    </div>
</div>
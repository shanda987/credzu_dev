<?php
global $ae_tax_factory, $wp_query;
$current = $wp_query->query_vars['mjob_category'];
$term = get_term_by('slug', $current, 'mjob_category');
$obj = $ae_tax_factory->get('mjob_category');
$term = $obj->convert($term);
if( !empty($term->mjob_category_banner_image) ) {
    $img_url = esc_url(wp_get_attachment_image_url($term->mjob_category_banner_image, 'full'));
}
else{
    $img_url = get_stylesheet_directory_uri() . '/assets/img/banner.png';
}
?>
<div class="banner">
    <div class="container">
        <div class="search-slider float-center job-items-title job-items-cat-title">
<!--            <h2 class="banner-title">--><?php //echo $term->name; ?><!--</h2>-->
            <p><?php echo $term->mjob_category_page_content; ?></p>
        </div>
    </div>
    <div class="header-images">
        <img src="<?php echo $img_url; ?>" alt="<?php echo $term->name ?>">
    </div>
</div>
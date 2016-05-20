<?php
global $ae_tax_factory, $wp_query;
$current = $wp_query->query_vars['mjob_category'];
$term = get_term_by('slug', $current, 'mjob_category');
$obj = $ae_tax_factory->get('mjob_category');
$term = $obj->convert($term);
$about_title = ae_get_option('about_title', __('ABOUT MICROJOB ENGINE', ET_DOMAIN));
$about_link = ae_get_option('about_link', home_url());
$about_col_1 = ae_get_option('about_col_1', array(
    'about_col_1_title'=> __('Effortless shopping', ET_DOMAIN),
    'about_col_1_link'=> '#',
    'about_col_1_desc'=> __('<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Atque dicta dolorem odit optio placeat praesentium quos reiciendis reprehenderit soluta ullam?</p>', ET_DOMAIN),
));
$col1_img = ae_get_option('about_col_1_image', false);

if( $col1_img ){
    $col1_img = $col1_img['thumbnail'][0];
}
else{
    $col1_img = get_stylesheet_directory_uri().'/assets/img/icon-intro-1.png';
}
$about_col_2 = ae_get_option('about_col_2', array(
    'about_col_2_title'=> __('Be tagged and follow', ET_DOMAIN),
    'about_col_2_link'=> '#',
    'about_col_2_desc'=> __('<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Atque dicta dolorem odit optio placeat praesentium quos reiciendis reprehenderit soluta ullam?</p>', ET_DOMAIN),
));
$col2_img = ae_get_option('about_col_2_image', false);
if( $col2_img ){
    $col2_img = $col2_img['thumbnail'][0];
}
else{
    $col2_img = get_stylesheet_directory_uri().'/assets/img/icon-intro-2.png';
}
$about_col_3 = ae_get_option('about_col_3', array(
    'about_col_3_title'=> __('Paid highly', ET_DOMAIN),
    'about_col_3_link'=> '#',
    'about_col_3_desc'=> __('<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Atque dicta dolorem odit optio placeat praesentium quos reiciendis reprehenderit soluta ullam?</p>', ET_DOMAIN),
));
$col3_img = ae_get_option('about_col_3_image', false);
if( $col3_img ){
    $col3_img = $col3_img['thumbnail'][0];
}
else{
    $col3_img = get_stylesheet_directory_uri().'/assets/img/icon-intro-3.png';
}
?>
<div class="block-intro">
    <div class="container">
        <p class="block-title float-center"><?php echo $about_title; ?></p>
        <ul>
            <li class="col-lg-4 col-md-4 col-sm-12 col-xs-12 clearfix wow fadeInUp">
                <div class="icon-article pull-left">
                    <img src="<?php echo $col1_img; ?>" alt="">
                </div>
                <div class="text-article pull-right">
                    <h5><a href="<?php echo $about_col_1['about_col_1_link']?>" class="title"><?php echo $about_col_1['about_col_1_title'] ?></a></h5>
                    <?php echo $about_col_1['about_col_1_desc']; ?>
                </div>
            </li>
            <li class="col-lg-4 col-md-4 col-sm-12 col-xs-12 clearfix wow fadeInUp">
                <div class="icon-article pull-left">
                    <img src="<?php echo $col2_img; ?>" alt="">
                </div>
                <div class="text-article pull-right">
                    <h5><a href="<?php echo $about_col_2['about_col_2_link']?>" class="title"><?php echo $about_col_2['about_col_2_title'] ?></a></h5>
                    <?php echo $about_col_2['about_col_2_desc']; ?>
                </div>
            </li>
            <li class="col-lg-4 col-md-4 col-sm-12x col-xs-12 clearfix wow fadeInUp">
                <div class="icon-article pull-left">
                    <img  src="<?php echo $col3_img; ?>" alt="">
                </div>
                <div class="text-article pull-right">
                    <h5><a href="<?php echo $about_col_3['about_col_3_link'] ?>" class="title"><?php echo $about_col_3['about_col_3_title'] ?></a></h5>
                    <?php echo $about_col_3['about_col_3_desc']; ?>
                </div>
            </li>
        </ul>
        <div class="load-more float-center">
            <a href="<?php echo $about_link; ?>" class="hvr-wobble-vertical"><?php _e('FIND OUT MORE', ET_DOMAIN); ?><i class="fa fa-angle-right"></i></a>
        </div>
    </div>
</div>

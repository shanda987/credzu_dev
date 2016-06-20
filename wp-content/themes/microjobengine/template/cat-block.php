<?php
global $ae_tax_factory, $wp_query;
$current = $wp_query->query_vars['mjob_category'];
$term = get_term_by('slug', $current, 'mjob_category');
$obj = $ae_tax_factory->get('mjob_category');
$term = $obj->convert($term);
?>
<div class="block-intro">
    <div class="container">
        <p class="mjob_cat_content_css"><?php echo $term->mjob_category_page_content?></p>
        <p class="block-title float-center"><?php echo $term->cat_bottom_title; ?></p>
        <ul>
            <li class="col-lg-4 col-md-4 col-sm-12 col-xs-12 clearfix wow fadeInUp">
<!--                <div class="icon-article pull-left">-->
<!--                    <img src="--><?php //echo $col1_img; ?><!--" alt="">-->
<!--                </div>-->
                <div class="text-article pull-right">
                    <h5><a href="#" class="title"><?php echo $term->cat_bottom_block1_title ?></a></h5>
                    <?php echo $term->cat_bottom_block1_content ?>
                </div>
            </li>
            <li class="col-lg-4 col-md-4 col-sm-12 col-xs-12 clearfix wow fadeInUp">
<!--                <div class="icon-article pull-left">-->
<!--                    <img src="--><?php //echo $col2_img; ?><!--" alt="">-->
<!--                </div>-->
                <div class="text-article pull-right">
                    <h5><a href="#" class="title"><?php echo $term->cat_bottom_block2_title ?></a></h5>
                    <?php echo $term->cat_bottom_block2_content ?>
                </div>
            </li>
            <li class="col-lg-4 col-md-4 col-sm-12x col-xs-12 clearfix wow fadeInUp">
<!--                <div class="icon-article pull-left">-->
<!--                    <img  src="--><?php //echo $col3_img; ?><!--" alt="">-->
<!--                </div>-->
                <div class="text-article pull-right">
                    <h5><a href="#" class="title"><?php echo $term->cat_bottom_block3_title ?></a></h5>
                    <?php echo $term->cat_bottom_block3_content ?>
                </div>
            </li>
        </ul>
    </div>
</div>

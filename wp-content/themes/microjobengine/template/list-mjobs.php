<?php
/**
 * Template list all project
*/
global $wp_query, $ae_post_factory, $post, $post_link;
$post_object = $ae_post_factory->get('mjob_post');

$absolute_url = full_url( $_SERVER );
$post_link = et_get_page_link('post-service') . '?return_url=' . $absolute_url;
?>
<ul class="row list-mjobs">

<?php if (mJobProfileAction()->isCompanyActive()): ?>
<?php // This displays a create box ?>
<li class="col-lg-4 col-md-4 col-sm-4 col-xs-6 mjob-item animation-element animated" nameAnimation="zoomIn">
    <div class="inner clearfix dashboard-new-listing">
        <a href="<?=$post_link;?>">
            <span>+</span>
            <span><?php _e('Post a Listing', ET_DOMAIN); ?></span>
        </a>
    </div>
</li>
<?php endif;?>

<?php
    $postdata = array();
    if(have_posts()) {
        while (have_posts()) { the_post();
            $convert = $post_object->convert($post);
            $postdata[] = $convert;
            get_template_part('template/mjob', 'item');
        }
    } else {
        ?>

        <?php
    }
?>

</ul>

<?php
echo '<script type="data/json" class="mJob_postdata" >'.json_encode($postdata).'</script>';
?>

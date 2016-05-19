<?php
/**
 * Template list all project
*/
global $wp_query, $ae_post_factory, $post;
$post_object = $ae_post_factory->get('mjob_post');
?>
<ul class="row list-mjobs">
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
        <div class="not-found"><?php _e('No Listings found!', ET_DOMAIN); ?></div>
        <?php
    }
?>

</ul>

<?php
echo '<script type="data/json" class="mJob_postdata" >'.json_encode($postdata).'</script>';
?>

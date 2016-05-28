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
<?php
    $postdatas = array();
    if(have_posts()) {
        while (have_posts()) { the_post();
            $convert = $post_object->convert($post);
            $postdatas[] = $convert;
          //     get_template_part('template/mjob', 'item');
        }
    } else {
        ?>

        <?php
    }
?>

</ul>

<?php
echo '<script type="data/json" class="mJob_postdata" >'.json_encode($postdatas).'</script>';
echo '<script type="data/json" class="mJob_postlink" >'.json_encode($post_link).'</script>';
?>

<?php
/**
 * Template list all project
*/
global $wp_query, $ae_post_factory, $post;
$post_object = $ae_post_factory->get('mjob_post');
?>
<ul class="row list-mjobs realated-job">
<?php
    $postdata = array();
    if(have_posts()) {
        while (have_posts()) { the_post();
            $convert = $post_object->convert($post);
            $postdata[] = $convert;
            get_template_part('template/related-mjob', 'item');
        }
    } else {
        echo '<h2>'. __('There are no mJobs found!', ET_DOMAIN) .'</h2>';
    }
?>

</ul>

<?php
echo '<script type="data/json" class="mJob_postdata" >'.json_encode($postdata).'</script>';
?>

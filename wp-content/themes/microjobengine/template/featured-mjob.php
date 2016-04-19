<?php
global $wp_query, $ae_post_factory, $post;
$post_object = $ae_post_factory->get('mjob_post');
?>
<ul class="row featured-job">
    <?php
    $postdata = array();
    if(have_posts()) {
        while (have_posts()) { the_post();
            $convert = $post_object->convert($post);
            $postdata[] = $convert;
            get_template_part('template/featured-mjob', 'item');
        }
    } else {
        echo '<h2>'. __('There are no services found!', ET_DOMAIN) .'</h2>';
    }
    ?>
</ul>
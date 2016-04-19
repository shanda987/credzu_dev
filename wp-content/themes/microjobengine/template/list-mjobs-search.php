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
        printf(__('<div class="not-found">This search matches 0 results! <p class="not-found-sub-text"><label for="input-search" class="new-search-link">New search</label> or <a href="%s">back to home page</a></p></div>', ET_DOMAIN), get_site_url());
    }
    ?>

</ul>

<?php
echo '<script type="data/json" class="mJob_postdata" >'.json_encode($postdata).'</script>';
?>

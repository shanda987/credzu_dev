<?php
global $post, $ae_post_factory;
$post_object = $ae_post_factory->get('mjob_extra');
?>
<ul class="list-extra mjob-list-extras">
    <?php
        $args = array(
            'post_type'=> 'mjob_extra',
            'post_status'=> 'publish',
            'showposts'=> ae_get_option('mjob_extra_numbers', 10),
        );
        $posts = query_posts($args);
        $postdata = array();
        if( have_posts()):
            while( have_posts() ):
                the_post();
                $convert = $post_object->convert($post);
                $postdata[] = $convert;
                //get_template_part('template/extra', 'item');
            endwhile;
        else:

            echo '<p>'. __('There are no extras found!', ET_DOMAIN) .'</p>';
        endif;
        wp_reset_query();
    ?>
</ul>
<?php
/**
* render post data for js
*/
echo '<script type="data/json" class="extra_postdata" >'.json_encode($postdata).'</script>';
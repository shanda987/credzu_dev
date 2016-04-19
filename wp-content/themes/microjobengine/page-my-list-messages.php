<?php
/**
 * Template Name: My List Messages
 */
global $ae_post_factory, $user_ID;
$post_object = $ae_post_factory->get('ae_message');

get_header();
?>
<div id="content" class="mjob_conversation_list_page">
    <div class="block-page">
        <div class="container dashboard withdraw all-list-message">
            <div class="row title-top-pages">
                <p class="block-title"><?php _e('Message list', ET_DOMAIN); ?></p>
                <a href="<?php echo et_get_page_link('dashboard'); ?>" class="btn-back"><i class="fa fa-angle-left"></i><?php _e('Back to dashboard', ET_DOMAIN); ?></a>
            </div>
            <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <?php
                        $post_data = array();
                        $default = mJobQueryConversationDefaultArgs();
                        $args = wp_parse_args(array(
                            'orderby' => 'meta_value',
                            'meta_key' => 'latest_reply_timestamp',
                        ), $default);
                        $conversations_query = new WP_Query($args);
                        if($conversations_query->have_posts()) :
                    ?>
                            <ul class="list-conversation">
                                <?php
                                    while($conversations_query->have_posts()) :
                                        $conversations_query->the_post();
                                        $post_data[] = $post_object->convert($post);

                                        get_template_part('template/conversation', 'item');

                                    endwhile;
                                    wp_reset_postdata();
                                ?>
                            </ul>
                            <?php
                                echo '<div class="paginations-wrapper">';
                                ae_pagination($conversations_query, get_query_var('paged'), 'load');
                                echo '</div>';
                                /**
                                 * render post data for js
                                 */
                                echo '<script type="data/json" class="conversation_postdata" >' . json_encode($post_data) . '</script>';
                            ?>
                    <?php else : ?>
                            <p class="not-found"><?php _e('There are no messages found!', ET_DOMAIN); ?></p>
                    <?php endif;?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
    get_footer();
?>

<?php
    global $post, $ae_post_factory, $user_ID;
    // Set converstaion status to read
    update_post_meta($post->ID, $user_ID . '_conversation_status', 'read');

    $post_object = $ae_post_factory->get('ae_message');
    $current = $post_object->convert($post);

    $from_user = $current->from_user;
    $to_user = $current->to_user;

    // Get receiver_id
    if($user_ID == $from_user) {
        $receiver_id = $to_user;
    } else if($user_ID == $to_user) {
        $receiver_id = $from_user;
    } else {
        wp_redirect(et_get_page_link('dashboard'));
    }

    get_header();
?>
<div id="content" class="mjob_conversation_detail_page">
    <div class="block-page">
        <div class="container">
            <div class="row title-top-pages dashboard withdraw no-margin">
                <p class="block-title"><?php _e('Message Detail', ET_DOMAIN); ?></p>
                <a href="<?php echo et_get_page_link('my-list-messages'); ?>" class="btn-back"><i class="fa fa-angle-left"></i><?php _e('Back to message list', ET_DOMAIN); ?></a>
            </div>
            <div class="row no-margin">
                <div class="col-lg-8 col-md-8 col-sm-12 col-xs-12 conversation-form">
                    <p class="text-dispute note-scroll"><?php _e('You can scroll up to view all messages', ET_DOMAIN); ?></p>
                    <div class="conversation-date float-center">
                        <div class="line"></div>
                        <span class="date"><?php echo get_the_date(get_option('date_format')); ?></span>
                    </div>
                        <?php
                            $post_data = array();
                            $args = array(
                                'post_type' => 'ae_message',
                                'post_status' => 'publish',
                                'meta_query' => array(
                                    'relation' => 'AND',
                                    array(
                                        'relation' => 'OR',
                                        array(
                                            'key' => 'to_user',
                                            'value' => $user_ID,
                                        ),
                                        array(
                                            'key' => 'from_user',
                                            'value' => $user_ID,
                                        )
                                    ),
                                    array(
                                        'key' => 'parent_conversation_id',
                                        'value' => $current->ID,
                                    )
                                ),
                                'orderby' => 'date',
                                'order' => 'DESC'
                            );

                            $messages_query = new WP_Query($args);
                            $messages_query->posts = array_reverse($messages_query->posts);
                            $messages_query->query = array_merge($messages_query->query, array(
                               'fetch_type' => 'message'
                            ));
                        // Load more link
                        echo '<div class="paginations-wrapper">';
                        ae_pagination($messages_query, get_query_var('paged'), 'load', __('Load older messages', ET_DOMAIN));
                        echo '</div>';

                        echo '<div class="wrapper-list-conversation"><ul class="list-conversation">';
                            //get_template_part('template/message', 'item');
                            while($messages_query->have_posts()):
                                $messages_query->the_post();
                                $convert = $post_object->convert($post);
                                $post_data[] = $convert;
                                get_template_part('template/message', 'item');
                            endwhile;
                            wp_reset_postdata();
                        echo '</ul></div>';

                        /**
                         * render post data for js
                         */
                        echo '<script type="data/json" class="message_postdata" >' . json_encode($post_data) . '</script>';
                        ?>

                    <div class="compose-conversation mjob-conversation-form">
                        <form>
                            <input type="hidden" id="from_user" value="<?php echo $user_ID; ?>">
                            <input type="hidden" id="to_user" value="<?php echo $receiver_id; ?>">
                            <input type="hidden" id="conversation_id" value="<?php echo $current->ID; ?>">
                            <input type="hidden" class="input-item post-service_nonce" name="_wpnonce" value="<?php echo de_create_nonce('ae-mjob_post-sync');?>" />
                            <div class="form-group compose">
                                <div class="attachment-file gallery_container_single_conversation" id="message_modal_gallery_container">
                                    <!-- attachments list-->
                                    <ul class="gallery-image carousel-list carousel_single_conversation-image-list" id="image-list">
                                    </ul>

                                    <!-- message input field -->
                                    <div class="group-compose">
                                        <div class="input-compose">
                                            <input type="text" name="post_content" id="post_content" placeholder="Type here to reply">
                                        </div>

                                        <!-- attachment and send button-->
                                        <div class="action-link">
                                            <div class="attachment-image">
                                                <div class="plupload_buttons" id="carousel_single_conversation_container">
                                                    <span class="img-gallery" id="carousel_single_conversation_browse_button">
                                                        <a href="#" class="add-img"><i class="fa fa-paperclip"></i></a>
                                                    </span>
                                                </div>
                                                <span class="et_ajaxnonce" id="<?php echo wp_create_nonce( 'ad_carousels_et_uploader' ); ?>"></span>
                                            </div>
                                            <button class="send-message"><?php _e('Send', ET_DOMAIN); ?></button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12 current">
                    <div class="current-order box-shadow">
                        <p class="title-column"><?php _e('Current Order', ET_DOMAIN); ?></p>
                        <?php
                            $args = array(
                                'post_type' => 'mjob_order',
                                'post_status' => 'publish',
                                'posts_per_page' => 1,
                                'meta_query' => array(
                                    'relation' => 'OR',
                                    array(
                                        'relation' => 'AND',
                                        array(
                                            'key' => 'seller_id',
                                            'value' => $from_user
                                        ),
                                        array(
                                            'key' => 'buyer_id',
                                            'value' => $to_user
                                        )
                                    ),
                                    array(
                                        'relation' => 'AND',
                                        array(
                                            'key' => 'buyer_id',
                                            'value' => $from_user
                                        ),
                                        array(
                                            'key' => 'seller_id',
                                            'value' => $to_user
                                        )
                                    ),
                                )
                            );
                            $current_order_query = new WP_Query($args);
                        ?>
                        <div class="list-order">
                            <?php
                                if($current_order_query->have_posts()) :
                            ?>
                            <ul>
                                <?php
                                    $order_object = $ae_post_factory->get('mjob_order');
                                    while($current_order_query->have_posts()):
                                        $current_order_query->the_post();
                                        $current = $order_object->convert($post);
                                ?>
                                <li>
                                    <h2><a href="<?php echo $current->permalink; ?>"><?php echo $current->post_title; ?></a></h2>
                                    <div class="label-status <?php echo $current->status_class; ?>">
                                        <span><?php echo $current->status_text; ?></span>
                                    </div>

                                    <span class="date-post"><?php echo $current->post_date; ?></span>
                                </li>
                                <?php
                                    endwhile;
                                    wp_reset_postdata();
                                ?>
                            </ul>
                            <?php else : ?>
                                    <p><?php _e('There is no order in progress.', ET_DOMAIN); ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
    echo '<script type="data/json" id="default-message-query">'.json_encode($messages_query->query).'</script>';
    get_footer();
?>

<?php
get_header();
global $wp_query, $ae_post_factory, $post, $user_ID, $current_user;
$post_object    = $ae_post_factory->get( 'mjob_order' );
$current        = $post_object->convert($post);
$user = mJobUser::getInstance();
$user_data = $user->convert($current_user->data);
$flag = false;
if( $user_ID == $current->post_author ){
    $to_user = $current->mjob_author;
}
elseif($user_ID == $current->mjob_author){
    $to_user = $current->post_author;
}
else{
    $to_user = $current->mjob_author;
   $flag = true;
}
$current->_wpnonce = de_create_nonce('ae-mjob_post-sync');
echo '<script type="text/template" id="order_single_data" >'.json_encode($current).'</script>';
?>
<div id="content" class="mjob-single-order-page mjob_conversation_detail_page">
    <div class="block-page">
        <div class="container">
            <div class="row title-top-pages dashboard withdraw no-margin">
                <p class="block-title"><?php _e('ORDER DETAILS AND STATUS', ET_DOMAIN); ?></p>
                <a href="<?php echo et_get_page_link('dashboard'); ?>" class="btn-back"><i class="fa fa-angle-left"></i><?php _e('Back to dashboard', ET_DOMAIN); ?></a>
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
                                'post_parent'=>$current->ID,
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
                        $msg_obj = $ae_post_factory->get('ae_message');
                            while($messages_query->have_posts()):
                                $messages_query->the_post();
                                $convert = $msg_obj->convert($post);
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
                            <input type="hidden" id="to_user" value="<?php echo $to_user; ?>">
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
                <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12 current block-items-detail profile">
                    <div class="box-aside">
                        <div class="order-detail-price">
                            <div class="order-extra">
                                <div class="personal-profile order-detail-profile">
                                    <div class="float-center profile-avatar">
                                        <div class="">
                                            <a href="#" class="">
                                                <?php
                                                echo mJobAvatar($user_ID, 75);
                                                ?>
                                            </a>
                                        </div>
                                    </div>
                                    <h4 class="float-center">
                                        <div id="display_name">
                                            <div class="" data-edit="user" data-id="" data-name="display_name" data-type="input"><?php echo $current_user->display_name; ?></div>
                                        </div>
                                    </h4>
                                    <div class="line">
                                        <span class="line-distance"></span>
                                    </div>
                                    <h4 class="float-center order-mjob-content">
                                        <div >
                                            <div class="" data-edit="user" data-id="" data-name="display_name" data-type="input"><?php echo $current->post_content; ?></div>
                                        </div>
                                    </h4>
                                </div>
                            </div>
                            <div class="order-price">
                                <p class="title-cate"><?php _e('Price', ET_DOMAIN); ?></p>
                                <p class="price-items"><?php echo $current->mjob_price_text; ?></p>
                                <p class="time-order"><i class="fa fa-clock-o" aria-hidden="true"></i><?php _e('Time delivery', ET_DOMAIN); ?></p>
                                <p class="days-order"><?php echo sprintf(__('%s days', ET_DOMAIN), $current->mjob_time_delivery); ?></p>
                            </div>
                            <div class="order-extra">
                                <p class="title-cate"><?php _e('Extra', ET_DOMAIN); ?></p>
                                <p><?php _e('All disputes have been sent to credit bureaus. Waiting for response', ET_DOMAIN); ?></p>
                            </div>
                            <div class="total-order">
                                <p><i class="fa fa-exclamation-circle" aria-hidden="true"></i><?php _e(' Here are the details for your order and company hired', ET_DOMAIN); ?></p>
                            </div>
                        </div>
                </div>
                    <div class="box-aside box-aside2">
                        <div class="order-detail-price">
                            <div class="order-price">
                                <p class="title-cate"><?php _e('Requirement here', ET_DOMAIN); ?></p>

                            </div>
                            <div class="total-order">
                                <p><i class="fa fa-exclamation-circle" aria-hidden="true"></i><?php _e(' There are tasks you must complete', ET_DOMAIN); ?></p>
                            </div>
                        </div>
            </div>
        </div>
    </div>
</div>
</div>
<?php
get_footer();

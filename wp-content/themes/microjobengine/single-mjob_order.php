<?php
get_header();
global $wp_query, $ae_post_factory, $post, $user_ID;
$post_object    = $ae_post_factory->get( 'mjob_order' );
$current        = $post_object->convert($post);
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
<div id="content" class="mjob-single-order-page">
    <div class="block-page">
        <div class="container">
            <div class="row">
                <p class="not-view"><?php if($flag && !is_super_admin()):
                        _e("You can't view this order!", ET_DOMAIN);
                        //return 0;
                    else: ?></p>
                <div class="col-lg-8 col-md-8 col-sm-12 col-xs-12">
                    <div class="dashboard mjob-profile-page">
                        <a href="<?php echo et_get_page_link('my-list-order'); ?>" class="btn-back"><i class="fa fa-angle-left"></i><?php _e('Back to my orders listing', ET_DOMAIN); ?></a>
                    </div>
                    <div class="order-name">
                        <h2><?php echo $current->post_title; ?> <span class="order_status <?php echo $current->status_text_color; ?>"><?php echo $current->status_text ?></span> </h2>
                        <div class="functions-items">
                            <p class="date"><span class="text-date"><?php _e('Modified date: ', ET_DOMAIN); ?></span><?php echo $current->modified_date; ?></p>
                            <div class="status-filter"><?php get_template_part('template/action', 'order') ?></div>
                        </div>
                    </div>
                    <div class="text-description">
                        <p class="title-description"><?php _e('Order detail', ET_DOMAIN); ?></p>
                        <?php echo $current->mjob_content; ?>
                    </div>
                    <?php if( empty($current->order_delivery)  ): ?>
                        <?php if( ($current->post_status == 'publish' || $current->post_status == 'late') && $current->mjob_author == $user_ID  ): ?>
                            <div class="delivery">
                                <button class="btn-submit btn-delivery order-delivery-btn" data-id="<?php echo $current->ID; ?>" data-toggle="modal" ><?php _e('deliver', ET_DOMAIN); ?></button>
                            </div>
                        <?php  endif; ?>
                    <?php else:  ?>
                    <div class="delivery">
                        <p class="title-description"><?php _e('Delivery info', ET_DOMAIN) ?></p>
                        <?php echo $current->order_delivery['0']->post_content;  ?>
                    </div>
                    <div class="file-attachment">
                        <p class="title-description"><?php _e('File attachment', ET_DOMAIN); ?></p>
                        <ul class="list-file">
                            <?php if( !empty($current->order_delivery['0']->et_carousels) ): ?>
                                <?php foreach($current->order_delivery['0']->et_carousels as $key=> $value ){
                                    ?>
                            <li class="image-item" id="<?php echo $value->ID ?>">
                                <a href="<?php echo $value->guid;?>"><i class="fa fa-paperclip"></i> <?php echo $value->post_title; ?></a>
                            </li>
                            <?php }
                            endif; ?>
                        </ul>
                    </div>
                    <?php endif; ?>

                    <?php if( $current->post_status != 'pending' && $current->post_status != 'finished' ): ?>
                        <?php if( $current->post_status != 'disputing' && $current->post_status != 'disputed' ): ?>
                        <div class="dispute" >
                            <p class="text-dispute"><?php _e("You aren't satisfied with this order? You can create dispute here.", ET_DOMAIN); ?></p>
                            <button class="btn-dispute mjob-dispute-order" data-id="<?php echo $current->ID; ?>"><?php _e('dispute', ET_DOMAIN) ;?></button>
                            <div class="compose-conversation mjob-dispute-form" style="display: none;">
                                <form>
                                    <div class="form-group compose">
                                        <div class="attachment-file gallery_container gallery_container_dispute" >
                                            <ul class="gallery-image carousel-list carousel_dispute-image-list carousel_single_conversation-image-list" >
                                            </ul>
                                            <div class="group-compose">
                                                <div class="input-compose">
                                                    <input name="post_content" type="text" placeholder="Type here to reply">
                                                </div>
                                                <div class="action-link">
                                                        <div class="attachment-image">
                                                            <div class="plupload_buttons" id="carousel_dispute_container">
                                                                <span class="img-gallery" id="carousel_dispute_browse_button">
                                                                    <a href="#" class="add-img"><i class="fa fa-paperclip"></i></a>
                                                                </span>
                                                            </div>
                                                            <span class="et_ajaxnonce" id="<?php echo wp_create_nonce( 'ad_carousels_et_uploader' ); ?>"></span>
                                                        </div>
                                                    <button class="send-message"><?php _e('Send', ET_DOMAIN) ?></button>
                                                </div>
                                                <input name="to_user" type="hidden" value="<?php  echo $to_user;  ?>">
                                                <input type="hidden" class="input-item post-service_nonce" name="_wpnonce" value="<?php echo de_create_nonce('ae-mjob_post-sync');?>" />
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <?php else: ?>
                        <div class="dispute mjob-dispute-content mjob_conversation_detail_page">
                            <p class="text-dispute"><?php _e('Dispute center', ET_DOMAIN); ?></p>
                            <div class="wrapper-list-conversation">
                                <ul class="conversation list-conversation">
                                    <?php
                                    $admin_message = '';
                                    if( !empty($current->ae_message)):
                                        foreach($current->ae_message as $key=>$value){
                                            if( $user_ID == $current->post_author || $user_ID == $current->mjob_author || is_super_admin() ):
                                                $winner = get_post_meta($value->ID,'winner', true);
                                                if( ae_user_role($value->post_author) != 'administrator' || !$winner ) :

                                            ?>
                                        <li class="clearfix">
                                            <div class="<?php echo $value->message_class; ?>">
                                                <div class="img-avatar">
                                                    <?php echo $value->author_avatar; ?>
                                                </div>
                                                <div class="conversation-text">
                                                    <?php echo $value->post_content; ?>
                                                    <ul>
                                                        <?php if( !empty($value->et_files) ): ?>
                                                            <?php foreach($value->et_files as $key1=> $value1 ){
                                                                ?>
                                                                <li class="image-item" id="<?php echo $value1->ID ?>">
                                                                    <a href="<?php echo $value1->guid;?>"><i class="fa fa-paperclip"></i> <?php echo $value1->post_title; ?></a>
                                                                </li>
                                                            <?php }
                                                        endif; ?>
                                                    </ul>
                                                </div>

                                                <span class="message-time">
                                                    <?php echo $value->post_date; ?>
                                                </span>
                                            </div>
                                        </li>
                                    <?php
                                            else:
                                                $admin_message = $value;
                                            endif;
                                        endif;
                                            }
                                    endif ?>
                                </ul>
                            </div>
                            <?php if( $current->post_status == 'disputing'): ?>
                            <div class="compose-conversation mjob-dispute-form" >
                                <form>
                                    <div class="form-group compose">
                                        <div class="attachment-file gallery_container gallery_container_dispute" >
                                            <ul class="gallery-image carousel-list carousel_dispute-image-list carousel_single_conversation-image-list" >
                                            </ul>
                                            <div class="group-compose">
                                                <div class="input-compose">
                                                    <input type="text" name="post_content" id="post_content" placeholder="Type here to reply">
                                                </div>
                                                <div class="action-link">
                                                    <div class="attachment-image">
                                                        <div class="plupload_buttons" id="carousel_dispute_container">
                                                            <span class="img-gallery" id="carousel_dispute_browse_button">
                                                                <a href="#" class="add-img"><i class="fa fa-paperclip"></i></a>
                                                            </span>
                                                        </div>
                                                        <span class="et_ajaxnonce" id="<?php echo wp_create_nonce( 'ad_carousels_et_uploader' ); ?>"></span>
                                                    </div>
                                                    <button class="send-message"><?php _e('Send', ET_DOMAIN) ?></button>
                                                </div>
                                            </div>
                                        </div>
                                        <input name="to_user" type="hidden" value="<?php echo $to_user; ?>">
                                        <input type="hidden" class="input-item post-service_nonce" name="_wpnonce" value="<?php echo de_create_nonce('ae-mjob_post-sync');?>" />
                                    </div>
                                </form>
                            </div>
                            <?php endif; ?>
                        </div>
                        <?php endif;
                        endif;
                        if( ($current->post_status == 'disputing' && is_super_admin()) || $current->post_status == 'disputed' ): ?>
                        <div class="decided mjob-admin-dispute-form">
                            <p class="text"><?php _e("Admin's decided", ET_DOMAIN) ?></p>
                            <?php if( $current->post_status == 'disputing' && is_super_admin() ): ?>
                            <form>
                                <div class="form-group">
                                    <textarea name="post_content" rows="10"></textarea>
                                </div>
                                <div class="form-group">
                                    <p class="text-result-choose"><?php _e('Admin choose results', ET_DOMAIN); ?></p>
                                    <ul class="list-decided">
                                        <li>
                                            <div class="checkbox">
                                                <label>
                                                    <input type="radio" name="winner" id="winner" value="<?php echo $current->post_author;  ?>" checked>
                                                    <span><?php echo sprintf(__("%s is winner", ET_DOMAIN), $current->author_name); ?></span>
                                                </label>
                                            </div>
                                        </li>
                                        <li>
                                            <div class="checkbox">
                                                <label>
                                                    <input type="radio" name="winner" id="winner" value="<?php echo $current->mjob_author ?>">
                                                    <span><?php echo sprintf(__("%s is winner", ET_DOMAIN), $current->mjob_author_name); ?></span>
                                                </label>
                                            </div>
                                        </li>
                                    </ul>
                                </div>
                                <div class="form-group">
                                    <button class="btn-submit"><?php _e('Submit', ET_DOMAIN); ?></button>
                                    <input name="to_user" type="hidden" value="<?php echo $to_user; ?>">
                                    <input type="hidden" class="input-item post-service_nonce" name="_wpnonce" value="<?php echo de_create_nonce('ae-mjob_post-sync');?>" />
                                </div>
                            </form>
                            <?php else:
                                if( !empty($admin_message) ):
                                echo $admin_message->post_content;
                                echo '<p class="text-name-winner">';
                                $winner_name = get_post_meta($admin_message->ID, 'winner_name', true);
                                echo sprintf(__('%s is winner', ET_DOMAIN), $winner_name);
                                echo '</p>';
                                endif;
                             endif; ?>
                        </div>
                        <?php endif; ?>
                    <div class="block-items related">
                        <?php
                        $author = get_userdata( ($current->seller_id ) );
                        $display_name = '';
                        if( $author ){
                            $display_name = $author->display_name;
                        }
                        ?>
                        <p class="text-dispute"><?php echo sprintf(__('job related: %s', ET_DOMAIN), $display_name); ?></p>
                        <?php
                        global $user_ID;
                        $args = array(
                            'post_type'=> 'mjob_post',
                            'post_status'=> 'publish',
                            'showposts'=> 3,
                            'author'=> $current->seller_id);
                        query_posts($args);
                        get_template_part('template/list-related', 'mjobs');
                        wp_reset_query();
                        ?>
                    </div>
                </div>
                <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12 block-items-detail profile">
                    <?php get_sidebar('single-profile'); ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
</div>
<?php
get_footer();

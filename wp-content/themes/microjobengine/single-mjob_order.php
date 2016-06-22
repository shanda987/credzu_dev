<?php
get_header();
global $wp_query, $ae_post_factory, $post, $user_ID, $current_user, $ae_tax_factory;
$obj_tax = $ae_tax_factory->get('mjob_requirement');
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
$user_role = ae_user_role($user_ID);
$profile = mJobProfileAction()->getProfile($to_user);
$profile_individual = mJobProfileAction()->getProfile($user_ID);
echo mJobProfileAction()->getProfileJson($profile_individual);
$current->_wpnonce = de_create_nonce('ae-mjob_post-sync');
echo '<script type="text/template" id="order_single_data" >'.json_encode($current).'</script>';
?>
<div id="content" class="mjob-single-order-page mjob_conversation_detail_page">
    <div class="block-page">
        <div class="container">
            <div class="row title-top-pages dashboard withdraw no-margin">
                <div class="box-shadow-title">
                    <p class="block-title">
                        <?php _e('ORDER DETAILS', ET_DOMAIN); ?>
                    </p>
                    <p class="btn-back"><?php _e('Communicate with your company below.', ET_DOMAIN); ?></p>
                </div>
            </div>
            <div class="row no-margin">
                <div class="col-lg-8 col-md-8 col-sm-12 col-xs-12 conversation-form">
<!--                    <p class="text-dispute note-scroll">--><?php //_e('You can scroll up to view all messages', ET_DOMAIN); ?><!--</p>-->
<!--                    <div class="conversation-date float-center">-->
<!--                        <div class="line"></div>-->
<!--                        <span class="date">--><?php //echo get_the_date(get_option('date_format')); ?><!--</span>-->
<!--                    </div>-->
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
                        $filess = array();
                        $post_data_msg = array();
                        if( $messages_query->have_posts() ):
                            while($messages_query->have_posts()):
                                $messages_query->the_post();
                                $convert_msg = $msg_obj->convert($post);
                                $post_data_msg[] = $convert_msg;
                                $filess[]= $convert_msg->et_files;
                                if( $user_role != COMPANY || $convert_msg->action_type != 'delivery_new' ) {
                                    get_template_part('template/message', 'item');
                                }
                            endwhile;
                            wp_reset_query();
                        else:
                            if( $user_role == COMPANY ){
                                $msg_cl = 'private-message';
                            }
                            else{
                                $msg_cl = 'guest-message';
                            }
                            ?>
                            <li class="clearfix message-item">
                                <div class="<?php echo $msg_cl; ?>">
                                    <div class="img-avatar">
                                        <?php echo mJobAvatar($current->mjob_author); ?>
                                    </div>
                                    <div class="conversation-text">
                                        <?php _e('Thank you for hiring and trusting us! Under the law, there is a 72 hour waiting period before we can begin work. Once that expires, we will begin. In the meantime, if you have any questions, comments or concerns, message us here. Also, this is a perfect time for you to get all your documents together (if you haven\'t done so already).', ET_DOMAIN);?>
                                    </div>
                                    <div class="message-time">
                                        <?php echo et_the_time(get_the_time('U', $current->ID));?>
                                    </div>
                                </div>
                            </li>

                        <?php endif;
                        echo '</ul></div>';


                        /**
                         * render post data for js
                         */
                        echo '<script type="data/json" class="message_postdata" >' . json_encode($post_data_msg) . '</script>';
                        ?>

                    <div class="compose-conversation mjob-conversation-form">
                        <form>
                            <input type="hidden" id="from_user" name="from_user" value="<?php echo $user_ID; ?>">
                            <input type="hidden" id="to_user" name="to_user" value="<?php echo $to_user; ?>">
                            <input type="hidden" id="post_parent" name="post_parent" value="<?php echo $current->ID; ?>">
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
                                                echo mJobAvatar($current->mjob_author, 75);
                                                ?>
                                            </a>
                                        </div>
                                    </div>
                                    <h4 class="float-center">
                                        <div id="display_name">
                                            <div class="" data-edit="user" data-id="" data-name="display_name" data-type="input"><?php echo $profile->first_name.' '.$profile->last_name; ?></div>
                                        </div>
                                    </h4>
                                    <div class="line">
                                        <span class="line-distance"></span>
                                    </div>
                                    <h4 class="float-center order-mjob-content">
                                        <div >
                                            <div class="" data-edit="user" data-id="" data-name="display_name" data-type="input">
                                                <?php
                                                    $to_role = ae_user_role($to_user);
                                                    if( $to_role == COMPANY):
                                                    echo $current->mjob->post_title;
                                                    elseif( $to_role == INDIVIDUAL ):
                                                        echo $profile->credit_goal;
                                                    else:
                                                        _e('User Description here', ET_DOMAIN);
                                                    endif;
                                                ?></div>
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
                            <div class="order-extra list-order">
                                <p class="title-cate"><?php _e('Status', ET_DOMAIN); ?></p>
                                <?php
//                                    if( empty($profile->company_status_message) ):
//                                        _e('Pursuant to Federal and State law, your company cannot begin credit repair services until the 72 hours cancellation period has ended. Which began at the moment you signed your agreement with the company', ET_DOMAIN);
//                                    else:
//                                        echo $profile->company_status_message;
//                                    endif;
                                    $t1 = strtotime($current->post_date);
                                    $t2 = time();
                                    $t = $t2 - $t1;
                                     if( $t >= 300 ):
                                         if( $current->post_status == 'publish' ) {
                                             mJobOrderAction()->updateOrderStatus($current->ID, 'processing');
                                         }
                                         ?>
                                         <?php if( $user_role == COMPANY): ?>
                                            <?php if( $current->post_status == 'processing' ): ?>
                                             <p><?php _e('Once you have completed the service, click "Completed" below after  which a payment from your client be generated and your client will be informed to wait for results', ET_DOMAIN); ?></p>
                                             <button class="btn-submit btn-work-complete-css btn-work-complete-action"><?php _e('Work Complete', ET_DOMAIN); ?></button>
                                             <?php elseif( $current->post_status == 'verification'): ?>
                                                <p><?php _e("It is important that you update your client with results as soon as you can. Once results are shown, your client can review your company's performanceas well as rehire you", ET_DOMAIN); ?></p>
                                                <button data-id="<?php echo $current->ID; ?>" class="btn-submit btn-work-complete-css btn-delivery order-delivery-btn"><?php _e('SUBMIT RESULTS', ET_DOMAIN); ?></button>
                                             <?php elseif($current->post_status == 'finished' || $current->post_status == 'delivery'): ?>
                                             <p><?php _e("This job has ended, so you will need to engourage your client to re-hire you if you want to continue. Upon re-hiring, you can continue services and new payment will be generated once you complete the next cycle of services.", ET_DOMAIN); ?></p>
                                             <?php else: ?>
                                             <p><?php _e('Once you have completed the service, click "Completed" below after  which a payment from your client be generated and your client will be informed to wait for results', ET_DOMAIN); ?></p>
                                             <button class="btn-submit btn-work-complete-css btn-work-complete-action"><?php _e('Work Complete', ET_DOMAIN); ?></button>
                                             <?php endif; ?>
                                         <?php else:?>
                                             <?php if( $current->post_status == 'processing' ): ?>
                                             <p><?php _e("Good news! The cancellation period has expired and the services will begin shortly, if they haven't begun already. Once the correspondence is prepared, you will be notified ", ET_DOMAIN); ?></p>
                                             <?php elseif( $current->post_status == 'verification'): ?>
                                             <p><?php _e("Good news! The service is complete, your payment is due and you are not waiting for result. Please forward all correspondence you receive from any creditor or credit bureau so that results can be verified.", ET_DOMAIN); ?></p>
                                             <?php elseif($current->post_status == 'finished' || $current->post_status == 'delivery'): ?>
                                                 <p><?php _e("Your company completed the work and the results are reported in the message area. No further work will be performed, unless you would like to re-hire the company to continue.", ET_DOMAIN); ?></p>
                                             <?php if( $current->can_review): ?>
                                                <button data-id="<?php echo $current->ID; ?>" class="btn-submit btn-continue-service-css  margin-top-20 order-action"  value="finished"><?php _e('CONTINUE SERVICES', ET_DOMAIN); ?></button>
                                             <?php else: ?>
                                                 <button data-id="<?php echo $current->ID; ?>" class="btn-submit btn-continue-service-css  btn-continue-service-btn margin-top-20" ><?php _e('CONTINUE SERVICES', ET_DOMAIN); ?></button>
                                             <?php endif; ?>
                                             <?php else: ?>
                                             <p><?php _e('Pursuant to Federal and State law, your company cannot begin credit repair services until the 72 hours cancellation period has ended. Which began at the moment you signed your agreement with the company', ET_DOMAIN); ?></p>
                                             <?php endif; ?>
                                         <?php endif ?>
                                         <?php
                                            if( $current->post_status  != 'verification' && $current->post_status != 'finished' ){
                                                $current->status_text == __('PENDING', ET_DOMAIN);
                                                $current->status_class == 'pending-color';
                                            }
                                         ?>
                                         <div class="label-status label-status-order <?php echo $current->status_class; ?>">
                                             <span><?php echo $current->status_text; ?></span>
                                         </div>
                                <?php else: ?>
                                    <p><?php _e('Pursuant to Federal and State law, your company cannot begin credit repair services until the 72 hours cancellation period has ended. Which began at the moment you signed your agreement with the company', ET_DOMAIN); ?></p>
                                     <div class="label-status label-status-order pending-color">
                                         <span><?php _e('PENDING', ET_DOMAIN); ?></span>
                                     </div>
                                <?php      endif; ?>
                            </div>
                            <div class="total-order">
                                <p><i class="fa fa-exclamation-circle" aria-hidden="true"></i><?php _e(' Here are the details for your order and company hired', ET_DOMAIN); ?></p>
                            </div>
                        </div>
                </div>
                <div class="box-aside box-aside2">
                    <div class="tabs-information">
                        <ul class="nav nav-tabs requirement-tabs" role="tablist">
                            <li role="presentation" class="active requirement-list-li"><a href="#requirement" aria-controls="requirement" role="tab" data-toggle="tab" class="left-tab-requirement"><?php _e('Requirements', ET_DOMAIN); ?></a></li>
                            <li role="presentation" class="requirement-list-li"><a href="#document" aria-controls="document" role="tab" data-toggle="tab"><?php _e('Documents', ET_DOMAIN); ?></a></li>
                        </ul>
                        <div class="tab-content">
                            <div role="tabpanel" class="tab-pane active order-detail-price" id="requirement">
                                <div class="requirment-tab-content">
                                    <?php $terms = get_the_terms($current->post_parent, 'mjob_requirement');
                                    if( !empty($terms) && !is_wp_error($terms) ):
                                        $user_role = ae_user_role($user_ID);
                                        if( $user_role == INDIVIDUAL):
                                            $cl1 = 'requirement-item';
                                        elseif( $user_role == COMPANY):
                                            $cl1 = 'need-uploads';
                                        endif;
                                        ?>
                                        <ul class="requirement-list">
                                            <?php foreach( $terms as $term):
                                                $f = false;
                                                $term = $obj_tax->convert($term);
                                                if( $term->click_type == 'open-contact-info' || $term->click_type == 'open-billing-info'):
                                                    $f = true;
                                                endif;
                                                if( $f ):
                                                    if( empty($current->need_uploads) || !in_array( $term->slug, (array)$current->need_uploads )):
                                                        $icon = '<i class="fa fa-check-square-o" aria-hidden="true"></i>';
                                                        $com = '   <a data-type="'.$term->check_type.'" data-id="'.$term->slug.'" data-name="'.$term->name.'" href="#" class="resend-requirement-style resend-requirement" title="'.__('unlock', ET_DOMAIN).'"><i class="fa fa-refresh" aria-hidden="true"></i></a>';
                                                        $class = 'disabled';
                                                    else:
                                                        $com = '';
                                                        $icon = '<i class="fa fa-square-o" aria-hidden="true"></i>';
                                                        $class = '';
                                                    endif;
                                                else:
                                                    if( in_array($term->slug, (array)$current->uploaded) ):
                                                        $icon = '<i class="fa fa-check-square-o" aria-hidden="true"></i>';
                                                        $com = '   <a data-type="'.$term->check_type.'" data-id="'.$term->slug.'" data-name="'.$term->name.'" href="#" class="resend-requirement-style resend-requirement" title="'.__('unlock', ET_DOMAIN).'"><i class="fa fa-refresh" aria-hidden="true"></i></a>';
                                                        $class = 'disabled';
                                                    else:
                                                        $com = '';
                                                        $icon = '<i class="fa fa-square-o" aria-hidden="true"></i>';
                                                        $class = '';
                                                    endif;
                                                endif;
                                                ?>
                                                <li>
                                                    <a href="#" data-type="<?php echo $term->click_type; ?>" class="<?php echo $cl1.' ';?> <?php echo $class; ?>" data-id="<?php echo $term->slug; ?>" data-name="<?php echo $term->name; ?>"><?php echo $icon; ?>  <?php echo ' '.$term->name ?></a>
                                                    <?php if( ae_user_role($user_ID) == COMPANY):
                                                        echo  ''.$com;
                                                    endif; ?>
                                                </li>
                                            <?php endforeach; ?>
                                        </ul>
                                    <?php endif; ?>
                                </div>
                                <div class="total-order">
                                    <p><i class="fa fa-exclamation-circle" aria-hidden="true"></i><?php _e(' These tasks must be completed by you. Without completing these tasks, your company cannot perform the tasks for which you hired them.', ET_DOMAIN); ?></p>
                                </div>
                                <input type="hidden" class="input-item" name="_wpnonce" id="profile_wpnonce" value="<?php echo de_create_nonce('ae-mjob_post-sync');?>" />
                            </div>
                            <div role="tabpanel" class="tab-pane " id="document">
                                <div id="incomingPaymentsForm">
                                    <ul class="requirement-list document-list">
                                        <?php
                                        if( !empty($current->requirement_files)): ?>

                                        <?php     foreach( $current->requirement_files as $key=> $files):
                                                    $term = get_term_by('slug', $key, 'mjob_requirement');
                                                    global $ae_tax_factory;
                                                    $term_obj = $ae_tax_factory->get('mjob_requirement');
                                                    $term = $term_obj->convert($term);
                                                    if(!empty($files)):
                                                        $i = 0;
                                                        $tx = '';
                                                        foreach($files as $file):
                                                            $f = get_post($file);
                                                            if( $i > 0):
                                                                $tx = '_'.$i;
                                                            endif;
                                                            ?>
                                                <li class="col-lg-6 col-md-6 col-xs-12 item-requirement">
                                                    <a  href="<?php echo et_get_page_link('simple-download').'?id='.$f->ID ?>" data-name="<?php echo $term->name.$tx.' : '.date('d/m/Y', strtotime($f->post_date))?>" class="show-requirement-docs">
                                                        <div class="doc-icon"> <i class="fa fa-file-pdf-o" aria-hidden="true"></i></div>
                                                        <div class="doc-name"><?php echo $term->requirement_short_name.$tx?></div>
                                                        <div class="doc-time"><?php echo date('d/m/Y', strtotime($f->post_date))?></div>

                                                    </a></li>
                                        <?php $i++;
                                                        endforeach;
                                                    endif;
                                                        endforeach;?>
                                        <?php
                                        endif; ?>
                                    </ul>
                                </div>
                            </div>
                        </div>

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

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
$current_save = (array)$current;
unset($current_save['ae_message']);
unset($current_save['order_delivery']);
$current_save = (object)$current_save;
echo '<script type="text/template" id="order_single_data" >'.json_encode($current_save).'</script>';
?>
<div id="content" class="mjob-single-order-page mjob_conversation_detail_page">
    <div class="block-page">
        <div class="container">
            <div class="row title-top-pages dashboard withdraw no-margin">
                <div class="box-shadow-title">
                    <p class="block-title">
                        <?php _e('ORDER DETAILS', ET_DOMAIN); ?>
                    </p>
                    <?php if( $user_role == COMPANY ):
                        $cls = 'company-side';
                        ?>
                        <p class="btn-back"><?php _e('Communicate with your client below.', ET_DOMAIN); ?></p>
                    <?php else: $cls = 'client-side'; ?>
                    <p class="btn-back"><?php _e('Communicate with your company below.', ET_DOMAIN); ?></p>
                    <?php endif; ?>
                </div>
            </div>
            <div class="row no-margin">
                <div class="col-lg-7 col-md-7 col-sm-12 col-xs-12 conversation-form ">
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

                        echo '<div class="wrapper-list-conversation new-styles-wrapper-list-conversation"><ul class="list-conversation">';
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
                                        <?php _e("Thank you for hiring and trusting us! Under the law, there is a 72 hour waiting period before we can begin work. Once that expires, we will begin. In the meantime, if you have any questions, comments or concerns, message us here. Also, this is a perfect time for you to get all your documents together (if you haven't done so already). <br><br><strong>NOTE: Under the \"REQUIREMENTS\" tab, you will see a list of documents we require. Just click on each one to upload the relevant documents. Thanks!</strong>", ET_DOMAIN);?>
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
                <div class="col-lg-5 col-md-5 col-sm-12 col-xs-12 current block-items-detail profile">
                    <div class="box-aside margin-bottom-100">
                        <div class="order-detail-price">
                            <?php if( $user_role == INDIVIDUAL): ?>
                            <div class="tabs-information">
                                <ul class="nav nav-tabs requirement-tabs" role="tablist">
                                    <li role="presentation" class="active requirement-list-li"><a href="#my_company" aria-controls="requirement" role="tab" data-toggle="tab" class="left-tab-requirement"><?php _e('MY COMPANY', ET_DOMAIN); ?></a></li>
                                    <li role="presentation" class="requirement-list-li requirement-task">
                                        <a href="#requirement" aria-controls="requirement" role="tab" data-toggle="tab" class="left-tab-requirement">
                                            <?php _e('MY TASKS ', ET_DOMAIN); ?><i class="fa fa-exclamation-circle" aria-hidden="true"></i></a>
                                    </li>
                                    <li role="presentation" class="requirement-list-li"><a href="#document" aria-controls="document" role="tab" data-toggle="tab"><?php _e('MY DOCUMENTS', ET_DOMAIN); ?></a></li>
                                </ul>
                                <div class="tab-content">
                                    <div role="tabpanel" class="tab-pane  active " id="my_company">
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
                                                    <div class="" data-edit="user" data-id="" data-name="display_name" data-type="input"><?php echo $profile->initial_display_name; ?></div>
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
                                        if( !empty($current->rehire_time) ):
                                            $t1 = $current->rehire_time;
                                        else:
                                            $t1 = get_the_time('U', $current->ID);
                                        endif;
                                        $t2 = time();
                                        $t = $t2 - $t1;
                                        if( $t >= 259200 ):
                                            if( $current->post_status == 'publish' ) {
                                                mJobOrderAction()->updateOrderStatus($current->ID, 'processing');
                                            }
                                            ?>
                                            <?php if( $user_role == COMPANY): ?>
                                            <?php if( $current->post_status == 'processing' ): ?>
                                                <p><?php _e('Once you have completed the service, click "Completed" below after  which a payment from your client be generated and your client will be informed to wait for results', ET_DOMAIN); ?></p>
                                                <p class="mjob_order_btn"><button class="btn-submit btn-work-complete-css btn-work-complete-action" data-content="<?php echo $current->mjob_category_modal_content; ?>"><?php _e('Work Complete', ET_DOMAIN); ?></button></p>
                                            <?php elseif( $current->post_status == 'verification'): ?>
                                                <p><?php _e("It is important that you update your client with results as soon as you can. Once results are shown, your client can review your company's performanceas well as rehire you", ET_DOMAIN); ?></p>
                                                <p class="mjob_order_btn"><button data-id="<?php echo $current->ID; ?>" class="btn-submit btn-work-complete-css btn-delivery order-delivery-btn"><?php _e('SUBMIT RESULTS', ET_DOMAIN); ?></button></p>
                                            <?php elseif($current->post_status == 'finished' || $current->post_status == 'delivery'): ?>
                                                <p><?php _e("This job has ended, so you will need to engourage your client to re-hire you if you want to continue. Upon re-hiring, you can continue services and new payment will be generated once you complete the next cycle of services.", ET_DOMAIN); ?></p>
                                            <?php else: ?>
                                                <p><?php _e('Once you have completed the service, click "Completed" below after  which a payment from your client be generated and your client will be informed to wait for results', ET_DOMAIN); ?></p>
                                                <p class="mjob_order_btn"><button class="btn-submit btn-work-complete-css btn-work-complete-action" data-content="<?php echo $current->mjob_category_modal_content; ?>" ><?php _e('Work Complete', ET_DOMAIN); ?></button></p>
                                            <?php endif; ?>
                                        <?php else:?>
                                            <?php if( $current->post_status == 'processing' ): ?>
                                                <p><?php _e("Good news! The cancellation period has expired and the services will begin shortly, if they haven't begun already. When the status changes, you will be notified.", ET_DOMAIN); ?></p>
                                            <?php elseif( $current->post_status == 'verification'): ?>
                                                <?php if( isset($current->mjob_category_verification_content) && !empty($current->mjob_category_verification_content)): ?>
                                                    <p><?php
                                                        $current->mjob_category_verification_content = str_ireplace('[date]', $current->work_complete_date, $current->mjob_category_verification_content);
                                                        echo $current->mjob_category_verification_content;
                                                        ?></p>
                                                <?php else: ?>
                                                    <p><?php _e("Good news! The service is complete. Your payment is due, and you are now waiting for results. Please communicate and cooperate with your company so that you can verify the results of your company's performance.", ET_DOMAIN); ?></p>
                                                <?php endif; ?>
                                            <?php elseif($current->post_status == 'finished' || $current->post_status == 'delivery'): ?>
                                                <p><?php _e("Your company completed the work and the results are reported in the message area. No further work will be performed, unless you would like to re-hire the company to continue.", ET_DOMAIN); ?></p>
                                                <?php if( $current->can_review): ?>
                                                    <p class="mjob_order_btn"><button data-id="<?php echo $current->ID; ?>" class="btn-submit btn-continue-service-css  margin-top-20 order-action"  value="finished"><?php _e('CONTINUE SERVICES', ET_DOMAIN); ?></button></p>
                                                <?php else: ?>
                                                    <p class="mjob_order_btn"><button data-id="<?php echo $current->ID; ?>" class="btn-submit btn-continue-service-css  btn-continue-service-btn margin-top-20" ><?php _e('CONTINUE SERVICES', ET_DOMAIN); ?></button></p>
                                                <?php endif; ?>
                                            <?php else: ?>
                                                <p><?php _e('Pursuant to Federal and State law, your company cannot begin credit related services until the 72 hours cancellation period has ended, which began at the moment you signed your agreement with the company.', ET_DOMAIN); ?></p>
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
                                    <div role="tabpanel" class="tab-pane  order-detail-price" id="requirement">
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
                                                    <?php
                                                    $q = false;
                                                    foreach( $terms as $term):
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
                                                                $q = true;
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
                                                                $q  = true;
                                                            endif;
                                                        endif;
                                                        ?>
                                                        <?php if( $term->term_id != 43 ): ?>
                                                        <li>
                                                            <a data-modal-name="<?php echo $term->requirement_modal_name ?>" data-checkbox-name="<?php echo $term->requirement_checkbox_name ?>" href="#" data-type="<?php echo $term->click_type; ?>" class="<?php echo $cl1.' ';?> <?php echo $class; ?>" data-id="<?php echo $term->slug; ?>" data-name="<?php echo $term->name; ?>"><?php echo $icon; ?>  <?php echo ' '.$term->name ?></a>
                                                            <?php if( ae_user_role($user_ID) == COMPANY):
                                                                echo  ''.$com;
                                                            endif; ?>
                                                        </li>
                                                    <?php endif; ?>
                                                    <?php endforeach; ?>
                                                </ul>
                                            <?php endif; ?>
                                            <input type="hidden" value="<?php echo $q; ?>" id="noti-show" />
                                        </div>
                                        <div class="total-order total-order-1">
                                            <p><i class="fa fa-exclamation-circle" aria-hidden="true"></i><?php _e(' These tasks must be completed by you. Without completing these tasks, your company cannot perform the tasks for which you hired them.', ET_DOMAIN); ?></p>
                                        </div>
                                        <input type="hidden" class="input-item" name="_wpnonce" id="profile_wpnonce" value="<?php echo de_create_nonce('ae-mjob_post-sync');?>" />
                                    </div>
                                    <div role="tabpanel" class="tab-pane " id="document">
                                        <div id="incomingPaymentsForm">
                                            <ul class="requirement-list document-list desktop-list">
                                                <?php if( isset($current->agreement_files) && !empty($current->agreement_files)):
                                                    foreach($current->agreement_files as $item): ?>
                                                        <li class="col-lg-6 col-md-6 col-xs-6 item-requirement">
                                                            <a  data-agreement="1" data-id="<?php echo $current->ID; ?>" href="#" data-name="<?php echo $item['name']?>"  class="show-requirement-doc">
                                                                <div class="doc-icon"> <i class="fa fa-file-pdf-o" aria-hidden="true"></i></div>
                                                                <div class="doc-name"><?php echo $item['name'] ?></div>
                                                                <div class="doc-time"><?php echo date('d/m/Y', strtotime($current->post_date))?></div>

                                                            </a></li>

                                                    <?php endforeach; endif; ?>
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
                                                                <li class="col-lg-6 col-md-6 col-xs-6 item-requirement">
                                                                    <?php if( $f->post_mime_type == 'application/msword'): ?>
                                                                        <a  href="<?php echo et_get_page_link('simple-download').'?cid='.$current->ID.'&n='.$item['name'] ?>" data-name="<?php echo $item['name'].' : '.date('d/m/Y', strtotime($current->post_date))?>" class="show-requirement-docs">
                                                                            <div class="doc-icon"> <i class="fa fa-file-pdf-o" aria-hidden="true"></i></div>
                                                                            <div class="doc-name"><?php echo $item['name'] ?></div>
                                                                            <div class="doc-time"><?php echo date('d/m/Y', strtotime($current->post_date))?></div>

                                                                        </a>
                                                                    <?php elseif($f->post_mime_type == 'aplication/pdf'): ?>
                                                                        <a  data-mime-type="<?php $f->post_mime_type; ?>" href="#" data-type="<?php echo $term->click_type; ?>" data-slug="<?php echo $term->slug; ?>"  data-id="<?php echo $f->ID; ?>"  data-name="<?php echo $term->name; ?>" class="show-requirement-doc">
                                                                            <div class="doc-icon"> <i class="fa fa-file-pdf-o" aria-hidden="true"></i></div>
                                                                            <div class="doc-name"><?php echo $term->requirement_short_name.$tx?></div>
                                                                            <div class="doc-time"><?php echo date('d/m/Y', strtotime($f->post_date))?></div>

                                                                        </a>
                                                                    <?php else:
                                                                        if( !$term ):
                                                                            $file_name = $f->post_title;
                                                                        else:
                                                                            $file_name = $term->requirement_short_name.$tx;
                                                                        endif;
                                                                        ?>
                                                                        <a  data-mime-type="<?php echo $f->post_mime_type; ?>" href="#" data-type="<?php echo $term->click_type; ?>" data-slug="<?php echo $term->slug; ?>"  data-id="<?php echo $f->ID; ?>"  data-name="<?php echo $term->name; ?>" class="show-requirement-doc">
                                                                            <div class="doc-icon"> <i class="fa fa-file-pdf-o" aria-hidden="true"></i></div>
                                                                            <div class="doc-name"><?php echo $file_name ?></div>
                                                                            <div class="doc-time"><?php echo date('d/m/Y', strtotime($f->post_date))?></div>

                                                                        </a>
                                                                    <?php endif; ?>
                                                                </li>
                                                                <?php $i++;
                                                            endforeach;
                                                        endif;
                                                    endforeach;?>
                                                    <?php
                                                endif; ?>
                                            </ul>
                                            <ul class="requirement-list document-list mobile-list">
                                                <?php if( isset($current->agreement_files) && !empty($current->agreement_files)):
                                                    foreach($current->agreement_files as $item): ?>
                                                        <li class="col-lg-6 col-md-6 col-xs-6 item-requirement">
                                                            <a  href="<?php echo et_get_page_link('simple-download').'?cid='.$current->ID.'&n='.$item['name'] ?>" data-name="<?php echo $item['name'].' : '.date('d/m/Y', strtotime($current->post_date))?>" class="show-requirement-docs">
                                                                <div class="doc-icon"> <i class="fa fa-file-pdf-o" aria-hidden="true"></i></div>
                                                                <div class="doc-name"><?php echo $item['name'] ?></div>
                                                                <div class="doc-time"><?php echo date('d/m/Y', strtotime($current->post_date))?></div>

                                                            </a></li>

                                                    <?php endforeach; endif; ?>
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
                                                                <li class="col-lg-6 col-md-6 col-xs-6 item-requirement">
                                                                    <?php  if( !$term ):
                                                                        $file_name = $f->post_title;
                                                                    else:
                                                                        $file_name = $term->requirement_short_name.$tx;
                                                                    endif; ?>
                                                                    <a  href="<?php echo et_get_page_link('simple-download').'?id='.$f->ID ?>" data-name="<?php echo $term->name.$tx.' : '.date('d/m/Y', strtotime($f->post_date))?>" class="show-requirement-docs">
                                                                        <div class="doc-icon"> <i class="fa fa-file-pdf-o" aria-hidden="true"></i></div>
                                                                        <div class="doc-name"><?php echo $file_name; ?></div>
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
                            <?php else: ?>
                                <div class="tabs-information">
                                    <ul class="nav nav-tabs requirement-tabs" role="tablist">
                                        <li role="presentation" class="active requirement-list-li"><a href="#my_company" aria-controls="requirement" role="tab" data-toggle="tab" class="left-tab-requirement"><?php _e('CLIENT', ET_DOMAIN); ?></a></li>
                                        <li role="presentation" class="requirement-list-li requirement-task">
                                            <a href="#requirement" aria-controls="requirement" role="tab" data-toggle="tab" class="left-tab-requirement">
                                                <?php _e('DOCUMENTS ', ET_DOMAIN); ?></a>
                                        </li>
                                        <li role="presentation" class="requirement-list-li"><a href="#document" aria-controls="document" role="tab" data-toggle="tab"><?php _e('PAYMENTS', ET_DOMAIN); ?></a></li>
                                    </ul>
                                    <div class="tab-content">
                                        <div role="tabpanel" class="tab-pane  active " id="my_company">
                                            <div class="personal-profile order-detail-profile">
                                                <div class="float-center profile-avatar">
                                                    <div class="">
                                                        <a href="#" class="">
                                                            <?php
                                                            echo mJobAvatar($current->post_author, 75);
                                                            ?>
                                                        </a>
                                                    </div>
                                                </div>
                                                <h4 class="float-center">
                                                    <div id="display_name">
                                                        <div class="" data-edit="user" data-id="" data-name="display_name" data-type="input"><?php echo $profile->initial_display_name; ?></div>
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
                                                <div class=" mjob-client-more-info">
                                                    <p>
                                                        <span class="mjob-order-left-info"><?php _e('Phone', ET_DOMAIN); ?></span>
                                                        <span class="mjob-order-right-info"><?php echo sprintf(__('%s', ET_DOMAIN), $profile->phone); ?></span>
                                                    </p>
                                                    <p>
                                                        <span class="mjob-order-left-info"><?php _e('Address', ET_DOMAIN); ?></span>
                                                        <span class="mjob-order-right-info"><?php echo sprintf(__('%s', ET_DOMAIN), $profile->billing_full_address); ?></span>
                                                    </p>
                                                    <p>
                                                        <span class="mjob-order-left-info"><?php _e('Email', ET_DOMAIN); ?></span>
                                                        <span class="mjob-order-right-info"><?php echo sprintf(__('%s', ET_DOMAIN), $profile->business_email); ?></span>
                                                    </p>
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
                                                if( !empty($current->rehire_time) ):
                                                    $t1 = $current->rehire_time;
                                                else:
                                                    $t1 = get_the_time('U', $current->ID);
                                                endif;
                                                $t2 = time();
                                                $t = $t2 - $t1;
                                                if( $t >= 600 ):
                                                    if( $current->post_status == 'publish' ) {
                                                        mJobOrderAction()->updateOrderStatus($current->ID, 'processing');
                                                    }
                                                    ?>
                                                    <?php if( $user_role == COMPANY): ?>
                                                    <?php if( $current->post_status == 'processing' ): ?>
                                                        <p><?php _e('Once you have completed the service, click "Work Complete." This will generate a payment from your client and your client will be informed. Please keep your client informed so they understand the status change.', ET_DOMAIN); ?></p>
                                                        <p class="mjob_order_btn"><button class="btn-submit btn-work-complete-css btn-work-complete-action" data-content="<?php echo $current->mjob_category_modal_content; ?>"><?php _e('Work Complete', ET_DOMAIN); ?></button></p>
                                                    <?php elseif( $current->post_status == 'verification'): ?>
                                                        <p><?php _e("It is important, especially in this stage, that you update your client with results as soon as you can. Once results are shown, your client can review your company and rehire you.", ET_DOMAIN); ?></p>
                                                        <p class="mjob_order_btn"><button data-id="<?php echo $current->ID; ?>" class="btn-submit btn-work-complete-css btn-delivery order-delivery-btn"><?php _e('SUBMIT RESULTS', ET_DOMAIN); ?></button></p>
                                                    <?php elseif($current->post_status == 'finished' || $current->post_status == 'delivery'): ?>
                                                        <p><?php _e("This job has ended, so you will need to engourage your client to re-hire you if you want to continue. Upon re-hiring, you can continue services and new payment will be generated once you complete the next cycle of services.", ET_DOMAIN); ?></p>
                                                    <?php else: ?>
                                                        <p><?php _e('Once you have completed the service, click "Work Complete." This will generate a payment from your client and your client will be informed. Please keep your client informed so they understand the status change.', ET_DOMAIN); ?></p>
                                                        <p class="mjob_order_btn"><button class="btn-submit btn-work-complete-css btn-work-complete-action" data-content="<?php echo $current->mjob_category_modal_content; ?>" ><?php _e('Work Complete', ET_DOMAIN); ?></button></p>
                                                    <?php endif; ?>
                                                <?php else:?>
                                                    <?php if( $current->post_status == 'processing' ): ?>
                                                        <p><?php _e("Good news! The cancellation period has expired and the services will begin shortly, if they haven't begun already. Once the correspondence is prepared, you will be notified ", ET_DOMAIN); ?></p>
                                                    <?php elseif( $current->post_status == 'verification'): ?>
                                                        <p><?php _e("Good news! The service is complete, your payment is due and you are not waiting for result. Please forward all correspondence you receive from any creditor or credit bureau so that results can be verified.", ET_DOMAIN); ?></p>
                                                    <?php elseif($current->post_status == 'finished' || $current->post_status == 'delivery'): ?>
                                                        <p><?php _e("Your company completed the work and the results are reported in the message area. No further work will be performed, unless you would like to re-hire the company to continue.", ET_DOMAIN); ?></p>
                                                        <?php if( $current->can_review): ?>
                                                            <p class="mjob_order_btn"><button data-id="<?php echo $current->ID; ?>" class="btn-submit btn-continue-service-css  margin-top-20 order-action"  value="finished"><?php _e('CONTINUE SERVICES', ET_DOMAIN); ?></button></p>
                                                        <?php else: ?>
                                                            <p class="mjob_order_btn"><button data-id="<?php echo $current->ID; ?>" class="btn-submit btn-continue-service-css  btn-continue-service-btn margin-top-20" ><?php _e('CONTINUE SERVICES', ET_DOMAIN); ?></button></p>
                                                        <?php endif; ?>
                                                    <?php else: ?>
                                                        <p><?php _e('The 72-hour cancellation period required by the Credit Repair Organizations Act is pending; no service may be performed until it has expired. You will be notified when you may begin.', ET_DOMAIN); ?></p>
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
                                                    <p><?php _e('The 72-hour cancellation period required by the Credit Repair Organizations Act is pending; no service may be performed until it has expired. You will be notified when you may begin.', ET_DOMAIN); ?></p>
                                                    <div class="label-status label-status-order pending-color">
                                                        <span><?php _e('PENDING', ET_DOMAIN); ?></span>
                                                    </div>
                                                <?php      endif; ?>
                                            </div>
                                            <div class="total-order">
                                                <p><i class="fa fa-exclamation-circle" aria-hidden="true"></i><?php _e(' Here are the details for your order and the client who hired you.', ET_DOMAIN); ?></p>
                                            </div>
                                        </div>
                                        <div role="tabpanel" class="tab-pane  order-detail-price" id="requirement">
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
                                                    <ul class="requirement-list document-list desktop-list">
                                                        <?php if( isset($current->agreement_files) && !empty($current->agreement_files)):
                                                            foreach($current->agreement_files as $item): ?>
                                                                <li class="col-lg-6 col-md-6 col-xs-6 item-requirement">
                                                                    <a data-agreement="1" data-id="<?php echo $current->ID; ?>" href="#" data-name="<?php echo $item['name']?>" class="show-requirement-doc">
                                                                        <div class="doc-icon"> <i class="fa fa-file-pdf-o" aria-hidden="true"></i></div>
                                                                        <div class="doc-name"><?php echo $item['name'] ?></div>
                                                                        <div class="doc-time"><?php echo date('d/m/Y', strtotime($current->post_date))?></div>

                                                                    </a></li>

                                                            <?php endforeach; endif;
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
                                                                        <li class="col-lg-6 col-md-6 col-xs-6 item-requirement">
                                                                            <?php if( $f->post_mime_type == 'application/msword'): ?>
                                                                                <a  href="<?php echo et_get_page_link('simple-download').'?cid='.$current->ID.'&n='.$item['name'] ?>" data-name="<?php echo $item['name'].' : '.date('d/m/Y', strtotime($current->post_date))?>" class="show-requirement-docs">
                                                                                    <div class="doc-icon"> <i class="fa fa-file-pdf-o" aria-hidden="true"></i></div>
                                                                                    <div class="doc-name"><?php echo $item['name'] ?></div>
                                                                                    <div class="doc-time"><?php echo date('d/m/Y', strtotime($current->post_date))?></div>

                                                                                </a>
                                                                            <?php elseif($f->post_mime_type == 'aplication/pdf'): ?>
                                                                                <a  data-mime-type="<?php $f->post_mime_type; ?>" href="#" data-type="<?php echo $term->click_type; ?>" data-slug="<?php echo $term->slug; ?>"  data-id="<?php echo $f->ID; ?>"  data-name="<?php echo $term->name; ?>" class="show-requirement-doc">
                                                                                    <div class="doc-icon"> <i class="fa fa-file-pdf-o" aria-hidden="true"></i></div>
                                                                                    <div class="doc-name"><?php echo $term->requirement_short_name.$tx?></div>
                                                                                    <div class="doc-time"><?php echo date('d/m/Y', strtotime($f->post_date))?></div>

                                                                                </a>
                                                                            <?php else:
                                                                                    if( !$term ):
                                                                                        $file_name = $f->post_title;
                                                                                    else:
                                                                                        $file_name = $term->requirement_short_name.$tx;
                                                                                    endif;
                                                                                ?>
                                                                                <a  data-mime-type="<?php echo $f->post_mime_type; ?>" href="#" data-type="<?php echo $term->click_type; ?>" data-slug="<?php echo $term->slug; ?>"  data-id="<?php echo $f->ID; ?>"  data-name="<?php echo $term->name; ?>" class="show-requirement-doc">
                                                                                    <div class="doc-icon"> <i class="fa fa-file-pdf-o" aria-hidden="true"></i></div>
                                                                                    <div class="doc-name"><?php echo $file_name ?></div>
                                                                                    <div class="doc-time"><?php echo date('d/m/Y', strtotime($f->post_date))?></div>

                                                                                </a>
                                                                            <?php endif; ?>
                                                                            </li>
                                                                        <?php $i++;
                                                                    endforeach;
                                                                endif;
                                                            endforeach;?>
                                                            <?php
                                                        endif; ?>
                                                        <?php
//                                                        if( !$k ):
//                                                            _e('Client has not uploaded a document', ET_DOMAIN);
//                                                         endif; ?>
                                                    </ul>
                                                    <ul class="requirement-list document-list mobile-list">
                                                        <?php if( isset($current->agreement_files) && !empty($current->agreement_files)):
                                                            foreach($current->agreement_files as $item):?>
                                                                <li class="col-lg-6 col-md-6 col-xs-6 item-requirement">
                                                                    <a  href="<?php echo et_get_page_link('simple-download').'?cid='.$current->ID.'&n='.$item['name'] ?>" data-name="<?php echo $item['name'].' : '.date('d/m/Y', strtotime($current->post_date))?>" class="show-requirement-docs">
                                                                        <div class="doc-icon"> <i class="fa fa-file-pdf-o" aria-hidden="true"></i></div>
                                                                        <div class="doc-name"><?php echo $item['name'] ?></div>
                                                                        <div class="doc-time"><?php echo date('d/m/Y', strtotime($current->post_date))?></div>

                                                                    </a></li>

                                                            <?php endforeach; endif; ?>
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
                                                                        <li class="col-lg-6 col-md-6 col-xs-6 item-requirement">
                                                                            <?php  if( !$term ):
                                                                                        $file_name = $f->post_title;
                                                                                    else:
                                                                                        $file_name = $term->requirement_short_name.$tx;
                                                                                    endif; ?>
                                                                            <a  href="<?php echo et_get_page_link('simple-download').'?id='.$f->ID ?>" data-name="<?php echo $term->name.$tx.' : '.date('d/m/Y', strtotime($f->post_date))?>" class="show-requirement-docs">
                                                                                <div class="doc-icon"> <i class="fa fa-file-pdf-o" aria-hidden="true"></i></div>
                                                                                <div class="doc-name"><?php echo $file_name; ?></div>
                                                                                <div class="doc-time"><?php echo date('d/m/Y', strtotime($f->post_date))?></div>

                                                                            </a></li>
                                                                        <?php $i++;
                                                                    endforeach;
                                                                endif;
                                                            endforeach;?>
                                                            <?php
                                                        endif; ?>
                                                    </ul>
                                                <?php endif; ?>
                                                <input type="hidden" value="<?php echo $q; ?>" id="noti-show" />
                                            </div>
                                            <div class="total-order total-order-1">
                                                <p><i class="fa fa-exclamation-circle" aria-hidden="true"></i><?php _e(' Here are the documents uploaded by your client. Click to view, download or request a new one if necessary.', ET_DOMAIN); ?></p>
                                            </div>
                                            <input type="hidden" class="input-item" name="_wpnonce" id="profile_wpnonce" value="<?php echo de_create_nonce('ae-mjob_post-sync');?>" />
                                        </div>
                                        <div role="tabpanel" class="tab-pane " id="document">
                                            <div id="incomingPaymentsForm">
                                                <?php
                                                    $args = array(
                                                        'post_type'=> 'payment_history',
                                                        'post_parent'=>$current->ID,
                                                        'post_author'=> $user_ID,
                                                        'post_status'=> array('pending', 'publish')
                                                    );
                                                    $posts = get_posts($args);
                                                ?>
                                                <ul class="requirement-list document-list desktop-list">
                                                    <?php if( !empty($posts)): ?>
                                                    <?php foreach($posts as $p ): ?>
                                                        <li class="col-lg-6 col-md-6 col-xs-6 item-requirement">
                                                            <a data-payment="1"  href="#" data-name="<?php echo $p->post_title; ?>" data-id="<?php echo $p->ID; ?>" class="show-requirement-doc">
                                                                <div class="doc-icon"> <i class="fa fa-file-pdf-o" aria-hidden="true"></i></div>
                                                                <div class="doc-name"><?php echo $p->post_title; ?></div>
                                                                <div class="doc-time"><?php echo date('d/m/Y', strtotime($current->post_date))?></div>

                                                            </a></li>
                                                    <?php endforeach; ?>
                                                    <?php else:
                                                        _e('<p class="padding-left-20 padding-right-20">No payment has been generated. Once you complete the work and a payment is generated, it will appear here</p>', ET_DOMAIN);
                                                    endif; ?>
                                                </ul>
                                                <ul class="requirement-list document-list mobile-list">
                                                    <?php if( !empty($posts)): ?>
                                                        <?php foreach($posts as $p ): ?>
                                                            <li class="col-lg-6 col-md-6 col-xs-6 item-requirement">
                                                                <a data-payment="1"  href="<?php echo et_get_page_link('simple-download').'?pid='.$p->ID;?>" data-name="<?php echo $p->post_title; ?>" data-id="<?php echo $p->ID; ?>" class="show-requirement-docs">
                                                                    <div class="doc-icon"> <i class="fa fa-file-pdf-o" aria-hidden="true"></i></div>
                                                                    <div class="doc-name"><?php echo $p->post_title; ?></div>
                                                                    <div class="doc-time"><?php echo date('d/m/Y', strtotime($current->post_date))?></div>

                                                                </a></li>
                                                        <?php endforeach; ?>
                                                    <?php else:
                                                        _e('<p class="padding-left-20">No payment has been generated. Once you complete the work and a payment is generated, it will appear here</p>', ET_DOMAIN);
                                                    endif; ?>
                                                </ul>
                                            </div>
                                            <div class="total-order total-order-1">
                                                <p><i class="fa fa-exclamation-circle" aria-hidden="true"></i><?php _e(' All payments form your client will appear here. You can click to view and download if payments have been generated.', ET_DOMAIN); ?></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
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

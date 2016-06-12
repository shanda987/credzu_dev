<?php
global $ae_post_factory, $current;
$order_obj = $ae_post_factory->get('mjob_order');
$current = $order_obj->convert($current);
$profile = mJobProfileAction()->getProfile($current->mjob_author);
?>
<div class="modal fade" id="reorder_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true"><img
                            src="<?php echo get_template_directory_uri() ?>/assets/img/icon-close.png" alt=""></span></button>
                <h4 class="modal-title" id="myModalLabelForgot"><?php _e('Continue Services', ET_DOMAIN); ?></h4>
            </div>
            <div class="modal-body delivery-order block-items-detail">
                <div class="unlock-requirement-modal">
                    <div class="form-group clearfix">
                        <div >
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
                                            echo $current->mjob->post_title;
                                            ?></div>
                                    </div>
                                </h4>
                                <div class="package-statistic">
                                    <div class="text-content">
                                        <?php $count_review = mJobCountReview($current->mjob->ID);?>
                                        <ul>
                                            <li>
                                                <span><i class="fa fa-star"></i><?php _e('Overall rate', ET_DOMAIN); ?></span>
                                                <div class="total-number"><?php echo round($current->mjob->rating_score, 1); ?></div>
                                            </li>
                                            <li>
                                                <span><i class="fa fa-commenting"></i><?php _e('Reviews', ET_DOMAIN); ?></span>
                                                <div class="total-number"><?php echo $count_review; ?></div>
                                            </li>
                                            <li>
                                                <span><i class="fa fa-shopping-cart"></i><?php _e('Sales', ET_DOMAIN); ?></span>
                                                <div class="total-number"><?php echo mJobCountOrder($current->mjob->ID); ?></div>
                                            </li>
                                            <li>
                                                <span><i class="fa fa-calendar"></i><?php _e('Time delivery', ET_DOMAIN); ?></span>
                                                <div class="total-number time-delivery-label"><?php echo $current->mjob_time_delivery.' '; ?>day(s)</div>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group clearfix float-right change-pass-button-method">
                        <button class="btn-submit btn-reorder"><?php echo sprintf(__('REORDER NOW (%s)', ET_DOMAIN),$current->amount_text); ?></button>
                        <input type="hidden" class="input-item" name="_wpnonce" id="profile_wpnonce" value="<?php echo de_create_nonce('ae-mjob_post-sync');?>" />
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
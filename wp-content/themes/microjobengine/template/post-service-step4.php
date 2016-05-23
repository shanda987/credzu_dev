<?php
global $user_ID, $post;
$step = 4;

$disable_plan = ae_get_option('disable_plan', false);
if($disable_plan) $step--;
if($user_ID) $step--;
?>
<div class="step-payment" id="step4">
    <div id="content" class="mjob-single-page mjob-order-page mjob-float-left">
        <div class="block-page">
            <div class="container dashboard withdraw">
                <div class="title-top-pages">
                    <p class="block-title"><?php _e('YOUR PAYMENT', ET_DOMAIN) ?></p>
                </div>
                <div class="row order-information">
                    <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12 items-chosen">
                        <div class="block-items">
                            <ul>
                                <p class="title-sub"><?php _e('Microjob name', ET_DOMAIN); ?></p>
                                <li>
                                    <div class="inner">
                                        <div class="vote">
                                            <div class="rate-it" data-score=""></div>
                                        </div>
                                        <a class="pm-mjob-img" href=""><img width="100%" src="" alt="" class="img-responsive"></a>

                                        <h2>
                                            <a class="pm-mjob-title" href=""> Title post</a>
                                        </h2>

                                        <div class="author">
                                            <p><span class="by-author"><?php _e('by ', ET_DOMAIN); ?></span><span class="pm-by-author">author</span>
                                            </p>
                                        </div>
                                        <div class="price">
                                            <span class="pm-price-text"></span>
                                        </div>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-lg-8 col-md-8 col-sm-12 col-xs-12 order mjob-order-info">
                        <div class="row">
                            <div class="col-lg-10 col-md-10 col-sm-10 col-xs-9">
                                <div class="title-sub"><?php _e('Summary description', ET_DOMAIN); ?></div>
                                <div class="pm-pack-description"> Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aliquam dapibus mauris diam, et blandit turpis faucibus in. Vivamus nisi lacus, sodales a consectetur vitae, rhoncus id orci. Curabitur varius, mauris vel congue consectetur, elit massa pretium dolor, id commodo enim nisi in nunc. Fusce ut diam tincidunt tortor laoreet cursus. Mauris id sagittis orci. Sed feugiat libero ex, id pharetra magna volutpat eget. Maecenas massa turpis, interdum vel enim sit amet, vehicula ullamcorper nibh. Pellentesque vehicula elementum est fermentum volutpat. Nunc facilisis viverra mi.</div>
                            </div>
                            <div class="col-lg-2 col-md-2 col-sm-2 col-xs-3 float-right mjob-order-info">
                                <div class="title-sub"><?php _e('Price', ET_DOMAIN) ;?></div>
                                <p class="price pm-pack-price-text">$100</p>
                            </div>
                        </div>
                        <div class="add-extra">
                            <span class="title-sub"><?php _e('Extra', ET_DOMAIN); ?></span>
                            <div class="extra-container">
                                <?php     get_template_part('template/new-list', 'extras'); ?>
                            </div>
                        </div>
                        <div class="float-right">
                            <p><span class="total-text"><?php _e('Total', ET_DOMAIN ); ?></span> <span class="total-price mjob-price pm-pack-price-total">$100</span></p>
                            <button class="btn-submit btn-checkout mjob-btn-checkout waves-effect waves-light"><?php _e('Checkout now', ET_DOMAIN); ?></button>
                        </div>
                    </div>
                </div>
            </div>
            <input type="hidden" name="amount" value="">
        </div>
    </div>
</div>

<?php
/**
 * Template Name: Page order
 *
 * @param void
 * @return void
 * @since 1.0
 * @package MicrojobEngine
 * @category void
 * @author JACK BUI
 */
get_header();
global $ae_post_factory;
$post_obj = $ae_post_factory->get('mjob_post');
$post_id = -1;
if( isset($_GET['mjob_id']) ){
    $post_id = $_GET['mjob_id'];
}
$post = get_post($post_id);
$current = '';
if( $post && "mjob_post" == $post->post_type ) {
    $current = $post_obj->convert($post);
    echo '<script type="text/template" id="mjob_single_data" >' . json_encode($current) . '</script>';

    $total = (float)$current->et_budget;
    $extras_ids = array();
    if (isset($_GET['extras_ids'])) {
        $extras_ids = $_GET['extras_ids'];
    }
    if (!empty($extras_ids)) {
        foreach ($extras_ids as $key => $value) {
            $extra = mJobExtraAction()->get_extra_of_mjob($value, $current->ID);
            if ($extra) {
                $total += (float)$extra->et_budget;
            } else {
                unset($extras_ids[$key]);
            }
        }
    }
    $total_text = mJobPriceFormat($total);
    echo '<script type="text/template" id="mjob-extra-ids">' . json_encode($extras_ids) . '</script>';
    $order_args = array(
        'post_title' => sprintf(__('Order for %s ', ET_DOMAIN), $current->post_title),
        'post_content' => sprintf(__('Order for %s ', ET_DOMAIN), $current->post_title),
        'post_parent' => $current->ID,
        '_wpnonce' => de_create_nonce('ae-mjob_post-sync'),
    );
    echo '<script type="text/template" id="mjob-order-info">' . json_encode($order_args) . '</script>';
    ?>
    <div id="content" class="mjob-single-page mjob-order-page">
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
                                        <div class="rate-it" data-score="<?php echo $current->rating_score; ?>"></div>
                                    </div>
                                    <a href="<?php echo $current->permalink; ?>"><img width="100%"
                                                                                      src="<?php echo $current->the_post_thumbnail; ?>"
                                                                                      alt="" class="img-responsive"></a>

                                    <h2>
                                        <a href="<?php echo $current->permalink; ?>"><?php echo $current->post_title; ?></a>
                                    </h2>

                                    <div class="author">
                                        <p><span
                                                class="by-author"><?php _e('by ', ET_DOMAIN); ?></span> <?php echo $current->author_name; ?>
                                        </p>
                                    </div>
                                    <div class="price">
                                        <span><?php echo $current->et_budget_text; ?></span>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
                <?php include(locate_template('template/order-step1.php')); ?>
                <?php include(locate_template('template/order-step2.php')); ?>
            </div>
        </div>
        <input type="hidden" name="amount" value="<?php echo $total ?>">
    </div>
    <?php
} else {
        // If not found any job match job id
        ?>
        <div id="content" class="mjob-single-page mjob-order-page">
            <div class="block-page">
                <div class="container dashboard withdraw">
                    <p><?php _e('Oops. Seems like there\'re no jobs for this order.', ET_DOMAIN); ?></p>
                </div>
            </div>
        </div>
        <?php
} ?>

<?php get_footer(); ?>

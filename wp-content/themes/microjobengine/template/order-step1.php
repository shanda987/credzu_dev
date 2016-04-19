<div class="col-lg-8 col-md-8 col-sm-12 col-xs-12 order mjob-order-info">
    <div class="row">
        <div class="col-lg-10 col-md-10 col-sm-10 col-xs-9">
            <div class="title-sub"><?php _e('Summary description', ET_DOMAIN); ?></div>
            <?php echo $current->post_content; ?>
        </div>
        <div class="col-lg-2 col-md-2 col-sm-2 col-xs-3 float-right mjob-order-info">
            <div class="title-sub"><?php _e('Price', ET_DOMAIN) ;?></div>
            <p class="price"><?php echo $current->et_budget_text; ?></p>
        </div>
    </div>
    <div class="add-extra">
        <span class="title-sub"><?php _e('Extra', ET_DOMAIN); ?></span>
        <div class="extra-container">
            <?php get_template_part('template/list', 'extras'); ?>
        </div>
    </div>
    <div class="float-right">
        <p><span class="total-text"><?php _e('Total', ET_DOMAIN ); ?></span> <span class="total-price mjob-price"><?php echo $total_text; ?></span></p>
        <button class="btn-submit btn-checkout mjob-btn-checkout waves-effect waves-light"><?php _e('Checkout now', ET_DOMAIN); ?></button>
    </div>
</div>
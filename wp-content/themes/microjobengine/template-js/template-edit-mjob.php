<?php
global $ae_post_factory;

// Get the Client Options for the checkboxes
$post_type = $ae_post_factory->get('mjob_post');
$options_array = [];
foreach ($post_type->meta as $key => $value) {
    if (strpos($value, 'option_') === false) {
        continue;
    }
    $options_array[] = $value;
}

// Re-tick the checkboxes
$existing_meta = get_post_meta($wp_query->post->ID);
$checkbox_fields = [];
foreach ($existing_meta as $key => $value) {
    if (strpos($key, 'option_') === false) {
        continue;
    }
    $checkbox_fields[] = $key;
}
?>
<form  class="post-job step-post post et-form edit-mjob-form" style="display: none">
    <p class="mjob-title"><?php _e('Edit Your Listing', ET_DOMAIN); ?></p>
    <div class="form-group clearfix">
        <div class="input-group width-100">
            <?php echo sprintf(__('<p class="cat-text"><label>Category: %s', ET_DOMAIN), '</label><span class="mjob-cat-single"></span></p>'); ?>
<!--            <div class="input-group-addon"></div>-->
<!--            --><?php //ae_tax_dropdown( 'mjob_category' ,
//                array(  'attr' => 'data-chosen-width="100%" data-chosen-disable-search=""  data-placeholder="'.__("Choose categories", ET_DOMAIN).'"',
//                    'class' => 'chosen chosen-single tax-item required',
//                    'hide_empty' => false,
//                    'hierarchical' => true ,
//                    'id' => 'mjob_category' ,
//                    'show_option_all' => false
//                )
//            ) ;?>
<!--            <label>--><?php //_e('Select the relevant category for your service ', ET_DOMAIN)?><!--<a href="#" class="mjob-question-post"> <i class="fa fa-question-circle" aria-hidden="true"></i></a></label>-->
        </div>
    </div>
    <div class="form-group clearfix">
        <div class="input-group width-100">
<!--            <div class="input-group-addon"></div>-->
            <input type="text" class="input-item input-full" name="post_title" placeholder="<?php _e('i.e., I will challenge all inaccurate negative items within 35 days', ET_DOMAIN); ?>" value="" required>
            <label><?php _e('Add a title for your listing ', ET_DOMAIN)?><a href="#" class="mjob-question-post"> <i class="fa fa-question-circle" aria-hidden="true"></i></a></label>
        </div>
    </div>
    <div class="form-group row margin-left-0 clearfix">
        <div class="input-group delivery-time width-100">
<!--            <div class="input-group-addon"></div>-->
            <input type="number" name="time_delivery" placeholder="<?php _e('i.e., 10', ET_DOMAIN); ?>" value="" class="input-item time-delivery" min="0">
            <span class="text-note"><?php _e('Day(s', ET_DOMAIN) ?></span>
            <label><?php _e('Enter the amount of days the service will take to complete ', ET_DOMAIN)?><a href="#" class="mjob-question-post"> <i class="fa fa-question-circle" aria-hidden="true"></i></a></label>
        </div>
    </div>
    <div class="form-group clearfix">
        <div class="input-group width-100">
<!--            <div class="input-group-addon"></div>-->
            <input type="number" min="0" class="input-item input-full" name="et_budget" placeholder="<?php _e('i.e., 100', ET_DOMAIN); ?>" required>
            <label><?php _e('Enter the amount your client will pay you for the service ', ET_DOMAIN)?><a href="#" class="mjob-question-post"> <i class="fa fa-question-circle" aria-hidden="true"></i></a></label>
        </div>
    </div>
    <div class="form-group">
        <?php wp_editor( '', 'post_content', ae_editor_settings()  );  ?>
        <label><?php _e('Create a thorough and compelling description of your service ', ET_DOMAIN)?><a href="#" class="mjob-question-post"> <i class="fa fa-question-circle" aria-hidden="true"></i></a></label>
    </div>
    <div class="form-group group-attachment gallery_container" id="gallery_container">
        <div class="outer-carousel-gallery">
            <div class="img-avatar carousel-gallery">
                <img width="100%" src="<?php echo TEMPLATEURL ?>/assets/img/image-avatar.jpg" alt="">
                <input type="hidden" class="input-item" name="et_carousels" value="" />
            </div>
            <label class="post-image-photo"><?php _e('Click the plus sign to add up to 5 images to represent your services. ', ET_DOMAIN)?><a href="#" class="mjob-question-post"> <i class="fa fa-question-circle" aria-hidden="true"></i></a></label>
        </div>
        <div class="attachment-image">
            <ul class="image-list" id="image-list">
                <li >
                    <div class="image-upload carousel_container" id="carousel_container">
                        <span for="file-input" class="carousel_browse_button" id="carousel_browse_button">
                            <a class="add-img"><img src="<?php echo get_stylesheet_directory_uri() ?>/assets/img/icon-plus.png" alt=""></a>
                        </span>
                    </div>
                </li>
            </ul>
            <span class="et_ajaxnonce" id="<?php echo wp_create_nonce( 'ad_carousels_et_uploader' ); ?>"></span>
        </div>
    </div>
    <div class="form-group post_listing_agreement_term">
        <label><?php _e('AGREEMENT TERMS', ET_DOMAIN) ?></label>
        <p>
            <?php _e('The terms  into which you and your client agree, are shown below. If you want to requrest a change, please email us at info@credzu.com', ET_DOMAIN); ?>
        </p>
    </div>
    <div class="form-group post_listing_agreement_term_content">
        <?php
        $p = get_post(160);
        if( isset($p->post_content)) {
            echo $p->post_content;
        }
        ?>
    </div>
    <div class="form-group post_listing_agreement_term_field">
        <label><?php _e('INFORMATION COLLECTED FROM YOUR CLIENT', ET_DOMAIN) ?></label>
        <p>
            <?php _e('We will collect and provide you with the following information and documentation from you client. If you would like other information, Please email us at information', ET_DOMAIN); ?>
        </p>
        <?php
        $requirements = get_terms( 'mjob_requirement', array(
            'hide_empty' => false,
        ) );
        if( !empty($requirements ) ): ?>
            <ul>
                <?php foreach( $requirements as $r): ?>
                    <li><?php echo $r->name; ?></li>
                <?php endforeach; ?>
            </ul>

        <?php endif;
        ?>
    </div>
    <div class="form-group post-listing-button-wrapper">
        <a href=="#" class="btn-discard mjob-discard-action"><?php _e('DISCARD', ET_DOMAIN ); ?></a>
        <button class="btn-submit btn-save" type="submit"><?php _e('SAVE', ET_DOMAIN); ?></button>
        <input type="hidden" class="input-item post-service_nonce" name="_wpnonce" value="<?php echo de_create_nonce('ae-mjob_post-sync');?>" />
    </div>
</form>
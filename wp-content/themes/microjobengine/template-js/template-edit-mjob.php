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
        <div class="input-group">
            <div class="input-group-addon"><i class="fa fa-adn"></i></div>
            <input type="text" class="input-item input-full" name="post_title" placeholder="Job name" value="" required>
        </div>
    </div>
    <div class="form-group row clearfix">
        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
            <div class="input-group delivery-time">
                <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                <input type="number" name="time_delivery" placeholder="Time delivery" value="" class="input-item time-delivery" min="0">
                <span class="text-note"><?php _e('Day(s', ET_DOMAIN) ?></span>
            </div>
        </div>
        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 category-area">
            <div class="input-group">
                <div class="input-group-addon"><i class="fa fa-pagelines"></i></div>
                <?php ae_tax_dropdown( 'mjob_category' ,
                    array(  'attr' => 'data-chosen-width="100%" data-chosen-disable-search=""  data-placeholder="'.__("Choose categories", ET_DOMAIN).'"',
                        'class' => 'chosen chosen-single tax-item required',
                        'hide_empty' => false,
                        'hierarchical' => true ,
                        'id' => 'mjob_category' ,
                        'show_option_all' => false
                    )
                ) ;?>
            </div>
        </div>
    </div>
    <div class="form-group clearfix">
        <div class="input-group">
            <div class="input-group-addon"><i class="fa fa-money" aria-hidden="true"></i></div>
            <input type="number" min="0" class="input-item input-full" name="et_budget" placeholder="<?php _e('Price', ET_DOMAIN); ?>" required>
        </div>
    </div>
    <div class="form-group">
        <label><?php _e('YOUR SERVICE DESCRIPTION', ET_DOMAIN) ?></label>
        <?php wp_editor( '', 'post_content', ae_editor_settings()  );  ?>
    </div>
    <div class="form-group">
        <label><?php _e('YOUR CLIENT REQUIREMENTS ', ET_DOMAIN) ?></label>
        <p>
            <?php _e('Select the information you require of your client in order to perform the job. Anything selected, we will collect from the client for you.', ET_DOMAIN); ?>
        </p>
        <div class="input-group requirement-style">
            <?php ae_tax_dropdown( 'mjob_requirement' ,
                array(  'attr' => 'multiple data-chosen-width="100%"   data-placeholder="'.__("Choose Client's requirement", ET_DOMAIN).'"',
                    'class' => 'chosen multi-tax-item tax-item required',
                    'hide_empty' => false,
                    'hierarchical' => true ,
                    'id' => 'mjob_requirement' ,
                    'show_option_all' => false
                )
            ) ;?>
        </div>
    </div>
    <div class="form-group">
        <label><?php _e('AGREEMENT TERMS', ET_DOMAIN) ?></label>
        <p>
            <?php _e('This will be appended to the agreement the client signs.', ET_DOMAIN); ?>
        </p>
        <?php wp_editor( '', 'agreement_terms', ae_editor_settings()  );  ?>
    </div>
    <div class="form-group group-attachment gallery_container" id="gallery_container">
        <div class="outer-carousel-gallery">
            <div class="img-avatar carousel-gallery">
                <img width="100%" src="<?php echo TEMPLATEURL ?>/assets/img/image-avatar.jpg" alt="">
                <input type="hidden" class="input-item" name="et_carousels" value="" />
            </div>
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
    <div class="mjob-extras-wrapper">
    </div>
    <div class="form-group">
        <button class="btn-submit btn-save" type="submit"><?php _e('SAVE', ET_DOMAIN); ?></button>
        <a href=="#" class="btn-discard mjob-discard-action"><?php _e('DISCARD', ET_DOMAIN ); ?></a>
        <input type="hidden" class="input-item post-service_nonce" name="_wpnonce" value="<?php echo de_create_nonce('ae-mjob_post-sync');?>" />
    </div>
</form>
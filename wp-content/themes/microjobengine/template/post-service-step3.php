<?php

// They will Submit their Post here with their billing info and the charge.
global $ae_post_factory, $user_ID;

if(isset($_REQUEST['id'])) {
    $post = get_post($_REQUEST['id']);
    if($post) {
        global $ae_post_factory;
        $post_object = $ae_post_factory->get($post->post_type);
        echo '<script type="data/json"  id="edit_postdata">'. json_encode($post_object->convert($post)) .'</script>';
    }

}
if( isset($_GET['return_url'])  ){
    $return = $_GET['return_url'];
}
else{
    $return = home_url();
}
// Get the Client Options for the checkboxes
?>
<div class="step-wrapper step-post" id="step-post">
        <p class="post-mjob-noti">
            <?php _e('By completing the following form, you will create a public listing on our site through which consumer can hire you. Your listing will remain indefinitely until a client has hired you.', ET_DOMAIN); ?>
        </p>
    <form class="post-job post et-form" id="">
        <div class="form-group clearfix">
            <div class="input-group">
                <div class="input-group-addon">
<!--                    <i class="fa fa-adn"></i>-->
                </div>
                <input type="text" class="input-item input-full" name="post_title" placeholder="<?php _e('Credit Counseling', ET_DOMAIN); ?>" required>
            </div>
        </div>
        <div class="form-group row clearfix">
            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 clearfix delivery-area">
                <div class="input-group">
                    <div class="input-group-addon">
<!--                        <i class="fa fa-calendar"></i>-->
                    </div>
                    <input type="number" name="time_delivery" placeholder="<?php _e('i.e., 10', ET_DOMAIN); ?>" class="input-item time-delivery" min="0" pattern="[-+]?[0-9]*[.,]?[0-9]+" required>
                    <span class="text-note"><?php _e('Day(s)', ET_DOMAIN); ?></span>
                </div>
            </div>
            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 clearfix category-area">
                <div class="input-group">
                    <div class="input-group-addon">
<!--                        <i class="fa fa-pagelines"></i>-->
                    </div>
                    <?php ae_tax_dropdown( 'mjob_category' ,
                        array(  'attr' => 'data-chosen-width="100%" data-chosen-disable-search=""  data-placeholder="'.__("i.e., I will challenge all inaccurate negative items within 35 days", ET_DOMAIN).'"',
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
                <div class="input-group-addon">
<!--                    <i class="fa fa-money" aria-hidden="true"></i>-->
                </div>
                <input type="number" name="et_budget" placeholder="<?php _e('i.e., 100', ET_DOMAIN); ?>" class="input-item" min="0" pattern="[-+]?[0-9]*[.,]?[0-9]+" required>
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
                <?php _e('Reduce your job to a legal description which includes any promises to your client.', ET_DOMAIN); ?>
            </p>
            <?php wp_editor( '', 'agreement_terms', ae_editor_settings()  );  ?>
        </div>
        <div class="form-group group-attachment gallery_container" id="gallery_container">
            <div class="outer-carousel-gallery">
                <div class="img-avatar carousel-gallery">
                    <img width="100%" src="<?php echo TEMPLATEURL ?>/assets/img/image-avatar.jpg" alt="">
                    <div class="upload-description">
                        <i class="fa fa-picture-o"></i>
                        <p><?php _e('Up to 5 pictures', ET_DOMAIN); ?></p>
                        <p><?php _e('Select one picture for your featured image', ET_DOMAIN); ?></p>
                    </div>
                    <input type="hidden" class="input-item show" name="et_carousels" value="" required />
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
            <button class="btn-submit btn-save waves-effect waves-light" type="submit"><?php _e('SAVE', ET_DOMAIN); ?></button>
            <a href="<?php echo $return; ?>" class="btn-discard"><?php _e('DISCARD', ET_DOMAIN ); ?></a>
            <input type="hidden" class="input-item post-service_nonce" name="_wpnonce" value="<?php echo de_create_nonce('ae-mjob_post-sync');?>" />
        </div>
    </form>
</div>
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
        <div class="form-group row clearfix">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 clearfix padding-0 ">
                <div class="input-group">
                    <div class="input-group-addon">
                        <!--                        <i class="fa fa-pagelines"></i>-->
                    </div>
                    <?php ae_tax_dropdown( 'mjob_category' ,
                        array(  'attr' => 'data-chosen-width="100%" data-chosen-disable-search=""  data-placeholder="'.__("select a category", ET_DOMAIN).'"',
                            'class' => 'chosen chosen-single tax-item required',
                            'hide_empty' => false,
                            'hierarchical' => true ,
                            'id' => 'mjob_category' ,
                            'show_option_all' => __('Select category', ET_DOMAIN)
                        )
                    ) ;?>
                    <input type="hidden" name="is_credit_repair" id="is_credit_repair" value="0" />
                    <label><?php _e('Select the relevant category for your service ', ET_DOMAIN)?><a href="#" class="mjob-question-post"> <i class="fa fa-question-circle" aria-hidden="true"></i></a></label>
                </div>
            </div>
        </div>
        <div class="form-group clearfix padding-0">
            <div class="input-group width-100">
                <input type="text" class="input-item input-full " name="post_title" placeholder="<?php _e('i.e., I will challenge all inaccurate negative items within 35 days', ET_DOMAIN); ?>" required>
                <label><?php _e('Add a title for your listing ', ET_DOMAIN)?><a href="#" class="mjob-question-post"> <i class="fa fa-question-circle" aria-hidden="true"></i></a></label>
            </div>
        </div>
        <div class="form-group row clearfix">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 clearfix ">
                <div class="input-group ">
                    <input type="number" name="time_delivery" placeholder="<?php _e('e.g. 22 (or anything over 20).', ET_DOMAIN); ?>" class="input-item time-delivery" id="time_delivery" min="0" pattern="[-+]?[0-9]*[.,]?[0-9]+" required>
                    <span class="text-note"><?php _e('Day(s)', ET_DOMAIN); ?></span>
                    <label><?php _e('Enter the amount of days the service will take to complete ', ET_DOMAIN)?><a href="#" class="mjob-question-post"> <i class="fa fa-question-circle" aria-hidden="true"></i></a></label>
                </div>
            </div>
        </div>
        <div class="form-group clearfix ">
            <div class="input-group  width-100">
                <select data-chosen-width="100%" data-chosen-disable-search=""  data-placeholder="<?php _e("select a category", ET_DOMAIN); ?>" name="et_budget_type" class="chosen chosen-single tax-item required">
                    <option value=""><?php _e('Select an option', ET_DOMAIN); ?></option>
                    <option value="fixed"><?php _e('Flat Fee', ET_DOMAIN); ?></option>
                    <option value="dynamic"><?php _e('Pay for results', ET_DOMAIN); ?></option>
                </select>
                <label><?php _e('Select your service payment type', ET_DOMAIN)?><a href="#" class="mjob-question-post" data-content="Slect an option"> <i class="fa fa-question-circle" aria-hidden="true"></i></a></label>
            </div>
        </div>
        <div class="form-group clearfix ">
            <div class="input-group  width-100">
                <input type="number" name="et_budget" placeholder="<?php _e('i.e., 100', ET_DOMAIN); ?>" class="input-item" min="0" pattern="[-+]?[0-9]*[.,]?[0-9]+" required>
                <label><?php _e('Enter the amount your client will pay you for the service ', ET_DOMAIN)?><a href="#" class="mjob-question-post"> <i class="fa fa-question-circle" aria-hidden="true"></i></a></label>
            </div>
        </div>
        <div class="form-group">
            <?php wp_editor( '', 'post_content', ae_editor_settings()  );  ?>
            <label><?php _e('Create a thorough and compelling description of your service ', ET_DOMAIN)?><a href="#" class="mjob-question-post"> <i class="fa fa-question-circle" aria-hidden="true"></i></a></label>
        </div>
<!--        <div class="form-group">-->
<!--            <label>--><?php //_e('YOUR CLIENT REQUIREMENTS ', ET_DOMAIN) ?><!--</label>-->
<!--            <p>-->
<!--            --><?php //_e('Select the information you require of your client in order to perform the job. Anything selected, we will collect from the client for you.', ET_DOMAIN); ?>
<!--            </p>-->
<!--                <div class="input-group requirement-style">-->
<!--                    --><?php //ae_tax_dropdown( 'mjob_requirement' ,
//                    array(  'attr' => 'multiple data-chosen-width="100%"   data-placeholder="'.__("Choose Client's requirement", ET_DOMAIN).'"',
//                        'class' => 'chosen multi-tax-item tax-item required',
//                        'hide_empty' => false,
//                        'hierarchical' => true ,
//                        'id' => 'mjob_requirement' ,
//                        'show_option_all' => false
//                    )
//                ) ;?>
<!--                </div>-->
<!--        </div>-->
<!--        <div class="form-group">-->
<!--            <label>--><?php //_e('AGREEMENT TERMS', ET_DOMAIN) ?><!--</label>-->
<!--            <p>-->
<!--                --><?php //_e('Reduce your job to a legal description which includes any promises to your client.', ET_DOMAIN); ?>
<!--            </p>-->
<!--            --><?php //wp_editor( '', 'agreement_terms', ae_editor_settings()  );  ?>
<!--        </div>-->
        <div class="form-group group-attachment gallery_container" id="gallery_container">
            <div id="carousel_container" >
                <div class="outer-carousel-gallery">
                    <div class="img-avatar carousel-gallery">
                        <img width="100%" src="<?php echo TEMPLATEURL ?>/assets/img/image-avatar.jpg" alt="">
                        <div class="upload-description">
                            <i class="fa fa-picture-o"></i>
                            <p><?php _e('Preview', ET_DOMAIN); ?></p>
                        </div>
                        <input type="hidden" class="input-item show" name="et_carousels" value="" required />
                    </div>
                    <label class="post-image-photo"><?php _e('Click the plus sign to add up to 5 images to represent your services. ', ET_DOMAIN)?><a href="#" class="mjob-question-post"> <i class="fa fa-question-circle" aria-hidden="true"></i></a></label>
                </div>
                <div class="attachment-image">
                    <ul class="image-list" id="image-list">
                        <li >
                            <div class="image-upload carousel_container" >
                                <span for="file-input" class="carousel_browse_button" id="carousel_browse_button">
                                    <a class="add-img"><img src="<?php echo get_stylesheet_directory_uri() ?>/assets/img/icon-plus.png" alt=""></a>
                                </span>
                            </div>
                        </li>
                    </ul>
                    <span class="et_ajaxnonce" id="<?php echo wp_create_nonce( 'ad_carousels_et_uploader' ); ?>"></span>
                </div>
            </div>
        </div>
        <div class="form-group post_listing_agreement_term">
            <label><?php _e('AGREEMENT TERMS', ET_DOMAIN) ?></label>
            <p>
                <?php _e('The terms to which you and your client agree, are shown below. If you want to requrest a change, please email us at info@credzu.com', ET_DOMAIN); ?>
            </p>
        </div>
        <div class="form-group post_listing_agreement_term_content">
            <?php
            $p = get_post(160);
            if( isset($p->post_content)) {
                echo apply_filters('the_content', $p->post_content);
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
            <a href="<?php echo $return; ?>" class="btn-discard"><?php _e('DISCARD', ET_DOMAIN ); ?></a>
            <button class="btn-submit btn-save waves-effect waves-light" type="submit"><?php _e('SAVE', ET_DOMAIN); ?></button>
            <input type="hidden" class="input-item post-service_nonce" name="_wpnonce" value="<?php echo de_create_nonce('ae-mjob_post-sync');?>" />
        </div>
    </form>
</div>
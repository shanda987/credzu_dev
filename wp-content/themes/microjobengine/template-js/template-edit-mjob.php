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
    <p class="mjob-title">Edit Your Listing</p>
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
                <span class="text-note">Day(s)</span>
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
    <div class="form-group">
        <label><?php _e('YOUR SERVICE DESCRIPTION', ET_DOMAIN) ?></label>
        <?php wp_editor( '', 'post_content', ae_editor_settings()  );  ?>
    </div>
    <div class="form-group">
        <label><?php _e('YOUR CLIENT REQUIREMENTS ', ET_DOMAIN) ?></label>
        <p>
        Select any items that your client must provide to you in order to do your services.
        </p>
        <div class="row">
            <?php
            // This will make a two column multi-row
            $i = 0;
            foreach ($options_array as $option):
                $break = ( ++$i % 2 == 0 ) ? true : false;
                if ($break) {
                    echo '<div class="row-fluid">';
                }
            ?>
                <div class="col-md-6">
                    <input type="checkbox" name="<?=$option?>" id="<?=$option?>" <?=in_array($option, $checkbox_fields) ? 'checked="checked"' : '' ?>>
                    <label for="<?=$option?>"><?=ucwords(str_replace(['option', '_'], ['', ' '], $option));?></label>
                </div>

            <?php
            if ($break) {
                echo "</div>";
            }
            endforeach; ?>
        </div>
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
    <div class="add-more">
        <a href="#" class="mjob-add-extra-btn"><?php _e('Add extra', ET_DOMAIN); ?><span class="icon-plus"><i class="fa fa-plus"></i></span></a>
    </div>
    <div class="form-group clearfix skill-control">
        <span>TAGS</span>
        <?php
        $switch_skill = ae_get_option('switch_skill');
        if(!$switch_skill){
            ?>
            <input type="text" class="form-control text-field skill" id="skill" placeholder="<?php _e("Enter listing tags", ET_DOMAIN); ?>" name=""  autocomplete="off" spellcheck="false" >
            <ul class="skills-list" id="skills_list"></ul>
            <?php
        }else{
            ae_tax_dropdown( 'skill' , array(  'attr' => 'data-chosen-width="100%" data-chosen-disable-search="" multiple data-placeholder="'.__(" Skills (max is 5)", ET_DOMAIN).'"',
                    'class' => 'sw_skill chosen multi-tax-item tax-item required',
                    'hide_empty' => false,
                    'hierarchical' => true ,
                    'id' => 'skill' ,
                    'show_option_all' => false
                )
            );
        }

        ?>
    </div>
    <div class="form-group">
        <button class="btn-submit btn-save" type="submit"><?php _e('SAVE', ET_DOMAIN); ?></button>
        <a href=="#" class="btn-discard mjob-discard-action"><?php _e('DISCARD', ET_DOMAIN ); ?></a>
        <input type="hidden" class="input-item post-service_nonce" name="_wpnonce" value="<?php echo de_create_nonce('ae-mjob_post-sync');?>" />
    </div>
</form>
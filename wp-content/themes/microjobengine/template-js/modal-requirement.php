<div class="modal fade" id="requirement_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel1">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true"><img
                            src="<?php echo get_template_directory_uri() ?>/assets/img/icon-close.png" alt=""></span></button>
                <h4 class="modal-title" id="myModalLabel1"><?php _e('<span class="requirement-modal-title"></span> Upload Form' , ET_DOMAIN); ?></h4>
            </div>
            <div class="modal-body">
                <div class="inner-form  et-form">
                    <div id="requirement_container" class="image-upload gallery_container_order_requirement" style="margin-bottom: 30px;">
                        <div  class="browse_button">
                            <ul class="gallery-image carousel-list requirement-image-list" id="image-list">

                            </ul>
                            <div class="drag-image" id="requirement_browse_button">
                                <i class="fa fa-cloud-upload"></i>
                                <p class="drag-image-title"><?php _e('Drag your <span class="requirement-modal-title-here"></span>') ?></p>
                                <span class="drag-image-text"><?php _e('or', ET_DOMAIN); ?></span>
                                <a class="drag-image-select-button"><?php _e('upload from local storage', ET_DOMAIN); ?></a>
                            </div>
                        </div>
                        <span class="et_ajaxnonce" id="<?php echo wp_create_nonce( 'ad_carousels_et_uploader' ); ?>"></span>
                        <label for="f_upload" class="error f-upload">This field is required.</label>
                    </div>
<!--                    <input type="hidden" class="et_ajax_nonce" value="--><?php //echo de_create_nonce( 'upload_avatar_et_uploader' ); ?><!--">-->
                    <div class="form-group allow-upload">
                        <input type="checkbox" class="required" required id="allow_upload" value=""/>
                        <label for="allow_upload" class="requirement-modal-title-allow"></label>
                        <br/><label for="allow_upload" class="error l-hide">This field is required.</label>
                    </div>
                    <div class="form-group float-right">
                        <button class="btn-submit btn-save btn-save-requirement" ><?php _e('UPLOAD', ET_DOMAIN); ?></button>
                        <a href="#" class="btn-remove"><?php _e('REMOVE IMAGE', ET_DOMAIN); ?></a>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="delivery" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true"><img
                            src="<?php echo get_template_directory_uri() ?>/assets/img/icon-close.png" alt=""></span></button>
                <h4 class="modal-title" id="myModalLabelForgot"><?php _e('Provide Results', ET_DOMAIN) ?></h4>
            </div>
            <div class="modal-body delivery-order">
                <div class="form-delivery-order">                         <div id="gallery_container" class="image-upload attachment-file attachment-file gallery_container inner-form" style="margin-bottom: 30px;">

                    <p class="notice-message">
                        <span class="note-action"><?php _e('Confirm your job is done.', ET_DOMAIN); ?></span><br/>
                        <?php _e('By confirming that you have completed the work ou agreed to perform for your client. a payment from your client will be generated and they will informed. Please communicate with your client so they understand the difference between performing work and obtaining results.', ET_DOMAIN ) ?>
                    </p>
                    <form class="form-authentication">
                        <div class="form-group">
                            <textarea name="post_content" placeholder="Inactive text field"></textarea>
                        </div>
                        <div id="gallery_container" class="image-upload attachment-file attachment-file gallery_container" style="margin-bottom: 30px;">
                            <div  class="browse_button carousel_container">
                                <ul class="gallery-image carousel-list carousel_deliver-image-list requirement-image-list" id="image-list">

                                </ul>
                                <div class="plupload_buttons" id="carousel_deliver_container">
                                    <div class="drag-image" id="carousel_deliver_browse_button">
                                        <i class="fa fa-cloud-upload"></i>
                                        <p class="drag-image-title"><?php _e('Drag your <span class="requirement-modal-title-here"></span>') ?></p>
                                        <span class="drag-image-text"><?php _e('or', ET_DOMAIN); ?></span>
                                        <a class="drag-image-select-button"><?php _e('upload from local storage', ET_DOMAIN); ?></a>
                                    </div>
                                </div>
                            </div>
                            <span class="et_ajaxnonce" id="<?php echo wp_create_nonce( 'ad_carousels_et_uploader' ); ?>"></span>
                        </div>
                        <div class="form-group">
                            <input type="hidden" class="input-item post-service_nonce" name="_wpnonce" value="<?php echo de_create_nonce('ae-mjob_post-sync');?>" />
                            <button class="btn-submit submit" disabled><?php _e('Send', ET_DOMAIN); ?></button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
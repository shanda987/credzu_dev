<div class="modal fade" id="conversation" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true"><img
                            src="<?php echo get_template_directory_uri() ?>/assets/img/icon-close.png" alt=""></span></button>
                <h4 class="modal-title" id="myModalLabelForgot"><?php _e('Conversation', ET_DOMAIN); ?></h4>
            </div>

            <div class="modal-body mjob-modal-conversation-form">
                <div class="outer-conversation">
                    <form class="">
                        <div class="form-group">
                            <textarea name="conversation_content" id="modal_conversation_content" placeholder="Inactive text field"></textarea>
                        </div>

                        <div class="attachment-file gallery_container_modal_conversation" id="message_modal_gallery_container">
                            <div class="attachment-image">
                                <ul class="gallery-image carousel-list carousel_modal_conversation-image-list" id="image-list">
                                </ul>
                                <div class="plupload_buttons" id="carousel_modal_conversation_container">
                                        <span class="img-gallery" id="carousel_modal_conversation_browse_button">
                                            <a href="#" class="add-img"><?php _e("Attach file", ET_DOMAIN); ?> <i class="fa fa-plus"></i></a>
                                        </span>
                                </div>
                                <span class="et_ajaxnonce" id="<?php echo wp_create_nonce( 'ad_carousels_et_uploader' ); ?>"></span>
                            </div>
                        </div>

                        <div class="form-group">
                            <input type="hidden" class="input-item post-service_nonce" name="_wpnonce" value="<?php echo de_create_nonce('ae-mjob_post-sync');?>" />
                            <button class="btn-submit submit"><?php _e('Send', ET_DOMAIN); ?></button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
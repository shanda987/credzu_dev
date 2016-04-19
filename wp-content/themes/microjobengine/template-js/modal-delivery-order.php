<div class="modal fade" id="delivery" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true"><img
                            src="<?php echo get_template_directory_uri() ?>/assets/img/icon-close.png" alt=""></span></button>
                <h4 class="modal-title" id="myModalLabelForgot">Delivery info</h4>
            </div>
            <div class="modal-body delivery-order">
                <div class="form-delivery-order">
                    <form class="form-authentication">
                        <div class="form-group">
                            <textarea name="post_content" placeholder="Inactive text field"></textarea>
                        </div>
                        <div class="attachment-file gallery_container" id="gallery_container">
                            <div class="attachment-image">
                                <ul class="gallery-image carousel-list image-list" id="image-list">
                                </ul>
                                <div>
                                    <div class="plupload_buttons" id="carousel_container">
                                        <span class="img-gallery" id="carousel_browse_button">
                                            <a href="#" class="add-img"><?php _e("Attach file", ET_DOMAIN); ?> <i class="fa fa-plus"></i></a>
                                        </span>
                                    </div>
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
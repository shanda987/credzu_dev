<script type="text/template" id="mjobExtraItem">
    <div class="col-lg-9 col-md-9 col-sm-9 col-xs-12 no-padding name-item-extra">
        <input type="text" placeholder="<?php _e('Enter the extra service description', ET_DOMAIN); ?>" name="post_title" value="{{=post_title}}" class="input-items">
    </div>
    <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12 no-padding extra-price">
        <span class="label-currency"><?php ae_currency_sign(); ?></span>
        <input type="number" class="text-currency" placeholder="<?php _e('0', ET_DOMAIN); ?>" name="et_budget" value="{{= et_budget}}" min="0">
    </div>
    <a href="#" class="remove-items mjob-remove-extra-item"><i class="fa fa-times"></i></a>
    <input type="hidden" class="input-item" name="_wpnonce" value="<?php echo de_create_nonce('ae-mjob_post-sync');?>" />
    <input type="hidden" class="input-item" name="post_parent" value="" />
</script>
<script type="text/template" id="mjob_my_account_header">
    <div class="list-notification">
        <span class="link-notification"><i class="fa fa-bell"></i></span>
    </div>
    <?php
    $user_role = ae_user_role($user_ID);
    if( is_super_admin() || $user_role == COMPANY ): ?>
    <div class="link-post-services">
        <?php if (mJobProfileAction()->isCompanyActive()): ?>
        <a class="btn-basic" href="<?php echo $post_link; ?>"><?php _e('Post a Listing', ET_DOMAIN); ?>
            <div class="plus-circle"><i class="fa fa-plus"></i></div>
        </a>
        <?php else: ?>
        <a class="btn-basic disabled" href="#" onclick="return false;" data-toggle="tooltip" data-placement="bottom" title="You must have an Active account to post a listing"><?php _e('Post a Listing', ET_DOMAIN); ?>
            <div class="plus-circle"><i class="fa fa-plus"></i></div>
        </a>
        <?php endif;?>
    </div>
    <?php endif;?>
    <div class="user-account">
        <div class="dropdown et-dropdown">
            <div class="dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown">
        <span class="avatar">
            <span class="display-avatar"><# if(typeof avatar !== 'undefined') { #>{{= avatar }}<# } #></span>
            <span class="display-name"><# if(typeof display_name !== 'undefined') { #>{{= display_name }}<# } #></span>
        </span>
                <span class="caret"></span>
            </div>
            <ul class="dropdown-menu et-dropdown-login" aria-labelledby="dLabel">
                <li><a href="<?php echo et_get_page_link('dashboard'); ?>"><?php _e('Dashboard', ET_DOMAIN); ?></a></li>
                <li><a href="<?php echo et_get_page_link("profile"); ?>"><?php _e('My profile', ET_DOMAIN); ?></a></li>
                <li class="post-service-link"><a href="<?php echo et_get_page_link('post-service'); ?>"><?php _e('Post a Listing', ET_DOMAIN); ?>
                        <div class="plus-circle"><i class="fa fa-plus"></i></div>
                    </a></li>
                <li class="get-message-link">
                    <a href="<?php echo et_get_page_link('my-list-messages'); ?>"><?php _e('Message', ET_DOMAIN); ?></a>
                </li>
                <li><a href="<?php echo wp_logout_url(home_url()); ?>"><?php _e('Sign out', ET_DOMAIN); ?></a></li>
            </ul>
        </div>
    </div>
</script>
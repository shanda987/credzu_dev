<?php
if(!function_exists('ae_render_connect_social_button')) {
    function ae_render_connect_social_button($icon_classes = array(), $button_classes = array(), $before_text = '', $after_text = '') {
        // Get social id
        global $current_user;
        $facebook_social_id = get_user_meta($current_user->ID, 'et_facebook_id', true);
        $twitter_social_id = get_user_meta($current_user->ID, 'et_twitter_id', true);
        $google_social_id = get_user_meta($current_user->ID, 'et_google_id', true);

        /* check enable option*/
        $use_facebook = ae_get_option('facebook_login');
        $use_twitter = ae_get_option('twitter_login');
        $gplus_login = ae_get_option('gplus_login');
        $linkedin_login = ae_get_option('linkedin_login') ;
        if( $icon_classes == ''){
            $icon_classes = 'fa fa-facebook-square';
        }
        $defaults_icon = array(
            'fb' => 'fa fa-facebook',
            'gplus' => 'fa fa-google-plus',
            'tw' => 'fa fa-twitter',
            'lkin' => 'fa fa-linkedin'
        );
        $icon_classes = wp_parse_args( $icon_classes, $defaults_icon );
        $icon_classes = apply_filters('ae_social_icon_classes', $icon_classes );
        $defaults_btn = array(
            'fb' => '',
            'gplus' => '',
            'tw' => '',
            'lkin' => ''
        );
        $button_classes = wp_parse_args( $button_classes, $defaults_btn );
        $button_classes = apply_filters('ae_social_button_classes', $button_classes );
        if( $use_facebook || $use_twitter || $gplus_login || $linkedin_login ){
            if( $before_text != '' ){ ?>
                <div class="socials-head"><?php echo $before_text ?></div>
            <?php } ?>
            <ul class="list-social-connect">
                <?php if($use_facebook){ ?>
                        <?php if(empty($facebook_social_id)) { ?>
                        <li>
                            <a href="javascript:void(0)" class="fb facebook_auth_btn <?php echo $button_classes['fb']; ?>">
                                <i class="<?php echo $icon_classes['fb']; ?>"></i>
                                <span class="social-text"><?php _e("Facebook", ET_DOMAIN); ?></span>
                            </a>
                        </li>
                        <?php } else { ?>
                        <li class="connected-text">
                            <a href="javascript:void(0)" class="fb connected-text facebook_disconnect<?php echo $button_classes['fb']; ?>">
                                <i class="<?php echo $icon_classes['fb']; ?>"></i>
                                <span class="social-text"><?php _e("Connected to Facebook", ET_DOMAIN); ?></span>
                            </a>
                        </li>
                        <?php } ?>
                <?php } ?>
                <?php if($gplus_login){ ?>
                        <?php if(empty($google_social_id)) { ?>
                        <li>
                            <a href="javascript:void(0)" class="gplus gplus_login_btn <?php echo $button_classes['gplus']; ?>">
                                <i class="<?php echo $icon_classes['gplus']; ?>"></i>
                                <span class="social-text"><?php _e("Plus", ET_DOMAIN); ?></span>
                            </a>
                        </li>
                        <?php } else { ?>
                        <li class="connected-text">
                            <a href="javascript:void(0)" class="gplus connected-text gplus_disconnect <?php echo $button_classes['gplus']; ?>">
                                <i class="<?php echo $icon_classes['gplus']; ?>"></i>
                                <span class="social-text"><?php _e("Connected to Plus", ET_DOMAIN); ?></span>
                            </a>
                        </li>
                        <?php } ?>
                <?php } ?>
                <?php if($use_twitter){ ?>
                        <?php if(empty($twitter_social_id)) { ?>
                        <li>
                            <a href="<?php echo add_query_arg('action', 'twitterauth', home_url()) ?>" class="tw <?php echo $button_classes['tw']; ?>">
                                <i class="<?php echo $icon_classes['tw']; ?>"></i>
                                <span class="social-text"><?php _e("Twitter", ET_DOMAIN); ?></span>
                            </a>
                        </li>
                        <?php } else { ?>
                        <li class="connected-text">
                            <a href="javascript:void(0)" class="tw connected-text twitter_disconnect <?php echo $button_classes['tw']; ?>">
                                <i class="<?php echo $icon_classes['tw']; ?>"></i>
                                <span class="social-text"><?php _e("Connected to Twitter", ET_DOMAIN); ?></span>
                            </a>
                        </li>
                        <?php } ?>
                <?php } ?>
                <?php if($linkedin_login){ ?>
                    <li>
                        <a href="javascript:void(0)" class="lkin <?php echo $button_classes['tw']; ?>" data-connect="true">
                            <i class="<?php echo $icon_classes['lkin']; ?>"></i>
                            <span class="social-text"><?php _e("Linkedin", ET_DOMAIN) ?></span>
                        </a>
                    </li>
                <?php } ?>
            </ul>
            <?php
            if( $after_text != '' ){ ?>
                <div class="socials-footer"><?php echo $after_text ?></div>
            <?php }
        }
    }
}
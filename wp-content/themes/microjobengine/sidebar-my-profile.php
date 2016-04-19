<?php
global $wp_query, $ae_post_factory, $post, $current_user, $user_ID;
$user = mJobUser::getInstance();
$user_data = $user->convert($current_user->data);
$user_role = ae_user_role($current_user->ID);

// Convert profile
$profile_obj = $ae_post_factory->get('mjob_profile');
$profile_id = get_user_meta($user_ID, 'user_profile_id', true);
if($profile_id) {
    $post = get_post($profile_id);
    if($post && !is_wp_error($post)) {
        $profile = $profile_obj->convert($post);
    }
}

$country_id = isset($profile->tax_input['country'][0]) ? $profile->tax_input['country'][0]->term_id : '';
$languages = isset($profile->tax_input['language']) ? $profile->tax_input['language'] : '';
$display_name = isset($user_data->display_name) ? $user_data->display_name : '';
?>

<div class="box-shadow">
    <?php
    if(is_page_template('page-profile.php')) {
        ?>
        <div class="personal-profile">
            <div class="float-center profile-avatar">
                <div class="upload-profile-avatar">
                    <div class="back-top-hover"><i class="fa fa-upload"></i></div>
                     <a href="#" class="">
                        <?php
                        echo mJobAvatar($user_ID, 75);
                        ?>
                    </a>
                </div>
            </div>
            <h4 class="float-center">
                <div id="display_name">
                    <div class="text-content" data-edit="user" data-id="#display_name" data-name="display_name" data-type="input"><?php echo $display_name; ?></div>
                </div>

                <div class="user-email">
                    <p><?php echo $user_data->user_email; ?></p>
                </div>
            </h4>
            <div class="line">
                <span class="line-distance"></span>
            </div>
            <ul>
                <li class="location clearfix">
                    <span><i class="fa fa-map-marker"></i><?php _e('From', ET_DOMAIN); ?></span>
                    <div class="chosen-location">
                        <?php
                        // Show countries
                        ae_tax_dropdown('country', array(
                            'id' => 'country',
                            'class' => 'chosen-single is-chosen',
                            'hide_empty' => false,
                            'show_option_all' => __('Select your country', ET_DOMAIN),
                            'selected' => $country_id,
                        ));
                        ?>
                    </div>
                </li>

                <li class="language clearfix">
                    <span><i class="fa fa-globe"></i><?php _e('Languages', ET_DOMAIN); ?></span>
                    <div class="choose-language">
                        <?php
                        // Show languages
                        $temp_languages = array();
                        if(!empty($languages)) {
                            foreach($languages as $language) {
                                $temp_languages[] = $language->term_id;
                            }
                        }

                        ae_tax_dropdown( 'language' , array(
                            'attr' => 'multiple data-placeholder="'.__("Add your languages", ET_DOMAIN).'"',
                            'class' => 'multi-tax-item is-chosen',
                            'hide_empty' => false,
                            'hierarchical' => true ,
                            'id' => 'language' ,
                            'show_option_all' => false,
                            'selected' =>$temp_languages
                        ));
                        ?>
                    </div>
                </li>
            </ul>
        </div>
        <?php
    }
    ?>
</div>
<div class="block-statistic">
    <div class="dropdown">
        <button class="button-dropdown-menu" id="dLabel" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            Categories
            <span class="caret"></span>
        </button>
        <ul class="dropdown-menu">
            <li class="hvr-wobble-horizontal"><a href="<?php echo et_get_page_link('revenues'); ?>"><?php _e('Revenues', ET_DOMAIN); ?></a></li>
            <li class="hvr-wobble-horizontal"><a href="<?php echo et_get_page_link('dashboard') . '#analytics' ?>"><?php _e('Analytics', ET_DOMAIN); ?></a></li>
            <li class="hvr-wobble-horizontal"><a href="<?php echo et_get_page_link('my-list-order'); ?>"><?php _e('My orders & tasks', ET_DOMAIN); ?></a></li>
            <li class="hvr-wobble-horizontal"><a href="<?php echo et_get_page_link('my-listing-jobs'); ?>"><?php _e('My jobs', ET_DOMAIN); ?></a></li>
            <li class="line-distance"></li>
            <li class="hvr-wobble-horizontal"><a href="<?php echo et_get_page_link('payment-method') ?>"><?php _e('Payment method', ET_DOMAIN); ?></a></li>
            <li class="hvr-wobble-horizontal"><a href="<?php echo et_get_page_link('change-password') ?>"><?php _e('Change password', ET_DOMAIN); ?></a></li>
        </ul>
    </div>
</div>
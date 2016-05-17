<?php
global $wp_query, $ae_post_factory, $post, $current_user, $user_ID;
// If public profile
if(is_author()) {
    $user_id = get_query_var('author');
    $user = mJobUser::getInstance();
    $user_data = $user->get($user_id);
} else {
    $user_id = $current_user->ID;
    $user = mJobUser::getInstance();
    $user_data = $user->convert($current_user->data);
}

// Convert profile
$profile_obj = $ae_post_factory->get('mjob_profile');
$profile_id = get_user_meta($user_id, 'user_profile_id', true);
if($profile_id) {
    $post = get_post($profile_id);
    if($post && !is_wp_error($post)) {
        $profile = $profile_obj->convert($post);
    }
}

// User profile information
$description = !empty($profile->profile_description) ? nl2br($profile->profile_description) : "";
$display_name = isset($user_data->display_name) ? $user_data->display_name : '';
$country_name = isset($profile->tax_input['country'][0]) ? $profile->tax_input['country'][0]->name : '';
$languages = isset($profile->tax_input['language']) ? $profile->tax_input['language'] : '';

// User payment and billing information
$payment_info = !empty($profile->payment_info) ? nl2br($profile->payment_info) : "";
$billing_full_name = !empty($profile->billing_full_name) ? $profile->billing_full_name : "";
$billing_full_address = !empty($profile->billing_full_address) ? nl2br($profile->billing_full_address) : "";
$billing_country = !empty($profile->billing_country) ? $profile->billing_country : '';
$billing_vat = !empty($profile->billing_vat) ? $profile->billing_vat : "";
?>
<div class="personal-profile box-shadow">
    <div class="float-center">
        <?php
        echo mJobAvatar($user_id, 75);
        ?>
    </div>
    <h4 class="float-center"><?php echo $display_name; ?></h4>

    <?php if(is_page_template('page-dashboard.php')) : ?>
        <div class="user-email">
            <p><?php echo $user_data->user_email; ?></p>
        </div>
    <?php endif; ?>

    <div class="line">
        <span class="line-distance"></span>
    </div>

    <?php if(!is_page_template('page-dashboard.php')): ?>
    <div class="vote">
        <div class="rate-it star" data-score="<?php echo mJobUserCountReview($user_id); ?>"></div>
    </div>
    <?php endif; ?>

    <ul class="profile">
        <li class="location clearfix">
            <div class="pull-left">
                <span><i class="fa fa-map-marker"></i><?php _e('From ', ET_DOMAIN) ?></span>
            </div>
            <div class="pull-right">
                <?php echo $country_name; ?>
            </div>
        </li>
        <li class="language clearfix">
            <div class="pull-left">
                <span><i class="fa fa-globe"></i><?php _e('Languages ', ET_DOMAIN); ?></span>
            </div>
            <div class="pull-right">
                <?php
                if(!empty($languages)) {
                    foreach($languages as $language) {
                        ?>
                        <p class="lang-item"><?php echo $language->name; ?></p>
                        <?php
                    }
                }
                ?>
            </div>

        </li>
        <li class="bio clearfix">
            <span> <i class="fa fa-info-circle"></i><?php _e('Bio', ET_DOMAIN); ?></span>
            <?php
                if(is_author()) {
                    $total_words = str_word_count($description);
                    ?>
                    <div class="content-bio <?php echo ($total_words > 50) ? 'hidden-bio gradient' : ''; ?>">
                        <?php
                        echo $description;
                        ?>
                    </div>
                    <?php


                    if($total_words > 50) {
                        echo '<a href="#" class="show-bio">'. __('Show more', ET_DOMAIN) .'</a>';
                    }
                } else {
                    ?>
                    <div class="content-bio">
                        <?php echo wp_trim_words($description, 50, '...'); ?>
                    </div>
                    <?php
                }
            ?>
        </li>

        <?php
            /**
             * Show information for public profile
             */
            if(is_author()) {
                ?>
                <li class="clearfix">
                    <span class="title-info"> <i class="fa fa-money"></i><?php _e('Payment info', ET_DOMAIN); ?></span>
                    <p class="payment-info">
                        <?php echo $payment_info; ?>
                    </p>
                </li>

                <li class="clearfix">
                    <span class="title-info"> <i class="fa fa-home"></i><?php _e('Billing info', ET_DOMAIN); ?></span>
                    <ul class="public-information">
                        <li>
                            <div class="cate-title"><?php _e('Business full name', ET_DOMAIN); ?></div>
                            <p><?php echo $billing_full_name; ?></p>
                        </li>
                        <li>
                            <div class="cate-title"><?php _e('Full Address', ET_DOMAIN); ?></div>
                            <p><?php echo $billing_full_address; ?></p>
                        </li>
                        <li>
                            <div class="cate-title"><?php _e('VAT or Tax Number', ET_DOMAIN); ?></div>
                            <p><?php echo $billing_vat; ?></p>
                        </li>
                    </ul>
                </li>
                <?php
            }
        ?>
    </ul>

    <?php if(is_page_template('page-dashboard.php')) { ?>
        <div class="edit-profile"><a href="<?php echo et_get_page_link('profile'); ?>"><i class="fa fa-pencil-square-o"></i><?php _e('Edit your profile', ET_DOMAIN); ?></a></div>
    <?php } else { ?>
        <div class="link-personal">
            <ul>
                <?php mJobShowContactLink($user_id); ?>
            </ul>
        </div>
    <?php } ?>
</div>
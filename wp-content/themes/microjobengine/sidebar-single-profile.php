<?php
global $wp_query, $ae_post_factory, $post, $user_ID;
// Get author data
$user_id = $post->post_author;

if($user_id == $user_ID) {
    $seller_id = get_post_meta($post->ID, 'seller_id', true);
    if(!empty($seller_id)) {
        $user_id = $seller_id;
    }
}
$user = mJobUser::getInstance();
$user_data = $user->get($user_id);

// Convert profile
$profile = mJobProfileAction()->getProfile($user_id);

// Get the other posts
$other_posts = mJobProfileAction()->getOtherPosts($user_id, 5, array($post->ID) );

// User profile information
$description = !empty($profile->profile_description) ? $profile->profile_description : "";
$display_name = $profile->first_name . ' '. $profile->last_name_initial;
$country_name = isset($profile->tax_input['country'][0]) ? $profile->tax_input['country'][0]->name : '';
$languages = isset($profile->tax_input['language']) ? $profile->tax_input['language'] : '';
?>
<!--<div class="box-aside">-->
    <div class="personal-profile">
        <div class="float-center">
            <?php
            echo mJobAvatar($user_id, 75);
            ?>
        </div>
        <h4 class="float-center"><?php echo $display_name; ?></h4>
        <p class="company-description"><?php echo $profile->company_description; ?></p>
        <div class="line">
            <span class="line-distance"></span>
        </div>
        <ul class="profile">
            <li class="location clearfix">
                <div class="pull-left">
                    <span><i class="fa fa-map-marker"></i><?php _e('Main Offices ', ET_DOMAIN) ?></span>
                </div>
                <div class="pull-right">
                    <?php echo $profile->company_city; ?>
                </div>
            </li>
            <li class="location clearfix">
                <div class="pull-left">
                    <span><i class="fa fa-clock-o" aria-hidden="true"></i><?php _e('Year Establisted ', ET_DOMAIN) ?></span>
                </div>
                <div class="pull-right">
                    <?php echo $profile->company_year_established; ?>
                </div>
            </li>
            <li class="location clearfix">
                <div class="pull-left">
                    <span><i class="fa fa-users" aria-hidden="true"></i><?php _e('Employees ', ET_DOMAIN) ?></span>
                </div>
                <div class="pull-right">
                    <?php echo $profile->company_amount_of_employees; ?>
                </div>
            </li>
            <?php
            /**
             * Show information for public profile
             */
            if(is_author()) {
                ?>
                <li class="clearfix">
                    <span> <i class="fa fa-money"></i><?php _e('Payment info', ET_DOMAIN); ?></span>
                    <p>
                        <?php echo $payment_info; ?>
                    </p>
                </li>

                <li class="clearfix">
                    <span> <i class="fa fa-home"></i><?php _e('Billing info', ET_DOMAIN); ?></span>
                    <ul>
                        <li>
                            <div class="cate-title"><?php _e('Business full name', ET_DOMAIN); ?></div>
                            <p><?php echo $billing_full_name; ?></p>
                        </li>
                        <li>
                            <div class="cate-title"><?php _e('Full Address', ET_DOMAIN); ?></div>
                            <p><?php echo $billing_full_address; ?></p>
                        </li>
                        <li>
                            <div class="cate-title"><?php _e('Country', ET_DOMAIN); ?></div>
                            <?php
                            $country = get_term($billing_country);
                            echo '<p>'. $country->name .'</p>';
                            ?>
                        </li>
                        <li>
                            <div class="cate-title"><?php _e('VAT Number (USA)', ET_DOMAIN); ?></div>
                            <p><?php echo $billing_vat; ?></p>
                        </li>
                    </ul>
                </li>
                <?php
            }
            ?>

        </ul>

        <div class="link-personal">
            <h4 class="order-listing">Other Listings</h4>
            <?php
            global $ae_post_factory;
            $obj = $ae_post_factory->get('mjob_post');
            foreach ($other_posts as $post):
                $post = $obj->convert($post);
                ?>
            <div class="row other-listing-item other-listing-item-new">
                <div class="col-md-4">
                    <a href="<?php echo $post->permalink; ?>"><img src="<?php echo $post->the_post_thumbnail; ?>" alt="<?php echo $post->post_title; ?>" class="img-response related-mjob-img" /></a>
                </div>
                <div class="col-md-8">
                    <span class="relate-mjob-post-title">"<?php echo $post->post_title?>"<i><?php _e(' in ', ET_DOMAIN); ?></i></span>
                    <a class="mjob-category-link" href="<?php echo get_term_link($post->mjob_category['0'], 'mjob_category'); ?>"> <?php echo $post->mjob_category_name; ?></a>
                </div>
            </div>
            <?php endforeach;?>
        </div>
    </div>
<!--</div>-->

<?php wp_reset_query(); ?>
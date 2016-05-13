<?php
/**
 * Step 1 select pricing to post a service
 *
 * @param void
 * @return void
 * @since 1.0
 * @package MicrojobEngine
 * @category void
 * @author JACK BUI
 */
global $user_ID, $ae_post_factory;
$ae_pack = $ae_post_factory->get('pack');
$packs = $ae_pack->fetch('pack');
$package_data = AE_Package::get_package_data( $user_ID );
$orders = AE_Payment::get_current_order($user_ID);
?>
<ul class="post-job step-wrapper step-plan" id="step1">
    <p class="note-plan"><?php _e('Choose your pricing plan', ET_DOMAIN); ?></p>
    <?php
    if( empty($packs) ){
        $admin_email = get_option('admin_email');
        $admin_email = '<a href="mailto:'.$admin_email.'">'.$admin_email.'</a>';
        if( is_super_admin() ){
            $url = get_admin_url().'admin.php?page=et-settings#section/payment-settings';
            echo sprintf(__('Currently, this function is not available. You should setup the mJob package <a href="%s">here</a>', ET_DOMAIN), $url);
        }
        else{
            echo sprintf(__('Currently, you cannot post a Listing. For more details, please contact %s', ET_DOMAIN), $admin_email);
        }
        return;
    }
    foreach ($packs as $key => $package) {
    $number_of_post =   $package->et_number_posts;
    $sku = $package->sku;
    $text = '';
    $order = false;
    if($number_of_post >= 1 ) {
        // get package current order
        if(isset($orders[$sku])) {
            $order = get_post($orders[$sku]);
        }
        if( isset($package_data[$sku] ) && $package_data[$sku]['qty'] > 0 ) {
            /**
             * print text when company has job left in package
             */
            $number_of_post =   $package_data[$sku]['qty'];
            if($number_of_post > 1 ) {
                $text = sprintf(__("You can submit %d posts using this plan.", ET_DOMAIN) , $number_of_post);
            }
            else  {
                $text = sprintf(__("You can submit %d post using this plan.", ET_DOMAIN) , $number_of_post);
            }
        }else {
            /**
             * print normal text if company dont have job left in this package
             */
            $text = sprintf(__("You can submit %d posts using this plan.", ET_DOMAIN) , $number_of_post);
        }
    }
    $class_select = 'class="form-group package';
    if($package->et_price > 0 && isset($package_data[$sku]['qty']) && $package_data[$sku]['qty'] > 0 ) {
        $order = get_post($orders[$sku]);
        if( $order && !is_wp_error( $order ) ){
            $class_select .= ' auto-select '.$order->post_status ;
        }
    }
    $class_select .= '"';
    ?>
    <li <?php echo $class_select; ?> data-sku="<?php echo $package->sku ?>" data-id="<?php echo $package->ID ?>" data-price="<?php echo $package->et_price; ?>"
        <?php if( $package->et_price ) { ?>
            data-label="<?php printf(__("You have selected: %s", ET_DOMAIN) , $package->post_title ); ?>"
        <?php } else { ?>
            data-label="<?php _e("You are currently using the 'Free' plan", ET_DOMAIN); ?>"
        <?php } ?>>
        <a class="select-plan" >
            <p class="name-package">
                <span class="cate-package"><?php echo $package->post_title .' - '; ?></span>
                <span class="size-package"><?php if($text) { echo $text; } ?></span>
            </p>
            <div class="content-package"><?php echo $package->post_content; ?></div>
            <div class="chose-package">
               <p class="price"><?php echo mJobPriceFormat($package->et_price); ?></p>
            </div>
        </a>
    </li>
    <?php } ?>
</ul>
<?php

/**
 * Class render order list in engine themes backend
 * - list order
 * - search order
 * - load more order
 * @since 1.0
 * @author Dakachi
 */
class AE_WithdrawList
{

    /**
     * construct a user container
     */
    function __construct($args = array(), $roles = '')
    {
        $this->args = $args;
        $this->roles = $roles;
    }

    /**
     * render list of withdraws list
     */
    function render()
    {
        $withdraws = get_withdraws();
        ?>
        <div class="et-main-content order-container fre-credit-withdraw-container" id="">
            <div class="search-box et-member-search">
                <form action="">
				<span class="et-search-role">
					<select name="post_status" id="" class="et-input">
                        <option value=""><?php _e("All", ET_DOMAIN); ?></option>
                        <option value="publish"><?php _e('Publish', ET_DOMAIN); ?></option>
                        <option value="pending"><?php _e('Pending', ET_DOMAIN); ?></option>
                        <option value="draft"><?php _e('Draft', ET_DOMAIN); ?></option>
                    </select>
				</span>
				<span class="et-search-input">
					<input type="text" class="et-input order-search search" name="s" style="height: auto;" placeholder="<?php
                    _e("Search post...", ET_DOMAIN); ?>">
					<span class="icon" data-icon="s"></span>
				</span>
                </form>
            </div>
            <!-- // user search box -->

            <div class="et-main-main no-margin clearfix overview list fre-credit-withdraw-list-wrapper">
                <div class="title font-quicksand"><?php _e('All Withdraws', ET_DOMAIN) ?></div>
                <!-- order list  -->
                <ul class="list-inner list-payment list-withdraws users-list">
                    <?php
                    $withdraw_data = array();
                    if ($withdraws->have_posts()) {
                        global $post, $ae_post_factory;
                        $withdraw_obj = $ae_post_factory->get('ae_credit_withdraw');
                        while ($withdraws->have_posts()) {
                            $withdraws->the_post();
                            $convert = $withdraw_obj->convert($post);
                            $withdraw_data[] = $convert;
                            include dirname(__FILE__) . '/admin-template/withdraw-item.php';
                        }
                    } else {
                        _e('There are no payments yet.', ET_DOMAIN);
                    } ?>
                </ul>
                <div class="col-md-12">
                    <div class="paginations-wrapper">
                        <?php
                        ae_pagination($withdraws, get_query_var('paged'), 'load');
                        wp_reset_query();
                        ?>
                    </div>
                </div>
                <?php echo '<script type="data/json" class="fre_credit_withdraw_dta" >' . json_encode($withdraw_data) . '</script>'; ?>
            </div>
            <!-- //user list -->
        </div>
    <?php }
}
class Fre_Credit_WithdrawAction extends AE_PostAction
{
    function __construct($post_type = 'ae_credit_withdraw')
    {
        $this->post_type = 'ae_credit_withdraw';
        // add action fetch profile
        $this->add_ajax('fre-admin-fetch-withdraw', 'fetch_post');
        $this->add_filter('ae_convert_ae_credit_withdraw', 'fre_credit_convert_withdraw');
        $this->add_ajax('fre-admin-withdraw-sync', 'sync_withdraw');
        $this->add_filter('ae_admin_globals', 'fre_credit_decline_msg');

    }
    /**
      * filter query
      *
      * @param array $query_args
      * @return array $query_args after filter
      * @since 1.0
      * @package FREELANCEENGINE
      * @category FRE CREDIT
      * @author Jack Bui
      */
    public function filter_query_args($query_args){
        $query_args['post_status'] = array('pending', 'publish', 'draft');
        if( isset($_REQUEST['query']['post_status']) ){
            $query_args['post_status'] = $_REQUEST['query']['post_status'];
        }
        return $query_args;
    }
    /**
      * description
      *
      * @param object $result
      * @return object $result;
      * @since 1.0
      * @package FREELANCEENGINE
      * @category FRE CREDIT
      * @author Jack Bui
      */
    public function  fre_credit_convert_withdraw($result){
        $result->withdraw_edit_link = get_edit_post_link($result->ID);
        $result->withdraw_author_url = get_author_posts_url($result->post_author, $author_nicename = '');
        $result->withdraw_author_name = get_the_author_meta('display_name',$result->post_author);
        return $result;
    }
    /**
      * sync withdraw
      *
      * @param void
      * @return void
      * @since 1.0
      * @package FREELANCEENGINE
      * @category FRE CREDIT
      * @author Jack Bui
      */
    public function sync_withdraw(){
        global $ae_post_factory, $user_ID;
        $request = $_REQUEST;
        $withdraw = $ae_post_factory->get('ae_credit_withdraw');
        if(isset($request['publish']) && $request['publish'] == 1 ){
            $request['post_status'] = 'publish';
        }
        if( isset($request['archive']) && $request['archive'] == 1 ){
            $request['post_status'] = 'draft';
            unset($request['archive']);
        }
        // sync notify
        if( is_super_admin() ) {
            $result = $withdraw->sync($request);
            if( $result ) {
                $charge = AE_WithdrawHistory()->retrieveHistory($result->charge_id);
                if( $charge ){
                    $user_id = $charge->post_author;
                    $user_freezable_wallet = AE_WalletAction()->getUserWallet($user_id, 'freezable');
                    $user_withdrew_wallet = AE_WalletAction()->getUserWallet($user_id, 'withdrew');
                    $wallet = new AE_VirtualWallet($charge->amount, $charge->currency);
                    $number = AE_WalletAction()->checkBalance($user_id, $wallet, 'freezable');
                    if( $number >= 0 ){
                        if( $result->post_status == 'publish' || $result->post_status == 'draft') {
                            $user_freezable_wallet->balance = $number;
                            AE_WalletAction()->setUserWallet($user_id, $user_freezable_wallet, 'freezable');
                        }
                        if( $result->post_status == 'draft' ){
                            $user_wallet = AE_WalletAction()->getUserWallet($user_id);
                            $user_wallet->balance += $charge->amount;
                            AE_WalletAction()->setUserWallet($user_id, $user_wallet);
                            update_post_meta($charge->ID, 'history_status', 'cancelled');

                            /**
                             * Decline withdraw
                             * @since MicrojobEngine 1.0
                             */
                            do_action('ae_decline_withdraw', $result);
                        }
                        if( $result->post_status == 'publish' ){
                            /**
                             * Update withdrew
                             * @since MicrojobEngine 1.0
                             */
                            $user_withdrew_wallet->balance += $charge->amount;
                            AE_WalletAction()->setUserWallet($user_id, $user_withdrew_wallet, 'withdrew');

                            update_post_meta($charge->ID, 'history_status', 'completed');

                            /**
                             * Approve withdraw
                             * @since MicrojobEngine 1.0
                             */
                            do_action('ae_approve_withdraw', $result);
                        }
                        update_post_meta($charge->id, 'user_balance', ae_price_format(AE_WalletAction()->getUserWallet($charge->post_author)->balance));
                        $response = array(
                            'success' => true,
                            'data' => $result,
                            'msg' => __("Update withdraw successful!", ET_DOMAIN)
                        );
                    }
                    else{
                        $response = array(
                            'success' => false,
                            'msg' => __("There isn't enough money in your wallet!", ET_DOMAIN)
                        );
                    }
                }
                else{
                    $response = array(
                        'success' => false,
                        'msg' => __("There isn't any charge for this withdraw request!", ET_DOMAIN)
                    );
                }
            }
            else{
                $response = array(
                    'success'=>false,
                    'msg'=> __('Update failed!', ET_DOMAIN)
                );
            }
        }
        else{
            $response = array(
                'success'=>false,
                'msg'=> __('Please login to your administrator to update withdraw!', ET_DOMAIN)
            );
        }
        wp_send_json($response);
    }
    /**
      * decline msg
      *
      * @param array $vars
      * @return array $vars
      * @since 1.0
      * @package FREELANCEENGINE
      * @category FRE CREDIT
      * @author Jack Bui
      */
    public function fre_credit_decline_msg($vars){
        $vars['confirm_message'] = __('Are you sure to decline this request?', ET_DOMAIN);
        return $vars;
    }
}
/**
 * add footer template
 *
 * @param void
 * @return void
 * @since 1.0
 * @package FREELANCEENGINE
 * @category FRE CREDIT
 * @author Jack Bui
 */
function fre_credit_admin_footer_function() {
    include_once dirname(__FILE__) . '/admin-template/withdraw-item-js.php';
}
add_action('admin_footer', 'fre_credit_admin_footer_function');
/**
 * get withdraws list
 *
 * @param array $args
 * @return WP_QUERY $withdraw_query
 * @since 1.0
 * @package FREELANCEENGINE
 * @category FRE CREDIT
 * @author Jack Bui
 */
function get_withdraws($args = array()){
    $default_args = array(
        'paged' => 1,
        'post_status' => array(
            'pending',
            'publish',
            'draft'
        )
    );
    $args = wp_parse_args($args, $default_args);
    $args['post_type'] = 'ae_credit_withdraw';
    $withdraw_query = new WP_Query($args);
    return $withdraw_query;
}
new Fre_Credit_WithdrawAction();
<?php

/**
 * Class render order list in engine themes backend
 * - list order
 * - search order
 * - load more order
 * @since 1.0
 * @author Dakachi
 */
class mJobOrderList
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
        $mjobOrders = get_mjobOrders();
        ?>
        <div class="et-main-content order-container mjob-order-container" id="">
            <div class="search-box et-member-search">
                <form action="">
				<span class="et-search-role">
					<select name="post_status" id="" class="et-input">
                        <option value=""><?php _e("All", ET_DOMAIN); ?></option>
                        <option value="publish"><?php _e('Publish', ET_DOMAIN); ?></option>
                        <option value="pending"><?php _e('Pending', ET_DOMAIN); ?></option>
                        <option value="draft"><?php _e('Draft', ET_DOMAIN); ?></option>
                        <option value="late"><?php _e('Late', ET_DOMAIN); ?></option>
                        <option value="delivery"><?php _e('Delivery', ET_DOMAIN); ?></option>
                        <option value="disputing"><?php _e('Disputing', ET_DOMAIN); ?></option>
                        <option value="disputed"><?php _e('Disputed', ET_DOMAIN); ?></option>
                        <option value="finished"><?php _e('Finished', ET_DOMAIN); ?></option>
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

            <div class="et-main-main no-margin clearfix overview list mjob-order-list-wrapper">
                <div class="title font-quicksand"><?php _e('All Microjob order', ET_DOMAIN) ?></div>
                <!-- order list  -->
                <ul class="list-inner list-payment list-mjob-orders users-list">
                    <?php if ($mjobOrders->have_posts()) {
                        global $post, $ae_post_factory;
                        $mjoborder_obj = $ae_post_factory->get('mjob_order');
                        $mjoborder_data = array();
                        while ($mjobOrders->have_posts()) {
                            $mjobOrders->the_post();
                            $convert = $mjoborder_obj->convert($post);
                            $mjoborder_data[] = $convert;
                            include TEMPLATEPATH . '/includes/modules/mJobOrder/template/mjob-order-item.php';
                        }
                    } else {
                        _e('There are no payments yet.', ET_DOMAIN);
                    } ?>
                </ul>
                <div class="col-md-12">
                    <div class="paginations-wrapper">
                        <?php
                        ae_pagination($mjobOrders, get_query_var('paged'), 'load');
                        wp_reset_query();
                        ?>
                    </div>
                </div>
                <?php echo '<script type="data/json" class="mjob_order_data" >' . json_encode($mjoborder_data) . '</script>'; ?>
            </div>
            <!-- //user list -->
        </div>
    <?php }
}
class mjobOrder_Action extends AE_PostAction
{
    function __construct($post_type = 'mjob_order')
    {
        $this->post_type = 'mjob_order';
        // add action fetch profile
        $this->add_ajax('mjob-admin-fetch-order', 'fetch_post');
        $this->add_filter('ae_convert_mjob_order', 'mjob_convert_order');
        $this->add_ajax('mjob-admin-order-sync', 'sync_order');
        $this->add_filter('ae_admin_globals', 'mjob_decline_msg');
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
    public function  mjob_convert_order($result){
        $result->mjob_order_edit_link = get_edit_post_link($result->ID);
        $result->mjob_order_author_url = get_author_posts_url($result->post_author, $author_nicename = '');
        $result->mjob_order_author_name = get_the_author_meta('display_name',$result->post_author);
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
    public function sync_order(){
        global $ae_post_factory, $user_ID;
        $request = $_REQUEST;
        $mjob_order = $ae_post_factory->get('mjob_order');
        if(isset($request['publish']) && $request['publish'] == 1 ){
            $request['post_status'] = 'publish';
        }
        if( isset($request['archive']) && $request['archive'] == 1 ){
            $request['post_status'] = 'draft';
            unset($request['archive']);
        }
        // sync notify
        if( is_super_admin() ) {
            $result = $mjob_order->sync($request);
            if( $result ) {
                if( $result->post_status == 'draft'){
                    do_action('mjob_decline_order', $result);
                }
                $response = array(
                    'success'=>true,
                    'msg'=> __('Update success!', ET_DOMAIN ),
                    'data' => $result,
                );

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
    public function mjob_decline_msg($vars){
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
function mjob_footer_function() {
    include_once TEMPLATEPATH . '/includes/modules/mJobOrder/template/mjob-order-item-js.php';
}
add_action('admin_footer', 'mjob_footer_function');
/**
 * script
 *
 * @param void
 * @return void
 * @since 1.0
 * @package MicrojobEngine
 * @category void
 * @author JACK BUI
 */
function mjob_admin_enqueue_script($hook) {
    if( is_super_admin() ){
        wp_enqueue_script('mjob_admin_js', get_template_directory_uri() . '/includes/modules/mJobOrder/assets/mjob_admin_pluginjs.js', array(
            'underscore',
            'backbone',
            'appengine'
        ), '1.0', true);
    }
}
add_action( 'admin_enqueue_scripts', 'mjob_admin_enqueue_script' );
/**
 * get withdraws list
 *
 * @param array $args
 * @return WP_QUERY $mjob_order_query
 * @since 1.0
 * @package FREELANCEENGINE
 * @category FRE CREDIT
 * @author Jack Bui
 */
function get_mjobOrders($args = array()){
    $default_args = array(
        'paged' => 1,
        'post_status' => array(
            'publish',
            'late',
            'draft',
            'pending',
            'delivery',
            'disputing',
            'disputed',
            'finished'
        )
    );
    $args = wp_parse_args($args, $default_args);
    $args['post_type'] = 'mjob_order';
    $mjob_order_query = new WP_Query($args);
    return $mjob_order_query;
}
new mjobOrder_Action();
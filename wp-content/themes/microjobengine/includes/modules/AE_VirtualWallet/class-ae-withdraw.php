<?php
class AE_Credit_Withdraw extends AE_Posts
{
    public static $instance;

    /**
     * getInstance method
     *
     */
    public static function getInstance()
    {
        if (!self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * The constructor
     *
     * @param string $post_type
     * @param array $taxs
     * @param array $meta_data
     * @param array $localize
     * @return void void
     *
     * @since 1.0
     * @author Jack Bui
     */
    public function __construct($post_type = '', $taxs = array(), $meta_data = array(), $localize = array())
    {
        parent::__construct('ae_credit_withdraw', $taxs, $meta_data, $localize);
    }
    /**
     * init for this class
     *
     * @param void
     * @return void
     * @since 1.0
     * @package FREELANCEENGINE
     * @category FRE CREDIT
     * @author Jack Bui
     */
    public function init()
    {
        $this->fre_credit_register_post_type();
    }
    /**
     * register post type
     *
     * @param void
     * @return void
     * @since 1.0
     * @package FREELANCEENGINE
     * @category FRE CREDIT
     * @author Jack Bui
     */
    public function  fre_credit_register_post_type(){

        register_post_type('AE_Credit_Withdraw', array(
            'labels' => array(
                'name' => __('Credit Withdraw', ET_DOMAIN) ,
                'singular_name' => __('Credit Withdraw', ET_DOMAIN) ,
                'add_new' => __('Add New', ET_DOMAIN) ,
                'add_new_item' => __('Add New Credit Withdraw', ET_DOMAIN) ,
                'edit_item' => __('Edit Credit Withdraw', ET_DOMAIN) ,
                'new_item' => __('New Credit Withdraw', ET_DOMAIN) ,
                'all_items' => __('All Credit Withdraws', ET_DOMAIN) ,
                'view_item' => __('View Credit withdraw', ET_DOMAIN) ,
                'search_items' => __('Search Credit withdraws', ET_DOMAIN) ,
                'not_found' => __('No Credit withdraw found', ET_DOMAIN) ,
                'not_found_in_trash' => __('No Credit withdraws found in Trash', ET_DOMAIN) ,
                'parent_item_colon' => '',
                'menu_name' => __('Credit Withdraws', ET_DOMAIN)
            ) ,
            'public' => false,
            'publicly_queryable' => true,
            'show_ui' => true,
            'show_in_menu' => false,
            'show_in_nav_menus' => false,
            'query_var' => true,
            'rewrite' => true,

            'capability_type' => 'post',
            // 'capabilities' => array(
            //     'manage_options'
            // ) ,
            'has_archive' => 'packs',
            'hierarchical' => false,
            'menu_position' => null,
            'supports' => array(
                'title',
                'editor',
                'author',
                'custom-fields'
            )
        ));
        global $ae_post_factory;
        $tax = array();
        $meta = array(
            'amount',
            'currency',
            'charge_id',
            'reject_message'
        );
        $ae_post_factory->set('ae_credit_withdraw', new AE_Posts('ae_credit_withdraw', $tax, $meta));
    }
    /**
      * convert
      *
      * @param array $post_data,
      * @param string $thumbnail
      * @param bool $excerpt = true,
      * @param bool $singular = false
      * @return void
      * @since 1.0
      * @package FREELANCEENGINE
      * @category FRE CREDIT
      * @author Jack Bui
      */
    public function convert($post_data, $thumbnail = 'medium_post_thumbnail', $excerpt = true, $singular = false) {
        $result = parent::convert($post_data, $thumbnail, $excerpt, $singular);
        $result->withdraw_edit_link = get_edit_post_link($result->ID);
        $result->withdraw_author_url = get_author_posts_url($result->post_author, $author_nicename = '');
        $result->withdraw_author_name = get_the_author_meta('display_name',$result->post_author);
        return $result;

    }
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
    public function get_withdraws($args = array()){
        $default_args = array(
            'paged' => 1,
            'post_status' => array(
                'pending',
                'publish',
                'draft'
            )
        );
        $args = wp_parse_args($args, $default_args);
        $args['post_type'] = 'AE_Credit_Withdraw';
        $withdraw_query = new WP_Query($args);
        return $withdraw_query;
    }
    /**
      * get edit post link
      *
      * @param integer $post_id
      * @return string withdraw link
      * @since 1.0
      * @package FREELANCEENGINE
      * @category FRE CREDIT
      * @author Jack Bui
      */
    public function get_withdraw_link($post_id){
        return get_edit_post_link($post_id);

    }
}
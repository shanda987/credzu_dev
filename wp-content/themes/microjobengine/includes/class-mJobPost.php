<?php
class mJobPost extends AE_Posts
{
    public static $instance;
    public $post_type;
    public $post_type_singular;
    public $post_type_regular;

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
        parent::__construct($this->post_type, $taxs, $meta_data, $localize);
    }
    /**
     * register post type
     *
     * @param array $argss
     * @return void
     * @since 1.0
     * @package FREELANCEENGINE
     * @category FRE CREDIT
     * @author Jack Bui
     */
    public function  registerPosttype($args = array())
    {
        $default = array(
            'labels' => array(
                'name' => __($this->post_type_singular, ET_DOMAIN),
                'singular_name' => __($this->post_type_singular, ET_DOMAIN),
                'add_new' => __('Add New', ET_DOMAIN),
                'add_new_item' => __('Add New ' . $this->post_type_singular, ET_DOMAIN),
                'edit_item' => __('Edit ' . $this->post_type_singular, ET_DOMAIN),
                'new_item' => __('New ' . $this->post_type_singular, ET_DOMAIN),
                'all_items' => __('All ' . $this->post_type_regular, ET_DOMAIN),
                'view_item' => __('View ' . $this->post_type_singular, ET_DOMAIN),
                'search_items' => __('Search ' . $this->post_type_regular, ET_DOMAIN),
                'not_found' => __('No ' . $this->post_type_regular . ' found', ET_DOMAIN),
                'not_found_in_trash' => __('No ' . $this->post_type_regular . ' found in Trash', ET_DOMAIN),
                'parent_item_colon' => '',
                'menu_name' => __($this->post_type_regular, ET_DOMAIN)
            ),
            'public' => true,
            'publicly_queryable' => true,
            'show_ui' => true,
            'show_in_menu' => true,
            'query_var' => true,
            'rewrite' => array(
                'slug' => ae_get_option($this->post_type.'_slug', $this->post_type)
            ) ,
            'capability_type' => 'post',
            // 'capabilities' => array(
            //     'manage_options'
            // ) ,
            'has_archive' => ae_get_option($this->post_type.'_archive', $this->post_type),
            'hierarchical' => true,
            'menu_position' => null,
            'supports' => array(
                'title',
                'editor',
                'author',
                'custom-fields',
                'thumbnail',
                'excerpt',
                'comments'
            )
        );
        $args = wp_parse_args($args, $default);
        register_post_type($this->post_type, $args);
        flush_rewrite_rules();
        global $ae_post_factory;
        $ae_post_factory->set($this->post_type, new AE_Posts($this->post_type, $this->taxs, $this->meta));
    }
    /**
     * Register taxonomy for post
     *
     * @param void
     * @return void
     * @since 1.0
     * @package MicrojobEngine
     * @category void
     * @author JACK BUI
     */
    public function registerTaxonomy($tax = '', $tax_text_singular = '', $tax_text_regular = '', $post_type = array(), $labels = array(), $args = array()){
        /**
         * Create a taxonomy project category
         *
         * @uses  Inserts new taxonomy project category  object into the list
         */
        $labels = wp_parse_args($labels, array(
            'name' => _x($tax_text_regular, 'Taxonomy plural name', ET_DOMAIN) ,
            'singular_name' => _x($tax_text_singular, 'Taxonomy singular name', ET_DOMAIN) ,
            'search_items' => __('Search '.$tax_text_regular, ET_DOMAIN) ,
            'popular_items' => __('Popular '.$tax_text_regular, ET_DOMAIN) ,
            'all_items' => __('All '.$tax_text_regular, ET_DOMAIN) ,
            'parent_item' => __('Parent '.$tax_text_singular, ET_DOMAIN) ,
            'parent_item_colon' => __('Parent '.$tax_text_singular, ET_DOMAIN) ,
            'edit_item' => __('Edit '.$tax_text_singular, ET_DOMAIN) ,
            'update_item' => __('Update '.$tax_text_singular, ET_DOMAIN) ,
            'add_new_item' => __('Add New '.$tax_text_singular, ET_DOMAIN) ,
            'new_item_name' => sprintf(__('New %s Name', ET_DOMAIN) , $tax_text_singular),
            'add_or_remove_items' => sprintf(__('Add or remove %s', ET_DOMAIN), $tax_text_regular) ,
            'choose_from_most_used' => __('Choose from most used enginetheme', ET_DOMAIN) ,
            'menu_name' => __($tax_text_singular, ET_DOMAIN) ,
        ));
        $args = wp_parse_args( $args, array(
            'labels' => $labels,
            'public' => true,
            'show_in_nav_menus' => true,
            'show_admin_column' => true,
            'hierarchical' => true,
            'show_tagcloud' => true,
            'show_ui' => true,
            'query_var' => true,
            'rewrite' => array(
                'slug' => ae_get_option($tax.'_slug', $tax) ,
                'hierarchical' => ae_get_option($tax.'_hierarchical', false)
            ) ,
            'capabilities' => array(
                'manage_terms',
                'edit_terms',
                'delete_terms',
                'assign_terms'
            )
        ));

        register_taxonomy($tax, $post_type , $args);

    }
}

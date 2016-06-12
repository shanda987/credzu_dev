<?php
class AE_Taxonomy_Meta extends AE_Base{
    public static $instance;
    public $tax;
    public $meta;
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
     * the constructor of this class
     *
     */
    public  function __construct( $tax = 'category', $meta = array() ){
        $this->add_action($tax.'_add_form_fields', 'ae_add_form_fields');
        $this->add_action( 'created_'.$tax, 'ae_save_tax_meta', 10, 2 );
        $this->add_action( $tax .'_edit_form_fields', 'ae_edit_tax_group_field', 10, 2 );
        $this->add_action( 'edited_'.$tax, 'ae_update_tax_meta', 10, 2 );
        $this->add_filter('manage_edit-'.$tax.'_columns', 'ae_add_tax_column' );
        $this->add_filter('manage_'.$tax.'_custom_column', 'ae_add_tax_column_content', 10, 3 );
        $this->add_action( 'admin_enqueue_scripts', 'ae_tax_enqueue_scripts'  );
        $this->tax = $tax;
        $this->meta = $meta;
    }
    /**
     * Description
     *
     * @param void
     * @return void
     * @since 1.0
     * @package MicrojobEngine
     * @category void
     * @author JACK BUI
     */
    public function ae_add_form_fields($taxonomy) {
        do_action('ae_tax_meta_add_field', $taxonomy);
    }
    /**
     * save tax meta
     *
     * @param integer $term_id
     * @param integer $tt_id
     * @return void
     * @since 1.0
     * @package MicrojobEngine
     * @category void
     * @author JACK BUI
     */
    public function ae_save_tax_meta( $term_id, $tt_id ){
        $request = $_REQUEST;
        foreach( $this->meta as $key=> $value){
            if( isset($request[$value]) ){
                $group =  $request[$value] ;
                update_term_meta($term_id, $value, $group);
            }
        }
//        if( isset( $_POST['featured-tax'] ) && '' !== $_POST['featured-tax'] ){
//            $group = sanitize_title( $_POST['featured-tax'] );
//            add_term_meta( $term_id, 'featured-tax', $group, true );
//        }
//        if( isset( $_POST['mjob_category_image'] ) && '' !== $_POST['mjob_category_image'] ){
//            $group = sanitize_title( $_POST['mjob_category_image'] );
//            update_term_meta( $term_id, 'mjob_category_image', $group );
//        }
    }
    /**
     * edit form tax
     *
     * @param object $term
     * @param string $taxonomy
     * @return void
     * @since 1.0
     * @package MicrojobEngine
     * @category void
     * @author JACK BUI
     */
    function ae_edit_tax_group_field( $term, $taxonomy ){
        do_action('ae_tax_meta_edit_field', $term, $taxonomy, $this->meta);
    }
    /**
     * save edit
     *
     * @param integer $term_id
     * @param integer $tt_id
     * @return void
     * @since 1.0
     * @package MicrojobEngine
     * @category void
     * @author JACK BUI
     */
    public function ae_update_tax_meta( $term_id, $tt_id ){
//        if( isset( $_POST['featured-tax'] ) && '' !== $_POST['featured-tax'] ){
//            $group = sanitize_title( $_POST['featured-tax'] );
//            update_term_meta( $term_id, 'featured-tax', $group );
//        }
//        else{
//            update_term_meta($term_id, 'featured-tax', false);
//        }
//        if( isset( $_POST['mjob_category_image'] ) && '' !== $_POST['mjob_category_image'] ){
//            $group = sanitize_title( $_POST['mjob_category_image'] );
//            update_term_meta( $term_id, 'mjob_category_image', $group );
//        }
//        else{
//            update_term_meta($term_id, 'mjob_category_image', false);
//        }
        $request = $_REQUEST;
        foreach( $this->meta as $key=> $value){
            if( isset($request[$value]) && !empty($request[$value])  ){
                $group =  $request[$value] ;
                update_term_meta($term_id, $value, $group);
            }
            else{
                update_term_meta($term_id, $value, false);
            }
        }
    }
    /**
     * Displaying The Term Meta Data In The Term List
     *
     * @param void
     * @return void
     * @since 1.0
     * @package MicrojobEngine
     * @category void
     * @author JACK BUI
     */
    function ae_add_tax_column( $columns ){
        $columns['featured_tax'] = __( 'Featured tax', ET_DOMAIN );
        return $columns;
    }
    /**
     * update
     *
     * @param void
     * @return void
     * @since 1.0
     * @package MicrojobEngine
     * @category void
     * @author JACK BUI
     */
    function ae_add_tax_column_content( $content, $column_name, $term_id ){
        global $featured_tax, $mjob_category_image;
        if( $column_name !== 'featured_tax' || $column_name !== 'mjob_category_image' ){
            return $content;
        }
        $term_id = absint( $term_id );
        $featured_tax = get_term_meta( $term_id, 'featured-tax', true );
        if( !empty( $featured_tax ) ){
            $content .= esc_attr( $featured_tax );
        }
        $mjob_category_image = get_term_meta( $term_id, 'mjob_category_image', true );
        $content.='<img id="ae-tax-images-photo" src="'.esc_url( wp_get_attachment_image_url( $mjob_category_image, 'full' ) ).'"<?php echo $hidden; ?> />';
        return $content;
    }
    /**
     * enqueue script
     *
     * @param void
     * @return void
     * @since 1.0
     * @package MicrojobEngine
     * @category void
     * @author JACK BUI
     */
    public function ae_tax_enqueue_scripts(){
        wp_enqueue_media();
        wp_enqueue_style( 'ae-tax-images-css',  get_template_directory_uri() . '/includes/modules/AE_Taxonomy_Meta/assets/ae-tax.css', array(), ET_VERSION);
        wp_enqueue_script( 'ae-tax-images', get_template_directory_uri() . '/includes/modules/AE_Taxonomy_Meta/assets/ae-tax.js',   array(
            'jquery',
            'underscore',
            'backbone',
            'appengine'
        ), 1.0, true );
        $term_id = ! empty( $_GET['tag_ID'] )
            ? (int) $_GET['tag_ID']
            : 0;
        // Localize
        wp_localize_script( 'ae-tax-images', 'i10n_WPTermImages', array(
            'insertMediaTitle' => esc_html__( 'Choose an Image', 'wp-user-avatars' ),
            'insertIntoPost'   => esc_html__( 'Set as image',    'wp-user-avatars' ),
            'deleteNonce'      => wp_create_nonce( 'remove_ae_tax_images_nonce' ),
            'mediaNonce'       => wp_create_nonce( 'assign_ae_tax_images_nonce' ),
            'term_id'          => $term_id,
        ) );
    }
    /**
      * convert
      *
      * @param object $term
      * @return void
      * @since 1.4
      * @package MicrojobEngine
      * @category CREDZU
      * @author JACK BUI
      */
    public function convert($term){
        foreach( $this->meta as $key=>$value ){
            $val = get_term_meta($term->term_id, $value, true);
            $term->$value = $val;
        }
        return apply_filters('jb_convert_'.$term->taxonomy, $term);
    }

}
/**
 * class AE_PostFact
 * factory class to generate ae post object
 */
class AE_TaxFact
{

    static $objects;

    /**
     * contruct init post type
     */
    function __construct() {
        self::$objects = array(
            'tax' => AE_Taxonomy_Meta::getInstance()
        );
    }

    /**
     * set a post type object to machine
     * @param String $post_type
     * @param AE_Post object $object
     */
    public function set($tax, $object) {
        self::$objects[$tax] = $object;
    }

    /**
     * get post type object in class object instance
     * @param String $post_type The post type want to use
     * @return Object
     */
    public function get($tax) {
        if (isset(self::$objects[$tax])) return self::$objects[$tax];
        return null;
    }
    /**
     * Description
     *
     * @param void
     * @return void
     * @since 1.0
     * @package MicrojobEngine
     * @category void
     * @author JACK BUI
     */
    public function get_all(){
        if ( isset( self::$objects ) ) {
            return self::$objects;
        }
        return NULL;
    }
}

/**
 * set a global object factory
 */
global $ae_tax_factory;
$ae_tax_factory = new AE_TaxFact();
$ae_tax_factory->set('category', new AE_Posts('category'));
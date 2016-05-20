<?php
class mJobPosttype extends mJobPost{
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
     * the constructor of this class
     *
     */
    public  function __construct($post_type = '', $taxs = array(), $meta_data = array(), $localize = array()){
        $this->post_type = 'mjob_post';
        parent::__construct( $this->post_type, $taxs, $meta_data, $localize);
        $this->meta = array(
            'time_delivery',
            'et_payment_package',
            'et_price',
            'et_budget',
            'rating_score',
            'total_orders',
            'total_reviews',
            'et_carousels',
            // The Options are for what a Company needs from a client
            'option_credit_report_upload',
            'option_credit_report_credentials',
            'option_utility_bill',
            'option_contact_information', // First, Last, Address, Phone, etc.
            'option_social_security_card',
            'option_government_issued_id',
            'option_billing_information',
            'modified_date'
        );
        $this->post_type_singular = 'Microjob';
        $this->post_type_regular = 'Microjobs';
        /**
         * the constructor of this class
         *
         */
        $tax = 'mjob_category';
        $tax_text_singular = 'Microjob Category';
        $tax_text_regular = 'Microjob Categories';
        $this->registerTaxonomy($tax, $tax_text_singular, $tax_text_regular, array( $this->post_type ));
        /**
         * Register skill taxonomy
         *
         */
        $status = false;
        $switch_skill = ae_get_option('switch_skill');
        if($switch_skill){
            $status = true;
        }
        $tax = 'skill';
        $tax_text_singular = 'Tag';
        $tax_text_regular = 'Tags';
        $labels = array();
        $args = array(
            'hierarchical'=> $status,
//            'rewrite' => array(
//                'slug' => 'mjob_tag' ,
//                'hierarchical' => 'mjob_tag_hierarchical'
//            ) ,
            );
        $this->registerTaxonomy($tax, $tax_text_singular, $tax_text_regular, array( $this->post_type ), $labels, $args);
        $this->taxs = array(
            'mjob_category',
            'skill'
        );
    }
    /**
     * init function
     *
     * @param void
     * @return void
     * @since 1.0
     * @package MicrojobEngine
     * @category void
     * @author JACK BUI
     */
    public function init(){
        $this->registerPosttype();
    }
    /**
      * override convert function
      *
      * @param object $post_data
     * @param string $thumbnail
     * @param boolean $excerpt
     * @param boolean $singular
      * @return void
      * @since 1.0
      * @package FREELANCEENGINE
      * @category FRE CREDIT
      * @author Jack Bui
      */
    public function convert( $post_data, $thumbnail = 'thumbnail', $excerpt = TRUE, $singular = FALSE ) {
        $data = parent::convert($post_data, $thumbnail, $excerpt, $singular);
        $data->post_content = $data->unfiltered_content;
        get_post_custom();
        return $data;
    }
}
$new_instance = mJobPosttype::getInstance();
$new_instance->init();
<?php
class mJobProfile extends mJobPost{
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
        $this->post_type = 'mjob_profile';
        parent::__construct( $this->post_type, $taxs, $meta_data, $localize);
        $this->meta = array(
            'rating_score',
            'payment_info',
            'billing_full_name',
            'billing_full_address',
            'billing_country',
            'billing_vat',
            'status',
            'time_delivery',
            'profile_description',
            'first_name',
            'last_name',
            'phone',
            'business_email',
            'address_line2',
            'city',
            'state',
            'zip_code',
            'billing_address_line2',
            'billing_city',
            'billing_state',
            'billing_zip_code',
            'credit_goal',
            'signature',
            'company_status',
            'company_name',
            'company_address',
            'company_phone',
            'company_email',
            'company_website',
            'company_year_established',
            'company_amount_of_employees',
            'company_description',
//            'company_first_name',
            'account_number',
            'routing_number',
            'billing_other_address',
            'account_holder',
            'use_holder_account',
            'use_billing_address',
            'bank_name',
            'wrong_request',
            'time_wrong_request'

        );
        $this->post_type_singular = 'Profile';
        $this->post_type_regular = 'Profiles';

        /**
         * Register taxonomies
         */
        // Country
        $tax = 'country';
        $tax_text_singular = 'Country';
        $tax_text_regular = 'Countries';
        $labels = array();
        $args = array('hierarchical'=> true);
        $this->registerTaxonomy($tax, $tax_text_singular, $tax_text_regular, array( $this->post_type ), $labels, $args);

        // Language
        $tax = 'language';
        $tax_text_singular = 'Language';
        $tax_text_regular = 'Languages';
        $labels = array();
        $args = array('hierarchical'=> true);
        $this->registerTaxonomy($tax, $tax_text_singular, $tax_text_regular, array( $this->post_type ), $labels, $args);

        $this->taxs = array(
            'country',
            'language'
        );
    }
    /**
     * init function
     *
     * @param void
     * @return void
     * @since 1.0
     * @package MicrojobEngine
     * @category Profile
     * @author Tat Thien
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
     * @package MicrojobEngine
     * @category Profile
     * @author Tat Thien
     */
    public function convert( $post_data, $thumbnail = 'thumbnail', $excerpt = TRUE, $singular = FALSE ) {
        $data = parent::convert($post_data, $thumbnail, $excerpt, $singular);
        $data->post_content = $data->unfiltered_content;
        return $data;
    }
}
$new_instance = mJobProfile::getInstance();
$new_instance->init();
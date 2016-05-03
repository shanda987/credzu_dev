<?php
class agreementPostType extends mJobPost{
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
        $this->post_type = 'mjob_agreement';
        parent::__construct( $this->post_type, $taxs, $meta_data, $localize);
        $this->meta = array(
        );
        $this->post_type_singular = 'Agreement';
        $this->post_type_regular = 'Agreements';
        $this->taxs = array(
            'mjob_category'
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
        register_taxonomy_for_object_type('mjob_category', 'mjob_agreement');
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
        return $data;
    }
}
$new_instance = agreementPostType::getInstance();
$new_instance->init();
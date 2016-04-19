<?php
class mJobOrderPosttype extends mJobPost{
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
        $this->post_type = 'mjob_order';
        parent::__construct( $this->post_type, $taxs, $meta_data, $localize);
        $this->meta = array(
            'extra_ids',
            'amount',
            'real_amount',
            'currency',
            'payment_type',
            'paid',
            'order_delivery',
            'seller_id',
            'reject_message',
            'order_delivery_day'
        );
        $this->post_type_singular = 'Microjob Order';
        $this->post_type_regular = 'Microjob Orders';
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
        $args = array(
            'hierarchical' => false,
            //'show_in_menu' => false,
            );
        $this->registerPosttype($args);
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
        return $data;
    }
}
$new_instance = mJobOrderPosttype::getInstance();
$new_instance->init();
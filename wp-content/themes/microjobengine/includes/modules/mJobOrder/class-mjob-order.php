<?php
/**
 * Created by PhpStorm.
 * User: Jack Bui
 * Date: 1/21/2016
 * Time: 2:23 PM
 */
class mJobOrder extends ET_Order{
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
    public  function __construct($order = array(), $ship = array()){
        parent::__construct($order, $ship);
        if( is_array($order) ) {
            $this->_ID = $order['ID'];
        }
        else{
            $this->_ID = intval($order);
        }
    }
    static function register_order_post_type()
    {

    }
    /**
     * add product
     *
     * @param object $product
     * @return void
     * @since 1.0
     * @package MicrojobEngine
     * @category void
     * @author JACK BUI
     */
    public function add_product($product, $number = 1) {

        $this->_products[$product->ID] = array(
            'ID' => $product->ID,
            'NAME' => $product->post_title,
            'AMT' => $product->amount,
            'QTY' => $number,
            'L_DESC' => $product->post_content,
            'TYPE' => $product->post_type
        );
        $this->_total_before_discount+= number_format($product->amount * $number, 2, '.', '');
        $this->_total = number_format($this->calculate_discount($this->_total_before_discount) , 2, '.', '');

        $this->_product_id = $product->ID;
        $args = array(
            'post_type'=> 'mjob_order',
            'post_title'=> $product->post_title
        );
        $this->_payment = $product->post_title;
        $this->update_order();
    }
    /**
     * Override parent class
     */
    function update_order() {
        parent::update_order();
        update_post_meta($this->_ID, 'et_order_plan_id', $this->_product_id);
    }
    /**
     * generate data
     *
     * @param void
     * @return void
     * @since 1.0
     * @package MicrojobEngine
     * @category void
     * @author JACK BUI
     */
    public function generate_data_to_pay() {
        $return = parent::generate_data_to_pay();
        $return['order_name'] = $this->_payment;
        $return['product_id'] = $this->_product_id;
        $return['ID'] = $this->_ID;
        return $return;
    }
    /**
     * get order data
     *
     * @param void
     * @return array $order_data
     * @since 1.0
     * @package MicrojobEngine
     * @category void
     * @author JACK BUI
     */
    public function get_order_data() {
        return array(
            'payer' => $this->_payer,

            'created_date' => $this->_created_date,
            'status' => $this->_stat,
            'payment' => $this->_payment,
            'products' => $this->_products,

            'currency' => $this->_currency,
            'payment_code' => $this->_payment_code,
            'total' => $this->_total,
            'total_before_discount' => $this->_total_before_discount,
            'discount_rate' => $this->_discount_rate,
            'discount_method' => $this->_discount_method,
            'paid_date' => $this->_paid_date,
            'shipping' => $this->_shipping
        );
    }

}
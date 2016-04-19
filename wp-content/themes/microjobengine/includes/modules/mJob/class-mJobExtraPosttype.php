<?php
class mJobExtraPosttype extends mJobPost{
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
        $this->post_type = 'mjob_extra';
        parent::__construct( $this->post_type, $taxs, $meta_data, $localize);
        $this->post_type_singular = 'Microjob Extra';
        $this->post_type_regular = 'Microjob Extras';
        $this->meta = array(
            'et_budget'
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
}
$new_instance = mJobExtraPosttype::getInstance();
$new_instance->init();
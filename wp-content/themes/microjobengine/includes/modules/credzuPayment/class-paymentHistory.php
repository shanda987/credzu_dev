<?php
class credzuPaymentHistory extends mJobPost{
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
        $this->post_type = 'payment_history';
        parent::__construct( $this->post_type, $taxs, $meta_data, $localize);
        $this->post_type_singular = 'Payment hostory';
        $this->post_type_regular = 'Payment histories';
        $this->meta = array(
            'amount',
            'mjob',
            'pdf_path'
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
       // $this->add_meta_box();
    }
    /**
     * add metabox
     *
     * @param void
     * @return void
     * @since 1.0
     * @package MicrojobEngine
     * @category void
     * @author JACK BUI
     */
    public function add_meta_box() {

        $post_type   = 'payment_history';
        $meta_box_id = $post_type . "_metabox";
        $title       = __('Payment history', ET_DOMAIN);
        $arg         = array(
            'post_type' => $post_type,
            'context'   => 'advanced',
            'priority'  => 'default',
        );
        $input       = array(
            array(
                'title' => __( 'Payment amount', ET_DOMAIN ),
                'type'  => 'text',
                'name'  => 'amount'
            ),
            array(
                'title' => __( 'PDF file path', ET_DOMAIN ),
                'type'  => 'text',
                'name'  => 'pdf_path'
            ),

        );
        new AE_Metabox( $meta_box_id, $title, $arg, $input );
    }
}
$new_instance = credzuPaymentHistory::getInstance();
$new_instance->init();
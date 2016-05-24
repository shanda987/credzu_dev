<?php
class credzuPaymentFormat extends mJobPost{
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
        $this->post_type = 'payment_format';
        parent::__construct( $this->post_type, $taxs, $meta_data, $localize);
        $this->post_type_singular = 'Payment format';
        $this->post_type_regular = 'Payment format';
        $this->meta = array(
            'company_to_credzu'
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
        $this->add_meta_box();
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
        $meta_box_id = $this->post_type . "_metabox";
        $title       = __('Extra meta', ET_DOMAIN);
        $arg         = array(
            'post_type' => $this->post_type,
            'context'   => 'advanced',
            'priority'  => 'default',
        );
        $input       = array(
            array(
                'title'=> __('This format is  "company pay for Creduz"', ET_DOMAIN),
                'type'=> 'select',
                'name'=> 'company_to_credzu',
                'choices'=> array(
                    'no',
                    'yes'
                )
            )

        );
        new AE_Metabox( $meta_box_id, $title, $arg, $input );
    }
}
$new_instance = credzuPaymentFormat::getInstance();
$new_instance->init();
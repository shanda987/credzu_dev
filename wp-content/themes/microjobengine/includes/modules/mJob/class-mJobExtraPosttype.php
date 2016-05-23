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
            'et_budget',
            'is_featured'
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

        $post_type   = 'mjob_extra';
        $meta_box_id = $post_type . "_metabox";
        $title       = __('Extra meta', ET_DOMAIN);
        $arg         = array(
            'post_type' => $post_type,
            'context'   => 'advanced',
            'priority'  => 'default',
        );
        $input       = array(
            array(
                'title' => __( 'Extra price', ET_DOMAIN ),
                'type'  => 'text',
                'name'  => 'et_budget'
            ),
            array(
                'title'=> __('Is Featured', ET_DOMAIN),
                'type'=> 'checkbox',
                'name'=> 'is_featured',
                'choices'=> array('featured')
            )

        );
        new AE_Metabox( $meta_box_id, $title, $arg, $input );
    }
}
$new_instance = mJobExtraPosttype::getInstance();
$new_instance->init();
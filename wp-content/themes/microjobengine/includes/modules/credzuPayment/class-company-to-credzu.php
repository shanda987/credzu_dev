<?php
class companyToCredzu extends AE_Base{
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
    public  function __construct(){

    }
    /**
      * Get payment format
      *
      * @param void
      * @return void
      * @since 1.4
      * @package MicrojobEngine
      * @category CREDZU
      * @author JACK BUI
      */
    public function getPaymentFormat(){
        $args = array(
            'post_type' => 'payment_format',
            'meta_query' => array(
                array(
                    'key' => 'company_to_credzu',
                    'value' => array('yes'),
                )
            ),
            'post_status'=> 'publish',
            'posts_per_page'=>1
        );
        $posts = get_posts( $args );
        if( !isset($posts['0']) ){
            return $posts['0'];
        }
        else{
            return false;
        }
    }
}
if( !function_exists('companyToCredzu') ){
    /**
      * get instance of companyToCredzu class
      *
      * @param void
      * @return object $instance
      * @since 1.4
      * @package MicrojobEngine
      * @category CREDZU
      * @author JACK BUI
      */
    function companyToCredzu(){
        return companyToCredzu::getInstance();
    }
}
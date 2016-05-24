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
      * init class
      *
      * @param void
      * @return void
      * @since 1.4
      * @package MicrojobEngine
      * @category CREDZU
      * @author JACK BUI
      */
    public function init(){
        $this->add_action('credzu_do_checkout', 'generatePaymentCheck');
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
                    'value' => 'yes',
                    'compare'=> '='
                )
            ),
            'post_status'=> 'publish',
            'posts_per_page'=>1
        );
        $posts = get_posts( $args );
        $p = '';
        global $ae_post_factory;
        $obj = $ae_post_factory->get('payment_format');
        foreach( $posts as $post){
            $p = $obj->convert($post);
            break;
        }
        return $p;
    }
    /**
      * generate  payment check
      *
      * @param object $data
      * @return void
      * @since 1.4
      * @package MicrojobEngine
      * @category CREDZU
      * @author JACK BUI
      */
    public function generatePaymentCheck($data){
        global $user_ID;
        $profile = mJobProfileAction()->getProfile($user_ID);
        $p = $this->getPaymentFormat();
        $content = '';
        if( !empty($p) ){
            $content = $p->post_content;
            $content = str_ireplace('[payment_company_name]', $profile->company_name, $content );
            $content = str_ireplace('[payment_company_phone]', $profile->company_phone, $content );
            $content = str_ireplace('[payment_company_address]', $profile->company_address, $content );
            $content = str_ireplace('[payment_company_bank_name]', $profile->bank_name, $content );
            $content = str_ireplace('[routing_number]', $profile->routing_number, $content );
            $content = str_ireplace('[account_number]', $profile->account_number, $content );
            date_default_timezone_set('US/Eastern');
            $time = date("F j, Y, g:i a");
            $content = str_ireplace('[current_time]', $time, $content );
        }
        $file_name = 'company_to_credzu_'.time();
        AE_Pdf_Creator()->init();
        $path = AE_Pdf_Creator()->pdfGenarate($content, $file_name);
        var_dump($path);
        exit;

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
companyToCredzu()->init();
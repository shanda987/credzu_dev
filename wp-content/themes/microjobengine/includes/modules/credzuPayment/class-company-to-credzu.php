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
        $check_number = (int)get_option('payment_check_number', 5000);
        $check_number = $check_number + 1;
        date_default_timezone_set('US/Eastern');
        $time = date("F j, Y, g:i a");
        $file_name = 'company_to_credzu_'.time();
        $ct = mjobCreatePdf($profile, $data, $check_number, $time);
        AE_Pdf_Creator()->init();
        $path = AE_Pdf_Creator()->pdfGenarate($ct, $file_name, true);
        if( !empty($path) ){
            if(isset($data['is_featured']) ){
                update_post_meta($data['ID'], 'is_featured', $data['is_featured']);
            }
            do_action('create_payment_history', $data, $profile, $path, $check_number);
            exit;
        }
        else{
         wp_send_json(array(
             'success'=> false,
             'msg'=> __('You got an error, Please try again!', ET_DOMAIN)
             ))   ;
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
companyToCredzu()->init();
<?php
class clientToCompany extends AE_Base
{
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
    public function __construct()
    {

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
    public function init()
    {
        $this->add_action('client_do_checkout', 'generatePaymentCheck');
    }
    public function generatePaymentCheck($data){
        global $user_ID;
        $profile = mJobProfileAction()->getProfile($user_ID);
        $check_number = (int)get_option('client_payment_check_number', 0);
        $check_number = $check_number + 1;
        date_default_timezone_set('US/Eastern');
        $time = date("F j, Y, g:i a");
        $file_name = 'client_to_company'.time();
        $ct = mjobCreateClientToCompanyPdf($profile, $data, $check_number, $time);
        AE_Pdf_Creator()->init();
        $path = AE_Pdf_Creator()->pdfGenarate($ct, $file_name, true);
        if( !empty($path) ){
            do_action('create_client_payment_history', $data, $profile, $path, $check_number);
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
if( !function_exists('clientToCompany') ){
    /**
     * get instance of clientToCompany class
     *
     * @param void
     * @return object $instance
     * @since 1.4
     * @package MicrojobEngine
     * @category CREDZU
     * @author JACK BUI
     */
    function clientToCompany(){
        return clientToCompany::getInstance();
    }
}
clientToCompany()->init();
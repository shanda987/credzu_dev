<?php
class AE_GVerify extends AE_Base{
    public static $instance;
    public $uri;
    public $url;
    protected $ApiUsername;
    protected $ApiPassword;
    public $soapClient;
    public $soapHeader;
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

    /**
      * init for this class
      *
      * @param void
      * @return void
      * @since 1.4
      * @package MicrojobEngine
      * @category CREDZU
      * @author JACK BUI
      */
    public function init($ApiUsername = '', $ApiPassword = '', $uri = '', $url = ''){
        if( empty($uri) ){
            $uri = 'https://api.giact.com/VerificationServices/V5/InquiriesWS.asmx?WSDL';
        }
        if( empty($url) ){
            $url = 'http://api.giact.com/verificationservices/v5';
        }
        $this->setUserName($ApiUsername);
        $this->setPassword($ApiPassword);
        $this->setURI($uri);
        $this->setPublishURL($url);
        $this->soapClient = new SoapClient($this->uri);
        $params = array(
            'ApiUsername'    =>    $this->ApiUsername,
            'ApiPassword'    =>    $this->ApiPassword);
        $this->soapHeader = new SoapHeader($url, 'AuthenticationHeader', $params);
    }
    /**
     * set value for API user name
     *
     * @param string $ApiUserName
     * @return void
     * @since 1.0
     * @package MicrojobEngine
     * @category void
     * @author JACK BUI
     */
    public function setUserName($ApiUserName = ''){
        $this->ApiUsername = $ApiUserName;
    }
    /**
      * Set value for $ApiPassword
      *
      * @param void
      * @return void
      * @since 1.4
      * @package MicrojobEngine
      * @category CREDZU
      * @author JACK BUI
      */
    public function setPassword( $ApiPassword = '' ){
        $this->ApiPassword = $ApiPassword;
    }
    /**
      * set value for $uri
      *
      * @param string $uri
      * @return void
      * @since 1.4
      * @package MicrojobEngine
      * @category CREDZU
      * @author JACK BUI
      */
    public function setURI( $uri = '' ){
        $this->uri = $uri;
    }
    /**
      * set value for publish url value
      *
      * @param void
      * @return void
      * @since 1.4
      * @package MicrojobEngine
      * @category CREDZU
      * @author JACK BUI
      */
    public function setPublishURL($url = ''){
        $this->url = $url;
    }
    /**
      * verify a a bank info
      *
      * @param string $routing_no
      * @param string $account_no
      * @return void
      * @since 1.4
      * @package MicrojobEngine
      * @category CREDZU
      * @author JACK BUI
      */
    public function verifyPayment($routing_no = '', $account_no = '', $verify_field = array() )
    {
        $this->soapClient->__setSoapHeaders(array($this->soapHeader));
        $check = array(
            "RoutingNumber" => $routing_no,
            "AccountNumber" => $account_no);
        $default = array(
            "TestMode" => false,
            'Inquiry' => array(
                'GVerifyEnabled' => true,
                'GAuthenticateEnabled' => false,
                'CustomerIdEnabled' => false,
                'FundsConfirmationEnabled' => false,
                'VoidedCheckImageEnabled' => false,
                'Check' => $check)
        );
        $verify_field = wp_parse_args($verify_field, $default);
        try {
            $info = $this->soapClient->__call("PostInquiry", array($verify_field));
            $validate = $info->PostInquiryResult;
            $response_account = $validate->AccountResponseCode;
            $code_pass = array(
                '_1111',
                '_2222',
                '_3333',
                '_5555',
                '_7777',
                '_8888',
                '_9999',
                'ND00'
            );
            if (in_array($response_account, $code_pass)) {
                $response = array(
                    'success'=>true,
                    'msg'=> __('Success!', ET_DOMAIN)
                );
            } else {
                $response = array(
                    'success'=> false,
                    'msg'=>$validate->VerificationResponse
                );
            }
        } catch (SoapFault $fault) {
            $response = array(
                'success'=> false,
                'msg'=>$fault->faultcode . "-" . $fault->faultstring
            );
        }
        return $response;
        unset($this->soapClient);
    }
}
if( !function_exists('AE_GVerify') ){
    /**
      * get instance of AE_GVerify class
      *
      * @param void
      * @return object instance of this class
      * @since 1.4
      * @package MicrojobEngine
      * @category CREDZU
      * @author JACK BUI
      */
    function AE_GVerify(){
        return AE_GVerify::getInstance();
    }
}
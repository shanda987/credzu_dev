<?php
class mJobPayment extends AE_Payment
{

    function __construct() {
        $this->no_priv_ajax = array();
        $this->priv_ajax = array(
            'et-setup-payment'
        );
        $this->init_ajax();
    }
    /**
     * override get plans function
     *
     * @param void
     * @return array $pack
     * @since 1.0
     * @package MicrojobEngine
     * @category void
     * @author JACK BUI
     */
    public function get_plans() {
        global $ae_post_factory;
        $packageType = 'pack';
        if( isset( $_POST['packageType'] ) && $_POST['packageType'] != '' ){
            $packageType = $_POST['packageType'];
        }
        $pack = $ae_post_factory->get( $packageType );
        return $pack->fetch();
    }
}
new mJobPayment();
<?php
class AE_WalletAction{
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
     * set user wallet
     *
     * @param integer $user_id;
     * @param FRE_Credit_Wallet $fre_user_wallet
     * @param string $type is freezable and available
     * @return void
     * @since 1.0
     * @package FREELANCEENGINE
     * @category FRE CREDIT
     * @author Jack Bui
     */
    public function setUserWallet($user_id, $fre_user_wallet, $type = 'available'){
        if( null == $fre_user_wallet || empty($fre_user_wallet) ){
            $fre_user_wallet = new AE_VirtualWallet();
        }
        if( $user_id ) {
            if( $type == 'available' ) {
                update_user_meta($user_id, 'ae_user_wallet', $fre_user_wallet);
            } else{
                update_user_meta($user_id, 'ae_user_wallet_' . $type, $fre_user_wallet);
            }
        }
    }
    /**
     * get user wallet
     *
     * @param integer $user_id
     * @param string $type is freezable and available
     * @return FRE_Credit_Wallet user's wallet
     * @since 1.0
     * @package FREELANCEENGINE
     * @category FRE CREDIT
     * @author Jack Bui
     */
    public function getUserWallet($user_id, $type = 'available' ){
        if( $type == 'available' ){
            $user_wallet = get_user_meta($user_id, 'ae_user_wallet', true);
        } else{
            $user_wallet = get_user_meta($user_id, 'ae_user_wallet_' . $type, true);
        }
        if( empty($user_wallet) ){
            $user_wallet = new AE_VirtualWallet();
        }
        return $user_wallet;
    }
    /**
     * update user balance only
     *
     * @param integer $user_id
     * @param $balance
     * @param string $type is freezable and available
     * @return void
     * @since 1.0
     * @package FREELANCEENGINE
     * @category FRE CREDIT
     * @author Jack Bui
     */
    public function updateUserBalance($user_id, $balance, $type = 'available'){
        $user_wallet = $this->getUserWallet($user_id, $type);
        $user_wallet->setBalance($balance);
        $this->setUserWallet($user_id, $user_wallet, $type);
    }

    /**
     * Transfer working fund to available fund
     * @param int $user_id
     * @return float $amount
     * @since 1.0.3
     * @package MicrojobEngine
     * @category Credit
     * @author Tat Thien
     */
    public function transferWorkingToAvailable($user_id, $order_id, $amount) {
        $is_transferred = get_post_meta($order_id, "is_transferred", true);
        if(!$is_transferred) {
            // Get wallet
            $working = $this->getUserWallet($user_id, 'working');
            $available = $this->getUserWallet($user_id);

            // check working balance
            if($working->balance >= $amount) {
                // Update balance
                $available->balance += $amount;
                $working->balance -= $amount;
            } elseif($working->balance > 0) {
                $available->balance += $working->balance;
                $working->balance = 0;
            }

            // Update wallet
            $this->setUserWallet($user_id, $working, 'working');
            $this->setUserWallet($user_id, $available);

            update_post_meta($order_id, "is_transferred", true);
        }
    }

    /**
     * check user balance
     *
     * @param integer $user_id
     * @param FRE_Credit_Wallet $number
     * @param string $type is freezable and available
     * @return float if user wallet is smaller user $number
     * @since 1.0
     * @package FREELANCEENGINE
     * @category FRE CREDIT
     * @author Jack Bui
     */
    public function checkBalance( $user_id, $number, $type = 'available'  ){
        if( $type == 'freezable' ) {
            $user_wallet = $this->getUserWallet($user_id, 'freezable');
        }
        else{
            $user_wallet = $this->getUserWallet($user_id);
        }
        if( empty($user_wallet) ){
            $user_wallet = new AE_VirtualWallet();
        }
        if( null == $number || empty($number) ){
            $number = new AE_VirtualWallet();
        }
        $credit_exchange = AE_Currency_Exchange::getInstance();
        $num = $credit_exchange->exchange($number->balance, $number->currency, $user_wallet->currency);
        return (float)($user_wallet->balance - $num);
    }
    /**
     * Generate secure code
     *
     * @param integer number of code
     * @return string secureCode
     * @since 1.0
     * @package FREELANCEENGINE
     * @category FRE CREDIT
     * @author Jack Bui
     */
    public function generateSecureCode( $number = 6 ){
        return wp_generate_password($number);
    }
    /**
     * set secure code for current user
     *
     * @param integer $user_id
     * @param string $code
     * @return void
     * @since 1.0
     * @package FREELANCEENGINE
     * @category FRE CREDIT
     * @author Jack Bui
     */
    public function setSecureCode($user_id, $code ){
        if( null == $code || empty($code) ){
            $code = '';
        }
        $this->secureCode = md5($code);
        update_user_meta($user_id, 'ae_wallet_secure_code', $this->secureCode);
    }
    /**
     * get secureCode
     *
     * @param integer $user_id
     * @return string secure code after md5
     * @since 1.0
     * @package FREELANCEENGINE
     * @category FRE CREDIT
     * @author Jack Bui
     */
    public function getSecureCode($user_id){
        $secure_code = get_user_meta($user_id, 'ae_wallet_secure_code', true);
        return $secure_code;
    }
    /**
     * Check user's secure code
     *
     * @param integer $user_id
     * @param string $code
     * @return boolean true if this is user's secure code
     * @since 1.0
     * @package FREELANCEENGINE
     * @category FRE CREDIT
     * @author Jack Bui
     */
    public function checkSecureCode($user_id, $code){
        $secureCode = $this->getSecureCode($user_id);
        if( $secureCode == md5($code) ){
            return true;
        }
        return false;
    }
    /**
     * charge
     *
     * @param array $charge_obj
     * @return void
     * @since 1.0
     * @package FREELANCEENGINE
     * @category FRE CREDIT
     * @author Jack Bui
     */
    public function charge($charge_obj = array()){
        global $user_ID;
        $default = array(
            'amount' => 0,
            'currency' => ae_get_payment_currency(),
            'customer' => $user_ID,
            'history_type'=> 'charge',
            'status'=> 'completed'
        );
        $charge_obj = wp_parse_args($charge_obj, $default);
        $user_wallet = $this->getUserWallet($charge_obj['customer']);
        $number = AE_Currency_Exchange()->exchange($charge_obj['amount'], $charge_obj['currency'], $user_wallet->currency);
        $wallet = new AE_VirtualWallet($number, $user_wallet->currency);
        $result = $this->checkBalance($charge_obj['customer'], $wallet);
        if( $result >= 0 ){
            $this->updateUserBalance($user_ID, $result);
            $froze_balance = $this->getUserWallet($user_ID, 'freezable');
            $froze_balance->balance +=  $wallet->balance;
            $this->updateUserBalance($user_ID, $froze_balance->balance, 'freezable');
            $charge_id = AE_WithdrawHistory()->saveHistory($charge_obj);
            $response = array(
                'success'=> true,
                'msg'=> __("Successful payment!", ET_DOMAIN),
                'id'=> $charge_id
            );
        }
        else{
            $response = array(
                'success'=> false,
                'msg'=> __("You don't have enough money in your wallet!", ET_DOMAIN)
            );
        }
        return $response;
    }
    /**
     * transfer money
     *
     * @param array $transfer_obj
     * @return array $response
     * @since 1.0
     * @package FREELANCEENGINE
     * @category FRE CREDIT
     * @author Jack Bui
     */
    public function transfer( $transfer_obj ){
        $default = array(
            "amount" => 0, // amount in cents
            "currency" => ae_get_payment_currency(),
            "destination" => '',
            "source_transaction" => '',
            "commission_fee"=> 0,
            "statement_descriptor" => ''
        );
        $transfer_obj = wp_parse_args($transfer_obj, $default);
        $user_wallet = $this->getUserWallet($transfer_obj['destination']);
        $number_transfer = AE_Currency_Exchange()->exchange($transfer_obj['amount'], $transfer_obj['currency'], $user_wallet->currency);
        $number_charge = AE_Currency_Exchange()->exchange((float)$transfer_obj['source_transaction']->amount, $transfer_obj['currency'], $user_wallet->currency);
        $wallet = new AE_VirtualWallet( $number_charge, $transfer_obj['currency'] );
        $charge = $transfer_obj['source_transaction'];
        if( $charge && !empty($charge) ) {
            $result = $this->checkBalance($charge->post_author, $wallet, 'freezable');
            $result_wallet = new AE_VirtualWallet($result);
            if( $result < 0 ){
                $response = array(
                    'success'=> false,
                    'msg'=> __("You don't have enough money in you wallet!", ET_DOMAIN)
                );
                return $response;
            }
            $this->setUserWallet($charge->post_author, $result_wallet, 'freezable');
            $user_wallet->balance += ( $number_transfer - $transfer_obj['commission_fee'] );
            $this->setUserWallet($transfer_obj['destination'], $user_wallet);
            $transfer_obj['history_type'] = 'transfer';
            $transfer_obj['status'] = 'completed';
            AE_WithdrawHistory()->saveHistory($transfer_obj);
            $response = array(
                'success'=> true,
                'msg'=> __("Success!", ET_DOMAIN)
            );
        }
        else{
            $response = array(
                'success'=> false,
                'msg'=> __("There is no charge for this transfer!", ET_DOMAIN)
            );
        }
        return $response;
    }
}
<?php
add_action('init', 'ae_init_wallet');
if(!function_exists('ae_init_wallet')) {
    function ae_init_wallet() {
        $withdraw = AE_Credit_Withdraw::getInstance();
        $withdraw->init();

        $history  = AE_WithdrawHistory::getInstance();
        $history->init();
    }
}

function ae_credit_admin_enqueue_script($hook) {
    if( is_super_admin() ){
        wp_enqueue_script('ae_credit_admin_js', get_template_directory_uri() . '/includes/modules/AE_VirtualWallet/assets/ae_credit_admin_pluginjs.js', array(
            'underscore',
            'backbone',
            'appengine'
        ), '1.0', true);
    }
}
add_action( 'admin_enqueue_scripts', 'ae_credit_admin_enqueue_script' );

if( !function_exists('AE_Currency') ) {
    /**
     * get instance of class AE_Currency
     *
     * @param void
     * @return object AE_Currency
     * @since 1.0
     * @package MicrojobEngine
     * @category void
     * @author JACK BUI
     */
    function AE_Currency()
    {
        return AE_Currency::getInstance();
    }
}
if( !function_exists('AE_Currency_Exchange') ) {
    /**
     * get instance of class AE_Currency_Exchange
     *
     * @param void
     * @return object AE_Currency_Exchange
     * @since 1.0
     * @package MicrojobEngine
     * @category void
     * @author JACK BUI
     */
    function AE_Currency_Exchange()
    {
        return AE_Currency_Exchange::getInstance();
    }
}
if( !function_exists('AE_WithdrawHistory') ) {
    /**
     * get instance of class AE_WithdrawHistory
     *
     * @param void
     * @return object AE_WithdrawHistory
     * @since 1.0
     * @package MicrojobEngine
     * @category void
     * @author JACK BUI
     */
    function AE_WithdrawHistory()
    {
        return AE_WithdrawHistory::getInstance();
    }
}
if( !function_exists('AE_VirtualWallet') ) {
    /**
     * get instance of class AE_VirtualWallet
     *
     * @param void
     * @return object AE_VirtualWallet
     * @since 1.0
     * @package MicrojobEngine
     * @category void
     * @author JACK BUI
     */
    function AE_VirtualWallet()
    {
        return AE_VirtualWallet::getInstance();
    }
}
if( !function_exists('AE_WalletAction') ) {
    /**
     * get instance of class AE_WalletAction
     *
     * @param void
     * @return object AE_WalletAction
     * @since 1.0
     * @package MicrojobEngine
     * @category void
     * @author JACK BUI
     */
    function AE_WalletAction()
    {
        return AE_WalletAction::getInstance();
    }
}
if( !function_exists('ae_get_payment_currency') ){
    /**
     * get site payment currency
     *
     * @param void
     * @return FRE_Credit_Currency $currency
     * @since 1.0
     * @package FREELANCEENGINE
     * @category FRE CREDIT
     * @author Jack Bui
     */
    function ae_get_payment_currency(){
        $currency = ae_get_option('currency', false);
        $code = 'usd';
        $signal = '$';
        $rate_exchange = 1;
        if( $currency ){
            $code = $currency['code'];
            $signal = $currency['icon'];
        }
        if(isset($currency['rate_exchange']) ){
            $rate_exchange = $currency['rate_exchange'];
        }
        $currency = new AE_Currency($code, $signal, true, $rate_exchange);
        return $currency;
    }
}

if( !function_exists('ae_credit_convert_wallet') ){
    /**
     * convert a number to wallet
     *
     * @param float $number
     * @return FRE_Credit_Wallet $wallet
     * @since 1.0
     * @package FREELANCEENGINE
     * @category FRE CREDIT
     * @author Jack Bui
     */
    function ae_credit_convert_wallet( $number = 0 ){
        if( null == $number || empty($number) ){
            $number = 0;
        }
        $currency = ae_get_payment_currency();
        $wallet = new AE_VirtualWallet($number, $currency);
        return $wallet;
    }
}

if( !function_exists('ae_credit_balance_info') ){
    /**
     * render json about balance infor
     *
     * @param integer $user_id
     * @return array
     * @since 1.0
     * @package FREELANCEENGINE
     * @category FRE CREDIT
     * @author Jack Bui
     */
    function ae_credit_balance_info($user_id){
        $total = ae_credit_get_user_total_balance($user_id);
        $available = AE_WalletAction()->getUserWallet($user_id);
        $freezable = AE_WalletAction()->getUserWallet($user_id, 'freezable');
        $withdrew = AE_WalletAction()->getUserWallet($user_id, 'withdrew');
        $working = AE_WalletAction()->getUserWallet($user_id, 'working');
        $minimum = ae_get_option('minimum_withdraw', 0);
        $balance_info = array(
            'total_text'=>  ae_price_format($total->balance),
            'available_text'=>ae_price_format($available->balance),
            'freezable_text'=> ae_price_format($freezable->balance),
            'withdrew_text'=> ae_price_format($withdrew->balance),
            'working_text' => ae_price_format($working->balance),
            'total'=> $total,
            'available'=> $available,
            'freezable'=> $freezable,
            'withdrew' => $withdrew,
            'working' => $working,
            'min_withdraw'=> $minimum,
            'min_withdraw_text'=> ae_price_format($minimum)
        );
        return $balance_info;
    }
}

if( !function_exists('ae_credit_get_user_total_balance') ){
    /**
     * get user balance
     *
     * @param integer $user_id
     * @return FRE_Credit_Wallet $available
     * @since 1.0
     * @package FREELANCEENGINE
     * @category FRE CREDIT
     * @author Jack Bui
     */
    function ae_credit_get_user_total_balance($user_id){
        $available = AE_WalletAction()->getUserWallet($user_id);
        $freezable = AE_WalletAction()->getUserWallet($user_id, 'freezable');
        $available->balance = $available->balance + $freezable->balance;
        return $available;
    }
}


if(!function_exists('ae_price_format')) {
    function ae_price_format($amount, $style = '<sup>') {

        $currency = ae_get_option('currency', array(
            'align' => 'left',
            'code' => 'USD',
            'icon' => '$'
        ));

        $align = $currency['align'];

        // dafault = 0 == right;

        $currency = $currency['icon'];
        $price_format = get_theme_mod('decimal_point', 1);
        $format = '%1$s';

        switch ($style) {
            case 'sup':
                $format = '<sup>%s</sup>';
                break;

            case 'sub':
                $format = '<sub>%s</sub>';
                break;

            default:
                $format = '%s';
                break;
        }

        $number_format = ae_get_option('number_format');
        $decimal = (isset($number_format['et_decimal'])) ? $number_format['et_decimal'] : get_theme_mod('et_decimal', 2);
        $decimal_point = (isset($number_format['dec_point']) && $number_format['dec_point']) ? $number_format['dec_point'] : get_theme_mod('et_decimal_point', '.');
        $thousand_sep = (isset($number_format['thousand_sep']) && $number_format['thousand_sep']) ? $number_format['thousand_sep'] : get_theme_mod('et_thousand_sep', ',');

        if ($align != "0") {
            $format = $format . '%s';
            return sprintf($format, $currency, number_format((double)$amount, $decimal, $decimal_point, $thousand_sep));
        } else {
            $format = '%s' . $format;
            return sprintf($format, number_format((double)$amount, $decimal, $decimal_point, $thousand_sep) , $currency);
        }
    }
}

if(!function_exists('ae_modal_decline_withdraw')) {
    /**
     * @todo chua co bootstrap trong backend
     * Add modal decline withdraw
     * @param void
     * @return void
     * @since MicrojobEngine 1.0
     * @package MicrojobEngine
     * @category Withdraw
     * @author Tat Thien
     */
    function ae_modal_decline_withdraw() {
        if(isset($_GET['page']) && $_GET['page'] == 'et-withdraws') {
            ?>
            <div class="modal fade" id="#decline-withdraw-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel1">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title" id="myModalLabel1"><?php _e('Decline Withdraw', ET_DOMAIN); ?></h4>
                        </div>
                        <div class="modal-body">
                            <div class="form-control">
                                <textarea name="decline_note" id="decline_note" cols="30" rows="10" placeholder="<?php __('Give a decline reason to user', ET_DOMAIN); ?>"></textarea>
                            </div>

                            <div class="form-control">
                                <button><?php __('Submit', ET_DOMAIN); ?></button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php
        }
    }

    add_action('admin_footer', 'ae_modal_decline_withdraw');
}

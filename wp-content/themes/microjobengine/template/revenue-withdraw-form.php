<div class="payment-method">
    <p class="choose-payment"><?php _e('Choose your way to get money', ET_DOMAIN); ?></p>
    <p class="link-change-payment">
        <a href="<?php echo et_get_page_link('payment-method'); ?>"><?php _e('Change your payment method', ET_DOMAIN); ?></a>
    </p>
</div>
<div id="withdrawForm">
    <form class="et-form">
        <div class="form-group check-payment">
            <div class="checkbox">
                <label for="paypal_account">
                    <input type="radio" name="account_type" id="paypal_account" value="paypal" checked>
                    <span><?php _e('PayPal account', ET_DOMAIN); ?></span>
                </label>
            </div>
            <div class="checkbox">
                <label for="bank_account">
                    <input type="radio" name="account_type" id="bank_account" value="bank">
                    <span><?php _e('Bank account', ET_DOMAIN); ?></span>
                </label>
            </div>
        </div>
        <div class="code-bank">
            <div class="form-group clearfix value-payment">
                <div class="input-group">
                    <div class="input-group-addon"><i class="fa fa-usd"></i></div>
                    <input type="number" min="0"  step="1" name="amount" id="amount" placeholder="<?php printf(__('Money amount (min %s)', ET_DOMAIN), mJobPriceFormat(MIN_WITHDRAW, "")); ?>">
                </div>
            </div>

            <div class="form-group clearfix value-payment">
                <div class="input-group">
                    <div class="input-group-addon"><i class="fa fa-shield"></i></div>
                    <input type="password" name="secure_code" id="secure_code" placeholder="<?php _e('Secure code', ET_DOMAIN); ?>">
                </div>
            </div>

            <div class="form-group">
                <span><?php _e("Don't have secure code or forgot it? ", ET_DOMAIN); ?><a href="#" class="request-secure-code"><?php _e('Request here', ET_DOMAIN); ?></a></span>
            </div>

            <input type="hidden" name="_wpnonce" id="_wpnonce" value="<?php echo wp_create_nonce('withdraw_action') ?>">

            <div class="form-group submit-bank">
                <button class="btn-submit btn-submit-bank"><?php _e('Submit', ET_DOMAIN); ?></button>
            </div>
        </div>
    </form>
</div>
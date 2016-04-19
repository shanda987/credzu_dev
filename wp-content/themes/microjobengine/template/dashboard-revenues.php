<?php
    global $user_ID;
    $revenues = ae_credit_balance_info($user_ID);
    $total_earned = ae_price_format($revenues['working']->balance + $revenues['available']->balance +$revenues['freezable']->balance);
?>
<div class="revenues box-shadow">
    <div class="title"><?php _e('Revenues', ET_DOMAIN); ?></div>
    <div class="line">
        <span class="line-distance"></span>
    </div>
    <ul class="row">
        <li class="col-lg-3 col-md-3 col-sm-3 col-xs-6">
            <p class="cate"><?php _e('Working', ET_DOMAIN); ?></p>
            <p class="currency"><?php echo $revenues['working_text']; ?></p>
        </li>

        <li class="col-lg-3 col-md-3 col-sm-3 col-xs-6">
            <p class="cate"><?php _e('Available', ET_DOMAIN); ?></p>
            <p class="currency available-text"><?php echo $revenues['available_text']; ?></p>
        </li>

        <li class="col-lg-3 col-md-3 col-sm-3 col-xs-6">
            <p class="cate"><?php _e('Pending', ET_DOMAIN); ?></p>
            <p class="currency freezable-text"><?php echo $revenues['freezable_text']; ?></p>
        </li>

        <li class="col-lg-3 col-md-3 col-sm-3 col-xs-6">
            <p class="cate"><?php _e('Withdrawal', ET_DOMAIN); ?></p>
            <p class="currency withdrew-text"><?php echo $revenues['withdrew_text']; ?></p>
        </li>
    </ul>
    <div class="balance-withdraw">
        <p class="currency-balance"><span><?php _e('Balance:', ET_DOMAIN); ?></span><span class="price-balance"><?php echo $total_earned; ?></span></p>
        <p class="note-balance"><?php _e('(Working + Available + Pending)', ET_DOMAIN); ?></p>
    </div>
</div>
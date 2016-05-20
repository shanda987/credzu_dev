<?php

require_once 'vendor/autoload.php';

// reference the Dompdf namespace
use Dompdf\Dompdf;

// instantiate and use the dompdf class
$dompdf = new Dompdf();
$dompdf->loadHtml('
<!DOCTYPE html>
<html lang="en">
    <meta charset="utf-8">
    <style>
    .check {
        border: 1px solid silver;
        width: 100%;
        height: 30%;
        position: relative;
    }
    .check .bank-name {
        text-align: center;
    }
    .check .check-owner {
        left: 10px;
        top: 10px;
        position: absolute;
    }
    .check .check-owner p:first-child {
        font-weight: bolder;
    }
    .check p {
        margin: 0;
    }
    .pay-to {
        position: absolute;
        top: 120px;
        left: 0%;
    }
    .pay-to div {
        display: inline-block
    }
    .pay-to div:first-child {
        font-weight: bolder;
    }
    .word-sum {
        position: absolute;
        top: 170px;
        left: 0%;
    }
    .word-sum div {
        display: inline-block;
    }
    </style>
</head>
<body>

<div class="check">
    <div class="bank-name">
        Bank Name
    </div>
    <div class="check-number">
        001
    </div>
    <div class="check-owner">
        <p>Credzu LLC</p>
        <p>500 Lala Land</p>
        <p>Somewhere, FL 339900</p>
        <p>(000) 000-0000</p>
    </div>
    <div class="date">
        <p>Date: May 18th 2016 02:36PM (EST)</p>
    </div>
    <div class="pay-to">
        <div class="pay-to">
            <p>Pay to the</p>
            <p>Order Of:</p>
        </div>
        <div class="name">
            <p>Credzu LLC</p>
        </div>
    </div>
    <div class="numeric-sum">
        <p>$1400.00</p>
    </div>
    <div class="word-sum">
        <div class="left">
            <p>The sum of:</p>
        </div>
        <div>
            One Thousand, Four Hundred Dollars and Zero Cents
        </div>
    </div>

    <div class="memo">
        <p>COMPANY NAME for services</p>
        <p>Memo: Authorized by the U.C.C. (2006), the UETA (1999) and ESIGN Act(2000)</p>
    </div>

    <div class="signature">
        <p>A image placed here</p>
        <p>Signed from IP Address: IP when created listing</p>
    </div>

    <div class="check-footer">
        <div class="check-number">
        123123123
        </div>
        <div class="account-no">
        123123123123
        </div>
        <div class="routing-no">
        123123123
        </div>

    </div>
</div>

</body>
</html>
');

// (Optional) Setup the paper size and orientation
$dompdf->setPaper('A4');

// Render the HTML as PDF
$dompdf->render();

// Output the generated PDF to Browser
$dompdf->stream();
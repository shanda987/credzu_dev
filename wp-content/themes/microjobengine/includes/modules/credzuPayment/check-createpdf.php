<?php
function mjobCreatePdf($profile, $data){
	ob_start();
global $wpdb, $user_ID;
$fee = $data['latest_amount_text'];
//--get width
function set_width($fee=""){
	switch(strlen($fee)){
		case '6':
			$return = '16%';
			break;
		case '7':
			$return = '16%';
			break;
		case '8':
			$return = '19%';
			break;
		case '9':
			$return = '20%';
			break;
		default:
			$return = '15%';
			break;
	}

	echo $return;
}
///
?>
<style>
	@font-face {font-family: MICR Encoding;src: url(css/micrenc.ttf) format("truetype");}
	.EntezarFont {font-family: MICR Encoding !important;}
	body{display: block;margin: 8px;font-size:10pt;font-family: Arial, Helvetica, sans-serif;}
	h4{margin:0;}
</style>
<body>

<table style="font-size:10pt;" width="940px">
	<tr valign="top">
		<td width="1%"></td>
		<td align="left">
			<strong><?php echo $profile->company_name;?></strong><br/>
			<span style="font-size:9pt"><?php echo $profile->company_address;?></span><br/>
			<span style="font-size:9pt"><?php echo $profile->company_city.", ".$profile->company_state." ".$profile->company_zip_code;?></span><br/>
			<span style="font-size:9pt"><?php echo $profile->company_phone;?></span>
		</td>
		<td align="center">
			<strong><?php echo $profile->bank_name;?></strong>
		</td>
		<td align="right" style="font-size:14pt">
			<?php
			$check_number = (int)get_option('payment_check_number', 0);
			$check_number = $check_number + 1;?>
			<strong><?php echo formatCheckNumber($check_number);?></strong>
		</td>
	</tr>
	<tr>
		<td></td>
		<td></td>
		<td colspan="2" style="text-align:right;">Date: <u><?php echo date("F jS h:i A Y",strtotime(time()));?> (EST)</u></td>
	</tr>
	<tr><td></td></tr>
</table>

<table style="font-size:10pt;">
	<tr>
		<td width="14%">
			<strong>Pay to the Order Of:</strong>
		</td>
		<td style="font-size:12pt;" width="60%">
			Credit Repair Connection Trust
		</td>
		<td width="<?php set_width($fee);?>">
			<div style="font-weight:bold;text-align:center;border: 1pt solid #000000;font-size: 17pt;">&nbsp;&nbsp;<?php echo $fee;?></div>
		</td>
	</tr>
</table>
<table style="font-size:10pt;" width="100%">
	<tr>
		<td width="11%">
			The Sum of:
		</td>
		<td colspan="2" style="border-bottom:1pt solid #000000;font-size:11pt;">
			<?php
			$amountin =str_replace('$','',$fee);
			$amountin =str_replace(',','',$amountin);
			echo convertMoney($data['latest_amount']);
			?>
		</td>
	</tr>
</table>

<table style="font-size:10pt;">
	<tr>
		<td style="font-size:9pt;width:430px;">
			<br/><br/><br/><br/>
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;For services<br/>
			Memo: Authorized by the U.C.C. (2006), the UETA (1999) and ESIGN Act (2000)
		</td>
		<td>
			<img src="<?php echo $profile->company_signature_img ?>" alt="Signature" width="140pt" height="54.5pt" /><br/>
			<small>Signed from IP Address <?php echo $_SERVER['REMOTE_ADDR'];?></small>
		</td>
	</tr>
</table>
<table cellspacing="0" cellpadding="0">
	<tr>
		<td align="left" style="font-size: 19pt;font-family: MICR Encoding !important;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; c<?php echo str_pad($check_number, 10, '0', STR_PAD_LEFT);?>c a<?php echo $profile->routing_number;?>a <?php echo $profile->account_number;?>c</td>
	</tr>
</table>

<table>
	<tr><td></td></tr>
	<tr><td></td></tr>
	<tr><td></td></tr>
</table>

<table style="font-size:10pt;">
	<tr valign="top">
		<td width="25%">
			Payer and Payee data has been repositioned for mailing purposes
		</td>
		<td align="center" width="50%">
			<strong><?php echo $profile->bank_name;?></strong>
		</td>
		<td align="right" width="25%" style="font-size:14pt">
			<strong><?php echo formatCheckNumber($check_number);?></strong>
		</td>
	</tr>
</table>
<table><tr><td></td></tr><tr><td></td></tr></table>

<table style="font-size:9pt;">
	<tr>
		<td width="26%">
			Memo: For services
		</td>
		<td width="40%">
			<i>Pay to the Order Of:</i>Credit Repair Connection Trust
		</td>
		<td align="right" style="font-size:10pt;"><u><?php echo date("F jS h:i A Y",strtotime(time()));?> (EST)</u></td>
	</tr>
	<tr><td></td></tr>
</table>

<table style="font-size:10pt;">
	<tr>
		<td width="11%">
			The Sum of:
		</td>
		<td style="font-size:11pt" width="60%">
			<div style="border-bottom:1pt solid #000000;">
				<?php
				echo convertMoney($data['latest_amount']);
				?>
			</div>
		</td>
		<td width="<?php set_width($fee);?>">
			<div style="font-weight:bold;text-align:center;border: 1pt solid #000000;font-size: 17pt;">&nbsp;&nbsp;<?php echo $fee;?></div>
		</td>
	</tr>
</table>

<table>
	<tr>
		<td style="font-weight:bold;font-size:9pt;width:440px;">
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $profile->company_name;?><br/>
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $profile->company_address;?><br/>
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $profile->company_city.", ".$profile->company_state." ".$profile->company_zip_code;?><br/>
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $profile->company_phone;?>
		</td>
		<td rowspan="2" style="text-align:center;border: 1pt solid #000000;width:265px;">
			<div style="font-size:14pt;font-weight:bold;">&nbsp;Check Authorization Notice Client Copy</div>
			<span style="font-size:9pt;margin:0;padding:0;">&nbsp;As per your authorization this check has been deposited to make the payment you requested.</span>
		</td>
	</tr>
	<tr>
		<td valign="top" style="font-size:8pt;width:440px;">
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <strong><?php echo $fee?></strong><br/>
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Authorized by the U.C.C. (2006), the UETA (1999) and ESIGN Act (2000)
		</td>
	</tr>
</table>
<table>
	<tr>
		<td align="left" style="font-size: 19pt;font-family: MICR Encoding !important">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; c<?php echo str_pad($check_number, 10, '0', STR_PAD_LEFT);?>c a<?php echo $profile->routing_number?>a <?php echo $profile->account_number;?>c</td>
	</tr>
</table>

<table>
	<tr><td></td></tr>
	<tr><td></td></tr>
	<tr><td></td></tr>
	<tr><td></td></tr>
</table>

<table style="font-size:10pt;" width="940px">
	<tr valign="top">
		<td width="1%"></td>
		<td align="left">
			<strong><?php echo $profile->company_number;?></strong><br/>
			<span style="font-size:9pt"><?php echo $profile->company_address.""?><br/></span>
			<span style="font-size:9pt"><?php echo $profile->company_city.", ".$profile->company_state." ".$profile->company_zip_code;?><br/></span>
			<span style="font-size:9pt"><?php echo $profile->cellphone;?></span>
		</td>
		<td align="center">
			<strong><?php echo $profile->bank_name;?></strong>
		</td>
		<td align="right" style="font-size:14pt">
			<strong><?php echo formatCheckNumber($check_number);?></strong>
		</td>
	</tr>
	<tr>
		<td></td>
		<td></td>
		<td colspan="2" style="text-align:right;"><?php echo date("F jS h:i A Y",strtotime(time()));?> (EST)</td>
	</tr>
</table>
<table width="712px">
	<tr>
		<td align="center" style="font-size:14pt;">FILE COPY ONLY - DO NOT CASH</td>
	</tr>
</table>

<table style="font-size:10pt;">
	<tr>
		<td width="14%">
			<strong>Pay to the Order Of:</strong>
		</td>
		<td style="font-size:12pt;" width="60%">
			Credit Repair Connection Trust
		</td>
		<td width="<?php set_width($fee);?>">
			<div style="font-weight:bold;text-align:center;border: 1pt solid #000000;font-size: 17pt;">&nbsp;&nbsp;<?php echo $fee;?></div>
		</td>
	</tr>
</table>
<table style="font-size:10pt;" width="100%">
	<tr>
		<td width="11%">
			The Sum of:
		</td>
		<td colspan="2" style="border-bottom:1pt solid #000000;font-size:11pt">
			<?php
			$amountin =str_replace('$','',$fee);
			$amountin =str_replace(',','',$amountin);
			echo convertMoney($data['latest_amount']);
			?>
		</td>
	</tr>
</table>
<br/>
<table style="font-size:9pt;">
	<tr>
		<td style="width:430px;">
			<br/><br/>
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;For services<br/>
			Memo: Authorized by the U.C.C. (2006), the UETA (1999) and ESIGN Act (2000)
		</td>
		<td style="border: 1pt solid #000000;font-size:14pt;width:180px;">
			<strong>NON NEGOTIABLE File Copy</strong>
		</td>
	</tr>
</table>
<table>
	<tr>
		<td align="left" style="font-size: 19pt;font-family: MICR Encoding !important;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; c<?php echo str_pad($check_number, 10, '0', STR_PAD_LEFT);?>c a<?php echo $profile->routing_number;?>a <?php echo $profile->account_number;?>c</td>
	</tr>
</table>

</body>
<?php
	$html = ob_get_clean();
	return $html;
} ?>
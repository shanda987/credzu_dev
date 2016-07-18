<?php
/**
 * Template for Email Header
 */
$mail_header = apply_filters('ae_get_mail_header', '');
if ($mail_header != '') return $mail_header;

$logo_url = get_template_directory_uri() . "/img/logo-de.png";
$options = AE_Options::get_instance();

// save this setting to theme options
$site_logo = $options->site_logo;
if (!empty($site_logo)) {
    $logo_url = $site_logo['large'][0];
}

$logo_url = apply_filters('ae_mail_logo_url', $logo_url);

$customize = et_get_customization();

$mail_header = '<html>
                        <head>
                        </head>
                        <body style="font-family: Arial, sans-serif;font-size: 0.9em;margin: 0; padding: 0; color: #222222;">
                        <div style="margin: 0px auto; width:600px; border: 1px solid ' . $customize['background'] . '">
                            <table width="100%" cellspacing="0" cellpadding="0">

                            <tr style="height: 63px; vertical-align: middle;">
                                <td style="padding: 10px 5px 10px 20px; width: 20%; padding-left: 5%;">
                                    <img style="max-height: 100px" src="' . $logo_url . '" alt="' . get_option('blogname') . '">
                                </td>
                                <td style="padding: 10px 20px 10px 5px">
                                    <span style="text-shadow: 0 0 1px #151515; color: #b0b0b0;">' . get_option('blogdescription') . '</span>
                                </td>
                            </tr>
                            <tr><td colspan="2" style="height: 5px; background-color: ' . $customize['background'] . ';"></td></tr>
                            <tr>
                                <td colspan="2" style="background: #ffffff; color: #222222; line-height: 18px; box-shadow: 1px 0px 30px rgba(188, 207, 219, 0.7);">
                                <div style="border: 2px solid rgba(254,254,255,.5))"><div style="border: 2px solid rgba(253,253,255,.5)"><div style="border: 1px solid rgba(252,252,254,.5)"><div style="border: 3px solid rgba(251,251,253,.5)"><div style="border: 1px solid rgba(250,250,250,.5)"><div style="border: 1px solid rgba(249,249,249,.5)"><div style="border: 1px solid rgba(247,248,250,.5)"><div style=""><div style="border: 1px solid rgba(246,247,249,.5)"><div style="border: 1px solid rgba(242,246,249,.5)"><div style="border: 1px solid rgba(241,245,248,.5)"><div style="border: 2px solid rgba(239,244,248,.5)"><div style="border: 1px solid rgba(236,241,245,.5)"><div style="border: 1px solid rgba(235,240,244,.5)"><div style="border: 1px solid rgba(234,238,241, .5);  padding-top:10%; padding-left:5%; padding-right: 5%; padding-bottom: 10%;">';
echo $mail_header;
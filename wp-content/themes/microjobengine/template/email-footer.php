<?php
/**
 * Template for Email Footer
 */
$mail_footer = apply_filters('ae_get_mail_footer', '');
if ($mail_footer != '') return $mail_footer;

$info = apply_filters('ae_mail_footer_contact_info', get_option('blogname') . ' <br>
                        ' . get_option('admin_email') . ' <br>');

$customize = et_get_customization();
$copyright = apply_filters('get_copyright', ae_get_option('copyright'));

$mail_footer = '</td>
                        </tr>
                        <tr>
                            <td colspan="2" style="background: ' . $customize['background'] . '; padding: 10px 20px; color: #666;">
                                <table width="100%" cellspacing="0" cellpadding="0">
                                    <tr>
                                        <td style="vertical-align: top; text-align: left; width: 50%;">' . $copyright . '</td>
                                        <td style="text-align: right; width: 50%;">' . $info . '</td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                        </table>
                    </div>
                    </body>
                    </html>';
echo $mail_footer;
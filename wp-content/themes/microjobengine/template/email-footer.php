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

$mail_footer = '</div></div></div></div></div></div></div></div></div></div></div></div></div></div></div></td>
                        </tr>
                        <tr>
                            <td colspan="2" style="background: ' . $customize['background'] . '; padding: 10px 20px; color: #666;">
                                <table width="100%" cellspacing="0" cellpadding="0">
                                    <tr>
                                        <td style="vertical-align: top; text-align: left; width: 50%; padding-left: 5%; padding-right:5%;"><i>' . $copyright . '</i></td>

                                    </tr>
                                    <tr>
                                    <td style="padding-left: 5%; padding-right:5%;"><br/><br/><i>NOTICE: This communication (including any accompanying document(s) is for the sole use of the intended recipient and may contain confidential information. Unauthorized use, distribution, disclosure or any action taken or omitted to be taken in reliance on this communication is prohibited, and may be unlawful. If you are not the intended recipient, please notify the sender and destroy all electronic and hard copies of this message. By inadvertent disclosure of this communication, Credzu, LLC does not waive confidentiality privilege with respect hereto.</i></td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                        </table>
                    </div>
                    </body>
                    </html>';
echo $mail_footer;
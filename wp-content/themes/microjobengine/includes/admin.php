<?php
define('ADMIN_PATH', TEMPLATEPATH . '/admin');

if (!class_exists('AE_Base')) return;

/**
 * Handle admin features
 * Adding admin menus
 */
class ET_Admin extends AE_Base
{
    function __construct() {
        /**
         * admin setup
         */
        $this->add_action('init', 'admin_setup');

        /**
         * update first options
         */
        $this->add_action('after_switch_theme', 'update_first_time');

        //declare ajax classes
        /**
         * @todo Khúc này không dùng project nữa nên xem lại.
         */
        // new AE_CategoryAjax(new AE_Taxonomy_Meta(array(
        //     'taxonomy' => 'project_category'
        // )));
        // new AE_CategoryAjax(new AE_Taxonomy_Meta(array(
        //     'taxonomy' => 'project_type'
        // )));

        $this->add_ajax('ae-reset-option', 'reset_option');

        /* User Actions */
        $this->add_action('ae_upload_image', 'ae_upload_image', 10, 2);

        /**
         * set default options
         */
        $options = AE_Options::get_instance();
        if (!$options->init) $options->reset($this->get_default_options());

        // kick subscriber user
        if (!current_user_can('manage_options') && basename($_SERVER['SCRIPT_FILENAME']) != 'admin-ajax.php') {

            // wp_redirect( home_url(  ) );
            // exit;

        }
        $this->add_filter( 'ae_setup_wizard_template', 'fre_setup_wizard_template' );
        $this->add_filter( 'notice_after_installing_theme', 'fre_notice_after_installing_theme' );
        $this->add_action( 'ae_insert_sample_data_success', 'fre_after_insert_sample_data' );
    }

    /**
     * update user avatar
     */
    public function ae_upload_image($attach_data, $data) {
        $options = AE_Options::get_instance();
        switch ($data) {
            case 'site_logo_black':
            case 'site_logo_white':

                // save this setting to theme options
                $options->$data = $attach_data;
                if ($data == 'site_logo_black') {
                    $options->site_logo = $attach_data;
                }
                $options->save();

                break;

            default:
                if(!is_array($data)) {
                     $options->$data = $attach_data;
                     $options->save();
                }
                break;
        }
    }

    /**
     * ajax function reset option
     */
    function reset_option() {

        $option_name = $_REQUEST['option_name'];
        $default_options = $this->get_default_options();

        if (isset($default_options[$option_name])) {
            $options = AE_Options::get_instance();
            $options->$option_name = $default_options[$option_name];
            wp_send_json(array(
                'msg' => $default_options[$option_name]
            ));
        }
    }

    function admin_custom_css() {
    ?>
        <style type="text/css">
        .custom-icon {
            margin: 10px;
        }
        .custom-icon input {
            width: 80%;
        }
        </style>
    <?php
    }

    /**
     * retrieve site default options
     */
    function get_default_options() {

        return apply_filters('fre_default_setting_option', array(
            'blogname' => get_option('blogname') ,
            'blogdescription' => get_option('blogdescription') ,
            'copyright' => '<span class="enginethemes"> <a href=http://www.enginethemes.com/themes/microjobengine/ >MicrojobEngine</a> - Powered by WordPress </span>',

            'project_demonstration' => array(
                'home_page' => 'The best way to <br/>  find a professional',
                'list_project' => 'A Million of Project.<br/> Find it out!'
            ) ,
            'profile_demonstration' => array(
                'home_page' => 'Need a job? <br/> Tell us your story',
                'list_profile' => 'Need a job? <br/> Tell us your story'
            ) ,

            // default forgot passmail
            'forgotpass_mail_template' => '<p>Hello [display_name],</p><p>You have just sent a request to recover the password associated with your account in [blogname]. If you did not make this request, please ignore this email; otherwise, click the link below to create your new password:</p><p>[recover_url]</p><p>Regards,<br />[blogname]</p>',

            // default register mail template
            'register_mail_template' => '<p>Hello [display_name],</p><p>You have successfully registered an account with &nbsp;&nbsp;[blogname].&nbsp;Here is your account information:</p><ol><li>Username: [user_login]</li><li>Email: [user_email]</li></ol><p>Thank you and welcome to [blogname].</p>',

            // default confirm mail template
            'password_mail_template' => '<p>Hello [display_name],</p><p>You have successfully registered an account with &nbsp;&nbsp;[blogname].&nbsp;Here is your account information:</p><ol><li>Username: [user_login]</li><li>Email: [user_email]</li><li>Password: [password]</li></ol><p>Thank you and welcome to [blogname].</p>',

            //  default reset pass mail template
            'resetpass_mail_template' => "<p>Hello [display_name],</p><p>You have successfully changed your password. Click this link &nbsp;[site_url] to login to your [blogname]'s account.</p><p>Sincerely,<br />[blogname]</p>",

            // default confirm mail template
            'confirm_mail_template' => '<p>Hello [display_name],</p><p>You have successfully registered an account with &nbsp;&nbsp;[blogname].&nbsp;Here is your account information:</p><ol><li>Username: [user_login]</li><li>Email: [user_email]</li></ol><p>Please click the link below to confirm your email address.</p><p>[confirm_link]</p><p>Thank you and welcome to [blogname].</p>',

            // default confirmed mail template
            'confirmed_mail_template' => "<p>Hi [display_name],</p><p>Your email address has been successfully confirmed.</p><p>Thank you and welcome to [blogname].</p>",

            //  default inbox mail template
            'inbox_mail_template' => "<p>Hello [display_name],</p><p>You have just received the following message from user: <a href=\"[sender_link]\">[sender]</a></p>
                                        <p>|--------------------------------------------------------------------------------------------------|</p>
                                        [message]
                                        <p>|--------------------------------------------------------------------------------------------------|</p>
                                        <p>You can answer the user by replying this email.</p><p>Sincerely,<br />[blogname]</p>",

            //  default inbox mail template
            'new_mjob_mail_template'        => "<p>Hi,</p>
                                               <p>User [author] has submitted a new mJob on your site. You could review it [here].</p>
                                               <p>Regards,<br>[blogname]</p>",

            'approve_mjob_mail_template'    => "<p>Dear [display_name],</p>
                                                <p>Your post [link] posted in [blogname] has been approved.</p>
                                                <p>Sincerely,<br>[blogname]</p>",

            'reject_mail_template'          => "<p>Dear [display_name],</p>
                                                <p>Your post [link] submitted in [blogname] has been rejected. Noted reason: [reject_message]</p>
                                                <p>Please contact the admin via [admin_email] for further information, or go to your dashboard at [dashboard] to edit your job offer and submit again.</p>
                                                <p>Sincerely,<br>[blogname]</p>",

            'archived_mjob_mail_template'   => "<p>Dear [display_name],</p>
                                                <p>Your post [link] posted in [blogname] has been archived. Noted reason: [reject_message]</p>
                                                <p>Please contact the admin via [admin_email] for further information, or go to your dashboard at [dashboard] to edit your job offer and submit again.</p>
                                                <p>Sincerely,<br>[blogname]</p>",

            'new_order'        =>               "<p>Dear [display_name],</p>
                                                <p>Your mJob - [link] -  posted in [blogname] has a new order.</p>
                                                <p>Here are the order’s details:</p>
                                                <p><ol>
                                                <li>Name:  [buyer_name]</li>
                                                <li>Total: [total]</li>
                                                </ol></p>
                                                <p>And here is the link to the order: [order_link].</p>
                                                <p>Sincerely,<br>[blogname]</p>",

            'delivery_order'                =>  "<p>Dear [display_name],</p>
                                                <p>Your order for mJob - [link] -  has been delivered.</p>
                                                <p>Here are the delivery details: [note].</p>
                                                <p>And here is the link to your order details: [order_link].</p>
                                                <p>Sincerely,<br>[blogname]</p>
                                                ",

            'cancel_order'                =>  "<p>Dear [display_name],</p>
                                                <p>The seller [author] has canceled your order for the mJob: [link]. </p>
                                                <p>You can review your order: [order_link] and stop the delivery.</p>
                                                <p>Sincerely,<br>[blogname]</p>
                                                ",
            'dispute_order'                =>  "<p>Hello Admin,</p>
                                                <p>[title] is in dispute.</p>
                                                <p>You can review the order at: [link]</p>
                                                <p>Sincerely,<br>[blogname]</p>",

            'dispute_order_user'                =>"<p>Hello [display_name],</p>
                                                <p>[title] you’ve worked on has been reported by your partner. You should review and send your feedback in 36 hours.</p>
                                                <p>You can review the order at: [link]</p>
                                                <p>Sincerely,<br>[blogname]</p>",

            'dispute_seller_win'          => "<p>Dear [display_name],</p>
                                              <p>Admin has made the final decision about the disputed [title].</p>
                                              <p>The payment will be transferred to the seller.</p>
                                              <p>You can review the order at: [link]</p>
                                              <p>Sincerely,<br>[blogname]</p>",
            'dispute_buyer_win'          => "<p>Dear [display_name],</p>
                                              <p>Admin has made the final decision about the disputed [title].</p>
                                              <p>The payment will be transferred back to the buyer.</p>
                                              <p>You can review the order at: [link]</p>
                                              <p>Sincerely,<br>[blogname]</p>",
            'new_withdraw'                =>  "<p>Hi,</p>
                                                <p>User [user_name] has sent a withdrawal request.</p>
                                                <p>Here are the withdrawal details:</p>
                                                <p>
                                                    <ul>
                                                    <li>Name:  [user_name]</li>
                                                    <li>Total: [total]</li>
                                                    <li>Withdraw info: [withdraw_info]</li>
                                                    </ul>
                                                </p>
                                                <p>And here is the link to the user info: [user_link].</p>
                                                <p>You can go to dashboard to approve or decline the request.</p>
                                                <p>Sincerely,<br>[blogname]</p>
                                                ",

            'approve_withdraw'            =>  "<p>Dear [display_name],</p>
                                                <p>Your withdrawal request has been approved. Please check your payment account.</p>
                                                <p>Your current balance is: [balance].</p>
                                                <p>Sincerely,<br>[blogname]</p>
                                                ",

            'decline_withdraw'            =>  "<p>Dear [display_name],</p>
                                                <p>Your withdrawal request has been declined. Noted reason: [note]</p>
                                                <p>Your current balance is: [balance].</p>
                                                <p>Sincerely,<br>[blogname]</p>
                                                ",
            'decline_mjob_order'            =>  "<p>Dear [display_name],</p>
                                                <p>Your microjob order request has been declined. Noted reason: [note]</p>
                                                <p>Sincerely,<br>[blogname]</p>
                                                ",

            'pay_package_by_cash'           => "<p>Dear [display_name],</p>
                                                <p>Thank you for your payment. Please send the payment to XXX account to complete the order.</p>
                                                <p>Here are the details of your transaction:</p>
                                                <p>Detail: [detail]</p>
                                                <p><strong>Customer info</strong></p>
                                                <p>
                                                    <ul>
                                                    <li>Name: [display_name]</li>
                                                    <li>Email: [user_email]</li>
                                                    </ul>
                                                </p>
                                                <p><strong>Invoice</strong></p>
                                                <p>
                                                    </ul>
                                                    <li>Invoice No: [invoice_id]</li>
                                                    <li>Date: [date]</li>
                                                    <li>Payment: [payment]</li>
                                                    <li>Total: [total] [currency]</li>
                                                    </ul>
                                                </p>
                                                <p>Sincerely,<br>[blogname]</p>",

            'ae_receipt_mail'           => "<p>Dear [display_name],</p>
                                                <p>Thank you for your payment.</p>
                                                <p>Here are the details of your transaction:</p>
                                                <p>Detail: [detail]</p>
                                                <p><strong>Customer info</strong></p>
                                                <p>
                                                    <ul>
                                                    <li>Name: [display_name]</li>
                                                    <li>Email: [user_email]</li>
                                                    </ul>
                                                </p>
                                                <p><strong>Invoice</strong></p>
                                                <p>
                                                    </ul>
                                                    <li>Invoice No: [invoice_id]</li>
                                                    <li>Date: [date]</li>
                                                    <li>Payment: [payment]</li>
                                                    <li>Total: [total] [currency]</li>
                                                    </ul>
                                                </p>
                                                <p>Sincerely,<br>[blogname]</p>",

            'secure_code_mail'              => "<p>Dear [display_name],</p>
                                                <p>Here is your secure code: [secure_code]</p>
                                                <p>Sincerely,<br>[blogname]</p>",

            'sign_up_intro_text'              => "<p><strong>Welcome to MicrojobEngine!</strong></p><p>If you have amazing skills, we have amazing mJobs. MicrojobEngine has opportunities for all types of fun. Let's turn your little hobby into Big Bucks.</p>",
            'init' => 1
        ));
    }

    function update_first_time() {
        update_option('de_first_time_install', 1);
        update_option('revslider-valid-notice', 'false');
    }
    /**
     * FrE setup wizard html template
     * @param string $html
     * @return string $html
     * @since 1.6.2
     * @package void
     * @category void
     * @author Tambh
     */
    public function fre_setup_wizard_template( $html ){
        ob_start();
        ?>
        <div class="et-main-content" id="overview_settings">
            <div class="et-main-right">
                <div class="et-main-main clearfix inner-content" id="wizard-sample">

                    <div class="title font-quicksand" style="padding-top:0;">
                        <h3><?php _e('SAMPLE DATA',ET_DOMAIN) ?></h3>

                        <div class="desc small"><?php _e('The sample data include some items from the list below: profile, project, etc.',ET_DOMAIN) ?></div>

                        <div class="btn-language padding-top10 f-left-all" style="padding-bottom:15px;height:65px;margin:0;">
                        <?php
                            $sample_data_op = get_option('option_sample_data');
                            if (!$sample_data_op) {
                                echo '<button class="primary-button" id="install_sample_data">'.__("Install sample data", ET_DOMAIN).'</button>';
                            }
                            else{
                                echo '<button class="primary-button" id="delete_sample_data">'.__("Delete sample data", ET_DOMAIN).'</button>';
                            }
                        ?>
                        </div>
                    </div>

                    <div class="desc" style="padding-top:0px;">
                        <div class="title font-quicksand sample-title">
                            <a target="_blank" href="<?php echo admin_url( 'edit.php?post_type=mjob_post' ); ?>" ><?php _e('Microjobs',ET_DOMAIN) ?></a> <span class="description"><?php _e('Add new Microjob types or modify the sample ones to suit your site style.',ET_DOMAIN) ?></span>
                        </div>
                        <div class="title font-quicksand sample-title">
                            <a target="_blank" href="<?php echo admin_url('edit-tags.php?taxonomy=mjob_category&post_type=mjob_post'); ?>" ><?php _e('Microjob Categories',ET_DOMAIN) ?></a> <span class="description"><?php _e('Add new categories or modify the sample data to match your business.',ET_DOMAIN) ?></span></div>
                         <div class="title font-quicksand sample-title">
                            <a target="_blank" href="<?php echo admin_url('edit.php?post_type=mjob_profile'); ?>" ><?php _e('Profiles',ET_DOMAIN) ?></a> <span class="description"><?php _e('Add new profile types or modify the sample ones to suit your site style.',ET_DOMAIN) ?></span>
                        </div>
                        <div class="title font-quicksand sample-title">
                            <a target="_blank" href="<?php echo admin_url('edit-tags.php?taxonomy=country&post_type=mjob_profile'); ?>" ><?php _e('Profile Countries',ET_DOMAIN) ?></a> <span class="description"><?php _e('Add new area or modify the sample one to match your business.',ET_DOMAIN) ?></span>
                        </div>
                        <div class="title font-quicksand sample-title">
                            <a target="_blank" href="<?php echo admin_url('edit-tags.php?taxonomy=language&post_type=mjob_profile'); ?>" ><?php _e('Profile Languages',ET_DOMAIN) ?></a> <span class="description"><?php _e('Add new language or modify the sample one to match your business.',ET_DOMAIN) ?></span>
                        </div>
                        <div class="title font-quicksand sample-title">
                            <a target="_blank" href="<?php echo admin_url( 'edit.php?post_type=page' ); ?>" ><?php _e('Pages',ET_DOMAIN) ?></a> <span class="description"><?php _e('Modify the sample "About us, Contact us, ..." pages or add your extra pages when needed.',ET_DOMAIN) ?></span>
                        </div>
                        <div class="title font-quicksand sample-title">
                            <a target="_blank" href="<?php echo admin_url( 'edit.php' ); ?>" ><?php _e('Posts',ET_DOMAIN) ?></a> <span class="description"><?php _e('A couple of news & event posts have been added for your review. You can delete it or add your own posts here.',ET_DOMAIN) ?></span>
                        </div>
                    </div>
                </div>

                <div class="et-main-main clearfix inner-content <?php if (!$sample_data_op) echo 'hide'; ?>" id="overview-listplaces">

                    <div class="title font-quicksand" style="padding-bottom:60px;">
                        <h3><?php _e('MORE SETTINGS',ET_DOMAIN) ?></h3>
                        <div class="desc small"><?php _e('Enhance your site by customizing these other features',ET_DOMAIN) ?></div>
                    </div>

                    <div style="clear:both;"></div>

                    <div class="title font-quicksand  sample-title">
                        <a target="_blank"  href="admin.php?page=et-settings" ><?php _e('General Settings',ET_DOMAIN) ?></a> <span class="description"><?php _e('Modify your site information, social links, analytics script, or add a language, etc.',ET_DOMAIN) ?></span>
                    </div>

                    <div class="title font-quicksand sample-title">
                        <a target="_blank"  href="edit.php?post_type=page" ><?php _e('Front Page',ET_DOMAIN) ?></a> <span class="description"><?php _e('Rearrange content elements or add more information in your front page to suit your needs.',ET_DOMAIN) ?></span>
                    </div>

                    <div class="title font-quicksand sample-title">
                        <a target="_blank" href="nav-menus.php" ><?php _e('Menus',ET_DOMAIN) ?></a> <span class="description"><?php _e('Edit all available menus in your site here.',ET_DOMAIN) ?></span>
                    </div>

                    <div class="title font-quicksand sample-title">
                        <a href="widgets.php" target="_blank"><?php _e('Sidebars & Widgets',ET_DOMAIN) ?></a> <span class="description"><?php _e('Add or remove widgets in sidebars throughout the site to best suit your need.',ET_DOMAIN) ?></span>
                    </div>

                </div>
            </div>
        </div>
        <style type="text/css">
        .hide{display: none;}.et-main-left .title, .et-main-main .title {text-transform: none;}.et-main-main{margin-left:0;}.title.font-quicksand h3{margin-bottom:0;margin-top:0;}.desc.small,span.description{font-family:Arial, sans-serif!important;font-weight:400;font-size:16px!important;color:#9d9d9d;font-style:normal; margin-top:10px; }span.description{margin-left:30px;}.sample-title{color:#427bab!important;padding-left:20px!important;font-size:18px!important;}.title.font-quicksand{padding-top:15px;}a.primary-button{right:50px;position:absolute;text-decoration:none;color:#ff9b78;}.et-main-main .title{padding-left:20px;}.sample-title a{text-decoration: none;}
        </style>
    <?php
        $html = ob_get_clean();
        return $html;
    }
    /**
     * FrE notification after setup theme
     * @param string $noti
     * @return string $noti
     * @since 1.6.2
     * @package void
     * @category void
     * @author Tambh
     */
    public function fre_notice_after_installing_theme( $noti ){
        $noti = sprintf( __("You have just installed MicrojobEngine theme, we recommend you follow through our <a href='%s'>setup wizard</a> to set up the basic configuration for your website! <a href='%s'>Close this message</a>", ET_DOMAIN), admin_url('admin.php?page=et-wizard'), add_query_arg('close_notices', '1'));
        return $noti;
    }
    /**
     * Set static page after insert sample data
     * @param void
     * @return void
     * @since void
     * @package void
     * @category void
     * @author Tambh
     */
    public function fre_after_insert_sample_data(){
        if( get_page(8) ){
            update_option('page_on_front', 8);
        }
        if( $blogid = url_to_postid(et_get_page_link('blog') ) ){
            update_option( 'page_for_posts ', $blogid );
        }
        update_option( 'show_on_front', 'page' );
    }
    /**
     * Admin setup
     */
    function admin_setup() {
        // disable admin bar for all users except admin
        if (!current_user_can('administrator') && !is_admin()) {
            show_admin_bar(false);
        }

        $sections = array();

        /**
         * General settings
         */
        $sections['general-settings'] = array(
            'args' => array(
                'title' => __("General", ET_DOMAIN) ,
                'id' => 'general-settings',
                'icon' => 'y',
                'class' => ''
            ) ,
            'groups' => array(
                array(
                    'args' => array(
                        'title' => __("Website Title", ET_DOMAIN) ,
                        'id' => 'site-name',
                        'class' => '',
                        'desc' => __("Enter your website title.", ET_DOMAIN)
                    ) ,

                    'fields' => array(
                        array(
                            'id' => 'blogname',
                            'type' => 'text',
                            'title' => __("Website Title", ET_DOMAIN) ,
                            'name' => 'blogname',
                            'class' => 'option-item bg-grey-input'
                        )
                    )
                ) ,
                array(
                    'args' => array(
                        'title' => __("Website Description", ET_DOMAIN) ,
                        'id' => 'site-description',
                        'class' => '',
                        'desc' => __("Enter your website description", ET_DOMAIN)
                    ) ,

                    'fields' => array(
                        array(
                            'id' => 'blogdescription',
                            'type' => 'text',
                            'title' => __("Website Title", ET_DOMAIN) ,
                            'name' => 'blogdescription',
                            'class' => 'option-item bg-grey-input '
                        )
                    )
                ) ,
                array(
                    'args' => array(
                        'title' => __("Copyright", ET_DOMAIN) ,
                        'id' => 'site-copyright',
                        'class' => '',
                        'desc' => __("This copyright information will appear in the footer.", ET_DOMAIN)
                    ) ,

                    'fields' => array(
                        array(
                            'id' => 'copyright',
                            'type' => 'text',
                            'title' => __("Copyright", ET_DOMAIN) ,
                            'name' => 'copyright',
                            'class' => 'option-item bg-grey-input '
                        )
                    )
                ) ,
                array(
                    'args' => array(
                        'title' => __("Google Analytics Script", ET_DOMAIN) ,
                        'id' => 'site-analytics',
                        'class' => '',
                        'desc' => __("Google analytics is a service offered by Google that generates detailed statistics about the visits to a website.", ET_DOMAIN)
                    ) ,

                    'fields' => array(
                        array(
                            'id' => 'opt-ace-editor-js',
                            'type' => 'textarea',
                            'title' => __("Google Analytics Script", ET_DOMAIN) ,
                            'name' => 'google_analytics',
                            'class' => 'option-item bg-grey-input '
                        )
                    )
                ) ,
                array(
                    'args' => array(
                        'title' => __("Sign up introduction text", ET_DOMAIN) ,
                        'id' => 'sign-up-intro-text',
                        'class' => '',
                        'desc' => __("Write a brief to promote your sign up process.", ET_DOMAIN)
                    ) ,

                    'fields' => array(
                        array(
                            'id' => 'sign_up_intro_text',
                            'type' => 'editor',
                            'title' => __("Sign up introduction text", ET_DOMAIN) ,
                            'name' => 'sign_up_intro_text',
                            'class' => 'option-item bg-grey-input ',
                            'reset' => 1
                        )
                    )
                ) ,
                array(
                    'args' => array(
                        'title' => __("Email Confirmation ", ET_DOMAIN) ,
                        'id' => 'user-confirm',
                        'class' => '',
                        'desc' => __("Enabling this will require users to confirm their email addresses after registration.", ET_DOMAIN)
                    ) ,

                    'fields' => array(
                        array(
                            'id' => 'user_confirm',
                            'type' => 'switch',
                            'title' => __("Email Confirmation", ET_DOMAIN) ,
                            'name' => 'user_confirm',
                            'class' => ''
                        )
                    )
                ) ,
                array(
                    'args' => array(
                        'title' => __("Login in admin panel", ET_DOMAIN) ,
                        'id' => 'login_init',
                        'class' => '',
                        'desc' => __("Prevent direct login to admin page.", ET_DOMAIN)
                    ) ,

                    'fields' => array(
                        array(
                            'id' => 'login-init',
                            'type' => 'switch',
                            'label' => __("Enable this option will prevent directly login to admin page.", ET_DOMAIN) ,
                            'name' => 'login_init',
                            'class' => 'option-item bg-grey-input '
                        )
                    )
                ) ,
                array(
                    'args' => array(
                        'title' => __("Social Links", ET_DOMAIN) ,
                        'id' => 'Social-Links',
                        'class' => 'Social-Links',
                        'desc' => __("Social links are displayed in the footer and in your blog sidebar..", ET_DOMAIN) ,

                        // 'name' => 'currency'

                    ) ,
                    'fields' => array()
                ) ,

                array(
                    'args' => array(
                        'title' => __("Twitter URL", ET_DOMAIN) ,
                        'id' => 'site-twitter',
                        'class' => 'payment-gateway',

                        //'desc' => __("Your twitter link .", ET_DOMAIN)

                    ) ,

                    'fields' => array(
                        array(
                            'id' => 'site-twitter',
                            'type' => 'text',
                            'title' => __("Twitter URL", ET_DOMAIN) ,
                            'name' => 'site_twitter',
                            'class' => 'option-item bg-grey-input '
                        )
                    )
                ) ,
                array(
                    'args' => array(
                        'title' => __("Facebook URL", ET_DOMAIN) ,
                        'id' => 'site-facebook',
                        'class' => 'payment-gateway',

                        //'desc' => __(".", ET_DOMAIN)

                    ) ,

                    'fields' => array(
                        array(
                            'id' => 'site-facebook',
                            'type' => 'text',
                            'title' => __("Copyright", ET_DOMAIN) ,
                            'name' => 'site_facebook',
                            'class' => 'option-item bg-grey-input '
                        )
                    )
                ) ,
                array(
                    'args' => array(
                        'title' => __("Google Plus URL", ET_DOMAIN) ,
                        'id' => 'site-google',
                        'class' => 'payment-gateway',

                        // 'desc' => __("This copyright information will appear in the footer.", ET_DOMAIN)

                    ) ,

                    'fields' => array(
                        array(
                            'id' => 'site-google',
                            'type' => 'text',
                            'title' => __("Google Plus URL", ET_DOMAIN) ,
                            'name' => 'site_google',
                            'class' => 'option-item bg-grey-input '
                        )
                    )
                )
            )
        );

        /**
         * Branding settings
         */
        $sections['branding'] = array(

            'args' => array(
                'title' => __("Branding", ET_DOMAIN) ,
                'id' => 'branding-settings',
                'icon' => 'b',
                'class' => ''
            ) ,

            'groups' => array(
                array(
                    'args' => array(
                        'title' => __("Site logo ", ET_DOMAIN) ,
                        'id' => 'site-logo',
                        'class' => '',
                        'name' => '',
                        'desc' => __("Your logo should be in PNG, GIF or JPG format, within 150x50px and less than 1500Kb.", ET_DOMAIN)
                    ) ,

                    'fields' => array(
                        array(
                            'id' => 'opt-ace-editor-js',
                            'type' => 'image',
                            'title' => __("Site Logo", ET_DOMAIN) ,
                            'name' => 'site_logo',
                            'class' => '',
                            'size' => array(
                                '150',
                                '50'
                            )
                        )
                    )
                ) ,
//                array(
//                    'args' => array(
//                        'title' => __("Mobile logo", ET_DOMAIN) ,
//                        'id' => 'mobile-logo',
//                        'class' => '',
//                        'name' => '',
//                        'desc' => __("Your logo should be in PNG, GIF or JPG format, within 150x50px and less than 1500Kb.", ET_DOMAIN)
//                    ) ,
//
//                    'fields' => array(
//                        array(
//                            'id' => 'opt-ace-editor-js',
//                            'type' => 'image',
//                            'title' => __("Mobile Logo", ET_DOMAIN) ,
//                            'name' => 'mobile_logo',
//                            'class' => '',
//                            'size' => array(
//                                '150',
//                                '50'
//                            )
//                        )
//                    )
//                ) ,
//
                array(
                    'args' => array(
                        'title' => __("Favicon", ET_DOMAIN) ,
                        'id' => 'mobile-icon',
                        'class' => '',
                        'name' => '',
                        'desc' => __("This icon will be used as a launcher icon for iPhone and Android smartphones and also as the website favicon. The image dimensions should be 57x57px.", ET_DOMAIN)
                    ) ,

                    'fields' => array(
                        array(
                            'id' => 'opt-ace-editor-js',
                            'type' => 'image',
                            'title' => __("Mobile Icon", ET_DOMAIN) ,
                            'name' => 'mobile_icon',
                            'class' => '',
                            'size' => array(
                                '57',
                                '57'
                            )
                        )
                    )
                ) ,
                array(
                    'args' => array(
                        'title' => __("User default logo & avatar", ET_DOMAIN) ,
                        'id' => 'default-logo',
                        'class' => '',
                        'name' => '',
                        'desc' => __("Your logo should be in PNG, GIF or JPG format, within 150x150px and less than 1500Kb.", ET_DOMAIN)
                    ) ,

                    'fields' => array(
                        array(
                            'id' => 'opt-ace-editor-js',
                            'type' => 'image',
                            'title' => __("User default logo & avtar", ET_DOMAIN) ,
                            'name' => 'default_avatar',
                            'class' => '',
                            'size' => array(
                                '150',
                                '150'
                            )
                        )
                    )
                )
            )
        );
        /**
         * Content settings
         */
        $sections['content'] = array(
            'args' => array(
                'title' => __("Content", ET_DOMAIN) ,
                'id' => 'content-settings',
                'icon' => 'l',
                'class' => ''
            ) ,
            'groups' => array(
                array(
                    'args' => array(
                        'title' => __("Filter Bad Words", ET_DOMAIN) ,
                        'id'    => 'filter-bad-words',
                        'class' => '',
                        'desc'  => __("Each word is separated by comma (,). E.g. foo, boo, too.", ET_DOMAIN),
                    ) ,

                    'fields' => array(
                        array(
                            'id'    => 'filter_bad_words',
                            'type'  => 'textarea',
                            'title' => __("Enter bad words here", ET_DOMAIN),
                            'name'  => 'filter_bad_words',
                            'class' => 'option-item bg-grey-input',
                        )
                    )
                ),
                array(
                    'args' => array(
                        'title' => __("Bad word replacement", ET_DOMAIN) ,
                        'id' => 'bad-word-replace',
                        'class' => 'bad-word-replace',
                        'desc' => 'Give a replacement word for all bad words.',
                    ) ,
                    'fields' => array(
                        array(
                            'id' => 'bad_word_replace',
                            'type' => 'text',
                            'title' => __("Bad words replace", ET_DOMAIN) ,
                            'name' => 'bad_word_replace',
                            'placeholder' => __("Enter a word", ET_DOMAIN) ,
                            'class' => 'option-item bg-grey-input',
                        )
                    )
                ),
                array(
                    'args' => array(
                        'title' => __("File types", ET_DOMAIN) ,
                        'id' => 'file-types',
                        'class' => 'file-types',
                        'desc' => 'Set up file types allowed on your site. Each type is separated by comma (,). E.g. doc, zip, png. Default is pdf,doc,zip,psd,jpg,png.',
                    ) ,
                    'fields' => array(
                        array(
                            'id' => 'file_types',
                            'type' => 'text',
                            'title' => __("File types", ET_DOMAIN) ,
                            'name' => 'file_types',
                            'placeholder' => "",
                            'class' => 'option-item bg-grey-input',
                        )
                    )
                ),
                array(
                    'args' => array(
                        'title' => __("Maximum size of a file", ET_DOMAIN) ,
                        'id' => 'max-file-size',
                        'class' => 'max-file-size',
                        'desc' => sprintf(__('Give a maximum file size in mb. Default is %s', ET_DOMAIN), wp_max_upload_size() / (1024*1024).'mb')
                    ) ,
                    'fields' => array(
                        array(
                            'id' => 'max_file_size',
                            'type' => 'number',
                            'title' => __("Maximum file size", ET_DOMAIN) ,
                            'name' => 'max_file_size',
                            'placeholder' => __("mb", ET_DOMAIN) ,
                            'class' => 'option-item bg-grey-input',
                        )
                    )
                ),
                array(
                    'args' => array(
                        'title' => __("mJob price", ET_DOMAIN) ,
                        'id' => 'mjob-price',
                        'class' => 'mjob-price',
                        'desc' => sprintf(__("Set up the price for all mJobs. (%s)", ET_DOMAIN), ae_currency_sign(false)),
                    ) ,
                    'fields' => array(
                        array(
                            'id' => 'mjob_price',
                            'type' => 'number',
                            'title' => __("mJob price", ET_DOMAIN) ,
                            'name' => 'mjob_price',
                            'placeholder' => __("Enter price for all mJobs", ET_DOMAIN) ,
                            'class' => 'option-item bg-grey-input',
                            'default' => '5'
                        )
                    )
                ),
                array(
                    'args' => array(
                        'title' => __("Pending Review", ET_DOMAIN) ,
                        'id' => 'use-pending',
                        'class' => 'use-pending',
                        'desc' => __("Enabling this will make every new mJob posted pending until you review and approve it manually.", ET_DOMAIN) ,
                    ) ,
                    'fields' => array(
                        array(
                            'id' => 'use_pending',
                            'type' => 'switch',
                            'title' => __("Align", ET_DOMAIN) ,
                            'name' => 'use_pending',
                            'class' => 'option-item bg-grey-input '
                        )
                    )
                ),
                 array(
                    'args' => array(
                        'title' => __("Temp User ID to order a service without login", ET_DOMAIN) ,
                        'id' => 'mjob-temp-user-id',
                        'class' => 'mjob-temp-user-id',
                        'desc' => __("Setup a temp user id here", ET_DOMAIN)
                    ) ,
                    'fields' => array(
                        array(
                            'id' => 'mjob_temp_user_id',
                            'type' => 'number',
                            'title' => __("emp User ID for order a service without login", ET_DOMAIN) ,
                            'name' => 'mjob_temp_user_id',
                            'placeholder' => __("Enter user ID  for all order without login", ET_DOMAIN) ,
                            'class' => 'option-item bg-grey-input',
                            'default' => '1'
                        )
                    )
                ),
                array(
                    'args' => array(
                        'title' => __("Post job block", ET_DOMAIN) ,
                        'id' => 'post-job-block',
                        'class' => '',
                        'name' => '',
                    ) ,
                    'fields' => array(
                        array(
                            'id' => 'post-job-block',
                            'type' => 'desc',
                            'title' => "",
                            'text' => __("Set up your Post Job block with title and banner image.", ET_DOMAIN) ,
                            'class' => '',
                            'name' => 'mail_description'
                        )
                    )
                ),
                array(
                    'args' => array(
                        'title' => __("Title", ET_DOMAIN) ,
                        'id' => 'mjob-temp-user-id',
                        'class' => 'payment-gateway',
                        'desc' => __("Setup the title here", ET_DOMAIN)
                    ) ,
                    'fields' => array(
                        array(
                            'id' => 'mjob_post_block_title',
                            'type' => 'text',
                            'title' => "",
                            'name' => 'mjob_post_block_title',
                            'placeholder' => "",
                            'class' => 'option-item bg-grey-input',
                            'default' => __('Earn money with us', ET_DOMAIN)
                        )
                    )
                ),

                array(
                    'args' => array(
                        'title' => __("Banner image ", ET_DOMAIN) ,
                        'id' => 'mjob-post-block-image',
                        'class' => 'payment-gateway',
                        'name' => '',
                        'desc' => __("Insert image link here", ET_DOMAIN)
                    ) ,

                    'fields' => array(
                        array(
                            'id' => 'mjob_post_block_image',
                            'type' => 'text',
                            'title' => "",
                            'name' => 'mjob_post_block_image',
                            'class' => '',
                        )
                    )
                ) ,
                array(
                    'args' => array(
                        'title' => __("Home page settings", ET_DOMAIN) ,
                        'id' => 'home-page-settings',
                        'class' => '',
                        'name' => '',
                    ) ,
                    'fields' => array(
                        array(
                            'id' => 'home-page-setting',
                            'type' => 'desc',
                            'title' => "",
                            'text' => __("Set up your home page settings  with blocks' title.", ET_DOMAIN) ,
                            'class' => '',
                            'name' => 'home_page_settings'
                        )
                    )
                ),
                array(
                    'args' => array(
                        'title' => __("Featured category title", ET_DOMAIN) ,
                        'id' => 'featured_category_title',
                        'class' => 'payment-gateway',
                        'desc' => __("Setup the title here", ET_DOMAIN)
                    ) ,
                    'fields' => array(
                        array(
                            'id' => 'featured_categories_block_title',
                            'type' => 'text',
                            'title' => "",
                            'name' => 'featured_categories_block_title',
                            'placeholder' => "",
                            'class' => '',
                            'default' => __('FIND WHAT YOU NEED', ET_DOMAIN)
                        )
                    )
                ),
                array(
                    'args' => array(
                        'title' => __("Featured micro job title", ET_DOMAIN) ,
                        'id' => 'featured_mjob_title',
                        'class' => 'payment-gateway',
                        'desc' => __("Setup the title here", ET_DOMAIN)
                    ) ,
                    'fields' => array(
                        array(
                            'id' => 'featured_mjob_block_title',
                            'type' => 'text',
                            'title' => "",
                            'name' => 'featured_mjob_block_title',
                            'placeholder' => "",
                            'class' => 'option-item bg-grey-input',
                            'default' => __('LATEST MICROJOBS', ET_DOMAIN)
                        )
                    )
                ),
                array(
                    'args' => array(
                        'title' => __("Microjob order settings", ET_DOMAIN) ,
                        'id' => 'mjob-order-settings',
                        'class' => '',
                        'name' => '',
                    ) ,
                    'fields' => array(
                        array(
                            'id' => 'mjob-order-setting',
                            'type' => 'desc',
                            'title' => "",
                            'text' => __("Set up microjob's order settings.", ET_DOMAIN) ,
                            'class' => '',
                            'name' => 'mjob_order_setting'
                        )
                    )
                ),
                array(
                    'args' => array(
                        'title' => __("Time Limit on Finishing Order", ET_DOMAIN) ,
                        'id' => 'mjob-order-finish-duration',
                        'class' => 'payment-gateway',
                        'desc' => __("Setup the time (days) that buyer has to finish an order once seller delivers his mJob", ET_DOMAIN)
                    ) ,
                    'fields' => array(
                        array(
                            'id' => 'mjob_order_finish_duration',
                            'type' => 'number',
                            'title' => "",
                            'name' => 'mjob_order_finish_duration',
                            'placeholder' => "",
                            'class' => 'option-item bg-grey-input positive_int',
                            'default' => 7
                        )
                    )
                ),
            )
        );

        /**
         * Payment settings
         */
        $sections['payment_settings'] = array(
            'args' => array(
                'title' => __("Payment", ET_DOMAIN) ,
                'id' => 'payment-settings',
                'icon' => '%',
                'class' => ''
            ) ,

            'groups' => array(

                array(
                    'args' => array(
                        'title' => __("Payment Currency", ET_DOMAIN) ,
                        'id' => 'payment-currency',
                        'class' => 'list-package',
                        'desc' => __("Enter currency code and sign.", ET_DOMAIN) ,
                        'name' => 'currency'
                    ) ,
                    'fields' => array(
                        array(
                            'id' => 'currency-code',
                            'type' => 'text',
                            'title' => __("Code", ET_DOMAIN) ,
                            'name' => 'code',
                            'placeholder' => __("Code", ET_DOMAIN) ,
                            'class' => 'option-item bg-grey-input '
                        ) ,
                        array(
                            'id' => 'currency-sign',
                            'type' => 'text',
                            'title' => __("Sign", ET_DOMAIN) ,
                            'name' => 'icon',
                            'placeholder' => __("Sign", ET_DOMAIN) ,
                            'class' => 'option-item bg-grey-input '
                        ) ,
                        array(
                            'id' => 'currency-align',
                            'type' => 'switch',
                            'title' => __("Align", ET_DOMAIN) ,
                            'name' => 'align',

                            // 'label' => __("Code", ET_DOMAIN),
                            'class' => 'option-item bg-grey-input ',
                            'label_1' => __("Left", ET_DOMAIN) ,
                            'label_2' => __("Right", ET_DOMAIN) ,
                        ) ,
                    )
                ),

                array(
                    'args' => array(
                        'title' => __("Number Format", ET_DOMAIN) ,
                        'id' => 'number-format',
                        'class' => 'list-package',
                        'desc' => __("Format a number with grouped thousands", ET_DOMAIN) ,
                        'name' => 'number_format'
                    ) ,
                    'fields' => array(
                        array(
                            'id' => 'decimal-point',
                            'type' => 'text',
                            'title' => __("Decimal point", ET_DOMAIN) ,
                            'label' => __("Decimal point", ET_DOMAIN) ,
                            'name' => 'dec_point',
                            'placeholder' => __("Decimal point", ET_DOMAIN) ,
                            'class' => 'option-item bg-grey-input '
                        ),
                        array(
                            'id' => 'thousand_sep',
                            'type' => 'text',
                            'label' => __("Thousand separator", ET_DOMAIN) ,
                            'title' => __("Thousand separator", ET_DOMAIN) ,
                            'name' => 'thousand_sep',
                            'placeholder' => __("Thousand separator", ET_DOMAIN) ,
                            'class' => 'option-item bg-grey-input '
                        ),
                        array(
                            'id' => 'et_decimal',
                            'type' => 'number',
                            'label' => __("Number of decimal points", ET_DOMAIN) ,
                            'title' => __("Number of decimal points", ET_DOMAIN) ,
                            'name' => 'et_decimal',
                            'placeholder' => __("Sets the number of decimal points.", ET_DOMAIN) ,
                            'class' => 'option-item bg-grey-input positive_int',
                            'default' => 2
                        ),
                    )
                ),
                array(
                    'args' => array(
                        'title' => __("Commission", ET_DOMAIN) ,
                        'id' => 'commission-format',
                        'class' => 'list-package',
                        'desc' => __("Setup commission fee as percentage (%) of mJob price.", ET_DOMAIN) ,
                        'name' => 'order_commissions'
                    ) ,
                    'fields' => array(
                        array(
                            'id' => 'order-commission',
                            'type' => 'number',
//                            'title' => __("Order commission", ET_DOMAIN) ,
//                            'label' => __("Order commission", ET_DOMAIN) ,
                            'name' => 'order_commission',
                            'placeholder' => __("10", ET_DOMAIN) ,
                            'class' => 'option-item bg-grey-input ',
                            'default'=> 10
                        )
                    )
                ),
                array(
                    'args' => array(
                        'title' => __("Free to submit mJob", ET_DOMAIN) ,
                        'id' => 'free-to-submit-mjob',
                        'class' => 'free-to-submit-mjob',
                        'desc' => __("Enabling this will allow users to submit mJob free.", ET_DOMAIN),
                    ) ,
                    'fields' => array(
                        array(
                            'id' => 'disable_plan',
                            'type' => 'switch',
                            'title' => __("Free submit mJob", ET_DOMAIN) ,
                            'name' => 'disable_plan',
                            'class' => 'option-item bg-grey-input '
                        )
                    )
                ) ,

                /* payment test mode settings */
                array(
                    'args' => array(
                        'title' => __("Payment Test Mode", ET_DOMAIN) ,
                        'id' => 'payment-test-mode',
                        'class' => 'payment-test-mode',
                        'desc' => __("Enabling this will allow you to test payment without charging your account.", ET_DOMAIN) ,

                        // 'name' => 'currency'


                    ) ,
                    'fields' => array(
                        array(
                            'id' => 'test-mode',
                            'type' => 'switch',
                            'title' => __("Align", ET_DOMAIN) ,
                            'name' => 'test_mode',
                            'class' => 'option-item bg-grey-input '
                        )
                    )
                ) ,
                 // payment test mode

                /* payment gateways settings */
                array(
                    'args' => array(
                        'title' => __("Payment Gateways", ET_DOMAIN) ,
                        'id' => 'payment-gateways',
                        'class' => 'payment-gateways',
                        'desc' => __("Set payment plans your users can choose when posting new mJobs.", ET_DOMAIN) ,

                        // 'name' => 'currency'

                    ) ,
                    'fields' => array()
                ) ,

                array(
                    'args' => array(
                        'title' => __("PayPal", ET_DOMAIN) ,
                        'id' => 'Paypal',
                        'class' => 'payment-gateway',
                        'desc' => __("Enabling this will allow your users to pay through PayPal", ET_DOMAIN) ,

                        'name' => 'paypal'
                    ) ,
                    'fields' => array(
                        array(
                            'id' => 'paypal',
                            'type' => 'switch',
                            'title' => __("Align", ET_DOMAIN) ,
                            'name' => 'enable',
                            'class' => 'option-item bg-grey-input '
                        ) ,
                        array(
                            'id' => 'paypal_mode',
                            'type' => 'text',
                            'title' => __("Align", ET_DOMAIN) ,
                            'name' => 'api_username',
                            'class' => 'option-item bg-grey-input ',
                            'placeholder' => __('Enter your PayPal email address', ET_DOMAIN)
                        )
                    )
                ) ,

                array(
                    'args' => array(
                        'title' => __("2Checkout", ET_DOMAIN) ,
                        'id' => '2Checkout',
                        'class' => 'payment-gateway',
                        'desc' => __("Enabling this will allow your users to pay through 2Checkout", ET_DOMAIN) ,

                        'name' => '2checkout'
                    ) ,
                    'fields' => array(
                        array(
                            'id' => '2Checkout_mode',
                            'type' => 'switch',
                            'title' => __("2Checkout mode", ET_DOMAIN) ,
                            'name' => 'enable',
                            'class' => 'option-item bg-grey-input '
                        ) ,
                        array(
                            'id' => 'sid',
                            'type' => 'text',
                            'title' => __("Sid", ET_DOMAIN) ,
                            'name' => 'sid',
                            'class' => 'option-item bg-grey-input ',
                            'placeholder' => __('Your 2Checkout Seller ID', ET_DOMAIN)
                        ) ,
                        array(
                            'id' => 'secret_key',
                            'type' => 'text',
                            'title' => __("Secret Key", ET_DOMAIN) ,
                            'name' => 'secret_key',
                            'class' => 'option-item bg-grey-input ',
                            'placeholder' => __('Your 2Checkout Secret Key', ET_DOMAIN)
                        )
                    )
                ) ,
                array(
                    'args' => array(
                        'title' => __("Cash", ET_DOMAIN) ,
                        'id' => 'Cash',
                        'class' => 'payment-gateway',
                        'desc' => __("Enabling this will allow your user to send cash to your bank account.", ET_DOMAIN) ,
                        'name' => 'cash'
                    ) ,
                    'fields' => array(
                        array(
                            'id' => 'cash_message_enable',
                            'type' => 'switch',
                            'title' => __("Align", ET_DOMAIN) ,
                            'name' => 'enable',
                            'class' => 'option-item bg-grey-input '
                        ) ,
                        array(
                            'id' => 'cash_message',
                            'type' => 'editor',
                            'title' => __("Align", ET_DOMAIN) ,
                            'name' => 'cash_message',
                            'class' => 'option-item bg-grey-input ',
                        )
                    )
                ) ,

                /**
                 * package plan list
                 */
                array(
                    'type' => 'list',
                    'args' => array(
                        'title' => __("Payment Plans", ET_DOMAIN) ,
                        'id' => 'list-package',
                        'class' => 'list-package',
                        'desc' => '',
                        'name' => 'payment_package',
                    ) ,

                    'fields' => array(
                        'form' => '/admin-template/package-form.php',
                        'form_js' => '/admin-template/package-form-js.php',
                        'js_template' => '/admin-template/package-js-item.php',
                        'template' => '/admin-template/package-item.php'
                    )
                ),
            )
        );

        /**
         * Content settings
         */
        $sections['withdraw'] = array(
            'args' => array(
                'title' => __("Withdraw", ET_DOMAIN) ,
                'id' => 'withdraw-settings',
                'icon' => '%',
                'class' => ''
            ) ,
            'groups' => array(
                array(
                    'args' => array(
                        'title' => __("Minimum amount of money for withdrawal", ET_DOMAIN) ,
                        'id' => 'minimum-withdraw',
                        'class' => '',
                        'desc' => sprintf(__("Set up the minimum amount allowed for withdrawal. (%s)", ET_DOMAIN), ae_currency_sign(false)),
                    ) ,
                    'fields' => array(
                        array(
                            'id' => 'minimum_withdraw',
                            'type' => 'number',
                            'title' => "",
                            'name' => 'minimum_withdraw',
                            'placeholder' => __("Enter amount of money", ET_DOMAIN) ,
                            'class' => 'option-item bg-grey-input',
                            'default' => '20'
                        )
                    )
                )
            )
        );
        /**
         * video background settings section
         */
        $sections['search_video'] = array(

            'args' => array(
                'title' => __("Search settings", ET_DOMAIN) ,
                'id' => 'search-settings',
                'icon' => 'V',
                'class' => ''
            ) ,

            'groups' => array(

//                array(
//                    'args' => array(
//                        'title' => __("Video Background Url", ET_DOMAIN) ,
//                        'id' => 'header-slider-settings',
//                        'class' => '',
//                        'desc' => __("Enter your video background url in page-home.php template (.mp4)", ET_DOMAIN)
//                    ) ,
//
//                    'fields' => array(
//                        array(
//                            'id' => 'header-video',
//                            'type' => 'text',
//                            'title' => __("header video url", ET_DOMAIN) ,
//                            'name' => 'header_video',
//                            'class' => 'option-item bg-grey-input ',
//                            'placeholder' => __('Enter your header video url', ET_DOMAIN)
//                        )
//                    )
//                ) ,
//                array(
//                    'args' => array(
//                        'title' => __("Video Background Via Youtube ID", ET_DOMAIN) ,
//                        'id' => 'header-youtube_id',
//                        'class' => '',
//                        'desc' => __("Enter youtube ID for background video instead of video url", ET_DOMAIN)
//                    ) ,
//
//                    'fields' => array(
//                        array(
//                            'id' => 'youtube_id-video',
//                            'type' => 'text',
//                            'title' => __("header video url", ET_DOMAIN) ,
//                            'name' => 'header_youtube_id',
//                            'class' => 'option-item bg-grey-input ',
//                            'placeholder' => __('Enter youtube video ID', ET_DOMAIN)
//                        )
//                    )
//                ) ,

                array(
                    'args' => array(
                        'title' => __("Homepage Background", ET_DOMAIN) ,
                        'id' => 'header-slider-settings',
                        'class' => '',
                        'desc' => __("Background image visitors first see when coming to your site.", ET_DOMAIN)
                    ) ,

                    'fields' => array(
                        array(
                            'id' => 'header-video',
                            'type' => 'text',
                            'title' => __("search video url", ET_DOMAIN) ,
                            'name' => 'search_video_fallback',
                            'class' => 'option-item bg-grey-input ',
                            'placeholder' => __('Enter your background image URL', ET_DOMAIN)
                        )
                    )
                ) ,

                array(
                    'args' => array(
                        'title' => __("Homepage Headline", ET_DOMAIN) ,
                        'id' => 'header-slider-settings',
                        'class' => '',
                        'name' => 'site_demonstration'

                        // 'desc' => __("Enter your header slider setting", ET_DOMAIN)

                    ) ,

                    'fields' => array(
                        array(
                            'id' => 'search-heading-text',
                            'type' => 'text',
                            'title' => __("Headline in homepage", ET_DOMAIN) ,
                            'name' => 'search_heading_text',
                            'class' => 'option-item bg-grey-input ',
                            'label' => __('Headline in homepage', ET_DOMAIN)
                        ) ,
                        array(
                            'id' => 'search-normal-text',
                            'type' => 'text',
                            'title' => __("Sub headline in homepage", ET_DOMAIN) ,
                            'name' => 'search_normal_text',
                            'class' => 'option-item bg-grey-input ',
                            'label' => __('Sub headline in homepage', ET_DOMAIN)
                        ),
						array(
                            'id' => 'search-input-textform',
                            'type' => 'text',
                            'title' => __("Search Form in homepage", ET_DOMAIN) ,
                            'name' => 'search_input_textform',
                            'class' => '',
                            'label' => __('Search Form in homepage', ET_DOMAIN)
                        )
                    )
                ) ,
//                array(
//                    'args' => array(
//                        'title' => __("Loop Header Video", ET_DOMAIN) ,
//                        'id' => 'header-video-loop-option',
//                        'class' => '',
//                        'desc' => __(" Enabling this will make the video on the header automatically repeated.", ET_DOMAIN)
//                    ) ,
//                    'fields' => array(
//                        array(
//                            'id' => 'header-video-loop',
//                            'type' => 'switch',
//                            'title' => __("Select video loop", ET_DOMAIN) ,
//                            'name' => 'header_video_loop',
//                            'class' => 'option-item bg-grey-input '
//                        )
//                    )
//                )
            )
        );
        $sections['about-settings'] = array(
            'args' => array(
                'title' => __("About settings", ET_DOMAIN) ,
                'id' => 'about-settings',
                'icon' => 'y',
                'class' => ''
            ) ,
            'groups' => array(
                array(
                    'args' => array(
                        'title' => __("Section title", ET_DOMAIN) ,
                        'id' => 'section-title',
                        'class' => '',
                        'desc' => __("Enter your section title.", ET_DOMAIN)
                    ) ,

                    'fields' => array(
                        array(
                            'id' => 'about_title',
                            'type' => 'text',
                            'title' => __("Section title", ET_DOMAIN) ,
                            'name' => 'about_title',
                            'class' => 'option-item bg-grey-input'
                        )
                    )
                ) ,
                array(
                    'args' => array(
                        'title' => __("About page link", ET_DOMAIN) ,
                        'id' => 'site-description',
                        'class' => '',
                        'desc' => __("Enter your about page link", ET_DOMAIN)
                    ) ,

                    'fields' => array(
                        array(
                            'id' => 'about_link',
                            'type' => 'text',
                            'title' => __("About page link", ET_DOMAIN) ,
                            'name' => 'about_link',
                            'class' => 'option-item bg-grey-input '
                        )
                    )
                ) ,
                array(
                    'args' => array(
                        'title' => __("First column  setting", ET_DOMAIN) ,
                        'id' => 'about-col-1',
                        'class' => '',
                        'desc' => __("Set the first column's content", ET_DOMAIN),
                        'name'=> 'about_col_1'
                    ) ,

                    'fields' => array(
                        array(
                            'id' => 'about_col_1_title',
                            'type' => 'text',
                            'title' => __("First column title", ET_DOMAIN) ,
                            'label' => __("First column title", ET_DOMAIN) ,
                            'name' => 'about_col_1_title',
                            'class' => 'option-item bg-grey-input '
                        ),
                         array(
                            'id' => 'about_col_1_link',
                            'type' => 'text',
                            'title' => __("First column link", ET_DOMAIN) ,
                            'label' => __("First column link", ET_DOMAIN) ,
                            'name' => 'about_col_1_link',
                            'class' => 'option-item bg-grey-input '
                        ),
                        array(
                            'id' => 'about_col_1_desc',
                            'type' => 'textarea',
                            'title' => __("First column description", ET_DOMAIN) ,
                            'label' => __("First column description", ET_DOMAIN) ,
                            'name' => 'about_col_1_desc',
                            'class' => 'option-item bg-grey-input '
                        )
                    )
                ) ,
                array(
                    'args' => array(
                        'title' => __("First column's image  setting", ET_DOMAIN) ,
                        'id' => 'about-col-1-image',
                        'class' => '',
                        'desc' => __("First column image(Recommended upload size is 30x30 pixels.  Image file formats: JPG, PNG, GIF)", ET_DOMAIN) ,
                    ) ,
                    'fields' => array(
                         array(
                            'id' => 'about_col_1_image',
                            'type' => 'image',
                            'title' => __("First column image", ET_DOMAIN) ,
                            //'label' => __("First column image(Recommended upload size is 30x30 pixels.  Image file formats: JPG, PNG, GIF)", ET_DOMAIN) ,
                            'name' => 'about_col_1_image',
                            'class' => '',
                            'size' => array(
                                '30',
                                '30'
                            )
                        )
                    )
                ),
                array(
                    'args' => array(
                        'title' => __("Second column  setting", ET_DOMAIN) ,
                        'id' => 'about-col-2',
                        'class' => '',
                        'desc' => __("Set the second column's content", ET_DOMAIN),
                        'name'=> 'about_col_2'
                    ) ,

                    'fields' => array(
                        array(
                            'id' => 'about_col_2_title',
                            'type' => 'text',
                            'title' => __("Second column title", ET_DOMAIN) ,
                            'label' => __("Second column title", ET_DOMAIN) ,
                            'name' => 'about_col_2_title',
                            'class' => 'option-item bg-grey-input '
                        ),
                        array(
                            'id' => 'about_col_2_link',
                            'type' => 'text',
                            'title' => __("Second column link", ET_DOMAIN) ,
                            'label' => __("Second column link", ET_DOMAIN) ,
                            'name' => 'about_col_2_link',
                            'class' => 'option-item bg-grey-input '
                        ),
                        array(
                            'id' => 'about_col_2_desc',
                            'type' => 'textarea',
                            'title' => __("Second column description", ET_DOMAIN) ,
                            'label' => __("Second column description", ET_DOMAIN) ,
                            'name' => 'about_col_2_desc',
                            'class' => 'option-item bg-grey-input '
                        )
                    )
                ) ,
                 array(
                    'args' => array(
                        'title' => __("Second column's image  setting", ET_DOMAIN) ,
                        'id' => 'about-col-2-image',
                        'class' => '',
                        'desc' => __("Second column image(Recommended upload size is 30x30 pixels.  Image file formats: JPG, PNG, GIF)", ET_DOMAIN),
                    ) ,
                    'fields' => array(
                         array(
                            'id' => 'about_col_2_image',
                            'type' => 'image',
                            'title' => __("Second column image", ET_DOMAIN) ,
                            //'label' => __("Second column image(Recommended upload size is 30x30 pixels.  Image file formats: JPG, PNG, GIF)", ET_DOMAIN) ,
                            'name' => 'about_col_2_image',
                            'class' => '',
                            'size' => array(
                                '30',
                                '30'
                            )
                        )
                    )
                ),
                array(
                    'args' => array(
                        'title' => __("Third column  setting", ET_DOMAIN) ,
                        'id' => 'about-col-3',
                        'class' => '',
                        'desc' => __("Set the Third column's content", ET_DOMAIN),
                        'name'=> 'about_col_3'
                    ) ,

                    'fields' => array(
                        array(
                            'id' => 'about_col_3_title',
                            'type' => 'text',
                            'title' => __("Third column title", ET_DOMAIN) ,
                            'label' => __("Third column title", ET_DOMAIN) ,
                            'name' => 'about_col_3_title',
                            'class' => 'option-item bg-grey-input '
                        ),
                        array(
                            'id' => 'about_col_3_link',
                            'type' => 'text',
                            'title' => __("Third column link", ET_DOMAIN) ,
                            'label' => __("Third column link", ET_DOMAIN) ,
                            'name' => 'about_col_3_link',
                            'class' => 'option-item bg-grey-input '
                        ),
                        array(
                            'id' => 'about_col_3_desc',
                            'type' => 'textarea',
                            'title' => __("Third column description", ET_DOMAIN) ,
                            'label' => __("Third column description", ET_DOMAIN) ,
                            'name' => 'about_col_3_desc',
                            'class' => 'option-item bg-grey-input '
                        )
                    )
                ) ,
                array(
                    'args' => array(
                        'title' => __("Third column's image  setting", ET_DOMAIN) ,
                        'id' => 'about-col-3-image',
                        'class' => '',
                        'desc' => __("Third column image(Recommended upload size is 30x30 pixels.  Image file formats: JPG, PNG, GIF)", ET_DOMAIN),
                    ) ,
                    'fields' => array(
                         array(
                            'id' => 'about_col_3_image',
                            'type' => 'image',
                            'title' => __("Third column image", ET_DOMAIN) ,
                            //'label' => __("Second column image(Recommended upload size is 30x30 pixels.  Image file formats: JPG, PNG, GIF)", ET_DOMAIN) ,
                            'name' => 'about_col_3_image',
                            'class' => '',
                            'size' => array(
                                '30',
                                '30'
                            )
                        )
                    )
                )
               )
        );
        /**
         * slug settings
         */
         $sections['url_slug'] = array(
             'args' => array(
                 'title' => __("Url slug", ET_DOMAIN) ,
                 'id' => 'Url-Slug',
                 'icon' => 'i',
                 'class' => ''
             ) ,
             'groups' => array(
                 array(
                     'args' => array(
                         'title' => __("Microjob Listing", ET_DOMAIN) ,
                         'id' => 'mjob-slug',
                         'class' => 'list-package',
                         'desc' => __("Enter a string to customize microjob listing permalink structure slug.", ET_DOMAIN) ,
                     ) ,
                     'fields' => array(
                         array(
                             'id' => 'mjob_post_archive',
                             'type' => 'text',
                             'title' => __("Listing microjob page Slug", ET_DOMAIN) ,
                             'name' => 'mjob_post_archive',
                             'placeholder' => __("Listing microjob page Slug", ET_DOMAIN) ,
                             'class' => 'option-item bg-grey-input ',
                             'default' => 'mjob_post'
                         )
                     )

                 ),
                 array(
                     'args' => array(
                         'title' => __("Microjob Single", ET_DOMAIN) ,
                         'id' => 'mjob-slug',
                         'class' => 'list-package',
                         'desc' => __("Enter a string to customize microjob single permalink structure slug.", ET_DOMAIN) ,
                     ) ,
                     'fields' => array(
                         array(
                             'id' => 'mjob_post_slug',
                             'type' => 'text',
                             'title' => __("Single microjob page Slug", ET_DOMAIN) ,
                             'name' => 'mjob_post_slug',
                             'placeholder' => __("Single microjob page Slug", ET_DOMAIN) ,
                             'class' => 'option-item bg-grey-input ',
                             'default' => 'mjob_post'
                         )
                     )
                 ) ,
                 array(
                     'args' => array(
                         'title' => __("Microjob Category", ET_DOMAIN) ,
                         'id' => 'microjob-category',
                         'class' => 'list-package',
                         'desc' => __("Enter a string to customize microjob category permalink structure slug.", ET_DOMAIN) ,
                     ) ,
                     'fields' => array(
                         array(
                             'id' => 'mjob_category_slug',
                             'type' => 'text',
                             'title' => __("Microjob category page Slug", ET_DOMAIN) ,
                             'name' => 'mjob_category_slug',
                             'placeholder' => __("Microjob category page Slug", ET_DOMAIN) ,
                             'class' => 'option-item bg-grey-input ',
                             'default' => 'mjob_category',
                         )
                     )
                 ) ,
                 array(
                     'args' => array(
                         'title' => __("Tag", ET_DOMAIN) ,
                         'id' => 'mjob-skill',
                         'class' => 'list-package',
                         'desc' => __("Enter a string to customize tag permalink structure slug.", ET_DOMAIN) ,
                     ) ,
                     'fields' => array(
                         array(
                             'id' => 'skill_slug',
                             'type' => 'text',
                             'title' => __("Skill tag page Slug", ET_DOMAIN) ,
                             'name' => 'skill_slug',
                             'placeholder' => __("Microjob tag page Slug", ET_DOMAIN) ,
                             'class' => 'option-item bg-grey-input ',
                             'default' => 'skill'
                         )
                     )
                 )

             )
         );

        /**
         * video background settings section
         */
        // $sections['header_video'] = array(

        //     'args' => array(
        //         'title' => __("Header Video", ET_DOMAIN) ,
        //         'id' => 'header-settings',
        //         'icon' => 'V',
        //         'class' => ''
        //     ) ,

        //     'groups' => array(

        //         array(
        //             'args' => array(
        //                 'title' => __("Video Background Url", ET_DOMAIN) ,
        //                 'id' => 'header-slider-settings',
        //                 'class' => '',
        //                 'desc' => __("Enter your video background url in page-home.php template (.mp4)", ET_DOMAIN)
        //             ) ,

        //             'fields' => array(
        //                 array(
        //                     'id' => 'header-video',
        //                     'type' => 'text',
        //                     'title' => __("header video url", ET_DOMAIN) ,
        //                     'name' => 'header_video',
        //                     'class' => 'option-item bg-grey-input ',
        //                     'placeholder' => __('Enter your header video url', ET_DOMAIN)
        //                 )
        //             )
        //         ) ,
        //         array(
        //             'args' => array(
        //                 'title' => __("Video Background Via Youtube ID", ET_DOMAIN) ,
        //                 'id' => 'header-youtube_id',
        //                 'class' => '',
        //                 'desc' => __("Enter youtube ID for background video instead of video url", ET_DOMAIN)
        //             ) ,

        //             'fields' => array(
        //                 array(
        //                     'id' => 'youtube_id-video',
        //                     'type' => 'text',
        //                     'title' => __("header video url", ET_DOMAIN) ,
        //                     'name' => 'header_youtube_id',
        //                     'class' => 'option-item bg-grey-input ',
        //                     'placeholder' => __('Enter youtube video ID', ET_DOMAIN)
        //                 )
        //             )
        //         ) ,

        //         array(
        //             'args' => array(
        //                 'title' => __("Video Background Fallback", ET_DOMAIN) ,
        //                 'id' => 'header-slider-settings',
        //                 'class' => '',
        //                 'desc' => __("Fallback image for video background when browser not support", ET_DOMAIN)
        //             ) ,

        //             'fields' => array(
        //                 array(
        //                     'id' => 'header-video',
        //                     'type' => 'text',
        //                     'title' => __("header video url", ET_DOMAIN) ,
        //                     'name' => 'header_video_fallback',
        //                     'class' => 'option-item bg-grey-input ',
        //                     'placeholder' => __('Enter your header video fallback image url', ET_DOMAIN)
        //                 )
        //             )
        //         ) ,

        //         array(
        //             'args' => array(
        //                 'title' => __("Project Demonstration", ET_DOMAIN) ,
        //                 'id' => 'header-slider-settings',
        //                 'class' => '',
        //                 'name' => 'project_demonstration'

        //                 // 'desc' => __("Enter your header slider setting", ET_DOMAIN)

        //             ) ,

        //             'fields' => array(
        //                 array(
        //                     'id' => 'header-left-text',
        //                     'type' => 'text',
        //                     'title' => __("header left text", ET_DOMAIN) ,
        //                     'name' => 'home_page',
        //                     'class' => 'option-item bg-grey-input ',
        //                     'label' => __('Project demonstration on header video background which can be view by employer', ET_DOMAIN)
        //                 ) ,
        //                 array(
        //                     'id' => 'header-right-text',
        //                     'type' => 'text',
        //                     'title' => __("header right text", ET_DOMAIN) ,
        //                     'name' => 'list_project',
        //                     'class' => 'option-item bg-grey-input ',
        //                     'label' => __('Project demonstration on header video background which can be view by freelancer', ET_DOMAIN)
        //                 )
        //             )
        //         ) ,

        //         array(
        //             'args' => array(
        //                 'title' => __("Profile Demonstration", ET_DOMAIN) ,
        //                 'id' => 'header-slider-settings',
        //                 'class' => '',
        //                 'name' => 'profile_demonstration'

        //                 // 'desc' => __("Enter your header slider setting", ET_DOMAIN)

        //             ) ,

        //             'fields' => array(
        //                 array(
        //                     'id' => 'header-left-text',
        //                     'type' => 'text',
        //                     'title' => __("header left text", ET_DOMAIN) ,
        //                     'name' => 'home_page',
        //                     'class' => 'option-item bg-grey-input ',
        //                     'label' => __('Profile demonstration on header video background which can be view by freelancer', ET_DOMAIN)
        //                 ) ,
        //                 array(
        //                     'id' => 'header-right-text',
        //                     'type' => 'text',
        //                     'title' => __("header right text", ET_DOMAIN) ,
        //                     'name' => 'list_profile',
        //                     'class' => 'option-item bg-grey-input ',
        //                     'label' => __('Profiles demonstration on list profiles page which can be view by employer', ET_DOMAIN)
        //                 )
        //             )
        //         ) ,
        //         array(
        //             'args' => array(
        //                 'title' => __("Loop Header Video", ET_DOMAIN) ,
        //                 'id' => 'header-video-loop-option',
        //                 'class' => '',
        //                 'desc' => __(" Enabling this will make the video on the header automatically repeated.", ET_DOMAIN)
        //             ) ,
        //             'fields' => array(
        //                 array(
        //                     'id' => 'header-video-loop',
        //                     'type' => 'switch',
        //                     'title' => __("Select video loop", ET_DOMAIN) ,
        //                     'name' => 'header_video_loop',
        //                     'class' => 'option-item bg-grey-input '
        //                 )
        //             )
        //         )
        //     )
        // );

        /**
         * mail template settings section
         */
        $sections['mailing'] = array(
            'args' => array(
                'title' => __("Mailing", ET_DOMAIN) ,
                'id' => 'mail-settings',
                'icon' => 'M',
                'class' => ''
            ) ,

            'groups' => array(
                array(
                    'args' => array(
                        'title' => __("Authentication Mail Template", ET_DOMAIN) ,
                        'id' => 'mail-description-group',
                        'class' => '',
                        'name' => ''
                    ) ,
                    'fields' => array(
                        array(
                            'id' => 'mail-description',
                            'type' => 'desc',
                            'title' => __("Mail description here", ET_DOMAIN) ,
                            'text' => __("Email templates for authentication process. You can use placeholders to include some specific content.", ET_DOMAIN) . '<a class="icon btn-template-help payment" data-icon="?" href="#" title="View more details"></a>' . '<div class="cont-template-help payment-setting">
                                                    [user_login],[display_name],[user_email] : ' . __("user's details you want to send mail", ET_DOMAIN) . '<br />
                                                    [dashboard] : ' . __("member dashboard url ", ET_DOMAIN) . '<br />
                                                    [title], [link], [excerpt],[desc], [author] : ' . __("mJob title, link, details, author", ET_DOMAIN) . ' <br />
                                                    [activate_url] : ' . __("activate link is require for user to renew password", ET_DOMAIN) . ' <br />
                                                    [site_url],[blogname],[admin_email] : ' . __(" site info, admin email", ET_DOMAIN) . '
                                                    [project_list] : ' . __("list of mJobs a buyer sends to a seller when inviting him to join", ET_DOMAIN) . '

                                                </div>',

                            'class' => '',
                            'name' => 'mail_description'
                        )
                    )
                ) ,
                array(
                    'args' => array(
                        'title' => __("Register Mail Template for Company role", ET_DOMAIN) ,
                        'id' => 'register-mail',
                        'class' => 'payment-gateway',
                        'name' => '',
                        'desc' => __("Send to user when he registers successfully.", ET_DOMAIN),
                        'toggle' => true
                    ) ,
                    'fields' => array(
                        array(
                            'id' => 'register_mail_template',
                            'type' => 'editor',
                            'title' => __("Register Mail", ET_DOMAIN) ,
                            'name' => 'register_mail_template',
                            'class' => '',
                            'reset' => 1
                        )
                    )
                ) ,
                array(
                    'args' => array(
                        'title' => __("Register Mail Template For Individual role", ET_DOMAIN) ,
                        'id' => 'register-mail-1',
                        'class' => 'payment-gateway',
                        'name' => '',
                        'desc' => __("Send to user when he registers successfully.", ET_DOMAIN),
                        'toggle' => true
                    ) ,
                    'fields' => array(
                        array(
                            'id' => 'register_mail_template_individual',
                            'type' => 'editor',
                            'title' => __("Register Mail for Individual role", ET_DOMAIN) ,
                            'name' => 'register_mail_template_individual',
                            'class' => '',
                            'reset' => 1
                        )
                    )
                ) ,
                array(
                    'args' => array(
                        'title' => __("Confirm Mail Template", ET_DOMAIN) ,
                        'id' => 'confirm-mail',
                        'class' => 'payment-gateway',
                        'name' => '',
                        'desc' => __("Send to user after he registered successfully when option of confirming email is on.", ET_DOMAIN),
                        'toggle' => true
                    ) ,
                    'fields' => array(
                        array(
                            'id' => 'confirm_mail_template',
                            'type' => 'editor',
                            'title' => __("Confirme Mail", ET_DOMAIN) ,
                            'name' => 'confirm_mail_template',
                            'class' => '',
                            'reset' => 1
                        )
                    )
                ) ,

                array(
                    'args' => array(
                        'title' => __("Confirmed Mail Template", ET_DOMAIN) ,
                        'id' => 'confirmed-mail',
                        'class' => 'payment-gateway',
                        'name' => '',
                        'desc' => __("Send to user to notify that he has successfully confirmed the email.", ET_DOMAIN),
                        'toggle' => true
                    ) ,
                    'fields' => array(
                        array(
                            'id' => 'confirmed_mail_template',
                            'type' => 'editor',
                            'title' => __("Confirmed Mail", ET_DOMAIN) ,
                            'name' => 'confirmed_mail_template',
                            'class' => '',
                            'reset' => 1
                        )
                    )
                ) ,

                array(
                    'args' => array(
                        'title' => __("Forgotpass Mail Template", ET_DOMAIN) ,
                        'id' => 'forgotpass-mail',
                        'class' => 'payment-gateway',
                        'name' => '',
                        'desc' => __("Send to user when he requests password reset.", ET_DOMAIN),
                        'toggle' => true
                    ) ,
                    'fields' => array(
                        array(
                            'id' => 'forgotpass_mail_template',
                            'type' => 'editor',
                            'title' => __("Register Mail", ET_DOMAIN) ,
                            'name' => 'forgotpass_mail_template',
                            'class' => '',
                            'reset' => 1
                        )
                    )
                ) ,
                array(
                    'args' => array(
                        'title' => __("Resetpass Mail Template", ET_DOMAIN) ,
                        'id' => 'resetpass-mail',
                        'class' => 'payment-gateway',
                        'name' => '',
                        'desc' => __("Send to user to notify him of successful password reset.", ET_DOMAIN),
                        'toggle' => true
                    ) ,
                    'fields' => array(
                        array(
                            'id' => 'resetpass_mail_template',
                            'type' => 'editor',
                            'title' => __("Resetpassword Mail", ET_DOMAIN) ,
                            'name' => 'resetpass_mail_template',
                            'class' => '',
                            'reset' => 1
                        )
                    )
                ) ,

                array(
                    'args' => array(
                        'title' => __("Microjob Related Mail Template", ET_DOMAIN) ,
                        'id' => 'mail-description-group',
                        'class' => '',
                        'name' => '',
                    ) ,
                    'fields' => array(
                        array(
                            'id' => 'mail-description',
                            'type' => 'desc',
                            'title' => __("Mail description here", ET_DOMAIN) ,
                            'text' => __("Email templates used for microjob-related event. You can use placeholders to include some specific content", ET_DOMAIN) ,
                            'class' => '',
                            'name' => 'mail_description'
                        )
                    )
                ),

                // Email template when post a new mJob
                array(
                    'args' => array(
                        'title' => __('New post notification', ET_DOMAIN),
                        'id' => 'new-mjob-mail-template',
                        'class' => 'payment-gateway',
                        'name' => '',
                        'desc' => __("Send to admin when a user posts a new mJob", ET_DOMAIN),
                        'toggle' => true
                    ),
                    'fields' => array(
                        array(
                            'id' => 'new_mjob_mail_template',
                            'type' => 'editor',
                            'title' => __("New mJob mail here", ET_DOMAIN) ,
                            'text' => "",
                            'class' => '',
                            'name' => 'new_mjob_mail_template',
                            'reset' => 1
                        )
                    )
                ),
                // Email template when approve a mJob
                array(
                    'args' => array(
                        'title' => __('mJob approved notification', ET_DOMAIN),
                        'id' => 'approve-mjob-mail-template',
                        'class' => 'payment-gateway',
                        'name' => '',
                        'desc' => __("Send to a user to notify that one of his posted jobs has been approved.", ET_DOMAIN),
                        'toggle' => true
                    ),
                    'fields' => array(
                        array(
                            'id' => 'approve_mjob_mail_template',
                            'type' => 'editor',
                            'title' => __("Approve mJob mail here", ET_DOMAIN) ,
                            'text' => "",
                            'class' => '',
                            'name' => 'approve_mjob_mail_template',
                            'reset' => 1
                        )
                    )
                ),
                // Email template when reject a mJob
                array(
                    'args' => array(
                        'title' => __('mJob rejected notification', ET_DOMAIN),
                        'id' => 'reject-mail-template',
                        'class' => 'payment-gateway',
                        'name' => '',
                        'desc' => __("Send to a user to notify that one of his posted jobs has been rejected.", ET_DOMAIN),
                        'toggle' => true
                    ),
                    'fields' => array(
                        array(
                            'id' => 'reject_mail_template',
                            'type' => 'editor',
                            'title' => __("Reject mJob mail here", ET_DOMAIN) ,
                            'class' => '',
                            'name' => 'reject_mail_template',
                            'reset' => 1
                        )
                    )
                ),
                // Email template when archive a mjob
                array(
                    'args' => array(
                        'title' => __('mJob archived notification', ET_DOMAIN),
                        'id' => 'archived-mjob-mail-template',
                        'class' => 'payment-gateway',
                        'name' => '',
                        'desc' => __("Send to a user to notify that one of his posted jobs has been archived.", ET_DOMAIN),
                        'toggle' => true
                    ),
                    'fields' => array(
                        array(
                            'id' => 'archived_mjob_mail_template',
                            'type' => 'editor',
                            'title' => __("Archived mJob mail here", ET_DOMAIN) ,
                            'class' => '',
                            'name' => 'archived_mjob_mail_template',
                            'reset' => 1
                        )
                    )
                ),
                array(
                    'args' => array(
                        'title' => __("Pay Package Related Mail Template", ET_DOMAIN) ,
                        'id' => 'mail-description-group',
                        'class' => '',
                        'name' => '',
                    ) ,
                    'fields' => array(
                        array(
                            'id' => 'mail-description',
                            'type' => 'desc',
                            'title' => __("Mail description here", ET_DOMAIN) ,
                            'text' => __("Email templates used for pay package-related event. You can use placeholders to include some specific content", ET_DOMAIN) ,
                            'class' => '',
                            'name' => 'mail_description'
                        )
                    )
                ),
                // Email template when pay package
                array(
                    'args' => array(
                        'title' => __('Cash payment receipt notification', ET_DOMAIN),
                        'id' => 'pay-package-by-cash',
                        'class' => 'payment-gateway',
                        'name' => '',
                        'desc' => __("Send to user when he pays by cash", ET_DOMAIN),
                        'toggle' => true
                    ),
                    'fields' => array(
                        array(
                            'id' => 'pay_package_by_cash',
                            'type' => 'editor',
                            'title' => __("Pay package mail here", ET_DOMAIN) ,
                            'class' => '',
                            'name' => 'pay_package_by_cash',
                            'reset' => 1
                        )
                    )
                ),
                 array(
                    'args' => array(
                        'title' => __('Payment receipt notification', ET_DOMAIN),
                        'id' => 'pay-package-by-cash',
                        'class' => 'payment-gateway',
                        'name' => '',
                        'desc' => __("Send to user when he pays by other gateways (exclude Cash)", ET_DOMAIN),
                        'toggle' => true
                    ),
                    'fields' => array(
                        array(
                            'id' => 'ae_receipt_mail',
                            'type' => 'editor',
                            'title' => __("Pay package mail here", ET_DOMAIN) ,
                            'class' => '',
                            'name' => 'ae_receipt_mail',
                            'reset' => 1
                        )
                    )
                ),
                array(
                    'args' => array(
                        'title' => __("Order Related Mail Template", ET_DOMAIN) ,
                        'id' => 'mail-description-group',
                        'class' => '',
                        'name' => '',
                    ) ,
                    'fields' => array(
                        array(
                            'id' => 'mail-description',
                            'type' => 'desc',
                            'title' => __("Mail description here", ET_DOMAIN) ,
                            'text' => __("Email templates used for order-related event. You can use placeholders to include some specific content", ET_DOMAIN) ,
                            'class' => '',
                            'name' => 'mail_description'
                        )
                    )
                ),
                // Email template when user's mJob has been bought
                array(
                    'args' => array(
                        'title' => __('New order notification', ET_DOMAIN),
                        'id' => 'new-order',
                        'class' => 'payment-gateway',
                        'name' => '',
                        'desc' => __("Send to seller when a user orders his mJob.", ET_DOMAIN),
                        'toggle' => true
                    ),
                    'fields' => array(
                        array(
                            'id' => 'new_order',
                            'type' => 'editor',
                            'title' => "",
                            'class' => '',
                            'name' => 'new_order',
                            'reset' => 1
                        )
                    )
                ),
                // Email template when order is deliveried
                array(
                    'args' => array(
                        'title' => __('Delivered order', ET_DOMAIN),
                        'id' => 'delivery-order',
                        'class' => 'payment-gateway',
                        'name' => '',
                        'desc' => __("Send to buyer when seller delivers the order.
", ET_DOMAIN),
                        'toggle' => true
                    ),
                    'fields' => array(
                        array(
                            'id' => 'delivery_order',
                            'type' => 'editor',
                            'title' => __("Delivery order mail here", ET_DOMAIN) ,
                            'class' => '',
                            'name' => 'delivery_order',
                            'reset' => 1
                        )
                    )
                ),
                // Email template when order is canceled
                // @todo hide in version 1.0
//                array(
//                    'args' => array(
//                        'title' => __('Order canceled', ET_DOMAIN),
//                        'id' => 'cancel-order',
//                        'class' => 'payment-gateway',
//                        'name' => '',
//                        'desc' => __("Send to buyer when the seller cancels his mJob order", ET_DOMAIN),
//                        'toggle' => true
//                    ),
//                    'fields' => array(
//                        array(
//                            'id' => 'cancel_order',
//                            'type' => 'editor',
//                            'title' => "",
//                            'class' => '',
//                            'name' => 'cancel_order',
//                            'reset' => 1
//                        )
//                    )
//                ),
                // Email template when order is dispute
                array(
                    'args' => array(
                        'title' => __('Order dispute (template for admin)', ET_DOMAIN),
                        'id' => 'dispute-order',
                        'class' => 'payment-gateway',
                        'name' => '',
                        'desc' => __("Send to admin when seller or buyer disputes over a mJob order", ET_DOMAIN),
                        'toggle' => true
                    ),
                    'fields' => array(
                        array(
                            'id' => 'dispute_order',
                            'type' => 'editor',
                            'title' => "",
                            'class' => '',
                            'name' => 'dispute_order',
                            'reset' => 1
                        )
                    )
                ),
                // Email template when order is dispute
                array(
                    'args' => array(
                        'title' => __('Order dispute (template for user)', ET_DOMAIN),
                        'id' => 'dispute-order-user',
                        'class' => 'payment-gateway',
                        'name' => '',
                        'desc' => __("Send to user when his partner disputes over a mJob order", ET_DOMAIN),
                        'toggle' => true
                    ),
                    'fields' => array(
                        array(
                            'id' => 'dispute_order_user',
                            'type' => 'editor',
                            'title' => '',
                            'class' => '',
                            'name' => 'dispute_order_user',
                            'reset' => 1
                        )
                    )
                ),
                array(
                    'args' => array(
                        'title' => __('Microjob order request declined', ET_DOMAIN),
                        'id' => 'decline-mjob-order',
                        'class' => 'payment-gateway',
                        'name' => '',
                        'desc' => __("Send to user when his microjob order request has been declined", ET_DOMAIN),
                        'toggle' => true
                    ),
                    'fields' => array(
                        array(
                            'id' => 'decline_mjob_order',
                            'type' => 'editor',
                            'title' => '',
                            'class' => '',
                            'name' => 'decline_mjob_order',
                            'reset' => 1
                        )
                    )
                ),
                array(
                    'args' => array(
                        'title' => __("Dispute Decision", ET_DOMAIN) ,
                        'id' => 'mail-description-group',
                        'class' => '',
                        'name' => '',
                    ) ,
                    'fields' => array(
                        array(
                            'id' => 'mail-description',
                            'type' => 'desc',
                            'title' => __("Mail description here", ET_DOMAIN) ,
                            'text' => __("Send to user when admin makes a decision on a disputed order", ET_DOMAIN) ,
                            'class' => '',
                            'name' => 'mail_description'
                        )
                    )
                ),
                // Seller win
                array(
                    'args' => array(
                        'title' => __('Seller wins', ET_DOMAIN),
                        'id' => 'dispute-seller-win',
                        'class' => 'payment-gateway',
                        'name' => '',
                        'desc' => '',
                        'toggle' => true
                    ),
                    'fields' => array(
                        array(
                            'id' => 'dispute_seller_win',
                            'type' => 'editor',
                            'title' => '',
                            'class' => '',
                            'name' => 'dispute_seller_win',
                            'reset' => 1
                        )
                    )
                ),
                // Buyer win
                array(
                    'args' => array(
                        'title' => __('Buyer wins', ET_DOMAIN),
                        'id' => 'dispute-buyer-win',
                        'class' => 'payment-gateway',
                        'name' => '',
                        'desc' => '',
                        'toggle' => true
                    ),
                    'fields' => array(
                        array(
                            'id' => 'dispute_buyer_win',
                            'type' => 'editor',
                            'title' => '',
                            'class' => '',
                            'name' => 'dispute_buyer_win',
                            'reset' => 1
                        )
                    )
                ),
                array(
                    'args' => array(
                        'title' => __("Withdraw Related Mail Template", ET_DOMAIN) ,
                        'id' => 'mail-description-group',
                        'class' => '',
                        'name' => '',
                    ) ,
                    'fields' => array(
                        array(
                            'id' => 'mail-description',
                            'type' => 'desc',
                            'title' => __("Mail description here", ET_DOMAIN) ,
                            'text' => __("Email templates used for withdraw-related event. You can use placeholders to include some specific content", ET_DOMAIN) ,
                            'class' => '',
                            'name' => 'mail_description'
                        )
                    )
                ),
                // Email template when user request a secure code
                array(
                    'args' => array(
                        'title' => __('New secure code request', ET_DOMAIN),
                        'id' => 'secure-code-mail',
                        'class' => 'payment-gateway',
                        'name' => '',
                        'desc' => __("Send to user when he requests a secure code to withdraw the money", ET_DOMAIN),
                        'toggle' => true
                    ),
                    'fields' => array(
                        array(
                            'id' => 'secure_code_mail',
                            'type' => 'editor',
                            'title' => '',
                            'class' => '',
                            'name' => 'secure_code_mail',
                            'reset' => 1
                        )
                    )
                ),
                // Email template when order is withdraw
                array(
                    'args' => array(
                        'title' => __('New withdrawal request', ET_DOMAIN),
                        'id' => 'new-withdraw',
                        'class' => 'payment-gateway',
                        'name' => '',
                        'desc' => __("Send to admin when a user requests to withdraw the money", ET_DOMAIN),
                        'toggle' => true
                    ),
                    'fields' => array(
                        array(
                            'id' => 'new_withdraw',
                            'type' => 'editor',
                            'title' => '',
                            'class' => '',
                            'name' => 'new_withdraw',
                            'reset' => 1
                        )
                    )
                ),
                // Email template when approve request withdraw
                array(
                    'args' => array(
                        'title' => __('Withdrawal request approved', ET_DOMAIN),
                        'id' => 'approve-withdraw',
                        'class' => 'payment-gateway',
                        'name' => '',
                        'desc' => __("Send to user when his withdrawal has been approved", ET_DOMAIN),
                        'toggle' => true
                    ),
                    'fields' => array(
                        array(
                            'id' => 'approve_withdraw',
                            'type' => 'editor',
                            'title' => '',
                            'class' => '',
                            'name' => 'approve_withdraw',
                            'reset' => 1
                        )
                    )
                ),
                // Email template when decline request withdraw
                array(
                    'args' => array(
                        'title' => __('Withdrawal request declined', ET_DOMAIN),
                        'id' => 'decline-withdraw',
                        'class' => 'payment-gateway',
                        'name' => '',
                        'desc' => __("Send to user when his withdrawal request has been declined", ET_DOMAIN),
                        'toggle' => true
                    ),
                    'fields' => array(
                        array(
                            'id' => 'decline_withdraw',
                            'type' => 'editor',
                            'title' => '',
                            'class' => '',
                            'name' => 'decline_withdraw',
                            'reset' => 1
                        )
                    )
                ),
                // Email template when user have new message
                array(
                    'args' => array(
                        'title' => __('New message alert', ET_DOMAIN),
                        'id' => 'inbox-mail-template',
                        'class' => '',
                        'name' => '',
                        'desc' => __("Send to user when he receives a new message", ET_DOMAIN),
                        'toggle' => true
                    ),
                    'fields' => array(
                        array(
                            'id' => 'inbox_mail_template',
                            'type' => 'editor',
                            'title' => '',
                            'class' => '',
                            'name' => 'inbox_mail_template',
                            'reset' => 1
                        )
                    )
                )
            )
        );

        /**
         * language settings
         */
        $sections['language'] = array(
            'args' => array(
                'title' => __("Language", ET_DOMAIN) ,
                'id' => 'language-settings',
                'icon' => 'G',
                'class' => ''
            ) ,

            'groups' => array(
                array(
                    'args' => array(
                        'title' => __("Website Language", ET_DOMAIN) ,
                        'id' => 'website-language',
                        'class' => '',
                        'name' => '',
                        'desc' => __("Select the language you want to use for your website.", ET_DOMAIN)
                    ) ,
                    'fields' => array(
                        array(
                            'id' => 'forgotpass_mail_template',
                            'type' => 'language_list',
                            'title' => __("Register Mail", ET_DOMAIN) ,
                            'name' => 'website_language',
                            'class' => ''
                        )
                    )
                ) ,
                array(
                    'args' => array(
                        'title' => __("Translator", ET_DOMAIN) ,
                        'id' => 'translator',
                        'class' => '',
                        'name' => 'translator',
                        'desc' => __("Translate a language", ET_DOMAIN)
                    ) ,
                    'fields' => array(
                        array(
                            'id' => 'translator-field',
                            'type' => 'translator',
                            'title' => __("Register Mail", ET_DOMAIN) ,
                            'name' => 'translate',
                            'class' => ''
                        )
                    )
                )
            )
        );

        /**
         * license key settings
         */
        $sections['update'] = array(
            'args' => array(
                'title' => __("Update", ET_DOMAIN) ,
                'id' => 'update-settings',
                'icon' => '~',
                'class' => ''
            ) ,

            'groups' => array(
                array(
                    'args' => array(
                        'title' => __("License Key", ET_DOMAIN) ,
                        'id' => 'license-key',
                        'class' => '',
                        'desc' => ''
                    ) ,
                    'fields' => array(
                        array(
                            'id' => 'et_license_key',
                            'type' => 'text',
                            'title' => __("License Key", ET_DOMAIN) ,
                            'name' => 'et_license_key',
                            'class' => ''
                        )
                    )
                )
            )
        );

        $temp = array();
        $options = AE_Options::get_instance();
        foreach ($sections as $key => $section) {
            $temp[] = new AE_section($section['args'], $section['groups'], $options);
        }

        $pages = array();

        /**
         * overview container
         * @todo Đợi phần mJob và mJobProfile
         */
         $container = new AE_Overview(array(
             'mjob_profile',
             'mjob_post',
             'mjob_order'
         ) , true);

         //$statics      =   array();
         // $header      =   new AE_Head( array( 'page_title'    => __('Overview', ET_DOMAIN),
         //                                  'menu_title'    => __('OVERVIEW', ET_DOMAIN),
         //                                  'desc'          => __("Overview", ET_DOMAIN) ) );
         $pages['overview'] = array(
             'args' => array(
                 'parent_slug' => 'et-overview',
                 'page_title' => __('Overview', ET_DOMAIN) ,
                 'menu_title' => __('OVERVIEW', ET_DOMAIN) ,
                 'cap' => 'administrator',
                 'slug' => 'et-overview',
                 'icon' => 'L',
                 'desc' => sprintf(__("%s overview", ET_DOMAIN) , $options->blogname)
             ) ,
             'container' => $container,
             // 'header' => $header
         );

        /**
         * setting view
         */
        $container = new AE_Container(array(
            'class' => '',
            'id' => 'settings'
        ) , $temp, '');
        $pages['settings'] = array(
            'args' => array(
                'parent_slug' => 'et-overview',
                'page_title' => __('Settings', ET_DOMAIN) ,
                'menu_title' => __('SETTINGS', ET_DOMAIN) ,
                'cap' => 'administrator',
                'slug' => 'et-settings',
                'icon' => 'y',
                'desc' => __("Manage how your MicrojobEngine looks and feels", ET_DOMAIN)
            ) ,
            'container' => $container
        );

        /**
         * user list view
         */

        $container = new AE_UsersContainer(array(
            'filter' => array(
                'moderate'
            )
        ));
        $pages['members'] = array(
            'args' => array(
                'parent_slug' => 'et-overview',
                'page_title' => __('Members', ET_DOMAIN) ,
                'menu_title' => __('MEMBERS', ET_DOMAIN) ,
                'cap' => 'administrator',
                'slug' => 'et-users',
                'icon' => 'g',
                'desc' => __("Overview of registered members", ET_DOMAIN)
            ) ,
            'container' => $container
        );

        /**
         * order list view
         */
        $orderlist = new AE_OrderList(array());
        $pages['payments'] = array(
            'args' => array(
                'parent_slug' => 'et-overview',
                'page_title' => __('Pricing Plan', ET_DOMAIN) ,
                'menu_title' => __('PRICING PLAN', ET_DOMAIN) ,
                'cap' => 'administrator',
                'slug' => 'et-payments',
                'icon' => '%',
                'desc' => __("Synthetize the purchase of pricing plans", ET_DOMAIN)
            ) ,
            'container' => $orderlist
        );

        /*
         * withdraw list view
         */
         /**
         * order list view
         */
        $withdrawList = new AE_WithdrawList(array());
        $pages['withdraws'] = array(
            'args' => array(
                'parent_slug' => 'et-overview',
                'page_title' => __('Withdraws', ET_DOMAIN) ,
                'menu_title' => __('WITHDRAWS', ET_DOMAIN) ,
                'cap' => 'administrator',
                'slug' => 'et-withdraws',
                'icon' => '%',
                'desc' => __("Overview of all withdraws", ET_DOMAIN)
            ) ,
            'container' => $withdrawList
        );
        /*
        * withdraw list view
        */
        /**
         * order list view
         */
        require_once dirname(__FILE__) . '/modules/mJobOrder/container-mjob-order.php';
        $mJobOrderList = new mJobOrderList(array());
        $pages['mjob_order'] = array(
            'args' => array(
                'parent_slug' => 'et-overview',
                'page_title' => __('Microjob order', ET_DOMAIN) ,
                'menu_title' => __('MICROJOB ORDER', ET_DOMAIN) ,
                'cap' => 'administrator',
                'slug' => 'et-mjob-order',
                'icon' => '%',
                'desc' => __("Synthetize all the microjob orders", ET_DOMAIN)
            ) ,
            'container' => $mJobOrderList
        );

        /**
         * setup wizard view
         */

        $container = new AE_Wizard();
        $pages[] = array(
            'args' => array(
                'parent_slug' => 'et-overview',
                'page_title'  => __('Setup Wizard', ET_DOMAIN) ,
                'menu_title'  => __('SETUP WIZARD', ET_DOMAIN) ,
                'cap'         => 'administrator',
                'slug'        => 'et-wizard',
                'icon'        => 'S',
                'desc'        => __("Set up and manage every content of your site", ET_DOMAIN)
            ) ,
            'container' => $container
        );


        /**
         *  filter pages config params so user can hook to here
         */
        $pages = apply_filters('ae_admin_menu_pages', $pages);

        /**
         * add menu page
         */
        $this->admin_menu = new AE_Menu($pages);

        /**
         * add sub menu page
         */
        foreach ($pages as $key => $page) {
            new AE_Submenu($page, $pages);
        }
    }
}


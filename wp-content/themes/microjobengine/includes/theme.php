<?php
class ET_Microjobengine extends AE_Base
{
    function __construct() {
        // disable admin bar if user can not manage options
        if (!current_user_can('manage_options') || et_load_mobile()) {
            show_admin_bar(false);
        };
        $this->init_user_roles();
        $this->add_action('init', 'theme_init');
        register_nav_menu('et_header_standard', __("Standard Header menu", ET_DOMAIN));
//        register_nav_menu('et_mobile', __("Mobile menu", ET_DOMAIN));
//        register_nav_menu('et_footer', __("Footer menu", ET_DOMAIN));

        $this->add_action( 'widgets_init', 'register_sidebar_widget' );
        /**
         * filter post thumnail image, if not set use no image
         */
        // $this->add_filter('post_thumbnail_html', 'post_thumbnail_html', 10, 5);
        /**
         * add query vars
         */
        $this->add_filter('query_vars', 'add_query_vars');
        /**
         * enqueue front end scripts
         */
        $this->add_action('wp_enqueue_scripts', 'on_add_scripts', 9);

        /**
         * enqueue front end styles
         */
        $this->add_action('wp_print_styles', 'on_add_styles', 10);
        /**
         * Filer query pre get post.
         */
        $this->add_action('pre_get_posts', 'pre_get_posts', 10);

        $this->add_filter('posts_orderby', 'order_by_post_status', 10, 2);
        /**
         * call new classes in footer
         */
        $this->add_action('wp_footer', 'script_in_footer', 100);
        /**
         * bundle some plugins
         */
        //$this->add_action('tgmpa_register', 'ae_required_plugins');

        /**
         * add return url for user after register
         */
        $this->add_filter('ae_after_insert_user', 'filter_link_redirect_register');
        /**
         * add return url for user after login
         */
        $this->add_filter('ae_after_login_user', 'filter_link_redirect_login');
        /**
         * add user default value
         */
        $this->add_action('ae_insert_user', 'add_user_default_values');
        /**
         * update user profile title
         */
        $this->add_filter('ae_update_user', 'sync_profile_data');
        /**
         * check role for user when register
         */
        $this->add_filter('ae_convert_post', 'add_new_post_fields');
        /**
         * add users custom fields
         */
        $this->add_filter('ae_define_user_meta', 'add_user_meta_fields');
        /**
         * restrict pages
         */
        $this->add_action('template_redirect', 'restrict_pages');
        /**
         * redirect user to home after logout
         */
        $this->add_filter('logout_url', 'logout_home', 10, 2);
        /**
         * filter profile link and replace by author post link
         */
        $this->add_filter('post_type_link', 'post_link', 10, 2);
        /**
         * add comment type filter dropdow
        */
        $this->add_filter('admin_comment_types_dropdown', 'admin_comment_types_dropdown');
        /**
         * add action admin menu prevent seller enter admin area
         */
        $this->add_action('admin_menu', 'redirect_seller');
        $this->add_action('login_init', 'redirect_login');
        // add theme support.
        add_theme_support('automatic-feed-links');
        /**
         * user front end control  : edit profile, update avatar
         */
        //$this->user_action = new AE_User_Front_Actions(new AE_Users());
        //new AE_PostMeta(PROJECT);
        $this->add_filter('ae_globals', 'mJobGlobals');
        $this->add_filter('use_pending', 'mjob_user_pending', 10, 2);
        /**
         * allow user to upload a video file
         * @author tam
         *
         */
        $this->add_filter('upload_mimes', 'mjob_add_mime_types');
        $this->add_filter('et_upload_file_upload_mimes', 'mjob_add_mime_types');
        $this->add_filter('ae_is_mobile', 'disableMobileVersion');
        new MjobReviewAction();
        /**
         * init place meta post
         */
        new AE_Schedule('mjob_post');
        /**
         * init microjob order schedule
         */
        new mJobOrderSchedule(4 * 3600);


        /**
         * Add image size
         */
        add_image_size("medium_post_thumbnail", 265, 160, true);
        add_image_size("mjob_detail_slider", 665, 375, true);

    }
    /**
     * initialize user role for this site
     *
     * @param void
     * @return void
     * @since 1.0
     * @package MicrojobEngine
     * @category void
     * @author JACK BUI
     */
    public function init_user_roles(){
        global $wp_roles;
        /**
         * register wp_role FREELANCER
         */
        if (!isset($wp_roles->roles[INDIVIDUAL])) {

            //all new roles
            add_role(INDIVIDUAL, __('Individual', ET_DOMAIN) , array(
                'read' => true,
                // true allows this capability
                'edit_posts' => true,
                'delete_posts' => false,
            ));
        }
        /**
         * add new role EMPLOYER
         */
        if (!isset($wp_roles->roles[COMPANY])) {
            add_role(COMPANY, __('Company', ET_DOMAIN) , array(
                'read' => true,
                // true allows this capability
                'edit_posts' => true,
                'delete_posts' => false,
            ));
        }
        /**
         * add new role STAFF
         */
        if (!isset($wp_roles->roles[STAFF])) {
            add_role(STAFF, __('Staff', ET_DOMAIN) , array(
                'read' => true,
                // true allows this capability
                'edit_posts' => true,
                'delete_posts' => false,
            ));
            // These are temporary examples, we can adjust them later.
            //
            $role = get_role(STAFF);
            $role->add_cap('manage_company_approval');
            $role->add_cap('manage_company_billing');
        }
    }
    /**
     * init theme
     * @since 1.0
     * @author Dakachi
     */
    function theme_init() {
        // register a post status: Reject (use when a project was rejected)
         register_post_status('reject', array(
             'label' => __('Reject', ET_DOMAIN) ,
             'private' => true,
             'public' => false,
             'exclude_from_search' => false,
             'show_in_admin_all_list' => true,
             'show_in_admin_status_list' => true,
             'label_count' => _n_noop('Reject <span class="count">(%s)</span>', 'Reject <span class="count">(%s)</span>') ,
         ));

        /* a project after expired date will be changed to archive */
         register_post_status('archive', array(
             'label' => __('Archive', ET_DOMAIN) ,
             'private' => false,
             'public' => true,
             'exclude_from_search' => true,
             'show_in_admin_all_list' => true,
             'show_in_admin_status_list' => true,
             'label_count' => _n_noop('Archive <span class="count">(%s)</span>', 'Archive <span class="count">(%s)</span>') ,
         ));

        /* after finish a project, project and accepted bid will be changed to complete */
         register_post_status('finished', array(
             'label' => _x('finished', 'post') ,
             'public' => true,
             'exclude_from_search' => false,
             'show_in_admin_all_list' => true,
             'show_in_admin_status_list' => true,
             'label_count' => _n_noop('Finished <span class="count">(%s)</span>', 'Finished <span class="count">(%s)</span>') ,
         ));

//         register_post_status('accept', array(
//             'label' => _x('accepted', 'post') ,
//             'public' => true,
//             'exclude_from_search' => false,
//             'show_in_admin_all_list' => true,
//             'show_in_admin_status_list' => true,
//             'label_count' => _n_noop('Accepted <span class="count">(%s)</span>', 'Accepted <span class="count">(%s)</span>') ,
//         ));

        /**
         * when a project was accept a bid, it will be change to close
         */
         register_post_status('close', array(
             'label' => _x('close', 'post') ,
             'public' => true,
             'exclude_from_search' => false,
             'show_in_admin_all_list' => true,
             'show_in_admin_status_list' => true,
             'label_count' => _n_noop('Close <span class="count">(%s)</span>', 'Close <span class="count">(%s)</span>') ,
         ));

        /**
         * when employer close project or freelancer quit a project, it change to disputing
         */
         register_post_status('disputing', array(
             'label' => _x('disputing', 'post') ,
             'public' => true,
             'exclude_from_search' => false,
             'show_in_admin_all_list' => true,
             'show_in_admin_status_list' => true,
             'label_count' => _n_noop('Disputing <span class="count">(%s)</span>', 'Disputing <span class="count">(%s)</span>') ,
         ));

        /**
         * when admin resolve a disputing project, it's status change to disputed
         */
         register_post_status('disputed', array(
             'label' => _x('disputed', 'post') ,
             'public' => true,
             'exclude_from_search' => false,
             'show_in_admin_all_list' => true,
             'show_in_admin_status_list' => true,
             'label_count' => _n_noop('Resolved <span class="count">(%s)</span>', 'Resolved <span class="count">(%s)</span>') ,
         ));

        /**
         * when a user dont want employer hide/contact him,
         * he can change his profile to hide, so no one can contact him
         */
        register_post_status('pause', array(
            'label' => __('Pause', ET_DOMAIN) ,
            'private' => false,
            'public' => true,
            'exclude_from_search' => true,
            'show_in_admin_all_list' => true,
            'show_in_admin_status_list' => true,
            'label_count' => _n_noop('Pause <span class="count">(%s)</span>', 'Pause <span class="count">(%s)</span>') ,
        ));
        /**
         * when a user dont want employer hide/contact him,
         * he can change his profile to hide, so no one can contact him
         */
        register_post_status('unpause', array(
            'label' => __('Active', ET_DOMAIN) ,
            'private' => false,
            'public' => true,
            'exclude_from_search' => true,
            'show_in_admin_all_list' => true,
            'show_in_admin_status_list' => true,
            'label_count' => _n_noop('Active <span class="count">(%s)</span>', 'Active <span class="count">(%s)</span>') ,
        ));
        /**
         * when a user dont want employer hide/contact him,
         * he can change his profile to hide, so no one can contact him
         */
        register_post_status('late', array(
            'label' => __('Late', ET_DOMAIN) ,
            'private'=> false,
            'public' => true,
            'exclude_from_search' => true,
            'show_in_admin_all_list' => true,
            'show_in_admin_status_list' => true,
            'label_count' => _n_noop('Late <span class="count">(%s)</span>', 'Late <span class="count">(%s)</span>') ,
        ));
        register_post_status('delivery', array(
            'label' => __('Delivered', ET_DOMAIN) ,
            'private'=> false,
            'public' => true,
            'exclude_from_search' => true,
            'show_in_admin_all_list' => true,
            'show_in_admin_status_list' => true,
            'label_count' => _n_noop('Delivered <span class="count">(%s)</span>', 'Delivered <span class="count">(%s)</span>') ,
        ));
        register_post_status('finish', array(
            'label' => __('Finished', ET_DOMAIN) ,
            'private'=> false,
            'public' => true,
            'exclude_from_search' => true,
            'show_in_admin_all_list' => true,
            'show_in_admin_status_list' => true,
            'label_count' => _n_noop('Finished <span class="count">(%s)</span>', 'Finished <span class="count">(%s)</span>') ,
        ));
        /**
         * set up social login
         */
        if(function_exists('init_social_login')){
            init_social_login();
        };
        /**
         * override author link
         */
        global $wp_rewrite;
        if ($wp_rewrite->using_permalinks()) {
            $wp_rewrite->author_base = ae_get_option('author_base', 'author');
            $wp_rewrite->author_structure = '/' . $wp_rewrite->author_base . '/%author%';
        }

        // Remove action
        global $et_appengine;
        remove_action('et_cash_checkout', 'ae_cash_message', 10);
        remove_action('ae_process_payment_action', array($et_appengine, 'notify_admin'), 10);
    }
    function add_user_default_values($result) {
        // if (ae_user_role($result) == FREELANCER) {
        //     update_user_meta($result, 'user_available', 'on');
        // }
    }
    public function sync_profile_data($result) {
        // $user = get_user_by('id', $result);
        // $ae_users = AE_Users::get_instance();
        // $user_data = $ae_users->convert($user);
        // $profile = get_post($user_data->user_profile_id);
        // if (ae_user_role($result) == FREELANCER && !empty($profile) && $profile->post_type == "profile") {

        //     //sync profile title
        //     $args = array(
        //         'ID' => $user_data->user_profile_id,
        //         'post_title' => $user->display_name
        //     );
        //     wp_update_post($args);

        //     //sync profile post_status
        //     global $wpdb;

        //     if (!$profile = get_post($profile)) return;

        //     $new_status = isset($user_data->user_available) && $user_data->user_available == "on" ? "publish" : "hide";

        //     if ($new_status == $profile->post_status) return;

        //     $wpdb->update($wpdb->posts, array(
        //         'post_status' => $new_status
        //     ) , array(
        //         'ID' => $profile->ID
        //     ));

        //     clean_post_cache($profile->ID);

        //     $old_status = $profile->post_status;
        //     $profile->post_status = $new_status;
        //     wp_transition_post_status($new_status, $old_status, $profile);
        // }
    }

    /**
     * filter redirect link after logout
     * @param string $logouturl
     * @param string $redir
     * @since 1.0
     * @author ThaiNt
     */
    public function logout_home($logouturl, $redir) {
        $redir = get_option('siteurl');
        return $logouturl . '&amp;redirect_to=' . urlencode($redir);
    }
    /**
     * add query var
     */
    function restrict_pages() {
        global $current_user, $user_ID;

        $restrict_singles = array(
            'mjob_profile',
            'mjob_extra',
            'order_delivery',
            'pack'
        );

        foreach($restrict_singles as $type) {
            if(is_singular($type)) {
                wp_redirect(home_url());
            }
        }


        if( !$user_ID ){
            $restrict_pages = array(
                'page-my-list-jobs.php',
                'page-my-list-order.php',
                'page-my-listing-jobs.php',
                'page-my-list-messages.php',
                'page-staff-manage-billing.php',
                'page-staff-manage-company.php',
                'page-staff-manage-dispute.php',
                'page-staff-manage-listing.php',
                'page-profile.php',
                'page-profile-company.php',
                'page-payment-method.php',
                'page-change-password.php',
                'page-revenues.php',
                'page-dashboard.php'
            );

            foreach($restrict_pages as $slug) {
                if( is_page_template($slug)){
                    wp_redirect(et_get_page_link('sign-in'));
                }
            }
        }
        if( is_singular('mjob_order') ){
            global $post;
            if( $post->post_status == 'draft'){
                wp_redirect(home_url('404'));
            }
        }
        if( is_page_template('page-post-service.php') ){
            $user_role = ae_user_role($user_ID);
            $profile = mJobProfileAction()->getProfile($user_ID);
            if( !is_super_admin() &&  $user_role != 'company' ){
                    wp_redirect(home_url());

            }
            else{
//                if( $user_role == COMPANY && $profile->company_status != COMPANY_STATUS_APPROVED ){
//                    wp_redirect(home_url());
//                }
            }
        }
        if( is_page_template('page-order.php') ){
            global $is_individual;
            $is_individual = mJobUserAction()->is_individual($user_ID);
            if( !is_super_admin() && !$is_individual ){
                wp_redirect(home_url());
            }
        }
        if( is_page_template('page-process-hiring.php') ){
            if( !$user_ID || !isset($_REQUEST['jid'])){
                wp_redirect(home_url());
            }
        }
        if( is_singular('payment_format')) {
            wp_redirect(home_url());
        }
    }
    /**
     * filter profile link and change it to author posts link
     * @param String $url The post url
     * @param Object $post current post object
     */
    public function post_link($url, $post) {
        return $url;
    }
    /**
     * hook to filter comment type dropdown and add review favorite to filter comment
     * @param Array $comment_types
    */
    function admin_comment_types_dropdown($comment_types) {
        // $comment_types['fre_review']   = __("Freelancer Review", ET_DOMAIN);
        // $comment_types['em_review']   = __("Employer Review", ET_DOMAIN);
        // $comment_types['fre_report']   = __("Report", ET_DOMAIN);
        // $comment_types['fre_invite']   = __("Invite", ET_DOMAIN);
        return $comment_types;
    }
    /**
     * redirect wp
     */
    function redirect_seller() {
        if (!(current_user_can('manage_options') || current_user_can('editor'))) {
            wp_redirect(home_url());
            exit;
        }
    }
    function redirect_login() {
        if (ae_get_option('login_init') && !is_user_logged_in()) {
            wp_redirect(home_url());
            exit;
        }
    }

    function register_sidebar_widget () {
        /**
        * Creates a sidebar blog
        * @param string|array  Builds Sidebar based off of 'name' and 'id' values.
        */
        $args = array(
            'name'          => __( 'Blog Sidebar', ET_DOMAIN ),
            'id'            => 'sidebar-blog',
            'description'   => '',
            'class'         => '',
            'before_widget' => '<aside id="%1$s" class="widget %2$s">',
            'after_widget'  => '</aside>',
            'before_title'  => '<h2 class="widgettitle">',
            'after_title'   => '</h2>'
        );

        register_sidebar( $args );
    }
    /**
     * add query var
     */
    function add_query_vars($vars) {
        array_push($vars, 'paymentType');
        return $vars;
    }
    //add new return custom fields for posts
    function add_new_post_fields($result) {

         //author name field
         if (!isset($result->author_name)) {
             $author = get_user_by('id', $result->post_author);
             $result->author_name = isset($author->display_name) ? $author->display_name : __('Unnamed', ET_DOMAIN);
         }
         //comments field
         if (!isset($result->comment_number)) {
             $num_comments = get_comments_number($result->ID);
             if (et_load_mobile()) {
                 $result->comment_number = $num_comments ? $num_comments : 0;
             } else {
                 if (comments_open($result->ID)) {
                     if ($num_comments == 0) {
                         $comments = __('No Comments', ET_DOMAIN);
                     } elseif ($num_comments > 1) {
                         $comments = $num_comments . __(' Comments', ET_DOMAIN);
                     } else {
                         $comments = __('1 Comment', ET_DOMAIN);
                     }
                     $write_comments = '<a href="' . get_comments_link() . '">' . $comments . '</a>';
                 } else {
                     $write_comments = __('Comments are off for this post.', ET_DOMAIN);
                 }
                 $result->comment_number = $write_comments;
             }
         }

         //post excerpt field
         if ($result->post_excerpt) {
             ob_start();
             echo apply_filters('the_excerpt', $result->post_excerpt);
             $post_excerpt = ob_get_clean();
             $result->post_excerpt = $post_excerpt;
         }

         //category field
         $categories = get_the_category();
         $separator = ' - ';
         $output = '';
         if ($categories) {
             foreach ($categories as $category) {
                 $output.= '<a href="' . get_category_link($category->term_id) . '" title="' . esc_attr(sprintf(__("View all posts in %s", ET_DOMAIN) , $category->name)) . '">' . $category->cat_name . '</a>' . $separator;
             }
             $result->category_name = trim($output, $separator);
         }
         //avatar field
         //if(!isset($result->avatar)) {
             $result->avatar = get_avatar($result->post_author, 65);
         //}
        return $result;
    }
    //redirect user to url after login
    function filter_link_redirect_login($result) {
        // $re_url = home_url();
        // if( isset($_REQUEST['ae_redirect_url']) ){
        //     $re_url = $_REQUEST['ae_redirect_url'];
        // }
        // $result->redirect_url = apply_filters('ae_after_login_link', $re_url);
        // $result->do = "login";
        return $result;
    }
    //redirect user to url after register
    function filter_link_redirect_register($result) {

        // if (!is_wp_error($result)) {

        //     // $user_info = get_userdata($result->ID);
        //     $role = ae_user_role($result->ID);
        // } else {
        //     $role = '';
        // }

        // $redirect_url = ($role == "employer" && AE_Users::is_activate($result->ID) ) ? et_get_page_link('submit-project') : et_get_page_link('profile');
        // if( $role == FREELANCER){
        //     if( et_load_mobile() ){
        //      $redirect_url = et_get_page_link('profile').'#tab_profile';
        //     }
        //     else{
        //         $redirect_url = et_get_page_link('profile').'#tab_profile_details';
        //     }
        // }
        // $result->redirect_url = apply_filters('ae_after_register_link', $redirect_url);
        // $result->do = "register";
        return $result;
    }
    //add custom fields for user
    function add_user_meta_fields($default) {
         $default = wp_parse_args(array(
             'user_hour_rate',
             'user_profile_id',
             'user_currency',
             'user_skills',
             'user_available'
         ) , $default);
        return $default;
    }

    function on_add_scripts() {

        global $user_ID;

        $this->add_existed_script('jquery');
        $this->add_existed_script('underscore');
        $this->add_existed_script('backbone');
        $this->add_existed_script('plupload');
        $this->add_existed_script('appengine');

        $this->add_existed_script('chosen');

        // add script validator
        $this->add_existed_script('jquery-validator');
        $this->add_existed_script('bootstrap');
        /**
         * bootstrap slider for search form
         */
        $this->add_existed_script('slider-bt');

        // Notification lib
        $this->add_script('toastr-js', get_template_directory_uri() . '/assets/js/lib/toastr.min.js', array(
            'jquery',
            'underscore',
            'backbone',
            'appengine'), ET_VERSION, true);

        $this->add_script('bootstrap-select', get_template_directory_uri() . '/assets/js/bootstrap-select.min.js', array(
            'jquery',
            'underscore',
            'backbone',
            'appengine'), ET_VERSION, true);

        $this->add_script('front', get_template_directory_uri() . '/assets/js/front.js', array(
            'jquery',
            'underscore',
            'backbone',
            'appengine'), ET_VERSION, true);

        $this->add_script('waves', get_template_directory_uri() . '/assets/js/waves.js', array(
            'jquery',
            'underscore',
            'backbone',
            'appengine'), ET_VERSION, true);

        $this->add_script('dot', get_template_directory_uri() . '/assets/js/jquery.dot.js', array(
            'jquery',
            'underscore',
            'backbone',
            'appengine'), ET_VERSION, true);

        $this->add_script('wow-animate', get_template_directory_uri() . '/assets/js/wow.min.js', array(
            'jquery',
            'underscore',
            'backbone',
            'appengine'), ET_VERSION, true);


        $this->add_script('scrollbar', get_template_directory_uri() . '/assets/js/customscrollbar.min.js', array(
            'jquery',
            'underscore',
            'backbone',
            'appengine'), ET_VERSION, true);


        if( is_page_template('page-post-service.php')) {
            $this->add_script('post-service', get_template_directory_uri() . '/assets/js/post-service.js', array(
                'jquery',
                'underscore',
                'backbone',
                'appengine',
                'front'), ET_VERSION, true);
        }
        if(is_page_template('page-profile.php') || is_page_template('page-profile-company.php')) {
            // Cropper library
            $this->add_script('cropper-js', get_template_directory_uri() . '/assets/js/lib/cropper.min.js', array(
                'jquery',
                'underscore',
                'backbone',
                'appengine',
                'front'), ET_VERSION, true);

            $this->add_script('textarea-auto-resize', get_template_directory_uri() . '/assets/js/lib/autosize.min.js', array(
                'jquery',
                'underscore',
                'backbone',
                'appengine',
                'front'), ET_VERSION, true);
            $this->add_script('mask-js', get_template_directory_uri() . '/assets/js/jquery.maskedinput.min.js', array(
                'jquery',
                'underscore',
                'backbone',
                'appengine',
                'front'), ET_VERSION, true);
            $this->add_script('profile', get_template_directory_uri() . '/assets/js/profile.js', array(
                'jquery',
                'underscore',
                'backbone',
                'appengine',
                'front'), ET_VERSION, true);

        }
        if( is_page_template('page-process-hiring.php') ){
            $this->add_script('mask-js', get_template_directory_uri() . '/assets/js/jquery.maskedinput.min.js', array(
                'jquery',
                'underscore',
                'backbone',
                'appengine',
                'front'), ET_VERSION, true);
        }
        if(is_page_template('page-dashboard.php')) {
            $this->add_script('chart', get_template_directory_uri() . '/assets/js/lib/chart.min.js', array(
                'jquery',
                'underscore',
                'backbone',
                'appengine',
                'front'), ET_VERSION, true);

            $this->add_script('dashboard', get_template_directory_uri() . '/assets/js/dashboard.js', array(
                'jquery',
                'underscore',
                'backbone',
                'appengine',
                'front'), ET_VERSION, true);
        }
        if(is_author()) {
            $this->add_script('profile', get_template_directory_uri() . '/assets/js/author.js', array(
                'jquery',
                'underscore',
                'backbone',
                'appengine',
                'front'), ET_VERSION, true);
        }
        if(is_page_template('page-payment-method.php')) {
            $this->add_script('payment-method', get_template_directory_uri() . '/assets/js/payment-method.js', array(
                'jquery',
                'underscore',
                'backbone',
                'appengine',
                'front'), ET_VERSION, true);
        }
        if(is_page_template('page-revenues.php')) {
            $this->add_script('revenues', get_template_directory_uri() . '/assets/js/revenues.js', array(
                'jquery',
                'underscore',
                'backbone',
                'appengine',
                'front'), ET_VERSION, true);
        }
        if( is_singular('mjob_post') || is_page_template('page-order.php') || is_page_template('page-process-payment.php')) {
            $this->add_script('single-mjob', get_template_directory_uri() . '/assets/js/single-mjob.js', array(
                'jquery',
                'underscore',
                'backbone',
                'appengine',
                'front'), ET_VERSION, true);
            $this->add_script('addthis-script', '//s7.addthis.com/js/300/addthis_widget.js#pubid=ra-4ed5eb280d19b26b', array() , ET_VERSION, true);
        }
        if( is_page_template('page-order.php') || is_page_template('page-process-payment.php')){
            $this->add_script('order-mjob', get_template_directory_uri() . '/assets/js/payment.js', array(
                'jquery',
                'underscore',
                'backbone',
                'appengine',
                'front',
                'single-mjob'
            ), ET_VERSION, true);
        }
        if( is_page_template('page-my-list-order.php') || is_singular('mjob_order') ){
            $this->add_script('order-list', get_template_directory_uri() . '/assets/js/order-list.js', array(
                'jquery',
                'underscore',
                'backbone',
                'appengine',
                'front',
                'ae-message-js'
            ), ET_VERSION, true);
        }
        // Add style css for mobile version.
        if (et_load_mobile()) {
            // if( is_page_template('page-submit-project.php') || is_page_template('page-upgrade-account.php') ){
            //     do_action('ae_payment_script');
            // }
            return;
        }
        // if( is_page_template('page-submit-project.php') || is_page_template('page-upgrade-account.php') ){
        //     do_action('ae_payment_script');
        // }
    }

    function on_add_styles() {
        //$this->add_existed_style('bootstrap');
        wp_deregister_style('bootstrap');
        $this->add_style('toastr', get_template_directory_uri() . '/assets/css/toastr.min.css', ET_VERSION);
        $this->add_style('bootstrap-select', get_template_directory_uri() . '/assets/css/bootstrap-select.min.css', ET_VERSION);
        $this->add_style('chosen', get_template_directory_uri() . '/assets/css/chosen.css', ET_VERSION);
        $this->add_style('cropper', get_template_directory_uri() . '/assets/css/cropper.min.css', ET_VERSION);
        // Font Awesome
        $this->add_style('font-icon', get_template_directory_uri() . '/assets/css/font-awesome.css', array() , ET_VERSION);
        $this->add_style('main-style-css', get_template_directory_uri() . '/assets/css/style.css', ET_VERSION);
        $this->add_style('custom-css', get_template_directory_uri() . '/assets/css/custom.css', ET_VERSION);
        $this->add_style('scroll-bar', get_template_directory_uri() . '/assets/css/customscrollbar.css', ET_VERSION);

    }
    /*
     * custom query prev query post
    */
    function pre_get_posts($query) {
        if (!is_admin() && (is_post_type_archive('mjob_post') || is_tax('mjob_category') ) || is_tax('skill') ) {
            if (!$query->is_main_query()) return $query;
            if (current_user_can('manage_options')) {
                $query->set('post_status', array(
                    'pending',
                    'publish',
                    'pause',
                    'unpause'
                ));
                //$query->set ('orderby', 'post_status');
            } else {
                if( is_page_template('page-my-list-jobs.php') ){
                    $query->set('is_author', true);
                    $query->set('post_status', array('publish', 'pause', 'reject', 'unpause') );
                }else {
                    $query->set('post_status', array('publish', 'pause', 'unpause'));
                }
            }
        }
        return $query;
    }
    /*
     * custom order when admin view page-archive-projects
    */
    function order_by_post_status($orderby, $object) {
         global $user_ID, $mjob_is_author;
         if(!isset($mjob_is_author)) {
             if ((is_post_type_archive('mjob_post') || is_tax('mjob_category') || is_tax('skill') ) && !is_admin() && current_user_can('edit_others_posts')) {
                 return self::order_by_post_pending($orderby, $object);
             }

             if (isset($object->query_vars['post_status']) && is_array($object->query_vars['post_status'] )
                 && isset($object->query_vars['author']) && $user_ID == $object->query_vars['author'] && $object->query_vars['post_type']=="mjob_post" && $user_ID) {
                 return self::order_by_post_pending($orderby, $object);
             }
         }
        return $orderby;
    }
    static function order_by_post_pending($orderby, $object) {
         global $wpdb;
         $orderby = " case {$wpdb->posts}.post_status
                             when 'pending' then 0
                             when 'disputing' then 1
                             when 'reject' then 2
                             when 'publish' then 3
                             when 'pause' then 4
                             when 'unpause' then 5
                             when 'close' then 6
                             when 'complete' then 7
                             when 'draft' then 8
                             when 'archive' then 9
                             end,
                         {$wpdb->posts}.post_date DESC";
        return $orderby;
    }
    // load bundle plugin
    function ae_required_plugins() {
    }
    function script_in_footer() {
        global $user_ID;
        do_action('ae_before_render_script');
?>
        <script type="text/javascript" id="frontend_scripts">
        (function ($ , Views, Models, AE) {
          $(document).ready(function(){
              var currentUser;
              if($('#user_id').length > 0 ) {
                  currentUser = new Models.User( JSON.parse($('#user_id').html()) );
                  //currentUser.fetch();
              } else {
                  currentUser = new Models.User();
              }
              // init view front
              if(typeof Views.Front !== 'undefined') {
                  AE.App = new Views.Front({model : currentUser});
              }
              AE.App.user = currentUser;
              if(typeof Views.PostSevice !== 'undefined' && $('.mjob-post-service').length > 0) {
                  AE.PostService = new Views.PostSevice({
                      el: '.mjob-post-service',
                      user_login: currentUser.get('id'),
                      free_plan_used: 0,
                      limit_free_plan: false,
                      step: 2
                  });
              }
            });
        })(jQuery, AE.Views, AE.Models, window.AE);

        </script>
        <?php
        do_action('ae_after_render_script');
        $this->mJobIncludeTemplate();
    }
    /**
     * include template
     *
     * @param void
     * @return void
     * @since 1.0
     * @package MicrojobEngine
     * @category void
     * @author JACK BUI
     */
    public function mJobIncludeTemplate(){
        /*
         * include skill item template
         */
        get_template_part('template-js/skill' , 'item');
        /*
         * Include carousel item template
         */
        get_template_part('template-js/carousel-file', 'item');
        get_template_part('template-js/carousel', 'item');

        /**
         * Include user header template
         */
        get_template_part('template-js/my-account', 'item');
        /*
         * Include extra item
         *
         */
        get_template_part('template-js/extra', 'item');
        get_template_part('template-js/edit-extra', 'item');
        /*
         * Include mjob item
         *
         */
        get_template_part('template-js/mjob', 'item');

        if(is_author()) {
            get_template_part('template-js/mjob-list', 'item');
        }
        /*
         * include modal reject mjob
         */
        get_template_part('template-js/modal', 'reject');
        if( is_page_template('page-my-list-order.php') || is_singular('mjob_order') ){
            get_template_part('template-js/order', 'item');
            get_template_part('template-js/task', 'item');
            get_template_part('template-js/modal-delivery', 'order');
        }
        if( is_page_template('page-process-hiring.php') ){
            get_template_part('template-js/modal', 'agreement');
        }
        get_template_part('template/modal', 'conversation');

        /**
         * Include history item
         */
        get_template_part('template-js/history', 'item');
        if( is_singular('mjob_order')){
            get_template_part('template-js/modal', 'review');
        }
        /**
         * Include conversation item
         */
        get_template_part('template-js/conversation', 'item');
        get_template_part('template-js/message', 'item');
        get_template_part('template-js/post', 'item');

        if(is_singular('mjob_post') || is_page_template('page-order.php') || is_page_template('page-process-payment.php')) {
            get_template_part('template-js/review', 'item');
        }
    }
    /**
     * add more global variable
     *
     * @param array $vars
     * @return array $vars
     * @since 1.0
     * @package MicrojobEngine
     * @category void
     * @author JACK BUI
     */
    public function mJobGlobals($vars){
        global $user_ID;
        $vars['user_ID'] = $user_ID;
        $vars['is_admin'] = false;
        if( is_super_admin() ){
            $vars['is_admin'] = true;
        }
        $vars['is_search'] = is_search();
        $vars['is_tax_mjob_category'] = is_tax('mjob_category');
        $vars['is_tax_skill'] = is_tax('skill');
        $vars['mJobDefaultGalleryImage'] = TEMPLATEURL. '/assets/img/image-avatar.jpg';
        $number_format = ae_get_option('number_format');
        $decimal = (isset($number_format['et_decimal'])) ? $number_format['et_decimal'] : get_theme_mod('et_decimal', 2);
        $decimal_point = (isset($number_format['dec_point']) && $number_format['dec_point']) ? $number_format['dec_point'] : get_theme_mod('et_decimal_point', '.');
        $thousand_sep = (isset($number_format['thousand_sep']) && $number_format['thousand_sep']) ? $number_format['thousand_sep'] : get_theme_mod('et_thousand_sep', ',');
        $vars['decimal'] = $decimal;
        $vars['decimal_point'] = $decimal_point;
        $vars['thousand_sep'] = $thousand_sep;
        $currency = ae_get_option('currency', array(
            'align' => 'left',
            'code' => 'USD',
            'icon' => '$'
        ));
        $vars['mjob_currency'] = $currency;
        $vars['order_link'] = et_get_page_link('order');
        $vars['profile_empty_text'] = __('There is no content', ET_DOMAIN);
        $vars['no_services'] = sprintf(__('<div class="not-found">This search matches 0 results! <p class="not-found-sub-text"><label for="input-search" class="new-search-link">New search</label> or <a href="%s">back to home page</a></p></div>', ET_DOMAIN), get_site_url());
        //$vars['no_services']
        $vars['no_mjobs'] = __('<div class="not-found">There are no mJobs found!</div>', ET_DOMAIN);
        $vars['no_orders'] = __('<p class="no-items" >There are no orders found!</p>', ET_DOMAIN);
        $vars['min_images'] = ae_get_option('min_carousel', 1);
        $vars['min_images_notification'] = __('You must have at least one picture!', ET_DOMAIN);
        $vars['delivery_status'] = __('DELIVERED', ET_DOMAIN);
        $vars['disputing_status'] = __('DISPUTING', ET_DOMAIN);
        $file_types = ae_get_option('file_types', 'pdf,doc,docx,zip,psd,jpg,png');
        $file_types = preg_replace('/\s+/', '', $file_types);
        $vars['file_types'] = $file_types;

        $max_file_size = ae_get_option('max_file_size');
        if(empty($max_file_size) || !is_numeric($max_file_size)) {
            $max_file_size = wp_max_upload_size() / (1024 * 1024) . 'mb';
        } else {
            $max_file_size = $max_file_size . 'mb';
        }
        $vars['plupload_config']['max_file_size'] = $max_file_size;
        $vars['progress_bar_3'] = mJobProgressBar(3, false);
        $vars['progress_bar_4'] = mJobProgressBar(4, false);

        $date_format = 'M j';
        $current_date = date($date_format, time());
        $last_week = date($date_format, strtotime('-1 week'));
        $vars['date_range'] = mJopGetDateRange($last_week, $current_date, '+1 day', $date_format);
        $vars['data_chart'] = mJobGetOrderChart();

        $vars['show_bio_text'] = __('Show more', ET_DOMAIN);
        $vars['hide_bio_text'] = __('Show less', ET_DOMAIN);

        $vars['pending_account_error_txt'] = __("Your account is pending. You have to activate your account to continue this step.", ET_DOMAIN);
        $vars['disableNotification'] = __('This mJob was paused by the seller.', ET_DOMAIN);
        $vars['priceMinNoti'] = __('Please enter a number greater than 0.', ET_DOMAIN);
        $vars['requiredField'] = __('This field is required!', ET_DOMAIN);
        $vars['uploadSuccess'] = __('Job slider updated successfully!', ET_DOMAIN);
        $vars['user_confirm'] = ae_get_option('user_confirm');
        $vars['permalink_structure'] = get_option('permalink_structure');
        $vars['process_hiring'] = et_get_page_link('process-hiring');
        $vars['process_hiring_step2'] = __('CONFIRM BILLING INFORMATION', ET_DOMAIN);
        $vars['process_hiring_step3'] = __('REVIEW AND SIGN AGREEMENTS', ET_DOMAIN);
        return $vars;
    }
    /**
     * user pending option
     *
     * @param boolean $pending
     * @param string $post_type
     * @return boolean $pending
     * @since 1.0
     * @package MicrojobEngine
     * @category void
     * @author JACK BUI
     */
    public function mjob_user_pending($pending, $post_type){
        if( $post_type != 'mjob_post' ){
            return false;
        }
        return $pending;
    }
    /**
     * allow user upload file type
     *
     * @param array $mimes
     * @return void
     * @since 1.0
     * @package MicrojobEngine
     * @category void
     * @author JACK BUI
     */
    public function mjob_add_mime_types($mimes) {
        /**
         * admin can add more file extension
         */
        if (current_user_can('manage_options')) {
            return array_merge($mimes, array(
                'ac3' => 'audio/ac3',
                'mpa' => 'audio/MPA',
                'flv' => 'video/x-flv',
                'svg' => 'image/svg+xml',
                'mp4' => 'video/MP4',
                'doc|docx' => 'application/msword',
                'pdf' => 'application/pdf',
                'psd' => 'application/psd',
                'zip' => 'multipart/x-zip'
            ));
        }
        // if user is normal user
        $mimes = array_merge($mimes, array(
            'doc|docx' => 'application/msword',
            'pdf' => 'application/pdf',
            'psd' => 'application/psd',
            'zip' => 'multipart/x-zip'
        ));
        return $mimes;
    }
    /**
     * disable mobile version
     *
     * @param void
     * @return void
     * @since 1.0
     * @package MicrojobEngine
     * @category void
     * @author JACK BUI
     */
    public function disableMobileVersion(){
        return false;
    }
}
global $et_mjob;
add_action('after_setup_theme', 'et_setup_theme');
function et_setup_theme() {
    global $et_mjob;
    $et_mjob = new ET_Microjobengine();
    if (is_admin() || current_user_can('manage_options')) {
        new ET_Admin();
    }
}
/**
 * add custom status to wordpress post status
 */
function fre_append_post_status_list() {
    if(!isset($_REQUEST['post'])) return ;
    $post = get_post($_REQUEST['post']);
    $complete = '';
    $closed = '';
    $disputing = '';
    $disputed = '';
    $label = '';

    if ($post && ($post->post_type == BID || $post->post_type == PROJECT) ) {
        if ($post->post_status == 'complete') {
            $complete = " selected='selected'";
            $label = '<span id="post-status-display">' . __("Completed", ET_DOMAIN) . '</span>';
        }
        if ($post->post_status == 'close') {
            $closed = " selected='selected'";
            $label = '<span id="post-status-display">' . __("Close", ET_DOMAIN) . '</span>';
        }
        if ($post->post_status == 'disputing') {
            $disputing = " selected='selected'";
            $label = '<span id="post-status-display">' . __("Disputing", ET_DOMAIN) . '</span>';
        }
        if ($post->post_status == 'disputed') {
            $disputed = " selected='selected'";
            $label = '<span id="post-status-display">' . __("Disputed", ET_DOMAIN) . '</span>';
        }
?>
          <script>
          jQuery(document).ready(function($){
               $("select#post_status").append("<option value='complete' <?php
        echo $complete; ?>>Completed</option><option value='close' <?php
        echo $closed; ?>>Close</option><option value='disputing' <?php
        echo $disputing; ?>>Disputing</option><option value='disputed' <?php
        echo $disputed; ?>>Disputed</option>");
               $(".misc-pub-section label").append('<?php
        echo $label; ?>');
          });
          </script>
          <?php
    }
}
//add_action('admin_footer-post.php', 'fre_append_post_status_list');

/**
 * MICROJOBENGINE ACTION AND FILTER
 */

/**
 * set default user roles for social login
 *
 *@author JACK BUI
 */
if( !function_exists( 'mJobDefaultUserRoles' ) ){
    function mJobDefaultUserRoles( $default_role ){
        $default_role = array('author');
        return $default_role;
    }

    add_filter( 'ae_social_login_user_roles_default', 'mJobDefaultUserRoles' );
}

if(!function_exists('mJobRemoveLinkedIn')) {
    /**
     * Filter remove social login with LinkedIn
     * @param void
     * @return boolean
     * @since 1.0
     * @package Microjobengine
     * @category Filter Hook
     * @author Tat Thien
     */
    function mJobRemoveLinkedIn() {
        return false;
    }

    add_filter('ae_enable_social_linkedin', 'mJobRemoveLinkedIn');
}


if(!function_exists('mJobPackageScripts')) {
    /**
     * Add scripts for package
     * @param void
     * @return void
     * @since 1.0
     * @package MicrojobEngine
     * @category Action Hook
     * @author Tat Thien
     */
    function mJobPackageScripts() {
        if(isset($_GET['page']) && $_GET['page'] == 'et-settings') {
            ?>
            <script>
                (function($) {
                    $('body').delegate('input[name="et_permanent"]', 'click', function() {
                        if($(this).is(':checked')) {
                            $(this).parent().find('input[name="et_duration"]').attr('disabled', true);
                        } else {
                            $(this).parent().find('input[name="et_duration"]').removeAttr('disabled');
                        }
                    });
                })(jQuery)
            </script>
            <?php
        }
    }

    add_filter('admin_footer', 'mJobPackageScripts');
}
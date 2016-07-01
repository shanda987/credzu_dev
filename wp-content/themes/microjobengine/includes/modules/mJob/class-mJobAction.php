<?php
class mJobAction extends mJobPostAction{
    public static $instance;
    /**
     * getInstance method
     *
     */
    public static function getInstance()
    {
        if (!self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    /**
     * the constructor of this class
     *
     */
    public  function __construct($post_type = 'mjob_post'){
        parent::__construct($post_type);
        $this->add_ajax('ae-fetch-mjob_post', 'fetch_post');
        $this->add_ajax('ae-mjob_post-sync', 'syncPost');
        $this->add_filter('ae_convert_mjob_post', 'convertPost');
        $this->add_filter('ae_request_thumbnail_size', 'filterThumbnailSize');
        $this->add_ajax('mjob-get-mjob-infor', 'getMjobPost');
        $this->add_ajax('mjob-get-skill-list', 'getMjobTags');
        $this->add_ajax('mjob-get-breadcum-list', 'getMjobCats');
        $this->add_action('ae_tax_meta_add_field', 'mjob_add_meta_field');
        $this->add_action('ae_tax_meta_edit_field', 'mjob_edit_meta_field', 10, 3);
        $this->add_filter('jb_convert_mjob_requirement', 'filterTaxInfo');
        $this->ruler = array(
            'post_title'=>'required',
            'post_content'=>'required',
            'time_delivery'=>'required',
            'et_budget'=>'required',
            //'et_carousels'=>'required'
        );
        $this->disable_plan = ae_get_option('disable_plan', false);
        $this->mail = mJobMailing::getInstance();
        $this->add_filter('ae_convert_user', 'mjob_convert_user');
        $this->add_ajax('check-mjob-category', 'checkMjobCat');
    }
    /**
     * sync Post function
     *
     * @param void
     * @return void
     * @since 1.0
     * @package MicrojobEngine
     * @category void
     * @author JACK BUI
     */
    public function syncPost(){
        $request = $_POST;
       // $request['et_budget'] = ae_get_option('mjob_price', 5);
        if (!isset($request['rating_score'])) {
            $request['rating_score'] = 0;
        }
        $response = $this->validatePost($request);
        if (!$response['success']) {
            wp_send_json($response);
            exit;
        }
        $request = $response['data'];
        if( isset($request['mjob_category'])) {
            global $ae_tax_factory;
            $term = get_term_by('id', $request['mjob_category'], 'mjob_category');
            $obj = $ae_tax_factory->get('mjob_category');
            $term = $obj->convert($term);
            if( empty($term->pricing_plan ) ){
                $request['et_payment_package'] = 'sku1';
            }
            else{
                $request['et_payment_package'] = $term->pricing_plan;
            }
        }
        if ($request['method'] != 'create' && !isset($request['renew']) ) {
            unset($request['et_payment_package']);
        }
        if( !isset($request['featured_image']) ){
            if( isset($request['et_carousels'] ) && !empty($request['et_carousels']) ){
                $request['featured_image'] = $request['et_carousels']['0'];
            }
            else{
                $response = array(
                    'success'=> false,
                    'msg'=> __('You have to upload at least one photo!', ET_DOMAIN)
                );
                wp_send_json($response);
                exit;
            }
        }
        else{
            if( isset($request['et_carousels'] ) && !empty($request['et_carousels']) ){
                if( !in_array($request['featured_image'], $request['et_carousels'])){
                    $request['featured_image'] = $request['et_carousels']['0'];
                }
            }
            else{
                $response = array(
                    'success'=> false,
                    'msg'=> __('You have to upload at least one photo!', ET_DOMAIN)
                );
                wp_send_json($response);
                exit;
            }
        }
        global $ae_post_factory;
        $obj = $ae_post_factory->get('mjob_extra');
        $arr_extras = array();
        $is_featured = 0;
        if( isset($request['checkout']) && $request['checkout'] == 1){
            $m = $this->get_mjob($request['ID']);
            $package = $ae_post_factory->get('pack');
            $plan = $package->get($m->et_payment_package);
            if( !empty($plan) ){
                $latest_amount = $plan->et_price;
            }
            else {
                $latest_amount = 0;
            }
            if( isset( $request['extra_ids']) && !empty($request['extra_ids']) ){
                foreach( $request['extra_ids'] as $key => $extra ){
                    $p = get_post( $extra );
                    if( !empty($p) ){
                        $p = $obj->convert($p);
                        $latest_amount += $p->et_budget;
                        if( in_array('featured', $p->is_featured) ){
                            $is_featured = 1;
                        }
                        array_push($arr_extras, $p);
                    }
                    else{
                        unset($request['extra_ids'][$key]);
                    }
                }
                $request['is_featured'] = $is_featured;
                $request['extra_objects'] = $arr_extras;
            }
            $request['latest_amount'] = $latest_amount;
            $request['latest_amount_text'] = mJobPriceFormat($latest_amount, 'default');
            do_action('credzu_do_checkout', $request);
        }
        $requirements = get_terms( 'mjob_requirement', array(
            'hide_empty' => false,
        ) );
        $arr_r = array();
        if( !empty($requirements ) ){
             foreach( $requirements as $r){
                array_push($arr_r, $r->term_id);
            }
        }
        if( !empty($arr_r)){
            $request['mjob_requirement'] = $arr_r;
        }
        $a = get_post(160);
        if( !empty($a) && !is_wp_error($a)){
            $request['agreement_terms'] = $a->post_content;
        }
        $response = $this->sync_post($request);
        if (isset($response['data']) && !empty($response['data'])) {
            $result = $response['data'];
            // Email notification to admin
            if($request['method'] == 'create') {
                global $user_ID;
                $profile = mJobProfileAction()->getProfile($user_ID);
                if( isset( $profile->ID) ){
                    update_post_meta($profile->ID, 'create_listing_completed', 1);
                }
                unset($result->skill);
                if(($result->post_status == 'pending' || $result->post_status == 'publish') || $result->post_status == 'draft') {
                   // $this->mail->mJobNewPost($result->ID);
                }
            }
            // Email notification to author when post has changed
            if($request['method'] == 'update') {
                if(isset($request['reject_message'])) {
                    $rejectMsg = $request['reject_message'];
                } else {
                    $rejectMsg = '';
                }
                $this->mail->mJobChangeStatus($result->post_status, $request['post_status'], $result, $rejectMsg);
            }

            /**
             * check payment package and check free or use package to send redirect link
             */
            if (isset($request['et_payment_package'])) {

                // check seller use package or not
                $check = AE_Package::package_or_free($request['et_payment_package'], $result);

                // check use package or free to return url
                if ($check['success']) {
                    $result->redirect_url = $check['url'];
                }

                $result->response = $check;

                // check seller have reached limit free plan
                $check = AE_Package::limit_free_plan($request['et_payment_package']);
                if ($check['success']) {

                    // false user have reached maximum free plan
                    $response['success'] = false;
                    $response['msg'] = $check['msg'];
                    $response['data'] = $result;
                    // send response to client
                    wp_send_json($response);
                }
            }

            // check payment package


            /**
             * check disable plan and submit place to view details
             */
            if ($this->disable_plan && $request['method'] == 'create') {
                $result->redirect_url = $result->permalink;
                // disable plan, free to post place
                $response = array(
                    'success' => true,
                    'data' => $result,
                    'msg' => __("Successful submission.", ET_DOMAIN)
                );

                // send response
                wp_send_json($response);
            }
        }
        wp_send_json($response);
    }
    /**
     * convert post
     *
     * @param object $result
     * @return object $result after convert
     * @since 1.0
     * @package MicrojobEngine
     * @category void
     * @author JACK BUI
     */
    public function convertPost($result){
        global $user_ID, $ae_post_factory;

        // Check if is search page
        $result->is_search = false;
        if(isset($_REQUEST['query']['is_search']) && $_REQUEST['query']['is_search'] == true) {
            $result->is_search = true;
        }

        $result->is_author = false;
        if( $result->post_author == $user_ID ){
            $result->is_author = true;
        }
        $result->is_admin = false;
        if( is_super_admin() ){
            $result->is_admin = true;
        }
        $profile = mJobProfileAction()->getProfile($result->post_author);
        $result->author_name = $profile->initial_display_name;
        $result->author_avatar = get_avatar($result->post_author, 35);
        $result->mjob_category_name = '';
        if( isset($result->tax_input['mjob_category']['0']->name) && !empty($result->tax_input['mjob_category']['0']->name)){
            $result->mjob_category_name = $result->tax_input['mjob_category']['0']->name;
        }
        if( $result->post_status == 'publish' ){
            $result->mjob_status = __('Approved');
            //$result->approve_class = 'mjob-approve';
            $result->status_action = 'unapprove';
        }
        else if( $result->post_status == 'pending'){
            $result->mjob_status = __('Unpprove');
            $result->status_action = 'approve';
        }
        $result->edit_link = $result->permalink .'?action=edit';
        if( $result->post_status == 'pending' ){
            $result->edit_link = $result->permalink .'&action=edit';
        }
        switch($result->post_status){
            case 'publish':
                $result->status_text = __('ACTIVE', ET_DOMAIN);
                $result->status_class = 'active-color';
                break;
            case 'pending':
                $result->status_text = __('PENDING', ET_DOMAIN);
                $result->status_class = 'pending-color';
                break;
            case 'archive':
                $result->status_text = __('ARCHIVED', ET_DOMAIN);
                $result->status_class = 'archive-color';
                break;
            case 'reject':
                $result->status_text = __('UNAPPROVE', ET_DOMAIN);
                $result->status_class = 'reject-color';
                break;
            case 'pause':
                $result->status_text = __('PAUSE', ET_DOMAIN);
                $result->status_class = 'pause-color';
                break;
            case 'draft':
                $result->status_text = __('DRAFT', ET_DOMAIN);
                $result->status_class = 'draft-color';
                break;
            case 'inactive':
                $result->status_text = __('INACTIVE', ET_DOMAIN);
                $result->status_class = 'draft-color';
                break;
            default:
                $result->status_text = __('ACTIVE', ET_DOMAIN);
                $result->status_class = 'active-color';
                break;

        }
        $result->mjob_status = '';
        $result->et_budget_text = mJobPriceFormat($result->et_budget);
        $m_orig		= get_post_field( 'post_modified', $result->ID, 'raw' );
        $m_stamp	= strtotime( $m_orig );
        $date_format = get_option('date_format');
        $result->modified_date = date( $date_format, $m_stamp );
        /**
         * return carousels
         */
        //if (current_user_can('manage_options') || $result->post_author == $user_ID) {
            $children = get_children(array(
                'numberposts' => 15,
                'order' => 'ASC',
                'post_parent' => $result->ID,
                'post_type' => 'attachment'
            ));

            $result->et_carousels = array();
            $result->et_carousel_urls = array();
            foreach ($children as $key => $value) {
                $result->et_carousels[] = $key;
                $result->et_carousel_urls[] = $value;
            }

            /**
             * set post thumbnail in one of carousel if the post thumbnail doesnot exists
             */
            if (has_post_thumbnail($result->ID)) {
                $thumbnail_id = get_post_thumbnail_id($result->ID);
                if (!in_array($thumbnail_id, $result->et_carousels)) $result->et_carousels[] = $thumbnail_id;

                $mjob_slider = wp_get_attachment_image_src($thumbnail_id, "mjob_detail_slider");
                $result->mjob_slider_thumbnail = $mjob_slider[0];
            }
            /*
             * extras
             */
            $children = get_posts(array(
                'post_type'=>'mjob_extra',
                'showposts'=> 20,
                'post_parent'=>$result->ID
            ));
            $extra_obj = $ae_post_factory->get('mjob_extra');
            $result->mjob_extras = array();
            foreach ($children as $key => $value) {
                $value = $extra_obj->convert($value);
                $result->mjob_extras[] = $value;
            }
        //}

//        $comment = get_comments(array(
//            'post_id' => $result->ID,
//            'type' => 'mjob_review'
//        ));
//        if ($comment) {
//            $result->mjob_comment = $comment;
//        } else {
//            $result->mjob_comment = '';
//        }
//        if(!empty($comment) ){
//            foreach($comment as $key=>$value){
//                $et_rate = get_comment_meta($value->comment_ID, 'et_rate', true);
//                $avatar = get_avatar( $value->comment_author_email, 40 );
//                $comment[$key]->et_rate = $et_rate;
//                $comment[$key]->avatar = $avatar;
//            }
//        }

        // Get total review
        $result->mjob_total_reviews = mJobConvertNumber(mJobCountReview($result->ID));
        if( isset($result->tax_input['skill']) ){
            $result->skill = $result->tax_input['skill'];
        }
        $result->plan_price = '';
        $result->plan_price_text = '';
        $result->plan_content = '';
        if( isset($result->et_payment_package) && !empty($result->et_payment_package) ) {
            //$package = $ae_post_factory->get('pack');
            $options = AE_Options::get_instance();
            $plan = '';
            if ($options->pack) {
                $packages = $options->pack;
                foreach ($packages as $key => $value) {
                    if ($value->sku == $result->et_payment_package) {
                        $plan = $value;
                    }
                }
            }
            if (!empty($plan)) {
                $result->plan_price = $plan->et_price;
                $result->plan_price_text = mJobPriceFormat($plan->et_price);
                $result->plan_content = $plan->post_content;
            }
        }
        $result->p_permalink = get_permalink( $result->ID );
        return $result;
    }

    /**
     * Add size when request thumbnail
     * @param array $thumbnail_size
     * @return  array $thumbnail_size
     * @since 1.0
     * @package MicrojobEngine
     * @category void
     * @author Tat Thien
     */
    public function filterThumbnailSize($thumbnail_size) {
        $thumbnail_size = wp_parse_args($thumbnail_size, array('mjob_detail_slider', 'medium_post_thumbnail'));
        return $thumbnail_size;
    }

    /**
     * validate data
     *
     * @param array $data
     * @return array $result
     * @since 1.0
     * @package MicrojobEngine
     * @category void
     * @author JACK BUI
     */
    public function validatePost($data){
        global $user_ID;
        $result = array(
            'success'=> true,
            'msg'=> __('Success!', ET_DOMAIN),
            'data'=> $data
        );
        /**
         * check payment package is valid or not
         * set up featured if this package is featured
         */
        if (isset($data['et_payment_package']) && !empty($data['et_payment_package'])) {

            /**
             * check package plan exist or not
             */
            global $ae_post_factory;
            $package = $ae_post_factory->get('pack');
            $plan = $package->get($data['et_payment_package']);
            if (!$plan){
              $result = array(
                  'success'=>false,
                  'msg'=> __("You have selected an invalid plan. Please choose another one!", ET_DOMAIN),
                  'data'=> $data
              );
            }
            /**
             * if user can not edit others posts the et_featured will no be unset and check,
             * this situation should happen when user edit/add post in backend.
             * Force to set featured post
             */
            if (!isset($data['et_featured']) || !$data['et_featured']) {
                $data['et_featured'] = 0;
                if (isset($plan->et_featured) && $plan->et_featured) {
                    $data['et_featured'] = 1;
                }
            }
            $result['data'] = $data;
        }
        if( isset($data['mjob_category']['0']) ){
            $t = get_term_by('id', $data['mjob_category']['0'], 'mjob_category');
            if( $t == 2 || $t->parent == 2){
                if( $data['time_delivery'] < 20 ){
                    $result = array(
                      'success'=>false,
                      'msg'=> __("Time delivery must be greater than 20 days for this category", ET_DOMAIN),
                      'data'=> $data
                    );
                }
            }
        }
        return $result;
    }
    /**
     * Override filter_query_args for action fetch_post.
     *
     */
    public function filter_query_args($query_args)
    {
        global $user_ID;
        $query = $_REQUEST['query'];
        // list featured profile
        if (isset($query['meta_key'])) {
            $query_args['meta_key'] = $query['meta_key'];
            if (isset($query['meta_value'])) {
                $query_args['meta_value'] = $query['meta_value'];
            }
        }

        //filter project by project category and skill
        if (isset($query['mjob_category']) && !empty($query['mjob_category'])) {
            if(is_numeric($query['mjob_category'])) {
                $tax_field = 'term_id';
            } else {
                $tax_field = 'slug';
            }

            // Filter by skill and mjob category
            if(isset($query['skill']) && !empty($query['skill'])) {
                $skill = $query['skill'];
                $query_args['tax_query'] = array(
                    'relation' => 'AND',
                    array(
                        'taxonomy' => 'skill',
                        'field' => 'term_id',
                        'terms' => $skill
                    ),
                    array(
                        'taxonomy' => 'mjob_category',
                        'field' => $tax_field,
                        'terms' => array($query['mjob_category'])
                    )
                );
            } else { // Filter by mjob category only
                $query_args['tax_query'] = array(
                    array(
                        'taxonomy' => 'mjob_category',
                        'field' => $tax_field,
                        'terms' => array($query['mjob_category'])
                    )
                );
            }
        } else if(isset($query['skill']) && !empty($query['skill'])) {
            // Filter by skill only
            $skill = $query['skill'];
            $query_args['tax_query'] = array(
                array(
                    'taxonomy' => 'skill',
                    'field' => 'term_id',
                    'terms' => $skill
                ),
            );
        }



        // project posted from query date
        if (isset($query['date'])) {
            $date = $query['date'];
            $day = date('d', strtotime($date));
            $mon = date('m', strtotime($date));
            $year = date('Y', strtotime($date));
            $query_args['date_query'][] = array(
                'year' => $year,
                'month' => $mon,
                'day' => $day,
                'inclusive' => true
            );
        }
        /**
         * add query when archive project type
         */

        if (current_user_can('manage_options') && isset($query['is_archive_mjob_post']) && $query['is_archive_mjob_post'] == TRUE) {
            $query_args['post_status'] = array(
                'pending',
                'publish'
            );
        }
        // query arg for filter page default
        if (isset($query['orderby'])) {
            $orderby = $query['orderby'];
            switch ($orderby) {
                case 'et_featured':
                    $query_args['meta_key'] = $orderby;
                    $query_args['orderby'] = 'meta_value_num date';
                    $query_args['meta_query'] = array(
                        'relation' => 'OR',
                        array(
                            //check to see if et_featured has been filled out
                            'key' => $orderby,
                            'compare' => 'IN',
                            'value' => array(
                                0,
                                1
                            )
                        ) ,
                        array(
                            //if no et_featured has been added show these posts too
                            'key' => $orderby,
                            'value' => 0,
                            'compare' => 'NOT EXISTS'
                        )
                    );
                    break;

                case 'et_budget':
                    $query_args['meta_key'] = 'et_budget';
                    $query_args['orderby'] = 'meta_value_num date';
                    break;

                case 'rating_score':
                    $query_args['meta_key'] = $orderby;
                    $query_args['orderby'] = 'meta_value_num date';
                    break;
                case 'date':
                    $query_args['orderby'] = 'date';
                    $query_args['order'] = 'DESC';
                    break;
                default:
                    add_filter('posts_orderby', array(
                        'ET_Microjobengine',
                        'order_by_post_pending'
                    ) , 2, 12);
                    break;
            }
        }

        /*
         * set post status when query in page profile or author.php
        */
        $query_args['post_status'] = array(
            'pause',
            'unpause',
            'publish',
            'inactive'
        );

        if (isset($query['is_author']) && $query['is_author']) {
            if (!isset($query['post_status'])) {
                $query_args['post_status'] = array(
                    'close',
                    'complete',
                    'publish',
                    'inactive'
                );
            }
            $query_args['post_status'] = $query['post_status'];
        }
        if ((isset($query['post_status']) && $query['post_status'] == 'publish') || current_user_can('manage_options')){
            $query_args['post_status'] = $query['post_status'];
        }
        if (isset($query['post_status']) && isset($query['author']) && $query['post_status'] && $user_ID == $query['author']) {
            $query_args['post_status'] = $query['post_status'];
        }

        // Post status is active will be convert to publish and unpause
        if(isset($query['post_status']) && $query['post_status'] == 'active') {
            $query_args['post_status'] = array(
                'publish',
                'unpause'
            );
        }
        return $query_args;
    }
    /**
     * convert user
     *
     * @param object $user
     * @return void
     * @since 1.0
     * @package MicrojobEngine
     * @category void
     * @author JACK BUI
     */
    public function mjob_convert_user($user){
        $user->mjobAjaxNonce = de_create_nonce('ae-mjob_post-sync');
        return $user;
    }
    /**
     * get mjob post
     *
     * @param integer $mjob_id
     * @return object $mjob_post / false
     * @since 1.0
     * @package MicrojobEngine
     * @category void
     * @author JACK BUI
     */
    public function get_mjob($mjob_id=''){
        if( empty($mjob_id) ){
            return false;
        }
        global $ae_post_factory;
        $mjob_obj = $ae_post_factory->get('mjob_post');
        $post = get_post($mjob_id);
        if( $post ){
            return $mjob_obj->convert($post);
        }
        return false;

    }
    /**
     * get mjob
     *
     * @param void
     * @return void
     * @since 1.0
     * @package MicrojobEngine
     * @category void
     * @author JACK BUI
     */
    public function getMjobPost(){
        global $user_ID;
        $request = $_REQUEST;
        $response = array(
            'success'=> false,
            'msg'=> __('failed', ET_DOMAIN)
        );
        if( isset($request['ID']) && !empty($request['ID']) ){
            global $ae_post_factory;
            $mjob_obj = $ae_post_factory->get('mjob_post');
            $post = get_post($request['ID']);
            if( $post && ($post->post_author == $user_ID || is_super_admin())){
                $mjob = $mjob_obj->convert($post);
                $response = array(
                    'success'=> true,
                    'msg'=> __('Success!', ET_DOMAIN),
                    'data'=>$mjob );
            }
        }
        wp_send_json($response);
    }
    /**
     * get mjob tags
     *
     * @param void
     * @return void
     * @since 1.0
     * @package MicrojobEngine
     * @category void
     * @author JACK BUI
     */
    public function getMjobTags(){
        $request = $_REQUEST;
        $result = '<h3 class="title-content">'.__('Tags', ET_DOMAIN).'</h3>';
        if(isset($request['ID']) ){
            $post = get_post($request['ID']);
            if( $post ) {
                $result .= get_the_taxonomy_list('skill', $post);
            }
        }
        wp_send_json($result);
    }
    /**
     * get breadcum tags
     *
     * @param void
     * @return void
     * @since 1.0
     * @package MicrojobEngine
     * @category void
     * @author JACK BUI
     */
    public function getMjobCats(){
        $request = $_REQUEST;
        $result = '';
        $breadcrum = '';
        if(isset($request['term_id']) ){
            $cat = get_term_by('ID', $request['term_id'], 'mjob_category');
            $breadcrumb = '<p class="mjob-breadcrumb"><a class="parent" href="'. get_term_link($cat) .'">'. $cat->name .'</a></p>';
            $breadcrum = '<p>'.$cat->name.'</p>';
            $parent = $cat->parent;
            if( $parent != 0 ){
                $parent = get_term_by('ID', $parent, 'mjob_category');
                $breadcrumb = '<p class="mjob-breadcrumb"><a class="parent" href="'. get_term_link($parent) .'">'. $parent->name .'</a> <i class="fa fa-angle-right"></i> <span><a class="child" href="'. get_term_link($cat) .'">'. $cat->name .'</a></span></p>';
            }
        }
        wp_send_json($breadcrumb);
    }
    /**
      * get user status
      *
      * @param integer/string $user_id
      * @return string $user_status
      * @since 1.4
      * @package MicrojobEngine
      * @category CREDZU
      * @author JACK BUI
      */
    public function getUserStatus( $user_id = '' ){
        $user_status = get_user_meta($user_id, 'user_status', true);
        return $user_status;
    }
    /**
      * add meta field for mjob category
      *
      * @param void
      * @return void
      * @since 1.4
      * @package MicrojobEngine
      * @category CREDZU
      * @author JACK BUI
      */
    public function mjob_add_meta_field($taxonomy){
        if( $taxonomy == 'mjob_category'):
            global $featured_tax, $user_ID, $ae_post_factory;
            $ae_pack = $ae_post_factory->get('pack');
            $packs = $ae_pack->fetch('pack');
            $term_id = 0;
            // Remove image URL
            $remove_url = add_query_arg( array(
            'action'   => 'remove-wp-term-images',
            'term_id'  => $term_id,
            '_wpnonce' => false,
            ) );
            // Get the meta value
            $value = get_term_meta($term_id, 'mjob_category_image', true);
            $banner_value = get_term_meta($term_id, 'mjob_category_banner_image', true);
            $hidden = empty( $value )
            ? ' style="display: none;"'
            : '';
             $hidden_banner = empty( $banner_value )
            ? ' style="display: none;"'
            : '';
            ?>
            <div class="form-field term-group">
            <label><?php _e('Page content', ET_DOMAIN) ?></label>
            <div>
                <textarea rows="5" name="<?php echo $taxonomy ?>_page_content" id="<?php echo $taxonomy ?>_page_content"></textarea>
            </div>
            <br/>
            <label><?php _e('Pricing plan', ET_DOMAIN) ?></label>
            <div>
                <select name="pricing_plan">
                    <?php if(!empty($packs)):
                        foreach($packs as $pack):
                            ?>
                            <option  value="<?php echo $pack->sku; ?>"><?php echo $pack->post_title; ?></option>
                        <?php endforeach;
                    endif; ?>
                </select>
            </div>
            <br/>
            <div class="form-field term-group">
                <p>
                <label><?php _e('Taxonomy image', ET_DOMAIN) ?></label>
                <div>
                    <img id="<?php echo $taxonomy ?>_image_photo" width="75px" height="75px" src="<?php echo esc_url( wp_get_attachment_image_url( $value, 'full' ) ); ?>"<?php echo $hidden; ?> />
                    <input type="hidden" name="<?php echo $taxonomy; ?>_image" id="<?php echo $taxonomy; ?>_image" value="<?php echo esc_attr( $value ); ?>" />
                </div>

                <a class="button-secondary ae-tax-images-media <?php echo $taxonomy ?>_image_button" data-id="<?php echo $taxonomy ?>_image">
                    <?php esc_html_e( 'Choose Image', ET_DOMAIN ); ?>
                </a>

                <a href="<?php echo esc_url( $remove_url ); ?>" class="button ae-tax-images-remove <?php echo $taxonomy ?>_image_photo_remove"<?php echo $hidden; ?> data-id="<?php echo $taxonomy ?>_image">
                    <?php esc_html_e( 'Remove', 'wp-user-avatars' ); ?>
                </a>
                </p>
                <br/>
                <p>
                 <label><?php _e('Taxonomy banner image', ET_DOMAIN) ?></label>
                <div>
                    <img id="<?php echo $taxonomy; ?>_banner_image_photo" width="75px" height="75px" src="<?php echo esc_url( wp_get_attachment_image_url( $banner_value, 'full' ) ); ?>"<?php echo $hidden_banner; ?> />
                    <input type="hidden" name="<?php echo $taxonomy; ?>_banner_image" id="<?php echo $taxonomy; ?>_banner_image" value="<?php echo esc_attr( $banner_value ); ?>" />
                </div>

                <a class="button-secondary ae-tax-images-media <?php echo $taxonomy ?>_banner_image_button" data-id="<?php echo $taxonomy; ?>_banner_image">
                    <?php esc_html_e( 'Choose Image', ET_DOMAIN ); ?>
                </a>

                <a href="<?php echo esc_url( $remove_url ); ?>"  class="button ae-tax-images-remove <?php echo $taxonomy; ?>_banner_image_photo_remove"<?php echo $hidden_banner; ?> data-id="<?php echo $taxonomy ?>_banner_image">
                    <?php esc_html_e( 'Remove', 'wp-user-avatars' ); ?>
                </a>
                </p>
                <div class="clearfix"></div>
                <br/>
                <div class="featured-tax">
                    <input type="checkbox" name="featured-tax" class="left margin-20 margin-top-3" value="true" />
                    <label for="featured-tax" class="left"><?php _e('Featured taxonomy', ET_DOMAIN); ?></label>
                </div>
                <br/>
                <br/>
                <p>
                    <label for="cat_bottom_title"><?php _e('Bottom Title', ET_DOMAIN) ?></label>
                    <input type="text" name="cat_bottom_title" />
                </p>
                <p>
                    <label for="cat_bottom_block1_title"><?php _e('Bottom block 1 title', ET_DOMAIN) ?></label>
                    <input type="text" name="cat_bottom_block1_title" />
                </p>
                <p>
                    <label for="cat_bottom_block1_content"><?php _e('Bottom block 1 content', ET_DOMAIN) ?></label>
                    <textarea name="cat_bottom_block1_content" rows="5"> </textarea>
                </p>
                <p>
                    <label for="cat_bottom_block2_title"><?php _e('Bottom block 2 title', ET_DOMAIN) ?></label>
                    <input type="text" name="cat_bottom_block2_title" />
                </p>
                <p>
                    <label for="cat_bottom_block2_content"><?php _e('Bottom block 2 content', ET_DOMAIN) ?></label>
                    <textarea name="cat_bottom_block2_content" rows="5"> </textarea>
                </p>
                <p>
                    <label for="cat_bottom_block3_title"><?php _e('Bottom block 3 title', ET_DOMAIN) ?></label>
                    <input type="text" name="cat_bottom_block3_title" />
                </p>
                <p>
                    <label for="cat_bottom_block3_content"><?php _e('Bottom block 3 content', ET_DOMAIN) ?></label>
                    <textarea name="cat_bottom_block3_content" rows="5"> </textarea>
                </p>
            </div>
            <div class="clearfix"></div>
            <br/>
                <?php
                elseif( $taxonomy == 'mjob_requirement'): ?>
                    <label><?php _e('What will open when click to this item', ET_DOMAIN) ?></label>
                    <div>
                        <select name="click_type">
                            <option value="open-upload-modal"><?php _e('Open upload modal', ET_DOMAIN); ?></option>
                            <option value="open-contact-info"><?php _e('Open Contact information', ET_DOMAIN); ?></option>
                            <option value="open-billing-info"><?php _e('Open billing information', ET_DOMAIN); ?></option>
                        </select>
                    </div>
                    <br/>
            <?php    endif;
        }
    /**
      * edit meta field
      *
      * @param void
      * @return void
      * @since 1.4
      * @package MicrojobEngine
      * @category CREDZU
      * @author JACK BUI
      */
    public function mjob_edit_meta_field($term, $taxonomy, $meta){
         global $featured_tax, $user_ID, $ae_post_factory, $ae_tax_factory;
         $obj_tax = $ae_tax_factory->get($taxonomy);
         $term = $obj_tax->convert($term);
        if( $taxonomy == 'mjob_category'):

            $ae_pack = $ae_post_factory->get('pack');
            $packs = $ae_pack->fetch('pack');
            // get current group
            $check = '';
            $featured_tax = get_term_meta( $term->term_id, 'featured-tax', true );
            if( $featured_tax ){
                $check = 'checked';
            }
            $remove_url = add_query_arg( array(
                'action'   => 'remove-ae-tax-images',
                'term_id'  => $term->term_id,
                '_wpnonce' => false,
            ) );
            $value = get_term_meta($term->term_id, 'mjob_category_image', true);
            $banner_value = get_term_meta($term->term_id, 'mjob_category_banner_image', true);
            $hidden = empty( $value )
                ? ' style="display: none;"'
                : '';
            $banner_hidden = empty( $banner_value )
                ? ' style="display: none;"'
                : '';
            $arr = array();
            foreach( $meta as $key=>$valu ){
                $arr[$valu] = get_term_meta($term->term_id, $valu, true);
            }
            ?>
            <tr class="form-field term-group-wrap">
                <th scope="row"><label for="page-content-tax"><?php _e( 'Page content', ET_DOMAIN ); ?></label></th>
                <td>
                   <?php wp_editor( $term->mjob_category_page_content, $taxonomy.'_page_content'  ); ?>
                </td>
            </tr>
            <tr class="form-field term-group-wrap">
                <th scope="row"><label for="featured-tax"><?php _e( 'Pricing plan', ET_DOMAIN ); ?></label></th>
                <td>
                    <select name="pricing_plan">
                    <?php if(!empty($packs)):
                        foreach($packs as $pack):
                            $selected = '';
                            if( $pack->sku == $arr['pricing_plan']):
                                $selected = 'selected';
                            endif;
                        ?>
                        <option <?php echo $selected; ?> value="<?php echo $pack->sku; ?>"><?php echo $pack->post_title; ?></option>
                    <?php endforeach;
                        endif; ?>
                    </select>
                </td>
            </tr>
            <tr class="form-field term-group-wrap">
            <th scope="row"><label for="featured-tax"><?php _e( 'Featured taxonomy', ET_DOMAIN ); ?></label></th>
            <td><input type="checkbox" name="featured-tax" value="true" <?php echo $check; ?>/> <label for="featured-tax"><?php _e('Featured taxonomy', ET_DOMAIN); ?></label></td>
            </tr>
            <tr>
                <th scope="row"><label for="tax-image"><?php _e( 'taxonomy image', ET_DOMAIN ); ?></label></th>
                <td>
                    <div>
                        <img id="<?php echo $taxonomy ?>_image_photo" width="75px" height="75px" src="<?php echo esc_url( wp_get_attachment_image_url( $value, 'full' ) ); ?>"<?php echo $hidden; ?> />
                        <input type="hidden" name="<?php echo $taxonomy; ?>_image" id="<?php echo $taxonomy; ?>_image" value="<?php echo esc_attr( $value ); ?>" />
                    </div>

                    <a class="button-secondary ae-tax-images-media <?php echo $taxonomy ?>_image_button" data-id="<?php echo $taxonomy ?>_image">
                        <?php esc_html_e( 'Choose Image', ET_DOMAIN ); ?>
                    </a>

                    <a href="<?php echo esc_url( $remove_url ); ?>" class="button ae-tax-images-remove <?php echo $taxonomy ?>_image_photo_remove"<?php echo $hidden; ?> data-id="<?php echo $taxonomy ?>_image">
                        <?php esc_html_e( 'Remove', 'wp-user-avatars' ); ?>
                    </a>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="tax-banner-image"><?php _e( 'Taxonomy banner image', ET_DOMAIN ); ?></label></th>
                <td>
                    <div>
                        <img id="<?php echo $taxonomy ?>_banner_image_photo" width="75px" height="75px" src="<?php echo esc_url( wp_get_attachment_image_url( $banner_value, 'full' ) ); ?>"<?php echo $hidden; ?> />
                        <input type="hidden" name="<?php echo $taxonomy; ?>_banner_image" id="<?php echo $taxonomy; ?>_banner_image" value="<?php echo esc_attr( $banner_value ); ?>" />
                    </div>

                    <a class="button-secondary ae-tax-images-media <?php echo $taxonomy ?>_banner_image_button" data-id="<?php echo $taxonomy ?>_banner_image">
                        <?php esc_html_e( 'Choose Image', ET_DOMAIN ); ?>
                    </a>

                    <a href="<?php echo esc_url( $remove_url ); ?>" class="button ae-tax-images-remove <?php echo $taxonomy ?>_banner_image_photo_remove"<?php echo $banner_hidden; ?> data-id="<?php echo $taxonomy ?>_banner_image">
                        <?php esc_html_e( 'Remove', 'wp-user-avatars' ); ?>
                    </a>
                </td>
            </tr>
            <tr class="form-field term-slug-wrap">
                <th scope="row"><label for="cat_bottom_title"><?php _e('Bottom Title', ET_DOMAIN) ?></label></th>
                <td><input type="text" name="cat_bottom_title" size="40" value="<?php echo $arr['cat_bottom_title']; ?>"/></td>
            </tr>
            <tr class="form-field term-slug-wrap">
                <th scope="row"><label for="cat_bottom_block1_title"><?php _e('Bottom block 1 title', ET_DOMAIN) ?></label></th>
                <td><input type="text" name="cat_bottom_block1_title" value="<?php echo $arr['cat_bottom_block1_title']; ?>"/></td>
            </tr>
            <tr class="form-field term-slug-wrap">
                <th scope="row"><label for="cat_bottom_block1_content"><?php _e('Bottom block 1 content', ET_DOMAIN) ?></label></th>
                <td><textarea name="cat_bottom_block1_content" rows="5"><?php echo $arr['cat_bottom_block1_content']; ?> </textarea></td>
            </tr>
            <tr class="form-field term-slug-wrap">
                <th scope="row"><label for="cat_bottom_block2_title"><?php _e('Bottom block 2 title', ET_DOMAIN) ?></label></th>
                <td><input type="text" name="cat_bottom_block2_title" value="<?php echo $arr['cat_bottom_block2_title']; ?>" /></td>
            </tr>
            <tr class="form-field term-slug-wrap">
                <th scope="row"><label for="cat_bottom_block2_content"><?php _e('Bottom block 2 content', ET_DOMAIN) ?></label></th>
                <td><textarea name="cat_bottom_block2_content" rows="5"> <?php echo $arr['cat_bottom_block2_content']; ?></textarea></td>
            </tr>
            <tr class="form-field term-slug-wrap">
                <th scope="row"><label for="cat_bottom_block3_title"><?php _e('Bottom block 3 title', ET_DOMAIN) ?></label></th>
                <td><input type="text" name="cat_bottom_block3_title" value="<?php echo $arr['cat_bottom_block3_title']; ?>" /></td>
            </tr>
            <tr class="form-field term-slug-wrap">
                <th scope="row"><label for="cat_bottom_block3_content"><?php _e('Bottom block  content', ET_DOMAIN) ?></label></th>
                <td><textarea name="cat_bottom_block3_content" rows="5"> <?php echo $arr['cat_bottom_block3_content']; ?></textarea></td>
            </tr>
            <?php
             elseif( $taxonomy == 'mjob_requirement'):
             ?>
            <tr class="form-field term-group-wrap">
                <th scope="row"><label for="featured-tax"><?php _e( 'What will open when click to this item', ET_DOMAIN ); ?></label></th>
                <td>
                    <select name="click_type">
                        <?php if( $term->click_type == 'open-upload-modal'): ?>
                        <option selected value="open-upload-modal"><?php _e('Open upload modal', ET_DOMAIN); ?></option>
                        <?php else: ?>
                            <option  value="open-upload-modal"><?php _e('Open upload modal', ET_DOMAIN); ?></option>
                        <?php endif; ?>
                        <?php if($term->click_type == 'open-contact-info' ):  ?>
                        <option selected value="open-contact-info"><?php _e('Open Contact information', ET_DOMAIN); ?></option>
                        <?php else: ?>
                        <option  value="open-contact-info"><?php _e('Open Contact information', ET_DOMAIN); ?></option>
                        <?php endif; ?>
                        <?php if( $term->click_type == 'open-billing-info' ): ?>
                        <option selected value="open-billing-info"><?php _e('Open billing information', ET_DOMAIN); ?></option>
                        <?php else: ?>
                            <option  value="open-billing-info"><?php _e('Open billing information', ET_DOMAIN); ?></option>
                        <?php endif; ?>
                    </select>
                </td>
            </tr>
            <tr class="form-field term-group-wrap">
                <th scope="row"><label for="featured-tax"><?php _e( 'Short name', ET_DOMAIN ); ?></label></th>
                <td>
                    <input type="text" name="requirement_short_name" id="requirement_short_name" value="<?php echo $term->requirement_short_name; ?>" />
                </td>
            </tr>
           <?php endif;
    }
    /**
      * filter tax information
      *
      * @param void
      * @return void
      * @since 1.4
      * @package MicrojobEngine
      * @category CREDZU
      * @author JACK BUI
      */
      public function filterTaxInfo($result){
        if( empty($result->requirement_short_name)){
            $result->requirement_short_name = $result->name;
        }
        return $result;
      }
      /**
        * check mjob cat is create repair
        *
        * @param void
        * @return void
        * @since 1.4
        * @package MicrojobEngine
        * @category CREDZU
        * @author JACK BUI
        */
        public function checkMjobCat(){
            $request = $_REQUEST;
            $response = array('success'=> false);
            if( isset($request['cat_id']) ){
                $term = get_term_by('id', $request['cat_id'], 'mjob_category');
                if( $term && !is_wp_error($term)){
                    if( $term->id == 2 || $term->parent == 2 ){
                        $response = array(
                            'success'=> true,
                            'msg'=> __('This is a credit repair or child of credit repair', ET_DOMAIN)
                        );
                    }
                }
            }
            wp_send_json($response);
        }

}
new mJobAction();
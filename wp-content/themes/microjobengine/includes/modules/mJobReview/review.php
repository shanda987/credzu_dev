<?php

/**
 * project review class
 */
class MjobReview extends AE_Comments
{
    static $current_review;
    static $instance;

    /**
     * return class $instance
     */
    public static function get_instance($type = "mjob_review") {
        if (self::$instance == null) {

            self::$instance = new MjobReview($type);
        }
        return self::$instance;
    }

    public function __construct($type = "mjob_review") {
        $this->comment_type = $type;
        $this->meta = array(
            'et_rate'
        );

        $this->post_arr = array();
        $this->author_arr = array();

        $this->duplicate = true;
        $this->limit_time = 120;
    }

    /**
     * The function retrieve employer rating score and review count
     * @param Integer $user_id The employer id
     * @since 1.0
     * @author JACK BUI
     */
    public static function user_rating_score($user_id) {
        global $wpdb;
        $sql = "SELECT AVG(M.meta_value)  as rate_point, COUNT(C.comment_ID) as count
                FROM $wpdb->posts as  p
                    join $wpdb->comments as C
                                ON p.ID = c.comment_post_ID
                    join $wpdb->commentmeta as M
                        ON C.comment_ID = M.comment_id
                WHERE
                    p.post_author = $user_id
                    AND p.post_status ='finished'
                    AND p.post_type ='mjob_order'
                    AND M.meta_key = 'et_rate'
                    AND C.comment_type ='mjob_review'
                    AND C.comment_approved = 1";

        $results = $wpdb->get_results($sql);
        if($results) {
            return array('rating_score' => $results[0]->rate_point , 'review_count' => $results[0]->count );
        }else {
            return array('rating_score' => 0 , 'review_count' => 0 );
        }
    }
}

/**
 * The class control all action related to freelancer and employer review
 * @since 1.0
 * @package MicrojobEngine
 * @category Review
 * @author Dakachi
 */
class MjobReviewAction extends AE_Base
{

    public function __construct() {

        //$this->mail = Fre_Mailing::get_instance();

        //$this->add_action('preprocess_comment', 'process_review');

        // $this->add_action( 'comment_post' , 'update_rating');
        $this->init_ajax();
        $this->add_filter('ae_convert_comment', 'mJobFilterComment');
    }

    /**
     * init ajax action
     * @since 1.0
     * @author Dakachi
     */
    function init_ajax() {
        $this->add_ajax('mjob-user-review', 'user_review_action', true, false);
        $this->add_ajax('mjob-fetch-review', 'mJobFetchReview');
    }

    /**
     * Fetch review
     * @param void
     * @return void
     * @since 1.0
     * @package MicrojobEngine
     * @category Review
     * @author Tat Thien
     */
    public function mJobFetchReview() {
        $request = $_REQUEST;
        $page = $request['page'];
        $query_args = $request['query'];
        $query_args['page'] = $page;

        $review_obj = MjobReview::get_instance();
        $reviews = $review_obj->fetch($query_args);
        $reviews = $reviews['data'];

        if(!empty($reviews)) {
            wp_send_json(array(
                'success' => true,
                'data' => $reviews,
                'max_num_pages' => $query_args['total']
            ));
        } else {
            wp_send_json(array(
                'success' => false
            ));
        }
    }

    /**
     * Filter review data
     * @param $comment
     * @return $comment
     * @since 1.0
     * @package MicrojobEngine
     * @category Review
     * @author Tat Thien
     */
    public function mJobFilterComment($comment) {
        $avatar = mJobAvatar($comment->user_id, 75);
        $comment->avatar_user = $avatar;
        return $comment;
    }

    /*
     * add review by freelancer.
    */
    function user_review_action() {
        global $user_ID, $ae_post_factory, $current_user;
        $args = $_POST;
        $order_id = $args['order_id'];
        $status = get_post_status($order_id);
        $order_obj = $ae_post_factory->get('mjob_order');
        $order = get_post($order_id);
        $order = $order_obj->convert($order);
        $mjob_id = $order->mjob_id;
        $author_order = get_post_field('post_author', $order_id);
        // Review class
        $review = MjobReview::get_instance("mjob_review");
        /*
         * validate data
        */
        if (!isset($args['score']) || empty($args['score'])) {
            $result = array(
                'succes' => false,
                'msg' => __('You have to rate this mJob!', ET_DOMAIN)
            );
            wp_send_json($result);
        }
        if (!isset($args['comment_content']) || empty($args['comment_content'])) {
            $result = array(
                'succes' => false,
                'msg' => __('Please write a review for this mJob!', ET_DOMAIN)
            );
            wp_send_json($result);
        }

        /*
         * check permission
        */
        if ($user_ID != $author_order || !$user_ID) {
            wp_send_json(array(
                'succes' => false,
                'msg' => __('You have to be the owner of this mJob to review!', ET_DOMAIN)
            ));
        }

        /*
         *  check status of project
        */
        if ($status != 'delivery' && $status != 'finished') {
            wp_send_json(array(
                'succes' => false,
                'msg' => __('Wait until the order is delivered to review!', ET_DOMAIN)
            ));
        }
        /**
         * check user reviewed project owner or not
         *
         */
        $type = 'mjob_review';
//        $comment = get_comments(array(
//            'status' => array('approve', 'hold'),
//            'type' => $type,
//            'post_id' => $mjob_id,
//            'author_email' => $current_user->user_email,
//            'meta_key' => 'order_id',
//            'meta_value' => $order_id
//        ));
//
//        if (!empty($comment)) {
//            wp_send_json(array(
//                'succes' => false,
//                'msg' => __('You have already reviewed this mJob!', ET_DOMAIN)
//            ));
//        }

        // end check user review project owner

        // add review
        $args['comment_post_ID'] = $mjob_id;
        $args['comment_approved'] = 1;
        $this->comment_type = 'mjob_reivew';
        $comment = $review->insert($args);
        if (!is_wp_error($comment)) {

            /**
             * fire action after freelancer review employer base on project
             * @param int $int project id
             * @param Array $args submit args (rating score, comment)
             * @since 1.2
             * @author Dakachi
             */
            do_action('user_review_mjob', $mjob_id, $args);

            //update project, bid, bid author, project author after review
            $this->update_after_MjobReview($mjob_id, $comment, $args);

            // Transfer working fund to available fund
            // Check if order is transferred or not
            AE_WalletAction()->transferWorkingToAvailable($order->seller_id, $order->ID, $order->real_amount);

            wp_send_json(array(
                'success' => true,
                'msg' => __("Your review has been submitted.", ET_DOMAIN)
            ));
        } else {

            // revert bid status
            wp_update_post(array(
                'ID' => $order_id,
                'post_status' => 'Delivery'
            ));

            wp_send_json(array(
                'success' => false,
                'msg' => $comment->get_error_message()
            ));
        }
    }
    /**
     * Description
     *
     * @param integer $mjob_id
     * @param integer $comment_id
     * @return void
     * @since 1.0
     * @package MicrojobEngine
     * @category void
     * @author JACK BUI
     */
    function update_after_MjobReview($mjob_id, $comment_id, $args) {
        global $wpdb;

        // Update comment meta
        update_comment_meta($comment_id, 'order_id', $args['order_id']);

        if (isset($_POST['score']) && $_POST['score']) {
            $rate = (float)$_POST['score'];
            if ($rate > 5) $rate = 5;
            update_comment_meta($comment_id, 'et_rate', $rate);
            update_post_meta($mjob_id, 'rating_score', $rate);
        }
        $user_id = (int)get_post_field('post_author', $mjob_id);
        $sql = "SELECT AVG(M.meta_value)  as rate_point, COUNT(C.comment_ID) as count
                FROM $wpdb->posts as  p
                    join $wpdb->comments as C
                                ON p.ID = C.comment_post_ID
                    join $wpdb->commentmeta as M
                        ON C.comment_ID = M.comment_id
                WHERE
                    p.post_author = $user_id
                    AND p.post_type ='mjob_post'
                    AND M.meta_key = 'et_rate'
                    AND C.comment_type ='mjob_review'
                    AND C.comment_approved = 1";
        $results = $wpdb->get_results($sql);
        if ($results) {
            wp_cache_set("reviews-{$user_id}", $results[0]->count);

            // update post rating score
            update_post_meta($mjob_id, 'rating_score', $results[0]->rate_point);
        } else {
            update_post_meta($mjob_id, 'rating_score', $rate);
        }
        // send mail to employer.
        //$this->mail->review_mjob_email($mjob_id);
    }

    /**
     * fetch comment
     */
    function fetch_comments() {

        global $ae_post_factory;
        $review_object = $ae_post_factory->get('de_review');

        // get review object

        $page = isset($_REQUEST['page']) ? $_REQUEST['page'] : 2;
        $query = $_REQUEST['query'];

        $map = array(
            'status' => 'approve',
            'meta_key' => 'et_rate',
            'type' => 'review',
            'post_type' => 'place',
            'number' => '4',
            'total' => '10'
        );

        $query['page'] = $page;

        $data = $review_object->fetch($query);
        if (!empty($data)) {
            $data['success'] = true;
            wp_send_json($data);
        } else {
            wp_send_json(array(
                'success' => false,
                'data' => $data
            ));
        }
    }

    /**
     * display form for freelancer review employer  after complete project.
     * @since  1.0
     * @author Dan
     */
    function mjob_review_form() {
        wp_reset_query();
        global $user_ID;
        $status = get_post_status(get_the_ID());
        $bid_accepted = get_post_field('accepted', get_the_ID());
        $freelan_id = (int)get_post_field('post_author', $bid_accepted);
        $comment = get_comments(array(
            'status' => 'approve',
            'post_id' => get_the_ID() ,
            'type' => 'mjob_review'
        ));
        $review = isset($_GET['review']) ? (int)$_GET['review'] : 0;
        $status = get_post_status(get_the_ID());

        if (empty($comment) && $user_ID == $freelan_id && $review && $status == 'complete') { ?>
            <script type="text/javascript">
            (function($, Views, Models, Collections) {
                $(document).ready(function(){
                    this.modal_review       = new AE.Views.Modal_Review();
                    this.modal_review.openModal();
                });
            })(jQuery, AE.Views, AE.Models, AE.Collections);
            </script>

            <?php
        }
    }
}


/**
 * Retrieve total review for employer or freelancer
 *
 *
 * @param int $user_id required. User ID.
 * @return object review stats.
 */
function fre_count_reviews($user_id = 0) {

    global $wpdb;

    $user_id = (int)$user_id;
    $role = ae_user_role($user_id);
    $count = wp_cache_get("reviews-{$user_id}");

    if (false !== $count) return $count;

    $sql = '';
    if ($role != 'freelancer') {
        $sql = "SELECT distinct  COUNT(C.comment_ID) as count
                    from $wpdb->posts as  p
                    Join $wpdb->comments as C
                        on p.ID = C.comment_post_ID
                        where p.post_author = $user_id
                              and p.post_status ='complete'
                              and p.post_type ='" . PROJECT . "'
                              and C.comment_type ='MjobReview'
                              and C.comment_approved = 1 ";
    } elseif ($role == 'freelancer') {
        $sql = "SELECT COUNT(C.comment_ID) as count
                from $wpdb->posts as  p
                left join $wpdb->comments as C
                    on p.ID = C.comment_post_ID
                where   p.post_status ='complete'
                        and p.post_author = $user_id
                        and p.post_type ='" . BID . "'
                        and C.comment_type ='em_review'
                        and C.comment_approved = 1 ";
    }

    $result = $wpdb->get_results($sql);
    if ($result) {
        $count = $result[0]->count;

        // $count = $wpdb->get_var($sql);


    } else {
        $count = 0;
    }
    wp_cache_set("reviews-{$user_id}", $count);
    return (int)$count;
}
?>
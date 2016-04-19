<?php
class mJobExtraAction extends mJobPostAction{
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
    public  function __construct($post_type = 'mjob_extra'){
        $this->post_type = 'mjob_extra';
        $this->add_ajax('ae-fetch-mjob_extra', 'fetch_post');
        $this->add_ajax('ae-mjob_extra-sync', 'syncPost');
        $this->add_filter('ae_convert_mjob_extra', 'convertPost');
        $this->add_filter('ae_convert_after_insert_mjob_extra', 'convertPost');
        $this->ruler = array(
            'post_title'=>'required',
            'et_budget'=>'required'
        );
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
        $result = $this->validatePost($request);
        if( $result['success'] ){
            $request['post_status'] = 'publish';
            if( $request['et_budget'] < 0 ){
                $request['et_budget'] = 0;
            }
            if( isset($request['post_title']) ) {
                $request['post_content'] = $request['post_title'];
            }
            else{
                $request['post_content'] = '';
            }
            $result = $this->sync_post($request);
        }
        wp_send_json($result);
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
        $result->et_budget_text = mJobPriceFormat($result->et_budget);
        return $result;
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
        $result = array(
            'success'=> true,
            'msg'=> __('Success!', ET_DOMAIN)
        );
        return $result;
    }
    /**
     * get extra of a Microjob
     *
     * @param integer $extra_id
     * @param integer $mjob_id;
     * @return object $extra or false
     * @since 1.0
     * @package MicrojobEngine
     * @category void
     * @author JACK BUI
     */
    public function get_extra_of_mjob($extra_id, $mjob_id){
        global $ae_post_factory;
        $post_obj = $ae_post_factory->get('mjob_extra');
        $post = get_post($extra_id);
        if( !$post || $post->post_parent != $mjob_id ){
            return false;
        }
        $extra = $post_obj->convert($post);
        return $extra;
    }
    /**
     * filter query_args
     *
     * @param void
     * @return void
     * @since 1.0
     * @package MicrojobEngine
     * @category void
     * @author JACK BUI
     */
    public function filter_query_args($query_args){
        $query = $_REQUEST['query'];
        $args = array();
        if( isset($query['post_parent']) ) {
            $args = array(
                'post_type' => 'mjob_extra',
                'post_status' => 'publish',
                'showposts' => ae_get_option('mjob_extra_numbers', 20),
                'post_parent'=> $query['post_parent']
            );
        }
        $query_args = wp_parse_args($args, $query_args);
        return $query_args;
    }
}
new mJobExtraAction();
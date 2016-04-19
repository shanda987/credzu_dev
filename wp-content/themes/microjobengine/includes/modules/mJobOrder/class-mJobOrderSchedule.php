<?php
class mJobOrderSchedule extends AE_Base{
    public static $instance;
    protected $cron_time;
    protected $cron_name;
    protected $cron_hook;
    protected $post_type;
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
     * @param integer $cron_time
     * @param string $cron_name
     * @param string $con_hook
     *
     */
    public  function __construct($cron_time = 3600, $cron_name = 'finish_mjob_order', $cron_hook = 'finish_mjob_order_hook', $post_type = 'mjob_order'){
        $this->init($cron_time, $cron_name, $cron_hook, $post_type);
        $this->initAjax();
    }
    /**
     * Init this class
     *
     * @param integer $cron_time
     * @param string $cron_name
     * @param string $con_hook
     * @return void
     * @since 1.0
     * @package MicrojobEngine
     * @category void
     * @author JACK BUI
     */
    public  function init($cron_time = 3600, $cron_name = 'finish_mjob_order', $cron_hook = 'finish_mjob_order_hook', $post_type = 'mjob_order'){
        $this->setCronTime($cron_time);
        $this->setCronName($cron_name);
        $this->setCronHook($cron_hook);
        $this->setPostType($post_type);
    }
    /**
     * init ajax
     *
     * @param void
     * @return void
     * @since 1.0
     * @package MicrojobEngine
     * @category void
     * @author JACK BUI
     */
    public  function initAjax(){
        $this->add_filter( 'cron_schedules',  'add_cron_time');
        $this->add_action('init', 'schedule_events', 100);
        $this->add_action( $this->cron_hook , 'cronAction' );

    }
    /**
     * Set $con_time
     *
     * @param integer $cron_time
     *
     */
    public function setCronTime($cron_time = 3600){
        if( null == $cron_time || empty($cron_time) ){
            $cron_time = 3600;
        }
        $this->cron_time = $cron_time;
    }

    /**
     * get cron time
     *
     * @param void
     * @return integer cron time
     *
     */
    public function getCronTime(){
        return $this->cron_time;
    }
    /**
     * set Cron Name
     *
     * @param string $cron_name
     * @return void
     * @since 1.0
     * @package MicrojobEngine
     * @category void
     * @author JACK BUI
     */
    public function setCronName( $cron_name = 'finish_mjob_order' ){
        if(  null == $cron_name || empty($cron_name) ){
            $cron_name = 'finish_mjob_order';
        }
        $this->cron_name = $cron_name;
    }
    /**
     * get cron name
     *
     * @param void
     * @return string $cron_name
     * @since 1.0
     * @package MicrojobEngine
     * @category void
     * @author JACK BUI
     */
    public function getCronName(){
        return $this->cron_name;
    }
    /**
     * set cron hook
     *
     * @param string $cron_hook
     * @return void
     * @since 1.0
     * @package MicrojobEngine
     * @category void
     * @author JACK BUI
     */
    public function setCronHook( $cron_hook = 'finish_mjob_order_hook'){
        if( null == $cron_hook || empty($cron_hook) ){
            $cron_hook = 'finish_mjob_order_hook';
        }
        $this->cron_hook = $cron_hook;
    }
    /**
     * get cron hook
     *
     * @param void
     * @return string $cron_hook
     * @since 1.0
     * @package MicrojobEngine
     * @category void
     * @author JACK BUI
     */
    public function getCronHook(){
        return $this->cron_hook;
    }
    /**
     * set post type
     *
     * @param string $post_type
     * @return void
     * @since 1.0
     * @package MicrojobEngine
     * @category void
     * @author JACK BUI
     */
    public function setPostType( $post_type = 'mjob_order'){
        if( null == $post_type || empty($post_type) ){
            $post_type = 'mjob_order';
        }
        $this->post_type = $post_type;
    }
    /**
     * get post type
     *
     * @param void
     * @return string $cron_hook
     * @since 1.0
     * @package MicrojobEngine
     * @category void
     * @author JACK BUI
     */
    public function getPostType(){
        return $this->post_type;
    }
    /**
     * Cron action
     *
     * @param void
     * @return void
     * @since 1.0
     * @package MicrojobEngine
     * @category void
     * @author JACK BUI
     */
    public function cronAction(){
        ob_start();
        global $wpdb, $ae_post_factory;
        $post_type = $this->post_type;
        $current = date('Y-m-d H:i:s', current_time('timestamp') );
        $duration = ae_get_option('mjob_order_finish_duration', 7);
//        $sql = "UPDATE {$wpdb->posts}  SET post_status = 'finished' WHERE ID IN (
//              SELECT DISTINCT ID FROM (SELECT * FROM {$wpdb->posts}) AS p
//				JOIN {$wpdb->postmeta} AS mt ON mt.post_id = p.ID AND mt.meta_key = 'order_delivery_day'
//				WHERE 	(p.post_type = '{$post_type}') 	AND
//						(p.post_status = 'delivery') AND
//						( DATEDIFF('{$current}', mt.meta_value ) >= {$duration} ) AND
//						(mt.meta_value != '' ) )";
//        $count = $wpdb->query($sql);
//        return $count;
        $sql = "SELECT * FROM {$wpdb->posts} AS p
				JOIN {$wpdb->postmeta} AS mt ON mt.post_id = p.ID AND mt.meta_key = 'order_delivery_day'
				WHERE 	(p.post_type = '{$post_type}') 	AND
						(p.post_status = 'delivery') AND
						( DATEDIFF('{$current}', mt.meta_value ) >= {$duration} ) AND
						(mt.meta_value != '' )";
        $posts = $wpdb->get_results($sql);
        // Update orders to finished status
        foreach($posts as $post) {
            $post_object = $ae_post_factory->get('mjob_order');
            $order = $post_object->convert($post);

            wp_update_post(array(
                'ID' => $order->ID,
                'post_status' => 'finished'
            ));

            // Update user balance
            AE_WalletAction()->transferWorkingToAvailable($order->mjob_author, $order->ID, $order->real_amount);
        }
    }
    /**
     * add cron time
     *
     * @param array $schedule
     * @return void
     * @since 1.0
     * @package MicrojobEngine
     * @category void
     * @author JACK BUI
     */
    public function add_cron_time( $schedule ){
        $schedule[$this->cron_name] = array(
            'interval'=> $this->cron_time,
            'display'=> apply_filters('mjob_cron_display', __('Finish microjob order in duration', ET_DOMAIN), $this->cron_name)
        );
        return $schedule;
    }
    /**
     * schedule events
     *
     * @param void
     * @return void
     * @since 1.0
     * @package MicrojobEngine
     * @category void
     * @author JACK BUI
     */
    public function schedule_events(){
       // wp_clear_scheduled_hook($this->cron_hook);
        if ( !wp_next_scheduled( $this->cron_hook ) ){
            strtotime( date( 'Y-m-d 00:00:00', strtotime('now')) );
            wp_schedule_event( time() , $this->cron_name, $this->cron_hook );
        }
    }
}
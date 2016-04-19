<?php
/**
 * AE message class
 */
class AE_AE_Message_Posttype extends mJobPost
{
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
     * The constructor
     *
     * @param string $post_type
     * @param array $taxs
     * @param array $meta_data
     * @param array $localize
     * @return void void
     *
     * @since 1.0
     * @author Tambh
     */
    public  function __construct($post_type = '', $taxs = array(), $meta_data = array(), $localize = array()){
        $this->post_type = 'ae_message';
        parent::__construct( $this->post_type, $taxs, $meta_data, $localize);
        $this->meta = array(
            'et_carousels',
            'from_user',
            'to_user',
            'last_sender',
            'send_date',
            'last_date',
            'is_conversation',
            'post_id',
            'post_name',
            'conversation_status',
            'archive_on_sender',
            'archive_on_receiver',
            'receiver_latest_reply',
            'sender_latest_reply',
            'latest_reply',
            'receiver_unread',
            'sender_unread',
            'type',
            'level'
        );
        $this->post_type_singular = 'Message';
        $this->post_type_regular = 'Messages';
    }
    /**
     * init function
     *
     * @param void
     * @return void
     * @since 1.0
     * @package MicrojobEngine
     * @category void
     * @author JACK BUI
     */
    public function init(){
        $args = array(
            'hierarchical' => false
        );
        $this->registerPosttype($args);
    }
}
$instance = AE_AE_Message_Posttype::getInstance();
$instance->init();
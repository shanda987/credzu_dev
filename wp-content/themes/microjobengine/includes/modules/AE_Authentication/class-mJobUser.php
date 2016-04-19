<?php
// Class mJobUser
class mJobUser extends AE_Users
{
    public static $instance;

    /**
     * Get instance method
     */
    public static function getInstance() {
        if(!self::$instance) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Constructor of class
     */
    public function __construct() {
        parent::__construct(array(
            'user_status',
        ));
    }

    /**
     * Add favorite service
     * @param int serviceID
     * @return array
     * @since 1.0
     * @package MicrojobEngine
     * @category mJob User
     * @author Tat Thien
     */
    public function addFavoriteService($userID, $serviceID) {

    }
}
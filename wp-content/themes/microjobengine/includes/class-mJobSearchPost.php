<?php
class mJobSearchPost extends AE_PostAction
{
    public static $instance;

    /**
     * get instance method
     */
    static function getInstance() {
        if (self::$instance == null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    function __construct() {
        $this->add_filter('pre_get_posts', 'mJobFilterSearchResult');
    }

    /**
     * Filter search results
     * @param $query
     * @return $query
     * @since 1.0
     * @package MicrojobEngine
     * @category Search
     * @author Tat Thien
     */
    public function mJobFilterSearchResult($query) {
        if(!is_admin() && $query->is_main_query()) {
            if($query->is_search()) {
                // Save keyword session
                global $wp_session;
                $keyword = get_query_var('s');
                setcookie('mjob_search_keyword', $keyword, time()+3600);

                $query->set('post_type', array('mjob_post'));
                $query->set('post_status', array('publish', 'pause', 'unpause'));
                if( is_super_admin() ){
                    $query->set('post_status', array('pending', 'publish', 'pause', 'unpause'));
                }
                // Filter by category
                if(isset($_GET['mjob_category']) && !empty($_GET['mjob_category'])) {
                    $categoryID = $_GET['mjob_category'];
                    $tax_query = array(
                        array(
                            'taxonomy' => 'mjob_category',
                            'field' => 'term_id',
                            'terms' => array($categoryID),
                        )
                    );
                    $query->tax_query->queries['relation'] = 'OR';
                    $query->tax_query->queries[] = $tax_query;
                    $query->query_vars['tax_query'] = $query->tax_query->queries;
                }
            }
        }
        return $query;
    }
}

$new_instance = mJobSearchPost::getInstance();
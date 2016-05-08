<?php
if( !function_exists('mJobLogo') ){
    function mJobLogo($option_name = '', $echo = true){
        $options = AE_Options::get_instance();
        // save this setting to theme options
        $site_logo = $options->$option_name;
        if (!empty($site_logo)) {
            $img = $site_logo['large'][0];
        }
        else{
            $img = TEMPLATEURL. '/assets/img/logo.png';
        }

        if($echo == false) {
            return '<img alt="' . $options->blogname . '" src="' . $img . '" />';
        } else {
            echo '<img alt="' . $options->blogname . '" src="' . $img . '" />';
        }

    }
}

if(!function_exists('mJobAvatar')) {
    /**
     * Show user avatar
     * @param int $userID
     * @param int $size             avatar size
     * @param array $params
     * @return string $avatar       img tag
     * @since 1.0
     * @package MicrojobEngine
     * @category File Functions
     * @author Tat Thien
     */
    function mJobAvatar($userID, $size = 150, $params = array('class'=> 'avatar' , 'title' => '', 'alt' => '')) {
        extract($params);
        $avatar = get_user_meta( $userID, 'et_avatar_url', true );
        if (!empty($avatar)){
            $avatar = '<img src="'.$avatar.'" class="'.$class.'" alt="'.$alt.'" />';
        } else if(ae_get_option('default_avatar')) {
            $avatar = mJobLogo('default_avatar', false);
        } else {
            $link 	= get_avatar( $userID, $size );
            preg_match( '/src=(\'|")(.+?)(\'|")/i', $link, $array );
            $sizes = get_intermediate_image_sizes();
            $avatar = array();
            foreach ($sizes as $size) {
                $avatar[$size] = $array[2];
            }
            $avatar = '<img src="'.$avatar['thumbnail'].'" class="'.$class.'" alt="'.$alt.'" />';
        }
        return $avatar;
    }
}

if(!function_exists('mJobShowUserHeader')) {
    /**
     * Show user section on main navigation
     * @param void
     * @return void
     * @since 1.0
     * @package Microjobengine
     * @category File Functions
     * @author Tat Thien
     */
    function mJobShowUserHeader() {
        global $current_user, $user_ID;
        $conversation_unread = mJobGetUnreadConversationCount();
        // Check empty current user
        if(!empty($current_user->ID)) {
            ?>
            <div class="list-message dropdown et-dropdown">
                <div class="dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown">
                    <span class="link-message">
                         <?php
                         if($conversation_unread > 0) {
                             echo '<span class="alert-sign"></span>';
                         }
                         ?>
                        <i class="fa fa-comment"></i>
                    </span>
                </div>
                <div class="dropdown-menu" aria-labelledby="dLabel">
                    <div class="list-message-box-header">
                        <span>
                            <?php
                                printf(__('%s New', ET_DOMAIN), $conversation_unread);
                            ?>
                        </span>
                        <a href="#" class="mark-as-read"><?php _e('Mark all as read', ET_DOMAIN); ?></a>
                    </div>

                    <ul class="list-message-box-body">
                        <?php
                        $default = mJobQueryConversationDefaultArgs();

                        $args = wp_parse_args(array(
                            'posts_per_page' => 5,
                            'orderby' => 'meta_value',
                            'meta_key' => 'latest_reply_timestamp',
                        ), $default);

                        $conversations_query = new WP_Query($args);
                        while($conversations_query->have_posts()) :
                            $conversations_query->the_post();
                            get_template_part('template/conversation-dropdown', 'item');

                        endwhile;
                        wp_reset_postdata();
                        ?>
                    </ul>

                    <div class="list-message-box-footer">
                        <a href="<?php echo et_get_page_link('my-list-messages'); ?>"><?php _e('View all', ET_DOMAIN); ?></a>
                    </div>
                </div>
            </div>

            <!--<div class="list-notification">
                <span class="link-notification"><i class="fa fa-bell"></i></span>
            </div>-->
            <?php
            $absolute_url = full_url( $_SERVER );
            if( is_page_template('page-post-service.php') ){
                $post_link = '#';
            }
            else {
                $post_link = et_get_page_link('post-service') . '?return_url=' . $absolute_url;
            }
           ?>
            <?php
             $user_role = ae_user_role($user_ID);
            if( is_super_admin() || $user_role == 'company' ): ?>
            <div class="link-post-services">
                <a href="<?php echo $post_link; ?>"><?php _e('Post a mJob', ET_DOMAIN); ?>
                    <div class="plus-circle"><i class="fa fa-plus"></i></div>
                </a>
            </div>
            <?php endif; ?>
            <div class="user-account">
                <div class="dropdown et-dropdown">
                    <div class="dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown">
                <span class="avatar">
                    <span class="display-avatar"><?php echo mJobAvatar($current_user->ID, 35); ?></span>
                    <span class="display-name"><?php echo $current_user->display_name; ?></span>
                </span>
                        <span><i class="fa fa-angle-right"></i></span>
                    </div>
                    <ul class="dropdown-menu et-dropdown-login" aria-labelledby="dLabel">
                        <li><a href="<?php echo et_get_page_link('dashboard'); ?>"><?php _e('Dashboard', ET_DOMAIN); ?></a></li>
                        <li><a href="<?php echo et_get_page_link("profile"); ?>"><?php _e('My profile', ET_DOMAIN); ?></a></li>
                        <li class="post-service-link"><a href="<?php echo et_get_page_link('post-service'); ?>"><?php _e('Post a mJob', ET_DOMAIN); ?>
                                <div class="plus-circle"><i class="fa fa-plus"></i></div>
                        </a></li>
                        <li class="get-message-link">
                            <a href="<?php echo et_get_page_link('my-list-messages'); ?>"><?php _e('Message', ET_DOMAIN); ?></a>
                        </li>
                        <li><a href="<?php echo wp_logout_url(home_url()); ?>"><?php _e('Sign out', ET_DOMAIN); ?></a></li>
                    </ul>
                </div>
            </div>
            <?php
        }
    }
}

if(!function_exists('mJobShowAuthenticationLink')) {
    /**
     * Show signup and signin link on main navigation
     * @param void
     * @return void
     * @since 1.0
     * @package Microjobengine
     * @category File Functions
     * @author Tat Thien
     */
    function mJobShowAuthenticationLink() {
        $sign_in_class = "signin-link open-signin-modal";
        $sign_up_class = "signup-link open-signup-modal";
        if(!is_page_template('page-sign-in.php') && !is_page_template('page-post-service.php') && !is_page_template('page-process-payment.php')) {
//            $sign_in_class = "signin-link focus-signin-form";
//            $sign_up_class = "signup-link focus-signup-form";
            ?>
            <div class="link-account">
                <ul>
                    <li><a href="" class="<?php echo $sign_in_class; ?>"><?php _e('Signin', ET_DOMAIN); ?></a></li>
                    <li><span><?php _e('or', ET_DOMAIN); ?></span></li>
                    <li><a href="" class="<?php echo $sign_up_class; ?>"><?php _e('Join us', ET_DOMAIN); ?></a></li>
                </ul>
            </div>
            <?php
        }
    }
}

if(!function_exists('mJobGetPrice')) {
    function mJobGetPrice($price, $open_sign = '(', $close_sign = ')') {
        $currency_sign = ae_currency_sign(false);

        $options = AE_Options::get_instance();
        $align = $options->currency['align'];

        if ($align) {
            return $open_sign . $currency_sign . $close_sign . $price;
        } else {
            return $price . $open_sign . $currency_sign . $close_sign;
        }
    }
}


if (!function_exists('et_get_customization')) {
    /**
     * @todo Tam thoi de ham nay de khong bi loi khi dung AE_Mailing
     * Get and return customization values for
     * @since 1.0
     */
    function et_get_customization() {
        $style = get_option('ae_theme_customization', true);
        $style = wp_parse_args($style, array(
            'background' => '#ffffff',
            'header' => '#2980B9',
            'heading' => '#37393a',
            'text' => '#7b7b7b',
            'action_1' => '#8E44AD',
            'action_2' => '#3783C4',
            'project_color' => '#3783C4',
            'profile_color' => '#3783C4',
            'footer' => '#F4F6F5',
            'footer_bottom' => '#fff',
            'font-heading-name' => 'Raleway,sans-serif',
            'font-heading' => 'Raleway',
            'font-heading-size' => '15px',
            'font-heading-style' => 'normal',
            'font-heading-weight' => 'normal',
            'font-text-name' => 'Raleway, sans-serif',
            'font-text' => 'Raleway',
            'font-text-size' => '15px',
            'font-text-style' => 'normal',
            'font-text-weight' => 'normal',
            'font-action' => 'Open Sans, Arial, Helvetica, sans-serif',
            'font-action-size' => '15px',
            'font-action-style' => 'normal',
            'font-action-weight' => 'normal',
            'layout' => 'content-sidebar'
        ));
        return $style;
    }
}
if( !function_exists('mJobPriceFormat')) {
    /**
     * Price format
     *
     * @param float $amount
     * @param string $style
     * @return string
     * @since 1.0
     * @package MicrojobEngine
     * @category void
     * @author JACK BUI
     */
    function mJobPriceFormat($amount, $style = 'sup')
    {
        $currency = ae_get_option('currency', array(
            'align' => 'left',
            'code' => 'USD',
            'icon' => '$'
        ));

        $align = $currency['align'];

        // dafault = 0 == right;

        $currency = $currency['icon'];
        $price_format = get_theme_mod('decimal_point', 1);
        $format = '%1$s';
        switch ($style) {
            case 'sup':
                $format = '<sup>%s</sup>';
                break;

            case 'sub':
                $format = '<sub>%s</sub>';
                break;

            default:
                $format = '%s';
                break;
        }
        $number_format = ae_get_option('number_format');
        $decimal = (isset($number_format['et_decimal'])) ? $number_format['et_decimal'] : get_theme_mod('et_decimal', 2);
        $decimal_point = (isset($number_format['dec_point']) && $number_format['dec_point']) ? $number_format['dec_point'] : get_theme_mod('et_decimal_point', '.');
        $thousand_sep = (isset($number_format['thousand_sep']) && $number_format['thousand_sep']) ? $number_format['thousand_sep'] : get_theme_mod('et_thousand_sep', ',');

        if ($align != "0") {
            $format = $format . '%s';
            return sprintf($format, $currency, number_format((double)$amount, $decimal, $decimal_point, $thousand_sep));
        } else {
            $format = '%s' . $format;
            return sprintf($format, number_format((double)$amount, $decimal, $decimal_point, $thousand_sep), $currency);
        }
    }
}
if( !function_exists('mJobNumberFormat')) {
    /**
     * number format
     *
     * @param float $amount
     * @param boolean $echo
     * @return void
     * @since 1.0
     * @package MicrojobEngine
     * @category void
     * @author JACK BUI
     */
    function mJobNumberFormat($amount, $echo = true)
    {
        $number_format = ae_get_option('number_format');
        $decimal = (isset($number_format['et_decimal'])) ? $number_format['et_decimal'] : get_theme_mod('et_decimal', 2);
        $decimal_point = (isset($number_format['dec_point']) && $number_format['dec_point']) ? $number_format['dec_point'] : get_theme_mod('et_decimal_point', '.');
        $thousand_sep = (isset($number_format['thousand_sep']) && $number_format['thousand_sep']) ? $number_format['thousand_sep'] : get_theme_mod('et_thousand_sep', ',');
        if ($echo) {
            echo number_format((double)$amount, $decimal, $decimal_point, $thousand_sep);
        } else {
            return number_format((double)$amount, $decimal, $decimal_point, $thousand_sep);
        }
    }
}

if(!function_exists('mJobShowFilterCategories')) {
    /**
     * Show categories filter on search result
     * @param array $taxonomies
     * @return void
     * @since 1.0
     * @package MicrojobEngine
     * @category File Functions
     * @author Tat Thien
     */
    function mJobShowFilterCategories($taxonomy = 'category', $args = array(), $current = "", $custom_filter = true) {
        $terms = get_terms($taxonomy, $args);
        $search_item = get_query_var('s');
        ?>
        <div class="dropdown">
            <button class="button-dropdown-menu" id="dLabel" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                Categories
                <span class="caret"></span>
            </button>
            <ul id="accordion" class="accordion <?php echo ($custom_filter) ? 'custom-filter-query' : ''?> dropdown-menu" aria-labelledby="dLabel">
                <?php
                    if(!is_category() && !is_singular('post')) {
                        if(is_search()) {
                            // render link all
                            ?>
                            <li>
                                <div class="link">
                                    <a href="<?php echo get_site_url() . "?s=$search_item&$taxonomy=0"; ?>" data-name="<?php echo $taxonomy; ?>" data-value="0" class="hvr-wobble-horizontal">
                                        <?php _e('All', ET_DOMAIN); ?>
                                    </a>
                                </div>
                            </li>
                            <?php
                        } else {
                            ?>
                            <li>
                                <div class="link">
                                    <a href="<?php echo get_post_type_archive_link('mjob_post');  ?>" data-name="<?php echo $taxonomy; ?>" data-value="0" class="hvr-wobble-horizontal">
                                        <?php _e('All', ET_DOMAIN); ?>
                                    </a>
                                </div>
                            </li>
                            <?php
                        }
                    }
                    foreach($terms as $term) {
                        // Get term link
                        if(is_search()) {
                            $term_link = get_site_url() . "?s=$search_item&$taxonomy=$term->term_id";
                        } else {
                            $term_link = get_term_link($term);
                        }

                        $current_term = get_term($current);
                        ?>
                        <li class="<?php echo (!is_wp_error($current_term) && $current_term->parent == $term->term_id) ? 'open active' : '';  ?>">
                            <?php
                                // Get child term
                                $child_terms = get_terms($taxonomy, array('parent' => $term->term_id));
                            ?>
                            <div class="link">
                                <a href="<?php echo $term_link; ?>" data-name="<?php echo $taxonomy; ?>" data-value="<?php echo $term->term_id ?>" class="<?php echo ($current == $term->term_id) ? 'active' : ''; ?> hvr-wobble-horizontal"><?php echo $term->name; ?>


                                </a>
                                <?php
                                if(!empty($child_terms)) :
                                    echo '<i class="fa fa-chevron-right"></i>';
                                endif;
                                ?>
                            </div>

                            <?php if(!empty($child_terms)) { ?>
                            <ul class="submenu">
                                <?php
                                foreach($child_terms as $child) {
                                    // Get term link
                                    if(is_search()) {
                                        $term_link = get_site_url() . "?s=$search_item&$taxonomy=$child->term_id";
                                    } else {
                                        $term_link = get_term_link($child);
                                    }

                                    ?>
                                    <li><a href="<?php echo $term_link; ?>" data-name="<?php echo $taxonomy; ?>" data-value="<?php echo $child->term_id; ?>" class="<?php echo ($current == $child->term_id) ? 'active' : ''; ?> hvr-wobble-horizontal"><?php echo $child->name; ?></a></li>
                                    <?php
                                }
                                ?>
                            </ul>
                            <?php } ?>
                        </li>
                        <?php
                    }
                ?>
            </ul>
        </div>
        <?php
    }
}

if(!function_exists('mJobShowFilterTags')) {
    /**
     * Show tags filter on search result
     * @param array $taxonomies
     * @return void
     * @since 1.0
     * @package MicrojobEngine
     * @category File Functions
     * @author Tat Thien
     */
    function mJobShowFilterTags($taxonomies, $args = array('hide_empty' => true), $current_tag = "", $custom_filter = true) {
        $defaults = array(
            'hide_empty' => true
        );
        $args = wp_parse_args($args, $defaults);
        $terms = get_terms($taxonomies, $args);
        echo '<div class="tags">';
        if($custom_filter) {
            echo '<select name="skill" class="multi-tax-item" multiple data-placeholder="' . __('Filter by tag', ET_DOMAIN) . '">';
            foreach ($terms as $term) {
                if($current_tag == $term->slug) {
                    ?>
                    <option value="<?php echo $term->term_id ?>" selected><?php echo $term->name; ?></option>
                    <?php
                } else {
                    ?>
                    <option value="<?php echo $term->term_id ?>"><?php echo $term->name; ?></option>
                    <?php
                }
            }
            echo '</select>';
        } else {
            foreach ($terms as $term) {
                ?>
                <a href="<?php echo get_term_link($term); ?>"><?php echo $term->name; ?></a>
                <?php
            }
        }
        echo "</div>";
    }
}

if( !function_exists('list_tax_of_mjob' )) {
    /**
     * display html of list skill or category of project
     * @param  int $id project id
     * @param  string $title - title apperance in h3
     * @param  string $slug taxonomy slug
     * @return display list taxonomy of project.
     */
    function list_tax_of_mjob($id, $title = '', $taxonomy = 'mjob_category', $class = '')
    {
        $class = 'list-categories';
        if ($class = 'skill') $class = 'list-skill';
        $terms = get_the_terms($id, $taxonomy); ?>
        <h3 class="title-content"><?php
            printf($title); ?></h3>
        <?php
        if ($terms && !is_wp_error($terms)): ?>
            <div class="list-require-skill-project list-taxonomires list-<?php
            echo $taxonomy; ?>">
                <?php
                the_taxonomy_list($taxonomy, '<span class="skill-name-profile">', '</span>', $class); ?>
            </div>
            <?php
        endif;
    }
}
if( !function_exists('mJobExtraAction') ) {
    /**
     * get instance of class mJobExtraAction
     *
     * @param void
     * @return object mJobExtraAction
     * @since 1.0
     * @package MicrojobEngine
     * @category void
     * @author JACK BUI
     */
    function mJobExtraAction()
    {
        return mJobExtraAction::getInstance();
    }
}
if(!function_exists('mJobShowSearchForm')) {
    /**
     * Show search form
     * @param void
     * @return void
     * @since 1.0
     * @package MicrojobEngine
     * @category File Functions
     * @author Tat Thien
     */
    function mJobShowSearchForm() {
        ?>
        <form action="<?php echo get_site_url(); ?>" class="et-form">
            <?php
            if(isset($_COOKIE['mjob_search_keyword'])) {
                $keyword = $_COOKIE['mjob_search_keyword'];
            } else {
                $keyword = '';
            }
            ?>
            <span class="icon-search"><i class="fa fa-search"></i></span>
            <?php if(is_singular('mjob_post')) : ?>
                <input type="text" name="s" id="input-search" value="<?php echo $keyword; ?>">
            <?php elseif(is_search()) : ?>
                <input type="text" name="s" id="input-search" value="<?php echo get_query_var('s'); ?>">
            <?php else: ?>
                <input type="text" name="s" id="input-search">
            <?php endif; ?>
        </form>
        <?php
    }
}
if( !function_exists('mJobAction') ) {
    /**
     * get instance of class mJobExtraAction
     *
     * @param void
     * @return object mJobExtraAction
     * @since 1.0
     * @package MicrojobEngine
     * @category File Functions
     * @author JACK BUI
     */
    function mJobAction()
    {
        return mJobAction::getInstance();
    }
}
if( !function_exists('mJobOrderAction') ) {
    /**
     * get instance of class mJobOrderAction
     *
     * @param void
     * @return object mJobExtraAction
     * @since 1.0
     * @package MicrojobEngine
     * @category File Functions
     * @author JACK BUI
     */
    function mJobOrderAction()
    {
        return mJobOrderAction::getInstance();
    }
}
/**
 * get currency of this site
 *
 * @param void
 * @return array $currency
 * @since 1.0
 * @package MicrojobEngine
 * @category File Functions
 * @author JACK BUI
 */
function mjob_get_currency(){
    $currency = ae_get_option('currency', array(
        'align' => 'left',
        'code' => 'USD',
        'icon' => '$'
    ));
    return $currency;
}
/**
 * get temp user
 *
 * @param void
 * @return integer $temp_user
 * @since 1.0
 * @package MicrojobEngine
 * @category File Functions
 * @author JACK BUI
 */
function mjob_get_temp_user_id(){
    return ae_get_option('mjob_temp_user_id',1);
}

/**
 * Check user is active or not
 * @param int $user_id
 * @return boolean
 * @since 1.0
 * @package MicrojobEngine
 * @category File Functions
 * @author Tat Thien
 */
if(!function_exists('mJobUserActive')) {
    function mJobUserActivate($user_id) {
        return AE_Users::is_activate($user_id);
    }
}

/**
 * Check page template
 * @param string $slug
 * @return boolean
 * @since 1.0
 * @package MicrojobEngine
 * @category File Functions
 * @author Tat Thien
 */
function mJobIsPageTemplate($slug) {
    $pageTemplate = get_page_template();
    $pageArray = explode("/", $pageTemplate);
    $pageTemplate = end($pageArray);
    if($pageTemplate == $slug) {
        return true;
    } else {
        return false;
    }
}

/**
 * Render contact link
 * @param void
 * @return void
 * @since 1.0
 * @package MicrojobEngine
 * @category File Functions
 * @author Tat Thien
 */
function mJobShowContactLink($to_user) {
    global $user_ID;

    if(mJobIsHasConversation($user_ID, $to_user)) {
        ?>
        <li><a href="<?php echo mJobGetConversationLink($user_ID, $to_user); ?>" class="contact-link"><?php _e('Contact me', ET_DOMAIN); ?><i class="fa fa-comment"></i></a></li>
        <?php
    } else if($to_user != $user_ID) {
        ?>
        <li><a href="" class="contact-link do-contact" data-touser= "<?php echo $to_user; ?>"><?php _e('Contact me', ET_DOMAIN); ?><i class="fa fa-comment"></i></a></li>
        <?php
    }

}
/**
 * price after commission
 *
 * @param float $price
 * @return float $price after subtract commission
 * @since 1.0
 * @package MicrojobEngine
 * @category void
 * @author JACK BUI
 */
 function mJobRealPrice($price){
     $commission = ae_get_option('order_commissions', array('order_commission'=> 10));
     $commission = $commission['order_commission'];
     if( $commission > 0 ){
         $price = $price - $commission*$price/100;
     }
     return $price;
 }
/**
 * Count all microjob
 *
 * @param void
 * @return void
 * @since 1.0
 * @package MicrojobEngine
 * @category void
 * @author JACK BUI
 */
 function mJobCountPost(){
     global $wp_query;
     $count_posts = 0;
     $args = array(
         'post_type'=> 'mjob_post',
         'post_status'=>array('publish', 'unpause', 'pause'),
     );
     $post = query_posts($args);
     $count_posts = $wp_query->found_posts;
     wp_reset_query();
     return $count_posts;
 }

if(!function_exists('mJobCountOrder')) {
    /**
     * Get mjob order count
     * @param int $mjob_post_id
     * @return int $count_posts
     * @since 1.0
     * @package MicrojobEngine
     * @category File Functions
     * @author Tat Thien
     */
    function mJobCountOrder($mjob_post_id = '') {
        global $wp_query;
        $count_posts = 0;
        $args = array(
            'post_type' => 'mjob_order',
            'post_status' => 'any',
            'posts_per_page' => -1,
        );

        if(!empty($mjob_post_id)) {
            $args = wp_parse_args(array('post_parent' => $mjob_post_id), $args);
        }

        query_posts($args);
        $count_posts = $wp_query->found_posts;
        wp_reset_query();
        return $count_posts;
    }
}

if(!function_exists('mJobGetTotalReview')) {
    /**
     * Get review count
     * @param int $mjob_post_id
     * @return int $count
     * @since 1.0
     * @package MicrojobEngine
     * @category File Functions
     * @author Tat Thien
     */
    function mJobCountReview($mjob_post_id = '') {
        $count = 0;
        $args = array(
            'status' => 'approve',
            'comment_type' => 'mjob_review'
        );

        if(!empty($mjob_post_id)) {
            $args = wp_parse_args(array('post_id' => $mjob_post_id), $args);
        }

        $comments = get_comments($args);

        $count = count($comments);
        return $count;
    }
}

if(!function_exists('mJobUserCountReview')) {
    function mJobUserCountReview($user_id) {
        $posts = get_posts(array(
            'post_type' => 'mjob_post',
            'post_status' => array(
                'publish',
                'pause',
                'unpause'
            ),
            'meta_query' => array(
                array(
                    'key' => 'rating_score',
                    'value' => 0,
                    'compare' => '>'
                )
            ),
            'posts_per_page' => -1,
            'author' => $user_id
        ));

        $count = 0;
        foreach($posts as $post) {
            $rating_score = get_post_meta($post->ID, 'rating_score', true);
            $count += $rating_score;
        }

        if(count($posts) != 0) {
            return $count / count($posts);
        } else {
            return 0;
        }
    }
}

if(!function_exists('mJobConvertNumber')) {
    /**
     * Convert long number to K, M. Eg: 1000 -> 1K, 1000.000 -> 1M
     * @param type $number
     * @return type
     * @since 1.0
     * @package MicrojobEngine
     * @category File Functions
     * @author Tat Thien
     */
    function mJobConvertNumber($number) {
        $number_fomart = 0;

        if(!empty($number)) {
            if($number < 1000) { // Anything less than 1 thousand
                $number_fomart = number_format($number);
            } else if($number < 1000000) { // Anything less than 1 milion
                $number_fomart = number_format($number / 1000) . 'K';
            } else if($number < 1000000000) { // Anything less than 1 billion
                $number_fomart = number_format($number / 1000000) . 'M';
            } else {
                $number_fomart = number_format($number / 1000000000) . 'B';
            }
        }

        return $number_fomart;
    }
}
function url_origin( $s, $use_forwarded_host = false )
{
    $ssl      = ( ! empty( $s['HTTPS'] ) && $s['HTTPS'] == 'on' );
    $sp       = strtolower( $s['SERVER_PROTOCOL'] );
    $protocol = substr( $sp, 0, strpos( $sp, '/' ) ) . ( ( $ssl ) ? 's' : '' );
    $port     = $s['SERVER_PORT'];
    $port     = ( ( ! $ssl && $port=='80' ) || ( $ssl && $port=='443' ) ) ? '' : ':'.$port;
    $host     = ( $use_forwarded_host && isset( $s['HTTP_X_FORWARDED_HOST'] ) ) ? $s['HTTP_X_FORWARDED_HOST'] : ( isset( $s['HTTP_HOST'] ) ? $s['HTTP_HOST'] : null );
    $host     = isset( $host ) ? $host : $s['SERVER_NAME'] . $port;
    return $protocol . '://' . $host;
}

function full_url( $s, $use_forwarded_host = false )
{
    return urlencode(url_origin( $s, $use_forwarded_host ) . $s['REQUEST_URI']);
}
if( !function_exists('mJobProgressBar') ){
    /**
     * progress bar
     *
     * @param integer $type
     * @param string $echo
     * @return void
     * @since 1.0
     * @package MicrojobEngine
     * @category void
     * @author JACK BUI
     */
    function mJobProgressBar($type = 3, $echo = ''){
        ob_start();
        if( $type == 4):
        ?>
        <ul class="step-4-col">
            <li class="post-service-step-1 active" data-id="step1"><span class="link-step1"><?php _e('1', ET_DOMAIN) ; ?></span></li>
            <li class="post-service-step-2" data-id="step2"><span class="link-step2"><?php _e('2', ET_DOMAIN); ?></span></li>
            <li class="post-service-step-3" data-id="step-post"><span class="link-step3"><?php _e('3', ET_DOMAIN) ;?></span></li>
            <li class="post-service-step-4" data-id="step4"><span class="link-step4"><?php _e('4', ET_DOMAIN) ;?></span></li>
            <div class="progress-bar-success"></div>
        </ul>
<?php   else: ?>
            <ul class="step-3-col">
                <li class="post-service-step-1 active" data-id="step1"><span class="link-step1"><?php _e('1', ET_DOMAIN) ; ?></span></li>
                <li class="post-service-step-2" data-id="step-post"><span class="link-step2"><?php _e('2', ET_DOMAIN); ?></span></li>
                <li class="post-service-step-3" data-id="step4"><span class="link-step3"><?php _e('3', ET_DOMAIN) ;?></span></li>
                <div class="progress-bar-success"></div>
            </ul>
<?php        endif;
        $html = ob_get_clean();
        if( $echo ):
            echo $html;
        else:
            return $html;
        endif;
        }
}

if(!function_exists('mJobGetDateRange')) {
    /**
     * Get date range
     * @param $first      start date
     * @param $end        end date
     * @param @step
     * @format            date format
     * @return array $dates
     * @since 1.0
     * @package MicrojobEngine
     * @category void
     * @author Tat Thien
     */
    function mJopGetDateRange($first, $last, $step = '+1 day', $format = 'Y/m/d') {
        date_default_timezone_get(get_option('timezone_string'));

        $dates = array();
        $current = strtotime($first);
        $last = strtotime($last);
        while($current <= $last) {
            $dates[] = date($format, $current);
            $current = strtotime($step, $current);
        }

        return $dates;
    }
}

if(!function_exists('mJobGetOrderByDate')) {
    /**
     * Get order by date
     * @param $date
     * @return $count_orders
     * @since 1.0
     * @package MicrojobEngine
     * @category void
     * @author Tat Thien
     */
    function mJobGetOrderByDate($date) {
        global $wpdb, $user_ID;
        $sql = "SELECT *
                FROM $wpdb->posts AS p
                INNER JOIN $wpdb->postmeta AS pm ON pm.post_id = p.ID
                WHERE p.post_type = 'mjob_order'
                  AND p.post_status != 'pending'
                  AND p.post_date LIKE '$date%'
                  AND p.post_author != $user_ID
                  AND pm.meta_key = 'seller_id'
                  AND pm.meta_value = $user_ID";
        $results = $wpdb->get_results($sql);
        $count_orders = count($results);
        return $count_orders;
    }
}

if(!function_exists('mJobGetOrderChart')) {
    /**
     * Get order data for chat
     * @param void
     * @return array $orders
     * @since 1.0
     * @package MicrojobEngine
     * @category void
     * @author Tat Thien
     */
    function mJobGetOrderChart() {
        $date_format = 'Y-m-d';
        $current_date = date($date_format, time());
        $last_week = date($date_format, strtotime('-1 week'));
        $dates = mJopGetDateRange($last_week, $current_date, '+1 day', $date_format);
        $orders = array();
        foreach($dates as $date) {
            $orders[] = mJobGetOrderByDate($date);
        }

        return $orders;
    }
}

if(!function_exists('mJobAddFacebookSharingImage')) {
    function mJobAddFacebookSharingImage() {
        if(is_singular('mjob_post')) {
            global $post;
            $attachment_id = get_post_thumbnail_id($post->ID);
            $feature_image = wp_get_attachment_image_src($attachment_id, 'medium');
            $feature_image_url = $feature_image['0'];
            echo '<meta property="og:image" content="'. $feature_image_url .'"/>';
        }
    }

    add_action('wp_head', 'mJobAddFacebookSharingImage');
}
if( !function_exists('mJobProfileAction') ){
    /**
     * return profile class
     *
     * @param void
     * @return void
     * @since 1.0
     * @package MicrojobEngine
     * @category void
     * @author JACK BUI
     */
    function mJobProfileAction(){
        return mJobProfileAction::getInstance();
    }
}
if( !function_exists('agreementAction') ){
    /**
     * return profile class
     *
     * @param void
     * @return void
     * @since 1.0
     * @package MicrojobEngine
     * @category void
     * @author JACK BUI
     */
    function agreementAction(){
        return agreementAction::getInstance();
    }
}

if( !function_exists('mJobPostAction') ){
    function mJobPostAction()
    {
        return mJobPostAction::getInstance();
    }
}
if( !function_exists('AE_Pdf_Creator') ){
    /**
     * get instance of AE_Pdf_Creator class
     *
     * @param void
     * @return object instance of class AE_Pdf_Creator
     * @since 1.0
     * @package MicrojobEngine
     * @category void
     * @author JACK BUI
     */
    function AE_Pdf_Creator(){
        $instance = AE_Pdf_Creator::getInstance();
        return $instance;
    }
}
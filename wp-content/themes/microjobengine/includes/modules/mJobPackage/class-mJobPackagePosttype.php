<?php
class mJobPackagePosttype extends mJobPost {
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
	 * The constructor of this class
	 */
	public function __construct($post_type = '', $taxs = array(), $meta_data = array(), $localize = array()) {
		$this->post_type = 'pack';
		parent::__construct($this->post_type, $taxs, $meta_data, $localize);
		$this->post_type_singular = 'Pack';
		$this->post_type_regular = 'Packs';
		$this->meta = array('et_price');
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
		$this->registerPosttype();
	}
}

// Call class
$new_instance = mJobPackagePosttype::getInstance();
$new_instance->init();

// New AE_Package
$package = new AE_Package('pack',
	array(
		'sku',
		'et_price',
		'et_number_posts',
		'et_duration',
		'et_featured',
		'et_permanent'
	),
	array(
		'backend_text' => array(
			'text' => __('%s for %d day', ET_DOMAIN),
			'data' => array(
				'et_price',
				'et_number_posts'
			)
		)
	)
);
$pack_action = new AE_PackAction($package);

global $ae_post_factory;
$ae_post_factory->set('pack', $package);

/**
 * Filter backend text
 * @param object $result
 * @return object $result
 * @since 1.0
 * @package MicrojobEngine
 * @category Authentication
 * @author Tat Thien
 */
if(!function_exists('mJobFilterPack')) {
	function mJobFilterPack($result) {
		$price = mJobGetPrice($result->et_price);
		$result->package_item_text = sprintf(__('%s for %s day', ET_DOMAIN), $price, $result->et_duration);
		if((int)$result->et_duration >= 0) {
			$result->package_item_text = sprintf(__('%s for %s days', ET_DOMAIN), $price, $result->et_duration);
		}
		if(isset($result->et_permanent) && $result->et_permanent == "1") {
			$result->package_item_text = sprintf(__('%s for permanent sell', ET_DOMAIN), $price);
		}
		return $result;
	}

	add_filter('ae_convert_pack', 'mJobFilterPack');
	add_filter('ae_convert_after_insert_pack', 'mJobFilterPack');
}
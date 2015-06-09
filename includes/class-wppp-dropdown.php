<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Class WPPP_Dropdown.
 *
 * Products per page dropdown class.
 *
 * @class       WPPP_Dropdown
 * @version     1.1.0
 * @author      Jeroen Sormani
 */
class WPPP_Dropdown {


	/**
	 * Construct.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		_deprecated_function( array( $this, __FUNCTION__ ), '1.2.0', 'Woocommerce_Products_Per_Page()->front_end->products_per_page_dropdown()' );
		return $this->front_end->products_per_page_dropdown();
	}


	/**
	 * Products per page dropdown.
	 *
	 * The actual dropdown for to select the number of products per page.
	 *
	 * @since 1.0.0
	 * @deprecated 1.1.0 Use wppp_dropdown() instead (rename).
	 */
	public function wppp_create_object() {
		_deprecated_function( array( $this, __FUNCTION__ ), '1.2.0', 'Woocommerce_Products_Per_Page()->front_end->products_per_page_dropdown()' );
		return $this->front_end->products_per_page_dropdown();
	}


	/**
	 * Products per page dropdown.
	 *
	 * The actual dropdown for to select the number of products per page.
	 *
	 * @since 1.1.0
	 *
	 * @global object $wp_query.
	 */
	public function wppp_dropdown() {
		_deprecated_function( array( $this, __FUNCTION__ ), '1.2.0', 'Woocommerce_Products_Per_Page()->front_end->products_per_page_dropdown()' );
		return $this->front_end->products_per_page_dropdown();
	}


}

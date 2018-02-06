<?php
/*
 * Plugin Name: Woocommerce Products Per Page
 * Plugin URI: https://wordpress.org/plugins/woocommerce-products-per-page/
 * Description: Integrate a 'products per page' dropdown on your WooCommerce website! Set-up in <strong>seconds</strong>!
 * Version: 1.2.6
 * Author: Jeroen Sormani
 * Author URI: http://jeroensormani.com
 *
 * WC requires at least: 3.1.0
 * WC tested up to:      3.3.0

 * Copyright Jeroen Sormani
 *
 *     This file is part of Woocommerce Products Per Page,
 *     a plugin for WordPress.
 *
 *     Woocommerce Products Per Page is free software:
 *     You can redistribute it and/or modify it under the terms of the
 *     GNU General Public License as published by the Free Software
 *     Foundation, either version 3 of the License, or (at your option)
 *     any later version.
 *
 *     Woocommerce Products Per Page is distributed in the hope that
 *     it will be useful, but WITHOUT ANY WARRANTY; without even the
 *     implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR
 *     PURPOSE. See the GNU General Public License for more details.
 *
 *     You should have received a copy of the GNU General Public License
 *     along with WordPress. If not, see <http://www.gnu.org/licenses/>.
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Class Woocommerce_Products_Per_Page
 *
 * Main WPPP class, initialized the plugin
 *
 * @class       Woocommerce_Products_Per_Page
 * @version     1.1.0
 * @author      Jeroen Sormani
 */
class Woocommerce_Products_Per_Page {


	/**
	 * Plugin version.
	 *
	 * @since 1.2.0
	 * @var string $version Plugin version number.
	 */
	public $version = '1.2.6';


	/**
	 * Instance of Woocommerce_Products_Per_Page.
	 *
	 * @since 1.1.3
	 * @access private
	 * @var object $instance The instance of Woocommerce_Products_Per_Page.
	 */
	private static $instance;


	/**
	 * Construct.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		// Check if WooCommerce is active
		require_once( ABSPATH . '/wp-admin/includes/plugin.php' );
		if ( ! is_plugin_active( 'woocommerce/woocommerce.php' ) && ! function_exists( 'WC' ) ) {
			return;
		}

		$this->init();

	}


	/**
	 * Instance.
	 *
	 * An global instance of the class. Used to retrieve the instance
	 * to use on other files/plugins/themes.
	 *
	 * @since 1.0.3
	 * @return object Instance of the class.
	 */
	public static function instance() {

		if ( is_null( self::$instance ) ) :
			self::$instance = new self();
		endif;

		return self::$instance;

	}


	/**
	 * Init.
	 *
	 * Initialize plugin parts.
	 *
	 * @since 1.0.0
	 */
	public function init() {

		if ( is_admin() ) :

			/**
			 * Settings
			 */
			require_once plugin_dir_path( __FILE__ ) . 'includes/admin/class-wppp-admin-settings.php';
			$this->admin_settings = new WPPP_Admin_Settings();

		endif;

		if ( ! is_admin() || ( defined( 'DOING_AJAX' ) && DOING_AJAX ) ) :

			/**
			 * Front end
			 */
			require_once plugin_dir_path( __FILE__ ) . 'includes/class-wppp-front-end.php';
			$this->front_end = new WPPP_Front_End();

		endif;

		// Plugin update function
		add_action( 'admin_init', array( $this, 'plugin_update' ) );

		// Load textdomain
		$this->load_textdomain();

		global $pagenow;
		if ( 'plugins.php' == $pagenow ) :
			// Plugins page
			add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), array( $this, 'add_plugin_action_links' ), 10, 2 );
		endif;

	}


	/**
	 * Textdomain.
	 *
	 * Load the textdomain based on WP language.
	 *
	 * @since 1.2.0
	 */
	public function load_textdomain() {

		$locale = apply_filters( 'plugin_locale', get_locale(), 'woocommerce-products-per-page' );

		// Load textdomain
		load_textdomain( 'woocommerce-products-per-page', WP_LANG_DIR . '/woocommerce-products-per-page/woocommerce-products-per-page-' . $locale . '.mo' );
		load_plugin_textdomain( 'woocommerce-products-per-page', false, basename( dirname( __FILE__ ) ) . '/languages' );

	}


	/**
	 * Update plugin.
	 *
	 * Plugin update function, update data when required.
	 *
	 * @since 1.2.0
	 */
	public function plugin_update() {

		if ( version_compare( get_option( 'wppp_version', '0' ), $this->version, '<' ) ) :

			// Updating to 1.2.0
			if ( version_compare( get_option( 'wppp_version', '0' ), '1.2.0', '<' ) ) :
				$dropdown_options_default =
					( apply_filters( 'loop_shop_columns', 4 ) * 3 ) . ' ' .
					( apply_filters( 'loop_shop_columns', 4 ) * 6 ) . ' ' .
					( apply_filters( 'loop_shop_columns', 4 ) * 9 );
				$settings = get_option( 'wppp_settings', array() );
				update_option( 'wppp_dropdown_location', isset( $settings['location'] ) ? $settings['location'] : 'topbottom'  );
				update_option( 'wppp_dropdown_options', isset( $settings['productsPerPage'] ) ? $settings['productsPerPage'] : $dropdown_options_default  );
				update_option( 'wppp_default_ppp', isset( $settings['default_ppp'] ) ? $settings['default_ppp'] : '12'  );
				update_option( 'wppp_shop_columns', isset( $settings['shop_columns'] ) ? $settings['shop_columns'] : '4'  );
				update_option( 'wppp_return_to_first', isset( $settings['behaviour'] ) && '1' == $settings['behaviour'] ? 'yes' : 'no'  );
				update_option( 'wppp_method', isset( $settings['method'] ) ? $settings['method'] : 'post'  );
			endif;

			// Updating to 1.3.0 - for the future, delete the old settings.
			if ( version_compare( get_option( 'wppp_version', '0' ), '1.3.0', '<' ) ) :
				// delete_option( 'wppp_settings' );
			endif;

			update_option( 'wppp_version', $this->version );
		endif;

	}


	/**
	 * Plugin action links.
	 *
	 * Add links to the plugins.php page below the plugin name
	 * and besides the 'activate', 'edit', 'delete' action links.
	 *
	 * @since 1.2.2
	 *
	 * @param	array	$links	List of existing links.
	 * @param	string	$file	Name of the current plugin being looped.
	 * @return	array			List of modified links.
	 */
	public function add_plugin_action_links( $links, $file ) {

		if ( $file == plugin_basename( __FILE__ ) ) :
			$links = array_merge( array(
				'<a href="' . esc_url( admin_url( 'admin.php?page=wc-settings&tab=products&section=display#s2id_wppp_dropdown_location' ) ) . '">' . __( 'Settings', 'woocommerce-products-per-page' ) . '</a>'
			), $links );
		endif;

		return $links;

	}


	/**
	 * Initialize admin settings page.
	 *
	 * @since 1.1.0
	 */
	public function wppp_settings_page_menu() {
		_deprecated_function( array( $this, __FUNCTION__ ), '1.2.0' );
	}


	/**
	 * Initialize settings.
	 *
	 * Settings will be put in an class parameter
	 *
	 * @since 1.1.0
	 *
	 * @return int number of columns.
	 */
	public function wppp_init_settings() {
		_deprecated_function( array( $this, __FUNCTION__ ), '1.2.0' );
	}


	/**
	 * Default settings
	 *
	 * Default settings will be used when no settings are available
	 *
	 * @since 1.0.0
	 *
	 * @return array default settings.
	 */
	public function wppp_settings_defaults() {
		_deprecated_function( array( $this, __FUNCTION__ ), '1.2.0' );
	}


	/**
	 * Shop columns.
	 *
	 * Set number of columns (products per row).
	 *
	 * @since 1.0.0
	 *
	 * @return int Number of columns.
	 */
	public function wppp_loop_shop_columns( $columns ) {
		_deprecated_function( array( $this, __FUNCTION__ ), '1.2.0', 'Woocommerce_Products_Per_Page()->front_end->loop_shop_columns()' );
		return $this->front_end->loop_shop_columns();
	}


	/**
	 * Per page hook.
	 *
	 * Return the number of products per page to the hook
	 *
	 * @since 1.0.0
	 *
	 * @return int Products per page.
	 */
	public function wppp_loop_shop_per_page() {
		_deprecated_function( array( $this, __FUNCTION__ ), '1.2.0', 'Woocommerce_Products_Per_Page()->front_end->loop_shop_per_page()' );
		return $this->front_end->loop_shop_per_page();
	}


	/**
	 * Posts per page.
	 *
	 * Set the number of posts per page on a hard way, build in fix for many themes who override the offical loop_shop_per_page filter.
	 *
	 * @since 1.1.0
	 *
	 * @return object Query object
	 */
	public function wppp_pre_get_posts( $q, $class ) {
		_deprecated_function( array( $this, __FUNCTION__ ), '1.2.0', 'Woocommerce_Products_Per_Page()->front_end->woocommerce_product_query()' );
		return $this->front_end->woocommerce_product_query();
	}


	/**
	 * Add dropdown.
	 *
	 * Add the dropdown to the category/shop pages.
	 *
	 * @since 1.0.0
	 */
	public function wppp_shop_hooks() {
		return _deprecated_function( array( $this, __FUNCTION__ ), '1.2.0' );
	}


	/**
	 * Products per page dropdown.
	 *
	 * @since 1.0.0
	 * @deprecated 1.1.0 Use wppp_dropdown() instead.
	 */
	public function wppp_dropdown_object() {
		_deprecated_function( array( $this, __FUNCTION__ ), '1.1.0', '$this->wppp_dropdown()' );
		$this->wppp_dropdown();
	}


	/**
	 * Include dropdown.
	 *
	 * Include dropdown class and create an instance.
	 *
	 * @since 1.0.0
	 */
	public function wppp_dropdown() {
		_deprecated_function( array( $this, __FUNCTION__ ), '1.2.0', 'Woocommerce_Products_Per_Page()->front_end->products_per_page_dropdown()' );
		return $this->front_end->products_per_page_dropdown();
	}


	/**
	 * Initilize session.
	 *
	 * Set an initial session for WC 2.1.X users. Cookies are set automatically prior 2.1.X.
	 *
	 * @since 1.0.0
	 */
	public function wppp_set_customer_session() {
		_deprecated_function( array( $this, __FUNCTION__ ), '1.2.0', 'Woocommerce_Products_Per_Page()->front_end->set_customer_session()' );
		return $this->front_end->set_customer_session();
	}


	/**
	 * Set session.
	 *
	 * Set products per page in session.
	 *
	 * @since 1.1.0
	 */
	public function wppp_submit_check() {
		_deprecated_function( array( $this, __FUNCTION__ ), '1.2.0', 'Woocommerce_Products_Per_Page()->front_end->products_per_page_action()' );
		return $this->front_end->products_per_page_dropdown();
	}


}


/**
 * The main function responsible for returning the Woocommerce_Products_Per_Page object.
 *
 * Use this function like you would a global variable, except without needing to declare the global.
 *
 * Example: <?php Woocommerce_Products_Per_Page()->method_name(); ?>
 *
 * @since 1.0.3
 *
 * @return object Woocommerce_Products_Per_Page class object.
 */
if ( ! function_exists( 'Woocommerce_Products_Per_Page' ) ) :

 	function Woocommerce_Products_Per_Page() {
		return Woocommerce_Products_Per_Page::instance();
	}

endif;

Woocommerce_Products_Per_Page();


// Backwards compatibility
$GLOBALS['wppp'] = Woocommerce_Products_Per_Page();

<?PHP
/*
Plugin Name: Woocommerce Products Per Page
Plugin URI: http://www.jeroensormani.com/
Description: Integrate a 'products per page' dropdown on your WooCommerce website! Set-up in <strong>seconds</strong>!
Version: 1.1.2
Author: Jeroen Sormani
Author URI: http://www.jeroensormani.com

 * Copyright Jeroen Sormani
 *
 *     This file is part of Woocomerce Products Per Page,
 *     a plugin for WordPress.
 *
 *     Woocomerce Products Per Page is free software:
 *     You can redistribute it and/or modify it under the terms of the
 *     GNU General Public License as published by the Free Software
 *     Foundation, either version 3 of the License, or (at your option)
 *     any later version.
 *
 *     Woocomerce Products Per Page is distributed in the hope that
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
	 * Settings from settings page.
	 *
	 * @since 1.1.0
	 * @access public
	 *
	 * @var array $settings Contains all the user settings.
	 */
	public $settings;


	/**
	 * Construct.
	 *
	 * @since 1.0.0
	 *
	 * @return void.
	 */
	public function __construct() {

		if ( ! function_exists( 'is_plugin_active_for_network' ) ) :
		    require_once( ABSPATH . '/wp-admin/includes/plugin.php' );
		endif;

		if ( ! in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) :
			if ( ! is_plugin_active_for_network( 'woocommerce/woocommerce.php' ) ) :
				return;
			endif;
		endif;

		// Initialise settings
		$this->wppp_init_settings();

		// Initialize hooks
		$this->wppp_hooks();

		// Load textdomain
		load_plugin_textdomain( 'woocommerce-products-per-page', false, basename( dirname( __FILE__ ) ) . '/languages' );

	}


	/**
	 * Add all actions and filters.
	 *
	 * @since 1.0.0
	 *
	 * @return int number of columns.
	 */
	public function wppp_hooks() {

		// Add admin settings page
		add_action( 'admin_menu', array( $this, 'wppp_settings_page_menu' ) );

		// Init settings
		add_action( 'admin_init', array( $this, 'wppp_init_settings_page' ) );

		// Add filter for product columns
		add_filter( 'loop_shop_columns', array( $this, 'wppp_loop_shop_columns' ) );

		// Customer number of products per page
		add_filter( 'loop_shop_per_page', array( $this, 'wppp_loop_shop_per_page' ) );
		add_filter( 'pre_get_posts', array( $this, 'wppp_pre_get_posts' ), 1, 50 );

		// Set cookie so PPP will be saved
		add_action( 'init', array( $this, 'wppp_set_customer_session' ), 10 );

		// Check if ppp form is submit
		add_action( 'init', array( $this, 'wppp_submit_check' ) );

	}


	/**
	 * Initialize admin settings page.
	 *
	 * @since 1.1.0
	 *
	 * @return void.
	 */
	public function wppp_settings_page_menu() {
		add_options_page( 'WooCommerce Products Per Page', 'Products Per Page', 'manage_options', 'wppp_settings', array( $this, 'wppp_settings_page' ) );
	}


	/**
	 * Initialize admin settings page.
	 *
	 * @since 1.0.0
	 *
	 * @return void.
	 */
	public function wppp_settings_page() {

		global $wppp_settings;
		$wppp_settings->wppp_render_settings_page();

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

		if ( !get_option( 'wppp_settings' ) ) :
			add_option( 'wppp_settings', $this->wppp_settings_defaults() );
		endif;

		$this->settings = get_option( 'wppp_settings' );

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

		// Set default options to (3|6|9|All) rows
		$ppp_default =
			( apply_filters( 'loop_shop_columns', 4 ) * 3) . ' ' .
			( apply_filters( 'loop_shop_columns', 4 ) * 6) . ' ' .
			( apply_filters( 'loop_shop_columns', 4 ) * 9) . ' ' .
			'-1';

		// Set default settings
		$settings = apply_filters( 'wppp_settings_defaults', array(
			'location'	 		=> 'topbottom',
			'productsPerPage' => $ppp_default,
			'default_ppp' 		=> apply_filters( 'loop_shop_per_page', get_option( 'posts_per_page' ) ),
			'shop_columns' 		=> apply_filters( 'loop_shop_columns', 4 ),
			'behaviour' 		=> '0',
			'method'	 		=> 'post',
		) );

		return $settings;

	}


	/**
	 * Initialise admin settings.
	 *
	 * Initializes settings, but doesn't display them.
	 *
	 * @since 1.1.0
	 *
	 * @return void.
	 */
	public function wppp_init_settings_page() {

		/**
		 * Settings page class
		 */
		require_once plugin_dir_path( __FILE__ ) . 'includes/class-wppp-settings.php';

		global $wppp_settings;
		$wppp_settings = new WPPP_Settings();
		$wppp_settings->wppp_settings_init();

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

		if ( $this->settings && $this->settings['shop_columns'] > 0 ) :
			$columns = $this->settings['shop_columns'];
		endif;

		return $columns;

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

		global $woocommerce;

		if ( isset( $_POST['wppp_ppp'] ) ) :
			return $_POST['wppp_ppp'];
		elseif ( isset( $_GET['wppp_ppp'] ) ) :
			return $_GET['wppp_ppp'];
		elseif ( $woocommerce->session->__isset( 'products_per_page' ) ) :
			return $woocommerce->session->__get( 'products_per_page' );
		else :
			return $this->settings['default_ppp'];
		endif;

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
	public function wppp_pre_get_posts( $q ) {

		if ( function_exists( 'woocommerce_products_will_display' ) && woocommerce_products_will_display() && $q->is_main_query() && ! is_admin() ) :
			$q->set( 'posts_per_page', $this->wppp_loop_shop_per_page() );
		endif;

		return $q;

	}


	/**
	 * Add dropdown.
	 *
	 * Add the dropdown to the category/shop pages.
	 *
	 * @since 1.0.0
	 *
	 * @return void.
	 */
	public function wppp_shop_hooks() {

		if ( $this->settings['location'] == 'top' ) :
			add_action( 'woocommerce_before_shop_loop', array( $this, 'wppp_dropdown' ) );
		elseif ( $this->settings['location'] == 'bottom' ) :
			add_action( 'woocommerce_after_shop_loop', array( $this, 'wppp_dropdown' ) );
		elseif ( $this->settings['location'] == 'topbottom' ):
			add_action( 'woocommerce_before_shop_loop', array( $this, 'wppp_dropdown' ) );
			add_action( 'woocommerce_after_shop_loop', array( $this, 'wppp_dropdown' ) );
		endif;

	}


	/**
	 * Products per page dropdown.
	 *
	 * @since 1.0.0
	 * @deprecated 1.1.0 Use wppp_dropdown() instead.
	 *
	 * @return void.
	 */
	public function wppp_dropdown_object() {
		$this->wppp_dropdown();
	}


	/**
	 * Include dropdown.
	 *
	 * Include dropdown class and create an instance.
	 *
	 * @since 1.0.0
	 *
	 * @return void.
	 */
	public function wppp_dropdown() {

		/**
		 * Products per page dropdown
		 */
		require_once plugin_dir_path( __FILE__ ) . 'includes/class-wppp-dropdown.php';
		new wppp_dropdown();

	}


	/**
	 * Initilize session.
	 *
	 * Set an initial session for WC 2.1.X users. Cookies are set automatically prior 2.1.X.
	 *
	 * @since 1.0.0
	 *
	 * @global object $woocommerce WooCommerce global object.
	 *
	 * @return void.
	 */
	public function wppp_set_customer_session() {

		global $woocommerce;

		if ( $woocommerce->version > '2.1' && ! is_admin() ) :
			$woocommerce->session->set_customer_session_cookie( true );
		endif;

	}


	/**
	 * Set session.
	 *
	 * Set products per page in session.
	 *
	 * @since 1.1.0
	 *
	 * @global object $woocommerce WooCommerce global object.
	 *
	 * @return void.
	 */
	public function wppp_submit_check() {

		global $woocommerce;

		if ( isset( $_POST['wppp_ppp'] ) ) :
			$woocommerce->session->set( 'products_per_page', $_POST['wppp_ppp'] );
		elseif ( isset( $_GET['wppp_ppp'] ) ) :
			$woocommerce->session->set( 'products_per_page', $_GET['wppp_ppp'] );
		endif;

	}


}

global $wppp;
$wppp = new woocommerce_products_per_page();
$wppp->wppp_shop_hooks();
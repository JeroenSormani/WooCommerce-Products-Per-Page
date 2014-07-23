<?PHP
/*
Plugin Name: Woocommerce Products Per Page
Plugin URI: http://www.jeroensormani.nl/
Description: Integrate a 'products per page' dropdown on your WooCommerce website! Set-up in <strong>seconds</strong>!
Version: 1.0.10
Author: Jeroen Sormani
Author URI: http://www.jeroensormani.nl

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

class woocommerce_products_per_page {

	public $options;

	/* 	__construct()
	*
	*
	*/
	public function __construct() {
	
		if ( ! in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
		    return;
		}

		$this->wppp_load_options();

		// Add an options page
		add_action( 'admin_menu', array( $this, 'wppp_add_options_menu' ) );

		// Check if ppp form is submit
		add_action( 'init', array( $this, 'wppp_submit_intercept' ) );

		// Initialise the settings page
		add_action( "admin_init", array( $this, "wppp_init_settings" ) );

		// Add filter to products per page displayed
		add_filter( 'loop_shop_per_page', array( $this, 'wppp_products_per_page_hook' ), 90 );

		// Add filter for product columns
		add_filter( 'loop_shop_columns', array( $this, 'wppp_shop_columns_hook' ) );

		// Enqueue some scripts
		add_action( 'wp_enqueue_scripts', array( $this, 'wppp_enqueue_scripts' ) );

		// Load textdomain
		load_plugin_textdomain( 'wppp', false, basename( dirname( __FILE__ ) ) . '/languages' );
		
		// Set cookie so PPP will be saved
		add_action( 'init', array( $this, 'wppp_set_customer_cookie' ), 10 );

	}

	/* 	wppp_hook_locations()
	*
	*	Hook into the right look positions of WooCommerce
	*/
	public function wppp_hook_locations() {

		if ( $this->options['location'] == 'top' ) :
			add_action( 'woocommerce_before_shop_loop', array( $this, 'wppp_dropdown_object' ) );
		elseif ( $this->options['location'] == 'bottom' ) :
			add_action( 'woocommerce_after_shop_loop', array( $this, 'wppp_dropdown_object' ) );
		elseif ( $this->options['location'] == 'topbottom' ):
			add_action( 'woocommerce_before_shop_loop', array( $this, 'wppp_dropdown_object' ) );
			add_action( 'woocommerce_after_shop_loop', array( $this, 'wppp_dropdown_object' ) );
		endif;

	}

	public function wppp_set_customer_cookie() {
	
		global $woocommerce;

		if ( $woocommerce->version > '2.1' )
			$woocommerce->session->set_customer_session_cookie( true );
		
	}

	/* 	wppp_submit_intercept()
	*
	*
	*/
	public function wppp_submit_intercept() {

		global $woocommerce;

		if ( isset( $_POST['wppp_ppp'] ) ) :
			$woocommerce->session->set( 'products_per_page', $_POST['wppp_ppp'] );
		endif;

	}


	public function wppp_products_per_page_hook() {

		global $woocommerce;

		if ( isset( $_POST['wppp_ppp'] ) ) :
			return $_POST['wppp_ppp'];
		elseif ( $woocommerce->session->__isset( 'products_per_page' ) ) :
			return $woocommerce->session->__get( 'products_per_page' );
		else :
			return $this->options['default_ppp'];
		endif;

	}

	public function wppp_shop_columns_hook( $columns ) {

		$settings = get_option( 'wppp_settings' );

		if ( $settings && $settings['shop_columns'] > 0 ) :
			$columns = $settings['shop_columns'];
		endif;

		return $columns;

	}


	/* 	wppp_add_options_menu()
	*
	*
	*/
	public function wppp_add_options_menu() {

		add_options_page( 'WooCommerce Products Per Page', 'Products Per Page', 'manage_options', 'wppp_settings', array( $this, 'wppp_options_page' ) );

	}

	public function wppp_enqueue_scripts() {

		wp_enqueue_style( 'products-per-page', plugins_url( '/assets/css/style.css', __FILE__ ) );

	}

	/* 	wppp_init_settings()
	*
	*	Initialize the settings
	*/
	public function wppp_init_settings() {

		global $wppp_options;

		require_once plugin_dir_path( __FILE__ ) . 'admin/options-page.php';
		$wppp_options = new wppp_options();
		$wppp_options->wppp_settings_init();

	}

	/* 	wppp_options_page()
	*
	*	Render options page
	*/
	public function wppp_options_page() {

		global $wppp_options;

		$wppp_options->wppp_render_settings_page();

	}


	/* 	wppp_dropdown_object()
	*
	*	Render dropdown object
	*/
	public function wppp_dropdown_object() {

		require_once plugin_dir_path( __FILE__ ) . 'objects/wppp-dropdown.php';
		new wppp_dropdown();

	}


	/* 	wppp_load_options()
	*
	*	Load the configured settings
	*/
	public function wppp_load_options() {

		if ( !get_option( 'wppp_settings' ) ) :
			add_option( 'wppp_settings', $this->wppp_settings_defaults() );
		endif;

		$this->options = get_option( 'wppp_settings' );

	}


	/* 	wppp_settings_defaults()
	*
	*	Set default settings when settings are not set yet.
	*/
	public function wppp_settings_defaults() {

		// Set default options to (3x|6x|9x)current Products Per Page
		$ppp_default =
			( apply_filters( 'loop_shop_columns', 4 ) * 3) . ' ' .
			( apply_filters( 'loop_shop_columns', 4 ) * 6) . ' ' .
			( apply_filters( 'loop_shop_columns', 4 ) * 9) . ' ' .
			'-1';

		// Set default settings
		$settings = apply_filters( 'wppp_settings_defaults', array(
			'location'	 		=> 'topbottom',
			'productsPerPage' 	=> $ppp_default,
			'default_ppp' 		=> apply_filters( 'loop_shop_per_page', get_option( 'posts_per_page' ) ),
			'shop_columns' 		=> apply_filters( 'loop_shop_columns', 4 ),
		) );

		return $settings;

	}


}
$wppp = new woocommerce_products_per_page();

// Call function to load dropdown objects
$wppp->wppp_hook_locations();

?>
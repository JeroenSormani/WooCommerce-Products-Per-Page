<?PHP
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Class WPPP_Admin_Settings.
 *
 * WooCommerce Products Per Page Admin settings class.
 *
 * @class		WPPP_Admin_Settings
 * @version		1.2.0
 * @author		Jeroen Sormani
 */
class WPPP_Admin_Settings {


	/**
	 * Constructor.
	 *
	 * @since 1.2.0
	 */
	public function __construct() {

		// Add settings to the settings array
		add_filter( 'woocommerce_product_settings', array( $this, 'add_settings' ) );

	}


	/**
	 * Add settings.
	 *
	 * Add setting to the 'WooCommerce' -> 'Settings' -> 'Products' -> 'Display'
	 * section.
	 *
	 * @since 1.2.0
	 *
	 * @param 	array $settings List of existing display settings.
	 * @return	array			List of modified display settings.
	 */
	public function add_settings( $settings ) {

		// Set default options to (3|6|9|All) rows
		$dropdown_options_default =
			( apply_filters( 'loop_shop_columns', 4 ) * 3 ) . ' ' .
			( apply_filters( 'loop_shop_columns', 4 ) * 6 ) . ' ' .
			( apply_filters( 'loop_shop_columns', 4 ) * 9 ) . ' ' .
			'-1';

		$new_settings = array(

			array(
				'type' 	=> 'sectionend',
				'id' 	=> 'wppp_start'
			),

			array(
				'title' => 'WooCommerce Products Per Page',
				'type' 	=> 'title',
				'desc' 	=> '',
				'id' 	=> 'wppp_title'
			),

			array(
				'title'	   => __( 'Drop-down location', 'woocommerce-products-per-page' ),
				'desc'		  => __( '', 'woocommerce-products-per-page' ),
				'id'		=> 'wppp_dropdown_location',
				'class'	   => 'wc-enhanced-select',
				'css'		 => 'min-width:300px;',
				'default'  => 'topbottom',
				'type'		  => 'select',
				'options'  => array(
					'top' 		=> __( 'Top', 'woocommerce-products-per-page' ),
					'bottom' 	=> __( 'Bottom', 'woocommerce-products-per-page' ),
					'topbottom' => __( 'Top/Bottom', 'woocommerce-products-per-page' ),
					'none' 		=> __( 'None', 'woocommerce-products-per-page' ),
				),
				'desc_tip' =>  true,
			),

			array(
				'title'			=> __( 'List of dropdown options', 'woocommerce-products-per-page' ),
				'desc'			=> __( 'Seperated by spaces <em>(-1 for all products)</em>', 'woocommerce-products-per-page' ),
				'id'			=> 'wppp_dropdown_options',
				'default'		=> $dropdown_options_default,
				'type'			=> 'text',
			),

			array(
				'title'			=> __( 'Default products per page', 'woocommerce-products-per-page' ),
				'desc'			=> __( '-1 for all products', 'woocommerce-products-per-page' ),
				'id'			=> 'wppp_default_ppp',
				'default'		=> apply_filters( 'loop_shop_per_page', get_option( 'posts_per_page' ) ),
				'css'			=> 'width:50px;',
				'type'			=> 'number',
			),

			array(
				'title'			=> __( 'Shop columns', 'woocommerce-products-per-page' ),
				'desc'			=> __( '', 'woocommerce-products-per-page' ),
				'id'			=> 'wppp_shop_columns',
				'default'		=> apply_filters( 'loop_shop_columns', 4 ),
				'css'			=> 'width:50px;',
				'custom_attributes' => array(
					'min'	=> 0,
					'step' => 1,
				),
				'type'			=> 'number',
			),

			array(
				'title'			=> __( 'First category page', 'woocommerce-products-per-page' ),
				'desc'			=> __( 'When checked and a new number of PPP is selected, the visitor will be send to the first page of the product category', 'woocommerce-products-per-page' ),
				'id'			=> 'wppp_return_to_first',
				'default'		=> 'no',
				'type'			=> 'checkbox',
			),

			array(
				'title'		=> __( 'HTTP method', 'woocommerce-products-per-page' ),
				'desc'		=> __( 'GET sends the products per page via the url, POST does this on the background', 'woocommerce-products-per-page' ),
				'id'		=> 'wppp_method',
				'class'		=> 'wc-enhanced-select',
				'default'	=> 'post',
				'type'		=> 'select',
				'options'	=> array(
					'post'		=> __( 'POST', 'woocommerce-products-per-page' ),
					'get'		=> __( 'GET', 'woocommerce-products-per-page' ),
				),
				'desc_tip' =>  true,
			),

			array(
				'type' 	=> 'sectionend',
				'id' 	=> 'wppp_options'
			),

		);

		return array_merge( $settings, $new_settings );

	}


}

<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Class WPPP_Settings
 *
 * Initialize settings page
 *
 * @class       WPPP_Settings
 * @version     1.1.0
 * @author      Jeroen Sormani
 */

class WPPP_Settings extends Woocommerce_Products_Per_Page {


	/**
	 * Construct.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		parent::__construct();

	}


	/**
	 * Register settings.
	 *
	 * Register setting, sections and settings fields.
	 *
	 * @since 1.0.0
	 */
	public function wppp_settings_init() {

		register_setting( 'wppp_settings', 'wppp_settings' );

		add_settings_section(
			'wppp_settings',
			'WooCommerce Products Per Page',
			array( $this, 'wppp_section_callback' ),
			'wppp_settings'
		);


		add_settings_field(
			'wppp_location',
			__( 'Dropdown location', 'woocommerce-products-per-page' ),
			array( $this, 'wppp_settings_field_location' ),
			'wppp_settings',
			'wppp_settings'
		);

		add_settings_field(
			'wppp_products_per_page_list',
			__( 'List of dropdown options', 'woocommerce-products-per-page' ),
			array( $this, 'wppp_settings_field_ppp_list' ),
			'wppp_settings',
			'wppp_settings'
		);

		add_settings_field(
			'wppp_default_ppp',
			__( 'Default products per page', 'woocommerce-products-per-page' ),
			array( $this, 'wppp_settings_field_default_ppp' ),
			'wppp_settings',
			'wppp_settings'
		);

		add_settings_field(
			'wppp_shop_columns',
			__( 'Shop columns', 'woocommerce-products-per-page' ),
			array( $this, 'wppp_settings_field_shop_columns' ),
			'wppp_settings',
			'wppp_settings'
		);

		add_settings_field(
			'wppp_ppp_behaviour',
			__( 'First category page', 'woocommerce-products-per-page' ),
			array( $this, 'wppp_settings_field_behaviour' ),
			'wppp_settings',
			'wppp_settings'
		);

		add_settings_field(
			'wppp_method',
			__( 'HTTP method', 'woocommerce-products-per-page' ),
			array( $this, 'wppp_settings_field_method' ),
			'wppp_settings',
			'wppp_settings'
		);

	}


	/**
	 * Settings page render.
	 *
	 * Load settings fields, sections and submit button.
	 *
	 * @since 1.0.0
	 */
	public function wppp_render_settings_page() {

		?><div class="wrap">

			<h2><?php _e( 'WooCommerce Products Per Page', 'woocommerce-products-per-page' ); ?></h2>

			<form method="POST" action="options.php">
				<?php
				settings_fields( 'wppp_settings' );
				do_settings_sections( 'wppp_settings' );
				submit_button();
				?>
			</form>

		</div><?php

	}


	/**
	 * Location setting.
	 *
	 * Settings dropdown to select the location of the dropdown.
	 *
	 * @since 1.0.0
	 */
	public function wppp_settings_field_location() {

		?><select name="wppp_settings[location]" class="">
			<option value="top" 		<?php selected( $this->settings['location'], 'top' ); ?>>		<?php _e( 'Top', 'woocommerce-products-per-page' ); ?></option>
			<option value="bottom" 		<?php selected( $this->settings['location'], 'bottom' ); ?>>	<?php _e( 'Bottom', 'woocommerce-products-per-page' ); ?></option>
			<option value="topbottom" 	<?php selected( $this->settings['location'], 'topbottom' ); ?>>	<?php _e( 'Top/Bottom', 'woocommerce-products-per-page' ); ?></option>
			<option value="none" 		<?php selected( $this->settings['location'], 'none' ); ?>>		<?php _e( 'None', 'woocommerce-products-per-page' ); ?></option>
		</select><?php

	}


	/**
	 * Dropdown options input.
	 *
	 * Settings input to set the options for the dropdown. Seperate with space.
	 *
	 * @since 1.0.0
	 */
	public function wppp_settings_field_ppp_list() {

		?><label for="products_per_page">
			<input type="text" id="products_per_page" name="wppp_settings[productsPerPage]" value="<?php echo $this->settings['productsPerPage']; ?>">
		<?php _e( 'Seperated by spaces <em>(-1 for all products)</em>', 'woocommerce-products-per-page' ); ?></label><?php

	}


	/**
	 * Default ppp input.
	 *
	 * Settings input to set the default products per page.
	 *
	 * @since 1.0.0
	 */
	public function wppp_settings_field_default_ppp() {

		?><label for="default_ppp">
			<input type="number" id="default_ppp" name="wppp_settings[default_ppp]" value="<?php echo $this->settings['default_ppp']; ?>">
			<em><?php _e( '-1 for all products', 'woocommerce-products-per-page' ); ?></em>
		</label><?php

	}


	/**
	 * Shop columns input.
	 *
	 * Settings input to set the shop columns per row.
	 *
	 * @since 1.0.0
	 */
	public function wppp_settings_field_shop_columns() {

		?><label for="shop_columns">
			<input type="number" id="shop_columns" name="wppp_settings[shop_columns]" value="<?php echo $this->settings['shop_columns']; ?>">
		</label><?php

	}


	/**
	 * Behaviour checkbox.
	 *
	 * Rendering method for behaviour checkbox.
	 *
	 * @since 1.0.0
	 */
	public function wppp_settings_field_behaviour() {

		?><label for="behaviour">
			<input type="checkbox" id="behaviour" name="wppp_settings[behaviour]" value="1" <?php @checked( $this->settings['behaviour'], 1 ); ?>>
			<?php _e( 'When checked and a new number of PPP is selected, the visitor will be send to the first page of the product category', 'woocommerce-products-per-page' ); ?>
		</label>
		<style>
		.tooltip {
			width: 200px;
			height: auto;
			background-color: rgba( 0,0,0,0.8 );
			color: #f1f1f1;
			padding: 10px;
			border-radius: 10px;
			display: block;
			font-family: 'Open Sans', sans-serif;
			font-size: 14px;
			display: none;
			position: relative;
			top: 15px;
			left: -12px;
		}
		.tooltip:before {
			border-left: 10px solid transparent;
			border-right: 10px solid transparent;
			border-bottom: 10px solid rgba( 0,0,0,0.8 );
			border-top: 10px solid transparent;
			content: " ";
			position: absolute;
			top: -20px;

		}
		.tooltip a {
			color: #f1f1f1;
			text-decoration: none;
		}
		.tooltip a:hover {
			text-decoration: underline;
		}
		*:hover > .tooltip {
			display: block;
		}
		</style><?php

	}


	/**
	 * Method checkbox.
	 *
	 * Rendering method for method checkbox.
	 *
	 * @since 1.0.0
	 */
	public function wppp_settings_field_method() {

		?><label for="method">
			<select name="wppp_settings[method]" class="">
				<option value="post" 	<?php @selected( $this->settings['method'], 'post' ); ?>>	<?php _e( 'POST', 'woocommerce-products-per-page' ); ?></option>
				<option value="get" 	<?php @selected( $this->settings['method'], 'get' ); ?>>	<?php _e( 'GET', 'woocommerce-products-per-page' ); ?></option>
			</select>
			<?php _e( 'GET sends the products per page via the url, POST does this on the background', 'woocommerce-products-per-page' ); ?>
		</label><?php

	}


	/**
	 * Settings page description.
	 *
	 * @since 1.0.0
	 */
	public function wppp_section_callback() {

		echo __( 'Configure the WooCommerce Product Per Page settings here.', 'woocommerce-products-per-page' );

	}

}

?>
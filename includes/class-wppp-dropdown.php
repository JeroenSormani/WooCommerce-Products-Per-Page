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
	 * Product per page option array.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @var array $product_per_page_options Array of options.
	 */
	public $products_per_page_options;


	/**
	 * Construct.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		// Get all the settings
		$this->settings = WooCommerce_Products_Per_page()->settings;

		// Create the dropdown
		$this->wppp_dropdown();

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
		$this->wppp_dropdown();
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

		global $wp_query;

		$action = '';
		$cat 	= '';
		$cat 	= $wp_query->get_queried_object();

		// Set the products per page options (e.g. 4, 8, 12)
		$products_per_page_options = $this->wppp_prep_ppp( apply_filters( 'wppp_products_per_page', $this->settings['productsPerPage'] ) );

		// Set action url if option behaviour is true
		// Paste QUERY string after for filter and orderby support
		$query_string = ! empty( $_SERVER['QUERY_STRING'] ) ? '?' . add_query_arg( array( 'wppp_ppp' => false ), $_SERVER['QUERY_STRING'] ) : null;

		if ( isset( $cat->term_id ) && isset( $cat->taxonomy ) && isset( $this->settings['behaviour'] ) && true == $this->settings['behaviour'] ) :
			$action = get_term_link( $cat->term_id, $cat->taxonomy ) . $query_string;
		elseif ( isset( $this->settings['behaviour'] ) &&  true == $this->settings['behaviour'] ) :
			$action = get_permalink( woocommerce_get_page_id( 'shop' ) ) . $query_string;
		endif;

		$method = 'post'; // default
		if ( isset( $this->settings['method'] ) && $this->settings['method'] == 'get' ) :
			$method = 'get';
		endif;

		// Only show on product categories
		if ( woocommerce_products_will_display() ) :

			 do_action( 'wppp_before_dropdown_form' );

			?><form method="<?php echo esc_attr( $method ); ?>" action="<?php echo esc_url( $action ); ?>" style='float: right; margin-left: 5px;' class="form-wppp-select products-per-page"><?php

				 do_action( 'wppp_before_dropdown' );

				?><select name="wppp_ppp" onchange="this.form.submit()" class="select wppp-select"><?php

					foreach( $products_per_page_options as $key => $value ) :

						// Get the right match for the selected option
						$ppp_session = WC()->session->get( 'products_per_page' );
						if ( isset( $_POST['wppp_ppp'] ) ) :
							$selected_match = $_POST['wppp_ppp'];
						elseif ( isset( $_GET['wppp_ppp'] ) ):
							$selected_match = $_GET['wppp_ppp'];
						elseif ( ! empty( $ppp_session ) ) :
							$selected_match = $ppp_session;
						else :
							$selected_match = $this->settings['default_ppp'];
						endif;

						?><option value="<?php echo esc_attr( $value ); ?>" <?php selected( $value, $selected_match ); ?>><?php

							$ppp_text = apply_filters( 'wppp_ppp_text', __( '%s products per page', 'woocommerce-products-per-page' ), $value );
							printf( $ppp_text, $value == -1 ? __( 'All', 'woocommerce-products-per-page' ) : $value ); // Set to 'All' when value is -1

						?></option><?php

					endforeach;

				?></select><?php

				do_action( 'wppp_after_dropdown' );

			?></form><?php

			do_action( 'wppp_after_dropdown_form' );

		endif;

	}


	/**
	 * Prepare dropdown options.
	 *
	 * Prepare the options for the products per page dropdown.
	 *
	 * @since 1.0.0
	 *
	 * @return array of options.
	 */
	public function wppp_prep_ppp( $products_per_page ) {

		return explode( ' ', $products_per_page );

	}

}

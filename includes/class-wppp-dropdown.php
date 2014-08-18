<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Class WPPP_Dropdown
 *
 * Products per page dropdown class
 *
 * @class       WPPP_Dropdown
 * @version     1.1.0
 * @author      Jeroen Sormani
 */
class WPPP_Dropdown extends Woocommerce_Products_Per_Page {


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
	public function __construct( $products_per_page_options = null ) {

		parent::__construct();

		$this->products_per_page = $this->wppp_prep_ppp( $products_per_page_options );

		if ( false == $products_per_page_options ) :
			$this->products_per_page_options = $this->wppp_prep_ppp( apply_filters( 'wppp_products_per_page', $this->settings['productsPerPage'] ) );
		endif;

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

		// Set action url if option behaviour is true
		// Paste QUERY string after for filter and orderby support
		$query_string = ! empty( $_SERVER['QUERY_STRING'] ) ? '?' . add_query_arg( array( 'wppp_ppp' => false ), $_SERVER['QUERY_STRING'] ) : null;

		if ( isset( $cat->term_id ) && isset( $cat->taxonomy ) && isset( $this->settings['behaviour'] ) && true == $this->settings['behaviour'] ) :
			$action = ' action="' . get_term_link( $cat->term_id, $cat->taxonomy ) . $query_string . '"';
		elseif ( isset( $this->settings['behaviour'] ) &&  true == $this->settings['behaviour'] ) :
			$action = 'action="' . get_permalink( woocommerce_get_page_id( 'shop' ) ) . $query_string . '"';
		endif;

		$method = 'post'; // default
		if ( isset( $this->settings['method'] ) && $this->settings['method'] == 'get' ) :
			$method = 'get';
		endif;

		// Only show on product categories
		if ( woocommerce_products_will_display() ) :
		?>

			<form method="<?php echo $method; ?>" <?php echo $action; ?> style='float: right;' class="form-wppp-select products-per-page">

				<?php do_action( 'wppp_before_dropdown' ); ?>

				<select name="wppp_ppp" onchange="this.form.submit()" class="select wppp-select">

					<?php
					global $woocommerce;
					foreach( $this->products_per_page_options as $key => $value ) :

						// Get the right match for the selected option
						$ppp_session = $woocommerce->session->get( 'products_per_page' );
						if( isset( $_POST['wppp_ppp'] ) ) :
							$selected_match = $_POST['wppp_ppp'];
						elseif( isset( $_GET['wppp_ppp'] ) ):
							$selected_match = $_GET['wppp_ppp'];
						elseif ( ! empty( $ppp_session ) ) :
							$selected_match = $ppp_session;
						else :
							$selected_match = $this->settings['default_ppp'];
						endif;

						?>
						<option value="<?php echo $value; ?>" <?php selected( $value, $selected_match ); ?>>
							<?php
							$ppp_text = apply_filters( 'wppp_ppp_text', __( '%s products per page', 'woocommerce-products-per-page' ), $value );
							printf( $ppp_text, $value == -1 ? __( 'All', 'woocommerce-products-per-page' ) : $value ); // Set to 'All' when value is -1
							?>
						</option>
						<?php

					endforeach;
					?>
				</select>

				<?php do_action( 'wppp_after_dropdown' ); ?>

			</form>
		<?php
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
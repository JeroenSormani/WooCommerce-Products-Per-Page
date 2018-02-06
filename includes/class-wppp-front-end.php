<?PHP
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Class WPPP_Front_End.
 *
 * Handles all front end related business.
 *
 * @class		WPPP_Front_End
 * @version		1.2.0
 * @author		Jeroen Sormani
 */
class WPPP_Front_End {


	/**
	 * Constructor.
	 *
	 * @since 1.2.0
	 */
	public function __construct() {

		// Set dropdowns
		$location = get_option( 'wppp_dropdown_location', 'none' );
		if ( $location == 'top' ) :
			add_action( 'woocommerce_before_shop_loop', array( $this, 'products_per_page_dropdown' ), 25 );
		elseif ( $location == 'bottom' ) :
			add_action( 'woocommerce_after_shop_loop', array( $this, 'products_per_page_dropdown' ), 25 );
		elseif ( $location == 'topbottom' ):
			add_action( 'woocommerce_before_shop_loop', array( $this, 'products_per_page_dropdown' ), 25 );
			add_action( 'woocommerce_after_shop_loop', array( $this, 'products_per_page_dropdown' ), 25 );
		endif;

		// Add filter for product columns
		add_filter( 'loop_shop_columns', array( $this, 'loop_shop_columns' ), 100 );

		// Custom number of products per page
		add_filter( 'loop_shop_per_page', array( $this, 'loop_shop_per_page' ), 100, 1 );

		// Check if ppp form is fired
		add_action( 'init', array( $this, 'products_per_page_action' ) );

	}


	/**
	 * Display drop down.
	 *
	 * Display the drop down front end to the user to choose
	 * the number of products per page.
	 *
	 * @since 1.2.0
	 */
	public function products_per_page_dropdown() {

		global $wp_query;

		$action = '';
		$cat 	= '';
		$cat 	= $wp_query->get_queried_object();
		$method = in_array( get_option( 'wppp_method', 'post' ), array( 'post', 'get' ) ) ? get_option( 'wppp_method', 'post' ) : 'post';

		// Set the products per page options (e.g. 4, 8, 12)
		$products_per_page_options = explode( ' ', apply_filters( 'wppp_products_per_page', get_option( 'wppp_dropdown_options' ) ) );

		// Set action url if option behaviour is true
		// Paste QUERY string after for filter and orderby support
		$query_string = ! empty( $_SERVER['QUERY_STRING'] ) ? '?' . add_query_arg( array( 'ppp' => false ), $_SERVER['QUERY_STRING'] ) : null;

		if ( isset( $cat->term_id ) && isset( $cat->taxonomy ) && 'yes' == get_option( 'wppp_return_to_first', 'no' ) ) :
			$action = get_term_link( $cat->term_id, $cat->taxonomy ) . $query_string;
		elseif ( 'yes' == get_option( 'wppp_return_to_first', 'no' ) ) :
			$action = get_permalink( wc_get_page_id( 'shop' ) ) . $query_string;
		endif;

		// Only show on product categories
		if ( ! woocommerce_products_will_display() ) :
			return;
		endif;

		do_action( 'wppp_before_dropdown_form' );

		?><form method="<?php echo esc_attr( $method ); ?>" action="<?php echo esc_url( $action ); ?>" style='float: right; margin-left: 5px;' class="form-wppp-select products-per-page"><?php

			 do_action( 'wppp_before_dropdown' );

			?><select name="ppp" onchange="this.form.submit()" class="select wppp-select"><?php

				foreach( $products_per_page_options as $key => $value ) :

					?><option value="<?php echo esc_attr( $value ); ?>" <?php selected( $value, $this->loop_shop_per_page( 12 ) ); ?>><?php
						$ppp_text = apply_filters( 'wppp_ppp_text', __( '%s products per page', 'woocommerce-products-per-page' ), $value );
						esc_html( printf( $ppp_text, $value == -1 ? __( 'All', 'woocommerce-products-per-page' ) : $value ) ); // Set to 'All' when value is -1
					?></option><?php

				endforeach;

			?></select><?php

			// Keep query string vars intact
			foreach ( $_GET as $key => $val ) :

				if ( 'ppp' === $key || 'submit' === $key ) :
					continue;
				endif;
				if ( is_array( $val ) ) :
					foreach( $val as $inner_val ) :
						?><input type="hidden" name="<?php echo esc_attr( $key ); ?>[]" value="<?php echo esc_attr( $inner_val ); ?>" /><?php
					endforeach;
				else :
					?><input type="hidden" name="<?php echo esc_attr( $key ); ?>" value="<?php echo esc_attr( $val ); ?>" /><?php
				endif;
			endforeach;

			do_action( 'wppp_after_dropdown' );

		?></form><?php

		do_action( 'wppp_after_dropdown_form' );

	}


	/**
	 * Shop columns.
	 *
	 * Set number of columns (products per row).
	 *
	 * @since 1.2.0
	 *
	 * @param int $columns Current number of shop columns.
	 * @return int Number of columns.
	 */
	public function loop_shop_columns( $columns ) {

		if ( ( $shop_columns = get_option( 'wppp_shop_columns', 0 ) ) > 0 ) :
			$columns = $shop_columns;
		endif;

		return $columns;

	}


	/**
	 * Per page hook.
	 *
	 * Return the number of products per page to the hook
	 *
	 * @since 1.2.0
	 *
	 * @return int Products per page.
	 */
	public function loop_shop_per_page( $per_page ) {

		if ( isset( $_REQUEST['wppp_ppp'] ) ) :
			$per_page = intval( $_REQUEST['wppp_ppp'] );
		elseif ( isset( $_REQUEST['ppp'] ) ) :
			$per_page = intval( $_REQUEST['ppp'] );
		elseif ( isset( $_COOKIE['woocommerce_products_per_page'] ) ) :
			$per_page = $_COOKIE['woocommerce_products_per_page'];
		else :
			$per_page = intval( get_option( 'wppp_default_ppp', '12' ) );
		endif;

		return $per_page;

	}


	/**
	 * Posts per page.
	 *
	 * Set the number of posts per page on a hard way, build in fix for many themes who override the offical loop_shop_per_page filter.
	 *
	 * @since 1.2.0
	 *
	 * @param	object 	$q		Existing query object.
	 * @param	object	$class	Class object.
	 * @return 	object 			Modified query object.
	 */
	public function woocommerce_product_query( $q, $class ) {

		if ( function_exists( 'woocommerce_products_will_display' ) && woocommerce_products_will_display() && $q->is_main_query() ) :
			$q->set( 'posts_per_page', $this->loop_shop_per_page() );
		endif;

	}


	/**
	 * PPP action.
	 *
	 * Set the number of products per page when the customer
	 * changes the amount in the drop down.
	 *
	 * @since 1.2.0
	 */
	public function products_per_page_action() {

		if ( isset( $_REQUEST['wppp_ppp'] ) ) :
			wc_setcookie( 'woocommerce_products_per_page', intval( $_REQUEST['wppp_ppp'] ), time() + DAY_IN_SECONDS * 2, apply_filters( 'wc_session_use_secure_cookie', false ) );
		elseif ( isset( $_REQUEST['ppp'] ) ) :
			wc_setcookie( 'woocommerce_products_per_page', intval( $_REQUEST['ppp'] ), time() + DAY_IN_SECONDS * 2, apply_filters( 'wc_session_use_secure_cookie', false ) );
		endif;

	}


}

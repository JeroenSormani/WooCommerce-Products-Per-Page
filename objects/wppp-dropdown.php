<?php

class wppp_dropdown extends woocommerce_products_per_page {
	
	public $productPerPage;
	
	public function __construct( $productPerPage = null ) {
		
		parent::__construct();
		
		$this->productsPerPage = $this->wppp_prep_ppp( $productPerPage );
		
		if ( false == $productPerPage )
			$this->productsPerPage = $this->wppp_prep_ppp( apply_filters( 'wppp_products_per_page', $this->options['productsPerPage'] ) );
			
		$this->wppp_create_object();
		
	}
	
	
	public function wppp_create_object() {
		
		global $wp_query;
		
		$cat = '';
		$cat = $wp_query->get_queried_object();

		// Set action url if option behaviour is true
		// Paste QUERY string after for filter and orderby support
		$query_string = !empty( $_SERVER['QUERY_STRING'] ) ? '?' . $_SERVER['QUERY_STRING'] : null;
		
		$action = '';
		if ( isset( $cat->term_id ) && isset( $cat->taxonomy ) && isset( $this->options['behaviour'] ) && true == $cat->term_id && true == $this->options['behaviour'] && 'product_cat' == $cat->taxonomy ) :
			$action = ' action="' . get_term_link( $cat->term_id, 'product_cat' ) . $query_string . '"';
		elseif ( isset( $this->options['behaviour'] ) &&  true == $this->options['behaviour'] ) :
			$action = 'action="' . get_permalink( woocommerce_get_page_id( 'shop' ) ) . $query_string . '"';
		endif;
		
		// Only show on product categories
		if ( woocommerce_products_will_display() ) :
		?>
		
		<form method="post" <?php echo $action; ?> class="form-wppp-select products-per-page">
			<?php
			do_action( 'wppp_before_dropdown' );
			?>
			<select name="wppp_ppp" onchange="this.form.submit()" class="select wppp-select">
			
				<?php
				global $woocommerce;
				foreach( $this->productsPerPage as $key => $value ) :
					
					// Get the right match for the selected option
					$ppp_session = $woocommerce->session->get( 'products_per_page' );
					if( isset( $_POST['wppp_ppp'] ) ) :
						$selected_match = $_POST['wppp_ppp'];
					elseif ( !empty( $ppp_session ) ) :
						$selected_match = $woocommerce->session->get( 'products_per_page' );
					else :
						$selected_match = $this->options['default_ppp'];
					endif;
					
					?>
					<option value="<?php echo $value; ?>" <?php selected( $value, $selected_match ); ?>>
						<?php 
						$ppp_text = apply_filters( 'wppp_ppp_text', __( '%s products per page', 'wppp' ) );
						printf( $ppp_text, $value == -1 ? __( 'All', 'wppp' ) : $value ); // Set to 'All' when value is -1
						?>
					</option>
					<?php
					
				endforeach;
				?>
			</select>
			<?php
			do_action( 'wppp_after_dropdown' );
			?>
		</form>
		<?php
		endif;
		
	}
	
	
	public function wppp_prep_ppp( $productPerPage ) {

		return explode( ' ', $productPerPage );
		
	}
	
}

?>
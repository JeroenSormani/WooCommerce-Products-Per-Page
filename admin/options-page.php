<?php

class wppp_options extends woocommerce_products_per_page {

	
	public function __construct() {
	
		parent::__construct();
					
	}

	
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
			__( 'Dropdown location', 'wppp' ),
			array( $this, 'wppp_settings_field_location' ),
			'wppp_settings',
			'wppp_settings'
		);
		
		add_settings_field( 
			'wppp_products_per_page_list', 
			__( 'List of dropdown options', 'wppp' ),
			array( $this, 'wppp_settings_field_ppp_list' ),
			'wppp_settings',
			'wppp_settings' 
		);
		
		add_settings_field(
			'wppp_default_ppp',
			__( 'Default products per page', 'wppp' ),
			array( $this, 'wppp_settings_field_default_ppp' ),
			'wppp_settings',
			'wppp_settings'
		);
		
		add_settings_field(
			'wppp_shop_columns', 
			__( 'Shop columns', 'wppp' ), 
			array( $this, 'wppp_settings_field_shop_columns' ),
			'wppp_settings',
			'wppp_settings'
		);
		
		add_settings_field(
			'wppp_ppp_behaviour', 
			__( 'First category page', 'wppp' ), 
			array( $this, 'wppp_settings_field_behaviour' ),
			'wppp_settings',
			'wppp_settings'
		);
		
	}
	
	
	public function wppp_render_settings_page() {
		
		?>
		<div class="wrap">
		
			<h2><?php _e( 'WooCommerce Products Per Page', 'wppp' ); ?></h2>
			
			<form method="POST" action="options.php">
				<?php
				settings_fields( 'wppp_settings' );
				do_settings_sections( 'wppp_settings' );
				submit_button();
				?>
			</form>
			
		</div>
		<?php
		
	}
	
	public function wppp_settings_field_location() {
		
		?>
		<select name="wppp_settings[location]" class="">
			<option value="top" 		<?php selected( $this->options['location'], 'top' ); ?>>		<?php _e( 'Top', 'wppp' ); ?></option>
			<option value="bottom" 		<?php selected( $this->options['location'], 'bottom' ); ?>>		<?php _e( 'Bottom', 'wppp' ); ?></option>
			<option value="topbottom" 	<?php selected( $this->options['location'], 'topbottom' ); ?>>	<?php _e( 'Top/Bottom', 'wppp' ); ?></option>
			<option value="none" 		<?php selected( $this->options['location'], 'none' ); ?>>		<?php _e( 'None', 'wppp' ); ?></option>
		</select>
		<?php
		
	}
	
	
	public function wppp_settings_field_ppp_list() {

		?>
		<label for="productsPerPage">
			<input type="text" id="productsPerPage" name="wppp_settings[productsPerPage]" value="<?php echo $this->options['productsPerPage']; ?>">
		<?php _e( 'Seperated by spaces <em>(-1 for all products)</em>', 'wppp' ); ?></label>
		<?php
		
	}
	
	
	public function wppp_settings_field_default_ppp() {
		
		?>
		<label for="default_ppp">
			<input type="number" id="default_ppp" name="wppp_settings[default_ppp]" value="<?php echo $this->options['default_ppp']; ?>">
		<em><?php _e( '-1 for all products', 'wppp' ); ?></em></label>
		<?php
		
	}
	
	
	public function wppp_settings_field_shop_columns() {
		
		?>
		<label for="shop_columns">
			<input type="number" id="shop_columns" name="wppp_settings[shop_columns]" value="<?php echo $this->options['shop_columns']; ?>">
		</label>		
		<?php
		
	}
	
	
	public function wppp_settings_field_behaviour() {
		
		?>
		<label for="behaviour">
			<input type="checkbox" id="behaviour" name="wppp_settings[behaviour]" value="1" <?php @checked( $this->options['behaviour'], 1 ); ?>>
			<?php _e( 'When checked and a new number of PPP is selected, the visitor will be send to the first page of the product category', 'wppp' ); ?>
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
		</style>
<!-- 		<div class="dashicons dashicons-info"><span class="tooltip"><a href="http://www.jeroensormani.nl">Read more why this function is in here</a></span></div> -->
		<?php
		
	}
	
	
	public function wppp_section_callback() {
		
		echo __( 'Configure the WooCommerce Product Per Page settings here.', 'wppp' );
		
	}
		
}

?>
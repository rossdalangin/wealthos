<?php
/**
 * Frontend Shortcodes and Renderer for WealthOS.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WealthOS_Public {

	public function __construct() {
		add_shortcode( 'wealthos_dashboard', array( $this, 'render_dashboard_shortcode' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
	}

	public function enqueue_scripts() {
		wp_enqueue_style( 'wealthos-css', WEALTHOS_URL . 'assets/css/wealthos.css', array(), WEALTHOS_VERSION );
		wp_enqueue_script( 'wealthos-js', WEALTHOS_URL . 'assets/js/wealthos.js', array( 'jquery' ), WEALTHOS_VERSION, true );

		wp_localize_script(
			'wealthos-js',
			'wealthosSettings',
			array(
				'apiRoot'        => esc_url_raw( rest_url( 'wealthos/v1/' ) ),
				'nonce'          => wp_create_nonce( 'wp_rest' ),
				'currencySymbol' => WealthOS_Settings::get_setting( 'currency_symbol', '$' ),
			)
		);
	}

	public function render_dashboard_shortcode( $atts ) {
		if ( ! is_user_logged_in() ) {
			return '<div class="wealthos-card"><p>' . esc_html__( 'Please log in to access your Personal Wealth Operating System.', 'wealthos' ) . '</p></div>';
		}

		ob_start();
		include WEALTHOS_PATH . 'templates/dashboard/main.php';
		return ob_get_clean();
	}
}

new WealthOS_Public();

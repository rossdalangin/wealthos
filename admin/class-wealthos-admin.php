<?php
/**
 * Admin Panel & Controller for WealthOS.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WealthOS_Admin {

	public function __construct() {
		add_action( 'admin_menu', array( $this, 'add_menu_pages' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_assets' ) );
	}

	public function add_menu_pages() {
		add_menu_page(
			'WealthOS',
			'WealthOS',
			'manage_options',
			'wealthos',
			array( $this, 'render_admin_dashboard' ),
			'dashicons-chart-line',
			30
		);

		add_submenu_page(
			'wealthos',
			__( 'Settings', 'wealthos' ),
			__( 'Settings', 'wealthos' ),
			'manage_options',
			'wealthos-settings',
			array( $this, 'render_admin_settings' )
		);
	}

	public function enqueue_admin_assets( $hook ) {
		if ( false === strpos( $hook, 'wealthos' ) ) {
			return;
		}

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

	public function render_admin_dashboard() {
		include WEALTHOS_PATH . 'admin/dashboard.php';
	}

	public function render_admin_settings() {
		if ( isset( $_POST['wealthos_save_settings'] ) && check_admin_referer( 'wealthos_settings_nonce' ) ) {
			$new_settings = array(
				'app_name'                 => WealthOS_Security::sanitize_text( $_POST['app_name'] ?? 'WealthOS' ),
				'tagline'                  => WealthOS_Security::sanitize_text( $_POST['tagline'] ?? '' ),
				'currency_symbol'          => WealthOS_Security::sanitize_text( $_POST['currency_symbol'] ?? '$' ),
				'primary_color'            => WealthOS_Security::sanitize_text( $_POST['primary_color'] ?? '#0f172a' ),
				'accent_color'             => WealthOS_Security::sanitize_text( $_POST['accent_color'] ?? '#10b981' ),
				'delete_data_on_uninstall' => isset( $_POST['delete_data_on_uninstall'] ) ? 1 : 0,
			);
			WealthOS_Settings::update_settings( $new_settings );
			echo '<div class="notice notice-success is-dismissible"><p>' . esc_html__( 'Settings updated successfully.', 'wealthos' ) . '</p></div>';
		}

		include WEALTHOS_PATH . 'admin/settings.php';
	}
}

new WealthOS_Admin();

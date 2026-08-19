<?php
/**
 * Settings and Customization Management for WealthOS.
 * Supports rebrandable plugin name, tagline, colors, currency, and white-labeling.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WealthOS_Settings {

	public static function get_defaults() {
		return array(
			'app_name'                 => 'WealthOS',
			'tagline'                  => 'Your Personal Wealth Operating System.',
			'primary_color'            => '#0f172a', // Deep slate/navy
			'accent_color'             => '#10b981', // Emerald green
			'currency_symbol'          => '$',
			'currency_code'            => 'USD',
			'delete_data_on_uninstall' => 0,
			'enable_notifications'     => 1,
			'disclaimer_text'          => __( 'WealthOS is an educational and financial-planning software tool, not a registered financial adviser or fiduciary. It does not guarantee investment returns or financial success.', 'wealthos' ),
		);
	}

	public static function get_setting( $key, $default = null ) {
		$settings = get_option( 'wealthos_settings', self::get_defaults() );
		if ( isset( $settings[ $key ] ) ) {
			return $settings[ $key ];
		}
		$defaults = self::get_defaults();
		return isset( $defaults[ $key ] ) ? $defaults[ $key ] : $default;
	}

	public static function update_settings( $new_settings ) {
		$current = get_option( 'wealthos_settings', self::get_defaults() );
		$updated = wp_parse_args( $new_settings, $current );
		update_option( 'wealthos_settings', $updated );
		return $updated;
	}

	public static function format_currency( $amount ) {
		$symbol = self::get_setting( 'currency_symbol', '$' );
		$amount = (float) $amount;
		return $symbol . number_format( $amount, 2, '.', ',' );
	}
}

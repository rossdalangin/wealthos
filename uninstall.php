<?php
/**
 * Fired when the plugin is uninstalled.
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

// Check settings if user wants to delete data on uninstall
$settings = get_option( 'wealthos_settings', array() );
if ( ! empty( $settings['delete_data_on_uninstall'] ) ) {
	global $wpdb;

	$tables = array(
		$wpdb->prefix . 'wealthos_income',
		$wpdb->prefix . 'wealthos_income_growth',
		$wpdb->prefix . 'wealthos_expenses',
		$wpdb->prefix . 'wealthos_budgets',
		$wpdb->prefix . 'wealthos_debts',
		$wpdb->prefix . 'wealthos_savings',
		$wpdb->prefix . 'wealthos_investments',
		$wpdb->prefix . 'wealthos_assets',
		$wpdb->prefix . 'wealthos_net_worth_snapshots',
		$wpdb->prefix . 'wealthos_goals',
		$wpdb->prefix . 'wealthos_business_metrics',
		$wpdb->prefix . 'wealthos_business_offers',
		$wpdb->prefix . 'wealthos_risk_checklist',
		$wpdb->prefix . 'wealthos_actions',
		$wpdb->prefix . 'wealthos_calendar',
		$wpdb->prefix . 'wealthos_notifications',
		$wpdb->prefix . 'wealthos_user_profile',
	);

	foreach ( $tables as $table ) {
		$wpdb->query( "DROP TABLE IF EXISTS {$table}" );
	}

	delete_option( 'wealthos_settings' );
	delete_option( 'wealthos_db_version' );
}

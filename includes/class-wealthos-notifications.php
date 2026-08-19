<?php
/**
 * Optional Notification System for WealthOS.
 * Respectful, empowering, non-manipulative alerts and updates.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WealthOS_Notifications {

	public static function get_user_notifications( $user_id ) {
		global $wpdb;

		// Check settings if notifications are enabled
		if ( ! WealthOS_Settings::get_setting( 'enable_notifications', 1 ) ) {
			return array();
		}

		return $wpdb->get_results(
			$wpdb->prepare( "SELECT * FROM {$wpdb->prefix}wealthos_notifications WHERE user_id = %d ORDER BY created_at DESC LIMIT 20", $user_id ),
			ARRAY_A
		);
	}

	public static function add_notification( $user_id, $title, $message, $type = 'info' ) {
		global $wpdb;

		if ( ! WealthOS_Settings::get_setting( 'enable_notifications', 1 ) ) {
			return false;
		}

		$wpdb->insert(
			$wpdb->prefix . 'wealthos_notifications',
			array(
				'user_id'    => $user_id,
				'title'      => WealthOS_Security::sanitize_text( $title ),
				'message'    => WealthOS_Security::sanitize_textarea( $message ),
				'type'       => WealthOS_Security::sanitize_text( $type ),
				'is_read'    => 0,
				'created_at' => current_time( 'mysql' ),
			)
		);

		return $wpdb->insert_id;
	}

	public static function generate_automated_insights( $user_id ) {
		$income    = WealthOS_Income::get_summary( $user_id );
		$expenses  = WealthOS_Expenses::get_summary( $user_id );
		$cash_flow = WealthOS_Calculations::calculate_cash_flow( $income['monthly_total'], $expenses['monthly_total'] );
		$emergency = WealthOS_Emergency_Fund::get_status( $user_id, $expenses['essential_monthly'] );

		if ( $cash_flow['savings_rate'] >= 20 ) {
			self::add_notification(
				$user_id,
				__( 'Great Savings Rate!', 'wealthos' ),
				sprintf( __( 'Your current savings rate is %s%%. Outstanding progress toward long-term wealth building!', 'wealthos' ), $cash_flow['savings_rate'] ),
				'success'
			);
		}

		if ( $emergency['percentage_completed'] >= 50 && $emergency['percentage_completed'] < 100 ) {
			self::add_notification(
				$user_id,
				__( 'Emergency Fund Milestone', 'wealthos' ),
				sprintf( __( 'Your emergency reserve has reached %s%% of your target goal.', 'wealthos' ), $emergency['percentage_completed'] ),
				'info'
			);
		}
	}
}

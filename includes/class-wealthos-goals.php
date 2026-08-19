<?php
/**
 * Financial Goals Module for WealthOS.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WealthOS_Goals {

	public static function get_all( $user_id ) {
		global $wpdb;
		return $wpdb->get_results(
			$wpdb->prepare( "SELECT * FROM {$wpdb->prefix}wealthos_goals WHERE user_id = %d ORDER BY created_at DESC", $user_id ),
			ARRAY_A
		);
	}

	public static function save( $user_id, $data ) {
		global $wpdb;

		$id       = isset( $data['id'] ) ? intval( $data['id'] ) : 0;
		$row_data = array(
			'user_id'              => $user_id,
			'title'                => WealthOS_Security::sanitize_text( $data['title'] ?? '' ),
			'target_amount'        => WealthOS_Security::sanitize_float( $data['target_amount'] ?? 0 ),
			'current_amount'       => WealthOS_Security::sanitize_float( $data['current_amount'] ?? 0 ),
			'deadline'             => WealthOS_Security::sanitize_date( $data['deadline'] ?? null ),
			'priority'             => WealthOS_Security::sanitize_text( $data['priority'] ?? 'medium' ),
			'monthly_contribution' => WealthOS_Security::sanitize_float( $data['monthly_contribution'] ?? 0 ),
			'category'             => WealthOS_Security::sanitize_text( $data['category'] ?? 'wealth' ),
			'status'               => WealthOS_Security::sanitize_text( $data['status'] ?? 'active' ),
			'notes'                => WealthOS_Security::sanitize_textarea( $data['notes'] ?? '' ),
		);

		if ( $id > 0 ) {
			$wpdb->update( $wpdb->prefix . 'wealthos_goals', $row_data, array( 'id' => $id, 'user_id' => $user_id ) );
			return $id;
		} else {
			$wpdb->insert( $wpdb->prefix . 'wealthos_goals', $row_data );
			return $wpdb->insert_id;
		}
	}
}

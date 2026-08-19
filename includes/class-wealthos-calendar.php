<?php
/**
 * Financial Calendar Module for WealthOS.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WealthOS_Calendar {

	public static function get_events( $user_id ) {
		global $wpdb;
		return $wpdb->get_results(
			$wpdb->prepare( "SELECT * FROM {$wpdb->prefix}wealthos_calendar WHERE user_id = %d ORDER BY due_date ASC", $user_id ),
			ARRAY_A
		);
	}

	public static function save( $user_id, $data ) {
		global $wpdb;

		$id       = isset( $data['id'] ) ? intval( $data['id'] ) : 0;
		$row_data = array(
			'user_id'    => $user_id,
			'title'      => WealthOS_Security::sanitize_text( $data['title'] ?? '' ),
			'event_type' => WealthOS_Security::sanitize_text( $data['event_type'] ?? 'bill' ),
			'amount'     => WealthOS_Security::sanitize_float( $data['amount'] ?? 0 ),
			'due_date'   => WealthOS_Security::sanitize_date( $data['due_date'] ?? current_time( 'Y-m-d' ) ),
			'status'     => WealthOS_Security::sanitize_text( $data['status'] ?? 'pending' ),
			'notes'      => WealthOS_Security::sanitize_textarea( $data['notes'] ?? '' ),
		);

		if ( $id > 0 ) {
			$wpdb->update( $wpdb->prefix . 'wealthos_calendar', $row_data, array( 'id' => $id, 'user_id' => $user_id ) );
			return $id;
		} else {
			$wpdb->insert( $wpdb->prefix . 'wealthos_calendar', $row_data );
			return $wpdb->insert_id;
		}
	}
}

<?php
/**
 * Action Center Module ("What Should I Do Next?") for WealthOS.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WealthOS_Action_Center {

	public static function get_actions( $user_id ) {
		global $wpdb;

		$custom_actions = $wpdb->get_results(
			$wpdb->prepare( "SELECT * FROM {$wpdb->prefix}wealthos_actions WHERE user_id = %d ORDER BY is_completed ASC, priority DESC", $user_id ),
			ARRAY_A
		);

		$system_actions = self::generate_system_actions( $user_id );

		return array(
			'custom_actions' => $custom_actions,
			'system_actions' => $system_actions,
		);
	}

	public static function save( $user_id, $data ) {
		global $wpdb;

		$id       = isset( $data['id'] ) ? intval( $data['id'] ) : 0;
		$row_data = array(
			'user_id'          => $user_id,
			'title'            => WealthOS_Security::sanitize_text( $data['title'] ?? '' ),
			'category'         => WealthOS_Security::sanitize_text( $data['category'] ?? 'general' ),
			'priority'         => WealthOS_Security::sanitize_text( $data['priority'] ?? 'medium' ),
			'difficulty'       => WealthOS_Security::sanitize_text( $data['difficulty'] ?? 'medium' ),
			'estimated_impact' => WealthOS_Security::sanitize_text( $data['estimated_impact'] ?? 'moderate' ),
			'deadline'         => WealthOS_Security::sanitize_date( $data['deadline'] ?? null ),
			'is_completed'     => isset( $data['is_completed'] ) ? ( $data['is_completed'] ? 1 : 0 ) : 0,
			'notes'            => WealthOS_Security::sanitize_textarea( $data['notes'] ?? '' ),
		);

		if ( $id > 0 ) {
			$wpdb->update( $wpdb->prefix . 'wealthos_actions', $row_data, array( 'id' => $id, 'user_id' => $user_id ) );
			return $id;
		} else {
			$wpdb->insert( $wpdb->prefix . 'wealthos_actions', $row_data );
			return $wpdb->insert_id;
		}
	}

	private static function generate_system_actions( $user_id ) {
		$bottleneck = WealthOS_Bottleneck::identify( $user_id );
		$actions    = array();

		foreach ( $bottleneck['recommendations'] as $index => $rec ) {
			$actions[] = array(
				'title'            => $rec,
				'reason'           => sprintf( __( 'Identified via bottleneck analysis: %s', 'wealthos' ), $bottleneck['title'] ),
				'priority'         => $index === 0 ? 'High' : 'Medium',
				'difficulty'       => 'Moderate',
				'estimated_impact' => 'High',
			);
		}

		return $actions;
	}
}

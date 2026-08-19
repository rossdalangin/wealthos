<?php
/**
 * Risk Management Module for WealthOS.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WealthOS_Risk {

	public static function get_default_checklist() {
		return array(
			array( 'category' => 'Cash Risk', 'item_key' => 'emergency_reserve', 'title' => __( 'Maintain 3–6 months essential expense reserve', 'wealthos' ) ),
			array( 'category' => 'Debt Risk', 'item_key' => 'high_interest_debt', 'title' => __( 'Eliminate high-interest credit card/consumer debt (>10%)', 'wealthos' ) ),
			array( 'category' => 'Income Risk', 'item_key' => 'income_diversity', 'title' => __( 'Maintain multiple or resilient income streams', 'wealthos' ) ),
			array( 'category' => 'Investment Concentration', 'item_key' => 'asset_diversification', 'title' => __( 'Avoid >70% concentration in a single stock or asset class', 'wealthos' ) ),
			array( 'category' => 'Insurance', 'item_key' => 'health_insurance', 'title' => __( 'Maintain active health and disability protection', 'wealthos' ) ),
			array( 'category' => 'Insurance', 'item_key' => 'life_insurance', 'title' => __( 'Maintain term life insurance if dependents rely on your income', 'wealthos' ) ),
			array( 'category' => 'Cybersecurity', 'item_key' => 'passwords_2fa', 'title' => __( 'Enable 2FA and unique passwords on all financial accounts', 'wealthos' ) ),
			array( 'category' => 'Legal & Estate', 'item_key' => 'will_beneficiaries', 'title' => __( 'Keep primary account beneficiary designations updated', 'wealthos' ) ),
		);
	}

	public static function get_summary( $user_id ) {
		global $wpdb;

		$stored = $wpdb->get_results(
			$wpdb->prepare( "SELECT * FROM {$wpdb->prefix}wealthos_risk_checklist WHERE user_id = %d", $user_id ),
			ARRAY_A
		);

		$stored_map = array();
		foreach ( $stored as $s ) {
			$stored_map[ $s['item_key'] ] = $s;
		}

		$defaults = self::get_default_checklist();
		$checklist = array();
		$complete_count = 0;
		$total_count    = count( $defaults );

		foreach ( $defaults as $def ) {
			$key = $def['item_key'];
			$status = isset( $stored_map[ $key ] ) ? $stored_map[ $key ]['status'] : 'needs_attention';
			$notes  = isset( $stored_map[ $key ] ) ? $stored_map[ $key ]['notes'] : '';

			if ( $status === 'complete' ) {
				$complete_count++;
			}

			$checklist[] = array(
				'item_key' => $key,
				'category' => $def['category'],
				'title'    => $def['title'],
				'status'   => $status,
				'notes'    => $notes,
			);
		}

		$completion_pct = $total_count > 0 ? ( $complete_count / $total_count ) * 100 : 0;

		// Calculate Risk Level (0-100 risk score, lower is safer)
		$risk_score = round( 100 - $completion_pct );
		$risk_label = 'Low';
		if ( $risk_score > 70 ) {
			$risk_label = 'Critical';
		} elseif ( $risk_score > 40 ) {
			$risk_label = 'High';
		} elseif ( $risk_score > 20 ) {
			$risk_label = 'Moderate';
		}

		return array(
			'checklist'     => $checklist,
			'risk_score'    => $risk_score,
			'risk_label'    => $risk_label,
			'completed'     => $complete_count,
			'total'         => $total_count,
		);
	}

	public static function update_item( $user_id, $data ) {
		global $wpdb;

		$item_key = WealthOS_Security::sanitize_text( $data['item_key'] ?? '' );
		$status   = WealthOS_Security::sanitize_text( $data['status'] ?? 'needs_attention' );
		$notes    = WealthOS_Security::sanitize_textarea( $data['notes'] ?? '' );
		$category = WealthOS_Security::sanitize_text( $data['category'] ?? 'General' );
		$title    = WealthOS_Security::sanitize_text( $data['title'] ?? 'Checklist Item' );

		if ( empty( $item_key ) ) {
			return false;
		}

		$existing_id = $wpdb->get_var(
			$wpdb->prepare( "SELECT id FROM {$wpdb->prefix}wealthos_risk_checklist WHERE user_id = %d AND item_key = %s", $user_id, $item_key )
		);

		$row_data = array(
			'user_id'  => $user_id,
			'item_key' => $item_key,
			'category' => $category,
			'title'    => $title,
			'status'   => $status,
			'notes'    => $notes,
		);

		if ( $existing_id ) {
			$wpdb->update( $wpdb->prefix . 'wealthos_risk_checklist', $row_data, array( 'id' => $existing_id ) );
		} else {
			$wpdb->insert( $wpdb->prefix . 'wealthos_risk_checklist', $row_data );
		}

		return true;
	}
}

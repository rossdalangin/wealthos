<?php
/**
 * Savings Module for WealthOS.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WealthOS_Savings {

	public static function get_all( $user_id ) {
		global $wpdb;
		return $wpdb->get_results(
			$wpdb->prepare( "SELECT * FROM {$wpdb->prefix}wealthos_savings WHERE user_id = %d ORDER BY created_at DESC", $user_id ),
			ARRAY_A
		);
	}

	public static function save( $user_id, $data ) {
		global $wpdb;

		$id       = isset( $data['id'] ) ? intval( $data['id'] ) : 0;
		$row_data = array(
			'user_id'              => $user_id,
			'goal_name'            => WealthOS_Security::sanitize_text( $data['goal_name'] ?? '' ),
			'target_amount'        => WealthOS_Security::sanitize_float( $data['target_amount'] ?? 0 ),
			'current_amount'       => WealthOS_Security::sanitize_float( $data['current_amount'] ?? 0 ),
			'deadline'             => WealthOS_Security::sanitize_date( $data['deadline'] ?? null ),
			'monthly_contribution' => WealthOS_Security::sanitize_float( $data['monthly_contribution'] ?? 0 ),
			'category'             => WealthOS_Security::sanitize_text( $data['category'] ?? 'general' ),
			'priority'             => WealthOS_Security::sanitize_text( $data['priority'] ?? 'medium' ),
			'status'               => WealthOS_Security::sanitize_text( $data['status'] ?? 'active' ),
		);

		if ( $id > 0 ) {
			$wpdb->update( $wpdb->prefix . 'wealthos_savings', $row_data, array( 'id' => $id, 'user_id' => $user_id ) );
			return $id;
		} else {
			$wpdb->insert( $wpdb->prefix . 'wealthos_savings', $row_data );
			return $wpdb->insert_id;
		}
	}

	public static function get_summary( $user_id ) {
		$goals = self::get_all( $user_id );

		$total_saved                = 0.0;
		$total_target               = 0.0;
		$total_planned_contribution = 0.0;
		$processed_goals            = array();

		foreach ( $goals as $g ) {
			$cur      = (float) $g['current_amount'];
			$tgt      = (float) $g['target_amount'];
			$contrib  = (float) $g['monthly_contribution'];
			$deadline = $g['deadline'];

			$total_saved                += $cur;
			$total_target               += $tgt;
			$total_planned_contribution += $contrib;

			// Calculate required monthly contribution based on deadline
			$req_monthly = 0.0;
			if ( ! empty( $deadline ) && $tgt > $cur ) {
				$now        = new DateTime();
				$due        = new DateTime( $deadline );
				$interval   = $now->diff( $due );
				$months_left = ( $interval->y * 12 ) + $interval->m;
				if ( $months_left > 0 ) {
					$req_monthly = ( $tgt - $cur ) / $months_left;
				} else {
					$req_monthly = $tgt - $cur;
				}
			}

			$g['required_monthly'] = round( $req_monthly, 2 );
			$g['progress_pct']     = $tgt > 0 ? round( min( 100, ( $cur / $tgt ) * 100 ), 1 ) : 100;
			$processed_goals[]     = $g;
		}

		return array(
			'goals'                      => $processed_goals,
			'total_saved'                => round( $total_saved, 2 ),
			'total_target'               => round( $total_target, 2 ),
			'total_planned_contribution' => round( $total_planned_contribution, 2 ),
		);
	}
}

<?php
/**
 * Net Worth Engine for WealthOS.
 * Net Worth = Total Assets − Total Liabilities.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WealthOS_Net_Worth {

	public static function get_current_net_worth( $user_id ) {
		// Calculate Assets: Investments + Productive Assets + Emergency Savings
		$investments_sum = WealthOS_Investments::get_summary( $user_id );
		$assets_sum      = WealthOS_Assets::get_summary( $user_id );
		$savings_sum     = WealthOS_Savings::get_summary( $user_id );

		$total_assets = $investments_sum['total_portfolio_value'] + $assets_sum['total_asset_value'] + $savings_sum['total_saved'];

		// Calculate Liabilities: Total Debts
		$debt_sum          = WealthOS_Debt::get_summary( $user_id );
		$total_liabilities = $debt_sum['total_debt'];

		$current_net_worth = $total_assets - $total_liabilities;

		// Get Previous Snapshot
		$history  = self::get_history( $user_id, 2 );
		$prev_nw  = count( $history ) > 1 ? (float) $history[1]['net_worth'] : $current_net_worth;
		$nw_change = $current_net_worth - $prev_nw;
		$nw_pct   = $prev_nw != 0 ? ( $nw_change / abs( $prev_nw ) ) * 100 : 0.0;

		return array(
			'total_assets'      => round( $total_assets, 2 ),
			'total_liabilities' => round( $total_liabilities, 2 ),
			'net_worth'         => round( $current_net_worth, 2 ),
			'previous_net_worth'=> round( $prev_nw, 2 ),
			'net_worth_change'  => round( $nw_change, 2 ),
			'net_worth_growth_pct' => round( $nw_pct, 1 ),
		);
	}

	public static function create_snapshot( $user_id, $notes = '' ) {
		global $wpdb;

		$nw_data = self::get_current_net_worth( $user_id );
		$today   = current_time( 'Y-m-d' );

		// Check if snapshot exists for today
		$existing_id = $wpdb->get_var(
			$wpdb->prepare(
				"SELECT id FROM {$wpdb->prefix}wealthos_net_worth_snapshots WHERE user_id = %d AND snapshot_date = %s",
				$user_id,
				$today
			)
		);

		$data = array(
			'user_id'           => $user_id,
			'snapshot_date'     => $today,
			'total_assets'      => $nw_data['total_assets'],
			'total_liabilities' => $nw_data['total_liabilities'],
			'net_worth'         => $nw_data['net_worth'],
			'notes'             => WealthOS_Security::sanitize_textarea( $notes ),
		);

		if ( $existing_id ) {
			$wpdb->update( $wpdb->prefix . 'wealthos_net_worth_snapshots', $data, array( 'id' => $existing_id ) );
			return $existing_id;
		} else {
			$wpdb->insert( $wpdb->prefix . 'wealthos_net_worth_snapshots', $data );
			return $wpdb->insert_id;
		}
	}

	public static function get_history( $user_id, $limit = 12 ) {
		global $wpdb;
		return $wpdb->get_results(
			$wpdb->prepare(
				"SELECT * FROM {$wpdb->prefix}wealthos_net_worth_snapshots WHERE user_id = %d ORDER BY snapshot_date DESC LIMIT %d",
				$user_id,
				$limit
			),
			ARRAY_A
		);
	}
}

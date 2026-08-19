<?php
/**
 * Investments Module for WealthOS.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WealthOS_Investments {

	public static function get_all( $user_id ) {
		global $wpdb;
		return $wpdb->get_results(
			$wpdb->prepare( "SELECT * FROM {$wpdb->prefix}wealthos_investments WHERE user_id = %d ORDER BY current_value DESC", $user_id ),
			ARRAY_A
		);
	}

	public static function save( $user_id, $data ) {
		global $wpdb;

		$id       = isset( $data['id'] ) ? intval( $data['id'] ) : 0;
		$row_data = array(
			'user_id'              => $user_id,
			'name'                 => WealthOS_Security::sanitize_text( $data['name'] ?? '' ),
			'asset_class'          => WealthOS_Security::sanitize_text( $data['asset_class'] ?? 'Stocks' ),
			'quantity'             => WealthOS_Security::sanitize_float( $data['quantity'] ?? 1.0 ),
			'purchase_price'       => WealthOS_Security::sanitize_float( $data['purchase_price'] ?? 0 ),
			'current_value'        => WealthOS_Security::sanitize_float( $data['current_value'] ?? 0 ),
			'monthly_contribution' => WealthOS_Security::sanitize_float( $data['monthly_contribution'] ?? 0 ),
			'account_type'         => WealthOS_Security::sanitize_text( $data['account_type'] ?? 'Brokerage' ),
		);

		if ( $id > 0 ) {
			$wpdb->update( $wpdb->prefix . 'wealthos_investments', $row_data, array( 'id' => $id, 'user_id' => $user_id ) );
			return $id;
		} else {
			$wpdb->insert( $wpdb->prefix . 'wealthos_investments', $row_data );
			return $wpdb->insert_id;
		}
	}

	public static function get_summary( $user_id ) {
		$items = self::get_all( $user_id );

		$total_portfolio_value = 0.0;
		$total_cost_basis      = 0.0;
		$monthly_investing     = 0.0;
		$allocation            = array();

		foreach ( $items as $item ) {
			$cur_val = (float) $item['current_value'];
			$cost    = ( (float) $item['quantity'] ) * ( (float) $item['purchase_price'] );
			$contrib = (float) $item['monthly_contribution'];

			$total_portfolio_value += $cur_val;
			$total_cost_basis      += $cost;
			$monthly_investing     += $contrib;

			$class = $item['asset_class'];
			if ( ! isset( $allocation[ $class ] ) ) {
				$allocation[ $class ] = 0.0;
			}
			$allocation[ $class ] += $cur_val;
		}

		$gain_loss        = $total_portfolio_value - $total_cost_basis;
		$gain_loss_pct    = $total_cost_basis > 0 ? ( $gain_loss / $total_cost_basis ) * 100 : 0.0;

		// Calculate Allocation Percentages
		$allocation_pct = array();
		if ( $total_portfolio_value > 0 ) {
			foreach ( $allocation as $class => $val ) {
				$allocation_pct[ $class ] = round( ( $val / $total_portfolio_value ) * 100, 1 );
			}
		}

		// Educational Concentration Warning
		$warnings = array();
		foreach ( $allocation_pct as $class => $pct ) {
			if ( $pct >= 75 && count( $allocation_pct ) > 1 ) {
				$warnings[] = sprintf( __( 'Concentration Alert: Asset class "%s" makes up %s%% of your portfolio. Consider evaluating whether this level of concentration aligns with your risk tolerance.', 'wealthos' ), $class, $pct );
			}
		}

		return array(
			'items'                 => $items,
			'total_portfolio_value' => round( $total_portfolio_value, 2 ),
			'total_cost_basis'      => round( $total_cost_basis, 2 ),
			'gain_loss'             => round( $gain_loss, 2 ),
			'gain_loss_pct'         => round( $gain_loss_pct, 1 ),
			'monthly_investing'     => round( $monthly_investing, 2 ),
			'allocation_pct'        => $allocation_pct,
			'warnings'              => $warnings,
		);
	}
}

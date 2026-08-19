<?php
/**
 * Visual Wealth Roadmap Module for WealthOS.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WealthOS_Roadmap {

	public static function get_roadmap_stages( $user_id ) {
		$income      = WealthOS_Income::get_summary( $user_id );
		$expenses    = WealthOS_Expenses::get_summary( $user_id );
		$cash_flow   = WealthOS_Calculations::calculate_cash_flow( $income['monthly_total'], $expenses['monthly_total'] );
		$emergency   = WealthOS_Emergency_Fund::get_status( $user_id, $expenses['essential_monthly'] );
		$debt        = WealthOS_Debt::get_summary( $user_id, $income['monthly_total'] );
		$investments = WealthOS_Investments::get_summary( $user_id );
		$assets      = WealthOS_Assets::get_summary( $user_id );

		$stages = array(
			array(
				'step'        => 1,
				'title'       => __( 'Know Your Numbers', 'wealthos' ),
				'description' => __( 'Log all income streams, monthly expenses, and existing account balances.', 'wealthos' ),
				'is_completed'=> $income['monthly_total'] > 0 || $expenses['monthly_total'] > 0,
			),
			array(
				'step'        => 2,
				'title'       => __( 'Control Cash Flow', 'wealthos' ),
				'description' => __( 'Ensure income exceeds monthly expenses and generates a positive surplus.', 'wealthos' ),
				'is_completed'=> $cash_flow['surplus'] > 0,
			),
			array(
				'step'        => 3,
				'title'       => __( 'Starter Emergency Reserve', 'wealthos' ),
				'description' => __( 'Accumulate at least $1,000 in liquid emergency protection.', 'wealthos' ),
				'is_completed'=> $emergency['current_amount'] >= 1000,
			),
			array(
				'step'        => 4,
				'title'       => __( 'High-Interest Debt Reduction', 'wealthos' ),
				'description' => __( 'Eliminate high-interest consumer/credit card debt.', 'wealthos' ),
				'is_completed'=> $debt['total_debt'] == 0 || $debt['weighted_avg_rate'] < 8.0,
			),
			array(
				'step'        => 5,
				'title'       => __( 'Full Emergency Protection', 'wealthos' ),
				'description' => __( 'Build a 3–6 month essential expense reserve.', 'wealthos' ),
				'is_completed'=> $emergency['percentage_completed'] >= 100,
			),
			array(
				'step'        => 6,
				'title'       => __( 'Consistent Saving & Long-Term Investing', 'wealthos' ),
				'description' => __( 'Maintain 15%+ savings rate and invest regularly in productive accounts.', 'wealthos' ),
				'is_completed'=> $investments['total_portfolio_value'] > 10000,
			),
			array(
				'step'        => 7,
				'title'       => __( 'Productive Asset Building & Growth', 'wealthos' ),
				'description' => __( 'Acquire cash-flowing productive assets and business streams.', 'wealthos' ),
				'is_completed'=> $assets['total_asset_value'] > 25000,
			),
		);

		// Determine Current User Stage
		$current_step = 1;
		foreach ( $stages as $st ) {
			if ( $st['is_completed'] ) {
				$current_step = $st['step'];
			} else {
				break;
			}
		}

		return array(
			'stages'        => $stages,
			'current_step'  => $current_step,
			'total_steps'   => count( $stages ),
		);
	}
}

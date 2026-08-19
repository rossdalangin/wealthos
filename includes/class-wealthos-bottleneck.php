<?php
/**
 * Financial Bottleneck Engine for WealthOS.
 * Identifies the user's primary financial bottleneck and provides action steps.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WealthOS_Bottleneck {

	public static function identify( $user_id ) {
		$income      = WealthOS_Income::get_summary( $user_id );
		$expenses    = WealthOS_Expenses::get_summary( $user_id );
		$cash_flow   = WealthOS_Calculations::calculate_cash_flow( $income['monthly_total'], $expenses['monthly_total'] );
		$emergency   = WealthOS_Emergency_Fund::get_status( $user_id, $expenses['essential_monthly'] );
		$debt        = WealthOS_Debt::get_summary( $user_id, $income['monthly_total'] );
		$investments = WealthOS_Investments::get_summary( $user_id );

		// Bottleneck Priority Cascade
		if ( $income['monthly_total'] <= 0 ) {
			return array(
				'title'          => __( 'No Active Income Recorded', 'wealthos' ),
				'description'    => __( 'You currently have no income sources logged in WealthOS.', 'wealthos' ),
				'recommendations'=> array(
					__( 'Log primary income streams or freelancing income.', 'wealthos' ),
					__( 'Define an income growth plan to establish cash flow.', 'wealthos' ),
				),
			);
		}

		if ( $cash_flow['is_deficit'] ) {
			return array(
				'title'          => __( 'Cash Flow Deficit', 'wealthos' ),
				'description'    => __( 'Your monthly expenses exceed your monthly income. Operating in deficit diminishes savings and forces debt accumulation.', 'wealthos' ),
				'recommendations'=> array(
					__( 'Audit non-essential expenses and discretionary spending immediately.', 'wealthos' ),
					__( 'Set strict monthly expense thresholds for top spending categories.', 'wealthos' ),
					__( 'Explore immediate opportunities to increase income.', 'wealthos' ),
				),
			);
		}

		if ( $debt['total_debt'] > 0 && $debt['weighted_avg_rate'] >= 10.0 ) {
			return array(
				'title'          => __( 'High-Interest Debt Burden', 'wealthos' ),
				'description'    => sprintf( __( 'You have high-interest debt with a weighted average rate of %s%%. Carrying high-interest debt consumes financial surplus.', 'wealthos' ), $debt['weighted_avg_rate'] ),
				'recommendations'=> array(
					__( 'Maintain minimum payments across all debts.', 'wealthos' ),
					__( 'Direct all available monthly surplus toward the highest-interest debt (Debt Avalanche method).', 'wealthos' ),
					__( 'Avoid adding new high-interest credit card debt.', 'wealthos' ),
				),
			);
		}

		if ( $emergency['percentage_completed'] < 50 ) {
			return array(
				'title'          => __( 'Insufficient Emergency Reserve', 'wealthos' ),
				'description'    => __( 'Your emergency reserve covers less than 50% of your target essential expenses.', 'wealthos' ),
				'recommendations'=> array(
					__( 'Direct a fixed monthly contribution into a dedicated liquid emergency fund.', 'wealthos' ),
					__( 'Build a baseline $1,000 reserve before allocating extra funds to long-term investments.', 'wealthos' ),
				),
			);
		}

		if ( $cash_flow['savings_rate'] < 15 ) {
			return array(
				'title'          => __( 'Low Savings Rate', 'wealthos' ),
				'description'    => sprintf( __( 'Your current savings rate is %s%%. Aiming for 15–20%+ helps accelerate long-term compounding.', 'wealthos' ), $cash_flow['savings_rate'] ),
				'recommendations'=> array(
					__( 'Automate monthly transfers to savings upon receiving income.', 'wealthos' ),
					__( 'Identify 1-2 expense categories to trim by 10%.', 'wealthos' ),
				),
			);
		}

		if ( $investments['total_portfolio_value'] <= 0 ) {
			return array(
				'title'          => __( 'Lack of Long-Term Investing', 'wealthos' ),
				'description'    => __( 'You have established cash-flow surplus and emergency protection, but have no active investment accounts tracked.', 'wealthos' ),
				'recommendations'=> array(
					__( 'Set up consistent monthly automated contributions to broad-market investments.', 'wealthos' ),
					__( 'Review compound growth projections in the WealthOS Compounding Calculator.', 'wealthos' ),
				),
			);
		}

		return array(
			'title'          => __( 'Income & Net Worth Acceleration', 'wealthos' ),
			'description'    => __( 'Your financial fundamentals are healthy! Your primary focus is scaling income streams and expanding productive assets.', 'wealthos' ),
			'recommendations'=> array(
				__( 'Focus on high-leverage business or career growth activities.', 'wealthos' ),
				__( 'Acquire or build income-producing productive assets.', 'wealthos' ),
			),
		);
	}
}

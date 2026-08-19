<?php
/**
 * Net Worth Scenario Planner & "What-If" Calculator for WealthOS.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WealthOS_Scenario_Planner {

	public static function simulate_scenarios( $user_id, $modifications = array() ) {
		$current_income   = WealthOS_Income::get_summary( $user_id );
		$current_expenses = WealthOS_Expenses::get_summary( $user_id );
		$current_debt     = WealthOS_Debt::get_summary( $user_id );
		$current_nw       = WealthOS_Net_Worth::get_current_net_worth( $user_id );

		$add_savings_monthly   = WealthOS_Security::sanitize_float( $modifications['add_savings_monthly'] ?? 0 );
		$add_income_monthly    = WealthOS_Security::sanitize_float( $modifications['add_income_monthly'] ?? 0 );
		$reduce_expense_monthly = WealthOS_Security::sanitize_float( $modifications['reduce_expense_monthly'] ?? 0 );
		$extra_debt_payment    = WealthOS_Security::sanitize_float( $modifications['extra_debt_payment'] ?? 0 );
		$add_investment_monthly = WealthOS_Security::sanitize_float( $modifications['add_investment_monthly'] ?? 0 );

		// Calculated monthly baseline surplus adjustment
		$baseline_monthly_surplus = $current_income['monthly_total'] - $current_expenses['monthly_total'];
		$new_monthly_surplus      = $baseline_monthly_surplus + $add_income_monthly + $reduce_expense_monthly - $add_savings_monthly - $add_investment_monthly - $extra_debt_payment;

		// 5-Year Projection Simulation
		$years               = 5;
		$current_start_nw    = $current_nw['net_worth'];
		$projected_nw_baseline = $current_start_nw + ( $baseline_monthly_surplus * 12 * $years );

		// Impact of additional monthly wealth builder activities (assuming conservative 6% annual return on extra investments/savings)
		$extra_monthly_alloc = $add_savings_monthly + $add_investment_monthly + $extra_debt_payment + max( 0, $add_income_monthly + $reduce_expense_monthly );
		$sim_compounding     = WealthOS_Compounding::calculate( 0, $extra_monthly_alloc, 6.0, $years );

		$projected_nw_scenario = $projected_nw_baseline + $sim_compounding['estimated_final_value'];
		$wealth_gain           = $projected_nw_scenario - $projected_nw_baseline;

		return array(
			'current_net_worth'      => round( $current_start_nw, 2 ),
			'baseline_5yr_net_worth' => round( $projected_nw_baseline, 2 ),
			'scenario_5yr_net_worth' => round( $projected_nw_scenario, 2 ),
			'estimated_additional_wealth' => round( $wealth_gain, 2 ),
			'assumptions_note'       => __( 'Calculated assuming a conservative 6.0% annual compound growth rate on additional investment surplus over 5 years.', 'wealthos' ),
		);
	}
}

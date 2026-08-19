<?php
/**
 * Reviews & Reports Module (Monthly Financial Review & Annual Wealth Report) for WealthOS.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WealthOS_Reports {

	public static function generate_monthly_review( $user_id ) {
		$income      = WealthOS_Income::get_summary( $user_id );
		$expenses    = WealthOS_Expenses::get_summary( $user_id );
		$cash_flow   = WealthOS_Calculations::calculate_cash_flow( $income['monthly_total'], $expenses['monthly_total'] );
		$debt        = WealthOS_Debt::get_summary( $user_id, $income['monthly_total'] );
		$savings     = WealthOS_Savings::get_summary( $user_id );
		$investments = WealthOS_Investments::get_summary( $user_id );
		$assets      = WealthOS_Assets::get_summary( $user_id );
		$net_worth   = WealthOS_Net_Worth::get_current_net_worth( $user_id );
		$bottleneck  = WealthOS_Bottleneck::identify( $user_id );

		return array(
			'review_month'   => current_time( 'F Y' ),
			'income_total'   => $income['monthly_total'],
			'expenses_total' => $expenses['monthly_total'],
			'surplus'        => $cash_flow['surplus'],
			'savings_rate'   => $cash_flow['savings_rate'],
			'debt_total'     => $debt['total_debt'],
			'savings_total'  => $savings['total_saved'],
			'investing_total'=> $investments['total_portfolio_value'],
			'assets_total'   => $assets['total_asset_value'],
			'net_worth'      => $net_worth['net_worth'],
			'net_worth_change'=> $net_worth['net_worth_change'],
			'primary_issue'  => $bottleneck['title'],
			'top_actions'    => array_slice( $bottleneck['recommendations'], 0, 3 ),
		);
	}

	public static function generate_annual_report( $user_id ) {
		$m_review  = self::generate_monthly_review( $user_id );
		$business  = WealthOS_Business::get_summary( $user_id );
		$goals     = WealthOS_Goals::get_all( $user_id );
		$risk      = WealthOS_Risk::get_summary( $user_id );

		return array(
			'report_year'        => current_time( 'Y' ),
			'annual_income'      => round( $m_review['income_total'] * 12, 2 ),
			'annual_expenses'    => round( $m_review['expenses_total'] * 12, 2 ),
			'annual_savings'     => round( $m_review['surplus'] * 12, 2 ),
			'savings_rate'       => $m_review['savings_rate'],
			'debt_total'         => $m_review['debt_total'],
			'investments_total'  => $m_review['investing_total'],
			'assets_total'       => $m_review['assets_total'],
			'net_worth'          => $m_review['net_worth'],
			'business_summary'   => $business,
			'goals'              => $goals,
			'risk_summary'       => $risk,
			'disclaimer'         => WealthOS_Settings::get_setting( 'disclaimer_text' ),
		);
	}
}

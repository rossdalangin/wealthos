<?php
/**
 * Transparent Educational Wealth Score (0–100) for WealthOS.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WealthOS_Wealth_Score {

	public static function calculate( $user_id ) {
		$income      = WealthOS_Income::get_summary( $user_id );
		$expenses    = WealthOS_Expenses::get_summary( $user_id );
		$cash_flow   = WealthOS_Calculations::calculate_cash_flow( $income['monthly_total'], $expenses['monthly_total'] );
		$emergency   = WealthOS_Emergency_Fund::get_status( $user_id, $expenses['essential_monthly'] );
		$debt        = WealthOS_Debt::get_summary( $user_id, $income['monthly_total'] );
		$investments = WealthOS_Investments::get_summary( $user_id );
		$risk        = WealthOS_Risk::get_summary( $user_id );

		// Component 1: Cash Flow Health (0-20)
		$cf_score = 0;
		if ( $cash_flow['savings_rate'] >= 30 ) {
			$cf_score = 20;
		} elseif ( $cash_flow['savings_rate'] >= 20 ) {
			$cf_score = 16;
		} elseif ( $cash_flow['savings_rate'] >= 10 ) {
			$cf_score = 12;
		} elseif ( $cash_flow['surplus'] > 0 ) {
			$cf_score = 8;
		}

		// Component 2: Emergency Reserve (0-20)
		$em_score = 0;
		if ( $emergency['percentage_completed'] >= 100 ) {
			$em_score = 20;
		} elseif ( $emergency['percentage_completed'] >= 50 ) {
			$em_score = 14;
		} elseif ( $emergency['current_amount'] >= 1000 ) {
			$em_score = 8;
		}

		// Component 3: Debt Burden (0-20)
		$debt_score = 20;
		if ( $debt['total_debt'] > 0 ) {
			if ( $debt['debt_to_income_ratio'] > 40 ) {
				$debt_score = 5;
			} elseif ( $debt['debt_to_income_ratio'] > 20 ) {
				$debt_score = 10;
			} else {
				$debt_score = 15;
			}
		}

		// Component 4: Investment & Asset Building (0-20)
		$inv_score = 0;
		if ( $investments['total_portfolio_value'] > 50000 ) {
			$inv_score = 20;
		} elseif ( $investments['total_portfolio_value'] > 10000 ) {
			$inv_score = 15;
		} elseif ( $investments['total_portfolio_value'] > 0 ) {
			$inv_score = 10;
		}

		// Component 5: Risk Protection (0-20)
		$risk_score = round( ( 100 - $risk['risk_score'] ) / 5 );

		$total_score = $cf_score + $em_score + $debt_score + $inv_score + $risk_score;

		// Status Label
		$status = 'Needs Attention';
		if ( $total_score >= 85 ) {
			$status = 'Building';
		} elseif ( $total_score >= 70 ) {
			$status = 'Strong';
		} elseif ( $total_score >= 50 ) {
			$status = 'Stable';
		} elseif ( $total_score >= 30 ) {
			$status = 'Needs Attention';
		} else {
			$status = 'Critical';
		}

		return array(
			'score'               => $total_score,
			'status'              => $status,
			'cash_flow_score'     => $cf_score,
			'emergency_score'     => $em_score,
			'debt_score'          => $debt_score,
			'investing_score'     => $inv_score,
			'risk_protection_score' => $risk_score,
			'disclaimer'          => __( 'Educational score from 0–100 representing relative financial fundamentals. This is not a professional credit rating or financial diagnosis.', 'wealthos' ),
		);
	}
}

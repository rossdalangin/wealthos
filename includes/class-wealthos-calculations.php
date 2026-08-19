<?php
/**
 * Shared Financial Calculations helper for WealthOS.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WealthOS_Calculations {

	/**
	 * Convert any payment frequency to a monthly amount.
	 */
	public static function normalize_to_monthly( $amount, $frequency ) {
		$amount    = (float) $amount;
		$frequency = strtolower( trim( $frequency ) );

		switch ( $frequency ) {
			case 'daily':
				return $amount * 30.4167;
			case 'weekly':
				return $amount * ( 52 / 12 );
			case 'biweekly':
			case 'bi-weekly':
				return $amount * ( 26 / 12 );
			case 'semimonthly':
			case 'semi-monthly':
				return $amount * 2;
			case 'quarterly':
				return $amount / 3;
			case 'annually':
			case 'yearly':
			case 'annual':
				return $amount / 12;
			case 'one-time':
			case 'onetime':
				return 0.0;
			case 'monthly':
			default:
				return $amount;
		}
	}

	/**
	 * Calculate Cash Flow & Surplus/Deficit.
	 */
	public static function calculate_cash_flow( $monthly_income, $monthly_expenses ) {
		$monthly_income   = max( 0, (float) $monthly_income );
		$monthly_expenses = max( 0, (float) $monthly_expenses );
		$surplus          = $monthly_income - $monthly_expenses;

		$savings_rate = 0.0;
		if ( $monthly_income > 0 ) {
			$savings_rate = ( $surplus / $monthly_income ) * 100;
		}

		return array(
			'income'       => $monthly_income,
			'expenses'     => $monthly_expenses,
			'surplus'      => $surplus,
			'is_deficit'   => $surplus < 0,
			'savings_rate' => round( max( 0, $savings_rate ), 2 ),
		);
	}
}

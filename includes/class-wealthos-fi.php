<?php
/**
 * Financial Independence (FI) Module for WealthOS.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WealthOS_FI {

	public static function calculate_projections( $annual_expenses, $current_investments, $monthly_contribution, $assumed_return_pct = 7.0, $inflation_pct = 2.5, $target_age = 60, $current_age = 30 ) {
		$annual_expenses       = max( 0, (float) $annual_expenses );
		$current_investments   = max( 0, (float) $current_investments );
		$monthly_contribution = max( 0, (float) $monthly_contribution );
		$assumed_return_pct   = (float) $assumed_return_pct;
		$inflation_pct         = (float) $inflation_pct;
		$years                 = max( 1, intval( $target_age - $current_age ) );

		// Standard 25x Annual Expenses Rule for Financial Independence Target
		$fi_target_number = $annual_expenses * 25;

		// Net nominal return adjusted roughly for inflation
		$real_rate = ( ( 1 + ( $assumed_return_pct / 100 ) ) / ( 1 + ( $inflation_pct / 100 ) ) ) - 1;
		$monthly_rate = $real_rate / 12;

		$future_value = $current_investments * pow( 1 + $real_rate, $years );
		if ( $monthly_rate > 0 ) {
			$future_value += $monthly_contribution * ( ( pow( 1 + $monthly_rate, $years * 12 ) - 1 ) / $monthly_rate );
		} else {
			$future_value += $monthly_contribution * $years * 12;
		}

		$progress_pct = $fi_target_number > 0 ? min( 100, ( $future_value / $fi_target_number ) * 100 ) : 0;

		$disclaimer = __( 'Illustration only. Actual investment returns, inflation, taxes, fees, and future expenses may differ substantially.', 'wealthos' );

		return array(
			'annual_expenses'     => round( $annual_expenses, 2 ),
			'fi_target_number'    => round( $fi_target_number, 2 ),
			'projected_value'     => round( $future_value, 2 ),
			'years'               => $years,
			'progress_pct'        => round( $progress_pct, 1 ),
			'disclaimer'          => $disclaimer,
		);
	}
}

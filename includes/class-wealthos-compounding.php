<?php
/**
 * Compounding Calculator for WealthOS.
 * Separates Money Contributed from Estimated Investment Growth.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WealthOS_Compounding {

	public static function calculate( $initial_investment, $monthly_contribution, $annual_return_pct, $years ) {
		$initial_investment    = max( 0, (float) $initial_investment );
		$monthly_contribution = max( 0, (float) $monthly_contribution );
		$annual_return_pct    = (float) $annual_return_pct;
		$years                 = max( 1, intval( $years ) );

		$total_months = $years * 12;
		$monthly_rate = ( $annual_return_pct / 100 ) / 12;

		$contributions = $initial_investment + ( $monthly_contribution * $total_months );

		$future_value = $initial_investment * pow( 1 + $monthly_rate, $total_months );
		if ( $monthly_rate > 0 ) {
			$future_value += $monthly_contribution * ( ( pow( 1 + $monthly_rate, $total_months ) - 1 ) / $monthly_rate );
		} else {
			$future_value += $monthly_contribution * $total_months;
		}

		$estimated_growth = max( 0, $future_value - $contributions );

		return array(
			'initial_investment'   => round( $initial_investment, 2 ),
			'monthly_contribution' => round( $monthly_contribution, 2 ),
			'years'                => $years,
			'total_contributions'  => round( $contributions, 2 ),
			'estimated_growth'     => round( $estimated_growth, 2 ),
			'estimated_final_value'=> round( $future_value, 2 ),
		);
	}
}

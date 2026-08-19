<?php
/**
 * Emergency Fund Module for WealthOS.
 * Calculates multi-stage emergency protection (Starter, 1 Month, 3 Months, 6 Months).
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WealthOS_Emergency_Fund {

	public static function get_status( $user_id, $essential_monthly_expenses = null ) {
		if ( null === $essential_monthly_expenses ) {
			$exp_summary               = WealthOS_Expenses::get_summary( $user_id );
			$essential_monthly_expenses = $exp_summary['essential_monthly'];
		}

		// Get current emergency savings from Savings module
		$savings = WealthOS_Savings::get_all( $user_id );
		$current_emergency_savings = 0.0;

		foreach ( $savings as $s ) {
			if ( strtolower( $s['category'] ) === 'emergency' || stristitle_contains( $s['goal_name'], 'emergency' ) ) {
				$current_emergency_savings += (float) $s['current_amount'];
			}
		}

		$target_months = 6;
		$target_amount = $essential_monthly_expenses * $target_months;
		$pct_completed = $target_amount > 0 ? min( 100, ( $current_emergency_savings / $target_amount ) * 100 ) : 100;
		$remaining     = max( 0, $target_amount - $current_emergency_savings );

		// Determine Stage
		$stage = 'Stage 0: Unprotected';
		if ( $current_emergency_savings >= $essential_monthly_expenses * 6 && $essential_monthly_expenses > 0 ) {
			$stage = 'Stage 4: 6 Months Protection (Fully Funded)';
		} elseif ( $current_emergency_savings >= $essential_monthly_expenses * 3 && $essential_monthly_expenses > 0 ) {
			$stage = 'Stage 3: 3 Months Essential Reserve';
		} elseif ( $current_emergency_savings >= $essential_monthly_expenses && $essential_monthly_expenses > 0 ) {
			$stage = 'Stage 2: 1 Month Protection';
		} elseif ( $current_emergency_savings >= 1000 ) {
			$stage = 'Stage 1: Starter Emergency Reserve ($1,000)';
		}

		return array(
			'current_amount'             => round( $current_emergency_savings, 2 ),
			'essential_monthly_expenses' => round( $essential_monthly_expenses, 2 ),
			'target_months'              => $target_months,
			'target_amount'              => round( $target_amount, 2 ),
			'percentage_completed'       => round( $pct_completed, 1 ),
			'amount_remaining'           => round( $remaining, 2 ),
			'stage'                      => $stage,
		);
	}
}

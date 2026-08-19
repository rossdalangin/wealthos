<?php
/**
 * Budget Module for WealthOS.
 * Supports Zero-Based, Percentage-Based, and Custom Budgeting models.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WealthOS_Budget {

	public static function get_all( $user_id ) {
		global $wpdb;
		return $wpdb->get_results(
			$wpdb->prepare( "SELECT * FROM {$wpdb->prefix}wealthos_budgets WHERE user_id = %d", $user_id ),
			ARRAY_A
		);
	}

	public static function save_budgets( $user_id, $budgets ) {
		global $wpdb;

		// Clear existing budget allocations for user
		$wpdb->delete( $wpdb->prefix . 'wealthos_budgets', array( 'user_id' => $user_id ) );

		foreach ( $budgets as $b ) {
			$wpdb->insert(
				$wpdb->prefix . 'wealthos_budgets',
				array(
					'user_id'              => $user_id,
					'category'             => WealthOS_Security::sanitize_text( $b['category'] ?? 'General' ),
					'budget_type'          => WealthOS_Security::sanitize_text( $b['budget_type'] ?? 'custom' ),
					'allocated_amount'     => WealthOS_Security::sanitize_float( $b['allocated_amount'] ?? 0 ),
					'allocated_percentage' => WealthOS_Security::sanitize_float( $b['allocated_percentage'] ?? 0 ),
					'period'               => WealthOS_Security::sanitize_text( $b['period'] ?? 'monthly' ),
				)
			);
		}

		return true;
	}

	public static function get_budget_vs_actual( $user_id ) {
		$budgets         = self::get_all( $user_id );
		$expense_summary = WealthOS_Expenses::get_summary( $user_id );
		$actuals         = $expense_summary['by_category'];

		$comparison = array();

		// Index budgets
		$budget_map = array();
		foreach ( $budgets as $b ) {
			$budget_map[ $b['category'] ] = (float) $b['allocated_amount'];
		}

		// Combine categories
		$all_categories = array_unique( array_merge( array_keys( $budget_map ), array_keys( $actuals ) ) );

		foreach ( $all_categories as $cat ) {
			$budgeted   = isset( $budget_map[ $cat ] ) ? $budget_map[ $cat ] : 0.0;
			$actual     = isset( $actuals[ $cat ] ) ? $actuals[ $cat ] : 0.0;
			$difference = $budgeted - $actual;

			$status = 'on_track';
			if ( $actual > $budgeted && $budgeted > 0 ) {
				$status = 'over_budget';
			} elseif ( $actual <= $budgeted ) {
				$status = 'under_budget';
			}

			$comparison[] = array(
				'category'   => $cat,
				'budgeted'   => round( $budgeted, 2 ),
				'actual'     => round( $actual, 2 ),
				'difference' => round( $difference, 2 ),
				'status'     => $status,
			);
		}

		return $comparison;
	}
}

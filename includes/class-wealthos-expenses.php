<?php
/**
 * Expenses Module & Expense Control Engine for WealthOS.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WealthOS_Expenses {

	public static function get_all( $user_id ) {
		global $wpdb;
		return $wpdb->get_results(
			$wpdb->prepare( "SELECT * FROM {$wpdb->prefix}wealthos_expenses WHERE user_id = %d ORDER BY amount DESC", $user_id ),
			ARRAY_A
		);
	}

	public static function save( $user_id, $data ) {
		global $wpdb;

		$id       = isset( $data['id'] ) ? intval( $data['id'] ) : 0;
		$row_data = array(
			'user_id'      => $user_id,
			'name'         => WealthOS_Security::sanitize_text( $data['name'] ?? '' ),
			'category'     => WealthOS_Security::sanitize_text( $data['category'] ?? 'Other' ),
			'amount'       => WealthOS_Security::sanitize_float( $data['amount'] ?? 0 ),
			'frequency'    => WealthOS_Security::sanitize_text( $data['frequency'] ?? 'monthly' ),
			'is_essential' => isset( $data['is_essential'] ) ? ( $data['is_essential'] ? 1 : 0 ) : 1,
			'start_date'   => WealthOS_Security::sanitize_date( $data['start_date'] ?? null ),
			'end_date'     => WealthOS_Security::sanitize_date( $data['end_date'] ?? null ),
			'is_recurring' => isset( $data['is_recurring'] ) ? ( $data['is_recurring'] ? 1 : 0 ) : 1,
			'notes'        => WealthOS_Security::sanitize_textarea( $data['notes'] ?? '' ),
		);

		if ( $id > 0 ) {
			$wpdb->update( $wpdb->prefix . 'wealthos_expenses', $row_data, array( 'id' => $id, 'user_id' => $user_id ) );
			return $id;
		} else {
			$wpdb->insert( $wpdb->prefix . 'wealthos_expenses', $row_data );
			return $wpdb->insert_id;
		}
	}

	public static function get_summary( $user_id ) {
		$items = self::get_all( $user_id );

		$monthly_total     = 0.0;
		$essential_monthly = 0.0;
		$discretionary_m   = 0.0;
		$by_category       = array();

		foreach ( $items as $item ) {
			$m_amount = WealthOS_Calculations::normalize_to_monthly( $item['amount'], $item['frequency'] );
			$monthly_total += $m_amount;

			if ( ! empty( $item['is_essential'] ) ) {
				$essential_monthly += $m_amount;
			} else {
				$discretionary_m += $m_amount;
			}

			$cat = $item['category'];
			if ( ! isset( $by_category[ $cat ] ) ) {
				$by_category[ $cat ] = 0.0;
			}
			$by_category[ $cat ] += $m_amount;
		}

		$leaks = self::detect_expense_leaks( $items, $monthly_total, $discretionary_m, $by_category );

		return array(
			'items'             => $items,
			'monthly_total'     => round( $monthly_total, 2 ),
			'annual_total'      => round( $monthly_total * 12, 2 ),
			'essential_monthly' => round( $essential_monthly, 2 ),
			'discretionary_m'   => round( $discretionary_m, 2 ),
			'by_category'       => $by_category,
			'leaks'             => $leaks,
		);
	}

	/**
	 * Detect Expense Leaks using respectful, non-shaming phrasing.
	 */
	private static function detect_expense_leaks( $items, $total, $discretionary, $by_category ) {
		$leaks = array();

		// Check high discretionary ratio
		if ( $total > 0 && ( $discretionary / $total ) > 0.45 ) {
			$leaks[] = array(
				'title'          => __( 'Potential Improvement Area: Discretionary Spending', 'wealthos' ),
				'description'    => __( 'Discretionary spending represents more than 45% of total monthly expenses. Reviewing non-essential subscriptions or entertainment could unlock additional cash-flow surplus.', 'wealthos' ),
				'recommendation' => __( 'Consider setting a soft monthly threshold for discretionary categories.', 'wealthos' ),
			);
		}

		// Check multiple recurring subscriptions
		$subscriptions_count = 0;
		$sub_total           = 0.0;
		foreach ( $items as $item ) {
			if ( strtolower( $item['category'] ) === 'subscriptions' || stristitle_contains( $item['name'], 'subscription' ) ) {
				$subscriptions_count++;
				$sub_total += WealthOS_Calculations::normalize_to_monthly( $item['amount'], $item['frequency'] );
			}
		}

		if ( $subscriptions_count >= 3 ) {
			$leaks[] = array(
				'title'          => __( 'Potential Improvement Area: Recurring Subscriptions', 'wealthos' ),
				'description'    => sprintf( __( 'You currently have %d recurring subscription services totaling %s/month.', 'wealthos' ), $subscriptions_count, WealthOS_Settings::format_currency( $sub_total ) ),
				'recommendation' => __( 'Consider auditing active subscriptions to ensure every recurring service provides clear ongoing value.', 'wealthos' ),
			);
		}

		return $leaks;
	}
}

function stristitle_contains( $haystack, $needle ) {
	return stripos( $haystack, $needle ) !== false;
}

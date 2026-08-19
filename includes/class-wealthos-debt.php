<?php
/**
 * Debt Module & Debt Reduction Strategies (Avalanche vs Snowball) for WealthOS.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WealthOS_Debt {

	public static function get_all( $user_id ) {
		global $wpdb;
		return $wpdb->get_results(
			$wpdb->prepare( "SELECT * FROM {$wpdb->prefix}wealthos_debts WHERE user_id = %d ORDER BY interest_rate DESC", $user_id ),
			ARRAY_A
		);
	}

	public static function save( $user_id, $data ) {
		global $wpdb;

		$id       = isset( $data['id'] ) ? intval( $data['id'] ) : 0;
		$row_data = array(
			'user_id'           => $user_id,
			'name'              => WealthOS_Security::sanitize_text( $data['name'] ?? '' ),
			'balance'           => WealthOS_Security::sanitize_float( $data['balance'] ?? 0 ),
			'interest_rate'     => WealthOS_Security::sanitize_float( $data['interest_rate'] ?? 0 ),
			'minimum_payment'   => WealthOS_Security::sanitize_float( $data['minimum_payment'] ?? 0 ),
			'payment_frequency' => WealthOS_Security::sanitize_text( $data['payment_frequency'] ?? 'monthly' ),
			'due_date'          => WealthOS_Security::sanitize_date( $data['due_date'] ?? null ),
			'type'              => WealthOS_Security::sanitize_text( $data['type'] ?? 'credit_card' ),
		);

		if ( $id > 0 ) {
			$wpdb->update( $wpdb->prefix . 'wealthos_debts', $row_data, array( 'id' => $id, 'user_id' => $user_id ) );
			return $id;
		} else {
			$wpdb->insert( $wpdb->prefix . 'wealthos_debts', $row_data );
			return $wpdb->insert_id;
		}
	}

	public static function get_summary( $user_id, $monthly_income = 0.0 ) {
		$debts = self::get_all( $user_id );

		$total_debt       = 0.0;
		$total_min_pay    = 0.0;
		$weighted_int_sum = 0.0;

		foreach ( $debts as $d ) {
			$bal    = (float) $d['balance'];
			$rate   = (float) $d['interest_rate'];
			$min_p  = (float) $d['minimum_payment'];

			$total_debt    += $bal;
			$total_min_pay += $min_p;
			$weighted_int_sum += ( $bal * $rate );
		}

		$weighted_avg_rate = $total_debt > 0 ? ( $weighted_int_sum / $total_debt ) : 0.0;
		$dti_ratio         = $monthly_income > 0 ? ( $total_min_pay / $monthly_income ) * 100 : 0.0;

		$avalanche = self::calculate_payoff_strategy( $debts, 'avalanche' );
		$snowball  = self::calculate_payoff_strategy( $debts, 'snowball' );

		return array(
			'items'               => $debts,
			'total_debt'          => round( $total_debt, 2 ),
			'total_min_payment'   => round( $total_min_pay, 2 ),
			'weighted_avg_rate'   => round( $weighted_avg_rate, 2 ),
			'debt_to_income_ratio' => round( $dti_ratio, 1 ),
			'avalanche_plan'      => $avalanche,
			'snowball_plan'       => $snowball,
		);
	}

	/**
	 * Calculate Payoff timeline and estimated interest for Avalanche vs Snowball.
	 */
	public static function calculate_payoff_strategy( $debts, $strategy = 'avalanche', $extra_payment = 0.0 ) {
		if ( empty( $debts ) ) {
			return array( 'months' => 0, 'total_interest' => 0.0, 'order' => array() );
		}

		$sorted = $debts;
		if ( $strategy === 'avalanche' ) {
			// Highest interest rate first
			usort( $sorted, function( $a, $b ) {
				return $b['interest_rate'] <=> $a['interest_rate'];
			} );
		} else {
			// Smallest balance first (Snowball)
			usort( $sorted, function( $a, $b ) {
				return $a['balance'] <=> $b['balance'];
			} );
		}

		// Simple estimation simulation
		$balances = array();
		$rates    = array();
		$mins     = array();
		$names    = array();

		foreach ( $sorted as $index => $d ) {
			$balances[ $index ] = (float) $d['balance'];
			$rates[ $index ]    = ( (float) $d['interest_rate'] ) / 100 / 12;
			$mins[ $index ]     = (float) $d['minimum_payment'];
			$names[ $index ]    = $d['name'];
		}

		$months         = 0;
		$total_interest = 0.0;
		$max_months     = 360; // 30 yr limit simulation

		while ( array_sum( $balances ) > 0.01 && $months < $max_months ) {
			$months++;
			$avail_extra = $extra_payment;

			foreach ( $balances as $idx => $bal ) {
				if ( $bal <= 0 ) {
					continue;
				}

				$interest = $bal * $rates[ $idx ];
				$total_interest += $interest;
				$bal += $interest;

				$pay = $mins[ $idx ];
				if ( $idx === 0 || min( array_keys( array_filter( $balances, function( $v ) { return $v > 0; } ) ) ) === $idx ) {
					$pay += $avail_extra;
				}

				$actual_pay = min( $bal, $pay );
				$balances[ $idx ] -= $actual_pay;
			}
		}

		return array(
			'strategy'       => $strategy,
			'months'         => $months,
			'total_interest' => round( $total_interest, 2 ),
			'target_order'   => array_column( $sorted, 'name' ),
		);
	}
}

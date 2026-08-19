<?php
/**
 * Income Module & Income Growth System for WealthOS.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WealthOS_Income {

	public static function get_all( $user_id ) {
		global $wpdb;
		return $wpdb->get_results(
			$wpdb->prepare( "SELECT * FROM {$wpdb->prefix}wealthos_income WHERE user_id = %d ORDER BY amount DESC", $user_id ),
			ARRAY_A
		);
	}

	public static function save( $user_id, $data ) {
		global $wpdb;

		$id        = isset( $data['id'] ) ? intval( $data['id'] ) : 0;
		$row_data  = array(
			'user_id'      => $user_id,
			'name'         => WealthOS_Security::sanitize_text( $data['name'] ?? '' ),
			'category'     => WealthOS_Security::sanitize_text( $data['category'] ?? 'Salary' ),
			'amount'       => WealthOS_Security::sanitize_float( $data['amount'] ?? 0 ),
			'frequency'    => WealthOS_Security::sanitize_text( $data['frequency'] ?? 'monthly' ),
			'start_date'   => WealthOS_Security::sanitize_date( $data['start_date'] ?? null ),
			'end_date'     => WealthOS_Security::sanitize_date( $data['end_date'] ?? null ),
			'is_recurring' => isset( $data['is_recurring'] ) ? ( $data['is_recurring'] ? 1 : 0 ) : 1,
			'notes'        => WealthOS_Security::sanitize_textarea( $data['notes'] ?? '' ),
		);

		if ( $id > 0 ) {
			$wpdb->update( $wpdb->prefix . 'wealthos_income', $row_data, array( 'id' => $id, 'user_id' => $user_id ) );
			return $id;
		} else {
			$wpdb->insert( $wpdb->prefix . 'wealthos_income', $row_data );
			return $wpdb->insert_id;
		}
	}

	public static function get_summary( $user_id ) {
		$items = self::get_all( $user_id );

		$monthly_total = 0.0;
		$by_category   = array();

		foreach ( $items as $item ) {
			$m_amount = WealthOS_Calculations::normalize_to_monthly( $item['amount'], $item['frequency'] );
			$monthly_total += $m_amount;

			$cat = $item['category'];
			if ( ! isset( $by_category[ $cat ] ) ) {
				$by_category[ $cat ] = 0.0;
			}
			$by_category[ $cat ] += $m_amount;
		}

		$annual_total = $monthly_total * 12;

		// Calculate percentages
		$primary_income_pct   = 0.0;
		$secondary_income_pct = 0.0;

		if ( $monthly_total > 0 && ! empty( $by_category ) ) {
			arsort( $by_category );
			$highest_cat         = reset( $by_category );
			$primary_income_pct   = ( $highest_cat / $monthly_total ) * 100;
			$secondary_income_pct = 100 - $primary_income_pct;
		}

		// Educational Recommendations for Opportunities
		$opportunities = self::generate_opportunities( $items, $monthly_total, $primary_income_pct );

		return array(
			'items'                => $items,
			'monthly_total'        => round( $monthly_total, 2 ),
			'annual_total'         => round( $annual_total, 2 ),
			'by_category'          => $by_category,
			'primary_income_pct'   => round( $primary_income_pct, 1 ),
			'secondary_income_pct' => round( $secondary_income_pct, 1 ),
			'opportunities'        => $opportunities,
		);
	}

	private static function generate_opportunities( $items, $monthly_total, $primary_pct ) {
		$opps = array();

		if ( $primary_pct >= 90 ) {
			$opps[] = __( 'High Income Concentration: Over 90% of your earnings rely on a single primary source. Consider developing side income streams, freelancing, or digital services to reduce dependency risk.', 'wealthos' );
		}

		if ( $monthly_total < 3000 ) {
			$opps[] = __( 'Skills Development: Focus on high-value skills development or compensation negotiation to build higher baseline earning power.', 'wealthos' );
		}

		$opps[] = __( 'Recurring Revenue Opportunity: Explore turning expertise or services into scalable digital products or retainer-based consulting.', 'wealthos' );

		return $opps;
	}
}

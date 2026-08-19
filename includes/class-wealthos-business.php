<?php
/**
 * Business Module & Growth Planner for WealthOS (Entrepreneurs & Freelancers).
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WealthOS_Business {

	public static function get_summary( $user_id ) {
		global $wpdb;

		$metrics = $wpdb->get_row(
			$wpdb->prepare( "SELECT * FROM {$wpdb->prefix}wealthos_business_metrics WHERE user_id = %d ORDER BY id DESC LIMIT 1", $user_id ),
			ARRAY_A
		);

		$offers = $wpdb->get_results(
			$wpdb->prepare( "SELECT * FROM {$wpdb->prefix}wealthos_business_offers WHERE user_id = %d", $user_id ),
			ARRAY_A
		);

		if ( ! $metrics ) {
			$metrics = array(
				'revenue'               => 0.0,
				'expenses'              => 0.0,
				'profit'                => 0.0,
				'customers'             => 0,
				'leads'                 => 0,
				'conversion_rate'       => 0.0,
				'avg_transaction_value' => 0.0,
				'recurring_revenue'     => 0.0,
				'cash_reserves'         => 0.0,
				'owner_compensation'    => 0.0,
			);
		} else {
			$metrics['profit'] = (float) $metrics['revenue'] - (float) $metrics['expenses'];
		}

		return array(
			'metrics' => $metrics,
			'offers'  => $offers,
		);
	}

	public static function save_metrics( $user_id, $data ) {
		global $wpdb;

		$rev = WealthOS_Security::sanitize_float( $data['revenue'] ?? 0 );
		$exp = WealthOS_Security::sanitize_float( $data['expenses'] ?? 0 );

		$row_data = array(
			'user_id'               => $user_id,
			'period'                => WealthOS_Security::sanitize_text( $data['period'] ?? 'monthly' ),
			'revenue'               => $rev,
			'expenses'              => $exp,
			'profit'                => $rev - $exp,
			'customers'             => intval( $data['customers'] ?? 0 ),
			'leads'                 => intval( $data['leads'] ?? 0 ),
			'conversion_rate'       => WealthOS_Security::sanitize_float( $data['conversion_rate'] ?? 0 ),
			'avg_transaction_value' => WealthOS_Security::sanitize_float( $data['avg_transaction_value'] ?? 0 ),
			'recurring_revenue'     => WealthOS_Security::sanitize_float( $data['recurring_revenue'] ?? 0 ),
			'cash_reserves'         => WealthOS_Security::sanitize_float( $data['cash_reserves'] ?? 0 ),
			'owner_compensation'    => WealthOS_Security::sanitize_float( $data['owner_compensation'] ?? 0 ),
		);

		$wpdb->insert( $wpdb->prefix . 'wealthos_business_metrics', $row_data );
		return $wpdb->insert_id;
	}

	public static function save_offer( $user_id, $data ) {
		global $wpdb;

		$id       = isset( $data['id'] ) ? intval( $data['id'] ) : 0;
		$row_data = array(
			'user_id'         => $user_id,
			'offer_name'      => WealthOS_Security::sanitize_text( $data['offer_name'] ?? '' ),
			'price'           => WealthOS_Security::sanitize_float( $data['price'] ?? 0 ),
			'delivery_cost'   => WealthOS_Security::sanitize_float( $data['delivery_cost'] ?? 0 ),
			'customers_count' => intval( $data['customers_count'] ?? 0 ),
			'offer_type'      => WealthOS_Security::sanitize_text( $data['offer_type'] ?? 'one-time' ),
		);

		if ( $id > 0 ) {
			$wpdb->update( $wpdb->prefix . 'wealthos_business_offers', $row_data, array( 'id' => $id, 'user_id' => $user_id ) );
			return $id;
		} else {
			$wpdb->insert( $wpdb->prefix . 'wealthos_business_offers', $row_data );
			return $wpdb->insert_id;
		}
	}

	/**
	 * Operational Growth Activity Calculator.
	 */
	public static function calculate_growth_requirements( $target_revenue, $avg_sale, $conversion_rate_pct ) {
		$target_revenue      = (float) $target_revenue;
		$avg_sale            = max( 1, (float) $avg_sale );
		$conversion_rate_pct = max( 0.1, (float) $conversion_rate_pct );

		$required_sales = ceil( $target_revenue / $avg_sale );
		$required_leads = ceil( $required_sales / ( $conversion_rate_pct / 100 ) );

		return array(
			'target_revenue'      => round( $target_revenue, 2 ),
			'required_sales'      => $required_sales,
			'conversion_rate_pct' => $conversion_rate_pct,
			'required_leads'      => $required_leads,
		);
	}
}

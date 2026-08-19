<?php
/**
 * Productive Assets Module for WealthOS.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WealthOS_Assets {

	public static function get_all( $user_id ) {
		global $wpdb;
		return $wpdb->get_results(
			$wpdb->prepare( "SELECT * FROM {$wpdb->prefix}wealthos_assets WHERE user_id = %d ORDER BY estimated_value DESC", $user_id ),
			ARRAY_A
		);
	}

	public static function save( $user_id, $data ) {
		global $wpdb;

		$id       = isset( $data['id'] ) ? intval( $data['id'] ) : 0;
		$row_data = array(
			'user_id'          => $user_id,
			'name'             => WealthOS_Security::sanitize_text( $data['name'] ?? '' ),
			'category'         => WealthOS_Security::sanitize_text( $data['category'] ?? 'Real Estate' ),
			'acquisition_cost' => WealthOS_Security::sanitize_float( $data['acquisition_cost'] ?? 0 ),
			'estimated_value'  => WealthOS_Security::sanitize_float( $data['estimated_value'] ?? 0 ),
			'monthly_income'   => WealthOS_Security::sanitize_float( $data['monthly_income'] ?? 0 ),
			'monthly_expenses' => WealthOS_Security::sanitize_float( $data['monthly_expenses'] ?? 0 ),
			'date_acquired'    => WealthOS_Security::sanitize_date( $data['date_acquired'] ?? null ),
			'notes'            => WealthOS_Security::sanitize_textarea( $data['notes'] ?? '' ),
		);

		if ( $id > 0 ) {
			$wpdb->update( $wpdb->prefix . 'wealthos_assets', $row_data, array( 'id' => $id, 'user_id' => $user_id ) );
			return $id;
		} else {
			$wpdb->insert( $wpdb->prefix . 'wealthos_assets', $row_data );
			return $wpdb->insert_id;
		}
	}

	public static function get_summary( $user_id ) {
		$items = self::get_all( $user_id );

		$total_asset_value   = 0.0;
		$total_monthly_income = 0.0;
		$total_monthly_exp    = 0.0;

		foreach ( $items as $item ) {
			$total_asset_value    += (float) $item['estimated_value'];
			$total_monthly_income += (float) $item['monthly_income'];
			$total_monthly_exp    += (float) $item['monthly_expenses'];
		}

		$net_monthly_income = $total_monthly_income - $total_monthly_exp;
		$net_annual_income  = $net_monthly_income * 12;

		return array(
			'items'                => $items,
			'total_asset_value'    => round( $total_asset_value, 2 ),
			'total_monthly_income' => round( $total_monthly_income, 2 ),
			'total_monthly_exp'    => round( $total_monthly_exp, 2 ),
			'net_monthly_income'   => round( $net_monthly_income, 2 ),
			'net_annual_income'    => round( $net_annual_income, 2 ),
		);
	}
}

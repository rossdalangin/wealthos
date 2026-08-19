<?php
/**
 * Security and Data Validation/Sanitization/Escaping Layer.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WealthOS_Security {

	/**
	 * Verify nonce with WordPress.
	 */
	public static function verify_nonce( $nonce_value, $action_name = 'wealthos_nonce' ) {
		if ( ! isset( $nonce_value ) || ! wp_verify_nonce( $nonce_value, $action_name ) ) {
			wp_send_json_error( array( 'message' => __( 'Security check failed. Invalid nonce.', 'wealthos' ) ), 403 );
			exit;
		}
	}

	/**
	 * Verify current user has required capability or matches specified user.
	 */
	public static function check_capability( $capability = 'read', $user_id = null ) {
		if ( ! is_user_logged_in() ) {
			wp_send_json_error( array( 'message' => __( 'Unauthorized access. User must be logged in.', 'wealthos' ) ), 401 );
			exit;
		}

		$current_user_id = get_current_user_id();

		if ( null !== $user_id && (int) $user_id !== $current_user_id && ! current_user_id_can_manage() ) {
			wp_send_json_error( array( 'message' => __( 'Forbidden access to requested user data.', 'wealthos' ) ), 403 );
			exit;
		}

		if ( ! current_user_can( $capability ) ) {
			wp_send_json_error( array( 'message' => __( 'Insufficient permissions.', 'wealthos' ) ), 403 );
			exit;
		}
	}

	/**
	 * Helper to check if current user is admin.
	 */
	public static function is_admin() {
		return current_user_can( 'manage_options' );
	}

	/**
	 * Sanitize decimal / currency inputs.
	 */
	public static function sanitize_float( $input, $default = 0.0 ) {
		if ( is_null( $input ) || '' === trim( (string) $input ) ) {
			return $default;
		}
		$clean = preg_replace( '/[^\d\.\-]/', '', (string) $input );
		return (float) $clean;
	}

	/**
	 * Sanitize text inputs.
	 */
	public static function sanitize_text( $input, $default = '' ) {
		if ( empty( $input ) ) {
			return $default;
		}
		return sanitize_text_field( trim( (string) $input ) );
	}

	/**
	 * Sanitize textarea/notes.
	 */
	public static function sanitize_textarea( $input, $default = '' ) {
		if ( empty( $input ) ) {
			return $default;
		}
		return sanitize_textarea_field( trim( (string) $input ) );
	}

	/**
	 * Sanitize dates (YYYY-MM-DD).
	 */
	public static function sanitize_date( $input, $default = null ) {
		if ( empty( $input ) ) {
			return $default;
		}
		$d = DateTime::createFromFormat( 'Y-m-d', trim( $input ) );
		if ( $d && $d->format( 'Y-m-d' ) === trim( $input ) ) {
			return trim( $input );
		}
		return $default;
	}
}

function current_user_id_can_manage() {
	return current_user_can( 'manage_options' );
}

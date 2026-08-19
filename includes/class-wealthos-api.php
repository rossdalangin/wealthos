<?php
/**
 * REST API Routes for WealthOS.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WealthOS_API {

	private $namespace = 'wealthos/v1';

	public function register_routes() {
		// Profile & Onboarding
		register_rest_route(
			$this->namespace,
			'/profile',
			array(
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_profile' ),
					'permission_callback' => array( $this, 'check_auth' ),
				),
				array(
					'methods'             => WP_REST_Server::CREATABLE,
					'callback'            => array( $this, 'save_profile' ),
					'permission_callback' => array( $this, 'check_auth' ),
				),
			)
		);

		// Overview Dashboard Data
		register_rest_route(
			$this->namespace,
			'/dashboard-summary',
			array(
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => array( $this, 'get_dashboard_summary' ),
				'permission_callback' => array( $this, 'check_auth' ),
			)
		);

		// CRUD Endpoint dispatcher
		register_rest_route(
			$this->namespace,
			'/(?P<module>[a-zA-Z0-9_\-]+)',
			array(
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'handle_get' ),
					'permission_callback' => array( $this, 'check_auth' ),
				),
				array(
					'methods'             => WP_REST_Server::CREATABLE,
					'callback'            => array( $this, 'handle_post' ),
					'permission_callback' => array( $this, 'check_auth' ),
				),
			)
		);

		register_rest_route(
			$this->namespace,
			'/(?P<module>[a-zA-Z0-9_\-]+)/(?P<id>\d+)',
			array(
				array(
					'methods'             => WP_REST_Server::DELETABLE,
					'callback'            => array( $this, 'handle_delete' ),
					'permission_callback' => array( $this, 'check_auth' ),
				),
			)
		);
	}

	public function check_auth( $request ) {
		return is_user_logged_in();
	}

	public function get_profile( $request ) {
		$user_id = get_current_user_id();
		global $wpdb;
		$profile = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}wealthos_user_profile WHERE user_id = %d", $user_id ), ARRAY_A );
		if ( ! $profile ) {
			$profile = array(
				'user_id'               => $user_id,
				'age_range'             => '25-34',
				'country'               => 'US',
				'currency'              => '$',
				'employment_type'       => 'employed',
				'income_stability'      => 'stable',
				'household_status'      => 'single',
				'dependents'            => 0,
				'financial_goal'        => 'Build Wealth',
				'milestone_target'      => '100000',
				'risk_tolerance'        => 'moderate',
				'investment_experience' => 'beginner',
				'onboarding_completed'  => 0,
			);
		}
		return rest_ensure_response( $profile );
	}

	public function save_profile( $request ) {
		$user_id = get_current_user_id();
		global $wpdb;

		$params = $request->get_json_params();
		$data   = array(
			'user_id'               => $user_id,
			'age_range'             => WealthOS_Security::sanitize_text( $params['age_range'] ?? '' ),
			'country'               => WealthOS_Security::sanitize_text( $params['country'] ?? '' ),
			'currency'              => WealthOS_Security::sanitize_text( $params['currency'] ?? '$' ),
			'employment_type'       => WealthOS_Security::sanitize_text( $params['employment_type'] ?? '' ),
			'income_stability'      => WealthOS_Security::sanitize_text( $params['income_stability'] ?? '' ),
			'household_status'      => WealthOS_Security::sanitize_text( $params['household_status'] ?? '' ),
			'dependents'            => intval( $params['dependents'] ?? 0 ),
			'financial_goal'        => WealthOS_Security::sanitize_text( $params['financial_goal'] ?? '' ),
			'milestone_target'      => WealthOS_Security::sanitize_text( $params['milestone_target'] ?? '' ),
			'risk_tolerance'        => WealthOS_Security::sanitize_text( $params['risk_tolerance'] ?? 'moderate' ),
			'investment_experience' => WealthOS_Security::sanitize_text( $params['investment_experience'] ?? 'beginner' ),
			'onboarding_completed'  => intval( $params['onboarding_completed'] ?? 1 ),
		);

		$existing = $wpdb->get_var( $wpdb->prepare( "SELECT id FROM {$wpdb->prefix}wealthos_user_profile WHERE user_id = %d", $user_id ) );

		if ( $existing ) {
			$wpdb->update( $wpdb->prefix . 'wealthos_user_profile', $data, array( 'user_id' => $user_id ) );
		} else {
			$wpdb->insert( $wpdb->prefix . 'wealthos_user_profile', $data );
		}

		return rest_ensure_response( array( 'success' => true, 'profile' => $data ) );
	}

	public function get_dashboard_summary( $request ) {
		$user_id = get_current_user_id();

		$income       = WealthOS_Income::get_summary( $user_id );
		$expenses     = WealthOS_Expenses::get_summary( $user_id );
		$cash_flow    = WealthOS_Calculations::calculate_cash_flow( $income['monthly_total'], $expenses['monthly_total'] );
		$emergency    = WealthOS_Emergency_Fund::get_status( $user_id, $expenses['essential_monthly'] );
		$debt         = WealthOS_Debt::get_summary( $user_id, $income['monthly_total'] );
		$savings      = WealthOS_Savings::get_summary( $user_id );
		$investments  = WealthOS_Investments::get_summary( $user_id );
		$assets       = WealthOS_Assets::get_summary( $user_id );
		$net_worth    = WealthOS_Net_Worth::get_current_net_worth( $user_id );
		$wealth_score = WealthOS_Wealth_Score::calculate( $user_id );
		$bottleneck   = WealthOS_Bottleneck::identify( $user_id );
		$actions      = WealthOS_Action_Center::get_actions( $user_id );

		return rest_ensure_response( array(
			'income'       => $income,
			'expenses'     => $expenses,
			'cash_flow'    => $cash_flow,
			'emergency'    => $emergency,
			'debt'         => $debt,
			'savings'      => $savings,
			'investments'  => $investments,
			'assets'       => $assets,
			'net_worth'    => $net_worth,
			'wealth_score' => $wealth_score,
			'bottleneck'   => $bottleneck,
			'actions'      => $actions,
		) );
	}

	public function handle_get( $request ) {
		$module  = $request->get_param( 'module' );
		$user_id = get_current_user_id();

		switch ( $module ) {
			case 'income':
				return rest_ensure_response( WealthOS_Income::get_all( $user_id ) );
			case 'expenses':
				return rest_ensure_response( WealthOS_Expenses::get_all( $user_id ) );
			case 'budgets':
				return rest_ensure_response( WealthOS_Budget::get_all( $user_id ) );
			case 'debts':
				return rest_ensure_response( WealthOS_Debt::get_all( $user_id ) );
			case 'savings':
				return rest_ensure_response( WealthOS_Savings::get_all( $user_id ) );
			case 'investments':
				return rest_ensure_response( WealthOS_Investments::get_all( $user_id ) );
			case 'assets':
				return rest_ensure_response( WealthOS_Assets::get_all( $user_id ) );
			case 'goals':
				return rest_ensure_response( WealthOS_Goals::get_all( $user_id ) );
			case 'business':
				return rest_ensure_response( WealthOS_Business::get_summary( $user_id ) );
			case 'risk':
				return rest_ensure_response( WealthOS_Risk::get_summary( $user_id ) );
			case 'actions':
				return rest_ensure_response( WealthOS_Action_Center::get_actions( $user_id ) );
			case 'calendar':
				return rest_ensure_response( WealthOS_Calendar::get_events( $user_id ) );
			case 'notifications':
				return rest_ensure_response( WealthOS_Notifications::get_user_notifications( $user_id ) );
			default:
				return new WP_Error( 'invalid_module', __( 'Unknown module.', 'wealthos' ), array( 'status' => 400 ) );
		}
	}

	public function handle_post( $request ) {
		$module  = $request->get_param( 'module' );
		$user_id = get_current_user_id();
		$data    = $request->get_json_params();

		switch ( $module ) {
			case 'income':
				$id = WealthOS_Income::save( $user_id, $data );
				return rest_ensure_response( array( 'success' => true, 'id' => $id ) );
			case 'expenses':
				$id = WealthOS_Expenses::save( $user_id, $data );
				return rest_ensure_response( array( 'success' => true, 'id' => $id ) );
			case 'budgets':
				$res = WealthOS_Budget::save_budgets( $user_id, $data );
				return rest_ensure_response( array( 'success' => true, 'result' => $res ) );
			case 'debts':
				$id = WealthOS_Debt::save( $user_id, $data );
				return rest_ensure_response( array( 'success' => true, 'id' => $id ) );
			case 'savings':
				$id = WealthOS_Savings::save( $user_id, $data );
				return rest_ensure_response( array( 'success' => true, 'id' => $id ) );
			case 'investments':
				$id = WealthOS_Investments::save( $user_id, $data );
				return rest_ensure_response( array( 'success' => true, 'id' => $id ) );
			case 'assets':
				$id = WealthOS_Assets::save( $user_id, $data );
				return rest_ensure_response( array( 'success' => true, 'id' => $id ) );
			case 'goals':
				$id = WealthOS_Goals::save( $user_id, $data );
				return rest_ensure_response( array( 'success' => true, 'id' => $id ) );
			case 'business-metric':
				$id = WealthOS_Business::save_metrics( $user_id, $data );
				return rest_ensure_response( array( 'success' => true, 'id' => $id ) );
			case 'business-offer':
				$id = WealthOS_Business::save_offer( $user_id, $data );
				return rest_ensure_response( array( 'success' => true, 'id' => $id ) );
			case 'risk':
				$res = WealthOS_Risk::update_item( $user_id, $data );
				return rest_ensure_response( array( 'success' => true, 'result' => $res ) );
			case 'actions':
				$id = WealthOS_Action_Center::save( $user_id, $data );
				return rest_ensure_response( array( 'success' => true, 'id' => $id ) );
			case 'calendar':
				$id = WealthOS_Calendar::save( $user_id, $data );
				return rest_ensure_response( array( 'success' => true, 'id' => $id ) );
			default:
				return new WP_Error( 'invalid_module', __( 'Unknown module for POST.', 'wealthos' ), array( 'status' => 400 ) );
		}
	}

	public function handle_delete( $request ) {
		$module  = $request->get_param( 'module' );
		$id      = intval( $request->get_param( 'id' ) );
		$user_id = get_current_user_id();

		global $wpdb;

		$table_map = array(
			'income'      => $wpdb->prefix . 'wealthos_income',
			'expenses'    => $wpdb->prefix . 'wealthos_expenses',
			'debts'       => $wpdb->prefix . 'wealthos_debts',
			'savings'     => $wpdb->prefix . 'wealthos_savings',
			'investments' => $wpdb->prefix . 'wealthos_investments',
			'assets'      => $wpdb->prefix . 'wealthos_assets',
			'goals'       => $wpdb->prefix . 'wealthos_goals',
			'actions'     => $wpdb->prefix . 'wealthos_actions',
			'calendar'    => $wpdb->prefix . 'wealthos_calendar',
		);

		if ( ! isset( $table_map[ $module ] ) ) {
			return new WP_Error( 'invalid_module', __( 'Cannot delete from this module.', 'wealthos' ), array( 'status' => 400 ) );
		}

		$deleted = $wpdb->delete( $table_map[ $module ], array( 'id' => $id, 'user_id' => $user_id ) );

		return rest_ensure_response( array( 'success' => (bool) $deleted ) );
	}
}

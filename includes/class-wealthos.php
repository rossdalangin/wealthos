<?php
/**
 * Main Plugin Orchestrator Class for WealthOS.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WealthOS {

	private static $instance = null;

	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	private function __construct() {
		$this->includes();
		$this->init_hooks();
	}

	private function includes() {
		// Module classes will be loaded here
		require_once WEALTHOS_PATH . 'includes/class-wealthos-calculations.php';
		require_once WEALTHOS_PATH . 'includes/class-wealthos-income.php';
		require_once WEALTHOS_PATH . 'includes/class-wealthos-expenses.php';
		require_once WEALTHOS_PATH . 'includes/class-wealthos-budget.php';
		require_once WEALTHOS_PATH . 'includes/class-wealthos-emergency-fund.php';
		require_once WEALTHOS_PATH . 'includes/class-wealthos-debt.php';
		require_once WEALTHOS_PATH . 'includes/class-wealthos-savings.php';
		require_once WEALTHOS_PATH . 'includes/class-wealthos-investments.php';
		require_once WEALTHOS_PATH . 'includes/class-wealthos-assets.php';
		require_once WEALTHOS_PATH . 'includes/class-wealthos-net-worth.php';
		require_once WEALTHOS_PATH . 'includes/class-wealthos-goals.php';
		require_once WEALTHOS_PATH . 'includes/class-wealthos-business.php';
		require_once WEALTHOS_PATH . 'includes/class-wealthos-risk.php';
		require_once WEALTHOS_PATH . 'includes/class-wealthos-fi.php';
		require_once WEALTHOS_PATH . 'includes/class-wealthos-compounding.php';
		require_once WEALTHOS_PATH . 'includes/class-wealthos-wealth-score.php';
		require_once WEALTHOS_PATH . 'includes/class-wealthos-bottleneck.php';
		require_once WEALTHOS_PATH . 'includes/class-wealthos-action-center.php';
		require_once WEALTHOS_PATH . 'includes/class-wealthos-roadmap.php';
		require_once WEALTHOS_PATH . 'includes/class-wealthos-scenario-planner.php';
		require_once WEALTHOS_PATH . 'includes/class-wealthos-calendar.php';
		require_once WEALTHOS_PATH . 'includes/class-wealthos-notifications.php';
		require_once WEALTHOS_PATH . 'includes/class-wealthos-reports.php';

		if ( is_admin() ) {
			require_once WEALTHOS_PATH . 'admin/class-wealthos-admin.php';
		}

		require_once WEALTHOS_PATH . 'public/class-wealthos-public.php';
	}

	private function init_hooks() {
		add_action( 'rest_api_init', array( $this, 'register_rest_routes' ) );
		add_action( 'init', array( $this, 'load_textdomain' ) );
	}

	public function register_rest_routes() {
		$api = new WealthOS_API();
		$api->register_routes();
	}

	public function load_textdomain() {
		load_plugin_textdomain( 'wealthos', false, dirname( plugin_basename( WEALTHOS_FILE ) ) . '/languages' );
	}
}

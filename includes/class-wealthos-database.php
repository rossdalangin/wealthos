<?php
/**
 * Database Management for WealthOS.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WealthOS_Database {

	public static $db_version = '1.0.0';

	public static function activate() {
		self::create_tables();
		update_option( 'wealthos_db_version', self::$db_version );
	}

	public static function deactivate() {
		// Cleanup temporary transients or cron jobs if needed
	}

	public static function create_tables() {
		global $wpdb;

		$charset_collate = $wpdb->get_charset_collate();

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';

		$sql = array();

		// User profile
		$sql[] = "CREATE TABLE {$wpdb->prefix}wealthos_user_profile (
			id bigint(20) NOT NULL AUTO_INCREMENT,
			user_id bigint(20) NOT NULL,
			age_range varchar(50) DEFAULT '',
			country varchar(100) DEFAULT '',
			currency varchar(10) DEFAULT 'USD',
			employment_type varchar(50) DEFAULT '',
			income_stability varchar(50) DEFAULT '',
			household_status varchar(50) DEFAULT '',
			dependents int(11) DEFAULT 0,
			financial_goal text DEFAULT NULL,
			milestone_target text DEFAULT NULL,
			risk_tolerance varchar(50) DEFAULT 'moderate',
			investment_experience varchar(50) DEFAULT 'beginner',
			onboarding_completed tinyint(1) DEFAULT 0,
			created_at datetime DEFAULT CURRENT_TIMESTAMP,
			updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
			PRIMARY KEY  (id),
			UNIQUE KEY user_id (user_id)
		) $charset_collate;";

		// Income
		$sql[] = "CREATE TABLE {$wpdb->prefix}wealthos_income (
			id bigint(20) NOT NULL AUTO_INCREMENT,
			user_id bigint(20) NOT NULL,
			name varchar(255) NOT NULL,
			category varchar(100) NOT NULL,
			amount decimal(15,2) NOT NULL DEFAULT 0.00,
			frequency varchar(50) NOT NULL DEFAULT 'monthly',
			start_date date DEFAULT NULL,
			end_date date DEFAULT NULL,
			is_recurring tinyint(1) DEFAULT 1,
			notes text DEFAULT NULL,
			created_at datetime DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY  (id),
			KEY user_id (user_id)
		) $charset_collate;";

		// Income Growth Plan
		$sql[] = "CREATE TABLE {$wpdb->prefix}wealthos_income_growth (
			id bigint(20) NOT NULL AUTO_INCREMENT,
			user_id bigint(20) NOT NULL,
			target_income decimal(15,2) NOT NULL DEFAULT 0.00,
			target_date date DEFAULT NULL,
			skills text DEFAULT NULL,
			services text DEFAULT NULL,
			potential_customers text DEFAULT NULL,
			pricing text DEFAULT NULL,
			sales_targets text DEFAULT NULL,
			weekly_activities text DEFAULT NULL,
			notes text DEFAULT NULL,
			created_at datetime DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY  (id),
			KEY user_id (user_id)
		) $charset_collate;";

		// Expenses
		$sql[] = "CREATE TABLE {$wpdb->prefix}wealthos_expenses (
			id bigint(20) NOT NULL AUTO_INCREMENT,
			user_id bigint(20) NOT NULL,
			name varchar(255) NOT NULL,
			category varchar(100) NOT NULL,
			amount decimal(15,2) NOT NULL DEFAULT 0.00,
			frequency varchar(50) NOT NULL DEFAULT 'monthly',
			is_essential tinyint(1) DEFAULT 1,
			start_date date DEFAULT NULL,
			end_date date DEFAULT NULL,
			is_recurring tinyint(1) DEFAULT 1,
			notes text DEFAULT NULL,
			created_at datetime DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY  (id),
			KEY user_id (user_id)
		) $charset_collate;";

		// Budgets
		$sql[] = "CREATE TABLE {$wpdb->prefix}wealthos_budgets (
			id bigint(20) NOT NULL AUTO_INCREMENT,
			user_id bigint(20) NOT NULL,
			category varchar(100) NOT NULL,
			budget_type varchar(50) DEFAULT 'custom',
			allocated_amount decimal(15,2) NOT NULL DEFAULT 0.00,
			allocated_percentage decimal(5,2) DEFAULT 0.00,
			period varchar(50) DEFAULT 'monthly',
			created_at datetime DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY  (id),
			KEY user_id (user_id)
		) $charset_collate;";

		// Debts
		$sql[] = "CREATE TABLE {$wpdb->prefix}wealthos_debts (
			id bigint(20) NOT NULL AUTO_INCREMENT,
			user_id bigint(20) NOT NULL,
			name varchar(255) NOT NULL,
			balance decimal(15,2) NOT NULL DEFAULT 0.00,
			interest_rate decimal(5,2) NOT NULL DEFAULT 0.00,
			minimum_payment decimal(15,2) NOT NULL DEFAULT 0.00,
			payment_frequency varchar(50) DEFAULT 'monthly',
			due_date date DEFAULT NULL,
			type varchar(100) DEFAULT 'credit_card',
			priority int(11) DEFAULT 0,
			created_at datetime DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY  (id),
			KEY user_id (user_id)
		) $charset_collate;";

		// Savings
		$sql[] = "CREATE TABLE {$wpdb->prefix}wealthos_savings (
			id bigint(20) NOT NULL AUTO_INCREMENT,
			user_id bigint(20) NOT NULL,
			goal_name varchar(255) NOT NULL,
			target_amount decimal(15,2) NOT NULL DEFAULT 0.00,
			current_amount decimal(15,2) NOT NULL DEFAULT 0.00,
			deadline date DEFAULT NULL,
			monthly_contribution decimal(15,2) DEFAULT 0.00,
			category varchar(100) DEFAULT 'general',
			priority varchar(50) DEFAULT 'medium',
			status varchar(50) DEFAULT 'active',
			created_at datetime DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY  (id),
			KEY user_id (user_id)
		) $charset_collate;";

		// Investments
		$sql[] = "CREATE TABLE {$wpdb->prefix}wealthos_investments (
			id bigint(20) NOT NULL AUTO_INCREMENT,
			user_id bigint(20) NOT NULL,
			name varchar(255) NOT NULL,
			asset_class varchar(100) NOT NULL,
			quantity decimal(15,4) DEFAULT 1.0000,
			purchase_price decimal(15,2) DEFAULT 0.00,
			current_value decimal(15,2) NOT NULL DEFAULT 0.00,
			monthly_contribution decimal(15,2) DEFAULT 0.00,
			account_type varchar(100) DEFAULT 'brokerage',
			created_at datetime DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY  (id),
			KEY user_id (user_id)
		) $charset_collate;";

		// Productive Assets
		$sql[] = "CREATE TABLE {$wpdb->prefix}wealthos_assets (
			id bigint(20) NOT NULL AUTO_INCREMENT,
			user_id bigint(20) NOT NULL,
			name varchar(255) NOT NULL,
			category varchar(100) NOT NULL,
			acquisition_cost decimal(15,2) DEFAULT 0.00,
			estimated_value decimal(15,2) NOT NULL DEFAULT 0.00,
			monthly_income decimal(15,2) DEFAULT 0.00,
			monthly_expenses decimal(15,2) DEFAULT 0.00,
			date_acquired date DEFAULT NULL,
			notes text DEFAULT NULL,
			created_at datetime DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY  (id),
			KEY user_id (user_id)
		) $charset_collate;";

		// Net Worth Snapshots
		$sql[] = "CREATE TABLE {$wpdb->prefix}wealthos_net_worth_snapshots (
			id bigint(20) NOT NULL AUTO_INCREMENT,
			user_id bigint(20) NOT NULL,
			snapshot_date date NOT NULL,
			total_assets decimal(15,2) NOT NULL DEFAULT 0.00,
			total_liabilities decimal(15,2) NOT NULL DEFAULT 0.00,
			net_worth decimal(15,2) NOT NULL DEFAULT 0.00,
			notes text DEFAULT NULL,
			created_at datetime DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY  (id),
			KEY user_id (user_id)
		) $charset_collate;";

		// Goals
		$sql[] = "CREATE TABLE {$wpdb->prefix}wealthos_goals (
			id bigint(20) NOT NULL AUTO_INCREMENT,
			user_id bigint(20) NOT NULL,
			title varchar(255) NOT NULL,
			target_amount decimal(15,2) NOT NULL DEFAULT 0.00,
			current_amount decimal(15,2) NOT NULL DEFAULT 0.00,
			deadline date DEFAULT NULL,
			priority varchar(50) DEFAULT 'medium',
			monthly_contribution decimal(15,2) DEFAULT 0.00,
			category varchar(100) DEFAULT 'wealth',
			status varchar(50) DEFAULT 'active',
			notes text DEFAULT NULL,
			created_at datetime DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY  (id),
			KEY user_id (user_id)
		) $charset_collate;";

		// Business Metrics
		$sql[] = "CREATE TABLE {$wpdb->prefix}wealthos_business_metrics (
			id bigint(20) NOT NULL AUTO_INCREMENT,
			user_id bigint(20) NOT NULL,
			period varchar(50) NOT NULL DEFAULT 'monthly',
			revenue decimal(15,2) DEFAULT 0.00,
			expenses decimal(15,2) DEFAULT 0.00,
			profit decimal(15,2) DEFAULT 0.00,
			customers int(11) DEFAULT 0,
			leads int(11) DEFAULT 0,
			conversion_rate decimal(5,2) DEFAULT 0.00,
			avg_transaction_value decimal(15,2) DEFAULT 0.00,
			recurring_revenue decimal(15,2) DEFAULT 0.00,
			cash_reserves decimal(15,2) DEFAULT 0.00,
			owner_compensation decimal(15,2) DEFAULT 0.00,
			created_at datetime DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY  (id),
			KEY user_id (user_id)
		) $charset_collate;";

		// Business Offers
		$sql[] = "CREATE TABLE {$wpdb->prefix}wealthos_business_offers (
			id bigint(20) NOT NULL AUTO_INCREMENT,
			user_id bigint(20) NOT NULL,
			offer_name varchar(255) NOT NULL,
			price decimal(15,2) DEFAULT 0.00,
			delivery_cost decimal(15,2) DEFAULT 0.00,
			customers_count int(11) DEFAULT 0,
			offer_type varchar(50) DEFAULT 'one-time',
			created_at datetime DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY  (id),
			KEY user_id (user_id)
		) $charset_collate;";

		// Risk Checklist
		$sql[] = "CREATE TABLE {$wpdb->prefix}wealthos_risk_checklist (
			id bigint(20) NOT NULL AUTO_INCREMENT,
			user_id bigint(20) NOT NULL,
			category varchar(100) NOT NULL,
			item_key varchar(100) NOT NULL,
			title varchar(255) NOT NULL,
			status varchar(50) DEFAULT 'needs_attention',
			notes text DEFAULT NULL,
			updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
			PRIMARY KEY  (id),
			KEY user_id (user_id)
		) $charset_collate;";

		// Actions
		$sql[] = "CREATE TABLE {$wpdb->prefix}wealthos_actions (
			id bigint(20) NOT NULL AUTO_INCREMENT,
			user_id bigint(20) NOT NULL,
			title varchar(255) NOT NULL,
			category varchar(100) DEFAULT 'general',
			priority varchar(50) DEFAULT 'medium',
			difficulty varchar(50) DEFAULT 'medium',
			estimated_impact varchar(100) DEFAULT 'moderate',
			deadline date DEFAULT NULL,
			is_completed tinyint(1) DEFAULT 0,
			notes text DEFAULT NULL,
			created_at datetime DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY  (id),
			KEY user_id (user_id)
		) $charset_collate;";

		// Calendar
		$sql[] = "CREATE TABLE {$wpdb->prefix}wealthos_calendar (
			id bigint(20) NOT NULL AUTO_INCREMENT,
			user_id bigint(20) NOT NULL,
			title varchar(255) NOT NULL,
			event_type varchar(100) NOT NULL,
			amount decimal(15,2) DEFAULT 0.00,
			due_date date NOT NULL,
			status varchar(50) DEFAULT 'pending',
			notes text DEFAULT NULL,
			created_at datetime DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY  (id),
			KEY user_id (user_id)
		) $charset_collate;";

		// Notifications
		$sql[] = "CREATE TABLE {$wpdb->prefix}wealthos_notifications (
			id bigint(20) NOT NULL AUTO_INCREMENT,
			user_id bigint(20) NOT NULL,
			title varchar(255) NOT NULL,
			message text NOT NULL,
			type varchar(50) DEFAULT 'info',
			is_read tinyint(1) DEFAULT 0,
			created_at datetime DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY  (id),
			KEY user_id (user_id)
		) $charset_collate;";

		foreach ( $sql as $query ) {
			dbDelta( $query );
		}
	}
}

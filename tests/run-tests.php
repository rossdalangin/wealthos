<?php
/**
 * Test Suite Runner for WealthOS Financial Calculations & Core Logic.
 */

define( 'ABSPATH', __DIR__ . '/../' );

// Mock WordPress functions if not defined
if ( ! function_exists( 'add_action' ) ) {
	function add_action() {}
}
if ( ! function_exists( 'add_shortcode' ) ) {
	function add_shortcode() {}
}
if ( ! function_exists( 'is_admin' ) ) {
	function is_admin() { return false; }
}
if ( ! function_exists( 'is_user_logged_in' ) ) {
	function is_user_logged_in() { return true; }
}
if ( ! function_exists( 'get_current_user_id' ) ) {
	function get_current_user_id() { return 1; }
}
if ( ! function_exists( 'get_option' ) ) {
	function get_option( $opt, $default = array() ) { return $default; }
}
if ( ! function_exists( 'update_option' ) ) {
	function update_option( $opt, $val ) { return true; }
}
if ( ! function_exists( '__' ) ) {
	function __( $str, $domain = 'default' ) { return $str; }
}
if ( ! function_exists( 'esc_html__' ) ) {
	function esc_html__( $str, $domain = 'default' ) { return $str; }
}
if ( ! function_exists( 'sanitize_text_field' ) ) {
	function sanitize_text_field( $str ) { return trim( $str ); }
}
if ( ! function_exists( 'sanitize_textarea_field' ) ) {
	function sanitize_textarea_field( $str ) { return trim( $str ); }
}

require_once __DIR__ . '/../includes/class-wealthos-calculations.php';
require_once __DIR__ . '/../includes/class-wealthos-security.php';
require_once __DIR__ . '/../includes/class-wealthos-settings.php';
require_once __DIR__ . '/../includes/class-wealthos-debt.php';
require_once __DIR__ . '/../includes/class-wealthos-compounding.php';
require_once __DIR__ . '/../includes/class-wealthos-fi.php';
require_once __DIR__ . '/../includes/class-wealthos-business.php';

$errors = 0;

echo "========================================\n";
echo "  WealthOS Financial Verification Tests \n";
echo "========================================\n\n";

// Test 1: Normalize Frequency to Monthly
echo "[Test 1] Normalize Frequency to Monthly... ";
$monthly = WealthOS_Calculations::normalize_to_monthly( 12000, 'annually' );
if ( abs( $monthly - 1000.0 ) < 0.01 ) {
	echo "PASSED\n";
} else {
	echo "FAILED (Expected 1000.0, got {$monthly})\n";
	$errors++;
}

// Test 2: Cash Flow & Surplus
echo "[Test 2] Cash Flow Calculation... ";
$cf = WealthOS_Calculations::calculate_cash_flow( 5000, 3500 );
if ( $cf['surplus'] === 1500.0 && $cf['savings_rate'] === 30.0 ) {
	echo "PASSED\n";
} else {
	echo "FAILED (Surplus: {$cf['surplus']}, Rate: {$cf['savings_rate']}%)\n";
	$errors++;
}

// Test 3: Compounding Calculator
echo "[Test 3] Compounding Calculator Growth... ";
$calc = WealthOS_Compounding::calculate( 1000, 500, 7.0, 10 );
if ( $calc['total_contributions'] === 61000.0 && $calc['estimated_final_value'] > 85000.0 ) {
	echo "PASSED (Final Value: \${$calc['estimated_final_value']})\n";
} else {
	echo "FAILED (Final Value: \${$calc['estimated_final_value']})\n";
	$errors++;
}

// Test 4: FI Projections Rule of 25
echo "[Test 4] FI Projections 25x Rule... ";
$fi = WealthOS_FI::calculate_projections( 40000, 10000, 500, 7.0, 2.5, 60, 30 );
if ( $fi['fi_target_number'] === 1000000.0 ) {
	echo "PASSED (Target: \${$fi['fi_target_number']})\n";
} else {
	echo "FAILED (Target: \${$fi['fi_target_number']})\n";
	$errors++;
}

// Test 5: Debt Payoff Simulation (Avalanche vs Snowball)
echo "[Test 5] Debt Payoff Simulation... ";
$mock_debts = array(
	array( 'name' => 'Card A', 'balance' => 2000, 'interest_rate' => 22.0, 'minimum_payment' => 50 ),
	array( 'name' => 'Card B', 'balance' => 5000, 'interest_rate' => 15.0, 'minimum_payment' => 100 ),
);
$avalanche = WealthOS_Debt::calculate_payoff_strategy( $mock_debts, 'avalanche', 200 );
if ( $avalanche['months'] > 0 && $avalanche['target_order'][0] === 'Card A' ) {
	echo "PASSED (Payoff Months: {$avalanche['months']})\n";
} else {
	echo "FAILED\n";
	$errors++;
}

// Test 6: Business Growth Activity Calculator
echo "[Test 6] Business Growth Activity Calculator... ";
$biz = WealthOS_Business::calculate_growth_requirements( 10000, 1000, 20 );
if ( (float) $biz['required_sales'] === 10.0 && (float) $biz['required_leads'] === 50.0 ) {
	echo "PASSED (Sales: {$biz['required_sales']}, Leads: {$biz['required_leads']})\n";
} else {
	echo "FAILED (Expected sales 10, got " . var_export( $biz['required_sales'], true ) . ")\n";
	$errors++;
}

echo "\n----------------------------------------\n";
if ( $errors === 0 ) {
	echo "ALL VERIFICATION TESTS PASSED SUCCESSFULLY!\n";
	exit(0);
} else {
	echo "{$errors} TEST(S) FAILED.\n";
	exit(1);
}

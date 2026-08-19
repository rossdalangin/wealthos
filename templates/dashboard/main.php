<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$app_name = WealthOS_Settings::get_setting( 'app_name', 'WealthOS' );
$tagline  = WealthOS_Settings::get_setting( 'tagline', 'Your Personal Wealth Operating System.' );
?>
<div id="wealthos-app" class="wealthos-app">
	<div class="wealthos-header">
		<div>
			<h1><?php echo esc_html( $app_name ); ?></h1>
			<p><?php echo esc_html( $tagline ); ?></p>
		</div>
	</div>

	<!-- Business Process Guidance Banner -->
	<div class="wealthos-card" style="margin-bottom: 20px; background: #f0fdf4; border-left: 4px solid var(--wealthos-accent);">
		<h4 style="margin: 0 0 4px 0; color: var(--wealthos-accent-hover);"><?php esc_html_e( 'Wealth Lifecycle Business Process:', 'wealthos' ); ?></h4>
		<p style="margin: 0; font-size: 13px; color: var(--wealthos-text);">
			<?php esc_html_e( 'Work through the 11-stage process sequentially: Income → Cash Flow → Expense Control → Emergency Fund → Debt Reduction → Savings → Investing → Asset Building → Business Growth → Risk Management → Net Worth Growth.', 'wealthos' ); ?>
		</p>
	</div>

	<div class="wealthos-nav">
		<button class="wealthos-nav-btn active" data-tab="overview"><?php esc_html_e( 'Overview', 'wealthos' ); ?></button>
		<button class="wealthos-nav-btn" data-tab="income"><?php esc_html_e( '1. Income', 'wealthos' ); ?></button>
		<button class="wealthos-nav-btn" data-tab="expenses"><?php esc_html_e( '2. Expense Control', 'wealthos' ); ?></button>
		<button class="wealthos-nav-btn" data-tab="debts"><?php esc_html_e( '3. Debt Reduction', 'wealthos' ); ?></button>
		<button class="wealthos-nav-btn" data-tab="savings"><?php esc_html_e( '4. Savings Goals', 'wealthos' ); ?></button>
		<button class="wealthos-nav-btn" data-tab="investments"><?php esc_html_e( '5. Investing', 'wealthos' ); ?></button>
		<button class="wealthos-nav-btn" data-tab="assets"><?php esc_html_e( '6. Productive Assets', 'wealthos' ); ?></button>
		<button class="wealthos-nav-btn" data-tab="business"><?php esc_html_e( '7. Business Growth', 'wealthos' ); ?></button>
		<button class="wealthos-nav-btn" data-tab="risk"><?php esc_html_e( '8. Risk Checklist', 'wealthos' ); ?></button>
		<button class="wealthos-nav-btn" data-tab="actions"><?php esc_html_e( 'Action Center', 'wealthos' ); ?></button>
		<button class="wealthos-nav-btn" data-tab="roadmap"><?php esc_html_e( 'Wealth Roadmap', 'wealthos' ); ?></button>
		<button class="wealthos-nav-btn" data-tab="fi"><?php esc_html_e( 'FI Projections', 'wealthos' ); ?></button>
		<button class="wealthos-nav-btn" data-tab="scenario"><?php esc_html_e( 'Scenario Planner', 'wealthos' ); ?></button>
		<button class="wealthos-nav-btn" data-tab="compounding"><?php esc_html_e( 'Compounding Calc', 'wealthos' ); ?></button>
		<button class="wealthos-nav-btn" data-tab="reports"><?php esc_html_e( 'Reports & Exports', 'wealthos' ); ?></button>
	</div>

	<div id="wealthos-tab-content">
		<div class="wealthos-card">
			<p><?php esc_html_e( 'Loading financial system dashboard...', 'wealthos' ); ?></p>
		</div>
	</div>

	<div class="wealthos-disclaimer">
		<strong><?php esc_html_e( 'Educational & Financial Planning Notice:', 'wealthos' ); ?></strong>
		<?php echo esc_html( WealthOS_Settings::get_setting( 'disclaimer_text' ) ); ?>
	</div>
</div>

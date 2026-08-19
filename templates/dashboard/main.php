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

	<!-- Contextual Guidance Banner -->
	<div class="wealthos-card" style="margin-bottom: 20px; background: #f0fdf4; border-left: 4px solid var(--wealthos-accent);">
		<h4 style="margin: 0 0 4px 0; color: var(--wealthos-accent-hover);"><?php esc_html_e( 'System Philosophy & Instructions:', 'wealthos' ); ?></h4>
		<p style="margin: 0; font-size: 13px; color: var(--wealthos-text);">
			<?php esc_html_e( 'WealthOS turns financial information into a clear action plan. Work through each module sequentially: Log Income → Control Expenses & Cash Flow → Build Emergency Reserve → Eliminate Debt → Accumulate Productive Assets.', 'wealthos' ); ?>
		</p>
	</div>

	<div class="wealthos-nav">
		<button class="wealthos-nav-btn active" data-tab="overview"><?php esc_html_e( 'Overview', 'wealthos' ); ?></button>
		<button class="wealthos-nav-btn" data-tab="income"><?php esc_html_e( 'Income', 'wealthos' ); ?></button>
		<button class="wealthos-nav-btn" data-tab="expenses"><?php esc_html_e( 'Expenses', 'wealthos' ); ?></button>
		<button class="wealthos-nav-btn" data-tab="debts"><?php esc_html_e( 'Debts', 'wealthos' ); ?></button>
		<button class="wealthos-nav-btn" data-tab="savings"><?php esc_html_e( 'Savings', 'wealthos' ); ?></button>
		<button class="wealthos-nav-btn" data-tab="investments"><?php esc_html_e( 'Investments', 'wealthos' ); ?></button>
		<button class="wealthos-nav-btn" data-tab="assets"><?php esc_html_e( 'Assets', 'wealthos' ); ?></button>
		<button class="wealthos-nav-btn" data-tab="business"><?php esc_html_e( 'Business', 'wealthos' ); ?></button>
		<button class="wealthos-nav-btn" data-tab="risk"><?php esc_html_e( 'Risk Checklist', 'wealthos' ); ?></button>
		<button class="wealthos-nav-btn" data-tab="actions"><?php esc_html_e( 'Action Center', 'wealthos' ); ?></button>
		<button class="wealthos-nav-btn" data-tab="compounding"><?php esc_html_e( 'Compounding Calc', 'wealthos' ); ?></button>
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

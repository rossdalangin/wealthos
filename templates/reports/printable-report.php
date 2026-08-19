<?php
/**
 * Printable / PDF Exportable Annual Wealth Report Template for WealthOS.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$user_id = get_current_user_id();
$report  = WealthOS_Reports::generate_annual_report( $user_id );
$symbol  = WealthOS_Settings::get_setting( 'currency_symbol', '$' );
$app     = WealthOS_Settings::get_setting( 'app_name', 'WealthOS' );
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title><?php echo esc_html( $app ); ?> — Annual Wealth Report <?php echo esc_html( $report['report_year'] ); ?></title>
	<style>
		body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif; color: #1e293b; background: #fff; padding: 40px; margin: 0; }
		.report-header { border-bottom: 2px solid #0f172a; padding-bottom: 20px; margin-bottom: 30px; display: flex; justify-content: space-between; align-items: flex-end; }
		.report-header h1 { margin: 0; font-size: 28px; color: #0f172a; }
		.report-header p { margin: 4px 0 0 0; color: #64748b; font-size: 14px; }
		.grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 24px; }
		.card { border: 1px solid #cbd5e1; border-radius: 8px; padding: 20px; background: #f8fafc; }
		.card h3 { margin: 0 0 10px 0; font-size: 14px; text-transform: uppercase; color: #64748b; }
		.value { font-size: 24px; font-weight: 800; color: #0f172a; }
		.table { width: 100%; border-collapse: collapse; margin-top: 12px; }
		.table th, .table td { text-align: left; padding: 10px 12px; border-bottom: 1px solid #cbd5e1; font-size: 13px; }
		.table th { background: #e2e8f0; color: #475569; font-weight: 600; }
		.disclaimer { margin-top: 40px; padding: 16px; background: #eff6ff; border-left: 4px solid #3b82f6; font-size: 12px; color: #1e40af; border-radius: 4px; }
		.btn-print { background: #10b981; color: #fff; border: none; padding: 10px 20px; font-size: 14px; font-weight: 600; border-radius: 6px; cursor: pointer; margin-bottom: 20px; }
		@media print { .btn-print { display: none; } body { padding: 0; } }
	</style>
</head>
<body>

	<button class="btn-print" onclick="window.print()"><?php esc_html_e( 'Print / Save as PDF', 'wealthos' ); ?></button>

	<div class="report-header">
		<div>
			<h1><?php echo esc_html( $app ); ?> — Annual Wealth Report</h1>
			<p><?php esc_html_e( 'Comprehensive Personal Financial Statement', 'wealthos' ); ?> &bull; <?php echo esc_html( $report['report_year'] ); ?></p>
		</div>
		<div>
			<p><strong><?php esc_html_e( 'Date Generated:', 'wealthos' ); ?></strong> <?php echo esc_html( current_time( 'F j, Y' ) ); ?></p>
		</div>
	</div>

	<div class="grid-2">
		<div class="card">
			<h3><?php esc_html_e( 'Annual Income', 'wealthos' ); ?></h3>
			<div class="value"><?php echo esc_html( $symbol . number_format( $report['annual_income'], 2 ) ); ?></div>
		</div>
		<div class="card">
			<h3><?php esc_html_e( 'Annual Expenses', 'wealthos' ); ?></h3>
			<div class="value"><?php echo esc_html( $symbol . number_format( $report['annual_expenses'], 2 ) ); ?></div>
		</div>
		<div class="card">
			<h3><?php esc_html_e( 'Annual Cash Flow Surplus', 'wealthos' ); ?></h3>
			<div class="value"><?php echo esc_html( $symbol . number_format( $report['annual_savings'], 2 ) ); ?></div>
		</div>
		<div class="card">
			<h3><?php esc_html_e( 'Current Net Worth', 'wealthos' ); ?></h3>
			<div class="value"><?php echo esc_html( $symbol . number_format( $report['net_worth'], 2 ) ); ?></div>
		</div>
	</div>

	<div class="card" style="margin-bottom: 24px;">
		<h3><?php esc_html_e( 'Balance Sheet Summary', 'wealthos' ); ?></h3>
		<table class="table">
			<thead>
				<tr>
					<th><?php esc_html_e( 'Category', 'wealthos' ); ?></th>
					<th><?php esc_html_e( 'Valuation / Amount', 'wealthos' ); ?></th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td><strong><?php esc_html_e( 'Investment Portfolio', 'wealthos' ); ?></strong></td>
					<td><?php echo esc_html( $symbol . number_format( $report['investments_total'], 2 ) ); ?></td>
				</tr>
				<tr>
					<td><strong><?php esc_html_e( 'Productive Cash-Flowing Assets', 'wealthos' ); ?></strong></td>
					<td><?php echo esc_html( $symbol . number_format( $report['assets_total'], 2 ) ); ?></td>
				</tr>
				<tr>
					<td><strong><?php esc_html_e( 'Total Liabilities & Debts', 'wealthos' ); ?></strong></td>
					<td><?php echo esc_html( $symbol . number_format( $report['debt_total'], 2 ) ); ?></td>
				</tr>
			</tbody>
		</table>
	</div>

	<div class="disclaimer">
		<strong><?php esc_html_e( 'Educational Disclaimer:', 'wealthos' ); ?></strong>
		<?php echo esc_html( $report['disclaimer'] ); ?>
	</div>

</body>
</html>

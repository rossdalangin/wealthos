<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="wrap">
	<h1><?php echo esc_html( WealthOS_Settings::get_setting( 'app_name', 'WealthOS' ) ); ?> — Admin Dashboard</h1>
	<p><?php echo esc_html( WealthOS_Settings::get_setting( 'tagline', 'Your Personal Wealth Operating System.' ) ); ?></p>

	<div id="wealthos-app" class="wealthos-app">
		<?php include WEALTHOS_PATH . 'templates/dashboard/main.php'; ?>
	</div>
</div>

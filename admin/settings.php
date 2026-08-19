<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="wrap">
	<h1><?php echo esc_html__( 'WealthOS Settings & White-Labeling', 'wealthos' ); ?></h1>
	<p class="description">
		<?php esc_html_e( 'Customize the branding, currency, terminology, visual theme, and database settings for your Personal Wealth Operating System.', 'wealthos' ); ?>
	</p>

	<form method="post" action="">
		<?php wp_nonce_field( 'wealthos_settings_nonce' ); ?>
		<table class="form-table">
			<tr>
				<th scope="row"><label for="app_name"><?php esc_html_e( 'Application Name', 'wealthos' ); ?></label></th>
				<td>
					<input name="app_name" type="text" id="app_name" value="<?php echo esc_attr( WealthOS_Settings::get_setting( 'app_name', 'WealthOS' ) ); ?>" class="regular-text">
					<p class="description"><?php esc_html_e( 'Example: WealthOS, Family WealthOS, or Personal Financial OS.', 'wealthos' ); ?></p>
				</td>
			</tr>
			<tr>
				<th scope="row"><label for="tagline"><?php esc_html_e( 'Tagline', 'wealthos' ); ?></label></th>
				<td>
					<input name="tagline" type="text" id="tagline" value="<?php echo esc_attr( WealthOS_Settings::get_setting( 'tagline', 'Your Personal Wealth Operating System.' ) ); ?>" class="regular-text">
					<p class="description"><?php esc_html_e( 'Short tagline displayed under the application title.', 'wealthos' ); ?></p>
				</td>
			</tr>
			<tr>
				<th scope="row"><label for="currency_symbol"><?php esc_html_e( 'Currency Symbol', 'wealthos' ); ?></label></th>
				<td>
					<input name="currency_symbol" type="text" id="currency_symbol" value="<?php echo esc_attr( WealthOS_Settings::get_setting( 'currency_symbol', '$' ) ); ?>" class="small-text">
					<p class="description"><?php esc_html_e( 'Symbol used across all reports and dashboards (e.g. $, €, £, ¥, R).', 'wealthos' ); ?></p>
				</td>
			</tr>
			<tr>
				<th scope="row"><label for="primary_color"><?php esc_html_e( 'Primary Color', 'wealthos' ); ?></label></th>
				<td>
					<input name="primary_color" type="color" id="primary_color" value="<?php echo esc_attr( WealthOS_Settings::get_setting( 'primary_color', '#0f172a' ) ); ?>">
					<p class="description"><?php esc_html_e( 'Primary background and navigation color.', 'wealthos' ); ?></p>
				</td>
			</tr>
			<tr>
				<th scope="row"><label for="accent_color"><?php esc_html_e( 'Accent Color', 'wealthos' ); ?></label></th>
				<td>
					<input name="accent_color" type="color" id="accent_color" value="<?php echo esc_attr( WealthOS_Settings::get_setting( 'accent_color', '#10b981' ) ); ?>">
					<p class="description"><?php esc_html_e( 'Accent color for primary buttons and positive metrics.', 'wealthos' ); ?></p>
				</td>
			</tr>
			<tr>
				<th scope="row"><?php esc_html_e( 'Data Removal on Uninstall', 'wealthos' ); ?></th>
				<td>
					<label for="delete_data_on_uninstall">
						<input name="delete_data_on_uninstall" type="checkbox" id="delete_data_on_uninstall" value="1" <?php checked( WealthOS_Settings::get_setting( 'delete_data_on_uninstall', 0 ), 1 ); ?>>
						<?php esc_html_e( 'Delete all WealthOS database tables when uninstalling the plugin.', 'wealthos' ); ?>
					</label>
					<p class="description" style="color: #d63638;"><?php esc_html_e( 'Warning: Enabling this will permanently wipe all stored financial records if the plugin is uninstalled.', 'wealthos' ); ?></p>
				</td>
			</tr>
		</table>

		<?php submit_button( __( 'Save Settings', 'wealthos' ), 'primary', 'wealthos_save_settings' ); ?>
	</form>
</div>

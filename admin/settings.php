<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="wrap">
	<h1><?php echo esc_html__( 'WealthOS Settings & White-Labeling', 'wealthos' ); ?></h1>

	<form method="post" action="">
		<?php wp_nonce_field( 'wealthos_settings_nonce' ); ?>
		<table class="form-table">
			<tr>
				<th scope="row"><label for="app_name"><?php esc_html_e( 'Application Name', 'wealthos' ); ?></label></th>
				<td><input name="app_name" type="text" id="app_name" value="<?php echo esc_attr( WealthOS_Settings::get_setting( 'app_name', 'WealthOS' ) ); ?>" class="regular-text"></td>
			</tr>
			<tr>
				<th scope="row"><label for="tagline"><?php esc_html_e( 'Tagline', 'wealthos' ); ?></label></th>
				<td><input name="tagline" type="text" id="tagline" value="<?php echo esc_attr( WealthOS_Settings::get_setting( 'tagline', 'Your Personal Wealth Operating System.' ) ); ?>" class="regular-text"></td>
			</tr>
			<tr>
				<th scope="row"><label for="currency_symbol"><?php esc_html_e( 'Currency Symbol', 'wealthos' ); ?></label></th>
				<td><input name="currency_symbol" type="text" id="currency_symbol" value="<?php echo esc_attr( WealthOS_Settings::get_setting( 'currency_symbol', '$' ) ); ?>" class="small-text"></td>
			</tr>
			<tr>
				<th scope="row"><label for="primary_color"><?php esc_html_e( 'Primary Color', 'wealthos' ); ?></label></th>
				<td><input name="primary_color" type="color" id="primary_color" value="<?php echo esc_attr( WealthOS_Settings::get_setting( 'primary_color', '#0f172a' ) ); ?>"></td>
			</tr>
			<tr>
				<th scope="row"><label for="accent_color"><?php esc_html_e( 'Accent Color', 'wealthos' ); ?></label></th>
				<td><input name="accent_color" type="color" id="accent_color" value="<?php echo esc_attr( WealthOS_Settings::get_setting( 'accent_color', '#10b981' ) ); ?>"></td>
			</tr>
			<tr>
				<th scope="row"><?php esc_html_e( 'Data Removal on Uninstall', 'wealthos' ); ?></th>
				<td>
					<label for="delete_data_on_uninstall">
						<input name="delete_data_on_uninstall" type="checkbox" id="delete_data_on_uninstall" value="1" <?php checked( WealthOS_Settings::get_setting( 'delete_data_on_uninstall', 0 ), 1 ); ?>>
						<?php esc_html_e( 'Delete all WealthOS database tables when uninstalling the plugin.', 'wealthos' ); ?>
					</label>
				</td>
			</tr>
		</table>

		<?php submit_button( __( 'Save Settings', 'wealthos' ), 'primary', 'wealthos_save_settings' ); ?>
	</form>
</div>

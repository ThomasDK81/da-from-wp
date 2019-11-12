<?php
/**
 * Admin settings page.
 *
 * @package ClanRoyale
 */

add_action( 'admin_menu', 'clanroyale_add_admin_menu' );
add_action( 'admin_init', 'clanroyale_settings_init' );
add_action( 'admin_enqueue_scripts', 'clanroyale_options_page_enqueue' );
add_action( 'admin_post_clanroyale_clear_cache', 'clanroyale_clear_cache' );
add_action( 'wp_ajax_clanroyale_clear_api_requests_cache', 'clanroyale_clear_cache_with_ajax' );
add_action( 'wp_ajax_clanroyale_settings_api_test_ajax', 'clanroyale_settings_api_test_ajax' );

/**
 * Add ClanRoyale settings page to the Options toplevel menu.
 */
function clanroyale_add_admin_menu() {
	global $clanroyale_settings_page;
	$clanroyale_settings_page = add_options_page( _x( 'ClanRoyale', 'Page title', 'clanroyale' ), _x( 'ClanRoyale', 'Menu title', 'clanroyale' ), 'manage_options', 'clanroyale', 'clanroyale_options_page' );
}

/**
 * Contents of the settings page.
 */
function clanroyale_options_page() {

	echo '<div class="wrap">';
	echo '<h2>' . esc_html_x( 'ClanRoyale', 'Settings page title', 'clanroyale' ) . '</h2>';
	echo esc_attr( settings_errors( 'clanroyale_options_page' ) );
	echo '<form id="clanroyale-settings-form" action="options.php" method="post">';

	settings_fields( 'clanroyale_options_page' );
	do_settings_sections( 'clanroyale_options_page' );
	submit_button( __( 'Save Changes' ), 'primary', 'clanroyale-settings-save', false );
	submit_button( __( 'Reset' ), 'delete', 'clanroyale-settings-reset', false );

	echo '</form>';
	echo '</div>';
}

/**
 * CSS and JS for the settings page.
 *
 * @param string $hook A global with the admin page id.
 */
function clanroyale_options_page_enqueue( $hook ) {
	global $clanroyale_settings_page;

	if ( $hook !== $clanroyale_settings_page ) {
		return;
	}

	wp_register_script( 'clanroyale-options-page-js', plugin_dir_url( __FILE__ ) . 'js/settings.js', array( 'jquery', 'wp-i18n' ), CLANROYALE_VERSION, true );
	wp_set_script_translations( 'clanroyale-options-page-js', 'clanroyale' );
	wp_enqueue_script( 'clanroyale-options-page-js' );

	wp_register_style( 'clanroyale-options-page-css', plugin_dir_url( __FILE__ ) . 'css/settings.css', false, CLANROYALE_VERSION, 'all' );
	wp_enqueue_style( 'clanroyale-options-page-css' );

}

/**
 * Use Settings API to create sections and fields.
 */
function clanroyale_settings_init() {
	register_setting( 'clanroyale_options_page', 'clanroyale_settings', 'clanroyale_options_page_validation' );

	add_settings_section( 'clanroyale_settings_api_section', __( 'API', 'clanroyale' ), 'clanroyale_settings_api_section_callback', 'clanroyale_options_page' );
	add_settings_field( 'clanroyale_settings_api_choose', __( 'Choose API', 'clanroyale' ), 'clanroyale_settings_api_choose_callback', 'clanroyale_options_page', 'clanroyale_settings_api_section', array( 'class' => 'clanroyale-settings-api-choose' ) );
	add_settings_field( 'clanroyale_settings_api_1_token', __( 'Token', 'clanroyale' ), 'clanroyale_settings_api_1_token_callback', 'clanroyale_options_page', 'clanroyale_settings_api_section', array( 'class' => 'clanroyale-settings-api-1-token clanroyale-settings-api-token' ) );
	add_settings_field( 'clanroyale_settings_api_2_token', __( 'Token', 'clanroyale' ), 'clanroyale_settings_api_2_token_callback', 'clanroyale_options_page', 'clanroyale_settings_api_section', array( 'class' => 'clanroyale-settings-api-2-token clanroyale-settings-api-token' ) );
	add_settings_field( 'clanroyale_settings_api_test', __( 'Test API connection', 'clanroyale' ), 'clanroyale_settings_api_test_callback', 'clanroyale_options_page', 'clanroyale_settings_api_section' );

	add_settings_section( 'clanroyale_settings_clan_section', __( 'Clan', 'clanroyale' ), 'clanroyale_settings_clan_section_callback', 'clanroyale_options_page' );
	add_settings_field( 'clanroyale_settings_clan_tag', __( 'Clan tag (without the #)', 'clanroyale' ), 'clanroyale_settings_clan_tag_callback', 'clanroyale_options_page', 'clanroyale_settings_clan_section' );
	add_settings_field( 'clanroyale_settings_clan_tag_test', __( 'Test clan tag', 'clanroyale' ), 'clanroyale_settings_clan_tag_test_callback', 'clanroyale_options_page', 'clanroyale_settings_clan_section' );

	add_settings_section( 'clanroyale_settings_cache_section', __( 'Cache', 'clanroyale' ), 'clanroyale_settings_cache_section_callback', 'clanroyale_options_page' );
	add_settings_field( 'clanroyale_transient_keys', __( 'Transient keys', 'clanroyale' ), 'clanroyale_transient_keys_callback', 'clanroyale_options_page', 'clanroyale_settings_cache_section', array( 'class' => 'clanroyale-transient-keys' ) );
	add_settings_field( 'clanroyale_delete_transient_keys', __( 'Delete transient keys', 'clanroyale' ), 'clanroyale_delete_transient_keys_callback', 'clanroyale_options_page', 'clanroyale_settings_cache_section', array( 'class' => 'clanroyale-delete-transient-keys' ) );

	add_settings_section( 'clanroyale_settings_backup_section', __( 'Backup', 'clanroyale' ), 'clanroyale_settings_backup_section_callback', 'clanroyale_options_page' );
	add_settings_field( 'clanroyale_create_backup', __( 'Create backups?', 'clanroyale' ), 'clanroyale_create_backup_callback', 'clanroyale_options_page', 'clanroyale_settings_backup_section', array( 'class' => 'clanroyale-create-backup' ) );
}

/**
 * Validation
 */
function clanroyale_options_page_validation( $input ) {
	// Check if the reset button has been clicked.
	if ( isset( $_POST['clanroyale_reset_settings'] ) ) {
		clanroyale_purge_transients();
		add_settings_error( 'clanroyale-reset-settings', esc_attr( 'settings_updated' ), __( 'Settings reset successfully.', 'clanroyale' ), 'updated' );
		return ''; // Returning an empty string, clears the settings.
	}

	if ( isset( $input['clanroyale_create_backup'] ) ) {
		if ( '1' === $input['clanroyale_create_backup'] ) {
			// Activate cron or check if it already on.
		} elseif ( '2' === $input['clanroyale_create_backup'] ) {
			// Deactivate if it activated.
		}
	}

	if ( isset( $input['clanroyale_clan_tag'] ) ) {
		$input['clanroyale_clan_tag'] = ltrim( $input['clanroyale_clan_tag'], '#' );
		$input['clanroyale_clan_tag'] = ltrim( $input['clanroyale_clan_tag'], '%23' );
	}

	return $input;
}


/**
 * API section description.
 */
function clanroyale_settings_api_section_callback() {
	esc_html_e( 'This plugin uses an API to display all the Clash Royale data. It supports the official Clash Royale API and RoyaleAPI', 'clanroyale' );
}

/**
 * Choose api fields.
 */
function clanroyale_settings_api_choose_callback() {
	$options = get_option( 'clanroyale_settings' );
	if ( $options && isset( $options['clanroyale_settings_api_choose'] ) ) {
		$checked = esc_attr( $options['clanroyale_settings_api_choose'] );
	} else {
		$checked = false;
	}
	echo '<fieldset><legend class="screen-reader-text"><span>' . esc_html__( 'Choose API', 'clanroyale' ) . '</span></legend>';
	echo '<label><input class="clanroyale-settings-api-choose-radio clanroyale-settings-api-choose-radio-1" type="radio" name="clanroyale_settings[clanroyale_settings_api_choose]" value="1" ' . checked( '1', $checked, false ) . '> ' . esc_html__( 'RoyaleAPI', 'clanroyale' ) . '</label><br/>';
	echo '<label><input class="clanroyale-settings-api-choose-radio clanroyale-settings-api-choose-radio-2" type="radio" name="clanroyale_settings[clanroyale_settings_api_choose]" value="2" ' . checked( '2', $checked, false ) . '> ' . esc_html__( 'Clash Royale (official)', 'clanroyale' ) . '</label>';
	echo '</fieldset>';
}

/**
 * RoyaleAPI token field.
 */
function clanroyale_settings_api_1_token_callback() {
	$options = get_option( 'clanroyale_settings' );
	if ( $options && isset( $options['clanroyale_settings_api_1_token'] ) ) {
		$token = $options['clanroyale_settings_api_1_token'];
	} else {
		$token = '';
	}
	echo '<textarea name="clanroyale_settings[clanroyale_settings_api_1_token]" rows="6" cols="120">' . esc_attr( $token ) . '</textarea>';
	// Translators: Link to RoyaleAPI docs about authentication.
	echo '<p class="description" id="clanroyale-settings-api-1-token-description">' . sprintf( esc_html__( 'See %s for more.', 'clanroyale' ), '<a href="https://docs.royaleapi.com/#/authentication" target="_blank">https://docs.royaleapi.com/#/authentication</a>' ) . '</p>';
}

/**
 * Clash Royale API (official) key field.
 */
function clanroyale_settings_api_2_token_callback() {
	$options = get_option( 'clanroyale_settings' );
	if ( $options && isset( $options['clanroyale_settings_api_2_token'] ) ) {
		$token = $options['clanroyale_settings_api_2_token'];
	} else {
		$token = '';
	}
	echo '<textarea name="clanroyale_settings[clanroyale_settings_api_2_token]" rows="6" cols="120">' . esc_attr( $token ) . '</textarea>';
	// Translators: Link to RoyaleAPI docs about authentication.
	echo '<p class="description" id="clanroyale-settings-api-2-token-description">' . sprintf( esc_html__( 'See %s for more.', 'clanroyale' ), '<a href="https://developer.clashroyale.com" target="_blank">https://developer.clashroyale.com</a> ' ) . '</p>';
}

/**
 * Test API connection.
 */
function clanroyale_settings_api_test_callback() {
	$chosen_api = clanroyale_get_chosen_api();
	if ( false !== $chosen_api ) {
		if ( isset( $chosen_api['token'] ) && ! empty( $chosen_api['token'] ) ) {
			$disable = '';

		} else {
			$disable = 'disabled';
		}
	} else {
		$disable = 'disabled';
	}

	// echo '<button id="clanroyale-settings-api-test-button" class="button clanroyale-button-spinner" ' . esc_html( $disable ) . '>' . esc_html__( 'Test API connection', 'clanroyale' ) . '</button>';
	echo '<button id="clanroyale-settings-api-test-button" class="button clanroyale-button-spinner">' . esc_html__( 'Test API connection', 'clanroyale' ) . '</button>';
}

/**
 * Use Ajax to clear cache.
 */
function clanroyale_settings_api_test_ajax() {
	// Todo: Connect to the API and return response status code.
	wp_die();
}


/**
 * Clan section description.
 */
function clanroyale_settings_clan_section_callback() {
	esc_html_e( 'This plugin needs the clan tag of the clan. It is possible to use other clan tags in the shortcode, but this will be the default.', 'clanroyale' );
}

/**
 * Clan tag field
 */
function clanroyale_clan_tag_callback() {
	$options = get_option( 'clanroyale_settings' );
	if ( $options && isset( $options['clanroyale_clan_tag'] ) ) {
		$clan_tag = $options['clanroyale_clan_tag'];
	} else {
		$clan_tag = '';
	}
	echo '<input id="clanroyale-clan-tag" type="text" name="clanroyale_settings[clanroyale_clan_tag]" value="' . $clan_tag . '" placeholder="E.g. P2UL1R7Q" class="regular-text">';
	// Translators: Link to RoyaleAPI docs about authentication.
	echo '<p class="description" id="clanroyale-clan_tag-description">' . sprintf( esc_html__( 'See %s to find your clan tag.', 'clanroyale' ), '<a href="https://royaleapi.com/clans/search" target="_blank">https://royaleapi.com/clans/search</a> ' ) . '</p>';
}


/**
 * Cache section description.
 */
function clanroyale_settings_cache_section_callback() {
	esc_html_e( 'This plugin uses WordPress transients as cache for the API responses.', 'clanroyale' );
}

/**
 * Transient keys field.
 */
function clanroyale_transient_keys_callback() {
	$transient_keys = get_option( 'clanroyale_transient_keys' );
	if ( ! $transient_keys ) {
		$transient_keys = array();
	}
	echo '<textarea id="clanroyale-settings-form-textarea-transient-keys" name="clanroyale_settings[transient_keys]" rows="6" cols="120" disabled>' . esc_html( implode( "\n", $transient_keys ) ) . '</textarea>';
	// Translators: Explains why the field for transient keys are disabled.
	echo '<p class="description" id="clanroyale-transient-keys-description">' . esc_html__( 'You can not edit the transient keys, you can purge the cache and delete them.', 'clanroyale' ) . '</p>';
}

/**
 * Delete transient keys field.
 */
function clanroyale_delete_transient_keys_callback() {
	$transient_keys = get_option( 'clanroyale_transient_keys' );
	if ( empty( get_option( 'clanroyale_transient_keys' ) ) ) {
		$disable_clear_cache = 'disabled';
	} else {
		$disable_clear_cache = '';
	}
	echo '<button id="clanroyale-settings-form-button-clearcache" class="button clanroyale-button-spinner" ' . esc_html( $disable_clear_cache ) . '>' . esc_html__( 'Clear cache', 'clanroyale' ) . '</button>';
}

/**
 * Use Ajax to clear cache.
 */
function clanroyale_clear_cache_with_ajax() {
	clanroyale_purge_transients();
	wp_die();
}


/**
 * Backup section description.
 */
function clanroyale_settings_backup_section_callback() {
	esc_html_e( 'This plugin can create a backup of the API response. It will be used if the API and local cache is not available.', 'clanroyale' );
}

/**
 * Choose api fields.
 */
function clanroyale_create_backup_callback() {
	$options = get_option( 'clanroyale_settings' );
	if ( $options && isset( $options['clanroyale_create_backup'] ) ) {
		$checked = esc_attr( $options['clanroyale_create_backup'] );
	} else {
		$checked = false;
	}
	echo '<fieldset><legend class="screen-reader-text"><span>' . esc_html__( 'Create backups?', 'clanroyale' ) . '</span></legend>';
	echo '<label><input id="clanroyale-create-backup-radio-1" class="clanroyale-create-backup-radio" type="radio" name="clanroyale_settings[clanroyale_create_backup]" value="1" ' . checked( '1', $checked, false ) . '> ' . esc_html__( 'Yes', 'clanroyale' ) . '</label><br/>';
	echo '<label><input id="clanroyale-create-backup-radio-2" class="clanroyale-create-backup-radio" type="radio" name="clanroyale_settings[clanroyale_create_backup]" value="2" ' . checked( '2', $checked, false ) . '> ' . esc_html__( 'No', 'clanroyale' ) . '</label>';
	echo '</fieldset>';
}

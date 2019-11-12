<?php
/**
 * Place for all the useful functions.
 *
 * @package ClanRoyale
 */

function clanroyale_activation() {
	// Lets make sure the user is coming from the right place and is allow to execute this.
	if ( ! current_user_can( 'activate_plugins' ) ) {
		return;
	}

	$plugin = isset( $_REQUEST['plugin'] ) ? $_REQUEST['plugin'] : '';
	check_admin_referer( "activate-plugin_{$plugin}" );

	// Prepare ...
}

function clanroyale_deactivation() {
	// Lets make sure the user is coming from the right place and is allow to execute this.
	if ( ! current_user_can( 'activate_plugins' ) ) {
		return;
	}

	$plugin = isset( $_REQUEST['plugin'] ) ? $_REQUEST['plugin'] : '';
	check_admin_referer( "deactivate-plugin_{$plugin}" );

	// Delete cache.
	clanroyale_purge_transients();
}


function clanroyale_cron_exec() {
	$options = get_option( 'clanroyale_settings' );
	if ( $options && isset( $options['clanroyale_create_backup'] ) ) {
		// Do cron task.
	}
}

/**
 * Pass the transient key to this function whenever we save a transient.
 * This allows us to keep track of transients, when we want to delete them.
 *
 * @param string $new_transient_key Transient key.
 */
function clanroyale_update_transient_keys( $new_transient_key ) {

	// Get the current list of transients.
	$transient_keys = get_option( 'clanroyale_transient_keys' );

	// Append our new one.
	$transient_keys[] = $new_transient_key;

	// No duplicates.
	$transient_keys = array_unique( $transient_keys );

	// Save it to the DB.
	update_option( 'clanroyale_transient_keys', $transient_keys );
}

/**
 * Call this function to purge cache transients.
 */
function clanroyale_purge_transients() {

	// Get our list of transient keys from the DB.
	$transient_keys = get_option( 'clanroyale_transient_keys' );

	// For each key, delete that transient.
	if ( false !== $transient_keys ) {
		foreach ( $transient_keys as $t ) {
			delete_transient( $t );
		}
	}

	// Reset DB value.
	update_option( 'clanroyale_transient_keys', array() );
}

function clanroyale_get_chosen_api() {
	// Todo: This is not where we want to set the urls.
	$api_1_url = 'https://api.royaleapi.com/';
	$api_2_url = 'https://api.clashroyale.com/v1/';

	$options = get_option( 'clanroyale_settings' );
	if ( $options && isset( $options['clanroyale_settings_api_choose'] ) ) {
		$chosen_api = esc_attr( $options['clanroyale_settings_api_choose'] );
		$url        = esc_url_raw( ${'api_' . $chosen_api . '_url'} );
		$token      = esc_attr( $options[ 'clanroyale_settings_api_' . $chosen_api . '_token' ] );

		return array(
			'id'    => $chosen_api,
			'url'   => $url,
			'token' => $token,
		);
	} else {
		return false;
	}
}

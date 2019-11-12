<?php
/**
 * Uninstall and clean up.
 *
 * @package ClanRoyale
 */

// Lets make sure the user is allow to execute this.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

if ( ! current_user_can( 'activate_plugins' ) ) {
	return;
}

// The clean up.
delete_option( 'clanroyale_transient_keys' );
delete_option( 'clanroyale_settings' );
// Multisite.
delete_site_option( 'clanroyale_transient_keys' );
delete_site_option( 'clanroyale_settings' );

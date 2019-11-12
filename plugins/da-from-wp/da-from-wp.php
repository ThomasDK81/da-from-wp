<?php
/**
 * ClanRoyale
 *
 * Show Clash Royale information from an API
 *
 * @package     ClanRoyale
 * @author      ThomasDK81
 * @copyright   2019 ThomasDK81
 * @license     GPL-2.0-or-later
 *
 * @wordpress-plugin
 * Plugin Name: ClanRoyale
 * Plugin URI: https://www.clanroyale.dk/theplugin
 * Description: Show Clash Royale information from an API.
 * Version: 0.0.1.2
 * Requires at least: 5.0
 * Requires PHP:      7.0
 * Author: ThomasDK81
 * Author URI: https://profiles.wordpress.org/thomasdk81/
 * License: GPLv2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	wp_die();
}

/**
 * Define this plugins version as constant.
 */
if ( ! function_exists( 'get_plugin_data' ) ) {
	require_once ABSPATH . 'wp-admin/includes/plugin.php';
}
$plugin_data = get_plugin_data( __FILE__ );
define( 'CLANROYALE_VERSION', $plugin_data['Version'] );

require plugin_dir_path( __FILE__ ) . 'includes/functions.php';
require plugin_dir_path( __FILE__ ) . 'includes/api.php';
require plugin_dir_path( __FILE__ ) . 'admin/settings.php';
require plugin_dir_path( __FILE__ ) . 'public/shortcode.php';

add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'clanroyale_action_links' );

function clanroyale_action_links( $links ) {
	$links[] = '<a href="' . esc_url( get_admin_url( null, 'options-general.php?page=clanroyale' ) ) . '">' . __( 'Settings', 'clanroyale' ) . '</a>';
	return $links;
}


register_activation_hook( __FILE__, 'clanroyale_activation' );
register_deactivation_hook( __FILE__, 'clanroyale_deactivation' );

add_action( 'clanroyale_cron', 'clanroyale_cron_exec' );


// Todo: API offline caching - look at WP cronjobs for cache refreshing. Save the json files to wp-contents.

// Todo: Make advanced tab in setting. API urls
// Todo: Make style tab in setting. Colors etc.

// Todo: Shortcode output.
// Todo: Theme framework for displaying of clan and player.

// Todo: WordPress plugin directory. Screenshots, Banner, Icon.
// Todo: https://developer.wordpress.org/plugins/wordpress-org/how-to-use-subversion/#task-3. https://10up.com/blog/2019/introducing-github-actions-for-wordpress-plugins/.

<?php
/**
 * Place for all the useful API functions.
 *
 * @package ClanRoyale
 */

/**
 * API getter
 *
 * @param string $url       API url.
 * @param string $endpoint  API endpoint.
 * @param string $token     API token.
 * @return object $body     API response body.
 */
function clanroyale_api_get( $url = '', $endpoint = '', $token = '' ) {
	$request_url = esc_url_raw( $url . $endpoint );

	$transient_key = 'clanroyale_v' . CLANROYALE_VERSION . '_' . $request_url; // Maybe we should md5() it.
	$cached        = get_transient( $transient_key );

	if ( false !== $cached ) {
		return $cached;
	} else {
		// Arguments for the API request.
		$args = array(
			'timeout'    => 30,
			'user-agent' => 'ClanRoyale v' . CLANROYALE_VERSION,
			'headers'    => array(
				'Authorization' => 'Bearer ' . $token,
			),
		);

		// Make API request.
		$response         = wp_remote_get( $request_url, $args );
		$response_code    = wp_remote_retrieve_response_code( $response );
		$response_message = wp_remote_retrieve_response_message( $response );

		if ( 200 !== $response_code && ! empty( $response_message ) ) {
			// If we don't get response code 200 but we get a response message, we return both.
			return new WP_Error( $response_code, $response_message );
		} elseif ( 200 !== $response_code ) {
			// If we don't get response code 200 and no response message, we return a generic error message with the response code.
			return new WP_Error( $response_code, __( 'Unknown error occurred', 'clanroyale' ) );
		} else {
			// The request succeeded (we got a response code 200) return the body of the response.
			$body = wp_remote_retrieve_body( $response );

			// Lets add a timestamp and cache indicator.
			$body                     = json_decode( $body, true );
			$date                     = new DateTime();
			$body['collectedFromAPI'] = gmdate( 'Y-m-d\TH:i:s\Z', $date->format( 'U' ) );
			$body['fromCache']        = false;

			$body_cache              = $body;
			$body_cache['fromCache'] = true;

			// Back in to JSON.
			$body       = wp_json_encode( $body );
			$body_cache = wp_json_encode( $body_cache );

			if ( WP_DEBUG ) {
				$lifespan = 30;
			} else {
				$lifespan = 60 * 5;
			}

			// Caching - Save transient.
			set_transient( $transient_key, $body_cache, $lifespan );
			// Update the list of transients keys we used.
			clanroyale_update_transient_keys( $transient_key );

			return $body;
		}
	}

}

function clanroyale_get_clan_from_tag( $clan_tag ) {
	$chosen_api = clanroyale_get_chosen_api();

	$url = esc_url( $chosen_api['url'] );
	if ( '1' === $chosen_api['id'] ) {
		$endpoint = 'clan/' . rawurlencode( $clan_tag );
	} elseif ( '2' === $chosen_api['id'] ) {
		$endpoint = 'clans/' . rawurlencode( '#' . $clan_tag );
	} else {
		return false;
	}
	$token    = esc_attr( $chosen_api['token'] );
	$response = clanroyale_api_get( $url, $endpoint, $token );

	// Todo: Parse the different APIs. We want an array with the same structure, no matter which API.
	return $response;
}



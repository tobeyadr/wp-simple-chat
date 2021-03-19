<?php

namespace SimpleChat;

// Divi compatibility
add_action( 'et_before_main_content', __NAMESPACE__ . '\wp_body_open_compat', 99 );

/**
 * Add wp_body_open compat for unsupported themes.
 */
function wp_body_open_compat() {
	if ( ! did_action( 'wp_body_open' ) ) {
		do_action( 'wp_body_open' );
	}
}

// No wp_body_open compat
add_action( 'wp_footer', __NAMESPACE__ . '\no_wp_body_open' );

/**
 * Insert in footer before closing body tag. Not ideal but it will do.
 */
function no_wp_body_open() {
	if ( ! did_action( 'wp_body_open' ) ) {
		// fallback compat in the event of no wp_body_open tag.
		do_action( 'sim_chat_body_close' );
	}
}
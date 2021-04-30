<?php

namespace SimpleChat;

use function ExtendedCore\get_array_var;

/**
 * Get the HTML class
 *
 * @return HTML
 */
function html() {
	return Plugin::$instance->html;
}

global $SIMPLE_CHAT_OPTIONS;
$SIMPLE_CHAT_OPTIONS = [];

function get_simchat_options() {
	global $SIMPLE_CHAT_OPTIONS;

	if ( empty( $SIMPLE_CHAT_OPTIONS ) ) {
		$SIMPLE_CHAT_OPTIONS = get_option( 'wp_simple_chat_options', [] );
	}

	return $SIMPLE_CHAT_OPTIONS;
}

/**
 * Get a simple chat option
 *
 * @param string $option_name
 * @param bool   $default
 *
 * @return mixed
 */
function get_simchat_option( $option_name = '', $default = false ) {
	return get_array_var( get_simchat_options(), $option_name, $default );
}

/**
 * Update a simple chat option
 *
 * @param string $option_name
 * @param string $value
 *
 * @return bool
 */
function update_simchat_option( $option_name = '', $value = '' ) {
	global $SIMPLE_CHAT_OPTIONS;

	if ( empty( $SIMPLE_CHAT_OPTIONS ) ) {
		$SIMPLE_CHAT_OPTIONS = get_option( 'wp_simple_chat_options' );
	}

	$SIMPLE_CHAT_OPTIONS[ $option_name ] = $value;

	return update_option( 'wp_simple_chat_options', $SIMPLE_CHAT_OPTIONS );
}

/**
 * Add a simchat option
 *
 * @param string $option_name
 * @param string $value
 *
 * @return bool
 */
function add_simchat_option( $option_name = '', $value = '' ) {
	return update_simchat_option( $option_name, $value );
}

/**
 * @param string $option_name
 *
 * @return bool
 */
function delete_simchat_option( $option_name = '' ) {
	global $SIMPLE_CHAT_OPTIONS;

	if ( empty( $SIMPLE_CHAT_OPTIONS ) ) {
		$SIMPLE_CHAT_OPTIONS = get_option( 'wp_simple_chat_options' );
	}

	unset( $SIMPLE_CHAT_OPTIONS[ $option_name ] );

	return update_option( 'wp_simple_chat_options', $SIMPLE_CHAT_OPTIONS );
}

/**
 * Return an option name for a simchat option
 *
 * @param string $option
 *
 * @return string
 */
function simchat_option_name( $option = '' ) {
	return sprintf( 'wp_simple_chat_options[%s]', $option );
}

/**
 * Whether Groundhogg is installed
 *
 * @return bool
 */
function is_groundhogg_installed() {
	return defined( 'GROUNDHOGG_VERSION' );
}

/**
 * Add the settings link
 *
 * @param $links
 *
 * @return mixed
 */
function settings_link( $links ) {
	$settings_link = '<a href="' . esc_url( admin_url( "options-general.php?page=wp-simple-chat" ) ) . '">' . __( 'Settings' ) . '</a>';
	array_unshift( $links, $settings_link );

	return $links;
}

add_filter( "plugin_action_links_" . SIMPLE_CHAT_PLUGIN_BASE, __NAMESPACE__ . '\settings_link' );

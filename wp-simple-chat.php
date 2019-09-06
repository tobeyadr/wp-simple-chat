<?php
/*
 * Plugin Name: A WP Simple Chat
 * Plugin URI:  https://www.groundhogg.io/wp-simple-chat?utm_source=wp-plugins&utm_campaign=plugin-uri&utm_medium=wp-dash
 * Description: A simple way to include the Facebook Chat Plugin on your website.
 * Version: 2.0.9.8
 * Author: Groundhogg Inc.
 * Author URI: https://www.groundhogg.io/?utm_source=wp-plugins&utm_campaign=author-uri&utm_medium=wp-dash
 * Text Domain: groundhogg
 * Domain Path: /languages
 *
 * Groundhogg is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * any later version.
 *
 * Groundhogg is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

define( 'SIMPLE_CHAT_VERSION', '2.0.9.8' );
define( 'SIMPLE_CHAT_PREVIOUS_STABLE_VERSION', '2.0.9.7' );

define( 'SIMPLE_CHAT__FILE__', __FILE__ );
define( 'SIMPLE_CHAT_PLUGIN_BASE', plugin_basename( SIMPLE_CHAT__FILE__ ) );
define( 'SIMPLE_CHAT_PATH', plugin_dir_path( SIMPLE_CHAT__FILE__ ) );

define( 'SIMPLE_CHAT_URL', plugins_url( '/', SIMPLE_CHAT__FILE__ ) );

define( 'SIMPLE_CHAT_ASSETS_PATH', SIMPLE_CHAT_PATH . 'assets/' );
define( 'SIMPLE_CHAT_ASSETS_URL', SIMPLE_CHAT_URL . 'assets/' );

add_action( 'plugins_loaded', 'simple_chat_load_plugin_textdomain' );

define( 'SIMPLE_CHAT_TEXT_DOMAIN', 'wp-simple-chat' );

if ( ! version_compare( PHP_VERSION, '5.6', '>=' ) ) {
    add_action( 'admin_notices', 'simple_chat_fail_php_version' );
} elseif ( ! version_compare( get_bloginfo( 'version' ), '4.9', '>=' ) ) {
    add_action( 'admin_notices', 'simple_chat_fail_wp_version' );
} else {
    require SIMPLE_CHAT_PATH . 'includes/plugin.php';
}

/**
 * Groundhogg loaded.
 *
 * Fires when Groundhogg was fully loaded and instantiated.
 *
 * @since 1.0.0
 */
do_action( 'simple_chat/loaded' );

/**
 * Load Groundhogg textdomain.
 *
 * Load gettext translate for Groundhogg text domain.
 *
 * @since 1.0.0
 *
 * @return void
 */
function simple_chat_load_plugin_textdomain() {
    load_plugin_textdomain( 'wp-simple-chat', false, basename( dirname( __FILE__ ) ) . '/languages' );
}

/**
 * Groundhogg admin notice for minimum PHP version.
 *
 * Warning when the site doesn't have the minimum required PHP version.
 *
 * @since 2.0
 *
 * @return void
 */
function simple_chat_fail_php_version() {
    /* translators: %s: PHP version */
    $message = sprintf( esc_html__( 'WP Simple Chat requires PHP version %s+, plugin is currently NOT RUNNING.', 'wp-simple-chat' ), '5.6' );
    $html_message = sprintf( '<div class="error">%s</div>', wpautop( $message ) );
    echo wp_kses_post( $html_message );
}

/**
 * Groundhogg admin notice for minimum WordPress version.
 *
 * Warning when the site doesn't have the minimum required WordPress version.
 *
 * @since 2.0
 *
 * @return void
 */
function simple_chat_fail_wp_version() {
    /* translators: %s: WordPress version */
    $message = sprintf( esc_html__( 'WP Simple Chat requires WordPress version %s+. Because you are using an earlier version, the plugin is currently NOT RUNNING.', 'wp-simple-chat' ), '4.9' );
    $html_message = sprintf( '<div class="error">%s</div>', wpautop( $message ) );
    echo wp_kses_post( $html_message );
}